<?php
$id = $this->input->post('ids');


$supplier_details = $this->db->select('s.name, s.tin, sa.address, sod.name AS accountname, sod.bank, sod.accountnum')
    ->from('eprs_suppliers_main AS s')
    ->join('eprs_quotation_suppliers AS qs','s.sysid = qs.supplierid','left')
    ->join('eprs_suppliers_address AS sa','s.sysid = sa.supplierid','left')
    ->join('eprs_suppliers_online_details AS sod','s.sysid = sod.supplierid AND sod.status = 1','left')
    ->where(array('qs.sysid' => $id,'s.status' => 1))
    ->get()->row();

//echo $this->db->last_query();

$po_details = $this->db->select('paytype,payterm,purpose,notes')
    ->from('eprs_po_details')
    ->where(array('quotationid' => $id,'status' => 1))
    ->get()->row();
?>

<div class="row" style="padding: 25px">
    <div class="col-md-12">
        <form action="<?php echo base_url();?>purchasing/savepaymentrequest" method="post" id="frm_paymen_request">
            <input type="hidden" name="quotedsupplier" value="<?php echo $id;?>">
            <h4 class="bold">Payment Details</h4>
            <div class="row">
                <div class="col-md-5">
                    For (Payee)
                    <br>
                    <div id="payment_name" class="bold">
                        <?php echo ($supplier_details) ? $supplier_details->name : 'X'; ?>
                    </div>
                </div>
                <div class="col-md-5">
                    Address
                    <div id="payment_address" class="bold">
                        <?php echo ($supplier_details) ? $supplier_details->address : 'X'; ?>
                    </div>
                </div>
                <div class="col-md-2">
                    TIN
                    <div id="supplier_tax_no" class="bold">
                    <?php
                    if ($supplier_details && $supplier_details->tin != '') {
                        echo '<i>'.$supplier_details->tin.'</i>';
                    } else {
                        echo '<input class="form-control" id="supplier_tin" name="suppliertin" required>';
                    }
                    ?>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    Payment Type
                    <input class="form-control" id="rfp_payment_type" name="paymenttype" value="<?php echo ($po_details) ? $po_details->paytype : ''; ?>">
                </div>
                <div class="col-md-4">
                    Payment Term
                    <input class="form-control" id="rfp_payment_term" name="paymentterm" value="<?php echo ($po_details) ? $po_details->payterm : ''; ?>">
                </div>
                <div class="col-md-4">
                    Purpose/Description
                    <textarea class="form-control" id="rfp_purpose" name="purpose" rows="1"><?php echo ($po_details) ? $po_details->purpose : ''; ?></textarea>
                </div>
            </div>
            <hr>
            <h4 class="bold">Online Payment Account Details <?php echo ($supplier_details->accountnum == '') ? '<small>(Add Online Payment Details)</small>' : '' ?></h4>
            <div class="row">
                <div class="col-md-4">
                    Name <span></span>
                    <div>
                    <?php
                    if ($supplier_details && $supplier_details->accountname != '') {
                        echo $supplier_details->accountname;
                    } else {
                        echo '<input class="form-control" id="rfp_account_name" name="accountname">';
                    }
                    ?>
                    </div>
                </div>
                <div class="col-md-4">
                    Bank <span></span>:
                    <div>
                    <?php
                    if ($supplier_details && $supplier_details->bank != '') {
                        echo $supplier_details->bank;
                    } else {
                        echo '<input class="form-control" id="rfp_account_bank" name="accountbank">';
                    }
                    ?>
                    </div>
                </div>
                <div class="col-md-4">
                    Account Number <span></span>:
                    <div>
                    <?php
                    if ($supplier_details && $supplier_details->accountnum != '') {
                        echo $supplier_details->accountnum;
                    } else {
                        echo '<input class="form-control" id="rfp_account_bank" name="accountnumber">';
                    }
                    ?>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    Notes:
                    <textarea class="form-control" id="rfp_notes" name="ponotes" rows="2" placeholder="Notes and remarks here..."><?php echo ($po_details) ? $po_details->notes : ''; ?></textarea>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary pull-right">Submit</button>
        </form>
    </div>
</div>
<script type="text/javascript">
    EPRS.payment();
</script>