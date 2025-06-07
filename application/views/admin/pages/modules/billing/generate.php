<!-- TESTING --->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.css">


<style>
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }

    .form-group {
        margin-bottom: 0px !important;
    }


    div.input-icon {
        background: #fff !important;
    }

    .help-block {
        color: #ccc;
        font-size: 11px;
    }
    .form-md-line-input .fileinput .input-group-addon {
        background: rgba(177,176,176,0.47) !important;
        z-index: 3000 !important;
    }
    .form-md-line-input .fileinput .input-group-addon .btn.red-intense {
        background: rgba(251,124,126,0.77) !important;
    }

    .tiles .tile:last-child{
        width: auto !important;
    }

    .tiles .tile {
        position: relative;
    }
    .tiles .tile .fa-bg {
        font-size: 200px !important;
        position: absolute;
        bottom: -20px;
        color: #fff;
        opacity: 0.2;
        -moz-opacity: 0.2;
        -webkit-opacity: 0.2;
        margin: 0px 0px !important;
        height: 100%;
    }

    .select2-data-flat .select2-search-choice {
        border: 1px solid transparent !important;
        padding: 0px 0px !important;
    }
    .select2-data-flat .select2-search-choice div {
        left: 0px !important;
    }
    .select2-data-flat .select2-search-choice-close {
        display: none;
    }

    .select2-data-flat .select2-container.select2-container-active {
        border-bottom: transparent 1px solid !important;
    }
    .select2-data-flat .select2-input.select2-default,
    .select2-data-flat .select2-search-field,
    .select2-data-flat .select2-choices{
        border: transparent 1px solid !important;
        padding: 2px 0px !important;
    }
    .select2-data-flat .select2-choices{
        width: 100% !important;
        height: 30px !important;
    }
    .select2-data-flat .select2-search-field, .select2-data-flat .select2-search-field input{
        height: 30px !important;
        margin: 0px 0px !important;
        padding: 0px 0px !important;
        top: -5px !important;
    }


</style>


