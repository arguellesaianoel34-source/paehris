<?php
/**
 * Upon creation, save transaction as draft status.
 * if items were added but transaction was not saved, set items as drafts as well.
 * change "add" button to delete to cancel transaction.
 * Update status to 1 when transaction is saved.
 **/
//LOOK-UP STATUS 1 TRN CREATED BY USER.
$active_qry = $this->db->select()
    ->from('inventory_transaction_group')
    ->where(array('status' => 1,'createdby' => user_id()))->get()->row();


?>
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.css" media="all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url() ?>assets/global/plugins/cubeportfolio/css/cubeportfolio.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url() ?>assets/pages/css/portfolio.min.css" rel="stylesheet" type="text/css" />

<style>
    /*.nav-tabs li:not(:first-child) {
        margin-left: 10px !important;
        border-left: #737373 1px solid !important;
        padding-left: 10px !important;
    }
    .nav-tabs > li > button {
        margin: -2px 0 0 10px;
    }*/
    .nav-tabs li.active a {
        font-weight: bold;
    }

    .nav-tabs li.active a i.close-tab {
        cursor: pointer;
        display: unset !important;
        padding-left: 5px !important;
        border-left: 1px solid gray !important;
    }

</style>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="icon-pin font-green-haze"></i>
                    <span class="caption-subject bold font-green-haze uppercase"> Inventory Data Entry </span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="well">
                    <form id="frm_create_inventory_trn" method="post" action="<?php echo base_url();?>inventory/createtrn">
                        <input type="hidden" id="inv_trn_id" value="<?php echo ($active_qry) ? $active_qry->sysid : ''; ?>">
                        <div class="row">
                            <div class="col-md-4">
                                Transaction Type
                                <input class="form-control" name="trntype" id="inv_trn_type" value="<?php echo ($active_qry) ? $active_qry->trntype : ''; ?>" placeholder="Check-In/Out" required <?php echo ($active_qry) ? 'disabled' : ''; ?> />
                                <!--Transaction Date
                                <input type="date" class="form-control" name="trndate" id="inv_trn_date" max="<?php echo date('Y-m-d'); ?>" value="<?php echo ($active_qry) ? $active_qry->trndate : ''; ?>" placeholder="Transaction Date..." required <?php echo ($active_qry) ? 'disabled' : ''; ?> />
                                -->
                                <?php if ($active_qry) { ?>
                                    <button type="button" id="btn_cancel_inv_trn" class="btn btn-default margin-top-10" style="width: 100%" data-id="<?php echo ($active_qry) ? $active_qry->sysid : ''; ?>"> <i class="fa fa-times text-danger bold"></i> Cancel Transaction</button>
                                <?php } else { ?>
                                    <button type="submit" id="btn_new_inv_trn" class="btn btn-default margin-top-10" style="width: 100%"> <i class="fa fa-plus text-success bold"></i> New Transaction</button>
                                <?php } ?>
                            </div>
                            <div class="col-md-8">
                                Transaction Description
                                <textarea class="form-control" name="desc" placeholder="Enter remarks..." rows="6" style="resize: none" required <?php echo ($active_qry) ? 'disabled' : ''; ?>><?php echo ($active_qry) ? $active_qry->desc : ''; ?></textarea>
                            </div>
                        </div>
                    </form>
                    <div class="row margin-top-10">
                        <div class="col-md-3">
                            <div class="btn-group margin-top-20">
                                <button href="frm_add_inv_reference" id="btn_add_reference" data-toggle="ajax-modal" data-arr="" data-view="<?php echo ($active_qry) ? $active_qry->sysid : ''; ?>" title="Add Transaction References" class="btn btn-primary" <?php echo (!$active_qry) ? 'disabled' : ''; ?>><i class="fa fa-plus"></i> Add TRN References</button>
                            </div>
                        </div>
                        <div class="col-md-3 pull-right">
                            <div class="btn-group pull-right margin-top-20">
                                <button id="btn_submit_trn" class="btn btn-primary" data-id="<?php echo ($active_qry) ? $active_qry->sysid : ''; ?>" disabled><i class="fa fa-send"></i> Submit Transaction</button>
                            </div>
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
    INVENTORY.dataEntry();
    PECO.ellipsisExpand();
    ATTACHEMENTS.inventory(<?php echo ($active_qry) ? $active_qry->sysid : ''; ?>);
</script>
