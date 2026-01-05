@extends('layouts.master')
@section('style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="text" class="form-control datepicker" id="from_date" placeholder="Pilih tanggal">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="text" class="form-control datepicker" id="to_date" placeholder="Pilih tanggal">
                    </div>

                    <div class="col-md-3">
                        <button class="btn btn-primary" id="filter">
                            Filter
                        </button>
                        <button class="btn btn-secondary" id="reset">
                            Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>


        {{-- <div class="card mb-3">
            <div class="row p-4">
                <div class="col-md-6">
                    <a href="{{ url('schedule-shift/generate') }}" class="btn btn-primary btn-lg">Generate Schedule</a>

                </div>
            </div>
        </div> --}}
        <!-- DataTable with Buttons -->
        <div class="card">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatables-basic table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Shift</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Terlambat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <hr class="my-12" />


    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
            allowInput: true,
            locale: "id"
        });
    </script>

    <script>
        $(function() {
            let table = $('.datatables-basic').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('reportabsensi.datatable') }}",
                    data: function(d) {
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                    }
                },

                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'name',
                        name: 'u.name'
                    },
                    {
                        data: 'schedule_name',
                        name: 'ss.name'
                    },
                    {
                        data: 'check_in',
                        name: 'ua.check_in'
                    },
                    {
                        data: 'check_out',
                        name: 'ua.check_out'
                    },
                    {
                        data: 'start_time',
                        name: 'ss.start_time'
                    },
                    {
                        data: 'end_time',
                        name: 'ss.end_time'
                    },
                    {
                        data: 'late_minutes',
                        name: 'late_minutes',
                        searchable: false
                    },
                    {
                        data: 'late_status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // FILTER
            $('#filter').click(function() {
                table.draw();
            });

            // RESET
            $('#reset').click(function() {
                $('#from_date').val('');
                $('#to_date').val('');
                table.draw();
            });
        });
    </script>
@endsection
