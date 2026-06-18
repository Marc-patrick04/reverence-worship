<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $announcement->title }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #667eea 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
            color: #555;
        }
        .announcement-title {
            font-size: 22px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        .announcement-content {
            font-size: 16px;
            line-height: 1.8;
            color: #4a5568;
            margin-bottom: 25px;
        }
        .announcement-content p {
            margin-bottom: 15px;
        }
        .meta-info {
            background: #f7fafc;
            padding: 15px 20px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 13px;
            color: #718096;
            border-left: 3px solid #667eea;
        }
        .meta-info p {
            margin: 5px 0;
        }
        .footer {
            background: #f7fafc;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #a0aec0;
            border-top: 1px solid #e2e8f0;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
            font-size: 14px;
        }
        .button:hover {
            background: #5a67d8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name', 'Reverence Worship') }}</h1>
            <p>Official Announcement</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Dear <strong>{{ $user->name }}</strong>,
            </div>
            
            <div class="announcement-title">
                {{ $announcement->title }}
            </div>
            
            <div class="announcement-content">
                {!! nl2br(e($announcement->content)) !!}
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>You are receiving this because you are a registered user.</p>
            <p><small> please do not reply.</small></p>
        </div>
    </div>
</body>
</html>