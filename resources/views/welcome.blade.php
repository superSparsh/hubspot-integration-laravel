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
            --primary: #ff7a59;
            --primary-dark: #d9552f;
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
            background: linear-gradient(180deg, #fff5f2 0%, #FFFFFF 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: var(--text-primary);
            line-height: 1.5;
        }

        .container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 24px 48px rgba(15, 23, 42, 0.1);
            max-width: 520px;
            width: 100%;
            padding: 56px 48px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 18px;
            background: var(--bg-secondary);
            border-radius: 12px;
            margin-bottom: 28px;
            font-size: 15px;
            font-weight: 600;
            color: var(--primary-dark);
            border: 1px solid #ffd4c7;
        }

        .logo-icon {
            font-size: 22px;
        }

        h1 {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 14px;
            letter-spacing: -0.02em;
        }

        .subtitle {
            font-size: 17px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .alert {
            padding: 16px 18px;
            border-radius: 14px;
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

        .features {
            margin: 36px 0;
        }

        .feature {
            display: flex;
            gap: 14px;
            padding: 18px 0;
        }

        .feature:not(:last-child) {
            border-bottom: 1px solid var(--border);
        }

        .feature-icon {
            flex-shrink: 0;
            font-size: 24px;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff5f2;
            border-radius: 12px;
        }

        .feature-content h3 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 4px;
            color: var(--text-primary);
        }

        .feature-content p {
            font-size: 14px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 16px 28px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 12px 24px rgba(255, 122, 89, 0.35);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 32px rgba(255, 122, 89, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-icon {
            font-size: 20px;
        }

        .step-indicator {
            text-align: center;
            margin-top: 28px;
            font-size: 13px;
            color: var(--text-secondary);
        }

        .step-indicator strong {
            color: var(--primary);
        }

        @media (max-width: 640px) {
            .container {
                padding: 40px 28px;
            }

            h1 {
                font-size: 26px;
            }

            .subtitle {
                font-size: 15px;
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
            <p class="subtitle">Link your HubSpot CRM with WhatsApp messaging to automate customer communications</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                <span class="alert-icon">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error" role="alert">
                <span class="alert-icon">✕</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="features">
            <div class="feature">
                <div class="feature-icon">📊</div>
                <div class="feature-content">
                    <h3>Real-Time Sync</h3>
                    <p>Automatically sync your contacts and deals from HubSpot</p>
                </div>
            </div>

            <div class="feature">
                <div class="feature-icon">⚡</div>
                <div class="feature-content">
                    <h3>Smart Triggers</h3>
                    <p>Send WhatsApp messages when contacts or deals update</p>
                </div>
            </div>

            <div class="feature">
                <div class="feature-icon">🔒</div>
                <div class="feature-content">
                    <h3>Secure Connection</h3>
                    <p>Enterprise-grade OAuth with encrypted token storage</p>
                </div>
            </div>
        </div>

        <a href="{{ route('hubspot.connect') }}" class="btn">
            <span class="btn-icon">🔗</span>
            <span>Connect to HubSpot</span>
        </a>

        <div class="step-indicator">
            <strong>Step 1 of 2:</strong> Connect your HubSpot account
        </div>
    </div>
</body>

</html>
