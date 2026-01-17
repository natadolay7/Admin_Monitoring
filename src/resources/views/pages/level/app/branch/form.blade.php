@extends('layouts.master')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <form action="{{ isset($data) ? url('/v1/branch/update/' . $data->id) : url('v1/branch/store') }}" method="post">
            @csrf
            <div class="row">

                <div class="card ">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ isset($data) ? 'Edit BRANCH' : 'Tambah BRANCH' }}</h5>
                        <small
                            class="text-body-secondary float-end">{{ isset($data) ? 'Edit BRANCH' : 'Tambah BRANCH' }}</small>
                    </div>

                    <div class="card-body p-4">

                        <!-- 🔐 USER LOGIN -->
                        <h6 class="fw-bold mb-3 text-primary">User Login</h6>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label mb-3">Username</label>
                                <input type="hidden" id="old_company" value="{{ $data->company_id ?? '' }}">

                                <input type="text" name="username" class="form-control"
                                    value="{{ old('username', $data2->email ?? '') }}" placeholder="Masukkan username">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label mb-3">Password</label>
                                <input type="password" name="password" id="passwordInput"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="{{ isset($data) ? 'Kosongkan jika tidak diubah' : 'Masukkan password' }}"
                                    {{ !isset($data) ? 'required' : '' }}>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- 🏢 Branch -->
                        <!-- 🏢 Branch -->
                        <h6 class="fw-bold mb-3 text-primary">Branch Information</h6>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-6">
                                <label class="form-label">Company </label>
                                <select id="company" class="form-control" name="company">
                                    <option value="">-- Pilih Company --</option>
                                </select>

                            </div>
                            <div class="col-md-6">
                                <label class="form-label mb-3">
                                    Name <span class="badge bg-danger">required</span>
                                </label>
                                <input type="text" name="branch_name" class="form-control"
                                    value="{{ old('name', $data->name ?? '') }}"
                                    placeholder="example Branch Client Satu - Jakarta or Branch Client Satu - Bandung"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label mb-3">
                                    Code <span class="badge bg-danger">required</span>
                                </label>
                                <input type="text" name="branch_code" value="{{ old('code', $data->code ?? '') }}"
                                    class="form-control" placeholder="example CBG1 or CBG2" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label mb-3">
                                    Longitude <span class="badge bg-danger">required</span>
                                </label>
                                <input type="text" name="longitude" class="form-control"
                                    value="{{ old('longitude', $data->longitude ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label mb-3">
                                    Latitude <span class="badge bg-danger">required</span>
                                </label>
                                <input type="text" name="latitude" class="form-control"
                                    value="{{ old('latitude', $data->latitude ?? '') }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label mb-3">
                                    Radius <span class="badge bg-danger">required</span>
                                </label>
                                <input type="number" name="radius" value="{{ old('radius', $data->radius ?? '') }}"
                                    class="form-control" placeholder="example 50 or 100" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label mb-3">
                                    Location <span class="badge bg-danger">required</span>
                                </label>
                                <input type="text" name="location" value="{{ old('location', $data->location ?? '') }}"
                                    class="form-control" placeholder="example Jakarta or Bandung" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label mb-3">
                                    Building <span class="badge bg-danger">required</span>
                                </label>
                                <input type="text" name="building" value="{{ old('building', $data->building ?? '') }}"
                                    class="form-control" placeholder="example Head Office" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label mb-3">
                                    Timezone <span class="badge bg-danger">required</span>
                                </label>
                                <select name="timezone" class="form-select" required>
                                    <option value="">Choose</option>

                                    <option value="1"
                                        {{ old('timezone', $data->timezone ?? '') == 1 ? 'selected' : '' }}>
                                        Asia/Jakarta (WIB)
                                    </option>

                                    <option value="2"
                                        {{ old('timezone', $data->timezone ?? '') == 2 ? 'selected' : '' }}>
                                        Asia/Makassar (WITA)
                                    </option>

                                    <option value="3"
                                        {{ old('timezone', $data->timezone ?? '') == 3 ? 'selected' : '' }}>
                                        Asia/Jayapura (WIT)
                                    </option>
                                </select>
                            </div>
                        </div>


                        <div class="card-footer  text-end rounded-bottom-4">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                            <button class="btn btn-primary">
                                <i class="bi bi-save"></i>{{ isset($data) ? 'UPDATE BRANCH' : 'Tambah BRANCH' }}
                            </button>
                        </div>

                    </div>

                </div>
        </form>

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
