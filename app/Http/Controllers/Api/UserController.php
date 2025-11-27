<?php

namespace App\Http\Controllers\Api;

use App\Models\User;

class UserController extends Controller
{
    public function show($id)
    {
        $user = User::with('posts')->findOrFail($id);
        return response()->json($user);
    }
}
