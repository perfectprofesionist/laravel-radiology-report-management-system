@extends('layouts.app')

@section("breadcrumb")
    <!-- <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li> -->
    <li class="breadcrumb-item active" aria-current="page">Cards</li>
@endsection

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
                <li>
                    <img src="{{ asset('images/tel-inc.png') }}" />
                    <a href="tel:{{ $user->mobile_number }}">{{ $user->mobile_number }}</a>
                </li>
                @role('user')
                    <li>
                        <img src="{{ asset('images/setting-icon.png') }}" />
                        <a href="{{ route('cards') }}">My Cards</a>
                    </li>
                @endrole
                </ul>
            </div>
            </div>

            <div class="upload-files-row2-right">

                {{-- Display success message from session if success --}}
                <div id="card-message">
                    @if(session('success'))
                        <script>
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: '{{ session('success') }}',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        </script>
                    @endif

           

               
                    <div class="patient-information-row">
                            <div class="add-card-box p-3">
                                <a href="{{ route('cards.add') }}" class="add-card-link">
                                    <i class="fa fa-plus-circle"></i> Add New Card
                                </a>
                            </div>
                      
                            <div class="cards-container pb-3">
                                @if(count($cards) > 0)
                                    @foreach ($cards as $card)
                                        <div class="card-box">
                                            <div class="card-details">
                                                <div class="card-brand">
                                                    <strong>{{ ucfirst($card->card->brand) }}</strong>
                                                    <span>**** **** **** {{ $card->card->last4 }}</span>
                                                </div>
                                                <div class="card-expiry">
                                                    <span>Expires: {{ $card->card->exp_month }}/{{ $card->card->exp_year }}</span>
                                                </div>
                                            </div>

                                            <!-- <form action="{{ route('cards.delete', $card->id) }}" method="POST" class="delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="delete-btn" title="Delete card" data-form-id="form-{{ $card->id }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form> -->
                                            <button type="button" class="delete-btn swal-confirm-delete" data-form-id="form-{{ $card->id }}" title="Delete card">
                                                <i class="fa fa-trash"></i>
                                            </button>

                                            <form id="form-{{ $card->id }}" action="{{ route('cards.delete', $card->id) }}" method="POST" class="delete-form d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>

                                        </div>
                                    @endforeach
                                @else
                                    <div class="card-box">
                                        <div class="card-details">
                                            <div class="card-expiry">
                                                <span>No cards saved.</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                    </div>
                
            </div>
        </div>
        </div>
    </div>
</div>


@push('scripts')

<script>

    $(document).on('click', '.swal-confirm-delete', function(e) {
        const formId = $(this).data('form-id');

        Swal.fire({
            title: 'Are you sure?',
            text: "This card will be permanently deleted.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    });
</script>


@endpush
@endsection
