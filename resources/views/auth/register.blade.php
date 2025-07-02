@extends('layouts.auth-layout')

@section('content')

      {{-- @if ($errors->any())
        <!-- <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div> -->
    @endif --}}
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
				<h1>Join Our Dental Network</h1>
				<p>Register to access patient referrals, manage your practice profile, <br> and connect with other dental professionals.</p>
			</div>
            
		</div>
	</section>
	
	<section class="temp-form-main">
		<div class="container">
			<div class="row">
				<div class="temp-form-inn">

          
					<form method="POST" id="register-form" action="{{ route('register') }}">
                            @csrf
						<div class="register-form">
							<div class="form-field-row full-row">
								<div class="form-field ">
									<label for="username">Username *</label>
									<input id="username" name="username" type="text" value="{{ old('username') }}" placeholder="Enter username">
                                    <!-- Display error message -->
                                    <small id="username-error" class="text-danger  mt-1"
                                        style="display:none;"></small>
                                    <!-- Suggestions container -->
                                    <div id="username-suggestions" style="display:none;"></div>
                                    @error('username')
                                        <small class="text-danger  d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
								</div>
								<div class="form-field">
									<label for="email">Email Address *</label>
									<input name="email" id="email" type="email" value="{{ old('email') }}" placeholder="Enter email address" >
                                    @error('email')
                                        <small class="text-danger  d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
								</div>
							</div>
							<div class="form-field-row full-row">
								<div class="form-field ">
									<label for="mobile_number">Mobile Number *</label>
									<input id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" type="text" placeholder="Enter mobile number">
                                    @error('mobile_number')
                                        <small class="text-danger  d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
								</div>
								<div class="form-field">
									<label for="dentist_name">Dentist Name *</label>
									<input id="dentist_name" name="dentist_name" type="text" value="{{ old('dentist_name') }}"  placeholder="Enter dentist name">
                                    @error('dentist_name')
                                        <small class="text-danger  d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
								</div>
							</div>
							<div class="form-field-row full-row">
								<div class="form-field">
									<label for="practice_name">Practice Name *</label>
									<input type="text" name="practice_name" id="practice_name" value="{{ old('practice_name') }}" placeholder="Enter practice name">
                                    @error('practice_name')
                                        <small class="text-danger  d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
								</div>
								<div class="form-field">
									<label for="practice_address">Practice Address *</label>
									<input type="text" id="practice_address" name="practice_address" value="{{ old('practice_address') }}" placeholder="Enter practice address">
                                    @error('practice_address')
                                        <small class="text-danger  d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
								</div>
							</div>
							<div class="form-field-row full-row">
								<div class="form-field">
									<label>City/Suburb *</label>
									<select name="city" id="city"> 
										<option value="">-- Select City --</option> 
									</select> 
									@error('city')
                                        <small class="text-danger  d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
								</div>	
								<div class="form-field">
									<label>State *</label>
									<select name="state" id="state"> 
										  	<option value="">-- Select State --</option> 
									</select> 
									@error('state')
                                        <small class="text-danger  d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
								</div>							
								<div class="form-field ">
									<label>Postcode *</label>
									<input type="text" id="post_code" name="post_code" value="{{ old('post_code') }}" placeholder="Enter postcode">
                                       @error('post_code')
                                        <small class="text-danger  d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
								</div>
							</div>
							<div class="form-field-row full-row">
								<div class="form-field">
									<label for="routine_phone">Phone Number for Routine Communication *</label>
									<input id="routine_phone" name="routine_phone" type="text" value="{{ old('routine_phone') }}"  autocomplete="new-password" placeholder="Enter phone number">
                                    @error('routine_phone')
                                        <small class="text-danger  d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
								</div>
								<div class="form-field ">
									<label for="urgent_phone">Phone Number for Urgent Communication *</label>
									<input id="urgent_phone" name="urgent_phone" type="text" value="{{ old('urgent_phone') }}" placeholder="Enter phone number">
                                    @error('urgent_phone')
                                        <small class="text-danger  d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
								</div>
							</div>

							<!-- card details  -->
							<div class="form-field-row full-row custFullWdth" style="display: none;">
								<div class="form-field ">
									<label>Card Details *</label>
									<div id="card-element" class="form-control mb-3"></div>
									<span class="text-muted text-xs d-block mt-0">
										Secure & safe: Adding your card here won't charge you anything right now. It's only saved for faster future payments.
									</span>
								</div>
								<input type="hidden" name="payment_method" id="payment-method">
							</div>


							<div class="form-field-row full-row pt-2 mb-2">
								<div class="checkbox-form-field ">
									<label class="custom-checkbox">I confirm that I am a registered dental professional with AHPRA.
										<input type="checkbox" id="confirm_ahpra" name="confirm_ahpra" value="1" {{ old('confirm_ahpra') ? 'checked' : '' }}>
										<span class="checkmark"></span>
									</label>
									 @error('confirm_ahpra')
                                        <small class="text-danger  d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
								</div>
							</div>
							<div class="form-field-row full-row pt-0 ">
								<div class="checkbox-form-field ">
									<label class="custom-checkbox">I agree to the [<a href="">Terms & Conditions</a>] and [<a href="">Privacy Policy</a>].
										<input type="checkbox" id="agree_terms" name="agree_terms" value="1" {{ old('agree_terms') ? 'checked' : '' }}>
										<span class="checkmark"></span>
									</label>
									 @error('agree_terms')
                                        <small class="text-danger  d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
								</div>
							</div>
							<div class="form-field-row button-fieldd">
								<!-- <a href="#"  onclick="event.preventDefault(); this.closest('form').requestSubmit();">Register Now </a> -->
								  <button type="button" id="register-btn" class="btn btn-primary">
                                        {{ __('Register Now') }}
                                    </button>
							</div>
							<div class="all-readsy-member-row ">
								<h6>Already a member? <a href="{{ route('login') }}">Log In</a></h6>
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
	<script src="https://js.stripe.com/v3/"></script>

    <script>
        // Show loader on form submit
	$(document).ready(function() {


		const stripe = Stripe("{{ env('STRIPE_KEY') }}");
		const elements = stripe.elements();
		const card = elements.create('card');
		card.mount('#card-element');




		// Fetch cities on page load
		$.ajax({
			url: "{{ route('ajax.get.cities') }}",
			type: "GET",
			dataType: "json",
			success: function (data) {
				var $citySelect = $('#city');
				$.each(data, function (index, city) {
					$citySelect.append(
						$('<option>', {
							value: city.name,
							text: city.name,
							'data-state-name': city.state.name
						})
					);
				});
			}
		});

		// When a city is selected, fetch related state
		$('#city').on('change', function () {
			var state_name = $(this).find('option:selected').attr('data-state-name');
			console.log(state_name);
			$('#state').html(`<option value="${state_name}">${state_name}</option>`);
		});

		$('#register-form').validate({
			errorElement: 'small',
			errorClass: 'text-danger d-block mt-1 server_error',

			errorPlacement: function(error, element) {
				if(element.attr('id') === 'username') {
					$('#username-error').text(error.text()).show();
				} else if(element.attr('type') === 'checkbox') {
					error.insertAfter(element.closest('label'));
				} else {
					error.insertAfter(element);
				}
			},

			success: function(label, element) {
				if ($(element).attr('id') === 'username') {
					$('#username-error').hide().text('');
				}
			},

			rules: {
				username: {
					required: true,
					minlength: 4,
					maxlength: 20,
					pattern: /^[a-zA-Z0-9_]+$/,
					// remote: {  // This requires a backend endpoint for checking username uniqueness (optional)
					// 	url: '/check-username',  
					// 	type: 'post',
					// 	data: {
					// 		username: function() {
					// 			return $('#username').val();
					// 		}
					// 	}
					// }
				},
				email: {
					required: true,
					email: true,
					maxlength: 255,
					// remote: {  // Backend endpoint for email uniqueness (optional)
					// 	url: '/check-email',
					// 	type: 'post',
					// 	data: {
					// 		email: function() {
					// 			return $('#email').val();
					// 		}
					// 	}
					// }
				},
				dentist_name: {
					required: true,
					maxlength: 255,
				},
				practice_name: {
					required: true,
					maxlength: 255,
				},
				practice_address: {
					required: true,
				},
				city: {
					required: true,
					maxlength: 255,
				},
				state: {
					required: true,
					maxlength: 255,
				},
				post_code: {
					required: true,
					maxlength: 20,
					pattern: /^[A-Za-z0-9\s\-]+$/,
				},
				mobile_number: {
					required: true,
					minlength: 9,
					maxlength: 15,
				},
				routine_phone: {
					required: true,
					minlength: 9,
					maxlength: 15,
				},
				urgent_phone: {
					required: true,
					minlength: 9,
					maxlength: 15,
				},
				confirm_ahpra: {
					required: true
				},
				agree_terms: {
					required: true
				}
			},

			messages: {
				username: {
					required: 'Username is required.',
					minlength: 'Username must be at least 4 characters.',
					maxlength: 'Username must not be more than 20 characters.',
					pattern: 'Username can only contain letters ( A-Z , a-z , 0-9 , _ ).',
					remote: 'This username is already taken.'
				},
				email: {
					required: 'Email is required.',
					email: 'Enter a valid email address.',
					maxlength: 'Email must not exceed 255 characters.',
					remote: 'This email is already registered.'
				},
				dentist_name: {
					required: 'Dentist name is required.',
					maxlength: 'Dentist name must not exceed 255 characters.'
				},
				practice_name: {
					required: 'Practice name is required.',
					maxlength: 'Practice name must not exceed 255 characters.'
				},
				practice_address: {
					required: 'Practice address is required.'
				},
				city: {
					required: 'City/Suburb is required.',
					maxlength: 'City/Suburb must not exceed 255 characters.'
				},
				state: {
					required: 'State is required.',
					maxlength: 'State must not exceed 255 characters.'
				},
				post_code: {
					required: 'Postcode is required.',
					maxlength: 'Postcode must not exceed 20 characters.',
					pattern: 'Postcode may only contain letters, numbers, spaces, or hyphens.'
				},
				mobile_number: {
					required: 'Mobile phone number is required.',
					minlength: 'Mobile phone number must be at least 9 digits.',
					maxlength: 'Mobile phone number must not exceed 15 digits.'
				},
				routine_phone: {
					required: 'Routine phone number is required.',
					minlength: 'Routine phone number must be at least 9 digits.',
					maxlength: 'Routine phone number must not exceed 15 digits.'
				},
				urgent_phone: {
					required: 'Urgent phone number is required.',
					minlength: 'Urgent phone number must be at least 9 digits.',
					maxlength: 'Urgent phone number must not exceed 15 digits.'
				},
				confirm_ahpra: {
					required: 'You must confirm you are a registered dental professional.'
				},
				agree_terms: {
					required: 'You must agree to the Terms & Conditions and Privacy Policy.'
				}
			},

			highlight: function(element) {
				$(element).addClass('is-invalid');
			},

			unhighlight: function(element) {
				$(element).removeClass('is-invalid');
			},

		});

	

		let isSubmitting = false;

		$('#register-btn').on('click', async function (e) {
			e.preventDefault();

			if (isSubmitting) return;
			isSubmitting = true;

			if (!$('#register-form').valid()) {
				isSubmitting = false;
				return;
			}

			$(this).prop('disabled', true).text('Registering...');

			const billingName = $('#username').val();
			const billingEmail = $('#email').val();

			try {
				const { setupIntent, error } = await stripe.confirmCardSetup(
					"{{ $clientSecret }}", {
						payment_method: {
							card: card,
							billing_details: {
								name: billingName,
								email: billingEmail
							},
						}
					}
				);

				if (error) {
					console.log("Stripe error:", error.message);
					$('#payment-method').val('');
				} else {
					$('#payment-method').val(setupIntent.payment_method);
				}

				// Submit the form either way
				$('#register-form').get(0).submit();

			} catch (e) {
				alert("Unexpected error: " + e.message);
				console.log("Unexpected error: " + e.message);
				$(this).prop('disabled', false).text('Save');
				isSubmitting = false;
			}
		});


	});



        $('#username, #dentist_name, #gdc_number, #practice_name, #practice_address, #email, #routine_phone, #urgent_phone')
            .on('input', function() {
			$(this).siblings('.server_error').remove();
		});



		function checkUsernameAndSuggest() {
			const username = $('#username').val();

			if (username.length >= 3) {
				$.get("{{ route('username.suggestions') }}", {
					username
				}, function(data) {
					if (data.exists) {
						// Show error message when username exists
						//$('#username-error').text(data.message).show();

						// Display suggestions in one line
						const suggestions = data.suggestions.map(s =>
							`<span class="suggestion-box-item m-1" style="cursor: pointer;">${s}</span>`
						).join('');

						// Show suggestions with heading and suggestions in one line
						$('#username-suggestions').html(`
					<div class=" mt-2">
						<span style="font-size: 0.75rem;" class="mb-1 text-muted me-2">${data.message}. Please choose a different one or select from the suggestions: </span>
						${suggestions}
					</div>
				`).show();
					} else {
						// Hide error message if username is available
						$('#username-error').hide();

						// Hide suggestions if username is available
						$('#username-suggestions').hide();
					}
				});
			} else {
				$('#username-suggestions').hide();
				$('#username-error').hide();
			}
		}

		// Trigger username check when user types
		$('#username').on('input', function() {
			checkUsernameAndSuggest();
		});

		$(document).on('click', '.suggestion-box-item', function() {
			$('#username').val($(this).text());
			$('#username-suggestions').hide();
			$('#username-error').hide();
		});

    </script>
@endpush
