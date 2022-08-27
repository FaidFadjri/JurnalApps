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
        @elseif(Session::has('error'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Oops!</strong> {{ Session::get('error') }}.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
        <div class="row mt-5">
            <form action="/export" method="POST" class="w-100">
                @csrf
                <div class="row">
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="startDate">Sejak Tanggal</label>
                            <input type="date" name="startDate" id="startDate" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="form-group">
                            <label for="endDate">Sampai Tanggal</label>
                            <input type="date" name="endDate" id="endDate" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-12 d-flex align-items-center justify-content-end">
                        <button type="submit" class="btn btn-success shadow">Export Jurnal</button>
                        <button type="button"
                            class="btn btn-primary ml-2 float-right d-flex align-items-center shadow modal-button"
                            data-toggle="modal" data-target="#importModal">
                            Import
                            <ion-icon name="cloud-download" class="ml-2"></ion-icon>
                        </button>
                    </div>
                </div>
            </form>
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
                            <th scope="col">PPN</th>
                            <th scope="col">Total Net Revenue</th>
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
