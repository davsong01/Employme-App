<style>
    .teacher-page {
        max-width: 1440px;
        margin: 0 auto;
        padding: 1rem 1.25rem 2.5rem;
    }

    .teacher-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin: 0.25rem 0 1.5rem;
    }

    .teacher-page-eyebrow {
        display: block;
        margin-bottom: 0.4rem;
        color: #64748b;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .teacher-page-title {
        margin: 0;
        color: #172033;
        font-size: clamp(1.6rem, 3vw, 2.15rem);
        font-weight: 750;
        letter-spacing: -0.04em;
    }

    .teacher-page-description {
        margin: 0.4rem 0 0;
        color: #64748b;
        font-size: 0.95rem;
    }

    .teacher-back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        flex-shrink: 0;
        padding: 0.65rem 0.9rem;
        border: 1px solid #dbe3ee;
        border-radius: 0.75rem;
        background: #fff;
        color: #334155;
        font-weight: 600;
        text-decoration: none;
        transition: border-color 160ms ease, background 160ms ease, transform 160ms ease;
    }

    .teacher-back-link:hover {
        transform: translateY(-1px);
        border-color: #b8c6d8;
        background: #f8fafc;
        color: #172033;
    }

    .teacher-form-card {
        overflow: hidden;
        border: 1px solid #e3e9f1;
        border-radius: 1.15rem;
        background: #fff;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.06);
    }

    .teacher-form-card > .card-body {
        padding: clamp(1.1rem, 2.6vw, 2rem);
    }

    .teacher-page form {
        color: #334155;
    }

    .teacher-page fieldset,
    .teacher-form-section {
        min-width: 0;
        margin: 0 0 1.25rem;
        padding: clamp(1rem, 2vw, 1.5rem);
        border: 1px solid #e6ebf2;
        border-radius: 1rem;
        background: #fff;
    }

    .teacher-page legend,
    .teacher-section-title {
        float: none;
        width: auto;
        margin: 0 0 1.1rem;
        padding: 0;
        color: #172033;
        font-size: 1.02rem;
        font-weight: 700;
    }

    .teacher-section-hint {
        margin: -0.7rem 0 1.1rem;
        color: #718096;
        font-size: 0.86rem;
    }

    .teacher-page label {
        margin-bottom: 0.45rem;
        color: #3b4759;
        font-size: 0.88rem;
        font-weight: 650;
    }

    .teacher-page .form-control,
    .teacher-page .select2-selection {
        min-height: 2.8rem;
        border: 1px solid #d7e0eb;
        border-radius: 0.7rem;
        background-color: #fff;
        color: #172033;
        box-shadow: none;
    }

    .teacher-page textarea.form-control {
        min-height: 10rem;
        padding: 0.85rem 1rem;
    }

    .teacher-page .form-control:focus,
    .teacher-page .select2-container--focus .select2-selection {
        border-color: #6a8df5;
        box-shadow: 0 0 0 0.2rem rgba(79, 112, 224, 0.12);
    }

    .teacher-page .form-check {
        margin: 0.3rem 0;
        padding: 0.75rem 0.75rem 0.75rem 2.15rem;
        border: 1px solid #e8edf4;
        border-radius: 0.7rem;
        background: #fbfcfe;
    }

    .teacher-page .form-check-input {
        margin-top: 0.2rem;
    }

    .teacher-page .form-check-label {
        margin: 0;
        font-weight: 550;
    }

    .teacher-page .teacher-profile-preview {
        display: grid;
        width: 6rem;
        height: 6rem;
        place-items: center;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 1.15rem;
        background: #f1f5f9;
        color: #64748b;
    }

    .teacher-page .teacher-profile-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .teacher-page .permissions-container {
        border: 1px solid #e6ebf2 !important;
        border-radius: 0.85rem !important;
        background: #f8fafc !important;
        box-shadow: none !important;
    }

    .teacher-menu-permissions-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
    }

    .teacher-page .accordion-item {
        overflow: hidden;
        margin-bottom: 0.75rem;
        border: 1px solid #e3e9f1;
        border-radius: 0.85rem !important;
    }

    .teacher-page .accordion-button {
        color: #263348;
        font-weight: 650;
    }

    .teacher-page .accordion-button:not(.collapsed) {
        background: #f3f6ff;
        color: #2947a3;
        box-shadow: none;
    }

    .teacher-page .teacher-submit-bar {
        display: flex;
        justify-content: flex-end;
        gap: 0.7rem;
        margin-top: 1.4rem;
        padding-top: 1.2rem;
        border-top: 1px solid #e8edf4;
    }

    .teacher-page .teacher-submit-button {
        min-width: 11rem;
        padding: 0.78rem 1.2rem;
        border: 0;
        border-radius: 0.75rem;
        background: #3f5bd8;
        color: #fff;
        font-weight: 700;
        box-shadow: 0 7px 16px rgba(63, 91, 216, 0.2);
    }

    .teacher-page .teacher-submit-button:hover {
        background: #304bc3;
        color: #fff;
    }

    @media (max-width: 767.98px) {
        .teacher-page {
            padding: 0.7rem 0.65rem 1.5rem;
        }

        .teacher-page-header {
            align-items: flex-start;
        }

        .teacher-page-description {
            max-width: 22rem;
        }

        .teacher-back-link {
            padding: 0.55rem 0.65rem;
            font-size: 0.85rem;
        }

        .teacher-form-card > .card-body {
            padding: 1rem;
        }

        .teacher-page .teacher-submit-bar {
            display: grid;
            grid-template-columns: 1fr;
        }

        .teacher-page .teacher-submit-button {
            width: 100%;
        }

        .teacher-menu-permissions-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        .teacher-menu-permissions-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>
