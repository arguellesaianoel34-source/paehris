var PAYROLL = function() {

    var init_testing_fn = function() {
        $(document).on('click', '#btn_triggger', function() {
            var payclass = $('#input_payclass', document).select2('val');
            var year = $('#input_year', document).select2('val');
            var month = $('#input_month', document).select2('val');
            var paytype = $('#input_paytype', document).select2('val');
            init_form_loop(0, 0, payclass, month, year, paytype);
        });

        $('#input_paytype').select2();

        PECO.select2Basic($('#input_month', document), 'systems/select2month', 'Select month..', false, false, false, false, true);
        PECO.select2Basic($('#input_year', document), 'systems/select2year', 'Select year..', false, false, false);
        PECO.select2Basic($('#input_payclass', document), 'systems/select2payclass', 'Select payclass..', false, false, false);
    };

    var init_form_loop = function(num, sysid, payclass, month, year, paytype) {
        $.ajax({
            url: PECO.base_url() + 'payroll/sendpayslips',
            type: 'post',
            data: {'num': num, 'sysid': sysid, 'payclass': payclass, 'years': year, 'months': month, 'paytype': paytype},
            dataType: 'json',
        }).done(function(d){
            if(d.end==false) {

                setTimeout(init_form_loop(d.num, d.sysid, payclass, month, year, paytype),3000);

                $('#stat_bar', document).closest('.progress').addClass('progress-striped active');
                $('#stat_bar', document).css('width', d.per + '%');
                $('#input_start', document).val('Item queried: ' + d.empname);
                $('#input_per', document).val(d.per + '%');
                $('#input_num', document).html(d.num);
                if(d.per < 30) {
                    $('#stat_bar', document).removeClass('progress-bar-success progress-bar-info').addClass('progress-bar-danger');
                }else{
                    if(d.per < 70) {
                        $('#stat_bar', document).removeClass('progress-bar-success progress-bar-danger').addClass('progress-bar-info');
                    }else{
                        $('#stat_bar', document).removeClass('progress-bar-info progress-bar-danger').addClass('progress-bar-success');
                    }
                }
            }else{
                $('#stat_bar', document).css('width', d.per + '%');
                $('#stat_bar', document).closest('.progress').removeClass('progress-striped active');
                $('#input_start', document).val(d.empname);
                $('#input_per', document).val(d.per + '%');
            }
        }).fail(function() {
            $('#input_start', document).html('<i class="fa fa-times text-danger"></i> PHP Error');
        });
    };

    var init_admin_fn = function() {
        var reporttype = $('#reporttype', document);
        var year = $('#year', document);
        var month = $('#month', document);
        var costcenter = $('#costcenter', document);
        var payclass = $('#payclass', document);
        var tbl_payroll_reports = $('#tbl_payroll_reports', document);

        PECO.select2Basic(month, 'systems/select2month', 'Select month..', false, false, false, false, true);
        PECO.select2Basic(costcenter, 'query/select2department', 'Select costcenter..', false, false, false, false, true);
        PECO.select2Basic(reporttype, 'query/select2reporttype', 'Select costcenter..', false, false, false, false, true);

        payclass.select2({
            allowClear: true,
            placeholder: 'Payclass'
        });


        $(document).on('click', '#btn_download_excel', function(e) {
            e.preventDefault();

            var month = $('#month', document).val();
            var year = $('#year', document).val();
            var type = $('#reporttype', document).val();
            var payclass = $('#payclass', document).val();
            var costcenter = $('#costcenter', document).val();

            var month_ = (month > 0) ? month : 0;
            var year_ = (year > 0) ? year : 0;
            var type_ = (type > 0) ? type : 0;
            var payclass_ = (payclass > 0) ? payclass : 0;
            var costcenter_ = (costcenter > 0) ? costcenter : 0;


            window.open(PECO.base_url() + 'reports/getpayrollreports/' + type_ + '/' + year_ + '/' + month_ + '/' + costcenter_ + '/' + payclass_ + '/' + '/excel', '_blank');
        });

        $(document).on('submit','#frm_get_payroll_rep',function (e) {
            e.preventDefault();
            var type = $('#reporttype', document).val();
            var month = $('#month', document).val();
            var year = $('#year', document).val();
            var this_ = $(this);
            $.ajax({
                url: this_.attr('action'),
                type: this_.attr('method'),
                data: this_.serialize(),
                dataType: 'json',
                beforeSend: function() {

                    $('thead tr', tbl_payroll_reports).html('');
                    $('tbody', tbl_payroll_reports).html('<tr><td><h3 style="margin-top: 10px;"><i class="fa fa-spinner fa-spin fa-pulse"></i> Loading schedules....</h3></td>');

                    //tbl_payroll_reports.dataTable().empty();
                    //$('thead', tbl_payroll_reports).html('<h4><i class="fa fa-spinner fa-pulse fa-spin"></i> Loading...</h4>');
                }
            }).done(function (d) {
                console.log(d.columns);

                $('tbody', tbl_payroll_reports).html('');
                $('thead', tbl_payroll_reports).html('');
                $('thead', tbl_payroll_reports).html('<tr></tr>');
                if(d.columns.length > 0) {
                    for(th = 0; th<d.columns.length; th++) {
                        $('thead tr', tbl_payroll_reports).append('<th>' + d.columns[th].title + '</th>');
                    }
                }
                setTimeout(function() {
                    tbl_payroll_reports.dataTable().empty();
                    tbl_payroll_reports.dataTable({
                        bDestroy: true,
                        bPaginate: true,
                        bFilter: true,
                        bInfo: true,
                        bStateSave: true,
                        bProcessing: true,
                        aaData: d.list,
                        aoColumns: d.columns,
                        order: [[0,'desc']],
                    }, 2000);
                });


            }).fail(function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                $('tbody', tbl_payroll_reports).html('<tr><td colspan="100%"><div class="alert alert-danger">Error loading payroll data: ' + error + '</div></td></tr>');
            });
        })
    };

    return {
        init: function() {
            init_testing_fn();
        },
        admin: function() {
            init_admin_fn();
        }
    }
}();