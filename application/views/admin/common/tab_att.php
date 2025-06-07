<?php

$uploadurl = 'admin/uploadapplicationfiles';

$navids = get_users_info_navigation_ids();
$modules = array();
$stages = array();
$trnname = array();

$get_flow_stages = $this->db->select('sysid,moduleid,desc')
    ->from('prime_transaction_flow_main_stages')
    ->where(array('flowid' => 2, 'status' => 1))
    ->get();

if ($get_flow_stages->num_rows() > 0) {
    foreach ($get_flow_stages->result() AS $stage) {
        if (check_user_nav_access($stage->moduleid)) {
            $modules[] = $stage->moduleid;
            $stages[] = $stage->sysid;
            $trnname[$stage->sysid] = $stage->desc;
        }
    }
}

$trn = array();

foreach ($stages AS $stage) {
    if ($stage == 92 && !in_array(92,$trn)) {
        $trn[] = $stage;
    }
    if ($stage == 93 && !in_array(92,$trn)) {
        $trn[] = 92;
    }
    if ($stage == 100 && !in_array(100,$trn)) {
        $trn[] = $stage;
    }
    if ($stage == 96 && !in_array(100,$trn)) {
        $trn[] = 100;
    }
    if ($stage == 115 && !in_array(100,$trn)) {
        $trn[] = 100;
    }
}

$trns = '';
$type = '';
$value = '';
$transaction = '';
if (count($trn) > 1) {
    $trns = implode(',',$trn);
    $required = 'required';
} else {
    $trns = implode(',',$trn);
    $transaction = ': '.$trnname[$trns].' ';
    $type = 'type="hidden"';
    $value = 'value="'.$trns.'"';
    $folder = $trnname[$trns];
}
?>


<link href="<?php echo base_url() ;?>assets/global/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url() ;?>assets/admin/pages/css/portfolio.css" rel="stylesheet" type="text/css"/>

<!-- END PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/address/address.css"/>

<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.css" media="all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url() ?>assets/global/plugins/cubeportfolio/css/cubeportfolio.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url() ?>assets/pages/css/portfolio.min.css" rel="stylesheet" type="text/css" />


<style>

    .modal-backdrop {
        margin-top: 50px !important;
    }

    .modal-dialog {
        margin-top: 100px !important;
        top: 100px !important;
    }

    pre {
        white-space: pre-wrap;       /* css-3 */
        white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
        white-space: -pre-wrap;      /* Opera 4-6 */
        white-space: -o-pre-wrap;    /* Opera 7 */
        word-wrap: break-word;       /* Internet Explorer 5.5+ */
    }

</style>

