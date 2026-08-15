<?php

namespace App\Services\Messaging;

use App\Models\TransportBooking;
use App\Models\TransportNotificationLog;
use Illuminate\Support\Facades\Log;
use Throwable;

class TransportNotificationService
{
    public function __construct(
        protected WebexInteractSmsService $sms,
        protected WebexInteractWhatsAppService $whatsApp,
    ) {}

    public function notifyNewBooking(TransportBooking $booking): void
    {
        $booking->loadMissing('pickupEvent');

        if (config('services.webex_interact.transport.notify_admin_sms', true)) {
            foreach ($this->csv((string) config('services.webex_interact.transport.admin_sms_recipients', '')) as $recipient) {
                $this->sendSmsAndLog(
                    $booking,
                    $recipient,
                    $this->adminSmsMessage($booking),
                    'transport-booking-admin-' . $booking->id
                );
            }
        }

        if (
            config('services.webex_interact.transport.notify_passenger_sms', true)
            && filled($booking->phone)
        ) {
            $this->sendSmsAndLog(
                $booking,
                $booking->phone,
                $this->passengerSmsMessage($booking),
                'transport-booking-passenger-' . $booking->id
            );
        }

        if (config('services.webex_interact.transport.notify_admin_whatsapp', false)) {
            $status = $this->whatsApp->status();

            foreach ($this->csv((string) config('services.webex_interact.transport.admin_whatsapp_recipients', '')) as $recipient) {
                TransportNotificationLog::query()->create([
                    'transport_booking_id' => $booking->id,
                    'channel' => TransportNotificationLog::CHANNEL_WHATSAPP,
                    'recipient' => $recipient,
                    'provider' => 'webex_interact',
                    'status' => TransportNotificationLog::STATUS_SKIPPED,
                    'provider_status' => $status['status'] ?? 'not_ready',
                    'error_message' => $status['message'] ?? null,
                    'payload' => ['reason' => 'whatsapp_not_ready'],
                ]);
            }
        }
    }

    private function sendSmsAndLog(
        TransportBooking $booking,
        string $recipient,
        string $message,
        string $correlationId,
    ): void {
        try {
            $result = $this->sms->send($recipient, $message, $correlationId, false);

            TransportNotificationLog::query()->create([
                'transport_booking_id' => $booking->id,
                'channel' => TransportNotificationLog::CHANNEL_SMS,
                'recipient' => $recipient,
                'provider' => 'webex_interact',
                'status' => ($result['ok'] ?? false)
                    ? TransportNotificationLog::STATUS_SENT
                    : (($result['skipped'] ?? false)
                        ? TransportNotificationLog::STATUS_SKIPPED
                        : TransportNotificationLog::STATUS_FAILED),
                'provider_status' => $result['status'] ?? null,
                'provider_message_sid' => $result['transaction_id'] ?? null,
                'response_code' => $result['http_status'] ?? null,
                'error_message' => $this->errorMessage($result),
                'payload' => [
                    'request_id' => $result['request_id'] ?? null,
                    'response' => $result['response'] ?? null,
                ],
                'sent_at' => ($result['ok'] ?? false) ? now() : null,
            ]);
        } catch (Throwable $e) {
            TransportNotificationLog::query()->create([
                'transport_booking_id' => $booking->id,
                'channel' => TransportNotificationLog::CHANNEL_SMS,
                'recipient' => $recipient,
                'provider' => 'webex_interact',
                'status' => TransportNotificationLog::STATUS_FAILED,
                'provider_status' => 'exception',
                'error_message' => $e->getMessage(),
                'payload' => null,
            ]);

            Log::error('Webex Interact transport SMS failed.', [
                'booking_id' => $booking->id,
                'recipient' => $recipient,
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function adminSmsMessage(TransportBooking $booking): string
    {
        $event = $booking->pickupEvent;

        return sprintf(
            'New Fresh Fountain pickup: %s, %s %s, %d passenger(s). %s. Tel: %s. Address: %s',
            $booking->name,
            optional($event?->pickup_date)->format('d M Y') ?? 'Date TBC',
            $this->pickupTime($booking),
            (int) $booking->party_size,
            $event?->title ?? 'Church pickup',
            $booking->phone,
            $booking->address
        );
    }

    private function passengerSmsMessage(TransportBooking $booking): string
    {
        $event = $booking->pickupEvent;

        return sprintf(
            'Fresh Fountain: your pickup request for %s on %s at %s has been received for %d passenger(s). We will contact you if anything changes.',
            $event?->title ?? 'church',
            optional($event?->pickup_date)->format('d M Y') ?? 'the selected date',
            $this->pickupTime($booking),
            (int) $booking->party_size
        );
    }

    private function pickupTime(TransportBooking $booking): string
    {
        try {
            return \Carbon\CarbonImmutable::parse($booking->pickup_time)->format('g:i A');
        } catch (Throwable) {
            return (string) $booking->pickup_time;
        }
    }

    private function csv(string $value): array
    {
        return collect(explode(',', $value))
            ->map(fn (string $item): string => trim($item))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function errorMessage(array $result): ?string
    {
        if ($result['ok'] ?? false) {
            return null;
        }

        if ($result['message'] ?? null) {
            return (string) $result['message'];
        }

        $errors = $result['errors'] ?? [];
        if (! is_array($errors) || $errors === []) {
            return 'Webex Interact did not accept the SMS request.';
        }

        return collect($errors)
            ->map(fn ($error): string => is_array($error)
                ? (string) ($error['message'] ?? json_encode($error))
                : (string) $error)
            ->implode('; ');
    }
}
