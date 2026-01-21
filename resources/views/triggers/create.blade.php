@extends('layouts.app')

@section('title', 'Create Trigger · WAPAPP for HubSpot')

@push('styles')
    <style>
        .trigger-card {
            position: relative;
            border-radius: 30px;
            padding: 30px 30px 26px;
            background:
                linear-gradient(#ffffff, #ffffff) padding-box,
                linear-gradient(135deg, #ffd4c7, #bfdbfe) border-box;
            border: 1px solid transparent;
            box-shadow:
                0 24px 46px rgba(15, 23, 42, 0.08),
                0 0 0 1px rgba(148, 163, 184, 0.08);
            backdrop-filter: blur(6px);
        }

        .trigger-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 26px;
        }

        .trigger-card-title {
            margin: 0;
            font-size: 26px;
            font-weight: 700;
            color: var(--text-main);
        }

        .trigger-card-subtitle {
            margin: 4px 0 0;
            font-size: 14px;
            color: var(--text-muted);
        }

        .badge-pill::before {
            content: "✅";
            font-size: 14px;
        }

        .form-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr);
            gap: 32px;
        }

        @media (max-width: 900px) {
            .form-layout {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        .section-card {
            border-radius: 22px;
            background: #f9fafb;
            border: 1px solid rgba(226, 232, 240, 0.9);
            padding: 18px 18px 16px;
        }

        .section-card-right {
            background: #fffbf8;
        }

        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .section-title::before {
            content: "⚡";
            font-size: 16px;
        }

        .section-card-right .section-title::before {
            content: "🔗";
        }

        .section-note {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 14px;
        }

        .phone-preview {
            position: relative;
            border-radius: 24px;
            padding: 8px 8px 10px;
            background: linear-gradient(135deg, #075E54, #128C7E);
            color: #ffffff;
            box-shadow:
                0 16px 32px rgba(15, 23, 42, 0.28),
                0 0 0 1px rgba(15, 23, 42, 0.08);
        }

        .phone-preview-notch {
            position: absolute;
            top: 4px;
            left: 50%;
            transform: translateX(-50%);
            width: 40%;
            height: 5px;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.45);
        }

        .phone-preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            margin-bottom: 6px;
            padding: 0 2px;
            opacity: 0.92;
        }

        .phone-preview-screen {
            background: #e5ddd5;
            border-radius: 18px;
            padding: 8px 6px 10px;
            min-height: 110px;
            position: relative;
            overflow: hidden;
        }

        .phone-bubble-out {
            max-width: 88%;
            border-radius: 16px;
            padding: 6px 9px;
            font-size: 11px;
            line-height: 1.35;
            margin-bottom: 4px;
            word-break: break-word;
            background: #dcf8c6;
            align-self: flex-end;
            margin-left: auto;
        }

        #template_preview {
            border: none;
            background: transparent;
            min-height: 40px;
            padding: 0;
            font-size: 11px;
            color: #111827;
        }

        .field-picker {
            max-height: 240px;
            overflow-y: auto;
            background: #ffffff;
            border: 1px solid #cdd3dd;
            padding: 10px;
            position: absolute;
            z-index: 20;
            width: 100%;
            border-radius: 18px;
            box-shadow: 0 22px 45px rgba(15, 23, 42, 0.18), 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .save-button {
            font-weight: 600;
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-dark));
            color: #ffffff;
            padding: 0.9rem 2rem;
            border-radius: 999px;
            border: none;
            font-size: 15px;
            box-shadow: 0 18px 40px rgba(255, 122, 89, 0.35), 0 0 0 2px rgba(255, 122, 89, 0.12);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .save-button::after {
            content: "→";
            font-size: 15px;
        }

        .save-button:hover {
            transform: translateY(-2px);
            filter: brightness(1.03);
            box-shadow: 0 24px 50px rgba(255, 122, 89, 0.42), 0 0 0 2px rgba(255, 122, 89, 0.16);
        }

        .btn-back {
            border-radius: 999px;
            font-size: 13px;
            padding: 0.7rem 1.4rem;
            border-color: #d1d5db;
            color: #4b5563;
            background: #ffffff;
        }

        .btn-back:hover {
            background: #f3f4f6;
        }

        .footer-actions {
            margin-top: 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }
    </style>
@endpush

