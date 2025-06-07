var EPRS_M = function () {

    var init_prs_history = function() {
        var tbl_prs_history = $('#tbl_prs_history',document);
        dt_prs_history();
        prs_history_haldler();


    };

    var prs_history_haldler = function () {
        var tbl_prs_history = $('#tbl_prs_history',document);
        tbl_prs_history.on('click','#btn_load_items',function () {
            var this_ = $(this);
            var this_id = this_.attr('data-id');

            swal({
                title: "Load all items?",
                text: "This will add all items from the selected PRF.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Yes!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url : PECO.base_url() + 'purchasing/loadprfitems',
                        type : 'post',
                        dataType : 'json',
                        data : {
                            id : this_id
                        }
                    }).done(function (d) {
                        if (d.qry) {
                            swal(d.title, d.msg, d.func);
                            EPRS.loadPRFItems();
                        }
                    }).fail(function () {
                        swal('FAIL!','Failed to execute function.','error');
                    });
                }else{
                    swal.close();
                }
            });
        });
    };

    var dt_prs_history = function () {
        var tbl_prs_history = $('#tbl_prs_history',document);
        PECO.dtSubDetails(tbl_prs_history,'purchasing/prfsubdetails',false,'max-heigh');

        $.ajax({
            url: PECO.base_url() + 'purchasing/prslist',
            type: 'post',
            dataType: 'json'
        }).done(function (d) {
            //tbl_prs_history.dataTable.empty();
            tbl_prs_history.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: {
                    emptyTable : '<h4><i class="fa fa-warning text-warning"></i> No past transactions!</h4>'
                },
                aoColumns: d.columns,
                fnRowCallback: function(nRow, aData, Index) {
                    //PECO.dtExpandBtn(nRow, aData.expand);
                }
            });
        }).fail(function () {

        });
    };

    var add_item_init = function () {
        PECO.select2Basic($('#item_unit', document), 'query/getunits', 'Unit...');
        PECO.select2Basic($('#last_purchase_supplier', document), 'purchasing/select2quotationsupplier', 'Supplier Name...');

        $('#frm_add_new_item',document).on('submit',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url: this_.attr('action'),
                type : 'post',
                dataType : 'json',
                data : this_.serialize()
            }).done(function (d) {
                if (typeof d.itemExist !== 'undefined' && d.itemExist === true) {
                    if (typeof d.reEnable !== 'undefined' && d.reEnable === true) {
                        PECO.processSwalForm({
                            title: d.title,
                            text: d.msg,
                            form: this_,
                            extradata: {reactivate: true}
                        });
                    } else {
                        swal({
                            title: d.title,
                            text: d.msg,
                            type: d.func
                        });
                    }
                }
            }).fail(function () {

            });
        });

        $('#icheck_last_purchase',document).iCheck({
            checkboxClass: 'icheckbox_minimal-blue', // minimal / square / polaris / futurico // red / green / blue
            //radioClass: 'iradio_minimal-blue',
            //increaseArea: '20%' // optional
        }).on('ifChecked', function () {
            var this_ = $(this);
            this_.attr('checked', true);
            $('#purchase_details',document).find('input').each(function () {
                $(this).attr('disabled',false);
            });
        }).on('ifUnchecked', function () {
            var this_ = $(this);
            this_.attr('checked', false);
            $('#purchase_details',document).find('input').each(function () {
                $(this).attr('disabled',true);
            });
        });
    }

    return {
        prsHistory : function () {
            init_prs_history();
        },
        addItem : function () {
            add_item_init();
        }
    }
}();