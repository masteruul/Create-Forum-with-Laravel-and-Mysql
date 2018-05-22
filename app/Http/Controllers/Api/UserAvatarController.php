<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class UserAvatarController extends Controller
{
    public function store()
    {
        request()->validate([
            'avatar'=>['required','image']
        ]);
        
        $storage = '/storage/';
        auth()->user()->update([
            'avatar_path' => $storage.request()->file('avatar')->store('avatars','public')
        ]);

        return response([],204);
    }
}
