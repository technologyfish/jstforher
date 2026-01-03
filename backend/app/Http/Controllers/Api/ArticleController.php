<?php

namespace App\Http\Controllers\Api;

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
        $query = Article::where('is_active', true)
                       ->with(['category', 'subCategory']);

        // 按一级栏目筛选
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 按二级栏目筛选
        if ($request->has('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }

        // 按栏目slug筛选
        if ($request->has('category_slug')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category_slug);
            });
        }

        if ($request->has('sub_category_slug')) {
            $query->whereHas('subCategory', function($q) use ($request) {
                $q->where('slug', $request->sub_category_slug);
            });
        }

        $articles = $query->orderBy('sort_order', 'asc')
                         ->orderBy('created_at', 'desc')
                         ->get();

        return response()->json([
            'success' => true,
            'data' => $articles
        ]);
    }

    /**
     * 获取单个文章详情
     */
    public function show($slug)
    {
        $article = Article::where('slug', $slug)
                         ->where('is_active', true)
                         ->with(['category', 'subCategory'])
                         ->firstOrFail();

        // 增加浏览次数
        $article->incrementViewCount();

        return response()->json([
            'success' => true,
            'data' => $article
        ]);
    }
}


