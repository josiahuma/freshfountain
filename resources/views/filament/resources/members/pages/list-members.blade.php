<x-filament-panels::page>
    <div class="ff-members-sms-help">
        <div class="ff-members-sms-help-icon" aria-hidden="true">💬</div>

        <div>
            <h3>Sending SMS to members</h3>

            <p>
                Select the members you want to contact using the checkboxes below.
                To contact everyone in the current results, use <strong>Select all</strong>.
                Then click <strong>Bulk actions → Send SMS</strong> and choose the SMS template you want to send.
            </p>

            <p class="ff-members-sms-help-note">
                Only members with a mobile number, SMS consent enabled and Do Not Contact disabled will receive the message.
                Birthday templates remain reserved for the dashboard birthday action.
            </p>
        </div>
    </div>

    {{ $this->table }}

    @if(! $dataProtectionAccepted)
        <div class="ff-gdpr-backdrop" role="dialog" aria-modal="true" aria-labelledby="ff-gdpr-title">
            <div class="ff-gdpr-dialog">
                <div class="ff-gdpr-icon">🔒</div>
                <p class="ff-gdpr-kicker">CONFIDENTIAL MEMBER DATA</p>
                <h2 id="ff-gdpr-title">Data protection acknowledgement</h2>

                <p>
                    Member information in Fresh Fountain CRM is confidential personal data and must be handled
                    in accordance with the Data Protection Act 2018, UK GDPR, church policy and your authorised role.
                </p>

                <div class="ff-gdpr-notice">
                    By continuing, you confirm that you will access this information only for legitimate authorised
                    church purposes and will not export, copy, disclose, share or otherwise use member data for any
                    unauthorised or illegitimate purpose.
                </div>

                <p class="ff-gdpr-small">
                    This acknowledgement is required once per signed-in session before the Members list can be accessed.
                </p>

                <button
                    type="button"
                    wire:click="acceptDataProtection"
                    wire:loading.attr="disabled"
                    class="ff-gdpr-accept"
                >
                    <span wire:loading.remove wire:target="acceptDataProtection">I understand and agree</span>
                    <span wire:loading wire:target="acceptDataProtection">Please wait…</span>
                </button>

                <a href="{{ \App\Filament\Pages\Dashboard::getUrl() }}" class="ff-gdpr-cancel">
                    Return to dashboard
                </a>
            </div>
        </div>
    @endif
</x-filament-panels::page>
