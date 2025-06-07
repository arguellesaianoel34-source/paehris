<?php
$ctt = [];
$ctt_details_qry = $this->db->select(
    'sysid, 
    inverter, 
    testingarea, 
    temp, 
    humidity, 
    testdate, 
    equipment, 
    eqtmodel AS equipmentmodel, 
    eqtsn AS equipmentsn,
    accepted,
    note
    ')
->from('frm_tnc_form_details')
->where(['tncid' => $tncid,'type' => 3,'status' => 1])
->get();

if ($ctt_details_qry->num_rows() > 0) {
    foreach ($ctt_details_qry->result() AS $ctt_detail) {
        $inverter = $ctt_detail->inverter;
        unset($ctt_detail->inverter,$ctt_detail->sysid);
        $ctt['ctt'][$inverter]['details'] = (array)$ctt_detail;
    }
}
?>
<div class="tab-pane fade in active" id="tnc_ctt">
    <style type="text/css">
        .components tbody tr td:first-child:not(.note):before {
            content: "•";
            font-size: 150%;
            position: absolute;
            left: 5px;
        }
        .components tbody tr td:first-child {
            position: relative;
            padding-left: 20px;
            text-align: justify;
            text-justify: inter-word;
        }
    </style>
    <div class="portlet light">
        <div class="portlet-body">
            <div class="row">
                <div class="col-md-1">
                    <ul class="nav nav-tabs tabs-left">
                        <?php
                        for ($i = 0; $i < $inverters; $i++) {
                            ?>
                            <li class="<?php echo $i == 0 ? 'active' : ''?>">
                                <a href="#tnc_<?php echo $i+1;?>_ctt" data-toggle="tab"> <?php echo $i+1;?> </a>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
                <div class="col-md-11">
                    <form id="frm_tnc_stringtest" action="<?php echo base_url().'forms/tncsavecontinuitytest'; ?>" method="post" data-title="Continuity Test" data-text="Save continuity test data?">
                        <div class="tab-content">
                            <?php
                            for ($i = 0; $i < $inverters; $i++) {
                                $st_data = array('inverter' => $i+1,'img' => $imgs['ctt']['inv'][$i+1] ?? false,'ctt' => $ctt['ctt'][$i+1] ?? false);
                                $this->load->view('admin/pages/modules/forms/tnc/continuity_frm', $st_data);
                            }
                            ?>
                        </div>
                        <div class="portlet-footer margin-top-15">
                            <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
