<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MicrosoftGraphMailer
{
    public function send(string $toEmail, string $subject, string $htmlBody): void
    {
        $tenantId = config('services.ms_graph.tenant_id');
        $clientId = config('services.ms_graph.client_id');
        $clientSecret = config('services.ms_graph.client_secret');
        $sender = config('services.ms_graph.sender');

        // 1) Get access token (client credentials)
        $tokenRes = Http::asForm()->post("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token", [
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'scope'         => 'https://graph.microsoft.com/.default',
            'grant_type'    => 'client_credentials',
        ]);

        if (! $tokenRes->successful()) {
            throw new \RuntimeException('Graph token error: ' . $tokenRes->body());
        }

        $token = $tokenRes->json('access_token');

        // 2) Send mail
        $sendRes = Http::withToken($token)->post("https://graph.microsoft.com/v1.0/users/{$sender}/sendMail", [
            'message' => [
                'subject' => $subject,
                'body' => [
                    'contentType' => 'HTML',
                    'content' => $htmlBody,
                ],
                'toRecipients' => [
                    ['emailAddress' => ['address' => $toEmail]],
                ],
            ],
            'saveToSentItems' => true,
        ]);

        if (! $sendRes->successful()) {
            throw new \RuntimeException('Graph sendMail error: ' . $sendRes->body());
        }
    }
}