<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <h4>Upload Documents</h4>
                    <hr>
                    Transaction<span class="required"><?php echo $transaction; ?></span>
                    <input id="input_stageid" <?php echo $type; ?> name="stageid" <?php echo $value; ?> class="form-control" placeholder="Select transaction..." data-id="<?php echo $trns; ?>" <?php echo $required ?? false; ?> />
                    <hr>
                    Browse File
                    <div id="fileuploader">
                        <input id="appfiledrop" placeholder="Browse file..." name="appfiledrop" data-upload-url="<?php echo base_url($uploadurl); ?>" multiple class="file" type="file" data-preview-file-type="any"  />
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="bordered">
                    <b>File Naming Formats:</b>
                    <div data-trn="file_name" data-id="92" <?php echo ($trns == '92') ? '' : 'class="hidden"'; ?> >
                        <ul class="list-group">
                            <li class="list-group-item">Location of Tapping Point : <b>tp</b></li>
                            <li class="list-group-item">Location of Inverter : <b>li</b></li>
                            <li class="list-group-item">PV Location : <b>pvl</b></li>
                            <li class="list-group-item">DC String Runway : <b>dcs</b></li>
                            <li class="list-group-item">Highest recorded voltage : <b>volt</b></li>
                            <li class="list-group-item">Highest recorded amperage : <b>amp</b></li>
                            <li class="list-group-item">Picture of Bill : <b>bill</b></li>
                            <li class="list-group-item">Picture of Roof : <b>roof</b></li>
                            <li class="list-group-item">PV Layout : Include "PV Layout" in filename without special characters in the name</li>
                            <li class="list-group-item">SLD : Just upload as is.</li>
                            <li class="list-group-item">Monthly Projected Production : <b>PAE123456 - MPP</b></li>
                        </ul>
                        Note: If picture of something is more than one, add _#. Ex: roof_1, roof_2
                    </div>
                    <div data-trn="file_name" data-id="93" class="hidden">
                        <ul class="list-group">
                            <li class="list-group-item">PV Layout : Include "PV Layout" in filename without special characters in the name</li>
                            <li class="list-group-item">Monthly Projected Production : <b>PAE123456 - MPP</b></li>
                        </ul>
                        Note: If picture of something is more than one, add _#. Ex: roof_1, roof_2
                    </div>
                    <div data-trn="file_name" <?php echo ($trns == '95') ? '' : 'class="hidden"'; ?> data-id="95">
                        <table class="table table-bordered table-condensed table-striped table-hover table-sm">
                            <thead>
                            <th>File Name</th>
                            <th>Full Name</th>
                            </thead>
                            <tbody>
                            <?php
                            $req_qry = $this->db->select()
                                ->from('prime_requirement_parameters')
                                ->where('status',1)->get();

                            if ($req_qry->num_rows() > 0) {
                                foreach ($req_qry->result() AS $req) {
                                    echo '<tr>';
                                    echo '<td class="bold">'.$req->codes.'</td>';
                                    echo '<td>'.$req->names.'</td>';
                                    echo '</tr>';
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title tabbable-line">
                <div class="caption"> <i class="fa fa-edit"></i> <span class="caption-subject font-green-sharp bold uppercase">Attachment(s)</span> <span class="caption-helper"></span> </div>

                <div class="tools">

                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-12">
                        <a href="javascript:" id="btn_reload_attachments" class="btn btn-default inline pull-right"><i class="fa fa-refresh"></i> Reload</a>
                    </div>
                </div>
                <div id="box_file_explorer" class="well mt-element-card mt-element-overlay" style="display: inline-block; width: 100%; min-height: 300px; border: 4px dashed #ccc; text-align: left;" data-folder="<?php echo $trns ?? false; ?>" >
                    <h3><i class="fa fa-warning text-warning"></i> No file uploaded yet!</h3>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade draggable-modal" id="assignfilemodallist" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog" style="width: 900px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Select Files</h4>
            </div>
            <div class="modal-body table">
                <div class="row">
                    <div id="divfiles" class="well col-md-12" style="height: 280px;overflow-y: scroll;background-color: white;">
                        <input type="hidden" name="requirementshiddenval" id="requirementshiddenval" />
                        <div id="showpictures"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>




<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/fileinput.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/fr.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/es.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.js" type="text/javascript"></script>


<script type="text/javascript" src="<?php echo base_url() ;?>assets/global/plugins/jquery-mixitup/jquery.mixitup.min.js"></script>
<script type="text/javascript" src="<?php echo base_url() ;?>assets/global/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
<script src="<?php echo base_url() ;?>assets/admin/pages/scripts/portfolio.js"></script>
<script src="<?php echo base_url() ;?>assets/admin/pages/scripts/form-fileupload.js"></script>


<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery.mockjax.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/bootstrap-editable/js/bootstrap-editable.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/address/address.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/wysihtml5/wysihtml5.js"></script>



<script src="<?php echo base_url() ?>assets/global/plugins/cubeportfolio/js/jquery.cubeportfolio.min.js" type="text/javascript"></script>


<script type="text/javascript" src="<?php echo file_versioning('assets/pages/cad/form-editable.js'); ?>"></script>
<script type="text/javascript" src="<?php echo file_versioning('assets/pages/attachements/main.js'); ?>"></script>


<script>
    ATTACHEMENTS.tab(<?php echo $dataid; ?>);
</script>
