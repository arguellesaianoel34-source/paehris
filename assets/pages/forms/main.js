var FORMS = function () {
    var formInitialized = false;
    var tnc_handlers = function () {
        textarea($('#inverter_sn',document));
        //console.log('TNC Loaded');
        $('form').on('keydown', function(e) {
            if (e.key === "Enter" && !$(e.target).is('textarea, button, [type="submit"]')) {
                e.preventDefault();
            }
        });

        var frm_tnc = $('#frm_tnc',document);
        var tnc_build_type = $('#tnc_build_type',frm_tnc);

        PECO.select2Types(tnc_build_type,'APPBUILDTYPE','Project Build Type...',false,false,false);

        var tnc_appid = $('#tnc_appid',document);

        if (tnc_appid.val() > 0) {
            var form_details = form_loader('tnc',$('#tnc_form_container',document), {appid : tnc_appid.val()});
            //console.log(form_details);
            if (form_details) {
                delete form_details['form'];
                $.each(form_details, function (i, v) {
                    var input = $('input[name="' + i + '"]', frm_tnc);

                    if (input.length) {
                        input.val(v).attr('disabled',false);
                    }

                    var inputId = input.attr('id');
                    //console.log(inputId + ' : ' + i);

                    $('.optional[data-for="' + inputId + '"]',frm_tnc).iCheck('check').attr('checked', true);
                    input.trigger('change');
                });

                if (form_details['tncid'] > 0) {
                    $('#btn_create_tnc', document).attr('disabled', true).hide().after(form_details['button']);
                    $('#btn_print_tnc', document).attr('disabled', false).show().attr('data-buildtype',form_details['buildtype']);
                }
            }
        }

        frm_tnc.on('click','#btn_tnc_update',function (e) {
            var this_ = $(this);
            var this_id = this_.attr('data-id');
            var addParams = $.param({tncid:this_id});
            swal({
                title: 'Proceed in updating T&C details?',
                text: 'Forms below may refresh after updating. Please save your progress before saving.',
                type: "warning",
                showCancelButton: true,
                cancelButtonText: "No!",
                cancelButtonClass: "btn-danger",
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Yes!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: PECO.base_url() + 'forms/tncupdate',
                        type: 'post',
                        dataType: 'json',
                        data: frm_tnc.serialize() + '&' + addParams,
                        cache: false
                    }).done(function (d) {
                        form_loader('tnc',$('#tnc_form_container',document), {appid : tnc_appid.val()});
                        swal({
                            title: d.title,
                            text: d.msg,
                            type: d.func
                        });
                    }).fail(function () {
                        //PECO.phpError();
                        swal({
                            title: 'PHP Error!',
                            text: 'Something went wrong!',
                            type: 'error'
                        });
                    });
                } else {
                    swal("Cancelled!", "You choose not to proceed.", "error");
                }
            });
        });


        frm_tnc.on('submit',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url : this_.attr('action'),
                type : this_.attr('method'),
                dataType : 'json',
                data : this_.serialize()
            }).done(function (d) {
                //LOAD ALL PAGES RELATED TO PAGE.
                $('#tnc_form_container',document).html(d.form);
                PECO.initAlerts(d.msg,d.title,d.func);
                $('#btn_print_tnc', document).attr('disabled', false).show().attr('data-buildtype',form_details['buildtype']);
            }).fail(function (d) {

            });
        });

        tnc_bloodhound();

        $('.optional',frm_tnc).on('ifChecked',function () {
            var this_ = $(this);
            var this_for = this_.attr('data-for');
            this_.attr('checked', true);
            frm_tnc.find('[id^='+this_for+']').each(function () {
                $(this).attr('disabled',false);
            });
        }).on('ifUnchecked',function () {
            var this_ = $(this);
            var this_for = this_.attr('data-for');
            this_.attr('checked', false);
            frm_tnc.find('[id^='+this_for+']').each(function () {
                $(this).attr('disabled',true);
            });
        });

        frm_tnc.find('.optional').each(function () {
            var this_ = $(this);
            if (this_.attr('checked')) {
                this_.iCheck('check');
            } else {
                this_.iCheck('uncheck');
            }
        });

        frm_tnc.on('click','#btn_print_tnc',function () {
            $.ajax({
                url: PECO.base_url() + 'forms/gettnclayout',
                
            })
        })
    };

    var tnc_mb_handlers = function () {
        $('form').on('keydown', function(e) {
            if (e.key === "Enter" && !$(e.target).is('textarea, button, [type="submit"]')) {
                e.preventDefault();
            }
        });

        $('#main_content .tab-pane').each(function () {
            //GET ATTRIBUTE ID OF THE TAB.
            var tabID = $(this).attr('id');
            var link = $('a[href="#'+tabID+'"]');
            var tab = link.closest('li');

            if (!tab.hasClass('active')) {
                $(this).removeClass('active');
            }
        });

        $('#main-nav a').on('shown.bs.tab', function(event){
            var target_ = $(event.target).attr('href');         // active tab
            var title = $(event.target).html();         // active tab text

            $('#form-title',document).html(title);
        });

        $('#form-title',document).html($('#main-nav .active a').html());

        setTimeout(function () {
            tabs_observer();
        },3000);

        //FOCUS/CLICK ON INPUT WHEN TD IS CLICKED
        $('#form_container',document).on('click','td:not(.note)',function () {
            let input = $(this).find('input');
            //console.log('TD Clicked!' + $('tr', $(this).closest("table")).index(this));
            if (input.length) {
                if (input.is('[class*="icheck"]')) {
                    input.iCheck('toggle');
                    if (input.is(':checkbox')) {
                        checklist_forced_send(input);
                    }
                }else if (input.is(':checkbox, :radio')) {
                    //input.attr('checked',true);
                    input.trigger('click');
                } else {
                    input.trigger('focus');
                }
            }
        });

        icheck_select($('.selectlist',document));
        icheck_select($('.ctt-accept',document));

        tnc_form_handlers($('#tncid',$('#tnc_form_container',document)).val());
        $('#tnc_form_container input[type="checkbox"][name^="checklist"]').each(function () {
            checklist_forced_send($(this));
        });

        $('#tnc_form_container').on('ifChanged', 'input[type="checkbox"][name^="checklist"]', function() {
            checklist_forced_send($(this));
        });

    };

    var icheck_select = function (container) {
        $('.icheck-select',container).each(function () {
            $(this).iCheck({
                radioClass: 'icheckbox_minimal-blue',
                increaseArea: '20%' // optional
            }).on('ifChecked', function(){
                var this_ = $(this);
                this_.attr('checked', true);
            }).on('ifUnchecked', function(){
                var this_ = $(this);
                this_.attr('checked', false);
            });
        });
    }

    var form_loader = function (type,container,data) {
        //load form for now, type is tnc.
        var details;
        $.ajax({
            url : PECO.base_url() + 'forms/load'+type+'form',
            type : 'post',
            dataType : 'json',
            data : data,
            async : false
        }).done(function (d) {
            if (typeof d.form !== 'undefined' && d.form !== '') {
                container.html(d.form);
            }

            details = d;
        }).fail(function (d) {
            return false
        });

        return details;
    }
    
    var textarea = function (el) {
        //console.log(['textarea Handler Loaded',el]);
        var initRows = el.attr('rows');
        var lines;
        el.on('keydown',function (e) {
            var this_ = $(this);
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                lines = this_.val().split("\n").length;
                var rows = this_.attr('rows');

                if (rows <= lines) {
                    this_.attr('rows',lines + 1);
                }
            }

            if (keyCode === 8) {
                lines = this_.val().split("\n").length;
                var rows = this_.attr('rows');
                if (rows >= lines && initRows < lines) {
                    this_.attr('rows',lines - 1);
                }
            }
        });
    }

    var tabs_observer = function () {
        //on show, tab show if parent is active, add class active to first root.
        $(document).ready(function() {

            // Monitor changes to "active" class on parent tab-pane
            $('#main_content',document).on('classChange', '.tab-pane', function() {
                let parentTabPane = $(this);

                if (parentTabPane.hasClass('active')) {
                    parentTabPane.children('.tab-pane').first().addClass('active'); // Add 'active' to first-level child
                }
            });

            // MutationObserver to detect class changes
            const observer = new MutationObserver(mutations => {
                mutations.forEach(mutation => {
                    if (mutation.attributeName === "class") {
                        $(mutation.target).trigger('classChange'); // Trigger custom event when class changes
                    }
                });
            });

            // Apply observer to all .tab-pane elements
            $('#main_content',document).find('.tab-pane').each(function() {
                observer.observe(this, { attributes: true });
            });
        });
    }

    var tnc_bloodhound = function () {
        var appnum = $('#tnc_app_number',document);
        var appid = $('#tnc_appid',document);
        var project_name = $('#tnc_project_name',document);
        var project_location = $('#tnc_project_location',document);

        var a = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "forms/tncapplookup?query=%QUERY", wildcard: "%QUERY"}
        });

        var responseLayout = [
            '<div class="media">',
            '<div class="media-body">',
            '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{appnum}}</b> - {{appname}}</h5>',
            "<p>{{address}}</p>",
            "</div>",
            "</div>"
        ];

        a.initialize(), appnum.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "appnum",
            source: a.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(responseLayout.join("")),
            },
        }).on('typeahead:selected', function(event, selection) {
            //console.log(selection);
            appid.val(selection.sysid);
            appnum.val(selection.appnum);
            project_name.val(selection.appname);
            project_location.val(selection.address);
            //Ajax function to lookup form info
            tnc_details(selection.sysid)
            var form_details = form_loader('tnc',$('#tnc_form_container',document), {appid : selection.sysid});
            //console.log(form_details);
            if (form_details) {
                delete form_details['form'];
                $.each(form_details,function (i,v) {
                    var input = $('input[name="' + i + '"]',$('#frm_tnc',document));

                    if (input.length) {
                        input.val(v).attr('disabled',false);
                    }

                    var inputId = input.attr('id');

                    var checkbox = $('.optional[data-for="' + inputId + '"]',$('#frm_tnc',document));
                    checkbox.iCheck('check').attr('checked', true);
                    input.trigger('change');
                });

                if (form_details['tncid'] > 0) {
                    $('#btn_create_tnc', document).attr('disabled', true).hide().after(form_details['button']);
                    $('#btn_print_tnc', document).attr('disabled', false).show().attr('data-buildtype',form_details['buildtype']);
                }
            }

        }).click(function() {
            PECO.initElScroller($('.tt-dropdown-menu', document));
        });
    };

    var tnc_details = function (appid) {
        /*
        Lookup appid from tnc table
        If form responses exists:
         Load all data to inputs,
         Lock inputs except app name,
         Change "create" button to Delete or Cancel.
         Load all related pages and responses.

        If application does not have a form response:
         Create new form data,
         Leave inputs blank except for inputs with related data to the application,
         Load form pages based on build type.
         */

        var btn_create_tnc = $('#btn_create_tnc',document);

        $.ajax({
            url : PECO.base_url() + 'forms/tncdetails',
            type : 'post',
            dataType : 'json',
            data : {
                appid : appid
            }
        }).done(function (d) {

        }).fail(function (d) {

        });
    }

    var tnc_form_handlers = function (dataid) {
        //console.log($('form',$('#form_container',document)))
         $('#form_container',document).find('form').each(function (e) {
             var this_ = $(this);
             var text = this_.attr('data-text') || 'Save form data?';
             var title = this_.attr('data-title') || 'Continue submitting form?';

             if (this_.find('.tab-pane').length) {
                 this_.attr('novalidate', true); //prevent form validation
             }

             this_.on('submit',function (e) {
                 e.preventDefault();
                 if (this_.find('.tab-pane').length) {
                     var required = this_.find(':input[required]'); //find required fields inside form
                     var isEmpty = required.filter(function () {
                         return !$(this).val();
                     }); //filter required fields to only blank values

                     if (isEmpty.length > 0) {
                         console.log('Some inputs are empty.');
                         var firstEmpty = isEmpty.first(); //select first instance
                         var tabPane = firstEmpty.closest('.tab-pane');
                         if (tabPane.length && !tabPane.hasClass('active')) {
                             var tabID = tabPane.attr('id');
                             var tab = $('a[href="#' + tabID + '"]');

                             if (tab.length) {
                                 tab.tab('show');
                                 setTimeout(function () {
                                     firstEmpty.focus();
                                 }, 500);
                             } else {
                                 firstEmpty.focus();
                             }
                         } else {
                             firstEmpty.focus();
                         }
                     } else {
                         PECO.processSwalForm({
                             form: this_,
                             title: title,
                             text: text,
                             extradata: {dataid: dataid},
                             callback: function (d) {
                                 console.log(d);
                                 if (typeof d.action !== 'undefined') {
                                     responseAction(d.action,d);
                                 }
                             },
                         });
                     }
                 } else {
                     PECO.processSwalForm({
                         form: this_,
                         title: title,
                         text: text,
                         extradata: {dataid: dataid},
                         callback: function (d) {
                             console.log(d);
                             if (typeof d.action !== 'undefined') {
                                 responseAction(d.action,d);
                             }
                         },
                     });
                 }
             });

             //PASTE CELLED DATA TO TABLE
             this_.find('table').each(function () {
                 //console.log({form : this_.attr('id'), table : $(this)});
                 let focusedInput = null;
                 var frm_table = $(this);


                 frm_table.on('focus','input:not([type="checkbox"]):not([type="radio"])',function () {
                     focusedInput = this;
                 });

                 frm_table.on('paste',function (e) {
                     if (!focusedInput) return;

                     e.preventDefault();
                     var clipboardData = e.originalEvent.clipboardData || window.clipboardData;
                     var pastedData = clipboardData.getData('Text');

                     var rows = pastedData.split(/\r?\n/).filter(row => row.trim() !== '');
                     //var cells = rows.map(row => row.split('\t'));

                     var cells = rows.map(row => {
                         if (row.includes('\t')) {
                             return row.split('\t');
                         } else {
                             // Fallback: multiple spaces or smart spacing
                             return row.trim().split(/\s{2,}|\s+\|\s+|\s+\t\s+/); // handles spaces or even " | " or " | "
                         }
                     });

                     var validFields = frm_table
                         .find('input:not([type="checkbox"]):not([type="radio"])')
                         .toArray();

                     var startIndex  = validFields.indexOf(focusedInput);

                     if (startIndex  === -1) return;

                     let inputIndex = startIndex ;
                     for (let r = 0; r < cells.length; r++) {
                         for (let c = 0; c < cells[r].length; c++) {
                             if (inputIndex < validFields.length) {
                                 $(validFields[inputIndex]).val(cells[r][c]);
                                 inputIndex++;
                             }
                         }
                     }
                 });
             });
         });
    };

    var checklist_forced_send = function (element) {
        //Substitute checkbox with hidden field to send value when unchecked
        var checkbox = element;
        var td = checkbox.closest('td');
        var hidden = $('<input>').attr({
            type: 'hidden',
            name: checkbox.attr('name'),
            value: '0',
        }).addClass('forcedsubmit');

        $('.forcedsubmit',td).remove();

        if (!checkbox.is(':checked')) {
            td.append(hidden);
        }

        checkbox.on('change',function () {
            td.find('input.forcedsubmit').remove();
            console.log(checkbox);
            if (!checkbox.is(':checked')) {
                td.append(hidden);
            }
        });
    }

    var formResponses = new Proxy({},{
        set(target, prop, value) {
            target[prop] = value;
        }
    });

    var dci_strings_handler = function (dci) {
        if (dci.length > 0) {
            $.each(dci,function (index,strings) {
                var tbody =  $('tbody #tbl_inv'+index+'_dci',$('#form_container',document));
                tbody.html('');
                if (strings.length > 0) {
                    $.each(strings,function (i,row) {
                        tbody.append(row);
                    })
                }
            });
        }
    }

    var responseAction = function (action,response) {
        switch (action) {
            case 'dci':
                dci_strings_handler(response.dci);
                break;

        }
    }

    var dt_tabled_form = function (table,url,dataid) {
        var data = {dataid:dataid};
        if (table.attr('data-arr')) {
            //var data = JSON.parse(table.attr('data-arr'));
            var dataArr = string_to_object(table.attr('data-arr'));
            if (dataArr) {
                data = $.extend({},data,dataArr);
            }
        }
        $.ajax({
            url : url.startsWith(PECO.base_url()) ? url : PECO.base_url() + url,
            type : 'post',
            dataType : 'json',
            data : data
        }).done(function (d) {
            if ($('thead th',table).length !== Object.keys(d.cols).length) {
                var colsCnt = Object.keys(d.cols).length;
                var thead = $('thead',table);
                thead.html('');
                for (var th = 0; th < colsCnt; th++) {
                    thead.append('<th></th>');
                }
            }

            setTimeout(function () {
                table.dataTable({
                    bDestroy: true,
                    bPaginate: false,
                    bFilter: false,
                    bInfo: false,
                    bStateSave: true,
                    bProcessing: true,
                    aaData: d.rows,
                    aoColumns: d.cols,
                    ordering: false
                });
            },500);
        }).fail(function (d) {

        })
    }

    var string_to_object = function (string) {
        if (string.length > 0) {
            var newObject = {};
            var newArray = string.split(',');
            newArray.forEach(function (v) {
                var split = v.split(':');
                if (split.length === 2) {
                    newObject[split[0].trim()] = split[1].trim();
                }
            });
            return Object.keys(newObject).length > 0 ? newObject : false;
        }
        return false;
    };

    return {
        tnc : function (type) {
            if (type) {
                var funcName = 'tnc_' + type;
                if (typeof this[funcName] === 'function') {
                    this[funcName]();
                }
            } else {
                tnc_handlers();
            }
        },
        tnc_mb : function () {
            tnc_mb_handlers();
        },
        tnc_rb : function () {

        },
        dtTabledForm : function (table,url,dataid) {
            dt_tabled_form(table,url,dataid);
        }
    }
}();