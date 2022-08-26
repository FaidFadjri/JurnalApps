@extends('app')

@section('content')
    <link rel="stylesheet" href="/css/report/style.css">

    <div class="container mt-5">
        @if (Session::has('pesan'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Selamat!</strong> {{ Session::get('pesan') }}.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-12">
                <button class="btn btn-primary float-right d-flex align-items-center shadow modal-button"
                    data-toggle="modal" data-target="#importModal">
                    Import
                    <ion-icon name="cloud-download" class="ml-2"></ion-icon>
                </button>
            </div>
            <div class="col-12 mt-4">
                <table class="table table-striped table-bordered" id="transactionTable">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nomor WO</th>
                            <th scope="col">Customer</th>
                            <th scope="col">Tanggal Invoice</th>
                            <th scope="col">Jasa</th>
                            <th scope="col">Disc Jasa</th>
                            <th scope="col">Part</th>
                            <th scope="col">Disc Part</th>
                            <th scope="col">Bahan</th>
                            <th scope="col">Disc Bahan</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('items.modal')
@endsection

@section('script')
    <script src="/js/report.js"></script>
@endsection
