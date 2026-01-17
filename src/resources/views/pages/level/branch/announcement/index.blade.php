@extends('layouts.master')

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card mb-3">
            <div class="row p-4">
                <div class="col-md-6">
                    @canAdd
                    <a href="{{ url('master-pengumuman/add') }}" class="btn btn-primary btn-lg">
                        Tambah Data
                    </a>
                    @endcanAdd
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body table-responsive">
                <table id="pengumumanTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
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

    <script>
        $(function() {
            $('#pengumumanTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('pengumuman.datatable') }}",
                order: [
                    [1, 'desc']
                ], // default sort ke kolom ke-2 (Judul)

                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: (d, t, r, m) => m.row + 1
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
                    },
                    {
                        data: 'status',
                        name: 'status'
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
    @include('layouts.component.toast')
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
                        url: `/master-pengumuman/delete/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            Swal.fire('Berhasil!', res.message, 'success');
                            $('#pengumumanTable').DataTable().ajax.reload();
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
