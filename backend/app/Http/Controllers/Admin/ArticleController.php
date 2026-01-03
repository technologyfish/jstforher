<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * 获取文章列表
     */
    public function index(Request $request)
    {
        $query = Article::with(['category', 'subCategory']);

        // 按一级栏目筛选
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 按二级栏目筛选
        if ($request->has('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }

        // 搜索
        if ($request->has('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('slug', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // 状态筛选
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // 分页
        $perPage = $request->get('per_page', 15);
        $articles = $query->orderBy('sort_order', 'asc')
                         ->orderBy('created_at', 'desc')
                         ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $articles
        ]);
    }

    /**
     * 创建文章
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'nullable|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'title' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200|unique:articles,slug',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'images' => 'nullable|array',
            'cover_image' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        // 只提取允许的字段
        $data = $request->only([
            'category_id',
            'sub_category_id',
            'title',
            'slug',
            'description',
            'content',
            'images',
            'cover_image',
            'sort_order',
            'is_active'
        ]);
        
        // 如果没有提供 slug，自动生成
        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['title']);
        }

        $article = Article::create($data);
        $article->load(['category', 'subCategory']);

        return response()->json([
            'success' => true,
            'data' => $article,
            'message' => '文章创建成功'
        ], 201);
    }

    /**
     * 获取单个文章
     */
    public function show($id)
    {
        $article = Article::with(['category', 'subCategory'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $article
        ]);
    }

    /**
     * 更新文章
     */
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $this->validate($request, [
            'category_id' => 'nullable|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'title' => 'nullable|string|max:200',
            'slug' => 'nullable|string|max:200|unique:articles,slug,' . $id,
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'images' => 'nullable|array',
            'cover_image' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean'
        ]);

        // 只提取允许更新的字段
        $data = $request->only([
            'category_id',
            'sub_category_id',
            'title',
            'slug',
            'description',
            'content',
            'images',
            'cover_image',
            'sort_order',
            'is_active'
        ]);

        $article->update($data);
        $article->load(['category', 'subCategory']);

        return response()->json([
            'success' => true,
            'data' => $article,
            'message' => '文章更新成功'
        ]);
    }

    /**
     * 删除文章
     */
    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();

        return response()->json([
            'success' => true,
            'message' => '文章删除成功'
        ]);
    }
}

