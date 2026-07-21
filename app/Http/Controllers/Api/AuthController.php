<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($credentials['username'] === 'admin' && $credentials['password'] === 'admin123') {
            return response()->json([
                'success' => true,
                'token' => 'token-admin-rt06-secret',
                'message' => 'Login berhasil'
            ]);
        }

        return response()->json(['message' => 'Username atau Password salah!'], 401);
    }
}
