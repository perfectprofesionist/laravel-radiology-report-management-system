@extends('layouts.app')

@section('content')



<div class="">
    <div class="row g-4">
        <!-- Exam Info Card -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header mb-2">
                        <i class="fas fa-clipboard-list me-2"></i>Exam Info
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-label">Exam Id</div>
                            <div class="info-value">{{ $request->exam_id }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Examination Status</div>
                            <div class="info-value">
                                 @role('user')
                                <span class="badge bg-warning text-dark">{{ $request->status }}</span>
                                @endrole
                                 @role(['admin', 'sub-admin'])

                                    
                                    @if($request->pending_status)
                                        <div class="alert alert-warning">
                                            <strong>Status Change Pending Approval</strong>
                                            <p class="mb-0">Requested by: {{ $request->statusUpdatedBy ? $request->statusUpdatedBy->username : 'N/A' }}</p>
                                            <p class="mb-0">Requested on: {{ $request->status_updated_at ? $request->status_updated_at->format('M d, Y H:i') : 'N/A' }}</p>
                                            <p class="mb-0">Requested Status: {{ $request->pending_status_value }}</p>
                                            
                                            @if(auth()->user()->hasRole('admin'))
                                                <div class="mt-3">
                                                    <button type="button" class="btn btn-success" data-bs-toggle="collapse" data-bs-target="#approveStatusCollapse">
                                                        Approve
                                                    </button>
                                                    <button type="button" class="btn btn-danger" data-bs-toggle="collapse" data-bs-target="#rejectStatusCollapse">
                                                        Reject
                                                    </button>
                                                </div>

                                                <div class="collapse mt-3" id="approveStatusCollapse">
                                                    <form action="{{ route('request-listing.approve-status', ['uuid' => $request->uuid]) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="action" value="approve">
                                                        <div class="mb-3">
                                                            <label for="approval_comment" class="form-label">Approval Comment (Optional)</label>
                                                            <textarea class="form-control" id="approval_comment" name="approval_comment" rows="2"></textarea>
                                                        </div>
                                                        <button type="submit" class="btn btn-success">Confirm Approval</button>
                                                    </form>
                                                </div>

                                                <div class="collapse mt-3" id="rejectStatusCollapse">
                                                    <form action="{{ route('request-listing.approve-status', ['uuid' => $request->uuid]) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="action" value="reject">
                                                        <div class="mb-3">
                                                            <label for="rejection_comment" class="form-label">Rejection Reason</label>
                                                            <textarea class="form-control" id="rejection_comment" name="rejection_comment" rows="2" required></textarea>
                                                        </div>
                                                        <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>

                                            @if(auth()->user()->hasRole('admin'))

                                                <form method="POST" action="{{ route('request-listing.update-status', $request->uuid) }}" id="statusForm">
                                                    @csrf
                                                    <div class="dropedown-table-sec">
                                                        <div class="dropdown">
                                                            <button class="btn dropdown-toggle" type="button" id="requestStatusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                                {{ $request->status }}
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-item-outer" aria-labelledby="requestStatusDropdown">
                                                                <li><a class="dropdown-item" href="#" data-status="Pending">Pending</a></li>
                                                                <li><a class="dropdown-item" href="#" data-status="Assigned">Assigned</a></li>
                                                                <li><a class="dropdown-item" href="#" data-status="Incident">Incident</a></li>
                                                                <li><a class="dropdown-item" href="#" data-status="Completed">Completed</a></li>
                                                            </ul>
                                                        </div>
                                                        <input type="hidden" name="status" id="statusInput">
                                                    </div>
                                                </form>
                                            @endif
                                       

                                            
                                        @else
                                       
                                       <form method="POST" action="{{ route('request-listing.update-status', $request->uuid) }}" id="statusForm">
                                           @csrf
                                           <div class="dropedown-table-sec">
                                               <div class="dropdown">
                                                   <button class="btn dropdown-toggle" type="button" id="requestStatusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                       {{ $request->status }}
                                                   </button>
                                                   <ul class="dropdown-menu dropdown-item-outer" aria-labelledby="requestStatusDropdown">
                                                       <li><a class="dropdown-item" href="#" data-status="Pending">Pending</a></li>
                                                       <li><a class="dropdown-item" href="#" data-status="Assigned">Assigned</a></li>
                                                       <li><a class="dropdown-item" href="#" data-status="Incident">Incident</a></li>
                                                       <li><a class="dropdown-item" href="#" data-status="Completed">Completed</a></li>
                                                   </ul>
                                               </div>
                                               <input type="hidden" name="status" id="statusInput">
                                           </div>
                                       </form>
                                    @endif
                                    
                                   
                                @endrole

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Appointement</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($request->appointment)->format('d/m/Y') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Upload Referred Date</div>
                            <div class="info-value">{{ $request->created_at->format('d/m/Y') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Referred By</div>
                            <div class="info-value">{{ ucfirst($request->user->dentist_name) }}</div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="info-label">Upload Referred Date</div>
                            <div class="info-value">04/03/2025</div>
                        </div> --}}
                        <div class="col-md-6">
                            <div class="info-label">Referred Doctor</div>
                            <div class="info-value">{{ ucfirst($request->user->dentist_name) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Info Card -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header mb-2">
                        <i class="fas fa-user me-2"></i>Patient Info
                    </div>
                    <div class="row g-3"  id="patientStaticInfo">
                        <div class="col-md-6">
                            <div class="info-label">Patient Name</div>
                            <div class="info-value" data-label="patient_address">{{ ucfirst($request->patient_name) }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Date of Birth</div>
                            <div class="info-value " data-label="patient_dob">{{ \Carbon\Carbon::parse($request->patient_dob)->format('d/m/Y') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Phone</div>
                            <div class="info-value " data-label="patient_phone">{{ $request->patient_phone }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Email</div>
                            <div class="info-value " data-label="patient_email">{{ $request->patient_email }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Address</div>
                            <div class="info-value " data-label="patient_address">{{ $request->patient_address }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Post Code</div>
                            <div class="info-value " data-label="patient_postcode">{{ $request->patient_postcode }}</div>
                        </div>
                        @role(['user', 'admin'])
                        <div class="col-12 mt-3">
                            <button type="button" id="editBtn" class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i>Update
                            </button>
                        </div>
                        @endrole
                    </div>

                    @role(['user', 'admin'])
                    {{-- Editable Form --}}
                    <form id="patientEditForm" class="row g-3" method="POST" action="{{ route('patient.update', $request->uuid) }}" style="display: none;">
                        @csrf
                        @method('PUT')
                        <div class="col-md-6">
                            <label class="form-label">Patient Name</label>
                            <input type="text" name="patient_name" class="form-control" value="{{ $request->patient_name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input type="text" name="patient_dob" id="patient_dob" class="form-control" value="{{ \Carbon\Carbon::parse($request->patient_dob)->format('d/m/Y') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="patient_phone" class="form-control" value="{{ $request->patient_phone }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="patient_email" class="form-control" value="{{ $request->patient_email }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <input type="text" name="patient_address" class="form-control" value="{{ $request->patient_address }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Post Code</label>
                            <input type="text" name="patient_postcode" class="form-control" value="{{ $request->patient_postcode }}">
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-success m-0">
                                <i class="fas fa-save me-2"></i>Save
                            </button>
                            <button type="button" id="cancelEdit" class="btn btn-secondary ms-2">
                                Cancel
                            </button>
                        </div>
                    </form>
                    @endrole
                </div>
            </div>
        </div>

        <!-- Radiology Information & Report -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
               
                <div class="card-body">
                    <div class="card-header mb-2">
                        <i class="fas fa-x-ray me-2"></i>Radiology Information & Report
                    </div>
                    <div id="radiologyStaticInfo">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-label">Radiology Report</div>
                                 @role(['admin', 'sub-admin'])
                                <a href="javascript:void(0);" id="reportButton" class="report-button">
                                    <i class="fas fa-file-alt"></i>
                                    Edit Report
                                </a>
                                |
                                <a href="{{ route('request-listing.pdf', $request->uuid) }}" class="report-button">
                                        <i class="fa fa-download mr-2" aria-hidden="true"></i>Download Report
                                </a>
                                @endrole
                                @role('user')
                                    @if($request->status == "Completed" )
                                    <a href="{{ route('request-listing.pdf', $request->uuid) }}" class="report-button">
                                        <i class="fa fa-download mr-2" aria-hidden="true"></i>Download Report
                                    </a>
                                @endif
                               @endrole
                            </div>
                            
                            <div class="col-md-6">
                                <div class="info-label">Question For Radiologist</div>
                                <div class="info-value" data-label="question">{{ $request->question }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Clinical History</div>
                                <div class="info-value" data-label="clinical_history">{{ $request->clinical_history }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Modality</div>
                                <div class="info-value" data-label="modality">{{ $request->modality }}</div>
                            </div>
                            @role(['user' , 'admin'])
                            <div class="col-12 mt-3">
                                <button type="button" id="editRadiologyBtn" class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i>Update Info
                                </button>
                            </div>
                            @endrole
                        </div>
                    </div>
                    @role(['user' , 'admin'])
                        <form id="radiologyEditForm" class="row g-3" method="POST" action="{{ route('radiology.update', $request->uuid) }}" style="display: none;">
                            @csrf
                            @method('PUT')
                            <div class="col-md-12">
                                <label class="form-label">Question For Radiologist</label>
                                <textarea name="question" id="question" cols="30" class="form-control" rows="10">{{ $request->question }}</textarea>
                                
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Clinical History</label>
                                <textarea name="clinical_history" id="clinical_history" cols="30" class="form-control" rows="10">{{ $request->clinical_history }}</textarea>
                            </div>
                            {{-- <div class="col-md-6">
                                <label class="form-label">Modality</label>
                                <input type="text" name="modality" class="form-control" value="{{ $request->modality }}">
                            </div> --}}
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-success m-0">
                                    <i class="fas fa-save me-2"></i>Save
                                </button>
                                <button type="button" id="cancelRadiologyEdit" class="btn btn-secondary ms-2">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    @endrole
                </div>
            </div>
        </div>

        <!-- Conversations -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
               
                <div class="card-body">
                     <div class="card-header mb-2">
                        <i class="fas fa-comments me-2"></i>Conversations
                    </div>
                    <div id="chat-messages"></div>
                    <div class="py-3" id="chat-box" data-request-id="{{ $request->uuid }}">
                        <form id="chat-form">
                            <label class="form-label mb-2">Type Your Message Here...</label>
                            <textarea class="form-control message-input" id="chat-message" placeholder="Type a message..."></textarea>
                            <div class="d-flex justify-content-start">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i> Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        

        <!-- Documents Download and Upload -->
        <div class="col-12">
            <div class="card">
             
                <div class="card-body">
                    <div class="card-header mb-2">
                        <i class="fas fa-folder me-2"></i>Documents Download and Upload
                    </div>
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="info-label mb-2">Patients Documents 
                                @role(['user', 'admin'])
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#uploadModal_patients_docs">
                                    <i class="fas fa-upload me-2"></i>Choose Files
                                </button>
                                @endrole
                            </div>
                            <div class="align-items-center gap-3 mb-3" id="patients_docs_listing">
                                <ol>
                                @foreach ($files as $file )
                                    @if($file->type == "patients_docs")
                                        <li><a target="_blank" href="{{ route('scan.file', [$file->file_name])}}" class="text-muted">{{ $file->original_name}}</a>
                                            <a href="#" 
                                                class="text-danger ms-2 remove-file-link" 
                                                data-file-path="{{ $file->file_name }}" 
                                                data-request-uuid="{{ $request->uuid ?? '' }}" 
                                                data-type="patients_docs"
                                                style="cursor:pointer;">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </a></li>
                                    @endif
                                @endforeach
                                </ol>
                            </div>
                        </div>
                        <div class="col-12">


                        

                            

                                <div class="info-label mb-2">Supporting Files 
                                    @role(['user', 'admin'])
                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#uploadModal_patients_supporting_files">
                                    <i class="fas fa-upload me-2"></i>Choose Files
                                    </button>
                                    @endrole
                                </div>
                                <div class="align-items-center gap-3 mb-3" id="patients_supporting_files_listing">
                                    <ol>
                                    @foreach ($files as $file )
                                        @if($file->type == "patients_supporting_files")
                                            <li><a target="_blank" href="{{ route('scan.file', [$file->file_name])}}" class="text-muted">{{ $file->original_name}}</a>
                                                <a href="#" 
                                                    class="text-danger ms-2 remove-file-link" 
                                                    data-file-path="{{ $file->file_name }}" 
                                                    data-request-uuid="{{ $request->uuid ?? '' }}" 
                                                    data-type="patients_supporting_files"
                                                    style="cursor:pointer;">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </a></li>
                                        @endif
                                    @endforeach
                                    </ol>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Download Reports Section -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header mb-2">
                        <i class="fas fa-download me-2"></i>Download Doctor Report
                    </div>
                    <div class="mb-3">
                    @role('user')
                        <a href="javascript:void(0);" id="reportButtondoctor" class="report-button">
                            <i class="fas fa-file-alt"></i>
                            Edit Report
                        </a>
                        |
                        <a href="{{ route('request-listing.pdfdoctor', $request->uuid) }}" class="report-button">
                            <i class="fa fa-download mr-2" aria-hidden="true"></i>Download Report
                        </a>
                    @endrole
                    @role('admin')
                        
                        <a href="{{ route('request-listing.pdfdoctor', $request->uuid) }}" class="report-button">
                            <i class="fa fa-download mr-2" aria-hidden="true"></i>Download Report
                        </a>
                    @endrole
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="info-label mb-2">Patients Documents 
                                @role(['user', 'admin'])
                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#uploadModal_doctors_docs">
                                    <i class="fas fa-upload me-2"></i>Choose Files
                                </button>
                                @endrole
                            </div>
                            <div class="align-items-center gap-3 mb-3" id="doctors_docs_listing">
                                <ol>
                                @foreach ($files as $file )
                                    @if($file->type == "doctors_docs")
                                        <li><a target="_blank" href="{{ route('scan.file', [$file->file_name])}}" class="text-muted">{{ $file->original_name}}</a>
                                            <a href="#" 
                                                class="text-danger ms-2 remove-file-link" 
                                                data-file-path="{{ $file->file_name }}" 
                                                data-request-uuid="{{ $request->uuid ?? '' }}" 
                                                data-type="doctors_docs"
                                                style="cursor:pointer;">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </a></li>
                                    @endif
                                @endforeach
                                </ol>
                            </div>
                        </div>
                        <div class="col-12">


                        

                            

                                <div class="info-label mb-2">Supporting Files 
                                    @role(['user', 'admin'])
                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#uploadModal_doctors_supporting_files">
                                        <i class="fas fa-upload me-2"></i>Choose Files
                                    </button>
                                    @endrole
                                </div>
                                <div class="align-items-center gap-3 mb-3" id="doctors_supporting_files_listing">
                                    <ol>
                                    @foreach ($files as $file )
                                        @if($file->type == "doctors_supporting_files")
                                            <li><a target="_blank" href="{{ route('scan.file', [$file->file_name])}}" class="text-muted">{{ $file->original_name}}</a>
                                                <a href="#" 
                                                    class="text-danger ms-2 remove-file-link" 
                                                    data-file-path="{{ $file->file_name }}" 
                                                    data-request-uuid="{{ $request->uuid ?? '' }}" 
                                                    data-type="doctors_supporting_files"
                                                    style="cursor:pointer;">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </a></li>
                                        @endif
                                    @endforeach
                                    </ol>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Download Hospital Request Form -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
             
                <div class="card-body">
                    <div class="card-header mb-2">
                        <i class="fas fa-hospital me-2"></i>Download Hospital Request Form
                    </div>
                    <a href="{{ asset('res/request-form.pdf') }}" download="request-form.pdf" class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>Request Form
                    </a>
                </div>
            </div>
        </div>

        @role(['admin', 'sub-admin'])
        <!-- Admin Chat -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-header mb-2">
                        <i class="fas fa-user-shield me-2"></i>Admin Chat
                    </div>
                    <div id="admin-chat-messages"></div>
                    <div class="py-3" id="admin-chat-box" data-request-id="{{ $request->uuid }}">
                        <form id="admin-chat-form">
                            <label class="form-label mb-2">Type Your Message Here...</label>
                            <textarea class="form-control message-input" id="admin-chat-message" placeholder="Type a message..."></textarea>
                            <div class="d-flex justify-content-start">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i> Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endrole

        {{-- 
        <div class="col-12">
            <!-- Notes History Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <i class="fas fa-history me-2"></i>Notes History
                </div>
                <div class="card-body">
                    <div class="accordion" id="notesHistoryAccordion">
                        @foreach($request->notesHistory()->orderBy('created_at', 'desc')->get() as $history)
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#history{{ $history->id }}">
                                        <div class="d-flex justify-content-between align-items-center w-100">
                                            <span>
                                                @if($history->status === 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($history->status === 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @else
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                                {{ $history->created_at->format('M d, Y H:i') }}
                                            </span>
                                            <span class="ms-2">
                                                By: {{ $history->updatedBy->username ?? 'Unknown' }}
                                            </span>
                                        </div>
                                    </button>
                                </h2>
                                <div id="history{{ $history->id }}" class="accordion-collapse collapse" data-bs-parent="#notesHistoryAccordion">
                                    <div class="accordion-body">
                                        <div class="notes-content mb-3">
                                            <strong>Notes Content:</strong>
                                            <div class="mt-2">{!! $history->notes_content !!}</div>
                                        </div>
                                        @if($history->comment)
                                            <div class="comment-section">
                                                <strong>Comment:</strong>
                                                <div class="mt-2">{!! $history->comment !!}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        --}}
        
    </div>
</div>
{{--
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Status</h5>
    </div>
    <div class="card-body">
        

        @if($request->statusHistory && $request->statusHistory->count() > 0)
            <div class="mt-4">
                <h6>Status History</h6>
                <div class="accordion" id="statusHistoryAccordion">
                    @foreach($request->statusHistory as $history)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#statusHistory{{ $history->id }}">
                                    <div class="d-flex justify-content-between align-items-center w-100">
                                        <span>
                                            <span class="badge {{ $history->status === 'pending' ? 'bg-warning' : ($history->status === 'approved' ? 'bg-success' : 'bg-danger') }}">
                                                {{ ucfirst($history->status) }}
                                            </span>
                                            {{ $history->created_at->format('d/m/Y H:i') }}
                                        </span>
                                        <span class="ms-2">
                                            Updated by: {{ $history->updatedBy->name ?? 'Unknown' }}
                                        </span>
                                    </div>
                                </button>
                            </h2>
                            <div id="statusHistory{{ $history->id }}" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <div class="status-content">
                                        <strong>Previous Status:</strong> {{ $history->previous_status }}<br>
                                        <strong>New Status:</strong> {{ $history->new_status }}<br>
                                        @if($history->comment)
                                            <div class="comment-section mt-2">
                                                <strong>Comment:</strong><br>
                                                {{ $history->comment }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <p class="text-muted">No status history available.</p>
        @endif
    </div>
</div>
--}}
@hasanyrole(['admin', 'sub-admin'])
<div id="statusNotesModal" style="display:none;" class="modal">
    <div class="modal-content">
        <span id="closeModal" class="close">&times;</span>

        <form action="{{ route('request-listing.update-status-notes', $request->uuid) }}" method="POST" id="statusNotesForm">
            @csrf
            <div class="mb-3">
                <label for="notes"><strong>Notes</strong></label>
                <input type="hidden" name="notes" id="notes">
                <div 
                    id="quill-editor" data-editable="{{ auth()->user()->hasRole('admin') ? 'true' : 'false' }}"
                    data-content="{!! htmlspecialchars($request->notes ?? '', ENT_QUOTES) !!}">
                </div>
                
                @if($request->notes_status === 'pending')
                    <div class="alert alert-warning mt-3">
                        <strong>Pending Approval</strong>
                        <p>These notes were updated by {{ $request->updatedBy->username ?? 'Unknown' }} 
                            @if($request->notes_updated_at)
                                on {{ $request->notes_updated_at->format('M d, Y H:i') }}
                            @endif
                        </p>
                        
                        <div class="pending-changes mt-3">
                            <h6>Pending Changes:</h6>
                            <div class="pending-content">
                                {!! $request->pending_notes !!}
                            </div>
                        </div>

                        @if(auth()->user()->hasRole('admin'))
                            <div class="mt-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-success " data-bs-toggle="collapse" data-bs-target="#approvalCommentCollapse">Approve</button>
                                    
                                        <button type="button" class="btn btn-danger " data-bs-toggle="collapse" data-bs-target="#rejectionForm">Reject</button>
                                    </div>
                                </div>
                                
                                <div class="collapse mt-2" id="approvalCommentCollapse">
                                    <form action="{{ route('request-listing.update-status-notes', ['uuid' => $request->uuid]) }}" method="POST" class="mt-2">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <input type="hidden" name="notes" value="{{ $request->pending_notes ?? $request->notes }}">
                                        <div class="mb-3">
                                            <label for="approval_comment" class="form-label">Approval Comment (Optional)</label>
                                            <textarea class="form-control" id="approval_comment" name="approval_comment" rows="2"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success">Confirm Approval</button>
                                    </form>
                                </div>

                                <div class="collapse mt-3" id="rejectionForm">
                                    <div class="card card-body">
                                        <form action="{{ route('request-listing.update-status-notes', ['uuid' => $request->uuid]) }}" method="POST" class="mt-2">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <input type="hidden" name="notes" value="{{ $request->pending_notes ?? $request->notes }}">
                                            <div class="mb-3">
                                                <label for="rejection_comment" class="form-label">Rejection Reason</label>
                                                <textarea class="form-control" id="rejection_comment" name="rejection_comment" rows="3"></textarea>
                                            </div>
                                            <button type="submit" name="reject" value="1" class="btn btn-danger">Reject</button>
                                        
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                @if($request->notes_status === 'rejected')
                    <div class="alert alert-danger mt-3">
                        <strong>Rejected</strong>
                        <p>These notes were rejected by {{ $request->rejectedBy->username ?? 'Unknown' }} 
                            @if($request->rejected_at)
                                on {{ $request->rejected_at->format('M d, Y H:i') }}
                            @endif
                        </p>
                        @if($request->rejection_comment)
                            <div class="rejection-comment mt-2">
                                <strong>Rejection Reason:</strong>
                                <p>{{ $request->rejection_comment }}</p>
                            </div>
                        @endif
                        <div class="mt-3">
                            <div class="accordion" id="rejectedNotesAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#rejectedNotesContent">
                                            View Rejected Notes
                                        </button>
                                    </h2>
                                    <div id="rejectedNotesContent" class="accordion-collapse collapse" data-bs-parent="#rejectedNotesAccordion">
                                        <div class="accordion-body">
                                            {!! $request->pending_notes !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($request->notes_status === 'approved' && $request->notes)
                    <div class="alert alert-success">
                        <div>
                            <strong>Notes Approved</strong>
                            <p class="mb-0">Changes pushed by: {{ $request->updatedBy ? $request->updatedBy->username : 'N/A' }}</p>
                            <p class="mb-0">Approved by: {{ $request->approvedBy ? $request->approvedBy->username : 'N/A' }}</p>
                            <p class="mb-0">Approved on: {{ $request->notes_approved_at ? $request->notes_approved_at->format('M d, Y H:i') : 'N/A' }}</p>
                            @if($request->approval_comment)
                                <p class="mb-0 mt-2"><strong>Approval Comment:</strong> {{ $request->approval_comment }}</p>
                            @endif
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#approvedNotesContent" aria-expanded="false">
                                View Notes
                            </button>
                            <div class="collapse mt-2" id="approvedNotesContent">
                                <div class="card card-body">
                                    {!! nl2br(e($request->notes)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>


@endhasanyrole
@role('user')
<div id="statusDoctorNotesModal" style="display:none;" class="modal">
    <div class="modal-content">
        <span id="closeModalDoc" class="close">&times;</span>
        <form action="{{ route('request-listing.update-doctor-notes', $request->uuid) }}" method="POST" id="statusNotesFormDoctor">
            @csrf
            <div class="mb-3">
                <label for="notes"><strong>Notes</strong></label>
                <input type="hidden" name="doctor_notes" id="doctor_notes">
                <div 
                    id="quill-editor-doc" data-editable="{{ auth()->user()->hasRole('admin') ? 'true' : 'false' }}"
                    data-doctor-content="{!! htmlspecialchars($request->doctor_notes ?? '', ENT_QUOTES) !!}">
                </div>
            </div>
            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endrole


@foreach ($types as $type)

<!-- Modal -->
<div class="modal fade" id="uploadModal_{{$type}}" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Patient Documents ({{$type}})</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <form action="/upload-files/{{$type}}" class="dropzone" id="myDropzone_{{$type}}">
                <input type="hidden" name="request_uuid" value="{{ $request->uuid }}">
                <input type="hidden" name="type" value="{{$type}}">
                @csrf
                 <div class="upload-icons"><img src="{{ asset('images/upload-icon.png') }}"></div>
                                            <h6  id="dropzone-placeholder-update">Drag and drop your files here or <a href="javascript:;">Browse files</a> from your device</h6>
                </form>
            </div>
        </div>
    </div>
</div>


    
@endforeach



@push('scripts')

<script>
$(document).ready(function() {


    $('[data-bs-target="#approvalCommentCollapse"]').on('click', function() {
        if ($('#rejectionForm').hasClass('show')) {
            $('#rejectionForm').collapse('hide');
        }
    });

    $('[data-bs-target="#rejectionForm"]').on('click', function() {
        if ($('#approvalCommentCollapse').hasClass('show')) {
            $('#approvalCommentCollapse').collapse('hide');
        }
    });

$(document).on('click', '.remove-file-link', function(e) {
    e.preventDefault();

    const $link = $(this);
    const filePath = $link.data('file-path');
    const requestUuid = $link.data('request-uuid');
    const type = $link.data('type');

    if (!filePath) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid file path',
            text: 'The file path is missing or incorrect.',
        });
        return;
    }

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

                        // Refresh the listing
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





    $("#patient_dob").datepicker({
        dateFormat: "dd/mm/yy",
        // minDate: +1  // Uncomment if you want to restrict past dates
    });

        $(document).on('click', '.dropdown-item-outer .dropdown-item', function (e) {
            e.preventDefault();
            let selectedStatus = $(this).data('status');
            $('#statusInput').val(selectedStatus);
            $('#statusForm').submit();
        });

      if (Dropzone.instances.length > 0) {
            Dropzone.instances.forEach(dz => dz.destroy());
        }

        $('#reportButton').click(function() {
            $('#statusNotesModal').fadeIn();
        });

        $('#closeModal').click(function() {
            $('#statusNotesModal').fadeOut();
        });

        $(window).click(function(event) {
            if ($(event.target).is('#statusNotesModal')) {
                $('#statusNotesModal').fadeOut();
            }
        });

        const baseUrl = "{{ url('/scan-files') }}";
        const requestUuid = "{{ $request->uuid ?? '' }}"; // Get the request UUID
 // Sending the UUID

    
    @foreach ($types as $type)

        const $dropzoneElement_{{$type}} = $("#myDropzone_{{$type}}");
        if ($dropzoneElement_{{$type}}.length) {
            const myDropzone_{{$type}} = new Dropzone("#myDropzone_{{$type}}", {
                url: "/upload-files/{{$type}}",
                paramName: "file",
                maxFilesize: 1500,
                // acceptedFiles: ".jpg,.jpeg,.png,.pdf,.dcm",
                maxFiles: null,
                chunking: true,
                chunkSize: 10485760,
                forceChunking: true,
                retryChunks: true,
                retryChunksLimit: 3,
                parallelChunkUploads: true,
                timeout: 300000,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                   clickable: [
                    '#myDropzone_{{$type}}',
                    '#myDropzone_{{$type}} h6',
                    '#myDropzone_{{$type}} .upload-icons',
                    // '#myDropzone_{{$type}} a'
                ],
                init: function () {
                    this.on("success", function (file, response) {
                        console.log("Uploaded:", response);

                        // Remove file preview from Dropzone
                        this.removeFile(file);

                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('uploadModal_{{$type}}'));
                        modal.hide();
                    });

                    this.on("error", function (file, response) {
                        console.error("Upload failed:", response);
                    });
                },
                sending: function (file, xhr, formData) {
                    formData.append("request_uuid", $('input[name="request_uuid"]').val());
                    formData.append("type", "{{$type}}");
                },
                complete: function(file) {
                    console.log('file_uploaded Successfully', file);
                    $.get('{{ route("showFilesWithType", [ $request->uuid, $type])}}', function(data){
                        console.log("files: ", data);
                        $('#{{$type}}_listing').html(data);
                        $('#uploadModal_{{$type}}').modal('hide');
                    });

                }
            });
        }
    @endforeach

// Use event delegation for dynamically rendered .remove-uploaded-file buttons
$(document).on("click", ".remove-uploaded-file", function () {
    $.ajax({
        url: "/remove-file",
        method: "POST",
        data: {
            file_path: relativePath,
            request_uuid: requestUuid
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.success) {
                $("#uploaded-file-info").hide();
                $("#dropzone-upload-section").show();
                $("#scan_file").val("");
                relativePath = "";
                console.log("File removed from backend as well");
            } else {
                console.error("Error removing file:", response);
            }
        },
        error: function (xhr, status, error) {
            console.error("Error:", error);
        }
    });
});









});




    $(document).ready(function () {
        $('.dropdown-item[data-status]').on('click', function (e) {
            e.preventDefault();

            var selectedStatus = $(this).data('status');
            $('#selectedStatus').val(selectedStatus);
            $('#requestStatusDropdown').text(selectedStatus);
        });
    });

    // quill text-editor for admin 
    if ($('#quill-editor').length) {
        var quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: 'Enter notes here...',
        modules: {
            toolbar: [
            [{ font: [] }],
            [{ header: [1, 2, 3, 4, 5, 6, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ color: [] }, { background: [] }],
            [{ script: 'sub' }, { script: 'super' }],
            [{ list: 'ordered' }, { list: 'bullet' }],
            [{ indent: '-1' }, { indent: '+1' }],
            [{ direction: 'rtl' }],
            [{ align: [] }],
            ['link', 'image', 'video'],
            ['clean']
            ]
        }
        });

        // If you want to load existing content:
        var existingContent = $('#quill-editor').data('content') || '';
        if (existingContent) {
        quill.root.innerHTML = existingContent;
        }

        // On form submit, copy Quill content to hidden input
        $('form').on('submit', function () {
        $('#notes').val(quill.root.innerHTML);
        });
    }

    // quill for users 
    if ($('#quill-editor-user').length) {
        const quill = new Quill('#quill-editor-user', {
            theme: 'snow',
            modules: { toolbar: false },
            readOnly: true,
            placeholder: ''
        });

        // Load existing content
        const existingContent = $('#quill-editor-user').data('content') || '';
        if (existingContent) {
            quill.root.innerHTML = existingContent;
        }
    }

$(function() {
    // Regular chat functionality
    const chatBox = $('#chat-box');
    const requestId = chatBox.data('request-id');
    const chatMessages = $('#chat-messages');
    const chatForm = $('#chat-form');
    const chatInput = $('#chat-message');

    function isUserNearBottom(element) {
        const threshold = 50;
        const scrollTop = element.scrollTop();
        const scrollHeight = element[0].scrollHeight;
        const clientHeight = element.innerHeight();
        return (scrollHeight - (scrollTop + clientHeight)) < threshold;
    }

    function scrollToBottom(element) {
        element.scrollTop(element[0].scrollHeight);
    }

    function loadMessages() {
        const wasNearBottom = isUserNearBottom(chatMessages);
        $.ajax({
            url: '/chat/' + requestId + '/messages',
            method: 'GET',
            success: function(data) {
                chatMessages.html(data.html);
                if (wasNearBottom) {
                    scrollToBottom(chatMessages);
                }
            }
        });
    }

    chatInput.on('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.submit();
        }
    });

    chatForm.on('submit', function(e) {
        e.preventDefault();
        const message = chatInput.val().trim();
        if (!message) return;

        $.ajax({
            url: '/chat/' + requestId + '/send',
            method: 'POST',
            data: {
                message: message,
                type: 'regular',
                _token: '{{ csrf_token() }}'
            },
            success: function() {
                chatInput.val('');
                loadMessages();
            }
        });
    });

    // Admin chat functionality
    @role(['admin', 'sub-admin'])
    const adminChatBox = $('#admin-chat-box');
    const adminChatMessages = $('#admin-chat-messages');
    const adminChatForm = $('#admin-chat-form');
    const adminChatInput = $('#admin-chat-message');

    function loadAdminMessages() {
        const wasNearBottom = isUserNearBottom(adminChatMessages);
        $.ajax({
            url: '/chat/' + requestId + '/admin-messages',
            method: 'GET',
            success: function(data) {
                adminChatMessages.html(data.html);
                if (wasNearBottom) {
                    scrollToBottom(adminChatMessages);
                }
            }
        });
    }

    adminChatInput.on('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            adminChatForm.submit();
        }
    });

    adminChatForm.on('submit', function(e) {
        e.preventDefault();
        const message = adminChatInput.val().trim();
        if (!message) return;

        $.ajax({
            url: '/chat/' + requestId + '/send',
            method: 'POST',
            data: {
                message: message,
                type: 'admin',
                _token: '{{ csrf_token() }}'
            },
            success: function() {
                adminChatInput.val('');
                loadAdminMessages();
            }
        });
    });

    // Load admin messages immediately and set up auto-refresh
    loadAdminMessages();
    setInterval(loadAdminMessages, 5000);
    @endrole

    // Load regular messages immediately and set up auto-refresh
    loadMessages();
    setInterval(loadMessages, 5000);

     $('#editBtn').on('click', function () {
        $('#patientStaticInfo').hide();
        $('#patientEditForm').show();
        //$(this).hide();
    });

    $('#cancelEdit').on('click', function () {
        $('#patientEditForm').hide();
        $('#patientStaticInfo').show();
        $('#editBtn').show();
    });

     $('#editRadiologyBtn').on('click', function () {
        $('#radiologyStaticInfo').hide();
        $('#radiologyEditForm').show();
        //$(this).hide();
    });

    $('#cancelRadiologyEdit').on('click', function () {
        $('#radiologyEditForm').hide();
        $('#radiologyStaticInfo').show();
        $('#editRadiologyBtn').show();
    });

    

     $('#patientEditForm').validate({
        rules: {
            patient_name: { required: true },
            patient_dob: { required: true, date: true },
            patient_phone: { required: true, minlength: 10 },
            patient_email: { required: true, email: true },
            patient_address: { required: true },
            patient_postcode: { required: true }
        },
        submitHandler: function (form) {
            const formData = $(form).serialize();
            const actionUrl = $(form).attr('action');

            $.ajax({
                url: actionUrl,
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                success: function (response) {
                    // Update HTML with new data
                    $('#patientStaticInfo').find('.info-value').each(function () {
                        
                        const field = $(this).attr('data-label');
                        const value = response[field];

                        if (field === 'date_of_birth') {
                            const formatted = new Date(value).toLocaleDateString('en-GB');
                            $(this).text(formatted);
                        } else {
                            $(this).text(value);
                        }
                    });

                    $('#patientEditForm').hide();
                    $('#patientStaticInfo').show();
                },
                error: function (xhr) {
                    alert("Something went wrong! Please check the input or try again.");
                }
            });

            return false;
        }
    });


    

    $('#reportButtondoctor').click(function() {
            $('#statusDoctorNotesModal').fadeIn();
        });

        $('#closeModalDoc').click(function() {
            $('#statusDoctorNotesModal').fadeOut();
        });

        $(window).click(function(event) {
            if ($(event.target).is('#statusDoctorNotesModal')) {
                $('#statusDoctorNotesModal').fadeOut();
            }
        });


        if ($('#quill-editor-doc').length) {
        var quillDoctor = new Quill('#quill-editor-doc', {
        theme: 'snow',
        placeholder: 'Enter notes here...',
        modules: {
            toolbar: [
            [{ font: [] }],
            [{ header: [1, 2, 3, 4, 5, 6, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ color: [] }, { background: [] }],
            [{ script: 'sub' }, { script: 'super' }],
            [{ list: 'ordered' }, { list: 'bullet' }],
            [{ indent: '-1' }, { indent: '+1' }],
            [{ direction: 'rtl' }],
            [{ align: [] }],
            ['link', 'image', 'video'],
            ['clean']
            ]
        }
        });

        // If you want to load existing content:
        var existingContent = $('#quill-editor-doc').data('doctor-content') || '';
        if (existingContent) {
            quillDoctor.root.innerHTML = existingContent;
        }

            // On form submit, copy Quill content to hidden input
        $('form').on('submit', function () {
            $('#doctor_notes').val(quillDoctor.root.innerHTML);
        });
    }


     // Radiology Form Validation & Submission
    $('#radiologyEditForm').validate({
        rules: {
            question: { required: true },
            clinical_history: { required: true },
        },
        submitHandler: function (form) {
            const formData = $(form).serialize();
            const actionUrl = $(form).attr('action');

            $.ajax({
                url: actionUrl,
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                success: function (response) {
                    // Update Static HTML
                    $('#radiologyStaticInfo .info-value').each(function () {
                        const field = $(this).attr('data-label');
                        const value = response[field];
                         $(this).text(value);
                    });

                    $('#radiologyEditForm').hide();
                    $('#radiologyStaticInfo').show();
                },
                error: function (xhr) {
                    alert("Update failed. Please check the input.");
                }
            });

            return false;
        }
    });


});

</script>

@endpush

@endsection
