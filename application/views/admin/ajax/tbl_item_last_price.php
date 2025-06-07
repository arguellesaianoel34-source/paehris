<?php
$itemid = $this->input->post('ids');

?>


<div class="modal-body">
    <div class="row margin-top-20">
        <div class="col-md-12">
            <table width="100%" class="table table-bordered table-striped" id="tbl_add_item_quotations">
                <thead>
                <th>#</th>
                <th>PRF #</th>
                <th>PO #</th>
                <th>Supplier</th>
                <th>Quoted Amt</th>
                <th>Spec/Remarks</th>
                <th>View PO</th>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    EPRS.lastPrice(<?php echo $itemid; ?>)
</script>