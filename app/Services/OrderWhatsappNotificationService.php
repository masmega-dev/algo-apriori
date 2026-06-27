<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\WhatsappStatus;
use App\Jobs\SendOrderWhatsappNotification;
use App\Models\Order;
use App\Models\StoreSetting;
use App\Models\WhatsappLog;

class OrderWhatsappNotificationService
{
    public function queueNewOrderNotifications(Order $order, StoreSetting $setting): void
    {
        $this->queue(
            order: $order,
            recipientType: 'customer',
            recipientName: $order->customer_name_snapshot,
            phone: $order->customer_phone_snapshot,
            messageType: 'new_order',
            message: $this->render($this->template($setting, 'customer_order_template'), $order),
        );

        $this->queue(
            order: $order,
            recipientType: 'admin',
            recipientName: 'Admin Toko',
            phone: $setting->admin_whatsapp,
            messageType: 'new_order',
            message: $this->render($this->template($setting, 'admin_order_template'), $order),
        );
    }

    public function queueCompletedOrderNotification(Order $order, StoreSetting $setting): void
    {
        $this->queue(
            order: $order,
            recipientType: 'customer',
            recipientName: $order->customer_name_snapshot,
            phone: $order->customer_phone_snapshot,
            messageType: 'order_completed',
            message: $this->render($this->template($setting, 'completed_order_template'), $order),
        );
    }

    private function template(StoreSetting $setting, string $key): string
    {
        $value = $setting->getAttribute($key);

        return is_string($value) && $value !== ''
            ? $value
            : StoreSetting::defaults()[$key];
    }

    private function queue(
        Order $order,
        string $recipientType,
        string $recipientName,
        string $phone,
        string $messageType,
        string $message,
    ): void {
        if ($phone === '') {
            return;
        }

        $log = WhatsappLog::query()->create([
            'order_id' => $order->id,
            'recipient_type' => $recipientType,
            'message_type' => $messageType,
            'recipient_name' => $recipientName,
            'phone' => $phone,
            'provider' => 'fonnte',
            'message' => $message,
            'status' => WhatsappStatus::Pending,
        ]);

        SendOrderWhatsappNotification::dispatch($log->id);
    }

    private function render(string $template, Order $order): string
    {
        $fulfillmentDate = $order->fulfillment_at->format('d M Y');
        $fulfillmentTime = $order->fulfillment_at->format('H:i');
        $completedAt = $order->completed_at;

        return strtr($template, [
            '{name}' => $order->customer_name_snapshot,
            '{customer_name}' => $order->customer_name_snapshot,
            '{customer_phone}' => $order->customer_phone_snapshot,
            '{order_number}' => $order->order_number,
            '{fulfillment_date}' => $fulfillmentDate,
            '{fulfillment_time}' => $fulfillmentTime,
            '{pickup_date}' => $fulfillmentDate,
            '{pickup_time}' => $fulfillmentTime,
            '{pickup_at}' => $order->fulfillment_at->format('d M Y H:i'),
            '{fulfillment_method}' => $order->fulfillment_method->label(),
            '{delivery_address}' => $order->delivery_address ?? '-',
            '{grand_total}' => number_format((float) $order->grand_total, 0, ',', '.'),
            '{invoice_url}' => route('invoice.show', $order->public_token),
            '{completed_date}' => $completedAt?->format('d M Y') ?? '-',
            '{completed_time}' => $completedAt?->format('H:i') ?? '-',
        ]);
    }
}
