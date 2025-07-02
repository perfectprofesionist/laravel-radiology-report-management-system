@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Modality Details</h4>
            <a href="{{ route('modalities.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <label class="col-md-2 fw-bold">ID:</label>
                <div class="col-md-10">
                    {{ $modality->id }}
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-md-2 fw-bold">Name:</label>
                <div class="col-md-10">
                    {{ $modality->name }}
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-md-2 fw-bold">Price:</label>
                <div class="col-md-10">
                    {{ $modality->price }}
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-md-2 fw-bold">Created At:</label>
                <div class="col-md-10">
                    {{ $modality->created_at }}
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-md-2 fw-bold">Updated At:</label>
                <div class="col-md-10">
                    {{ $modality->updated_at }}
                </div>
            </div>
            <div class="row">
                <div class="col-md-10 offset-md-2">
                    <a href="{{ route('modalities.edit', $modality->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('modalities.destroy', $modality->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this modality?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
