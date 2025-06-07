<style>
    #tbl_requirements_list_filter {
        display: inline-block !important;
        width: 150px;
    }
    #tbl_requirements_list_filter input{
        width: 100% !important;
    }
</style>
<?php
$check_contract = $this->model_cad->check_contract($dataid);

$requirements = '';

$online = $this->db->select()->from('application_customers_online_ticket_ref')
    ->where(array('appid' => $dataid, 'status' => 1))->get()->row();

$trans = $this->db->select('trmt.*,	tfms.`desc`')
    ->from('transaction_request_main_trails as trmt')
    ->join('prime_transaction_flow_main_stages as tfms','trmt.stageid = tfms.sysid','left')
    ->where('trmt.dataid',$dataid)
    ->order_by('trmt.datecreated','DESC')
    ->get()->row();

//echo $this->db->last_query();
?>

<div class="tab-pane fade in <?php ($task_flow == false) ? 'active' : ''; ?>" id="data">
    <div class="row">
        <?php
        if ($trans->desc == 'Profile Data Entry') {
            ?>
            <div class="col-md-7" id="customer_info_field">
                <?php customer_application_editinfo($dataid,true,true); ?>
            </div>
            <div class="col-md-5">
                <?php echo customer_application_view_right($dataid); ?>
            </div>
            <?php
        }

        if ($trans->desc == 'Accomplishment') {
            ?>
            <div class="col-md-7">
                <?php customer_application_editinfo($dataid,true,true); ?>

            </div>
            <div class="col-md-5">
                <?php echo customer_application_requirements_list($dataid); ?>
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-history"></i>
                            <span class="caption-subject font-green-sharp bold uppercase">Owners and Authorized Representatives</span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-bordered table-condensed table-sm">
                            <thead>
                            <th>#</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th><i class="fa fa-sliders bold"></i> </th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php
        }

        if ($trans->desc == 'Archiving') {
            $data = $this->_ci_cached_vars;
            $this->load->view('admin/pages/modules/cad/archiving', $data);
        }
        ?>
    </div>
</div>


<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/moment.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery.mockjax.js"></script>

<!--
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/fileinput.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/fr.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/js/locales/es.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.js" type="text/javascript"></script>
-->

<!-- BEGIN PAGE LEVEL PLUGINS -->

<script src="<?php echo base_url(); ?>assets/global/plugins/fuelux/js/spinner.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery.input-ip-address-control-1.0.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-tags-input/jquery.tagsinput.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/typeahead/handlebars.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/typeahead/typeahead.bundle.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/select2/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/additional-methods.min.js"></script>

<!-- END PAGE LEVEL PLUGINS -->

<script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js"></script>
<script src="<?php echo base_url(); ?>assets/global/scripts/address.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/pages/cad/form-editable.js"></script>


<script type="text/javascript">
    $(document).find('select').each(function () {
        $(this).select2();
    });

    CAD.application();
    CAD.profile();
    ADDRESS.init(<?php echo isset($country) ? $country : 0; ?>,<?php echo isset($region) ? $region : 0; ?>,<?php echo isset($province) ? $province : 0; ?>,<?php echo isset($citymun) ? $citymun : 0; ?>);
</script>
