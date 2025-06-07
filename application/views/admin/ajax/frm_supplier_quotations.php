<?php
$ids = $this->input->post('ids');
$supplierid = false;
$appid = false;
if (strpos($ids,',') !== false) {
    list($appid,$supplierid) = explode(',',$ids);
} else {
    $appid = $ids;
}

$button = '';
$action = '';

if ($supplierid) {
    $button .= '<i class="fa fa-save"></i> Update Quotations';
    $action = base_url().'purchasing/updatesupplierquotation';
} else {
    $button .= '<i class="fa fa-plus"></i> Add Quotations';
    $action = base_url().'purchasing/addsupplierquotation';
}

$supplier_details = $this->db->select('s.name, s.tin, sa.address, sod.name AS accountname, sod.bank, sod.accountnum')
    ->from('eprs_suppliers_main AS s')
    ->join('eprs_quotation_suppliers AS qs','s.sysid = qs.supplierid','left')
    ->join('eprs_suppliers_address AS sa','s.sysid = sa.supplierid','left')
    ->join('eprs_suppliers_online_details AS sod','s.sysid = sod.supplierid AND sod.status = 1','left')
    ->where(array('qs.sysid' => $appid,'s.status' => 1))
    ->get()->row();

//SUPPLIER SELECTION (NOT EXISTING IN CURRENT SUPPLIER LIST)
//LIST ITEMS FOR QUOTATION
//INCLUDE LAST QUOTED PRICE FOR ITEM
//CHECK IF SAME PRICE AS PREVIOUS QUOTATION
//DISABLE TEXTBOX IF CHECKED
//ADD CURRENCY FOR SUPPLIERS OUTSIDE OF THE COUNTRY
?>
<div class="modal-body">
    <form id="frm_add_quotations" action="<?php echo $action; ?>" method="post">
        <div class="row">
            <?php if ($supplierid) {
                $supplier = $this->db->select('esm.name,eqs.rfop,eqs.paytype')
                    ->from('eprs_quotation_suppliers as eqs')
                    ->join('eprs_suppliers_main as esm','eqs.supplierid = esm.sysid','left')
                    ->where('eqs.sysid',$supplierid)->get()->row();

                if ($supplier) {
                    echo '<div class="col-md-6">';
                    echo '<h4 class="bold">'.$supplier->name.'</h4>';
                    echo '<input type="hidden" class="form-control" placeholder="Select Supplier" name="supplier" value="'.$supplierid.'">';
                    echo '</div>';

                    echo '<div class="col-md-4">';
                    echo '<h4 class="bold pull-right">RFOP#:</h4>';
                    echo '</div>';
                    echo '<div class="col-md-2">';
                    if ($supplier->rfop) {
                        echo '<h4>'.$supplier->rfop.'</h4>';
                    } else {
                        echo '<input type="number" class="form-control" id="rfop_no" placeholder="RFOP#" name="rfop">';
                    }
                    echo '</div>';

                }
                ?>
            <?php } else { ?>
                <div class="col-md-2">
                    Select Supplier
                </div>
                <div class="col-md-6">
                    <input class="form-control" id="select2_supplier" placeholder="Select Supplier" name="supplier">
                </div>
                <div class="col-md-2">
                    <span class="pull-right">RFOP#:</span>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" id="rfop_no" placeholder="RFOP#" name="rfop" >
                </div>
            <?php } ?>
        </div>
        <div class="row hidden" style="padding: 25px" id="quotation_supplier_details">
            <div class="col-md-12">
                <h4 class="bold">Payment Details</h4>
                <div class="row">
                    <!-- SAVED PO DETAILS -->
                    <div class="col-md-4">
                        Payment Type
                        <input class="form-control" id="select2_paytype" name="paytype" value="">
                    </div>
                    <div class="col-md-4">
                        Payment Term
                        <input class="form-control" id="rfp_payment_term" name="paymentterm" value="">
                    </div>
                    <div class="col-md-4">
                        Purpose/Description
                        <textarea class="form-control" id="rfp_purpose" name="purpose" rows="1"></textarea>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        Notes:
                        <textarea class="form-control" id="rfp_notes" name="ponotes" rows="2" placeholder="Notes and remarks here..."></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="row margin-top-20">
            <div class="col-md-6" id="exchange_rate">

            </div>
        </div>
        <div class="row margin-top-20">
            <div class="col-md-12">
                <input type="hidden" name="appid" value="<?php echo $appid;?>">

                <table width="100%" class="table table-bordered table-striped" id="tbl_add_item_quotations">
                    <thead>
                    <th>#</th>
                    <th>Items</th>
                    <th>Last Quote</th>
                    <th>Quoted Amt</th>
                    <th><i class="fa fa-check-square fa-lg"></i> Same as Previous</th>
                    <th>Spec/Remarks</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <div class="row">
                <div class="col-md-4 text-align-left">
                    <input type="checkbox" class="icheck" id="icheck_exvat" name="exvat" value="1"> VAT-Ex
                </div>

                <div class="col-md-4">
                    <?php if (!$supplierid) { ?>
                        <button type="button" class="btn btn-success" id="export_quotation_sheet" disabled><i class="fa fa-file-excel-o"></i> Export Quotation Sheet</button>
                    <?php } ?>
                </div>

                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary pull-right" id="btn_add_selected_requirements"><?php echo $button; ?></button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    EPRS.supplierQuote(<?php echo strpos($ids,',') ? $ids : $appid;?>);
</script>
