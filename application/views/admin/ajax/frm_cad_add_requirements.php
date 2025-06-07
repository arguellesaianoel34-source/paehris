<?php
$ids = $this->input->post('ids');

?>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <form id="frm_add_requirements" action="<?php echo base_url();?>cad/addrequirement" method="post">
                <input type="hidden" name="appid" value="<?php echo $ids;?>">
                <table width="100%" class="table table-hover table-striped table-condensed tbl-sm" id="tbl_add_requirement_list">
                    <thead>
                    <th>#</th>
                    <th>Req Code</th>
                    <th>Requirement</th>
                    <th><i class="fa fa-check-square fa-lg"></th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <button type="submit" class="btn btn-primary pull-right" id="btn_add_selected_requirements"><i class="fa fa-plus"></i> Add Selected</button>
            </form>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/global/plugins/icheck/icheck.min.js"></script>
<script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js"></script>

<script type="text/javascript">
    CAD.addRequirements(<?php echo $ids;?>);
</script>