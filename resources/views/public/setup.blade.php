@extends('layouts.app')

@section('title', 'Setup Guide · WAPAPP Integration')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 fw-bold mb-1">WAPAPP for HubSpot Setup Guide</h1>
                    <p class="text-muted mb-0">Learn how to install, configure, and use the WAPAPP integration.</p>
                </div>
                <a href="{{ route('home') }}" class="btn btn-outline-soft">
                    <i class="me-2" data-lucide="arrow-left"></i> Back to Home
                </a>
            </div>

            <div class="card-modern mb-5">
                <div class="mb-4">
                    <p class="text-muted">
                        WAPAPP allows you to send automated WhatsApp messages based on HubSpot events. Typical use cases
                        include:
                    </p>
                    <ul class="text-muted">
                        <li>Sending welcome messages to new leads.</li>
                        <li>Notifying contacts when deal stages change.</li>
                        <li>Automating follow-ups for form submissions.</li>
                    </ul>
                </div>

                <hr class="my-4">

                <h2 class="h5 fw-bold mb-3">Install the app</h2>
                <div class="mb-4">
                    <p class="text-muted mb-3">
                        Follow these steps to connect WAPAPP to your HubSpot account:
                    </p>
                    <ol class="text-muted mb-0">
                        <li class="mb-2">Click the <strong>"Connect HubSpot Account"</strong> button on the WAPAPP home
                            page.</li>
                        <li class="mb-2">You will be redirected to HubSpot. Select your HubSpot account and click
                            <strong>Choose Account</strong>.
                        </li>
                        <li class="mb-2">Review the requested permissions (Contacts, Deals) and click <strong>Connect
                                app</strong>.</li>
                        <li class="mb-2">You will be redirected back to the WAPAPP dashboard to complete the login
                            process.</li>
                    </ol>
                </div>

                <hr class="my-4">

                <h2 class="h5 fw-bold mb-3">Configure the app</h2>
                <div class="mb-4">
                    <p class="text-muted mb-3">
                        Once installed, you need to set up triggers to start sending messages:
                    </p>
                    <ol class="text-muted mb-0">
                        <li class="mb-2">Log in to your WAPAPP dashboard.</li>
                        <li class="mb-2">Navigate to the <strong>Triggers</strong> section.</li>
                        <li class="mb-2">Click <strong>"Create New Trigger"</strong>.</li>
                        <li class="mb-2">Map a HubSpot event (e.g., "Deal Stage Changed") to a WhatsApp template.</li>
                        <li class="mb-2">Save the trigger. It is now active and will listen for changes in HubSpot.</li>
                    </ol>
                </div>

                <hr class="my-4">

                <h2 class="h5 fw-bold mb-3">Use the app</h2>
                <div class="mb-4">
                    <p class="text-muted">
                        The app runs automatically in the background. When a configured event occurs in HubSpot (e.g., a
                        Contact is created), WAPAPP will automatically send the corresponding WhatsApp message to the
                        contact's phone number.
                    </p>
                    <p class="text-muted">
                        You can view logs of all sent messages in the WAPAPP <strong>Logs</strong> section.
                    </p>
                </div>

                <hr class="my-4">

                <h2 class="h5 fw-bold mb-3">Disconnect the app</h2>
                <div class="mb-4">
                    <p class="text-muted">
                        To stop using WAPAPP and disconnect your account:
                    </p>
                    <ol class="text-muted mb-0">
                        <li class="mb-2"><strong>Log in</strong> to your WAPAPP dashboard.</li>
                        <li class="mb-2">Click on your profile or settings.</li>
                        <li class="mb-2">Click <strong>"Logout"</strong> or <strong>"Disconnect HubSpot"</strong>.</li>
                        <li class="mb-2">Confirm that you want to disconnect. This will stop all message automation.
                        </li>
                    </ol>
                </div>

                <hr class="my-4">

                <h2 class="h5 fw-bold mb-3">Uninstall the app</h2>
                <div class="mb-4">
                    <p class="text-muted">
                        To completely remove the app from your HubSpot portal, follow the instructions in this HubSpot
                        Knowledge Base article:
                    </p>
                    <a href="https://knowledge.hubspot.com/integrations/connect-apps-to-hubspot#uninstall-an-app"
                        target="_blank" class="text-primary text-decoration-underline">
                        Uninstall an app from HubSpot
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        lucide.createIcons();
    </script>
@endpush
