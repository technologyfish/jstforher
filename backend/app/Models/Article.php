<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';

    protected $fillable = [
        'category_id',
        'sub_category_id',
        'title',
        'slug',
        'description',
        'content',
        'images',
        'cover_image',
        'sort_order',
        'is_active',
        'view_count'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'view_count' => 'integer',
        'category_id' => 'integer',
        'sub_category_id' => 'integer',
        'images' => 'array'
    ];

    /**
     * 获取所属的一级栏目
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * 获取所属的二级栏目
     */
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    /**
     * 增加浏览次数
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }
}


