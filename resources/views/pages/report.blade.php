@extends('app')

@section('content')
    <link rel="stylesheet" href="/css/report/style.css">

    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <button class="btn btn-primary float-right d-flex align-items-center shadow modal-button" data-toggle="modal"
                    data-target="#importModal">
                    Import
                    <ion-icon name="cloud-download" class="ml-2"></ion-icon>
                </button>
            </div>
            <div class="col-12 mt-4">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nomor WO</th>
                            <th scope="col">Customer</th>
                            <th scope="col">Total Tagihan</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Import Data Penjualan DMS</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="/import" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body border-0">
                        <div class="container-fluid d-flex align-items-center justify-content-center">
                            <label for="files" class="file-label">
                                <ion-icon name="cloud-download" class="ml-2 icon-label"></ion-icon>
                                <img src="/assets/images/excel.png" alt="thumbnail" class="import-thumbnail d-none"
                                    id="import-thumbnail">
                                <p class="text-input-label">Browse Excel Files</p>
                                <input type="file" id="files" class="file-import" name="file"
                                    accept=".xls, .xlsx">
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" id="dismiss-btn" class="btn btn-secondary dismiss-btn"
                            data-dismiss="modal">Batalkan</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#files').change(function(e) {
                e.preventDefault();
                var myFile = $(this).prop('files')[0];
                if (myFile) {
                    $(".text-input-label").html(myFile.name);
                    $(".icon-label").addClass('d-none');
                    $('.import-thumbnail').removeClass('d-none');
                }
            });

            $('.modal-button').click(function(e) {
                e.preventDefault();
                $(".icon-label").removeClass('d-none');
                $('.import-thumbnail').addClass('d-none');
                $(".text-input-label").html("Browse Excel Files");
            });
        });
    </script>
@endsection
