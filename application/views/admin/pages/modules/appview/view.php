<?php
$this->load->helper('cad_helper');

if (strpos($dataid,'PAE') !== false) {
    $newid = ltrim(str_replace('PAE','',$dataid),0);
    $appid_qry = $this->db->select('sysid')
        ->from('application_customers_details')
        ->where('essrno',$newid)->get()->row();

    if ($appid_qry) {
        $dataid = $appid_qry->sysid;
    }
}

/*$trnid = $this->uri->segment(4);
$qry_trn = $this->db->select()->from('transaction_request_main_trails')->where('sysid', $trnid)->get()->row();
$qry_stg = $this->db->select()->from('prime_transaction_flow_main_stages')->where(array('sysid' => $qry_trn->stageid))->get()->row();
$flowid = $qry_stg->flowid;*/

$get_flow_stages = $this->db->select('sysid,moduleid')
    ->from('prime_transaction_flow_main_stages')
    ->where(array('flowid' => 2, 'status' => 1))
    ->get();

if ($get_flow_stages->num_rows() > 0) {
    foreach ($get_flow_stages->result() AS $stage) {
        if (check_user_nav_access($stage->moduleid)) {
            $modules[] = $stage->moduleid;
            //$access[$stage->moduleid] = 'true';
        }
        $stages[] = $stage->sysid;
    }
}

$qry_trl = $this->db->select('trmt.*,stg.moduleid')
    ->from('transaction_request_main_trails AS trmt')
    ->join('prime_transaction_flow_main_stages AS stg','trmt.stageid = stg.sysid','left')
    ->where(array('trmt.dataid' => $dataid))
    ->where_in('trmt.stageid', $stages)
    ->order_by('trmt.datecreated','DESC')
    ->get()->row();

$docs_qry = $this->db->select('d.doctype,t.names,t.desc,d.signed')
    ->from('prime_documents_main as d')
    ->join('prime_types_parameter as t','d.doctype = t.sysid','left')
    ->where(array('d.dataid' => $dataid,'d.status' => 1))
    ->where_in('d.doctype',array(3433,3435))->order_by('d.doctype ASC')
    ->get();

$user_role = get_users_info_roles();

if (count($user_role) > 0) {
    foreach ($user_role AS $role) {
        //LOOKUP ALL SPECIAL ACCESS MODULEID PER ROLEID
        $get_sp_access = $this->db->select('ts.moduleid')
            ->from('transaction_viewer_role_access as sp')
            ->join('prime_transaction_flow_main_stages as ts','sp.stageid = ts.sysid AND ts.flowid = 2','left')
            ->join('prime_module_navigations_main as nav','ts.moduleid = nav.sysid','left')
            ->where(array('sp.roleid' => $role->roleid,'sp.status' => 1))
            ->get();

        if ($get_sp_access->num_rows() > 0) {
            foreach ($get_sp_access->result() AS $stage) {
                $modules[] = $stage->moduleid;
            }
        }
    }
}

//set tab and view as active if moduleid = current trail_module

?>
<!-- DATEPICKER CSS START!-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/datepicker3.css">
<!-- DATEPICKER CSS END!-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/css/fileinput.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.css">
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

    .comment-content {
        border-radius: 5px 15px 15px 15px !important;
        padding: 5px;
        min-width: 200px;
        max-width: 400px;
        min-height: 30px;
        /*line-height: 25px;*/
    }

    .quoted-content {
        border-radius: 15px 15px 15px 15px !important;
        width: max-content;
        max-width: 400px;
        padding: 5px;
        margin-bottom: -20px;
        outline-style: solid;
        outline-width: 2px;
        outline-offset: -2px;
        background-color: transparent;
        /*line-height: 25px;*/
    }

    .comment-you {
        /*padding-left: 25px !important;*/
        float: right !important;
    }

    .comment-you .comment-content {
        background-color: rgba(10, 182, 255, 1);
    }

    .comment-them .quoted-content {
        background-color: rgba(168, 158, 163, 0.3);
        outline-color: rgba(168, 158, 163, 0.3);
    }

    .comment-you .quoted-content {
        background-color: rgba(168, 158, 163, 0.3);
        outline-color: rgba(10, 182, 255, 0.3);
    }

    .comment-them .comment-content {
        outline-style: solid;
        outline-width: 2px;
        outline-offset: -2px;
        outline-color: rgba(117, 114, 114, 0.5);
        background-color: rgba(168, 158, 163, 1);
    }

    .comment-content p {
        margin: 0px !important;
    }

    #comments_section {
        overflow-x: hidden;
        overflow-y: auto;
        border: 1px solid #ddd;
        border-collapse: collapse;
        background: rgba(37, 125, 207, 0.06);
    }


