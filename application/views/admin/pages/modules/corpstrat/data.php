<?php
$trnid = $this->uri->segment(4);
$qry_trn = $this->db->select()->from('transaction_request_main_trails')->where('sysid', $trnid)->get()->row();
$qry_stg = $this->db->select()->from('prime_transaction_flow_main_stages')->where(array('sysid' => $qry_trn->stageid))->get()->row();
$flowid = $qry_stg->flowid;

$docs_qry = $this->db->select('d.doctype,t.names,t.desc,d.signed')
    ->from('prime_documents_main as d')
    ->join('prime_types_parameter as t', 'd.doctype = t.sysid', 'left')
    ->where(array('d.dataid' => $dataid, 'd.status' => 1))
    ->where_in('d.doctype', array(3433, 3435))->order_by('d.doctype ASC')
    ->get();
$documents = array();
if ($docs_qry->num_rows() > 0) {
    $documents = $docs_qry->result();
}

$user_role = get_users_roles_matrix_id_arr();
?>
<!-- DATEPICKER CSS START!-->
<link rel="stylesheet" type="text/css"
    href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/datepicker3.css">
<!-- DATEPICKER CSS END!-->
<link rel="stylesheet" type="text/css"
    href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/css/fileinput.css">
<link rel="stylesheet" type="text/css"
    href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.css">
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
    <div class="col-md-6">
        <?php
        $appdetails = get_application_details($dataid);
        $rateclassid = (isset($appdetails->rateclassid)) ? $appdetails->rateclassid : false;
        customer_application_basicinfo($dataid, true, false);
        ?>

        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-line-chart"></i><span class="caption-subject font-red-flamingo bold uppercase">
                        System Rates
                        <?php echo ($appdetails->info->systemtype == 1) ? '<small>(Standard)</small>' : ''; ?></span>
                </div>
            </div>
            <div class="portlet-body">
                <form id="frm_rate_update" method="post" action="<?php echo base_url(); ?>cad/saveproposedsystemrates">
                    <?php if ($appdetails->info->systemtype == 2) {

                        $amount_lookup = $this->db->select('sg.appid,sg.desc as sizename,p.outright,p.twoyrs,p.threeyrs,p.fiveyrs,p.tenyrs,p.monthlyave,p.summerave,p.buildtime')
                            ->from('customer_system_group AS sg')
                            ->join('proposal_nonstandard_system_rates AS p', 'sg.sysid = p.systemsizeid AND p.`status` = 1', 'left')
                            ->where(array('sg.appid' => $dataid, 'sg.status' => 1))
                            ->get()->row();

                        if ($amount_lookup) {
                            extract((array) $amount_lookup);
                        }

                        ?>
                        <div class="row margin-bottom-5 " id="nonstandardsize">
                            <input type="hidden" name="systemsizeid" value="<?php echo $appdetails->info->systemsizeid; ?>">
                            <div class="col-md-12 margin-top-10">
                                <table style="width: 100%;" id="tbl_sysrates"
                                    class="zui-table table table-hover table-striped table-bordered">
                                    <thead>
                                        <th>Outright</th>
                                        <th>Two Years</th>
                                        <th>Three Years</th>
                                        <th>Five Years</th>
                                        <th>Ten Years</th>
                                        <th>Monthly Average</th>
                                        <th>Summer Average</th>
                                        <th>Build Time</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <?php echo dt_inline_input('outright', 'number', $outright ?? false, array('step' => '.01', 'disabled' => false, 'required' => true), 'text-align-right', array('width' => '100%', 'height' => '34px')); ?>
                                            </td>
                                            <td>
                                                <?php echo dt_inline_input('twoyrs', 'number', $twoyrs ?? false, array('step' => '.01', 'disabled' => false, 'required' => true), 'text-align-right', array('width' => '100%', 'height' => '34px')); ?>
                                            </td>
                                            <td>
                                                <?php echo dt_inline_input('threeyrs', 'number', $threeyrs ?? false, array('step' => '.01', 'disabled' => false, 'required' => true), 'text-align-right', array('width' => '100%', 'height' => '34px')); ?>
                                            </td>
                                            <td>
                                                <?php echo dt_inline_input('fiveyrs', 'number', $fiveyrs ?? false, array('step' => '.01', 'disabled' => false, 'required' => true), 'text-align-right', array('width' => '100%', 'height' => '34px')); ?>
                                            </td>
                                            <td>
                                                <?php echo dt_inline_input('tenyrs', 'number', $tenyrs ?? false, array('step' => '.01', 'disabled' => false, 'required' => true), 'text-align-right', array('width' => '100%', 'height' => '34px')); ?>
                                            </td>
                                            <td>
                                                <?php echo dt_inline_input('monthlyave', 'number', $monthlyave ?? false, array('step' => '.01', 'disabled' => false, 'required' => true), 'text-align-right', array('width' => '100%', 'height' => '34px')); ?>
                                            </td>
                                            <td>
                                                <?php echo dt_inline_input('summerave', 'number', $summerave ?? false, array('step' => '.01', 'disabled' => false, 'required' => true), 'text-align-right', array('width' => '100%', 'height' => '34px')); ?>
                                            </td>
                                            <td>
                                                <?php echo dt_inline_input('buildtime', 'number', $buildtime ?? false, array('step' => '1', 'disabled' => false), 'text-align-right', array('width' => '100%', 'height' => '34px')); ?>
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
                            ->where(array('systemsizeid' => $appdetails->info->systemsizeid, 'status' => 1))
                            ->get()->row();

                        $rates = array();
                        if ($proposal_rates_qry) {
                            $proposed = $proposal_rates_qry;
                            ?>
                            <div class="row margin-bottom-5 " id="nonstandardsize">
                                <div class="col-md-12 margin-top-10">
                                    <table style="width: 100%;" id="tbl_sysrates"
                                        class="zui-table table table-hover table-striped table-bordered">
                                        <thead>
                                            <th>Outright</th>
                                            <th>Two Years</th>
                                            <th>Three Years</th>
                                            <th>Five Years</th>
                                            <th>Ten Years</th>
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
                                                    <?php echo ($proposed->threeyrs) ?? 0.00 ?>
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
                    if ($appdetails->info->systemtype == 2) {
                        ?>
                        <div class="portlet-footer">
                            <button type="submit" class="btn btn-primary pull-right" style="padding-top: 10px"><i
                                    class="fa fa-save"></i> Save</button>
                        </div>
                    <?php } ?>
                </form>
            </div>
        </div>

    </div>

    <div class="col-md-6">
        <div class="row  margin-bottom-10">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-red-flamingo bold uppercase"> Assigned Sales
                                Officer</span>
                        </div>
                        <div class="tools">
                            <?php if (array_intersect(array(1, 50), $user_role)) { ?>
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
        <div class="row  margin-bottom-10">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title" style="position: relative">
                        <div class="caption">
                            <i class="fa fa-bolt"></i>
                            <span class="caption-subject font-red-flamingo bold uppercase"><span
                                    class="label label-danger">2</span> Documents Preview</span>
                        </div>
                        <div class="tools" id="inspection_tools">

                        </div>
                        <div class="tabbable-line pull-right" id="doc_preview_box">
                            <ul class="nav nav-tabs " id="doc_preview_tabs">
                                <li class="active">
                                    <a href="#doc_tssr" data-toggle="tab" aria-expanded="true" data-id="3436"> TSSR </a>
                                </li>
                                <?php
                                if (!result_array_look_up($documents, 'doctype', 3433)->result) {
                                    echo '<li class="">';
                                    echo '<a href="#doc_proposal" data-toggle="tab" aria-expanded="true" data-id="3433"> Proposal </a>';
                                    echo '</li>';
                                }

                                foreach ($documents as $dox) {
                                    echo '<li class="">';
                                    echo '<a href="#doc_' . strtolower($dox->names) . '" data-toggle="tab" aria-expanded="true" data-id="' . $dox->doctype . '"> ' . $dox->names . ' </a>';
                                    echo '</li>';
                                }
                                ?>
                                <li class="">
                                    <a href="#doc_others" data-toggle="tab" aria-expanded="true" data-id="3436"> Other
                                        Documents </a>
                                </li>
                            </ul>

                        </div>
                    </div>
                    <div class="portlet-body">

                        <div class="tab-content" id="doc_preview_pane">
                            <div class="tab-pane fade in active" id="doc_tssr">
                                <div class="row">
                                    <div class="btn-group col-md-12 pull-right">
                                        <a href="javascript:" id="btn_open_preview"
                                            class="btn btn-primary btn-sm inline" data-type="3436"><i
                                                class="fa fa-search"></i> Open in Tab</a>
                                    </div>
                                </div>
                                <!--<iframe id="iframe_tssr_preview" data-type="3436" src="" style="width:100%; height:500px;" frameborder="0"></iframe>-->
                            </div>
                            <?php
                            if (!result_array_look_up($documents, 'doctype', 3433)->result) {
                                ?>
                                <div class="tab-pane fade in" id="doc_proposal">
                                    <div class="row">
                                        <div class="btn-group">
                                            <a href="javascript:" id="btn_reload_preview"
                                                class="btn btn-primary btn-sm inline" data-type="3433"><i
                                                    class="fa fa-refresh"></i> Refresh</a>
                                            <a href="javascript:" id="btn_open_preview"
                                                class="btn btn-primary btn-sm inline" data-type="3433"><i
                                                    class="fa fa-search"></i> Open in Tab</a>
                                        </div>
                                        <hr>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php
                            foreach ($documents as $dox) {
                                if ($dox->doctype == 3433) {
                                    ?>
                                    <div class="tab-pane fade in" id="doc_proposal">
                                        <div class="row">
                                            <div class="btn-group">
                                                <?php echo (!$dox->signed || is_null($dox->signed)) ? '<a href="javascript:" id="btn_sign_doc" class="btn btn-primary btn-sm inline" data-name="' . $dox->names . '" data-type="' . $dox->doctype . '"><i class="fa fa-pencil"></i> Sign</a>' : ''; ?>
                                                <a href="javascript:" id="btn_reload_preview"
                                                    class="btn btn-primary btn-sm inline"><i class="fa fa-refresh"></i>
                                                    Refresh</a>
                                                <a href="javascript:" id="btn_open_preview"
                                                    class="btn btn-primary btn-sm inline"
                                                    data-type="<?php echo $dox->doctype; ?>"><i class="fa fa-search"></i> Open
                                                    in Tab</a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    echo '<div class="tab-pane fade in " id="doc_' . strtolower($dox->names) . '">';
                                    echo '<div class="row">';
                                    echo '<div class="btn-group">';
                                    if (!$dox->signed || is_null($dox->signed)) {
                                        echo '<a href="javascript:" id="btn_sign_doc" class="btn btn-primary btn-sm inline" data-name="' . $dox->names . '" data-type="' . $dox->doctype . '"><i class="fa fa-pencil"></i> Sign</a>';
                                    }
                                    echo '<a href="javascript:" id="btn_open_preview" class="btn btn-primary btn-sm inline" data-type="' . $dox->doctype . '"><i class="fa fa-search"></i> Open in Tab</a>';
                                    echo '</div></div>';
                                    //echo '<iframe id="iframe_'.strtolower($dox->names).'_preview" data-type="'.$dox->doctype.'" src="" style="width:100%; height:500px;" frameborder="0"></iframe>';
                                    echo '</div>';
                                }
                            }
                            ?>
                            <div class="tab-pane fade in" id="doc_others">
                                <table id="tbl_assessment_docs" class="table table-condensed table-bordered table-hover"
                                    width="100%"
                                    data-folder="<?php echo 'cad/applications/' . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/" . get_stage_specific(92)->desc . "/Docs/"; ?>">
                                    <thead>
                                        <th>#</th>
                                        <th width="100%">File</th>
                                        <th class="center"><i class="fa fa-search"></i> </th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="portlet-footer">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>



<!--<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/datatables/dataTables.bootstrap.js"></script>-->

<!-- DATE PICKER!-->
<script type="text/javascript"
    src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

<script src="<?php echo base_url(); ?>assets/global/plugins/fuelux/js/spinner.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery.input-ip-address-control-1.0.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js"
    type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.min.js"
    type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js"
    type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js"
    type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript"
    src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script type="text/javascript"
    src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script type="text/javascript"
    src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- DATE PICKER END!-->
<!-- GOOGLE MAPS LIBS START !-->
<script src="<?php echo base_url(); ?>assets/global/plugins/gmaps/gmaps.js" type="text/javascript"></script>

<script src="<?php echo file_versioning('assets/pages/cad/newaccount.js'); ?>" type="text/javascript"></script>
<script src="<?php echo file_versioning('assets/pages/inspection/main.js'); ?>" type="text/javascript"></script>
<script src="<?php echo file_versioning('assets/pages/sales/main.js'); ?>" type="text/javascript"></script>
<script src="<?php echo file_versioning('assets/pages/maps/main.js'); ?>" type="text/javascript"></script>
<script src="<?php echo file_versioning('assets/pages/attachements/main.js'); ?>" type="text/javascript"></script>

<script type="text/javascript">
    //GMAPSMAIN.mapping(<?php echo $dataid; ?>, '#gmap_geocoding', true, <?php echo $moduleid; ?>);
    INSPECTION.application(<?php echo $dataid; ?>);
    INSPECTION.team(<?php echo $dataid; ?>, <?php echo $moduleid; ?>);
    CAD.profile(<?php echo $dataid; ?>, <?php echo $flowid; ?>);
    ATTACHEMENTS.init(<?php echo $dataid; ?>);
    ATTACHEMENTS.docs(<?php echo $dataid; ?>);
    SALES.viewer(<?php echo $dataid; ?>)
</script>