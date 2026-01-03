<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * 获取产品列表（前端用户接口）
     */
    public function index(Request $request)
    {
        $query = Product::where('is_active', 1);

        // 排序
        $query->orderBy('sort_order', 'asc')
              ->orderBy('created_at', 'desc');

        // 可选分页
        if ($request->has('per_page')) {
            $perPage = $request->input('per_page', 10);
            $products = $query->paginate($perPage);
        } else {
            // 不分页，返回所有启用的产品
            $products = $query->get();
        }

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * 获取产品详情
     */
    public function show($id)
    {
        $product = Product::where('is_active', 1)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }
}

