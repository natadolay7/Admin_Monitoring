@extends('layouts.master')
@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <form
            action="{{ isset($data) ? url('/master-pengumuman/update/' . $data->id) : url('/master-pengumuman/store') }}"
            method="post">
            @csrf

            <div class="card">
                <div class="card-header">
                    <h5>{{ isset($data) ? 'Edit Pengumuman' : 'Tambah Pengumuman' }}</h5>
                </div>

                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" name="title" class="form-control"
                            value="{{ old('title', $data->title ?? '') }}" placeholder="Judul pengumuman">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Isi Pengumuman</label>
                        <textarea name="content" class="form-control" rows="5" placeholder="Isi pengumuman">{{ old('content', $data->content ?? '') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="text" name="start_date" id="start_date" class="form-control"
                                value="{{ old('start_date', $data->start_date ?? '') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="text" name="end_date" id="end_date" class="form-control"
                                value="{{ old('end_date', $data->end_date ?? '') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active"
                                    {{ old('status', $data->status ?? '') == 'active' ? 'selected' : '' }}>
                                    Aktif
                                </option>
                                <option value="inactive"
                                    {{ old('status', $data->status ?? '') == 'inactive' ? 'selected' : '' }}>
                                    Nonaktif
                                </option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="card-footer text-end">
                    <button class="btn btn-primary">
                        <i class="bi bi-save"></i>
                        {{ isset($data) ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </div>

        </form>
    </div>
@endsection

@section('script')
    @include('layouts.component.toast')

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#start_date", {
            dateFormat: "Y-m-d"
        });
        flatpickr("#end_date", {
            dateFormat: "Y-m-d"
        });
    </script>
@endsection
