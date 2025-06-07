<?php
$thm = [];
$saved_qry = $this->db->select('testingarea,testdate,equipment,eqtmodel,note')
    ->from('frm_tnc_form_details')
    ->where(['tncid' => $tncid,'type' => 7,'status' => 1])
    ->get()->row();

//echo $this->db->last_query();

if ($saved_qry) {
    foreach ($saved_qry AS $field => $value) {
        $thm['details'][$field] = $value;
    }
}
?>
<div class="tab-pane fade in active" id="thermal_frm">
    <form id="frm_tnc_vift" action="<?php echo base_url().'forms/tncsavethermaltest'; ?>" method="post" data-title="Torque Test" data-text="Save torque test data?">
        <div class="well">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-4">
                        Device No. :
                        <label>INVERTER <?php echo ($inverters > 1) ? '1-'.$inverters : 1; ?></label>
                    </div>
                    <div class="col-md-4">
                        Description : Thermal Scanning
                        <input type="hidden" name="thm[details][type]" value="7" />
                    </div>
                </div>
            </div>
        </div>
        <h4>THERMAL SCANNING</h4>
        <div class="well">
            <div class="row">
                <div class="col-md-6">
                    Testing Area
                    <input class="form-control inline" name="thm[details][testingarea]" value="<?php echo $thm['details']['testingarea'] ?? 'Electrical Room'; ?>" placeholder="Testing area..." style="border-bottom: 1.5px grey solid !important;" required />
                </div>
                <div class="col-md-6">
                    Date & Time
                    <input type="datetime-local" class="form-control inline" name="thm[details][testdate]" value="<?php echo isset($thm['details']['testdate']) ? date('Y-m-d\TH:i',strtotime($thm['details']['testdate'])) : date('Y-m-d\TH:i'); ?>" placeholder="MM/DD/YYYY hh:mm AM/PM" style="border-bottom: 1.5px grey solid !important;" required >
                </div>
            </div>
        </div>
        <!-- Thermal Scanning Tables Here -->
        <div class="row margin-top-15">
            <div class="col-md-12">
                <table class="table table-condensed table-bordered" id="tbl_inverter_thermal" data-arr="eqttype:1">
                    <thead>
                    <th>Inverter</th>
                    <th>Serial/Identification</th>
                    <th>Energized Temperature</th>
                    <th>Remarks</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="col-md-12">
                <table class="table table-condensed table-bordered" id="tbl_dc_thermal" data-arr="eqttype:2">
                    <thead>
                    <th>DC Breaker Scan</th>
                    <th>Serial/Identification</th>
                    <th>Energized Temperature</th>
                    <th>Remarks</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="col-md-12">
                <table class="table table-condensed table-bordered" id="tbl_ac_thermal" data-arr="eqttype:3">
                    <thead>
                    <th>AC Breaker Scan</th>
                    <th>Serial/Identification</th>
                    <th>Energized Temperature</th>
                    <th>Remarks</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="col-md-12">
                <table class="table table-condensed table-bordered" id="tbl_rsd_thermal" data-arr="eqttype:4">
                    <thead>
                    <th>RSD Scan</th>
                    <th>Serial/Identification</th>
                    <th>Energized Temperature</th>
                    <th>Remarks</th>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        <div class="row margin-top-15">
            <div class="col-md-12">
                <label for="htm_note" class="bold">Note / Criteria:</label>
                <textarea id="htm_note" class="form-control" rows="5" name="thm[details][note]" maxlength="2500"><?php echo $thm['details']['note'] ?? ''; ?></textarea>
            </div>
        </div>
        <div class="portlet-footer margin-top-15">
            <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save</button>
        </div>
    </form>
</div>
<script type="text/javascript">
    FORMS.dtTabledForm($('#tbl_inverter_thermal'),'forms/tnctabledform',<?php echo $tncid; ?>);
    FORMS.dtTabledForm($('#tbl_dc_thermal'),'forms/tnctabledform',<?php echo $tncid; ?>);
    FORMS.dtTabledForm($('#tbl_ac_thermal'),'forms/tnctabledform',<?php echo $tncid; ?>);
    FORMS.dtTabledForm($('#tbl_rsd_thermal'),'forms/tnctabledform',<?php echo $tncid; ?>);
</script>