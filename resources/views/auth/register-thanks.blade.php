@extends('layouts.auth-layout')

@section('content')
	<!--HEADER START-->	
	<header class="template-header">
		<div class="container">
			<div class="row">
				<div class="col-12 template-header-inn">
					<div class="header-logo">
                            <a href="{{ route('login') }}" class="fw-bold fs-4 text-primary text-decoration-none text-black">
                                Radiology Report Management System
                            </a>
                        </div>
					<div class="header-btn">
						<!-- <a href="{{ route('login') }}">Login</a> -->
							@if ($isrequest == 'RequestSubmitted')
							<a href="{{ route('request-listing.indexuser') }}">My Examinations</a>
						@else
							<a href="{{ route('login') }}">Login</a>
						@endif
					</div>
				</div>
			</div>
			</div>
		</div>
	</header>
	<!--HEADER END-->
	
	<section class="page-banner">
		<div class="container">
			<div class="row">
				@if ($isrequest == 'RequestSubmitted')
					<h1>Patient Request Submitted</h1>
					<p>Thank you for registering.</p>
				@else
					<h1>Registration Request Submitted</h1>
					<p>Thank you for registering.</p>
				@endif
			</div>
		</div>
	</section>
	
	<section class="temp-form-main">
		<div class="container">
			<div class="row">
				<div class="temp-form-inn">
					<div class="register-form register-request">
						<div class="register-inner-wrap">
                            <img src="images/registr-request.svg" alt="">
                            <h3>Your request has been received and is now 
                                <br>under review by our team.</h3>
                            <p> You will be notified via email once your access is approved. In the meantime, 
                                feel free to explore our Help Center or contact support if you have any questions.</p>
                        </div>
                        <div class="register-request-grid">
                            <div class="register-left-inner">
                                <h4>What <br>Happens Next?</h4>
                            </div>
                            <div class="register-right-inner">
                                <ul>
                                    <li>Your details will be reviewed within 1-2 business days</li>
                                    <li>You will receive a confirmation email upon approval</li>
                                    <li>Once approved, you'll gain access to the scan upload portal</li>
                                </ul>
                            </div>
                        </div>
					</div>
				</div>
			</div>
		</div>
	</section>
	
	<!--FOOTER START-->
	<footer class="template-footer">
		<div class="container">
			<div class="row">
				<div class="template-footer-inn">
					<a href="#">Privacy Policy</a>
					<p>© 2025 Lee Feinberg. All rights reserved.</p>
					<a href="#">Terms of Service</a>
				</div>
			</div>
		</div>
	</footer>
	<!--FOOTER END-->
	
@endsection
