<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use App\Models\AdditionalItem;
use App\Models\CakeFlavor;
use App\Models\CakeShape;
use App\Models\CakeSize;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /** @param array<string,mixed> $data */
    public function create(array $data, int $userId): Order
    {
        return DB::transaction(function () use ($data, $userId): Order {
            $phone = $this->phone($data['customer_phone']);
            $customer = Customer::query()->updateOrCreate(['phone' => $phone], ['name' => $data['customer_name'], 'last_order_at' => now()]);
            $size = CakeSize::query()->whereKey($data['cake_size_id'])->where('is_active', true)->firstOrFail();
            $shape = CakeShape::query()->whereKey($data['cake_shape_id'])->where('is_active', true)->firstOrFail();
            $base = CakeFlavor::query()->whereKey($data['base_flavor_id'])->where('type', 'base')->where('is_active', true)->firstOrFail();
            $filling = CakeFlavor::query()->whereKey($data['filling_flavor_id'])->where('type', 'filling')->where('is_active', true)->firstOrFail();
            $requested = collect($data['items'] ?? [])->keyBy('id');
            $items = AdditionalItem::query()->whereIn('id', $requested->keys())->where('is_active', true)->get();
            if ($items->count() !== $requested->count()) {
                throw ValidationException::withMessages(['items' => 'Item tambahan tidak tersedia.']);
            } $cakePrice = (float) $size->base_price + (float) $shape->price_adjustment + (float) $base->price_adjustment + (float) $filling->price_adjustment;
            $additional = $items->sum(fn (AdditionalItem $item) => (float) $item->price * (int) $requested[$item->id]['quantity']);
            $order = Order::query()->create(['public_token' => (string) Str::ulid(), 'order_number' => 'ORD-'.now()->format('Ym').'-'.str_pad((string) (Order::query()->withTrashed()->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->count() + 1), 4, '0', STR_PAD_LEFT), 'customer_id' => $customer->id, 'cake_size_id' => $size->id, 'cake_shape_id' => $shape->id, 'base_flavor_id' => $base->id, 'filling_flavor_id' => $filling->id, 'created_by' => $userId, 'source' => OrderSource::Admin, 'ordered_at' => now(), 'pickup_at' => $data['pickup_at'], 'fulfillment_method' => 'pickup', 'customer_name_snapshot' => $customer->name, 'customer_phone_snapshot' => $phone, 'delivery_fee' => 0, 'cake_size_snapshot' => $size->name, 'cake_shape_snapshot' => $shape->name, 'base_flavor_snapshot' => $base->name, 'filling_flavor_snapshot' => $filling->name, 'cake_text' => $data['cake_text'] ?? null, 'age_text' => $data['age_text'] ?? null, 'base_color' => $data['base_color'] ?? null, 'decoration_color' => $data['decoration_color'] ?? null, 'character_theme' => $data['character_theme'] ?? null, 'cake_price' => $cakePrice, 'additional_items_total' => $additional, 'grand_total' => $cakePrice + $additional, 'status' => OrderStatus::Pending, 'customer_notes' => $data['customer_notes'] ?? null, 'admin_notes' => $data['admin_notes'] ?? null]);
            foreach ($items as $item) {
                $quantity = (int) $requested[$item->id]['quantity'];
                $order->additionalItems()->create(['additional_item_id' => $item->id, 'item_name_snapshot' => $item->name, 'unit_snapshot' => $item->unit, 'unit_price' => $item->price, 'quantity' => $quantity, 'subtotal' => (float) $item->price * $quantity]);
            } OrderStatusHistory::query()->create(['order_id' => $order->id, 'changed_by' => $userId, 'new_status' => OrderStatus::Pending->value]);

            return $order;
        });
    }

    public function complete(Order $order, int $userId, ?string $notes = null): void
    {
        if ($order->status === OrderStatus::Completed) {
            return;
        } DB::transaction(function () use ($order, $userId, $notes): void {
            $previous = $order->status;
            $order->update(['status' => OrderStatus::Completed, 'completed_at' => now(), 'completed_by' => $userId]);
            OrderStatusHistory::query()->create(['order_id' => $order->id, 'changed_by' => $userId, 'previous_status' => $previous->value, 'new_status' => OrderStatus::Completed->value, 'notes' => $notes]);
        });
    }

    private function phone(string $value): string
    {
        $phone = preg_replace('/\D+/', '', $value) ?? '';
        $phone = str_starts_with($phone, '0') ? '62'.substr($phone, 1) : $phone;
        if (! preg_match('/^62\d{8,13}$/', $phone)) {
            throw ValidationException::withMessages(['customer_phone' => 'Nomor WhatsApp tidak valid.']);
        }

        return $phone;
    }
}
