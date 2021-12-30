<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'team_id', 
        'avatar', 'price', 'currency', 'vat', 'type', 'stock', 'unit', 'status'
    ];
    
    public static function emptyRecord() {
    	$result = JSON_decode('{
			"id":0,
			"name":"",
			"team_id":0,
			"description":"",
			"avatar":"/img/team.png",
			"status":"active",
			"price":0,
			"currency":"HUF",
			"vat":0,
			"type":"",
			"stock":0,
			"unit":"db",
			"created_at":"1900-01-01",
			"updated_at":"1900-01-01"
    	}');
    	return $result;
    }
    
    /**
     * like, dislike infók és memberCount meghatátozása
     * @param object $result
     * @param Team $team
     * @return void  $result -ot modosítja
     */
    public static function getLikeInfo(&$result, $project): void {
        // like, disLike, memberCount infok
        $user = \Auth::user();
        $t = \DB::table('likes');
        $result->likeCount = $t->where('parent_type','=','products')
        ->where('parent','=',$product->id)
        ->where('like_type','=','like')->count();
        $t = \DB::table('likes');
        $result->disLikeCount = $t->where('parent_type','=','product')
        ->where('parent','=',$product->id)
        ->where('like_type','=','dislike')->count();
        if ($user) {
            $t = \DB::table('likes');
            $result->userDisLiked = ($t->where('parent_type','=','product')
                ->where('parent','=',$product->id)
                ->where('like_type','=','dislike')
                ->where('user_id','=',$user->id)
                ->count() >= 1);
            $t = \DB::table('likes');
            $result->userLiked = ($t->where('parent_type','=','product')
                ->where('parent','=',$product->id)
                ->where('like_type','=','like')
                ->where('user_id','=',$user->id)
                ->count() >= 1);
	        $t = \DB::table('members');
	        $result->userAdmin = ($t->select('distinct user_id')
	        ->where('parent_type','=','teams')
	        ->where('parent','=',$product->team_id)
	        ->where('status','=','active')
	        ->where('user_id','=',$user->id)
	        ->where('rank','=','admin')
	        ->count() > 0);
        }
        $t = \DB::table('members');
        $result->memberCount = $t->select('distinct user_id')
        ->where('parent_type','=','teams')
        ->where('parent','=',$product->team_id)
        ->where('status','=','active')
        ->count();
    }
    
    
    public static function getInfo($product) {

		$result = JSON_decode('{
			"userLiked":false,
			"userDisLiked":false,
			"likeCount":0,
			"likeReq":0,
			"disLikeCount":0,
			"disLikeReq":0,
         "memberCount":0,
         "userAdmin":false,
         "usedCount":0,
         "evaulation":0
		}');
		Product::getLikeInfo($result, $product);
		$recs = \DB::select('select sum(orderitems.quatity) quantity
		from orderitems
		where product_id = '.$product->id.' and
		status = "success"');
		if count($recs) > 0) {
			$result->usedCount = $recs[0]->quantity;		
		}		
		$recs = \DB::select('select avg(evaluations.value) value
		from evaluations
		where product_id = '.$product->id);
		if count($recs) > 0) {
			$result->evaluation = $recs[0]->value;		
		}		
		return $result;
    }
    
    /**
     * like/dilike ellenörzés, ha szülséges status modosítás
     * @param string $teamId
     * @return void
     */
    public function checkStatus(string $teamId):void {
    }
}


