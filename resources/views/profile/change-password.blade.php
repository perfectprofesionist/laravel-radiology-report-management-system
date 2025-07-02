@extends('layouts.app')

@section("breadcrumb")
    <li class="breadcrumb-item"><a href="{{ route('profile.show') }}">Profile</a></li>
    <li class="breadcrumb-item active" aria-current="page">Change Password</li>
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
                    <img src="{{ asset('images/site-logo.png') }}" style="width: 100px; height: 100px;"/>
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
                    @role('user')
                        <li>
                            <img src="{{ asset('images/tel-inc.png') }}" />
                            <a href="tel:{{ $user->mobile_number }}">{{ $user->mobile_number }}</a>
                        </li>
                        <li>
                            {{-- <img src="{{ asset('images/setting-icon.png') }}" /> --}}
                            <a href="{{ route('cards') }}">My Cards</a>
                        </li>
                    @endrole
                    <li>
                        {{-- <img src="{{ asset('images/password-icon.png') }}" /> --}}
                        <a href="{{ route('password.change.form') }}">Change Password</a>
                    </li>
                    </ul>
                </div>
            </div>

            <div class="upload-files-row2-right">

                {{-- Display success message from session if success --}}
               

                {{-- Display form validation errors --}}
                {{-- @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif --}}

                <form method="POST" id="passwordupdate" action="{{ route('password.update.custom') }}">
                @csrf

                    <div class="patient-information-row">
                         <div class="patient-information-inn patient-wrap">
                              <div class="form-field-row full-row">
                                   <div class="form-field">
                                        <label>Current Password <sup>*</sup></label>
                                        <div class="input-icon-group">
                                        <input type="password" id="password" name="current_password" placeholder="current password" value="{{ old('current_password') }}" />
                                          <i class="fa fa-eye toggle-password" data-target="#password"></i>
                                        </div>
                                        @error('current_password')
                                             <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                        @enderror
                                   </div>
                              </div>
                                <div class="form-field-row full-row">
                                     <div class="form-field">
                                        <label>New Password <sup>*</sup></label>
                                        <div class="input-icon-group">
                                        <input type="password" id="new_password" name="new_password" placeholder="new password" value="{{ old('new_password') }}" />
                                          <i class="fa fa-eye toggle-password" data-target="#new_password"></i>
                                        </div>
                                        @error('new_password')
                                             <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                        @enderror
                                   </div>
                                       <div class="form-field">
                                        <label>Confirm New Password <sup>*</sup></label>
                                         <div class="input-icon-group">
                                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="new password" value="{{ old('new_password_confirmation') }}" />
                                          <i class="fa fa-eye toggle-password" data-target="#new_password_confirmation"></i>
                                        </div>
                                        @error('new_password_confirmation')
                                             <small class="text-danger d-block mt-1 server_error">{{ $message }}</small>
                                        @enderror
                                   </div>
                              </div>
                              
                              <div class="form-field-row submit-button">
                                   <!-- <input type="submit" name="" value="Update "> -->
                                   <input type="submit" value="Update Password" id="submit-btn" style="opacity:1;">

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

    $('.toggle-password').on('click', function () {
        const input = $($(this).data('target'));
        const type = input.attr('type') === 'password' ? 'text' : 'password';
        input.attr('type', type);
        $(this).toggleClass('fa-eye fa-eye-slash');
    });

    

    // Initialize jQuery Validation Plugin with rules and messages
    $('#passwordupdate').validate({
         errorElement: 'small',
         errorClass: 'text-danger d-block mt-1',
         ignore: ":hidden",
        rules: {
            current_password: {
                required: true,
                minlength: 8,
                maxlength: 50
            },
            new_password: {
                required: true,
                minlength: 8,
                maxlength: 32,
                pattern: /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,32}$/
            },
            new_password_confirmation: {
                required: true,
                equalTo: '[name="new_password"]'
            },
        },
        messages: {
            current_password: {
                required: "Current Password is required",
                minlength: "Current Password must be at least 8 characters",
                maxlength: "Current Password must not exceed 50 characters"
            },
            new_password: {
                required: "Password is required",
                minlength: "Password must be at least 8 characters",
                maxlength: "Password must not exceed 32 characters",
                pattern: "Password must include uppercase, lowercase, number, and special character"
            },
            new_password_confirmation: {
                required: "Confirm Password is required",
                equalTo: "Passwords do not match"
            },
        },
         submitHandler: function (form) {
            var $btn = $('#submit-btn');
            $btn.val('Updating...')
                .prop('disabled', true)
                .css('opacity', 0.6);
             form.submit();
         }
     });

    $("#insurance_expired_date, #next_appraisal_date").on('change', function() {
        // Trigger the validation on the specific field that has been modified
        $(this).valid();
    });

});
</script>


@endpush
@endsection