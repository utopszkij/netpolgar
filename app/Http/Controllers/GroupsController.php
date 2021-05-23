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
 * csoportok kezelő controller osztály
 * @author utopszkij
 */
class GroupsController extends Controller {

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
     * @param int $parent_id
     * @param int $member_id
     * @param int $admin_id
     * @return Controller Response
     */
    public function list(Request $request, int $parent_id, int $member_id, int $admin_id) {
        $br = 'groups';
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
        
        $model = new \App\Models\Groups();
        $groups = $model->list($parent_id, $member_id, $admin_id,
            $offset, $limit, $order, $orderDir, $filterStr);
        
        $user = new \App\Models\User();
        $member = $user->where('id','=',$member_id)->first(); // null ha nem ltezik, profile_phptp_url is van benne
        
        $user = new \App\Models\User();
        $admin = $user->where('id','=',$admin_id)->first();
        
        $model = new \App\Models\Groups();
        $parent = $model->where('id','=',$parent_id)->first();
        
        $parentPath = $this->getParentPath($parent_id);

        return view('groups',['groups' => $groups,
            'member' => $member,
            'admin' => $admin,
            'order' => $order,
            'orderDir' => $orderDir,
            'filterStr' => $filterStr,
            'parent' => $parent,
            'parentPath' => $parentPath
        ]);
    }
    
    /**
     * edit/add ürlap
     * @param Request $request
     * @param int $id
     * @return Controller Response
     */
    public function form(Request $request, int $id, int $parent_id) {
        $user = \Auth::user();
        // csak bejelentkezett user használhatja
        if ($user == false) {
            return redirect('/groups/0/0/0')->with('error',__('accessViolation'));
        }
        // ha új felvitel akkor csak a parent group tagja használhatja
        if ($id == 0) {
            $memberModel = new \App\Models\Group_members();
            $member = $memberModel->where('group_id','=',$parent_id)
            ->where('user_id','=',$user->id)
            ->where('rank','in',['member','admin'])->first();
            
            if (($member == false) & ($user->current_team_id <> 0)) {
                return redirect('/groups/0/0/0')->with('error',__('accessViolation'));
            }
            $creator = \Auth::user();
        }
        // ha modositás akkor csak az aktuáis adminja használhatja
        if ($id > 0) {
            $memberModel = new \App\Models\Group_members();
            $member = $memberModel->where('group_id','=',$id)
            ->where('user_id','=',$user->id)
            ->where('rank','=','admin')->first();
            if (($member == false) & ($user->current_team_id <> 0)) {
                return redirect('/groups/0/0/0')->with('error',__('accessViolation'));
            }
        }
                
        $model = new \App\Models\Groups();
        if ($id > 0) {
            $group = $model->where('id','=',$id)->first();
            if (!$group) {
                $group = $model->init($parent_id, \Auth::user());
            }
            $group->updated_at = date('Y-m-d');
            $creator = \DB::table('users')->where('id','=',$group->created_by)->first();
        } else {
            $group = $model->init($parent_id, \Auth::user());
            $group->parent_id = $parent_id;
            $group->created_at = date('Y-m-d');
        }
        $parent = \DB::table('groups')->where('id','=',$parent_id)->first();
        $parentPath = $this->getParentPath($parent_id); 
            
        return view('group_form',["group" => $group, 
            "user" => \Auth::user(),
            "parent" => $parent,
            "parentPath" => $parentPath,
            'creator' => $creator
        ]);
    }
    
    /**
     * csoport adatlap
     * @param Request $request
     * @param int $id
     * @return Controller Response
     */
    public function show(Request $request, int $id) {
        $user = \Auth::user();
        $member = JSON_decode('{"rank":""}');
        if (($id > 0) & ($user != false)) {
            $memberModel = new \App\Models\Group_members();
            $member = $memberModel->where('group_id','=',$id)
            ->where('user_id','=',$user->id)
            ->where('rank','=','admin')->first();
            if (($member == false) & ($user->current_team_id <> 0)) {
                return redirect('/groups/0/0/0')->with('error',__('accessViolation'));
            }
            if (!isset($member->rank)) {
                $member->rank = "";
            }
        } else {
            $member = JSON_decode('{"rank":""}');
        }
        $model = new \App\Models\Groups();
        if ($id > 0) {
            $group = $model->where('id','=',$id)->first();
            $creator = \DB::table('users')->where('id','=',$group->created_by)->first();
        }
        if ($group) {
            $parent = \DB::table('groups')->where('id','=',$group->parent_id)->first();
            if ($group->parent_id > 0) {
                $parentPath = $this->getParentPath($group->parent_id);
            } else {
                $parentPath = [];
            }
            
            // like,dislike információk 
            $messageModel = new \App\Models\Messages();
            $likeCount = $messageModel->where('parent_type','=','group')
            ->where('parent_id','=',$group->id)
            ->where('type','=','like')
            ->count();
            $messageModel = new \App\Models\Messages();
            $disLikeCount = $messageModel->where('parent_type','=','group')
            ->where('parent_id','=',$group->id)
            ->where('type','=','dislike')
            ->count();
            $messageModel = new \App\Models\Messages();
            $userLiked = ($messageModel->where('parent_type','=','group')
                ->where('parent_id','=',$group->id)
                ->where('type','=','like')
                ->where('user_id','=',$user->id)
                ->count() > 0);
            $messageModel = new \App\Models\Messages();
            $userDisLiked = ($messageModel->where('parent_type','=','group')
                ->where('parent_id','=',$group->id)
                ->where('type','=','dislike')
                ->where('user_id','=',$user->id)
                ->count() > 0);
            
            return view('group_show',["group" => $group,
                "user" => \Auth::user(),
                "member" => $member,
                "parent" => $parent,
                "parentPath" => $parentPath,
                'creator' => $creator,
                'likeCount' => $likeCount,
                'disLikeCount' => $disLikeCount,
                'userLiked' => $userLiked, 
                'userDisLiked' => $userDisLiked
            ]);
        } else {
            return redirect('/groups/0/0/0')->with('error',__('notFound'));
        }
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
    
    /**
     * csoport logikai törlése
     * @param Request $request
     * @param int $id
     * @return Controller response
     */
    public function delete(Request $request, int $id) {
        return 'group form';
    }
}