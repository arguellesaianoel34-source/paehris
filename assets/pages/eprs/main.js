var EPRS = function () {
    var tbl_po_items = $('#tbl_po_items',document);
    var dtEmptyMsg = 'No items listed.';
    var po_items = tbl_po_items.DataTable({
        bDestroy: true,
        bPaginate: false,
        bFilter: true,
        bInfo: true,
        bStateSave: true,
        bProcessing: true,
        //aaData: d.itemlist, // USE FOR DYNAMIC LOCAL TABLE LIST
        language: PECO.DTEmptyMessage(dtEmptyMsg),
        aoColumns: [
            {"data": "num", sClass:'number', sWidth: '25px'},
            {"data": "item"},
            {"data": "qty", sClass:'editable_number number', sWidth: '80px'},
            {"data": "unit", sClass: 'editable_unit', sWidth: '50px'},
            {"data": "remarks", sClass: 'editable_remarks', sWidth: '25%'},
            {"data": "control", sClass: 'text-align-center', sWidth: '100px'}
        ],
        searchHighlight: true,
    });

    var new_prf = function (dataid) {
        var dt_po = dt_po_items(dataid);
        if (dt_po === false) {
            dt_prf_items_list(dataid);
        }
        items_bloodhound();
        PECO.select2Basic($('#unitid', document),'query/getunits','Unit...',false,false,$('#unitid', document).val());
        prf_handler(dataid);
    };

    var prf_handler = function (dataid) {
        var frm_add_prf_item = $('#frm_add_prf_item',document);
        var param = dataid ? '&' + $.param({prfid : dataid}) : '';
        frm_add_prf_item.on('submit',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url : this_.attr('action'),
                type : this_.attr('method'),
                dataType : 'json',
                data : this_.serialize() +  param
            }).done(function (d) {
                console.log(d);
                PECO.initAlerts(d.msg,'Add Item!',d.func);
                if (d.qry) {
                    var tbody = $('tbody',tbl_po_items);
                    var rowcount = $('tr',tbody).length;
                    if ($('td.dataTables_empty',tbl_po_items).length) {
                        rowcount = 0;
                        //po_items.row($('td.dataTables_empty').parents('tr')).remove().draw();
                        $('td.dataTables_empty',tbl_po_items).remove();
                    }
                    //console.log(rowcount);
                    po_items.row.add({
                        num: (rowcount+1) + d.item.prsitem,
                        item: d.item.desc,
                        qty: d.item.qty,
                        unit: d.item.unit,
                        remarks: d.item.remarks,
                        control: d.item.controls
                    }).draw();

                    this_.trigger('reset');
                }
            }).fail(function () {
                PECO.initAlerts('Failed to add item!', 'FAILED!', 'error');
            });
        });

        $('#btn_prf_draft',document).on('click',function (e) {
            //make swal
            swal({
                title: "Save as Draft?",
                text: "PRF will be saved as draft.",
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
                        url : PECO.base_url() + 'purchasing/saveprfdraft',
                        type : 'post',
                        dataType : 'json',
                        data : {
                            justification : $('#prf_justification',document).val()
                        }
                    }).done(function (d) {
                        swal(d.title, d.msg, d.func);
                        dt_po_items();
                    }).fail(function () {
                        swal('FAIL!','Failed to execute function.','error');
                    });
                }else{
                    swal.close();
                }
            });
        });

        $('#btn_prf_delete',document).on('click',function () {
            var this_ = $(this);
            var prfid = this_.attr('data-id');
            //alert('prfid : ' + prfid);
            swal({
                title: "Are you sure?",
                text: "All PRF items will be removed.",
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
                        url : PECO.base_url() + 'purchasing/discardprf',
                        type : 'post',
                        dataType : 'json',
                        data : {
                            prfid : prfid
                        }
                    }).done(function (d) {
                        swal(d.title, d.msg, d.func);
                        //swal('Test', 'Test', 'success');
                        if (d.qry === true) {
                            dt_po_items();
                        }
                    }).fail(function () {
                        swal('FAIL!','Failed to execute function.','error');
                    });
                }else{
                    swal.close();
                }
            });
        });

        $('#btn_prf_approval',document).on('click',function () {
            var this_ = $(this);
            var prfid = this_.attr('data-id');
            var justification = $('#prf_justification',document).val();
            //alert('prfid : ' + prfid);
            swal({
                title: "Send for Approval?",
                text: "This will send PRF for HCS' approval.",
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
                        url : PECO.base_url() + 'purchasing/sendprfapproval',
                        type : 'post',
                        dataType : 'json',
                        data : {
                            prfid : prfid,
                            justification : justification
                        }
                    }).done(function (d) {
                        swal(d.title, d.msg, d.func);
                        //swal('Test', 'Test', 'success');
                        if (d.qry === true) {
                            dt_po_items();
                        }
                    }).fail(function () {
                        swal('FAIL!','Failed to execute function.','error');
                    });
                }else{
                    swal.close();
                }
            });
        });

        tbl_po_items.on('click','#prf_item_edit',function () {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_td = this_.closest('td');
            var qty = $('.editable_number',this_tr);
            $('#prf_qty',qty).attr('hidden',true);
            qty.html(qty.html() + '<input type="number"  style="width: 100% !important;" class="form-control" id="prf_item_qty" name="qty" value="'+ $('#prf_qty',qty).text() +'">');
            //po_items.cell(qty).data('<input type="number" width="80px" class="form-control" id="prf_item_qty" name="qty" value="'+ qty.text() +'">');

            var unit = $('.editable_unit',this_tr);
            $('#prf_unit_name',unit).attr('hidden',true);
            $('#prf_item_unit',unit).attr('type',false);
            PECO.select2Basic($('#prf_item_unit', unit),'query/getunits','Unit...',false,false,$('#prf_item_unit', unit).val());

            var remarks = $('.editable_remarks',this_tr);
            $('#prf_remarks',remarks).attr('hidden',true);
            remarks.html(remarks.html()+'<textarea id="prf_item_remarks" style="width: 100% !important;" name="remarks" rows="1" class="form-control">'+ $('#prf_remarks',remarks).text() +'</textarea>');
            //po_items.cell(remarks).data('<textarea id="prf_item_remarks" name="remarks" class="form-control">'+ $('#prf_remarks',remarks).text() +'</textarea>');

            this_.attr('id','prf_item_save');
            this_.html('<i class="fa fa-save"></i>');

            var this_cancel = this_.next('button');
            this_cancel.attr('id','prf_cancel_edit');

            po_items.draw();
        });

        tbl_po_items.on('click','#prf_cancel_edit',function () {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var qty = $('.editable_number',this_tr);
            var unit = $('.editable_unit',this_tr);
            var remarks = $('.editable_remarks',this_tr);

            $('#prf_item_qty',qty).remove();
            $('#prf_item_unit', unit).select2('destroy').attr('type','hidden');
            $('#prf_item_remarks',remarks).remove();

            this_tr.find('span').each(function () {
                $(this).attr('hidden',false);
            });

            $('#prf_item_save',this_tr).attr('id','prf_item_edit').html('<i class="fa fa-edit"></i>');
            this_.attr('id','prf_item_delete');
        });

        tbl_po_items.on('click','#prf_item_save',function () {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var prf_item_id = $('#prf_item_id',this_tr).val();
            var prf_item_qty = $('#prf_item_qty',this_tr).val();
            var prf_item_unit = $('#prf_item_unit',this_tr).val();
            var prf_item_remarks = $('#prf_item_remarks',this_tr).val();

            $.ajax({
                url : PECO.base_url() + 'purchasing/saveitemedit',
                type : 'post',
                dataType : 'json',
                data : {
                    id : prf_item_id,
                    qty : prf_item_qty,
                    unit : prf_item_unit,
                    remarks : prf_item_remarks
                }
            }).done(function (d) {
                if (d.qry) {
                    var qty = $('.editable_number',this_tr);
                    var unit = $('.editable_unit',this_tr);
                    var remarks = $('.editable_remarks',this_tr);

                    $('#prf_item_qty',qty).remove();
                    $('#prf_item_unit', unit).select2('destroy').attr('type','hidden');
                    $('#prf_item_remarks',remarks).remove();

                    this_tr.find('span').each(function () {
                        $(this).attr('hidden',false);
                    });

                    this_.attr('id','prf_item_edit').html('<i class="fa fa-edit"></i>');
                    $('#prf_cancel_edit',this_tr).attr('id','prf_item_delete');

                    //console.log('Updated: ' + d.updated.length);
                    if (d.updated !== undefined) {
                        $.each(d.updated,function (key,value) {
                            $('#'+key,this_tr).text(value);
                        });
                    }
                }
                PECO.initAlerts(d.msg,d.title,d.func);
            }).fail(function () {
                PECO.initAlerts('Failed to modify requested item!', 'FAILED!', 'error');
            });

        });

        tbl_po_items.on('click','#prf_item_delete',function () {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var itemid = $('#prf_item_id',this_tr).val();

            swal({
                title: "Remove item?",
                text: "This will remove item from the current list.",
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
                        url : PECO.base_url() + 'purchasing/removeprsitem',
                        type : 'post',
                        dataType : 'json',
                        data : {
                            itemid : itemid
                        }
                    }).done(function (d) {
                        if (d.qry) {
                            swal(d.title, d.msg, d.func);
                            po_items.row(this_tr).remove().draw();
                            /*var tbody = $('tbody',tbl_po_items);
                            var rowcount = $('tr',tbody).length;
                            if (rowcount === 0) {
                            }*/
                            //po_items.draw();
                        }
                    }).fail(function () {
                        swal('FAIL!','Failed to execute function.','error');
                    });
                }else{
                    swal.close();
                }
            });
        });

        $(document).on('click','#btn_refresh_item_list',function () {
            var dt_po = dt_po_items();
            if (dt_po === false) {
                dt_prf_items_list(dataid);
            }
        });

        var justifcation = '';

        $(document).on('click','#btn_edit_justification',function () {
            var this_ = $(this);
            var prf_justification = $('#prf_justification',document);

            prf_justification.attr('disabled',false);
            justifcation = prf_justification.val();
            this_.after('<button type="button" class="btn btn-danger btn-sm pull-right" id="btn_discard_justification"><i class="fa fa-times"></i> Cancel</button>');
            this_.attr('id','btn_save_justification').html('<i class="fa fa-save"></i> Save');
        });

        $(document).on('click','#btn_discard_justification',function () {
            var this_ = $(this);
            var prf_justification = $('#prf_justification',document);
            var btn_save_justification = $('#btn_save_justification',document);

            prf_justification.attr('disabled',true).val(justifcation);
            this_.remove();
            justifcation = '';
            btn_save_justification.attr('id','btn_edit_justification').html('<i class="fa fa-edit"></i> Edit');
        });

        $(document).on('click','#btn_save_justification',function () {
            var this_ = $(this);
            var prf_justification = $('#prf_justification',document);
            var newval = prf_justification.val();
            var btn_discard_justification = $('#btn_discard_justification',document);

            $.ajax({
                url: PECO.base_url() + 'purchasing/editjustification',
                type : 'post',
                dataType : 'json',
                data : {
                    prfid : dataid,
                    justification : newval
                }
            }).done(function (d) {
                PECO.initAlerts(d.msg,'Justification',d.func);
                prf_justification.attr('disabled',true);
                btn_discard_justification.remove();
                this_.attr('id','btn_edit_justification').html('<i class="fa fa-edit"></i> Edit');
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('click','#btn_approve_prf',function () {
            var this_ = $(this);
            var stageid = this_.attr('data-stageid');
            var flowid = this_.attr('data-flowid');
            var trnid = this_.attr('data-trnid');
            var type = this_.attr('data-type');

            swal({
                title: "Send for Approval?",
                text: "This will send PRF for GM's approval.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Yes!",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            },
            function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: PECO.base_url() + 'purchasing/sendprfapproval',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            prfid: dataid,
                            type: type,
                            flowid : flowid,
                            stageid : stageid,
                            trnid : trnid
                        }
                    }).done(function (d) {
                        if (d.qry) {
                            //po_items.row(this_tr).remove().draw();
                            swal('Success!', d.type + ' has been approved!', 'success');
                        } else {
                            swal('Fail!', 'Failed to approve '+ d.type +'!', 'error');
                        }
                    }).fail(function () {
                        swal('FAIL!', 'Failed to execute function.', 'error');
                    });
                }
            });
        });

    };

    var dt_po_items = function (dataid) {
        var param = dataid ? {prfid : dataid} : false;
        var items = false;
        $.ajax({
            url : PECO.base_url() + 'purchasing/dtprfitems',
            type : 'post',
            dataType : 'json',
            data : param,
            async : false
        }).done(function (d) {
            if (d.itemlist && d.itemlist.length > 0) {
                po_items.rows().remove();
                $.each(d.itemlist, function (i, item) {
                    //console.log(item);
                    po_items.row.add(item);
                });
                po_items.draw();
                items = true;
            } else {
                po_items.rows().remove().draw();
            }
        }).fail(function () {
            dtEmptyMsg = '<h4 style="margin: 0px 10px;"><i class="fa fa-times font-red"></i> PHP Error! </h4>';
            po_items.draw();
        });

        return items;
    };

    var items_bloodhound = function () {
        var itemid = $('#itemid', document);
        var item_desc = $('#item_desc', document);
        var unit = $('#unitid', document);

        var a = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/eprsitemsearch?query=%QUERY", wildcard: "%QUERY"}
        });

        a.initialize(), item_desc.typeahead(null, {
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
            item_desc.val(selection.desc);
            unit.val(selection.unitid);
            unit.trigger('change.select2');
        }).click(function() {
            PECO.initElScroller($('.tt-dropdown-menu', document));
        });
    };

    function my_prf() {
        var select2routes = $('#select2routes', document);
        if (select2routes.val() !== '') {
            dt_myprf_list(select2routes.val());
        } else {
            dt_myprf_list();
        }


        dt_myprf_drafts();
        //console.log($('#select2routes', document));
        PECO.select2Basic($('#select2routes', document), 'purchasing/select2routes', 'Select route', true, false,false,false,false,subroute);

        $(document).on('change','#select2routes',function () {
            var this_ = $(this);
            var this_val = this_.val();
            if (this_val === '' && (subroute !== false || subroute !== undefined)) {
                this_val = subroute;
            }
            dt_myprf_list(this_val);
        });
    }

    function dt_myprf_list(subroute) {
        var eprs_trn_list = $('#eprs_trn_list',document);
        var route = (subroute) ? subroute : false;
        //console.log('New route: ' + route);
        $.ajax({
            url : PECO.base_url() + 'purchasing/myprslist',
            type : 'post',
            dataType : 'json',
            data : {
                route : route
            },
            beforeSend: function () {
                PECO.DTphpLoading(eprs_trn_list,'Fetching your PRS list...')
            }
        }).done(function (d) {
            eprs_trn_list.dataTable().empty();
            eprs_trn_list.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: {
                    emptyTable : '<h4><i class="fa fa-warning text-warning"></i> No transaction related records yet!</h4>'
                },
                aoColumns: d.columns,
            });
        }).fail(function () {

        });
    }

    function dt_myprf_drafts() {
        var eprs_trn_draft = $('#eprs_trn_draft',document);
        //console.log('New route: ' + route);
        $.ajax({
            url : PECO.base_url() + 'purchasing/myprsdraft',
            type : 'post',
            dataType : 'json',
            data : false,
            beforeSend: function () {
                PECO.DTphpLoading(eprs_trn_draft,'Fetching your PRS drafts...')
            }
        }).done(function (d) {
            eprs_trn_draft.dataTable().empty();
            eprs_trn_draft.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: {
                    emptyTable : '<h4><i class="fa fa-warning text-warning"></i> No drafts to list!</h4>'
                },
                aoColumns: d.columns,
            });
        }).fail(function () {

        });
    }

    function init_prf(subroute) {
        //dt_prf_list(subroute);
        var select2routes = $('#select2routes', document);
        if (select2routes.val() !== '') {
            dt_prf_list(select2routes.val());
        } else {
            dt_prf_list(subroute);
        }

        /*$('#select2routes', document).change(function(d) {
                    var this_ = $(this);
                    var this_val = this_.val();
                    if (this_val === '' && (subroute !== false || subroute !== undefined)) {
                        this_val = subroute;
                    }
                    dt_prf_list(this_val);
                });*/

        $(document).on('change','#select2routes',function () {
            var this_ = $(this);
            var this_val = this_.val();
            if (this_val === '' && (subroute !== false || subroute !== undefined)) {
                this_val = subroute;
            }
            dt_prf_list(this_val);
        });
    }

    function dt_prf_list(subroute) {
        var eprs_trn_list = $('#eprs_trn_list',document);
        var route = (subroute) ? subroute : false;
        $.ajax({
            url : PECO.base_url() + 'purchasing/getprslist',
            type : 'post',
            dataType : 'json',
            data : {
                route : route
            },
            beforeSend: function () {
                PECO.DTphpLoading(eprs_trn_list,'Fetching your PRS list...')
            }
        }).done(function (d) {
            eprs_trn_list.dataTable().empty();
            eprs_trn_list.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: {
                    emptyTable : '<h4><i class="fa fa-warning text-warning"></i> No transaction related records yet!</h4>'
                },
                aoColumns: [
                    {data: 'expand', sWidth: '1%', sClass: 'text-align-center'},
                    {data: 'prfno', sWidth: '10%',sClass: 'text-primary bold'},
                    {data: 'pono', sWidth: '10%', sClass: 'text-primary bold'},
                    {data: 'submitted', sWidth:'10%'},
                    {data: 'updated', sWidth:'10%'},
                    {data: 'items', sClass: 'number'},
                    {data: 'justification', sWidth: '300px'},
                    {data: 'requestor', sClass: 'text-primary', sWidth: '150px'},
                    {data: 'trn', sClass: 'text-danger'},
                    {data: 'remarks', sClass: 'text-info'},
                    {data: 'status', sClass: 'text-info'},
                    {data: 'control', sClass: 'controls', sWidth: '13%'}
                ],
            });
        }).fail(function () {

        });
    }

    function prf_approval(dataid,type) {
        approval_handlers(dataid);
        //prf_handler(dataid);
        if (type === 'prf') {
            dt_prf_items_list(dataid);
            PECO.select2Basic($('#unitid', document),'query/getunits','Unit...',false,false,$('#unitid', document).val());
            items_bloodhound();
            prf_handler(dataid);
        }
        if (type === 'rfq') {
            dt_prf_items_list(dataid);
        }
    }

    function dt_prf_items_list(dataid) {
        //var tbl_prf_items = $('#tbl_prf_items',document);

        $.ajax({
            url : PECO.base_url() + 'purchasing/getprfitemsforapproval',
            type : 'post',
            dataType : 'json',
            data : {
                prfid : dataid
            }
        }).done(function (d) {
            if (d.itemlist && d.itemlist.length > 0) {
                po_items.rows().remove();
                $.each(d.itemlist, function (i, item) {
                    //console.log(item);
                    po_items.row.add(item);
                });
                po_items.draw();
            } else {
                po_items.rows().remove().draw();
            }
        }).fail(function () {
            dtEmptyMsg = '<h4 style="margin: 0px 10px;"><i class="fa fa-times font-red"></i> PHP Error! </h4>';
            po_items.draw();
        });
    }

    function approval_handlers(dataid) {
        PECO.dtSubComments(tbl_po_items,'purchasing/showprfitemcomments');
        $(document).on('click','#btn_refresh_item_list',function () {
            dt_prf_items_list(dataid);
        });

        tbl_po_items.on('click','#prf_item_disapprove',function () {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_item = $('#prf_item_id',this_tr).val();
            var item_details = [];

            this_tr.find('td').each(function () {
                item_details.push($(this).text())
            });

            //console.log(item_details);
            var item_desc = item_details[2] + ' ' + item_details[3] + ' of ' + item_details[1];

            swal({
                title: "Disapprove Item?",
                text: "You are about to disapprove " + item_desc + ".",
                type: "input",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Disapprove!",
                closeOnConfirm: false,
                closeOnCancel : true,
                inputPlaceholder: "Please provide a reason for disapproval (optional)"
            }, function(inputValue) {
                var remarks = '';
                //console.log(inputValue);

                if (inputValue || inputValue === "") {
                    //console.log(inputValue);

                    //if (inputValue === null) return false;

                    if (inputValue !== "") {
                        //swal.showInputError("You need to write something!");
                        //return false
                        remarks = inputValue;
                    }

                    $.ajax({
                        url: PECO.base_url() + 'purchasing/disapproveprfitem',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            itemid: this_item,
                            remarks: remarks
                        }
                    }).done(function (d) {
                        if (d.qry) {
                            po_items.row(this_tr).remove().draw();
                            swal('Success!', item_desc + ' has been disapproved.', 'success');
                        } else {
                            swal('Fail!', 'Failed to disapprove ' + item_desc + '.', 'error');
                        }
                    }).fail(function () {
                        swal('FAIL!', 'Failed to execute function.', 'error');
                    });
                }
            });

        });

        $(document).on('click','#btn_approve_prf',function () {
            var this_ = $(this);
            var stageid = this_.attr('data-stageid');
            var flowid = this_.attr('data-flowid');
            var trnid = this_.attr('data-trnid');
            var type = this_.attr('data-type');

            swal({
                title: "Approve PRF?",
                text: "Approve PRF and forward to RFQ.",
                type: "input",
                showCancelButton: true,
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Approve!",
                closeOnConfirm: false,
                closeOnCancel : true,
                inputPlaceholder: "Add remarks if applicable. (optional)"
            },
            function(inputValue) {
                var remarks = '';
                if (inputValue || inputValue === "") {
                    //if (inputValue === null) return false;

                    if (inputValue !== "") {
                        //swal.showInputError("You need to write something!");
                        //return false
                        remarks = inputValue;
                    }

                    $.ajax({
                        url: PECO.base_url() + 'purchasing/approveprf',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            prfid: dataid,
                            remarks: remarks,
                            type: type,
                            flowid : flowid,
                            stageid : stageid,
                            trnid : trnid
                        }
                    }).done(function (d) {
                        if (d.qry) {
                            //po_items.row(this_tr).remove().draw();
                            swal('Success!', d.type + ' has been approved!', 'success');
                        } else {
                            swal('Fail!', 'Failed to approve '+ d.type +'!', 'error');
                        }
                    }).fail(function () {
                        swal('FAIL!', 'Failed to execute function.', 'error');
                    });
                }
            });
        });

        $(document).on('click','#btn_disapprove_prf',function () {
            var this_ = $(this);
            var stageid = this_.attr('data-stageid');
            var flowid = this_.attr('data-flowid');
            var trnid = this_.attr('data-trnid');
            var type = this_.attr('data-type');

            swal({
                title: "Disapprove PRF?",
                text: "Disapproving this request will terminate the transaction. Do you wish to proceed?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Disapprove!",
                closeOnConfirm: false,
                closeOnCancel : true
            },
            function(isConfirm) {
                var remarks = '';
                if (isConfirm) {

                    $.ajax({
                        url: PECO.base_url() + 'purchasing/disapproveprf',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            prfid: dataid,
                            type: type,
                        }
                    }).done(function (d) {
                        swal({
                            title: d.title,
                            text: d.msg,
                            type: d.func,
                            html: true
                        }/*, function () {
                            if (d.qry) {
                                var closeInSeconds = 6,
                                    displayText = "This window will close in #1 seconds.",
                                    timer;
                                timer = setInterval(function() {
                                    closeInSeconds--;
                                    swal({
                                        title: 'Window is Closing',
                                        text: displayText.replace(/#1/, closeInSeconds),
                                        type: 'warning',
                                        timer: closeInSeconds * 1000,
                                        showConfirmButton: false
                                    });
                                    if (closeInSeconds < 0) {
                                        clearInterval(timer);
                                        window.close();
                                    }
                                    $('.sweet-alert > p').text(displayText.replace(/#1/, closeInSeconds));
                                    console.log(closeInSeconds);
                                }, 1000);
                            }
                        }*/);

                    }).fail(function () {
                        swal('FAIL!', 'Failed to execute function.', 'error');
                    });
                }
            });
        });

        $(document).on('click','#btn_revise_prf',function () {
            var this_ = $(this);
            var stageid = this_.attr('data-stageid');
            var flowid = this_.attr('data-flowid');
            var trnid = this_.attr('data-trnid');
            var type = this_.attr('data-type');

            swal({
                    title: "Return PRF?",
                    text: "Return PRF to requestor.",
                    type: "input",
                    showCancelButton: true,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Return!",
                    closeOnConfirm: false,
                    closeOnCancel : true,
                    inputPlaceholder: "Add remarks if applicable. (optional)"
                },
                function(inputValue) {
                    var remarks = '';
                    if (inputValue || inputValue === "") {
                        //if (inputValue === null) return false;

                        if (inputValue !== "") {
                            //swal.showInputError("You need to write something!");
                            //return false
                            remarks = inputValue;
                        }

                        $.ajax({
                            url: PECO.base_url() + 'purchasing/returnprf',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                prfid: dataid,
                                remarks: remarks,
                                type: type,
                                flowid : flowid,
                                stageid : stageid,
                                trnid : trnid
                            }
                        }).done(function (d) {
                            swal(d.title, d.msg, d.func);
                            if (d.qry) {
                                //po_items.row(this_tr).remove().draw();
                                window.location.replace(d.url);
                            }
                        }).fail(function () {
                            swal('FAIL!', 'Failed to execute function.', 'error');
                        });
                    }
                });
        });
    }

    var init_rfq = function (dataid) {
        dt_rfq_items(dataid);
        dt_quoted_suppliers(dataid);
        dt_rfq_attachments()
        rfq_handlers(dataid);
    };

    var rfq_handlers = function (dataid) {
        var tbl_rfq_items = $('#tbl_rfq_items',document);
        var tbl_cost_summary = $('#tbl_cost_summary',document);
        PECO.dtSubComments(tbl_rfq_items,'purchasing/showrfqitemcomments');

        $(document).on('click','#btn_refresh_rfq_item_list',function () {
            dt_rfq_items(dataid);
            dt_quoted_suppliers(dataid);
        });

        tbl_rfq_items.on('ifChecked','#icheck_input',function () {
            var this_ = $(this);
            var this_td = this_.closest('td');
            var this_tr = this_.closest('tr');
            this_td.css('background-color','yellow');
            $('#rfq_item_price',this_td).addClass('bold');
            $('.est_total_amt',this_tr).text('-');

            //COMPUTE QTY BY SELECTED PRICE
            var item_qty = parseInt($('#prf_qty',this_tr).html());
            var selected_price = parseFloat($('#rfq_item_price',this_td).attr('data-price'));
            var selected_currency = this_.attr('data-currency');
            var est_total_amt = $('span[data-currency="'+selected_currency+'"]',this_tr);
            var price = item_qty * selected_price;
            var formatted = price.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
            est_total_amt.html(formatted);

            //IF SELECTED IS NON-PESO
            if (selected_currency !== 'total') {
                var converted_price_cell = (selected_currency !== 'php_total') ? this_td.next() : this_td.prev();
                var converted_price = parseFloat($('#rfq_item_price',converted_price_cell).attr('data-price'));
                converted_price_cell.css('background-color','yellow');
                $('#rfq_item_price',converted_price_cell).addClass('bold')
                var converted_amt = item_qty * converted_price;
                var formatted_amt = converted_amt.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
                $('span[data-currency="php_total"]',this_tr).html(formatted_amt);
            }

            //ADD AMOUNT TO SUMMARY OF COST IF SELECTED
            compute_cost_summary(dataid);

        }).on('ifUnchecked','#icheck_input',function () {
            var this_ = $(this);
            var this_td = this_.closest('td');
            this_td.css('background-color','transparent');
            $('#rfq_item_price',this_td).removeClass('bold');
            if (this_.attr('data-currency') !== 'total') {
                var converted_price_cell = (this_.attr('data-currency') !== 'php_total') ? this_td.next() : this_td.prev();
                converted_price_cell.css('background-color','transparent');
                $('#rfq_item_price',converted_price_cell).removeClass('bold')
            }
        });

        $(document).on('submit','#frm_submit_quotation',function (e) {
            e.preventDefault();
            var this_ = $(this);

            swal({
                title: "Send for approval?",
                text: "This will send quotations for approval.",
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
                        url : this_.attr('action'),
                        type : this_.attr('method'),
                        dataType : 'json',
                        data : this_.serialize()
                    }).done(function (d) {
                        if (d.qry) {
                            swal({
                                title: d.title,
                                text: d.msg,
                                type: d.func,
                                html: true
                            }, function () {
                                if (d.qry) {
                                    window.location.href = d.url;
                                }
                            });
                        }
                    }).fail(function () {
                        swal('FAIL!','Failed to execute function.','error');
                    });
                }else{
                    swal.close();
                }
            });
        });

        $(document).on('click','#btn_delete_supplier_quote',function () {
            var this_ = $(this);
            var this_id = this_.attr('data-id');

            swal({
                title: "Delete quotation?",
                text: "This will remove selected supplier's quotation.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, delete!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url : PECO.base_url() + 'purchasing/deletesupplierquotation',
                        type : 'post',
                        dataType : 'json',
                        data : {
                            id : this_id
                        }
                    }).done(function (d) {
                        swal(d.title, d.msg, d.func);
                        if (d.qry) {
                            dt_rfq_items(dataid);
                            dt_quoted_suppliers(dataid);
                        }
                    }).fail(function () {
                        swal('FAIL!','Failed to execute function.','error');
                    });
                }else{
                    swal.close();
                }
            });
        });

        $(document).on('click','#btn_edit_rfq_qty',function () {
            var this_ = $(this);
            this_.attr('id','btn_save_rfq_qty').html('<i class="fa fa-save"></i>').after('<button type="button" class="btn btn-danger inline" id="btn_cancel_edit_qty"><i class="fa fa-times-rectangle-o"></i></button>');
            var this_tr = this_.closest('tr');
            var qty = $('#prf_qty',this_tr);
            qty.attr('hidden',true);
            this_.removeClass('btn-danger').addClass('btn-primary');
            qty.after('<input type="number" style="width: 100% !important;" class="form-control" id="prf_item_qty" name="qty" value="'+ qty.text() +'">');
            var unit = $('#prf_unit_name',this_tr);
            unit.attr('hidden',true);
            unit.after('<input type="number" style="width: 100% !important;" class="form-control" id="prf_item_unit" name="unit" value="'+ unit.attr('data-id') +'">');
            PECO.select2Basic($('#prf_item_unit',this_tr),'query/getunits','Select Unit...',false,false,$('#prf_item_unit',this_tr).val());
        });

        $(document).on('click','#btn_cancel_edit_qty',function () {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var save_btn = $('#btn_save_rfq_qty',this_tr);
            var qty = $('#prf_qty',this_tr);
            var unit = $('#prf_unit_name',this_tr);
            qty.attr('hidden',false);
            unit.attr('hidden',false);
            this_.remove();
            save_btn.attr('id','btn_edit_rfq_qty').html('<i class="fa fa-edit"></i>');
            $('#prf_item_qty',this_tr).remove();
            $('#prf_item_unit',this_tr).select2('destroy').remove();
            save_btn.removeClass('btn-primary').addClass('btn-danger');
        });

        $(document).on('click','#btn_save_rfq_qty',function () {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var itemid = this_.attr('data-id');
            var itemqty = $('#prf_item_qty',this_tr).val();
            var itemunit = $('#prf_item_unit',this_tr).val();
            $.ajax({
                url : PECO.base_url() + 'purchasing/reviseitemqty',
                type : 'post',
                dataType : 'json',
                data : {
                    itemid : itemid,
                    itemqty : itemqty,
                    itemunit : itemunit
                }
            }).done(function (d) {
                PECO.initAlerts(d.msg,d.title,d.func);
                if (d.qry) {
                    var amount = $('input[name^=amount]:checked', this_tr);
                    if (amount.length === 0) {
                        amount  = $('i.quoted',this_tr);
                    }
                    var amount_td = amount.closest('td');
                    var selected_price = parseFloat($('#rfq_item_price', amount_td).attr('data-price'));
                    var est_total_amt = $('.est_total_amt', this_tr);
                    var price = d.qty * selected_price;
                    var formatted = price.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,");
                    est_total_amt.html(formatted);
                    compute_cost_summary(dataid);
                    //return to previous state
                    var qty = $('#prf_qty',this_tr);
                    var unit = $('#prf_unit_name',this_tr);
                    qty.attr('hidden',false).html(d.qty);
                    unit.attr('hidden',false).html(d.unitname).attr('data-id',d.unit);
                    $('#btn_cancel_edit_qty',this_tr).remove();
                    this_.attr('id','btn_edit_rfq_qty').html('<i class="fa fa-edit"></i>');
                    $('#prf_item_qty',this_tr).remove();
                    $('#prf_item_unit',this_tr).select2('destroy').remove();
                    this_.removeClass('btn-primary').addClass('btn-danger');
                }
            }).fail(function () {
                PECO.initAlerts('Error executing function!','ERROR!!!','error');
            });
        });

        let keyupTimer;
        tbl_cost_summary.on('keyup','#supplier_ship',function () {
            clearTimeout(keyupTimer);
            keyupTimer = setTimeout(function () {
                compute_cost_summary(dataid);
            },500);
        });

        prf_cancel_button(dataid);
    };

    var prf_cancel_button = function (dataid) {
        $(document).on('click','#btn_cancel_rfq',function () {
            var this_ = $(this);
            var stageid = this_.attr('data-stageid');
            var flowid = this_.attr('data-flowid');
            var trnid = this_.attr('data-trnid');
            var type = this_.attr('data-type');

            swal({
                    title: "Cancel Purchase Request?",
                    text: "This will remove PRF from the process tree. Continue?",
                    type: "input",
                    showCancelButton: true,
                    cancelButtonText: "No!",
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, cancel!",
                    closeOnConfirm: false,
                    closeOnCancel : true,
                    inputPlaceholder: "Add remarks if applicable. (optional)"
                },
                function(inputValue) {
                    var remarks = '';
                    if (inputValue || inputValue === "") {
                        //if (inputValue === null) return false;

                        if (inputValue !== "") {
                            //swal.showInputError("You need to write something!");
                            //return false
                            remarks = inputValue;
                        }

                        $.ajax({
                            url: PECO.base_url() + 'purchasing/cancelpurchaserequest',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                prfid: dataid,
                                remarks: remarks,
                                type: type,
                                flowid : flowid,
                                stageid : stageid,
                                trnid : trnid,
                            }
                        }).done(function (d) {
                            if (d.qry) {
                                //po_items.row(this_tr).remove().draw();
                                swal('Cancelled!','Purchase request has been removed!', 'success');
                            } else {
                                swal('Fail!', 'Failed to cancel PRF!', 'error');
                            }
                        }).fail(function () {
                            swal('FAIL!', 'Failed to execute function.', 'error');
                        });
                    }
                });
        });
    }

    var dt_rfq_items = function (dataid,approval) {
        var tbl_rfq_items = $('#tbl_rfq_items',document);
        //tbl_rfq_items.DataTable().clear();
        $.ajax({
            url : PECO.base_url() + 'purchasing/getrfqitemslist',
            type : 'post',
            dataType : 'json',
            data : {
                prfid : dataid,
                approval : approval
            },
            beforeSend : function () {
                //PECO.DTphpLoading(tbl_rfq_items,'Fetching RFQ items...');
            }
        }).done(function (d) {
            //console.log(d.columns);
            //tbl_rfq_items.DataTable().destroy();
            //console.log(d);
            if (d.columns.length > 0 && d.itemlist.length > 0) {
                //$('thead', tbl_rfq_items).empty();
                //var suppliers_quote =  $('#suppliers_quote', tbl_rfq_items);
                //suppliers_quote.empty();

                if (typeof d.total_cols !== undefined && d.total_cols.length > 0) {
                    var supplier_remarks = $('#supplier_remarks', tbl_rfq_items);
                    $('.totals',tbl_rfq_items).remove();
                    $.each(d.total_cols,function (key,values) {
                        supplier_remarks.before('<th class="totals" data-desc="added_row" rowspan="2">' + values.sTitle + '</th>');
                        console.log('column added: ' + values.data);
                    });

                }

                if (d.subtotals !== undefined && Object.keys(d.subtotals).length > 0) {
                    var nextCell = $('#blank_remarks',tbl_rfq_items);
                    $('.subtotal',tbl_rfq_items).remove();
                    $.each(d.subtotals,function (key,values) {
                        nextCell.before('<td id="'+values+'" class="subtotal number" style="padding: 8px !important;">0.00</td>')
                    });
                }

                if (d.suppliers !== undefined && d.suppliers.length > 0) {
                    $('#suppliers_label', tbl_rfq_items).attr('colspan', d.suppliers.length);
                    $('#tr_headers > th:not(#suppliers_label)',tbl_rfq_items).attr('rowspan',2);
                    $('#suppliers_quote', tbl_rfq_items).remove();
                    $('thead',tbl_rfq_items).append('<tr id="suppliers_quote"></tr>');
                    var suppliers_quote =  $('#suppliers_quote', tbl_rfq_items);
                    suppliers_quote.empty();
                    //console.log(d.suppliers.length);
                    $.each(d.suppliers, function (key, values) {
                        suppliers_quote.append('<th>' + values.sTitle + '</th>');
                        //console.log('column added: ' + values.data);
                        //values.sTitle = false;
                    });
                    $('#buffer_label',tbl_rfq_items).attr('colspan',5+d.suppliers.length);
                    $('#subtotal_label',tbl_rfq_items).attr('colspan',5+d.suppliers.length);
                } else {
                    $('#suppliers_quote', tbl_rfq_items).remove();
                    $('th',tbl_rfq_items).attr('rowspan',1);
                    $('#suppliers_label', tbl_rfq_items).attr('colspan', 1);
                    $('#buffer_label',tbl_rfq_items).attr('colspan',6);
                    $('#subtotal_label',tbl_rfq_items).attr('colspan',6);
                }

                $('tbody',tbl_rfq_items).empty();
                setTimeout(
                    function () {
                        tbl_rfq_items.DataTable({
                            bDestroy: true,
                            bPaginate: false,
                            bFilter: true,
                            bInfo: true,
                            bStateSave: true,
                            bProcessing: true,
                            ordering: false,
                            //aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                            language: {
                                "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No transaction related records yet!</h4>'
                            },
                            aoColumns: d.columns,
                            aaData: d.itemlist, // USE FOR DYNAMIC LOCAL TABLE LIST
                            fnRowCallback: function (nRow, data, index) {
                                PECO.iCheckRow($('.icheck', nRow), 'square', 'blue');
                                var el = $('.icheck', nRow);
                                //console.log(el);
                                el.each(function () {
                                    var this_ = $(this);
                                    //console.log(this_.attr('checked'));
                                    var el_td = this_.closest('td');
                                    if (typeof this_.attr('checked') !== 'undefined' && this_.attr('checked') !== false) {
                                        el_td.css('background-color', 'yellow');
                                        $('#rfq_item_price', el_td).addClass('bold');
                                        if (this_.attr('data-currency') !== 'total') {
                                            var next_td = el_td.next();
                                            next_td.css('background-color', 'yellow');
                                            $('#rfq_item_price', next_td).addClass('bold');
                                        }

                                    } else {
                                        $('#rfq_item_price', el_td).removeClass('bold');
                                    }
                                });

                                var quoted = $('.quoted', nRow);
                                quoted.each(function () {
                                    var qt = $(this);
                                    var qt_td = qt.closest('td');
                                    qt_td.css('background-color', 'yellow');
                                });
                            }
                        });
                    },1000);
            }
        }).fail(function () {
            PECO.DTphpLoading
        });
    };

    var init_supplier_quotation = function (dataid,supplier) {
        var tbl_add_item_quotations = $('#tbl_add_item_quotations',document);

        if (supplier) {
            $('#select2_supplier', document).val(supplier).attr('type','hidden');
        } else {
            PECO.select2Basic($('#select2_supplier', document), 'purchasing/select2quotationsupplier', 'Select supplier...', false, false, false, false, false, dataid);
        }

        PECO.select2Basic($('#select2_paytype',document),'purchasing/select2paytype','Select Payment Type...',false,false,$('#select2_paytype',document).val());

        dt_items_forquotations(dataid,supplier);
        supplier_payment_details(dataid,supplier);

        $('#select2_supplier',document).on('change',function () {
            var this_ = $(this);
            var this_val = this_.val();
            if (this_val > 0) {
                //LOOKUP PAYMENT DETAILS
                supplier_payment_details(dataid);
                dt_items_forquotations(dataid);
            }
        });

        $('#icheck_exvat',document).iCheck({
            checkboxClass: 'icheckbox_square-blue', // minimal / square / polaris / futurico // red / green / blue
            //radioClass: 'iradio_minimal-blue',
            //increaseArea: '20%' // optional
        }).on('ifChecked', function () {
            var this_ = $(this);
            this_.attr('checked', true);
        }).on('ifUnchecked', function () {
            var this_ = $(this);
            this_.attr('checked', false);
        });

        tbl_add_item_quotations.on('keyup','#input_amount',function () {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var numVal = parseFloat(this_.val());

            if (isNaN(numVal) === false && numVal > 0) {
                $('#same_amount',this_tr).iCheck('disable');
            } else {
                $('#same_amount',this_tr).iCheck('enable');
            }
            //console.log(parseFloat(this_.val()));
            /*if (isNaN(numVal) === false && (numVal !== 0)) {
                $('#prf_item_id', this_tr).attr('disabled',false);
            } else {
                $('#prf_item_id', this_tr).attr('disabled',true);
            }*/
        });

        tbl_add_item_quotations.on('ifChecked','#prf_item_id',function () {
            var this_ = $(this);
            var this_tr = this_.closest('tr')
            var checked = tbl_add_item_quotations.find('#prf_item_id:checked');
            //console.log(this_.val() + ' : Checked');
            this_tr.find('input').not('input[id=prf_item_id],.icheck').each(function () {
                //console.log($(this).attr('id'));
                $(this).attr('disabled',false);
            });
            $('#same_amount',this_tr).iCheck('enable');
            if (checked.length > 0) {
                $('#export_quotation_sheet',document).attr('disabled',false);
            } else {
                $('#export_quotation_sheet',document).attr('disabled',true);
            }
        }).on('ifUnchecked','#prf_item_id',function () {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var checked = tbl_add_item_quotations.find('#prf_item_id:checked');
            //console.log(this_.val() + ' : Unchecked');
            this_tr.find('input').not('input[id=prf_item_id],.icheck').each(function () {
                $(this).attr('disabled',true);
            });
            $('#same_amount',this_tr).iCheck('disable');
            if (checked.length > 0) {
                $('#export_quotation_sheet',document).attr('disabled',false);
            } else {
                $('#export_quotation_sheet',document).attr('disabled',true);
            }
        });

        tbl_add_item_quotations.on('ifChecked','#same_amount',function () {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            //console.log(this_.val() + ' : Checked');
            $('#input_amount',this_tr).attr('disabled',true);
        }).on('ifUnchecked','#same_amount',function () {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            //console.log(this_.val() + ' : Unchecked');
            $('#input_amount',this_tr).attr('disabled',false);
        });


        $('#frm_add_quotations',document).on('submit',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var itemid = this_.find('table td #prf_item_id:checkbox:checked');

            //console.log(itemid);

            if (itemid.length > 0) {
                $.ajax({
                    url: this_.attr('action'),
                    type: this_.attr('method'),
                    dataType: 'json',
                    data: this_.serialize()
                }).done(function (d) {
                    PECO.initAlerts(d.msg, d.title, d.func);
                    if (d.qry) {
                        if (d.hasOwnProperty('ponum')) {
                            $('#po_number',document).text(d.ponum);
                        }
                        dt_rfq_items(dataid);
                        dt_quoted_suppliers(dataid);
                    }
                }).fail(function () {
                    PECO.phpError();
                });
            } else {
                PECO.initAlerts('No item was quoted!', 'No Price!', 'error');
            }
        });

        $('#export_quotation_sheet',document).on('click',function () {
            var supplier_ = (supplier > 0) ? supplier : $('#select2_supplier',document).val();
            var items = [];

            tbl_add_item_quotations.find('#prf_item_id:checked').each(function () {
                var this_ = $(this);
                items.push(this_.val());
            });

            //console.log(items);

            if (items.length > 0) {
                $.ajax({
                    url: PECO.base_url() + 'purchasing/exportquotationsheet',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        dataid : dataid,
                        supplier : supplier_,
                        items : items
                    }
                }).done(function (d) {
                    var xls = d.xls;
                    var a = document.createElement('a');
                    a.href = xls.file;
                    a.setAttribute('download', xls.filename);
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                }).fail(function () {
                    PECO.phpError();
                });
            }
        });
    };

    var supplier_payment_details = function (dataid,supplier) {
        var supplier_ = (supplier > 0) ? supplier : $('#select2_supplier',document).val();
        var supplier_fields = $('#quotation_supplier_details',document);
        $.ajax({
            url : PECO.base_url() + 'purchasing/getsupplierpaymentdetails',
            type : 'POST',
            dataType : 'json',
            data : {
                supplierid : supplier_,
                prfid : dataid
            }
        }).done(function(d) {
            if (Object.keys(d).length > 0) {
                if (supplier_fields.hasClass('hidden')) {
                    supplier_fields.removeClass('hidden');
                }
                $.each(d, function (key, value) {
                    var el = $('#' + key);
                    if (el.is('input')) {
                        el.val(value);
                        if (key.indexOf('select2') > -1) {
                            el.trigger('change');
                        }
                    } else {
                        el.text(value);
                    }
                });
            } else {
                supplier_fields.addClass('hidden');
            }
        })

        dt_items_forquotations(dataid);
    }

    var dt_items_forquotations = function (dataid,supplier) {
        var tbl_add_item_quotations = $('#tbl_add_item_quotations',document);
        var supplier_ = (supplier > 0) ? supplier : $('#select2_supplier',document).val();
        $.ajax({
            url : PECO.base_url() + 'purchasing/dtquotationitems',
            type : 'post',
            dataType : 'json',
            data : {
                dataid : dataid,
                supplier : supplier_
            }
        }).done(function (d) {
            if (supplier_ > 0) {
                tbl_add_item_quotations.DataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    ordering: false,
                    language: {
                        "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No items listed for quotations!</h4>'
                    },
                    aoColumns: d.columns,
                    aaData: d.itemlist, // USE FOR DYNAMIC LOCAL TABLE LIST
                    fnRowCallback: function (nRow, data, index) {
                        PECO.iCheckRow($('.icheck',nRow),'minimal','blue')
                    }
                });
            } else {
                PECO.DTphpError(tbl_add_item_quotations,'<i class="fa fa-warning text-warning"></i> Please select a supplier to load item list!');
            }

            $('#exchange_rate',document).html(d.exchange_rate);
        }).fail(function () {
            PECO.DTphpError(tbl_add_item_quotations,'<i class="fa fa-warning text-danger"></i> Error fetching items for quotation');
        });
    };

    var dt_quoted_suppliers = function (dataid,approval) {
        var tbl_cost_summary = $('#tbl_cost_summary',document);

        $.ajax({
            url : PECO.base_url() + 'purchasing/getsuppliersummaryofcost',
            type : 'post',
            dataType : 'json',
            data : {
                id : dataid,
                approval : approval
            }
        }).done(function (d) {
            if (typeof d.columns !== undefined && d.columns.length > 0) {
                var thead = $('thead tr',tbl_cost_summary);
                var th = thead.children().length;
                var cols = d.columns.length;

                var buffer = $('#buffer',document);
                var gtoal = $('#gtotal',tbl_cost_summary);

                var buffer_tr = buffer.closest('tr');
                var gtoal_tr = gtoal.closest('tr');

                if (cols > th) {
                    var append = cols - th;
                    for (var t = 0; append > t; t++) {
                        thead.append('<th></th>');
                    }
                }
            }
            if (d.hasOwnProperty('supplist') && d.supplist.length > 0) {
                tbl_cost_summary.DataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: false,
                    bInfo: false,
                    bStateSave: true,
                    bProcessing: true,
                    ordering: false,
                    language: {
                        "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No supplier was quoted!</h4>'
                    },
                    aoColumns: d.columns,
                    aaData: d.supplist, // USE FOR DYNAMIC LOCAL TABLE LIST
                });
                $('#gtotal',tbl_cost_summary).text(d.grandtotal);
                //$('#subtotal_amt',document).text(d.subtotal);
                if (d.hasOwnProperty('subtotals') && Object.keys(d.subtotals).length > 0) {
                    //console.log(d.subtotals);
                    $.each(d.subtotals,function (id,value) {
                        setTimeout(function () {
                            $('#'+id,document).text(value);
                        },500);
                        console.log(value);
                    });
                }
                $('#buffer_amt',document).text(d.buffer);
                $('#buffer',document).text(d.buffer);
            } else {
                PECO.DTphpError(tbl_cost_summary,'<i class="fa fa-warning text-warning"></i>  No supplier was quoted!');
            }
        }).fail(function () {
            PECO.DTphpError(tbl_cost_summary,'<i class="fa fa-warning text-danger"></i> Error fetching suppliers!');
        });
    };

    var compute_cost_summary = function (dataid) {
        var tbl_cost_summary = $('#tbl_cost_summary',document);
        var tbl_rfq_items = $('#tbl_rfq_items :input[type=radio]',document);
        var data = '';
        if (tbl_rfq_items.length === 0) {
            var amount = [];
            $(document).find('#tbl_rfq_items .quoted').each(function () {
                var this_ = $(this);
                var amount_ = this_.next();
                var qt = amount_.attr('data-value');
                var item = amount_.attr('data-item');
                amount.push('amount['+ item +']='+qt);
            });
            var amountData = amount.join('&');

            data = amountData + '&id='+dataid + '&' + $('input',tbl_cost_summary).serialize();
        } else {
            data = tbl_rfq_items.serialize() + '&id='+dataid + '&' + $('input',tbl_cost_summary).serialize();
        }

        $.ajax({
            url : PECO.base_url() + 'purchasing/computesummaryofcost',
            type : 'post',
            dataType : 'json',
            data : data
        }).done(function (d) {
            if (Object.keys(d.soc).length > 0) {
                /*$.each(d.soc,function (id,value) {
                    //scan if other suppliers are quoted. Otherwise, zero value.
                    $('#soc_'+id,tbl_cost_summary).text(parseFloat(value).toFixed(2));
                });*/
                $('span[id="supplier_netvat"]').each(function () {
                    var this_ = $(this);
                    var this_id = this_.attr('data-id');
                    if (d.netvat[this_id] !== undefined) {
                        this_.text(parseFloat(d.netvat[this_id]).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,"));
                    } else {
                        this_.text('0.00');
                    }
                });

                $('span[id="supplier_vat"]').each(function () {
                    var this_ = $(this);
                    var this_id = this_.attr('data-id');
                    if (d.vat[this_id] !== undefined) {
                        this_.text(parseFloat(d.vat[this_id]).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,"));
                    } else {
                        this_.text('0.00');
                    }
                });

                $('span[id="supplier_gross"]').each(function () {
                    var this_ = $(this);
                    var this_id = this_.attr('data-id');
                    if (d.gross[this_id] !== undefined) {
                        this_.text(parseFloat(d.gross[this_id]).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,"));
                    } else {
                        this_.text('0.00');
                    }
                });

                $('span[id="supplier_ewt"]').each(function () {
                    var this_ = $(this);
                    var this_id = this_.attr('data-id');
                    if (d.ewt[this_id] !== undefined) {
                        this_.text(parseFloat(d.ewt[this_id]).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,"));
                    } else {
                        this_.text('0.00');
                    }
                });

                $('span[id$=supplier_soc]').each(function () {
                    var this_ = $(this);
                    var this_id = this_.attr('data-id');
                    /*if (d.suptotal[this_id] !== undefined) {
                        this_.text(parseFloat(d.suptotal[this_id]).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,"));
                    } else {
                        this_.text('0.00');
                    }*/

                    /*var this_currency = this_.attr('id').replace('supplier_soc','').substring(0,3);
                    if (this_currency.length === 3) {
                        console.log(d.suptotal);
                        if (typeof d.suptotal[this_id][this_currency] !== undefined) {
                            this_.text(parseFloat(d.suptotal[this_id][this_currency]).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,"));
                        } else {
                            this_.text('0.00');
                        }
                    } else {
                        if (typeof d.suptotal[this_id]['p'] !== undefined) {
                            this_.text(parseFloat(d.suptotal[this_id]['p']).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,"));
                        } else {
                            this_.text('0.00');
                        }
                    }*/

                    if (this_id in d.suptotal) {
                        var suptotal = d.suptotal[this_id];
                        var this_currency = this_.attr('id').replace('supplier_soc','').substring(0,3);
                        if (this_currency.length === 3 && this_currency in suptotal) {
                            this_.text(parseFloat(suptotal[this_currency]).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,"));
                        } else {
                            if (this_currency.length < 3 && 'p' in suptotal) {
                                this_.text(parseFloat(suptotal['p']).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,"));
                            } else {
                                this_.text('0.00');
                            }
                        }
                    } else {
                        this_.text('0.00');
                    }
                });



                $('#gtotal',tbl_cost_summary).text(d.gtotal);
                //$('#subtotal_amt',document).text(d.subtotal);
                $('#buffer',document).text(d.buffer);
                //console.log(d.subtotals);
                //console.log(typeof d.subtotals !== undefined);
                //console.log(d.subtotals.length);
                if (typeof d.subtotals !== undefined && Object.keys(d.subtotals).length > 0) {
                    //console.log(d.subtotals);
                    $.each(d.subtotals,function (id,value) {
                        //console.log([id,value]);
                        $('#' + id,document).text(value);
                    });
                }
            }
        }).fail(function () {

        });
    };

    var eprs_approval = function (elem,trn,dataid) {
        var stageid = elem.attr('data-stageid');
        var flowid = elem.attr('data-flowid');
        var trnid = elem.attr('data-trnid');
        var type = elem.attr('data-type');

        swal({
                title: "Approve " + trn + "?",
                text: "Approve " + trn + " and will be forwarded to next step.",
                type: "input",
                showCancelButton: true,
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Approve!",
                closeOnConfirm: false,
                closeOnCancel : true,
                inputPlaceholder: "Add remarks if applicable. (optional)"
            },
            function(inputValue) {
                var remarks = '';
                if (inputValue || inputValue === "") {
                    //if (inputValue === null) return false;

                    if (inputValue !== "") {
                        //swal.showInputError("You need to write something!");
                        //return false
                        remarks = inputValue;
                    }
                    var params = {
                        prfid: dataid,
                        remarks: remarks,
                        type: type,
                        flowid : flowid,
                        stageid : stageid,
                        trnid : trnid
                    };

                    console.log({stageid: parseInt(stageid)});
                    if (parseInt(stageid) === 107) {
                        var rfq_val = $('#tbl_rfq_items :input', document).serialize();
                        params = $.param(params) + '&' + rfq_val;
                    }

                    $.ajax({
                        url: PECO.base_url() + 'purchasing/approveprf',
                        type: 'post',
                        dataType: 'json',
                        data: params
                    }).done(function (d) {
                        if (d.qry) {
                            //po_items.row(this_tr).remove().draw();
                            swal({
                                title: d.title,
                                text: d.msg,
                                type: d.func,
                                html: true
                            }, function () {
                                if (d.qry) {
                                    window.location.href = d.url;
                                }
                            });
                        } else {
                            swal('Fail!', 'Failed to approve '+ d.type +'!', 'error');
                        }
                    }).fail(function () {
                        swal('FAIL!', 'Failed to execute function.', 'error');
                    });
                }
            });
    };

    var disapprove_eprs = function (elem,trn,dataid) {
        var stageid = elem.attr('data-stageid');
        var flowid = elem.attr('data-flowid');
        var trnid = elem.attr('data-trnid');
        var type = elem.attr('data-type');

        swal({
                title: "Disapprove " + trn + "?",
                text: "Disapprove " + trn + " and return to step prior to approval.",
                type: "input",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Disapprove!",
                closeOnConfirm: false,
                closeOnCancel : true,
                inputPlaceholder: "Add remarks if applicable. (optional)"
            },
            function(inputValue) {
                var remarks = '';
                if (inputValue || inputValue === "") {
                    //if (inputValue === null) return false;

                    if (inputValue !== "") {
                        //swal.showInputError("You need to write something!");
                        //return false
                        remarks = inputValue;
                    }

                    $.ajax({
                        url: PECO.base_url() + 'purchasing/disapproveprf',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            prfid: dataid,
                            remarks: remarks,
                            type: type,
                            flowid : flowid,
                            stageid : stageid,
                            trnid : trnid
                        }
                    }).done(function (d) {
                        if (d.qry) {
                            //po_items.row(this_tr).remove().draw();
                            swal('Success!', d.type + ' has been disapproved!', 'success');
                        } else {
                            swal('Fail!', 'Failed to disapprove '+ d.type +'!', 'error');
                        }
                    }).fail(function () {
                        swal('FAIL!', 'Failed to execute function.', 'error');
                    });
                }
            });
    };

    var rfq_approval = function (dataid,stageid) {
        var approval = (stageid !== 107);
        dt_rfq_items(dataid,approval);
        dt_quoted_suppliers(dataid,approval);
        dt_approver_remarks(dataid);
        rfq_handlers(dataid);
        dt_rfq_attachments();
        prf_cancel_button(dataid);
        var tbl_rfq_items = $('#tbl_rfq_items',document);
        PECO.dtSubComments(tbl_rfq_items,'purchasing/showrfqitemcomments');

        //approve and forward, disapprove and return to quotations, requote return to quotations
        $(document).on('click','#btn_approve_rfq',function () {
            var this_ = $(this);
            eprs_approval(this_,'RFQ',dataid);
        });

        /*$(document).on('click','#btn_disapprove_rfq',function () {
            var this_ = $(this);
            disapprove_eprs(this_,'RFQ',dataid);
        });*/

        $(document).on('click','#btn_requote_rfq',function () {
            var this_ = $(this);
            var stageid = this_.attr('data-stageid');
            var flowid = this_.attr('data-flowid');
            var trnid = this_.attr('data-trnid');
            var type = this_.attr('data-type');

            swal({
                title: "Requote Items?",
                text: "Return to purchasing for requotation.",
                type: "input",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Requote!",
                closeOnConfirm: false,
                closeOnCancel : true,
                inputPlaceholder: "Add remarks if applicable. (optional)"
            },
            function(inputValue) {
                var remarks = '';
                if (inputValue || inputValue === "") {
                    //if (inputValue === null) return false;

                    if (inputValue !== "") {
                        //swal.showInputError("You need to write something!");
                        //return false
                        remarks = inputValue;
                    }

                    $.ajax({
                        url: PECO.base_url() + 'purchasing/requoterfq',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            prfid: dataid,
                            remarks: remarks,
                            type: type,
                            flowid : flowid,
                            stageid : stageid,
                            trnid : trnid
                        }
                    }).done(function (d) {
                        swal(d.title, d.msg, d.func);
                    }).fail(function () {
                        swal('FAIL!', 'Failed to execute function.', 'error');
                    });
                }
            });
        });
    };

    var dt_rfq_attachments = function () {
        var tbl_rfq_attachments = $('#tbl_rfq_attachments',document);
        PECO.dtDocsList(tbl_rfq_attachments,4);
    }

    var dt_approver_remarks = function (dataid) {
        var tbl_approver_remarks = $('#tbl_approver_remarks',document);
        var typesid = tbl_approver_remarks.attr('data-type');
        PECO.DTDefault(tbl_approver_remarks,'');
        $.ajax({
            url : PECO.base_url() + 'purchasing/dtapproverremarks',
            type : 'post',
            dataType : 'json',
            data : {
                id : dataid,
                typesid : typesid
            }
        }).done(function (d) {
            if (d.list !== undefined && d.list.length > 0) {
                tbl_approver_remarks.DataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: false,
                    bInfo: false,
                    bStateSave: true,
                    bProcessing: true,
                    ordering: false,
                    language: {
                        "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No logged approvals, yet.</h4>'
                    },
                    aoColumns: d.columns,
                    aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                });
            } else {
                PECO.DTDefault(tbl_approver_remarks,'No logged approvals, yet.');
            }
        }).fail(function () {
            PECO.DTDefault(tbl_approver_remarks,'Error in fetching Approvers\' remarks');
        });
    };

    var purchase_order_handling = function (dataid) {
        dt_po_suppliers(dataid);
        var tbl_po_suppliers = $('#tbl_po_suppliers',document);

        $(document).on('click','#btn_refresh_po_list',function () {
            dt_po_suppliers(dataid);
        });

        tbl_po_suppliers.on('click','#btn_po_preview',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var this_id = this_.attr('data-id');
            //OPEN NEW TAB FOR PREVIEW
            $.ajax({
                url : PECO.base_url() + 'printer/printpo',
                type : 'post',
                dataType : 'json',
                data : {
                    suppid : this_id
                }
            }).done(function (d) {
                let title = d.title || 'PO Preview';
                let html = d.html || '';
                let papersize = d.papersize || false;
                
                PECO.pdfPreview(title, html, papersize);
            }).fail(function () {

            });

        });

        $(document).on('click','#btn_generate_po',function () {
            //CHECK IF ALL SUPPLIERS HAS DETAILS
            swal({
                title: "Generate PO?",
                text: "Please make sure all suppliers has CORRECT details provided.",
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
                        url : PECO.base_url() + 'purchasing/generatepo',
                        type : 'post',
                        dataType: 'json',
                        data : {
                            id : dataid
                        }
                    }).done(function (d) {
                        if (d.qry === true) {
                            //CHANGE PREVIEW BUTTON TO PRINT WITH ALERT CONFIRMATION.
                            swal('PO Generated!!!',d.msg,d.func);
                            $('#po_number',document).text(d.ponumber);
                        } else {
                            swal('Lacking details!',d.msg,d.func);
                        }
                    }).fail(function (d) {
                        swal('ERROR!!!','Error executing script!','error');
                    });
                }else{
                    swal.close();
                }
            });
        });
    };

    var dt_po_suppliers = function (dataid) {
        var tbl_po_suppliers = $('#tbl_po_suppliers',document);
        PECO.DTDefault(tbl_po_suppliers,'No Suppliers for this PO.');
        var tbl_view = tbl_po_suppliers.attr('data-view');

        $.ajax({
            url : PECO.base_url() + 'purchasing/dtposuppliers',
            type : 'post',
            dataType : 'json',
            data : {
                id : dataid,
                view : (tbl_view > 0) ? true : false
            }
        }).done(function (d) {
            var thead = $('thead tr',tbl_po_suppliers);
            var th = thead.children().length;
            var cols = d.columns.length;

            if (cols > th) {
                var append = cols - th;
                for (var c = 0; append > c; c++) {
                    thead.append('<th></th>');
                }
            }
            if (d.list !== undefined && d.list.length > 0) {
                tbl_po_suppliers.DataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: false,
                    bInfo: false,
                    bStateSave: true,
                    bProcessing: true,
                    ordering: false,
                    language: {
                        "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No logged approvals, yet.</h4>'
                    },
                    aoColumns: d.columns,
                    aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                });
            }
        }).fail(function () {

        });
    };

    var init_eprs_payments = function () {
        PECO.select2Types($('#rfp_payment_type',document),'PAYTYPE','Select payment type...',false,false,false);
        PECO.limitTextArea($('#rfp_notes',document),500);

        $('#frm_paymen_request',document).on('submit',function (e) {
            e.preventDefault();
            var this_ = $(this);

            $.ajax({
                url : this_.attr('action'),
                type : this_.attr('method'),
                dataType : 'json',
                data : this_.serialize()
            }).done(function (d) {
                PECO.initAlerts(d.msg,d.title,d.func);
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('change','#rfp_payment_type',function () {
            var this_ = $(this);
            var value = this_.val();

            if (value > 1) {
                //console.log('Payment Type condition: True / Value : ' + value);
                $('#frm_paymen_request',document).find('input[name^=account]').each(function () {
                    $(this).attr('required',true);
                    $(this).prev('span').attr('class','required');
                });
            } else {
                //console.log('Payment Type condition: False / Value : ' + value);
                $('#frm_paymen_request',document).find('input[name^=account]').each(function () {
                    $(this).attr('required',false);
                    $(this).prev('span').attr('class','');
                });
            }
        })
    };

    var list_prf = function () {
        var select2routes = $('#select2routes', document);
        if (select2routes.val() !== '') {
            dt_prf_view_list(select2routes.val(),300);
        } else {
            dt_prf_view_list(false,300);
        }

        //console.log($('#select2routes', document));
        PECO.select2Basic($('#select2routes', document), 'purchasing/select2routes', 'Select route', true, false,false,false,false,select2routes.val());

        $(document).on('change','#select2routes',function () {
            var this_ = $(this);
            var this_val = this_.val();
            dt_prf_view_list(this_val);
        });

        var purchase_list_tab = $('a[data-toggle="tab"]',$('#purchase_list_tab',document));
        var eprs_trn_list = $('#eprs_trn_list',document);

        purchase_list_tab.on('shown.bs.tab', function (e) {
            var this_ = $(this);
            var status = this_.attr('data-id');
            console.log('itemtype : '+ status);
            var msg = '';
            var load = '';

            if (status == 300) {
                msg = "No pending PRF Transactions!";
            }
            if (status == 301) {
                msg = "No approved PRF Transactions!";
            }
            if (status == 302) {
                msg = "No disapproved or cancelled PRF Transactions!";
            }
            PECO.DTDefault(eprs_trn_list,msg);
            dt_prf_view_list(select2routes.val(),status);
        });
    };

    var dt_prf_view_list = function (subroute,status) {
        var eprs_trn_list = $('#eprs_trn_list',document);
        var route = (subroute) ? subroute : false;
        var stat = false;
        //console.log('New route: ' + route);
        var msg = '';
        if (status == 300) {
            msg = "No pending PRF Transactions!";
            stat = [1,300,305];
        }
        if (status == 301) {
            msg = "No approved PRF Transactions!";
            stat = status;
        }
        if (status == 302) {
            msg = "No disapproved or cancelled PRF Transactions!";
            stat = [302,303]
        }
        $.ajax({
            url : PECO.base_url() + 'purchasing/prsviewerlist',
            type : 'post',
            dataType : 'json',
            data : {
                route : route,
                status : stat
            },
            beforeSend: function () {
                PECO.DTphpLoading(eprs_trn_list,'Fetching your PRS list...')
            }
        }).done(function (d) {
            eprs_trn_list.dataTable().empty();
            eprs_trn_list.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: {
                    emptyTable : '<h4><i class="fa fa-warning text-warning"></i> ' + msg + '</h4>'
                },
                aoColumns: d.columns,
                fnRowCallback: function (nRow) {
                    PECO.dtEllipsisBtn(nRow);
                }
            });
        }).fail(function () {

        });
    };

    var init_last_price = function (itemid) {
        var tbl_add_item_quotations = $('#tbl_add_item_quotations',document);
        $.ajax({
            url : PECO.base_url() + 'purchasing/itemlastprice',
            type : 'post',
            dataType : 'json',
            data : {
                itemid : itemid
            },
            beforeSend: function () {
                PECO.DTphpLoading(tbl_add_item_quotations,'Fetching last price...')
            }
        }).done(function (d) {
            tbl_add_item_quotations.dataTable().empty();
            tbl_add_item_quotations.dataTable({
                bDestroy: true,
                bPaginate: false,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                aaData: d.lastprice, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: {
                    emptyTable : '<h4><i class="fa fa-warning text-warning"></i> Item has no last approved purchase!</h4>'
                },
                aoColumns: d.columns,
            });
        }).fail(function () {

        });

        tbl_add_item_quotations.on('click','#btn_view_po',function (e) {
            e.preventDefault();
            var this_ = $(this);
            var this_id = this_.attr('data-id');
            //OPEN NEW TAB FOR PREVIEW
            $.ajax({
                url : PECO.base_url() + 'printer/printpo',
                type : 'post',
                dataType : 'json',
                data : {
                    suppid : this_id
                }
            }).done(function (d) {
                PECO.pdfPreview(d.title,d.html,d.papersize);
            }).fail(function () {

            });

        });
    }

    var addPRFItem = function (dataid) {
        items_bloodhound();

        PECO.select2Basic($('#unitid', document),'query/getunits','Unit...',false,false,$('#unitid', document).val());
        var param = dataid ? '&' + $.param({prfid : dataid}) : '';
        var frm_add_prf_item = $('#frm_add_prf_item',document);
        frm_add_prf_item.on('submit',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url : this_.attr('action'),
                type: this_.attr('method'),
                dataType : 'json',
                data : this_.serialize() + param
            }).done(function (d) {
                if (d.qry) {
                    reset();
                    dt_rfq_items(dataid);
                }
                PECO.initAlerts(d.msg, d.title, d.func);
            }).fail(function () {

            })
        });

        var reset = function () {
            var this_ = $('#frm_add_prf_item',document);
            this_.find('input,textarea').each(function () {
                var this_input = $(this);
                this_input.val('')
                this_input.trigger('change');
                this_input.trigger('change.select2');
            });
        }
        frm_add_prf_item.on('reset',function (e) {
            reset()
        });
    }

    PECO.ellipsisExpand();

    return {
        init : function (subroute) {
            init_prf(subroute);
        },
        new : function (dataid) {
            new_prf(dataid);
        },
        myPRF : function () {
            my_prf();
        },
        approval : function (dataid,type) {
            prf_approval(dataid,type);
        },
        rfq : function (dataid) {
            init_rfq(dataid);
        },
        supplierQuote : function (dataid,supplier) {
            init_supplier_quotation(dataid,supplier);
        },
        rfqApproval : function (dataid,stageid) {
            rfq_approval(dataid,stageid);
        },
        purchaseOrder : function (dataid) {
            purchase_order_handling(dataid);
        },
        payment : function () {
            init_eprs_payments();
        },
        approvalList : function () {

        },
        loadPRFItems : function () {
            dt_po_items();
        },
        listPRF : function () {
            list_prf();
        },
        lastPrice : function (itemid) {
            init_last_price(itemid);
        },
        newItem : function (dataid) {
            addPRFItem(dataid);
        }
    }
}();