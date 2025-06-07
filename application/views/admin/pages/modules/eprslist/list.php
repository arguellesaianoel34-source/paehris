<?php
$stages_qry = $this->db->select('levels')
    ->from('prime_transaction_flow_main_stages')
    ->where(array('moduleid' => $navid, 'status' => 1))
    ->get();

$stages = '';

if ($stages_qry->num_rows() > 0) {
    $stage = array();
    foreach ($stages_qry->result() AS $row) {
        $stage[] = $row->levels;
    }
    $stages .= '['.implode(',',$stage).']';
}
?>
<div class="portlet light tab-pane fade in">
    <div class="portlet-title">
        <div class="caption"></div>
        <div class="tabbable-line pull-right" id="doc_preview_box">
            <ul class="nav nav-tabs " id="purchase_list_tab">
                <li class="active">
                    <a href="#" data-toggle="tab" aria-expanded="true" data-id="300"> Pending </a>
                </li>
                <li class="">
                    <a href="#" data-toggle="tab" aria-expanded="true" data-id="301"> Approved </a>
                </li>
                <li class="">
                    <a href="#" data-toggle="tab" aria-expanded="true" data-id="302"> Disapproved/Cancelled </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-6 pull-left">
                    <input value="" id="select2routes" class="form-control" style="margin-left: -15px; width: 50%;" placeholder="Select Route.. " />
                </div>

                <table style="width: 100%;" id="eprs_trn_list" class="table table-hover table-striped table-condensed table-bordered no-footer tbl-sm" >
                    <thead>
                    <th></th>
                    <th>PRF</th>
                    <th>PO</th>
                    <th>Submitted</th>
                    <th>Updated</th>
                    <th>Items</th>
                    <th>Justification</th>
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
    </div>
</div>

<script src="<?php echo file_versioning('assets/pages/eprs/main.js'); ?>"></script>
<script>
    EPRS.listPRF();
</script>