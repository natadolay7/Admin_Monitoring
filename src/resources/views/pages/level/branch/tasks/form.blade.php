@extends('layouts.master')
@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">


        <form action="{{ isset($data) ? url('/task/update/' . $data->id) : url('/task/store') }}" method="post">
            @csrf
            <div class="row">

                <div class="card ">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ isset($data) ? 'Update Task' : 'Add Task' }}</h5>
                        <small class="text-body-secondary float-end">{{ isset($data) ? 'Update Role' : 'Add Role' }}</small>
                    </div>

                    <div class="card-body p-4">

                        <div class="row mb-3">
                            <div class="col-md-6 mb-6">
                                <label class="form-label">Title</label>
                                <input type="text" value="{{ old('title', $data->name ?? '') }}" name="title"
                                    class="form-control" placeholder="Masukkan Title">
                            </div>
                            <div class="col-md-6 mb-6">
                                <label class="form-label">Task Type</label>
                                <select name="task_type" id="task_type" class="form-select">
                                    <option value="">-- Pilih --</option>
                                    @foreach ($task_type as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-6" id="assign_wrapper" style="display:none;">
                                <label class="form-label">Task Assign</label>
                                <select name="task_asign[]" id="task_assign" class="form-select select2" multiple>
                                    <option value="">-- Pilih --</option>
                                    @foreach ($tad as $item)
                                        <option value="{{ $item->user_id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-6">
                                <label class="form-label">Note</label>
                                <textarea class="form-control" name="note"></textarea>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Start Time</label>
                                        <input type="time" name="start_time" class="form-control" step="60"
                                            value="{{ old('start_time', $data->start_time ?? '') }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">End Time</label>
                                        <input type="time" name="end_time" class="form-control" step="60"
                                            value="{{ old('end_time', $data->end_time ?? '') }}">
                                    </div>
                                </div>
                            </div>




                        </div>

                        <hr class="my-4">

                        <!-- 🏢 COMPANY -->


                    </div>

                    <div class="card-footer  text-end rounded-bottom-4">
                        {{-- <button class="btn btn-secondary me-2">Cancel</button> --}}
                        <button class="btn btn-primary">
                            <i class="bi bi-save"></i>{{ isset($data) ? 'Update Role' : 'Save Role' }}
                        </button>
                    </div>

                </div>

            </div>
        </form>

    </div>
@endsection
@section('script')
    @include('layouts.component.toast')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#task_assign').select2({
                placeholder: "-- Pilih --",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#task_type').on('change', function() {
                let value = $(this).val();

                if (value == '2') {
                    $('#assign_wrapper').show();
                } else {
                    $('#assign_wrapper').hide();
                    $('#task_assign').val(null).trigger('change'); // reset select2
                }
            });
        });
    </script>
@endsection
