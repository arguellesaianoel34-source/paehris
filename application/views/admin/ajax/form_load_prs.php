<?php
?>
<div class="modal-body">
    <div class="portlet box white">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-history"></i>
                <span class="caption-subject bold font-green-haze uppercase"> Request History </span>
            </div>
        </div>
        <div class="portlet-body">
            <table id="tbl_prs_history" class="table table-bordered table-condensed table-sm table-hover">
                <thead>
                <th>#</th>
                <th>PRF#</th>
                <th>Items</th>
                <th>Justification</th>
                <th>Status</th>
                <th>Control</th>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/pages/eprs/modal.js"></script>
<script type="text/javascript">
    EPRS_M.prsHistory();
</script>