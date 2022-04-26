<?php

namespace App\Actions\Jetstream;

use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     *
     * @param  mixed  $user
     * @return void
     */
    public function delete($user)
    {
        $user->deleteProfilePhoto();
        $user->tokens->each->delete();

        // $user->delete();   csak logikai törlés a megngedett
        try {
            $user->update(["name" => "deleted".$user->id,
            "email" => "deleted".$user->id."@deleted.com",
            "password" => "psw".rand(100000,999999)]);

            \DB::table('members')
            ->where('user_id','=',$user->id)
            ->delete();
         } catch (\Illuminate\Database\QueryException $exception) {
            echo JSON_encode($exception->errorInfo); exit();
        }

    }
}
