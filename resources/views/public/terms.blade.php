@extends('layouts.app')

@section('title', 'Terms of Service · WAPAPP Integration')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 fw-bold mb-1">Terms of Service</h1>
                    <p class="text-muted mb-0">Last updated: {{ date('F d, Y') }}</p>
                </div>
                <a href="{{ route('home') }}" class="btn btn-outline-soft">
                    <i class="me-2" data-lucide="arrow-left"></i> Back to Home
                </a>
            </div>

            <div class="card-modern p-4 p-md-5">
                <div class="alert alert-warning d-flex align-items-center gap-3 mb-4">
                    <i data-lucide="alert-triangle"></i>
                    <div class="small">
                        <strong>Note to Administrator:</strong> Please replace this placeholder text with your actual Terms
                        of Service before publishing.
                    </div>
                </div>

                <article class="typography">
                    <h4 class="fw-bold mb-3">1. Acceptance of Terms</h4>
                    <p class="text-muted mb-4">
                        By accessing and using this application ("Service"), you accept and agree to be bound by the terms
                        and provision of this agreement. In addition, when using this Service, you shall be subject to any
                        posted guidelines or rules applicable to such services.
                    </p>

                    <h4 class="fw-bold mb-3">2. Service Description</h4>
                    <p class="text-muted mb-4">
                        The Service provides an integration between HubSpot CRM and WAPAPP to enable automated WhatsApp
                        messaging based on CRM events. The Service is provided "as is" and "as available".
                    </p>

                    <h4 class="fw-bold mb-3">3. User Obligations</h4>
                    <p class="text-muted mb-4">
                        You agree to use the Service only for lawful purposes. You represent that you are of legal age to
                        form a binding contract and are not a person barred from receiving services under the laws of the
                        applicable jurisdiction.
                    </p>

                    <h4 class="fw-bold mb-3">4. Disclaimer of Liability</h4>
                    <p class="text-muted mb-4">
                        The Service is not responsible for any messages sent or failed to be sent via WAPAPP. We shall not
                        be liable for any indirect, incidental, special, consequential or punitive damages, including
                        without limitation, loss of profits, data, use, goodwill, or other intangible losses.
                    </p>

                    <h4 class="fw-bold mb-3">5. Termination</h4>
                    <p class="text-muted mb-0">
                        We may terminate or suspend access to our Service immediately, without prior notice or liability,
                        for any reason whatsoever, including without limitation if you breach the Terms.
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