</style>


<div class="row tab-pane fade in" id="page_document_window">
    <div class="col-md-6">
        <?php
        $appdetails = get_application_details($dataid);
        $rateclassid = (isset($appdetails->rateclassid)) ? $appdetails->rateclassid : false;
        customer_application_basicinfo($dataid, true, false);
        $status = ($appdetails->info) ? $appdetails->info->status : false;
        ?>
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-history"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">TRN Logs and Comms</span>
                    <span class="caption-helper">
                        <?php

                        ?>
                    </span>
                </div>
                <div class="tools">
                </div>
                <div class="tabbable-line pull-right">
                    <ul class="nav nav-tabs ">
                        <li class="active">
                            <a href="#trn_history" data-toggle="tab" aria-expanded="true"> Transaction History  </a>
                        </li>
                        <li class="">
                            <a href="#comment_view" data-toggle="tab" aria-expanded="true"> Comments </a>
                        </li>
                        <li class="">
                            <a href="#upload_docs" data-toggle="tab" aria-expanded="true"> Upload  </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="portlet-body tab-content">
                <div class="tab-pane fade in active" id="trn_history">
                <?php if (in_array($qry_trl->moduleid,$modules) || super_admin()) { /* If user has access to current module, allow sending. */ ?>
                    <div class="row margin-bottom-15">
                        <div class="col-md-2">
                            <span class="font-red-flamingo bold">Send to</span>
                        </div>
                        <div class="col-md-10">
                            <form id="frm_update_trn" action="<?php echo base_url(); ?>query/requestprocess" method="post">
                                <input name="trnid" type="hidden" class="form-control" value="<?php echo $qry_trl->trnid; ?>"/>
                                <input name="flowid" type="hidden" class="form-control" value="2"/>
                                <input name="stageid" type="hidden" class="form-control" value="<?php echo $qry_trl->stageid; ?>"/>
                                <input name="moduleid" type="hidden" class="form-control" value="<?php echo $qry_trl->moduleid; ?>" />
                                <input name="dataid" type="hidden"class="form-control" value="<?php echo $dataid; ?>" />
                                <div class="input-group">
                                    <input class="form-control" name="routeto" id="select2_routes" placeholder="Forward or return to..." value="<?php echo $qry_trl->stageid; ?>">
                                    <div class="input-group-btn">
                                        <button class="btn btn-primary" type="submit"><i class="fa fa-send"></i> Send</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php } ?>
                    <table class="table table-condensed table-bordered table-striped table-advanced table-hover" id="trn_logs">
                        <thead>
                        <th>#</th>
                        <th>Descriptions</th>
                        <th>Current</th>

                        </thead>
                        <tbody>
                        <?php

                        $a = $this->model_query->hist_trans($dataid,$qry_trl->trnid);

                        if ($a->num_rows() > 0) {
                            $num = 1;
                            foreach ($a->result() as $row) {

                                echo '<tr>';
                                echo '<td>' . $num++ . '</td>';
                                echo '<td>' . $row->desc. ' (' .$row->datecreated. ')' . '</td>';
                                echo '<td class="center">';
                                if ($row->status == 1) {
                                    echo '<span class="bold">[ <i class="fa fa-check fa-sm text-success"></i> CURRENT ]</span>';
                                }
                                echo '</td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade in " id="comment_view">
                    <div class="row">
                        <div class="col-md-12" id="comments_section" style="max-height: 190px !important;min-height: 185px !important;">

                        </div>
                        <div class="col-md-12" style="max-height: 195px !important;">
                            <form id="frm_new_comment" method="post" action="<?php echo base_url('admin/addtrncomment');?>">
                                <h4 class="bold"> Post a comment</h4>
                                <small id="reply_quote" class="bold"></small>
                                <div class="margin-top-10">
                                    <input type="hidden" name="types" value="3441">
                                    <input type="hidden" name="moduleid" value="<?php echo $qry_trl->moduleid;?>">
                                    <input type="hidden" name="dataid" value="<?php echo $dataid;?>">
                                    <input type="hidden" name="stageid" value="<?php echo $qry_trl->stageid;?>">
                                    <input type="hidden" id="quoted_id" name="quotedid">
                                    <div class="input-group">
                                        <input id="comment_area" class="form-control" rows="3" name="messages" style="width: 100% !important; padding-bottom: 20px" placeholder="What do you want to say?" maxlength="200" required />
                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-comment"></i> Send</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade in" id="upload_docs">
                    <?php
                    $this->load->view('admin/common/tab_att');
                    ?>
                </div>
            </div>
        </div>

    </div>

    <div class="col-md-6">
        <div class="row  margin-bottom-10">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title" style="position: relative">
                        <div class="caption">
                            <i class="fa fa-bolt"></i>
                            <span class="caption-subject font-red-flamingo bold uppercase">Details</span>
                        </div>
                        <div class="tabbable-line pull-right">
                            <ul class="nav nav-tabs ">
                                <li class="<?php echo $qry_trl->moduleid == 36 ? 'active' : ''; ?>">
                                    <a href="#assessment_view" data-toggle="tab" aria-expanded="true"> Assessment  </a>
                                </li>
                                <li class="<?php echo $qry_trl->moduleid == 208 ? 'active' : ''; ?>">
                                    <a href="#matscomp_view" data-toggle="tab" aria-expanded="true"> Materials  </a>
                                </li>
                                <li class="<?php echo $qry_trl->moduleid == 203 ? 'active' : ''; ?>">
                                    <a href="#gm_view" data-toggle="tab" aria-expanded="true"> GM  </a>
                                </li>
                                <li class="<?php echo $qry_trl->moduleid == 201 ? 'active' : ''; ?>">
                                    <a href="#sales_view" data-toggle="tab" aria-expanded="true"> Sales  </a>
                                </li>
                                <li class="<?php echo $qry_trl->moduleid == 204 ? 'active' : ''; ?>">
                                    <a href="#billing_view" data-toggle="tab" aria-expanded="true"> Billing  </a>
                                </li>
                                <li class="">
                                    <a href="#sys_docs" data-toggle="tab" aria-expanded="true"> Documents  </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="portlet-body tab-content">
                        <div class="tab-pane fade in <?php echo $qry_trl->moduleid == 36 ? 'active' : ''; ?>" id="assessment_view">
                            <?php
                            application_stages_layout($dataid,36,array('rateclassid' => $rateclassid));
                            ?>
                        </div>
                        <div class="tab-pane fade in <?php echo $qry_trl->moduleid == 208 ? 'active' : ''; ?>" id="matscomp_view">
                            <?php
                            application_stages_layout($dataid,208);
                            ?>
                        </div>
                        <div class="tab-pane fade in <?php echo $qry_trl->moduleid == 203 ? 'active' : ''; ?>" id="gm_view">
                            <?php
                            application_stages_layout($dataid,203);
                            ?>
                        </div>
                        <div class="tab-pane fade in <?php echo $qry_trl->moduleid == 201 ? 'active' : ''; ?>" id="sales_view">
                            <?php
                            application_stages_layout($dataid,201);
                            ?>
                        </div>
                        <div class="tab-pane fade in <?php echo $qry_trl->moduleid == 204 ? 'active' : ''; ?>" id="billing_view">
                            <?php
                            application_stages_layout($dataid,204);
                            ?>
                        </div>
                        <div class="tab-pane fade in " id="sys_docs">
                            <?php
                            application_stages_layout($dataid,0);
                            ?>
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
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

<script src="<?php echo base_url(); ?>assets/global/plugins/fuelux/js/spinner.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery.input-ip-address-control-1.0.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- DATE PICKER END!-->
<!-- GOOGLE MAPS LIBS START !-->
<script src="<?php echo base_url(); ?>assets/global/plugins/gmaps/gmaps.js" type="text/javascript"></script>

<script src="<?php echo file_versioning('assets/pages/cad/newaccount.js'); ?>" type="text/javascript"></script>
<script src="<?php echo file_versioning('assets/pages/sales/main.js'); ?>" type="text/javascript"></script>
<script src="<?php echo file_versioning('assets/pages/inspection/main.js'); ?>" type="text/javascript"></script>
<script src="<?php echo file_versioning('assets/pages/maps/main.js'); ?>" type="text/javascript"></script>
<script src="<?php echo file_versioning('assets/pages/attachements/main.js'); ?>" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo file_versioning('assets/global/scripts/comments.js')?>"></script>

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
    COMM.viewer(3441,<?php echo $qry_trl->moduleid;?>,<?php echo $dataid;?>,<?php echo $qry_trl->stageid;?>);

    <?php if ($status && in_array($status,array(0,303))) { ?>
    $(document).ready(function () {
        $('#page_document_window',document).find('input,button,textarea,[data-toggle="ajax-modal"]').each(function (i,obj) {
            var this_ = $(this);
            this_.attr('disabled',true);
            if (this_.is('button') && !this_.hasClass('hidden')) {
                this_.addClass('hidden');
            }

            if (this_.attr('data-toggle') === 'ajax-modal') {
                this_.addClass('hidden');
            }
        });
    });
    <?php } ?>

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
</script>