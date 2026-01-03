<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $table = 'sub_categories';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'cover_image',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'category_id' => 'integer'
    ];

    /**
     * 获取所属的一级栏目
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * 获取该二级栏目下的文章
     */
    public function articles()
    {
        return $this->hasMany(Article::class, 'sub_category_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order', 'asc');
    }
}


