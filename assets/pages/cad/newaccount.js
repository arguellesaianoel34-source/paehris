
var CAD  = function() {

    PECO.getSelect2Plugins();
    PECO.getSweetAlert();

    var form = $('#frm_newaccount');
    var steps = $('.steps');

    var print_requirements = function() {
        var html = '';
        $.ajax({
            url: PECO.base_url() + 'cad/getrequirementsres',
            type: 'post',
            data: {
                'ids': $('#acct_req').val()
            },
            dataType: 'json'
        }).done(function (data) {
            var req_num = data.list.length;
            html += '<ul class="list-group summary column">';
            for (req = 0; req < req_num; req++) {
                //var req_text = data[req].text.substring(0, 45);
                var req_text = data.list[req].text;
                html += '<li class="list-group-item"><span class="label label-default">' + req_text + '</span></li>';
            }
            html += '</ul>';
            PECO.pecoRepPrint('Application Requirements', html);
        });
    };
    var format = function (state) {
        if (!state.id) return state.text;
        return state.text;
    };

    var init_validation_wizard = function() {
        $('#form_wizard_1').find('.button-submit').hide();

        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);

        var contact_person = ('') ? false : true;

        var frm_newaccount = $('#frm_newaccount',document);

        var namerequire = ($('#input_apptype',frm_newaccount).val() > 1) ? false : true;

        $.validator.addMethod(
            "regex",
            function(value, element, regexp) {
                var re = new RegExp(regexp);
                return this.optional(element) || re.test(value);
            },
            "Please check your input."
        );

        $.validator.addMethod(
            "multiPhone",
            function (value, element) {
            var regex = /^(09|\+639|00)\d{9}$/;
            var numbers = value.split(/\s*,\s*/); // Split by commas and trim spaces

            // Validate each number in the list
            return numbers.every(num => regex.test(num));
        }, "Enter valid phone numbers separated by commas.");

        form.validate({
            doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {

                //ESSRNO
                essrno: {
                    required: false
                },
                //profile
                firstname: {
                    required: namerequire
                },
                lastname: {
                    required: namerequire
                },
                middlename: {
                    required: false
                },
                birthdate: {
                    required: false
                },
                email: {
                    required: false,
                    email: true
                },
                phone: {
                    required: '#mobile:blank',
                    regex: /^((0\d{1,2}|\+63\d{1,2})[-\s]?)?\d{7,8}$/
    },
                mobile: {
                    required: '#phone:blank',
                    multiPhone: true
                },
                gender: {
                    required: false
                },
                address: {
                    required: false
                },
                city: {
                    required: false
                },
                country: {
                    required: false
                },
                region: {
                    required: false
                },
                province: {
                    required: false
                },
                addrspecific: {
                    required: false
                },
                googlemap: {
                    required: false
                },

                // ACCOUNT
                acctrate: {
                    required: false
                },
                accttype: {
                    required: false
                },
                conntype: {
                    required: false
                },
                loctype: {
                    required: false
                },
                acctreq: {
                    required: false
                }
            },

            messages: { // custom messages for radio buttons and checkboxes
                mobile: {
                    regex: '09xxxxxxxxx or +639xxxxxxxxx',
                    multiPhone: "Invalid phone number(s). Use format: 09xxxxxxxxx, +639xxxxxxxxx, or 00xxxxxxxxx."
                },
                phone: {
                    regex: "Invalid format. Examples: (033) 3210123, (034) 4412345, (02) 81234567, 0331234567, 3210456, +63324412345"
                }
            },

            errorPlacement: function (error, element) { // render error placement for each input type
                if (element.attr("name") == "gender") { // for uniform radio buttons, insert the after the given container
                    error.insertAfter("#form_gender_error");
                } else {
                    error.insertAfter(element); // for other inputs, just perform default behavior
                }
            },

            invalidHandler: function (event, validator) { //display error alert on form submit
                success.hide();
                error.show();
                PECO.scrollTo(error, -200);
                // PECO.initNicescroll();
            },

            highlight: function (element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
            },

            unhighlight: function (element) { // revert the change done by hightlight
                $(element)
                    .closest('.form-group').removeClass('has-error'); // set error class to the control group
            },

            success: function (label) {

                if (label.attr("for") == "gender") { // for checkboxes and radio buttons, no need to show OK icon
                    label
                        .closest('.form-group').removeClass('has-error').addClass('has-success');
                    label.remove(); // remove error label here
                } else { // display success icon for other inputs
                    label
                        .addClass('valid') // mark the current input as valid and display OK icon
                        .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                }
            },

            submitHandler: function (form) {
                swal({
                    title: "Are you sure?",
                    text: "New account information will be saved!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, save!",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        var form = $('#frm_newaccount');
                        $.ajax({
                            url: form.attr('action'),
                            type: form.attr('method'),
                            data: form.serialize(),
                            dataType: 'json',
                            beforeSend: function () {
                                // $('#query-status').html('Loading..');
                            }
                        }).fail(function (d) {
                            PECO.sweetNotif('Error' , 'Error in execution of function. Please fill-out all required fields and try again.' , 'error')
                        }).done(function (d) {

                            if (d.qry == true) {
                                swal.close();
                                PECO.initAlerts(d.msg, d.title, d.func);
                                success.show();
                                error.hide();
                                form.find('[id^=select2]').each(function () {
                                    $(this).select2('val','');
                                });
                                form[0].reset();
                            } else {
                                if (d.func == 'warning'){
                                    PECO.sweetNotif(d.title ,  d.msg, d.func);
                                } else {
                                    swal.close();
                                    PECO.initAlerts(d.msg, d.title, d.func);
                                }
                                success.hide();
                                error.show();
                            }

                            if (d.submitType === 'update') {
                                reload_datainfo(d.dataid);
                            }
                        });
                    }else{
                        swal.close();
                    }
                });

            }

        });

        // default form wizard

        /*$('#form_wizard_1').bootstrapWizard({
            'nextSelector': '.button-next',
            'previousSelector': '.button-previous',
            onTabClick: function (tab, navigation, index, clickedIndex) {
                return false;

                success.hide();
                error.hide();
                if (form.valid() == false) {
                    return false;
                }
                handleTitle(tab, navigation, clickedIndex);

            },
            onNext: function (tab, navigation, index) {
                success.hide();
                error.hide();

                if (form.valid() == false) {
                    return false;
                }

                handleTitle(tab, navigation, index);
            },
            onPrevious: function (tab, navigation, index) {
                success.hide();
                error.hide();

                handleTitle(tab, navigation, index);
            },
            onTabShow: function (tab, navigation, index) {
                var total = navigation.find('li').length;
                var current = index + 1;
                var $percent = (current / total) * 100;
                $('#form_wizard_1').find('.progress-bar').css({
                    width: $percent + '%'
                });
                if(current == 3) {


                    $("#acct_type", document).val('');
                    $('#acct_type', document).select2("destroy").trigger('change');

                    $("#owner_type", document).attr('disabled', true).val('');
                    $('#owner_type', document).select2("destroy").trigger('change');

                    $("#loc_type", document).attr('disabled', true).val('');
                    $('#loc_type', document).select2("destroy").trigger('change');

                    PECO.select2Basic($('#acct_type', document), 'admin/select2accttype', 'Select ...', true);

                    $('#acct_type', document).change(function() {
                        var this_ = $(this);
                        var this_val = this_.val();
                        if(this_val > 0) {
                            $("#owner_type", document).attr('disabled', false);
                            select2_ownertype();
                        } else {
                            $("#owner_type", document).attr('disabled', true).val('');
                            $('#owner_type', document).select2("destroy").trigger('change');
                        }
                    });


                }
            }
        });*/



        //$('#form_wizard_1').find('.button-previous').hide();
        /*
         $('#form_wizard_1 .button-submit').click(function () {
         alert('Finished! Hope you like it :)');
         }).hide();
         */

        //apply validation on select2 dropdown value change, this only needed for chosen dropdown integration.
        $('#country_list', form).change(function () {
            form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
        });

        $(document).find('#matches_persons_list .md-radio-list').on('click', 'div.md-radio', function(e) {
            e.preventDefault();
            /*
            var md_ = $(this);
            var input_ = md_.find('input');
            var value_ = input_.val();
            alert(value_);
            */
            alert('clicked!');
        });

        $(document).find('#matches_persons_list .md-radio-list').find('input[type=radio]').live('change', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.val();
            $.ajax({
                url: PECO.base_url() + 'cad/getselectednamematched',
                type: 'post',
                data: {'personid': this_val},
                dataType: 'json',
            }).done(function(d){

            }).fail(function(){
                PECO.phpError();
            });
        });
    };

    var reload_datainfo = function (dataid) {
        var customer_info_field = $('#customer_info_field',document);
        $.ajax({
            url: PECO.base_url() + 'cad/getapplicationbasicinfo',
            type: 'post',
            dataType: 'json',
            data: {
                appid: dataid
            }
        }).done(function (d) {
            customer_info_field.html(d.html);
        }).fail(function () {

        });
    };

    var init_validation_wizard_govt = function() {
        $('#form_wizard_1').find('.button-submit').hide();

        var error = $('.alert-danger', form);
        var success = $('.alert-success', form);

        form.validate({
            doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {

                //ESSRNO
                essrno: {
                    required: false
                },
                //profile
                firstname: {
                    required: false
                },
                lastname: {
                    required: false
                },
                middlename: {
                    required: false
                },
                birthdate: {
                    required: false
                },
                email: {
                    required: false,
                    email: true
                },
                phone: {
                    required: true
                },
                mobile: {
                    required: false
                },
                gender: {
                    required: false
                },
                address: {
                    required: true
                },
                city: {
                    required: true
                },
                country: {
                    required: true
                },
                addrspecific: {
                    required: true
                },

                // ACCOUNT
                acctrate: {
                    required: true
                },
                accttype: {
                    required: true
                },
                conntype: {
                    required: true
                },
                loctype: {
                    required: true
                },
                acctreq: {
                    required: true
                }
            },

            messages: { // custom messages for radio buttons and checkboxes
                //'payment[]': {
                //   required: "Please select at least one option",
                //    minlength: jQuery.validator.format("Please select at least one option")
                //}
            },

            errorPlacement: function (error, element) { // render error placement for each input type
                if (element.attr("name") == "gender") { // for uniform radio buttons, insert the after the given container
                    error.insertAfter("#form_gender_error");
                } else {
                    error.insertAfter(element); // for other inputs, just perform default behavior
                }
            },

            invalidHandler: function (event, validator) { //display error alert on form submit
                success.hide();
                error.show();
                PECO.scrollTo(error, -200);
                // PECO.initNicescroll();
            },

            highlight: function (element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
            },

            unhighlight: function (element) { // revert the change done by hightlight
                $(element)
                    .closest('.form-group').removeClass('has-error'); // set error class to the control group
            },

            success: function (label) {

                if (label.attr("for") == "gender") { // for checkboxes and radio buttons, no need to show OK icon
                    label
                        .closest('.form-group').removeClass('has-error').addClass('has-success');
                    label.remove(); // remove error label here
                } else { // display success icon for other inputs
                    label
                        .addClass('valid') // mark the current input as valid and display OK icon
                        .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                }
            },

            submitHandler: function (form) {
                swal({
                    title: "Are you sure?",
                    text: "New account information will be saved!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes, save!",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        var form = $('#frm_newaccount');
                        $.ajax({
                            url: form.attr('action'),
                            type: form.attr('method'),
                            data: form.serialize(),
                            dataType: 'json',
                            beforeSend: function () {
                                $('#query-status').html('Loading..');
                            }
                        }).fail(function (d) {
                            swal("Error: New account!" , d.msg , 'error');
                        }).done(function (d) {
                            PECO.initAlerts(d.msg, 'New Account', d.func);
                            if (d.qry == true) {
                                success.show();
                                error.hide();
                            } else {
                                success.hide();
                                error.show();
                            }
                            swal.close();
                        });
                    }else{
                        swal.close();
                    }
                });

            }

        });

        // default form wizard

        $('#form_wizard_1').bootstrapWizard({
            'nextSelector': '.button-next',
            'previousSelector': '.button-previous',
            onTabClick: function (tab, navigation, index, clickedIndex) {
                return false;

                success.hide();
                error.hide();
                if (form.valid() == false) {
                    return false;
                }
                handleTitle(tab, navigation, clickedIndex);

            },
            onNext: function (tab, navigation, index) {
                success.hide();
                error.hide();

                if (form.valid() == false) {
                    return false;
                }

                handleTitle(tab, navigation, index);
            },
            onPrevious: function (tab, navigation, index) {
                success.hide();
                error.hide();

                handleTitle(tab, navigation, index);
            },
            onTabShow: function (tab, navigation, index) {
                var total = navigation.find('li').length;
                var current = index + 1;
                var $percent = (current / total) * 100;
                $('#form_wizard_1').find('.progress-bar').css({
                    width: $percent + '%'
                });
            }
        });

        $('#form_wizard_1').find('.button-previous').hide();
        /*
         $('#form_wizard_1 .button-submit').click(function () {
         alert('Finished! Hope you like it :)');
         }).hide();
         */

