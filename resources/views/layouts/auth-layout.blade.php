<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Radiology Report Management System</title> 
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- Stylesheets -->
<link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}?ver={{ time() }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}?ver={{ time() }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-select.min.css') }}?ver={{ time() }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.mCustomScrollbar.css') }}?ver={{ time() }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery-ui.css') }}?ver={{ time() }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/media.css') }}?ver={{ time() }}">

<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Radio+Canada:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet">

<!-- Favicon -->
<link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />

<style>
    /* Loader styles (copied from app.blade.php) */
    .page-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        flex-direction: column;
    }
    .dental-loader-container {
        position: relative;
        width: 200px;
        height: 200px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .dental-loader-ring {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 4px solid transparent;
        border-top-color: #3498db;
        border-right-color: #3498db;
        animation: spin 2s linear infinite;
    }
    .dental-loader-ring:nth-child(2) {
        width: 80%;
        height: 80%;
        border-top-color: #2ecc71;
        border-right-color: #2ecc71;
        animation-duration: 1.5s;
        animation-direction: reverse;
    }
    .dental-loader-ring:nth-child(3) {
        width: 60%;
        height: 60%;
        border-top-color: #e74c3c;
        border-right-color: #e74c3c;
        animation-duration: 1s;
    }
    .dental-icon {
        position: absolute;
        width: 80px;
        height: 80px;
        background: url('/images/loader-logo-1.png') no-repeat center center;
        background-size: contain;
        animation: pulse 2s ease-in-out infinite;
        filter: drop-shadow(0 0 8px rgba(52, 152, 219, 0.3));
    }
    .loader-text-container {
        margin-top: 30px;
        text-align: center;
    }
    .loader-text {
        font-size: 24px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        animation: slideUp 0.5s ease-out;
    }
    .loader-subtext {
        font-size: 16px;
        color: #7f8c8d;
        margin-top: 10px;
        animation: slideUp 0.5s ease-out 0.2s both;
    }
    .loading-dots {
        display: inline-block;
        animation: dots 1.5s infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    @keyframes pulse {
        0% { transform: scale(0.95); filter: drop-shadow(0 0 8px rgba(52, 152, 219, 0.3)); }
        50% { transform: scale(1.05); filter: drop-shadow(0 0 12px rgba(52, 152, 219, 0.5)); }
        100% { transform: scale(0.95); filter: drop-shadow(0 0 8px rgba(52, 152, 219, 0.3)); }
    }
    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    @keyframes dots {
        0%, 20% { content: '.'; }
        40% { content: '..'; }
        60% { content: '...'; }
        80%, 100% { content: ''; }
    }
    .main-content {
        display: none;
    }
    .main-content.loaded {
        display: block;
    }
</style>

</head>
<body class="white-bg">
<!-- Loader (copied from app.blade.php) -->
<div class="page-loader">
    <div class="dental-loader-container">
        <div class="dental-loader-ring"></div>
        <div class="dental-loader-ring"></div>
        <div class="dental-loader-ring"></div>
        <div class="dental-icon"></div>
    </div>
    <div class="loader-text-container">
        <h2 class="loader-text"></h2>
        <p class="loader-subtext">Loading<span class="loading-dots">...</span></p>
    </div>
</div>

<div class="main-content">
    <main>@yield('content')</main>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- JS Files -->

<script src="{{ asset('js/bootstrap.bundle.min.js') }}?ver={{ time() }}"></script>
<script src="{{ asset('js/bootstrap-select.min.js') }}?ver={{ time() }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.js"></script>
<script src="{{ asset('js/jquery-ui.js') }}?ver={{ time() }}"></script>
<script src="{{ asset('js/custom.js') }}?ver={{ time() }}"></script>

<script>
    $(document).ready(function() {
        setTimeout(function() {
            $('.page-loader').fadeOut(800, function() {
                $('.main-content').addClass('loaded');
            });
        }, 1000);
    });
    $(window).on('load', function() {
        $('.page-loader').fadeOut(800, function() {
            $('.main-content').addClass('loaded');
        });
    });
</script>

<script>
        $(document).ready(function() {
            // Function to show SweetAlert with a 2-second timer
            function showAlert(type, title, message) {
                Swal.fire({
                    icon: type,
                    title: title,
                    text: message,
                    confirmButtonText: "OK"
                });
            }

            // Check for Laravel session messages and show appropriate alert
            @if (session('success'))
                showAlert('success', 'Success', "{{ session('success') }}");
            @endif

            @if (session('status'))
                showAlert('success', 'Success', "{{ session('status') }}");
            @endif

            @if (session('error'))
                showAlert('error', 'Error', "{{ session('error') }}");
            @endif

            @if (session('info'))
                showAlert('info', 'Information', "{{ session('info') }}");
            @endif

            @if (session('warning'))
                showAlert('warning', 'Warning', "{{ session('warning') }}");
            @endif

        });
    </script>

@stack('scripts')
</body>
</html>
