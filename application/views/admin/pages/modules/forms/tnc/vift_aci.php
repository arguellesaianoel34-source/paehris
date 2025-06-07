<?php
?>
<div class="tab-pane fade in active" id="form_vift_aci">
    <style>
        #form_vift_aci .table.table-testing th, #form_vift_aci .table.table-testing td {
            text-align: center;
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-condensed table-numbered selectlist">
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
                $aci_query = $this->db->select('*')
                    ->from('frm_tnc_checklist_items')
                    ->where(['subtype' => 4])
                    ->order_by('order')
                    ->get();

                if ($aci_query->num_rows() > 0) {
                    foreach ($aci_query->result() AS $aci) {
                        //QUERY FOR VALUE FIRST

                        echo '<tr>';
                        if (in_array($aci->type,['checkbox','radio'])) {
                            echo '<td>'.$aci->item.'</td>';
                            $aci_val = explode(',',$aci->default);
                            if (count($aci_val) > 0) {
                                $icheck = ($aci->type == 'radio') ? 'icheck-select' : 'icheck';
                                foreach ($aci_val as $val) {
                                    $response_qry = $this->db->select()
                                        ->from('frm_tnc_checklist_responses')
                                        ->where(['itemid' => $aci->sysid,'tncid' => $tncid,'status' => 1])
                                        ->get()->row();

                                    $isChecked = ($response_qry && $response_qry->check == $val) ? 'checked' : '';
                                    echo '<td class="text-align-center"><input type="'.$aci->type.'" name="aci[cl]['.$aci->sysid.']" value="'.$val.'" '.$isChecked.' class="'.$icheck.'"></td>';
                                }
                            }
                        } else {
                            //ADD HAS INPUT FOR LATER
                            echo '<td>'.$aci->item.'</td>';
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
        $saved_qry = $this->db->select('equipment,eqtmodel')
            ->from('frm_tnc_form_details')
            ->where(['tncid' => $tncid,'type' => 4,'status' => 1])
            ->get()->row();
        ?>
        <div class="col-md-4">
            Testing Equipment
            <input class="form-control" name="aci[details][equipment]" value="<?php echo ($saved_qry && $saved_qry->equipment) ? $saved_qry->equipment : 'Insulation Resistance Tester'; ?>" placeholder="Equipment Name..." required />
        </div>
        <div class="col-md-4">
            Model
            <input class="form-control" name="aci[details][eqtmodel]" value="<?php echo ($saved_qry && $saved_qry->equipment) ? $saved_qry->eqtmodel : 'Uni-Trend UT511'; ?>" placeholder="Equipment Model..." required />
        </div>
    </div>
    <div class="row margin-top-15">
        <div class="col-md-12">
            <table class="table table-condensed table-bordered table-testing" style="width: 100%">
                <thead>
                <th>Inverter</th>
                <th>L1-G</th>
                <th>L2-G</th>
                <th>L3-G</th>
                <th>N-G</th>
                </thead>
                <tbody>
                <?php
                for ($i = 0; $i < $inverters; $i++) {
                    $n = $i+1;
                    ?>
                    <tr>
                        <td><?php echo $n; ?></td>
                        <?php
                        $types_qry = $this->db->select('sysid,datatype,symbol')
                            ->from('frm_tnc_datatypes')
                            ->where(['subtype' => 4])
                            ->order_by('col ASC')
                            ->get();

                        if ($types_qry->num_rows() > 0) {
                            foreach ($types_qry->result() AS $row) {
                                $val_qry = $this->db->select('tid.value')
                                    ->from('frm_tnc_form_data AS tid')
                                    ->join('frm_tnc_form_details AS tfd','tfd.sysid = tid.detailsid','left')
                                    ->where(['tfd.tncid' => $tncid,'tid.datatype' => $row->sysid,'tid.string' => $n,'tfd.type' => 4,'tid.status' => 1])
                                    ->get()->row();

                                $value = ($val_qry && $val_qry->value) ? 'value="'.$val_qry->value.'"' : '';

                                echo '<td>';
                                if (strpos($row->datatype,'_s') !== false) {
                                    $dataType = substr($row->datatype,0,-2);
                                    echo '<div class="testing-wrapper">';
                                    echo '<input type="'.$dataType.'" class="form-control inline testing-vals" name="aci[inv]['.$n.']['.$row->sysid.']" '.$value.' required><span class="testing-unit">'.$row->symbol.'</span>';
                                    echo '</div>';
                                } else {
                                    echo '<input type="'.$row->datatype.'" class="form-control inline testing-vals" name="aci[inv]['.$n.']['.$row->sysid.']" '.$value.' required>';
                                }
                                echo '</td>';
                            }
                        }
                        ?>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-12">
            Noted:
            <textarea id="aci_note" class="form-control" rows="5" name="aci[details][note]"></textarea>
        </div>
    </div>

</div>
