<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect HubSpot · WAPAPP Integration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --hubspot-orange: #ff7a59;
            --hubspot-dark: #d9552f;
            --whatsapp-green: #25D366;
            --success: #10B981;
            --error: #EF4444;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --border: #e2e8f0;
            --bg-soft: #f8fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: var(--text-primary);
            line-height: 1.6;
            background:
                radial-gradient(ellipse at top left, rgba(255, 122, 89, 0.12) 0%, transparent 50%),
                radial-gradient(ellipse at bottom right, rgba(37, 211, 102, 0.1) 0%, transparent 50%),
                linear-gradient(135deg, #fefefe 0%, #f8fafc 100%);
        }

        .page-container {
            display: flex;
            gap: 0;
            max-width: 1100px;
            width: 100%;
            background: #ffffff;
            border-radius: 32px;
            overflow: hidden;
            box-shadow:
                0 25px 50px -12px rgba(0, 0, 0, 0.08),
                0 0 0 1px rgba(0, 0, 0, 0.03);
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Left Panel - Branding */
        .brand-panel {
            flex: 1.1;
            background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%);
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .brand-panel::before {
            content: "";
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 122, 89, 0.15), transparent 70%);
            pointer-events: none;
        }

        .brand-panel::after {
            content: "";
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(37, 211, 102, 0.12), transparent 70%);
            pointer-events: none;
        }

        .brand-header {
            position: relative;
            z-index: 1;
        }

        .brand-logos {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 40px;
        }

        .logo-box {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-hubspot {
            background: linear-gradient(135deg, var(--hubspot-orange), var(--hubspot-dark));
            box-shadow: 0 8px 24px rgba(255, 122, 89, 0.4);
        }

        .logo-whatsapp {
            background: linear-gradient(135deg, #25D366, #128C7E);
            box-shadow: 0 8px 24px rgba(37, 211, 102, 0.4);
        }

        .logo-box i {
            width: 28px;
            height: 28px;
            color: white;
        }

        .brand-connector {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.4);
        }

        .brand-connector i {
            width: 20px;
            height: 20px;
        }

        .brand-title {
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.3;
            margin-bottom: 12px;
        }

        .brand-title span {
            background: linear-gradient(135deg, var(--hubspot-orange), #fbbf24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-subtitle {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.7;
        }

        .feature-list {
            margin-top: 40px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            position: relative;
            z-index: 1;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .feature-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .feature-icon i {
            width: 22px;
            height: 22px;
            color: var(--hubspot-orange);
        }

        .feature-icon.green i {
            color: var(--whatsapp-green);
        }

        .feature-icon.blue i {
            color: #3b82f6;
        }

        .feature-text h3 {
            font-size: 15px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 4px;
        }

        .feature-text p {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.55);
            line-height: 1.5;
        }

        .brand-footer {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-top: 40px;
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 1;
        }

        .trust-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
        }

        .trust-badge i {
            width: 16px;
            height: 16px;
            color: var(--success);
        }

        /* Right Panel - Connect */
        .connect-panel {
            flex: 1;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        .connect-header {
            margin-bottom: 32px;
        }

        .step-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 1px solid #fbbf24;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            color: #92400e;
            margin-bottom: 20px;
        }

        .step-badge i {
            width: 14px;
            height: 14px;
        }

        .connect-title {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 10px;
            letter-spacing: -0.02em;
        }

        .connect-subtitle {
            font-size: 15px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* Alerts */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 16px 18px;
            border-radius: 14px;
            margin-bottom: 24px;
            font-size: 14px;
            line-height: 1.5;
        }

        .alert-success {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .alert-error {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        .alert i {
            width: 22px;
            height: 22px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* Connection Steps */
        .connection-steps {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 32px;
        }

        .connection-step {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 18px;
            background: var(--bg-soft);
            border: 1px solid var(--border);
            border-radius: 14px;
            transition: all 0.2s ease;
        }

        .connection-step:hover {
            background: #fff;
            border-color: var(--hubspot-orange);
            box-shadow: 0 4px 12px rgba(255, 122, 89, 0.1);
        }

        .step-number {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--hubspot-orange), var(--hubspot-dark));
            color: white;
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .step-content h4 {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 2px;
        }

        .step-content p {
            font-size: 13px;
            color: var(--text-secondary);
        }

        /* Connect Button */
        .btn-connect {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            padding: 18px 28px;
            background: linear-gradient(135deg, var(--hubspot-orange), var(--hubspot-dark));
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow:
                0 12px 24px rgba(255, 122, 89, 0.35),
                0 0 0 0 rgba(255, 122, 89, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-connect::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-connect:hover::before {
            left: 100%;
        }

        .btn-connect:hover {
            transform: translateY(-3px);
            box-shadow:
                0 20px 40px rgba(255, 122, 89, 0.4),
                0 0 0 4px rgba(255, 122, 89, 0.15);
        }

        .btn-connect:active {
            transform: translateY(-1px);
        }

        .btn-connect i {
            width: 22px;
            height: 22px;
            transition: transform 0.2s ease;
        }

        .btn-connect:hover i {
            transform: translateX(4px);
        }

        .security-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
            font-size: 12px;
            color: var(--text-secondary);
        }

        .security-note i {
            width: 14px;
            height: 14px;
            color: var(--success);
        }

        @media (max-width: 900px) {
            .page-container {
                flex-direction: column;
                max-width: 500px;
            }

            .brand-panel {
                padding: 32px 28px;
            }

            .connect-panel {
                padding: 32px 28px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 16px;
            }

            .page-container {
                border-radius: 24px;
            }

            .brand-title {
                font-size: 22px;
            }

            .connect-title {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>
    <div class="page-container">
        <!-- Left Panel - Branding -->
        <div class="brand-panel">
            <div class="brand-header">
                <div class="brand-logos">
                    <div class="logo-box logo-hubspot">
                        <i data-lucide="database"></i>
                    </div>
                    <div class="brand-connector">
                        <i data-lucide="arrow-right"></i>
                        <i data-lucide="arrow-right"></i>
                    </div>
                    <div class="logo-box logo-whatsapp">
                        <i data-lucide="message-circle"></i>
                    </div>
                </div>

                <h1 class="brand-title">
                    Connect <span>HubSpot CRM</span> to WhatsApp Automation
                </h1>
                <p class="brand-subtitle">
                    Automate customer conversations with powerful event-driven messaging. Send personalized WhatsApp
                    messages when your CRM data changes.
                </p>
            </div>

            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i data-lucide="zap"></i>
                    </div>
                    <div class="feature-text">
                        <h3>Instant Triggers</h3>
                        <p>Auto-send messages on contact creation, deal updates, and custom events</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon green">
                        <i data-lucide="file-text"></i>
                    </div>
                    <div class="feature-text">
                        <h3>Template Variables</h3>
                        <p>Personalize messages with dynamic HubSpot data like names, deals & more</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon blue">
                        <i data-lucide="shield-check"></i>
                    </div>
                    <div class="feature-text">
                        <h3>Enterprise Security</h3>
                        <p>OAuth 2.0 authentication with encrypted token storage</p>
                    </div>
                </div>
            </div>

            <div class="brand-footer">
                <div class="trust-badge">
                    <i data-lucide="check-circle"></i>
                    <span>Official HubSpot App</span>
                </div>
                <div class="trust-badge">
                    <i data-lucide="lock"></i>
                    <span>GDPR Compliant</span>
                </div>
            </div>
        </div>

        <!-- Right Panel - Connect Action -->
        <div class="connect-panel">
            <div class="connect-header">
                <div class="step-badge">
                    <i data-lucide="sparkles"></i>
                    <span>Step 1 of 2 · Quick Setup</span>
                </div>
                <h2 class="connect-title">Get Started in Seconds</h2>
                <p class="connect-subtitle">
                    Connect your HubSpot account to enable automated WhatsApp messaging for your CRM workflows.
                </p>
            </div>

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    <i data-lucide="check-circle-2"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error" role="alert">
                    <i data-lucide="alert-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="connection-steps">
                <div class="connection-step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h4>Authorize HubSpot Access</h4>
                        <p>Grant read access to contacts and deals</p>
                    </div>
                </div>
                <div class="connection-step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h4>Link WAPAPP Account</h4>
                        <p>Connect your WhatsApp templates</p>
                    </div>
                </div>
                <div class="connection-step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h4>Create Your First Trigger</h4>
                        <p>Start automating in minutes</p>
                    </div>
                </div>
            </div>

            <a href="{{ route('hubspot.connect') }}" class="btn-connect">
                <span>Connect HubSpot Account</span>
                <i data-lucide="arrow-right"></i>
            </a>

            <div class="security-note">
                <i data-lucide="shield-check"></i>
                <span>Secure OAuth connection · Your data stays private</span>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>
