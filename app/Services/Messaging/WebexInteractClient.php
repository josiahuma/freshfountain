<?php

namespace App\Services\Messaging;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WebexInteractClient
{
    public function request(): PendingRequest
    {
        $token = trim((string) config('services.webex_interact.token'));

        if ($token === '') {
            throw new RuntimeException('WEBEX_INTERACT_TOKEN is not configured.');
        }

        return Http::acceptJson()
            ->asJson()
            ->withHeaders(['X-AUTH-KEY' => $token])
            ->timeout(max(5, (int) config('services.webex_interact.timeout', 15)))
            ->retry(2, 250, throw: false);
    }

    public function post(string $url, array $payload): Response
    {
        return $this->request()->post($url, $payload);
    }

    public function get(string $url, array $query = []): Response
    {
        return $this->request()->get($url, $query);
    }
}
