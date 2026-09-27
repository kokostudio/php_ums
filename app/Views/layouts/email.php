<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\View::escape($subject ?? 'System Notification') ?></title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 40px 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        .email-header {
            background-color: #3b82f6;
            color: #ffffff;
            padding: 30px 40px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px;
        }
        .email-footer {
            background-color: #f1f5f9;
            padding: 20px 40px;
            text-align: center;
            font-size: 14px;
            color: #64748b;
        }
        .btn {
            display: inline-block;
            background-color: #3b82f6;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .btn:hover {
            background-color: #2563eb;
        }
        .text-sm {
            font-size: 14px;
        }
        .text-muted {
            color: #64748b;
        }
        .mt-4 {
            margin-top: 16px;
        }
        .mb-4 {
            margin-bottom: 16px;
        }
        .hr {
            border: 0;
            height: 1px;
            background-color: #e2e8f0;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <div class="email-header">
                <h1><?= \App\Core\View::escape(\App\Core\Config::get('APP_NAME', 'PHP UMS')) ?></h1>
            </div>
            <div class="email-body">
                <?= $content ?? '' ?>
            </div>
            <div class="email-footer">
                <p>&copy; <?= date('Y') ?> <?= \App\Core\View::escape(\App\Core\Config::get('APP_NAME', 'PHP UMS')) ?>. All rights reserved.</p>
                <p>If you have any questions, please contact our support team.</p>
            </div>
        </div>
    </div>
</body>
</html>
