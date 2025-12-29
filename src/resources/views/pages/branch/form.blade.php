@extends('layouts.master')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <form action="{{ url('branch/store') }}" method="post">
            @csrf
            <div class="row">

                <div class="card ">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tambah Branch</h5>
                        <small class="text-body-secondary float-end">Tambah Branch</small>
                    </div>

                    <div class="card-body p-4">

                        <!-- 🔐 USER LOGIN -->
                        <h6 class="fw-bold mb-3 text-primary">User Login</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label mb-3">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Masukkan username">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label mb-3">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Masukkan password">
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- 🏢 Branch -->
                        <!-- 🏢 Branch -->
                        <h6 class="fw-bold mb-3 text-primary">Branch Information</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label mb-3">
                                    Name <span class="badge bg-danger">required</span>
                                </label>
                                <input type="text" name="branch_name" class="form-control"
                                    placeholder="example Branch Client Satu - Jakarta or Branch Client Satu - Bandung"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label mb-3">
                                    Code <span class="badge bg-danger">required</span>
                                </label>
                                <input type="text" name="branch_code" class="form-control"
                                    placeholder="example CBG1 or CBG2" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label mb-3">
                                    Longitude <span class="badge bg-danger">required</span>
                                </label>
                                <input type="text" name="longitude" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label mb-3">
                                    Latitude <span class="badge bg-danger">required</span>
                                </label>
                                <input type="text" name="latitude" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label mb-3">
                                    Radius <span class="badge bg-danger">required</span>
                                </label>
                                <input type="number" name="radius" class="form-control" placeholder="example 50 or 100"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label mb-3">
                                    Location <span class="badge bg-danger">required</span>
                                </label>
                                <input type="text" name="location" class="form-control"
                                    placeholder="example Jakarta or Bandung" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label mb-3">
                                    Building <span class="badge bg-danger">required</span>
                                </label>
                                <input type="text" name="building" class="form-control" placeholder="example Head Office"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label mb-3">
                                    Timezone <span class="badge bg-danger">required</span>
                                </label>
                                <select name="timezone" class="form-select" required>
                                    <option value="">Choose</option>
                                    <option value="1">Asia/Jakarta (WIB)</option>
                                    <option value="2">Asia/Makassar (WITA)</option>
                                    <option value="3">Asia/Jayapura (WIT)</option>
                                </select>
                            </div>
                        </div>


                        <div class="card-footer  text-end rounded-bottom-4">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                            <button class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Data
                            </button>
                        </div>

                    </div>

                </div>
        </form>

    </div>
    </div>
@endsection
