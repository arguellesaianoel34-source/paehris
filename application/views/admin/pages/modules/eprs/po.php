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

$po = $this->db->select('ponumber as number')
    ->from('eprs_po')
    ->where(array('prfid' => $dataid,'status' => 1))
    ->get()->row();

if ($po) {
    $ponum = 'PAE-'.str_pad($po->number,8,'0',STR_PAD_LEFT);
    $hide = 'hidden';
} else {
    $ponum = 'N/A';
    $hide = '';
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
                    <i class="icon-pin font-green-haze"></i>
                    <span class="caption-subject bold font-green-haze uppercase"> Purchase Order Generation </span>
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
                                    <span class="col-md-8 font-blue-steel " id="po_number"><?php echo $ponum; ?></span>
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
                            </ul>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-striped table-condensed" id="tbl_po_suppliers" data-view="1">
                    <thead>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<script src="<?php echo file_versioning('assets/pages/eprs/main.js'); ?>"></script>
<script>
    EPRS.purchaseOrder(<?php echo $dataid; ?>)
</script>

