<script>
    let fieldData = {};

    // Event Tab Switching
    document.querySelectorAll('.event-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.event-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const category = this.dataset.category;
            document.querySelectorAll('.event-category').forEach(cat => {
                cat.style.display = cat.dataset.category === category ? 'block' : 'none';
            });
        });
    });

    // Select Event
    function selectEvent(value, element) {
        document.querySelectorAll('.event-option').forEach(opt => {
            opt.classList.remove('selected');
            opt.style.background = 'transparent';
        });

        element.classList.add('selected');
        element.style.background = '#dbeafe';

        document.getElementById('event_input').value = value;

        const display = document.getElementById('selected_event_display');
        display.style.display = 'block';
        document.getElementById('selected_event_text').textContent = element.textContent.trim();

        document.getElementById('webhookTip').style.display = 'block';
        fetchFields(value);
    }

    // Initialize event selection from existing value
    const currentEvent = document.getElementById('event_input').value;
    if (currentEvent) {
        const option = document.querySelector('.event-option[data-value="' + currentEvent + '"]');
        if (option) {
            const category = option.closest('.event-category').dataset.category;
            document.querySelectorAll('.event-tab').forEach(t => t.classList.remove('active'));
            document.querySelector('.event-tab[data-category="' + category + '"]')?.classList.add('active');
            document.querySelectorAll('.event-category').forEach(cat => {
                cat.style.display = cat.dataset.category === category ? 'block' : 'none';
            });
            selectEvent(currentEvent, option);
        }
    }

    async function fetchFields(eventName) {
        if (!eventName) return;

        const res = await fetch('{{ route('api.payload-fields') }}?event=' + encodeURIComponent(eventName));
        const data = await res.json();
        fieldData = data || {};

        if (Object.keys(fieldData).length === 0) {
            console.warn("⚠️ No payload data found for event:", eventName);
        }

        if (typeof updateJsonPreview === 'function') {
            updateJsonPreview();
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
                resultContainer.innerHTML = '<div class="text-muted small">No matching fields</div>';
                return;
            }

            filtered.forEach(path => {
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
            });
        }

        searchInput.addEventListener('input', (e) => renderButtons(e.target.value));

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
        const rowId = 'var_' + Math.random().toString(36).substring(2, 8);
        const container = document.getElementById('variables_container');
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
        preview.textContent = selectedOption?.getAttribute('data-preview') || '<em>No preview available</em>';
        document.getElementById('template_name').value = selectedOption?.getAttribute('data-name') || '';
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.input-group') && !e.target.closest('.field-picker')) {
            document.querySelectorAll('.field-picker').forEach(el => el.classList.add('d-none'));
        }
    });

    // JSON Tree View Functions
    let isTreeView = true;

    function toggleJsonPreview() {
        const panel = document.getElementById('jsonPreviewPanel');
        const btn = document.getElementById('jsonToggleBtn');
        const modeBtn = document.getElementById('viewModeBtn');
        if (panel.classList.contains('d-none')) {
            panel.classList.remove('d-none');
            btn.textContent = 'Hide Data';
            modeBtn.style.display = 'inline-block';
        } else {
            panel.classList.add('d-none');
            btn.textContent = 'Show Data';
            modeBtn.style.display = 'none';
        }
    }

    function toggleViewMode() {
        const treeView = document.getElementById('jsonTreeView');
        const rawView = document.getElementById('jsonRawView');
        const btn = document.getElementById('viewModeBtn');

        isTreeView = !isTreeView;
        if (isTreeView) {
            treeView.classList.remove('d-none');
            rawView.classList.add('d-none');
            btn.textContent = '📋 Raw JSON';
        } else {
            treeView.classList.add('d-none');
            rawView.classList.remove('d-none');
            btn.textContent = '🌳 Tree View';
        }
    }

    function updateJsonPreview() {
        const treeView = document.getElementById('jsonTreeView');
        const rawView = document.getElementById('jsonRawView');
        const warning = document.getElementById('noDataWarning');

        if (Object.keys(fieldData).length === 0) {
            warning.classList.remove('d-none');
            treeView.innerHTML =
                '<div class="text-muted text-center py-3" style="font-size:12px;">No data available</div>';
            rawView.textContent = 'No sample data available yet...';
        } else {
            warning.classList.add('d-none');
            treeView.innerHTML = buildTreeView(fieldData);
            rawView.textContent = JSON.stringify(fieldData, null, 2);
        }
    }

    function buildTreeView(data) {
        let html = '<div style="font-family:monospace;font-size:12px;">';

        const groups = {};
        Object.keys(data).forEach(key => {
            const parts = key.split('.');
            const group = parts[0];
            if (!groups[group]) groups[group] = [];
            groups[group].push({
                key,
                value: data[key],
                subKey: parts.slice(1).join('.') || key
            });
        });

        Object.keys(groups).forEach(group => {
            const icon = getGroupIcon(group);
            html += `<div class="mb-2">
                <div class="fw-bold mb-1" style="color:#6366f1;cursor:pointer;" onclick="toggleGroup('${group}')">
                    ${icon} ${group.charAt(0).toUpperCase() + group.slice(1)}
                    <span style="color:#9ca3af;font-weight:normal;font-size:10px;">(${groups[group].length} fields)</span>
                </div>
                <div id="group_${group}" class="ms-3" style="border-left:2px solid #e5e7eb;padding-left:12px;">`;

            groups[group].forEach(item => {
                const valueType = getValueType(item.value);
                const valueColor = getValueColor(valueType);
                const displayValue = formatValue(item.value);

                html += `<div class="py-1 px-2 rounded field-row" 
                    style="cursor:pointer;transition:all 0.15s;" 
                    onclick="copyFieldPath('${item.key}')"
                    onmouseover="this.style.background='#f0f9ff'"
                    onmouseout="this.style.background='transparent'">
                    <span style="color:#1f2937;font-weight:500;">${item.subKey}</span>
                    <span style="color:#9ca3af;margin:0 4px;">:</span>
                    <span style="color:${valueColor};">${displayValue}</span>
                    <span class="ms-2 copy-hint" style="color:#3b82f6;font-size:10px;opacity:0.7;">📋 click to copy</span>
                </div>`;
            });

            html += '</div></div>';
        });

        html += '</div>';
        return html;
    }

    function getGroupIcon(group) {
        const icons = {
            'contact': '👤',
            'deal': '💰',
            'event': '⚡',
            'portal_id': '🔗',
            'occurred_at': '🕐',
            'raw': '📦'
        };
        return icons[group] || '📁';
    }

    function getValueType(value) {
        if (value === null || value === undefined) return 'null';
        if (typeof value === 'string') return 'string';
        if (typeof value === 'number') return 'number';
        if (typeof value === 'boolean') return 'boolean';
        if (Array.isArray(value)) return 'array';
        return 'object';
    }

    function getValueColor(type) {
        const colors = {
            'string': '#059669',
            'number': '#0891b2',
            'boolean': '#7c3aed',
            'null': '#6b7280',
            'array': '#ea580c',
            'object': '#dc2626'
        };
        return colors[type] || '#1f2937';
    }

    function formatValue(value) {
        if (value === null || value === undefined) return 'null';
        if (typeof value === 'string') {
            const display = value.length > 40 ? value.substring(0, 40) + '...' : value;
            return `"${display}"`;
        }
        if (typeof value === 'boolean') return value ? 'true' : 'false';
        if (Array.isArray(value)) return `[${value.length} items]`;
        if (typeof value === 'object') return '{...}';
        return String(value);
    }

    function toggleGroup(group) {
        const el = document.getElementById('group_' + group);
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }

    function copyFieldPath(path) {
        const openBrace = String.fromCharCode(123, 123);
        const closeBrace = String.fromCharCode(125, 125);
        const text = openBrace + path + closeBrace;
        navigator.clipboard.writeText(text).then(() => {
            showCopyToast(path);
        });
    }

    function showCopyToast(path) {
        let toast = document.getElementById('copy-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'copy-toast';
            toast.style.cssText =
                'position:fixed;bottom:20px;right:20px;background:#10b981;color:white;padding:12px 20px;border-radius:8px;font-size:13px;z-index:9999;box-shadow:0 4px 12px rgba(0,0,0,0.15);';
            document.body.appendChild(toast);
        }
        const openBrace = String.fromCharCode(123, 123);
        const closeBrace = String.fromCharCode(125, 125);
        toast.innerHTML =
            '✓ Copied: <code style="background:rgba(255,255,255,0.2);padding:2px 6px;border-radius:4px;">' + openBrace +
            path + closeBrace + '</code>';
        toast.style.display = 'block';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 2000);
    }

    // Load template preview on page load
    window.addEventListener('DOMContentLoaded', () => {
        const selectedTemplate = document.getElementById('template_select')?.selectedOptions[0];
        if (selectedTemplate && selectedTemplate.value) {
            const content = selectedTemplate.getAttribute('data-preview') || '<em>No preview available</em>';
            document.getElementById('template_preview').textContent = content;
            document.getElementById('template_name').value = selectedTemplate.getAttribute('data-name') || '';
        }
    });
</script>
