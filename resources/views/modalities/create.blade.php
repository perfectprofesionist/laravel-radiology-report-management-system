@extends('layouts.app')

<style>

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
    <div class="card">
        <div class="card-body">
            <div class="card-header mb-4">
                <span>Create New Modality</span>
            </div>
            <form action="{{ route('modalities.store') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <label for="name" class="col-md-2 col-form-label">Name:</label>
                    <div class="col-md-10">
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter modality name" value="{{ old('name') }}">
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="price" class="col-md-2 col-form-label">Price:</label>
                    <div class="col-md-10">
                        <input type="text" class="form-control" id="price" name="price" placeholder="Enter price" value="{{ old('price') }}">
                        @error('price')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-10 offset-md-2">
                        <button type="submit" class="btn btn-primary mt-0" id="submit-btn">Create</button>
                        <a href="{{ route('modalities.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
