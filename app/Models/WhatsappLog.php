<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WhatsappStatus;
use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['status' => WhatsappStatus::class, 'provider_response' => 'array', 'sent_at' => 'datetime', 'failed_at' => 'datetime'];
    }
}
