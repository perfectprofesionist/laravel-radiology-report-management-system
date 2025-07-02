@extends('layouts.app')
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
                            <span>{{ __('Modalities Management') }}</span>
                            {{-- <a href="{{ route('modalities.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add New Modality
                            </a> --}}
                             <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('modalities.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add New Modality
                                </a>
                                <input type="search" id="customSearch" placeholder="Search Name or Price">
                            </div>
                        </div>
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table  data-table" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th class="w-25">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="modalities-table-body">


                                </tbody>
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

        $(document).on('click', '.delete-modality', function () {
            const id = $(this).data('id');
            const name = $(this).data('name');

            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete "${name}". This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/modalities/${id}`,
                        method: 'POST', // Method spoofing
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            _method: 'DELETE'
                        },
                        success: function () {
                            Swal.fire('Deleted!', `"${name}" has been deleted.`, 'success');
                            table.ajax.reload(); // Reload DataTable
                        },
                        error: function () {
                            Swal.fire('Error!', 'Something went wrong while deleting.', 'error');
                        }
                    });
                }
            });
        });


        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            /*ordering: false,*/
            // iDisplayLength: 25,
            retrieve: true,
            // ajax: "{{ route('modalities.index') }}",
            ajax: {
                url: '{{ route("modalities.index") }}',
                data: function(d) {
                    d.search_custom = $('#customSearch').val(); // send custom input
                }
            },
            columns: [{
                data: 'name',
                name: 'name'

            }, {
                data: 'price',
                name: 'price'

            }, {
                data: 'action',
                name: 'action',
                orderable: false,
            }, ]
        });

        $('#customSearch').on('input', function() {
            table.ajax.reload();
        });

    });
</script>

@endpush
@endsection
