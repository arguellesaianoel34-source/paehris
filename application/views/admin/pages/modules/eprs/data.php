<?php

$prf_qry = $this->db->select()
    ->from('eprs_transaction')
    ->where('sysid',$dataid)
    ->get()->row();

if ($prf_qry) {
    $creator = get_users_info($prf_qry->createdby);
    $prfid = 'PRF'.date('ym',strtotime($prf_qry->datecreated)).str_pad($dataid,5,'0',STR_PAD_LEFT);
    $justification = $prf_qry->justification;
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
                    <span class="caption-subject bold font-green-haze uppercase"> Purchase Request Form </span>
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
                                    <span class="col-md-8 font-blue-steel ">
                                        <textarea class="col-md-8 form-control" rows="4" cols="10"><?php echo $justification; ?></textarea>
                                    </span>
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
                            <div class="btn-group">
                                <button id="btn_approve_prf" data-flowid="<?php echo $flowid;?>" data-stageid="<?php echo $stageid;?>" data-trnid="<?php echo $trnid;?>" data-type="1206" class="btn btn-primary"><i class="fa fa-check"></i> Send for Approval</button>
                                <button id="btn_prf_delete" class="btn btn-danger"><i class="fa fa-times"></i> Delete PRF</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="well">
                    <form id="frm_add_prf_item" method="post" action="<?php echo base_url();?>purchasing/addprfitem">
                        <input type="hidden" name="itemid" id="itemid">
                        <div class="row">
                            <div class="col-md-6">
                                Search Item
                                <input class="form-control" id="item_desc" placeholder="Item name / code" />
                            </div>
                            <div class="col-md-3">
                                Qty
                                <input class="form-control" name="qty" placeholder="Qty..." />
                            </div>
                            <div class="col-md-3">
                                Unit
                                <input class="form-control" name="unitid" id="unitid" placeholder="Unit..." />
                            </div>
                        </div>
                        <div class="row margin-top-10">
                            <div class="col-md-12">
                                <label class="control-label">
                                    Remarks
                                </label>
                                <textarea class="form-control" name="remarks" placeholder="Enter remarks..." rows="1" ></textarea>
                            </div>
                            <div class="col-md-3">
                                <div class="btn-group margin-top-20">
                                    <a href="#frm_add_item" data-toggle="ajax-modal" title="Load Past Request" class="btn btn-default"><i class="fa fa-plus"></i> New Item</a>
                                </div>
                            </div>
                            <div class="col-md-3 pull-right">
                                <div class="btn-group pull-right margin-top-20">
                                    <button type="reset" class="btn btn-default"> <i class="fa fa-refresh"></i> Reset</button>
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-cart-plus"></i> Add</button>
                                </div>
                            </div>
                        </div>
                    </form>
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
    EPRS.new(<?php echo $dataid; ?>);
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
