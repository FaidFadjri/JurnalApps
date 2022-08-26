$(document).ready(function () {
    var table = $('#transactionTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '/load_transaksi',
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex'
            },
            {
                data: 'wo',
                name: 'wo'
            },
            {
                data: 'customer',
                name: 'customer'
            },
            {
                data: 'invoice_date',
                name: 'invoice_date'
            },
            {
                data: 'jasa',
                name: 'jasa'
            },
            {
                data: 'discJasa',
                name: 'discJasa'
            },
            {
                data: 'parts',
                name: 'parts'
            },
            {
                data: 'discParts',
                name: 'discParts'
            },
            {
                data: 'bahan',
                name: 'bahan'
            },
            {
                data: 'discBahan',
                name: 'discBahan'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ]
    });


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
        $(".text-input-label").html("Browse Excel Files");
    });
});

$(document).on('click', '.btn-delete', function () { 
    vex.dialog.confirm({
        message: 'Yakin Hapus data ini ?',
        callback: function(value) {
            if (value) {
            } else {
                vex.dialog.alert({
                    message: 'Penghapusan dibatalkan',
                })
            }
        }
    })
});