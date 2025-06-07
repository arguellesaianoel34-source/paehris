<?php
if (isset($inverter) && $inverter > 0) {
    $active = ($inverter == 1) ? 'active' : 0;
}


?>
<div class="tab-pane fade in <?php echo $active; ?>" id="vift_<?php echo $inverter; ?>_dci">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-condensed table-numbered selectlist" id="dci_inv<?php echo $inverter; ?>_">
                <thead>
                <tr>
                    <th rowspan="2" width="60%">VISUAL INSPECTION AND FUNCTION TEST</th>
                    <th colspan="2" class="text-align-center">Check</th>
                </tr>
                <tr>
                    <th class="text-align-center">Accepted</th>
                    <th class="text-align-center">Not Accepted</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $dci_query = $this->db->select('*')
                    ->from('frm_tnc_checklist_items')
                    ->where(['subtype' => 5])
                    ->order_by('order')
                    ->get();

                if ($dci_query->num_rows() > 0) {
                    foreach ($dci_query->result() AS $dci) {
                        //QUERY FOR VALUE FIRST

                        echo '<tr>';
                        if (in_array($dci->type,['checkbox','radio'])) {
                            echo '<td>'.$dci->item.'</td>';
                            $dci_val = explode(',',$dci->default);
                            if (count($dci_val) > 0) {
                                $icheck = ($dci->type == 'radio') ? 'icheck-select' : 'icheck';
                                foreach ($dci_val as $val) {
                                    $response_qry = $this->db->select()
                                        ->from('frm_tnc_checklist_responses')
                                        ->where(['itemid' => $dci->sysid,'tncid' => $tncid,'status' => 1])
                                        ->get()->row();

                                    $isChecked = ($response_qry && $response_qry->check == $val) ? 'checked' : '';
                                    echo '<td class="text-align-center"><input type="'.$dci->type.'" name="dci[inv]['.$inverter.'][cl]['.$dci->sysid.']" value="'.$val.'" '.$isChecked.' class="'.$icheck.'"></td>';
                                }
                            }
                        } else {
                            //ADD HAS INPUT FOR LATER
                            echo '<td>'.$dci->item.'</td>';
                        }
                        echo '</tr>';
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <h4>INSULATION RESISTANCE MEASUREMENT</h4>
    <div class="row">
        <?php
        $saved_qry = $this->db->select('equipment,eqtmodel,testdate,humidity')
            ->from('frm_tnc_form_details')
            ->where(['tncid' => $tncid,'inverter' => $inverter,'type' => 5,'status' => 1])
            ->get()->row();
        ?>
        <div class="col-md-8">
            Testing Equipment
            <input class="form-control" name="dci[inv][<?php echo $inverter; ?>][details][equipment]" value="<?php echo ($saved_qry && $saved_qry->equipment) ? $saved_qry->equipment : 'Insulation Resistance Tester'; ?>" placeholder="Equipment..." required />
        </div>
        <div class="col-md-4">
            Model
            <input class="form-control" name="dci[inv][<?php echo $inverter; ?>][details][eqtmodel]" value="<?php echo ($saved_qry && $saved_qry->eqtmodel) ? $saved_qry->eqtmodel : 'Uni-Trend UT511'; ?>" placeholder="Equipment Model..." required />
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            Date
            <input type="date" class="form-control" name="dci[inv][<?php echo $inverter; ?>][details][date]" value="<?php echo ($saved_qry && $saved_qry->testdate) ? date('Y-m-d',strtotime($saved_qry->testdate)) : date('Y-m-d') ?>" placeholder="Date..." required />
        </div>
        <div class="col-md-4">
            Start Time
            <input type="time" class="form-control text-align-center" name="dci[inv][<?php echo $inverter; ?>][details][time]" value="<?php echo ($saved_qry && $saved_qry->testdate) ? date('H:i',strtotime($saved_qry->testdate)) : date('H:i') ?>" placeholder="Time Started..." required />
        </div>
        <div class="col-md-4">
            Weather
            <input class="form-control" name="dci[inv][<?php echo $inverter; ?>][details][humidity]" value="<?php echo ($saved_qry && $saved_qry->humidity) ? $saved_qry->humidity : 'Cloudy, Light Rain'; ?>" placeholder="Weather..." required />
        </div>
    </div>
    <div class="row margin-top-15">
        <div class="col-md-12">
            <table class="table table-condensed table-bordered table-testing" style="width: 100%" id="tbl_inv<?php echo $inverter; ?>_dci">
                <thead>
                <th>String #</th>
                <th>Positive-Ground</th>
                <th>Negative-Ground</th>
                <th>Positive-Negative</th>
                </thead>
                <tbody>
                <?php
                //LOOKUP STRING TEST DATA, COUNT NUMBER OF STRING DATA PROVIDED.
                $strings_qry = $this->db->select('fd.inverter,stt.string as strnum')
                    ->from('frm_tnc_form_data AS stt')
                    ->join('frm_tnc_form_details AS fd', 'stt.detailsid = fd.sysid AND stt.`status` = 1', 'LEFT')
                    ->where(['fd.inverter' => $inverter])
                    ->group_by(array('stt.string', 'fd.inverter'))
                    ->order_by('fd.inverter ASC, stt.string ASC')
                    ->get();

                //echo $this->db->last_query();

                if ($strings_qry->num_rows() > 0) {
                    foreach ($strings_qry->result() AS $string) {
                        //GET VALUES IF AVAILABLE BEFORE SENDING

                        echo '<tr>';
                        echo '<td>'.$string->strnum.'</td>';

                        $types_qry = $this->db->select('sysid,datatype,symbol')
                            ->from('frm_tnc_datatypes')
                            ->where(['subtype' => 5])
                            ->order_by('col ASC')
                            ->get();

                        if ($types_qry->num_rows() > 0) {
                            foreach ($types_qry->result() AS $row) {
                                $val_qry = $this->db->select('tid.value')
                                    ->from('frm_tnc_form_data AS tid')
                                    ->join('frm_tnc_form_details AS tfd','tfd.sysid = tid.detailsid','left')
                                    ->where(['tfd.tncid' => $tncid,'tid.datatype' => $row->sysid,'tfd.inverter' => $string->inverter,'tid.string' => $string->strnum,'tid.status' => 1])
                                    ->get()->row();

                                $value = ($val_qry && $val_qry->value) ? 'value="'.$val_qry->value.'"' : '';

                                echo '<td>';
                                if ($row->symbol) {
                                    echo '<div class="testing-wrapper">';
                                    echo '<input type="'.$row->datatype.'" class="form-control inline testing-vals" name="dci[inv]['.$string->inverter.'][str]['.$string->strnum.']['.$row->sysid.']" '.$value.' required><span class="testing-unit">'.$row->symbol.'</span>';
                                    echo '</div>';
                                } else {
                                    echo '<input type="'.$row->datatype.'" class="form-control inline testing-vals" name="dci[inv]['.$string->inverter.'][str]['.$string->strnum.']['.$row->sysid.']" '.$value.' required>';
                                }
                                echo '</td>';
                            }
                        }
                        echo '</tr>';
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-12">
            Noted:
            <textarea id="inverter_sn" class="form-control" rows="5" name="dci[inv][<?php echo $inverter; ?>][details][note]"></textarea>
        </div>
    </div>
</div>
