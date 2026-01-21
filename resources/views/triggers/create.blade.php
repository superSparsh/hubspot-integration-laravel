<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Trigger · WAPAPP for HubSpot</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-primary: #ff7a59;
            --brand-dark: #d9552f;
            --brand-light: #fff5f2;
            --border-soft: #e5e7eb;
            --border-medium: #d1d5db;
            --text-main: #111827;
            --text-muted: #6b7280;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(circle at top left, #ffe8e0 0, transparent 55%),
                radial-gradient(circle at bottom right, #dcfce7 0, transparent 55%),
                #f7fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 18px;
        }

        .page-shell {
            width: 100%;
            max-width: 1020px;
            animation: fadeIn 0.35s ease-out;
            display: flex;
            justify-content: center;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

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

        .badge-pill {
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12px;
            background: #fff5f2;
            color: var(--brand-dark);
            border: 1px solid #ffd4c7;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
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

        label.form-label {
            font-weight: 600;
            color: var(--text-main);
            font-size: 14px;
            margin-bottom: 4px;
        }

        .form-control,
        .form-select {
            background-color: #ffffff;
            border-radius: 14px;
            border: 1px solid var(--border-medium);
            font-size: 14px;
            padding: 0.75rem 0.9rem;
            transition: all 0.16s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(255, 122, 89, 0.18);
        }

        .tiny-text {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 4px;
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

        .phone-bubble-in,
        .phone-bubble-out {
            max-width: 88%;
            border-radius: 16px;
            padding: 6px 9px;
            font-size: 11px;
            line-height: 1.35;
            margin-bottom: 4px;
            word-break: break-word;
        }

        .phone-bubble-in {
            background: #141414;
            align-self: flex-start;
        }

        .phone-bubble-out {
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
            box-shadow:
                0 22px 45px rgba(15, 23, 42, 0.18),
                0 2px 8px rgba(0, 0, 0, 0.08);
            animation: pickerPop 0.2s ease-out;
        }

        @keyframes pickerPop {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-outline-secondary {
            border-radius: 999px;
            font-size: 13px;
        }

        .btn-outline-secondary.btn-sm {
            border-radius: 999px;
            font-size: 12px;
        }

        .footer-actions {
            margin-top: 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .btn-back {
            border-radius: 999px;
            font-size: 13px;
            padding: 0.7rem 1.4rem;
            border-color: #d1d5db;
            color: #4b5563;
            background: #ffffff;
            transition: 0.18s;
        }

        .btn-back:hover {
            background: #f3f4f6;
        }

        .save-button {
            font-weight: 600;
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-dark));
            color: #ffffff;
            padding: 0.9rem 2rem;
            border-radius: 999px;
            border: none;
            font-size: 15px;
            box-shadow:
                0 18px 40px rgba(255, 122, 89, 0.35),
                0 0 0 2px rgba(255, 122, 89, 0.12);
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
            box-shadow:
                0 24px 50px rgba(255, 122, 89, 0.42),
                0 0 0 2px rgba(255, 122, 89, 0.16);
        }

        .tip-box {
            border-radius: 18px;
            padding: 12px 13px;
            background: #fff5f2;
            border: 1px dashed #ffd4c7;
            font-size: 12px;
            color: var(--text-muted);
        }

        .tip-box strong {
            color: var(--text-main);
        }
    </style>
</head>

<body>
    <div class="page-shell">
        <div class="trigger-card">
            <div class="trigger-card-header">
                <div>
                    <h1 class="trigger-card-title">Create New Trigger</h1>
                    <p class="trigger-card-subtitle">
                        Map a HubSpot event to a WhatsApp template and dynamic fields.
                    </p>
                </div>
                <span class="badge-pill">
                    Step 1 of 1 · Trigger setup
                </span>
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
                            Choose a HubSpot event and the WhatsApp template you want to send.
                        </p>

                        <div class="mb-3">
                            <label class="form-label">Trigger Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="trigger_name" id="trigger_name"
                                placeholder="e.g. New contact notification" value="{{ old('trigger_name') }}" required>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">HubSpot Event <span class="text-danger">*</span></label>
                            <select class="form-select" name="event" id="event_select" required>
                                <option value="">-- Select Event --</option>
                                <optgroup label="Contact Events">
                                    <option value="contact.created"
                                        {{ old('event') == 'contact.created' ? 'selected' : '' }}>Contact Created
                                    </option>
                                    <option value="contact.updated"
                                        {{ old('event') == 'contact.updated' ? 'selected' : '' }}>Contact Updated
                                    </option>
                                </optgroup>
                                <optgroup label="Deal Events">
                                    <option value="deal.updated" {{ old('event') == 'deal.updated' ? 'selected' : '' }}>
                                        Deal Updated</option>
                                </optgroup>
                            </select>
                            <div class="d-flex align-items-center mt-2">
                                <small id="webhookTip" class="text-muted me-2" style="display:none; font-size:11px">
                                    ⚠️ Create a test contact or deal in HubSpot to trigger this event and load dynamic
                                    fields.
                                </small>
                            </div>
                        </div>

                        <div class="mb-1">
                            <label class="form-label mt-2">Select Template <span class="text-danger">*</span></label>
                            <div class="row g-2">
                                <div class="col-md-12">
                                    <select class="form-select" name="template_uid" id="template_select" required>
                                        <option value="">-- Choose Template --</option>
                                        @foreach ($templates as $tpl)
                                            <option value="{{ $tpl['uid'] ?? '' }}"
                                                data-preview="{{ $tpl['body'] ?? '' }}"
                                                data-name="{{ $tpl['name'] ?? '' }}"
                                                {{ old('template_uid') == ($tpl['uid'] ?? '') ? 'selected' : '' }}>
                                                {{ $tpl['name'] ?? 'Unnamed Template' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="template_name" id="template_name"
                                        value="{{ old('template_name') }}">
                                    <div class="tiny-text">
                                        Template list is pulled from your WAPAPP account.
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <div class="phone-preview">
                                        <div class="phone-preview-notch"></div>
                                        <div class="phone-preview-header">
                                            <span>WAPAPP · Preview</span>
                                            <span>● Online</span>
                                        </div>
                                        <div class="phone-preview-screen d-flex flex-column">
                                            <div class="phone-bubble-in">
                                                Hi! 👋 This is how your WhatsApp<br>message can look.
                                            </div>
                                            <div class="phone-bubble-out">
                                                <div id="template_preview">
                                                    <em>Select a template to preview</em>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: mapping -->
                    <div class="section-card">
                        <h2 class="section-title">Dynamic mapping</h2>
                        <p class="section-note">
                            Choose which HubSpot fields should populate the WhatsApp message.
                        </p>

                        <div class="mb-3 position-relative">
                            <label class="form-label">Recipient ("To") <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="to" id="to_input"
                                    placeholder="e.g. @{{ contact.phone }}" value="{{ old('to') }}" required>
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
                            <button type="button" class="btn btn-outline-secondary btn-sm mt-2"
                                onclick="addVariable()">+ Add Variable</button>
                            <p class="tiny-text">
                                Variable names should match the placeholders defined in your WhatsApp template.
                            </p>
                        </div>

                        <div class="border rounded-4 p-3 mt-1" style="background:#f9fafb;font-size:12px;">
                            <div class="helper-chip tip-box mt-1">
                                <strong>Tip:</strong> First trigger the HubSpot event once
                                (e.g. create a test contact). We'll store the payload and show its fields
                                in the picker so you can map them easily.
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
    </div>

    <script>
        let fieldData = {};

        async function fetchFields(eventName) {
            if (!eventName) return;

            const res = await fetch('{{ route('api.payload-fields') }}?event=' + encodeURIComponent(eventName));
            const data = await res.json();
            fieldData = data || {};

            if (Object.keys(fieldData).length === 0) {
                console.warn("⚠️ No payload data found for event:", eventName);
            }
        }

        function buildFieldPicker(inputId) {
            const picker = document.getElementById('field_picker_' + inputId);
            picker.innerHTML = '';

            const searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.className = 'form-control form-control-sm mb-2';
            searchInput.placeholder = '🔍 Search fields...';

            const resultContainer = document.createElement('div');

            function renderButtons(filter = '') {
                resultContainer.innerHTML = '';
                const filtered = Object.keys(fieldData).filter(path =>
                    path.toLowerCase().includes(filter.toLowerCase())
                );

                if (filtered.length === 0) {
                    const noMatch = document.createElement('div');
                    noMatch.className = 'text-muted small';
                    noMatch.textContent = 'No matching fields';
                    resultContainer.appendChild(noMatch);
                    return;
                }

                for (const path of filtered) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-sm btn-light d-block text-start w-100 mb-1';
                    const openBrace = String.fromCharCode(123, 123);
                    const closeBrace = String.fromCharCode(125, 125);
                    btn.textContent = openBrace + path + closeBrace;
                    btn.onclick = () => {
                        const input = document.getElementById(inputId);
                        input.value += openBrace + path + closeBrace;
                        picker.classList.add('d-none');
                    };
                    resultContainer.appendChild(btn);
                }
            }

            searchInput.addEventListener('input', (e) => {
                renderButtons(e.target.value);
            });

            picker.appendChild(searchInput);
            picker.appendChild(resultContainer);
            renderButtons();
        }

        function togglePicker(inputId) {
            const picker = document.getElementById('field_picker_' + inputId);
            if (picker.classList.contains('d-none')) {
                buildFieldPicker(inputId);
                picker.classList.remove('d-none');
            } else {
                picker.classList.add('d-none');
            }
        }

        function addVariable() {
            const container = document.getElementById('variables_container');
            const rowId = 'var_' + Math.random().toString(36).substr(2, 5);
            const row = document.createElement('div');
            row.className = 'row mb-2 align-items-center';
            row.innerHTML = `
                <div class="col">
                    <input type="text" name="vars[keys][]" class="form-control" placeholder="Variable Name" />
                </div>
                <div class="col position-relative">
                    <div class="input-group">
                        <input type="text" name="vars[values][]" class="form-control" id="${rowId}" placeholder="Dynamic value" />
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePicker('${rowId}')">+</button>
                    </div>
                    <div id="field_picker_${rowId}" class="field-picker d-none"></div>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.row').remove()">🗑️</button>
                </div>
            `;
            container.appendChild(row);
        }

        document.getElementById('template_select')?.addEventListener('change', function() {
            const preview = document.getElementById('template_preview');
            const selectedOption = this.selectedOptions[0];

            const content = selectedOption?.getAttribute('data-preview') || '<em>No preview available</em>';
            preview.textContent = content;

            const name = selectedOption?.getAttribute('data-name') || '';
            document.getElementById('template_name').value = name;
        });

        document.getElementById('event_select')?.addEventListener('change', function() {
            const eventName = this.value;
            fetchFields(eventName);

            const tip = document.getElementById('webhookTip');
            tip.style.display = eventName ? 'inline' : 'none';
        });

        // Initial load
        const initialEvent = document.getElementById('event_select')?.value;
        if (initialEvent) {
            fetchFields(initialEvent);
        }
    </script>
</body>

</html>
