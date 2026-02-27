<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactForm;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * 提交联系表单
     */
    public function submit(Request $request)
    {
        $this->validate($request, [
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'email'             => 'required|email|max:100',
            'business_info'     => 'nullable|string|max:255',
            'location'          => 'nullable|string|max:200',
            'estimated_quantity' => 'nullable|string|max:100',
            'message'           => 'nullable|string',
        ]);

        $data = $request->only([
            'first_name', 'last_name', 'email',
            'business_info', 'location', 'estimated_quantity', 'message',
        ]);

        // 同时保留组合 name 字段（向后兼容）
        $data['name']       = trim($data['first_name'] . ' ' . $data['last_name']);
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = $request->header('User-Agent');
        $data['message']    = $data['message'] ?? '';

        $contactForm = ContactForm::create($data);

        // TODO: 发送邮件通知管理员

        return response()->json([
            'success' => true,
            'message' => '提交成功，我们会尽快与您联系',
            'data'    => $contactForm,
        ], 201);
    }
}
