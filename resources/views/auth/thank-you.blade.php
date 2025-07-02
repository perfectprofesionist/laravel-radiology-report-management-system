@extends('layouts.auth-layout')

@section('content')
<!-- Page Content -->
<div class="thank-you-container">
    <div class="thank-you-content">
        <div class="thank-you-icon">
            <i class="fas fa-check-circle fa-5x text-success"></i>
        </div>
        <h2 class="thank-you-heading">Thank You!</h2>
        @if(session('message'))
            <p class="thank-you-message">{{ session('message') }}</p>
        @endif
        <!-- <p class="thank-you-message">Your account is now active and ready to use.</p> -->
        <div>
            <a href="{{ route('request-listing.index') }}" class="thank-you-button">Go to Request Listing</a>
        </div>
    </div>
</div>

@endsection
