<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribed - Travell</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        .unsubscribe-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .success-icon {
            font-size: 64px;
            color: #10b981;
            margin-bottom: 20px;
        }
        h1 {
            color: #1f2937;
            font-size: 28px;
            margin-bottom: 15px;
        }
        p {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.6;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="unsubscribe-card">
        <div class="success-icon">✓</div>
        <h1>You've Been Unsubscribed</h1>
        <p>You will no longer receive email notifications from us. We're sorry to see you go!</p>
        <p class="text-muted small">You can update your preferences anytime by logging into your account.</p>
        <a href="{{ url('/') }}" class="btn btn-primary">Return to Homepage</a>
    </div>
</body>
</html>
