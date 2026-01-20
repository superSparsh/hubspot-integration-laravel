<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HubSpot Integration - WAPAPP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Animated background elements */
        body::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            top: -200px;
            right: -200px;
            animation: float 20s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            bottom: -150px;
            left: -150px;
            animation: float 15s ease-in-out infinite reverse;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(50px, 50px) scale(1.1);
            }
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 32px;
            box-shadow:
                0 30px 90px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.5);
            max-width: 700px;
            width: 100%;
            padding: 60px;
            position: relative;
            z-index: 1;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-container {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea15, #764ba215);
            border-radius: 50px;
            animation: glow 3s ease-in-out infinite;
        }

        @keyframes glow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
            }

            50% {
                box-shadow: 0 0 30px rgba(102, 126, 234, 0.5);
            }
        }

        .logo {
            font-size: 32px;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .brand {
            font-size: 20px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        h1 {
            font-size: 42px;
            font-weight: 700;
            background: linear-gradient(135deg, #2d3748, #1a202c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 12px;
            line-height: 1.2;
        }

        .subtitle {
            color: #718096;
            font-size: 18px;
            font-weight: 400;
            line-height: 1.6;
        }

        /* Alert styles */
        .alert {
            padding: 20px 24px;
            border-radius: 16px;
            margin-bottom: 32px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            animation: slideIn 0.4s ease-out;
            border: 1px solid;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border-color: #28a745;
            color: #155724;
        }

        .alert-error {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            border-color: #dc3545;
            color: #721c24;
        }

        .alert-icon {
            font-size: 28px;
            flex-shrink: 0;
            animation: bounce 1s ease-in-out;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .alert-content {
            flex: 1;
        }

        .alert-title {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 4px;
        }

        .alert-message {
            font-size: 15px;
            opacity: 0.9;
        }

        /* Feature cards */
        .features {
            display: grid;
            gap: 20px;
            margin: 32px 0;
        }

        .feature-card {
            background: linear-gradient(135deg, #f7fafc, #edf2f7);
            padding: 24px;
            border-radius: 20px;
            border: 1px solid rgba(102, 126, 234, 0.1);
            transition: all 0.3s ease;
            cursor: default;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(102, 126, 234, 0.15);
            border-color: rgba(102, 126, 234, 0.3);
        }

        .feature-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .feature-icon {
            font-size: 28px;
        }

        .feature-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
        }

        .feature-description {
            color: #4a5568;
            line-height: 1.6;
            font-size: 15px;
        }

        /* Button styles */
        .btn-container {
            display: flex;
            gap: 16px;
            margin-top: 40px;
        }

        .btn {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 18px 32px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 16px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:active {
            transform: translateY(-1px);
        }

        .btn span {
            position: relative;
            z-index: 1;
        }

        /* Status badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 24px;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
            animation: blink 2s ease-in-out infinite;
        }

        @keyframes blink {

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
                padding: 40px 30px;
            }

            h1 {
                font-size: 32px;
            }

            .subtitle {
                font-size: 16px;
            }

            .btn-container {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                <span class="logo">🚀</span>
                <span class="brand">WAPAPP × HubSpot</span>
            </div>
            <h1>Welcome to Your Integration Hub</h1>
            <p class="subtitle">Seamlessly connect HubSpot with your WhatsApp messaging platform</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                <span class="alert-icon">✨</span>
                <div class="alert-content">
                    <div class="alert-title">Success!</div>
                    <div class="alert-message">{{ session('success') }}</div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                <span class="alert-icon">⚠️</span>
                <div class="alert-content">
                    <div class="alert-title">Connection Failed</div>
                    <div class="alert-message">{{ session('error') }}</div>
                </div>
            </div>
        @endif

        <div class="features">
            <div class="feature-card">
                <div class="feature-header">
                    <span class="feature-icon">📊</span>
                    <h3 class="feature-title">Real-Time Sync</h3>
                </div>
                <p class="feature-description">
                    Automatically sync your HubSpot contacts and deals with your messaging platform in real-time.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-header">
                    <span class="feature-icon">⚡</span>
                    <h3 class="feature-title">Smart Triggers</h3>
                </div>
                <p class="feature-description">
                    Set up intelligent workflows that trigger WhatsApp messages based on HubSpot CRM events.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-header">
                    <span class="feature-icon">🔒</span>
                    <h3 class="feature-title">Secure & Encrypted</h3>
                </div>
                <p class="feature-description">
                    Your data is protected with enterprise-grade encryption and secure OAuth 2.0 authentication.
                </p>
            </div>
        </div>

        <div class="btn-container">
            <a href="{{ route('hubspot.connect') }}" class="btn btn-primary">
                <span>🔗</span>
                <span>Connect to HubSpot</span>
            </a>
        </div>

        @if (session('success'))
            <div class="status-badge">
                <span class="status-indicator"></span>
                <span>Connected & Active</span>
            </div>
        @endif
    </div>
</body>

</html>
