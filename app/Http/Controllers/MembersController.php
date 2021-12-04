<?php 
/**
 * csoportok kezelése (böngészés, megjelenítés, modositás, felvitel, törlés
 * használja az \Auth::user() -t.
 * - false: nincs bejelentkezve
 * ->current_team_id == 0  system admin
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

/**
 * csoport vagy projekt tagokat kezelő controller osztály
 * @author utopszkij
 */
class MembersController extends Controller {
   
    protected function getParentPath(int $parent_id) {
        $parentPath = [];
        $model = new \App\Models\Groups();
        $p = $model->where('id','=',$parent_id)->first();
        while ($p) {
            array_splice($parentPath,0,0,[$p]);
            $model = new \App\Models\Groups();
            $p = $model->where('id','=',$p->parent_id)->first();
        }
        return $parentPath;
    }
    
    
    /**
     * böngésző képernyő
     * @param Request $request (sessionban: offset, order, order_dir, folderStr
     * @param string $parentType 'group' | 'project'
     * @param int $parent_id
     * @return Controller Response
     */
    public function list(Request $request, string $parentType,  int $parentId) {
        $br = $parentType.'members';
        $offset = $request->session()->get($br.'offset',0);
        $limit = $request->session()->get($br.'limit',10);
        $order = $request->session()->get($br.'order','name');
        $orderDir = $request->session()->get($br.'orderDir','ASC');
        $filterStr = $request->session()->get($br.'filterStr','');

        if ($request->input('order') == $order) {
            if ($orderDir == 'ASC') {
                $orderDir = 'DESC';
            } else {
                $orderDir = 'ASC';
            }
        }
        
        
        $offset = $request->input('offset',$offset);
        $limit = $request->input('limit',$limit);
        $order = $request->input('order',$order);
        $filterStr = $request->input('filterStr',$filterStr);
        if (!isset($filterStr)) {
            $filterStr = '';
        }

        $request->session()->put($br.'offset', $offset);
        $request->session()->put($br.'limit', $limit);
        $request->session()->put($br.'order', $order);
        $request->session()->put($br.'orderDir', $orderDir);
        $request->session()->put($br.'filterStr', $filterStr);
        
        $model = new \App\Models\Members();
        $members = $model->list($parentType, $parentId,
            $offset, $limit, $order, $orderDir, $filterStr);
        
        if ($parentType == 'group') {
            $model = new \App\Models\Groups();
            $parent = $model->where('id','=',$parentId)->first();
            $parentPath = $this->getParentPath($parentId);
            if (\Auth::user()) {
                $admin = \DB::table('group_members')
                ->where('group_id','=',$parentId)
                ->where('user_id','=',\Auth::user()->id)
                ->where('status','=','active')
                ->where('rank','=','admin')
                ->first();
            } else {
                $admin = false;
            }
        } else if ($parentType == 'project') {
            $model = new \App\Models\Projects();
            $parent = $model->where('id','=',$parentId)->first();
            $parentPath = [];
            if (\Auth::user()) {
                $admin = \DB::table('project_members')
                ->where('group_id','=',$parentId)
                ->where('user_id','=',\Auth::user()->id)
                ->where('status','=','active')
                ->where('rank','=','admin')
                ->first();
            } else {
                $admin = false;
            }
        } else {
            $parent = JSON_decode('{"id":0, "name":"", "avatar":""}');
            $parentPath = [];
            $admin = false;
        }
        
        $users = \DB::table('users')->orderBy('name')->get();
        return view('members',['members' => $members,
            'parentType' => $parentType,
            'parentId' => $parentId,
            'parentPath' => $parentPath,
            'parent' => $parent,
            'admin' => $admin,
            'order' => $order,
            'orderDir' => $orderDir,
            'filterStr' => $filterStr,
            'users' => $users
        ]);
    }
    
