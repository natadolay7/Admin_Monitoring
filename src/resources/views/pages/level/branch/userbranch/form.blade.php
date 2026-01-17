@extends('layouts.master')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endsection

@section('content')
    <div class="container-xxl py-4">

        <form action="{{ isset($data) ? url('/core/users/update/' . $data->id) : url('/core/users/store/') }}" method="post">
            @csrf

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="bi bi-person-{{ isset($data) ? 'gear' : 'plus' }} me-2"></i>
                            {{ isset($data) ? 'Edit User' : 'Tambah User Baru' }}
                        </h5>
                        <small class="text-muted">
                            {{ isset($data) ? 'Perbarui informasi pengguna' : 'Tambahkan pengguna baru ke sistem' }}
                        </small>
                    </div>

                    <span class="badge bg-{{ isset($data) ? 'warning' : 'success' }}">
                        {{ isset($data) ? 'Edit Mode' : 'Tambah Mode' }}
                    </span>
                </div>

                <div class="card-body">
                    <h6 class="mb-3">
                        <i class="bi bi-key me-2"></i>Informasi Login
                    </h6>

                    <div class="row">

                        <!-- Username -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Username <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="text" name="username"
                                    class="form-control @error('username') is-invalid @enderror"
                                    value="{{ old('username', $data->email ?? '') }}" placeholder="Masukkan username">
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Password
                                @if (!isset($data))
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-key"></i>
                                </span>
                                <input type="password" name="password" id="passwordInput"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="{{ isset($data) ? 'Kosongkan jika tidak diubah' : 'Masukkan password' }}"
                                    {{ !isset($data) ? 'required' : '' }}>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">
                                {{ isset($data) ? 'Kosongkan jika tidak ingin mengubah password' : 'Minimal 8 karakter' }}
                            </small>
                        </div>

                        <!-- Nama -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $data->name ?? '') }}" placeholder="Masukkan nama lengkap">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Role <span class="text-danger">*</span>
                            </label>
                            <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Role --</option>
                                @foreach ($data2 as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('role_id') == $item->id || (isset($data) && $data->role_id == $item->id) ? 'selected' : '' }}>
                                        {{ $item->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ url('/core/users') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-{{ isset($data) ? 'check-circle' : 'plus-circle' }} me-1"></i>
                        {{ isset($data) ? 'Update User' : 'Simpan User' }}
                    </button>
                </div>

            </div>
        </form>
    </div>
@endsection

@section('script')
    @include('layouts.component.toast')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Toggle password visibility
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('passwordInput');
            const toggleIcon = document.getElementById('toggleIcon');

            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    // Toggle icon
                    if (type === 'text') {
                        toggleIcon.classList.remove('bi-eye');
                        toggleIcon.classList.add('bi-eye-slash');
                    } else {
                        toggleIcon.classList.remove('bi-eye-slash');
                        toggleIcon.classList.add('bi-eye');
                    }
                });
            }

            // Form validation
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const password = passwordInput.value;
                    const isEditMode = {{ isset($data) ? 'true' : 'false' }};

                    if (!isEditMode && password.length < 8) {
                        e.preventDefault();
                        alert('Password minimal harus 8 karakter');
                        passwordInput.focus();
                    }
                });
            }

            // Add visual feedback for required fields
            const requiredFields = document.querySelectorAll('input[required], select[required]');
            requiredFields.forEach(field => {
                field.addEventListener('blur', function() {
                    if (this.value.trim() === '') {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });
            });
        });
    </script>
@endsection
