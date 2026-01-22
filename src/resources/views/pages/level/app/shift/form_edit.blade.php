@extends('layouts.master')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <form action="{{ url('v1/schedule-shift/update/' . $data->id) }}" method="post">
            @csrf

            <div class="row">
                <div class="card">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Shift</h5>
                        <small class="text-body-secondary">Edit Shift</small>
                    </div>

                    <div class="card-body p-4">

                        <div class="row mb-3">
                            <div class="col-md-6 mb-6">
                                <label class="form-label">Company </label>
                                <select id="company" class="form-control" name="company_id">
                                    <option value="">-- Pilih Company --</option>
                                </select>

                            </div>
                            <!-- CODE -->
                            <div class="col-md-6 mb-6">
                                <label class="form-label">Code Shift</label>
                                <input type="hidden" id="old_company" value="{{ $data->company_id ?? '' }}">
                                <input type="text" name="code" value="{{ $data->code }}" class="form-control">
                            </div>

                            <!-- NAME -->
                            <div class="col-md-6 mb-6">
                                <label class="form-label">Nama Shift</label>
                                <input type="text" name="name" value="{{ $data->name }}" class="form-control">
                            </div>

                            <!-- START TIME -->
                            @php
                                [$startHour, $startMinute] = explode(':', $data->start_time);
                            @endphp
                            <div class="col-md-6 mb-6">
                                <label class="form-label">Start Time</label>
                                <div class="d-flex gap-2">
                                    <select name="start_hour" class="form-select">
                                        @for ($i = 0; $i < 24; $i++)
                                            @php $h = sprintf('%02d', $i); @endphp
                                            <option value="{{ $h }}" {{ $h == $startHour ? 'selected' : '' }}>
                                                {{ $h }}
                                            </option>
                                        @endfor
                                    </select>

                                    <span class="align-self-center">:</span>

                                    <select name="start_minute" class="form-select">
                                        @for ($i = 0; $i < 60; $i += 5)
                                            @php $m = sprintf('%02d', $i); @endphp
                                            <option value="{{ $m }}" {{ $m == $startMinute ? 'selected' : '' }}>
                                                {{ $m }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <!-- END TIME -->
                            @php
                                [$endHour, $endMinute] = explode(':', $data->end_time);
                            @endphp
                            <div class="col-md-6 mb-6">
                                <label class="form-label">End Time</label>
                                <div class="d-flex gap-2">
                                    <select name="end_hour" class="form-select">
                                        @for ($i = 0; $i < 24; $i++)
                                            @php $h = sprintf('%02d', $i); @endphp
                                            <option value="{{ $h }}" {{ $h == $endHour ? 'selected' : '' }}>
                                                {{ $h }}
                                            </option>
                                        @endfor
                                    </select>

                                    <span class="align-self-center">:</span>

                                    <select name="end_minute" class="form-select">
                                        @for ($i = 0; $i < 60; $i += 5)
                                            @php $m = sprintf('%02d', $i); @endphp
                                            <option value="{{ $m }}" {{ $m == $endMinute ? 'selected' : '' }}>
                                                {{ $m }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                        </div>

                        <hr class="my-4">

                    </div>

                    <div class="card-footer text-end">
                        <button class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Data
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
                }
            });





        });
    </script>
@endsection