<div>
    <div class="row">
        <form role="form" class="form-horizontal asset-entry-form"  action="<?php echo base_url(); ?>billing/createcontractdraft" method="post" id="frm_newaccount">
            <div class="col-md-12">
                <div class="portlet light form" id="" >

                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-check"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">Generate Contract</span>
                        </div>
                    </div>
                    <div class="portlet-body ">
                        <br>
                        <div id="query-status"></div>
                        <div class="row">
                            <div class="col-md-6">
                            <div class="portlet box green col-md-12">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-check-square-o"></i>
                                        <span class="caption-subject bold uppercase">Type</span>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group" id="apptype_row">
                                                <label class="form-label col-md-1 center"> <span class="required"></span></label>
                                                <div class="col-md-11">
                                                    <div class="icheck-inline">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <label><input name="apptype" value="1" type="radio" data-radio="iradio_square-red" class="icheck" required/> Residential</label>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label><input name="apptype" value="2" type="radio" data-radio="iradio_square-red" class="icheck" required/> Commercial</label>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label><input name="apptype" value="3" type="radio" data-radio="iradio_square-red" class="icheck" required/> Government</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="portlet box red-sunglo col-md-12">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-user"></i>
                                        <span class="caption-subject bold uppercase">Basic Info</span>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div id="non_residential">

                                    </div>
                                    <div id="person_info">
                                        <div class="form-group margin-top-10">
                                            <label class="col-md-3 control-label"> Last Name <span class="required"></span></label>
                                            <div class="col-md-9">
                                                <input name="lastname" type="text" class="form-control data-entry" id="lastname" placeholder="Last Name" data-toggle="autocomplete" col-name="lastname" value>
                                                <div class="form-control-focus"> </div>
                                            </div>
                                        </div>
                                        <div class="form-group margin-top-10">
                                            <label class="col-md-3 control-label">First Name <span class="required"></span></label>
                                            <div class="col-md-9">
                                                <input name="firstname" type="text" class="form-control data-entry" id="firstname" placeholder="First Name" data-toggle="autocomplete" col-name="firstname">
                                                <div class="form-control-focus"> </div>
                                            </div>
                                        </div>
                                        <div class="form-group margin-top-10">
                                            <label class="col-md-3 control-label">Middle Name <span class="required"></span></label>
                                            <div class="col-md-9">
                                                <input name="middlename" type="text" class="form-control data-entry" id="middle_initial" placeholder="Middle Name" data-toggle="autocomplete" col-name="middlename">
                                                <div class="form-control-focus"> </div>
                                            </div>
                                        </div>
                                        <div class="form-group margin-top-10">
                                            <label class="col-md-3 control-label">Suffix</label>
                                            <div class="col-md-9">
                                                <select name="suffix" class="form-control data-entry" id="suffix">
                                                    <option value=""></option>
                                                    <?php foreach (select_person_title(70) as $row) { ?>
                                                        <option value="<?php echo $row->sysid; ?>"><?php echo $row->names; ?> - <?php echo $row->descriptions; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <div class="form-control-focus"> </div>
                                            </div>
                                        </div>
                                        <div class="form-group margin-top-10">
                                            <label class="col-md-3 control-label"><span class="required"></span> Specific Address</label>
                                            <div class="col-md-9">
                                                <textarea class="form-control" rows="2" id="addrspecific" name="addrspecific" placeholder="Ex: Blk9 Lot20, DECA Homes Subd., Red Gate, Near Security Guard Outpost"></textarea>
                                                <span class="help-block">Provide specific street address, blk, house number and landmark.</span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            </div>
                            <div class="col-md-6">
                            <div class="portlet box blue col-md-12" id="frm_application_missing_details">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-bar-chart"></i>
                                        <span class="caption-subject bold uppercase">System Type and Plan</span>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="form-group margin-top-10" id="systemtype_row">
                                        <label class="col-md-3 control-label person-name bold" for="name"><i class="fa fa-check-square-o"></i> System Type</span></label>
                                        <div class="col-md-9">
                                            <div class="col-md-6 icheck-inline">
                                                <input class="icheck" data-target="#standardsize" data-radio="iradio_square-red" id="standardtype" name="systemtype" type="radio" checked value="1" required> <label class="bold uppercase" for="standardtype">Standard</label>
                                            </div>
                                            <div class="col-md-6 icheck-inline">
                                                <input class="icheck" data-target="#nonstandardsize" data-radio="iradio_square-red" id="nonstandardtype" name="systemtype" type="radio" aria-label="" value="2" required> <label class="bold uppercase" for="nonstandardtype">Non-standard</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group margin-top-10">
                                        <label class="col-md-3 control-label person-name bold" for="name"><i class="fa fa-line-chart"></i> System Size</span></label>
                                        <div class="col-md-9">
                                            <div class="row margin-bottom-5 " id="standardsize">
                                                <div class="col-md-12">
                                                    <input class="form-control" id="select2_systemsize" name="newsize" required>
                                                </div>
                                            </div>
                                            <div class="row margin-bottom-5 " id="nonstandardsize">
                                                <div class="col-md-12">
                                                    <input class="form-control" id="newsystemsize" name="newsize" placeholder="Build Name..." disabled required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group margin-top-10">
                                        <div class="col-md-3 control-label bold">
                                            <i class="fa fa-clock-o"></i> Installment Plan
                                        </div>
                                        <div id="installmentplan">
                                            <div class="col-md-4">
                                                <input class="form-control" id="select2_plantype" name="plantype" placeholder="Plan Duration...">
                                                <!--<div class="row" id="installmentplan">
                                                    <div class="col-md-3 icheck-inline">
                                                        <input class="icheck" data-radio="iradio_square-blue" id="outrightpay" name="plantype" type="radio" checked value="0"> <label class="bold " for="outrightpay">Outright</label>
                                                    </div>
                                                    <div class="col-md-3 icheck-inline">
                                                        <input class="icheck" data-radio="iradio_square-blue" id="payment2years" name="plantype" type="radio" aria-label="" value="2"> <label class="bold " for="payment2years">2 Years</label>
                                                    </div>
                                                    <div class="col-md-3 icheck-inline">
                                                        <input class="icheck" data-radio="iradio_square-blue" id="payment5years" name="plantype" type="radio" aria-label="" value="5"> <label class="bold " for="payment5years">5 Years</label>
                                                    </div>
                                                    <div class="col-md-3 icheck-inline">
                                                        <input class="icheck" data-radio="iradio_square-blue" id="payment10years" name="plantype" type="radio" aria-label="" value="10"> <label class="bold " for="payment10years">10 Years</label>
                                                    </div>
                                                </div>-->
                                            </div>
                                            <div class="col-md-5">
                                                <input type="number" class="form-control" id="systemprice" name="price" placeholder="Monthly Amortization..." required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="portlet box yellow-gold col-md-12">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <span class="caption-subject bold uppercase">Other Details</span>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="form-group margin-top-10">
                                        <label class="col-md-3 control-label bold"><i class="fa fa-calendar"></i> Installation Date</span></label>
                                        <div class="col-md-9">
                                            <input type="date" class="form-control" name="installdate" required>
                                        </div>
                                    </div>
                                    <div class="form-group margin-top-10">
                                        <label class="col-md-3 control-label bold"><i class="fa fa-file-text"></i> Billing Start</span></label>
                                        <div class="col-md-9">
                                            <div class="row">
                                                <div class="form-group col-md-8">
                                                    <div class="row">
                                                        <div class="col-md-5">
                                                            <label class="control-label bold">Year</span></label>
                                                            <input class="form-control" type="number" id="billingstart_year" name="billingyear" value="<?=date('Y')?>">
                                                        </div>
                                                        <div class="col-md-7">
                                                            <label class="control-label bold">Month</span></label>
                                                            <input class="form-control" id="select2_billingstart" name="billingstart">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="control-label bold"> Frequency</span></label>
                                                    <input class="form-control" id="select_billdate" name="billdate" required>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-footer btn-group">
                        <div>
                            <button class="btn btn-default inline pull-left" type="reset" style="margin-right: 20px;"><i class="fa fa-refresh"></i> Reset</button>
                            <button class="btn btn-primary pull-right" style="margin-right: 20px;" type="submit"><i class="fa fa-gears"></i> Generate</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- BEGIN PAGE LEVEL PLUGINS -->

<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/gmaps/gmaps.js" type="text/javascript"></script>

<!-- END PAGE LEVEL PLUGINS -->

<script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js"></script>
<script src="<?php echo base_url(); ?>assets/global/scripts/address.js"></script>
<script src="<?php echo base_url(); ?>assets/pages/sales/main.js" type="text/javascript"></script>


<script type="text/javascript">
    //ADDRESS.init(175);
    CAD.contract();
    SALES.contract();
    PECO.select2Basic($('#select2_billingstart',document),'systems/select2month','Select start of billing series...',false,false,false);
    PECO.select2Basic($('#select_billdate',document),'billing/select2billingdate','Billing date...',false,false,false);
</script>
