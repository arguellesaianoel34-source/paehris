<?php

$prf_qry = $this->db->select()
    ->from('eprs_transaction')
    ->where('sysid',$dataid)
    ->get()->row();

if ($prf_qry) {
    $creator = get_users_info($prf_qry->createdby);
    $prfid = 'PRF'.date('ym',strtotime($prf_qry->datecreated)).str_pad($dataid,5,'0',STR_PAD_LEFT);
    $justification = ellipsis($prf_qry->justification,50);
    $requestor = ($creator) ? $creator->firstname.' '.$creator->lastname : 'N/A';
}

$trn_qry = $this->db->select()
    ->from('transaction_request_main')
    ->where('sysid',$trnflowid)
    ->get()->row();

if ($trn_qry) {
    $request_date = date('F j, Y',strtotime($trn_qry->datecreated));
}

?>
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
        /*padding-left: 25px !important;*/
        float: right !important;
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
                    <span class="caption-subject bold font-green-haze uppercase"> Purchase Request Approval </span>
                    <span class="caption-helper">more details..</span>
                </div>
            </div>
            <div class="portlet-body ">
                <div class="well">
                    <div class="row">
                        <div class="col-md-4">
                            <ul class="list-group summary column no-border">
                                <li class="list-group-item">
                                    <span class="label-name col-md-4 bold">PRF #</span>
                                    <span class="col-md-8 font-blue-steel "><?php echo $prfid; ?></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="label-name col-md-4 bold">Justification</span>
                                    <span class="col-md-8 font-blue-steel "><?php echo $justification; ?></span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <ul class="list-group summary column no-border">
                                <li class="list-group-item">
                                    <span class="label-name col-md-4 bold">Requested by</span>
                                    <span class="col-md-8 font-blue-steel "><?php echo $requestor; ?></span>
                                </li>
                                <li class="list-group-item">
                                    <span class="label-name col-md-4 bold">Request Date</span>
                                    <span class="col-md-8 font-blue-steel "><?php echo $request_date; ?></span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <ul class="list-group summary column no-border">

                            </ul>
                            <?php if ((!isset($trnview) || $trnview == false) && $prf_qry->status != 302) { ?>
                                <div class="btn-group">
                                    <button id="btn_approve_prf" data-flowid="<?php echo $flowid;?>" data-stageid="<?php echo $stageid;?>" data-trnid="<?php echo $trnid;?>" data-type="1206" class="btn btn-primary"><i class="fa fa-check"></i> Approve</button>
                                    <button id="btn_disapprove_prf" class="btn btn-danger" data-type="1206"><i class="fa fa-times"></i> Disapprove</button>
                                </div>

                                <div class="btn-group pull-right">
                                    <button id="btn_revise_prf" data-flowid="<?php echo $flowid;?>" data-stageid="<?php echo $stageid;?>" data-trnid="<?php echo $trnid;?>" data-type="1206" class="btn btn-success"><i class="fa fa-undo"></i> Revise</button>
                                </div>
                            <?php } else {
                                echo '<h4 class="text-danger"><i class="fa fa-times"></i> Disapproved</h4>';
                            } ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <button type="button" id="btn_refresh_item_list" class="btn btn-primary inline"><i class="fa fa-refresh"></i> Refresh</button>
                </div>
                <table class="table table-bordered table-hover table-striped" width="100%" id="tbl_po_items">
                    <thead>
                    <th width="25px">#</th>
                    <th>Item</th>
                    <th width="80px">Qty</th>
                    <th width="50px">Unit</th>
                    <th width="30%">Spec/Remarks</th>
                    <th width="100px">Controls</th>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo file_versioning('assets/pages/eprs/main.js'); ?>"></script>
<script>
    EPRS.approval(<?php echo $dataid; ?>,'prf');
    /**
     * LOGIC: load the budgets approved so please refer to these tables `bos_capex_sp` and `bos_opex`. You will use the `transaction_id` if you want to
     * refer to the whole transaction or the `budget_data_id` if you want to refer to a budget only and the `job_order_id` if you want to refer to the permanent
     * job order of the budget.
     * Difference from the BOS new:
     * 1. There's no add budget.
     * 2. all available budgets and items were there but they're uneditable for CAPEX.
     * 3. An ability to add items inside a budget when using SP.
     * 4. An ability to add items inside an OPEX budget.
     * 5. An ability to delete newly added budgets.
     * Procedure:
     * 1. Load the approved budgets based on the chosen budget-type.
     */
    /*$('#tbl_po_items', document).DataTable({
        bPaginate: false,
    });*/
</script>
