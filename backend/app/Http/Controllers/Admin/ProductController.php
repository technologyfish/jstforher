<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * 获取产品列表
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // 搜索
        if ($request->has('keyword') && $request->input('keyword')) {
            $keyword = $request->input('keyword');
            $query->where('name', 'like', "%{$keyword}%");
        }

        // 状态筛选
        if ($request->has('is_active') && $request->input('is_active') !== null && $request->input('is_active') !== '') {
            $query->where('is_active', (int)$request->input('is_active'));
        }

        // 排序
        $query->orderBy('sort_order', 'asc')
              ->orderBy('created_at', 'desc');

        // 分页
        $perPage = $request->input('per_page', 15);
        $products = $query->paginate($perPage);

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
        $product = Product::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * 创建产品
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        // 只提取允许的字段
        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'cover_image' => $request->input('cover_image'),
            'sort_order' => $request->input('sort_order', 0),
            'is_active' => $request->input('is_active', true)
        ];

        $product = Product::create($data);

        return response()->json([
            'success' => true,
            'message' => '创建成功',
            'data' => $product
        ], 201);
    }

    /**
     * 更新产品
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $this->validate($request, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean'
        ]);

        // 只提取允许更新的字段
        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'cover_image' => $request->input('cover_image'),
            'sort_order' => $request->input('sort_order', 0),
            'is_active' => $request->input('is_active', true)
        ];

        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => '更新成功',
            'data' => $product
        ]);
    }

    /**
     * 删除产品
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => '删除成功'
        ]);
    }
}

