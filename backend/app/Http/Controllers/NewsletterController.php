<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsletterSubscription;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    /**
     * 提交订阅
     */
    public function subscribe(Request $request)
    {
        // 验证
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255'
        ], [
            'email.required' => '请输入邮箱地址',
            'email.email' => '请输入有效的邮箱地址',
            'email.max' => '邮箱地址过长'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => $validator->errors()->first(),
                'data' => null
            ], 400);
        }

        // 检查是否已经订阅
        $existingSubscription = NewsletterSubscription::where('email', $request->email)->first();
        
        if ($existingSubscription) {
            return response()->json([
                'code' => 400,
                'message' => 'This email is already subscribed',
                'data' => null
            ], 400);
        }

        try {
            // 创建订阅记录
            $subscription = NewsletterSubscription::create([
                'email' => $request->email,
                'status' => NewsletterSubscription::STATUS_PENDING,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'code' => 200,
                'message' => 'Thank you for subscribing!',
                'data' => $subscription
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Failed to subscribe. Please try again.',
                'data' => null
            ], 500);
        }
    }
}

