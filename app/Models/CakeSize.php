<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CakeSize extends Model
{
    protected $fillable = ['name', 'base_price', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['base_price' => 'decimal:2', 'is_active' => 'boolean'];
    }
}
