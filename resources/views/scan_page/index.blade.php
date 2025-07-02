@extends('layouts.app')

@section('content')
<div class="right-body-con">
    <div class="right-body-con-inn">
        {{-- Error display section (currently commented out) --}}
        {{-- @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}

        <div class="dasboard-panel-row">
            {{-- Header section with instructions --}}
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
                
                {{-- Right column: Main form --}}
                <div class="upload-files-row2-right">
                    
                    {{-- Main scan upload form --}}
                    <form id="scanForm" action="{{ route('scan.uploadform') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- Patient Information Section --}}
                        <div class="patient-information-row">
                            <h3>Patient Information</h3>
                            <div class="patient-information-inn">
                                {{-- First row: Patient name and date of birth --}}
                                <div class="form-field-row full-row">
                                    <div class="form-field ">
                                        <label for="patient_name">Patient Name <sup>*</sup></label>
                                        <input type="text" name="patient_name" placeholder="Enter the full legal name of the patient.">
                                    </div>
                                    <div class="form-field">
                                        <label for="patient_dob">Date of Birth <sup>*</sup></label>
                                        <input type="text" name="patient_dob" id="patient_dob" placeholder="dd/mm/yyyy">
                                    </div>
                                </div>
                                
                                {{-- Second row: Patient phone and appointment date --}}
                                <div class="form-field-row full-row">
                                    <div class="form-field ">
                                        <label for="patient_phone">Patient phone <sup>*</sup></label>
                                        <input type="text" name="patient_phone" placeholder="Enter phone number">
                                    </div>
                                    <div class="form-field">
                                        <label for="appointment">Appointment <sup>*</sup></label>
                                        <input type="text" name="appointment" id="appointment" placeholder="dd/mm/yyyy">
                                    </div>
                                </div>
                                
                                {{-- Third row: Patient postcode and email --}}
                                <div class="form-field-row full-row">
                                    <div class="form-field ">
                                        <label for="patient_postcode">Patient Postcode <sup>*</sup></label>
                                        <input type="text" name="patient_postcode" placeholder="Enter postcode">
                                    </div>
                                   
                                     <div class="form-field ">
                                        <label for="patient_email">Patient email <sup>*</sup></label>
                                        <input type="email" name="patient_email" placeholder="Enter patient email">
                                    </div>
                                </div>
                                
                                {{-- Fourth row: Patient address and clinical history --}}
                                <div class="form-field-row full-row">
                                    <div class="form-field ">
                                        <label for="patient_address">Patient address <sup>*</sup></label>
                                        <textarea name="patient_address" placeholder="Enter patient address" rows="10"></textarea>
                                    </div>
                                    <div class="form-field ">
                                        <label for="clinical_history">Patient clinical history <sup>*</sup></label>
                                         <textarea name="clinical_history" placeholder="Enter clinical history" rows="10"></textarea>
                                    </div>
                                </div>
                                
                                {{-- Fifth row: Question for radiologist --}}
                                <div class="form-field-row">
                                    <div class="form-field">
                                        <label for="question">Question <sup>*</sup></label>
                                        <textarea name="question" placeholder="Enter any question .." rows="10"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Scan Details Section --}}
                        <div class="patient-information-row row2">
                            <h3>Scan Details</h3>
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
                                        <input type="text" name="scan_date" id="scan_date" placeholder="dd/mm/yyyy">
                                    </div>
                                    <div class="form-field">
                                        <label for="modality">Modality Selection <sup>*</sup></label>
                                        <select name="modality" id="modality" > 
                                            <option value="">Select Modality</option>
                                            {{-- Loop through available modalities with pricing data --}}
                                            @foreach ($modalities as $modality)
                                            <option value="{{ $modality->name }}" data-price="{{ $modality->price }}">{{ $modality->name }}</option>
                                             @endforeach
                                        </select> 
                                    </div>
                                </div>

                                {{-- File upload section with advanced features --}}
                                <div class="form-field-row full-row file-upload d-flex flex-column">
                                    <label>Scan File Upload <sup>*</sup> <span>(DICOM format only, max 1GB)</span></label>
                                    
                                    {{-- Uploaded file display area --}}
                                    <div id="uploaded-file-info" style="margin-top: 10px;">
                                        <div class="align-items-center gap-3 mb-3" id="patients_docs_listing">
                                            
                                        </div>
                                    </div>

                                    {{-- Progress bar for upload tracking --}}
                                    <div id="upload-progress-wrapper" style="display: none;">
                                        <div style="background-color: #e0e0e0; height: 8px; border-radius: 4px; overflow: hidden;">
                                            <div id="upload-progress-bar" style="height: 100%; width: 0%; background-color: #4caf50;"></div>
                                        </div>
                                        <small id="upload-progress-text">0%</small>
                                    </div>

                                    {{-- Dropzone for drag-and-drop file uploads --}}
                                    <div id="dropzone-upload-section" class="form-field" >
                                        <div class="upload-box dropzone" id="myDropzone_patients_docs">
                                            <div class="upload-icons"><img src="{{ asset('images/upload-icon.png') }}"></div>
                                            <h6  id="dropzone-placeholder-update">Drag and drop your files here or <a href="javascript:;">Browse files</a> from your device</h6>
                                            <div class="file-name" id="fileName_update"></div>
                                            <form action="/upload-files/patients_docs">
                                                <input type="hidden" name="request_uuid" value="{{ $uuid }}">
                                                <input type="hidden" name="type" value="patients_docs">
                                                @csrf
                                            </form>
                                        </div>
                                    </div>
                                    
                                    {{-- Legacy upload section (commented out) --}}
                                   {{-- <div id="dropzone-upload-section" class="form-field" style="display: none;">
                                        <div class="upload-box" id="dropzone_update">
                                            <div class="upload-icons"><img src="{{ asset('images/upload-icon.png') }}"></div>
                                            <h6  id="dropzone-placeholder-update">Drag and drop your files here or <a href="javascript:;">Browse files</a> from your device</h6>
                                            <div class="file-name" id="fileName_update"></div>
                                        </div>
                                    </div>
                                    <!-- Progress bar area -->
                                    <div id="upload-progress-wrapper" style="display: none; margin-top: 10px;">
                                        <div style="background-color: #e0e0e0; height: 8px; border-radius: 4px; overflow: hidden;">
                                            <div id="upload-progress-bar" style="height: 100%; width: 0%; background-color: #4caf50;"></div>
                                        </div>
                                        <small id="upload-progress-text">0%</small>
                                    </div>
                                    <!-- Uploaded file display area -->
                                    <div id="uploaded-file-info" style="display: none; margin-top: 10px;">
                                            <a id="uploaded-file-name" href="" target="_blank" style="font-weight: 500; text-decoration: underline;"></a>
                                        <a href="javascript:;" id="remove-uploaded-file" style="color: red; margin-left: 10px;">Remove</a>
                                    </div>
                                    --}}

                                </div>

                                {{-- Hidden fields for form processing --}}
                                 <div class="form-field-row full-row">
                                   <div class="form-field" style="display:none;">       
                                        <label for="scan_file">Scan file</label>
                                        <input type="text" name="scan_file" id="scan_file" value="">
                                        <input type="text" name="uuid" id="uuid" value="{{ $uuid }}">
                                    </div>
                                </div>
                                
                                {{-- Submit button and price display --}}
                                <div class="form-field-row submit-button d-flex align-items-center justify-content-between">
                                    <input type="submit" id="submitBtn" name="" value="Save">
                                    <div id="priceDisplay" style="font-weight:bold; font-size:16px;">Price: £<span id="priceValue">0</span></div>
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

        {{-- File removal functionality with confirmation --}}
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

            // Show confirmation dialog
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

                    // AJAX call to remove file
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
                                // Show success alert
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
                    // '#myDropzone_patients_docs a'
                ],
                init: function () {
                    // Add request UUID and type to form data
                    this.on("sending", function (file, xhr, formData) {
                        formData.append("request_uuid", $('input[name="request_uuid"]').val());
                        formData.append("type", "patients_docs");

                        // Show the progress bar (commented out)
                        // $("#upload-progress-wrapper").show();
                    });
                    
                    // Track upload progress (commented out)
                    this.on("uploadprogress", function (file, progress) {
                        // Update progress bar and text (commented out)
                        // $("#upload-progress-bar").css("width", progress + "%");
                        // $("#upload-progress-text").text(Math.round(progress) + "%");
                    });
                    
                    // Handle successful upload
                    this.on("success", function (file, response) {
                        console.log("Uploaded:", response);
                        this.removeFile(file);
                    });
                    
                    // Handle upload errors
                    this.on("error", function (file, response) {
                        console.error("Upload failed:", response);
                        // Optionally reset progress bar on error (commented out)
                        // $("#upload-progress-wrapper").hide();
                        // $("#upload-progress-bar").css("width", "0%");
                        // $("#upload-progress-text").text("0%");
                    });
                    
                    // Handle upload completion
                    this.on("complete", function (file) {
                        console.log('File uploaded successfully', file);
                        // Reset progress bar (commented out)
                        // $("#upload-progress-wrapper").hide();
                        // $("#upload-progress-bar").css("width", "0%");
                        // $("#upload-progress-text").text("0%");
                        this.removeAllFiles(true);    //   this.removeFile(file);
                        
                        // Refresh file listing and close modal
                        $.get('{{ route("showFilesWithType", [$uuid, "patients_docs"]) }}', function (data) {
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
        $("#patient_dob, #scan_date ,#appointment").datepicker({
            dateFormat: "dd/mm/yy"
            // minDate: +1 
        });

        {{-- Handle modality selection and price display --}}
        {{-- Handle modality selection and price display --}}
        $("#modality").change(function () {
            let selectedOption = $(this).find(":selected");
            let price = selectedOption.data("price");
            let modality_id = selectedOption.val();

            // Update price display
            $("#priceValue").text(price);
            $("#priceAmount").text(price);
            $("#priceSection").show();
            $("#card-section").show(); // show card field
            $("#scanUploadButton").prop("disabled", true); // disable until payment

            // Store price + modality globally for payment processing
            $("#payNowBtn").data("price", price).data("modality", modality_id);
        });

        {{-- Button state management --}}
        const submitBtn = document.getElementById("submitBtn");
        
        // Disable buttons initially if file uploading or if needed
        function disableButtons() {
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = 0.6;
                submitBtn.style.cursor = "not-allowed";
            }
        }

        function enableButtons() {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = 1;
                    submitBtn.style.cursor = "pointer";
                }
        }

        {{-- Legacy Dropzone configuration (commented out) --}}
        // Dropzone.autoDiscover = false;

        // let relativePath = "";
        // const dropzoneElement = document.querySelector("div#dropzone_update");
        // const dropzoneContainer = document.getElementById("dropzone-upload-section");
        // const uploadedFileInfo = document.getElementById("uploaded-file-info");
        // const uploadedFileName = document.getElementById("uploaded-file-name");
        // const removeFileLink = document.getElementById("remove-uploaded-file");
        // const progressBar = document.getElementById("upload-progress-bar");
        // const progressWrapper = document.getElementById("upload-progress-wrapper");
        // const progressText = document.getElementById("upload-progress-text");

        // const existingPath = "{{ $requestListing->scan_file ?? '' }}";
        // const existingName = "{{ basename($requestListing->scan_file ?? '') }}";
        // //console.log(existingPath);
        // if (existingPath) {
        //     relativePath = existingPath;
        //     uploadedFileName.innerText = existingName;
        //     uploadedFileName.href =existingPath;
        //     console.log(existingPath);
        //     document.getElementById("scan_file").value = relativePath;
        //     uploadedFileInfo.style.display = "block";
        // } else {
        //     dropzoneContainer.style.display = "block";
        // }

        // if (dropzoneElement) {
        //     const myDropzone = new Dropzone(dropzoneElement, {
        //         url: "/upload-advanced",
        //         paramName: "file",
        //         maxFilesize: 1500,
        //         // acceptedFiles: ".jpg,.jpeg,.png,.pdf,.dcm",
        //         maxFiles: 1,
        //         chunking: true,
        //         chunkSize: 10485760,
        //         forceChunking: true,
        //         retryChunks: true,
        //         retryChunksLimit: 3,
        //         parallelChunkUploads: true,
        //         timeout: 300000,
        //         headers: {
        //             "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        //         },
        //         previewsContainer: false, // Don't show inside Dropzone
        //         clickable: true,
        //         init: function () {
        //             this.on("processing", () => {
        //                 progressWrapper.style.display = "block";
        //                 progressBar.style.width = "0%";
        //                 progressText.innerText = "0%";
        //             });

        //             this.on("uploadprogress", function (file, progress) {
        //                 progressBar.style.width = `${progress}%`;
        //                 progressText.innerText = `${Math.round(progress)}%`;
        //             });
                    
        //             this.on("sending", function (file, xhr, formData) {
        //                 const chunkIndex = file.upload.chunkIndex || 0;
        //                 const totalChunks = file.upload.totalChunkCount || 1;
        //                 console.log(`Sending chunk ${chunkIndex + 1} of ${totalChunks}`);
        //             });


        //             this.on("success", function (file, response) {
        //                 relativePath = response.path + response.name;
        //                 document.getElementById("scan_file").value = relativePath;
        //                 uploadedFileName.innerText = response.name;
        //                 // uploadedFileName.href = response.path+ response.name;
        //                 uploadedFileName.href = baseUrl + "/" + response.name;
        //                 uploadedFileInfo.style.display = "block";
        //                 dropzoneContainer.style.display = "none";
        //                 progressWrapper.style.display = "none";
        //                 this.removeAllFiles(true);
        //             });

        //             this.on("error", function (file, errorMessage) {
        //                 Swal.fire({
        //                     icon: "error",
        //                     title: "Upload Error",
        //                     text: errorMessage
        //                 });
        //                 progressWrapper.style.display = "none";
        //             });
        //         }
        //     });
        // }

        // // Remove uploaded file
        // removeFileLink.addEventListener("click", function () {
        //     $.ajax({
        //         url: "/remove-uploaded-file",
        //         method: "POST",
        //         data: { file_path: relativePath },
        //         headers: {
        //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         success: function (response) {
        //             if (response.success) {
        //                 uploadedFileInfo.style.display = "none";
        //                 dropzoneContainer.style.display = "block";
        //                 document.getElementById("scan_file").value = "";
        //                 relativePath = "";
        //                 console.log("file removed from bacend as well ");
        //             } else {
        //                 console.error("Error removing file:", response);
        //             }
        //         },
        //         error: function (xhr, status, error) {
        //             console.error("Error:", error);
        //         }
        //     });
        // });
        
        {{-- Form validation configuration --}}
        {{-- Form validation configuration --}}
        $('#scanForm').validate({
            errorElement: 'small',
            errorClass: 'text-danger d-block mt-1',

            errorPlacement: function(error, element) {
                error.insertAfter(element);
            },
            
            {{-- Validation rules for all form fields --}}
            rules: {
                patient_name: {
                    required: true,
                    maxlength: 255
                },
                patient_dob: {
                    required: true,
                    date: false, // disable native date validation
                    pattern: /^(0?[1-9]|[12][0-9]|3[01])[\/](0?[1-9]|1[0-2])[\/]\d{4}$/ // dd/mm/yyyy format
                },
                appointment: {
                    required: true,
                    date: false, // disable native date validation
                    pattern: /^(0?[1-9]|[12][0-9]|3[01])[\/](0?[1-9]|1[0-2])[\/]\d{4}$/ // dd/mm/yyyy format
                },
                scan_date: {
                    required: true,
                    pattern: /^(0?[1-9]|[12][0-9]|3[01])[\/](0?[1-9]|1[0-2])[\/]\d{4}$/ // dd/mm/yyyy format
                },
                modality: {
                    required: true
                },
                question: {
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
                }
            },
            
            {{-- Custom error messages for each field --}}
            messages: {
                patient_name: {
                    required: 'Patient name is required.',
                    maxlength: 'Patient name cannot exceed 255 characters.'
                },
                patient_dob: {
                    required: 'Date of birth is required.',
                    pattern: 'Enter date in dd/mm/yyyy format.'
                },
                appointment: {
                    required: 'Appointment date is required.',
                    pattern: 'Enter date in dd/mm/yyyy format.'
                },
                scan_date: {
                    required: 'Scan date is required.',
                    pattern: 'Enter date in dd/mm/yyyy format.'
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
                modality: {
                    required: 'Please select a modality.'
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
            
            {{-- Form submission handler --}}
            submitHandler: function(form) {
                form.submit();
            }
        });

        {{-- Trigger validation on date field changes --}}
        $('#patient_dob, #appointment, #scan_date').on('change', function() {
            $(this).valid(); // Trigger validation for the field that was changed
        });

    });

</script>
    
@endpush
@endsection




       