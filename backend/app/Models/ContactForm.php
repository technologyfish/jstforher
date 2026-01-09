<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactForm extends Model
{
    protected $table = 'contact_forms';

    protected $fillable = [
        'name',
        'email',
        'business_info',
        'phone',
        'subject',
        'message',
        'ip_address',
        'user_agent',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean'
    ];

    /**
     * 标记为已读
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
}