@section('content')
    <div class="trigger-card">
        <div class="trigger-card-header">
            <div>
                <h1 class="trigger-card-title">Create New Trigger</h1>
                <p class="trigger-card-subtitle">
                    Map a HubSpot event to a WhatsApp template and dynamic fields.
                </p>
            </div>
            <span class="badge-pill badge-hubspot">Step 1 of 1 · Trigger setup</span>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('triggers.store') }}">
            @csrf
            <div class="form-layout">
                <!-- LEFT COLUMN: basic details -->
                <div class="section-card">
                    <h2 class="section-title">Trigger details</h2>
                    <p class="section-note">
                        Configure when and how the WhatsApp message is sent.
                    </p>

                    <div class="mb-3">
                        <label class="form-label">Trigger Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="trigger_name" id="trigger_name"
                            placeholder="e.g. New contact notification" value="{{ old('trigger_name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">HubSpot Event <span class="text-danger">*</span></label>

                        <!-- Event Type Tabs -->
                        <div class="btn-group w-100 mb-2" role="group">
                            <button type="button" class="btn btn-outline-secondary event-tab active"
                                data-category="Contact">👤 Contact</button>
                            <button type="button" class="btn btn-outline-secondary event-tab" data-category="Deal">💰
                                Deal</button>
                        </div>

                        <!-- Hidden input for form submission -->
                        <input type="hidden" name="event" id="event_input" value="{{ old('event') }}" required>

                        <!-- Event Options Container -->
                        <div id="event_options" class="border rounded-3 p-2"
                            style="max-height:200px;overflow-y:auto;background:#fff;">
                            @php
                                $events = \App\Services\HubSpot\HubSpotWebhookProcessor::getSupportedEvents();
                            @endphp
                            @foreach ($events as $category => $categoryEvents)
                                <div class="event-category" data-category="{{ $category }}"
                                    style="{{ $category !== 'Contact' ? 'display:none' : '' }}">
                                    @foreach ($categoryEvents as $eventValue => $eventLabel)
                                        <div class="event-option p-2 rounded-2 mb-1" data-value="{{ $eventValue }}"
                                            style="cursor:pointer;transition:all 0.15s;"
                                            onmouseover="this.style.background='#f0f9ff'"
                                            onmouseout="this.classList.contains('selected') ? null : this.style.background='transparent'"
                                            onclick="selectEvent('{{ $eventValue }}', this)">
                                            <span style="font-size:14px;">{{ $eventLabel }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>

                        <div id="selected_event_display" class="mt-2 p-2 rounded-2"
                            style="background:#dcfce7;display:none;">
                            <small class="text-success fw-bold">✓ Selected: <span id="selected_event_text"></span></small>
                        </div>

                        <small id="webhookTip" class="text-muted d-block mt-2" style="display:none; font-size:11px">
                            ⚠️ Trigger this event once in HubSpot to load dynamic fields.
                        </small>
                    </div>

                    <div class="mb-1">
                        <label class="form-label mt-2">Select Template <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-md-12">
                                <select class="form-select" name="template_uid" id="template_select" required>
                                    <option value="">-- Choose Template --</option>
                                    @foreach ($templates as $tpl)
                                        <option value="{{ $tpl['uid'] ?? '' }}" data-preview="{{ $tpl['body'] ?? '' }}"
                                            data-name="{{ $tpl['name'] ?? '' }}"
                                            {{ old('template_uid') == ($tpl['uid'] ?? '') ? 'selected' : '' }}>
                                            {{ $tpl['name'] ?? 'Unnamed Template' }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="template_name" id="template_name"
                                    value="{{ old('template_name') }}">
                                <div class="tiny-text">
                                    Templates are fetched from your WAPAPP account.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- WhatsApp Preview -->
                    <div class="phone-preview mt-3">
                        <div class="phone-preview-notch"></div>
                        <div class="phone-preview-header">
                            <span>WhatsApp</span>
                            <span>Preview</span>
                        </div>
                        <div class="phone-preview-screen">
                            <div class="phone-bubble-out">
                                <div id="template_preview">Select a template to preview</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: mapping -->
                <div class="section-card section-card-right">
                    <h2 class="section-title">Dynamic mapping</h2>
                    <p class="section-note">
                        Connect HubSpot fields to your WhatsApp message.
                    </p>

                    <div class="mb-3 position-relative">
                        <label class="form-label">Recipient ("To") <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="to" id="to_input"
                                placeholder="e.g. @{{ contact.phone }}" required value="{{ old('to') }}">
                            <button class="btn btn-outline-secondary" type="button"
                                onclick="togglePicker('to_input')">Insert field</button>
                        </div>
                        <div id="field_picker_to_input" class="field-picker d-none"></div>
                        <div class="tiny-text">
                            Use the picker to insert a dynamic phone field from the HubSpot payload.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Template Variables</label>
                        <div id="variables_container"></div>
                        <button type="button" class="btn btn-outline-secondary btn-sm mt-2" onclick="addVariable()">+
                            Add Variable</button>
                        <p class="tiny-text">
                            Variable names should match the placeholders defined in your WhatsApp template.
                        </p>
                    </div>

                    <!-- Sample JSON Preview Panel -->
                    <div class="border rounded-4 p-3 mt-2" style="background:#f9fafb;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold" style="font-size:13px;">📄 Sample Payload Data</span>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-secondary me-1"
                                    onclick="toggleViewMode()" id="viewModeBtn" style="display:none;">
                                    🌳 Tree View
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    onclick="toggleJsonPreview()" id="jsonToggleBtn">
                                    Show Data
                                </button>
                            </div>
                        </div>
                        <div id="jsonPreviewPanel" class="d-none">
                            <div class="mb-2 text-muted" style="font-size:11px;">
                                💡 Click any field path to copy it. Use in your template variables.
                            </div>

                            <!-- Tree View -->
                            <div id="jsonTreeView" class="rounded p-2"
                                style="background:#ffffff;border:1px solid #e5e7eb;max-height:300px;overflow:auto;">
                                <div class="text-muted text-center py-3" style="font-size:12px;">
                                    Select an event type to view available fields...
                                </div>
                            </div>

                            <!-- Raw JSON View (hidden by default) -->
                            <pre id="jsonRawView" class="p-2 rounded d-none"
                                style="background:#1f2937;color:#22c55e;font-size:11px;max-height:300px;overflow:auto;white-space:pre-wrap;">
Select an event type to view sample payload...
                        </pre>
                        </div>
                        <div id="noDataWarning" class="d-none text-warning" style="font-size:12px;">
                            ⚠️ No payload data yet. Trigger this event in HubSpot first to capture sample data.
                        </div>
                        <div class="helper-chip tip-box mt-2" style="font-size:11px;">
                            <strong>Tip:</strong> Click any field to copy its path, then paste in the "Insert field" picker.
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="api_token" value="{{ $token }}" />

            <div class="footer-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-back">
                    ← Back
                </a>
                <button type="submit" class="save-button">
                    <span>Save Trigger</span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    @include('triggers.partials.scripts')
@endpush
