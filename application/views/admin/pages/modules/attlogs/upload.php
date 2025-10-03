<?php
$dataid = 1;
/*$trnid = ($this->uri->segment(4)) ? $this->uri->segment(4) : false;
$qry_trl = $this->db->select()->from('transaction_request_main_trails')
    ->where(array('dataid' => $dataid, 'sysid' => $trnid))
    ->get()->row();*/

//echo $this->db->last_query();
$uploadurl = 'hris/attlogs';

/*if ($qry_trl) {
    $location = $qry_trl->stageid;

    $url = array(
        92 => 'inspection/uploadsurveypics',
        93 => 'inspection/uploadsurveypics',
        95 => 'cad/uploadrequirements',
        98 => 'installation/uploadpics',
        99 => '',
        100 => 'cad/uploadrequirements',
        101 => 'inspection/uploadsurveypics',
        115 => 'cad/uploadrequirements',
        104 => 'purchasing/uploadpastpurchases'
    );

    if ($url[$qry_trl->stageid] != '') {
        $uploadurl = $url[$qry_trl->stageid];
    }
}*/

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

    <div class="col-md-6">
        <div class="form-group">
            <h4>Upload Log File</h4>

            <hr>
            Browse File
            <input id="attfiledrop" placeholder="Browse file..." name="attfiledrop[]" data-upload-url="<?php echo base_url($uploadurl); ?>" class="file" type="file" data-preview-file-type="any" multiple />
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


<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/cad/form-editable.js"></script>
<script type="text/javascript" src="<?php echo file_versioning('assets/pages/attachements/main.js'); ?>"></script>


<script>
    ATTACHEMENTS.attLogs();
</script>
