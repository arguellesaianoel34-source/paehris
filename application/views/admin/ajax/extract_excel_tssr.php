<?php
$dataid = $this->input->post('ids');

?>

<div class="modal-body">
    <form id="frm_save_extracted_tssr" method="post" action="<?php echo base_url();?>cad/saveextractedtssrdata">
        <input type="hidden" name="appid" value="<?php echo $dataid;?>"/>
        <h3 class="center">File: <span id="sourcefile"></span></h3>
        <ul class="list-group summary column">
            <li class="list-group-item">
                <span class="col-md-3 label-name">Application No.</span>
                <span class="col-md-9 bold" id="appnumber" style="text-align: center !important;"></span>
                <input type="hidden" name="appnumber" id="input_appnumber"/>
            </li>
            <li class="list-group-item">
                <span class="col-md-3 label-name">Date</span>
                <span class="col-md-9 bold" id="inspectionDate" style="text-align: center !important;"></span>
                <input type="hidden" name="inspectiondate" id="input_inspectionDate"/>
            </li>
            <li class="list-group-item">
                <span class="col-md-3 label-name">Roof Orientation</span>
                <span class="col-md-9 bold" id="roofOrientation" style="text-align: center !important;"></span>
                <input type="hidden" name="rooforientation" id="input_roofOrientation"/>
            </li>
            <li class="list-group-item">
                <span class="col-md-3 label-name">Kind of Roof</span>
                <span class="col-md-9 bold" id="kindOfRoof" style="text-align: center !important;"></span>
                <input type="hidden" name="roofing" id="input_kindOfRoof"/>
            </li>
            <li class="list-group-item">
                <span class="col-md-3 label-name">Roof Inclination</span>
                <span class="col-md-9 bold" id="roofInclination" style="text-align: center !important;"></span>
                <input type="hidden" name="roofinclination" id="input_roofInclination"/>
            </li>
            <li class="list-group-item">
                <span class="col-md-3 label-name">Voltage Drop Condition</span>
                <span class="col-md-9 bold" id="voltageDropCondition" style="text-align: center !important;"></span>
                <input type="hidden" name="vdcondition" id="input_voltageDropCondition"/>
            </li>
            <li class="list-group-item">
                <span class="col-md-3 label-name">Generator Rating</span>
                <span class="col-md-9 bold" id="generatorRating" style="text-align: center !important;"></span>
                <input type="hidden" name="gensetrate" id="input_generatorRating"/>
            </li>
        </ul>
        <h4 class="text-align-center bold">READINGS</h4>
        <div class="row">
            <div class=" col-md-4">
                <ul class="list-group summary column">
                    <li class="list-group-item">
                        <span class="col-md-4 label-name">L1 - L2</span>
                        <span class="col-md-8 bold" id="l1l2" style="text-align: center !important; min-height:24px !important;"></span>
                        <input type="hidden" name="l1l2" id="input_l1l2"/>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-4 label-name">L1 - G</span>
                        <span class="col-md-8 bold" id="l1g" style="text-align: center !important; min-height:24px !important;"></span>
                        <input type="hidden" name="l1g" id="input_l1g"/>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-4 label-name">L1 - L2(A)</span>
                        <span class="col-md-8 bold" id="l1l2a" style="text-align: center !important; min-height:24px !important;"></span>
                        <input type="hidden" name="l1l2a" id="input_l1l2a"/>
                    </li>
                </ul>
            </div>
            <div class=" col-md-4">
                <ul class="list-group summary column">
                    <li class="list-group-item">
                        <span class="col-md-4 label-name">L1 - L3</span>
                        <span class="col-md-8 bold" id="l1l3" style="text-align: center !important; min-height:24px !important;"></span>
                        <input type="hidden" name="l1l3" id="input_l1l3"/>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-4 label-name">L2 - G</span>
                        <span class="col-md-8 bold" id="l2g" style="text-align: center !important; min-height:24px !important;"></span>
                        <input type="hidden" name="l2g" id="input_l2g"/>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-4 label-name">L1 - L3(A)</span>
                        <span class="col-md-8 bold" id="l1l3a" style="text-align: center !important; min-height:24px !important;"></span>
                        <input type="hidden" name="l1l3a" id="input_l1l3a"/>
                    </li>
                </ul>
            </div>
            <div class=" col-md-4">
                <ul class="list-group summary column">
                    <li class="list-group-item">
                        <span class="col-md-4 label-name">L2 - L3</span>
                        <span class="col-md-8 bold" id="l2l3" style="text-align: center !important; min-height:24px !important;"></span>
                        <input type="hidden" name="l2l3" id="input_l2l3"/>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-4 label-name">L3 - G</span>
                        <span class="col-md-8 label-name" id="l3g" style="text-align: center !important; min-height:24px !important;"></span>
                        <input type="hidden" name="l3g" id="input_l3g"/>
                    </li>
                    <li class="list-group-item">
                        <span class="col-md-4 label-name">L2 - L3(A)</span>
                        <span class="col-md-8 bold" id="l2l3a" style="text-align: center !important; min-height:24px !important;"></span>
                        <input type="hidden" name="l2l3a" id="input_l2l3a"/>
                    </li>
                </ul>
            </div>
        </div>

        <?php
        $surveydetails_qry = $this->db->select()
            ->from('prime_types_parameter')
            ->where(array('codes' => 'INSFILETYPES','sysid !=' => 3427,'status' => 1))
            ->get();

        if ($surveydetails_qry->num_rows() > 0) {
            foreach ($surveydetails_qry->result() AS $surveydetail) {?>
                <h4 class="text-align-center bold uppercase"><?php echo $surveydetail->names;?></h4>
                <div class="form-group row">
                    <div class="col-md-12">
                        <span class="bold">PICTURES:</span>
                    </div>
                    <div class="col-md-12 text-align-center" id="<?php echo $surveydetail->sysid;?>_htmlpic">

                    </div>
                    <input type="hidden" id="input_<?php echo $surveydetail->sysid;?>_htmlpic" name="img[<?php echo $surveydetail->sysid;?>]">
                    <div class=" col-md-12">
                        <ul class="list-group summary column">
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">Measurements</span>
                                <span class="col-md-8 bold" id="<?php echo $surveydetail->sysid;?>_measurements" style="text-align: center !important;"></span>
                                <input type="hidden" name="surveydetail[<?php echo $surveydetail->sysid;?>][measurements]"  id="input_<?php echo $surveydetail->sysid;?>_measurements" />
                            </li>
                            <li class="list-group-item">
                                <span class="col-md-4 label-name">Remarks</span>
                                <span class="col-md-8 bold" id="<?php echo $surveydetail->sysid;?>_remarks" style="text-align: center !important;"></span>
                                <input type="hidden" name="surveydetail[<?php echo $surveydetail->sysid;?>][remarks]" id="input_<?php echo $surveydetail->sysid;?>_remarks"/>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php }
        }
        ?>
        <h4 class="text-align-center bold uppercase">Remarks</h4>
        <ul class="list-group summary column">
            <li class="list-group-item">
                <span class="col-md-4 label-name">Remarks</span>
                <span class="col-md-8 bold" id="remarks" style="text-align: center !important;"></span>
                <input type="hidden" name="remarks" id="input_remarks"/>
            </li>
        </ul>
        <h4 class="text-align-center bold uppercase">Additional Information</h4>
        <ul class="list-group summary column">
            <li class="list-group-item">
                <span class="col-md-4 label-name">Roof Dimensions</span>
                <span class="col-md-8 bold" id="roofDimensions" style="text-align: center !important;"></span>
                <input type="hidden" name="roofdimension"  id="input_roofDimensions" />
            </li>
            <li class="list-group-item">
                <span class="col-md-4 label-name">Electrical/Structural Plans</span>
                <span class="col-md-8 bold" id="esplan" style="text-align: center !important;"></span>
                <input type="hidden" name="esplans"  id="input_esplan" />
            </li>
            <li class="list-group-item">
                <span class="col-md-4 label-name">Normal Loads or for Clamping</span>
                <span class="col-md-8 bold" id="normalloads" style="text-align: center !important;"></span>
                <input type="hidden" name="normalloads"  id="input_normalloads" />
            </li>
            <li class="list-group-item">
                <span class="col-md-4 label-name">Meter # / Billing Details</span>
                <span class="col-md-8 bold" id="billing" style="text-align: center !important;"></span>
                <input type="hidden" name="billingdetails"  id="input_billing" />
            </li>
            <li class="list-group-item">
                <span class="col-md-4 label-name">Daytime Appliances</span>
                <span class="col-md-8 bold" id="dtAppliances" style="text-align: center !important;"></span>
                <input type="hidden" name="dtappliances"  id="input_dtAppliances" />
            </li>
        </ul>
        <h4><hr></h4>
        <div class="row">
            <div class="col-md-12">
                <span class="bold">CLAMP RESULT PICTURES:</span>
            </div>
            <div class="col-md-6 text-align-center" id="amp_htmlpic">

            </div>

            <div class="col-md-6 text-align-center" id="volt_htmlpic">

            </div>
            <span class="col-md-6 text-align-center">Ampere<input type="hidden" id="input_amp_htmlpic"></span>
            <span class="col-md-6 text-align-center">Volt<input type="hidden" id="input_volt_htmlpic"></span>
        </div>
        <h4><hr></h4>
        <div class="row">
            <div class="col-md-12">
                <span class="bold">PICTURE OF BILLS:</span>
            </div>
            <div class="col-md-12 text-align-center" id="bills_htmlpic">

            </div>
            <input type="hidden" id="input_bills_htmlpic">

        </div>
        <h4><hr></h4>
        <div class="row">
            <div class="col-md-12">
                <span class="bold">PICTURES OF ROOF:</span>
            </div>
            <div class="col-md-12 text-align-center" id="roof_htmlpic">

            </div>
            <input type="hidden" id="input_roof_htmlpic">
        </div>
        <div class="modal-footer">
            <div class="btn-group col-md-12">
                <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save</button>
                <button type="reset" class="btn btn-default"><i class="fa fa-undo"></i> Reset</button>
            </div>
        </div>
    </form>
</div>

<script src="<?php echo base_url(); ?>assets/pages/inspection/main.js" type="text/javascript"></script>
<script type="text/javascript">
    INSPECTION.extract(<?php echo $dataid;?>)
</script>