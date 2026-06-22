<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\WhatsappStatus;
use App\Models\WhatsappLog;
use App\Services\Whatsapp\FonnteWhatsappService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SendOrderWhatsappNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 20;

    public function __construct(private readonly int $logId) {}

    public function backoff(): array
    {
        return [60, 300, 900];
    }

    public function handle(FonnteWhatsappService $whatsapp): void
    {
        $log = WhatsappLog::query()->findOrFail($this->logId);
        if ($log->status === WhatsappStatus::Sent) {
            return;
        }

        $log->increment('attempt_count');
        $result = $whatsapp->send($log->phone, $log->message);
        if (! $result['success']) {
            throw new \RuntimeException($result['message']);
        }

        $log->update(['status' => WhatsappStatus::Sent, 'provider_response' => $result['response'], 'provider_message_id' => (string) ($result['response']['id'][0] ?? $result['response']['requestid'] ?? ''), 'sent_at' => now(), 'error_message' => null]);
    }

    public function failed(Throwable $exception): void
    {
        WhatsappLog::query()->whereKey($this->logId)->update(['status' => WhatsappStatus::Failed, 'error_message' => $exception->getMessage(), 'failed_at' => now()]);
    }
}
