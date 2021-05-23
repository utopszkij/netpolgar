<?php

namespace App\Models;

use Illuminate\Database;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Group_members extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'group_id',
        'status',
        'rank'
    ];
    
   
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function init($parent_id) {
        $result = JSON_decode('{
            "user_id":0,
            "group_id":'.$parent_id.',
            "status":"proposal",
            "rank":"member",
            "created_at":"'.date('Y-m-d').'",
            "created_by":"'.\Auth::user()->id.'",
            "updated_at":""
        }');
        $result->config = $config;
        return $result;
    }
    
    public function list(int $group_id, 
        int $offset, int $limit, string $order, string $orderDir, string $filterStr) {
        $table = DB::table('group_members');
        $table->leftJoin('users','users.id','=','group_id');
        $table->leftJoin('groups','groups.id','=','group_id');
        $table->where('group_members.group_id','=',$group_id);
        $table->where('group_members.status','<>','deleted');
        if ($filterStr != '') {
            $table->where('groups.name','like','%'.$filterStr.'%');
        }
        $table->offset($offset);
        $table->orderBy($order,$orderDir);
        $result = $table->paginate($limit);
        // echo $table->toSql(); 
        return $result;
    }
    
}

