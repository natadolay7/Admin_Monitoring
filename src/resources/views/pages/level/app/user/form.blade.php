@extends('layouts.master')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <form
            action="{{ isset($data) ? url('/v1/management-users/update/' . $data->id) : url('/v1/management-users/store/') }}"
            method="post">
            @csrf
            <div class="row">

                <div class="card ">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ isset($data) ? 'Edit TAD' : 'Tambah TAD' }}</h5>
                        <small class="text-body-secondary float-end">{{ isset($data) ? 'Edit TAD' : 'Tambah TAD' }}</small>
                    </div>

                    <div class="card-body p-4">

                        <!-- 🔐 USER LOGIN -->
                        <h6 class="fw-bold mb-3 text-primary">User Login</h6>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-6">
                                <label class="form-label">Username</label>
                                <input type="hidden" id="old_company" value="{{ $data2->company_id ?? '' }}">
                                <input type="hidden" id="old_branch" value="{{ $data2->id ?? '' }}">
                                <input type="text" name="username" class="form-control"
                                    value="{{ old('username', $data->email ?? '') }}" placeholder="Masukkan username">
                            </div>
                            <div class="col-md-6 mb-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" id="passwordInput"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="{{ isset($data) ? 'Kosongkan jika tidak diubah' : 'Masukkan password' }}"
                                    {{ !isset($data) ? 'required' : '' }}>
                            </div>
                            <div class="col-md-6 mb-6">
                                <label class="form-label">Nama </label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $data->name ?? '') }}" placeholder="Masukkan nama lengkap">
                            </div>
                            <div class="col-md-6 mb-6">
                                <label class="form-label">Company </label>
                                <select id="company" class="form-control">
                                    <option value="">-- Pilih Company --</option>
                                </select>

                            </div>
                            <div class="col-md-6 mb-6">
                                <label class="form-label">Branch </label>
                                <select id="branch" class="form-control" name="branch_id">
                                    <option value="">-- Pilih Branch --</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- 🏢 COMPANY -->


                    </div>

                    <div class="card-footer  text-end rounded-bottom-4">
                        {{-- <button class="btn btn-secondary me-2">Cancel</button> --}}
                        <button class="btn btn-primary">
                            <i class="bi bi-save"></i> {{ isset($data) ? 'Update TAD' : 'Tambah TAD' }}
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
    <script>
        $(document).ready(function() {

            let oldCompany = $('#old_company').val();
            let oldBranch = $('#old_branch').val();

            // Load Company
            $.ajax({
                url: "/v1/get-company",
                type: "GET",
                success: function(data) {
                    $('#company').empty().append('<option value="">-- Pilih Company --</option>');

                    $.each(data, function(key, value) {
                        let selected = (value.id == oldCompany) ? 'selected' : '';
                        $('#company').append(
                            `<option value="${value.id}" ${selected}>${value.name}</option>`
                        );
                    });

                    // Kalau edit → langsung load branch
                    if (oldCompany) {
                        loadBranch(oldCompany, oldBranch);
                    }
                }
            });

            // Saat Company berubah
            $('#company').on('change', function() {
                let company_id = $(this).val();
                loadBranch(company_id, null);
            });

            function loadBranch(company_id, selectedBranch) {
                if (company_id) {
                    $.ajax({
                        url: "/v1/get-branch/" + company_id,
                        type: "GET",
                        success: function(data) {
                            $('#branch').empty().append('<option value="">-- Pilih Branch --</option>');

                            $.each(data, function(key, value) {
                                let selected = (value.id == selectedBranch) ? 'selected' : '';
                                $('#branch').append(
                                    `<option value="${value.id}" ${selected}>${value.name}</option>`
                                );
                            });
                        }
                    });
                } else {
                    $('#branch').html('<option value="">-- Pilih Branch --</option>');
                }
            }

        });
    </script>
@endsection
