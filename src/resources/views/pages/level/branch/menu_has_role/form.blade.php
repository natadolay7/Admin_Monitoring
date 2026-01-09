@extends('layouts.master')

@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

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

        <form action="{{ url('core/menu-has-role/store') }}" method="POST">
            @csrf

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Add Role</h5>
                    <small class="text-body-secondary">Add Role</small>
                </div>

                <div class="card-body p-4">

                    <!-- ROLE & MENU -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Pilih Role</label>
                            <select name="role_id" class="form-control select-role"></select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Pilih Menu</label>
                            <select name="menu_id" class="form-control select-menu"></select>
                        </div>
                    </div>

                    <!-- PERMISSION -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label d-block mb-2">Permission</label>

                            <div class="d-flex gap-4 flex-wrap">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="add" value="1"
                                        id="add">
                                    <label class="form-check-label" for="add">Add</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="edit" value="1"
                                        id="edit">
                                    <label class="form-check-label" for="edit">Edit</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="delete" value="1"
                                        id="delete">
                                    <label class="form-check-label" for="delete">Delete</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="view" value="1"
                                        id="view">
                                    <label class="form-check-label" for="view">View</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                </div>

                <div class="card-footer text-end">
                    <button class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Data
                    </button>
                </div>
            </div>
        </form>

    </div>
@endsection
@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {

            $('.select-role').select2({
                placeholder: 'Pilih Role',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: '{{ route('role.ajax') }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: data => ({
                        results: data.map(item => ({
                            id: item.id,
                            text: item.title
                        }))
                    }),
                    cache: true
                }
            });

            $('.select-menu').select2({
                placeholder: 'Pilih Menu',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: '{{ route('menu.ajax') }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: data => ({
                        results: data.map(item => ({
                            id: item.id,
                            text: item.name
                        }))
                    }),
                    cache: true
                }
            });

        });
    </script>
@endsection
