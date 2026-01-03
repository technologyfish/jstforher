<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    /**
     * 获取二级栏目列表
     */
    public function index(Request $request)
    {
        $query = SubCategory::with('category');

        // 按一级栏目筛选
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

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

        $subCategories = $query->orderBy('sort_order', 'asc')
                              ->orderBy('created_at', 'desc')
                              ->get();

        return response()->json([
            'success' => true,
            'data' => $subCategories
        ]);
    }

    /**
     * 创建二级栏目
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        // 只提取允许的字段，过滤掉 id、slug、created_at、updated_at 等
        $data = $request->only([
            'category_id',
            'name',
            'description',
            'cover_image',
            'sort_order',
            'is_active'
        ]);
        
        // 自动生成唯一的 slug
        $data['slug'] = 'subcat_' . time() . '_' . rand(1000, 9999);

        $subCategory = SubCategory::create($data);
        $subCategory->load('category');

        return response()->json([
            'success' => true,
            'data' => $subCategory,
            'message' => '二级栏目创建成功'
        ], 201);
    }

    /**
     * 获取单个二级栏目
     */
    public function show($id)
    {
        $subCategory = SubCategory::with(['category', 'articles'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $subCategory
        ]);
    }

    /**
     * 更新二级栏目
     */
    public function update(Request $request, $id)
    {
        $subCategory = SubCategory::findOrFail($id);

        $this->validate($request, [
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        // 只提取允许更新的字段，过滤掉 id、slug、created_at、updated_at、category 等
        $data = $request->only([
            'category_id',
            'name',
            'description',
            'cover_image',
            'sort_order',
            'is_active'
        ]);

        $subCategory->update($data);
        $subCategory->load('category');

        return response()->json([
            'success' => true,
            'data' => $subCategory,
            'message' => '二级栏目更新成功'
        ]);
    }

    /**
     * 删除二级栏目
     */
    public function destroy($id)
    {
        $subCategory = SubCategory::findOrFail($id);
        $subCategory->delete();

        return response()->json([
            'success' => true,
            'message' => '二级栏目删除成功'
        ]);
    }
}

