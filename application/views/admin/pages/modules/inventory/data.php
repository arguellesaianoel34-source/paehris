<?php
$inv_qry = $this->db->select()
    ->from('inventory_transaction_group`')
    ->where('sysid',$dataid)
    ->get()->row();

$trnTitle = 'Inventory Approval';

if ($inv_qry) {
    $creator = get_users_info($inv_qry->createdby);
    $invno = 'INV'.date('Ym',strtotime($inv_qry->datecreated)).str_pad($inv_qry->sysid,3,'0',STR_PAD_LEFT);
    $justification = ellipsis($inv_qry->desc,50);
    $requestor = ($creator) ? $creator->firstname.' '.$creator->lastname : 'N/A';


    if ($inv_qry->trntype ==23) {
        $trnTitle = 'Receiving Approval';
    }

    if ($inv_qry->trntype ==24) {
        $trnTitle = 'Installation Monitoring';
    }
}

$trn_qry = $this->db->select()
    ->from('transaction_request_main')
    ->where('sysid',$trnflowid)
    ->get()->row();

if ($trn_qry) {
    $request_date = date('F j, Y',strtotime($trn_qry->datecreated));
}

$remarks_qry = $this->db->select('remarks')
    ->from('eprs_transaction_logs')
    ->where(array(
        'prsid' => $dataid,
        'typesid' => 1207,
        'moduleid' => 193,
        'statusid' => 305,
        'status' => 1
    ))->get()->row();

$po_qry = $this->db->select('ponumber')
    ->from('eprs_po')
    ->where(array('prfid' => $dataid,'status' => 1))
    ->get()->row();
if ($po_qry) {
    $ponum = 'PAE-'.str_pad($po_qry->ponumber,8,'0',STR_PAD_LEFT);
} else {
    $ponum = 'N/A';
}
?>

