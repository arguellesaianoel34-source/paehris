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
<div class="portlet light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#eprs_request_approvals" data-toggle="tab"> EPRS Requests </a>
            </li>
            <li class="">
                <a href="#eprs_request_drafts" data-toggle="tab" aria-expanded="true"> Drafts </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body">
        <div class="tab-content">
            <div class="tab-pane fade in active" id="eprs_request_approvals">
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
            <div class="tab-pane fade in " id="eprs_request_drafts">
                <div class="row">
                    <div class="col-md-12">

                        <table style="width: 100%;" id="eprs_trn_draft" class="table table-hover table-striped table-condensed table-bordered no-footer tbl-sm" >
                            <thead>
                            <th></th>
                            <th>PRF</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th>Items</th>
                            <th>Justification</th>
                            <th>View</th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo file_versioning('assets/pages/eprs/main.js'); ?>"></script>
<script>
    EPRS.myPRF();
</script>