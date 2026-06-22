<?php

declare(strict_types=1);

namespace App\Services\Whatsapp;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteWhatsappService
{
    /** @return array{success: bool, message: string, response: array<string, mixed>} */
    public function send(string $target, string $message): array
    {
        $token = config('services.fonnte.token');
        if (!is_string($token) || $token === '') {
            return ['success' => false, 'message' => 'Fonnte token belum dikonfigurasi.', 'response' => []];
        }

        try {
            $response = Http::asForm()->timeout(15)->withHeaders(['Authorization' => $token])->post('https://api.fonnte.com/send', ['target' => $target, 'message' => $message]);
            $body = $response->json() ?? [];
            if (!$response->successful() || ($body['status'] ?? false) !== true) {
                Log::warning('Fonnte rejected WhatsApp message.', ['target' => $target, 'response' => $body]);
                return ['success' => false, 'message' => (string) ($body['reason'] ?? 'Pesan gagal dikirim.'), 'response' => $body];
            }
            return ['success' => true, 'message' => 'Pesan diterima oleh gateway.', 'response' => $body];
        } catch (ConnectionException $exception) {
            Log::error('Fonnte connection failed.', ['message' => $exception->getMessage()]);
            return ['success' => false, 'message' => 'Gateway WhatsApp tidak dapat dihubungi.', 'response' => []];
        }
    }
}
