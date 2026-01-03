<?php

namespace App\Providers;

use App\Models\Admin;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // JWT token authentication
        $this->app['auth']->viaRequest('api', function ($request) {
            try {
                $token = $request->bearerToken();
                if (!$token) {
                    return null;
                }
                
                // 使用 JWT Auth 来验证 token 并获取用户
                $payload = app('tymon.jwt')->setRequest($request)->parseToken()->getPayload();
                
                if ($payload && isset($payload['sub'])) {
                    return Admin::find($payload['sub']);
                }
            } catch (\Exception $e) {
                // Token invalid, expired, etc.
                return null;
            }
            return null;
        });
    }
}

