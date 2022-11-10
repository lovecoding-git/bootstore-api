<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{

    public function __construct()
    {
        //API middleware setup except login and register function
        $this->middleware('jwt.verify', ['except' => ['login','register']]);
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);
            $credentials = $request->only('username', 'password');

            $token = Auth::attempt($credentials);
            if (!$token) {
                return response()->preferredFormat([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                ]);
            }

            $user = Auth::user();
            return response()->preferredFormat([
                'status' => 'success',
                'user' => $user,
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
        }catch(\Exception $e)
        {
            return response()->preferredFormat([
                'status' => 'false',
                'message' => $e->getMessage(),
            ]);
        }

    }


    public function register(Request $request){
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|min:4|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = Auth::login($user);

            return response()->preferredFormat([
                'status' => 'success',
                'message' => 'User created successfully',
                'user' => $user->toArray(),
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
        }catch(\Exception $e)
        {
            return response()->preferredFormat([
                'status' => 'false',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function logout()
    {
        try {
            Auth::logout();
            return response()->preferredFormat([
                'status' => 'success',
                'message' => 'Successfully logged out',
            ]);
        }catch(\Exception $e)
        {
            return response()->preferredFormat([
                'status' => 'false',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function refresh()
    {
        try {
            return response()->preferredFormat([
                'status' => 'success',
                'user' => Auth::user()->toArray(),
                'authorization' => [
                    'token' => Auth::refresh(),
                    'type' => 'bearer',
                ]
            ]);
        }catch(\Exception $e)
        {
            return response()->preferredFormat([
                'status' => 'false',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
