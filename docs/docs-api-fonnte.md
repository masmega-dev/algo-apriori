## Fonnte Docs

buat helper Whatsapp.go
contoh jika di php:

<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsApp
{
    /**
     * Kirim pesan WhatsApp dengan format yang sudah ditentukan.
     *
     * @param string $key Kunci template pesan
     * @param mixed ...$params Nilai pengganti
     * @return void
     */
    public static function sendMessage($target, string $message)
    {
        $payload = [
            'target'  => $target,
            'message' => $message,
        ];

        $response = Http::withHeaders([
            'Authorization' => site_setting('wa_token')
        ])->asForm()
            ->post('https://api.fonnte.com/send', $payload);

        $responseData = $response->json();

        if (!isset($responseData['status']) || $responseData['status'] !== true) {
            Log::error('WhatsApp Message Error: ' . json_encode($responseData));
            return [
                'success' => false,
                'message' => $responseData['reason'] ?? 'Pesan gagal dikirim'
            ];
        }

        return [
            'success' => true,
            'response' => $responseData
        ];
    }
}


dan whatsapp_config.md
untuk helper kirim pesan dan format pesan nya
<?php

use App\Helpers\WhatsApp;
use App\Models\WebsiteConfig;

if (!function_exists('formatNotif')) {
    /**
     * Ambil template notifikasi dari tabel settings (JSON).
     *
     * @param string|null $key
     * @return mixed
     */
    function formatNotif(?string $key = null)
    {
        return WebsiteConfig::where('key', $key)->value('value');
        // return $key;
    }
}

if (!function_exists('formatMessage')) {
    /**
     * Format pesan dengan mengganti placeholder {name}, {var1}, {var2}, dst.
     *
     * @param string $key Kunci template pesan
     * @param mixed ...$params Nilai pengganti
     * @return string
     */
    function formatMessage(string $key, ...$params): string
    {
        // Ambil template pesan
        $message = formatNotif($key);

        if (!$message) {
            return "[Template {$key} tidak ditemukan]";
        }

        // Placeholder default
        $placeholders = [
            '{name}',
            '{var1}',
            '{var2}',
            '{var3}',
            '{var4}',
            '{var5}',
            '{var6}',
            '{var7}',
            '{var8}',
            '{var9}',
            '{var10}'
        ];

        // Hanya pakai sebanyak param yang ada
        $replace = array_slice($params, 0, count($placeholders));

        return str_replace(array_slice($placeholders, 0, count($replace)), $replace, $message);
    }
}

if (!function_exists('sendWhatsAppMessage')) {
    /**
     * Kirim pesan WhatsApp dengan format yang sudah ditentukan.
     *
     * @param string $target Nomor tujuan
     * @param string $key Kunci template pesan
     * @param mixed ...$params Parameter untuk formatMessage
     * @return array Response dari WhatsApp API
     */
    function sendWhatsAppMessage(string $target, string $key, ...$params)
    {
        $message = formatMessage($key, ...$params);

        return WhatsApp::sendMessage($target, $message);
    }
}
