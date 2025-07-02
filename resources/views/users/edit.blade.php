@extends('layouts.app')

@section("breadcrumb")
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

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

@section('content')


<div class="right-body-con">

    <div class="right-body-con-inn">
        <div class="dasboard-panel-row">
        <div class="upload-files-row2">
            <div class="upload-files-row2-left">
            <div class="lt-logo">
                <img src="{{ asset('images/site-logo.png') }}" />
                <!-- <div>{{ $user->username }}</div> -->
            </div>
            <div class="contact-address">
                <ul>
                <li>
                    <img src="{{ asset('images/mail-inc.png') }}" />
                    <a href="mailto:{{ $user->email }}"
                    >{{ $user->email }}</a
                    >
                </li>
                <li>
                    <img src="{{ asset('images/tel-inc.png') }}" />
                    <a href="tel:{{ $user->mobile_number }}">{{ $user->mobile_number }}</a>
                </li>
                </ul>
            </div>
            </div>

            <div class="upload-files-row2-right">

                {{-- Display success message from session if success --}}

                    {{-- @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif --}}

                <form id="updateform" method="POST" action="{{ route('userupdate', $user->id) }}"  enctype="multipart/form-data">
                    @csrf
                    <div class="patient-information-row">
                        
                        <div class="patient-information-inn patient-wrap">
                            <div class="form-field-row full-row">
                                <div class="form-field">
                                    <label>Profile Picture</label>
                                    <label id="avatar-preview" class="mt-2" for="avatar">
                                        @if($user->avatar)
                                            <div class="mb-2">
                                                <img src="{{ route('user.avatar', ['filename' => $user->avatar]) }}" alt="Current Avatar" style="max-width: 200px; max-height: 200px;">
                                            </div>
                                        @else
                                            <div class="mb-2">
                                                <img src="{{ asset('images/default-doc-profile.jpg') }}" alt="Default Profile" style="max-width: 200px; max-height: 200px;">
                                            </div>
                                        @endif
                                    </label>
                                    <input type="file" class="form-control @error('avatar') is-invalid @enderror d-none" id="avatar" name="avatar" readonly accept="image/*">
                                    @error('avatar')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                    {{-- <div id="avatar-preview" class="mt-2" style="display: none;">
                                        <img src="" alt="Avatar Preview" style="max-width: 200px; max-height: 200px;">
                                    </div> --}}
                                </div>
                                <div class="form-field">
                                    <label>Role <sup>*</sup></label>
                                    <select name="role" class="form-control @error('role') is-invalid @enderror" disabled>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" {{ old('role', $user->getRoleNames()->first()) == $role->name ? 'selected' : '' }}>
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
                                    <input type="text" name="username" placeholder="Enter username" value="{{ old('username', $user->username) }}" readonly />
                                    @error('username')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-field">
                                    <label>User Email <sup>*</sup></label>
                                    <input type="email" name="email" placeholder="Enter email" value="{{ old('email', $user->email) }}" readonly />
                                    @error('email')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-field-row full-row">
                                <div class="form-field">
                                    <label>Password <sup>*</sup></label>
                                    <div class="input-icon-group">
                                        <input type="password" id="password" name="password" placeholder="Enter password" value="{{ old('password') }}"  class="input-has-icon"/>
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
                                    <input type="text" name="first_name" placeholder="Enter first name" value="{{ old('first_name', $user->first_name) }}" />
                                    @error('first_name')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-field">
                                    <label>Last Name <sup>*</sup></label>
                                    <input type="text" name="last_name" placeholder="Enter last name" value="{{ old('last_name', $user->last_name) }}" />
                                    @error('last_name')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>



                            <div class="form-field-row full-row usersCSTMoption">
                                <div class="form-field">
                                    <label>Mobile number <sup>*</sup></label>
                                    <input type="text" name="mobile_number" placeholder="Enter mobile number" value="{{ old('mobile_number', $user->mobile_number) }}" readonly />
                                    @error('mobile_number')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-field">
                                    <label>Dentist Name <sup>*</sup></label>
                                    <input type="text" name="dentist_name" placeholder="Enter name" value="{{ old('dentist_name', $user->dentist_name) }}" readonly />
                                    @error('dentist_name')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-field-row full-row usersCSTMoption">
                                <div class="form-field">
                                    <label>Practice Name <sup>*</sup></label>
                                    <input type="text" name="practice_name" placeholder="" value="{{ old('practice_name', $user->practice_name) }}" readonly />
                                    @error('practice_name')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-field">
                                    <label>Practice Address <sup>*</sup></label>
                                    <input type="text" name="practice_address" placeholder="" value="{{ old('practice_address', $user->practice_address) }}" readonly />
                                    @error('practice_address')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- <div class="form-field-row full-row">
                                <div class="form-field">
                                    <label>City <sup>*</sup></label>
                                    <input type="text" name="city" placeholder="" value="{{ old('city', $user->city) }}" readonly />
                                </div>
                                <div class="form-field">
                                    <label>State <sup>*</sup></label>
                                    <input type="text" name="state" placeholder="" value="{{ old('state', $user->state) }}" readonly />
                                </div>
                            </div> -->

                            <div class="form-field-row full-row usersCSTMoption">
                                <div class="form-field">
                                    <label>City <sup>*</sup></label>
                                    <select name="city" id="city-dropdown" disabled >
                                        <option value="">Select City</option>
                                    </select>
                                    @error('city')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-field">
                                    <label>State <sup>*</sup></label>
                                    <input type="text" name="state" id="state-field" value="{{ old('state', $user->state) }}" readonly >
                                    @error('state')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-field-row full-row usersCSTMoption">
                                <div class="form-field">
                                    <label>Home Address <sup>*</sup></label>
                                    <input type="text" name="home_address" placeholder="" value="{{ old('home_address', $user->home_address ? $user->home_address : '') }}" readonly />
                                    @error('home_address')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-field">
                                    <label>Home Post Code <sup>*</sup></label>
                                    <input type="text" name="home_post_code" placeholder="" value="{{ old('home_post_code', $user->home_post_code) }}" readonly />
                                    @error('home_post_code')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-field-row full-row usersCSTMoption">
                                <div class="form-field">
                                    <label>Hospital Name (Work Place) <sup>*</sup></label>
                                    <input type="text" name="hospital_name" placeholder="Kings College" value="{{ old('hospital_name', $user->hospital_name) }}" readonly />
                                    @error('hospital_name')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-field">
                                    <label>Hospital Address <sup>*</sup></label>
                                    <input type="text" name="hospital_address" placeholder="Flat 3, Melanby House" value="{{ old('hospital_address', $user->hospital_address) }}" readonly />
                                    @error('hospital_address')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-field-row full-row usersCSTMoption">
                                <div class="form-field">
                                    <label>Hospital Post Code <sup>*</sup></label>
                                    <input type="text" name="hospital_post_code" placeholder="NW7 ISU" value="{{ old('hospital_post_code', $user->hospital_post_code) }}" readonly />
                                    @error('hospital_post_code')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-field">
                                    <label>GDC/GMC Registration No <sup>*</sup></label>
                                    <input type="text" name="gdc_number" placeholder="244482" value="{{ old('gdc_number', $user->gdc_number) }}" readonly />
                                    @error('gdc_number')
                                        <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-field-row submit-button">
                                <!-- <input type="submit" name="" value="Update "> -->
                                <input type="submit" value="Update" disabled id="submit-btn" style="opacity:0.6;">

                                <a href="#" class="edit-btn"></a>
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

    // Initial toggle based on selected value
    toggleUserOptions(roleSelect.find(':selected').val());

    // Toggle sections when role is changed
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
                const selectedCity = "{{ old('city', $user->city) }}";
                const selectedState = "{{ old('state', $user->state) }}";

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

    //     const $formInputs = $('#updateform input');
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
    $('.edit-btn').on('click', function (e) {
        e.preventDefault();
        $('#updateform input').removeAttr('readonly ');
        $('#updateform select').removeAttr('disabled');
        // $('#updateform input, #updateform select').prop('disabled', false);
        // $('#city-dropdown').removeAttr('disabled');
        $('#submit-btn').removeAttr('disabled').css('opacity', '1');
        $('#edit-message').hide();

        $("#insurance_expired_date, #next_appraisal_date").datepicker({
            dateFormat: "dd/mm/yy"
            // minDate: +1 
        });
    });

    // Add custom date validation rule for dd/mm/yyyy format
    $.validator.addMethod("dateDDMMYYYY", function(value, element) {
        return this.optional(element) || /^\d{2}\/\d{2}\/\d{4}$/.test(value);
    }, "Please enter a valid date in dd/mm/yyyy format");

    // Initialize jQuery Validation Plugin with rules and messages
     $('#updateform').validate({
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
            first_name: {
                required: true,
            },
            last_name: {
                required: true,
            },
            // insurance_expired_date: {
            //     required: true,
            //     dateDDMMYYYY: true
            // },
            // next_appraisal_date: {
            //     required: true,
            //     dateDDMMYYYY: true
            // }
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
             first_name: {
                required: "First name is required"
            },
            last_name: {
                required: "Last name is required"
            },
            // insurance_expired_date: {
            //     required: "Insurance expiry date is required"
            // },
            // next_appraisal_date: {
            //     required: "Next appraisal date is required"
            // }
        },
         submitHandler: function (form) {
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