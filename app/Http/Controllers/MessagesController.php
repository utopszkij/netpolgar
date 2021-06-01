<?php 
/**
 * üzenetek lik, dislike, voksok kezelése (böngészés, megjelenítés, modositás, felvitel, törlés
 * használja az \Auth::user() -t.
 * - false: nincs bejelentkezve
 * 
 * parentType:
 *   group
 *   project
 *   product
 *   event
 *   poll
 *   Priv_targetUserId_senderUserId 
 * 
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

/**
 * csoportok kezelő controller osztály
 * @author utopszkij
 */
class MessagesController extends Controller {

    /**
     * bejelntkezett user jogosult üzenet kezelésre?
     * @param string $parentType
     * @param int $id
     * @return boolean
     */
    protected function checkAccessRight(string $parentType, int $id) {
        $result = false;
        if (\Auth::user()) {
            if ($parentType == 'group') {
                $member = \DB::table('group_members')
                ->where('group_id','=',$id)
                ->where('user_id','=', \Auth::user()->id)
                ->whereIn('rank',["member","admin"])
                ->where('status','=','active')
                ->first();
                if ($member) {
                    $result = true;
                }
            }
            if ($parentType == 'project') {
                $member = \DB::table('project_members')
                ->where('project_id','=',$id)
                ->where('user_id','=', \Auth::user()->id)
                ->whereIn('rank',["member","admin"])
                ->where('status','=','active')
                ->first();
                if ($member) {
                    $result = true;
                }
            }
            // privát üzenet csak regisztráltaknak engedélyezett
            // product üzenet minden regisztrált felhasználónak engedélyezett
            // eseményhez minden regisztrált felhasználónak enegedélyezett
            // szavazáshoz a szavazásra jogosultaknak engedélyezett
        }
        return $result;
    }
    
    /**
     * like/dislike 
     * @param Request $request
     * @param string $parentType
     * @param int $id parentRec->id
     * @param string $likeType
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function like(Request $request, string $parentType, int $id, string $likeType) {
        if (\Auth::user()) {
            if (!$this->checkAccessRight($parentType, $id)) {
                return redirect(\URL::previous());
            }
            if (($likeType == 'like') | ($likeType == 'dislike')) {
                $model = new \App\Models\Messages();
                $w = $model->where('parent_type','=',$parentType)
                ->where('parent_id','=',$id)
                ->where('type','=',$likeType)
                ->where('user_id','=',\Auth::user()->id)->first();
                if ($w) {
                    $model = new \App\Models\Messages();
                    $w = $model->where('parent_type','=',$parentType)
                    ->where('parent_id','=',$id)
                    ->where('type','=',$likeType)
                    ->where('user_id','=',\Auth::user()->id)->delete();
                } else {
                    $model = new \App\Models\Messages();
                    $model->id = 0;
                    $model->parent_type = $parentType;
                    $model->parent_id = $id;
                    $model->user_id = \Auth::user()->id;
                    $model->type = $likeType;
                    $model->value = "";
                    $model->created_at = date('Y-m-d');
                    $model->moderatorinfo = "";
                    $model->save();
                }
                return redirect(\URL::previous());
            }
        } else {
            return redirect(\URL::previous());
        }
    }
    
    /**
     * üzenetek böngésző
     * @param Request $request
     * @param string $parentType
     * @param int $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function list(Request $request, string $parentType, int $id) {
        if ($this->checkAccessRight($parentType,$id))  {
            $br = 'messages';
            $offset = $request->session()->get($br.'offset',0);
            $limit = $request->session()->get($br.'limit',10);
            $order = $request->session()->get($br.'order','created_at');
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
            
            if ($parentType == 'group') {
                $member = new \App\Models\Group_members();
                $member->where('group_id','=',$id)
                ->where('user_id','=', \Auth::user()->id)
                ->whereIn('rank',['admin','member'])
                ->where('status','=','active')
                ->orderBy('rank','asc')
                ->first();
                $groupModel = new \App\Models\Groups();
                $parent = $groupModel->where('id','=',$id)->first();
            }
            if ($parentType == 'project') {
                $member = new \App\Models\Project_members();
                $member->where('project_id','=',$id)
                ->where('user_id','=', \Auth::user()->id)
                ->whereIn('rank',['member','admin'])
                ->where('status','=','active')
                ->orderBy('rank','asc')
                ->first();
                $projectModel = new \App\Models\Projects();
                $parent = $projectModel->where('id','=',$id)->first();
            }
            if ($parentType == 'product') {
                $productModel = new \App\Models\Product();
                $parent = $productModel->where('id','=',$id)->first();
                $member = new \App\Models\Project_members();
                $member->where('project_id','=',$parent->project_id)
                ->where('user_id','=', \Auth::user()->id)
                ->whereIn('rank',['member','admin'])
                ->where('status','=','active')
                ->orderBy('rank','asc')
                ->first();
            }
            if ($parentType == 'event') {
                $eventModel = new \App\Models\Events();
                $parent = $eventModel->where('id','=',$id)->first();
                $member = new \App\Models\Project_members();
                $member->first();
                if (\Auth::user()->id == $parent->created_by) {
                    $member->rank = 'admin';
                } else {
                    $member->rank = 'member';
                }
            }
            if (substr($parentType,0,4) == 'priv') {
                $userModel = new \App\Models\User();
                $parent = $userModel->where('id','=',$id)->first();
                $member = new \App\Models\Project_members();
                $member->first();
                $member->rank = 'member';
            }
            
            $messages = \DB::table('messages');
            $messages->leftJoin('users','users.id','=','messages.user_id')
                ->select('messages.id as id', 'messages.created_at as created_at', 
                    'value', 'name', 'profile_photo_path', 'users.id as user_id')
                ->where('parent_type','=',$parentType)
                ->where('parent_id','=',$id)
                ->where('type','=','message');
            if ($filterStr != '') {
                $messages->where('value','like','%'.$filterStr.'%');
            }
            $items = $messages->offset($offset)
                ->orderBy('messages.created_at')
                ->paginate($limit);
            return view('messages',[
                'parentId'=> $id,
                'items' => $items,
                'parentType' => $parentType,
                'parent' => $parent,
                'member' => $member,
                'filterStr' => ''
            ]);
        } else {
            return redirect(\URL::previous())->with('error',__('accessViolation'));
        }
    }
    
    public function add(Request $request, string $parentType, int $parentId, string $message) {
        if ($this->checkAccessRight($parentType,$parentId)) {
            $model = new \App\Models\Messages();
            $model->parent_type = $parentType;
            $model->parent_id = $parentId;
            $model->value = urldecode($message);
            $model->type="message";
            $model->user_id = \Auth::user()->id;
            $model->save();
            return ('saved '.$model->errorMsg);
        } else {
            return '';
        }
    }
}

