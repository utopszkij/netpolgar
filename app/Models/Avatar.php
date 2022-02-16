<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avatar extends Model {
    public static function userAvatar(string $profile_photo_path, string $email): string {
        if ($profile_photo_path == '') {
            $result = 'https://gravatar.com/avatar/'.
                md5($email).
                '?d='.urlencode('https://www.pinpng.com/pngs/m/341-3415688_no-avatar-png-transparent-png.png');
        } else {
            $result = \URL::to('/storage/app/public').'/'.$value->profile_photo_path;
        }
        return $result;
    }
    
    public function otherAvatar(string $s): string {
        if ($s == '') {
            $result = \URL::to('/img/noimage.png');
        } else {
            if (substr($s,0,4) != 'http') {
                if (substr($s,0,1) == '/') {
                    $result = \URL::to('/').$s;
                } else  if (substr($s,0,2) == './') {
                    $result = \URL::to('/').substr($s,1,200);
                } else {
                    $result = \URL::to('/').'/'.$s;
                }
            } else {
                $result = $s;
            }
        }
        return $result;
    }
}