<?php
$ids = $this->input->post('ids');
list($dataid,$trnid) = array_pad(explode(',',$ids),2,null);
?>
<div class="modal-body">
    <div class="well">
        <form id="frm_add_prf_item" method="post" action="<?php echo base_url();?>purchasing/addprfitem">
            <input type="hidden" name="itemid" id="itemid" required>
            <?php echo ($trnid) ? '<input type="hidden" name="trnid" id="trnid" value="'.$trnid.'">' : ''; ?>
            <div class="row">
                <div class="col-md-6">
                    Search Item
                    <input class="form-control" id="item_desc" placeholder="Item name / code" required />
                </div>
                <div class="col-md-3">
                    Qty
                    <input class="form-control" name="qty" placeholder="Qty..." required />
                </div>
                <div class="col-md-3">
                    Unit
                    <input class="form-control" name="unitid" id="unitid" placeholder="Unit..." required />
                </div>
            </div>
            <div class="row margin-top-10">
                <div class="col-md-12">
                    <label class="control-label">
                        Remarks
                    </label>
                    <textarea class="form-control" name="remarks" placeholder="Enter remarks..." rows="1" ></textarea>
                </div>

                <div class="col-md-3 pull-right">
                    <div class="btn-group pull-right margin-top-20">
                        <button type="reset" class="btn btn-default"> <i class="fa fa-refresh"></i> Reset</button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-cart-plus"></i> Add</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    EPRS.newItem(<?php echo $dataid; ?>);
</script>