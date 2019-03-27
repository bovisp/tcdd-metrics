<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\{RegisterFormRequest, LoginFormRequest};
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{    

    public function register(RegisterFormRequest $request) {

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        dd($user);
        $token = JWTAuth::attempt($request->only('email', 'password'));

        return response()->json([
            'data' => $user,
            'meta' => [
                'token' => $token
            ]
        ], 200);
    }

    public function login(LoginFormRequest $request) {
        try {
            if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'errors' => [
                        'root' => 'Could not sign you in with those details.'
                    ]
                    ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'errors' => [
                    'root' => 'Failed.'
                ]
                ], $e->getStatusCode());
        }

        return response()->json([
            'data' => $request->user(),
            'meta' => [
                'token' => $token
            ]
        ], 200);

    }

    public function logout()
	{
	    JWTAuth::invalidate();
	    
	    return response([
            'status' => 'success',
            'msg' => 'Logged out Successfully.'
        ], 200);
	}

    public function user(Request $request) {
        return response()->json([
            'data' => $request->user(),
        ]);
    }
}
