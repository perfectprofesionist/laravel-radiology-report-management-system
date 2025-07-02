@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('cards') }}">Cards</a></li>
    <li class="breadcrumb-item active">Add Card</li>
@endsection

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header">Add New Card</div>
        <div class="card-body">

            {{-- @if(session('success'))
                <small class="text-danger d-block mt-2">{{ session('success') }}</small>
            @endif
        
            @if ($errors->has('card_error'))
                <div class="alert alert-danger">
                    {{ $errors->first('card_error') }}
                </div>
            @endif --}}


            <form id="card-form" method="POST" action="{{ route('cards.store') }}">
                @csrf

                <div class="form-group mb-3">
                    <label for="card-element">Credit or debit card</label>
                    <div id="card-element" class="form-control">
                        <!-- Stripe Elements will be inserted here -->
                    </div>
                </div>

                <input type="hidden" name="payment_method" id="payment_method" />

                @if ($errors->has('card_error'))    
                    <small class="text-danger d-block mt-1">
                        {{ $errors->first('card_error') }}
                    </small>
                @endif
                <div id="card-message" class="mb-3"></div>
                <button id="card-button" class="btn btn-primary">
                    <i class="fa fa-plus-circle"></i> Add Card
                </button>

                
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>

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
<script>
    $(function() {
        const stripe = Stripe('{{ $stripeKey }}');
        const elements = stripe.elements();
        const cardElement = elements.create('card');
        cardElement.mount('#card-element');

        $('#card-form').on('submit', async function(e) {
            e.preventDefault();

            const $button = $('#card-button');
            const $message = $('#card-message');
            $message.html('');
            $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Adding...');

            const { paymentMethod, error } = await stripe.createPaymentMethod('card', cardElement);

            if (error) {
                $message.html(`<small class="text-danger d-block mt-1">${error.message}</small>`);
                $button.prop('disabled', false).html('<i class="fa fa-plus-circle"></i> Add Card');
            } else {
                // AJAX request
                $.ajax({
                    url: "{{ route('cards.store') }}",
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        payment_method: paymentMethod.id
                    },
                    success: function(response) {
                        $message.html(`<small class="text-success fw-bold d-block mt-2">${response.message}</small>`);
                        $button.prop('disabled', false).html('<i class="fa fa-check-circle"></i> Done');
                        setTimeout(() => {
                            window.location.href = response.redirect; // Redirect to card list
                        }, 1500);
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message ?? 'Something went wrong.';
                        $message.html(`<small class="text-danger d-block mt-2">${msg}</small>`);
                        $button.prop('disabled', false).html('<i class="fa fa-plus-circle"></i> Add Card');
                    }
                });
            }
        });
    });
</script>

@endpush

