@extends('layouts.app')

@section('title', 'Setup Guide · WAPAPP Integration')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 fw-bold mb-1">Setup Documentation</h1>
                    <p class="text-muted mb-0">Get started with WAPAPP for HubSpot in minutes.</p>
                </div>
                <a href="{{ route('home') }}" class="btn btn-outline-soft">
                    <i class="me-2" data-lucide="arrow-left"></i> Back to Home
                </a>
            </div>

            <div class="card-modern mb-5">
                <h2 class="h5 fw-bold mb-4 text-primary">Installation Steps</h2>

                <!-- Step 1 -->
                <div class="d-flex gap-3 mb-4">
                    <div class="flex-shrink-0">
                        <span
                            class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-bold"
                            style="width: 32px; height: 32px;">1</span>
                    </div>
                    <div>
                        <h3 class="h6 fw-bold mb-2">Connect HubSpot</h3>
                        <p class="text-muted small mb-2">
                            Click the "Connect HubSpot Account" button on the home page. You will be redirected to HubSpot
                            to authorize our application.
                            We require the following permissions:
                        </p>
                        <ul class="text-muted small mb-0">
                            <li><code>crm.objects.contacts.read</code> - To trigger messages when contacts are
                                created/updated.</li>
                            <li><code>crm.objects.deals.read</code> - To trigger messages on deal stages.</li>
                        </ul>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="d-flex gap-3 mb-4">
                    <div class="flex-shrink-0">
                        <span
                            class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-bold"
                            style="width: 32px; height: 32px;">2</span>
                    </div>
                    <div>
                        <h3 class="h6 fw-bold mb-2">Login to WAPAPP</h3>
                        <p class="text-muted small">
                            After authorizing HubSpot, you will be prompted to log in to your WAPAPP account. This links
                            your HubSpot portal to your WhatsApp messaging capability.
                        </p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="d-flex gap-3">
                    <div class="flex-shrink-0">
                        <span
                            class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-bold"
                            style="width: 32px; height: 32px;">3</span>
                    </div>
                    <div>
                        <h3 class="h6 fw-bold mb-2">Create Triggers</h3>
                        <p class="text-muted small mb-0">
                            Navigate to the Dashboard and click "Create New Trigger".
                            You can set up rules like "When Deal moves to 'Closed Won', send 'Congratulations' template".
                        </p>
                    </div>
                </div>
            </div>

            <div class="card-modern">
                <h2 class="h5 fw-bold mb-3">Frequently Asked Questions</h2>

                <div class="accordion accordion-flush" id="accordionFlushExample">
                    <div class="accordion-item bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-transparent shadow-none" type="button"
                                data-bs-toggle="collapse" data-bs-target="#flush-collapseOne">
                                <strong>How do I disconnect?</strong>
                            </button>
                        </h2>
                        <div id="flush-collapseOne" class="accordion-collapse collapse"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body text-muted small">
                                You can uninstall the app directly from your HubSpot settings under "Connected Apps", or
                                click "Logout" from the WAPAPP dashboard.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-transparent shadow-none" type="button"
                                data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo">
                                <strong>Is my data secure?</strong>
                            </button>
                        </h2>
                        <div id="flush-collapseTwo" class="accordion-collapse collapse"
                            data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body text-muted small">
                                Yes. We only store the OAuth tokens required to communicate with HubSpot. We do not store
                                your contact data permanently; it is processed in real-time to send messages.
                            </div>
                        </div>
                    </div>
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
