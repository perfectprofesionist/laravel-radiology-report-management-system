<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 Page Expired</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <style>
        body { background: #f8f9fa; color: #343a40; font-family: 'Roboto', Arial, sans-serif; margin: 0; padding: 0; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .container { background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 48px 32px; text-align: center; max-width: 400px; }
        .logo { margin-bottom: 24px; }
        .logo img { height: 48px; }
        h1 { font-size: 3rem; margin: 0 0 12px 0; color: #ffc107; }
        h2 { font-size: 1.5rem; margin: 0 0 16px 0; font-weight: 400; }
        p { color: #6c757d; margin-bottom: 32px; }
        .btn { display: inline-block; padding: 12px 28px; background: #007bff; color: #fff; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 1rem; transition: background 0.2s; }
        .btn:hover { background: #0056b3; }
        @media (max-width: 500px) { .container { padding: 32px 8px; } h1 { font-size: 2.2rem; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="/images/logo.png" alt="Logo">
        </div>
        <h1>419</h1>
        <h2>Page Expired</h2>
        <p>Sorry, your session has expired. Please refresh the page and try again.</p>
        <a href="{{ url('/') }}" class="btn">Go to Dashboard</a>
    </div>
</body>
</html> 