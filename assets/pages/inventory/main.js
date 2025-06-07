var INVENTORY = function() {

    PECO.getHighlightsPlugin();
    PECO.getDataTablePlugin();
    PECO.getSelect2Plugins();

    var init_products = function() {

        //tbl_products();
        $(document).on('click', '#btn_product_refresh', function(e) {
            e.preventDefault();
            tbl_products();
        });

        handler_tbl_stocks();

        $(document).on('click', '#btn_refresh_stocks', function(e) {
            e.preventDefault();
            handler_tbl_stocks();
        });

        $(document).on('submit', '#frm_add_stock_item', function(e) {
            e.preventDefault();
            var form = $(this);
            swal({
                title: "Are you sure?",
                text: 'Save stock items',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Process",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: form.attr('action'),
                        type: 'post',
                        data: form.serialize(),
                        dataType: 'json'
                    }).done(function (d) {
                        PECO.initAlerts(d.msg,d.title,d.func);
                        swal.close();
                        handler_tbl_stocks();
                    }).fail(function() {
                        PECO.phpError();
                        swal.close();
                    });
                }else{
                    swal.close();
                }
            });
        });


        $(document).on('submit', '#frm_stock_out', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                type: 'post',
                data: form.serialize(),
                dataType: 'json',
            }).done(function(d) {
                PECO.initAlerts(d.msg, d.title, d.func);
                handler_tbl_stocks();
            });
        });

        $(document).on('keyup', '#search_stockout_code', function(e) {
            handler_stockout_query();
        });
    };


    var handler_stockout_query = function() {
        var codes = $('#search_stockout_code', document).val();
        $.ajax({
            url: PECO.base_url() + 'inventory/querystockout',
            type: 'post',
            data: {'codes': codes},
            dataType: 'json',
        }).done(function(d) {
            if(d.qry) {
                $('#stockout_text_desc', document).text(d.desc);
                $('#stockout_text_stocks', document).text(d.qty);
                $('#stock_out_stat', document).text('');
            } else {
                $('#search_stockout_code', document).val('');
                $('#stock_out_stat', document).text(d.msg);
            }
        });
    };



    var handler_tbl_stocks = function() {
        var tbl_stocks_ = $('#tbl_stocks', document);
        $.ajax({
            url: PECO.base_url() + 'inventory/tblstocklist',
            type: 'post',
            data: {},
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_stocks_, 'Loading stocks...');
            }
        }).done(function(d) {
            tbl_stocks_.DataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                ordering: false,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: {
                    "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No transaction related records yet!</h4>'
                },
                aoColumns: d.columns,
                searchHighlight: true,
                fnRowCallback: function(nRow, aData) {
                    $(nRow).addClass(aData.rowbg);
                    //PECO.dtExpandBtn($(nRow), aData.num);
                    PECO.popOverRow($('.popovers', nRow), true, true, 'popover-info')
                }
            });
        });
    };

    var search_handler = function() {

        var item_search = $('#item_search', document);
        var item_id = $('#item_id', document);
        var item_desc = $('#item_desc', document);
        var text_lastdate = $('#text_lastdate', document);

        var text_lastprice = $('#text_lastprice', document);
        var text_itemtotal = $('#text_itemtotal', document);

        var a = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/itemsearch?query=%QUERY", wildcard: "%QUERY"}
        });

        a.initialize(), item_search.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "desc",
            source: a.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<img src="{{img}}" width="50" height="50"/>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{code}}</b></h5>', "<p>{{desc}}</p>", "</div>", "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {
            item_id.val(selection.id);
            item_desc.val(selection.desc);
            // text_lastprice.text(selection.amts_text);
            text_lastdate.text(selection.date);
            // text_itemtotal.text(Number(selection.amts * Number(item_qty.val()))).number(true, 2);
        }).click(function() {
            PECO.initElScroller($('.tt-dropdown-menu', document));
        });



        var search_text_supplier = $('#search_text_supplier', document);
        var supp_id = $('#supplier_id', document);


        var b = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/suppliers?query=%QUERY", wildcard: "%QUERY"}
        });

        b.initialize(), search_text_supplier.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "names",
            source: b.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile([
                    '<div class="media">',
                    '<div class="pull-left">',
                    '<div class="media-object">',
                    '<img src="{{picture}}" width="50" height="50"/>',
                    "</div>",
                    "</div>",
                    '<div class="media-body">',
                    '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{names}}</b></h5>',
                    "<p>{{address}}</p>",
                    "</div>",
                    "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {
            supp_id.val(selection.id);
        }).click(function() {
            PECO.initElScroller($('.tt-dropdown-menu', document));
        });

        PECO.select2Types($('#select2brands', document), 'ITEMBRANDS', 'Brand...');
    };

    var tbl_products = function() {
        var tbl_products = $('#tbl_products', document);
        $.ajax({
            url: PECO.base_url() + 'inventory/tblproducts',
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_products, 'Loading products...');
            }
        }).done(function(d) {
            tbl_products.DataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: PECO.DTEmptyMessage('No data yet'),
                aoColumns: [
                    {"data": "num", sWidth: '20px', sClass: ''},
                    {"data": "supplier", sWidth: '', sClass: ''},
                    {"data": "product", sWidth: '', sClass: ''},
                    {"data": "brand", sWidth: '', sClass: ''},
                    {"data": "qty", sWidth: '', sClass: 'number'},
                    {"data": "control", sWidth: '', sClass: 'text-align-center controls'},
                ],
                searchHighlight: true
            });
        }).fail(function() {
            PECO.DTphpError(tbl_products);
        });
    };


    var init_inventory = function() {
        $('table.types', document).each(function() {
            var table = $(this);
            init_tbl_initialization(table, table.attr('data-code'), table.attr('data-title'));
        });

        $(document).on('click', '.btn-refresh', function() {
            var this_ = $(this);
            var this_portlet = this_.closest('.portlet');
            var table = $('table.types', this_portlet);
            init_tbl_initialization(table, table.attr('data-code'), table.attr('data-title'));
        });

        $(document).on('submit', '#frm_add_types', function(e) {
            e.preventDefault();
            var form = $(this);
            var model_content = form.closest('#modal_ajax');
            var modal_title = $('#modal_title', model_content);
            var msgtitle = modal_title.text();

            $.SmartMessageBox({
                title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i> Confirm: " + msgtitle + "</span>",
                content: 'Please confirm action taken',
                buttons: '[Yes][No]',
                buttonsPosition: 'right',
                buttonClass: 'btn-primary, btn-danger',
                buttonsIcon: 'fa-angle-double-right, fa-times',
                inputIcon: 'fa fa-user',
                inputIconPosition: 'left',
            },function (ButtonPressed) {
                if (ButtonPressed === "Yes") {
                    $.ajax({
                        url: form.attr('action'),
                        type: form.attr('method'),
                        data: form.serialize(),
                        dataType: 'json'
                    }).done(function (data) {

                        PECO.initAlerts(data.msg, msgtitle, data.func);
                        var _portlet = $('#' + data.table).closest('.portlet ');
                        $('.btn-refresh', _portlet).trigger('click');

                    }).fail(function () {
                        PECO.phpError();
                    });
                }
            });
        });

        $('#inventory_tab a[data-toggle="tab"]', document).on('shown.bs.tab', function (e) {
            var this_ = $(this);
            var this_href = this_.attr('href').replace('#', '');
            if(this_href == 'suppliers') {
                handler_tbl_suppliers();
            }
        });



        $(document).on('submit', '#frm_stock_in', function(e) {
            var form = $(this);
            e.preventDefault();
            $.ajax({
                url: form.attr('action'),
                type: 'post',
                data: form.serialize(),
                dataType: 'json',
            }).done(function(d) {
                if(d.qry) {
                    tbl_stocks_in_list();
                }else{
                    PECO.initAlerts(d.msg, 'Error', d.func);
                }
            });
        });

        PECO.dtSubDetails($('#tbl_stocks', document), 'inventory/stockdetails');


        $(document).on('submit', '#frm_generate_codes', function(e) {
            e.preventDefault();
            var form = $(this);
            generate_barcode_html(form, 0);
        });

        $(document).on('click', '#btn_print_codes', function(e) {
            e.preventDefault();
            var form = $('#frm_generate_codes', document);
            generate_barcode_html(form, 1);
        });
    };

    var generate_barcode_html = function(form, type) {
        var barcode_content = $('#barcode_content', document);
        var input_stockid = $('#select2stock', document).val();
        var input_codestart = $('#input_codestart', document).val();
        var input_codecount = $('#input_codecount', document).val();
        $.ajax({
            url: form.attr('action'),
            data: {stockid: input_stockid, codestart: input_codestart, codecount: input_codecount, type: type},
            type: 'post',
            dataType: 'json',
            beforeSend: function () {
                barcode_content.html('<h3><i class="fa fa-circle-o-notch fa-spin"></i> Loading preview items...</h3>');
            }
        }).done(function (d) {
            if(type == 1) {
                PECO.pecoRepPrint('Barcode', d.html);
            }
            barcode_content.html(d.msg);
        }).fail(function(e) {
            PECO.phpError();
            barcode_content.html('<h3><i class="fa fa-times text-danger"></i> PHP Error!</h3>');
        });
    }

    var handler_tbl_suppliers = function() {
        var tbl_supplier = $('#tbl_supplier', document);
        $.ajax({
            url: PECO.base_url() + 'inventory/tblsuppliers',
            type: 'popst',
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_supplier, 'Loading suppliers...');
            }
        }).done(function(d) {
            tbl_supplier.DataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: PECO.DTEmptyMessage('No data yet'),
                aoColumns: [
                    {"data": "num", sWidth: '20px', sClass: ''},
                    {"data": "name", sWidth: '', sClass: ''},
                    {"data": "address", sWidth: '', sClass: ''},
                    {"data": "email", sWidth: '', sClass: ''},
                    {"data": "telephone", sWidth: '', sClass: ''},
                    {"data": "cellphone", sWidth: '', sClass: ''},
                    {"data": "control", sWidth: '', sClass: 'text-align-center controls'},
                ],
                searchHighlight: true
            });
        }).fail(function() {
            PECO.DTphpError(tbl_supplier);
        });
    };

    var init_tbl_initialization = function(el, code, msg) {
        $.ajax({
            url: PECO.base_url() + 'inventory/tblgetdatainit',
            data: {codes: code},
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(el, msg);
            }
        }).done(function(d) {
            el.DataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                searching: false,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: PECO.DTEmptyMessage('No data yet'),
                aoColumns: [
                    {"data": "expand", sWidth: '20px', sClass: ''},
                    {"data": "codes", sWidth: '', sClass: ''},
                    {"data": "descs", sWidth: '', sClass: ''},
                    {"data": "control", sWidth: '', sClass: 'text-align-center controls'},
                ],
                searchHighlight: true
            });
        }).fail(function() {
            PECO.DTphpError(el);
        });
    };

    var stocks_handler = function(dataid) {
        tbl_stocks_list(dataid);
    };

    var tbl_stocks_list = function(dataid) {
        tbl_stocks_in_list(dataid);
    };

    var stocks_entry_window = function() {

        setTimeout(function() {

            PECO.select2Basic($('#select2stock', document), 'assets/select2stocks', 'Select stock...', true, false, false, true);


            PECO.DTDefault($('#tbl_stocks_in_list', document), 'Select stock!');
            $(document).on('change', '#select2stock', function(e) {
                tbl_stocks_in_list();
                $('#search_text', document).focus();
            });
        }, 500);



        $(document).on('submit', '#frm_scan_entry', function(e) {
            var form = $(this);
            e.preventDefault();
            $.ajax({
                url: form.attr('action'),
                type: 'post',
                data: form.serialize(),
                dataType: 'json',
            }).done(function(d) {
                if(d.qry) {
                    tbl_stocks_in_list();
                }else{
                    PECO.initAlerts(d.msg, 'Error', d.func);
                }
            });
        });

    };

    var stocks_in_hanlder = function() {
        PECO.select2Basic($('#select2stock', document), 'assets/select2stocks', 'Select stock...', true, false, false, true);

        PECO.DTDefault($('#tbl_stocks_in_list', document), 'Select stock!');
        $(document).on('change', '#select2stock', function(e) {
            tbl_stocks_in_list();
        });

        $(document).on('click', '#btn_stock_in_save', function(e) {
            var stockid = $('#select2stock', document).val();
            if(stockid>0) {
                swal({
                    title: "Are you sure?",
                    text: 'Save stock items',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, Process",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: PECO.base_url() + 'inventory/savestockin',
                            type: 'post',
                            data: {stockid: stockid},
                            dataType: 'json'
                        }).done(function (d) {
                            PECO.initAlerts(d.msg,d.title,d.func);
                            swal.close();
                            tbl_stocks_in_list();
                        }).fail(function() {
                            PECO.phpError();
                            swal.close();
                        });
                    }else{
                        swal.close();
                    }
                });
            }
        });



    };


    var tbl_stocks_list = function(dataid) {
        var tbl_stocks_in_list = $('#tbl_stocks_in_list', document);
        $.ajax({
            url: PECO.base_url() + 'inventory/tblgetstockin',
            data: {stockid: dataid, status: 304},
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_stocks_in_list, 'Loading encoding...');
            }
        }).done(function(d) {
            tbl_stocks_in_list.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: true,
                bStateSave: true,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: PECO.DTEmptyMessage('No data yet'),
                aoColumns: [
                    {"data": "num", sWidth: '20px', sClass: ''},
                    {"data": "serials", sWidth: '', sClass: ''},
                    {"data": "date", sWidth: '', sClass: ''},
                    {"data": "status", sWidth: '', sClass: ''},
                    {"data": "control", sWidth: '', sClass: 'text-align-center controls'},
                ],
                searchHighlight: true
            });
        }).fail(function() {
            PECO.DTphpError(el);
        });
    };

    var tbl_stocks_in_list = function() {
        var stockid = $('#select2stock', document).val();
        var tbl_stocks_in_list = $('#tbl_stocks_in_list', document);
        if(stockid > 0) {
            $.ajax({
                url: PECO.base_url() + 'inventory/tblgetstockin',
                data: {stockid: stockid},
                type: 'post',
                dataType: 'json',
                beforeSend: function () {
                    PECO.DTphpLoading(tbl_stocks_in_list, 'Loading encoding...');
                }
            }).done(function (d) {
                tbl_stocks_in_list.DataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: false,
                    bInfo: true,
                    bStateSave: true,
                    aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                    language: PECO.DTEmptyMessage('No data yet'),
                    aoColumns: [
                        {"data": "num", sWidth: '20px', sClass: ''},
                        {"data": "serials", sWidth: '', sClass: ''},
                        {"data": "date", sWidth: '', sClass: ''},
                        {"data": "status", sWidth: '', sClass: ''},
                        {"data": "control", sWidth: '', sClass: 'text-align-center controls'},
                    ],
                    searchHighlight: true
                });
            }).fail(function () {
                PECO.DTphpError(el);
            });
        } else {

            PECO.DTphpLoading(tbl_stocks_in_list, 'Loading encoding...');
            PECO.DTDefault(tbl_stocks_in_list, 'Select stock!');
        }
    };

    var stock_generate_code = function() {
        PECO.select2Basic($('#select2stock', document), 'assets/select2stocks', 'Select stock...', true, false, false, true);


    };

    var init_new_invtrn = function (dataid) {
        //Transaction type: Select2 type
        var inv_trn_type = $('#inv_trn_type',document);
        var btn_add_reference = $('#btn_add_reference',document);
        var inventory_transactions = $('#inventory_transactions',document);
        var inv_reference_tabs = $('#inv_reference_tabs',inventory_transactions);
        var frm_create_inventory_trn = $('#frm_create_inventory_trn',document);
        PECO.select2Basic(inv_trn_type,'inventory/select2trntype','Select transaction...',false,false,inv_trn_type.val());
        //Reference
        var trnid = (dataid) ? dataid : $('#inv_trn_id',frm_create_inventory_trn).val();
        if (trnid > 0) {
            init_inventory_trn_items(trnid);
        }
        btn_add_reference.attr('data-arr',inv_trn_type.val());
        var btn_ref_html = btn_add_reference.html();
        inv_trn_type.on('change',function () {
            var this_ = $(this);
            btn_add_reference.attr('data-arr',this_.val());
        });

        if (btn_add_reference.attr('data-view') !== '') {

        }

        if (inv_reference_tabs.children().length > 0) {
            $('#btn_submit_trn',document).attr('disabled',false);
        }


        $('input,textarea', frm_create_inventory_trn).each(function () {
            $(this).trigger('change');
        });

        var btn_new_default = '<button type="submit" id="btn_new_inv_trn" class="btn btn-default margin-top-10" style="width: 100%"> <i class="fa fa-plus text-success bold"></i> New Transaction</button>';

        frm_create_inventory_trn.on('submit',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url : this_.attr('action'),
                type : this_.attr('method'),
                dataType : 'json',
                data : this_.serialize()
            }).done(function (d) {
                if (d.qry !== false) {
                    $('#btn_submit_trn',document).attr('data-id',d.trnid);
                    btn_add_reference.attr('data-view',d.trnid).attr('disabled',false);
                    $('#btn_new_inv_trn',this_).after(d.btn).remove();
                    $('input,textarea',this_).attr('disabled',true);
                    $('#inv_trn_id',frm_create_inventory_trn).val(d.trnid);
                } else {
                    swal('Inventory Transaction',d.msg,d.func);
                }
            }).fail(function () {

            });
        });

        $(document).on('click','#btn_cancel_inv_trn',function () {
            var this_ = $(this);
            var this_id = this_.attr('data-id');
            var this_form = this_.closest('form');
            var inventory_transactions = $('#inventory_transactions',document);
            swal({
                title: 'Cancel Transaction?',
                text: 'You\'re about to cancel the current transaction. All details will be erased. Do you wish to proceed?',
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: "btn-danger",
                cancelButtonText: "No!",
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Yes!",
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url : PECO.base_url() + 'inventory/canceltrn',
                        type : 'post',
                        dataType : 'json',
                        data : {
                            trnid : this_id
                        }
                    }).done(function (d) {
                        if (d.qry !== false) {
                            this_form.trigger('reset');
                            $('input,textarea', this_form).each(function () {
                                $(this).attr('disabled',false).val('').trigger('change');
                            });
                            $('#btn_submit_trn',document).attr('data-id','').attr('disabled',true);
                            btn_add_reference.attr('data-view','').attr('disabled',true);
                            this_.after(btn_new_default).remove();
                            var inventory_trn_fields = $('#inventory_trn_fields',document);
                            var no_reference_notice = $('#no_reference_notice',inventory_trn_fields);
                            var inventory_transactions = $('#inventory_transactions',inventory_trn_fields);
                            var tabs = $('#inv_reference_tabs',inventory_transactions);
                            var content = $('#inv_reference_content',inventory_transactions);
                            if (tabs.children().length > 0) {
                                $('li:not(#attachment_tab)',tabs).remove();
                                $('div.tab-pane:not(#inventory_attachements)',content).remove();
                                //tabs.children().not(':last').remove();
                                //content.children().not(':last').remove();
                                no_reference_notice.show();
                                inventory_transactions.addClass('hidden');
                            }
                            swal('Inventory Transaction','Transaction has been Cancelled!','success');
                        } else {
                            swal('Inventory Transaction','Failed to cancel transaction!','error');
                        }
                    });
                }
            });
        });

        $(document).on('click','#btn_delete_reference,.close-tab',function () {
            var this_ = $(this);
            var trnref,pane,paneid,tab_link,tab;

            if (this_.is('a')) {
                //CLOSE ON BUTTON AT THE END
                trnref = $('i',this_).attr('data-id');
                pane = this_.closest('.tab-pane');
                paneid = pane.attr('id');
                tab_link = $('#inv_reference_tabs', document).find('a[href="#' + paneid + '"]');
                tab = tab_link.closest('li');
            }

            if (this_.is('i')) {
                //CLOSE ON TAB BUTTON
                trnref = this_.attr('data-id');
                tab = this_.closest('li');
                tab_link = $('a',tab);
                paneid = tab_link.attr('href');
                pane = $(paneid);
            }

            var ref = tab_link.text();

            //MAKE SWAL
            swal({
                title: "Remove " + ref + "?",
                text: 'Are you sure you want to remove ' + ref + ' from this transaction?',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url : PECO.base_url() + 'inventory/deletetransactionreference',
                        type : 'post',
                        dataType : 'json',
                        data : {
                            referenceid : trnref,
                            refname : ref
                        }
                    }).done(function (d) {
                        if (d.qry) {
                            if (pane.is(':last-child')) {
                                pane.prev().addClass('active in');
                                tab.prev().addClass('active');
                            } else {
                                pane.next().addClass('active in');
                                tab.next().addClass('active');
                            }
                            pane.remove();
                            tab.remove();
                            var tabs = $('li',inv_reference_tabs).length;

                            if (tabs < 2) {
                                inventory_transactions.addClass('hidden');
                                $('#no_reference_notice',document).show();
                            }
                        }
                        swal(d.title,d.msg,d.func);
                    });
                }else{
                    swal.close();
                }
            });
        });

        $(document).on('click','#btn_submit_trn',function () {
            //CHECK IF ALL REFERENCE HAS AT LEAST 1 DATA.
            var this_ = $(this);
            var invtrnid = this_.attr("data-trnid");
            var flowid = this_.attr("data-flowid");
            var stageid = this_.attr("data-stageid");
            $.ajax({
                url : PECO.base_url() + 'inventory/checkiventoryitems',
                type : 'post',
                dataType: 'json',
                data: {
                    trnid : trnid
                }
            }).done(function (d) {
                if (d.proceed) {
                    swal({
                        title: "Send transaction for approval?",
                        text: 'Please verify that all documents are complete before sending.',
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes!",
                        closeOnConfirm: false,
                        closeOnCancel: false,
                        showLoaderOnConfirm: true
                    }, function(isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                url : PECO.base_url() + 'inventory/submittrn',
                                type : 'post',
                                dataType : 'json',
                                data : {
                                    trnid : trnid,
                                    invtrnid : invtrnid,
                                    flowid : flowid,
                                    stageid : stageid
                                }
                            }).done(function (d) {
                                swal(d.title,d.msg,d.func);
                            });
                        }else{
                            swal.close();
                        }
                    });
                } else {
                    swal('Blank Reference','One or more documents has no items quantified. Please provide quantity or remove the document.','error');
                }
            });
        });

        $(document).on('click','#btn_print_form',function () {
            var this_ = $(this);
            var trnid = this_.attr('data-id');
            var refid = this_.attr('data-ref');

            $.ajax({
                url : PECO.base_url() + 'printer/inventoryform',
                type : 'post',
                dataType : 'json',
                data : {
                    trnid : trnid,
                    refid : refid
                }
            }).done(function (d) {
                PECO.pdfPreview(d.title,d.html,d.papersize);
            })
        });
    };

    var init_references = function (trntype) {
        //dt_inventory_reference_list(trntype);
        referrence_lookup(trntype);

        var frm_add_reference = $('#frm_add_reference',document);
        var inventory_trn_fields = $('#inventory_trn_fields',document);
        var inventory_transactions = $('#inventory_transactions',inventory_trn_fields);
        var inv_reference_tabs = $('#inv_reference_tabs',inventory_transactions);
        var attachment_tab = $('#attachment_tab',inv_reference_tabs);
        var inv_reference_content = $('#inv_reference_content',inventory_transactions);
        var attachment_content = $('#inventory_attachements',inv_reference_content);

        frm_add_reference.on('submit',function (e) {
            e.preventDefault();
            var team_selection = $('#installation_team_selection',document);
            var submit = [];
            if (!team_selection.hasClass('hidden')) {
                if ($('input[type="checkbox"]').filter(function () {
                    return this.checked && !this.disabled;
                }).length > 0) {
                    submit.push(true);
                } else {
                    submit.push(false);
                    alert('Please select at least one team.');
                }
            }
            //var contents = $('div.tab-pane',inv_reference_content).length;
            var this_ = $(this);

            if (!submit.includes(false)) {
                $.ajax({
                    url: this_.attr('action'),
                    type: this_.attr('method'),
                    dataType: 'json',
                    data: this_.serialize()
                }).done(function (d) {
                    //Create tab list
                    //Create unique reference divs and table
                    //load datatable for each tab
                    attachment_tab.before(d.tab);
                    attachment_content.before(d.content);
                    if (typeof d.tableid === 'string') {
                        setTimeout(function () {
                            dt_inventory_item_list(d.tableid);
                        }, 1000);
                    } else {
                        $.each(d.tableid, function (i, table) {
                            setTimeout(function () {
                                dt_inventory_item_list(table);
                            }, 1000);
                        });
                    }

                    var tabs = $('li',inv_reference_tabs).length;
                    console.log(tabs);
                    if (tabs > 1) {
                        $('#inv_reference_tabs li:first').addClass('active');
                        $('#inv_reference_content div.tab-pane:first').addClass('active').show();
                        inventory_transactions.removeClass('hidden');
                        $('#no_reference_notice', document).hide();
                    }
                }).fail(function () {

                });
            }
        });
    };

    var dt_inventory_reference_list = function (trntype) {
        var tbl_reference_list = $('#tbl_reference_list',document);
        if (trntype) {
            $.ajax({
                url : PECO.base_url() + 'inventory/dtreferencelist',
                type : 'post',
                dataType : 'json',
                data : {
                    trntype : trntype
                }
            }).done(function (d) {
                if (Object.keys(d.columns).length > 0) {
                    $.each(d.columns, function (i, v) {
                        $('thead', tbl_reference_list).append('<th class="' + v.sClass + '">' + v.sTitle + '</th>');
                    })
                }
                //PECO.DTDefault(tbl_reference_list, 'No reference found for this transaction.');
                setTimeout(
                    function () {
                        tbl_reference_list.DataTable({
                            bDestroy: true,
                            bPaginate: true,
                            bFilter: true,
                            bInfo: true,
                            bStateSave: true,
                            aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                            language: PECO.DTEmptyMessage('No reference found for this transaction.'),
                            aoColumns: d.columns,
                            //searchHighlight: true,
                            fnRowCallback: function (nRow, data, index) {
                                PECO.iCheckRow($('.icheck', nRow), 'square', 'blue');
                            }
                        });
                },1000);
                $('#modal_footer',document).append(d.btnSubmit);
            }).fail(function () {
                PECO.DTphpError(tbl_reference_list);
            });
        }
    };

    var referrence_lookup = function (trntype) {
        var frm_add_reference = $('#frm_add_reference',document);
        var refid = $('#refid', frm_add_reference);
        if (trntype === 23) {
            var inv_reference_po = $('#inv_reference_po', frm_add_reference);
            PECO.DTDefault($('#tbl_po_items', document), 'Please select a supplier.');
            $('#btn_add_po', frm_add_reference).attr('disabled', true);
            var a = new Bloodhound({
                datumTokenizer: function (e) {
                    return e.tokens
                },
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {url: PECO.base_url() + "inventory/polookup?query=%QUERY", wildcard: "%QUERY"}
                //local : $.parseJSON(list)
            });

            a.initialize(), inv_reference_po.typeahead(null, {
                hint: false,
                highlight: true,
                minLength: 0,
                displayKey: "ponum",
                source: a.ttAdapter(),
                cache: false,
                templates: {
                    suggestion: Handlebars.compile(['<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{ponum}}</b></h5>', '</div>'].join("")),
                },
            }).on('typeahead:selected', function (event, selection) {
                console.log(selection);
                refid.val(selection.id);
                inv_reference_po.val(selection.ponum);
                reference_po_details(selection.id)
                $('#btn_add_po', frm_add_reference).attr('disabled', false);
            }).click(function () {
                PECO.initElScroller($('.tt-dropdown-menu', document));
            });
        }

        if (trntype === 24) {
            var inv_reference_install = $('#inv_reference_install', frm_add_reference);
            PECO.DTDefault($('#tbl_installation_items', document), 'Please select a customer.');
            $('#btn_add_installation', inv_reference_install).attr('disabled', true);
            var a = new Bloodhound({
                datumTokenizer: function (e) {
                    return e.tokens
                },
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {url: PECO.base_url() + "inventory/cadlookup?query=%QUERY", wildcard: "%QUERY"}
                //local : $.parseJSON(list)
            });

            var responseLayout = [
                '<div class="media">',
                '<div class="media-body">',
                '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{appnum}}</b> - {{appname}}</h5>',
                "<p>{{address}}</p>",
                "</div>",
                "</div>"
            ];

            a.initialize(), inv_reference_install.typeahead(null, {
                hint: false,
                highlight: true,
                minLength: 1,
                displayKey: "appnum",
                source: a.ttAdapter(),
                cache: false,
                templates: {
                    suggestion: Handlebars.compile(responseLayout.join("")),
                },
            }).on('typeahead:selected', function (event, selection) {
                console.log(selection);
                refid.val(selection.sysid);
                inv_reference_install.val(selection.appnum);
                reference_installation_details(selection.sysid)
                $('#btn_add_installation', frm_add_reference).attr('disabled', false);
            }).click(function () {
                PECO.initElScroller($('.tt-dropdown-menu', document));
            });
        }
    };

    var active_po_list = function () {
        var po_list = null;
        $.ajax({
            url : PECO.base_url() + 'inventory/activepolist',
            type : 'post',
            dataType : 'json',
            async : false,
            success: function (d) {
                po_list = Object.keys(d.list).map(key => {
                    let ar = ObjectOfObjects[key]

                    // Apppend key if one exists (optional)
                    ar.key = key

                    return ar
                });
            }
        });
        return po_list;
    };

    var reference_po_details = function (poid) {
        var tbl_po_items = $('#tbl_po_items',document);
        var po_supplier_name = $('#po_supplier_name',document);
        var po_supplier_address = $('#po_supplier_address',document);
        var po_supplier_item = $('#po_supplier_item',document);
        $.ajax({
            url: PECO.base_url() + 'inventory/poinfo',
            type : 'post',
            dataType : 'json',
            data : {
                poid: poid
            }
        }).done(function (d) {
            po_supplier_name.text(d.supplier);
            po_supplier_address.text(d.address);
            po_supplier_item.text(d.items);
            tbl_po_items.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bSort: false,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: PECO.DTEmptyMessage('No items found for this PO.'),
                aoColumns: d.columns,
            });
        })
    };

    var reference_installation_details = function (appid) {
        var tbl_po_items = $('#tbl_po_items',document);
        var installation_customer_name = $('#installation_customer_name',document);
        var installation_customer_address = $('#installation_customer_address',document);
        var installation_customer_build = $('#installation_customer_build',document);
        var installation_customer_number = $('#installation_customer_number',document);
        var selec2_installation_template = $('#selec2_installation_template',document);
        var inv_rr_date = $('#inv_rr_date',document);

        $.ajax({
            url: PECO.base_url() + 'inventory/appinfo',
            type : 'post',
            dataType : 'json',
            data : {
                appid: appid
            }
        }).done(function (d) {
            installation_customer_name.text(d.appname);
            installation_customer_number.text(d.appnumber);
            installation_customer_address.text(d.address);
            installation_customer_build.html(d.systemsizename);
            if (!d.installationitems) {
                //CREATE SELECT2 FOR TEMPLATES LIST
                $('#install_template_selection').removeClass('hidden')
                selec2_installation_template.attr('disabled', false);
                PECO.select2Basic(selec2_installation_template, 'inventory/select2installationtemplate')
            }
            if (!d.installationteam) {
                $('#installation_team_selection').removeClass('hidden').find('input').each(function () {
                    var this_ = $(this);
                    this_.attr('disabled',false);
                    this_.iCheck({
                        checkboxClass: this_.attr('data-checkbox'), // minimal / square / polaris / futurico // red / green / blue
                        increaseArea: '20%' // optional
                    }).on('ifChecked', function () {
                        var this_i = $(this);
                        this_i.attr('checked', true);
                    }).on('ifUnchecked', function () {
                        var this_i = $(this);
                        this_i.attr('checked', false);
                    });
                });
                PECO.iCheckRow()
            } else {
                $('#installation_team').text(d.installationteam);
            }
            if (d.installationdate) {
                inv_rr_date.val(d.installationdate);
            }
        });
        
        selec2_installation_template.on('change',function () {
            var this_ = $(this);
            dt_installation_template_items(this_.val());
        });
    };
    
    function dt_installation_template_items(template) {
        if (template > 0) {
            $('#template_item_list', document).removeClass('hidden');
        } else {
            $('#template_item_list', document).addClass('hidden');
        }

        $.ajax({
            url: PECO.base_url() + 'inventory/dtinstallationtemplateitems',
            type : 'post',
            dataType : 'json',
            data : {
                templateid : template,
            }
        }).done(function (d) {
            var empty = 'No items allocated for this list.'
            $('#tbl_installation_components').DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bSort: false,
                aaData: d.componentlist, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: PECO.DTEmptyMessage(empty),
                aoColumns: d.columns,
            });
            $('#tbl_installation_accessories').DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bSort: false,
                aaData: d.accessorylist, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: PECO.DTEmptyMessage(empty),
                aoColumns: d.columns,
            });
            $('#tbl_installation_optional').DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bSort: false,
                aaData: d.optionallist, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: PECO.DTEmptyMessage(empty),
                aoColumns: d.columns,
            });
            $('#tbl_installation_others').DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bSort: false,
                aaData: d.otherlist, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: PECO.DTEmptyMessage(empty),
                aoColumns: d.columns,
            });
        })
    }
    
    var dt_inventory_item_list = function (table) {
        var tbl = $('#' + table,document);
        var id = tbl.attr('data-id');
        var type = tbl.attr('data-type');
        var trnid = tbl.attr('data-trn');
        var itemtype = tbl.attr('data-itemtype');
        var dt;
        var parent_box = tbl.closest('div.panel');

        var params = {
            dataid : id,
            datatype : type,
            trnid : trnid
        }

        if (typeof itemtype !== 'undefined' && itemtype !== false) {
            params.itemtype = itemtype;
        }

        $.ajax({
            url : PECO.base_url() + 'inventory/dtinventorytrnitems',
            type : 'post',
            dataType : 'json',
            data : params
        }).done(function (d) {
            if (typeof d.list !== 'undefined' && d.list.length > 0) {
                parent_box.removeClass('hidden');
            }
            dt = tbl.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bSort: false,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: PECO.DTEmptyMessage('No items found for this list.'),
                aoColumns: d.columns,
                fnRowCallback: function(nRow, aData) {
                    PECO.dtEllipsisBtn(nRow);
                }
            });
        }).fail(function () {

        });

        tbl.on('focus','input,textarea',function () {
            var this_ = $(this);
            var this_td = this_.closest('td');
            var style = (typeof this_td.attr('style') !== 'undefined' ? this_td.attr('style') : '');
            this_td.attr('style', style + 'border-bottom: 1px solid black !important;');
        }).on('focusout','input,textarea',function () {
            var this_ = $(this);
            var this_td = this_.closest('td');
            var style = (typeof this_td.attr('style') !== 'undefined' ? this_td.attr('style') : '');
            this_td.attr('style', style.replace('border-bottom: 1px solid black !important;',''));
        });

        //KEYBOARD EVENT FOR EACH TABLE
        tbl.on('keydown','input,textarea',function (e) {
            var this_ = $(this);
            var keyCode = e.keyCode || e.which;
            var this_row = this_.closest('tr');
            var next_row = this_row.next();
            var prev_row = this_row.prev();
            var save = false;
            if (keyCode == 13) {
                if (this_.is('input#input_qty')) {
                    if (e.shiftKey) {
                        save = save_inventory_trn_item(this_);
                        if (save) {
                            $('#input_qty', next_row).focus();
                        }
                    } else {
                        if ($('#input_additional',this_row).length > 0) {
                            $('#input_additional',this_row).focus();
                        } else {
                            $('#input_itemremarks',this_row).focus();
                        }
                    }
                    return false;
                }
                if (this_.is('input#input_additional')) {
                    if (e.shiftKey) {
                        save = save_inventory_trn_item(this_);
                        if (save) {
                            $('#input_qty', next_row).focus();
                        }
                    } else {
                        if ($('#input_returned',this_row).length > 0) {
                            $('#input_returned',this_row).focus();
                        }
                    }
                    return false;
                }
                if (this_.is('input#input_returned')) {
                    if (e.shiftKey) {
                        save = save_inventory_trn_item(this_);
                        if (save) {
                            $('#input_qty', next_row).focus();
                        }
                    } else {
                        $('#input_itemremarks',this_row).focus();
                    }
                    return false;
                }
                if (this_.is('textarea#input_itemremarks') && e.shiftKey) {
                    save = save_inventory_trn_item(this_);
                    if (save === true) {
                        $('#input_qty', next_row).focus();
                    }
                    return false;
                    e.preventDefault();
                }
            }

            if (keyCode == 9) {
                if (this_.is('textarea#input_itemremarks')) {
                    if (!e.shiftKey) {
                        save = save_inventory_trn_item(this_);
                        if (save) {
                            $('#input_qty', next_row).focus();
                        }
                        e.preventDefault();
                    }
                }

                if (e.shiftKey) {
                    if (this_.is('input#input_qty')) {
                        $('#input_qty', prev_row).focus();
                        return false;
                    }
                }
            }
        });
        
        tbl.on('click','#btn_clear_item',function () {
            var this_ = $(this);
            var this_row = this_.closest('tr');
            var this_table = this_row.closest('table');
            var item_id = $('#inventory_item_id',this_row);
            var utilized_qty = $('#item_utilized_qty',this_row);

            if (item_id.val() > 0 || parseFloat(utilized_qty.text()) > 0) {
                var values = $('td:first :input',this_row).serializeArray();
                values.push({name :'trntype', value: this_table.attr('data-type')})
                var params = $.param(values);
                //console.log({table : table,values: values,params: params});
                swal({
                    title: "Remove item from list?",
                    text: 'Are you sure you want to remove item quantities and remarks?',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes!",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url : PECO.base_url() + 'inventory/removeinventoryitem',
                            type : 'post',
                            dataType : 'json',
                            data : params
                        }).done(function (d) {
                            if (d.qry) {
                                /*$('#inventory_item_id',this_row).val('');
                                $('#input_qty',this_row).val('');
                                $('#rcv_item_remarks',this_row).val('');
                                $('input:not([type=hidden])',this_row).val('');*/
                                dt.row(this_row).remove().draw();

                                setTimeout(function () {
                                    var rowCnt = tbl.find('tbody tr:not(:has(td.dataTables_empty))').length;
                                    if (rowCnt === 0) {
                                        parent_box.addClass('hidden');
                                    }
                                },500);
                            }
                            swal(d.msg,d.title,d.func);
                        });
                    }else{
                        swal.close();
                    }
                });
            }
        });

        tbl.on('click','#btn_save_item',function () {
            var this_ = $(this);
            save_inventory_trn_item(this_);
        });
    };

    var save_inventory_trn_item = function (this_) {
        var result = false;
        var this_row = this_.closest('tr');
        var this_table = this_.closest('table');
        var item_id = $('#inventory_item_id',this_row);
        var item_utilized_qty = $('#item_utilized_qty',this_row);
        var utilized = item_utilized_qty.text();
        console.log(item_id);
        var params = {};
        params['referenceid'] = this_table.attr('data-id');
        params['trntype'] = this_table.attr('data-type');
        params['trnid'] = this_table.attr('data-trn');
        var inputCnt = 0,inputVal = 0;
        this_row.find('input,textarea').each(function () {
            var name = $(this).attr('name');
            params[name] = $(this).val();
            if ($(this).is('input:not([type=hidden])')) {
                inputCnt += 1;
                if ($(this).val() > 0) {
                    inputVal += 1;
                }
            }
        });

        if (inputCnt > 0 && inputVal > 0) {
            $.ajax({
                url: PECO.base_url() + 'inventory/savetrnitemqty',
                type: 'post',
                dataType: 'json',
                data: params,
                async: false,
            }).done(function (d) {
                PECO.initAlerts(d.msg, d.title, d.func);
                if (d.qry) {
                    if (typeof d.newitemid !== 'undefined') {
                        console.log('itemid : ' + d.newitemid);
                        item_id.val(d.newitemid);
                    }
                    utilized = 0;
                    this_row.find('input[type=number]').each(function () {
                        var val = parseFloat($(this).val());
                        if ($(this).attr('id') === 'input_returned') {
                            val = -val;
                        }
                        if ($.isNumeric(val)) {
                            utilized += val;
                        }
                    });
                    item_utilized_qty.text(utilized);
                    result = true;
                }
            }).fail(function () {
                PECO.phpError();
            });
        } else {
            result = true;
        }

        return result;
    }

    var init_inventory_trn_items = function (trnid) {
        var inventory_trn_fields = $('#inventory_trn_fields',document);
        var no_reference_notice = $('#no_reference_notice',inventory_trn_fields);
        var inventory_transactions = $('#inventory_transactions',inventory_trn_fields);
        var tabs = $('#inv_reference_tabs',inventory_transactions);
        var attachment_tab = $('#attachment_tab',tabs);
        var content = $('#inv_reference_content',inventory_transactions);
        var attachments = $('#inventory_attachements',content);


        $.ajax({
            url : PECO.base_url() + 'inventory/gettransactionitems',
            type : 'post',
            dataType : 'json',
            data : {
                trnid : trnid
            }
        }).done(function (d) {
            if (Object.keys(d.tabs).length > 0) {
                $.each(d.tabs,function (key,val) {
                    attachment_tab.before(val);
                });

                $.each(d.contents,function (key,val) {
                    attachments.before(val);
                });

                $.each(d.tableids,function (key,val) {
                    if (typeof val === 'string') {
                        setTimeout(function () {
                            dt_inventory_item_list(val);
                        },1000);
                    } else {
                        $.each(val,function (i,table) {
                            setTimeout(function () {
                                dt_inventory_item_list(table);
                            },500);
                        });
                    }
                });

                no_reference_notice.hide();

                var tabs_cnt = $('#inv_reference_tabs li').length;
                console.log(tabs_cnt);
                $('#inv_reference_tabs li:first').addClass('active');
                $('#inv_reference_content div.tab-pane:first').addClass('active').show();
                inventory_transactions.removeClass('hidden');
                $('#btn_submit_trn',document).attr('disabled',false);

                $(document).find('.btn_refresh_items_table').each(function () {
                    var this_ = $(this);
                    var tableid = this_.attr('data-table');
                    this_.on('click',function () {
                        dt_inventory_item_list(tableid);
                    });
                });
                adjustTabs('#inv_reference_tabs');
            } else {
                //inventory_transactions.addClass('hidden');
                //no_reference_notice.show();
            }
        }).fail(function () {

        });
    };

    var adjustTabs = function (ul) {
        let parent = $(document).find(ul+'.nav-tabs');
        let $tabs = $("li:not(#attachment_tab)",parent);
        let tabCount = $tabs.length;
        let parentWidth = parent.width() - (parent.width()*0.2);
        let tabWidth = ((parentWidth / tabCount)/parentWidth)*100; // Calculate each tab's width
        console.log({ul:parent,tabs:$tabs,parentWidth: parent.width(), maxTabSpace:parentWidth, tabCount: tabCount, tabWidth: tabWidth});
        let minTabWidth = 25; // Set a threshold for when to shorten text

        if (tabCount > 4) {
            $tabs.each(function () {
                let $link = $(this).find('a');
                let fullText = $link.attr("data-full-text"); // Store full text
                let span = $('span',$link);
                let shortname = $link.attr('data-short-name');

                if (!fullText) {
                    fullText = span.text().trim();
                    $link.attr("data-full-text", fullText); // Save original text
                }

                // Shorten text if tab width is too small
                if (tabWidth < minTabWidth) {
                    if (shortname.length > 0) {
                        span.text(shortname); // Show first 3 chars
                        var newTitle = $link.attr('title') + '(' + fullText.split(' ')[0] + ')';
                        $link.attr('title', newTitle);
                    } else {
                        span.text(fullText.substring(0, 6) + "..."); // Show first 6 chars
                    }
                } else {
                    $link.text(fullText); // Restore full text
                }
            });

            $tabs.css("width", (80 / tabCount) + "%");
        }
    }

    var init_inventory_trn = function (route) {
        dt_inventory_transaction_list(route);
    };

    var dt_inventory_transaction_list = function (route) {
        var tbl_inv_trn_list = $('#tbl_inv_trn_list',document);

        $.ajax({
            url : PECO.base_url() + 'inventory/dtinventorytransactionlist',
            type : 'post',
            dataType : 'json',
            data : {
                route : route,
            }
        }).done(function (d) {
            tbl_inv_trn_list.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bSort: false,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: PECO.DTEmptyMessage('No Inventory transactions are being processed.'),
                aoColumns: d.columns,
            });
        }).fail(function () {

        });
    };

    var dt_approved_invtrn_list = function () {
        var tbl_inv_trn_list = $('#tbl_inv_trn_list',document);

        $.ajax({
            url : PECO.base_url() + 'inventory/approvedtrnlist',
            type : 'post',
            dataType : 'json',
        }).done(function (d) {
            tbl_inv_trn_list.DataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: false,
                bInfo: false,
                bStateSave: true,
                bSort: false,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: PECO.DTEmptyMessage('No Approved Inventory Transactions.'),
                aoColumns: d.columns,
            });
        }).fail(function () {

        });
    };

    var init_transaction_approval = function(dataid) {
        init_inventory_trn_items(dataid);

        $('#btn_approve_inv',document).on('click',function () {
            //APPROVE TRANSACTION. CHANGE STATUS OR ITEMS IN INVENTORY
            var this_ = $(this);
            var trnid = this_.attr('data-trnid');
            var flowid = this_.attr('data-flowid');
            var stageid = this_.attr('data-stageid');
            swal({
                title: "Are you sure?",
                text: 'Approve Inventory Transaction?',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Approve!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url : PECO.base_url() + 'inventory/approvetrn',
                        type : 'post',
                        dataType : 'json',
                        data : {
                            dataid : dataid,
                            trnid : trnid,
                            flowid : flowid,
                            stageid : stageid
                        }
                    }).done(function (d) {
                        //PECO.initAlerts(d.msg,d.title,d.func);
                        PECO.sweetNotif(d.title,d.msg,d.func);
                        if (d.qry) {
                            $('#trn_button_group', document).html(d.approved);
                            $('#inventory_transactions :input').attr('disabled',true);
                        }
                    }).fail(function () {
                        PECO.sweetNotif('Error!!!','PHP Error Occurred!','error');
                    });
                } else {
                    swal.close();
                }
            });
        });

        $('#btn_disapprove_inv',document).on('click',function () {
            //DISAPPROVE TRANSACTION. CHANGE STATUS OF ITEMS TO 303.
            var this_ = $(this);
            var trnid = this_.attr('data-trnid');
            var flowid = this_.attr('data-flowid');
            var stageid = this_.attr('data-stageid');
            $.ajax({
                url : PECO.base_url() + 'inventory/disapprovetrn',
                type : 'post',
                dataType : 'json',
                data : {
                    dataid : dataid,
                    trnid : trnid,
                    flowid : flowid,
                    stageid : stageid
                }
            }).done(function (d) {
                PECO.initAlerts(d.msg,d.title,d.func);
                if (d.qry) {
                    $('#trn_button_group', document).html(d.approved);
                    $('#inventory_transactions :input').attr('disabled',true);
                }
            }).fail(function () {
                PECO.phpError();
            });
        });
    }

    var frm_add_item = function (refTable,refID) {
        var frm = $('#'+refTable+'_add_spsitem',document);
        var frmUnit = $('#input_new_item_unit',frm);
        var frmType = $('#input_new_item_type',frm);
        var frmItem = $('#input_newitem',frm);
        var itemid = $('#itemid',frm);

        PECO.select2Basic(frmUnit,'query/getunits','Unit...',false,false,frmUnit.val());
        frmType.select2({
            allowClear: true,
            placeholder: 'Item Type...'
        });

        var a = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: PECO.base_url() + "search/eprsitemsearch?query=%QUERY",
                wildcard: "%QUERY"
            }
        });

        a.initialize(), frmItem.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "desc",
            source: a.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media-body">','<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{desc}}</b></h5>','</div>'].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {
            itemid.val(selection.id);
            frmItem.val(selection.desc);
            frmUnit.val(selection.unitid);
            frmUnit.trigger('change.select2');
        }).click(function() {
            PECO.initElScroller($('.tt-dropdown-menu', document));
        });

        frm.on('submit', function (e) {
            e.preventDefault();
            var this_ = $(this);

            $.ajax({
                url : this_.attr('action'),
                type : this_.attr('method'),
                dataType : 'json',
                data : this_.serialize() + '&' + $.param({appid : refID})
            }).done(function (d) {
                PECO.initAlerts(d.msg,d.title,d.func);
                if (d.qry) {
                    var tbl = refTable + '_' + d.itemtype;
                    dt_inventory_item_list(tbl);
                    this_.find('input').each(function () {
                        $(this).val('');
                    });
                    frmUnit.trigger('change.select2');
                    frmType.select2('val','');
                }
            });
        });

        frm.on('reset',function (){
            frm.find('input').each(function () {
                $(this).val('');
            });

            frmUnit.trigger('change.select2');
            frmType.select2('val','');
        })
    }

    var serial_frm = function (itemid,appid) {
        var frm_add_serial = $('#frm_add_serial', document);
        if (itemid > 0 && appid > 0) {
            frm_add_serial.on('submit', function (e) {
                e.preventDefault();
                var this_ = $(this);
                var itemqty = $('#itemqty', this_).val();
                var sn_textbox = $('#sn_text', this_);
                var sn_text = sn_textbox.val();
                //console.log(sn_textbox,sn_text.length);
                if (sn_text.length > 0) {
                    var sn_ = sn_text.replace(/\n|\t|\r/g, ",");
                    var sn_arr = sn_.split(",");
                    var sn = sn_arr.filter(x => !!x);
                    console.log(sn_,sn_arr,sn);
                    if (sn.length > 0) {
                        console.log(sn.length,itemqty);
                        if (sn.length === parseFloat(itemqty)) {
                            //SUBMIT FORM
                            $.ajax({
                                url: this_.attr('action'),
                                type: this_.attr('method'),
                                dataType: 'json',
                                data: {
                                    appid : appid,
                                    itemid : itemid,
                                    serials : sn
                                }
                            }).done(function (d) {
                                PECO.initAlerts(d.msg,d.title,d.func);
                                if (d.qry) {
                                    dt_inventory_item_list()
                                }
                            })
                        } else {
                            PECO.initAlerts('Serial count is not the same as the current item count.','Serial Count','warning');
                        }
                    } else {
                        PECO.initAlerts('Serial is empty','Serial Count','error');
                    }
                }
            })
        } else {
            frm_add_serial.attr('disabled',true);
        }
    }

    return {
        init: function() {
            init_inventory();
        }, products: function() {
            init_products();
        }, search: function() {
            search_handler();
        }, stocks: function(dataid) {
            stocks_handler(dataid);
        }, stocksin: function() {
            stocks_in_hanlder();
        }, stockinentry: function() {
            stocks_entry_window();
        }, stockgeneratecode: function() {
            stock_generate_code();
        }, dataEntry: function (dataid) {
            init_new_invtrn(dataid);
        }, references: function (trntype) {
            init_references(trntype);
        }, inventoryTrn: function (route) {
            if (route && !$.isNumeric(route) && route === 'approved') {
                dt_approved_invtrn_list();
            } else {
                init_inventory_trn(route);
            }
        }, trnApproval: function (dataid) {
            init_transaction_approval(dataid);
        }, itemForm: function (table,refid) {
            frm_add_item(table,refid);
        }, serial: function (itemid,appid) {
            serial_frm(itemid,appid);
        }
    }
}();