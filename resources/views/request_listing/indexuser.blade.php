@extends('layouts.app')

@section('content')

{{-- User-specific examination list interface for viewing and managing personal radiology requests --}}
<div class="right-body-con">
    <div class="right-body-con-inn">
        <div class="deshboard-table">
            {{-- Header controls section with create button, status filter and search --}}
            <div class="header-controls">
                {{-- Page title and description --}}
                <div class="exam-table-list">
                    <h4>Examination List</h4>
                </div>
                
                {{-- Action buttons, status filter dropdown and search functionality --}}
                <div class="dropedown-table-sec">
                    {{-- Create new task button for users to submit new requests --}}
                    <a class="btn btn-primary btn-sm" href="{{ route('scan.upload')}}">Create New Task</a>
                    
                    {{-- Status filter dropdown for filtering requests by status --}}
                    <label for="examStatus"><strong>Select Exam Status</strong></label>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" id="requestStatusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            All
                        </button>
                        <ul class="dropdown-menu menu-status" aria-labelledby="requestStatusDropdown">
                            <li><a class="dropdown-item" href="#" data-status="All">All</a></li>
                            <li><a class="dropdown-item" href="#" data-status="Pending">Pending</a></li>
                            <li><a class="dropdown-item" href="#" data-status="Assigned">Assigned</a></li>
                            <li><a class="dropdown-item" href="#" data-status="Incident">Incident</a></li>
                            <li><a class="dropdown-item" href="#"  data-status="Completed">Completed</a></li>
                        </ul>
                    </div>
                    
                    {{-- Search input for filtering by patient name or referrer --}}
                    <input id="requestSearch" type="search" placeholder="Search Patient or Referrer" />
                </div>
            </div>
            
            {{-- DataTable container for displaying user's examination requests --}}
            <div class="des-table">
                <table class="data-table1">
                    {{-- Table header with column definitions --}}
                    <thead>
                    <tr>
                        <th>Exam Id</th>
                        <th>Modality</th>
                        <th>Patient Name</th>
                        <th>Upload Date</th>
                        <th>Appointment</th>
                        <th>Referrer</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    {{-- Table body - populated dynamically by DataTables --}}
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')

<script>
    $(function() {
        {{-- Initialize variables for status filtering and DataTable --}}
        var selectedStatus = 'All'; 
        
        {{-- Initialize DataTable with server-side processing for user-specific data --}}
        var table = $('.data-table1').DataTable({
            processing: true, // Show processing indicator
            serverSide: true, // Enable server-side processing for large datasets
            retrieve: true,   // Allow table to be retrieved if already initialized
            searching: false, // Disable built-in search as we have custom search
            ajax: {
                url: "{{ route('request-listing.indexuser') }}",
                type: 'GET',
                data: function(d) {
                    d.status = selectedStatus;          // Send selected status filter
                    d.searchValue = $('#requestSearch').val(); // Send search input value
                },
                error: function(xhr, error, thrown) {
                    // Error handling for AJAX requests
                    console.error("AJAX Error:", {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        errorThrown: thrown
                    });
                },
                dataSrc: function (json) {
                    // Log server response for debugging
                    console.log("Server Data Response:", json);
                    return json.data;
                }
            },
            
            {{-- Column definitions for the DataTable --}}
            columns: [
                {
                    data: 'exam_id',
                    name: 'exam_id',
                    orderable: true,
                },
                {
                    data: 'modality',
                    name: 'modality',
                    orderable: true,
                },
                {
                    data: 'patient_name',
                    name: 'patient_name',
                    orderable: true,
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    orderable: true,
                    render: function(data, type, row) {
                        // Format upload date for display
                        if (!data) return '';
                        let dateObj = new Date(data);
                        return dateObj.toLocaleDateString('en-GB', {
                            day: 'numeric', month: 'long', year: 'numeric'
                        }) + ' ' + dateObj.toLocaleTimeString('en-US', {
                            hour: '2-digit', minute: '2-digit', hour12: true
                        });
                    }
                },
                {
                    data: 'appointment',
                    name: 'appointment',
                    orderable: true,
                    render: function(data, type, row) {
                        // Format appointment date for display
                        if (!data) return '';
                        let dateObj = new Date(data);
                        let dateStr = dateObj.toLocaleDateString('en-GB', {
                            day: 'numeric', month: 'long', year: 'numeric'
                        });
                        return dateStr;
                    }
                },
                {
                    data: 'user.username',
                    name: 'username',
                    orderable: false
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false
                },
                {
                    data: 'payment_status',
                    name: 'payment_status',
                    orderable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        {{-- Handle status filter dropdown clicks --}}
        $('.menu-status a.dropdown-item').on('click', function(e) {
            e.preventDefault();
            selectedStatus = $(this).data('status');
            $('#requestStatusDropdown').text($(this).text());
            table.ajax.reload(); // Reload table with new filter
        });

        {{-- Handle search input with debouncing for better performance --}}
        let typingTimer;
        $('#requestSearch').on('keyup', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function() {
                table.ajax.reload(); // Reload table after user stops typing
            }, 500); // 500ms delay after typing stops
        });

    });
</script>

@endpush

@endsection

