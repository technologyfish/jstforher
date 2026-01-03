<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * 获取所有启用的一级栏目
     */
    public function index()
    {
        $categories = Category::where('is_active', true)
                             ->with(['subCategories' => function($query) {
                                 $query->where('is_active', true);
                             }])
                             ->orderBy('sort_order', 'asc')
                             ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * 获取单个栏目详情
     */
    public function show($slug)
    {
        $category = Category::where('slug', $slug)
                           ->where('is_active', true)
                           ->with(['subCategories' => function($query) {
                               $query->where('is_active', true);
                           }])
                           ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }
}


