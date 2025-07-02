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
							<a href="{{ route('login') }}">Login</a>
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
				<!-- <h1>Registration Request Submitted</h1>
				<p>Thank you for registering.</p> -->
				<h1>Reset Password</h1>
            	<p>Enter your registered email address below and we'll send you a link to reset your password.</p>
			</div>
		</div>
	</section>
	
	<section class="temp-form-main">
		<div class="container">
			<div class="row">
				<div class="temp-form-inn">
					
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
						<div class="register-form admin-login">
							<div class="form-field-row ">
								<div class="form-field ">
									<label for="email">Email Address</label>   
									<input id="email" type="text" name="email" value="{{ old('email') }}" autofocus placeholder="Enter your email..." autocomplete="new-password"
									class="@error('email') is-invalid @enderror">
									@error('email')
										@if( $message != "These credentials do not match our records.")
											<span class="invalid-feedback" role="alert">
												<strong>{{ $message }}</strong>
											</span>
										@endif
									@enderror
								</div>
							</div>

							<div class="form-field-row button-fieldd">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Send Password Reset Link') }}
                                </button>
							</div>
						</div>
					</form>
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

    
@push('scripts')
<script>

   $(document).ready(function () {
        $('#resetPasswordForm').validate({
            errorElement: 'small',
            errorClass: 'text-danger d-block mt-1',

            errorPlacement: function (error, element) {
                error.insertAfter(element);
            },

            rules: {
                email: {
                    required: true,
                    email: true,
                    maxlength: 255
                }
            },

            messages: {
                email: {
                    required: "Please enter your email address.",
                    email: "Please enter a valid email address.",
                    maxlength: "Email must be less than 255 characters."
                }
            },

            submitHandler: function (form) {
                form.submit();
            }
        });
    });

</script>
@endpush
	
	
@endsection

