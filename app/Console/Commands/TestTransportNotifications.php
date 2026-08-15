<?php

namespace App\Console\Commands;

use App\Models\TransportNotificationLog;
use App\Services\Messaging\TransportNotificationService;
use Illuminate\Console\Command;

class TestTransportNotifications extends Command
{
    protected $signature = 'transport:test-notifications
        {--sms : Test SMS only}
        {--whatsapp : Test WhatsApp only}';

    protected $description = 'Send test Fresh Fountain transport notifications using the configured Twilio channels.';

    public function handle(TransportNotificationService $service): int
    {
        $smsOnly = (bool) $this->option('sms');
        $whatsAppOnly = (bool) $this->option('whatsapp');

        $channel = null;

        if ($smsOnly && ! $whatsAppOnly) {
            $channel = TransportNotificationLog::CHANNEL_SMS;
        }

        if ($whatsAppOnly && ! $smsOnly) {
            $channel = TransportNotificationLog::CHANNEL_WHATSAPP;
        }

        $results = $service->sendTestNotifications($channel);

        $rows = [];
        $failed = false;

        foreach ($results as $channelName => $items) {
            foreach ($items as $result) {
                $ok = (bool) ($result['ok'] ?? false);
                $failed = $failed || ! $ok;

                $rows[] = [
                    strtoupper((string) ($result['channel'] ?? $channelName)),
                    (string) ($result['recipient'] ?? '-'),
                    $ok ? 'OK' : 'FAILED',
                    (string) ($result['status'] ?? ''),
                    (string) ($result['sid'] ?? ''),
                    (string) ($result['error'] ?? ''),
                ];
            }
        }

        $this->table(
            ['Channel', 'Recipient', 'Result', 'Provider status', 'Message SID', 'Error'],
            $rows,
        );

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
