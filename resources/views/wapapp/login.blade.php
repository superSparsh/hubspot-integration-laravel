<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect to WhatsApp Automation Platform (WAPAPP)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-primary: #ff7a59;
            --brand-dark: #d9552f;
            --brand-light: #fff5f2;
            --accent-blue: #2563eb;
            --accent-soft-blue: #dbeafe;
            --accent-soft-yellow: #fef3c7;
            --text-main: #1f2933;
            --text-muted: #6b7280;
            --border-soft: #e5e7eb;
            --error-bg: #fef2f2;
            --error-border: #fecaca;
            --error-text: #b91c1c;
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
                #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 24px;
            color: var(--text-main);
        }

        .page-wrapper {
            width: 100%;
            max-width: 960px;
            background: #ffffff;
            border-radius: 26px;
            box-shadow:
                0 24px 60px rgba(15, 24, 40, 0.12),
                0 0 0 1px rgba(148, 163, 184, 0.12);
            overflow: hidden;
            display: flex;
            flex-direction: row;
            position: relative;
        }

        .page-wrapper::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at -10% 10%, rgba(255, 122, 89, 0.08), transparent 55%),
                radial-gradient(circle at 110% 90%, rgba(34, 197, 94, 0.08), transparent 55%);
        }

        /* Left branding panel */
        .brand-panel {
            position: relative;
            flex: 1.15;
            background: linear-gradient(135deg, #fff5f2, #e0f2fe);
            padding: 28px 32px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid #e5e7eb;
            z-index: 1;
        }

        .brand-panel::after {
            content: "";
            position: absolute;
            right: -60px;
            top: 40%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255, 122, 89, 0.25), transparent 60%);
            opacity: 0.8;
            pointer-events: none;
        }

        .brand-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .brand-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            background: #ffffff;
            border: 1px solid #ffd4c7;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
            font-size: 28px;
        }

        .brand-name {
            font-weight: 700;
            letter-spacing: 0.04em;
            font-size: 13px;
            text-transform: uppercase;
            color: #d9552f;
        }

        .brand-subname {
            font-size: 11px;
            color: #6b7280;
        }

        .brand-pill-small {
            padding: 4px 10px;
            border-radius: 999px;
            background: #fff5f2;
            border: 1px solid #ffd4c7;
            font-size: 11px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #d9552f;
        }

        .brand-content {
            margin-top: 24px;
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(0, 0.9fr);
            gap: 18px;
            align-items: center;
        }

        .brand-tagline {
            font-size: 22px;
            font-weight: 600;
            line-height: 1.35;
            color: #022c22;
        }

        .brand-tagline span {
            color: var(--brand-primary);
        }

        .brand-bullets {
            margin-top: 14px;
            font-size: 13px;
            color: #374151;
            padding-left: 0;
            list-style: none;
        }

        .brand-bullets li {
            margin-bottom: 8px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .brand-bullets li span.icon {
            font-size: 15px;
            margin-top: 1px;
        }

        /* Phone illustration */
        .brand-visual {
            display: flex;
            justify-content: center;
        }

        .phone-mock {
            width: 210px;
            border-radius: 28px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 10px 10px 14px;
            box-shadow:
                0 18px 32px rgba(15, 23, 42, 0.12),
                0 0 0 1px rgba(148, 163, 184, 0.18);
            position: relative;
        }

        .phone-mock::before {
            content: "";
            position: absolute;
            top: 6px;
            left: 50%;
            transform: translateX(-50%);
            width: 58px;
            height: 4px;
            border-radius: 999px;
            background: #e5e7eb;
        }

        .phone-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 14px;
            margin-bottom: 8px;
            font-size: 10px;
            color: #6b7280;
        }

        .phone-header-left {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .phone-avatar {
            width: 20px;
            height: 20px;
            border-radius: 999px;
            background: #ffd4c7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: #d9552f;
            font-weight: 600;
        }

        .phone-title {
            font-size: 10px;
            font-weight: 600;
            color: #111827;
        }

        .bubble {
            max-width: 80%;
            padding: 6px 8px;
            border-radius: 14px;
            font-size: 10px;
            line-height: 1.4;
            margin-bottom: 5px;
        }

        .bubble-incoming {
            background: #fff5f2;
            color: #d9552f;
            border-bottom-left-radius: 4px;
        }

        .bubble-outgoing {
            background: #dcfce7;
            color: #166534;
            border-bottom-right-radius: 4px;
            margin-left: auto;
        }

        .phone-footer-chip {
            margin-top: 6px;
            padding: 4px 6px;
            border-radius: 999px;
            background: #fef3c7;
            font-size: 9px;
            color: #92400e;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .brand-footer {
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 26px;
            color: #4b5563;
        }

        .pill {
            padding: 4px 10px;
            border-radius: 999px;
            background: #fff5f2;
            border: 1px solid #ffd4c7;
            font-size: 11px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #d9552f;
        }

        /* Right login panel */
        .login-panel {
            flex: 1;
            padding: 32px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
            position: relative;
            z-index: 1;
        }

        .login-header {
            margin-bottom: 18px;
        }

        .login-stepper {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .step-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: var(--brand-primary);
        }

        .step-line {
            width: 16px;
            height: 2px;
            border-radius: 999px;
            background: #d1d5db;
        }

        .step-dot-muted {
            background: #d1d5db;
        }

        .login-title {
            font-size: 22px;
            font-weight: 700;
            margin: 0;
            color: var(--text-main);
        }

        .login-subtitle {
            margin-top: 6px;
            font-size: 13px;
            color: var(--text-muted);
        }

        .error {
            background: var(--error-bg);
            border: 1px solid var(--error-border);
            color: var(--error-text);
            padding: 10px 12px;
            border-radius: 10px;
            margin-bottom: 14px;
            font-size: 13px;
        }

        .success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 10px 12px;
            border-radius: 10px;
            margin-bottom: 14px;
            font-size: 13px;
        }

        form.form {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-top: 4px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
        }

        .input-wrapper {
            position: relative;
        }

        .form input[type="email"],
        .form input[type="password"],
        .form input[type="text"] {
            width: 100%;
            border-radius: 999px;
            padding: 0.85rem 0.95rem;
            border: 1px solid var(--border-soft);
            background-color: #f9fafb;
            font-size: 14px;
            outline: none;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
        }

        .form input::placeholder {
            color: #9ca3af;
        }

        .form input:focus {
            background-color: #ffffff;
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 1px rgba(255, 122, 89, 0.15), 0 8px 18px rgba(15, 23, 42, 0.08);
        }

        .toggle-password {
            margin-top: 3px;
            font-size: 12px;
            text-align: right;
            color: var(--brand-primary);
            cursor: pointer;
            user-select: none;
        }

        .toggle-password.notactive {
            color: var(--text-muted);
        }

        .submit {
            margin-top: 6px;
            padding: 0.9rem 0.75rem;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-dark));
            color: #ffffff;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
            letter-spacing: 0.02em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow:
                0 14px 28px rgba(255, 122, 89, 0.35),
                0 0 0 1px rgba(255, 122, 89, 0.15);
            transition: transform 0.12s ease, box-shadow 0.12s ease, filter 0.12s ease;
        }

        .submit span.icon {
            font-size: 16px;
            transition: transform 0.16s ease;
        }

        .submit:hover {
            transform: translateY(-1px);
            filter: brightness(1.03);
            box-shadow:
                0 18px 36px rgba(255, 122, 89, 0.42),
                0 0 0 1px rgba(255, 122, 89, 0.2);
        }

        .submit:hover span.icon {
            transform: translateX(3px);
        }

        .submit:active {
            transform: translateY(0);
            box-shadow:
                0 8px 18px rgba(255, 122, 89, 0.32),
                0 0 0 1px rgba(255, 122, 89, 0.3);
        }

        .helper-text {
            margin-top: 10px;
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        .helper-link {
            color: var(--brand-primary);
            text-decoration: none;
            font-weight: 500;
        }

        .helper-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .page-wrapper {
                flex-direction: column;
                max-width: 480px;
            }

            .brand-panel {
                padding: 18px 20px 18px;
            }

            .brand-content {
                grid-template-columns: minmax(0, 1fr);
            }

            .brand-visual {
                margin-top: 16px;
            }

            .login-panel {
                padding: 22px 20px 24px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 14px;
            }

            .page-wrapper {
                border-radius: 22px;
            }

            .login-title {
                font-size: 19px;
            }
        }
    </style>
