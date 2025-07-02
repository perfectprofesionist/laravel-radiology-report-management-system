@extends('layouts.app')

@section("breadcrumb")
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('content')

<style>
.form-field {
    position: relative;
}

.input-has-icon {
    /* padding-right: 40px; */
}

.toggle-password {
    position: absolute;
    right: 12px;
    top: calc(50% + 7px); /* Adjust if input height is custom */
    transform: translateY(-50%);
    cursor: pointer;
    color: #888;
    z-index: 2;
}
</style>


<div class="right-body-con">

    <div class="right-body-con-inn">
        <div class="dasboard-panel-row">
        <div class="upload-files-row2">
            <div class="upload-files-row2-left">
            <div class="lt-logo">
                <img src="{{ asset('images/site-logo.png') }}" />
                <!-- <div{{ $user->username ?? 'New User' }}</div> -->
            </div>
            <div class="contact-address">
                <ul>
                <li>
                    <img src="{{ asset('images/mail-inc.png') }}" />
                    <a href="mailto:{{ $user->email ?? "new email" }}"
                    >{{ $user->email ?? "new email" }}</a
                    >
                </li>
                <li>
                    <img src="{{ asset('images/tel-inc.png') }}" />
                    <a href="tel:{{ $user->mobile_number ?? "888-888-8888" }}">{{ $user->mobile_number ?? "888-888-8888" }}</a>
                </li>
                </ul>
            </div>
            </div>

            <div class="upload-files-row2-right">

                {{-- Display success message from session if success --}}
                <!-- @if(session('success'))
                    <div class="alert alert-success">{{-- session('success') --}}</div>
                @endif -->

                 {{-- Display form validation errors --}}
                {{-- @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif --}}

                {{-- Display form validation errors --}}
                @if($errors->any())
                    <!-- <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{-- $error --}}</li>
                            @endforeach
                        </ul>
                    </div> -->
                @endif

                <form id="formcreate" method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">
                        @csrf
                    <div class="patient-information-row">
                        
                        <div class="patient-information-inn patient-wrap">
                            <div class="form-field-row full-row">
                                <div class="form-field">
                                    <label>Profile Picture</label>
                                    <label id="avatar-preview" class="mt-2" for="avatar">
                                        <div class="mb-2">
                                            <img src="{{ asset('images/default-doc-profile.jpg') }}" alt="Default Profile" style="max-width: 200px; max-height: 200px;">
                                        </div>
                                    </label>
                                    <input type="file" class="form-control @error('avatar') is-invalid @enderror d-none" id="avatar" name="avatar" accept="image/*">
                                    @error('avatar')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                    {{-- <div id="avatar-preview" class="mt-2" style="display: none;">
                                        <img src="" alt="Avatar Preview" style="max-width: 200px; max-height: 200px;">
                                    </div> --}}
                                </div>
                                <div class="form-field">
                                    <label>Role <sup>*</sup></label>
                                    <select name="role" class="form-control @error('role') is-invalid @enderror">
                                        <option value="">-- Select Role --</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-field-row full-row">
                                <div class="form-field">
                                    <label>Username <sup>*</sup></label>
                                    <input type="text" name="username" placeholder="Enter username" value="{{ old('username') }}" autocomplete="new-password"/>
                                    @error('username')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-field">
                                    <label>User Email <sup>*</sup></label>
                                    <input type="email" name="email" placeholder="Enter email" value="{{ old('email') }}" autocomplete="new-password" />
                                    @error('email')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                             <div class="form-field-row full-row">
                                <div class="form-field">
                                    <label>Password <sup>*</sup></label>
                                    <div class="input-icon-group">
                                        <input type="password" id="password" name="password" placeholder="Enter password" value="{{ old('password') }}"  class="input-has-icon" autocomplete="new-password"/>
                                        <i class="fa fa-eye toggle-password" data-target="#password"></i>
                                    </div>
                                    @error('password')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-field">
                                    <label>Confirm Password <sup>*</sup></label>
                                    <div class="input-icon-group">
                                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="password confirmation" value="{{ old('password_confirmation') }}"  class="input-has-icon"/>
                                        <i class="fa fa-eye toggle-password" data-target="#password_confirmation"></i>
                                    </div>
                                    @error('password_confirmation')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-field-row full-row adminCSTMoption">
                                <div class="form-field">
                                    <label>First Name <sup>*</sup></label>
                                    <input type="text" name="first_name" placeholder="Enter first name" value="{{ old('first_name') }}" />
                                    @error('first_name')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-field">
                                    <label>Last Name <sup>*</sup></label>
                                    <input type="text" name="last_name" placeholder="Enter last name" value="{{ old('last_name') }}" />
                                    @error('last_name')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-field-row full-row usersCSTMoption">
                                <div class="form-field">
                                    <label>Mobile number <sup>*</sup></label>
                                    <input type="text" name="mobile_number" placeholder="Enter mobile number" value="{{ old('mobile_number') }}"  />
                                    @error('mobile_number')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-field">
                                    <label>Dentist Name <sup>*</sup></label>
                                    <input type="text" name="dentist_name" placeholder="Enter name" value="{{ old('dentist_name') }}"  />
                                    @error('dentist_name')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-field-row full-row usersCSTMoption">
                                <div class="form-field">
                                    <label>Practice Name <sup>*</sup></label>
                                    <input type="text" name="practice_name" placeholder="" value="{{ old('practice_name') }}"  />
                                    @error('practice_name')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-field">
                                    <label>Practice Address <sup>*</sup></label>
                                    <input type="text" name="practice_address" placeholder="" value="{{ old('practice_address') }}"  />
                                    @error('practice_address')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- <div class="form-field-row full-row">
                                <div class="form-field">
                                    <label>City <sup>*</sup></label>
                                    <input type="text" name="city" placeholder="" value="{{ old('city') }}"  />
                                </div>
                                <div class="form-field">
                                    <label>State <sup>*</sup></label>
                                    <input type="text" name="state" placeholder="" value="{{ old('state') }}"  />
                                </div>
                            </div> -->

                            <div class="form-field-row full-row usersCSTMoption">
                                <div class="form-field">
                                    <label>City <sup>*</sup></label>
                                    <select name="city" id="city-dropdown" >
                                        <option value="">Select City</option>
                                    </select>
                                    @error('city')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-field">
                                    <label>State <sup>*</sup></label>
                                    <input type="text" name="state" id="state-field" value="{{ old('state') }}"  >
                                    @error('state')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            

                            <div class="form-field-row full-row usersCSTMoption">
                                <div class="form-field">
                                    <label>Home Address <sup>*</sup></label>
                                    <input type="text" name="home_address" placeholder="" value="{{ old('home_address') }}" />
                                    @error('home_address')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-field">
                                    <label>Home Post Code <sup>*</sup></label>
                                    <input type="text" name="home_post_code" placeholder="" value="{{ old('home_post_code') }}" />
                                    @error('home_post_code')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-field-row full-row usersCSTMoption">
                                <div class="form-field">
                                    <label>Hospital Name (Work Place) <sup>*</sup></label>
                                    <input type="text" name="hospital_name" placeholder="" value="{{ old('hospital_name') }}" />
                                    @error('hospital_name')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-field">
                                    <label>Hospital Address <sup>*</sup></label>
                                    <input type="text" name="hospital_address" placeholder="" value="{{ old('hospital_address') }}" />
                                    @error('hospital_address')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-field-row full-row usersCSTMoption">
                                <div class="form-field">
                                    <label>Hospital Post Code <sup>*</sup></label>
                                    <input type="text" name="hospital_post_code" placeholder="" value="{{ old('hospital_post_code') }}" />
                                    @error('hospital_post_code')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-field">
                                    <label>GDC/GMC Registration No <sup>*</sup></label>
                                    <input type="text" name="gdc_number" placeholder="" value="{{ old('gdc_number') }}" />
                                    @error('gdc_number')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-field-row submit-button">
                                <!-- <input type="submit" name="" value="Update "> -->
                                <input type="submit" value="Create" id="submit-btn" style="opacity:1;">

                                {{-- <a href="#" class="edit-btn"></a> --}}
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
</div>


