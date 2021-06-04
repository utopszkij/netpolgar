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

    /**
     * csoport tulajdonosok elérése a fa szerint felfelé haladva
     * @param int $parent_id
     * @return array
     */
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
        
        return view('members',['members' => $members,
            'parentType' => $parentType,
            'parentId' => $parentId,
            'parent' => $parent,
            'admin' => $admin,
            'order' => $order,
            'orderDir' => $orderDir,
            'filterStr' => $filterStr,
            'parentPath' => $parentPath
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
          view('memberForm',[
             "members" => $members,
             "admin" => $admin, 
             "parent" => $parent 
          ]);
    }
    
    
    /**
     * add/edit ürlap tárolása
     * @param Request $request
     * @param int $id
     * @return Controller Response
     */
    public function save(Request $request) {

        // csr token check 
        $token = $request->input('_token','');
        if ($token != $request->session()->token()) {
            return redirect('/')->with('error',__('csrf_token_error'));
        }
        
        // validator
        $validatorRules = [
            'name' => 'required',
            'config' => 'required|json',
            'status' => 'required',
            'parent_id' => 'required',
            'created_by' => 'exists:users,id',
        ];
        if ($request->input('parent_id') > 0) {
            $validatorRules['parent_id'] = 'exists:groups,id';
        }
        if ($request->input('avatar') != '') {
            $validatorRules['avatar'] = 'url';
        }
        $validated = $request->validate($validatorRules);

        
        // csak bejelentkezett user használhatja
        if (\Auth::user() == false) {
            return redirect('/groups/0/0/0')->with('error',__('accessViolation'));
        }
        $user = \Auth::user();
        // ha új felvitel akkor csak a parent group tagja használhatja
        if ($request->input('id') == 0) {
            $member = \DB::table('group_members')
            ->where('group_id','=',$request->input('parent_id'))
            ->where('user_id','=',$user->id)
            ->where('rank','in',['member','admin'])->first();
            if (($member == false) & ($user->current_team_id <> 0)) {
                return redirect('/groups/0/0/0')->with('error',__('accessViolation'));
            }
            $creator = \Auth::user();
        }
        // ha modositás akkor csak az aktuáis group adminja használhatja
        if ($request->input('id') > 0) {
            $member = \DB::table('group_members')
            ->where('group_id','=',$request->input('id'))
            ->where('user_id','=',$user->id)
            ->where('status','=','admin');
            if (($member == false) & ($user->current_team_id <> 0)) {
                return redirect('/groups/0/0/0')->with('error',__('accessViolation'));
            }
        }
        $model = new \App\Models\Groups();
        if ($request->input('id') > 0) {
            $model = $model->find($request->input('id'));
            $model->updated_at = date('Y-m-d');
        } else {
            $model->created_at = date('Y-m-d');
            $model->created_by = \Auth::user()->id;
        }
        $model->id = $request->input('id');
        $model->parent_id = $request->input('parent_id');
        $model->name = $request->input('name');
        $model->description = $request->input('description');
        $model->avatar = $request->input('avatar');
        $model->status = $request->input('status');
        $model->config = $request->input('config');
        $model->created_at =$request->input('created_at');
        $model->created_by =$request->input('created_by');
        $model->activated_at =$request->input('activated_at','');
        $model->closed_at =$request->input('closed_at','');
        $model->save();
        
        $url = '/groups/'.$model->parent_id.'/0/0';
        if ($model->errorMsg == '') {
            // felvivő user az új group adminja
            if (\DB::table('group_members')->insert([
               "id" => 0,
               "group_id" => $model->id,
               "user_id" => \Auth::user()->id,
               "status" => "active",
               "rank" => "admin",
                "activated_at" => date('Y-m-d'),
                "created_at" => date('Y-m-d'),
                "created_by" => \Auth::user()->id
                ])) {
                    return redirect($url)->with('success', __('groups.saved') );
                } else {
                    return redirect($url)->with('error', 'db error in save member record');
                }
        } else {
            return redirect(\URL::previous())->with('error', $model->errorMsg);
        }
        return 'group form';
    }
    
}