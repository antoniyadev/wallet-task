<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING   = 'pending_payment';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED  = 'refunded';

    public const STATUSES = [
        self::STATUS_PENDING   => 'Pending Payment',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_CANCELLED => 'Cancelled',
        self::STATUS_REFUNDED  => 'Refunded',
    ];

    public const STATUS_COLORS = [
        self::STATUS_PENDING   => 'info',
        self::STATUS_COMPLETED => 'success',
        self::STATUS_CANCELLED => 'danger',
        self::STATUS_REFUNDED  => 'secondary',
    ];

    protected $fillable = ['user_id', 'title', 'amount', 'status', 'description'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