<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.css" media="all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url() ?>assets/global/plugins/cubeportfolio/css/cubeportfolio.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url() ?>assets/pages/css/portfolio.min.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    .comment-content {
        border-radius: 5px 15px 15px 15px !important;
        padding: 5px;
        min-width: 200px;
        max-width: 400px;
        min-height: 30px;
        /*line-height: 25px;*/
    }

    .comment-you {
        padding-left: 25px !important;
    }

    .comment-you .comment-content {
        background-color: rgba(10, 182, 255, 0.5);
    }

    .comment-them .comment-content {
        background-color: rgba(117, 114, 114, 0.5);
    }

    .comment-content p {
        margin: 0px !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="fa fa-check font-green-haze"></i>
                    <span class="caption-subject bold font-green-haze uppercase"> <?php echo $trnTitle; ?> </span>
                    <span class="caption-helper">more details..</span>
                </div>
            </div>
            <div class="portlet-body ">
                <div class="well">
                    <div class="row">
                        <div class="col-md-4">
                            <ul class="list-group summary column no-border">
                                <li class="list-group-item">
                                    <span class="label-name col-md-4 bold">Inventory #</span>
                                    <span class="col-md-8 font-blue-steel "><?php echo $invno; ?></span>
                                </li>
                                <ul class="list-group summary column no-border">
                                    <li class="list-group-item">
                                        <span class="label-name col-md-4 bold">Description</span>
                                        <span class="col-md-8 font-blue-steel "><?php echo $justification; ?></span>
                                    </li>
                                </ul>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <ul class="list-group summary column no-border">
                                <li class="list-group-item">
                                    <span class="label-name col-md-4 bold">Submitted by</span>
                                    <span class="col-md-8 font-blue-steel "><?php echo $requestor; ?></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="label-name col-md-4 bold">Submitted</span>
                                    <span class="col-md-8 font-blue-steel "><?php echo $request_date; ?></span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">

                            <?php if ($inv_qry->status != 0) {
                                if (in_array($inv_qry->status,array(302,303))) {
                                    $status = get_types_name($inv_qry->status)->names;
                                    echo '<button class="btn btn-danger" disabled><i class="fa fa-times"></i> '.$status.' : '.date('M-d-Y',strtotime($prf_qry->dateupdated)).'</button>';
                                } else {
                                    if (!isset($trnview) || $trnview == false) { ?>
                                        <div class="btn-group">
                                            <button id="btn_submit_trn" data-flowid="<?php echo $flowid;?>" data-stageid="<?php echo $stageid;?>" data-trnid="<?php echo $trnid;?>" class="btn btn-primary"><i class="fa fa-send"></i> Submit Transaction</button>
                                        </div>
                                    <?php   }
                                    if ($inv_qry->createdby == user_id()) {
                                        ?>
                                        <div class="btn-group">
                                            <button id="btn_cancel_rfq" data-flowid="<?php echo $flowid;?>" data-stageid="<?php echo $stageid;?>" data-trnid="<?php echo $trnid;?>" data-type="1207" class="btn btn-danger"><i class="fa fa-times"></i> Cancel TRN</button>
                                        </div>
                                        <?php
                                    }
                                }
                            } else {
                                echo '<button class="btn btn-danger" disabled><i class="fa fa-times"></i> Canceled : '.date('M-d-Y',strtotime($prf_qry->dateupdated)).'</button>';
                            } ?>
                        </div>
                    </div>
                </div>
                <div class="row margin-top-10" id="inventory_trn_fields">
                    <!--
                    RECEIVING: ADD NEW TAB FOR EACH PO BEING RECEIVED.
                    - QUERY PO

                    CHECK-OUT: ADD NEW TAB FOR EACH BUILD.
                    - QUERY CUSTOMER APPLICATION

                    IF TRN TYPE AND TRN DATE IS SET, QUERY EXISTING TRANSACTION NOT FORWARDED FOR APPROVAL.
                    IF PENDING TRN EXIST, ASK TO LOAD THRU SWAL.
                    -->
                    <div class="note note-info text-align-center" id="no_reference_notice">
                        <h4><i class="fa fa-warning text-warning"></i> Please select references for item lists to be loaded.</h4>
                    </div>
                    <!--<table class="table table-bordered table-condensed" id="tbl_inv_items" style="width: 100%">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th id="item_desc">Item Name</th>
                            <th>Unit</th>
                            <th>Control</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>-->
                    <div id="inventory_transactions" class="hidden">
                        <ul id="inv_reference_tabs" class="nav nav-tabs">
                            <li class="" id="attachment_tab">
                                <a href="#inventory_attachements" data-toggle="tab" aria-expanded="true" style="margin-right: 10px;" title="Attachments">Attachments <i class="fa fa-clipboard"></i> </a>
                            </li>
                        </ul>
                        <div class="tab-content" id="inv_reference_content">
                            <div class="tab-pane fade in" id="inventory_attachements">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <h4>Upload Attachment</h4>
                                            <hr>
                                            Browse File
                                            <input id="appfiledrop" placeholder="Browse file..." name="appfiledrop" data-upload-url="<?php echo base_url('inventory/uploadattachments'); ?>" multiple class="file" type="file" data-preview-file-type="any"  />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
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
                                                <div id="box_file_explorer" class="well mt-element-card mt-element-overlay" style="display: inline-block; width: 100%; min-height: 300px; border: 4px dashed #ccc; text-align: left;" data-folder="117" >
                                                    <h3><i class="fa fa-warning text-warning"></i> No file uploaded yet!</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/fileinput.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/fr.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/es.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.js" type="text/javascript"></script>

<script type="text/javascript" src="<?php echo base_url() ;?>assets/global/plugins/jquery-mixitup/jquery.mixitup.min.js"></script>
<script type="text/javascript" src="<?php echo base_url() ;?>assets/global/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
<script src="<?php echo base_url() ;?>assets/admin/pages/scripts/portfolio.js"></script>
<script src="<?php echo base_url() ;?>assets/admin/pages/scripts/form-fileupload.js"></script>

<script type="text/javascript" src="<?php echo file_versioning('assets/pages/attachements/main.js'); ?>"></script>
<script type="text/javascript" src="<?php echo file_versioning('assets/pages/inventory/main.js'); ?>"></script>
<script type="text/javascript">
    INVENTORY.dataEntry(<?php echo $dataid; ?>);
    PECO.ellipsisExpand();
    ATTACHEMENTS.inventory(<?php echo $dataid; ?>);
</script>
