//---- Running Datatable
var table = $('#transactionTable').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    ajax: '/load_transaksi',
    columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            responsivePriority: 1
        },
        {
            data: 'wo',
            name: 'wo',
            responsivePriority: 2
        },
        {
            data: 'customer',
            name: 'customer',
            responsivePriority: 3
        },
        {
            data: 'invoice_date',
            name: 'invoice_date',
            responsivePriority: 4
        },
        {
            data: 'jasa',
            name: 'jasa',
            render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp. ' )
        },
        {
            data: 'discJasa',
            name: 'discJasa',
            render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp. ' )
        },
        {
            data: 'parts',
            name: 'parts',
            render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp. ' )
        },
        {
            data: 'discParts',
            name: 'discParts',
            render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp. ' )
        },
        {
            data: 'bahan',
            name: 'bahan',
            render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp. ' )
        },
        {
            data: 'discBahan',
            name: 'discBahan',
            render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp. ' )
        },
        {
            data: 'ppn',
            name: 'ppn',
            render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp. ' )
        },
        {
            data: 'total',
            name: 'total',
            responsivePriority: 5,
            render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp. ' )
        },
        {
            data: 'action',
            name: 'action',
            responsivePriority: 6,
            orderable: false,
            searchable: false
        }
    ]
});
//---- Delete PKB
$(document).on('click', '.btn-delete', function () { 
    var NoWo = $(this).attr('data-wo');
    vex.dialog.confirm({
        message: 'Yakin Hapus data WO ini ?',
        callback: function(value) {
            if (value) {
                $.ajax({
                    type: "POST",
                    url: "/delete",
                    data: {
                        wo : NoWo
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "json",
                    success: function (response) {
                        vex.dialog.alert({
                            message: 'Data WO Sudah Berhasil Di Hapus',
                        })

                        table.draw();
                    }
                });
            } else {
                vex.dialog.alert({
                    message: 'Penghapusan dibatalkan',
                })
            }
        }
    })
});




$(document).ready(function () {
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