    /**
     * edit/add ürlap
     * @param Request $request
     * @param string $parentType
     * @param int $parent_id
     * @param string $name
     * @return Controller Response
     */
    public function form(Request $request, string $parentType, int $parentId, string $name) {
        $model = \DB::table($parentType.'_members');
          $members = $model->leftJoin('users','users.id',$parentType.'_members.user_id')
          ->select([$parentType.'_members.id as id',
              $parentType.'_members.user_id',
              $parentType.'_members.status',
              $parentType.'_members.rank',
              'users.name',
              'users.profile_photo_path'])
          ->where($parentType.'_id','=',$parentId) 
          ->where('users.name','=',$name)  
          ->orderBy('name')
          ->get();
          if (\Auth::user()) {
            $admin = \DB::table($parentType.'_members')
            ->where($parentType.'_id','=',$parentId)
            ->where('user_id','=',\Auth::user()->id)
            ->where('rank','=','admin')
            ->where('status','=','avtive')->first();
          } else {
              $admin = false;
          }
          $parent = \DB::table($parentType.'s')->where('id','=',$parentId)->first();
          
          return view('member_form',[
             "members" => $members,
             "admin" => $admin, 
             "parent" => $parent,
             "parentType" => $parentType 
          ]);
    }
    
    
    /**
     * add/edit ürlap tárolása
     * @param Request $request parentType, parentId, name, status_###, rank
     * @param int $id
     * @return Controller Response
     */
    public function save(Request $request) {

        // csr token check 
        $token = $request->input('_token','');
        if ($token != $request->session()->token()) {
            return redirect('/')->with('error',__('csrf_token_error'));
        }
        
        
        // csak bejelentkezett user használhatja
        if (\Auth::user() == false) {
            return redirect('/')->with('error',__('accessViolation'));
        }
        $user = \Auth::user();
        
        // csak a parent group vagy project admin használhatja
        if ($request->input('parentType') == 'group') {
            $member = \DB::table('group_members')
            ->where('group_id','=',$request->input('parentId'))
            ->where('user_id','=',$user->id)
            ->where('rank','=','admin')->first();
            if ((!$member) & ($user->current_team_id != 0)) {
                return redirect('/')->with('error',__('accessViolation'));
            }
        }
        if ($request->input('parentType') == 'project') {
            $member = \DB::table('project_members')
            ->where('project_id','=',$request->input('parentId'))
            ->where('user_id','=',$user->id)
            ->where('rank','=','admin')->first();
            if ((!$member) & ($user->current_team_id != 0)) {
                return redirect('/')->with('error',__('accessViolation'));
            }
        }
        
        $memberUser = \DB::table('users')->where('name','=',$request->input('name'))->first();
        $modelName = "\\App\Models\\".ucFirst($request->input('parentType')).'_members';
        if ($request->input('rank','') != '') {
            // új felvitel
            // nézzük nincs-e már ilyen?
            $idName = $request->input('parentType').'_id';
            $model = new $modelName ();
            $record = $model->where($idName,'=',$request->input('parentId'))
            ->where('user_id','=',$memberUser->id)
            ->where('rank','=',$request->input('rank'))->first();
            if (!$record) {
                // - nics, felvisszük
                $model = new $modelName ();
                $model->id = 0;
                $model->$idName = $request->input('parentId');
                $model->user_id = $memberUser->id;
                $model->rank = $request->input('rank');
                $model->status = 'active';
                $model->created_at = date('Y-m-d H:i:s');
                $model->created_by = $user->id;
                $model->save();
            }
        }
        
        // modositások
        $inputs = $request->all();
        foreach ($inputs as $fn => $fv) {
            if (substr($fn,0,7) == 'status_') {
                $id = (int) substr($fn,7,10);
                $model = new $modelName ();
                $record = $model->where('id','=',$id)->first();
                $record->status = $fv;
                $record->save();
            }
        }
        
        if ($request->input('user_id','') != '') {
            // új tag felvitele
            $idName = $request->input('parentType').'_id';
            $user = \Auth::user();
            $model = new $modelName ();
            $model->id = 0;
            $model->$idName = $request->input('parentId');
            $model->user_id = $request->input('user_id');
            $model->rank = 'member';
            $model->status = 'active';
            $model->created_at = date('Y-m-d H:i:s');
            $model->created_by = $user->id;
            $model->save();
        }
        
        $url = '/members/'.$request->input('parentType').'/'.$request->input('parentId');
        return redirect($url)->with('success', __('Saved') );
   }
    
}