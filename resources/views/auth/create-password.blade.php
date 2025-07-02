@extends('layouts.auth-layout')

@section('content')

<style>
     .input-wrapper {
        position: relative;
    }

    .input-wrapper input {
        padding-right: 40px;
    }

    .input-wrapper .toggle-password {
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

                
                        <form method="POST" action="{{ route('password.store', $user->uuid) }}" class="password-reset-form">
                            @csrf
                            <div class="register-form">
                                <!-- Manual Password Section -->
                                <div class="form-field-row full-row">
                                    <div class="form-field input-wrapper">
                                        <label for="password">New Password *</label>
                                        <input id="password" name="password" type="password" value="{{ old('password') }}" placeholder="Enter your password" required>
                                        <i class="fa fa-eye toggle-password" data-target="#password"></i>
                                    </div>
                                     <div class="form-field input-wrapper">
                                        <label for="password_confirmation">Confirm Password *</label>
                                        <input id="password_confirmation" name="password_confirmation" type="password" value="{{ old('password') }}" placeholder="Confirm password" required>
                                        <i class="fa fa-eye toggle-password" data-target="#password_confirmation"></i>
                                    </div>
                                </div>

                               
                                

                                <div class="form-field-row button-fieldd">
								    <!-- <a href="#"  onclick="event.preventDefault(); this.closest('form').requestSubmit();">Register Now </a> -->
								  <button type="submit" class="btn btn-primary">
                                        {{ __('Set My Password') }}
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
        $('.toggle-password').on('click', function () {
            const input = $($(this).data('target'));
            const type = input.attr('type') === 'password' ? 'text' : 'password';
            input.attr('type', type);
            $(this).toggleClass('fa-eye fa-eye-slash');
        });

        // Password Generator Logic
        // $('#generate-password').on('click', function () {
        //     const length = parseInt($('#password-length').val()) || 12;
        //     let chars = '';
        //     if ($('#include-lowercase').is(':checked')) chars += 'abcdefghijklmnopqrstuvwxyz';
        //     if ($('#include-uppercase').is(':checked')) chars += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        //     if ($('#include-numbers').is(':checked')) chars += '0123456789';
        //     if ($('#include-symbols').is(':checked')) chars += '!@#$%^&*()_+[]{}|;:,.<>?';

        //     if (!chars.length) {
        //         Swal.fire({
        //             icon: 'warning',
        //             title: 'Oops!',
        //             text: 'Please select at least one character type!',
        //             confirmButtonColor: '#3085d6'
        //         });
        //         return;
        //     }

        //     let password = '';
        //     for (let i = 0; i < length; i++) {
        //         password += chars[Math.floor(Math.random() * chars.length)];
        //     }

        //     $('#generated-password').val(password);
        //     $('#password').val(password);
        //     $('#password_confirmation').val(password);
        // });

        // Trigger password generation on option change
        // $('#include-uppercase, #include-lowercase, #include-numbers, #include-symbols').on('change', function () {
        //     $('#generate-password').trigger('click');
        // });

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

        $('.password-reset-form').validate({
            rules: {
                password: {
                    required: true,
                    minlength: 8
                },
                password_confirmation: {
                    required: true,
                    equalTo: '#password'
                }
            },
            messages: {
                password: {
                    required: "Please enter a password",
                    minlength: "Password must be at least 8 characters"
                },
                password_confirmation: {
                    required: "Please confirm your password",
                    equalTo: "Passwords do not match"
                }
            },
            errorElement: 'div',
            errorClass: 'invalid-feedback',
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            },
            errorPlacement: function (error, element) {
                if (element.parent('.input-wrapper').length) {
                    error.appendTo(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });
    });
</script>
@endpush
