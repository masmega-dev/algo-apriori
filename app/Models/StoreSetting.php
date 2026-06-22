<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StoreSetting extends Model { protected $guarded=[]; protected function casts(): array{return ['public_order_enabled'=>'boolean'];} }
