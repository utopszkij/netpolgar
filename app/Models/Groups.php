<?php

namespace App\Models;

use Illuminate\Database;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Groups extends Model {
    
    protected $primaryKey = 'id';
    protected $table = 'groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];
    
   
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function init($parent_id) {
        $config = '{
"groupCloseVoks":"80%",
"subGroupActivateVoks":5,
"memberActivateVoks":2,
"memberExludeVoks":"80%",
"rankEnableVoks":"60%",
"rankDisableVoks":"80%",
"projectActivateVoks":5
}';
        $result = JSON_decode('{
            "id":0,
            "parent_id":'.$parent_id.',
            "name":"",
            "description":"",
            "avatar":"",
            "status":"proposal",
            "config":"{}",
            "created_at":"'.date('Y-m-d').'",
            "created_by":"'.\Auth::user()->id.'",
            "updated_at":"",
            "activated_at":"",
            "closed_at":""
        }');
        $result->config = $config;
        return $result;
    }
    
    public function list(int $parent_id, int $member_id, int $admin_id,
        int $offset, int $limit, string $order, string $orderDir, string $filterStr) {
        $table = DB::table('groups');
        $table->leftJoin('group_members','group_members.group_id','=','groups.id');
        $table->leftJoin('projects','projects.group_id','=','groups.id');
        $table->select(DB::raw('groups.id,
            max(groups.avatar) as avatar, 
            max(groups.name) as name, 
            max(groups.status) as status,
            count(group_members.id) as member_count, 
            count(projects.id) as project_count'));
        $table->where('groups.status','<>','deleted');
        $table->where('groups.parent_id','=',$parent_id);
        if ($member_id > 0) {
            $table->where('group_members.id','=',$member_id);
            $table->where('group_members.status','=','active');
        }
        if ($admin_id > 0) {
            $table->where('group_members.id','=',$member_id);
            $table->where('group_members.status','=','active');
            $table->where('group_members.rank','=','admin');
        }
        if ($filterStr != '') {
            $table->where('groups.name','like','%'.$filterStr.'%');
        }
        $table->groupBy('groups.id');
        $table->offset($offset);
        $table->orderBy($order,$orderDir);
        $result = $table->paginate($limit);
        // echo $table->toSql(); 
        return $result;
    }
    
}

