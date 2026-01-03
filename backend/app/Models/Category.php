<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * 获取该栏目下的二级栏目
     */
    public function subCategories()
    {
        return $this->hasMany(SubCategory::class, 'category_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order', 'asc');
    }

    /**
     * 获取该栏目下的文章
     */
    public function articles()
    {
        return $this->hasMany(Article::class, 'category_id')
                    ->where('is_active', true)
                    ->orderBy('sort_order', 'asc');
    }
}


