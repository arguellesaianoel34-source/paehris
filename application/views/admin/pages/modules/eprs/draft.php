<?php

$justval = '';

$prf_qry = $this->db->select()
    ->from('eprs_transaction')
    ->where('sysid',$dataid)
    ->get()->row();

if ($prf_qry) {
    $creator = get_users_info($prf_qry->createdby);
    $prfid = 'PRF'.date('ym',strtotime($prf_qry->datecreated)).str_pad($dataid,5,'0',STR_PAD_LEFT);
    $justval = $prf_qry->justification;
    $requestor = ($creator) ? $creator->firstname.' '.$creator->lastname : 'N/A';
}
?>

<div class="row">
    <div class="col-md-2">
        <h4><i class="fa fa-comment-o"></i> Remarks</h4>
        <hr>
        <div class="form-group">
            <textarea disabled rows="10" name="remarks" class="form-control" placeholder="Enter justification here." id="prf_justification" required><?php echo $justval; ?></textarea>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
            <button type="button" class="btn btn-primary btn-sm" id="btn_edit_justification"><i class="fa fa-edit"></i> Edit</button>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="portlet light table">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <?php if (isset($prfid)) { ?>
                        <i class="icon-pin font-red-flamingo"></i>
                        <span class="caption-subject bold font-red-flamingo uppercase"> <?php echo $prfid; ?> </span>
                    <?php } else { ?>
                        <i class="icon-pin font-green-haze"></i>
                        <span class="caption-subject bold font-green-haze uppercase"> Breakdown </span>
                    <?php } ?>
                    <span class="caption-helper">more details..</span>
                </div>
            </div>
            <div class="portlet-body">
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
                            <div class="col-md-3 pull-right">
                                <div class="btn-group pull-right margin-top-20">
                                    <button type="reset" class="btn btn-default"> <i class="fa fa-refresh"></i> Reset</button>
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Add</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-condensed table-hover table-striped" id="tbl_po_items" width="100%">
                            <thead>
                            <th width="25px">#</th>
                            <th>Item</th>
                            <th width="80px">Qty</th>
                            <th width="50px">Unit</th>
                            <th width="30%">Remarks/Specs</th>
                            <th width="10px">Controls</th>
                            </thead>
                            <tbody></tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="btn-group">
            <button class="btn btn-primary btn-block" data-id="<?php echo $dataid; ?>" id="btn_prf_approval">
                Send for Approval
            </button>
            <button class="btn btn-danger btn-block" data-id="<?php echo $dataid; ?>" id="btn_prf_delete">
                Discard
            </button>
        </div>
        <hr>
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
</script>
