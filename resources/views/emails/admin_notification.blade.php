<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New User Registration</title>
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
            color: black !important;
            margin-top:10px;
        }

        .content {
            padding: 30px;
        }

        .content p {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .details p {
            font-size: 15px;
            margin: 6px 0;
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
            <h1>New User Registration</h1>
        </div>
        <div class="content">
            <p>A new user has signed up. Please review the details and activate their account:</p>

            <div class="details">
                <p><strong>Name:</strong> {{ $user->username }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
            </div>

            {{-- <a href="{{ route('admin.activate_user', $user->uuid) }}" class="btn" target="_blank">
                Check details to Activate
            </a> --}}
            <a href="{{ route('check.user-details', $user->uuid) }}" class="btn" target="_blank">
                Check details to Activate
            </a>

        </div>
    </div>
</body>
</html>
