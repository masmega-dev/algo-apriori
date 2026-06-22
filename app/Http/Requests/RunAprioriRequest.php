<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RunAprioriRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }
    public function rules(): array { return ['date_from' => ['required', 'date'], 'date_to' => ['required', 'date', 'after_or_equal:date_from'], 'minimum_support' => ['required', 'numeric', 'between:0,1'], 'minimum_confidence' => ['required', 'numeric', 'between:0,1'], 'minimum_lift' => ['nullable', 'numeric', 'min:0']]; }
}
