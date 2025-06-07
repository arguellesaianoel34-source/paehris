<?php
$ids = $this->input->post('ids');
$trnid = $this->input->post('view');
$data = array();
?>
<div class="modal-body">
<?php
if ($ids > 0) {
    $data['trntype'] = $ids;
    $data['trnid'] = $trnid;
    if ($ids == 23) {
        $this->load->view('admin/pages/modules/inventory/rr_po', $data, FALSE);
    }
    if ($ids == 24) {
        $this->load->view('admin/pages/modules/inventory/install_mats', $data, FALSE);
    }
} else {
    echo '<h1 class="text-align-center"><i class="fa fa-times text-danger"></i> No transaction type selected!</h1>';
    echo '<h1 class="text-align-center">Please select one and try again.</h1>';
}
?>
</div>
