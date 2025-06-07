<?php

/*function has_access ($module,$access) {
    if (is_array($module)) {
        foreach ($module AS $key => $value) {
            if (!has_access($value,$access[$key])) {
                return false;
            }
        }
        return true;
    } else if (in_array($module,$access)) {
        return true;
    } else {
        return false;
    }
}*/

//IF MODULEID IS NO IN NAVIDS, TEXT, ELSE, INPUT

$get_flow_stages = $this->db->select('sysid,moduleid')
    ->from('prime_transaction_flow_main_stages')
    ->where(array('flowid' => 2, 'status' => 1))
    ->get();

$modules = array();
//$access = array();

$qry_trl = $this->db->select('trmt.*')->from('transaction_request_main_trails AS trmt')
    ->join('prime_transaction_flow_main_stages AS stg','trmt.stageid = stg.sysid AND stg.flowid = 2','left')
    ->where(array('trmt.dataid' => $dataid))
    ->get()->row();

$a = $this->model_query->hist_trans($dataid,$qry_trl->trnid);

$trn = array();
if ($a->num_rows() > 0) {
    foreach ($a->result() AS $hty) {
        $trn[] = $hty->stageid;
    }
}


if ($get_flow_stages->num_rows() > 0) {
    foreach ($get_flow_stages->result() AS $stage) {
        if (check_user_nav_access($stage->moduleid)) {
            $modules[] = $stage->moduleid;
            //$access[$stage->moduleid] = 'true';
        }
    }
}

$user_role = get_users_roles_matrix_id_arr();

if (count($user_role) > 0) {
    foreach ($user_role AS $role) {
        //LOOKUP ALL SPECIAL ACCESS MODULEID PER ROLEID
        $get_sp_access = $this->db->select('ts.moduleid')
            ->from('transaction_viewer_role_access as sp')
            ->join('prime_transaction_flow_main_stages as ts','sp.stageid = ts.sysid AND ts.flowid = 2','left')
            ->join('prime_module_navigations_main as nav','ts.moduleid = nav.sysid','left')
            ->where(array('sp.roleid' => $role,'sp.status' => 1))
            ->get();

        if ($get_sp_access->num_rows() > 0) {
            foreach ($get_sp_access->result() AS $stage) {
                $modules[] = $stage->moduleid;
            }
        }
    }
}

$modules[] = 0;

if (isset($otherdata)) {
    extract($otherdata);
}

$has_access = false;

if (in_array($module,$modules)) {
    $has_access = true;
}

$appdetails = get_application_details($dataid);
$rateclassid = (isset($appdetails->rateclassid)) ? $appdetails->rateclassid : false;
$netmetering = ($appdetails->info->netmetering);

