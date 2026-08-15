<?php

namespace App\Console\Commands;

use App\Services\Messaging\WebexInteractSmsService;
use App\Services\Messaging\WebexInteractWhatsAppService;
use Illuminate\Console\Command;
use Throwable;

class TestWebexInteractMessaging extends Command
{
    protected $signature = 'transport:test-messaging
        {--to= : Mobile number to validate/send}
        {--live : Send a real SMS instead of using the free Webex test endpoint}
        {--status : Show messaging configuration status only}';

    protected $description = 'Test Fresh Fountain transport messaging through Webex Interact.';

    public function handle(
        WebexInteractSmsService $sms,
        WebexInteractWhatsAppService $whatsApp,
    ): int {
        if ($this->option('status')) {
            $wa = $whatsApp->status();

            $this->table(
                ['Setting', 'Value'],
                [
                    ['Webex token', filled(config('services.webex_interact.token')) ? 'Configured' : 'Missing'],
                    ['SMS enabled', config('services.webex_interact.sms.enabled') ? 'Yes' : 'No'],
                    ['SMS sender', config('services.webex_interact.sms.from') ?: 'Missing'],
                    ['WhatsApp', $wa['status'] ?? 'Unknown'],
                ]
            );

            return self::SUCCESS;
        }

        $to = trim((string) $this->option('to'));
        if ($to === '') {
            $this->error('Pass a test number, e.g. --to=+447700900123');
            return self::FAILURE;
        }

        $live = (bool) $this->option('live');

        $live
            ? $this->warn('LIVE mode: Webex Interact will send and charge for this SMS.')
            : $this->info('TEST mode: Webex validates the request without sending or charging.');

        try {
            $result = $sms->send(
                $to,
                'Fresh Fountain Webex Interact transport messaging test.',
                'fresh-fountain-transport-test-' . now()->timestamp,
                ! $live
            );
        } catch (Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        $this->table(
            ['Result', 'Value'],
            [
                ['Accepted', ($result['ok'] ?? false) ? 'Yes' : 'No'],
                ['Status', $result['status'] ?? 'Unknown'],
                ['HTTP', $result['http_status'] ?? '-'],
                ['Request ID', $result['request_id'] ?? '-'],
                ['Transaction ID', $result['transaction_id'] ?? '-'],
                ['Errors', empty($result['errors']) ? '-' : json_encode($result['errors'], JSON_UNESCAPED_SLASHES)],
            ]
        );

        return ($result['ok'] ?? false) ? self::SUCCESS : self::FAILURE;
    }
}
