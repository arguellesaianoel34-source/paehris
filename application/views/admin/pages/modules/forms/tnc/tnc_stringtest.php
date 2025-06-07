<?php
$stt = [];
$stt_details_qry = $this->db->select(
        'sysid, 
        inverter, 
        testingarea, 
        invsn AS serialnumber, 
        testdate, 
        equipment, 
        eqtmodel AS equipmentmodel, 
        eqtsn AS equipmentsn
        ')
    ->from('frm_tnc_form_details')
    ->where(['tncid' => $tncid,'type' => 1,'status' => 1])
    ->get();

if ($stt_details_qry->num_rows() > 0) {
    foreach ($stt_details_qry->result() AS $stt_detail) {

        $inverter = $stt_detail->inverter;
        $details_id = $stt_detail->sysid;
        unset($stt_detail->inverter,$stt_detail->sysid);

        $stt['stringtest'][$inverter]['details'] = (array)$stt_detail;

        $stt_strings_qry = $this->db->select('string,datatype,value')
            ->from('frm_tnc_form_data')
            ->where(['detailsid' => $details_id,'status' => 1])
            ->get();

        if ($stt_strings_qry->num_rows() > 0) {
            foreach ($stt_strings_qry->result() AS $string) {
                $stt['stringtest'][$inverter]['strings'][$string->string][$string->datatype] = $string->value;
            }
        }
    }
}
?>
<div class="tab-pane fade in active" id="tnc_stringtest">
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
        <div class="portlet-title tabbable-line">
            <ul class="nav nav-tabs">
                <?php
                for ($i = 0; $i < $inverters; $i++) {
                    ?>
                    <li class="<?php echo $i == 0 ? 'active' : ''?>">
                        <a href="#tnc_<?php echo $i+1;?>_stringtest" data-toggle="tab"> <?php echo $i+1;?> </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
        <div class="portlet-body">
            <div class="row">
                <form id="frm_tnc_stringtest" action="<?php echo base_url().'forms/tncsavestringtest'; ?>" method="post" data-title="String Test" data-text="Save string test data?">
                    <div class="tab-content col-md-12">
                        <?php
                        for ($i = 0; $i < $inverters; $i++) {
                            $st_data = array('inverter' => $i+1,'stringtest' => $stt['stringtest'][$i+1] ?? false);
                            $this->load->view('admin/pages/modules/forms/tnc/stringtest_frm', $st_data, FALSE);
                        }
                        ?>

                    </div>
                    <div class="portlet-footer">
                        <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>