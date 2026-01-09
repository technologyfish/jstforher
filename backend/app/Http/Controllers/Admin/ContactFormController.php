<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactForm;
use Illuminate\Http\Request;

class ContactFormController extends Controller
{
    /**
     * 获取留资列表
     */
    public function index(Request $request)
    {
        $query = ContactForm::query();

        // 搜索
        if ($request->has('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('business_info', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%")
                  ->orWhere('subject', 'like', "%{$keyword}%")
                  ->orWhere('message', 'like', "%{$keyword}%");
            });
        }

        // 状态筛选
        if ($request->has('is_read')) {
            $query->where('is_read', $request->is_read);
        }

        // 分页
        $perPage = $request->get('per_page', 15);
        $contactForms = $query->orderBy('created_at', 'desc')
                             ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $contactForms
        ]);
    }

    /**
     * 获取单个留资
     */
    public function show($id)
    {
        $contactForm = ContactForm::findOrFail($id);
        
        // 标记为已读
        if (!$contactForm->is_read) {
            $contactForm->markAsRead();
        }

        return response()->json([
            'success' => true,
            'data' => $contactForm
        ]);
    }

    /**
     * 标记为已读
     */
    public function markAsRead($id)
    {
        $contactForm = ContactForm::findOrFail($id);
        $contactForm->markAsRead();

        return response()->json([
            'success' => true,
            'message' => '已标记为已读'
        ]);
    }

    /**
     * 批量标记为已读
     */
    public function batchMarkAsRead(Request $request)
    {
        $this->validate($request, [
            'ids' => 'required|array',
            'ids.*' => 'required|exists:contact_forms,id'
        ]);

        ContactForm::whereIn('id', $request->ids)->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => '批量标记成功'
        ]);
    }

    /**
     * 删除留资
     */
    public function destroy($id)
    {
        $contactForm = ContactForm::findOrFail($id);
        $contactForm->delete();

        return response()->json([
            'success' => true,
            'message' => '删除成功'
        ]);
    }
}


