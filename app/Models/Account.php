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


}
