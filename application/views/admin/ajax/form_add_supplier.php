<?php

?>
<form action="<?php echo base_url();?>purchasing/savenewsupplier" method="post" id="frm_new_supplier">
    <div class="modal-body">
        <div class="row" style="padding: 25px">
            <div class="col-md-12">
                <h4 class="bold">Supplier Details</h4>
                <div class="row">
                    <div class="col-md-2 control-group">
                        <label class="control-label" for="supplier_code">Code</label> <span class="required"></span>
                        <input class="form-control" id="supplier_code" name="suppliercode" data-location="main" data-field="codes" required>
                    </div>
                    <div class="col-md-5 control-group">
                        <label class="control-label" for="supplier_name">Full Official Name</label> <span class="required"></span>
                        <input class="form-control" id="supplier_name" name="suppliername" data-location="main" data-field="name" required>
                    </div>
                    <div class="col-md-3 control-group">
                        <label class="control-label" for="supplier_desc">Short Name</label> <span class="required"></span>
                        <input class="form-control" id="supplier_desc" name="supplierdesc" data-location="main" data-field="descs" required>
                    </div>
                    <div class="col-md-2 control-group">
                        <label class="control-label" for="supplier_desc">Currency</label>
                        <input class="form-control" id="supplier_currency" name="suppliercurrency" data-field="currency">
                    </div>
                </div>
                <hr>
                <h4 class="bold">Address, Contact and TIN</h4>
                <div class="row">
                    <div class="col-md-5 control-group">
                        <label class="control-label" for="supplier_address">Address</label> <span class="required"></span>
                        <input class="form-control" id="supplier_address" name="supplieraddress" required>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label" for="supplier_tin">TIN</label>
                        <input class="form-control" id="supplier_tin" name="suppliertin" data-location="main" data-field="tin" >
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 control-group">
                        <label class="control-label" for="supplier_phone">Phone</label> <span class="required"></span>
                        <input class="form-control" id="supplier_phone" name="supplierphone" data-location="contact" data-type="1050" required>
                    </div>
                    <div class="col-md-4 control-group">
                        <label class="control-label" for="supplier_mobile">Mobile</label> <span class="required"></span>
                        <input class="form-control" id="supplier_mobile" name="suppliermobile" data-location="contact" data-type="1051" required>
                    </div>
                    <div class="col-md-4">
                        <label class="control-label" for="supplier_email">E-Mail</label>
                        <input class="form-control" id="supplier_email" name="supplieremail" data-location="contact" data-type="1053" >
                    </div>
                </div>
                <hr>
                <h4 class="bold">Online Payment Account Details <small>(Add Online Payment Details)</small></h4>
                <div class="row">
                    <div class="col-md-4">
                        <label class="control-label" for="rfp_account_name">Name</label> <span></span>
                        <input class="form-control" id="rfp_account_name" name="accountname">
                    </div>
                    <div class="col-md-4">
                        <label class="control-label" for="rfp_account_bank">Bank</label> <span></span>
                        <input class="form-control" id="rfp_account_bank" name="accountbank">
                    </div>
                    <div class="col-md-4">
                        <label class="control-label" for="rfp_account_number">Account Number</label> <span></span>
                        <input class="form-control" id="rfp_account_number"  name="accountnumber">
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
    SUPPLIER.new();
</script>