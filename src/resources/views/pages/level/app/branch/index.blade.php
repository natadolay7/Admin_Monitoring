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
        <div class="card mb-3">
            <div class="row p-4">
                <div class="col-md-6">
                    <a href="{{ url('v1/branch/add') }}" class="btn btn-primary btn-lg">Tambah Data</a>

                </div>
            </div>
        </div>
        <!-- DataTable with Buttons -->
        <div class="card">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatables-basic table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Company</th>
                            <th>Company Code</th>
                            <th>Branch Name</th>
                            <th>Email/Username </th>
                            <th>Status</th>
                            <th>Action</th>
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
                ajax: "{{ route('v1.branch.datatable') }}",

                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'company',
                        name: 'company'
                    },
                     {
                        data: 'branch_name',
                        name: 'branch_name'
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).on('click', '.btn-delete', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Yakin hapus?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/v1/branch/delete/${id}`,
                        type: 'GET',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            Swal.fire('Berhasil!', res.message, 'success');
                            $('.datatables-basic ').DataTable().ajax.reload();
                        },
                        error: function() {
                            Swal.fire('Gagal!', 'Tidak bisa menghapus data', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endsection
