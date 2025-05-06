<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /** @use HasFactory<\Database\Factories\NotificationFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_code',
        'title',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_code', 'order_code');
    }
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
