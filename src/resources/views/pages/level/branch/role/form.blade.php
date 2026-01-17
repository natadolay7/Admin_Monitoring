@extends('layouts.master')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">


        <form action="{{ isset($data) ? url('/core/role/update/' . $data->id) : url('/core/role/store') }}" method="post">
            @csrf
            <div class="row">

                <div class="card ">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ isset($data) ? 'Update Role' : 'Add Role'}}</h5>
                        <small class="text-body-secondary float-end">{{ isset($data) ? 'Update Role' : 'Add Role'}}</small>
                    </div>

                    <div class="card-body p-4">

                        <div class="row mb-3">
                            <div class="col-md-6 mb-6">
                                <label class="form-label">Title</label>
                                <input type="text" value="{{ old('title', $data->title ?? '') }}" name="title"
                                    class="form-control" placeholder="Masukkan Title">
                            </div>




                        </div>

                        <hr class="my-4">

                        <!-- 🏢 COMPANY -->


                    </div>

                    <div class="card-footer  text-end rounded-bottom-4">
                        {{-- <button class="btn btn-secondary me-2">Cancel</button> --}}
                        <button class="btn btn-primary">
                            <i class="bi bi-save"></i>{{ isset($data) ? 'Update Role' : 'Save Role'}}
                        </button>
                    </div>

                </div>

            </div>
        </form>

    </div>

@endsection
@section('script')
    @include('layouts.component.toast')
@endsection
