@extends('layouts.master')
@section('style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
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
        @canAdd
        <div class="card mb-3">
            <div class="row p-4">
                <div class="col-md-6">
                    <a href="{{ url('schedule-shift/generate') }}" class="btn btn-primary btn-lg">Generate Schedule</a>

                </div>
            </div>
        </div>
        @endcanAdd
        <!-- DataTable with Buttons -->
        <div class="card">
            <div class="card-body table-responsive pt-0">

                <table class="datatables-basic table table-bordered table-striped"">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Day</th>
                            <th>Tanggal</th>

                            <th>Nama TAD </th>
                            <th>Nama Shift </th>

                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Status</th>
                            <th>Created Date</th>


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
    <script>
        $(function() {
            $('.datatables-basic').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('schedulelist.datatable') }}",

                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'hari',
                        name: 'hari'
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'nama_tad',
                        name: 'nama_tad'
                    },
                    {
                        data: 'nama_shift',
                        name: 'nama_shift'
                    },
                    {
                        data: 'start_time',
                        name: 'start_time'
                    },
                    {
                        data: 'end_time',
                        name: 'end_time',
                        orderable: false
                    },
                    {
                        data: 'holiday',
                        name: 'hodiday',
                        orderable: false
                    },

                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: false
                    },

                ]
            });
        });
    </script>
@endsection
