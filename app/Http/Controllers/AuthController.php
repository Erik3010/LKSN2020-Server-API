<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if ($token = Auth::attempt($request->only(['username', 'password']))) {
            return $this->respondToken($token);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function logout()
    {
        if (!Auth::user())
            return response()->json(['message' => 'Unauthorized'], 422);

        Auth::logout();

        return response()->json(['message' => 'successfully logged out'], 200);
    }

    public function me()
    {
        return response()->json(Auth::user());
    }

    public function reset(Request $request)
    {
        $check = Hash::check($request->old_password, Auth::user()->password);
        if (!$check)
            return response()->json(['message' => 'old password did not match'], 422);

        Auth::user()->update(['password' => bcrypt($request->new_password)]);

        return response()->json(['message' => 'reset success, user logged out'], 200);
    }

    public function respondToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }
}
