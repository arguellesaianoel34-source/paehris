<?php
$appdetails = application_info($dataid);
$rateclassid = (isset($appdetails->rateclassid)) ? $appdetails->rateclassid : false;
$basic_submit = true;
$account_submit = true;

$system_size = $this->db->select()
    ->from('application_customers_system_size')
    ->where('sysid',$dataid)
    ->get()->row();

if ($system_size) {
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

//GET TEMP DETAILS
//IF APPDETAILS IS FALSE, GET TEMP DETAILS


//Hide submit if all details are present
if ($appdetails->duid && $appdetails->durate && $paneltype && $nop && $appdetails->systemtype && $appdetails->systemsizeid) {
    $basic_submit = false;
}
?>
<div class="row tab-pane fade in">
    <div class="col-md-6">
        <?php /*if ($info->apptype > 1) {
        //$data['ids'] = array($dataid,false);
        //$data['dataid'] = $dataid;
        //$this->load->view('admin/ajax/frm_cad_update_owner_info', $data, FALSE);

        $qry_corp_app = $this->db->select()
            ->from('application_customers_corporation')
            ->where(array('appid' => $dataid, 'types' => $info->apptype))
            ->get()->row();

        if($qry_corp_app) {
            //$info = array();
            if($info->apptype == 2) {
                $non_res = get_corporation_info($qry_corp_app->corpid);
                $pic_dir = 'corporation';
            } else {
                $non_res = get_government_info($qry_corp_app->corpid);
                $pic_dir = 'government';
            }
            $pic_id = $qry_corp_app->corpid;
            if ($non_res->qry) {
                $corpname = $non_res->info->descs;


                if($info->apptype == 2) {
                    $qry_branch = $this->db->select()
                        ->from('corporation_branches')
                        ->where(array('corpid' => $qry_corp_app->corpid, 'sysid' => $qry_corp_app->branchid))
                        ->get()->row();
                    if ($qry_branch) {
                        $corpbranch = $qry_branch->names;
                    }
                }else{
                    $corpbranch = ($non_res) ? $non_res->info->names : '';
                }
            }
        }
        ?>
        <div class="portlet light bordered">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="fa fa-user-circle"></i> Customer Basic Information
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#customer_corp_info" data-toggle="tab" aria-expanded="true"> Customer Info  </a>
                    </li>
                    <?php if ($info->personid > 0) { ?>
                        <li class="">
                            <a href="#customer_rep_info" data-toggle="tab" aria-expanded="true"> Authorized Representative  </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <div class="portlet-body tab-content">
                <div class="tab-pane fade in active" id="customer_corp_info">

                    <table class="table table-hover table-striped">
                        <tbody>
                        <tr>
                            <td>
                                <div class="form-group margin-top-10" id="non_res_details">
                                    <label class="col-md-2 control-label">Establishment <span class="required"></span></label>
                                    <div class="col-md-6">
                                        <input type="hidden" name="corpid" value="<?php echo ($qry_corp_app) ? $qry_corp_app->corpid : ''; ?>">
                                        <input name="corpname" type="text" class="form-control data-entry input-lg" id="corpname" placeholder="Establishment name..." data-toggle="autocomplete" col-name="corpname" value="<?php echo ($qry_corp_app) ? $corpname : ''; ?>">
                                        <div class="form-control-focus"> </div>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="hidden" name="branchid" value="<?php echo ($qry_corp_app && $qry_branch) ? $qry_corp_app->branchid : ''; ?>">
                                        <input name="corpbranch" type="text" class="form-control data-entry input-lg" id="corpbranch" placeholder="Branch" data-toggle="autocomplete" col-name="corpbranch" value="<?php echo ($qry_corp_app && $qry_branch) ? $corpbranch : ''; ?>">
                                        <div class="form-control-focus"> </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group ">
                                    <label class="control-label col-md-2">Contacts <span class="required"></span>
                                    </label>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control data-entry" id="phone" name="phone" placeholder="Ex: 3290002" value="<?php echo $info->contactphone;?>"/>
                                        <span class="help-block">Phone Number </span>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control data-entry" id="mobile" name="mobile" placeholder="Ex: 09179999988" value="<?php echo $info->contactmobile;?>"/>
                                        <span class="help-block">Mobile Number </span>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="email" class="form-control data-entry" id="email" name="email" placeholder="Ex: yourname@email.com" value="<?php echo $info->contactemail;?>"/>
                                        <span class="help-block">E-Mail Address </span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label class="control-label col-md-2">Address <span class="required"></span>
                                    </label>
                                    <div class="col-md-5">
                                        <input id="select2_country" class="form-control" name="country" value="<?php echo $info->country;?>" />
                                        <span class="help-block">Country</span>
                                    </div>
                                    <div class="col-md-5">
                                        <input id="select2_region" class="form-control" name="region"  placeholder="Select region.." value="<?php echo $info->region;?>"/>
                                        <span class="help-block">Region</span>
                                    </div>
                                    <div class="col-md-5 col-md-offset-2">
                                        <input id="select2_province" class="form-control" name="province" placeholder="Select province.." value="<?php echo $info->province;?>"/>
                                        <span class="help-block">Province</span>
                                    </div>
                                    <div class="col-md-5">
                                        <input id="select2_citymun" class="form-control" name="city"  placeholder="Select Municipal / City.." value="<?php echo $info->city;?>"/>
                                        <span class="help-block">Municipal / City</span>
                                    </div>
                                    <div class="col-md-10 col-md-offset-2 margin-top-10">
                                        <textarea class="form-control" rows="2" id="addrspecific" name="addrspecific" placeholder="Ex: Blk9 Lot20, DECA Homes Subd., Red Gate, Near Security Guard Outpost"><?php echo $info->addrspec;?></textarea>
                                        <span class="help-block">Provide specific street address, blk, house number and landmark.</span>
                                    </div>
                                    <label class="control-label col-md-2">Google Map Location</span>
                                    </label>
                                    <div class="col-md-10">
                                        <div class="input-icon">
                                            <i class="fa fa-map-marker"></i>
                                            <input class="form-control" rows="3" id="addrgmap" name="googlemap" value="<?php echo $info->geolink; ?>" placeholder="Paste Google Map here!"/>
                                        </div>
                                        <span class="help-block">Ex: https://www.google.com/maps/@10.8459772,122.6544582,11.75z</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade in " id="customer_rep_info">
                    <h1>Person Info</h1>
                </div>
            </div>
        </div>

        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-history"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">Owners and Authorized Representatives</span>
                </div>
            </div>
            <div class="portlet-body">
                <table class="table table-bordered table-condensed table-sm">
                    <thead>
                    <th>#</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th><i class="fa fa-sliders bold"></i> </th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>

        <script src="<?php echo base_url(); ?>assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/jquery.validate.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/additional-methods.min.js"></script>

        <script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js"></script>
        <script src="<?php echo base_url(); ?>assets/global/scripts/address.js"></script>
        <script type="text/javascript">
            $(document).find('select').each(function () {
                $(this).select2();
            });
            CAD.application();
            ADDRESS.init(<?php echo $info->country; ?>,<?php echo $info->region; ?>,<?php echo $info->province; ?>,<?php echo $info->city; ?>);
        </script>
    <?php } else {
        customer_application_editinfo($dataid, true, true);
    } */?>
        <?php
        customer_application_basicinfo($dataid, false, false);
        ?>
        <div class="portlet light bordered margin-top-10">
            <div class="portlet-title">
                <div class="caption">
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
                    <div class="form-group row">
                        <div class="col-md-8">
                            <label class="control-label bold uppercase">DU Name</label>
                            <?php if ($appdetails->duid) {
                                $du = get_dist_utility_list($appdetails->duid);
                                echo '<span class="form-control">'.$du->fullname.'</span>';
                            } else {?>
                                <input class="form-control" id="select2_du" name="distutility" placeholder="Distribution Utility..." value="">
                            <?php } ?>
                        </div>
                        <div class="col-md-4">
                            <label class="control-label bold uppercase">DU Rate</label>
                            <?php if ($appdetails->durate) {
                                echo '<span class="form-control">'.number_format($appdetails->durate,2).'</span>';
                            } else {?>
                                <input type="number" step="any" class="form-control" id="durate" name="durate" placeholder="DU Rate..." value="">
                            <?php } ?>
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
                                <input class="form-control" id="select2_panel_type" name="paneltype" placeholder="Panel Type..." value="">
                            <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="control-label bold uppercase"># Panels</label>
                            <div id="detail_nop">
                            <?php if ($nop) {
                                echo '<span class="form-control">'.$nop.'</span>';
                            } else {?>
                                <input type="number" step="1" class="form-control" id="nop" name="nop" placeholder="Number of Panels..." value="">
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
                                                <input id="radio_standard" name="systemtype" data-target="standard" data-radio="iradio_square-orange" class="icheck" value="1" type="radio"> Standard
                                            </label>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="radio_nonstandard" class="bold" style="width: 100%">
                                                <input id="radio_nonstandard" name="systemtype" data-target="nonstandard" data-radio="iradio_square-orange" class="icheck" value="2" type="radio"> Non-Standard
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
                            } else {?>
                                <input class="form-control" id="systemsize" name="systemsize" placeholder="System Size..." value="">
                            <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php if ($basic_submit) { ?>
                    <div class="portlet-footer">
                        <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save</button>
                    </div>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="portlet light bordered margin-top-10">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-link"></i>
                    <span class="caption-subject font-red-flamingo bold uppercase">Account Info HERE!!!</span>
                </div>
                <div class="tools">
                    <button class="btn btn-primary btn-outline btn-xs">Create Account!</button>
                </div>
            </div>
            <div class="portlet-body">
                <h1>PUT ACCOUNT INFO HERE!</h1>
                Account:
                <ol>
                    <li>PAE #</li>
                    <li>Installment Plan</li>
                    <li>Agreed Amount</li>
                    <li>Start of Payment</li>
                    <li>Frequency</li>
                    <li>Online Monitoring</li>
                    <li>Date of Installation</li>
                </ol>
            </div>
        </div>
        <div class="portlet light bordered margin-top-10">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-file-pdf-o"></i>
                    <span class="caption-subject font-red-flamingo bold uppercase">Docs Preview goes here!!!</span>
                </div>
            </div>
            <div class="portlet-body">
                <h1>PUT Uploaded documents HERE!</h1>
                Docs:
                <ol>
                    <li>TSSR</li>
                    <li>Proposal (Optional)</li>
                    <li>CCA (If Any)</li>
                    <li>Contract</li>
                    <li>Acknowledgement Form</li>
                    <li>Docs Submitted</li>
                </ol>
            </div>
        </div>
        <!--<div class="row margin-bottom-10" id="form_inspection_report">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title" style="position: relative">
                        <div class="caption">
                            <i class="fa fa-bolt"></i>
                            <span class="caption-subject font-red-flamingo bold uppercase">Site Survey</span>
                        </div>
                        <-- If Inspection details exist: Hide ->
                        <div class="tools" id="inspection_tools">
                            <div class="btn-group">
                                <a href="#extract_excel_tssr" data-arr="<?php echo $dataid; ?>" data-toggle="ajax-modal" title="Load TSSR Data from File" class="btn btn-danger btn-sm inline"><i class="fa fa-download"></i> Load from file</a>
                            </div>
                        </div>
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
                            <div class="tab-pane fade in active" id="tssr_va_reading">
                                <div class="row">
                                    <div class=" col-md-4">
                                        <ul class="list-group summary column">
                                            <li class="list-group-item">
                                                <span class="col-md-6 label-name font-red-flamingo bold uppercase">L1 - L2</span>
                                                <span class="col-md-6 bold inspection" id="l1l2" style="text-align: center !important; min-height:24px !important;"></span>
                                            </li>
                                            <li class="list-group-item">
                                                <span class="col-md-6 label-name font-red-flamingo bold uppercase">L1 - G</span>
                                                <span class="col-md-6 bold inspection" id="l1g" style="text-align: center !important; min-height:24px !important;"></span>
                                            </li>
                                            <li class="list-group-item">
                                                <span class="col-md-6 label-name font-red-flamingo bold uppercase">L1 - L2(A)</span>
                                                <span class="col-md-6 bold inspection" id="l1l2a" style="text-align: center !important; min-height:24px !important;"></span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class=" col-md-4">
                                        <ul class="list-group summary column">
                                            <li class="list-group-item">
                                                <span class="col-md-6 label-name font-red-flamingo bold uppercase">L1 - L3</span>
                                                <span class="col-md-6 bold inspection" id="l1l3" style="text-align: center !important; min-height:24px !important;"></span>
                                            </li>
                                            <li class="list-group-item">
                                                <span class="col-md-6 label-name font-red-flamingo bold uppercase">L2 - G</span>
                                                <span class="col-md-6 bold inspection" id="l2g" style="text-align: center !important; min-height:24px !important;"></span>
                                            </li>
                                            <li class="list-group-item">
                                                <span class="col-md-6 label-name font-red-flamingo bold uppercase">L1 - L3(A)</span>
                                                <span class="col-md-6 bold inspection" id="l1l3a" style="text-align: center !important; min-height:24px !important;"></span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class=" col-md-4">
                                        <ul class="list-group summary column">
                                            <li class="list-group-item">
                                                <span class="col-md-6 label-name font-red-flamingo bold uppercase">L2 - L3</span>
                                                <span class="col-md-6 bold inspection" id="l2l3" style="text-align: center !important; min-height:24px !important;"></span>
                                            </li>
                                            <li class="list-group-item">
                                                <span class="col-md-6 label-name font-red-flamingo bold uppercase">L3 - G</span>
                                                <span class="col-md-6 inspection" id="l3g" style="text-align: center !important; min-height:24px !important;"></span>
                                            </li>
                                            <li class="list-group-item">
                                                <span class="col-md-6 label-name font-red-flamingo bold uppercase">L2 - L3(A)</span>
                                                <span class="col-md-6 bold inspection" id="l2l3a" style="text-align: center !important; min-height:24px !important;"></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>


                                <div class="well well-sm" style="margin-bottom: 5px;">
                                    <div class="form-group row" style="margin-bottom: 0px;">
                                        <div class="col-md-4">
                                            <span class="caption-subject font-red-flamingo bold uppercase">Grid Service</span>
                                            <span class="form-control disabled inspection" id="gridservice">0</span>
                                        </div>
                                        <div class="col-md-4">
                                            <span class="caption-subject font-red-flamingo bold uppercase">Panel Type</span>
                                            <span class="form-control disabled inspection" id="panels">0</span>
                                        </div>
                                        <div class="col-md-4">
                                            <span class="caption-subject font-red-flamingo bold uppercase">Inspection Date</span>
                                            <span class="form-control disabled inspection" id="inspectiondate">0</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-12">
                                    <ul class="list-group summary column">
                                        <li class="list-group-item">
                                            <span class="col-md-3 label-name font-red-flamingo bold uppercase">Remarks</span>
                                            <span class="col-md-9 bold inspection" id="remarks" style="text-align: center !important; min-height:24px !important;"></span>
                                        </li>
                                    </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade in " id="tssr_info">
                                <ul class="list-group summary column no-border">
                                    <li class="list-group-item">
                                        <span class="col-md-4 label-name">Roof Orientation</span>
                                        <span class="col-md-8 label-default inspection" id="rooforientation"></span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="col-md-4 label-name">Kind of Roof</span>
                                        <span class="col-md-8 label-default inspection" id="rooftype"></span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="col-md-4 label-name">Roof Inclination</span>
                                        <span class="col-md-8 label-default inspection" id="roofinclination"></span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="col-md-4 label-name">Voltage Drop Condition</span>
                                        <span class="col-md-8 label-default inspection" id="vdcondition"></span>
                                    </li>
                                    <li class="list-group-item">
                                        <span class="col-md-4 label-name">Generator Rating</span>
                                        <span class="col-md-8 label-default inspection" id="gensetrate"></span>
                                    </li>
                                </ul>
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
                                                <ul class="list-group summary column">
                                                    <li class="list-group-item">
                                                        <span class="col-md-4 label-name">Measurements</span>
                                                        <span class="col-md-8 label-default inspection" id="surveydetail[<?php echo $surveydetail->sysid;?>][measurements]" style="text-align: center !important; min-height:24px !important;"></span>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <span class="col-md-4 label-name">Remarks</span>
                                                        <span class="col-md-8 label-default inspection" id="surveydetail[<?php echo $surveydetail->sysid;?>][remarks]" style="text-align: center !important; min-height:24px !important;"></span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php }
                                }
                                ?>
                            </div>
                            <div class="tab-pane fade in" id="tssr_other_info">
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <span class="caption-subject font-red-flamingo bold uppercase">Roof Dimensions</span>
                                        <span class="form-control inspection" rows="2" id="roofdimension" placeholder="Roof Dimensions..."></span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <span class="caption-subject font-red-flamingo bold uppercase">Electrical/Structural Plans</span>
                                        <span class="form-control inspection" rows="2" id="esplans" placeholder="Electrical/Structural Plans..."></span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <span class="caption-subject font-red-flamingo bold uppercase">Normal Loads or for Clamping</span>
                                        <span class="form-control inspection" rows="2" id="forclamping" placeholder="Normal Loads or for Clamping..."></span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <span class="caption-subject font-red-flamingo bold uppercase">Meter # / Billing Details</span>
                                        <span class="form-control inspection" rows="2" id="billingdetails" placeholder="Meter # / Billing Details..."></span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <span class="caption-subject font-red-flamingo bold uppercase">Daytime Appliances</span>
                                        <span class="form-control inspection" rows="2" name="dtappliances" placeholder="Daytime Appliances..."></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>-->
    </div>
    <div class="row">
        <div class="col-md-12">
            <button id="btn_process_customer" class="btn btn-primary btn-lg center-block"><i class="fa fa-refresh"></i> Process customer!</button>
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