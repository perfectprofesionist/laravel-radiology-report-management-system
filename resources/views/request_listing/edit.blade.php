@extends('layouts.app')

@section('content')

{{-- Main edit form container for updating radiology request details --}}

{{-- Main edit form container for updating radiology request details --}}
<div class="right-body-con">
    <div class="right-body-con-inn">
        <div class="dasboard-panel-row">
            {{-- Header section with instructions for users --}}
            <div class="upload-files-row1">
                <h3>Please complete the form below to upload your scan and submit payment.</h3>
                <p>Submissions will only be reviewed after successful payment. If you have any questions, contact our support team.</p>
            </div>
            
            {{-- Main form container with two columns --}}
            <div class="upload-files-row2">
                {{-- Left column: Company logo and contact information --}}
                <div class="upload-files-row2-left">
                    <div class="lt-logo">
                        <img src="{{ asset('images/site-logo.png') }}">
                    </div>
                    <div class="contact-address">
                        <ul>
                            <li><img src="{{ asset('images/mail-inc.png') }}"> <a href="{{ Auth::user()->email }}">{{ Auth::user()->email }}</a></li>
                            <li><img src="{{ asset('images/tel-inc.png') }}"> <a href="tel:{{ Auth::user()->mobile_number }}">{{ Auth::user()->mobile_number }}</a></li>
                        </ul>
                    </div>
                </div>
                
                {{-- Right column: Main edit form --}}
                <div class="upload-files-row2-right">
                    
                    {{-- Check if the request has been paid to determine field editability --}}
                    @php
                        $isPaid = ($requestListing->payment_status ?? '') === 'paid';
                    @endphp
                    
                    {{-- Main form for updating request details --}}
                    <form id="scanForm_update" action="{{ route('scan.formupdate', ['uuid' => $requestListing->uuid]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- Patient Information Section --}}
                        <div class="patient-information-row">
                            <h3>Patient Information <sup>*</sup></h3>
                            <div class="patient-information-inn">
                                {{-- First row: Patient name and date of birth --}}
                                <div class="form-field-row full-row">
                                    <div class="form-field ">
                                        <label for="patient_name">Patient Name</label>
                                        <input type="text" name="patient_name_update" id="patient_name_update" placeholder="Enter the full legal name of the patient."
                                         value="{{ old('patient_name', $requestListing->patient_name ?? '') }}"
                                          @if($isPaid) readonly @endif>
                                    </div>
                                    <div class="form-field">
                                        <label for="patient_dob">Date of Birth <sup>*</sup></label>
                                        <input type="text" name="patient_dob_update" id="patient_dob_update" placeholder="dd/mm/yyyy"
                                        value="{{ old('patient_dob', isset($requestListing->patient_dob) ? \Carbon\Carbon::parse($requestListing->patient_dob)->format('d/m/Y') : '') }}"
                                        @if($isPaid) readonly data-disable-datepicker="1" @endif>
                                    </div>
                                </div>

                                {{-- Second row: Patient phone and appointment date --}}
                                <div class="form-field-row full-row">
                                    <div class="form-field ">
                                        <label for="patient_phone">Patient phone <sup>*</sup></label>
                                        <input type="text" name="patient_phone" placeholder="Enter phone number"
                                        value="{{ old('patient_phone', $requestListing->patient_phone ?? '') }}"
                                         @if($isPaid) readonly @endif>
                                    </div>
                                    <div class="form-field">
                                        <label for="appointment">Appointment <sup>*</sup></label>
                                        <input type="text" name="appointment" id="appointment" placeholder="dd/mm/yyyy"
                                        value="{{ old('appointment', isset($requestListing->appointment) ? \Carbon\Carbon::parse($requestListing->appointment)->format('d/m/Y') : '') }}"
                                        @if($isPaid) readonly data-disable-datepicker="1" @endif>
                                    </div>
                                </div>
                                
                                {{-- Third row: Patient postcode and email --}}
                                <div class="form-field-row full-row">
                                    <div class="form-field ">
                                        <label for="patient_postcode">Patient Postcode <sup>*</sup></label>
                                        <input type="text" name="patient_postcode" placeholder="Enter postcode"
                                         value="{{ old('patient_postcode', $requestListing->patient_postcode ?? '') }}"
                                         @if($isPaid) readonly @endif>
                                    </div>
                                    <div class="form-field ">
                                        <label for="patient_email">Patient email <sup>*</sup></label>
                                        <input type="email" name="patient_email" placeholder="Enter patient email"
                                        value="{{ old('patient_email', $requestListing->patient_email ?? '') }}"
                                        @if($isPaid) readonly @endif>
                                    </div>
                                </div>
                                
                                {{-- Fourth row: Patient address and clinical history --}}
                                <div class="form-field-row full-row">
                                     <div class="form-field ">
                                        <label for="patient_address">Patient address <sup>*</sup></label>
                                        <textarea name="patient_address" placeholder="Enter patient address"
                                        @if($isPaid) readonly @endif>{{ old('patient_address', $requestListing->patient_address ?? '') }}</textarea>
                                    </div>
                                    <div class="form-field ">
                                        <label for="clinical_history">Patient clinical history <sup>*</sup></label>
                                        <textarea name="clinical_history" placeholder="Enter clinical history"
                                        value="{{ old('clinical_history', $requestListing->clinical_history ?? '') }}"
                                        @if($isPaid) readonly @endif>{{ old('clinical_history', $requestListing->clinical_history ?? '') }}</textarea>
                                    </div>
                                </div>
                                
                                {{-- Fifth row: Question for radiologist --}}
                                <div class="form-field-row full-row">
                                    <div class="form-field ">
                                        <label for="question">Question <sup>*</sup></label>
                                        <textarea name="question" placeholder="Enter any question .."
                                        value="{{ old('question', $requestListing->question ?? '') }}"
                                        @if($isPaid) readonly @endif>{{ old('question', $requestListing->question ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Scan Details Section --}}
                        <div class="patient-information-row row2">
                            <h3>Scan Details <sup>*</sup></h3>
                            <div class="patient-information-inn">
                                {{-- Clinical details description --}}
                                <div class="sub-patient-information">
                                    <h4>Clinical Details / Questions for Radiologist</h4>
                                    <p>Provide a clear clinical history or specify any questions you would like the radiologist to address.</p>
                                </div>
                                
                                {{-- Scan date and modality selection --}}
                                <div class="form-field-row full-row">
                                    <div class="form-field">
                                        <label for="scan_date">Scan Date <sup>*</sup></label>
                                        <input type="text" name="scan_date_update" id="scan_date_update" placeholder="dd/mm/yyyy"
                                        value="{{ old('scan_date', isset($requestListing->scan_date) ? \Carbon\Carbon::parse($requestListing->scan_date)->format('d/m/Y') : '') }}"
                                        @if($isPaid) readonly data-disable-datepicker="1" @endif>
                                    </div>
                                    <div class="form-field">
                                        <label for="modality">Modality Selection <sup>*</sup></label>
                                        <select name="modality_update" id="modality_update"  @if($isPaid) disabled @endif> 
                                            <option value="">Select Modality</option>
                                            {{-- Loop through available modalities with pricing data --}}
                                            @foreach ($modalities as $modality)
                                            <option value="{{ $modality->name }}" data-price="{{ $modality->price }}"
                                            {{ old('modality', $requestListing->modality ?? '') == $modality->name ? 'selected' : '' }}>
                                            {{ $modality->name }}</option>
                                             @endforeach
                                        </select> 
                                    </div>
                                </div>
                                
                                {{-- File upload section with existing files display --}}
                                <div class="form-field-row full-row file-upload d-flex flex-column">
                                    <label>Scan File Upload <sup>*</sup> <span>(DICOM format only, max 1GB)</span></label>

                                    {{-- Display area for existing uploaded files --}}
                                    <div id="uploaded-file-info" style="margin-top: 10px;">
                                        <div class="align-items-center gap-3 mb-3" id="patients_docs_listing">
                                            <ol>
                                                {{-- Loop through existing files and display them with delete options --}}
                                                @foreach ($files as $file )
                                                    @if($file->type == "patients_docs")
                                                        <li>
                                                            <a target="_blank" href="{{ route('scan.file', [$file->file_name])}}" class="text-muted">{{ $file->original_name}}</a>
                                                            {{-- File removal link with confirmation --}}
                                                            <a href="#" 
                                                                class="text-danger ms-2 remove-file-link" 
                                                                data-file-path="{{ $file->file_name }}" 
                                                                data-request-uuid="{{ $requestListing->uuid ?? '' }}" 
                                                                data-type="patients_docs"
                                                                style="cursor:pointer;">
                                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ol>
                                        </div>
                                    </div>

                                    {{-- Progress bar for file upload tracking --}}
                                    <div id="upload-progress-wrapper" style="display: none; margin-top: 10px;">
                                        <div style="background-color: #e0e0e0; height: 8px; border-radius: 4px; overflow: hidden;">
                                            <div id="upload-progress-bar" style="height: 100%; width: 0%; background-color: #4caf50;"></div>
                                        </div>
                                        <small id="upload-progress-text">0%</small>
                                    </div>

                                    {{-- Dropzone for new file uploads --}}
                                    <div id="dropzone-upload-section" class="form-field">
                                        <div class="upload-box dropzone" id="myDropzone_patients_docs">
                                            <div class="upload-icons"><img src="{{ asset('images/upload-icon.png') }}"></div>
                                            <h6  id="dropzone-placeholder-update">Drag and drop your files here or <a href="javascript:;">Browse files</a> from your device</h6>
                                            <div class="file-name" id="fileName_update"></div>
                                            <form action="/upload-files/patients_docs">
                                                <input type="hidden" name="request_uuid" value="{{ $requestListing->uuid }}">
                                                <input type="hidden" name="type" value="patients_docs">
                                                @csrf
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Hidden fields for form processing --}}
                                 <div class="form-field-row full-row">
                                   <div class="form-field" style="display:none;">       
                                        <label for="scan_file">Scan file</label>
                                        <input type="text" name="scan_file_update" id="scan_file_update"
                                         value="{{ old('scan_file', $requestListing->scan_file ?? '') }}"
                                        @if($isPaid) readonly @endif>
                                    </div>
                                </div>

                                {{-- Hidden UUID field for request identification --}}
                                <input type="hidden" name="uuid" id="uuid_field" value="{{ $requestListing->uuid }}">

                                {{-- Submit button and price display --}}
                                <div class="form-field-row submit-button d-flex align-items-center justify-content-between">
                                    <input type="submit" id="submitBtn_update"
                                    @if($isPaid)
                                        disabled 
                                        style="opacity: 0.6; cursor: not-allowed;"
                                    @endif
                                     name="" value="Save">
                                    <div id="priceDisplayView" style="font-weight:bold; font-size:16px;">Price: £<span id="priceValueEdit">0</span></div>
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Payment section - only show if payment is pending --}}
                    @if(($requestListing->payment_status ?? '') === 'unpaid')
                        {{-- Payment form with Stripe integration --}}
                        <form id="payment_stripe" action="{{ route('stripe.ajax.charge') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="patient-information-row row2" id="card-section">
                                <h3>Payment <sup>*</sup></h3>
                                <div class="patient-information-inn ">   <!--   payment-row -->
                                    <div class="col-md-9 payment-row-grid">
                                        <div class="sub-patient-information">
                                            <h4>Before your case is reviewed, payment must be made in full.</h4>
                                            <p>Please select your modality above and proceed with payment via our secure Stripe checkout. 
                                            Once payment is confirmed, your submission will be finalized and sent to our radiologists.</p>
                                        </div>
                                    </div>
                                    <div class="form-field-row full-row custFullWdth mx-3">
                                           <div class="form-field">

                                               {{-- Display saved payment methods if available --}}
                                               @if(isset($paymentMethods['data']) && count($paymentMethods['data']))
                                                    <label><strong>Saved Cards</strong></label>
                                                    <div id="saved-card-options">
                                                        @foreach($paymentMethods->data as $index => $method)
                                                            <div class="form-check mb-2">
                                                                <input 
                                                                    class="form-check-input card-input" 
                                                                    type="radio" 
                                                                    name="saved_card_option" 
                                                                    id="savedCard{{ $index }}" 
                                                                    value="{{ $method->id }}"
                                                                    {{ $index === 0 ? 'checked' : '' }}>
                                                                <label class="form-check-label card-label" for="savedCard{{ $index }}">
                                                                    {{ ucfirst($method->card->brand) }} •••• {{ $method->card->last4 }} 
                                                                    — Expires {{ str_pad($method->card->exp_month, 2, '0', STR_PAD_LEFT) }}/{{ $method->card->exp_year }}
                                                                </label>
                                                            </div>
                                                        @endforeach

                                                        {{-- Option to use a new card --}}
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input card-input" type="radio" name="saved_card_option" id="useNewCard" value="new">
                                                            <label class="form-check-label card-label" for="useNewCard"><i class="fa fa-plus-circle" aria-hidden="true"></i> Use a different card</label>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- New card input section --}}
                                                <div id="card-element-wrapper" class="mt-3">
                                                    <label>Card Details <sup>*</sup></label>
                                                    <div id="card-element" class="form-control mb-3"></div>
                                                    {{-- Save card checkbox for future use --}}
                                                    <div class="mt-2 d-flex align-items-center justify-content-start text-muted">
                                                        <input class="" type="checkbox" name="save_card" id="save-card">
                                                        <label class="m-0 pl-2" for="save-card">
                                                            Save card details
                                                        </label>
                                                    </div>
                                                    {{-- Security notice about card storage --}}
                                                    <div class="text-muted mt-1" style="font-size: 0.9em;">
                                                        Your credit card details will be securely stored by 
                                                        <a href="https://stripe.com" target="_blank" rel="noopener noreferrer">stripe.com</a>.
                                                    </div>
                                                </div>
                                                {{-- Loading indicator for payment processing --}}
                                                <div id="card-loader" class="text-center my-3" style="display: none;">
                                                    <div class="spinner-border text-primary" role="status"></div>
                                                </div>
                                                {{-- Payment status messages --}}
                                                <div id="payment-message" class="mt-2 text-danger"></div>

                                            </div>
                                    </div>

                                    {{-- Payment button section --}}
                                    <div class="payment-row-grid btn">
                                        <div class="form-field-row submit-button">
                                            <div>Pay £<span id="priceAmount" data-price="0">0</span></div>
                                            <input type="submit" id="payNowBtn" name="" value="Proceed to Payment ">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        {{-- Payment confirmation section for completed payments --}}
                        <div class="patient-information-row row2" id="card-section">
                            <h3>Payment</h3>
                            <div class="patient-information-inn ">   <!--   payment-row -->
                                <div class="col-md-9 payment-row-grid">
                                    <div class="sub-patient-information">
                                        <h4>Payment successful :</h4>
                                        <p><p>Your payment is already complete. Thank you!</p></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>			