//apply validation on select2 dropdown value change, this only needed for chosen dropdown integration.
        $('#country_list', form).change(function () {
            form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
        });

        $(document).find('#matches_persons_list .md-radio-list').on('click', 'div.md-radio', function(e) {
            e.preventDefault();
            /*
            var md_ = $(this);
            var input_ = md_.find('input');
            var value_ = input_.val();
            alert(value_);
            */
            alert('clicked!');
        });

        $(document).find('#matches_persons_list .md-radio-list').find('input[type=radio]').live('change', function(e) {
            e.preventDefault();
            var this_ = $(this);
            var this_val = this_.val();
            $.ajax({
                url: PECO.base_url() + 'cad/getselectednamematched',
                type: 'post',
                data: {'personid': this_val},
                dataType: 'json',
            }).done(function(d){

            }).fail(function(){
                PECO.phpError();
            });
        });
    };

    var displayConfirm = function () {
        $('#tab5 .form-control-static', form).each(function () {
            var input = $('[name="' + $(this).attr("data-display") + '"]', form);
            if (input.is(":radio")) {
                input = $('[name="' + $(this).attr("data-display") + '"]:checked', form);
            }
            if (input.is(":text") || input.is("textarea")) {
                $(this).html(input.val());
            } else if (input.is("select")) {
                $(this).html(input.find('option:selected').text());
            } else if (input.is(":radio") && input.is(":checked")) {
                $(this).html(input.attr("data-title"));
            }
        });
    };

    var init_verification = function(current, form){
        if(current==4) {
            $.ajax({
                url: PECO.base_url()+'query/getnewcustaccountpreview',
                type: 'post',
                data: form.serialize(),
                dataType: 'json',
                beforeSend: function () {
                    $('#verify_loading').html('<i class="fa fa-spinner  fa-spin fa-pulse text-info" aria-hidden="true"></i> ');
                }
            }).done(function(d){
                $('#app_essrno').html(d.essrno).closest('li').find('#item_check_stats').html(d.essrn_stat);
                $('#app_fname').html(d.firstname).closest('li').find('#item_check_stats').html(d.firstname_count);
                $('#app_mname').html(d.middlename).closest('li').find('#item_check_stats').html(d.middlename_count);
                $('#app_lname').html(d.lastname).closest('li').find('#item_check_stats').html(d.lastname_count);
                $('#app_birthday').html($('#date_birth', document).val()).closest('li').find('#item_check_stats').html('<i class="fa fa-check text-success pull-right"></i>');
                $('#app_email').html(d.email).closest('li').find('#item_check_stats').html(d.emailcount);
                $('#app_mobile').html(d.mobile).closest('li').find('#item_check_stats').html(d.mobilecount);
                $('#app_phone').html(d.phone).closest('li').find('#item_check_stats').html(d.phonecount);
                $('#app_district').html(d.addrdist).closest('li').find('#item_check_stats').html(d.addrdistcount);
                $('#app_account').html(d.appacctmsg).closest('li').find('#item_check_stats').html(d.appacctcnt);
                $('#app_address').html(d.addrspec).closest('li').find('#item_check_stats').html('<i class="fa fa-check text-success pull-right"></i>');

                $(document).find('#input_acctex').val(d.acctex);
                $(document).find('#input_acctra').val(d.acctra);

                if(d.qry == true) {
                    $('#verify_loading').html('<i class="fa fa-check text-success"></i> ');
                    $('#verfiy_result').html(d.html);
                    if (Number(d.num) > 0) {
                        $('#verify_message').html('<span class="text-danger"><i class="fa fa-warning"></i> Note: This application needs to be forwarded to Legal Department for checking..</span>');
                    }

                }
                console.log(d);
            });
        }


    };

    var handleTitle = function (tab, navigation, index) {
        var total = navigation.find('li').length;
        var current = index + 1;
        // init_verification(current, $('#frm_newaccount'));


        // set wizard title
        $('.step-title', $('#form_wizard_1')).text('Step ' + (index + 1) + ' of ' + total);
        // set done steps
        jQuery('li', $('#form_wizard_1')).removeClass("done");
        var li_list = navigation.find('li');
        for (var i = 0; i < index; i++) {
            jQuery(li_list[i]).addClass("done");
        }

        if (current == 1) {
            $('#form_wizard_1').find('.button-previous').hide();
            $('#form_wizard_1').find('.button-submit').hide();
        } else {
            if (current == total) {
                $('#form_wizard_1').find('.button-submit').show();
            }
            $('#form_wizard_1').find('.button-previous').show();
        }

        if (current >= total) {
            $('#form_wizard_1').find('.button-next').hide();
            $('#form_wizard_1').find('.button-submit').show();
            displayConfirm();
        } else {
            $('#form_wizard_1').find('.button-next').show();
            $('#form_wizard_1').find('.button-submit').hide();
        }
        PECO.scrollTo($('.page-title'));
        //PECO.initNicescroll();
    };

    var getbarangay = function(distid) {
        PECO.select2Basic($('#brgy' , document) , 'cad/getbarangays' , 'Select Barangay' , false,false,false,false,false , distid);
    };

    var reset_owner_info = function () {
        var ids = [];
        form.find('input').not('#input_appid').each(function () {
            $(this).val('');
            ids.push($(this).attr('id'));
            console.log('Inputs cleared.');
        });
        form.find('radio,checkbox').each(function () {
            $(this).attr('checked',false);
            ids.push($(this).attr('id'));
            console.log('Check and radio boxes cleared.');
        });
        form.find('select').each(function () {
            $(this).attr('selected',false);
            ids.push($(this).attr('id'));
            console.log('Selections cleared.');
        });
        form.find('textarea').each(function () {
            $(this).text('');
            ids.push($(this).attr('id'));
            console.log('Textarea cleared.');
        });
        form.find('[id^=select2]:not([id=select2_country])').each(function () {
            var this_ = $(this);
            this_.val('');
            ids.push($(this).attr('id'));
            this_.trigger('change');
        });
        console.log(ids);
    };

    var init_customers_applications = function () {

        var selectjobtype = $(document).find('#selectjobtype');

        var frm_newaccount = $('#frm_newaccount',document);
        var non_residential = $('#non_residential',frm_newaccount);

        frm_newaccount.find();

        frm_newaccount.on('click','#btn_new_owner',function () {
            //alert($(this).attr('id') + ': Clicked');
            //frm_newaccount.reset();
            var this_ = $(this);
            swal({
                title: "Change Owner/Representative",
                text: "Continue changing owner or authorized representative?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-info",
                confirmButtonText: "Proceed!",
                closeOnConfirm: true,
                closeOnCancel: false
            }, function(isConfirm) {
                if (isConfirm) {
                    reset_owner_info();
                    $('#btn_update_owner',frm_newaccount).html('<i class="fa fa-save"></i> Save');
                    this_.addClass('hidden');
                    $('#btn_cancel_newowner',frm_newaccount).removeClass('hidden');
                    $('#btn_cancel_application',frm_newaccount).addClass('hidden');
                }else{
                    swal.close();
                }
            });

        });

        frm_newaccount.on('click','#btn_cancel_newowner',function () {
            var this_ = $(this);
            var appid = $('#input_appid',frm_newaccount).val();
            swal({
                title: "Are you sure?",
                text: "Cancel creating new owner?.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, cancel current operation!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url : PECO.base_url() + 'cad/retrieveapplicationinfo',
                        type : 'post',
                        dataType : 'json',
                        data : {
                            appid : appid
                        }
                    }).done(function (data) {
                        if (data) {
                            $('#personid',frm_newaccount).val(data.personid);
                            $('#lastname',frm_newaccount).val(data.lastname);
                            $('#firstname',frm_newaccount).val(data.firstname);
                            $('#middlename',frm_newaccount).val(data.middlename);
                            frm_newaccount.find('radio').each(function () {
                                if ($(this).val() === data.gender) {
                                    $(this).attr('checked',true);
                                }
                            });
                            $('#phone',frm_newaccount).val(data.contactphone);
                            $('#mobile',frm_newaccount).val(data.contactmobile);
                            $('#email',frm_newaccount).val(data.contactemail);
                            $('#select2_country',frm_newaccount).val(data.country).trigger('change');
                            $('#select2_region',frm_newaccount).val(data.region).trigger('change');
                            $('#select2_province',frm_newaccount).val(data.province).trigger('change');
                            $('#select2_citymun',frm_newaccount).val(data.city).trigger('change');
                            $('#suffix',frm_newaccount).find('option').each(function () {
                                if ($(this).val() === data.suffix) {
                                    $(this).attr('selected',true);
                                }
                            }).trigger('change');
                            $('#marital',frm_newaccount).find('option').each(function () {
                                if ($(this).val() === data.marital) {
                                    $(this).attr('selected',true);
                                }
                            }).trigger('change');
                            $('#addrspecific',frm_newaccount).text(data.addrspec);
                            $('#addrgmap',frm_newaccount).val(data.geolink);
                        }
                        swal('Canceled','Changing Owner has been cancelled!','success');
                        this_.addClass('hidden');
                        $('#btn_new_owner',frm_newaccount).removeClass('hidden');
                        $('#btn_cancel_application',frm_newaccount).removeClass('hidden');
                        $('#btn_update_owner',frm_newaccount).html('<i class="fa fa-check"></i> Update');
                    }).fail(function () {
                        PECO.phpError();
                    });
                }else{
                    swal.close();
                }
            });
        });


        /*$('.icheck', frm_newaccount).each(function(){
            $(this).iCheck({
                checkboxClass: 'icheckbox_square-red', // minimal / square / polaris / futurico // red / green / blue
                radioClass: 'iradio_square-red',
                increaseArea: '20%' // optional
            }).on('ifChecked', function(event){
                var this_ = $(this);
                this_.attr('checked', true);
                apptype = this_.val();
                alert(event.type + ' callback');
            }).on('ifUnchecked', function(event){
                var this_ = $(this);
                this_.attr('checked', false);
                alert(event.type + ' callback');
            });
        });

        $('#btn_reset',frm_newaccount).on('click',function () {
            frm_newaccount.find('[id^=select2]').each(function () {
                $(this).select2('val','');
            });
            frm_newaccount[0].reset();
        });*/

        var apptype = 0;

        var non_res_html = '';
        non_res_html += '<div class="form-group margin-top-10" id="non_res_details">';
        non_res_html += '<label class="col-md-3 control-label"><span class="required"></span> Establishment</label>';
        non_res_html += '<div class="col-md-6">';
        non_res_html += '<input name="corpname" type="text" class="form-control data-entry input-lg" id="corpname" placeholder="Establishment name..." data-toggle="autocomplete" col-name="corpname" value>';
        non_res_html += '<div class="form-control-focus"> </div>';
        non_res_html += '</div>';
        non_res_html += '<div class="col-md-3">';
        non_res_html += '<input name="corpbranch" type="text" class="form-control data-entry input-lg" id="corpbranch" placeholder="Branch" data-toggle="autocomplete" col-name="corpbranch" value>';
        non_res_html += '<div class="form-control-focus"> </div>';
        non_res_html += '</div>';
        non_res_html += '</div>';
        non_res_html += '<hr>';
        non_res_html += '<div class="row">';
        non_res_html += '<div class="col-md-8">';
        non_res_html += '<h4>Contact Person</h4>';
        non_res_html += '</div>';
        non_res_html += '<div class="col-md-4 pull-right">';
        non_res_html += '<label>';
        non_res_html += '<input name="no_person" id="no_person" value="1" type="checkbox" data-checkbox="icheckbox_flat-red" class="icheck"/> No Contact Person';
        non_res_html += '</label>';
        non_res_html += '</div>';
        non_res_html += '</div>';

        var no_person = $('#no_person',frm_newaccount);


        //Event on change of AppType selection.
        $(document).find('#apptype_row .icheck-inline .icheck').each(function () {
            console.log('#apptype_row found.');
            $(this).on('ifChecked',function (event) {
                var this_ = $(this);
                this_.attr('checked', true);
                $('#input_apptype',frm_newaccount).attr('disabled',true);
                console.log('radio change');
                apptype = this_.val();
                if (apptype > 1) {
                    if (!non_residential.find('#non_res_details').length) {
                        non_residential.html(non_res_html).hide().fadeIn(200);
                    }

                    $('#no_person',frm_newaccount).iCheck({
                        checkboxClass: 'icheckbox_flat-red', // minimal / square / polaris / futurico // red / green / blue
                        increaseArea: '20%' // optional
                    }).on('ifChecked', function () {
                        var this_ = $(this);
                        this_.attr('checked', true);
                        //alert('checked');
                        disable_person_info(true);
                    }).on('ifUnchecked', function () {
                        var this_ = $(this);
                        this_.attr('checked', false);
                        disable_person_info(false);
                    });

                    $('#no_person',frm_newaccount).iCheck('uncheck').attr('checked',false);
                } else {
                    non_residential.html('');
                }
            }).on('ifUnchecked',function (event) {
                var this_ = $(this);
                this_.attr('checked', false);
                disable_person_info(false);
            });
        });

        $('#has_referral',frm_newaccount).on('ifChecked', function () {
            var this_ = $(this);
            this_.attr('checked', true);
            disable_referral_info(false);
        }).on('ifUnchecked', function () {
            var this_ = $(this);
            this_.attr('checked', false);
            disable_referral_info(true);
        });

        referrer_lookup();

        var disable_person_info = function (trigger) {
            $('#person_info',frm_newaccount).find('input,select').each(function () {
                $(this).attr('disabled',trigger);
            });
        };

        var disable_referral_info = function (trigger) {
            $('#referral_info',frm_newaccount).find('input,select').each(function () {
                $(this).attr('disabled',trigger);
                if (trigger) {
                    $(this).val([]).trigger('change');
                }
            });
        };

        //Event when AppType is already selected on page load.
        $(document).find('.icheck-inline .icheck').each(function () {
            var this_ = $(this);
            if (this_.is(':checked')) {
                //alert(apptype);
                apptype = this_.val();
                if (apptype != 1) {
                    non_residential.html(non_res_html).hide().fadeIn(200);
                    $('#no_person',frm_newaccount).on('ifChecked', function () {
                        var this_ = $(this);
                        this_.attr('checked', true);
                        //alert('checked');
                        disable_person_info(true);
                    }).on('ifUnchecked', function () {
                        var this_ = $(this);
                        this_.attr('checked', false);
                        disable_person_info(false);
                    });
                } else {
                    non_residential.html('');
                    disable_person_info(false);
                }
            }
        });


        $('#marital', document).select2({'placeholder': 'Marital Status', allowClear: true,}).change(function() {
            var this_val = $(this).val();
            if(this_val == 5) {
                $('#partner_info').removeClass('hidden');
            }else{
                $('#partner_info').addClass('hidden')
            }
        });

        PECO.select2Basic($('#select2_du',document),'cad/select2du','Distribution Utility...',true,false,false);

        //PECO.select2Basic(selectjobtype, 'cad/getjobtype', 'Select Job Type..', false, false, false);
        $('#corpandgovinfo').addClass('hidden');

        $(document).on('change' , '#district_select' , function () {
            var this_ = $(this);
            if(this_.val() != ''){
                getbarangay(this_.val());
                $(document).find('#brgy').removeAttr("disabled");
            }else{
                getbarangay();
                $(document).find('#brgy').attr('disabled', 'true');
            }
        });

        $('#application_type').select2({
            "allowClear": true,
            "placeholder": 'Select Application Type'
        }).change(function () {
            var this_ = $(this).val();
            if(this_ == ''){
                $('#corpandgovinfo').addClass('hidden');
            }else{
                if (this_ == 1){
                    $('#corpandgovinfo').addClass('hidden');
                }else{
                    $('#corpandgovinfo').removeClass('hidden');
                    if(this_ == 2){
                        $(document).find('#typename').text("Corp. :");
                    }else if(this_ == 3){
                        $(document).find('#typename').text("Gov. :");
                    }
                }
            }
        });

        form.find('input[name$=name]').not('input[name=corpname]').each(function () {
            var this_ = $(this);
            var newVal = false;
            this_.on('blur',function () {
                //console.log(this_.attr('name') + ' value: ' + this_.val());
                newVal = capitalEachWord(this_.val());
                this_.val(newVal);
                //console.log(newVal);
            });
        });

        $('#btn_print_req').click(function(e) {
            e.preventDefault();
            var this_ = $(this);
            PECO.print_acct_requirements(this_.attr('data-id'));
        });
        $("#mask_number").inputmask({
            "mask": "9",
            "repeat": 10,
            "greedy": false
        });

        $("#podate").inputmask("d/m/y", {
            autoUnmask: true
        });
        $("#waranty").inputmask("d/m/y", {
            autoUnmask: true
        });
        $("#acct_issued").inputmask("d/m/y", {
            autoUnmask: true
        });

        $('#suffix').select2({'placeholder': 'Suffix', allowClear: true,});
        $('#prefix').select2({'placeholder': 'Prefix', allowClear: true});

        $('#draft_button').click(function () {
            alert('Under Construction.');
        });

        $('#cancel_button').click(function () {
            alert('Under Construction.');
        });

        $("#country_list").select2({
            placeholder: "Select",
            allowClear: true,
            formatResult: format,
            formatSelection: format,
            escapeMarkup: function (m) {
                return m;
            }
        });




        $('#suffix').select2({'placeholder': 'Suffix', allowClear: true,});
        $('#prefix').select2({'placeholder': 'Prefix', allowClear: true});

        // ################################################
        $('#draft_button').click(function () {
            alert('Under Construction.');
        });

        $('#cancel_button').click(function () {
            alert('Under Construction.');
        });
        // ######################################################

        $("#mask_number").inputmask({
            "mask": "9",
            "repeat": 10,
            "greedy": false
        });

        $("#podate").inputmask("d/m/y", {
            autoUnmask: true
        });
        $("#waranty").inputmask("d/m/y", {
            autoUnmask: true
        });
        $("#acct_issued").inputmask("d/m/y", {
            autoUnmask: true
        });


        $("#acct_rate", document).val('');

        $("#stat_conn", document).attr('disabled', true).val('');
        $('#stat_conn', document).select2("destroy").trigger('change');

        $("#owner_type", document).attr('disabled', true).val('');
        $('#owner_type', document).select2("destroy").trigger('change');

        $("#loc_type", document).attr('disabled', true).val('');
        $('#loc_type', document).select2("destroy").trigger('change');



        $(document).on('change', '#tab3 .crit input', function() {
            tbl_requirements();
        });


        $("#acct_user").select2({
            //url: base_url+"admin/sample_select2",
            tags: true,
            triggerChange: true,
            allowClear: true,
            maximumSelectionLength: 3,
            ajax: {
                url: base_url + "admin/get_user_basic/",
                dataType: 'json',
                quietMillis: 100,
                data: function (term) {
                    return {
                        term: term
                    };
                },
                results: function (data) {
                    var myResults = [];
                    $.each(data, function (index, item) {
                        myResults.push({
                            'id': item.id,
                            'text': item.text
                        });
                    });
                    return {
                        results: myResults
                    };
                }

            },
        }).change(function () {
            // ADD AJAX UPDATE IF APPLICABLE //
            console.log('USER: ' + $(this).val());
        });

        if (!jQuery().pulsate) {
            return;
        }
        // INITIALIZE LAST NAME AS DROP DOWN PERSON MENU
        // FUNCTION(elementid, detailed = bolean);


        $('#editprofile').click(function () {
            if ($(this).is(':checked')) {
                $('#district_select').attr('disabled', false);
                $('#city_select').attr('disabled', false);
                $('#country_list').attr('disabled', false);
                $('#phone').attr('disabled', false);
                $('#mobile').attr('disabled', false);
                $('#email').attr('disabled', false);
                $('#addrspecific').attr('disabled', false);
            }
            else {
                disabletab3();
            }
        });

        $('#checkcust').click(function () {
            if ($(this).is(':checked')) {
                var addrcity = $('#city_select').val();
                var addrdistrict = $('#district_select').val();
                var country = $('#country_list').val();

                $('#phonecust').html($('#phone').val());
                $('#mobilecust').html($('#mobile').val());
                $('#emailcust').html($('#email').val());
                $('#custspecific').html($('#addrspecific').val());
                $('#custaddress').html(function () {
                    $.ajax({
                        url: base_url + "query/getaddress/",
                        dataType: 'json',
                        type: "POST",
                        data: {
                            'addrcity': addrcity,
                            'addrdistrict': addrdistrict,
                            'country': country
                        }
                    }).done(function (d) {
                        console.log(d.address);
                        $('#custaddress').html(d.address);
                    }).fail(function () {
                        console.log('error');
                    });
                });
                $('#checkedcust').removeClass('hidden');
                $('#uncheckedcust').addClass('hidden');
            } else {
                $('#checkedcust').addClass('hidden');
                $('#uncheckedcust').removeClass('hidden');
            }
        });


        $("#acct_type_selection").select2({
            //url: base_url+"admin/sample_select2",
            tags: true,
            triggerChange: true,
            allowClear: true,
            maximumSelectionLength: 3,
            ajax: {
                url: base_url + "admin/get_types",
                dataType: 'json',
                quietMillis: 100,
                data: function (term) {
                    return {
                        term: term
                    };
                },
                results: function (data) {
                    var myResults = [];
                    $.each(data, function (index, item) {
                        myResults.push({
                            'id': item.id,
                            'text': item.text
                        });
                    });
                    return {
                        results: myResults
                    };
                }

            },
            initSelection: function (element, callback) {
                $.ajax({
                    url: base_url + "admin/get_types",
                    dataType: 'json',
                }).done(function (data) {
                    var selections = [];
                    $.each(data, function (index, item) {
                        selections.push({
                            'id': item.id,
                            'text': item.text
                        });
                    });

                    callback(selections)

                });

            },
        }).change(function () {
            console.log($(this).val());
            // ADD AJAX UPDATE IF APPLICABLE //
        });

        $('#country_select, #city_select, #district_select').each(function () {
            $(this).select2();
        });

        // PRINT REQUIREMENTS
        $('#btn_print_req').click(function (e) {
            e.preventDefault();
            print_requirements();
        });

        // Customer Account Preview on tab 5
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href");
            if (target == '#tab5') {
                var input = $('input:disabled').attr('disabled', false);
                var textarea = $('textarea:disabled').attr('disabled', false);
                var select = $('select:disabled').attr('disabled', false);



                $.ajax({
                    url: PECO.base_url() + 'query/generateinputstohtml',
                    data: $('#frm_newaccount').serialize(),
                    type: 'POST',
                    dataType: 'json'
                }).done(function (d) {
                    $('#input_summary', document).html(d.html);
                    setTimeout(function() {
                        PECO.initMapDrawer('#google_map_preview', d.lat, d.lon, d.zoom);
                    },500);
                }).fail(function (d) {
                    console.log(d);
                });

            }
        });
    };

    var referrer_lookup = function () {
        var referral_info = $('#referral_info',document);
        var personid = $('#personid', referral_info);
        var ref_lastname = $('#ref_lastname', referral_info);
        var ref_firstname = $('#ref_firstname', referral_info);
        var ref_middlename = $('#ref_middlename', referral_info);
        var ref_suffix = $('#ref_suffix', referral_info);
        var ref_mobile_number = $('#ref_mobile_number', referral_info);
        var ref_phone_number = $('#ref_phone_number', referral_info);

        var a = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/referrersearch?query=%QUERY", wildcard: "%QUERY"}
        });

        a.initialize(), ref_lastname.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "lastname",
            source: a.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<img src="{{img}}" width="50" height="50"/>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{lastname}}</b>, {{firstname}} {{middlename}}{{suffixtxt}}</h5>', "<p>{{district}} - {{addr}}</p>", "</div>", "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {
            console.log(selection);
            personid.val(selection.sysid);
            ref_firstname.val(selection.firstname);
            ref_middlename.val(selection.middlename);
            if (selection.suffix > 0) {
                ref_suffix.val(selection.suffix).trigger('change');
            }
            ref_mobile_number.val(selection.mobile);
            ref_phone_number.val(selection.phone);
        }).click(function() {
            PECO.initElScroller($('.tt-dropdown-menu', document));
        });
    }


    var select2_accstat = function() {
        var data = { 'codes': 'SAPPS', 'full': 0};
        PECO.select2Basic($("#stat_conn", document), 'cad/getapplicationparam', 'Select define..', false, false, false, false, false, data);
        $("#stat_conn", document).change(function() {
            var this_ = $(this);
            var this_val = this_.val();
            if(this_val > 0) {
                $("#owner_type", document).attr('disabled', false);
                select2_ownertype();
            } else {
                $("#owner_type", document).attr('disabled', true).val('');
                $('#owner_type', document).select2("destroy").trigger('change');
            }
        });
    };

    var select2_ownertype = function() {
        var data = { 'codes': 'CADOWNERTYPE', 'full': 1};
        PECO.select2Basic($("#owner_type", document), 'cad/getapplicationparam', 'Select ownership..', false, false, false, false, false, data);
        $("#owner_type", document).change(function() {
            var this_ = $(this);
            var this_val = this_.val();
            if(this_val > 0) {
                $("#loc_type", document).attr('disabled', false);
                select2_paytype();
            } else {
                $("#loc_type", document).attr('disabled', true).val('');
                $('#loc_type', document).select2("destroy").trigger('change');
            }
        });
    };

    var select2_paytype = function() {
        var data = { 'codes': 'CADAPPPAYTYPE', 'full': 1};
        PECO.select2Basic($("#pay_type", document), 'cad/getapplicationparam', 'Select ownership..', false, false, false, false, false, data);
    };


    var tbl_requirements = function() {
        var tbl_temp_requirements = $('#tbl_basic_req', document);
        var tbl_summary_requirements = $('#tbl_summary_req', document);

        $.ajax({
            url: PECO.base_url() + 'cad/getrequirements',
            type: 'post',
            dataType: 'json',
            data: {
                'acctype': $('#acct_type', document).val(),
                'ownertype': $('#owner_type', document).val(),
                'paytype': $('#pay_type', document).val()
            },
            beforeSend: function() {
                PECO.DTphpLoading(tbl_temp_requirements, 'Loading requirements...');
                PECO.DTphpLoading(tbl_summary_requirements, 'Loading requirements...');
            }
        }).done(function(d) {
            tbl_temp_requirements.dataTable({
                bDestroy: true,
                bPaginate: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                bFilter: false,
                aaData: d.list,
                order: [[0,'asc']],
                aoColumns: [
                    {"data":"num", sClass: 'text-align-right', sWidth: '30px'},
                    {"data":"name", sClass:'text-info',sWidth:'90%'}
                ]
            });
            tbl_summary_requirements.dataTable({
                bDestroy: true,
                bPaginate: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                bFilter: false,
                aaData: d.list,
                order: [[0,'asc']],
                aoColumns: [
                    {"data":"num", sClass: 'text-align-right', sWidth: '30px'},
                    {"data":"name", sClass:'text-info',sWidth:'90%'}
                ]
            });
        }).fail(function() {
            PECO.DTphpError(tbl_temp_requirements);
            PECO.DTphpError(tbl_summary_requirements);
        });
    };


    var init_corporation = function() {
        PECO.select2Basic($('#acct_rate', document), 'admin/get_rate_class_corp', 'Select ...', true);
        $('#acct_rate', document).change(function() {
            var this_ = $(this);
            var this_val = this_.val();
            if(this_val > 0) {
                $("#stat_conn", document).attr('disabled', false);
                select2_accstat();
            } else {
                $("#stat_conn", document).attr('disabled', true).val('');
                $('#stat_conn', document).select2("destroy").trigger('change');
            }
        });
    };

    var init_government = function() {
        PECO.select2Basic($('#acct_rate', document), 'admin/get_rate_class_corp', 'Select ...', true);
        $('#acct_rate', document).change(function() {
            var this_ = $(this);
            var this_val = this_.val();
            if(this_val > 0) {
                $("#stat_conn", document).attr('disabled', false);
                select2_accstat();
            } else {
                $("#stat_conn", document).attr('disabled', true).val('');
                $('#stat_conn', document).select2("destroy").trigger('change');
            }
        });
    };

    var disabletab3 = function() {
        $('#district_select').attr('disabled', true);
        $('#city_select').attr('disabled', true);
        $('#country_list').attr('disabled', true);
        $('#phone').attr('disabled', true);
        $('#mobile').attr('disabled', true);
        $('#email').attr('disabled', true);
        $('#addrspecific').attr('disabled', true);
    };

    var pulsate = function() {
        jQuery('#pulsate-regular').pulsate({
            color: "#399bc3",
            reach: 50,
            repeat: 2,
            speed: 500,
            glow: true
        }).find('a').focus();
    };

    var removeDisabledInputInform = function() {
        $('#frm_newaccount').each(function (e) {

            // INDIVIDUAL
            if ($('#apptype').val() == 1) {
                $('#firstname').val('').attr('disabled', false);
                $('#middle_initial').val('').attr('disabled', false);
                $('#suffix').val('').attr('disabled', false);
                $('#prefix').val('').attr('disabled', false);
                $('#birthdate').val('').attr('disabled', false);
                $('#marital').val('').attr('disabled', false);
                $('#gender').val('').attr('disabled', false);
            }

            // CORPORATION
            if ($('#apptype').val() == 2) {
                $('#corp_district').val('').attr('disabled', false);
                $('#corp_addrspecific').val('').attr('disabled', false);

                if ($('#persontype').val() == 1) {
                    $('#firstname').val('').attr('disabled', false);
                    $('#middle_initial').val('').attr('disabled', false);
                    $('#suffix').val('').attr('disabled', false);
                    $('#prefix').val('').attr('disabled', false);
                    $('#birthdate').val('').attr('disabled', false);
                    $('#marital').val('').attr('disabled', false);
                    $('#gender').val('').attr('disabled', false);
                }
            }

            /*
             if ($('#profiletype').val() === '1' || $('#profiletype').val() === '') {
             $(this).find('input:not(#apptype):input:not(#lastname):not(input[name="gender"]):not(#moduleid):not(#stagelevel)').val('');
             } else {
             $(this).find('input:not(#profiletype):not(#acctidentity):not(#moduleid):not(#stagelevel):not(#corpidentity):not(#lastname):not(input[name="gender"]):not(#corp_name):not(#corpaddrspecific):not(#corp_district):not(#city_select):not(#district_select):not(#country_select)').val('');
             }
             $(this).find('input').attr('disabled', false);
             */
        });
    };

    // ##############################################################
    // CAD EVALUATION MODULE ########################################
    var table_services = $('#tbl_services');
    var init_evaluation = function(dataid){
        var frm_service = $('#frm_add_service');
        // MESSAGE TO CONSOLE DEVELOPMENT MODE
        if(PECO.sysCheckMode()==true) {
            console.log('Evaluation script initialized!');
        }

        init_dt_services(dataid);
        PECO.select2Basic($('#serv_item'), 'cad/selectservicematerials', 'Select Labor / Services...', false, false);

        $('#frm_add_service').submit(function(e) {
            e.preventDefault();
            var frm_submit = PECO.ajaxFormSubmit(frm_service);
            if(frm_submit) {
                init_dt_services(dataid);
            }
        });
        PECO.initMapSpec('#custmap', dataid, 0);

        $('body').on('click', '#btn_process_ar', function(e){
            e.preventDefault();
            var this_ = $(this);
            $.SmartMessageBox({
                    title: "<i class='fa fa-question fa-fw fa-lg txt-color-yellow'></i> Confirm: Process Account Receivables.</span>",
                    content: 'Please confirm action taken',
                    buttons: '[Yes][No]',
                    buttonsPosition: 'right',
                    buttonClass: 'btn-primary, btn-danger',
                    buttonsIcon: 'fa-angle-double-right, fa-times',
                    inputIcon: 'fa fa-user',
                    inputIconPosition: 'left',
                },
                function (ButtonPressed) {
                    if (ButtonPressed === "Yes") {
                        $.ajax({
                            url: PECO.base_url()+'cad/processar',
                            type: 'post',
                            data: {'id': this_.attr('data-id')},
                            dataType: 'json',
                            beforeSend: function(){
                                this_.attr('disabled', true);
                            }
                        }).done(function (data) {
                            if(PECO.sysCheckMode()==true) {
                                console.log(data);
                            }
                            if (data.qry == true) {
                                var func = (data.func) ? data.func : 'success';
                                PECO.initAlerts(data.msg, 'Account Receivable', func, true);
                                this_.attr('disabled', true);
                                init_dt_services(dataid);
                            } else {
                                var func = (data.func) ? data.func : 'warning';
                                PECO.initAlerts(data.msg, 'Account Receivable', func, true);
                                this_.attr('disabled', false);
                            }
                        }).fail(function () {
                            PECO.phpError();
                            this_.attr('disabled', false);
                        });
                    }
                });
            if(PECO.sysCheckMode()==true) {
                console.log('Processing Account Receivable...');
            }
        });
    };

    var init_dt_services = function(dataid) {


    };

    var init_edit_owner = function (dataid) {
        tbl_sub_owners(dataid);

        var curr_appname = $('#curr_appname',document);
        var curr_status = $('#curr_status',document);
        var curr_address = $('#curr_address',document);
        var curr_contact = $('#curr_contact',document);
        var sub_owners_list = $('#sub_owners_list',document);
        var remove_owner = $('#remove_owner',document);

        $('#frm_ownership_edit',document).on('submit',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url: this_.attr('action'),
                type: 'post',
                data: this_.serialize(),
                dataType: 'json',
            }).done(function (d) {
                if (d.type == 1) {
                    curr_appname.html(d.owner.appname);
                    curr_status.html(d.owner.status);
                    curr_address.html(d.owner.address);
                    curr_contact.html(d.owner.contact);
                }

                if (d.type == 2) {
                    tbl_sub_owners(d.dataid);
                }
            }).fail(function () {
                PECO.phpError();
            });
        });

        sub_owners_list.on('click','#remove_owner', function () {
            var this_ = $(this);
            var id = this_.attr('data-id');
            var this_tr = this_.closest('tr');

            $.ajax({
                url: base_url +'cad/removesubowner',
                type: 'post',
                data: {'id': id},
                dataType: 'json',
            }).done(function () {
                this_tr.remove();
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('click','#btn_new_owner',function () {
            var values = [];
            $('#frm_ownership_edit',document).find('.form-control').each(function () {
                $(this).val('');
                $(this).trigger('change');
                console.log($(this).attr('name') + ':' + $(this).val());
            });
            //console.log(values);
            $('#newowner',document).val(1);
        });

        PECO.handleriCheckForm($('#frm_ownership_edit',document));
    };

    var tbl_sub_owners = function (dataid) {
        var sub_owners_list = $('#sub_owners_list',document);
        PECO.DTDefault(sub_owners_list,'No Sub-Owners yet.');

        $.ajax({
            url:base_url + 'cad/dtsubowners',
            type:'post',
            data: {
                'appid' : dataid,
            },
            dataType:'json',
            beforeSend: function () {
                PECO.DTphpLoading(sub_owners_list, 'Fetching Sub-Owners');
            }
        }).done(function (d) {
            sub_owners_list.dataTable().empty();
            sub_owners_list.dataTable({
                bDestroy: true,
                bPaginate: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                bFilter: false,
                aaData: d.list,
                aoColumns: [
                    {"data":"num"},
                    {"data":"name" , sClass:'text-info',sWidth:'35%'},
                    {"data":"address", sWidth:'40%'},
                    {"data":"contact", sWidth:'25%'},
                    {"data":"remove"},
                ],
                searchHighlight: false
            });
        })
    };

    var frm_jo_newconn = function () {

        //PECO.select2Basic('#rate_class_select','cad/select2rateclass','Select Rate Class...',false,false,false);

        $(document).on('submit','#frm_jo_newconn',function (e) {
            e.preventDefault();
            var this_ = $(this);
            swal({
                title: "Are you sure?",
                text: "Proceed in creating Job Order.",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-info",
                confirmButtonText: "Yes, Create Job Order!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: this_.attr('action'),
                        type: this_.attr('method'),
                        data: this_.serialize(),
                        dataType: 'json',
                    }).done(function (data) {
                        swal(data.title,data.msg,data.func);
                    }).fail(function () {
                        PECO.phpError();
                    });
                }else{
                    swal.close();
                }
            });
        });

        $(document).on('click','#btn_job_order',function () {
            swal({
                title: "Already in process!",
                text: "There is already an existing Job Order for this application.",
                type: "info",
                button: "Okay!"
            });
        });

    };

    // ##############################################################
    // END CAD EVALUATION MODULE ####################################

    var handler_editable = function() {
        // $.fn.editable.defaults.mode = 'inline';

        PECO.getEditablePlugins();

        setTimeout(function(e) {


            $('#essrnoprofile', document).editable({
                url: PECO.base_url() + 'cad/submiteditable',
                type: 'text',
                name: 'essrno',
                title: 'Edit ESSR No.',
                placement: 'bottom'
            }).click(function() {
                $(this).next().find(".editable-input input").addClass('form-control');
            });

            $('#input_mobile', document).editable({
                url: PECO.base_url() + 'cad/submiteditable',
                type: 'text',
                name: 'mobile',
                title: 'Add Mobile Number',
                placement: 'right',
            }).click(function() {
                $(this).next().find(".editable-input input").addClass('form-control');
            });

            $('#input_phone', document).editable({
                url: PECO.base_url() + 'cad/submiteditable',
                type: 'text',
                name: 'phone',
                title: 'Add Phone Number',
                placement: 'right'
            }).click(function() {
                $(this).next().find(".editable-input input").addClass('form-control');
            });

            $('#input_email', document).editable({
                url: PECO.base_url() + 'cad/submiteditable',
                type: 'email',
                name: 'email',
                title: 'Add Email',
                placement: 'right'
            }).click(function() {
                $(this).next().find(".editable-input input").addClass('form-control');
            });

            $('#input_servno', document).editable({
                url: PECO.base_url() + 'cad/submiteditable',
                type: 'servno',
                name: 'servno',
                title: 'Edit Service Number',
                placement: 'right'
            }).click(function() {
                $(this).next().find(".editable-input input").addClass('form-control');
            });

            $('#input_address', document).editable({
                url: PECO.base_url() + 'cad/submiteditable',
                type: 'text',
                name: 'addressspec',
                title: 'Edit ESSR No.',
                placement: 'bottom'
            }).click(function() {
                $(this).next().find(".editable-input input").addClass('form-control');
            });

            //new editable for district
            $('#input_district', document).editable({
                success: function (response, newValue) {
                    if (!response.success)
                        return response.msg;
                },
                error: function (response, newValue) {
                    if (response.status === 500) {
                        return 'Service unavailable. Please try later.';
                    } else {
                        return response.responseText;
                    }
                },
                select2: {
                    //tags: [],
                    allowClear: true,
                    width: 'resolve',
                    placeholder: 'Select...',
                    id: function (item) {
                        return item.id;
                    },
                    ajax: {
                        url: PECO.base_url() + 'hris/select2district',
                        type: 'post',
                        dataType: 'json',
                        data: function (term) {
                            return {
                                term: term,
                            };
                        },
                        results: function (data) {
                            return {
                                results: $.map(data.list, function (item) {
                                    return {
                                        text: item.text,
                                        id: item.id,
                                    };
                                })
                            };
                        }
                    },
                    initSelection: function (element, callback) {
                        var init_val = element.val();
                        return callback(init_val);
                    },
                    escapeMarkup: function (markup) {
                        return markup;
                    }, // let our custom formatter work
                    formatResult: PECO.formatStateEditable, // omitted for brevity, see the source of this page
                    formatSelection: PECO.formatDataSelectionEditable, // omitted for brevity, see the source of this page
                },
                url: PECO.base_url() + 'cad/submiteditable',
                name: 'district',
                title: 'Modify District',
                placeholder: 'Modify District',
                inputclass: 'form-control input-large',
                emptytext: 'Enter District',
                placement: 'bottom',
            }).on('click', function () {
                PECO.select2_scroller();
            }).on('shown', function(e, editable) {

                var popover = editable.input.$input[0].closest('.popover');
                var popover_id = popover.id;

                $(document).on('change', editable, function() {

                    var new_value = editable.input.$input[0].value;

                    if (new_value != $(this).val()) {
                        $('#' + popover_id).find('.help-block').html('<div class="alert alert-warning margin-top-20"><i class="fa fa-warning"></i> Are you sure you want to change the district?').show();
                    } else {
                        $('#' + popover_id).find('.help-block').html('').hide();
                    }

                });
            }).on('save', function(e, params) {
                setTimeout(function() {

                    $(this).text(params.newValue);
                }, 300);
            });

            //new editable for marital
            $('#input_marital', document).editable({
                success: function (response, newValue) {
                    if (!response.success)
                        return response.msg;
                },
                error: function (response, newValue) {
                    if (response.status === 500) {
                        return 'Service unavailable. Please try later.';
                    } else {
                        return response.responseText;
                    }
                },
                select2: {
                    //tags: [],
                    allowClear: true,
                    width: 'resolve',
                    placeholder: 'Select...',
                    id: function (item) {
                        return item.id;
                    },
                    ajax: {
                        url: PECO.base_url() + 'query/select2civilstatus',
                        type: 'post',
                        dataType: 'json',
                        data: function (term) {
                            return {
                                term: term,
                            };
                        },
                        results: function (data) {
                            return {
                                results: $.map(data.list, function (item) {
                                    return {
                                        text: item.text,
                                        id: item.id,
                                    };
                                })
                            };
                        }
                    },
                    initSelection: function (element, callback) {
                        var init_val = element.val();
                        return callback(init_val);
                    },
                    escapeMarkup: function (markup) {
                        return markup;
                    }, // let our custom formatter work
                    formatResult: PECO.formatStateEditable, // omitted for brevity, see the source of this page
                    formatSelection: PECO.formatDataSelectionEditable, // omitted for brevity, see the source of this page
                },
                url: PECO.base_url() + 'cad/submiteditable',
                name: 'marital',
                title: 'Update Marital Status',
                placeholder: 'Update Marital Status',
                inputclass: 'form-control input-large',
                emptytext: 'Select Marital Status',
                placement: 'bottom',
            }).on('click', function () {
                PECO.select2_scroller();
            }).on('shown', function(e, editable) {

                var popover = editable.input.$input[0].closest('.popover');
                var popover_id = popover.id;

                $(document).on('change', editable, function() {

                    var new_value = editable.input.$input[0].value;

                    if (new_value != $(this).val()) {
                        $('#' + popover_id).find('.help-block').html('<div class="alert alert-warning margin-top-20"><i class="fa fa-warning"></i> Are you sure you want to change current marital status?').show();
                    } else {
                        $('#' + popover_id).find('.help-block').html('').hide();
                    }

                });
            }).on('save', function(e, params) {
                setTimeout(function() {

                    $(this).text(params.newValue);
                }, 300);
            });


            $('#gender',document).editable({
                source: [
                    {value: 1, text: 'Male', name: 'gender'},
                    {value: 2, text: 'Female', name: 'gender'},
                ],
            });

            $('.editable', document).editable('toggleDisabled');
            $('#enable_edit').click(function() {
                swal({
                    title: "Toggle Editable?",
                    text: "please confirm toggle of editable fields",
                    type: "info",
                    showCancelButton: true,
                    confirmButtonClass: "btn-success",
                    confirmButtonText: "Toggle",
                    closeOnConfirm: false,
                    closeOnCancel: false,
                    showLoaderOnConfirm: true
                }, function (isConfirm) {
                    if (isConfirm) {
                        $('.editable', document).editable('toggleDisabled');
                        swal.close();
                    }else{
                        swal.close();
                    }
                });
            });


        }, 300);
    };

    var handler_filetags = function(dataid) {
        $.ajax({
            url: PECO.base_url() + 'admin/fetchfiletype',
            data: {dataid: dataid},
            dataType: 'json',
            type: 'post',
            beforeSend: function() {
                $('#file_tags', document).html('<h4><i class="fa fa-refresh fa-spin text-info"></i> Loading files...</h4>');
            }

        }).done(function(d) {
            $('#file_tags', document).html(d.html);
        });
    };

    var hanlder_filetags_delete = function(dataid) {
        $.ajax({
            url: PECO.base_url() + 'admin/deleteallfiles',
            data: {dataid: dataid},
            dataType: 'json',
            type: 'post',
        }).done(function(d) {
            handler_filetags(dataid);
        });
    };

    var fn_application_profile = function(dataid, flowid) {
        //alert(dataid);
        init_application_requirements(dataid);
        profile_events(dataid);
        //handler_editable();
        handler_filetags(dataid);

        $('#refresh_file_tags',document).on('click',function () {
            handler_filetags(dataid);
        });

        $('#clear_file_tags',document).on('click',function () {
            hanlder_filetags_delete(dataid);
        });

        $('#btn_reload_req',document).on('click',function () {
            init_application_requirements(dataid);
        });

        $(document).on('click', '#btn_email_requirements', function(e) {
            e.preventDefault();
            var dataid = $(this).attr('data-id');
            swal({
                title: "Send list of requirements?",
                text: "please confirm sending of list of requirements",
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Send",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: PECO.base_url() + 'cad/sendfinalrequirementlist',
                        type: 'post',
                        dataType: 'json',
                        data: {'dataid': dataid}
                    }).done(function (d) {
                        swal(d.msg, 'Email sending', d.func);
                    });
                } else {
                    swal.close();
                }
            });
        });
    };

    var get_application_charges = function (dataid,moduleid) {
        //cad/getcustomerservices
        // alert(dataid);
        init_application_charges_list(dataid,moduleid);

        $('#btn_reload_charges',document).on('click',function () {
            init_application_charges_list(dataid,moduleid);
        });
    };

    var init_application_charges_list = function (dataid,moduleid) {
        var charges_list = $('#charges_list',document);
        var total_charges = $('#total_charges',document);
        var tbl_charges_list = $('#tbl_charges_list',document);
        $.ajax({
            url: base_url + 'cad/getcustomerservices',
            type: 'post',
            data: {
                appid : dataid,
                moduleid: moduleid
            },
            dataType: 'json',
            beforeSend: function () {
                charges_list.html('<li><h4 align="center"><i class="fa fa-refresh fa-spin fa-pulse text-info"></i> Fetching Charges...</h4></li>');
                total_charges.text('0.00');
                PECO.DTphpLoading(tbl_charges_list,'Fetching charges...');
            }
        }).done(function (d) {
            charges_list.html(d.charges);
            total_charges.text(d.total);
            tbl_charges_list.DataTable({
                bDestroy: true,
                bPaginate: false,
                bInfo: false,
                bStateSave: true,
                bProcessing: true,
                bLengthChange: false,
                bFilter: false,
                aaData: d.chargelist,
                aoColumns: [
                    {"data":"desc", sWidth:'', sClass: 'text-primary'},
                    {"data":"amt", sClass: 'text-align-right'},
                    {"data":"status", sClass: 'controls'},
                ],
                searchHighlight: false
            });
        }).fail(function () {
            charges_list.html('<li><h4><i class="fa fa-exclamation-triangle text-warning"></i> Error fetching charges!</h4></li>');
        });
    };

    var init_application_requirements = function(dataid,list = true) {
        //alert(dataid);

        dt_requirements(dataid,list);

        $('#btn_reload_req',document).on('click',function () {
            dt_requirements(dataid,list);
        });

        var delete_requirement = $('#delete_requirement',document);
        var tbl_requirements_list = $('#tbl_requirements_list', document);
        tbl_requirements_list.on('click', '#delete_requirement',function () {
            //alert(dataid);
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var reqid = this_.attr('data-id');
            var moduleid = this_.attr('data-module');

            $.ajax({
                url: base_url + 'cad/deleterequirement',
                type: 'post',
                data: {
                    appid: dataid,
                    moduleid: moduleid,
                    reqid: reqid
                },
                dataType: 'json'
            }).done(function (d) {
                if (d.qry == true) {
                    this_tr.html('<td colspan="4">Requirement Removed</td>');
                    setTimeout(
                        function () {
                            this_tr.remove();
                        },1000);
                } else {
                    PECO.initAlerts('Failed to remove requirement.','Fail','error');
                }
                setTimeout(
                    function () {
                        if ($('#tbl_requirements_list tbody tr').length === 0) {
                            init_application_requirements(dataid);
                        }
                    },1000);
            }).fail(function () {
                PECO.initAlerts('Failed to remove requirement.','Fail','error');
            });
        });
    };

    /**/

    var dt_requirements = function (dataid,list) {
        var tbl_requirements_list = $('#tbl_requirements_list', document);
        $.ajax({
            url: PECO.base_url() + 'cad/getcustomerrequirements',
            type: 'post',
            data: {dataid: dataid, list:list},
            dataType: 'json',
            beforeSend: function() {
                PECO.DTphpLoading(tbl_requirements_list, 'Loading requirements...');
            }
        }).done(function(d) {
            if (list) {

            }
            tbl_requirements_list.DataTable({
                bDestroy: true,
                bPaginate: true,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                bLengthChange: false,
                bFilter: false,
                aaData: d.list,
                aoColumns: d.columns,
                searchHighlight: false,
                language: {
                    "emptyTable": '<i class="fa fa-warning text-warning"></i> Requirements uploaded.'
                },
            });
        }).fail(function() {
            PECO.DTphpError(tbl_requirements_list);
        });
    };

    var profile_events = function (dataid) {
        //alert(dataid);

    };

    var add_requirements = function (dataid) {
        dt_requirement_list(dataid);
    };

    var dt_requirement_list = function (dataid) {
        var tbl_add_requirement_list = $('#tbl_add_requirement_list',document);
        $.ajax({
            url: base_url + 'cad/addrequirementlist',
            type: 'post',
            dataType: 'json',
            data: {
                dataid: dataid,
            },
            beforeSend: function() {
                PECO.DTphpLoading(tbl_add_requirement_list, 'Loading requirements...');
            }
        }).done(function (d) {
            tbl_add_requirement_list.DataTable({
                bDestroy: true,
                bPaginate: false,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                "bLengthChange": false,
                bFilter: true,
                aaData: d.list,
                aoColumns: [
                    {"data":"num", sClass: 'number'},
                    {"data":"code", sClass: 'text-primary text-align-center'},
                    {"data":"names", sWidth: '95%'},
                    {"data":"select", sClass: 'controls'},
                ],
                searchHighlight:false,
                ordering:false,
                fnRowCallback(nRow, aData, i) {
                    $('.icheck', nRow);
                    PECO.iCheckRow($('.icheck', nRow), 'minimal', 'blue');
                }
            });
        }).fail(function() {
            PECO.DTphpError(tbl_add_requirement_list);
        });

        $('#tbl_add_requirement_list',document).on('click','#add_req_row',function () {
            var this_ = $(this);
            var reqid = this_.attr('data-id');
            var this_tr = this_.closest('tr');

            $.ajax({
                url: base_url + 'cad/addrequirement',
                type: 'post',
                dataType: 'json',
                data: {
                    reqid: reqid,
                    appid: dataid
                },
            }).done(function (d) {
                PECO.initAlerts(d.msg,d.title,d.func);
                this_tr.remove();
            }).fail(function () {
                PECO.initAlerts('Failed to add requirements.','Fail','error');
            });
        });

        $('#frm_add_requirements',document).on('submit',function (e) {
            e.preventDefault();
            var this_ = $(this);
            $.ajax({
                url: this_.attr('action'),
                type: this_.attr('method'),
                dataType: 'json',
                data: this_.serialize(),
            }).done(function (d) {
                PECO.initAlerts(d.msg,d.title,d.func);
                $('#tbl_add_requirement_list',document).find('.icheck:checkbox:checked').each(function () {
                    var this_tr = $(this).closest('tr');
                    /*setTimeout(
                        function () {
                            this_tr.remove();
                        },1000);*/
                    this_tr.fadeOut(function () {
                        this_tr.remove();
                    })
                });
                init_application_requirements(dataid,false);
            }).fail(function () {
                PECO.initAlerts('Failed to add requirements.','Fail','error');
            });
        })
    };


    var sample_gdr = function (rate_class,dataid) {
        $.ajax({
            type: "POST",
            url: PECO.base_url() + "inspection/initaccountgdr",
            dataType: "json",
            data: {
                'rate': rate_class,
                'appid': dataid
            }
        }).done(function (result) {
            if (result.qry === true) {
                $('#rates').html(result.rates);
                $('#demand').html(result.demand);
                $('#dailyop').html(result.dailyops);
                $('#monthlyop').html(result.monthlyops);
                $('#totalwatt').html(result.total_load_text);
                $('#totalcost').html(result.deposit_cost_text);
                $('#deposit_cost').val(result.total_init_charges);
                $('#totalinitc').html(result.total_init_charges_text);
                if (result.isecales == true) {
                    $('#totalecales').html(result.total_ecales_amt_text);
                }
            }
        });

        $(document).on('click','#btn_generate_gdr',function () {
            var acctamt = $('#deposit_cost').val();
            swal({
                title: "Are you sure?",
                text: "Continue processing for payment?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-info",
                confirmButtonText: "Yes, Process for Payment!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: base_url + 'cad/addcustomercharges',
                        type: 'post',
                        data: {
                            acctcode : 162,
                            acctamt : acctamt,
                            dataid : dataid,
                            origin : 35
                        },
                        dataType: 'json',
                    }).done(function (data) {
                        swal("PECO" , data.msg , data.func);
                    }).fail(function () {
                        PECO.phpError();
                    });
                }else{
                    swal.close();
                }
            });
        });

    };

    var charges_override = function (dataid,moduleid) {
        $('#frm_override_amt',document).on('submit',function (e) {
            e.preventDefault();
            var this_ = $(this);
            swal({
                title: 'Override Amount?',
                text: "Are you sure you want to override this amount?",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: this_.attr('action'),
                        type: this_.attr('method'),
                        dataType: 'json',
                        data: this_.serialize()
                    }).done(function (d) {
                        $('#modal_ajax',document).modal('hide');
                        swal('Override Amount Charges', d.msg, d.func);
                        setTimeout(function () {
                            init_application_charges_list(dataid,moduleid);
                        },1000);
                    }).fail(function () {
                        swal('Override Amount Charges', 'PHP Error!', 'error');
                    })
                }
            });
        });
    };

    var capitalEachWord = function (value) {
        var splitStr = value.toLowerCase().split(' ');
        for (var i = 0; i < splitStr.length; i++) {
            // You do not need to check if i is larger than splitStr length, as your for does that for you
            // Assign it back to the array
            if (splitStr[i] == splitStr[i].toUpperCase()) {
                splitStr[i] = splitStr[i].toUpperCase();
            } else {
                splitStr[i] = splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1);
            }
        }
        // Directly return the joined string
        return splitStr.join(' ');
    };

    var edit_representative = function (dataid) {

    };

    var load_on_tab = function (dataid) {
        var recom_setup = $('#recom_setup', document);
        var tab = $('a[data-toggle="tab"]', recom_setup);
        var id = 1;

        tab.on('shown.bs.tab', function (e) {
            var target = $(e.target).attr('href');
            id = $(e.target).attr('data-id');
            var msg = '';
            var load = '';

            if (id == 1) {
                /** LOAD APPLICATION DATA */
            }
            if (id == 2) {
                /** CLEAR ALL INPUT VALUES */
            }
            if (id == 3) {
                /** LOAD DATATABLE FOR AR HISTORY */
            }
        });
    };

    var init_owner_update = function (dataid) {
        var frm_ownership_edit = $('#frm_ownership_edit',document);
        PECO.handleriCheckForm(frm_ownership_edit);
        handling_owner_update(dataid);
        $.ajax({
            url : PECO.base_url() + 'cad/initownerinfo',
            type : 'post',
            dataType : 'json',
            data : {
                id : dataid
            }
        }).done(function (d) {
            frm_ownership_edit.find('input.form-control').each(function () {
                var this_ = $(this);
                var name = this_.attr('name');
                if (d.hasOwnProperty(name)) {
                    var value = d[name];
                    var val = 0;
                    if(typeof value === 'number'){
                        val = parseFloat(value);
                    } else{
                        val = value
                    }
                    this_.val(val).trigger('change');
                }
            });
            frm_ownership_edit.find('textarea.form-control').each(function () {
                var this_ = $(this);
                var name = this_.attr('name');
                if (d.hasOwnProperty(name)) {
                    this_.text(d[name]);
                }
            });
            frm_ownership_edit.find('input.icheck').each(function () {
                var this_ = $(this);
                var name = this_.attr('name');
                if (d.hasOwnProperty(name) && this_.val() == d[name]) {
                    //alert(this_.val());
                    this_.iCheck('check');
                }
            });
        }).fail(function () {

        })
    };

    var handling_owner_update = function (dataid) {
        var frm_ownership_edit = $('#frm_ownership_edit',document);
        $('#btn_new_owner',document).on('click',function () {
            frm_ownership_edit.find('input').each(function () {
                $(this).trigger('change');
            });
            frm_ownership_edit.find('input.icheck').each(function () {
                $(this).iCheck('uncheck');
            });
            $('#newowner',document).val(1);
        });
    };

    var viewer_handler = function (dataid) {
        PECO.select2Basic($('#select2_routes', document), 'query/gettrnflowstages', 'Forward or return to...', false, false, $('#select2_routes', document).val(), false, false, {flowid: 2});

        $(document).on('submit', '#frm_update_trn', function (e) {
            e.preventDefault();
            var this_ = $(this);

            swal({
                title: "Move transaction",
                text: "Confirm sending transaction to selected route.",
                type: "warning",
                showCancelButton: true,
                cancelButtonText: "No!",
                cancelButtonClass: "btn-danger btn-rounded",
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Yes, send!",
                closeOnConfirm: false,
                closeOnCancel: false,
                showLoaderOnConfirm: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: this_.attr('action'),
                        type: this_.attr('method'),
                        dataType: 'json',
                        data: this_.serialize()
                    }).done(function (d) {
                        swal({
                            title: "Transaction Update!",
                            text: d.msg,
                            type: d.func,
                            html: true
                        }, function () {
                            if (d.qry) {
                                window.location.reload();
                            }
                        });
                    }).fail(function () {
                        PECO.phpError();
                    });
                } else {
                    swal("Cancelled", "Transaction was not updated.", "error");
                }
            });
        });
    }

    var archiving_handler = function (dataid) {
        var system_size = $('#systemsize',document);
        var select2_paneltype = $('#select2_panel_type',document);
        var paneltype = $('#paneltype',document).val();
        var systype = $('.icheck:checked',$('#icheck_system_type',document)).attr('data-target');
        var num_panel = $('#nop',document);
        var nop = $('#nop',document).val();
        archiving_forms_check();


        if (select2_paneltype.length) {
            PECO.select2Basic(select2_paneltype,'inspection/select2paneltype', 'Panel type..');
            paneltype = select2_paneltype.val();
        }

        system_size.on('change',function () {
            var this_ = $(this);
            if (systype === 'standard') {
                //Query for paneltype and max number of panels
                $.ajax({
                    url : PECO.base_url() + 'inspection/select2systemsize',
                    type : 'post',
                    dataType : 'json',
                    data : {
                        sizeid : this_.val(),
                        archiving : true
                    }
                }).done(function (d) {
                    console.log(d);
                    select2_paneltype.val(d.paneltype).trigger('change');
                    num_panel.val(d.nop);
                });
            }
        });

        /*select2_paneltype.on('change',function () {
            var this_ = $(this);
            paneltype = this_.val();
            //console.log({paneltype : paneltype, systype:systype});
            if (systype === 'standard' && nop > 0) {
                //system_size.select2('destroy');
                PECO.select2Basic(system_size,'inspection/select2systemsize','Select System Size...',false,false,false,false,false,{paneltype : paneltype, nop : nop});
                system_size.attr('name','systemsizeid');
            } else {
                system_size.attr('name','systemsize');
            }
        });*/

        /*num_panel.on('keyup',function () {
            var this_ = $(this);
            if (nop !== this_.val()) {
                nop = this_.val();
                if (paneltype > 0 && systype === 'standard') {
                    PECO.select2Basic(system_size,'inspection/select2systemsize','Select System Size...',false,false,false,false,false,{paneltype : paneltype, nop : nop});
                }
            }
        });*/

        $('.icheck-inline .icheck', $('#icheck_system_type',document)).each(function(){
            if ($(this).is(':checked')) {
                systype = $(this).attr('data-target');

                //console.log({paneltype : paneltype, systype:systype});

                /*if (systype === 'standard' && paneltype > 0 && nop > 0) {
                    console.log('dapat may select2!');
                    PECO.select2Basic(system_size,'inspection/select2systemsize','Select System Size...',false,false,false,false,false,{paneltype : paneltype, nop : nop});
                }*/

                if (systype === 'standard') {
                    //console.log('dapat may select2!');
                    PECO.select2Basic(system_size,'inspection/select2systemsize','Select System Size...',false,false,false,false,false,{archiving:true});
                    system_size.attr('name','systemsizeid');
                } else {
                    system_size.attr('name','systemsize');
                }
            }

            $(this).on('ifChecked', function(){
                var this_ = $(this);
                this_.attr('checked', true);
                var target = this_.attr('data-target');
                //console.log(target);
                systype = target;
                if (target === 'standard') {
                    //console.log('Paneltype: '+paneltype);
                    PECO.select2Basic(system_size,'inspection/select2systemsize','Select System Size...',false,false,false,false,false,{archiving:true});
                }
                if (target === 'nonstandard') {
                    system_size.select2('destroy');
                }
            }).on('ifUnchecked', function(){
                var this_ = $(this);
                this_.attr('checked', false);
            });
        });

        $('.icheck-inline .icheck', $('#icheck_wifi_access',document)).each(function(){
            $(this).on('ifChecked', function(){
                var this_ = $(this);
                this_.attr('checked', true);
            }).on('ifUnchecked', function(){
                var this_ = $(this);
                this_.attr('checked', false);
            });
        });


        $(document).on('click','#btn_process_customer',function () {
            var inputs = $('#archiving',document).find('form[id!=frm_upload_pic] input[type!=hidden]');

            if (inputs.length > 0) {
                swal("Ooops!", "It seems like some of the customer's details are still lacking. Please complete and try again!", "warning");
            } else {
                swal({
                    title: "Process Customer",
                    text: "All customer details are complete. Do you want to process customer and finalize the application process?",
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
                            url: PECO.base_url() + 'cad/processcustomerapplication',
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
                //swal("Process Customer", "All customer details are complete. Do you want to process customer and finalize the application process?", "warning");
            }
        });

        $(document).on('input','#essr_no',function (e) {
            var this_ = $(this);
            var val = this_.val();
            var maxLength = this_.attr('maxlength');
            if ($.isNumeric(val) && Math.floor(val) === val) {
                maxLength = maxLength - 1;
            }

            const newValue = ("0".repeat(maxLength) + val).slice(-maxLength);

            this_.val(newValue);
        });

        var installmentplan = $(document).find('#select2_installmentplan');

        if (installmentplan.length) {
            PECO.select2Basic(installmentplan, 'cad/select2planduration', 'Select plan...', false, false, $('#select2_planduration', document).val());

            if (installmentplan.val() > 0) {
                $('#select2_billing_start', document).attr('disabled', false);
                $('#select2_billing_year', document).attr('disabled', false);
                $('#select2_bill_frequency', document).attr('disabled', false);
            } else {
                $('#select2_billing_start', document).attr('disabled', true);
                $('#select2_billing_year', document).attr('disabled', true);
                $('#select2_bill_frequency', document).attr('disabled', true);
            }


            $(document).on('change', '#select2_installmentplan', function () {
                var this_ = $(this);
                var this_val = this_.val();
                console.log('Installment Plan: ' + this_val);

                if (this_val !== '') {
                    $.ajax({
                        url: PECO.base_url() + 'cad/getselectedplanamount',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            appid: dataid,
                            duration: this_val
                        }
                    }).done(function (d) {
                        if (d.value && d.value > 0) {
                            $('#planamount', document).val(d.value);
                        }
                    }).fail(function () {

                    });

                    if (this_val > 0) {
                        $('#select2_billing_start', document).attr('disabled', false);
                        $('#select2_billing_year', document).attr('disabled', false);
                        $('#select2_bill_frequency', document).attr('disabled', false);
                    } else {
                        $('#select2_billing_start', document).attr('disabled', true);
                        $('#select2_billing_year', document).attr('disabled', true);
                        $('#select2_bill_frequency', document).attr('disabled', true);
                    }
                } else {
                    $('#planamount', document).val('');
                    if (this_val === 0) {
                        $('#select2_billing_start', document).attr('disabled', true);
                        $('#select2_billing_year', document).attr('disabled', true);
                        $('#select2_bill_frequency', document).attr('disabled', true);
                    }
                }
            });
        }

        PECO.select2Basic($('#select2_billing_start',document),'systems/select2month','Select start of billing series...',false,false,false);
        PECO.select2Basic($('#select2_bill_frequency',document),'billing/select2billingdate','Billing date...',false,false,$('#select_billdate',document).val());

        //ON SAVE, REMOVE INPUTS, ADD DETAIL, REMOVE SAVE.
        $('#archiving',document).find('form[id!=frm_upload_pic]').each(function () {
            var form = $(this);
            PECO.processSwalForm({
                form: form,
                title: 'Save?',
                text: 'Save provided information?',
                buttons: {
                    nbText : 'Cancel',
                    ybText : 'Yes, save!',
                    ybClass : 'btn-success'
                },
                callback: function(d) {
                    console.log(d);
                    console.log(d.values.length);
                    if (d.qry && Object.keys(d.values).length > 0) {
                        var values = d.values;
                        var count = Object.keys(values).length;
                        $.each(d.values,function (key,value) {
                            if (value !== '' || value !== false) {
                                form.find('#detail_' + key).html(value);
                            }
                            if (!--count) {
                                archiving_forms_check();
                            }
                        });
                    }
                }
            });
        });

        //=====DOCUMENTS PROCESSING=====//
        //dt_appdocs_list(dataid);
        appdocs_handler(dataid);

    };

    var archiving_forms_check = function () {
        var basic = $('#form_basic_information',document);
        var account = $('#form_account_information',document);
        var docs = $('#form_docs_checklist',document);

        $('.portlet-footer',basic).hide().find('button').attr('disabled',true);
        $('.portlet-footer',account).hide().find('button').attr('disabled',true);
        $('.portlet-footer',docs).hide().find('button').attr('disabled',true);

        basic.find('input').each(function () {
            var this_ = $(this);
            this_.attr('disabled', true);
        });

        account.find('input').each(function () {
            var this_ = $(this);
            this_.attr('disabled', true);
        });

        docs.find('input').each(function () {
            var this_ = $(this);
            this_.attr('disabled', true);
        });

        var basic_inputs = basic.find('input[type!=hidden]');
        if (basic_inputs.length) {
            basic.find('input,button').each(function () {
                var this_ = $(this);
                this_.attr('disabled', false);
            });
            $('.portlet-footer',basic).show('slow').find('button').attr('disabled',false);
        }

        var account_inputs = account.find('input[type!=hidden]');
        if (account_inputs.length && !basic_inputs.length) {
            account.find('input').each(function () {
                var this_ = $(this);
                this_.attr('disabled',false);
            });
            $('.portlet-footer',account).show('slow').find('button').attr('disabled',false);
        }

        var docs_inputs = docs.find('input[type!=hidden]');
        if (docs_inputs.length && !account_inputs.length && !basic_inputs.length) {
            docs.find('input').each(function () {
                var this_ = $(this);
                this_.attr('disabled',false);
            });
        }
        $('.portlet-footer',docs).show('slow').find('button').attr('disabled',false);
    }

    var dt_appdocs_list = function (appid,elem_tbl) {
        var tbl_appdocs_list = elem_tbl ? elem_tbl : $('#tbl_appdoc_list',document);
        PECO.DTDefault(tbl_appdocs_list,'List is yet to be populated.');
        var table;
        var type = tbl_appdocs_list.attr('id').split('_')[1].substring(3);
        console.log(type);
        $.ajax({
            url: PECO.base_url() + 'cad/getapplicationdocumentslist',
            type: 'post',
            dataType: 'json',
            async: false,
            data: {
                appid : appid,
                type : type
            }
        }).done(function (d) {
            table = tbl_appdocs_list.DataTable({
                bDestroy: true,
                bPaginate: false,
                bInfo: true,
                bStateSave: true,
                bProcessing: true,
                "bLengthChange": false,
                bFilter: false,
                aaData: d.list,
                aoColumns: d.cols,
                searchHighlight:false,
                ordering:false,
                fnRowCallback(nRow, aData, i) {
                    $('.icheck', nRow);
                    PECO.iCheckRow($('.icheck', nRow), 'minimal', 'blue');
                },
            });
            if ((type === 'app' && Object.keys(d.list).length === 6) || (type === 'req' && Object.keys(d.list).length === 17)) {
                $('#btn_add_document',document).attr('disabled',true);
            } else {
                $('#btn_add_document',document).attr('disabled',false);
            }
            archiving_forms_check();
        }).fail(function () {
            PECO.initAlerts();
        });

        return table;
    };

    var appdocs_handler = function (appid) {
        var tbl_appdocs_list = $('#tbl_appdoc_list',document);
        var type = tbl_appdocs_list.attr('id').split('_')[1].substring(3);
        var dt_docs = dt_appdocs_list(appid);
        var tab = $('a[data-toggle="tab"]',$('#doc_list_tabs',document));
        tab.on('shown.bs.tab',function (e) {
            tbl_appdocs_list.find('*').off();
            var table = $(e.target).attr('data-table');
            type = table.split('_')[1].substring(3);
            tbl_appdocs_list = $('#' + table,document);
            dt_docs = dt_appdocs_list(appid,tbl_appdocs_list);
        });

        tbl_appdocs_list.on('ifChecked','[id^=input_source]', function(){
            var this_ = $(this);
            this_.attr('checked', true);
            var this_tr = this_.closest('tr');
            var this_td = this_.closest('td');
            var input_location = this_tr.find('#input_location');

            if (parseInt(this_.val()) === 2) {
                input_location.empty().append($('<input>').attr({type : 'text',name : 'location', class : 'form-control', style : 'width: 100% !important;'}));
                //CHANGE BUTTON TO VERIFY
            } else {
                input_location.empty().append($('<input>').attr({type : 'file', name : 'location', class : 'form-control', style : 'width: 100% !important;', multiple : true}));
            }
        }).on('ifUnchecked', function(){
            var this_ = $(this);
            this_.attr('checked', false);
        });

        //ON CLICK SAVE, IF PDF OR SINGLE FILE, UPLOAD FILE TO ACCOUNT FOLDER.
        //IF MULTIPLE IMAGE, COMPILE AS PDF AND SAVE TO ACCOUNT FOLDER
        $(document).on('click','#btn_docs_save',function () {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var this_file = $('#docs_location',this_tr);
            var doctype = $('#doctype',this_tr);

            var formData = new FormData();

            if (type === 'req') {
                formData.append('requirecode',$('#requirecode',this_tr).val());
            }

            if (type === 'doc') {
                formData.append('doctype', doctype.val());
            }
            formData.append('appid',appid);

            $.each(this_file,function (obj,v) {
                $.each($(v)[0].files,function (i,file) {
                    formData.append("location[]", file);
                    //console.log('File['+i+']: ' + file.name);
                })
            })

            $.ajax({
                url: PECO.base_url() + 'cad/savetempdocs',
                type: 'post',
                dataType: 'json',
                data: formData,
                cache: false,
                contentType: false,
                processData: false
            }).done(function (d) {
                console.log(d);
                if (d.qry) {
                    $('#input_location', this_tr).html(d.link);
                    //this_.attr('id','#btn_docs_delete').html('<i class="fa fa-times text-danger"></i>')
                    this_.closest('td').html(d.buttons);
                    PECO.initAlerts(d.msg,'Document upload!',d.func);
                    if (typeof d.document !== 'undefined') {
                        $('#doctype_name', this_tr).text(d.document);
                        var typeid = (type === 'doc') ? 'doctype' : 'requirecode';
                        $('#'+type , this_tr).select2('destroy').attr('type','hidden');
                    }
                }

                if (tbl_appdocs_list.find('input[name=doctype]').length < 5 || tbl_appdocs_list.find('input[name=requirecode]').length < 17) {
                    $('#btn_add_document',document).attr('disabled',false);
                }
            }).fail(function () {
                PECO.phpError();
            });
        });

        $(document).on('click','#btn_docs_delete',function () {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var inputid = type === 'doc' ? 'doctype' : 'requirecode';
            var doctype = $('#' + inputid,this_tr).val();

            $.ajax({
                url : PECO.base_url() + 'cad/deletetempdoc',
                type : 'post',
                dataType : 'json',
                data : {
                    appid : appid,
                    doctype : doctype
                }
            }).done(function (d) {
                if (d.qry) {
                    if (d.remove) {
                        dt_docs.row(this_tr).remove().draw();
                    } else {
                        $('#input_location', this_tr).html(d.file);
                        this_.closest('td').html(d.buttons);
                    }
                    PECO.initAlerts(d.msg, 'Delete Document', d.func);
                    $('#btn_add_document',document).attr('disabled',false);
                }
            }).fail(function () {

            });
        });

        $(document).on('click','#btn_add_document',function () {
            var this_ = $(this);
            var doctypes = [];
            var requirecodes = [];
            var params = {type:type};

            //var formData = new FormData();

            if (type === 'doc') {
                tbl_appdocs_list.find('input[name=doctype]').each(function () {
                    var value = $(this).val();
                    doctypes.push(value);
                });

                params.doctypes = doctypes;
            }

            if (type === 'req') {
                tbl_appdocs_list.find('input[name=requirecode]').each(function () {
                    var value = $(this).val();
                    requirecodes.push(value);
                });

                params.requirecodes = requirecodes;
            }

            $.ajax({
                url : PECO.base_url() + 'cad/addnewdocument',
                type : 'post',
                dataType : 'json',
                data : params,
            }).done(function (d) {
                if (Object.keys(d.row).length > 0) {
                    tbl_appdocs_list.find('tr td.dataTables_empty').remove();
                    dt_docs.row.add(d.row).draw();
                    if (typeof d.doctypes !== 'undefined' && Object.keys(d.doctypes).length > 1) {
                        var newRow = $('tbody tr:last', tbl_appdocs_list);

                        newRow.find('input[name=doctype]').each(function () {
                            var this_ = $(this);
                            PECO.select2BasicId(this_,'cad/select2doctypes',d.doctypes,'Select...',false,false,false);
                        })
                        newRow.find('input[name=requirecode]').each(function () {
                            var this_ = $(this);
                            PECO.select2BasicId(this_,'cad/select2requirecodes',d.doctypes,'Select...',false,false,false);
                        })
                        this_.attr('disabled',true);
                    }
                }
            }).fail(function () {

            });
        });

        $(document).on('click','#btn_docs_preview',function () {
            var this_ = $(this);
            var this_tr = this_.closest('tr');
            var inputid = type === 'doc' ? 'doctype' : 'requirecode';
            var doctype = $('#' + inputid,this_tr).val();
            var filename = $('#doctype_name',this_tr).text();

            var win = window.open('','');
            win.document.title = (filename) ? filename : 'Print Preview';

            const form = document.createElement('form');
            form.method = 'post';
            form.action = PECO.base_url() + 'printer/printarchivingdocs';

            const appID = document.createElement('input');
            appID.type = 'hidden';
            appID.name = 'appid';
            appID.value = appid;

            form.appendChild(appID);

            const titleField = document.createElement('input');
            titleField.type = 'hidden';
            titleField.name = 'title';
            titleField.value = filename;

            form.appendChild(titleField);
            const filenameField = document.createElement('input');
            filenameField.type = 'hidden';
            filenameField.name = 'filename';
            filenameField.value = filename;

            form.appendChild(filenameField);

            const htmlField = document.createElement('input');
            htmlField.type = 'hidden';
            htmlField.name = 'doctype';
            htmlField.value = doctype;

            form.appendChild(htmlField);

            const typeField = document.createElement('input');
            typeField.type = 'hidden';
            typeField.name = 'type';
            typeField.value = inputid;

            form.appendChild(typeField);

            win.document.body.appendChild(form);
            form.submit();
        });
    };

    var cancel_application_btn = function (dataid) {
        $(document).on('click','#btn_cancel_application',function () {
            var this_ = $(this);
            var remarks = '';
            var frm_newaccount = $('#frm_newaccount',document);
            swal({
                title: "Cancel Application?",
                text: "Cancel application process for this customer?",
                type: "input",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, cancel application!",
                cancelButtonText: "No!",
                closeOnConfirm: false,
                closeOnCancel: true,
                showLoaderOnConfirm: true,
                inputPlaceholder: "Add remarks if applicable. (optional)"
            }, function(inputValue) {
                if (inputValue || inputValue === "") {
                    if (inputValue !== "") {
                        //swal.showInputError("You need to write something!");
                        //return false
                        remarks = inputValue;

                    }

                    //swal('Done!','Function Executed!','success');
                    $.ajax({
                        url: PECO.base_url() + 'cad/cancelcustomerapplication',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            appid: dataid,
                            remarks: remarks
                        }
                    }).done(function (data) {
                        swal({
                            title: data.title,
                            text: data.msg,
                            type: data.func,
                        });
                        if (data.qry) {
                            //this_.addClass('hidden');
                            $('#page_document_window',document).find('input,button,textarea,[data-toggle="ajax-modal"]').each(function (i,obj) {
                                var this_ = $(this);
                                this_.attr('disabled',true);
                                if (this_.is('button') && !this_.hasClass('hidden')) {
                                    this_.addClass('hidden');
                                }

                                if (this_.attr('data-toggle') === 'ajax-modal') {
                                    this_.addClass('hidden');
                                }
                            });

                            $('#frm_newaccount',document).find('input,button,textarea,[data-toggle="ajax-modal"]').each(function (i,obj) {
                                var this_ = $(this);
                                this_.attr('disabled',true);
                                if (this_.is('button') && !this_.hasClass('hidden')) {
                                    this_.addClass('hidden');
                                }

                                if (this_.attr('data-toggle') === 'ajax-modal') {
                                    this_.addClass('hidden');
                                }
                            });
                        }
                    });
                } else {
                    swal.close();
                }
            });
        });
    }


    return {
        profile: function(dataid, flowid) {
            fn_application_profile(dataid, flowid);
        },
        application: function() {
            init_customers_applications();
            init_validation_wizard();
        },
        evaluation: function(dataid) {
            init_evaluation(dataid)
        },
        corporation: function() {
            init_corporation();
        },
        government: function() {
            init_government();
            init_customers_applications();
            init_validation_wizard_govt();
        },
        editOwner: function (dataid) {
            init_edit_owner(dataid);
        },
        joNewconn: function () {
            frm_jo_newconn();
            //sample_gdr(rate_class,dataid);
        },
        addRequirements: function (dataid) {
            add_requirements(dataid);
        },
        requirements: function (dataid,list) {
            init_application_requirements(dataid,list);
        },
        charges: function (dataid,moduleid) {
            get_application_charges(dataid,moduleid);
        },
        override: function (dataid,moduleid) {
            charges_override(dataid,moduleid);
        },
        ownership: function (dataid) {
            edit_representative(dataid);
        },
        updateOwner: function (dataid) {
            init_owner_update(dataid);
        },
        contract: function() {
            init_customers_applications();
        },
        viewer: function (dataid) {
            viewer_handler(dataid);
        },
        archiving: function (dataid) {
            archiving_handler(dataid);
        },
        cancelAppBtn: function (dataid) {
            cancel_application_btn(dataid);
        }
    }
}();
