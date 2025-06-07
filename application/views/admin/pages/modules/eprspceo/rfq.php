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

$remarks_qry = $this->db->select('remarks')
    ->from('eprs_transaction_logs')
    ->where(array(
        'prsid' => $dataid,
        'typesid' => 1207,
        'moduleid' => 193,
        'statusid' => 305,
        'status' => 1
    ))->get()->row();
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
                    <i class="fa fa-check font-green-haze"></i>
                    <span class="caption-subject bold font-green-haze uppercase"> Quotation Approval </span>
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
                                    <span class="label-name col-md-4 bold">PO #</span>
                                    <span class="col-md-8 font-blue-steel ">N/A</span>
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
                            <div class="btn-group">
                                <button id="btn_approve_rfq" data-flowid="<?php echo $flowid;?>" data-stageid="<?php echo $stageid;?>" data-trnid="<?php echo $trnid;?>" data-type="1207" class="btn btn-primary"><i class="fa fa-check"></i> Approve</button>
                                <button id="btn_disapprove_rfq" data-flowid="<?php echo $flowid;?>" data-stageid="<?php echo $stageid;?>" data-trnid="<?php echo $trnid;?>" data-type="1207" class="btn btn-danger"><i class="fa fa-times"></i> Disapprove</button>
                                <button id="btn_approve_rfq" data-flowid="<?php echo $flowid;?>" data-stageid="<?php echo $stageid;?>" data-trnid="<?php echo $trnid;?>" data-type="1207" class="btn btn-success"><i class="fa fa-undo"></i> Requote</button>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-striped" id="tbl_rfq_items">
                    <thead>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-md-3">
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
                                    <th>Total</th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                    <tr class="bold">
                                        <td style="padding: 8px !important;">Grand Total</td>
                                        <td id="gtotal" class="number" style="padding: 8px !important;">0.00</td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        Notes/Remarks:
                        <div class="form-group">
                            <div class="note note-info"><?php echo ($remarks_qry) ? $remarks_qry->remarks : 'No remarks posted.'; ?></div>
                        </div>
                        <hr>
                        Approvers' Notes/Remarks
                        <table class="table table-bordered table-striped" id="tbl_approver_remarks" data-type="1207">
                            <thead>
                            <th>Approver</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <h4 class="bold">Approval Status</h4>
                <div class="well">
                    <div class="row">
                        <?php
                        $approvers = array(
                            213 => 'Audit',
                            214 => 'General Manager',
                            215 => 'PCEO'
                        );

                        foreach ($approvers AS $module => $app) {
                            //Query if approval exists
                            $status = '<i class="fa fa-warning"></i> Pending</span>';
                            $current = ($module == $moduleid) ? 'Current: ' : '';
                            $label = 'warning';
                            $log = $this->db->select('statusid')
                                ->from('eprs_transaction_logs')
                                ->where(array(
                                    'prsid' => $dataid,
                                    'typesid' => 1207,
                                    'moduleid' => $module,
                                    'status' => 1
                                ))->get()->row();

                            if ($log) {
                                if ($log->statusid == 301) {
                                    $status = '<i class="fa fa-check"></i> Approved</span>';
                                    $label = 'success';
                                }
                                if ($log->statusid == 302) {
                                    $status = '<i class="fa fa-times"></i> Disapproved</span>';
                                    $label = 'danger';
                                }
                            }
                            ?>
                            <div class="col-md-4">
                                <ul class="list-group summary column no-border">
                                    <li class="list-group-item">
                                        <span class="label-name col-md-4 bold"><?php echo $app;?></span>
                                        <span class="col-md-8 font-blue-steel ">
                                        <span class="label label-<?php echo $label;?>" style="padding: 2px 5px !important; width: auto!important;"><?php echo $current.$status;?></span>
                                    </span>
                                    </li>
                                </ul>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/pages/eprs/main.js"></script>
<script>
    EPRS.rfqApproval(<?php echo $dataid;?>);
</script>
