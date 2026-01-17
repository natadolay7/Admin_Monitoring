@extends('layouts.master')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <form action=" {{ isset($data) ? url('/v1/company/update/' . $data->id) : url('/v1/company/store/') }}"
            method="post">
            @csrf
            <div class="row">

                <div class="card ">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ isset($data) ? 'Edit COMPANY' : 'Tambah COMPANY' }}</h5>
                        <small
                            class="text-body-secondary float-end">{{ isset($data) ? 'Edit COMPANY' : 'Tambah COMPANY' }}</small>
                    </div>

                    <div class="card-body p-4">

                        <!-- 🔐 USER LOGIN -->
                        <h6 class="fw-bold mb-3 text-primary">User Login</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Masukkan username"
                                    value="{{ old('username', $data->username ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" id="passwordInput"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="{{ isset($data) ? 'Kosongkan jika tidak diubah' : 'Masukkan password' }}"
                                    {{ !isset($data) ? 'required' : '' }}>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- 🏢 COMPANY -->
                        <h6 class="fw-bold mb-3 text-primary">Company Information</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Company</label>
                                <input type="text" name="company_name" class="form-control" placeholder="Nama perusahaan"
                                    value="{{ old('name', $data->name ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Code Company</label>
                                <input type="text" name="company_code" class="form-control" placeholder="Kode perusahaan"
                                    value="{{ old('code', $data->code ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Email Company</label>
                                <input type="email" name="company_email" class="form-control"
                                    placeholder="email@company.com" value="{{ old('email', $data->email ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact</label>
                                <input type="text" name="company_contact" class="form-control" placeholder="08xxxxxxxx"
                                    value="{{ old('contact', $data->contact ?? '') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="company_address" rows="3" class="form-control" placeholder="Alamat lengkap perusahaan">{{ old('address', $data->address ?? '') }}</textarea>
                        </div>

                    </div>

                    <div class="card-footer  text-end rounded-bottom-4">
                        {{-- <button class="btn btn-secondary me-2">Cancel</button> --}}
                        <button class="btn btn-primary">
                            <i class="bi bi-save"></i> {{ isset($data) ? 'UPDATE COMPANY' : 'Tambah COMPANY' }}
                        </button>
                    </div>

                </div>

            </div>
        </form>

    </div>
@endsection
@section('script')
    @include('layouts.component.toast')
@endsection
