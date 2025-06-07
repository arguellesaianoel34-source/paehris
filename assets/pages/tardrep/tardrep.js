var TARDREP = function(){


    var tardinesstable = $('#tardinesstable',document);

    var init_tardrep = function(flexi){
        init_tardreptable(false,false,flexi);
        events(flexi);
    };

    var events = function (flexi){
        $(document).on('click','#printmonthlytard',function () {
            var monthdata = (month) ? month : false;
            var year = (year) ? year : false;
            var d = new Date();
            var defaultyear = d.getFullYear();
            var monthdefault = d.getMonth() + 1;
            if(monthdata > 0){
                monthdefault = monthdata;
            }
            if(year > 0){
                defaultyear = year;
            }
            var monthpass = $(document).find('#month').val();
            var yearpass = $(document).find('#year').val();
            /*$.ajax({
                url:PECO.base_url()+'hris/getalltardinessrep',
                type:'post',
                data:{"monthdata":monthpass,"yeardata":yearpass},
                dataType:'json'
            }).done(function (d) {
                 PECO.pecoRepPrint("Monthly Tardiness Report as of "+monthdefault+"/"+defaultyear , d.html);
            }).fail(function () {
                PECO.phpError();
            });*/
            if (flexi) {
                dt_flexi_tardrep(monthpass,yearpass,false,'summary');
            } else {
                dt_tardrep(monthpass,yearpass,false,'summary');
            }
        });

        $(document).on('click','#searchtardibtn',function (e) {
            var month = $(document).find('#month').val();
            var year = $(document).find('#year').val();
            if (flexi) {
                dt_flexi_tardrep(month , year);
            } else {
                dt_tardrep(month , year);
            }
        });

        $(document).on('click','#printmonthlytarddetails',function () {
            var monthdata = (month) ? month : false;
            var year = (year) ? year : false;
            var d = new Date();
            var defaultyear = d.getFullYear();
            var monthdefault = d.getMonth() + 1;
            if(monthdata > 0){
                monthdefault = monthdata;
            }
            if(year > 0){
                defaultyear = year;
            }
            var monthpass = $(document).find('#month').val();
            var yearpass = $(document).find('#year').val();

            if (flexi) {
                dt_flexi_tardrep(monthpass,yearpass,false,'details');
            } else {
                dt_tardrep(monthpass,yearpass,false,'details');
            }
        });
    };
    var init_tardreptable = function(month , year, flexi){
        var monthdata = (month) ? month : false;
        var year = (year) ? year : false;
        var d = new Date();
        var defaultyear = d.getFullYear();
        var monthdefault = d.getMonth() + 1;
        if(monthdata > 0){
            monthdefault = monthdata;
        }
        if(year > 0){
            defaultyear = year;
        }

        PECO.select2Basic($('#month'),'systems/select2month', 'Select Month...', false, false, monthdefault);
        PECO.select2Basic($('#year'),'hris/select2year','Select Year',false,false,defaultyear);
        //dt_tardrep(monthdefault,defaultyear);
        if (flexi) {
            dt_flexi_tardrep(monthdefault,defaultyear)
        } else {
            dt_tardrep(monthdefault,defaultyear);
        }
        /*$.ajax({
            url:PECO.base_url()+'hris/getalltardinessrep',
            type:'post',
            data:{"monthdata":monthdefault,"yeardata":defaultyear},
            dataType:'json',
            beforeSend: function(){
                tardinesstable.dataTable().empty();
                PECO.DTphpLoading(tardinesstable, 'Loading tardiness... ');
            }
        }).done(function (d) {
            tardinesstable.dataTable().empty();
            tardinesstable.dataTable({
                bDestroy: true,
                bPaginate: true,
                bFilter: true,
                bInfo: true,
                bStateSave: true,
                aaData: d.tardinessdata,
                aoColumns: [
                    //  {"data": "expand", sWidth: '10px', sClass: 'expand'},
                    {"data": "num", sWidth: '20px'},
                    {"data": "empid", sWidth: '30px', sClass: 'text-danger text-bold'},
                    {"data": "workshiftass", sWidth: '80px', sClass: 'text-success text-bold'},
                    {"data": "datelog", sWidth: '20px', sClass: 'text-primary'},
                    {"data": "amin", sWidth: '20px', sClass: ''},
                    {"data": "amout", sWidth: '20px', sClass: ''},
                    {"data": "amlate", sWidth: '20px', sClass: ''},
                    {"data": "pmin", sWidth: '20px'},
                    {"data": "pmout", sWidth: '20px'},
                    {"data": "pmlate", sWidth: '20px'},
                    {"data": "total", sWidth: '20px'}
                ],
                searchHighlight: true,
                language: {
                    "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No record found.</h4>'
                },
            });
        }).fail(function () {
            PECO.phpError();
        });*/
    };

    var dt_tardrep = function (month,year,id,print) {
        PECO.dtSubDetails(tardinesstable,'hris/generatetardiness',{month: month,year: year,print:'details'});
        $.ajax({
            url : PECO.base_url() + 'hris/generatetardiness',
            type : 'post',
            dataType : 'json',
            data : {
                month : month,
                year : year,
                id : id,
                print : print
            },
            beforeSend: function(){
                if (print === '') {
                    tardinesstable.dataTable().empty();
                    PECO.DTphpLoading(tardinesstable, 'Loading tardiness... ');
                }
            }
        }).done(function (d) {
            if (print && d.html.length) {
                var date = new Date(year,month,1);
                PECO.pecoRepPrint("Monthly Tardiness Report as of "+month+"/"+year , d.html);
            }

            if (d.list) {
                tardinesstable.dataTable().empty();
                tardinesstable.dataTable({
                    bDestroy: true,
                    bPaginate: true,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    aaData: d.list,
                    aoColumns: [
                        //  {"data": "expand", sWidth: '10px', sClass: 'expand'},
                        {"data": "num", sWidth: '20px'},
                        {"data": "name", sWidth: '30px', sClass: 'text-danger text-bold'},
                        {"data": "bioid", sWidth: '20px', sClass: 'text-primary'},
                        {"data": "position", sWidth: '30px', sClass: 'text-danger text-bold'},
                        {"data": "sched", sWidth: '80px', sClass: 'text-success text-bold'},
                        {"data": "latecount", sWidth: '20px', sClass: ''},
                        {"data": "totallates", sWidth: '20px', sClass: ''},
                    ],
                    searchHighlight: true,
                    language: {
                        "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No record found.</h4>'
                    },
                });
            }
        }).fail(function () {

        });
    };

    var dt_flexi_tardrep = function (month,year,id,print) {
        PECO.dtSubDetails(tardinesstable,'hris/generateflexibletardiness',{month: month,year: year,print:'details'});
        $.ajax({
            url : PECO.base_url() + 'hris/generateflexibletardiness',
            type : 'post',
            dataType : 'json',
            data : {
                month : month,
                year : year,
                id : id,
                print : print
            },
            beforeSend: function(){
                if (print === '') {
                    tardinesstable.dataTable().empty();
                    PECO.DTphpLoading(tardinesstable, 'Loading tardiness... ');
                }
            }
        }).done(function (d) {
            if (print && d.html.length) {
                var date = new Date(year,month,1);
                PECO.pecoRepPrint("Monthly Tardiness Report as of "+month+"/"+year , d.html);
            }

            if (d.list) {
                tardinesstable.dataTable().empty();
                tardinesstable.dataTable({
                    bDestroy: true,
                    bPaginate: true,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    aaData: d.list,
                    aoColumns: d.columns,
                    searchHighlight: true,
                    language: {
                        "emptyTable": '<h4><i class="fa fa-warning text-warning"></i> No record found.</h4>'
                    },
                });
            }
        }).fail(function () {

        });
    };

    return{
        init:function(flexi){
            init_tardrep(flexi);
        }
    }
}();