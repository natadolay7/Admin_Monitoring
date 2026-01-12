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
                    <a href="{{ url('schedule-shift/add') }}" class="btn btn-primary btn-lg">Tambah Data</a>

                </div>
            </div>
        </div>
        @endcanAdd
        <!-- DataTable with Buttons -->
        <div class="card">
            <div class="card-body table-responsive pt-0">
                <table class="datatables-basic table able-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Code</th>
                            <th>Nama Shift </th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Created Date</th>

                            <th>Edit</th>
                            <th>Delete</th>

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {
            $('.datatables-basic').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url('schedule-shift/datatable') }}",

                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'name',
                        name: 'name'
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
                        data: 'created_at',
                        name: 'created_at',
                        orderable: false
                    },
                    {
                        data: 'edit',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'delete',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
    <script>
        $(document).on('click', '.btn-delete', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Yakin hapus?',
                text: 'Data shift akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/schedule-shift/delete') }}/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: res.message,
                                showConfirmButton: false,
                                timer: 3000
                            });

                            $('.datatables-basic').DataTable().ajax.reload();
                        },
                        error: function() {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: 'Gagal menghapus shift',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection
