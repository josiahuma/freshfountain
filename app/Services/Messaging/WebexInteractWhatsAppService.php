<?php

namespace App\Services\Messaging;

/**
 * Safe WhatsApp placeholder for Webex Interact.
 *
 * Webex Interact documents WhatsApp profiles and approved templates, but the
 * current public Interact API reference does not publish the corresponding
 * WhatsApp send endpoint/payload alongside the documented /v1/sms API.
 * We intentionally do not guess an API contract.
 */
class WebexInteractWhatsAppService
{
    public function status(): array
    {
        if (! config('services.webex_interact.whatsapp.enabled', false)) {
            return [
                'ready' => false,
                'status' => 'disabled',
                'message' => 'Webex Interact WhatsApp is disabled.',
            ];
        }

        if (! config('services.webex_interact.whatsapp.profile_approved', false)) {
            return [
                'ready' => false,
                'status' => 'pending_profile',
                'message' => 'WhatsApp profile is not yet marked approved.',
            ];
        }

        return [
            'ready' => false,
            'status' => 'pending_api_contract',
            'message' => 'WhatsApp profile is approved, but the Webex Interact WhatsApp send API contract still needs to be confirmed before automated sends are enabled.',
        ];
    }
}
