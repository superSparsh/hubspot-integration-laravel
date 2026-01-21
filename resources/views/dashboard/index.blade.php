@extends('layouts.app')

@section('title', 'WAPAPP · HubSpot Dashboard')

@push('head')
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
@endpush

@push('styles')
    <style>
        body {
            background:
                radial-gradient(circle at top left, #e0f2fe 0, transparent 55%),
                radial-gradient(circle at bottom right, #dcfce7 0, transparent 55%),
                #f3f4f6;
            align-items: flex-start;
            padding: 32px 16px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 18px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 650;
            color: var(--text-main);
            margin: 0;
        }

        .page-title span {
            color: var(--brand-primary);
        }

        .page-subtitle {
            margin: 4px 0 0;
            font-size: 13px;
            color: var(--text-muted);
        }

        .shop-chip-header {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 12px;
            border: 1px solid #bfdbfe;
            white-space: nowrap;
        }

        .main-card {
            background: #ffffff;
            border-radius: 26px;
            padding: 18px 18px 20px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow:
                0 20px 40px rgba(15, 23, 42, 0.06),
                0 0 0 1px rgba(148, 163, 184, 0.08);
        }

        .profile-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-soft);
            margin-bottom: 8px;
        }

        .profile-main {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .profile-avatar {
            width: 44px;
            height: 44px;
            border-radius: 999px;
            background: #d1fae5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
            color: var(--brand-dark);
        }

        .profile-text-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-main);
            margin: 0 0 2px;
        }

        .profile-text-meta {
            font-size: 12px;
            color: var(--text-muted);
        }

        .profile-right-chip {
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 4px;
        }

        @media (max-width: 700px) {
            .profile-strip {
                flex-direction: column;
                align-items: flex-start;
            }

            .profile-right-chip {
                align-items: flex-start;
            }
        }

        .nav-tabs {
            border-bottom: 1px solid var(--border-soft);
        }

        .nav-tabs .nav-link {
            border: none;
            border-bottom: 2px solid transparent;
            color: var(--text-muted);
            font-weight: 500;
            font-size: 13px;
            padding: 8px 14px;
        }

        .nav-tabs .nav-link.active {
            color: var(--brand-primary);
            border-color: var(--brand-primary);
            background: transparent;
        }

        .nav-tabs .nav-link:hover {
            color: var(--brand-dark);
            border-color: rgba(15, 157, 88, 0.25);
        }

        .tab-wrapper {
            margin-top: 10px;
        }

        .label {
            color: var(--brand-primary);
            font-weight: 600;
            margin-bottom: 4px;
            font-size: 13px;
        }

        .toggle-token {
            text-align: right;
            font-size: 12px;
            color: var(--brand-primary);
            cursor: pointer;
            user-select: none;
            margin-top: 4px;
        }

        .toggle-token.notactive {
            color: var(--text-muted);
        }

        .settings-note {
            margin-top: 8px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .triggers-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            margin-top: 4px;
        }

        .triggers-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-main);
        }

        .triggers-subtitle {
            font-size: 12px;
            color: var(--text-muted);
        }

        .btn-add-trigger {
            background: var(--brand-primary);
            border-color: var(--brand-primary);
            font-size: 13px;
            border-radius: 999px;
            padding: 6px 16px;
            color: #fff;
        }

        .btn-add-trigger:hover {
            background: var(--brand-dark);
            border-color: var(--brand-dark);
        }

        .card-triggers-body {
            padding: 10px 0 0;
        }

        .empty-state {
            text-align: center;
            padding: 32px 12px 34px;
            color: var(--text-muted);
            font-size: 13px;
        }

        .empty-state-icon {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .empty-state strong {
            color: var(--text-main);
        }

        .table-wrapper {
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            background: #ffffff;
        }

        table.dataTable thead th {
            font-size: 12px;
            font-weight: 600;
            color: #4b5563;
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb !important;
        }

        table.dataTable tbody td {
            font-size: 13px;
            border-color: #f3f4f6;
        }

        table.dataTable tbody tr:hover td {
            background-color: #f9fafb;
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 999px !important;
        }

        .dataTables_wrapper .dataTables_filter {
            font-size: 13px;
        }

        .btn-table {
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 999px;
        }

        .btn-table-test {
            border-color: #0ea5e9;
            color: #0369a1;
        }

        .btn-table-test:hover {
            background: #e0f2fe;
            border-color: #0284c7;
        }

        .btn-table-edit {
            border-color: #eab308;
            color: #854d0e;
        }

        .btn-table-edit:hover {
            background: #fef9c3;
            border-color: #d97706;
        }

        .btn-delete-modern {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            border-radius: 999px;
            font-size: 12px;
            padding: 6px 14px;
            transition: all 0.2s ease;
        }

        .btn-delete-modern:hover {
            background: #fecaca;
            color: #7f1d1d;
        }

        .btn-logout {
            background: transparent;
            border: 1px solid #e5e7eb;
            color: var(--text-muted);
            border-radius: 999px;
            font-size: 12px;
            padding: 6px 14px;
        }

        .btn-logout:hover {
            background: #f3f4f6;
            color: var(--text-main);
        }
    </style>
@endpush

@section('content')
    <!-- HEADER -->
    <div class="page-header">
        <div>
            <h2 class="page-title">
                Welcome back, <span>{{ $user['first_name'] ?? 'User' }}</span>!
            </h2>
            <p class="page-subtitle">
                Manage your WhatsApp triggers and settings for your HubSpot account from here.
            </p>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="shop-chip-header">
                <span>🎯</span>
                <span>{{ $connection->hubspot_portal_id ?? ($shopDomain ?? 'Not Connected') }}</span>
            </div>
            <form action="{{ route('wapapp.logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger mt-2" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- MAIN CARD -->
    <div class="main-card">
        <!-- Profile strip -->
        @php
            $initials = '';
            if (!empty($user['first_name'])) {
                $initials .= strtoupper(mb_substr($user['first_name'], 0, 1));
            }
            if (!empty($user['last_name'])) {
                $initials .= strtoupper(mb_substr($user['last_name'], 0, 1));
            }
        @endphp
        <div class="profile-strip">
            <div class="profile-main">
                <div class="profile-avatar">
                    {{ $initials ?: 'WA' }}
                </div>
                <div>
                    <p class="profile-text-title">
                        {{ ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '') }}
                    </p>
                    <div class="profile-text-meta">
                        <div>{{ $user['email'] ?? '' }}</div>
                        @if (!empty($user['phone']))
                            <div>{{ $user['phone'] }}</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="profile-right-chip">
                <span>Logged in to WAPAPP</span>
                <small>Use the tabs below to manage triggers & API token.</small>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="triggers-tab" data-bs-toggle="tab" data-bs-target="#triggers"
                    type="button" role="tab" aria-controls="triggers" aria-selected="true">
                    Triggers
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button"
                    role="tab" aria-controls="settings" aria-selected="false">
                    Settings
                </button>
            </li>
        </ul>

        <div class="tab-content tab-wrapper" id="dashboardTabsContent">
            <!-- SETTINGS TAB -->
            <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                <form method="POST" action="{{ route('api-token.update') }}" class="mt-2">
                    @csrf
                    <div class="mb-3">
                        <label class="label" for="api_token">WAPAPP API Token</label>
                        <input type="password" name="api_token" id="api_token" class="form-control"
                            value="{{ $wapappToken ?? '' }}" />
                        <div class="toggle-token notactive" id="toggle-token" onclick="toggleToken()">
                            Show API Token
                        </div>
                        <div class="settings-note">
                            Paste your API token from the WAPAPP dashboard. This is required to fetch templates and
                            send WhatsApp messages.
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-success btn-sm px-4">Save Token</button>
                    </div>
                </form>
            </div>

            <!-- TRIGGERS TAB -->
            <div class="tab-pane fade show active" id="triggers" role="tabpanel" aria-labelledby="triggers-tab">
                @if ($tokenExists)
                    <div class="triggers-header">
                        <div>
                            <div class="triggers-title">Your Triggers</div>
                            <div class="triggers-subtitle">
                                Create WhatsApp messages for contacts, deals, and other HubSpot events.
                            </div>
                        </div>
                        <a href="{{ route('triggers.create') }}" class="btn btn-add-trigger">+ Add Trigger</a>
                    </div>

                    <div class="card-triggers-body">
                        @if ($triggers->isEmpty())
                            <div class="empty-state">
                                <div class="empty-state-icon">✨</div>
                                <div><strong>No triggers yet.</strong></div>
                                <div class="mt-1">
                                    Start by creating your first trigger using the button above.
                                </div>
                            </div>
                        @else
                            <div class="table-wrapper p-3">
                                <table id="triggersTable" class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Trigger Name</th>
                                            <th>Event</th>
                                            <th>Template Name</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($triggers as $trigger)
                                            <tr>
                                                <td>{{ $trigger->trigger_name }}</td>
                                                <td><code>{{ $trigger->event }}</code></td>
                                                <td>{{ $trigger->template_name }}</td>
                                                <td class="text-end">
                                                    <form method="POST"
                                                        action="{{ route('triggers.test', $trigger->id) }}"
                                                        style="display:inline-block;">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-outline-primary btn-table btn-table-test">Test</button>
                                                    </form>

                                                    <a href="{{ route('triggers.edit', $trigger->uuid) }}"
                                                        class="btn btn-outline-secondary btn-table btn-table-edit ms-1">
                                                        Edit
                                                    </a>

                                                    <button type="button" class="btn-delete-modern ms-1"
                                                        onclick="openDeleteModal({{ $trigger->id }})">
                                                        🗑 Delete
                                                    </button>

                                                    <form method="POST"
                                                        action="{{ route('triggers.destroy', $trigger->id) }}"
                                                        style="display:none;" id="deleteForm_{{ $trigger->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="alert alert-warning mt-2" role="alert">
                        Please add your API token in the <strong>Settings</strong> tab to enable triggers.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:18px;">
                <div class="modal-header" style="border:none;">
                    <h5 class="modal-title">Delete Trigger?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="color:#555;">
                    Are you sure you want to delete this trigger?
                    <br><strong>This action cannot be undone.</strong>
                </div>
                <div class="modal-footer" style="border:none;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleToken() {
            const input = document.getElementById('api_token');
            const toggle = document.getElementById('toggle-token');

            if (input.type === 'password') {
                input.type = 'text';
                toggle.textContent = 'Hide API Token';
                toggle.classList.remove('notactive');
            } else {
                input.type = 'password';
                toggle.textContent = 'Show API Token';
                toggle.classList.add('notactive');
            }
        }

        $(document).ready(function() {
            $('#triggersTable').DataTable({
                pageLength: 10,
                order: [
                    [0, 'asc']
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search triggers..."
                }
            });
        });

        let deleteTargetId = null;

        function openDeleteModal(id) {
            deleteTargetId = id;
            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            modal.show();
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (deleteTargetId) {
                document.getElementById('deleteForm_' + deleteTargetId).submit();
            }
        });
    </script>
@endpush