if ($module == 36) {
    if (in_array(92,$trn)) {
    ?>
    <div class="row  margin-bottom-10">
        <div class="col-md-12">
            <form action="<?php echo base_url(); ?>inspection/saveinspectionreport" method="post" id="form_inspection_report">
                <input type="hidden" id="selected_inspection" name="surveyid">
                <div class="portlet  grey box">
                    <div class="portlet-title" style="position: relative">
                        <div class="caption">
                            <i class="fa fa-bolt"></i>
                            <span class="caption-subject font-red-flamingo bold uppercase"> Technical Site Survey Report</span>
                        </div>
                        <?php if ($has_access) { ?>
                        <div class="tools" id="inspection_tools">
                            <div class="btn-group">
                                <a href="#extract_excel_tssr" data-arr="<?php echo $dataid; ?>" data-toggle="ajax-modal" title="Load TSSR Data from File" class="btn btn-danger btn-sm inline"><i class="fa fa-download"></i> Load from file</a>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="portlet-body" id="tech_report_input">
                        <div class="tabbable-line pull-right" style="padding-top: 0px !important; padding-bottom: 15px !important;">
                            <ul class="nav nav-tabs ">
                                <li class="active">
                                    <a href="#tssr_va_reading" data-toggle="tab" aria-expanded="true" data-id="2"> Readings  </a>
                                </li>
                                <li class="">
                                    <a href="#tssr_info" data-toggle="tab" aria-expanded="true" data-id="1"> Details  </a>
                                </li>
                                <li class="">
                                    <a href="#tssr_measurements" data-toggle="tab" aria-expanded="true" data-id="3"> Measurements and Remarks </a>
                                </li>
                                <li class="">
                                    <a href="#tssr_other_info" data-toggle="tab" aria-expanded="true" data-id="4"> Additional Info </a>
                                </li>
                            </ul>

                        </div>
                        <div class="tab-content">
                            <div class="tab-pane fade in " id="tssr_info">
                                <ul class="list-group summary column no-border">
                                    <li class="list-group-item">
                                        <span class="col-md-4 label-name">Roof Orientation</span>
                                        <span class="col-md-8 label-default">
                                            <?php if ($has_access) { ?>
                                                <input class="form-control" placeholder="Roof Orientation..." name="rooforientation" id="rooforientation"/>
                                            <?php } else { ?>
                                                <span class="inspection" id="rooforientation" style="text-align: center !important; min-height:24px !important;"></span>
                                            <?php } ?>
                                        </span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="col-md-4 label-name">Kind of Roof</span>
                                        <span class="col-md-8 label-default">
                                            <?php if ($has_access) { ?>
                                                <input class="form-control" id="select2_rooftype" name="roofing" placeholder="Kind of Roof...">
                                            <?php } else { ?>
                                                <span class="inspection" id="rooftype" style="text-align: center !important; min-height:24px !important;"></span>
                                            <?php } ?>
                                        </span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="col-md-4 label-name">Roof Inclination</span>
                                        <span class="col-md-8 label-default">
                                            <?php if ($has_access) { ?>
                                                <input required="required" class="form-control" id="roof_inclination" name="roofinclination" value="" placeholder="Inclination.. " />
                                            <?php } else { ?>
                                                <span class="inspection" id="roofinclination" style="text-align: center !important; min-height:24px !important;"></span>
                                            <?php } ?>
                                        </span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="col-md-4 label-name">Voltage Drop Condition</span>
                                        <span class="col-md-8 label-default">
                                            <?php if ($has_access) { ?>
                                                <input class="form-control" id="vd_condition" name="vdcondition" value="" placeholder="Voltage Drop Condition.. " />
                                            <?php } else { ?>
                                                <span class="inspection" id="vdcondition" style="text-align: center !important; min-height:24px !important;"></span>
                                            <?php } ?>
                                        </span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="col-md-4 label-name">Generator Rating</span>
                                        <span class="col-md-8 label-default">
                                            <?php if ($has_access) { ?>
                                                <input name="gensetrate" id="genset_rate" class="form-control" placeholder="Generator Rating...">
                                            <?php } else { ?>
                                                <span class="inspection" id="gensetrate" style="text-align: center !important; min-height:24px !important;"></span>
                                            <?php } ?>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-pane fade in active" id="tssr_va_reading">
                                <?php if ($has_access) { ?>
                                <div class="form-group row">
                                    <div class="col-md-4 l1l2">
                                        <label class="control-label font-red-flamingo bold uppercase">L1-L2</label>
                                        <div class="form-control disabled">0</div>
                                        <input tabindex="1" class="form-control" placeholder="Volts.." name="l1l2" id="l1_l2" />
                                    </div>
                                    <div class="col-md-4 l1l3">
                                        <label class="control-label font-red-flamingo bold uppercase">L1-L3</label>
                                        <div class="form-control disabled">0</div>
                                        <input tabindex="2" class="form-control" placeholder="Volts.." name="l1l3" id="l1_l3" />
                                    </div>
                                    <div class="col-md-4 l2l3">
                                        <label class="control-label font-red-flamingo bold uppercase">L2-L3</label>
                                        <div class="form-control disabled">0</div>
                                        <input tabindex="3" class="form-control" placeholder="Volts.." name="l2l3" id="l2_l3" />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-4 l1g">
                                        <label class="control-label font-red-flamingo bold uppercase">L1-G</label>
                                        <div class="form-control disabled">0</div>
                                        <input tabindex="4" class="form-control" placeholder="Volts.." name="l1g"/>
                                    </div>
                                    <div class="col-md-4 l2g">
                                        <label class="control-label font-red-flamingo bold uppercase">L2-G</label>
                                        <div class="form-control disabled">0</div>
                                        <input tabindex="5" class="form-control" placeholder="Volts.." name="l2g"/>
                                    </div>
                                    <div class="col-md-4 l3g">
                                        <label class="control-label font-red-flamingo bold uppercase">L3-G</label>
                                        <div class="form-control disabled">0</div>
                                        <input tabindex="6" class="form-control" placeholder="Volts.." name="l3g"/>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-4 l1l2a">
                                        <label class="control-label font-red-flamingo bold ">L1-L2(A)</label>
                                        <div class="form-control disabled">0</div>
                                        <input tabindex="7" class="form-control" placeholder="Ampere.." name="l1l2a"/>
                                    </div>
                                    <div class="col-md-4 l1l3a">
                                        <label class="control-label font-red-flamingo bold ">L1-L3(A)</label>
                                        <div class="form-control disabled">0</div>
                                        <input tabindex="8" class="form-control" placeholder="Ampere.." name="l1l3a"/>
                                    </div>
                                    <div class="col-md-4 l2l3a">
                                        <label class="control-label font-red-flamingo bold ">L2-L3(A)</label>
                                        <div class="form-control disabled">0</div>
                                        <input tabindex="9" class="form-control" placeholder="Ampere.." name="l2l3a"/>
                                    </div>
                                </div>

                                <div class="well well-sm" style="margin-bottom: 5px;">
                                    <div class="form-group row" style="margin-bottom: 0px;">
                                        <div class="col-md-4">
                                            <span class="caption-subject font-red-flamingo bold uppercase">Grid Service</span>
                                            <input tabindex="14" required="required" class="form-control" id="rate_class_select" name="rateclass" value="<?php echo $rateclassid;?>" />
                                            <div class="form-control disabled">0</div>
                                        </div>
                                        <div class="col-md-4">
                                            <span class="caption-subject font-red-flamingo bold uppercase">Panel Type</span>
                                            <input tabindex="15" required="required" class="form-control" id="select2_panel_type" name="paneltype" value="" placeholder="Select panel.. " />
                                            <div class="form-control disabled">0</div>
                                        </div>
                                        <div class="col-md-4">
                                            <span class="caption-subject font-red-flamingo bold uppercase">Inspection Date</span>
                                            <input tabindex="17" name="inspectiondate" id="inspectiondate" class="form-control border-blue" placeholder="Date...">
                                            <div class="form-control disabled">0</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <span class="caption-subject font-red-flamingo bold uppercase">Remarks</span>
                                        <textarea class="form-control" rows="2" name="remarks" placeholder="Remarks..."></textarea>
                                        <div class="form-control disabled" style="height: 60px;">0</div>
                                    </div>
                                </div>
                                <?php } else { ?>

                                    <div class="row">
                                        <div class=" col-md-4">
                                            <ul class="list-group summary column">
                                                <li class="list-group-item">
                                                    <span class="col-md-6 label-name">L1 - L2</span>
                                                    <span class="col-md-6 bold inspection" id="l1l2" style="text-align: center !important; min-height:24px !important;"></span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="col-md-6 label-name">L1 - G</span>
                                                    <span class="col-md-6 bold inspection" id="l1g" style="text-align: center !important; min-height:24px !important;"></span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="col-md-6 label-name">L1 - L2(A)</span>
                                                    <span class="col-md-6 bold inspection" id="l1l2a" style="text-align: center !important; min-height:24px !important;"></span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class=" col-md-4">
                                            <ul class="list-group summary column">
                                                <li class="list-group-item">
                                                    <span class="col-md-6 label-name">L1 - L3</span>
                                                    <span class="col-md-6 bold inspection" id="l1l3" style="text-align: center !important; min-height:24px !important;"></span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="col-md-6 label-name">L2 - G</span>
                                                    <span class="col-md-6 bold" id="l2g" style="text-align: center !important; min-height:24px !important;"></span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="col-md-6 label-name">L1 - L3(A)</span>
                                                    <span class="col-md-6 bold inspection" id="l1l3a" style="text-align: center !important; min-height:24px !important;"></span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class=" col-md-4">
                                            <ul class="list-group summary column">
                                                <li class="list-group-item">
                                                    <span class="col-md-6 label-name">L2 - L3</span>
                                                    <span class="col-md-6 bold inspection" id="l2l3" style="text-align: center !important; min-height:24px !important;"></span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="col-md-6 label-name">L3 - G</span>
                                                    <span class="col-md-6 bold inspection" id="l3g" style="text-align: center !important; min-height:24px !important;"></span>
                                                </li>
                                                <li class="list-group-item">
                                                    <span class="col-md-6 label-name">L2 - L3(A)</span>
                                                    <span class="col-md-6 bold inspection" id="l2l3a" style="text-align: center !important; min-height:24px !important;"></span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="well well-sm" style="margin-bottom: 5px;">
                                        <div class="form-group row" style="margin-bottom: 0px;">
                                            <div class="col-md-4">
                                                <span class="col-md-12 caption-subject font-red-flamingo bold uppercase">Grid Service</span>
                                                <span class="col-md-12 bold inspection" id="gridservice" style="min-height:24px !important;"></span>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="col-md-12 caption-subject font-red-flamingo bold uppercase">Panel Type</span>
                                                <span class="col-md-12 bold inspection" id="panels" style="min-height:24px !important;"></span>
                                            </div>
                                            <div class="col-md-4">
                                                <span class="col-md-12 caption-subject font-red-flamingo bold uppercase">Inspection Date</span>
                                                <span class="col-md-12 bold inspection" id="inspectiondate" style="min-height:24px !important;"></span>
                                            </div>
                                        </div>
                                    </div>

                                <?php } ?>
                                <div class="row margin-bottom-10">
                                    <div class="col-md-12">
                                        <ul class="list-group summary column list-group-lg">
                                            <li class="list-group-item">
                                                <span class="col-md-6 label-name" style="padding: 10px 20px !important;">Number of Panel <span class="small">(s)</span></span>
                                                <span class="col-md-6 font-red-flamingo bold number" id="text_nop" style="padding: 10px 20px !important;"></span>
                                            </li>
                                            <li class="list-group-item">
                                                <span class="col-md-6 label-name" style="padding: 10px 20px !important;">Total Watt <span class="small">(s)</span></span>
                                                <span class="col-md-6 font-red-flamingo bold number" id="text_power" style="padding: 10px 20px !important;"></span>
                                            </li>
                                            <li class="list-group-item">
                                                <span class="col-md-6 label-name" style="padding: 10px 20px !important;">System Size <span class="small">Recommendation</span></span>
                                                <span class="col-md-6 font-red-flamingo bold number" id="text_system_size" style="padding: 10px 20px !important;"></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade in" id="tssr_measurements">
                                <?php
                                $surveydetails_qry = $this->db->select()
                                    ->from('prime_types_parameter')
                                    ->where(array('codes' => 'INSFILETYPES','sysid !=' => 3427,'status' => 1))
                                    ->get();

                                if ($surveydetails_qry->num_rows() > 0) {
                                    foreach ($surveydetails_qry->result() AS $surveydetail) {?>
                                        <div class="form-group row">
                                            <div class="col-md-12">
                                                <span class="caption-subject font-red-flamingo bold uppercase"><?php echo $surveydetail->names;?></span>
                                                <?php if ($has_access) { ?>
                                                <input class="form-control" name="surveydetail[<?php echo $surveydetail->sysid;?>][measurements]" placeholder="Measurements...">
                                                <textarea class="form-control" rows="2" name="surveydetail[<?php echo $surveydetail->sysid;?>][remarks]" placeholder="Remarks..."></textarea>
                                                <?php } else { ?>
                                                    <ul class="list-group summary column no-border">
                                                        <li class="list-group-item">
                                                            <span class="label-name col-md-3">Measurements</span>
                                                            <span class="label-default col-md-7 inspection" id="surveydetail[<?php echo $surveydetail->sysid;?>][measurements]"></span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <span class="label-name col-md-3">Remarks</span>
                                                            <span class="label-default col-md-7 inspection" id="surveydetail[<?php echo $surveydetail->sysid;?>][remarks]"></span>
                                                        </li>
                                                    </ul>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php }
                                }
                                ?>
                            </div>
                            <div class="tab-pane fade in" id="tssr_other_info">
                                <?php if ($has_access) { ?>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <span class="caption-subject font-red-flamingo bold uppercase">Roof Dimensions</span>
                                        <textarea class="form-control" rows="2" name="roofdimension" placeholder="Roof Dimensions..."></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <span class="caption-subject font-red-flamingo bold uppercase">Electrical/Structural Plans</span>
                                        <textarea class="form-control" rows="2" name="esplans" placeholder="Electrical/Structural Plans..."></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <span class="caption-subject font-red-flamingo bold uppercase">Normal Loads or for Clamping</span>
                                        <textarea class="form-control" rows="2" name="forclamping" placeholder="Normal Loads or for Clamping..."></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <span class="caption-subject font-red-flamingo bold uppercase">Meter # / Billing Details</span>
                                        <textarea class="form-control" rows="2" name="billingdetails" placeholder="Meter # / Billing Details..."></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <span class="caption-subject font-red-flamingo bold uppercase">Daytime Appliances</span>
                                        <textarea class="form-control" rows="2" name="dtappliances" placeholder="Daytime Appliances..."></textarea>
                                    </div>
                                </div>
                                <?php } else { ?>
                                    <ul class="list-group summary column">
                                        <li class="list-group-item">
                                            <span class="col-md-4 label-name">Roof Dimensions</span>
                                            <span class="col-md-8 bold inspection" id="roofdimension" style="text-align: center !important;"></span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="col-md-4 label-name">Electrical/Structural Plans</span>
                                            <span class="col-md-8 bold inspection" id="esplan" style="text-align: center !important;"></span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="col-md-4 label-name">Normal Loads or for Clamping</span>
                                            <span class="col-md-8 bold inspection" id="forclamping" style="text-align: center !important;"></span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="col-md-4 label-name">Meter # / Billing Details</span>
                                            <span class="col-md-8 bold inspection" id="billingdetails" style="text-align: center !important;"></span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="col-md-4 label-name">Daytime Appliances</span>
                                            <span class="col-md-8 bold inspection" id="dtappliances" style="text-align: center !important;"></span>
                                        </li>
                                    </ul>
                                <?php } ?>
                            </div>
                        </div>
                        <?php if ($has_access) { ?>
                        <div class="portlet-footer">
                            <button type="button" id="save_inspection_load" class="btn btn-info"><i class="fa fa-save"></i> Save</button>
                            <button type="button" id="publish_inspection_load" class="btn btn-success"><i class="fa fa-send"></i> Publish</button>
                            <button type="button" id="preview_inspection_load" class="btn btn-primary pull-right"><i class="fa fa-search"></i> Preview</button>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-red-flamingo bold uppercase"> Distribution Utility</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <form id="frm_du_update" method="post" action="<?php echo base_url(); ?>cad/updatedistutility">
                        <div class="form-group row">
                            <div class="col-md-<?php echo ($has_access || $netmetering) ? 5 : 8; ?>">
                                <?php if ($has_access) { ?>
                                    <label class="control-label bold uppercase">DU Name</label>
                                    <input class="form-control" id="select2_du" name="distutility" placeholder="Distribution Utility..." value="<?php echo $appdetails->info->duid; ?>">
                                <?php } else {
                                    if (isset($appdetails->info->duid) && $appdetails->info->duid > 0) {
                                        $dist_utility = get_dist_utility_list($appdetails->info->duid);
                                        $du_name = $dist_utility->fullname.' (<b>'.$dist_utility->name.'</b>)';
                                    } else {
                                        $du_name = 'Not set.';
                                    }

                                    ?>
                                    <span class="col-md-12 caption-subject bold uppercase">DU Name</span>
                                    <span class="col-md-12 " id="distutility" style="min-height:24px !important;"><?php echo $du_name; ?></span>
                                <?php } ?>
                            </div>
                            <div class="col-md-<?php echo ($has_access || $netmetering) ? 3 : 4; ?>">
                                <?php if ($has_access) { ?>
                                    <label class="control-label bold uppercase">DU Rate</label>
                                    <input type="number" step="any" class="form-control" id="durate" name="durate" placeholder="DU Rate..." value="<?php echo $appdetails->info->durate; ?>">
                                <?php } else { ?>
                                    <span class="col-md-12 caption-subject bold uppercase">DU Rate</span>
                                    <span class="col-md-12 " id="durate" style="min-height:24px !important;"><?php echo $appdetails->info->durate; ?></span>
                                <?php } ?>
                            </div>
                            <?php if ($has_access) { ?>
                                <div class="col-md-4">
                                    <label class="control-label bold uppercase">Bill</label>
                                    <input type="number" step="any" class="form-control" id="bill" name="bill" placeholder="Current Bill..." value="<?php echo $appdetails->info->bill; ?>" <?php echo (!$appdetails->info->netmetering) ? 'disabled' : ''; ?> required >
                                </div>
                            <?php } else { ?>
                                <?php if ($netmetering) { ?>
                                    <div class="col-md-4">
                                        <span class="col-md-12 caption-subject bold uppercase">Bill</span>
                                        <span class="col-md-12 " id="bill" style="min-height:24px !important;"><?php echo $appdetails->info->bill ?: 'N/A'; ?></span>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <?php if ($has_access) { ?>
                            <hr>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <input name="netmetering" id="net_metering" value="1" type="checkbox" data-checkbox="icheckbox_square-blue" class="icheck" data-target="monthly_use" <?php echo ($appdetails->info->netmetering) ? 'checked' : ''; ?> />
                                    <label class="control-label bold uppercase" for="net_metering">Net Metering</label>
                                </div>
                                <div class="col-md-5">
                                    <label class="control-label bold">Monthly Usage (KwH)</label>
                                    <input type="number" step="any" class="form-control" id="monthly_use" name="aveusage" placeholder="Average Monthly Consumption..." value="<?php echo ($appdetails->info->netmetering && $appdetails->info->aveusage > 0) ? $appdetails->info->aveusage : ''; ?>" <?php echo (!$appdetails->info->netmetering) ? 'disabled' : ''; ?> required >
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label bold">Gen. Charge</label>
                                    <input type="number" step="any" class="form-control" id="generation_charge" name="gencharge" placeholder="DU Generation Charge..." value="<?php echo ($appdetails->info->netmetering && $appdetails->info->gencharge > 0) ? $appdetails->info->gencharge : ''; ?>" <?php echo (!$appdetails->info->netmetering) ? 'disabled' : ''; ?> required >
                                </div>
                                <div class="col-md-4">
                                    <label class="control-label bold">Est. Production</label>
                                    <input type="number" step="any" class="form-control" id="monthlyprod" name="monthlyprod" placeholder="Estimated Monthly Production..." value="<?php echo ($appdetails->info->netmetering && $appdetails->info->monthlyprod > 0) ? $appdetails->info->monthlyprod : ''; ?>" <?php echo (!$appdetails->info->netmetering) ? 'disabled' : ''; ?> required >
                                </div>
                            </div>
                        <?php } else { ?>
                            <?php if ($appdetails->info->netmetering) { ?>
                                <hr>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label class="control-label bold uppercase" for="net_metering">Net Metering Details</label>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="control-label bold">Monthly Usage (KwH)</label>
                                        <span class="number" id="net_metering" style="display: block !important;"><?php echo ($appdetails->info->netmetering && $appdetails->info->aveusage > 0) ? $appdetails->info->aveusage : ''; ?></span>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label bold">Gen. Charge</label>
                                        <span class="block" id="generation_charge" style="display: block !important;"><?php echo ($appdetails->info->netmetering && $appdetails->info->gencharge > 0) ? $appdetails->info->gencharge : ''; ?></span>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="control-label bold">Est. Production</label>
                                        <span class="number" id="monthlyprod" style="display: block !important;"><?php echo ($appdetails->info->netmetering && $appdetails->info->monthlyprod > 0) ? $appdetails->info->monthlyprod : ''; ?></span>

                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <?php if ($has_access) { ?>
                            <div class="portlet-footer">
                                <button type="submit" class="btn btn-primary pull-right" style="padding-top: 10px"><i class="fa fa-save"></i> Save</button>
                            </div>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="portlet grey box margin-top-20">
                <div class="portlet-title" style="position: relative">
                    <div class="caption">
                        <i class="fa fa-users"></i>
                        <span class="caption-subject font-red-flamingo bold uppercase"> Inspection Team</span>
                        <span class="caption-helper">employee that executes the inspection</span>
                    </div>
                    <?php if ($has_access) { ?>
                    <div class="btn-group pull-right" style="margin-top: 5px;">
                        <a href="#frm_add_team_member" title="Add Team" modal-class="modal-sm" data-arr="<?php echo $dataid . ',' . $module; ?>" data-toggle="ajax-modal" class="btn btn-primary btn-sm inline"><i class="fa fa-plus"></i> Add</a>
                        <a href="javascript:" id="btn_refresh_team_list" class="btn btn-default btn-sm inline"><i class="fa fa-refresh"></i></a>
                    </div>
                    <?php } ?>
                </div>
                <div class="portlet-body ">
                    <table class="table table-striped table-hover" id="tbl_inspection_team" style="width: 100%">
                        <thead>
                        <th>Emp ID</th>
                        <th>Employee Name</th>
                        <th>Date Added</th>
                        <th><i class="fa fa-wrench"></i></th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php
    } else {
        echo '<h3>Application has not yet passed thru this stage.</h3>';
    }
}

if ($module == 208) {
    if (in_array(101,$trn)) {
    if ($has_access) {
        customer_application_installation_setup($dataid);
    } else {
        ?>
        <style type="text/css">
            .table tr#details td {
                background: transparent !important;
            }
        </style>

        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center bold" id="name_template"></h3>
            </div>
            <div class="col-md-12" id="sps_details">
                <div class="list-group summary column">
                    <div class="row" style="border: transparent !important; border-bottom: 1px solid #ddd !important;">
                        <div class="list-group-item col-md-4">
                            <span class="col-md-6 label-name text-primary bold" >Panel </span>
                            <span class="col-md-6 font-red-flamingo bold number" id="paneltype_viewing">N/A</span>
                        </div>
                        <div class="list-group-item col-md-4" >
                            <span class="col-md-6 label-name text-primary bold" ># of Panels </span>
                            <span class="col-md-6 font-red-flamingo bold number" id="nop_viewing">N/A</span>
                        </div>
                        <div class="list-group-item col-md-4" >
                            <span class="col-md-6 label-name text-primary bold" ># of Strings </span>
                            <span class="col-md-6 font-red-flamingo bold number" id="nos_viewing">N/A</span>
                        </div>
                    </div>
                    <div class="row" style="border: transparent !important; border-bottom: 1px solid #ddd !important;">
                        <div class="list-group-item  col-md-6">
                            <span class="col-md-6 label-name text-primary bold" >Panels / String </span>
                            <span class="col-md-6 font-red-flamingo bold number" id="panelsperstring_viewing">N/A</span>
                        </div>
                        <div class="list-group-item  col-md-6">
                            <span class="col-md-6 label-name text-primary bold" >Inverter Size(s) </span>
                            <span class="col-md-6 font-red-flamingo bold number" id="invertersize_viewing">N/A</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tabbable-line" id="recom_setup">
            <ul class="nav nav-tabs ">
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
                    <table class="table table-striped table-hover types table-condensed table-sm" id="tbl_sps_components">
                        <thead>
                        <th>#</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }
    } else {
        echo '<h3>Application has not yet passed thru this stage.</h3>';
    }
}

if ($module == 204) {
    if (in_array(97,$trn)) {
    $billing_qry = $this->db->select()
        ->from('customer_billing_group')
        ->where(array(
            'appid' => $dataid,
            'status' => 1
        ))->get()->row();

    $billing = array();
    if ($billing_qry) {
        $billing = $billing_qry;
    }

    ?>
    <div class="portlet light bordered">
        <div class="portlet-title">
            <div class="caption">
                <span class="caption-subject font-red-flamingo bold uppercase">Other Missing Details</span>
            </div>
        </div>
        <div class="portlet-body">
            <form id="frm_application_missing_details" action="<?php echo base_url()?>billing/savecontractdetails" method="post">
                <input type="hidden" value="<?php echo $dataid; ?>" name="appid">
                <table class="table table-hover table-striped table-bordered">
                    <tbody>
                    <tr style="margin: 15px !important;">
                        <td>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="control-label person-name bold"><i class="fa fa-sun-o"></i> Installation Date</span></label>
                                        <?php if ($has_access) { ?>
                                        <input type="date" class="form-control" name="installdate" value="<?php echo $billing->installdate ?? false; ?>" required>
                                        <?php } else {
                                            echo '<span class="col-md-12">'.(isset($billing->installdate) ? date('F j, Y',strtotime($billing->installdate)) : 'N/A').'</span>';
                                        } ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label person-name bold"><i class="fa fa-calendar"></i> Billing Frequency</span></label>
                                        <?php if ($has_access) { ?>
                                        <input class="form-control" id="select_billdate" name="billdate" value="<?php echo $billing->billfrequency ?? false; ?>" required>
                                        <?php } else {
                                            echo '<span class="col-md-12">'.(isset($billing->billfrequency) ? ' Every <b>'.ordinal($billing->billfrequency).'</b> of the Month</span>' : 'N/A');
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php if ($has_access) { ?>
                    <tr>
                        <td>
                            <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save</button>
                        </td>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

    <?php
    } else {
        echo '<h3>Application has not yet passed thru this stage.</h3>';
    }
}

if ($module == 0) {

    $docmodule = array(3433,3435,3434);

    $doctypes = array();

    $doc_stages = array(
        3433 => 94,
        3435 => 100,
        3434 => 97
    );

    foreach ($docmodule AS $docs) {
        $doctype_qry = $this->db->select('t.sysid,t.names,t.desc')
            ->from('prime_types_parameter as t')
            ->where('t.sysid',$docs)
            ->get()->row();

        if ($doctype_qry) {
            $doctypes[] = $doctype_qry;
        }
    }
    ?>
    <div class="tabbable-line" id="doc_preview_box">
        <ul class="nav nav-tabs " id="doc_preview_tabs">
            <li class="active">
                <a href="#doc_tssr" data-toggle="tab" aria-expanded="true" data-id="3436"> TSSR  </a>
            </li>
            <?php
            foreach ($doctypes as $dox) {
                if (in_array($doc_stages[$dox->sysid],$trn)) {
                    echo '<li class="">';
                    echo '<a href="#doc_' . strtolower($dox->names) . '" data-toggle="tab" aria-expanded="true" data-id="' . $dox->sysid . '"> ' . $dox->names . ' </a>';
                    echo '</li>';
                }
            }
            ?>
            <li class="">
                <a href="#doc_others" data-toggle="tab" aria-expanded="true" data-id="3436"> Other Documents </a>
            </li>
        </ul>
        <div class="tab-content" id="doc_preview_pane">
            <div class="tab-pane fade in active" id="doc_tssr">
                <div class="row">
                    <div class="btn-group col-md-12 pull-right">
                        <a href="javascript:" id="btn_open_preview" class="btn btn-primary btn-sm inline" data-type="3436"><i class="fa fa-search"></i> Open in Tab</a>
                    </div>
                </div>
                <!--<iframe id="iframe_tssr_preview" data-type="3436" src="" style="width:100%; height:500px;" frameborder="0"></iframe>-->
            </div>
            <?php
            $proposal_html = '';


            foreach ($doctypes as $dox) {
                $doc = $this->db->select('d.doctype,d.signed')
                    ->from('prime_documents_main as d')
                    ->where(array('dataid' => $dataid,'d.doctype' => $dox->sysid,'status' => 1))
                    ->get()->row();

                if (in_array($doc_stages[$dox->sysid],$trn)) {
                    echo '<div class="tab-pane fade in " id="doc_' . strtolower($dox->names) . '">';
                    echo '<div class="row">';
                    echo '<div class="btn-group">';

                    if ($has_access && $doc) {
                        if (!$doc->signed || is_null($doc->signed)) {
                            echo '<a href="javascript:" id="btn_sign_doc" class="btn btn-primary btn-sm inline" data-name="' . $dox->names . '" data-type="' . $dox->sysid . '"><i class="fa fa-pencil"></i> Sign</a>';
                        }
                    }
                    echo '<a href="javascript:;" id="btn_open_preview" class="btn btn-primary btn-sm inline" data-type="' . $dox->sysid . '"><i class="fa fa-search"></i> Open in Tab</a>';
                    echo '<a href="javascript:;" id="btn_reload_preview" class="btn btn-primary btn-sm inline pull-right" data-type="' . $dox->sysid . '"><i class="fa fa-refresh"></i> Refresh</a>';
                    echo '</div></div>';

                    echo '</div>';
                }
            }
            ?>
            <div class="tab-pane fade in" id="doc_others">
                <?php
                $viewing = (!in_array(92,$modules)) ? 'true' : 'false';
                ?>
                <table id="tbl_assessment_docs" class="table table-condensed table-bordered table-hover" width="100%" data-folder="<?php echo 'cad/applications/' . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/".get_stage_specific(92)->desc."/Docs/"; ?>" data-viewing="<?php echo $viewing; ?>" >
                    <thead>
                    <th>#</th>
                    <th width="100%">File</th>
                    <th><i class="fa fa-search"></i> </th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
}

if ($module == 201) {
    if (in_array(100,$trn)) { ?>
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-list-ul"></i>
                    <span class="caption-subject font-red-flamingo bold uppercase"> Customer Plan Details</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-archive"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">Documents Submitted</span>
                            <span class="caption-helper">
                                        <a href="javascript:" id="btn_reload_req" class="btn btn-default btn-xs inline text-align-right"><i class="fa fa-retweet"></i> Reload</a>
                                    </span>
                        </div>
                    </div>
                    <div class="portlet-body">

                        <table class="table table-hover table-striped table-condensed" width="100%" id="tbl_requirements_list">
                            <thead>
                            <th>#</th>
                            <th>Name</th>
                            <th>Complied</th>
                            <th><i class="fa fa-wrench"></i></th>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <form id="frm_application_plan_details" action="<?php echo base_url()?>cad/savecustomerplan" method="post">
                    <?php if ($appdetails->info->systemtype == 1) {?>
                        <input type="hidden" name="standard" value="1">
                    <?php }?>
                    <table class="table table-hover table-striped table-bordered">
                        <tbody>
                        <?php
                        $peso = '<span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span>';
                        $plan_values = array();
                        $customer_plan_details = $this->db->select()
                            ->from('customer_plan_details')
                            ->where(array('appid' => $dataid,'status !=' => 0))
                            ->get()->row();

                        //echo $this->db->last_query();

                        if ($customer_plan_details) {
                            $plan_values = (array)$customer_plan_details;
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
                            $plan_values = array_merge($plan_values,(array)$plan);
                            if ($plan && $plan->years != 0) {
                                $wifi = $customer_plan_details->wifiaccess ? 'Yes' : 'No';
                                $monthly = 0;

                                $pae_letter_head = FCPATH . 'assets/global/img/logo/peco-logo-login.png';
                                $tick = iconv('UTF-8', 'Windows-1250', '&#10003;');
                                $system_parts_qry = $this->db->select('csp.*,imd.fulldescription,u.unit_code as unit')
                                    ->from('customer_system_parts AS csp')
                                    ->join('items_main_description as imd', 'csp.itemid = imd.sysid and imd.status = 1', 'left')
                                    ->join('prime_unit as u', 'csp.unitid = u.sysid', 'left')
                                    ->where('imd.fulldescription REGEXP \'Inverter|Panel\'')
                                    ->where(array('appid' => $dataid, 'csp.status !=' => 0))->get();

                                $panels = '';
                                $inverter = array();
                                if ($system_parts_qry->num_rows() > 0) {
                                    foreach ($system_parts_qry->result() as $parts) {
                                        if (strpos($parts->fulldescription, 'Panel')) {
                                            $panels = $parts->qty;
                                        }

                                        if (strpos($parts->fulldescription, 'Inverter')) {
                                            $inverter[] = $parts->qty . 'x ' . $parts->fulldescription;
                                        }
                                    }
                                }

                                $appsize_qry = $this->db->select()
                                    ->from('application_customers_system_size')
                                    ->where(array('appid' => $dataid, 'status' => 305))
                                    ->get()->row();

                                if ($appsize_qry) {
                                    $size = $appsize_qry;
                                    $ratesize = (($size->rateclass == 1) ? 'Single' : $size->rateclass) . ' Phase';
                                }
                            }
                        }

                        ?>
                        <tr style="margin: 15px !important;">
                            <td>
                                <label class="col-md-3 control-label person-name" for="name"><i class="fa fa-wifi"></i> Wifi Access</span></label>

                                <div class="col-md-9">
                                    <div class="row">
                                        <?php if ($has_access) { ?>
                                            <div class="icheck-inline">
                                                <div class="col-md-6 text-align-center">
                                                    <label for="radio_yes" class="bold" style="width: 100%">
                                                        <input id="radio_yes" name="wifiaccess" data-radio="iradio_square-orange" class="icheck" value="1" <?php echo (isset($plan_values['wifiaccess']) && $plan_values['wifiaccess'] == 1) ? 'checked' : ''; ?> type="radio">
                                                        Yes
                                                    </label>
                                                </div>
                                                <div class="col-md-6 text-align-center">
                                                    <label for="radio_no" class="bold" style="width: 100%">
                                                        <input id="radio_no" name="wifiaccess" data-radio="iradio_square-orange" class="icheck" value="0" <?php echo (isset($plan_values['wifiaccess']) && $plan_values['wifiaccess'] != 1) ? 'checked' : ''; ?> type="radio">
                                                        No
                                                    </label>
                                                </div>
                                            </div>
                                        <?php } else {
                                            echo '<i class="fa fa-check text-success"></i> '.$wifi;
                                        } ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="col-md-3 bold">
                                    <i class="fa fa-clock-o"></i> Plan
                                </div>
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="icheck-inline">
                                            <?php
                                            if ($has_access) {
                                                if ($appdetails->info->systemtype == 1) {
                                                    $standard_rates_qry = $this->db->select()
                                                        ->from('customer_standard_system_rates')
                                                        ->where(array('systemsizeid' => $appdetails->info->systemsizeid,'status' => 1))
                                                        ->get();

                                                    $rates = array();
                                                    if ($standard_rates_qry->num_rows() > 0) {
                                                        foreach ($standard_rates_qry->result() as $plan) {
                                                            $years = ($plan->years == 0) ? 'Outright' : $plan->years.' Years';
                                                            ?>
                                                            <div class="col-md-3 text-align-center">
                                                                <label for="radio_<?php echo strtolower($plan->years);?>" class="bold" style="width: 100%">
                                                                    <input id="radio_<?php echo strtolower($plan->years);?>" name="installmentplan" data-radio="iradio_square-blue" class="icheck" value="<?php echo $plan->sysid;?>" <?php echo (isset($plan_values['rateid']) && $plan->sysid == $plan_values['rateid']) ? 'checked' : '';?> type="radio">
                                                                    <?php echo $years;?>
                                                                </label>
                                                            </div>
                                                            <?php
                                                        }
                                                    }
                                                } else {
                                                    //SELECTION INSTALLMENT YEARS AND INPUT AMOUNT

                                                    ?>
                                                    <div class="col-md-6">
                                                        <input name="installmentplan" id="select2_planduration" value="<?php echo $plan_values['years'] ?? '' ;?>" class="form-control" placeholder="Select plan duration...">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="number" name="planamount" id="planamount" value="<?php echo $plan_values['monthlyamt'] ?? '' ;?>" class="form-control" step="any" placeholder="Agreed amount...">
                                                    </div>
                                                    <?php
                                                }
                                            } else { ?>
                                                <ul class="list-group summary column">
                                                    <li class="list-group-item">
                                                        <span class="col-md-6 label-name"><?php echo isset($plan_values['years']) ? '<i class="fa fa-check text-success"></i> ' . ($plan_values['years'] > 0 ? $plan_values['years'] . ' Years' : 'Outright') : 'N/A'; ?></span>
                                                        <span class="col-md-6 bold inspection" id="l2l3a" style="text-align: center !important; min-height:24px !important;"> <?php echo $peso.(number_format($plan_values['monthlyamt'],2) ?? 'N/A'); ?> </span>
                                                    </li>
                                                </ul>
                                            <?php }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="col-md-3 bold">
                                    <i class="fa fa-pencil"></i> Remarks
                                </div>
                                <div class="col-md-9">
                                    <?php echo ($has_access) ? '<input class="form-control" name="remarks" id="ccremarks" value="'.($plan_values['remarks'] ?? '').'" >' : $plan_values['remarks'] ?? ''; ?>

                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="portlet-footer btn-group">
                        <button type="submit" class="btn btn-primary pull-right" id="btn_finalize_proposal"><i class="fa fa-save"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    <?php } else {
        echo '<h3>Application has not yet passed thru this stage.</h3>';
    }
}

if ($module == 203) {
    if (in_array(94,$trn)) { ?>
            <div class="row  margin-bottom-10">
                <div class="col-md-12">
                    <div class="portlet grey box">
                        <div class="portlet-title">
                            <div class="caption">
                                <span class="caption-subject font-red-flamingo bold uppercase"> Assigned Sales Officer</span>
                            </div>
                            <div class="tools">
                                <?php if (array_intersect(array(1,50,48),$user_role)) { ?>
                                    <div id="so_tools" class="btn-group">

                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div id="application_sales_officer">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet grey box">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-red-flamingo bold uppercase"><i class="fa fa-line-chart"></i> System Rates <?php echo ($appdetails->info->systemtype == 1) ? '<small>(Standard)</small>' : ''; ?></span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <form id="frm_rate_update" method="post" action="<?php echo base_url(); ?>cad/saveproposedsystemrates">

                            <?php if ($appdetails->info->systemtype == 2) {

                                $amount_lookup = $this->db->select('sg.appid,sg.desc as sizename,p.outright,p.twoyrs,p.threeyrs,p.fiveyrs,p.tenyrs,p.monthlyave,p.summerave,p.buildtime')
                                    ->from('customer_system_group AS sg')
                                    ->join('proposal_nonstandard_system_rates AS p','sg.sysid = p.systemsizeid AND p.`status` = 1','left')
                                    ->where(array('sg.appid' => $dataid,'sg.status' => 1))
                                    ->get()->row();

                                if ($amount_lookup) {
                                    extract((array)$amount_lookup);
                                }

                                ?>
                                <div class="row margin-bottom-5 " id="nonstandardsize">
                                    <input type="hidden" name="systemsizeid" value="<?php echo $appdetails->info->systemsizeid; ?>">
                                    <div class="col-md-12 margin-top-10">
                                        <table style="width: 100%;" id="tbl_sysrates" class="zui-table table table-hover table-striped table-bordered" >
                                            <thead>
                                            <th>Outright</th>
                                            <th>2 Years</th>
                                            <th>3 Years</th>
                                            <th>5 Years</th>
                                            <th>10 Years</th>
                                            <th>Monthly Average</th>
                                            <th>Summer Average</th>
                                            <th>Build Time</th>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td class="number">
                                                    <?php echo ($has_access) ? dt_inline_input('outright','number',($outright) ?? false,array('step' => '.01','disabled' => false,'required' => true),'text-align-right',array('width' => '100%','height' => '34px')) : ($outright) ?? false; ?>
                                                </td>
                                                <td class="number">
                                                    <?php echo ($has_access) ? dt_inline_input('twoyrs','number',($twoyrs) ?? false,array('step' => '.01','disabled' => false,'required' => true),'text-align-right',array('width' => '100%','height' => '34px')) : ($twoyrs) ?? false; ?>
                                                </td>
                                                <td class="number">
                                                    <?php echo ($has_access) ? dt_inline_input('threeyrs','number',($threeyrs) ?? false,array('step' => '.01','disabled' => false,'required' => true),'text-align-right',array('width' => '100%','height' => '34px')) : ($twoyrs) ?? false; ?>
                                                </td>
                                                <td class="number">
                                                    <?php echo ($has_access) ? dt_inline_input('fiveyrs','number',($fiveyrs) ?? false,array('step' => '.01','disabled' => false,'required' => true),'text-align-right',array('width' => '100%','height' => '34px')) : ($fiveyrs) ?? false; ?>
                                                </td>
                                                <td class="number">
                                                    <?php echo ($has_access) ? dt_inline_input('tenyrs','number',($tenyrs) ?? false,array('step' => '.01','disabled' => false,'required' => true),'text-align-right',array('width' => '100%','height' => '34px')) : ($tenyrs) ?? false; ?>
                                                </td>
                                                <td class="number">
                                                    <?php echo ($has_access) ? dt_inline_input('monthlyave','number',($monthlyave) ?? false,array('step' => '.01','disabled' => false,'required' => true),'text-align-right',array('width' => '100%','height' => '34px')) : ($monthlyave) ?? false; ?>
                                                </td>
                                                <td class="number">
                                                    <?php echo ($has_access) ? dt_inline_input('summerave','number',($summerave) ?? false,array('step' => '.01','disabled' => false,'required' => true),'text-align-right',array('width' => '100%','height' => '34px')) : ($summerave) ?? false; ?>
                                                </td>
                                                <td class="number">
                                                    <?php echo ($has_access) ? dt_inline_input('buildtime','number',($buildtime) ?? false,array('step' => '1','disabled' => false),'text-align-right',array('width' => '100%','height' => '34px')) : ($buildtime) ?? false; ?>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php } else {
                                //lookup standard system rates
                                $proposal_rates_qry = $this->db->select()
                                    ->from('proposal_standard_system_rates')
                                    ->where(array('systemsizeid' => $appdetails->info->systemsizeid,'status' => 1))
                                    ->get()->row();

                                $rates = array();
                                if ($proposal_rates_qry) {
                                    $proposed = $proposal_rates_qry;
                                    ?>
                                    <div class="row margin-bottom-5 " id="nonstandardsize">
                                        <div class="col-md-12 margin-top-10">
                                            <table style="width: 100%;" id="tbl_sysrates" class="zui-table table table-hover table-striped table-bordered" >
                                                <thead>
                                                <th>Outright</th>
                                                <th>2 Years</th>
                                                <th>5 Years</th>
                                                <th>10 Years</th>
                                                <th>Monthly Average</th>
                                                <th>Summer Average</th>
                                                <th>Build Time</th>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td class="number">
                                                        <?php echo ($proposed->outright) ?? 0.00 ?>
                                                    </td>
                                                    <td class="number">
                                                        <?php echo ($proposed->twoyrs) ?? 0.00 ?>
                                                    </td>
                                                    <td class="number">
                                                        <?php echo ($proposed->fiveyrs) ?? 0.00 ?>
                                                    </td>
                                                    <td class="number">
                                                        <?php echo ($proposed->tenyrs) ?? 0.00 ?>
                                                    </td>
                                                    <td class="number">
                                                        <?php echo ($proposed->monthlyave) ?? 0.00 ?>
                                                    </td>
                                                    <td class="number">
                                                        <?php echo ($proposed->summerave) ?? 0.00 ?>
                                                    </td>
                                                    <td class="number">
                                                        <?php echo ($proposed->buildtime) ?? 0 ?>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            if ($has_access && $appdetails->info->systemtype == 2) {
                                ?>
                                <div class="portlet-footer">
                                    <button type="submit" class="btn btn-primary pull-right" style="padding-top: 10px"><i class="fa fa-save"></i> Save</button>
                                </div>
                            <?php } ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php } else {
        echo '<h3>Application has not yet passed thru this stage.</h3>';
    }
}