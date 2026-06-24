<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\FulfillmentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePublicOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->input('website') === null || $this->input('website') === '';
    }

    public function rules(): array
    {
        return [
            'submission_token' => ['required', 'uuid'],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'fulfillment_method' => ['required', Rule::enum(FulfillmentMethod::class)],
            'fulfillment_at' => ['required', 'date'],
            'delivery_address' => ['nullable', 'required_unless:fulfillment_method,pickup', 'string', 'max:2000'],
            'cake_size_id' => ['required', 'integer', 'exists:cake_sizes,id'],
            'cake_shape_id' => ['required', 'integer', 'exists:cake_shapes,id'],
            'cake_text' => ['nullable', 'string', 'max:120'],
            'age_text' => ['nullable', 'string', 'max:30'],
            'base_color' => ['nullable', 'string', 'max:60'],
            'decoration_color' => ['nullable', 'string', 'max:60'],
            'character_theme' => ['nullable', 'string', 'max:120'],
            'customer_notes' => ['nullable', 'string', 'max:2000'],
            'reference_image' => ['nullable', 'image', 'max:5120'],
            'items' => ['array'],
            'items.*.id' => ['required', 'integer', 'distinct', 'exists:additional_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'items.*.configuration' => ['nullable', 'array'],
            'items.*.configuration.type' => ['nullable', 'string', 'in:number,spiral'],
            'items.*.configuration.number' => ['nullable', 'string', 'regex:/^\\d+$/', 'max:10'],
            'website' => ['nullable', 'max:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'required_unless' => ':attribute wajib diisi untuk pesanan delivery / COD.',
            'uuid' => 'Permintaan pesanan tidak valid. Silakan muat ulang halaman dan coba lagi.',
            'string' => ':attribute harus berupa teks.',
            'integer' => ':attribute harus berupa angka.',
            'date' => ':attribute harus berupa tanggal dan jam yang valid.',
            'array' => ':attribute tidak valid.',
            'exists' => ':attribute yang dipilih tidak tersedia.',
            'distinct' => ':attribute tidak boleh dipilih lebih dari satu kali.',
            'enum' => ':attribute tidak valid.',
            'in' => ':attribute tidak valid.',
            'regex' => ':attribute hanya boleh berisi angka.',
            'image' => ':attribute harus berupa file gambar.',
            'min.numeric' => ':attribute minimal :min.',
            'max.string' => ':attribute maksimal :max karakter.',
            'max.numeric' => ':attribute maksimal :max.',
            'reference_image.max' => 'Foto referensi maksimal 5 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'submission_token' => 'permintaan pesanan',
            'customer_name' => 'nama pemesan',
            'customer_phone' => 'nomor WhatsApp',
            'fulfillment_method' => 'metode penerimaan',
            'fulfillment_at' => 'jadwal pesanan',
            'delivery_address' => 'alamat lengkap',
            'cake_size_id' => 'ukuran kue',
            'cake_shape_id' => 'bentuk kue',
            'cake_text' => 'tulisan pada kue',
            'age_text' => 'usia',
            'base_color' => 'warna dasar',
            'decoration_color' => 'warna hiasan',
            'character_theme' => 'tema atau karakter',
            'customer_notes' => 'catatan tambahan',
            'reference_image' => 'foto referensi',
            'items' => 'item tambahan',
            'items.*.id' => 'item tambahan',
            'items.*.quantity' => 'jumlah item tambahan',
            'items.*.configuration.type' => 'jenis lilin',
            'items.*.configuration.number' => 'angka pada lilin',
        ];
    }
}
