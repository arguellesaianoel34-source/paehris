<?php
$id = $this->input->post('ids');
//LOOLUP SUPPLIER DETAILS
$supplier_qry = $this->db->select('esm.sysid, esm.codes, esm.name, esm.descs, esm.tin, esm.currency, esod.name AS accountname, esod.bank, esod.accountnum, esa.address')
    ->from('eprs_suppliers_main AS esm')
    ->join('eprs_suppliers_online_details AS esod','esm.sysid = esod.supplierid AND esod.status = 1','left')
    ->join('eprs_suppliers_address AS esa','esm.sysid = esa.supplierid AND esa.status = 1','left')
    ->where(array('esm.sysid' => $id,'esm.status' => 1))->get()->row();

if ($supplier_qry) {
    $supplier_contact = $this->db->select('typesid,contact')
        ->from('eprs_suppliers_contact')
        ->where(array('supplierid' => $supplier_qry->sysid,'status' => 1))
        ->get();

    if ($supplier_contact->num_rows() > 0) {
        foreach ($supplier_contact->result() AS $contact) {
            if ($contact->typesid == 1050) {
                $phone = $contact->contact;
            }

            if ($contact->typesid == 1051) {
                $mobile = $contact->contact;
            }

            if ($contact->typesid == 1053) {
                $mail = $contact->contact;
            }
        }
    }
}

?>
<form action="<?php echo base_url();?>purchasing/updatesupplierdetails" method="post" id="frm_new_supplier">
    <div class="modal-body">
        <div class="row" style="padding: 25px">
            <div class="col-md-12">
                <h4 class="bold">Supplier Details</h4>
                <input type="hidden" value="<?php echo $supplier_qry->sysid ?? ''; ?>" name="supplierid">
                <div class="row">
                    <div class="col-md-2 control-group">
                        <label class="control-label" for="supplier_code">Code</label> <span class="required"></span>
                        <input class="form-control" id="supplier_code" name="suppliercode" data-location="main" data-field="codes" value="<?php echo $supplier_qry->codes ?? ''; ?>" required>
                    </div>
                    <div class="col-md-5 control-group">
                        <label class="control-label" for="supplier_name">Full Official Name</label> <span class="required"></span>
                        <input class="form-control" id="supplier_name" name="suppliername" data-location="main" data-field="name" value="<?php echo $supplier_qry->name ?? ''; ?>" required>
                    </div>
                    <div class="col-md-3 control-group">
                        <label class="control-label" for="supplier_desc">Short Name</label> <span class="required"></span>
                        <input class="form-control" id="supplier_desc" name="supplierdesc" data-location="main" data-field="descs" value="<?php echo $supplier_qry->descs ?? ''; ?>" required>
                    </div>
                    <div class="col-md-2 control-group">
                        <label class="control-label" for="supplier_desc">Currency</label>
                        <input class="form-control" id="supplier_currency" name="suppliercurrency" data-field="currency">
                    </div>
                </div>
                <hr>
                <h4 class="bold">Address, Contact and TIN</h4>
                <div class="row">
                    <div class="col-md-8 control-group">
                        <label class="control-label" for="supplier_address">Address</label> <span class="required"></span>
                        <input class="form-control" id="supplier_address" name="supplieraddress" value="<?php echo $supplier_qry->address ?? ''; ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="control-label" for="supplier_tin">TIN</label>
                        <input class="form-control" id="supplier_tin" name="suppliertin" value="<?php echo $supplier_qry->tin ?? ''; ?>" data-location="main" data-field="tin" >
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 control-group">
                        <label class="control-label" for="supplier_phone">Phone</label> <span class="required"></span>
                        <input class="form-control" id="supplier_phone" name="supplierphone" value="<?php echo $phone ?? ''; ?>" data-location="contact" data-type="1050" required>
                    </div>
                    <div class="col-md-4 control-group">
                        <label class="control-label" for="supplier_mobile">Mobile</label> <span class="required"></span>
                        <input class="form-control" id="supplier_mobile" name="suppliermobile" value="<?php echo $mobile ?? ''; ?>" data-location="contact" data-type="1051" required>
                    </div>
                    <div class="col-md-4">
                        <label class="control-label" for="supplier_email">E-Mail</label>
                        <input class="form-control" id="supplier_email" name="supplieremail" value="<?php echo $mail ?? ''; ?>" data-location="contact" data-type="1053" >
                    </div>
                </div>
                <hr>
                <h4 class="bold">Online Payment Account Details <small>(Add Online Payment Details)</small></h4>
                <div class="row">
                    <div class="col-md-4">
                        <label class="control-label" for="rfp_account_name">Name</label> <span></span>
                        <input class="form-control" id="rfp_account_name" name="accountname" value="<?php echo $supplier_qry->accountname ?? ''; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="control-label" for="rfp_account_bank">Bank</label> <span></span>
                        <input class="form-control" id="rfp_account_bank" name="accountbank" value="<?php echo $supplier_qry->bank ?? ''; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="control-label" for="rfp_account_number">Account Number</label> <span></span>
                        <input class="form-control" id="rfp_account_number"  name="accountnumber" value="<?php echo $supplier_qry->accountnum ?? ''; ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <span class="pull-left" id="validation_result"></span>
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
    </div>
</form>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript">
    PECO.select2Basic($('#supplier_currency',document),'systems/select2currency','Select Currency...',false,false,<?php echo $supplier_qry->currency ?? false; ?>);
    SUPPLIER.update();
</script>