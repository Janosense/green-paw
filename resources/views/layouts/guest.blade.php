<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Green Paw LMS')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg-primary: #0a0f0d;
            --bg-secondary: #111a16;
            --bg-card: #162019;
            --bg-input: #0d1511;
            --border: #1e3028;
            --border-focus: #2dd4a0;
            --text-primary: #e8f5ee;
            --text-secondary: #8ba89a;
            --text-muted: #5a7a6a;
            --accent: #2dd4a0;
            --accent-dim: #1a8060;
            --accent-glow: rgba(45, 212, 160, 0.15);
            --danger: #ef4444;
            --radius-sm: 6px;
            --radius: 10px;
            --radius-lg: 16px;
            --transition: 200ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Ambient background */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(ellipse at 30% 50%, rgba(45, 212, 160, 0.06) 0%, transparent 50%),
                radial-gradient(ellipse at 70% 80%, rgba(26, 128, 96, 0.04) 0%, transparent 50%);
            animation: drift 20s ease-in-out infinite;
            z-index: 0;
        }

        @keyframes drift {

            0%,
            100% {
                transform: translate(0, 0);
            }

            50% {
                transform: translate(-30px, 20px);
            }
        }

        .auth-container {
            width: 100%;
            max-width: 420px;
            padding: 24px;
            position: relative;
            z-index: 1;
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 40px;
        }

        .auth-brand-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, var(--accent), #0f9b6e);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 16px;
            box-shadow: 0 8px 30px rgba(45, 212, 160, 0.2);
        }

        .auth-brand-title {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .auth-brand-title span {
            color: var(--accent);
        }

        .auth-brand-subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 6px;
        }

        .auth-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 32px;
            box-shadow: 0 4px 40px rgba(0, 0, 0, 0.3);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-family: inherit;
            font-size: 14px;
            transition: border-color var(--transition);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .form-error {
            font-size: 12px;
            color: var(--danger);
            margin-top: 4px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px 20px;
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all var(--transition);
        }

        .btn-primary {
            background: var(--accent);
            color: var(--bg-primary);
        }

        .btn-primary:hover {
            background: #3ae4b0;
            box-shadow: 0 0 20px var(--accent-glow);
        }

        .btn-google {
            background: #fff;
            color: #333;
            border: 1px solid #ddd;
        }

        .btn-google:hover {
            background: #f8f8f8;
        }

        .btn-google svg {
            width: 18px;
            height: 18px;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 24px 0;
            color: var(--text-muted);
            font-size: 13px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .auth-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: var(--text-muted);
        }

        .auth-footer a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .checkbox-row input {
            accent-color: var(--accent);
            width: 16px;
            height: 16px;
        }

        .checkbox-row label {
            font-size: 13px;
            color: var(--text-secondary);
            cursor: pointer;
        }

        .alert {
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            margin-bottom: 16px;
            font-size: 13px;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--danger);
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-card {
            animation: fadeUp 0.5s ease-out;
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <div class="auth-brand">
            <div class="auth-brand-icon">🐾</div>
            <h1 class="auth-brand-title">Green<span>Paw</span></h1>
            <p class="auth-brand-subtitle">Learning Management System</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="auth-card">
            @yield('content')
        </div>

        <div class="auth-footer">
            @yield('footer')
        </div>
    </div>
</body>

</html>