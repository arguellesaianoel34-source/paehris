<?php
$trnid = $this->uri->segment(4);
$qry_trn = $this->db->select()->from('transaction_request_main_trails')->where('sysid', $trnid)->get()->row();
$qry_stg = $this->db->select()->from('prime_transaction_flow_main_stages')->where(array('sysid' => $qry_trn->stageid))->get()->row();
$flowid = $qry_stg->flowid;

?>
<!-- DATEPICKER CSS START!-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/datepicker3.css">
<!-- DATEPICKER CSS END!-->
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

    #tbl_components.dataTable td:nth-child(2) {
        max-width: 250px;
    }

    table.dataTable td  {
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }
</style>

<div class="row tab-pane fade in">
    <div class="col-md-6">
        <?php
        $appdetails = get_application_details($dataid);
        $rateclassid = (isset($appdetails->rateclassid)) ? $appdetails->rateclassid : false;
        customer_application_basicinfo($dataid, true, false);
        ?>
    </div>
    <div class="col-md-6">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-red-flamingo bold uppercase">Installation Details</span>
                </div>
                <div class="tools">
                </div>
                <div class="tabbable-line pull-right">
                    <ul class="nav nav-tabs ">
                        <li class="active">
                            <a href="#install_date" data-toggle="tab" aria-expanded="true"> Installation Dates </a>
                        </li>
                        <li class="">
                            <a href="#install_items" data-toggle="tab" aria-expanded="true"> Items </a>
                        </li>
                        <li class="">
                            <a href="#inverter_sn" data-toggle="tab" aria-expanded="true"> Inverters  </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="portlet-body tab-content">
                <div class="tab-pane fade in active" id="install_date">
                    <!--<table class="table table-hover table-striped table-bordered">
                        <tbody>
                        <tr style="margin: 15px !important;">
                            <td id="installation">
                                <label class="col-md-4 control-label bold" for="installation_date"><i class="fa fa-calendar-plus-o"></i> Installation</span></label>
                                <div class="col-md-6" id="installation_text">
                                    <input type="date" id="installation_date" class="form-control" name="installationdate" data-type="1" required>
                                </div>
                                <div class="col-md-2" id="installation_buttons">
                                    <button type="button" class="btn btn-primary pull-right btn-sm" id="btn_save_date"><i class="fa fa-save"></i> </button>
                                </div>
                            </td>
                        </tr>
                        <tr style="margin: 15px !important;">
                            <td id="energized">
                                <label class="col-md-4 control-label bold" for="completion_date"><i class="fa fa-calendar-check-o"></i> Energized</span></label>
                                <div class="col-md-6" id="energized_text">
                                    <input type="date" id="energized_date" class="form-control" name="dateenergized" data-type="2" required>
                                </div>
                                <div class="col-md-2" id="energized_buttons">
                                    <button type="button" class="btn btn-primary pull-right btn-sm" id="btn_save_date"><i class="fa fa-save"></i> </button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>-->
                    <ul class="list-group summary column">
                        <li class="list-group-item" id="installation">
                            <label class="label-name col-md-4 bold" for="installation_date"><i class="fa fa-calendar-plus-o"></i> Installation</label>
                            <div class="col-md-6" id="installation_text">
                                <input type="date" id="installation_date" class="form-control" name="installationdate" data-type="1" required>
                            </div>
                            <div class="col-md-2" id="installation_buttons">
                                <div class="btn-group pull-right" id="date_controls" style="width: 75px !important;">
                                    <button type="button" class="btn btn-primary pull-right btn-sm" id="btn_save_date"><i class="fa fa-save"></i> </button>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item" id="energized">
                            <label class="label-name col-md-4 bold" for="completion_date"><i class="fa fa-calendar-check-o"></i> Energized</label>
                            <div class="col-md-6" id="energized_text">
                                <input type="date" id="energized_date" class="form-control" name="dateenergized" data-type="2" required>
                            </div>
                            <div class="col-md-2" id="energized_buttons">
                                <div class="btn-group pull-right" id="date_controls" style="width: 75px !important;">
                                    <button type="button" class="btn btn-primary pull-right btn-sm" id="btn_save_date"><i class="fa fa-save"></i> </button>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="tab-pane fade in " id="install_items">
                    <div id="installation_items">
                        <div class="row ">
                            <div class="col-md-12">
                                <div id="installation_items_note" style="width: 100%;"></div>
                                <div class="list-group summary column" id="sps_params">
                                    <div class="row" style="border: transparent !important; border-bottom: 1px solid #ddd !important;">
                                        <div class="list-group-item col-md-4">
                                            <span class="col-md-7 label-name text-primary bold" >Panel Type </span>
                                            <span class="col-md-5 font-red-flamingo bold number params" id="paneltype"></span>
                                        </div>
                                        <div class="list-group-item col-md-4" >
                                            <span class="col-md-8 label-name text-primary bold" ># of Panels </span>
                                            <span class="col-md-4 font-red-flamingo bold number params" id="nop"></span>
                                        </div>
                                        <div class="list-group-item col-md-4" >
                                            <span class="col-md-8 label-name text-primary bold" ># of Strings </span>
                                            <span class="col-md-4 font-red-flamingo bold number params" id="nos"></span>
                                        </div>
                                    </div>
                                    <div class="row" style="border: transparent !important; border-bottom: 1px solid #ddd !important;">
                                        <div class="list-group-item  col-md-6">
                                            <span class="col-md-7 label-name text-primary bold" >Panels per String </span>
                                            <span class="col-md-5 font-red-flamingo bold number params" id="panelsperstring"></span>
                                        </div>
                                        <div class="list-group-item  col-md-6">
                                            <span class="col-md-5 label-name text-primary bold" >Inverter(s) </span>
                                            <span class="col-md-7 font-red-flamingo bold number params" id="invertersize"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tabbable-line">
                            <ul class="nav nav-tabs " id="installation_item_tabs">
                                <li class="active">
                                    <a href="#sps_components" data-toggle="tab" aria-expanded="true" data-id="1"> Components </a>
                                </li>
                                <li class="">
                                    <a href="#sps_accessories" data-toggle="tab" aria-expanded="true" data-id="2"> Accessories </a>
                                </li>
                                <li class="">
                                    <a href="#sps_consumables" data-toggle="tab" aria-expanded="true" data-id="3"> Consumables </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="sps_items">
                                    <table class="table table-striped table-hover types table-condensed table-sm" id="tbl_components">
                                        <thead>
                                        <th style="width: 10% !important; ">#</th>
                                        <th style="width: 50% !important;">Item</th>
                                        <th style="width: 5% !important;">Qty</th>
                                        <th style="width: 5% !important;">Unit</th>
                                        <th style="width: 5% !important;"><i class="fa fa-wrench"></i> </th>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade in " id="inverter_sn">
                    <form id="frm_inverter_details" class="row" method="post" action="<?php echo base_url(); ?>installation/addinverterdetails">
                        <div class="col-sm-4" style="padding-right: 0 !important;">
                            <label class="form-label bold"><i class="fa fa-bolt"></i> Inverter</td></label>
                            <input class="form-control" id="inverter_type" name="invertertype">
                        </div>
                        <div class="col-sm-3" style="padding-right: 0 !important;">
                            <label class="form-label bold"><i class="fa fa-tag"></i> Brand</td></label>
                            <input class="form-control" id="inverter_brand" name="inverterbrand">
                        </div>
                        <div class="col-sm-3" style="padding-right: 0 !important;">
                            <label class="form-label bold"><i class="fa fa-barcode"></i> Serial #</td></label>
                            <input class="form-control" id="inverter_serial" name="invertersn">
                        </div>
                        <div class="col-sm-2" style="padding-right: 0 !important;">
                            <button type="submit" class="btn btn-primary btn-sm" style="margin-top: 25px !important;"><i class="fa fa-plus"></i> Add</button>
                        </div>
                    </form>

                    <div class="row margin-top-15">
                        <div class="col-md-12">
                            <table id="tbl_inverter_details" class="table table-hover table-striped table-bordered" style="width: 100% !important;">
                                <thead>
                                <th>Inverter</th>
                                <th>Brand</th>
                                <th>Serial #</th>
                                <th><i class="fa fa-wrench"></i> </th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="portlet light bordered">
            <div class="portlet-body">
                <button id="btn_finalize_installation" class="btn btn-primary btn-lg" style="width: 100%">FINALIZE!</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/installation/main.js"></script>
<script type="text/javascript">
    INSTALLATION.application(<?php echo $dataid; ?>);
</script>