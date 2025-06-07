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

$remarks_qry = $this->db->select('remarks')
    ->from('eprs_transaction_logs')
    ->where(array(
        'prsid' => $dataid,
        'typesid' => 1207,
        'moduleid' => 193,
        'statusid' => 305,
        'status' => 1
    ))->order_by('datecreated ASC')->get()->row();
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
                    <i class="fa fa-quote-left font-green-haze"></i>
                    <span class="caption-subject bold font-green-haze uppercase"> PRF Quotations </span>
                    <span class="caption-helper"></span>
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
                                    <span class="label-name col-md-4 bold">PO #</span>
                                    <span class="col-md-8 font-blue-steel " id="po_number">N/A</span>
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
                                <li class="list-group-item">
                                    <span class="label-name col-md-4 bold">Justification</span>
                                    <span class="col-md-8 font-blue-steel "><?php echo $justification; ?></span>
                                </li>
                                <?php if ($prf_qry->status != 0) {
                                    if (in_array($prf_qry->status,array(302,303))) {
                                        $status = get_types_name($prf_qry->status)->names;
                                        echo '<h4 class="text-danger"><i class="fa fa-times"></i> '.$status.' : '.date('M-d-Y',strtotime($prf_qry->dateupdated)).'</h4>';
                                    } else {
                                ?>
                                        <div class="btn-group">
                                            <button id="btn_cancel_rfq" data-flowid="<?php echo $flowid;?>" data-stageid="<?php echo $stageid;?>" data-trnid="<?php echo $trnid;?>" data-type="1207" class="btn btn-danger"><i class="fa fa-times"></i> Cancel PRF</button>
                                        </div>
                                <?php
                                    }
                                } else {
                                    echo '<h4 class="text-danger"><i class="fa fa-times"></i> Canceled : '.date('M-d-Y',strtotime($prf_qry->dateupdated)).'</h4>';
                                } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="btn-group">
                        <?php if (!in_array($prf_qry->status,array(0,302,303))) { ?>
                            <a href="frm_supplier_quotations" data-toggle="ajax-modal" data-arr="<?php echo $dataid;?>" title="Add Supplier Quotations" class="btn btn-primary inline"><i class="fa fa-plus"></i> Add Supplier</a>
                            <button class="btn btn-success inline" id="btn_refresh_rfq_item_list"><i class="fa fa-refresh"></i> Refresh</button>
                            <a href="frm_add_prf_items" data-toggle="ajax-modal" data-arr="<?php echo $dataid.(($trnid) ? ','.$trnid : '');?>" title="Add Item To Request" class="btn btn-primary inline"><i class="fa fa-shopping-cart"></i> Add Item</a>
                        <?php } ?>
                    </div>
                </div>
                <form id="frm_submit_quotation" action="<?php echo base_url();?>purchasing/saveprfquotation" method="post">
                    <input type="hidden" name="prfid" value="<?php echo $dataid;?>">
                    <input type="hidden" name="flowid" value="<?php echo $flowid;?>">
                    <input type="hidden" name="stageid" value="<?php echo $stageid;?>">
                    <input type="hidden" name="trnid" value="<?php echo $trnid;?>">
                    <input type="hidden" name="typesid" value="1207">
                    <table class="table table-bordered table-condensed table-striped table-sm" id="tbl_rfq_items">
                        <thead>
                        <tr id="tr_headers">
                            <th rowspan="2">#</th>
                            <th rowspan="2">Items</th>
                            <th rowspan="2">Last Price</th>
                            <th rowspan="2">Qty</th>
                            <th rowspan="2">Unit</th>
                            <th id="suppliers_label" class="bg-color-blue text-align-center" style="color: white !important;" colspan="">Suppliers' Quotations</th>

                            <th rowspan="2" id="supplier_remarks">Remarks</th>
                            <th rowspan="2">Control</th>
                        </tr>
                        <tr id="suppliers_quote"></tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                        <tr>
                            <td id="subtotal_label" class="text-default" style="padding: 8px !important; text-align: left !important;" colspan="5">Subtotal</td>
                            <td id="subtotal_amt" class="subtotal number" style="padding: 8px !important;">0.00</td>
                            <td id="blank_remarks" class="number" style="padding: 8px !important;">

                            </td>
                            <td class="text-align-center">

                            </td>
                        </tr>
                        <!--<tr>
                            <td id="buffer_label" class="text-danger" style="padding: 8px !important; text-align: left !important;" colspan="5">Buffer 2% incase of price increase</td>
                            <td id="buffer_amt" class="number" style="padding: 8px !important;">0.00</td>
                            <td class="number" style="padding: 8px !important;">

                            </td>
                            <td class="text-align-center">

                            </td>
                        </tr>-->
                        </tfoot>
                    </table>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="portlet light bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        Summary of Cost
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <table class="table table-bordered table-striped" id="tbl_cost_summary">
                                        <thead>
                                        <th>Supplier</th>
                                        <th>Net of VAT</th>
                                        <th>12% VAT</th>
                                        <th>Gross</th>
                                        <th>EWT</th>
                                        <th>Shipping (Estimate)</th>
                                        <th>Total</th>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <!-- <tfoot>
                                        <tr class="bold">
                                            <td class="text-danger" style="padding: 8px !important; text-align: left !important;" colspan="6">Buffer 2% incase of price increase</td>
                                            <td id="buffer" class="number" style="padding: 8px !important;">0.00</td>
                                        </tr>
                                        <tr class="bold">
                                            <td style="padding: 8px !important;" colspan="6">Grand Total w/ Shipping Fee </td>
                                            <td id="gtotal" class="number" style="padding: 8px !important;">0.00</td>
                                        </tr>
                                        </tfoot> -->
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            Quotation Attachments
                            <table class="table table-bordered table-striped" id="tbl_rfq_attachments" data-folder="<?php echo 'eprs/pastpurchases/' . str_pad($dataid, 6, "0", STR_PAD_LEFT) . '/'; ?>" style="width: 100% !important;" data-text="12">
                                <thead>
                                <th>#</th>
                                <th>File</th>
                                <th>View</th>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12">
                            Notes/Remarks:
                            <?php if (!in_array($prf_qry->status,array(0,302,303))) { ?>
                            <div class="form-group">
                                <textarea class="form-control" rows="2" name="rfqremarks" placeholder="Notes/Remarks..."><?php echo ($remarks_qry) ? $remarks_qry->remarks : '';?></textarea>
                            </div>
                            <div class="btn-group pull-right">
                                <button class="btn btn-primary "><i class="fa fa-check"></i> Submit</button>
                            </div>
                            <?php } else { ?>
                                <div class="form-group">
                                    <div class="note note-info"><?php echo ($remarks_qry) ? $remarks_qry->remarks : 'No remarks posted.'; ?></div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo file_versioning('assets/pages/eprs/main.js'); ?>" ></script>
<script>
    EPRS.rfq(<?php echo $dataid;?>);
</script>
