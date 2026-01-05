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

        // 分页处理
        if ($request->has('page') && $request->input('page')) {
            $perPage = $request->input('per_page', 9); // 默认每页9条
            $subCategories = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $subCategories->items(),
                'pagination' => [
                    'total' => $subCategories->total(),
                    'per_page' => $subCategories->perPage(),
                    'current_page' => $subCategories->currentPage(),
                    'last_page' => $subCategories->lastPage(),
                    'from' => $subCategories->firstItem(),
                    'to' => $subCategories->lastItem(),
                    'has_more' => $subCategories->hasMorePages()
                ]
            ]);
        }

        // 不分页，返回所有数据
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

