<?php

namespace App\Services\Messaging;

use Illuminate\Http\Client\Response;
use RuntimeException;

class WebexInteractSmsService
{
    public function __construct(protected WebexInteractClient $client) {}

    public function send(
        string $recipient,
        string $message,
        ?string $correlationId = null,
        bool $test = false,
    ): array {
        if (! config('services.webex_interact.sms.enabled', false)) {
            return [
                'ok' => false,
                'skipped' => true,
                'status' => 'skipped',
                'message' => 'Webex Interact SMS is disabled.',
            ];
        }

        $from = trim((string) config('services.webex_interact.sms.from'));
        if ($from === '') {
            throw new RuntimeException('WEBEX_INTERACT_SMS_FROM is not configured.');
        }

        $phone = $this->normalisePhone($recipient);
        $payload = [
            'message_body' => $message,
            'from' => $from,
            'to' => [[
                'correlation_id' => $correlationId,
                'phone' => [$phone],
            ]],
            'name' => $correlationId,
        ];

        $url = rtrim((string) config(
            'services.webex_interact.sms.endpoint',
            'https://api.webexinteract.com/v1/sms'
        ), '/');

        if ($test) {
            $url .= '/test';
        }

        return $this->resultFromResponse(
            $this->client->post($url, $payload),
            $phone,
            $payload
        );
    }

    public function normalisePhone(string $phone): string
    {
        $phone = trim($phone);
        $phone = preg_replace('/[^\d+]/', '', $phone) ?? '';

        if (str_starts_with($phone, '00')) {
            $phone = '+' . substr($phone, 2);
        }

        if (str_starts_with($phone, '0')) {
            $country = trim((string) config(
                'services.webex_interact.default_country_calling_code',
                '+44'
            ));
            $phone = $country . substr($phone, 1);
        }

        if (! str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }

        if (! preg_match('/^\+[1-9]\d{7,14}$/', $phone)) {
            throw new RuntimeException('Invalid phone number after normalisation: ' . $phone);
        }

        return $phone;
    }

    private function resultFromResponse(Response $response, string $recipient, array $payload): array
    {
        $json = $response->json();

        if (is_array($json) && array_is_list($json) && isset($json[0]) && is_array($json[0])) {
            $json = $json[0];
        }

        $messages = is_array($json) ? ($json['messages'] ?? []) : [];
        $errors = is_array($json) ? ($json['errors'] ?? []) : [];
        $message = collect($messages)->first(
            fn (array $item): bool => ($item['to'] ?? null) === $recipient
        ) ?? ($messages[0] ?? null);

        $accepted = $response->successful()
            && is_array($message)
            && in_array($message['status'] ?? null, ['queued', 'validated'], true);

        return [
            'ok' => $accepted,
            'skipped' => false,
            'http_status' => $response->status(),
            'request_id' => is_array($json) ? ($json['request_id'] ?? null) : null,
            'transaction_id' => is_array($message) ? ($message['transaction_id'] ?? null) : null,
            'status' => is_array($message)
                ? ($message['status'] ?? ($accepted ? 'accepted' : 'failed'))
                : 'failed',
            'code' => is_array($message) ? ($message['code'] ?? null) : null,
            'errors' => $errors,
            'response' => $json,
            'payload' => $payload,
        ];
    }
}
