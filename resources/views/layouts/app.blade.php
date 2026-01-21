<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WAPAPP for HubSpot')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @stack('head')

    <style>
        /* ========== CSS Variables ========== */
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
            align-items: flex-start;
            padding: 40px 18px;
        }

        /* ========== Animations ========== */
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

        /* ========== Common Components ========== */
        .page-shell {
            width: 100%;
            max-width: 1040px;
            animation: fadeIn 0.35s ease-out;
        }

        .card-modern {
            position: relative;
            border-radius: 24px;
            padding: 28px;
            background:
                linear-gradient(#ffffff, #ffffff) padding-box,
                linear-gradient(135deg, #ffd4c7, #bfdbfe) border-box;
            border: 1px solid transparent;
            box-shadow:
                0 24px 46px rgba(15, 23, 42, 0.08),
                0 0 0 1px rgba(148, 163, 184, 0.08);
            backdrop-filter: blur(6px);
        }

        .badge-pill {
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-hubspot {
            background: #fff5f2;
            color: var(--brand-dark);
            border: 1px solid #ffd4c7;
        }

        .badge-wapapp {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        /* ========== Form Styles ========== */
        .form-label {
            font-weight: 500;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid var(--border-soft);
            padding: 10px 14px;
            font-size: 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(255, 122, 89, 0.15);
        }

        .tiny-text {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* ========== Buttons ========== */
        .btn-brand {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-dark) 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 12px 28px;
            border-radius: 999px;
            transition: all 0.2s ease;
        }

        .btn-brand:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(255, 122, 89, 0.35);
            color: #fff;
        }

        .btn-outline-soft {
            border: 1px solid var(--border-soft);
            background: #fff;
            color: var(--text-muted);
            border-radius: 999px;
            padding: 10px 20px;
        }

        .btn-outline-soft:hover {
            background: #f9fafb;
            color: var(--text-main);
        }

        /* ========== Section Cards ========== */
        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-main);
            margin: 0 0 4px;
        }

        .section-note {
            font-size: 12px;
            color: var(--text-muted);
            margin: 0 0 16px;
        }

        /* ========== Helper Chips ========== */
        .helper-chip {
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 12px;
        }

        .tip-box {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
        }

        /* ========== Field Picker ========== */
        .field-picker {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 100;
            background: #fff;
            border: 1px solid var(--border-soft);
            border-radius: 10px;
            padding: 10px;
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            margin-top: 4px;
        }

        /* ========== Footer Actions ========== */
        .footer-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 20px;
            margin-top: 20px;
            border-top: 1px solid var(--border-soft);
        }

        @stack('styles')
    </style>
</head>

<body>
    <div class="page-shell">
        @yield('content')
    </div>

    @stack('scripts')
</body>

</html>
