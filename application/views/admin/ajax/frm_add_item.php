<?php

?>
<form id="frm_add_new_item" action="<?php echo base_url(); ?>query/addnewitem" method="post">
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <h4>Item Details</h4>
            </div>
            <div class="col-md-9">
                <div class="form-group">
                    <label for="item_select">Full Item Description
                        <a class="" href="javascript:;" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-title="Specification Example" data-content="Querty Keyboard Mechanical w/ Num Pads, Gaming Mouse RoG."><i class="fa fa-question"></i></a>
                    </label>
                    <div class="input-icon">
                        <i class="fa fa-tag"></i>
                        <input class="form-control input-reset" id="item_spec_search" placeholder="Specification" required name="specifications" />
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="item_select">Default Unit</label>
                    <div class="input-icon">
                        <i class="fa fa-gears"></i>
                        <input class="form-control input-reset" id="item_unit" placeholder="Unit" required name="unit" />
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <h4>Last Purchase Details <span class="small">(Optional)</span> <input type="checkbox" class="icheck" id="icheck_last_purchase" value="1"></h4>
            </div>
        </div>
        <div class="row" id="purchase_details">
            <div class="col-md-5">
                <div class="form-group">
                    <label for="item_select">Supplier</label>
                    <div class="input-icon">
                        <i class="fa fa-shopping-cart"></i>
                        <input class="form-control input-reset" id="last_purchase_supplier" placeholder="Supplier Name" required name="supplier" disabled />
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="item_select">PO Number</label>
                    <div class="input-icon">
                        <i class="fa fa-hashtag"></i>
                        <input class="form-control input-reset" id="item_po_num" placeholder="PO#" required name="ponum" disabled />
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="item_select">Price</label>
                    <div class="input-icon">
                        <i class="fa fa-money"></i>
                        <input class="form-control input-reset" id="item_last_price" placeholder="Last Price" required name="amount" disabled />
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="reset" class="btn btn-danger"><i class="fa fa-refresh"></i> Reset</button>
            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
        </div>
    </div>
</form>

<script src="<?php echo base_url(); ?>assets/pages/eprs/modal.js"></script>
<script type="text/javascript">
    EPRS_M.addItem();
</script>