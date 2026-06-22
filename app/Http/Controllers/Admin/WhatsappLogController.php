<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\WhatsappStatus;
use App\Http\Controllers\Controller;
use App\Jobs\SendOrderWhatsappNotification;
use App\Models\Order;
use App\Models\WhatsappLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WhatsappLogController extends Controller
{
    public function resend(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate(['recipient_type' => ['required', 'in:customer,admin']]);
        $log = WhatsappLog::query()->where('order_id', $order->id)->where('recipient_type', $data['recipient_type'])->latest()->firstOrFail();
        $log->update(['status' => WhatsappStatus::Pending, 'error_message' => null, 'failed_at' => null]);
        SendOrderWhatsappNotification::dispatch($log->id);
        return back()->with('success', 'Notifikasi dimasukkan ke antrean pengiriman.');
    }
}
