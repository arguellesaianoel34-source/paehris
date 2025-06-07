<?php
?>

<div class="row">
    <div class="col-md-12">

        <table style="width: 100%;" id="tbl_prf_list" class="table table-hover table-striped table-condensed table-bordered no-footer tbl-sm" >
            <thead>
            <th></th>
            <th>PRF #</th>
            <th>PO #</th>
            <th>Date Requested</th>
            <th>Date Updated</th>
            <th>Justification</th>
            <th>Requested by</th>
            <th>Transaction</th>
            <th>Remarks</th>
            <th>Status</th>
            <th>View</th>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/pages/eprs/main.js"></script>
<script>
    EPRS.myPRF();
</script>