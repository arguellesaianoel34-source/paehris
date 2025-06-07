<?php
//BLOODHOUND PO NUMBER
?>
<form id="frm_add_reference" action="<?php echo base_url(); ?>inventory/addreference" method="post" autocomplete="off">
    <input type="hidden" name="trngroupid" value="<?php echo $trnid; ?>">
    <input type="hidden" name="trntype" value="<?php echo $trntype; ?>">
    <input type="hidden" id="refid" name="refid">
    <div class="row margin-bottom-15">
        <div class="col-md-5">
            Select PO Number
            <input class="form-control" name="ponumber" id="inv_reference_po" required>
        </div>
        <div class="col-md-5">
            Receiving Date
            <input type="date" class="form-control" name="trndate" id="inv_rr_date" max="<?php echo date('Y-m-d'); ?>" value="" placeholder="Receiving Date..." required />
        </div>
        <div class="col-md-2 align-text-bottom" style="height: 54px">
            <button type="submit" id="btn_add_po" class="btn btn-primary" style="position: absolute; bottom: 0" disabled><i class="fa fa-download"></i> Add PO</button>
        </div>
    </div>
    <hr>
    <div class="portlet light bordered">
        <div class="portlet-title">
            <div class="caption bold">Supplier: <span id="po_supplier_name" class="font-red-flamingo"></span></div>
        </div>
        <div class="portlet-body">
            <div class="row">
                <div class="col-md-9">
                    <b>Address:</b>
                    <span id="po_supplier_address"></span>
                </div>
                <div class="col-md-3">
                    <b>Items:</b>
                    <span id="po_supplier_item"></span>
                </div>
            </div>
            <table class="table table-condensed table-bordered margin-top-10" style="width: 100%" id="tbl_po_items">
                <thead>
                <th>SN#</th>
                <th>Item Description</th>
                <th>Qty</th>
                <th>Unit</th>
                <th>Remarks</th>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</form>

<script type="text/javascript" src="<?php echo file_versioning('assets/pages/inventory/main.js'); ?>"></script>
<script type="text/javascript">
    INVENTORY.references(<?php echo $trntype; ?>);
</script>
