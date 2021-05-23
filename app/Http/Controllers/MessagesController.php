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

    public function like(Request $request, string $parentType, int $id, string $likeType) {
        if (\Auth::user()) {
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
        }   
    }
}

