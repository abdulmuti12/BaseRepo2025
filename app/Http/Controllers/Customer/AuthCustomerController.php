<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthCustomerController extends BaseController
{
    public function login(Request $request)
    {

        // dd(33);
        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('customer')->attempt($credentials)) {
            return response()->json(['success'=>false,'message' => 'Invalid credentials'], 401);
        }


        $user = Auth::guard('customer')->user();

        return $this->sendResponse([
            "id" => $user->id,
            "username" => $user->username,
            "email" => $user->email,
            "token" => $token
        ], 'Login Success');

        // $credentials = $request->only(['email', 'password']);

        // if (!$token = Auth::guard('customer')->attempt($credentials)) {
        //     return $this->sendError('Unauthorized', 'Email atau password salah');
        // }

        // $user = Auth::guard('customers')->user();
        // dd($user);

        // return $this->sendResponse([
        //     'id' => $user->id,
        //     'name' => $user->name,
        //     'email' => $user->email,
        //     'role' => 'customer',
        //     'token' => $token,
        // ], 'Login customer berhasil');
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
