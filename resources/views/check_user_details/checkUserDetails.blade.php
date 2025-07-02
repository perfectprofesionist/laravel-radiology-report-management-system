@extends('layouts.app')

@section("breadcrumb")
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">Activate</li>
@endsection

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">User Details</h5>
                    </div>


                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped border custSetFontTbl">
                                <tbody>
                                    <tr>
                                        <th class="bg-light w-25">Username</th>
                                        <td class="text-muted">{{ $user->username }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Email</th>
                                        <td class="text-muted">{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Mobile Number</th>
                                        <td class="text-muted">{{ $user->mobile_number }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Dentist Name</th>
                                        <td class="text-muted">{{ $user->dentist_name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Practice Name</th>
                                        <td class="text-muted">{{ $user->practice_name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Practice Address</th>
                                        <td class="text-muted">{{ $user->practice_address }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">City</th>
                                        <td class="text-muted">{{ $user->city }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">State</th>
                                        <td class="text-muted">{{ $user->state }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Postcode</th>
                                        <td class="text-muted">{{ $user->post_code }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Routine Phone</th>
                                        <td class="text-muted">{{ $user->routine_phone }}</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Urgent Phone</th>
                                        <td class="text-muted">{{ $user->urgent_phone }}</td>
                                    </tr>
                                 
                                    <tr>
                                        <th class="bg-light">Created At</th>
                                        <td class="text-muted">{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                    {{--<tr>
                                        <th class="bg-light">Updated At</th>
                                        <td class="text-muted">{{ $user->updated_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>--}}
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 text-end">
                            @if( !$user->is_active)
                            <a href="{{ route('admin.activate_user', $user->uuid) }}" class="btn btn-primary">
                                {{ $user->is_active ? 'Activate' : 'Activate' }}
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
