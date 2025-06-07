<?php
$tqt = [];
$saved_qry = $this->db->select('testingarea,testdate,temp,humidity,equipment,eqtmodel')
    ->from('frm_tnc_form_details')
    ->where(['tncid' => $tncid,'type' => 6,'status' => 1])
    ->get()->row();

if ($saved_qry) {
    foreach ($saved_qry AS $col => $value) {
        $tqt['details'][$col] = $value;
    }
}
?>
<div class="tab-pane fade in active" id="tqt_frm">
    <form id="frm_tnc_vift" action="<?php echo base_url().'forms/tncsavetorquetest'; ?>" method="post" data-title="Torque Test" data-text="Save torque test data?">
        <div class="well">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-4">
                        Device No. :
                        <label>INVERTER <?php echo ($inverters > 1) ? '1-'.$inverters : 1; ?></label>
                    </div>
                    <div class="col-md-4">
                        Description : Torque Test
                        <input type="hidden" name="type" value="6" />
                    </div>
                </div>
            </div>
        </div>
        <h4>TORQUE TEST</h4>
        <div class="well">
            <div class="row">
                <div class="col-md-6">
                    Testing Area
                    <input class="form-control inline" name="testingarea" id="inv_trn_type" value="Electrical Room" placeholder="Customer name/number" style="border-bottom: 1.5px grey solid !important;" required />
                </div>
                <div class="col-md-6">
                    Date & Time
                    <input type="datetime-local" class="form-control inline" name="testdate" value="<?php echo isset($tqt['details']['testdate']) ? date('Y-m-d\TH:i',strtotime($tqt['details']['testdate'])) : date('Y-m-d\TH:i'); ?>" placeholder="MM/DD/YYYY hh:mm AM/PM" style="border-bottom: 1.5px grey solid !important;" required >
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    Ambient Temperature
                    <input class="form-control inline" name="temp" value="<?php echo $tqt['details']['temp'] ?? ''; ?>" placeholder="Temperature..." style="border-bottom: 1.5px grey solid !important;"  />
                </div>
                <div class="col-md-4">
                    Humidity (%)
                    <div class="testing-wrapper">
                        <input type="number" class="form-control input-sm inline testing-vals" name="humidity" value="<?php echo $tqt['details']['humidity'] ?? ''; ?>" placeholder="Humidity..." style="border-bottom: 1.5px grey solid !important;" >
                        <span class="testing-unit" style="border-bottom: 1.5px grey solid !important; background: white;margin-left: unset;padding: 2.5px 0;">%</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    Testing Equipment
                    <input class="form-control inline" name="equipment" id="stt_equipment" value="<?php echo $tqt['details']['equipment'] ?? ''; ?>" placeholder="Testing Equipment..." style="border-bottom: 1.5px grey solid !important;" required />
                </div>
                <div class="col-md-4">
                    Model
                    <input class="form-control inline" name="eqtmodel" id="stt_eqt_model" value="<?php echo $tqt['details']['eqtmodel'] ?? ''; ?>" placeholder="Equipment Model..." style="border-bottom: 1.5px grey solid !important;"  />
                </div>
                <div class="col-md-4">
                    Serial Number
                    <input class="form-control inline" name="eqtsn" id="stt_eqt_sn" value="<?php echo $tqt['details']['eqtsn'] ?? ''; ?>" placeholder="Serial Number..." style="border-bottom: 1.5px grey solid !important;"  />
                </div>
            </div>
        </div>
        <div class="row margin-top-15">
            <div class="col-md-12">
                Note / Criteria: As per Manufacturer
                <textarea id="ctt_note" class="form-control" rows="5" name="note" value="<?php echo $tqt['details']['note'] ?? ''; ?>"></textarea>
            </div>
        </div>
        <div class="portlet-footer margin-top-15">
            <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save</button>
        </div>
    </form>
</div>