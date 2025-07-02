@extends('layouts.app')

@section("breadcrumb")
    <li class="breadcrumb-item active" aria-current="page">Users</li>
@endsection
<style>
.modal-wrap input[type="search"]{
    max-width: 100%;
    padding: 9px 43px !important;
    background-image: url(../images/search-icon-b.svg);
    width: 308px;
    background-repeat: no-repeat;
    background-position: left 16px center;
    background-color: #fff;
    border-radius: 4px;
    font-size: 14px;
    border: 1px solid #00000033;
}

.modal-wrap .dataTables_length select {
    font-weight: 400;
    font-size: 12px;
    line-height: 100%;
    color: #1E1E1E;
    width: 168px;
}
.modal-wrap .card-header span {
    font-size: 18px;
    font-weight: 600;
    font-size: 18px;
    line-height: 100%;
    color: #1E1E1E;
    margin: 0;
}
</style>

@section('content')
<div class="">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card modal-wrap">
                <div class="card-body">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Users Management</span>
                            {{-- <a href="{{ route('users.create') }}" class="btn btn-primary">Create New User</a> --}}
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">Create New User</a>
                                <input type="search" id="customSearch" placeholder="Search Username, Email or Role">
                            </div>
                        </div>
                    <div style="overflow-x: auto;">
                        <table class="table nowrap" id="users-table"  style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Avatar</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Dentist Name</th>
                                    <th>Practice Name</th>
                                    <th>Mobile Number</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {

    const secureAvatarRouteBase = "{{ route('user.avatar', ['filename' => '__FILENAME__']) }}";
    const fallbackAvatar = "{{ asset('images/default-doc-profile.jpg') }}";

    $('#users-table').DataTable({
        processing: true,
        scrollX: true,
        serverSide: true,
        searching: false,
        order: [], 
        // ajax: '{{ route("users.index") }}',
        ajax: {
            url: '{{ route("users.index") }}',
            data: function(d) {
                d.search_custom = $('#customSearch').val(); // send custom input
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {
                data: 'avatar',
                name: 'avatar',
                orderable: false,
                render: function(data) {
                    console.log(data);
                    if (data) {
                        const avatarUrl = secureAvatarRouteBase.replace('__FILENAME__', data);
                        return `<img src="${avatarUrl}" alt="Avatar" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">`;
                    } else {
                        return `<img src="${fallbackAvatar}" alt="Default Profile" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">`;
                    }
                }
            },
            {data: 'username', name: 'username'},
            {data: 'email', name: 'email'},
            {data: 'dentist_name', name: 'dentist_name'},
            {data: 'practice_name', name: 'practice_name'},
            {data: 'mobile_number', name: 'mobile_number'},
            {data: 'roles', name: 'roles'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });

    $('#customSearch').on('input', function() {
        $('#users-table').DataTable().ajax.reload();
    });

    $(document).on('click', '.delete-user', function () {
        const userId = $(this).data('id');
        const username = $(this).data('username');

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete the user "${username}". This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/users/${userId}`, // RESTful DELETE route
                    type: 'POST',            // Laravel accepts DELETE via POST + _method
                    data: {
                        _method: 'DELETE',  // Spoof the DELETE method
                        _token: $('meta[name="csrf-token"]').attr('content') // Use CSRF from meta tag
                    },
                    success: function (response) {
                        Swal.fire('Deleted!', `User "${username}" has been deleted.`, 'success');
                        $('#users-table').DataTable().ajax.reload(null, false); 
                        // refresh table without resetting pagination
                    },
                    error: function () {
                        Swal.fire('Error!', 'Something went wrong while deleting the user.', 'error');
                    }
                });
            }
        });
    });


});
</script>
@endpush
@endsection
