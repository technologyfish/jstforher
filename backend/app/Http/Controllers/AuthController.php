<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * 管理员登录
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $admin = Admin::where('username', $request->username)
                     ->where('is_active', true)
                     ->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => '用户名或密码错误'
            ], 401);
        }

        $token = JWTAuth::fromUser($admin);
        $admin->updateLastLogin();

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'admin' => [
                    'id' => $admin->id,
                    'username' => $admin->username,
                    'name' => $admin->name,
                    'email' => $admin->email
                ]
            ]
        ]);
    }

    /**
     * 获取当前登录管理员信息
     */
    public function me()
    {
        $admin = auth()->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $admin->id,
                'username' => $admin->username,
                'name' => $admin->name,
                'email' => $admin->email
            ]
        ]);
    }

    /**
     * 登出
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'success' => true,
            'message' => '登出成功'
        ]);
    }
}


