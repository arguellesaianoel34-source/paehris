<?php

$this->load->helper('cad_helper');

$appdetails = application_info($dataid);
$rateclassid = (isset($appdetails->rateclassid)) ? $appdetails->rateclassid : false;
$basic_submit = true;
$account_submit = true;

/* ======= BASIC INFORMATION QUERIES ====== */

if ($appdetails->systemtype == 1) {
    $system_size = $this->db->select()
        ->from('application_customers_system_size')
        ->where(array('appid' => $dataid,'status' => 305))
        ->order_by('datecreated DESC')
        ->get()->row();

    $get_paneltype = $this->db->select('descs')
        ->from('solar_panel_types')
        ->where('sysid', $system_size->paneltype)
        ->get()->row();

    $nop = $system_size->nop;
    $paneltype = $system_size->paneltype;
} else {
    $get_paneltype = $this->db->select('csg.sysid,csg.systypeid,csg.sptypeid,spt.descs,csg.nop,csg.nos,csg.panelsperstring,csg.invertersize')
        ->from('customer_system_group AS csg')
        ->join('solar_panel_types AS spt','csg.sptypeid = spt.sysid')
        ->where(array('csg.appid' => $dataid, 'csg.status' => 1))
        ->get()->row();

    $nop = ($get_paneltype) ? $get_paneltype->nop : false;
    $paneltype = ($get_paneltype) ? $get_paneltype->sptypeid : false;
}

/* ====== GET TEMP DETAILS ====== */
//IF APPDETAILS IS FALSE, GET TEMP DETAILS

$temp_qry = get_temp_info($dataid);

if ($temp_qry) {
    unset($temp_qry->sysid,$temp_qry->appid);
    foreach ($temp_qry AS $key => $value) {
        if ($value) {
            $appdetails->$key = $value;
        }
    }
    if (!$appdetails->systemsizename) {
        if ($appdetails->systemtype == 1) {
            $system_size = $this->db->select()
                ->from('customer_system_size')
                ->where('sysid', $temp_qry->systemsizeid)
                ->get()->row();
            $appdetails->systemsizename = $system_size->descs;
        }
    }

    if (!$paneltype) {
        $get_paneltype = $this->db->select('descs')
            ->from('solar_panel_types')
            ->where('sysid', $temp_qry->paneltype)
            ->get()->row();

        $paneltype = $temp_qry->paneltype;
    }

    if (!$nop) {
        $nop = $temp_qry->nop;
    }

}

/* ====== ACCOUNT INFORMATION QUERY ====== */

$customer_plan_details = $this->db->select()
    ->from('customer_plan_details')
    ->where(array('appid' => $dataid,'status !=' => 0))
    ->get()->row();

//echo $this->db->last_query();

if ($customer_plan_details) {
    if ($customer_plan_details->standard) {
        $plan_qry = $this->db->select()
            ->from('customer_standard_system_rates')
            ->where(array('sysid' => $customer_plan_details->rateid))
            ->get()->row();

        //echo $this->db->last_query();
    } else {
        $plan_qry = $this->db->select()
            ->from('customer_nonstandard_system_rates')
            ->where(array('appid' => $dataid, 'status' => 1))
            ->get()->row();
    }

    $plan = $plan_qry;
    if ($plan->years != 0) {
        $plan->wifiaccess = $customer_plan_details->wifiaccess ? '<i class="fa fa-check text-success"></i> Yes' : '<i class="fa fa-times text-danger"></i> No';
    }

    $billing_qry = $this->db->select()
        ->from('customer_billing_group')
        ->where(array('planid' => $customer_plan_details->sysid,'status' => 1))
        ->get()->row();

    if ($billing_qry) {
        $plan->installdate = $billing_qry->installdate;
        $plan->billfrequency = $billing_qry->billfrequency;

        $billstart_qry = $this->db->select()
            ->from('customer_billing_trn')
            ->where(array('groupid' => $billing_qry->sysid,'status !=' => 0))
            ->get()->row();

        if ($billstart_qry) {
            $plan->billingyear = $billstart_qry->years;
            $plan->billingstart = $billstart_qry->months;
        }
    }

    $temp_info = get_temp_info($dataid,'installdate,billingstart,billingyear,billfrequency');

    if ($temp_info) {
        foreach ($temp_info AS $key => $value) {
            if ($value) {
                $plan->$key = $value;
            }
        }
    }
} else {
    $plan = get_temp_info($dataid,'years,monthlyamt,wifiaccess,installdate,billingstart,billingyear,billfrequency');
    if ($plan) {
        $plan->wifiaccess = ($plan->wifiaccess) ? '<i class="fa fa-check text-success"></i> Yes' : '<i class="fa fa-times text-danger"></i> No';
    }
}

