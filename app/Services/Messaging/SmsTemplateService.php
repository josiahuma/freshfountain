<?php

namespace App\Services\Messaging;

use App\Models\Member;
use App\Models\SmsLog;
use App\Models\SmsTemplate;
use Illuminate\Support\Collection;
use Throwable;

class SmsTemplateService
{
    public function __construct(protected WebexInteractSmsService $sms) {}

    public function render(SmsTemplate $template, Member $member): string
    {
        $firstName = trim((string) $member->first_name);
        $lastName = trim((string) $member->last_name);
        $displayName = trim((string) $member->display_name);

        return strtr($template->body, [
            '{first_name}' => $firstName,
            '{last_name}' => $lastName,
            '{name}' => $displayName,
            '{full_name}' => $displayName,
        ]);
    }

    public function sendToMember(Member $member, SmsTemplate $template): array
    {
        if (! $member->can_receive_sms) {
            return ['status' => 'skipped', 'ok' => false, 'reason' => 'No SMS consent, no mobile number, or member is marked do not contact.'];
        }

        $message = $this->render($template, $member);
        $recipient = (string) $member->mobile_number;

        try {
            $result = $this->sms->send($recipient, $message, 'member-' . $member->id . '-template-' . $template->id . '-' . now()->timestamp);
            $status = ($result['ok'] ?? false) ? 'sent' : (($result['skipped'] ?? false) ? 'skipped' : 'failed');

            SmsLog::create([
                'member_id' => $member->id,
                'sms_template_id' => $template->id,
                'sent_by' => auth()->id(),
                'recipient' => $recipient,
                'message' => $message,
                'status' => $status,
                'provider_transaction_id' => $result['transaction_id'] ?? null,
                'provider_request_id' => $result['request_id'] ?? null,
                'error_message' => $status === 'failed' ? json_encode($result['errors'] ?? $result['response'] ?? null) : null,
                'sent_at' => $status === 'sent' ? now() : null,
            ]);

            return ['status' => $status, 'ok' => $status === 'sent'];
        } catch (Throwable $e) {
            SmsLog::create([
                'member_id' => $member->id,
                'sms_template_id' => $template->id,
                'sent_by' => auth()->id(),
                'recipient' => $recipient,
                'message' => $message,
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return ['status' => 'failed', 'ok' => false, 'reason' => $e->getMessage()];
        }
    }

    public function sendToMembers(Collection $members, SmsTemplate $template): array
    {
        $summary = ['sent' => 0, 'skipped' => 0, 'failed' => 0];
        foreach ($members as $member) {
            $result = $this->sendToMember($member, $template);
            $summary[$result['status']] = ($summary[$result['status']] ?? 0) + 1;
        }
        return $summary;
    }
}
