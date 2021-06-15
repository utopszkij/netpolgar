<?php
/**
 * members kezelő osztály  FIGYELEM! két táblát is kezel: group_members, project_members
 */
namespace App\Models;

use Illuminate\Database;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Members extends Model {
    
    protected $primaryKey = 'id';
    protected $table = 'group_members'; // vagy 'project_members' 

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    ];
    
   
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * browser adat lekérő
     * @param string $parentType
     * @param int $parent_id
     * @param int $offset
     * @param int $limit
     * @param string $order
     * @param string $orderDir
     * @param string $filterStr
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list(string $parentType, int $parent_id,
        int $offset, int $limit, string $order, string $orderDir, string $filterStr) {
        $table = DB::table($parentType.'_members');
        $table->leftJoin('users','users.id','=',$parentType.'_members.user_id');
        $table->select(DB::raw('group_concat(concat(`status`," ",`rank`)) as ranks, 
            users.id as user_id,
            users.profile_photo_path, 
            users.name, 
            users.current_team_id'));
        $table->where('status','<>','deleted');
        $table->where($parentType.'_id','=',$parent_id);
        if ($filterStr != '') {
            $table->where('groups.name','like','%'.$filterStr.'%');
        }
        $table->groupBy('users.id');
        $table->offset($offset);
        $table->orderBy($order,$orderDir);
        $result = $table->paginate($limit);
        // echo $table->toSql(); 
        return $result;
    }
    
}

