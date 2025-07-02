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
							<a href="{{ route('register') }}">Register</a>
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
				<h1>Welcome Back</h1>
            	<p>Please log in to access your dashboard and manage your account.</p>
			</div>



		</div>
	</section>
	
	<section class="temp-form-main">
		<div class="container">
			<div class="row">
				<div class="temp-form-inn">
					<form id="loginForm" method="POST" action="{{ route('login') }}">
						@csrf
						<div class="register-form admin-login">
							<div class="form-field-row ">
								<div class="form-field ">
									<label for="username_email">Username / Email*</label>   
									<input id="username_email" type="text" name="username_email" value="{{ old('username_email') }}" placeholder="Antoine Laurent" autocomplete="new-password"
									class="@error('username_email') is-invalid @enderror">
									@error('username_email')
										@if( $message != "These credentials do not match our records." && $message != "Your account is not active. Please contact support.")
											<span class="invalid-feedback" role="alert">
												<strong>{{ $message }}</strong>
											</span>
										@endif
									@enderror
								</div>
							</div>
							<div class="form-field-row">
								<div class="password-container form-field">
									<label for="password">Password*</label>
									<div class="input-wrapper">
										<input type="password" id="password" name="password" value="{{ old('password') }}" autocomplete="new-password" placeholder="********" 
										class="@error('password') is-invalid @enderror"/>
									
										<img src="{{ asset('images/password-icon.png') }}" alt="Show Password" class="toggle-visibility" id="showIcon" onclick="togglePassword()" />
										<img src="{{ asset('images/password-icon-open.png') }}" alt="Hide Password" class="toggle-visibility" id="hideIcon" onclick="togglePassword()" />
									</div>
							
									<div class="forgot-link">
										<a href="{{ route('password.request') }}">Forgot Password?</a>
									</div>
								
								</div>
									@error('password')
										@if( $message != "These credentials do not match our records." && $message != "Your account is not active. Please contact support.")
											<span class="invalid-feedback" style="display:block" role="alert">
												<strong>{{ $message }}</strong>
											</span>
										@endif
									@enderror
							</div>
							<!-- <div class="form-field-row full-row pt-2 mb-2">
								<div class="checkbox-form-field ">
									<label class="custom-checkbox" for="remember">Remember Me.
										<input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
										<span class="checkmark"></span>
									</label>
								</div>
							</div> -->

							<div class="form-field-row full-row pt-2 mb-2">
								<div class="checkbox-form-field ">
									<label class="custom-checkbox">By logging in, you agree to our <a href="#">Terms of Service</a>  and <a href="#">Privacy Policy</a> as 
										they apply at the time of access. Your information is used solely 
										to provide and maintain the functionality of the service and to communicate with you regarding your account.
										<input type="checkbox" id="agreeCheckbox" name="agreeCheckbox" {{ old('agreeCheckbox') ? 'checked' : '' }}>
										<span class="checkmark"></span>
									</label>
									<div id="agreeError" class="text-danger mt-1" style="display: none; font-size: 14px;">
										Please agree to the Terms of Service and Privacy Policy before logging in.
									</div>
								</div>
							</div>
								@error('username_email')
									@if( $message == "These credentials do not match our records." || $message == "Your account is not active. Please contact support.")
										<span class="invalid-feedback mb-2" style="display:block" role="alert">
											<strong>{{ $message }}</strong>
										</span>
									@endif
								@enderror

								{{-- @if ($errors->any())
									@foreach ($errors->all() as $error)
											<span class="invalid-feedback mb-2" style="display:block" role="alert"><strong>{{ $error }}</strong></span>
									@endforeach
								@endif --}}

							<div class="form-field-row button-fieldd">
								<!-- <a href="#">Log In</a> -->
							
								<button type="submit" class="btn btn-primary">
									{{ __('Log In') }}
								</button>
							</div>
							<div class="all-readsy-member-row ">
								<h6>Not a member? <a href="{{ route('register') }}">Register</a></h6>
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
		$('#hideIcon').hide(); // Initially hide "eye open" icon

		// $('#agreeCheckbox').on('change', function () {
		// 	if ($(this).is(':checked')) {
		// 		$('#agreeError').hide();
		// 	}
		// });

	// validate js for the input fields and checkbox 
		$('#loginForm').validate({
			
			errorElement: 'small',
			errorClass: 'text-danger d-block mt-1',

			errorPlacement: function(error, element) {
				if (element.attr('type') === 'checkbox') {
					error.insertAfter(element.closest('label'));
				} else {
					error.insertAfter(element);
				}
			},

			rules: {
				username_email: {
					required: true,
					minlength: 4,
					maxlength: 255
				},
				password: {
					required: true,
					minlength: 4,
					maxlength: 50,
				},
				agreeCheckbox: {
					required: true
				}
			},

			messages: {
				username_email: {
					required: "Please enter your username or email.",
					minlength: "Must be at least 4 characters.",
					maxlength: "Must be less than 255 characters."
				},
				password: {
					required: "Password is required.",
					minlength: "Password must be at least 6 characters.",
					maxlength: "Password must be less than 50 characters."
				},
				agreeCheckbox: {
					required: "You must agree to the Terms and Privacy Policy."
				}
			},
			
			// Hide server-side error before validation starts
			onfocusout: function(element) {
				$('.invalid-feedback.mb-2').css('display', 'none');
				this.element(element);
			},
			onkeyup: function(element) {
				$('.invalid-feedback.mb-2').css('display', 'none');
				this.element(element);
			},
			onclick: function(element) {
				$('.invalid-feedback.mb-2').css('display', 'none');
				this.element(element);
			},
			// highlight: function(element) {
			// 	$('.invalid-feedback.mb-2').css('display', 'none');
			// 	$(element).addClass('is-invalid');
			// },
			// unhighlight: function(element) {
			// 	$(element).removeClass('is-invalid');
			// },

			submitHandler: function (form) {
				$('#agreeError').hide(); // Hide custom error if jQuery Validate passes
			  	$('.invalid-feedback.mb-2').css('display', 'none');
				form.submit();
			}
		});
		});

	function togglePassword() {
		const passwordInput = document.getElementById('password');
		const showIcon = document.getElementById('showIcon');
		const hideIcon = document.getElementById('hideIcon');

		const isPassword = passwordInput.type === 'password';
		passwordInput.type = isPassword ? 'text' : 'password';

		if (isPassword) {
			showIcon.style.display = 'none';
			hideIcon.style.display = 'inline';
		} else {
			showIcon.style.display = 'inline';
			hideIcon.style.display = 'none';
		}
	}

</script>
@endpush
	
@endsection

