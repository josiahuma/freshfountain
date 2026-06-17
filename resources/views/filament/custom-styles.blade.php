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