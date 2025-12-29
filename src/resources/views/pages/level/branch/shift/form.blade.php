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
        <form action="{{ url('/schedule-shift/store') }}" method="post">
            @csrf
            <div class="row">

                <div class="card ">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tambah Shift</h5>
                        <small class="text-body-secondary float-end">Tambah Shift</small>
                    </div>

                    <div class="card-body p-4">

                        <div class="row mb-3">
                            <div class="col-md-6 mb-6">
                                <label class="form-label">Code Shift</label>
                                <input type="text" name="code" class="form-control" placeholder="Masukkan username">
                            </div>
                            <div class="col-md-6 mb-6">
                                <label class="form-label">Nama Shift</label>
                                <input type="text" name="name" class="form-control" placeholder="Masukkan password">
                            </div>
                            <div class="col-md-6 mb-6">
                                <label class="form-label">Start Time</label>
                                <div class="d-flex gap-2">
                                    <select name="start_hour" class="form-select">
                                        @for ($i = 0; $i < 24; $i++)
                                            <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                        @endfor
                                    </select>
                                    <span class="align-self-center">:</span>
                                    <select name="start_minute" class="form-select">
                                        @for ($i = 0; $i < 60; $i += 5)
                                            <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mb-6">
                                <label class="form-label">End Time</label>
                                <div class="d-flex gap-2">
                                    <select name="end_hour" class="form-select">
                                        @for ($i = 0; $i < 24; $i++)
                                            <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                        @endfor
                                    </select>
                                    <span class="align-self-center">:</span>
                                    <select name="end_minute" class="form-select">
                                        @for ($i = 0; $i < 60; $i += 5)
                                            <option value="{{ sprintf('%02d', $i) }}">{{ sprintf('%02d', $i) }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- 🏢 COMPANY -->


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
