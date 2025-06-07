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
        <form role="form" class="form-horizontal asset-entry-form"  action="<?php echo base_url(); ?>cad/createproposaldraft" method="post" id="frm_newaccount">
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
                                                    <div class="col-md-12 margin-top-10">
                                                        <table style="width: 100%;" id="tbl_sysrates" class="zui-table table table-hover table-striped table-bordered" >
                                                            <thead>
                                                            <th>Outright</th>
                                                            <th>Two Years</th>
                                                            <th>Three Years</th>
                                                            <th>Five Years</th>
                                                            <th>Ten Years</th>
                                                            <th>Monthly Average</th>
                                                            <th>Summer Average</th>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <td>
                                                                    <?php echo dt_inline_input('outright','number',false,array('step' => '.01'),'text-align-right',array('width' => '100%','height' => '34px')); ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo dt_inline_input('twoyrs','number',false,array('step' => '.01'),'text-align-right',array('width' => '100%','height' => '34px')); ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo dt_inline_input('threeyrs','number',false,array('step' => '.01'),'text-align-right',array('width' => '100%','height' => '34px')); ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo dt_inline_input('fiveyrs','number',false,array('step' => '.01'),'text-align-right',array('width' => '100%','height' => '34px')); ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo dt_inline_input('tenyrs','number',false,array('step' => '.01'),'text-align-right',array('width' => '100%','height' => '34px')); ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo dt_inline_input('monthlyave','number',false,array('step' => '.01'),'text-align-right',array('width' => '100%','height' => '34px')); ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo dt_inline_input('summerave','number',false,array('step' => '.01'),'text-align-right',array('width' => '100%','height' => '34px')); ?>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>

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
                                        <div class="form-group margin-top-10 row">
                                            <div class="col-md-8">
                                                <label class="control-label bold uppercase">DU Name</label>
                                                <input class="form-control" id="select2_du" name="duid" placeholder="Distribution Utility...">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="control-label bold uppercase">DU Rate</label>
                                                <input type="number" step="any" class="form-control" id="durate" name="durate" placeholder="DU Rate...">
                                            </div>
                                        </div>
                                        <div class="form-group margin-top-10 row">
                                            <div class="col-md-12">
                                                <label class="control-label bold uppercase">PV_Location Picture</label>
                                                <input type="file" class="form-control" id="pvlayout" name="pvlayout[]" multiple placeholder="Picture of PV Layout...">
                                            </div>
                                        </div>
                                        <div class="form-group margin-top-10 row">
                                            <div class="col-md-12">
                                                <label class="control-label bold uppercase">Monthly Production Picture</label>
                                                <input type="file" class="form-control" id="mpprojection" name="mpprojection[]" multiple placeholder="Picture of Monthly Production Projection...">
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
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/fileinput.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/fr.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/es.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/gmaps/gmaps.js" type="text/javascript"></script>

<!-- END PAGE LEVEL PLUGINS -->

<script src="<?php echo file_versioning('assets/pages/cad/newaccount.js'); ?>"></script>
<script src="<?php echo file_versioning('assets/global/scripts/address.js'); ?>"></script>
<script src="<?php echo file_versioning('assets/pages/sales/main.js'); ?>" type="text/javascript"></script>
<script src="<?php echo file_versioning('assets/pages/attachements/main.js'); ?>" type="text/javascript"></script>


<script type="text/javascript">
    //ADDRESS.init(175);
    CAD.contract();
    SALES.generator();
</script>
