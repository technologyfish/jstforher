<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * 获取栏目列表
     */
    public function index(Request $request)
    {
        $query = Category::query();

        // 搜索
        if ($request->has('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('slug', 'like', "%{$keyword}%");
            });
        }

        // 状态筛选
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $categories = $query->orderBy('sort_order', 'asc')
                           ->orderBy('created_at', 'desc')
                           ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * 创建栏目
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        // 只提取允许的字段
        $data = $request->only([
            'name',
            'description',
            'sort_order',
            'is_active'
        ]);
        
        // 自动生成唯一的 slug
        $data['slug'] = 'cat_' . time() . '_' . rand(1000, 9999);

        $category = Category::create($data);

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => '栏目创建成功'
        ], 201);
    }

    /**
     * 获取单个栏目
     */
    public function show($id)
    {
        $category = Category::with('subCategories')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * 更新栏目
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $this->validate($request, [
            'name' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        // 只提取允许更新的字段
        $data = $request->only([
            'name',
            'description',
            'sort_order',
            'is_active'
        ]);

        $category->update($data);

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => '栏目更新成功'
        ]);
    }

    /**
     * 删除栏目
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => '栏目删除成功'
        ]);
    }
}

