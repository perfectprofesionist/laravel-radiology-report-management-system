<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Activated</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f8;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        .header {
            background-color: white;
            color: #fff;
            padding: 25px 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            letter-spacing: 0.5px;
            color: #363636 !important;
            margin-top:10px;
        }

        .content {
            padding: 30px;
            font-size: 16px;
            line-height: 1.5;
        }

        .content h3 {
            font-size: 18px;
            margin-bottom: 15px;
        }

        .content p {
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 24px;
            background-color: #007bff;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 15px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }

     
        @media only screen and (max-width: 600px) {
            .container {
                margin: 20px;
                width: 100% !important;
            }

            .btn {
                width: 100%;
                text-align: center;
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="container">

        <div class="header">
             <img src="{{ asset('images/header-logo.png') }}" alt="Logo" style="height: 60px;">
            <h1>Notes Change Request</h1>
        </div>

        <div class="content">
        <p>Hello {{ $notifiable->name }},</p>

            <p>A sub-admin has made changes to the notes for request #{{ $requestListing->exam_id }}.</p>

            <div class="info-box">
                <p><strong>Updated by:</strong> {{ $user->name }}</p>
                <p><strong>Updated at:</strong> {{ $requestListing->notes_updated_at->format('M d, Y H:i') }}</p>
            </div>

            
            <p>Please review the changes by clicking the button below:</p>

            <a href="{{ $url }}" class="btn" style="color: #ffffff;">Review Changes</a>

            <p>If you're unable to click the button, you can copy and paste this URL into your browser:</p>
            <p style="word-break: break-all;">{{ $url }}</p>
        </div>
    </div>
</body>
</html>
