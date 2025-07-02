@extends('layouts.auth-layout')

@section('content')


<style>
     .input-wrappertxt {
        position: relative;
    }

    .input-wrappertxt input {
        padding-right: 40px;
    }

    .input-wrappertxt .toggle-passwordtxt {
        position: absolute;
        top: 45px !important;
        right: 12px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #888;
    }
</style>
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
                    <h1>Set Your New Password</h1>
                    <p>Please choose a strong password for your account.</p>
                </div>
                
            </div>
        </section>

    	<section class="temp-form-main">
		<div class="container">
			<div class="row">
				<div class="temp-form-inn">

                
                        
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="register-form">
                                <!-- Manual Password Section -->
                                <div class="form-field-row full-row custFullWdth">
                                    <div class="form-field">
                                        <label for="email">Email *</label>
                                        <input id="email" name="email" type="email" value="{{ $email ?? old('email') }}" placeholder="Enter your email.." required autocomplete="email" autofocus>
                                    </div>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-field-row full-row">

                                    <div class="form-field input-wrappertxt">
                                        <label for="password">New Password *</label>
                                        <input id="password" name="password" type="password" value="{{ old('password') }}" placeholder="Enter new password" required>
                                        <i class="fa fa-eye toggle-passwordtxt" data-target="#password"></i>
                                    </div>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                     <div class="form-field input-wrappertxt">
                                        <label for="password_confirmation">Confirm Password *</label>
                                        <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Confirm password" required>
                                        <i class="fa fa-eye toggle-passwordtxt" data-target="#password_confirmation"></i>
                                    </div>
                                </div>

                                {{--
                                <div class="separator"><span>OR</span></div>

                                <!-- Password Generator Section -->
                                <div class="form-field-row full-row custPassWtBtn">
                                    <div class="form-field ">
                                        <div class="custPassWtBtnLbl">
                                            <label for="password">Use Password Generator (optional)</label>
                                        </div>
                                        <div class="custPassWtBtnFld">                                            
                                            <input id="generated-password" name="generated-password" type="text" readonly placeholder="Click 'Generate' to create a password">
                                            <button type="button" id="generate-password" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-magic me-1"></i> Generate
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="px-3 bg-light rounded password-options mb-1">
                                    <div class="row g-2">
                                         <label for="password">Password options</label>
                                        <div class="col-3">
                                            <label><input type="checkbox" id="include-uppercase">ABC</label>
                                        </div>
                                        <div class="col-3">
                                            <label><input type="checkbox" id="include-lowercase" checked>abc</label>
                                        </div>
                                        <div class="col-3">
                                            <label><input type="checkbox" id="include-numbers">123</label>
                                        </div>
                                        <div class="col-3">
                                            <label><input type="checkbox" id="include-symbols">#$&</label>
                                        </div>
                                        <div class="form-field-row full-row custFullWdth">
                                            <div class="form-field ">
                                                <label>Password Length</label>
                                                <input id="password-length" name="password-length" type="number"  value="15" min="8" max="50" autocomplete="new-password">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                --}}

                                <div class="form-field-row button-fieldd">
								  <button type="submit" class="btn btn-primary">
                                        {{ __('Reset Password') }}
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

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        // Toggle password visibility
        $('.toggle-passwordtxt').on('click', function () {
            const input = $($(this).data('target'));
            const type = input.attr('type') === 'password' ? 'text' : 'password';
            input.attr('type', type);
            $(this).toggleClass('fa-eye fa-eye-slash');
        });

        // Password Generator Logic
        $('#generate-password').on('click', function () {
            const length = parseInt($('#password-length').val()) || 12;
            let chars = '';
            if ($('#include-lowercase').is(':checked')) chars += 'abcdefghijklmnopqrstuvwxyz';
            if ($('#include-uppercase').is(':checked')) chars += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            if ($('#include-numbers').is(':checked')) chars += '0123456789';
            if ($('#include-symbols').is(':checked')) chars += '!@#$%^&*()_+[]{}|;:,.<>?';

            if (!chars.length) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops!',
                    text: 'Please select at least one character type!',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            let password = '';
            for (let i = 0; i < length; i++) {
                password += chars[Math.floor(Math.random() * chars.length)];
            }

            $('#generated-password').val(password);
            $('#password').val(password);
            $('#password_confirmation').val(password);
        });

        // Trigger password generation on option change
        $('#include-uppercase, #include-lowercase, #include-numbers, #include-symbols').on('change', function () {
            $('#generate-password').trigger('click');
        });

        // Password match validation on form submit
        $('form').on('submit', function (e) {
            if ($('#password').val() !== $('#password_confirmation').val()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Mismatch!',
                    text: 'Passwords do not match!',
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    });
</script>
@endpush
