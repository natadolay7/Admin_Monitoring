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
        <form action="{{ url('company/store') }}" method="post">
            @csrf
            <div class="row">

                <div class="card ">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tambah Company</h5>
                        <small class="text-body-secondary float-end">Tambah Company</small>
                    </div>

                    <div class="card-body p-4">

                        <!-- 🔐 USER LOGIN -->
                        <h6 class="fw-bold mb-3 text-primary">User Login</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Masukkan username">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Masukkan password">
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- 🏢 COMPANY -->
                        <h6 class="fw-bold mb-3 text-primary">Company Information</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Company</label>
                                <input type="text" name="company_name" class="form-control"
                                    placeholder="Nama perusahaan">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Code Company</label>
                                <input type="text" name="company_code" class="form-control"
                                    placeholder="Kode perusahaan">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Email Company</label>
                                <input type="email" name="company_email" class="form-control"
                                    placeholder="email@company.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact</label>
                                <input type="text" name="company_contact" class="form-control" placeholder="08xxxxxxxx">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="company_address" rows="3" class="form-control" placeholder="Alamat lengkap perusahaan"></textarea>
                        </div>

                    </div>

                    <div class="card-footer  text-end rounded-bottom-4">
                        {{-- <button class="btn btn-secondary me-2">Cancel</button> --}}
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