?>
<style type="text/css">
    .ellipsis {
        max-width: 30% !important;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        display: inline-block !important;
    }
</style>
<div class="row tab-pane fade in" id="archiving">
    <div class="col-md-6">
        <?php
        customer_application_basicinfo($dataid, false, false);
        ?>
        <div class="portlet light bordered margin-top-10">
            <div class="portlet-title">
                <div class="caption">
                    <span class="label label-danger bold pull-left margin-right-10">1</span>
                    <i class="fa fa-info-circle"></i>
                    <span class="caption-subject font-red-flamingo bold uppercase">Basic Info</span>
                </div>
            </div>
            <div class="portlet-body">
                <!--<h1>PUT BASIC HERE!</h1>
                Basic:
                <ol>
                    <li>DU</li>
                    <li>DU Rate</li>
                    <li>System Type</li>
                    <li>System Size</li>
                    <li>Number of Panels</li>
                    <li>Panel Type</li>
                </ol>-->
                <form action="<?php echo base_url(); ?>cad/savetempinfo" method="post" id="form_basic_information">
                    <input type="hidden" name="appid" value="<?php echo $dataid; ?>">
                    <div class="form-group row">
                        <div class="col-md-8">
                            <label class="control-label bold uppercase">DU Name</label>
                            <div id="detail_distutility">
                            <?php if ($appdetails->duid) {
                                $du = get_dist_utility_list($appdetails->duid);
                                echo '<span class="form-control">'.$du->name .' - '.$du->fullname.'</span>';
                            } else {?>
                                <input class="form-control" id="select2_du" name="duid" placeholder="Distribution Utility..." value="" required>
                            <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="control-label bold uppercase">DU Rate</label>
                            <div id="detail_durate">
                            <?php if ($appdetails->durate) {
                                echo '<span class="form-control">'.number_format($appdetails->durate,2).'</span>';
                            } else {?>
                                <input type="number" step="any" class="form-control" id="durate" name="durate" placeholder="DU Rate..." value="" required>
                            <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-7">
                            <label class="control-label bold uppercase">System Type</label>
                            <div id="detail_systemtype">
                                <?php if ($appdetails->systemtype) {
                                    if ($appdetails->systemtype == 1) {
                                        echo '<span class="form-control"><i class="fa fa-check text-success"></i> Standard</span>';
                                    }
                                    if ($appdetails->systemtype == 2) {
                                        echo '<span class="form-control"><i class="fa fa-check text-success"></i> Non-Standard</span>';
                                    }
                                } else {?>
                                    <div class="row" id="icheck_system_type">
                                        <div class="icheck-inline">
                                            <div class="col-md-6">
                                                <label for="radio_standard" class="bold" style="width: 100%">
                                                    <input id="radio_standard" name="systemtype" data-target="standard" data-radio="iradio_square-orange" class="icheck" value="1" type="radio" required> Standard
                                                </label>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="radio_nonstandard" class="bold" style="width: 100%">
                                                    <input id="radio_nonstandard" name="systemtype" data-target="nonstandard" data-radio="iradio_square-orange" class="icheck" value="2" type="radio" required> Non-Standard
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="control-label bold uppercase">System Size</label>
                            <div id="detail_systemsize">
                                <?php if ($appdetails->systemsizeid) {
                                    echo '<span class="form-control">'.$appdetails->systemsizename.'</span>';
                                } else {
                                    ?>
                                    <input class="form-control" id="systemsize" name="systemsizeid" placeholder="System Size..." value="" required>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-8">
                            <label class="control-label bold uppercase">Panel Type</label>
                            <div id="detail_paneltype">
                            <?php if (isset($get_paneltype) && $get_paneltype && $get_paneltype->descs) {
                                echo '<span class="form-control">'.$get_paneltype->descs.'</span>';
                                echo '<input type="hidden" id="paneltype" value="'.$paneltype.'">';
                            } else { ?>
                                <input class="form-control" id="select2_panel_type" name="paneltype" placeholder="Panel Type..." value="<?php echo $paneltype; ?>" required>
                            <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="control-label bold uppercase"># Panels</label>
                            <div id="detail_nop">
                            <?php if ($nop) {
                                echo '<span class="form-control">'.number_format($nop).'</span>';
                                echo '<input type="hidden" id="nop" value="'.$nop.'">';
                            } else {?>
                                <input type="number" step="1" class="form-control" id="nop" name="nop" placeholder="Number of Panels..." value="<?php echo $nop; ?>" required>
                            <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-8">
                            <label class="control-label bold uppercase">Inverter Brand</label>
                            <div id="detail_inverterbrand">
                            <?php if (isset($appdetails->inverterbrand)) {
                                echo '<span class="form-control">'.$appdetails->inverterbrand.'</span>';
                            } else { ?>
                                <input class="form-control" id="inverterbrand" name="inverterbrand" placeholder="Inverter brand..." value="" required>
                            <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="control-label bold uppercase">Serial Number</label>
                            <div id="detail_invertersn">
                            <?php if (isset($appdetails->invertersn)) {
                                echo '<span class="form-control">'.$appdetails->invertersn.'</span>';
                            } else {?>
                                <input class="form-control" id="invertersn" name="invertersn" placeholder="Serial number..." value="" required>
                            <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-footer">
                        <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="label label-danger bold pull-left margin-right-10">2</span>
                    <i class="fa fa-link"></i>
                    <span class="caption-subject font-green-sharp bold uppercase" id="caption_essrno"><?php echo ($appdetails->essrno) ? 'PAE'.str_pad($appdetails->essrno,6,'0',STR_PAD_LEFT) : ''; ?></span>
                    <span class="caption-subject font-red-flamingo bold uppercase">Account Info</span>
                </div>
                <div class="tools">

                </div>
            </div>
            <div class="portlet-body">
                <!--<h1>PUT ACCOUNT INFO HERE!</h1>
                Account:
                <ol>
                    <li>PAE #</li>
                    <li>Installment Plan</li>
                    <li>Agreed Amount</li>
                    <li>Online Monitoring</li>
                    <li>Date of Installation</li>
                    <li>Start of Payment</li>
                    <li>Frequency</li>
                </ol>-->
                <form action="<?php echo base_url(); ?>cad/savetempinfo" method="post" id="form_account_information">
                    <input type="hidden" name="appid" value="<?php echo $dataid; ?>">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="input-group input-group-lg" id="detail_essrno">
                            <?php if ($appdetails->essrno) {
                                echo false;
                            } else { ?>
                                <span class="input-group-addon h1">PAE</span>
                                <input type="number" id="essr_no" name="essrno" class="form-control" maxlength="7" placeholder="000000A" required>
                            <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="control-label bold uppercase">Installment Plan</label>
                            <div id="detail_years">
                                <?php if (isset($plan) && isset($plan->years)) {
                                    if ($plan->years > 0) {
                                        echo '<span class="form-control">'.$plan->years.' Years</span>';
                                    } else {
                                        echo '<span class="form-control">Outright</span>';
                                    }
                                } else { ?>

                                    <input class="form-control" id="select2_installmentplan" name="years" placeholder="Installment Plan..." value="" required>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="control-label bold uppercase">Agreed Amount</label>
                            <div id="detail_monthlyamt">
                                <?php if (isset($plan) && isset($plan->monthlyamt)) {
                                    echo '<span class="form-control">&#8369; '.number_format($plan->monthlyamt,2).'</span>';
                                } else {?>
                                    <div class="input-group" >
                                        <span class="input-group-addon">&#8369;</span>
                                        <input type="number" step="1" class="form-control" id="monthlyamt" name="monthlyamt" placeholder="Agreed amount..." value="" required>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="control-label bold uppercase">Online Monitoring</label>
                            <div id="detail_wifiaccess">
                                <?php if (isset($plan) && isset($plan->wifiaccess)) {
                                    echo '<span class="form-control">'.$plan->wifiaccess.'</span>';
                                } else {?>
                                    <div class="row" id="icheck_wifi_access">
                                        <div class="icheck-inline">
                                            <div class="col-md-6">
                                                <label for="radio_standard" class="bold" style="width: 100%">
                                                    <input id="radio_haswifi" name="wifiaccess" data-target="standard" data-radio="iradio_square-orange" class="icheck" value="1" type="radio" required> Yes
                                                </label>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="radio_nonstandard" class="bold" style="width: 100%">
                                                    <input id="radio_nowifi" name="wifiaccess" data-target="nonstandard" data-radio="iradio_square-orange" class="icheck" value="0" type="radio" required> No
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="control-label bold uppercase">Date of Installation</label>
                            <div id="detail_installdate">
                                <?php if (isset($plan) && isset($plan->installdate)) {
                                    echo '<span class="form-control">'.date('F j, Y', strtotime($plan->installdate)).'</span>';
                                } else {?>
                                    <input type="date" class="form-control" id="installdate" name="installdate" placeholder="Installation date..." value="" required>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label class="control-label bold uppercase">Start of Payment</label>
                            <div id="detail_billingstart">
                                <?php if (isset($plan) && isset($plan->billingstart)) {
                                    echo '<span class="form-control">'.date('F, Y',strtotime($plan->billingstart.'/1/'.$plan->billingyear)).'</span>';
                                } else { ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input class="form-control" id="select2_billing_start" name="billingstart" placeholder="Billing start..." value="" required>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="number" step="1" min="2020" max="<?php echo date('Y') + 1; ?>" class="form-control" id="select2_billing_year" name="billingyear" placeholder="Year..." value="" required>

                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="control-label bold uppercase">Frequency</label>
                            <div id="detail_billfrequency">
                                <?php if (isset($plan) && isset($plan->billfrequency)) {
                                    echo '<span class="form-control">Every '.ordinal($plan->billfrequency).' of the month.</span>';
                                } else {?>
                                    <input class="form-control" id="select2_bill_frequency" name="billfrequency" placeholder="Billing frequency..." value="" required>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-footer">
                        <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="portlet light bordered margin-top-10">
            <div class="portlet-title">
                <div class="caption">
                    <span class="label label-danger bold pull-left margin-right-10">3</span>
                    <i class="fa fa-file-pdf-o"></i>
                    <span class="caption-subject font-red-flamingo bold uppercase">Documents</span>
                </div>
                <div class="tabbable-line pull-right">
                    <ul class="nav nav-tabs " id="doc_list_tabs">
                        <li class="active">
                            <a href="#appdocs_view" data-toggle="tab" aria-expanded="true" data-table="tbl_appdoc_list"> Application Docs  </a>
                        </li>
                        <li class=" ">
                            <a href="#reqdocs_view" data-toggle="tab" aria-expanded="true" data-table="tbl_appreq_list"> Required Docs  </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="portlet-body">
                <!--<h1>PUT Uploaded documents HERE!</h1>
                Docs:
                <ol>
                    <li>TSSR</li>
                    <li>Proposal (Optional)</li>
                    <li>CCA (If Any)</li>
                    <li>Contract</li>
                    <li>Acknowledgement Form</li>
                    <li>Docs Submitted</li>
                </ol>-->
                <?php
                //TSSR: CHECK IF THERE IS A PUBLISHED INSPECTION REPORT.
                //PROPOSAL: CHECK FOR SIGNED PROPOSAL
                //CCA: CHECK FOR SIGNED CCA
                //CONTRACT: UPLOAD SIGNED CONTRACT
                //ACKNOWLEDGEMENT FORM: UPLOAD OR PASTE G-DRIVE LINK
                //CLICK REMOVE FOR OPTIONAL DOCUMENTS
                //IF UPLOADED BUT WANTS TO REPLACE, CLICK EDIT FOR RE-UPLOADING
                ?>
                <form action="<?php echo base_url(); ?>cad/savetempinfo" method="post" id="form_docs_checklist">
                    <input type="hidden" name="appid" value="<?php echo $dataid; ?>">
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="appdocs_view">
                            <table class="table table-bordered table-condensed table-hover" id="tbl_appdoc_list" style="width: 100%">
                                <thead>
                                <th>Document</th>
                                <th><i class="fa fa-check-square-o"></i></th>
                                <th>Location</th>
                                <th><i class="fa fa-cogs"></i></th>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade in " id="reqdocs_view">
                            <table class="table table-bordered table-condensed table-hover" id="tbl_appreq_list" style="width: 100%">
                                <thead>
                                <th>Document</th>
                                <th>Present</th>
                                <th>Location</th>
                                <th>Control</th>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="portlet-footer">
                        <button type="button" class="btn btn-primary" id="btn_add_document" style="width: 100%"><i class="fa fa-plus"></i> Add Document</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <button id="btn_process_customer" class="btn btn-primary btn-lg center-block"><i class="fa fa-refresh"></i> Process Customer!</button>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/global/plugins/icheck/icheck.min.js"></script>
<script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js" type="text/javascript"></script>
<script type="text/javascript">
    CAD.archiving(<?php echo $dataid; ?>);
</script>
<!-- DATE PICKER!-->

<!--<script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/sales/main.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/inspection/main.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/maps/main.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/attachements/main.js" type="text/javascript"></script>

<script type="text/javascript">
    INSPECTION.application(<?php echo $dataid; ?>);
    INSPECTION.team(<?php echo $dataid; ?>, 36);
    CAD.profile(<?php echo $dataid; ?>, 2);
    //ATTACHEMENTS.init(<?php echo $dataid; ?>);
    ATTACHEMENTS.docs(<?php echo $dataid; ?>);
    SALES.viewer(<?php echo $dataid; ?>);
    //SALES.contract(<?php echo $dataid; ?>);
    CAD.requirements(<?php echo $dataid; ?>,true);
    CAD.viewer(<?php echo $dataid; ?>,2);

    $('#trn_logs').dataTable({
        bDestroy: true,
        bPaginate: true,
        bFilter: false,
        bInfo: false,
        bStateSave: true,
        bLengthChange: false,
        bAutoWidth: false,
        scrollY: '250px',
    })
</script>-->