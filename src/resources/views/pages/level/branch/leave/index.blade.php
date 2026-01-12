@extends('layouts.master')

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- FILTER -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label>Dari Tanggal</label>
                        <input type="text" id="start_date" class="form-control" placeholder="Pilih tanggal">
                    </div>

                    <div class="col-md-4">
                        <label>Sampai Tanggal</label>
                        <input type="text" id="end_date" class="form-control" placeholder="Pilih tanggal">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button id="filter" class="btn btn-primary">Filter</button>
                        <button id="reset" class="btn btn-secondary ms-2">Reset</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- DATATABLE -->
        <div class="card">
            <div class="card-body table-responsive">
                <table id="leaveTable" class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Jenis Leave</th>
                            <th>Tanggal Request</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        $(function() {

            let table = $('#leaveTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('leave.datatable') }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
                    },
                    {
                        data: 'code'
                    },
                    {
                        data: 'type_leave'
                    },
                    {
                        data: 'date_request'
                    },
                    {
                        data: 'date_start'
                    },
                    {
                        data: 'date_end'
                    },
                    {
                        data: 'status'
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
                table.ajax.reload();
            });

            // RESET
            $('#reset').click(function() {
                $('#start_date').val('');
                $('#end_date').val('');
                table.ajax.reload();
            });

        });
    </script>
    <script>
        flatpickr("#start_date", {
            dateFormat: "Y-m-d"
        });

        flatpickr("#end_date", {
            dateFormat: "Y-m-d"
        });
    </script>
@endsection
