<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    protected $fillable = [
        'from_type', 'from', 'target_type', 'target', 'status', 'value', 
        'comment', 'info'
    ];
    
    
	/**
	 * egyenleg lekérdezés
	 * @param string $actorType
	 * @param int $actorId
	 * @return float;
	 */ 
    public static function getBallance(string $actorType, int $actorId): float {
		$result = 0;
		
		if ($actorType == 'users') {
			// van már kezdeti feltötlés?
			$rec = Account::where('target_type','=',$actorType)
					->where('target','=',$actorId)
					->where('info','=','startInit')
					->first();
			if (!$rec) {
				// még nincs, most létrehozzuk
				Account::create(["from_type" => '',
				"from" => 0,
				"target_type" => $actorType,
				"target" => $actorId,
				"status" => "",
				"value" => env('AccountStart'),
				"info" => "startInit",
				"comment" => "Kezdeti feltöltés"
				]);
			}		
		}
		// egyenleg számítás
		$recs = \DB::select('
		select sum(value) value
		from accounts
		where target_type = "'.$actorType.'" and
			target = '.$actorId.' and
			status = ""
		');
		if (count($recs) > 0) {
			$result = $result + $recs[0]->value;
		}
		$recs = \DB::select('
		select sum(value) value
		from accounts
		where from_type = "'.$actorType.'" and
			`from` = '.$actorId.' and
			(status = "" or status = "allocated")
		');
		if (count($recs) > 0) {
			$result = $result - $recs[0]->value;
		}
		
		return $result;
	}
	
	/**
	 * van elég NTC a számlán $value terheléshez?
	 * @param string $actorType
	 * @param int $actorId
	 * @param float $value
	 * @return bool
	 */ 
	public static function checkBallance(string $actorType, 
		int $actorId, float $value): bool {
		return true;
	}
	
	public static function getData($actorType, $actorId, $pageSize) {
		$table1 = \DB::table('accounts')
		->where('from_type','=',$actorType)
		->where('from','=',$actorId)
		->where('status','=','');

		$table2 = \DB::table('accounts')
		->where('target_type','=',$actorType)
		->where('target','=',$actorId)
		->where('status','=','')
		->union($table1)
		->orderBy('created_at');
		
		return $table2->paginate($pageSize);
		 
	}
	
	/**
	 * user team tag, vagy actor ?
	 * @param string $actorType
	 * @param int $actorId
	 * @param int $userId 
	 * @return bool
	 */ 
	public static function userMember(string $actorType, int $actorId, 
		int $userId): bool {
		return (\DB::table('members')
					->where('user_id','=',$userId)
					->where('parent_type','=','teams')
					->whereIn('rank',['member','admin'])
					->where('status','=','active')
					->count() > 0);
			
	} 

	/**
	 * user team admin, vagy actor ?
	 * @param string $actorType
	 * @param int $actorId
	 * @param int $userId 
	 * @return bool
	 */ 
	public static function userAdmin(string $actorType, int $actorId, 
		int $userId): bool {
		return (\DB::table('members')
					->where('user_id','=',$userId)
					->where('parent_type','=','teams')
					->where('rank','=','admin')
					->where('status','=','active')
					->count() > 0);
	} 


}