</div>

@push('scripts')

<script>

    $(document).ready(function () {

        {{-- File removal functionality with confirmation dialog --}}
        $(document).on('click', '.remove-file-link', function(e) {
            e.preventDefault();

            const $link = $(this);
            const filePath = $link.data('file-path');
            const requestUuid = $link.data('request-uuid');
            const type = $link.data('type');

            // Validate file path before proceeding
            if (!filePath) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid file path',
                    text: 'The file path is missing or incorrect.',
                });
                return;
            }

            // Show confirmation dialog before deletion
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you really want to delete this file?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    const getUrl = `/show-files/${requestUuid}/${type}`;

                    // AJAX call to remove file from server
                    $.ajax({
                        url: '{{ route("removeUploadedFile") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            file_path: filePath,
                            request_uuid: requestUuid || null
                        },
                        success: function(response) {
                            if (response.success) {
                                // Show success message
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message || 'The file has been deleted.',
                                    timer: 3000,
                                    showConfirmButton: true
                                });

                                // Refresh the file listing
                                $.get(getUrl, function(data) {
                                    $('#' + type + '_listing').html(data);
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed',
                                    text: response.message || 'Failed to delete the file.'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'An error occurred while deleting the file.'
                            });
                        }
                    });
                }
            });
        });

        {{-- Initialize Dropzone for file uploads --}}
        // Clean up any existing Dropzone instances
        {{-- Initialize Dropzone for file uploads --}}
        // Clean up any existing Dropzone instances
        if (Dropzone.instances.length > 0) {
            Dropzone.instances.forEach(dz => dz.destroy());
        }

        const $dropzoneElement_patients_docs = $("#myDropzone_patients_docs");
        if ($dropzoneElement_patients_docs.length) {
            const myDropzone_patients_docs = new Dropzone("#myDropzone_patients_docs", {
                url: "/upload-files/patients_docs",
                paramName: "file",
                maxFilesize: 1500, // 1.5GB max file size
                maxFiles: null, // Allow multiple files
                chunking: true, // Enable chunked uploads
                chunkSize: 10485760, // 10MB chunks
                forceChunking: true,
                retryChunks: true,
                retryChunksLimit: 3,
                parallelChunkUploads: true,
                timeout: 300000, // 5 minutes timeout
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                clickable: [
                    '#myDropzone_patients_docs',
                    '#myDropzone_patients_docs h6',
                    '#myDropzone_patients_docs .upload-icons',
                ],
                init: function () {
                    // Add request UUID and type to form data
                    this.on("sending", function (file, xhr, formData) {
                        formData.append("request_uuid", $('input[name="request_uuid"]').val());
                        formData.append("type", "patients_docs");
                    });
                    
                    // Handle successful upload
                    this.on("success", function (file, response) {
                        console.log("Uploaded:", response);
                        this.removeFile(file);
                    });
                    
                    // Handle upload errors
                    this.on("error", function (file, response) {
                        console.error("Upload failed:", response);
                    });
                    
                    // Handle upload completion
                    this.on("complete", function (file) {
                        console.log('File uploaded successfully', file);
                        this.removeAllFiles(true);
                        
                        // Refresh file listing after upload
                        $.get('{{ route("showFilesWithType", [$requestListing->uuid, "patients_docs"]) }}', function (data) {
                            console.log("files: ", data);
                            $('#patients_docs_listing').html(data);
                            $('#uploadModal_patients_docs').modal('hide');
                        });
                    });
                }
            });
        }

        {{-- Base URL for file operations --}}
        const baseUrl = "{{ url('/scan-files') }}";

        {{-- Initialize date pickers for date fields --}}
        $("#patient_dob_update, #scan_date_update , #appointment").datepicker({
            dateFormat: "dd/mm/yy"
        });

        {{-- Update price display based on selected modality --}}
        function updatePrice() {
            console.log("function update price according to modality runs on page reload");
            let selectedOption = $("#modality_update option:selected");
            let price = selectedOption.data("price") || 0;
            $("#priceValueEdit").text(price);
            $("#priceAmount").text(price);
            $("#priceAmount").attr("data-price", price);
        }
        
        // Update price on page load
        updatePrice();

        // Update price when modality selection changes
        $("#modality_update").change(function () {
            updatePrice();
        });

        {{-- Button state management for form submission and payment --}}
        const submitBtn = document.getElementById("submitBtn_update");
        const payNowBtn = document.getElementById("payNowBtn");

        // Disable buttons during processing
        function disableButtons() {
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = 0.6;
                submitBtn.style.cursor = "not-allowed";
            }
            if (payNowBtn) {
                payNowBtn.disabled = true;
                payNowBtn.style.opacity = 0.6;
                payNowBtn.style.cursor = "not-allowed";
            }
        }

        // Enable buttons after processing
        function enableButtons() {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = 1;
                    submitBtn.style.cursor = "pointer";
                }
                if (payNowBtn) {
                    payNowBtn.disabled = false;
                    payNowBtn.style.opacity = 1;
                    payNowBtn.style.cursor = "pointer";
                }
        }

        {{-- Form validation configuration --}}
        $('#scanForm_update').validate({
            errorElement: 'small',
            errorClass: 'text-danger d-block mt-1',

            errorPlacement: function(error, element) {
                error.insertAfter(element);
            },

            {{-- Validation rules for all form fields --}}
            rules: {
                patient_name_update: {
                    required: true,
                    maxlength: 255
                },
                patient_dob_update: {
                    required: true,
                    date: false, // disable native date validation
                    pattern: /^(0?[1-9]|[12][0-9]|3[01])[\/](0?[1-9]|1[0-2])[\/]\d{4}$/ // dd/mm/yyyy format
                },
                appointment: {
                    required: true,
                    date: false, // disable native date validation
                    pattern: /^(0?[1-9]|[12][0-9]|3[01])[\/](0?[1-9]|1[0-2])[\/]\d{4}$/ // dd/mm/yyyy format
                },
                scan_date_update: {
                    required: true,
                    pattern: /^(0?[1-9]|[12][0-9]|3[01])[\/](0?[1-9]|1[0-2])[\/]\d{4}$/ // dd/mm/yyyy format
                },
                modality_update: {
                    required: true
                },
                patient_phone: {
                    required: true,
                    pattern: /^[0-9]{9,15}$/,  // Phone number between 9 and 15 digits
                    minlength: 9,               // Minimum 9 digits
                    maxlength: 15               // Maximum 15 digits
                },
                patient_postcode: {
                    required: true,
                    maxlength: 10 // assuming UK postcode or similar length
                },
                patient_address: {
                    required: true,
                    maxlength: 255
                },
                patient_email: {
                    required: true,            // Email is required
                    email: true,               // Email must be in a valid format
                    maxlength: 255             // Email must not exceed 255 characters
                },
                clinical_history: {
                    required: true,
                    maxlength: 500             // You can adjust the max length as needed
                },
                question: {
                    required: true
                },
            },

            {{-- Custom error messages for each field --}}
            messages: {
                patient_name_update: {
                    required: 'Patient name is required.',
                    maxlength: 'Patient name cannot exceed 255 characters.'
                },
                patient_dob_update: {
                    required: 'Date of birth is required.',
                    pattern: 'Enter date in dd/mm/yyyy format.'
                },
                appointment: {
                    required: 'Date of birth is required.',
                    pattern: 'Enter date in dd/mm/yyyy format.'
                },
                scan_date_update: {
                    required: 'Scan date is required.',
                    pattern: 'Enter date in dd/mm/yyyy format.'
                },
                modality_update: {
                    required: 'Please select a modality.'
                },
                patient_phone: {
                    required: 'Phone number is required.',
                    pattern: 'Enter a valid phone number between 9 and 15 digits.',
                    minlength: 'Phone number must be at least 9 digits.',
                    maxlength: 'Phone number cannot exceed 15 digits.'
                },
                patient_postcode: {
                    required: 'Postcode is required.',
                    maxlength: 'Postcode cannot exceed 10 characters.'
                },
                patient_address: {
                    required: 'Address is required.',
                    maxlength: 'Address cannot exceed 255 characters.'
                },
                patient_email: {
                    required: 'Patient email is required.',
                    email: 'Enter a valid email address.',
                    maxlength: 'Email cannot exceed 255 characters.'
                },
                clinical_history: {
                    required: 'Clinical history is required.',
                    maxlength: 'Clinical history cannot exceed 500 characters.' // Adjust as needed
                },
                question: {
                    required: 'Question is required.'
                }
            },

            {{-- Visual feedback for validation --}}
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },

            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            },
            
            {{-- Form submission handler with AJAX --}}
            submitHandler: function(form) {
                var $btn = $('#submitBtn_update');
                $btn.val('Saving...').prop('disabled', true).css('opacity', 0.5).addClass('saving').removeClass('saved');

                var formData = new FormData(form);

                // AJAX submission for form updates
                $.ajax({
                    url: "{{ route('scan.formupdate', ['uuid' => $requestListing->uuid]) }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log("workingggg...........", response.success);
                        $btn.val('Saved').prop('disabled', true).css('opacity', 0.7).removeClass('saving').addClass('saved');
                        setTimeout(function() {
                            $btn.val('Save').prop('disabled', false).css('opacity', 1).removeClass('saved');
                        }, 3000);
                    },
                    error: function(xhr) {
                        $btn.val('Save').prop('disabled', false).css('opacity', 1).removeClass('saving saved');
                        alert("There was an error. Please check the form and try again.");
                        console.log(xhr.responseText);
                    }
                });

                // Prevent default form submission
                return false;
            }
        });

    });

