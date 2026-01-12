@extends('layouts.master')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <form action="{{ url('management-users/update') }}" method="post">
            @csrf
            <div class="row">

                <div class="card ">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Update TAD</h5>
                        <small class="text-body-secondary float-end">Update TAD</small>
                    </div>

                    <div class="card-body p-4">

                        <!-- 🔐 USER LOGIN -->
                        <h6 class="fw-bold mb-3 text-primary">User Login</h6>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-6">
                                <label class="form-label">Username</label>
                                <input type="hidden" value="{{ $data->id }}" name="id">
                                <input type="text" name="username" value="{{ $data->email }}" class="form-control"
                                    placeholder="Masukkan username">
                            </div>
                            <div class="col-md-6 mb-6">
                                <label class="form-label">Password</label>
                                <small style="font-size: 10px" class="text-danger">*Kosongkan bila tidak ada
                                    perubahan</small>
                                <input type="password" name="password" class="form-control" placeholder="Masukkan password">
                            </div>
                            <div class="col-md-6 mb-6">
                                <label class="form-label">Nama </label>
                                <input type="text" name="name" value="{{ $data->name }}" class="form-control"
                                    placeholder="Nama ">
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- 🏢 COMPANY -->


                    </div>

                    <div class="card-footer  text-end rounded-bottom-4">
                        {{-- <button class="btn btn-secondary me-2">Cancel</button> --}}
                        <button class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Data
                        </button>
                    </div>

                </div>

            </div>
        </form>

    </div>
    </div>
@endsection
@section('script')
    @include('layouts.component.toast')
@endsection
