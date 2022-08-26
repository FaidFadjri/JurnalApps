 <link rel="stylesheet" href="/css/modal/style.css">

 <!-- Import Modal -->
 <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
     <div class="modal-dialog" role="document">
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


 <!-- Detail Modals -->
 <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
     aria-hidden="true">
     <div class="modal-dialog modal-lg" role="document">
         <div class="modal-content">
             <div class="modal-header border-0">
                 <h5 class="modal-title" id="exampleModalCenterTitle">Detail PKB | 2022-08-25</h5>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <div class="modal-body border-0">
                 <div class="container-fluid">
                     <div class="row">
                         <div class="col-4" id="accordion-wrap">
                             <div class="card border rounded-sm">
                                 <div class="card-body">
                                     <h5 class="card-title">Total Debit</h5>
                                     <p class="card-text">
                                         Total Debit di hitung dari <strong>Revenue</strong> dengan <strong>PPN</strong>
                                     </p>
                                     <a href="#" class="btn btn-primary mt-1 w-100"><strong>Rp.
                                             25.000.000</strong></a>
                                 </div>
                             </div>
                         </div>
                         <div class="col-4">
                             <div class="card border rounded-sm">
                                 <div class="card-body">
                                     <h5 class="card-title">Total Kredit</h5>
                                     <p class="card-text">
                                         Total Kredit di hitung dari seluruh jumlah <strong>Sales</strong>
                                     </p>
                                     <a href="#" class="btn btn-primary mt-1 w-100"><strong>Rp.
                                             25.000.000</strong></a>
                                 </div>
                             </div>
                         </div>
                         <div class="col-4">
                             <div class="card border rounded-sm">
                                 <div class="card-body">
                                     <h5 class="card-title">Total Kredit</h5>
                                     <p class="card-text">
                                         Total Kredit di hitung dari seluruh jumlah <strong>Sales</strong>
                                     </p>
                                     <a href="#" class="btn btn-primary mt-1 w-100"><strong>Rp.
                                             25.000.000</strong></a>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
             <div class="modal-footer border-0">
                 <button type="button" id="dismiss-btn" class="btn btn-secondary dismiss-btn"
                     data-dismiss="modal">Tutup</button>
                 <button type="button" class="btn btn-primary">Simpan</button>
             </div>
         </div>
     </div>
 </div>
