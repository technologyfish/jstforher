<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewsletterSubscription;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class NewsletterSubscriptionController extends Controller
{
    /**
     * 获取订阅列表
     */
    public function index(Request $request)
    {
        // 不使用关联查询，避免潜在的外键问题
        $query = NewsletterSubscription::query();

        // 筛选状态
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // 搜索邮箱
        if ($request->has('email') && $request->email) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        // 排序
        $sortField = $request->get('sort_field', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        // 分页
        $perPage = $request->get('per_page', 15);
        $subscriptions = $query->paginate($perPage);

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $subscriptions
        ]);
    }

    /**
     * 获取订阅详情
     */
    public function show($id)
    {
        $subscription = NewsletterSubscription::find($id);

        if (!$subscription) {
            return response()->json([
                'code' => 404,
                'message' => '订阅记录不存在',
                'data' => null
            ], 404);
        }

        return response()->json([
            'code' => 200,
            'message' => 'success',
            'data' => $subscription
        ]);
    }

    /**
     * 更新处理状态
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:0,1',
            'admin_note' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => $validator->errors()->first(),
                'data' => null
            ], 400);
        }

        $subscription = NewsletterSubscription::find($id);

        if (!$subscription) {
            return response()->json([
                'code' => 404,
                'message' => '订阅记录不存在',
                'data' => null
            ], 404);
        }

        try {
            $subscription->status = (int)$request->status;
            
            if ($request->has('admin_note')) {
                $subscription->admin_note = $request->admin_note;
            }

            // 如果标记为已处理，记录处理时间和处理人
            if ((int)$request->status === NewsletterSubscription::STATUS_PROCESSED) {
                $subscription->processed_at = Carbon::now();
                // 从JWT token中获取管理员ID
                try {
                    $admin = auth()->user();
                    if ($admin) {
                        $subscription->processed_by = $admin->id;
                    }
                } catch (\Exception $authException) {
                    // 如果获取用户失败，记录日志但不影响主流程
                    \Log::warning('Failed to get auth user: ' . $authException->getMessage());
                }
            }

            $subscription->save();

            return response()->json([
                'code' => 200,
                'message' => '更新成功',
                'data' => $subscription
            ]);
        } catch (\Exception $e) {
            // 记录详细错误日志
            \Log::error('Newsletter subscription update failed', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'code' => 500,
                'message' => '更新失败: ' . $e->getMessage(),
                'data' => [
                    'error_line' => $e->getLine(),
                    'error_file' => basename($e->getFile())
                ]
            ], 500);
        }
    }

    /**
     * 批量删除
     */
    public function batchDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:newsletter_subscriptions,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'message' => $validator->errors()->first(),
                'data' => null
            ], 400);
        }

        try {
            NewsletterSubscription::whereIn('id', $request->ids)->delete();

            return response()->json([
                'code' => 200,
                'message' => '删除成功',
                'data' => null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '批量删除失败: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * 删除单个订阅
     */
    public function destroy($id)
    {
        $subscription = NewsletterSubscription::find($id);

        if (!$subscription) {
            return response()->json([
                'code' => 404,
                'message' => '订阅记录不存在',
                'data' => null
            ], 404);
        }

        try {
            $subscription->delete();

            return response()->json([
                'code' => 200,
                'message' => '删除成功',
                'data' => null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => '删除失败: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * 导出订阅列表（CSV）
     */
    public function export(Request $request)
    {
        $query = NewsletterSubscription::query();

        // 筛选
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->get();

        $filename = 'newsletter_subscriptions_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($subscriptions) {
            $file = fopen('php://output', 'w');
            
            // 添加 BOM 以支持 Excel 正确显示中文
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // 表头
            fputcsv($file, ['ID', 'Email', 'Status', 'Admin Note', 'IP Address', 'Created At', 'Processed At']);
            
            // 数据
            foreach ($subscriptions as $subscription) {
                fputcsv($file, [
                    $subscription->id,
                    $subscription->email,
                    $subscription->status_text,
                    $subscription->admin_note,
                    $subscription->ip_address,
                    $subscription->created_at,
                    $subscription->processed_at,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

