<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Minimarkdown;
use App\Models\Member;

class Event extends Model {

    use HasFactory;
    protected $fillable = [
        'parent_type', 'parent', 'name', 'description',
        'avatar', 'location', 'date', 'hours', 'minutes', 'length',
        'created_by','created_at','updated_at'
    ];
    
    public static function emptyRecord() {
        return JSON_decode('{
        "id":0,
        "parent_type": "", 
        "parent": 0,
        "name":"", 
        "description":"", 
        "avatar":"", 
        "location":"", 
        "date":"'.date('Y-m-d').'",
        "hours":0,
        "minutes":0, 
        "length":"30 perc",
        "created_by":0,
        "created_at":"'.date('Y-m-d').'",
        "updated_at":"'.date('Y-m-d').'"
    }');
    }
    
    /**
     * Request valid Event record? (tárolás előtti ellenörzés)
     * @param Request
     * @return bool
    */
    public static function valid(Request $request):bool {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'location' => 'required',
            'date' => 'required',
            'hours' => ['required','numeric','min:0','max:24'],
            'minutes' => ['required','numeric','min:0','max:59'],
            'length' => 'required'
        ]);
        // ide csak akkor kerül a vezérlés ha minden OK
        // egyébként redirekt az elöző oldalra
        return true;
    }
    /**
     * event rekord irása az adatbázisba a $request-be lévő információkból
     * @param Request $request
     * @return string
     */
    public function updateOrCreate(Request $request): string {
        $errorInfo = '';
        $id = $request->input('id',0);
        // rekord array kialakitása
        $eventArr = [];
        $eventArr['parent_type'] = $request->input('parent_type');
        $eventArr['parent'] = $request->input('parent');
        $eventArr['name'] = strip_tags($request->input('name'));
        $eventArr['description'] = strip_tags($request->input('description'));
        $eventArr['avatar'] = strip_tags($request->input('avatar'));
        $eventArr['location'] = strip_tags($request->input('location'));
        $eventArr['date'] = $request->input('date');
        $eventArr['hours'] = $request->input('hours');
        $eventArr['minutes'] = $request->input('minutes');
        $eventArr['length'] = $request->input('length');
        
        $fileInfo = Minimarkdown::getRemoteFileInfo($eventArr['avatar']);
        if (($fileInfo['fileSize'] > 2000000) |
            ($fileInfo['fileSize'] < 10)) {
                $eventArr['avatar'] = '/img/noimage.png';
        }
            
        if ($id == 0) {
                $eventArr['status'] = 'proposal';
                if (\Auth::user()) {
                    $eventArr['created_by'] = \Auth::user()->id;
                } else {
                    $eventArr['created_by'] = 0;
                }
         }
         // events rekord tárolás az adatbázisba
         $errorInfo = '';
         try {
                $model = new Event();
                if ($id == 0) {
                    $eventRec = $model->create($eventArr);
                    $id = $eventRec->id;
                } else {
                    $model->where('id','=',$id)
                    ->update($eventArr);
                }
                // file upload kezelése
                $uploadMsg = Upload::processUpload('img',
                    storage_path().'/events/'.$id.'/',
                    'avatar',
                    ['jpg','png','gif']);
                if ($uploadMsg != 'no upload') {
                    if (substr($uploadMsg,0,5) != 'ERROR') {
                        $avatarUrl = str_replace(storage_path(),\URL::to('/storage'),$uploadMsg);
                        \DB::table('events')
                        ->where('id','=',$id)
                        ->update(["avatar" => $avatarUrl]);
                    } else {
                        $errorInfo = $uploadMsg;
                    }
                }
         } catch (\Illuminate\Database\QueryException $exception) {
                $errorInfo = JSON_encode($exception->errorInfo);
         }
         return $errorInfo;
    }
    
    /**
     * event rekord tölrése
     * @param int $id
     * @return string $errorInfo '' vagy hibaüzenet
     */
    public function eventDelete(int $id) {
        $result = '';
        try {
            \DB::table('members')->where('parent_type','=','events')
            ->where('parent','=',$id)
            ->delete();
            \DB::table('likes')->where('parent_type','=','events')
            ->where('parent','=',$id)
            ->delete();
            \DB::table('events')->where('id','=',$id)
            ->delete();
        } catch (\Illuminate\Database\QueryException $exception) {
            $errorInfo = JSON_encode($exception->errorInfo);
        }
        return $result;
        
    }

    
    /**
     * kiegészítő infók kiolvasása
     * @param int $id
     * @return object
     */
    public static function getInfo(int $id) {
        $result = JSON_decode('{
			"userLiked":false,
			"userDisLiked":false,
			"likeCount":0,
			"disLikeCount":0,
            "memberCount":0,
            "userMember": false,
            "userAdmin": false
		}');
        if ($id == 0) {
            return $result;
        }
        $event = $this->where('id','=',$id)->first();
        if (!$event) {
            return $result;
        }
        Event::getLikeInfo($result, $event);
        $result->userMember = Member::userMember($event->parent_type, $event->parent);
        $result->userAdmin = Member::userAdmin($event->parent_type, $event->parent);
        return $result;
    }

    /**
     * like, dislike infók és memberCount meghatátozása
     * @param object $result
     * @param Event $event
     * @return void  $result -ot modosítja
     */
    public static function getLikeInfo(&$result, $event): void {
        $user = \Auth::user();
        $t = \DB::table('likes');
        $result->likeCount = $t->where('parent_type','=','events')
        ->where('parent','=',$event->id)
        ->where('like_type','=','like')->count();
        $t = \DB::table('likes');
        $result->disLikeCount = $t->where('parent_type','=','events')
        ->where('parent','=',$event->id)
        ->where('like_type','=','dislike')->count();
        if ($user) {
            $t = \DB::table('likes');
            $result->userDisLiked = ($t->where('parent_type','=','events')
                ->where('parent','=',$event->id)
                ->where('like_type','=','dislike')
                ->where('user_id','=',$user->id)
                ->count() >= 1);
            $t = \DB::table('likes');
            $result->userLiked = ($t->where('parent_type','=','events')
                ->where('parent','=',$event->id)
                ->where('like_type','=','like')
                ->where('user_id','=',$user->id)
                ->count() >= 1);
        }
        $t = \DB::table('members');
        $result->memberCount = $t->select('distinct user_id')
        ->where('parent_type','=','events')
        ->where('parent','=',$event->id)
        ->where('status','=','active')
        ->count();
    }

    /**
     * lapozható adat objekt kialakítása 
     * @param string $parentType
     * @param int $parentId
     * @param int $pageSize
     * @return object
     */
    public static function getData(string $parentType, int $parentId, int $pageSize) {
        return \DB::table('events')
            ->where('parent_type','=',$parentType)
            ->where('parent','=',$parentId)
            ->orderBy('date','desc')
            ->paginate($pageSize);
    }  
    
}
	
