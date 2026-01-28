@extends('layouts.app')

@section('title', 'Privacy Policy · WAPAPP Integration')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 fw-bold mb-1">Privacy Policy</h1>
                    <p class="text-muted mb-0">Last updated: {{ date('F d, Y') }}</p>
                </div>
                <a href="{{ route('home') }}" class="btn btn-outline-soft">
                    <i class="me-2" data-lucide="arrow-left"></i> Back to Home
                </a>
            </div>

            <div class="card-modern p-4 p-md-5">
                <div class="alert alert-warning d-flex align-items-center gap-3 mb-4">
                    <i data-lucide="shield-alert"></i>
                    <div class="small">
                        <strong>Note to Administrator:</strong> Please replace this placeholder text with your actual
                        Privacy Policy.
                    </div>
                </div>

                <article class="typography">
                    <h4 class="fw-bold mb-3">1. Information We Collect</h4>
                    <p class="text-muted mb-4">
                        We collect information necessary to provide the integration service between HubSpot and WAPAPP. This
                        includes:
                    </p>
                    <ul class="text-muted mb-4 small">
                        <li><strong>HubSpot Account Information:</strong> Portal ID and authentication tokens.</li>
                        <li><strong>CRM Data:</strong> We process Contact and Deal data only in response to configured
                            webhooks. We do not store this data permanently.</li>
                        <li><strong>Usage Data:</strong> We may collect information on how the Service is accessed and used.
                        </li>
                    </ul>

                    <h4 class="fw-bold mb-3">2. How We Use Your Information</h4>
                    <p class="text-muted mb-4">
                        We use the collected data for the sole purpose of executing the automation rules you define. For
                        example, reading a Contact's phone number to send a WhatsApp message via WAPAPP.
                    </p>

                    <h4 class="fw-bold mb-3">3. Data Sharing</h4>
                    <p class="text-muted mb-4">
                        We do not sell, trade, or otherwise transfer your personally identifiable information to outside
                        parties. Your data is only shared with WAPAPP API for the purpose of message delivery.
                    </p>

                    <h4 class="fw-bold mb-3">4. Data Security</h4>
                    <p class="text-muted mb-4">
                        We implement a variety of security measures to maintain the safety of your personal information.
                        OAuth tokens are encrypted at rest.
                    </p>

                    <h4 class="fw-bold mb-3">5. Contact Us</h4>
                    <p class="text-muted mb-0">
                        If there are any questions regarding this privacy policy, you may contact us using the information
                        on our website.
                    </p>
                </article>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        lucide.createIcons();
    </script>
@endpush
