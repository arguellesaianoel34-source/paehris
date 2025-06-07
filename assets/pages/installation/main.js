var INSTALLATION = function () {

    var init_application = function (dataid) {
        installation_details_handler(dataid);
        inverter_details_handler(dataid);
        init_installation_items(dataid);
    };

    var dt_inverter_details = function (dataid) {
        var tbl_inverter_details = $('#tbl_inverter_details',document);

        PECO.DTDefault(tbl_inverter_details,'No inverters were listed for this customer!');

        $.ajax({
            url : PECO.base_url() + 'installation/dtinstallinverters',
            type : 'post',
            dataType : 'json',
            data : {
                appid : dataid
            }
        }).done(function (d) {
            if (typeof d.list !== 'undefined' && Object.keys(d.list).length > 0) {
                tbl_inverter_details.dataTable({
                    bDestroy: true,
                    bPaginate: true,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    aaData: d.list,
                    aoColumns: d.columns,
                    searchHighlight: true,
                    language: PECO.DTEmptyMessage(d.empty),
                    fnRowCallback: function (nRow,aData,i) {
                        PECO.select2Basic($('#select2brand',nRow),'installation/select2brand','Select inverter brand...',false,false,$('#select2brand',nRow).val());
                    }
                });
            } else {
                PECO.DTDefault(tbl_inverter_details,'No inverters were listed for this customer.');
            }
        }).fail(function () {

        });
    }

    var installation_items_handler = function (dataid) {
        var installation_items = $('#installation_items',document);
        var installation_tab = $('a[data-toggle="tab"]',installation_items);
        var tbl_components = $('#tbl_components',document);
        //console.log(installation_tab);

        installation_tab.on('shown.bs.tab', function (e) {
            var target = $(e.target).attr('href');
            var itemtype = $(e.target).attr('data-id');
            console.log('itemtype : '+itemtype);
            var msg = '';
            var load = '';

            if (itemtype == 1) {
                msg = "No system components loaded!";
            }
            if (itemtype == 2) {
                msg = "No system Accessories loaded!";
            }
            if (itemtype == 3) {
                msg = "No installation consumables loaded!";
            }
            if (itemtype == 4) {
                msg = "No installation consumables loaded!";
            }
            //PECO.DTDefault(installation_items,msg);
            dt_installation_items(itemtype,dataid);
        });
    }

    var init_installation_items = function (dataid) {
        var installation_items = $('#installation_items',document);
        var sps_params = $('#sps_params',installation_items);
        var installation_item_tabs = $('#installation_item_tabs',installation_items)

        $.ajax({
            url: PECO.base_url() + 'installation/getinstallationsystemsize',
            type: 'post',
            dataType: 'json',
            data: {
                appid : dataid
            }
        }).done(function (d) {
            if (typeof d.tabs !== "undefined") {
                sps_params.html('');
                installation_item_tabs.html(d.tabs);
                dt_installation_items(1,dataid);
                installation_items_handler(dataid);
                if (typeof d.notes !== "undefined") {
                    $('#installation_items_note',installation_items).html(d.notes).addClass('note note-warning');
                }
            }
            if (typeof d.details !== "undefined") {
                sps_params.find('.params').each(function () {
                    var this_ = $(this);
                    var id = this_.attr('id');
                    var val = d.details[id];
                    if (val && (val !== '' || val !== 0)) {
                        this_.text(val);
                    }
                });
                dt_installation_items(1,dataid);
                installation_items_handler(dataid);
                if (typeof d.notes !== "undefined") {
                    $('#installation_items_note',installation_items).html(d.notes).addClass('note note-warning');
                }
            }

            if (typeof d.tabs === "undefined" && typeof d.details === "undefined") {
                $('#installation_items_note',installation_items).html(d.nosetup).addClass('note note-danger');
            }
        });
    }

    var dt_installation_items = function (itemtype,dataid) {
        var tbl_components = $('#tbl_components',document);

        PECO.DTDefault(tbl_components,'No setup items were listed for this customer.');

        $.ajax({
            url : PECO.base_url() + 'installation/dtinstallationsetup',
            type : 'post',
            dataType : 'json',
            data : {
                appid : dataid,
                itemtype : itemtype
            }
        }).done(function (d) {
            if (typeof d.parts !== 'undefined' && Object.keys(d.parts).length > 0) {
                tbl_components.dataTable({
                    bDestroy: true,
                    bPaginate: true,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    aaData: d.parts,
                    aoColumns: d.columns,
                    searchHighlight: true,
                });
            } else {
                PECO.DTDefault(tbl_components,'No inverters were listed for this customer.');
            }
        }).fail(function () {
            PECO.DTphpError(tbl_components,'Error fetching inverter list.');
        });
    }

    var installation_details_handler = function (dataid) {
        var install_date = $('#install_date',document);

        //QUERY FOR SAVED INSTALLATION DATE AND ENERGIZED DATE

        $.ajax({
            url : PECO.base_url() + 'installation/getinstallationdates',
            type : 'post',
            dataType : 'json',
            data : {
                appid : dataid
            }
        }).done(function (d) {
            console.log(d);
            install_date.find('li').each(function () {
                var this_ = $(this);
                var id = this_.attr('id');
                $.each(d[id],function (key,val) {
                    //console.log(key + ' : ' + val);
                    var elem = $('#'+id+'_'+key,this_);

                    if (key === 'text') {
                        if (typeof d[id].value !== "undefined") {
                            $('input', elem).addClass('hidden').after(val);
                        } else {
                            $('input', elem).after(val);
                        }
                    }

                    if (key === 'buttons') {
                        elem.html(val);
                    }
                });
            });
        }).fail(function () {

        });

        install_date.on('click','#btn_edit_date',function () {
            var this_ = $(this);
            var this_td = this_.closest('li');
            var span = $('span',this_td);
            var date = span.attr('data-value');
            var input = $('input',this_td);
            var button_container = $('#date_controls',this_td);

            input.removeClass('hidden').val(date);
            span.addClass('hidden');
            var edit = '';
            edit += '<a href="#save" class="btn btn-sm btn-primary inline" id="btn_save_date"><i class="fa fa-save"></i> </a>';
            edit += '<a href="#cancel" class="btn btn-sm btn-danger inline" id="btn_cancel_edit"><i class="fa fa-times"></i> </a>';
            button_container.html(edit)
        });

        install_date.on('click','#btn_cancel_edit',function () {
            var this_ = $(this);
            var this_td = this_.closest('li');
            var span = $('span',this_td);
            var date = span.attr('data-value');
            var input = $('input',this_td);
            var button_container = $('#date_controls',this_td);

            input.addClass('hidden').val('');
            span.removeClass('hidden');
            var edit = '';
            edit += '<a href="#save" class="btn btn-sm btn-primary inline" id="btn_edit_date"><i class="fa fa-edit"></i> </a>';
            edit += '<a href="#cancel" class="btn btn-sm btn-danger inline" id="btn_remove_date"><i class="fa fa-times"></i> </a>';
            button_container.html(edit)
        });

        install_date.on('click','#btn_save_date',function () {
            var this_ = $(this);
            var this_td = this_.closest('li');
            var span = $('span',this_td);
            var date = span.attr('data-value');
            var input = $('input',this_td);
            var button_container = $('#date_controls',this_td);

            $.ajax({
                url : PECO.base_url() + 'installation/savedate',
                type : 'post',
                dataType : 'json',
                data : {
                    appid : dataid,
                    setdate : input.val(),
                    type : span.attr('data-type')
                }
            }).done(function (d) {
                PECO.initAlerts(d.msg,d.title,d.func);
                if (d.qry !== false) {
                    span.html(d.text).attr('data-value', d.value).removeClass('hidden');
                    input.val('').addClass('hidden');
                    var edit = '';
                    edit += '<a href="#save" class="btn btn-sm btn-primary inline" id="btn_edit_date"><i class="fa fa-edit"></i> </a>';
                    edit += '<a href="#cancel" class="btn btn-sm btn-danger inline" id="btn_remove_date"><i class="fa fa-times"></i> </a>';
                    if (!button_container) {
                        this_.after('<div class="btn-group pull-right" id="date_controls" style="width: 65px !important;">'+edit+'</div>').remove()
                    } else {
                        button_container.html(edit);
                    }
                }
            });
        });

        $(document).on('submit','#frm_inverter_details',function (e) {
            e.preventDefault();
            var this_ = $(this);

            $.ajax({
                url : this_.attr('action'),
                type : this_.attr('method'),
                dataType : 'json',
                data : this_.serialize()
            }).done(function (d) {
                if (d.qry) {
                    dt_inverter_details(dataid);
                }
            }).fail(function (d) {

            });
        });

        $(document).on('click','#btn_finalize_installation',function () {
            var install_date = $('#install_date',document);
            var tbl_inverter_details = $('#tbl_inverter_details',document);

            var dates = install_date.find('input:not(.hidden)');
            var inverters = tbl_inverter_details.find('input[type!=hidden]');

            console.log('dates: ' + dates.length);
            console.log('inverters: ' + inverters.length);

            if (dates.length > 0 || inverters.length > 0) {
                swal("Ooops!", "It seems like some details are still lacking. Please complete them and try again!", "warning");
            } else {
                swal({
                    title: "Finalize Customer Installation",
                    text: "All installation details are complete. Do you want to finalize installation and the application process?",
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonText: "No",
                    cancelButtonClass: "btn-danger",
                    confirmButtonClass: "btn-primary",
                    confirmButtonText: "Yes!",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
                }, function (isConfirm) {
                    if (isConfirm) {
                        $.ajax({
                            url: PECO.base_url() + 'installation/finalizecustomerapplication',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                appid : dataid
                            },
                            cache: false,
                            success: function (d) {
                                swal({
                                    title: d.title,
                                    text: d.msg,
                                    type: d.func
                                });
                            },
                            error: function () {
                                //PECO.phpError();
                                swal({
                                    title: 'PHP Error!',
                                    text: 'Something went wrong!',
                                    type: 'error'
                                });
                                return false;
                            }
                        });
                    } else {
                        swal("Cancelled!", "You choose not to proceed.", "error");
                    }
                });
            }
        });
    }

    var inverter_details_handler = function (dataid) {
        dt_inverter_details(dataid);
        //PECO.select2Basic($('#inverter_type',document),'installation/select2inverter','Select Inverter',false,false,false);
        PECO.select2Basic($('#inverter_brand',document),'installation/select2brand','Select Brand',false,false,false);

        var tbl_inverter_details = $('#tbl_inverter_details',document);

        tbl_inverter_details.on('click','#btn_save_inverter',function (e) {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var inverter_id = $('#inverter_id',this_tr);
            var inverter_brand = $('#select2brand',this_tr);
            var inverter_sn = $('#inverter_sn',this_tr);
            var itemid = this_.attr('data-item');
            var button_container = $('#item_controls',this_tr);

            $.ajax({
                url : PECO.base_url() + 'installation/saveinverterdetails',
                type : 'post',
                dataType : 'json',
                data : {
                    id : inverter_id.val(),
                    appid : dataid,
                    itemid : itemid,
                    brand : inverter_brand.val(),
                    serialnumber : inverter_sn.val()
                }
            }).done(function (d) {
                if (d.qry) {
                    if (typeof d.newid !== "undefined" && parseInt(d.newid) > 0) {
                        inverter_id.val(d.newid);
                    }

                    var brand_td = inverter_brand.closest('td');
                    inverter_brand.select2('destroy').remove();
                    brand_td.append(d.brand);

                    var sn_td = inverter_sn.closest('td');
                    inverter_sn.remove();
                    sn_td.append(d.serialnumber);

                    var edit = '';
                    edit += '<a href="#save" class="btn btn-sm btn-primary inline" id="btn_edit_inverter"><i class="fa fa-edit"></i> </a>';
                    edit += '<a href="#cancel" class="btn btn-sm btn-danger inline" id="btn_remove_inverter"><i class="fa fa-times"></i> </a>';
                    button_container.html(edit);
                }

                PECO.initAlerts(d.msg,d.title,d.func);
            }).fail(function () {

            });
        });

        tbl_inverter_details.on('click','#btn_edit_inverter',function () {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var button_container = $('#item_controls',this_tr);

            this_tr.find('.inverter_editable').each(function () {
                var editable = $(this);
                var editable_id = editable.attr('data-id');
                var editable_value = editable.attr('data-value');
                var editable_td = editable.closest('td');

                editable.addClass('hidden').after('<input class="form-control" id="'+editable_id+'" value="'+ editable_value +'" style="width: 100% !important;">');
                if (editable_id === 'select2brand') {
                    PECO.select2Basic($('#select2brand',editable_td),'installation/select2brand','Select inverter brand...',false,false,editable_value);
                }
            });

            var edit = '';
            edit += '<a href="#save" class="btn btn-sm btn-primary inline" id="btn_save_inverter"><i class="fa fa-save"></i> </a>';
            edit += '<a href="#cancel" class="btn btn-sm btn-danger inline" id="btn_cancel_edit"><i class="fa fa-times"></i> </a>';
            button_container.html(edit);
        });

        tbl_inverter_details.on('click','#btn_cancel_edit',function () {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var button_container = $('#item_controls',this_tr);

            this_tr.find('.inverter_editable').each(function () {
                var editable = $(this);
                var editable_id = editable.attr('data-id');
                var editable_value = editable.attr('data-value');

                editable.removeClass('hidden').after('<input class="form-control" id="'+editable_id+'" value="'+ editable_value +'">');
                var editable_td = editable.closest('td');
                var input = $('input',editable_td);
                if (input.attr('id') === 'select2brand') {
                    input.select2('destroy').remove();
                } else {
                    input.remove();
                }
            });

            var edit = '';
            edit += '<a href="#save" class="btn btn-sm btn-primary inline" id="btn_edit_inverter"><i class="fa fa-edit"></i> </a>';
            edit += '<a href="#cancel" class="btn btn-sm btn-danger inline" id="btn_remove_inverter"><i class="fa fa-times"></i> </a>';
            button_container.html(edit);
        });

        tbl_inverter_details.on('click','#btn_remove_inverter',function () {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var inverter_id = $('#inverter_id',this_tr);
            var col_cnt = $('td',this_tr).length;
            var tr_contents = this_tr.html();
            var button_container = $('#item_controls',this_tr);

            /*this_tr.addClass('table-danger').html('<td colspan="'+col_cnt+'" id="confirm_row"></td>');
            var confirm_row = $('#confirm_row',this_tr);
            confirm_row.html('<div class=""></div>');*/

            //SWAL ASK TO CONTINUE DELETION.
            swal({
                title: "Remove inverter detail?",
                text: "Do you want to remove this inverter's detail?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel : true
            },function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: PECO.base_url() + 'installation/deleteinverterdetails',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            id: inverter_id.val()
                        }
                    }).done(function (d) {
                        swal({
                            title: d.title,
                            text: d.msg,
                            type: d.func
                        });

                        if (d.qry) {
                            inverter_id.val('');

                            this_tr.find('.inverter_editable').each(function () {
                                var editable = $(this);
                                var editable_id = editable.attr('data-id');
                                var editable_value = editable.attr('data-value');
                                var editable_td = editable.closest('td');

                                editable.addClass('hidden').after('<input class="form-control" id="'+editable_id+'" value="" style="width: 100% !important;">');
                                if (editable_id === 'select2brand') {
                                    PECO.select2Basic($('#select2brand',editable_td),'installation/select2brand','Select inverter brand...',false,false,false);
                                }
                            });

                            var edit = '';
                            edit += '<a href="#save" class="btn btn-sm btn-primary inline" id="btn_save_inverter"><i class="fa fa-save"></i> </a>';
                            button_container.html(edit);
                        }
                    }).fail(function () {
                        swal('FAIL!', 'Failed to execute function.', 'error');
                    });
                }
            });
        });
    };

    return {
        application : function (dataid) {
            init_application(dataid);
        }
    };
}();