</head>

<body>
    <div class="page-wrapper">
        <!-- Left side: branding / value prop -->
        <aside class="brand-panel">
            <div class="brand-top">
                <div class="brand-left">
                    <div class="brand-logo-wrapper">
                        💬
                    </div>
                    <div>
                        <div class="brand-name">WhatsApp Automation Platform</div>
                        <div class="brand-subname">by WAPAPP</div>
                    </div>
                </div>
                <div class="brand-pill-small">
                    <span>⚡</span><span>Event-based automation</span>
                </div>
            </div>

            <div class="brand-content">
                <div class="brand-copy">
                    <div class="brand-tagline">
                        Turn your HubSpot events into<br />
                        <span>instant WhatsApp conversations.</span>
                    </div>

                    <ul class="brand-bullets">
                        <li>
                            <span class="icon">✅</span>
                            <span>Send contact updates, deal notifications & custom alerts.</span>
                        </li>
                        <li>
                            <span class="icon">💬</span>
                            <span>Use pre-approved templates with dynamic HubSpot data.</span>
                        </li>
                        <li>
                            <span class="icon">⏱️</span>
                            <span>No code. Just connect, choose triggers & go live in minutes.</span>
                        </li>
                    </ul>
                </div>

                <!-- Simple phone illustration -->
                <div class="brand-visual">
                    <div class="phone-mock">
                        <div class="phone-header">
                            <div class="phone-header-left">
                                <div class="phone-avatar">WA</div>
                                <div>
                                    <div class="phone-title">WAPAPP Alerts</div>
                                    <div>HubSpot · Connected</div>
                                </div>
                            </div>
                            <div>12:45</div>
                        </div>

                        <div class="bubble bubble-incoming">
                            New contact added: John Doe
                        </div>
                        <div class="bubble bubble-incoming">
                            Thanks for your interest! 😊
                        </div>
                        <div class="bubble bubble-outgoing">
                            Great, looking forward to it.
                        </div>
                        <div class="bubble bubble-incoming">
                            We'll be in touch shortly.
                        </div>

                        <div class="phone-footer-chip">
                            <span>💬</span>
                            <span>Sample WhatsApp conversation</span>
                        </div>

                    </div>
                </div>
            </div>

            <div class="brand-footer">
                <div class="pill">
                    <span>🎯</span><span>Built for HubSpot users</span>
                </div>
                <span>Secure API connection · Your data stays private</span>
            </div>
        </aside>

        <main class="login-panel">
            <header class="login-header">
                <div class="login-stepper">
                    <div class="step-dot" style="background:#10B981;">✓</div>
                    <span style="color:#10B981;">HubSpot Connected</span>
                    <div class="step-line" style="background:var(--brand-primary);"></div>
                    <div class="step-dot"></div>
                    <span>Connect WAPAPP</span>
                </div>

                <h1 class="login-title">Log in to your WAPAPP account</h1>
                <p class="login-subtitle">
                    HubSpot Portal <strong>{{ $hubspotPortalId ?? 'Unknown' }}</strong> is connected. Now link your
                    WAPAPP account to manage triggers.
                </p>
            </header>

            @if ($errors->any())
                <div class="error">{{ $errors->first() }}</div>
            @endif

            @if (session('success'))
                <div class="success">{{ session('success') }}</div>
            @endif

            <form class="form" method="POST" action="{{ route('wapapp.login.post') }}">
                @csrf
                <div class="input-group">
                    <label for="email" class="label">WAPAPP Account Email</label>
                    <div class="input-wrapper">
                        <input type="email" name="email" id="email" autocomplete="email"
                            placeholder="you@brand.com" value="{{ old('email') }}" required />
                    </div>
                </div>

                <div class="input-group">
                    <label for="password" class="label">Password</label>
                    <div class="input-wrapper">
                        <input type="password" name="password" id="password" autocomplete="current-password"
                            placeholder="Enter your password" required />
                    </div>
                    <div class="toggle-password notactive" id="toggle-password" onclick="togglePassword()">Show password
                    </div>
                </div>

                <button class="submit" type="submit">
                    <span>Connect WAPAPP</span>
                    <span class="icon">→</span>
                </button>

                <div class="helper-text">
                    <span>Don't have an account?</span>
                    <a href="https://wapapp.tittu.in/register" class="helper-link" target="_blank">Sign up for
                        WAPAPP</a>
                </div>
            </form>
        </main>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const toggle = document.getElementById('toggle-password');
            if (input.type === 'password') {
                input.type = 'text';
                toggle.textContent = 'Hide password';
                toggle.classList.remove('notactive');
            } else {
                input.type = 'password';
                toggle.textContent = 'Show password';
                toggle.classList.add('notactive');
            }
        }
    </script>
</body>

</html>
