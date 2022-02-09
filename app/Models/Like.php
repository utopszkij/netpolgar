<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model {
    use HasFactory;
    protected $fillable = [
        'parent_type', 'parent', 'user_id', 'like_type'
    ];
    
	/**
	 * egy rekord beolvasása
	 * @param string $parent_type
	 * @param int $parentId
	 * @param int $userId
	 * @param string $likeType 'like'|'dislike'
	 * @return object|false
	 */
		
	public static function getRecord(string $parent_type, int $parentId, 
					int $userId, string $likeType) {
		return Like::where('parent_type','=',$parent_type)
			->where('parent','=',$parentId)
			->where('user_id','=',$userId)
			->where('like_type','=',$likeType)
			->first();
	}
    
    /**
     * egy rekord törlése
	 * @param string $parent_type
	 * @param int $parentId
	 * @param int $userId
	 * @param string $likeType 'like'|'dislike'
     * @return bool
     */ 
	public static function delRecord(string $parent_type, int $parentId,
		int $userId, string $likeType): bool {
        return Like::where('parent_type','=',$parent_type)
          ->where('parent','=',$parentId)
          ->where('user_id','=',$userId)
          ->where('like_type','=',$likeType)
          ->delete();
    }            
    
    /**
     * egy rekord felvitele
	 * @param string $parent_type
	 * @param int $parentId
	 * @param int $userId
	 * @param string $likeType 'like'|'dislike'
     * @return object
     */ 
	public static function createRecord(string $parent_type, int $parentId,
		int $userId, string $likeType) {
		return Like::create([
			"parent_type" => $parent_type,
			"parent" => $parentId,
			"user_id" => $userId,
			"like_type" => $likeType
		]);
    } 
    
    /**
     * like / dislike userek listájának lekérése
     * @param string $parenType
     * @param int $parentId
     * @param string $likeType 'like'|'dislike'
     * @return array
     */ 
    public static function getList(string $parentType, int  $parentId, 
		string $likeType) {  
        return Like::select('users.id', 'users.name', 
        'users.profile_photo_path', 'users.email')
        ->leftJoin('users','users.id','=','likes.user_id')
        ->where('parent_type', '=', $parentType)
        ->where('parent', '=',$parentId)
        ->where('like_type','=','like')
        ->orderBy('name')
        ->get();
    }    
    
	/**
	 * parent beolvasása, szükség esetén "name" kialakitása
	 * @param string $parentType
	 * @param int $parentId
	 * @return object
	 */ 
	public static function getParent(string $parentType, int $parentId)	{
        $parentTable = \DB::table($parentType);
        $parent = $parentTable->where('id','=',$parentId)->first();
        if (!$parent) {
            echo 'Ftaal error in likeInfo. parent not found'; exit();
        }
        // $parent -be kell 'name' !
        if ($parentType == 'members') {
            $parent->name = '?';
            $groupTable = \DB::table($parent->parent_type);
            $group = $groupTable->where('id','=',$parent->parent)->first();
            if ($group) {
                $parent->name = $group->name;
            }
            $userTable = \DB::table('users');
            $user = $userTable->where('id','=',$parent->user_id)->first();
            if ($user) {
                $parent->name .= ' / '.$user->name;
            }
        }
        if ($parentType == 'messages') {
            $parent->name = $parent->value;
        }
        return $parent;
     }   
    
    
}
