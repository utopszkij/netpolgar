<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    use HasFactory;
    protected $fillable = [
        'parent_type', 'parent', 'user_id', 'like_type'
    ];
    
}
