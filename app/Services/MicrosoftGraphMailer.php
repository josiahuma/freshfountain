<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class MicrosoftGraphMailer
{
    public function send(string $toEmail, string $subject, string $htmlBody): void
    {
        $tenantId = (string) config('services.ms_graph.tenant_id');
        $clientId = (string) config('services.ms_graph.client_id');
        $clientSecret = (string) config('services.ms_graph.client_secret');
        $sender = (string) config('services.ms_graph.sender');

        foreach ([
            'MS Graph tenant ID' => $tenantId,
            'MS Graph client ID' => $clientId,
            'MS Graph client secret' => $clientSecret,
            'MS Graph sender' => $sender,
        ] as $label => $value) {
            if (blank($value)) {
                throw new RuntimeException("{$label} is not configured.");
            }
        }

        if (! filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('The recipient email address is invalid.');
        }

        $tokenResponse = Http::asForm()
            ->timeout(30)
            ->retry(2, 500)
            ->post(
                "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token",
                [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'scope' => 'https://graph.microsoft.com/.default',
                    'grant_type' => 'client_credentials',
                ]
            );

        if (! $tokenResponse->successful()) {
            throw new RuntimeException(
                'Graph token error: '.$tokenResponse->body()
            );
        }

        $token = $tokenResponse->json('access_token');

        if (blank($token)) {
            throw new RuntimeException('Microsoft Graph did not return an access token.');
        }

        $sendResponse = Http::withToken($token)
            ->acceptJson()
            ->timeout(30)
            ->retry(2, 500)
            ->post(
                'https://graph.microsoft.com/v1.0/users/'.rawurlencode($sender).'/sendMail',
                [
                    'message' => [
                        'subject' => $subject,
                        'body' => [
                            'contentType' => 'HTML',
                            'content' => $htmlBody,
                        ],
                        'toRecipients' => [
                            [
                                'emailAddress' => [
                                    'address' => $toEmail,
                                ],
                            ],
                        ],
                    ],
                    'saveToSentItems' => true,
                ]
            );

        if (! $sendResponse->successful()) {
            throw new RuntimeException(
                'Graph sendMail error: '.$sendResponse->body()
            );
        }
    }
}