{{-- Saved card selection handling --}}
$('#saved-card-select').on('change', function () {
    const selected = $(this).val();
    if (selected === 'new') {
        $('#card-element').show();
    } else {
        $('#card-element').hide();
    }
});

    {{-- Stripe payment processing initialization --}}
    // Check if Stripe key is available
    const stripeKey = "{{ $stripeKey ?? '' }}";

    // Add error handling for missing Stripe key
    if (!stripeKey) {
        console.error('Stripe key is missing. Please check your environment configuration.');
        $('#payment-message').text('Payment system configuration error. Please contact support.').show();
        $('#card-section').hide();
    } else {
        
        {{-- Initialize Stripe with the key passed from the controller --}}
        const stripe = Stripe(stripeKey);

        const elements = stripe.elements();
        const card = elements.create('card');
        card.mount('#card-element');

        {{-- Function to check if all required fields are filled --}}
        function checkFormCompletion() {
            const patientName = $('#patient_name_update').val().trim();
            const patientDob = $('#patient_dob_update').val().trim();
            const appointment = $('#appointment').val().trim();
            const scanDate = $('#scan_date_update').val().trim();
            const modalitySelected = $('#modality_update').val();

            // Check if all required fields are filled
            return patientName && patientDob && appointment && scanDate && modalitySelected;
        }

        {{-- Monitor form fields for changes to show/hide payment section --}}
        $('#patient_name_update, #patient_dob_update, #appointment, #scan_date_update, #modality_update , #scan_file_update')
            .on('change keyup', function() {
                // Show card section if form is complete
                if (checkFormCompletion()) {
                    $("#card-section").show(); // show card field
                } else {
                    $("#card-section").hide();
                    $("#scanUploadButton").prop("disabled", true);
                }
            });

        {{-- Toggle card element visibility based on saved card selection --}}
        function toggleCardElement() {
            const savedOptions = $('input[name="saved_card_option"]');
            
            if (savedOptions.length === 0) {
                // No saved cards, always show new card input
                $('#card-element-wrapper').show();
                return;
            }

            const selected = savedOptions.filter(':checked').val();

            if (selected === 'new') {
                $('#card-element-wrapper').show();
            } else {
                $('#card-element-wrapper').hide();
            }

            $('input[name="saved_card_option"]').removeClass('cardinputACTVE');
            // Add the class to the currently selected input
            $('input[name="saved_card_option"]:checked').addClass('cardinputACTVE');
        }

        // Attach event listener for saved card selection
        $('input[name="saved_card_option"]').on('change', toggleCardElement);

        // Initial check on page load
        toggleCardElement();

        {{-- Payment button click handler --}}
        $('#payNowBtn').click(async function(e) {
            e.preventDefault();

            $('#payment-message').text('');
            $('#card-loader').show();
            $('#payNowBtn').prop('disabled', true).css('opacity', 0.8).val('Payment in Progress...');

            let price = parseFloat($('#priceAmount').data('price'));
            if (isNaN(price) || price <= 0) {
                $('#payment-message').text('Invalid price. Please check again.');
                $('#card-loader').hide();
                $('#payNowBtn').prop('disabled', false).val('Proceed to Payment');
                return;
            }

            const amount = Math.round(price * 100);
            const modality_name = $('#payNowBtn').data('modality-name');
            const selectedCard = $('input[name="saved_card_option"]:checked').val();

            let paymentData = {
                _token: "{{ csrf_token() }}",
                amount: amount,
                modality_name: modality_name,
                uuid: $('#uuid_field').val(),
                save_card: $('#save-card').is(':checked') ? 1 : 0
            };

            if (selectedCard && selectedCard !== 'new') {
                // Using saved card for payment
                paymentData.payment_method_id = selectedCard;
            } else {
                const saveCard = $('#save-card').is(':checked');

                if (saveCard) {
                    // Create SetupIntent for saving card
                    let setupIntentClientSecret;
                    try {
                        const setupIntentRes = await $.post("{{ route('stripe.setup.intent') }}", {
                            _token: "{{ csrf_token() }}",
                            modality_name: modality_name,
                            uuid: $('#uuid_field').val()
                        });
                        setupIntentClientSecret = setupIntentRes.client_secret;
                    } catch (err) {
                        $('#payment-message').text("Failed to initialize payment. Please try again.");
                        $('#card-loader').hide();
                        $('#payNowBtn').prop('disabled', false).val('Proceed to Payment');
                        return;
                    }

                    const { setupIntent, error } = await stripe.confirmCardSetup(
                        setupIntentClientSecret,
                        {
                            payment_method: {
                                card: card,
                                billing_details: {
                                    name: $('#card-holder-name').val() || undefined,
                                },
                            }
                        }
                    );

                    if (error) {
                        $('#payment-message').text(error.message);
                        $('#card-loader').hide();
                        $('#payNowBtn').prop('disabled', false).val('Proceed to Payment');
                        return;
                    }

                    paymentData.payment_method_id = setupIntent.payment_method;
                } else {
                    // Use one-time payment method (not saved)
                    const { paymentMethod, error } = await stripe.createPaymentMethod({
                        type: 'card',
                        card: card,
                        billing_details: {
                            name: $('#card-holder-name').val() || undefined,
                        },
                    });

                    if (error) {
                        $('#payment-message').text(error.message);
                        $('#card-loader').hide();
                        $('#payNowBtn').prop('disabled', false).val('Proceed to Payment');
                        return;
                    }

                    paymentData.payment_method_id = paymentMethod.id;
                }
            }

            {{-- Process payment via AJAX --}}
            $.ajax({
                url: "{{ route('stripe.ajax.charge') }}",
                method: "POST",
                data: paymentData,
                success: function(res) {
                    $('#card-loader').hide();
                    if (res.status === 'success') {
                        $('#payment-message').text('Payment successful!').removeClass('text-danger').addClass('text-success');
                        $('#payNowBtn').prop('disabled', true).val('Payment Done');
                        setTimeout(() => {
                            window.location.href = "{{ route('thank.you') }}?isrequest=RequestSubmitted";
                        }, 2000);
                    } else {
                        $('#payment-message').text('Payment failed: ' + (res.message || 'Please try again'));
                        $('#payNowBtn').prop('disabled', false).val('Proceed to Payment');
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'An error occurred. Please try again.';
                    $('#payment-message').text(msg);
                    $('#card-loader').hide();
                    $('#payNowBtn').prop('disabled', false).val('Proceed to Payment');
                }
            });
        });

    }
</script>

@endpush

@endsection
