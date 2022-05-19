<?php

namespace App\Models;

use Illuminate\Database;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Messages extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'parent_id',
        'parent_type',
        'type'
    ];
    
   
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function init() {
        $result = JSON_decode('{
            "parent_type":"",
            "parent_id".0,
            "user_id":0,
            "type":"",
            "value":"",
            "created_at":"'.date('Y-m-d').'",
            "updated_at":"",
            "moderatorinfo":"",
            "moderated_by":"",
        }');
        $result->config = $config;
        return $result;
    }
    
}

