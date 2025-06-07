<link rel="stylesheet" href="<?php echo base_url();?>/assets/global/plugins/icheck/skins/all.css">
<?php
$ids = $ids = $this->input->post('ids');
$ids_arr = explode(',',$ids);

list($dataid,$systemsize,$apptype) = $ids_arr;

if ($apptype == 1) {
    $systemsize_info = $this->db->query("SELECT * FROM customer_system_size WHERE sysid = {$systemsize}")->row();
} else {
    $systemsize_info = $this->db->query("SELECT * FROM customer_system_group WHERE sysid = {$systemsize}")->row();
}





?>
<div class="modal-body">
    <h4>Current System Size: <b><?php echo ($apptype <= 1) ? $systemsize_info->descs : $systemsize_info->desc;?></b></h4>
    <div class="row">
        <div class="col-md-12">
            <form id="frm_override_system_size" action="<?php echo base_url();?>inspection/overridesystemsize" method="post">
                <input type="hidden" name="appid"  value="<?php echo $dataid; ?>">
                <div class="portlet grey box">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject bold uppercase">New System Size</span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="input-group margin-bottom-5">
                            <div class="input-group-addon icheck-inline">
                                <input class="icheck" data-target="#select2_systemsize" id="standardtype" name="systemtype" type="radio" checked aria-label="" value="1"> <label class="bold uppercase" for="standardtype">Standard</label>
                            </div>
                            <input class="form-control" id="select2_systemsize" name="newsize">
                        </div>
                        <div class="input-group margin-bottom-5">
                            <div class="input-group-addon icheck-inline">
                                <input class="icheck" data-target="#newsystemsize" id="nonstandardtype" name="systemtype" type="radio" aria-label="" value="2"> <label class="bold uppercase" for="nonstandardtype">Non-standard</label>
                            </div>
                            <input class="form-control" id="newsystemsize" name="newsize" placeholder="Ex: 22kWp Grid-Tied" disabled>
                        </div>
                        <div class="portlet-footer">
                            <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save</button>
                        </div>
                    </div>
                </div>


            </form>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/global/plugins/icheck/icheck.min.js"></script>
<script type="text/javascript">
    PECO.select2Basic($('#select2_systemsize',document),'inspection/select2systemsize','Select System Size...',false,false,false);

    $('.icheck-inline .icheck', $('#frm_override_system_size',document)).each(function(){
        $(this).iCheck({
            checkboxClass: 'icheckbox_square-red',
            radioClass: 'iradio_square-red',
            increaseArea: '-10%'
        }).on('ifChecked', function(){
            var this_ = $(this);
            this_.attr('checked', true);
            var target = this_.attr('data-target');
            $(target).attr('disabled',false);
        }).on('ifUnchecked', function(){
            var this_ = $(this);
            this_.attr('checked', false);
            var target = this_.attr('data-target');
            $(target).attr('disabled',true);
        });
    });

    INSPECTION.override(<?php echo $dataid; ?>)
</script>
