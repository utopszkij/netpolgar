<?php 
/**
 * üzenetek lik, dislike, voksok kezelése (böngészés, megjelenítés, modositás, felvitel, törlés
 * használja az \Auth::user() -t.
 * - false: nincs bejelentkezve
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
     * group like/dislike -ra jogosult?
     * @param int $id group->id
     * @return bool
     */
    protected function likeCheckGroup(int $id): bool {
        $result = false;
        $user = \Auth::user();
        if ($user) {
            $group = \DB::table('groups')->where('id','=',$id)->first();
            if ($group) {
                if (($group->parent_id == 0) & ($user->current_team_id == 0)) {
                    $result = true;
                } else if ($group->parent_id > 0) {
                    $w = \DB::table('users')->where('id','=',$group->parent_id)->first();
                    if ($w) {
                        $result = true;
                    }
                }
            }
        }
        return $result;
    }
    
    /**
     * parent like/dislike -ra jogosult?
     * @param string $parentType
     * @param int $id
     * @return bool
     */
    protected function likeCheck(string $parentType, int $id):bool {
        $result = false;
        if ($parentType == 'group') {
            $result = $this->checkGroup($id);
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
            $enabled = $this->likeCheck($parentType, $id); 
            if (!$enabled) {
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
}