@push('scripts')

<script>
$(document).ready(function () {

        function toggleUserOptions(role) {
            if (role === 'user') {
                $('.usersCSTMoption').show();
                $('.adminCSTMoption').hide();
            } else {
                $('.usersCSTMoption').hide();
                $('.adminCSTMoption').show();
            }
        }
        $(document).ready(function () {
            const roleSelect = $('select[name="role"]');

            // Initial toggle on page load
            toggleUserOptions(roleSelect.val());

            // Toggle on change
            roleSelect.on('change', function () {
                toggleUserOptions($(this).val());
            });
        });



    loadCitiesDropdown()

    function loadCitiesDropdown() {
        $.ajax({
            url: "{{ route('ajax.get.cities') }}",
            method: "GET",
            success: function(response) {
                var cityDropdown = $('#city-dropdown');
                cityDropdown.empty().append('<option value="">Select City</option>');
                
                response.forEach(function(city) {
                    cityDropdown.append(
                        `<option value="${city.name}" data-state="${city.state.name}">${city.name}</option>`
                    );
                });

                // Pre-select values if available (on page load/edit)
                const selectedCity = "{{ old('city') }}";
                const selectedState = "{{ old('state') }}";

                if (selectedCity) {
                    cityDropdown.val(selectedCity).trigger('change');
                    $('#state-field').val(selectedState);
                }
            }
        });
    }

    $('#city-dropdown').on('change', function() {
        const selectedState = $(this).find(':selected').data('state');
        $('#state-field').val(selectedState || '');
    });


    // $('.edit-btn').on('click', function (e) {
    //     e.preventDefault();

    //     const $formInputs = $('#formcreate input');
    //     const $submitBtn = $('#submit-btn');
    //     const isdisabled = $formInputs.prop('disabled');

    //     if (isdisabled) {
    //         // Make editable
    //         $formInputs.removeAttr('disabled');
    //         $submitBtn.removeAttr('disabled').css('opacity', '1');

    //         $("#insurance_expired_date, #next_appraisal_date").datepicker({
    //             dateFormat: "mm/dd/yy",
    //             minDate: +1
    //         });
    //     } else {
    //         // Make read-only
    //         $formInputs.attr('disabled', true);
    //         $submitBtn.attr('disabled', true).css('opacity', '0.6');
    //     }
    // });

    

    // Enable form fields and show date pickers when Edit is clicked
    
    // $('.edit-btn').on('click', function (e) {
    //     e.preventDefault();
    //     $('#formcreate input').removeAttr('readonly ');
    //     $('#formcreate select').removeAttr('disabled');
    //     // $('#formcreate input, #formcreate select').prop('disabled', false);
    //     // $('#city-dropdown').removeAttr('disabled');
    //     $('#submit-btn').removeAttr('disabled').css('opacity', '1');
    //     $('#edit-message').hide();

    //     $("#insurance_expired_date, #next_appraisal_date").datepicker({
    //         dateFormat: "dd/mm/yy"
    //         // minDate: +1 
    //     });
    // });

    // Add custom date validation rule for dd/mm/yyyy format
    $.validator.addMethod("dateDDMMYYYY", function(value, element) {
        return this.optional(element) || /^\d{2}\/\d{2}\/\d{4}$/.test(value);
    }, "Please enter a valid date in dd/mm/yyyy format");

    // Initialize jQuery Validation Plugin with rules and messages
     $('#formcreate').validate({
         errorElement: 'small',
         errorClass: 'text-danger d-block mt-1',
         ignore: ":hidden",
        rules: {
            username: {
                required: true,
                minlength: 3,
                maxlength: 50
            },
            email: {
                required: true,
                email: true
            },
            mobile_number: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 15
            },
            dentist_name: {
                required: true
            },
            practice_name: {
                required: true
            },
            practice_address: {
                required: true
            },
            city: {
                required: true
            },
            state: {
                required: true
            },
            home_address: {
                required: true
            },
            home_post_code: {
                required: true
            },
            hospital_name: {
                required: true
            },
            hospital_address: {
                required: true
            },
            hospital_post_code: {
                required: true
            },
            gdc_number: {
                required: true,
                // minlength: 8,
                // maxlength: 8,
                // pattern: /^[A-Za-z0-9]{8}$/
            },
             role: {
                required: true
            },
            password: {
                required: true,
                minlength: 8,
                maxlength: 32,
                pattern: /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,32}$/
            },
            password_confirmation: {
                required: true,
                equalTo: '[name="password"]'
            },
            first_name: {
                required: true,
            },
            last_name: {
                required: true,
            },
        },
        messages: {
            username: {
                required: "Username is required",
                minlength: "Must be at least 3 characters",
                maxlength: "Must be less than 50 characters"
            },
            email: {
                required: "Email is required",
                email: "Please enter a valid email address"
            },
            mobile_number: {
                required: "Mobile number is required",
                digits: "Only digits are allowed",
                minlength: "Must be at least 10 digits",
                maxlength: "Must be no more than 15 digits"
            },
            dentist_name: {
                required: "Dentist name is required"
            },
            practice_name: {
                required: "Practice name is required"
            },
            practice_address: {
                required: "Practice address is required"
            },
            city: {
                required: "City is required"
            },
            state: {
                required: "State is required"
            },
            home_address: {
                required: "Home address is required"
            },
            home_post_code: {
                required: "Home post code is required"
            },
            hospital_name: {
                required: "Hospital name is required"
            },
            hospital_address: {
                required: "Hospital address is required"
            },
            hospital_post_code: {
                required: "Hospital post code is required"
            },
            gdc_number: {
                required: "GDC/GMC number is required",
                // minlength: "Must be exactly 8 characters",
                // maxlength: "Must be exactly 8 characters",
                // pattern: "Only letters and digits allowed"
            },
            role: {
                required: "Role is required"
            },
            password: {
                required: "Password is required",
                minlength: "Password must be at least 8 characters",
                maxlength: "Password must not exceed 32 characters",
                pattern: "Password must include uppercase, lowercase, number, and special character"
            },
            password_confirmation: {
                required: "Confirm Password is required",
                equalTo: "Passwords do not match"
            },
            first_name: {
                required: "First name is required"
            },
            last_name: {
                required: "Last name is required"
            },
        },
         submitHandler: function (form) {
            var $btn = $('#submit-btn');
            $btn.val('Creating...')
                .prop('disabled', true)
                .css('opacity', 0.6);
             form.submit();
         }
     });

    // $("#insurance_expired_date, #next_appraisal_date").on('change', function() {
    //     // Trigger the validation on the specific field that has been modified
    //     $(this).valid();
    // });

    document.getElementById('avatar').addEventListener('change', function(e) {
        const preview = document.getElementById('avatar-preview');
        const file = e.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.querySelector('img').src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    });

    $('.toggle-password').on('click', function () {
        const input = $($(this).data('target'));
        const type = input.attr('type') === 'password' ? 'text' : 'password';
        input.attr('type', type);
        $(this).toggleClass('fa-eye fa-eye-slash');
    });
});
</script>


@endpush
@endsection