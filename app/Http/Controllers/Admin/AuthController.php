<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->sendError('Unauthorized', 'Email atau password salah');
        }

        return $this->sendResponse([
            'id' => Auth::user()->id,
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'role' => Auth::user()->roles,
            'token' => $token,
            // 'admin' => Auth::user()
        ], 'Login berhasil');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $admin->roles()->attach($request->role_id);

        return $this->sendResponse($admin, 'Register berhasil');
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return $this->sendResponse(null, 'Logout berhasil');
        } catch (\Exception $e) {
            return $this->sendError('Gagal logout', $e->getMessage());
        }

    }

    public function me()
    {
        return $this->sendResponse(Auth::user(), 'Data user');
    }
}
