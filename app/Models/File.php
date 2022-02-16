<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


class File extends Model {
    use HasFactory;
    protected $fillable = [
        'parent_type', 'parent', 'name', 'description', 'type', 'licence','created_by',
        'created_at','updated_at'
    ];
   
    /**
     * Üres rekord létrehozása
     * @return mixed
     */
    public static function emptyRecord() {
        $result = JSON_decode('{
			"id":0,
			"parent_type":"",
			"parent":0,
			"name":"",
			"description":"",
			"type":"",
			"licence":"MIT",
			"created_at":"1900-01-01",
			"created_by":0
    	}');
        return $result;
    }
    
    public function getInfo($file) {
        if (\Auth::check()) {
            $logged = \Auth::user()->id;
        } else {
            $logged = 0;
        }
        $result = JSON_decode('{
            "filePath": "",
            "userAdmin": false,
            "fileSize": 0, 
            "likeCount": 0, 
            "userLiked": false, 
            "disLikeCount": 0, 
            "userDisLiked": false,
            "msgCount": 0,
            "downloadCount": 0 
        }');
        $filePath = 'storage/'.$file->parent_type.'/'.
            substr((1000+$file->id),0,3).'/'.
            substr((1000+$file->id),3,100).'.'.$file->type;
        if (defined('UNITTEST')) {
                $fileSize = 0;
        } else {
                $fileSize = filesize($filePath);
        }
        $result->filePath = $filePath;
        $result->fileSize = $fileSize;
        $parentType = $file->parent_type;
        $parentId = $file->parent;
        if ($parentType == 'users') {
            $userId = $parentId;
        } else {
            $userId = 0;
        }
        $result->userAdmin = $this->userAdmin($parentType, $parentId, $userId);
        $result->likeCount = \DB::table('likes')->where('parent_type','=','files')
        ->where('parent','=',$file->id)
        ->where('like_type','=','like')
        ->count();
        $result->userLiked = (\DB::table('likes')->where('parent_type','=','files')
            ->where('parent','=',$file->id)
            ->where('like_type','=','like')
            ->where('user_id','=',$logged)
            ->count() > 0);
        $result->disLikeCount = \DB::table('likes')->where('parent_type','=','files')
        ->where('parent','=',$file->id)
        ->where('like_type','=','dislike')
        ->count();
        $result->userDisLiked = (\DB::table('likes')->where('parent_type','=','files')
            ->where('parent','=',$file->id)
            ->where('like_type','=','dislike')
            ->where('user_id','=',$logged)
            ->count() > 0);
        $result->msgCount = \DB::table('messages')->where('parent_type','=','files')
        ->where('parent','=',$file->id)
        ->count();
        $result->downloadCount = \DB::table('members')->where('parent_type','=','files')
        ->where('parent','=',$file->id)
        ->count();
        if (\Auth::check()) {
            if ($file->created_by == \Auth::user()->id) {
                $result->userAdmin = true;
            }
        }
        return $result;
    }
    
    /**
     * bejelentkezett user tagja a parent .nek?
     * @param string $parentType
     * @param int $parentId
     * @return bool
     */
    public static function userMember(string $parentType, int $parentId): bool {
        if (\Auth::check()) {
            return (\DB::table('members')
            ->where('parent_type','=',$parentType)
            ->where('parent','=',$parentId)
            ->where('user_id','=',\Auth::user()->id)
            ->where('status','=','active')
            ->count() > 0);
        } else {
            return false;
        }
    }
    
    /**
     * bejelentkezett user adminja a parent -nek?
     * @param string $parentType
     * @param int $parentId
     * @return bool
     */
    public static function userAdmin(string $parentType, int $parentId): bool {
        if (\Auth::check()) {
            return (\DB::table('members')
            ->where('parent_type','=',$parentType)
            ->where('parent','=',$parentId)
            ->where('user_id','=',\Auth::user()->id)
            ->where('rank','=','admin')
            ->where('status','=','active')
            ->count() > 0);
        } else {
            return false;
        }
    }
    
    /**
     * A request -ben valós rekord adatok vannak?
     * @param Request $request
     * @return bool
     */
    public static function valid(Request $request): bool {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'licence' => 'required'
        ]);
        // ide csak akkor jut a vezérlés ha minden OK, egyébként redirekt az elöző oldalra
        return true;
    }
    
    /**
     * rekord tárolás
     * @param int $id
     * @param Request $request
     * @return string
     */
    public static function storeOrUpdate(int &$id, Request $request): string {
        $parentType = $request->input('parent_type');
        $parent = $request->input('parent');
        
        // rekord array kialakitása
        $fileArr = [];
        $fileArr['parent_type'] = $request->input('parent_type');
        $fileArr['parent'] = $request->input('parent');
        $fileArr['name'] = strip_tags($request->input('name'));
        $fileArr['description'] = strip_tags($request->input('description'));
        $fileArr['type'] = strip_tags($request->input('type'));
        $fileArr['licence'] = strip_tags($request->input('licence'));
        if ($id == 0) {
            if (\Auth::user()) {
                $fileArr['created_by'] = \Auth::user()->id;
            } else {
                $fileArr['created_by'] = 0;
            }
        }
        
        // file rekord tárolás az adatbázisba
        $errorInfo = '';
        try {
            $model = new File();
            if ($id == 0) {
                $fileRec = $model->create($fileArr);
                $id = $fileRec->id;
            } else {
                $model->where('id','=',$id)->update($fileArr);
            }
        } catch (\Illuminate\Database\QueryException $exception) {
            $errorInfo = JSON_encode($exception->errorInfo);
        }
        return $errorInfo;
    }
    
    /**
     * lapozható adat objekt lekérése az adatbázisból
     * @param string $parentType
     * @param int $parentId
     * @param int $userId
     * @param int $pageSize
     * @return object
     */
    public function getData(string $parentType, int $parentId,
        int $userId, int $pageSize) {
        if ($userId != 0) {
                $data = $this->latest()
                ->where('created_by', $userId)
                ->orderBy('name')
                ->paginate($pageSize);
        } else if ($parentId != 0) {
                $data = $this->latest()
                ->where('parent_type','=',$parentType)
                ->where('parent','=',$parentId)
                ->orderBy('name')
                ->paginate($pageSize);
        }
        return $data;
    }
    
}
