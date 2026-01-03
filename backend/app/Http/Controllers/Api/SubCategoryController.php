<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    /**
     * 获取产品册列表（前端用户接口）
     */
    public function index(Request $request)
    {
        $query = SubCategory::with(['category', 'articles'])
                           ->where('is_active', 1);

        // 按一级栏目筛选
        if ($request->has('category_id') && $request->input('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // 排序
        $query->orderBy('sort_order', 'asc')
              ->orderBy('created_at', 'desc');

        $subCategories = $query->get();

        return response()->json([
            'success' => true,
            'data' => $subCategories
        ]);
    }

    /**
     * 获取产品册详情
     */
    public function show($id)
    {
        $subCategory = SubCategory::with(['category', 'articles'])
                                  ->where('is_active', 1)
                                  ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $subCategory
        ]);
    }
}

