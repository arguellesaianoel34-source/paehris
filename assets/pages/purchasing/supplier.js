var SUPPLIER = function () {
    PECO.getHighlightsPlugin();

    var handler_search_supplier = function() {
        var item_category = $('#item_category_search', document);
        var supp_category = $('#item_supplier_search', document);
        var supplier_branch = $('#supplier_branch', document);
        var a = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/itemcategory?query=%QUERY", wildcard: "%QUERY"}
        });

        a.initialize(), item_category.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "names",
            source: a.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<i class="fa fa-tag"></i>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{codes}}</b>, {{names}}</h5>', "</div>", "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {

        }).click(function() {
            PECO.initElScroller($('.tt-dropdown-menu', document));
        });
    };

    var handler_search_vendor = function() {
        var item_category = $('#item_category_search', document);
        var supp_category = $('#item_supplier_search', document);
        var supplier_branch = $('#supplier_branch', document);
        var a = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/itemcategory?query=%QUERY", wildcard: "%QUERY"}
        });

        a.initialize(), item_category.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "names",
            source: a.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<i class="fa fa-tag"></i>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{codes}}</b>, {{names}}</h5>', "</div>", "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {

        }).click(function() {
            PECO.initElScroller($('.tt-dropdown-menu', document));
        });
    };


    var tbl_supplier = function () {
        var tbl_supplier = $('#tbl_supplier', document);

        var date_start = $('#input_date_start', document).val();
        var date_end = $('#input_date_start', document).val();

        $.ajax({
            url: PECO.base_url() + 'purchasing/tblsuppliers',
            type: 'post',
            data: {datestart: date_start, dateend: date_end},
            dataType: 'json',
            beforeSend: function () {
                PECO.DTphpLoading(tbl_supplier, 'Loading assets lists..');
            }
        }).done(function (d) {
            tbl_supplier.DataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                aaData: d.list, // USE FOR DYNAMIC LOCAL TABLE LIST
                language: PECO.DTEmptyMessage('No data yet'),
                aoColumns: d.columns,
                searchHighlight: true
            });
        }).fail(function () {
            PECO.DTphpError(tbl_supplier);
        });
    };
    var init_list = function () {
        tbl_supplier();
        $(document).on('click', '.btn-refresh', function (e) {
            e.preventDefault();
            tbl_supplier();
        });

        //$('.input-daterange').datepicker({});
    };
    var new_supplier_validation = function () {

        var new_supplier = $('#frm_new_supplier',document);

        new_supplier.validate({
            rules: {
                suppliercode: {
                    minlength: 3,
                    required: true
                },
                suppliername: {
                    required: true
                },
                supplierdesc: {
                    required: true
                },
                supplierphone: {
                    required: '#supplier_mobile:blank'
                },
                suppliermobile: {
                    required: '#supplier_phone:blank'
                },
                supplieremail: {
                    required: false,
                    email: true
                },
                accountname: {
                    required: function (element) {
                        return ($('#rfp_account_bank').val().length > 0 || $('#rfp_account_number').val().length > 0);
                    }
                },
                accountbank: {
                    required: function (element) {
                        return ($('#rfp_account_name').val().length > 0  || $('#rfp_account_number').val().length > 0);
                    }
                },
                accountnumber: {
                    required: function (element) {
                        return ($('#rfp_account_bank').val().length > 0 || $('#rfp_account_name').val().length > 0);
                    }
                }
            },
            highlight: function(element) {
                $(element).closest('.control-group').removeClass('has-success').addClass('has-error');
            },
            success: function(element) {
                element
                    .text('OK!').addClass('valid')
                    .closest('.control-group').removeClass('has-error').addClass('has-success');
            },
            submitHandler: function (form) {
                $.ajax({
                    url : new_supplier.attr('action'),
                    type : new_supplier.attr('method'),
                    dataType : 'json',
                    data : new_supplier.serialize()
                }).done(function (d) {
                    PECO.initAlerts(d.msg,d.title,d.func);
                    tbl_supplier();
                }).fail(function () {
                    PECO.phpError();
                });
            }
        });
    }

    var init_new_supplier = function () {
        var new_supplier = $('#frm_new_supplier',document);
        PECO.select2Basic($('#supplier_currency',new_supplier),'systems/select2currency','Select Currency...',false,false,false);
        new_supplier.find('input[data-location]').each(function () {
            var timeout;
            $(this).on('keyup',function () {
                clearTimeout(timeout);
                var this_ = $(this);
                var this_value = this_.val();
                var field = this_.attr('data-field');
                var type = this_.attr('data-type');
                var location = this_.attr('data-location');
                var validation = $('#validation_result',document);
                var this_label = this_.prev();
                console.log(this_label.html());
                $('#res_icon').remove();

                timeout = setTimeout(function () {
                    $.ajax({
                        url : PECO.base_url() + 'purchasing/newsuppliervalidation',
                        type : 'post',
                        dataType : 'json',
                        data : {
                            value : this_value,
                            field : field,
                            type : type,
                            location : location
                        },bofereSend : function () {
                            this_label.after('<span id="res_icon" class="text-primary pull-right"><i class="fa fa-refresh fa-spin"></i></span>');
                        }
                    }).done(function (d) {
                        //console.log(d);
                        this_.after()
                        if (d.qry === false) {
                            validation.removeClass('text-success');
                        } else {
                            validation.removeClass('text-danger');
                        }
                        validation.addClass('text-'+d.func);
                        validation.text(d.msg);
                        this_label.after('<span id="res_icon" class="text-' + d.func + ' pull-right"><i class="'+ d.icon +'"></i></span> ');
                    }).fail(function () {

                    });
                },500);
            });
        });
    }

    return {
        list: function () {
            init_list();
        },
        new: function () {
            init_new_supplier();
            new_supplier_validation();
        },
        update: function () {
            new_supplier_validation();
        }
    }
}();
