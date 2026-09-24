<style>
    /* Dashboard stat cards - force readable text in light/dark mode */
    [data-stat-card="jobs"],
    [data-stat-card="jobs"] * {
        color: #1e3a8a !important;
    }

    [data-stat-card="applicants"],
    [data-stat-card="applicants"] * {
        color: #14532d !important;
    }

    [data-stat-card="blog"],
    [data-stat-card="blog"] * {
        color: #78350f !important;
    }

    [data-stat-card] svg {
        opacity: 1 !important;
    }

    /* Job application coloured rows - force readable text in dark mode */
    [data-application-status="reviewed"],
    [data-application-status="reviewed"] * {
        background-color: #fef3c7 !important;
        color: #78350f !important;
    }

    [data-application-status="rejected"],
    [data-application-status="rejected"] * {
        background-color: #fee2e2 !important;
        color: #7f1d1d !important;
    }

    [data-application-status="shortlisted"],
    [data-application-status="shortlisted"] * {
        background-color: #dcfce7 !important;
        color: #14532d !important;
    }

    [data-application-status] select {
        border-color: rgba(0, 0, 0, 0.15) !important;
        font-weight: 700 !important;
    }
</style>

<style>
    /*
    |--------------------------------------------------------------------------
    | Fresh Fountain global Filament notifications
    |--------------------------------------------------------------------------
    */
    .fi-no-notifications,
    .ff-centred-notification-tray {
        position: fixed !important;
        top: 1.5rem !important;
        left: 50% !important;
        right: auto !important;
        bottom: auto !important;
        width: min(92vw, 32rem) !important;
        transform: translateX(-50%) !important;
        z-index: 99999 !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: .75rem !important;
        pointer-events: none !important;
    }

    .fi-no-notification {
        position: relative !important;
        inset: auto !important;
        width: 100% !important;
        max-width: none !important;
        border: 1px solid #dbe4f0 !important;
        border-radius: 16px !important;
        background: #ffffff !important;
        box-shadow: 0 22px 55px rgba(15, 23, 42, .22) !important;
        pointer-events: auto !important;
    }

    .fi-no-notification-title {
        font-weight: 800 !important;
    }

    @media (max-width: 640px) {
        .fi-no-notifications,
        .ff-centred-notification-tray {
            top: 1rem !important;
            width: calc(100vw - 2rem) !important;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Members bulk SMS help
    |--------------------------------------------------------------------------
    */
    .ff-members-sms-help {
        margin-bottom: 1rem;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        border: 1px solid #bfdbfe;
        border-radius: 16px;
        background: #eff6ff;
        padding: 16px 18px;
        color: #1e3a8a;
    }

    .ff-members-sms-help-icon {
        display: flex;
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #dbeafe;
        font-size: 20px;
    }

    .ff-members-sms-help h3 {
        margin: 0;
        color: #172554;
        font-size: 15px;
        font-weight: 800;
    }

    .ff-members-sms-help p {
        margin: 5px 0 0;
        font-size: 14px;
        line-height: 1.55;
    }

    .ff-members-sms-help strong {
        font-weight: 800;
    }

    .ff-members-sms-help-note {
        color: #475569;
        font-size: 12px !important;
    }

    /*
    |--------------------------------------------------------------------------
    | Members data-protection acknowledgement
    |--------------------------------------------------------------------------
    */
    .ff-gdpr-backdrop {
        position: fixed;
        inset: 0;
        z-index: 99990;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: rgba(15, 23, 42, .72);
        backdrop-filter: blur(5px);
    }
    .ff-gdpr-dialog {
        width: min(100%, 620px);
        border-radius: 24px;
        background: #fff;
        padding: 34px;
        box-shadow: 0 28px 80px rgba(0,0,0,.3);
        color: #0f172a;
    }
    .ff-gdpr-icon { font-size: 34px; margin-bottom: 12px; }
    .ff-gdpr-kicker { margin: 0 0 8px; color: #1d4ed8; font-size: 12px; font-weight: 900; letter-spacing: .14em; }
    .ff-gdpr-dialog h2 { margin: 0 0 14px; font-size: 26px; line-height: 1.2; font-weight: 800; }
    .ff-gdpr-dialog > p:not(.ff-gdpr-kicker) { color: #475569; line-height: 1.65; }
    .ff-gdpr-notice { margin: 20px 0; border-left: 4px solid #2563eb; border-radius: 12px; background: #eff6ff; padding: 16px 18px; color: #1e3a8a; line-height: 1.6; font-weight: 600; }
    .ff-gdpr-small { font-size: 13px; }
    .ff-gdpr-accept { margin-top: 18px; width: 100%; border: 0; border-radius: 12px; background: #1d4ed8; padding: 13px 18px; color: white; font-weight: 800; cursor: pointer; }
    .ff-gdpr-accept:hover { background: #1e40af; }
    .ff-gdpr-accept:disabled { opacity: .7; cursor: wait; }
    .ff-gdpr-cancel { display: block; margin-top: 12px; text-align: center; color: #64748b; font-size: 14px; font-weight: 700; text-decoration: none; }
    .ff-gdpr-cancel:hover { color: #0f172a; }
</style>

<script>
    (() => {
        const centreFilamentNotifications = () => {
            document.querySelectorAll('.fi-no-notification').forEach((notification) => {
                const tray = notification.closest('.fi-no-notifications') ?? notification.parentElement;

                if (tray) {
                    tray.classList.add('ff-centred-notification-tray');
                }
            });
        };

        const initialise = () => {
            centreFilamentNotifications();

            if (!document.body || window.__ffNotificationObserverStarted) {
                return;
            }

            window.__ffNotificationObserverStarted = true;

            const observer = new MutationObserver(centreFilamentNotifications);
            observer.observe(document.body, {
                childList: true,
                subtree: true,
            });
        };

        document.addEventListener('DOMContentLoaded', initialise);
        document.addEventListener('livewire:navigated', initialise);

        if (document.readyState !== 'loading') {
            initialise();
        }
    })();
</script>
