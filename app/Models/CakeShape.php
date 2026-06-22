<?php
declare(strict_types=1);
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CakeShape extends Model { protected $fillable=['name','price_adjustment','is_active','sort_order']; protected function casts(): array { return ['price_adjustment'=>'decimal:2','is_active'=>'boolean']; } }
