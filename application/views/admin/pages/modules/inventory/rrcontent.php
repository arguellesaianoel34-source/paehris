<?php
$supplier_details = $this->db->select('s.name, s.tin, sa.address, sod.name AS accountname, sod.bank, sod.accountnum, qs.exvat, qs.rfop, qs.exrate, qs.shipping, s.currency, qs.paytype, s.type')
    ->from('eprs_suppliers_main AS s')
    ->join('eprs_quotation_suppliers AS qs','s.sysid = qs.supplierid','left')
    ->join('eprs_suppliers_address AS sa','s.sysid = sa.supplierid','left')
    ->join('eprs_suppliers_online_details AS sod','s.sysid = sod.supplierid AND sod.status = 1','left')
    ->where(array('qs.sysid' => $refid,'s.status' => 1))
    ->get()->row();

$po_details = $this->db->select('po.sysid as poid,po.ponumber,qd.sysid,qd.paytype,qd.payterm,qd.purpose,qd.notes')
    ->from('eprs_po_details as qd')
    ->join('eprs_po as po','qd.poid = po.sysid','left')
    ->where(array('qd.quotationid' => $refid,'qd.status' => 1))
    ->get()->row();

if ($po_details) {
    $po_count = $this->db->select('COUNT(po.sysid) as cnt')
        ->from('eprs_po_details as po')
        ->where(array('po.sysid <=' => $po_details->sysid, 'po.status' => 1))
        ->get()->row();
}

$trn_qry = $this->db->select('')
    ->from('inventory_transaction_reference')
    ->where('sysid', $trnref)->get()->row();
?>

<div class="portlet light">
    <div class="portlet-title">
        <div class="caption" style="width: 100%">
            <div class="row">
                <div class="col-md-2">
                    <span class="">PO/RFOP No.: <?php echo (isset($po_count)) ? $po_count->cnt : 'TBA' ?></span>
                </div>
                <div class="col-md-7 text-align-center">
                    <span class="bold"><?php echo ($supplier_details) ? $supplier_details->name : ''; ?></span>
                </div>
                <div class="col-md-3">
                    <?php if ($trn_qry && $trn_qry->createdby == user_id()) {?>
                        <a id="btn_delete_reference" class="btn btn-danger btn-sm pull-right"><i class="fa fa-times" style="font-size: 18px !important;" data-id="<?php echo $trnref; ?>"></i></a>
                    <?php } ?>
                    <span class="pull-right margin-right-10">RR Date: <?php echo date('m-d-Y',strtotime($trndate)); ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-condensed" id="<?php echo $tableid; ?>" data-id="<?php echo $refid; ?>" data-type="<?php echo $trntype; ?>" data-trn="<?php echo $trn_qry->groupid; ?>" style="width: 100% !important;">
                    <thead>
                    <th>#</th>
                    <th>Item Description</th>
                    <th>Unit</th>
                    <th>Qty Ordered</th>
                    <th>Qty Received</th>
                    <th>Remarks</th>
                    <?php if ($trn_qry && $trn_qry->createdby == user_id()) {?>
                        <th>Controls</th>
                    <?php } ?>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
