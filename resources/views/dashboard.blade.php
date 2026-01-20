<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HubSpot Integration - WAPAPP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #5B68E8;
            --primary-dark: #4A56D7;
            --success: #10B981;
            --error: #EF4444;
            --text-primary: #1F2937;
            --text-secondary: #6B7280;
            --border: #E5E7EB;
            --bg-secondary: #F9FAFB;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(180deg, #F9FAFB 0%, #FFFFFF 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: var(--text-primary);
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            max-width: 480px;
            width: 100%;
            padding: 48px 40px;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: var(--bg-secondary);
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .logo-icon {
            font-size: 18px;
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }

        .subtitle {
            font-size: 16px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* Alerts */
        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 32px;
            display: flex;
            gap: 12px;
            font-size: 14px;
            line-height: 1.5;
        }

        .alert-success {
            background: #ECFDF5;
            color: #065F46;
            border: 1px solid #A7F3D0;
        }

        .alert-error {
            background: #FEF2F2;
            color: #991B1B;
            border: 1px solid #FECACA;
        }

        .alert-icon {
            flex-shrink: 0;
            font-size: 20px;
        }

        /* Features */
        .features {
            margin: 32px 0;
        }

        .feature {
            display: flex;
            gap: 12px;
            padding: 16px 0;
        }

        .feature:not(:last-child) {
            border-bottom: 1px solid var(--border);
        }

        .feature-icon {
            flex-shrink: 0;
            font-size: 20px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-secondary);
            border-radius: 8px;
        }

        .feature-content h3 {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 4px;
            color: var(--text-primary);
        }

        .feature-content p {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        /* Button */
        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 14px 24px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(91, 104, 232, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-icon {
            font-size: 18px;
        }

        /* Status Badge */
        .status {
            margin-top: 24px;
            text-align: center;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #ECFDF5;
            color: var(--success);
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            background: var(--success);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        /* Responsive */
        @media (max-width: 640px) {
            .container {
                padding: 32px 24px;
            }

            h1 {
                font-size: 24px;
            }

            .subtitle {
                font-size: 15px;
            }
        }

        /* Accessibility */
        .btn:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <span class="logo-icon">🚀</span>
                <span>WAPAPP × HubSpot</span>
            </div>
            <h1>Connect HubSpot</h1>
            <p class="subtitle">Sync your HubSpot CRM with WhatsApp messaging</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                <span class="alert-icon" aria-hidden="true">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error" role="alert">
                <span class="alert-icon" aria-hidden="true">✕</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="features">
            <div class="feature">
                <div class="feature-icon" aria-hidden="true">📊</div>
                <div class="feature-content">
                    <h3>Real-Time Sync</h3>
                    <p>Auto-sync contacts and deals</p>
                </div>
            </div>

            <div class="feature">
                <div class="feature-icon" aria-hidden="true">⚡</div>
                <div class="feature-content">
                    <h3>Smart Triggers</h3>
                    <p>Automated WhatsApp workflows</p>
                </div>
            </div>

            <div class="feature">
                <div class="feature-icon" aria-hidden="true">🔒</div>
                <div class="feature-content">
                    <h3>Secure Connection</h3>
                    <p>Enterprise-grade encryption</p>
                </div>
            </div>
        </div>

        <a href="{{ route('hubspot.connect') }}" class="btn">
            <span class="btn-icon" aria-hidden="true">🔗</span>
            <span>Connect to HubSpot</span>
        </a>

        @if (session('success'))
            <div class="status">
                <div class="status-badge">
                    <span class="status-dot" aria-hidden="true"></span>
                    <span>Connected</span>
                </div>
            </div>
        @endif
    </div>
</body>

</html>
