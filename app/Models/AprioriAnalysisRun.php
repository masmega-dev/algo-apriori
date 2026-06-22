<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AprioriAnalysisRun extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['date_from' => 'date', 'date_to' => 'date', 'executed_at' => 'datetime', 'minimum_support' => 'decimal:6', 'minimum_confidence' => 'decimal:6', 'minimum_lift' => 'decimal:6'];
    }

    public function rules(): HasMany
    {
        return $this->hasMany(AprioriRule::class, 'analysis_run_id');
    }
}
