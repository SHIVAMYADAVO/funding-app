@extends('layout.master')

@section('title', 'Edit Home Banner')

@section('content')
    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Edit Banner</h3>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Edit Banner</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <form action="{{ route('banners.update', $Banner->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label>Current Image</label><br>
                                        <img src="{{ asset('uploads/banners/' . $Banner->image) }}" width="100"><br><br>
                                        <label>New Image</label>
                                        <input type="file" name="image" class="form-control">
                                        @error('image')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-success">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
