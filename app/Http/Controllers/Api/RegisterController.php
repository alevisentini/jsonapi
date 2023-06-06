<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\TokenResponse;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:sanctum');
    }

    public function __invoke(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|max:255|confirmed',
            'device_name' => 'required|string|max:255'
        ]); 

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return new TokenResponse($user);
    }

}
