<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AprioriRule extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['support' => 'decimal:6', 'confidence' => 'decimal:6', 'lift' => 'decimal:6'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(AprioriRuleItem::class);
    }
}
