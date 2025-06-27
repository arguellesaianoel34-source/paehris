<?php
$trnid = $this->uri->segment(4);
$qry_trn = $this->db->select()->from('transaction_request_main_trails')->where('sysid', $trnid)->get()->row();
$qry_stg = $this->db->select()->from('prime_transaction_flow_main_stages')->where(array('sysid' => $qry_trn->stageid))->get()->row();
$flowid = $qry_stg->flowid;

$doctype = array(
    92 => 3436,
    93 => 3433,
    100 => 3435,
);

$user_role = get_users_roles_matrix_id_arr();
?>
<!-- DATEPICKER CSS START!-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/datepicker3.css">
<!-- DATEPICKER CSS END!-->
<script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/sales/main.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/inspection/main.js" type="text/javascript"></script>
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
</style>

<div class="row tab-pane fade in">
    <?php if ($qry_stg->desc == 'Pending Agreement') { ?>
        <div class="col-md-7">
            <?php customer_application_editinfo($dataid,true,true); ?>

        </div>
        <div class="col-md-5">
            <?php echo customer_application_requirements_list($dataid); ?>
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
        </div>

        <script type="text/javascript">
            CAD.profile(<?php echo $dataid; ?>, <?php echo $flowid;?>);
        </script>
    <?php } else { ?>
        <div class="col-md-6">
            <?php
            $appdetails = get_application_details($dataid);
            $rateclassid = (isset($appdetails->rateclassid)) ? $appdetails->rateclassid : false;
            customer_application_basicinfo($dataid, true, false);
            ?>
            <?php
            //echo '<h1>'.$qry_stg->desc.'</h1>';
            if ($qry_stg->desc == 'Proposal') {
                ?>
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-red-flamingo bold uppercase"> Distribution Utility</span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <form id="frm_du_update" method="post" action="<?php echo base_url(); ?>cad/updatedistutility">
                            <div class="form-group row">
                                <div class="col-md-8">
                                    <label class="control-label bold uppercase">DU Name</label>
                                    <input class="form-control" id="select2_du" name="distutility" placeholder="Distribution Utility..." value="<?php echo $appdetails->info->duid; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="control-label bold uppercase">DU Rate</label>
                                    <input type="number" step="any" class="form-control" id="durate" name="durate" placeholder="DU Rate..." value="<?php echo $appdetails->info->durate; ?>">
                                </div>
                            </div>
                            <?php if ($appdetails->info->systemtype == 2) {

                                $amount_lookup = $this->db->select('sg.appid,sg.desc as sizename,p.outright,p.twoyrs,p.fiveyrs,p.tenyrs,p.monthlyave,p.summerave,p.buildtime')
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
                                            <th>Two Years</th>
                                            <th>Five Years</th>
                                            <th>Ten Years</th>
                                            <th>Monthly Average</th>
                                            <th>Summer Average</th>
                                            <th>Build Time</th>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <?php echo dt_inline_input('outright','number',$outright ?? false,array('step' => '.01','disabled' => false,'required' => true),'text-align-right',array('width' => '100%','height' => '34px')); ?>
                                                </td>
                                                <td>
                                                    <?php echo dt_inline_input('twoyrs','number',$twoyrs ?? false,array('step' => '.01','disabled' => false,'required' => true),'text-align-right',array('width' => '100%','height' => '34px')); ?>
                                                </td>
                                                <td>
                                                    <?php echo dt_inline_input('fiveyrs','number',$fiveyrs ?? false,array('step' => '.01','disabled' => false,'required' => true),'text-align-right',array('width' => '100%','height' => '34px')); ?>
                                                </td>
                                                <td>
                                                    <?php echo dt_inline_input('tenyrs','number',$tenyrs ?? false,array('step' => '.01','disabled' => false,'required' => true),'text-align-right',array('width' => '100%','height' => '34px')); ?>
                                                </td>
                                                <td>
                                                    <?php echo dt_inline_input('monthlyave','number',$monthlyave ?? false,array('step' => '.01','disabled' => false,'required' => true),'text-align-right',array('width' => '100%','height' => '34px')); ?>
                                                </td>
                                                <td>
                                                    <?php echo dt_inline_input('summerave','number',$summerave ?? false,array('step' => '.01','disabled' => false,'required' => true),'text-align-right',array('width' => '100%','height' => '34px')); ?>
                                                </td>
                                                <td>
                                                    <?php echo dt_inline_input('buildtime','number',$buildtime ?? false,array('step' => '1','disabled' => false),'text-align-right',array('width' => '100%','height' => '34px')); ?>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="portlet-footer">
                                <button type="submit" class="btn btn-primary pull-right" style="padding-top: 10px"><i class="fa fa-save"></i> Save</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php
            }

            if ($qry_stg->desc == 'Credit Check Info') {
                ?>
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
                                <div class="col-md-6 pull-left" style="margin-left: -15px;">
                                    <a class="btn btn-default inline" id='btn_add_app_req' data-toggle="ajax-modal" href="#frm_cad_add_requirements" title="Add Application Requirements" data-arr="<?php echo $dataid;?>"><i class="fa fa-plus"></i> Add Documents</a>
                                </div>
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
                                        $wifi = $customer_plan_details->wifiaccess ? 1 : 0;
                                        $monthly = 0;

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
                                                <div class="icheck-inline">
                                                    <div class="col-md-6 text-align-center">
                                                        <label for="radio_yes" class="bold" style="width: 100%">
                                                            <input id="radio_yes" name="wifiaccess" data-radio="iradio_square-orange" class="icheck" value="1" <?php echo (isset($wifi) && $wifi == 1) ? 'checked' : ''; ?> type="radio">
                                                            Yes
                                                        </label>
                                                    </div>
                                                    <div class="col-md-6 text-align-center">
                                                        <label for="radio_no" class="bold" style="width: 100%">
                                                            <input id="radio_no" name="wifiaccess" data-radio="iradio_square-orange" class="icheck" value="0" <?php echo (isset($wifi) && $wifi == 0) ? 'checked' : ''; ?> type="radio">
                                                            No
                                                        </label>
                                                    </div>
                                                </div>
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
                                                            <input name="installmentplan" id="select2_planduration" class="form-control" value="<?php echo $plan_values['years'] ?? '' ;?>" placeholder="Select plan duration...">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <input type="number" name="planamount" id="planamount" class="form-control" value="<?php echo $plan_values['monthlyamt'] ?? '' ;?>" step="any" placeholder="Agreed amount...">
                                                        </div>
                                                        <?php
                                                    }
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
                                            <input class="form-control" name="remarks" id="ccremarks" value="<?php echo $plan_values['remarks'] ?? '' ;?>">
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
                <?php
            }
            ?>
        </div>
        <div class="col-md-6">
            <?php if ($qry_stg->desc == 'Proposal') { ?>
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-red-flamingo bold uppercase"> Assigned Sales Officer</span>
                        </div>
                        <div class="tools">
                            <?php if (array_intersect(array(1,50),$user_role)) { ?>
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
            <?php } ?>
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-red-flamingo bold uppercase"> Preview</span>
                    </div>
                    <div class="tools">
                        <div class="btn-group">
                            <a href="javascript:" id="btn_reload_preview" class="btn btn-primary btn-sm inline" data-type="<?php echo $doctype[$qry_stg->sysid]; ?>"><i class="fa fa-refresh"></i> Refresh</a>
                            <a href="javascript:" id="btn_open_preview" class="btn btn-primary btn-sm inline" data-type="<?php echo $doctype[$qry_stg->sysid]; ?>"><i class="fa fa-search"></i> Open in Tab</a>
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <!--<embed src="<?php echo base_url(); ?>cad/getproposalpdf/<?php echo $dataid; ?>" width="100%" height="500"
                       type="application/pdf">-->
                    <?php
                    $iframe_id = '';
                    if ($qry_stg->desc == 'Proposal') {
                        $iframe_id = 'iframe_prop_preview';
                    }
                    if ($qry_stg->desc == 'Credit Check Info') {
                        $iframe_id = 'iframe_cca_preview';
                    }
                    ?>
                    <!--<iframe id="iframe_doc_preview" src="" style="width:100%; height:500px;" frameborder="0"></iframe>-->

                    <div id="iframe_box" data-id="<?php echo $doctype[$qry_stg->sysid]; ?>">

                    </div>
                    <div class="portlet-footer btn-group" id="preview_actions">
<?php
                                    echo user_info()->user_id;
                                ?>

                    </div>
                </div>
            </div>
            <?php if ($flowid == 100) { ?>
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <div class="caption">
                                <i class="fa fa-gears"></i>
                                <span class="caption-subject font-red-flamingo bold uppercase">Panels and Inverters</span>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <form id="frm_add_spsitem" action="<?php echo base_url()?>inspection/addspsitem" method="post">
                            <table class="table table-striped table-condensed table-bordered table-responsive" id="tbl_add_components">
                                <thead>
                                <th style="width: 50% !important;">Select Item</th>
                                <th style="width: 10% !important;">Qty</th>
                                <th style="width: 10% !important;">Unit</th>
                                <!--<th style="width: 15% !important;">Price</th>
                                <th style="width: 15% !important;">Total</th>-->
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <input class="form-control input-sm " id="select2_newitem" name="newitem" placeholder="Select item...">
                                    </td>
                                    <td class="number">
                                        <input class="form-control input-sm " id="input_new_item_qty" name="itemqty" placeholder="Qty...">
                                    </td>
                                    <td>
                                        <input type="hidden" name="itemtype" id="input_new_item_type">
                                        <input type="hidden" name="itemunit" id="input_new_item_unit">
                                        <span id="item_unit"></span>
                                    </td>
                                    <!--<td class="number">
                                        <input class="form-control input-sm " id="input_new_item_price" placeholder="Price...">
                                    </td>
                                    <td class="number">
                                        <span id="item_total">0.00</span>
                                    </td>-->
                                </tr>

                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="5">
                                        <div class="btn-group" id="item_controls">
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-primary pull-left" id="btn_save_new_item"><i class="fa fa-plus"></i> Add </button>
                                        <button type="reset" href="javascript:;" class="btn btn-sm btn-danger pull-right" id="btn_reset_new_item"><i class="fa fa-rotate-left"></i> Reset </button>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </form>
                        <hr>
                        <table class="table table-striped table-hover table-bordered table-condensed types" id="tbl_sps_components" width="100%">
                            <thead>
                            <th>#</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <!--<th>Price</th>
                            <th>Total</th>-->
                            <th><i class="fa fa-check-square-o"></i></th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <?php
            } ?>
        </div>

        <script type="text/javascript">
            SALES.init(<?php echo $dataid; ?>);
            CAD.requirements(<?php echo $dataid; ?>,true);
            INSPECTION.application(<?php echo $dataid; ?>);
        </script>
    <?php }?>
</div>
