<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['ordered_at' => 'datetime', 'pickup_at' => 'datetime', 'completed_at' => 'datetime', 'status' => OrderStatus::class, 'source' => OrderSource::class, 'delivery_fee' => 'decimal:2', 'cake_price' => 'decimal:2', 'additional_items_total' => 'decimal:2', 'grand_total' => 'decimal:2'];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function additionalItems(): HasMany
    {
        return $this->hasMany(OrderAdditionalItem::class);
    }

    public function whatsappLogs(): HasMany
    {
        return $this->hasMany(WhatsappLog::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
}
