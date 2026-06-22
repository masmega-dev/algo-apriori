<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return ['customer_name' => ['required', 'string', 'max:120'], 'customer_phone' => ['required', 'string', 'max:20'], 'pickup_at' => ['required', 'date', 'after:now'], 'cake_size_id' => ['required', 'exists:cake_sizes,id'], 'cake_shape_id' => ['required', 'exists:cake_shapes,id'], 'base_flavor_id' => ['required', 'exists:cake_flavors,id'], 'filling_flavor_id' => ['required', 'exists:cake_flavors,id'], 'cake_text' => ['nullable', 'string', 'max:120'], 'age_text' => ['nullable', 'string', 'max:30'], 'base_color' => ['nullable', 'string', 'max:60'], 'decoration_color' => ['nullable', 'string', 'max:60'], 'character_theme' => ['nullable', 'string', 'max:120'], 'customer_notes' => ['nullable', 'string', 'max:2000'], 'admin_notes' => ['nullable', 'string', 'max:2000'], 'items' => ['array'], 'items.*.id' => ['required', 'integer', 'distinct', 'exists:additional_items,id'], 'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100']];
    }
}
