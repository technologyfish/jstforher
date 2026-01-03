<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscription extends Model
{
    protected $table = 'newsletter_subscriptions';

    protected $fillable = [
        'email',
        'status',
        'admin_note',
        'ip_address',
        'user_agent',
        'processed_at',
        'processed_by'
    ];

    protected $casts = [
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    // 状态常量
    const STATUS_PENDING = 0;
    const STATUS_PROCESSED = 1;

    // 关联处理人
    public function processedBy()
    {
        return $this->belongsTo(Admin::class, 'processed_by');
    }

    // 获取状态文本
    public function getStatusTextAttribute()
    {
        return $this->status === self::STATUS_PROCESSED ? '已处理' : '未处理';
    }

    // 作用域：未处理
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // 作用域：已处理
    public function scopeProcessed($query)
    {
        return $query->where('status', self::STATUS_PROCESSED);
    }
}

