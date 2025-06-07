<?php
if (isset($inverter) && $inverter > 0) {
    $active = ($inverter == 1) ? 'active' : '';
}


?>
<div class="tab-pane fade in <?php echo $active; ?>" id="tnc_<?php echo $inverter; ?>_stringtest">
    <style>
        #tnc_<?php echo $inverter; ?>_stringtest .table .main-headers th {
            text-align: center !important;
            vertical-align: middle;
        }
        #tnc_<?php echo $inverter; ?>_stringtest .table tr:not(.main-headers) th {
            vertical-align: center;
            /*-webkit-transform: rotate(-90deg);
            -moz-transform: rotate(-90deg);
            -o-transform: rotate(-90deg);
            -ms-transform: rotate(-90deg);
            transform: rotate(-90deg);*/
            text-align: right;
            writing-mode: vertical-rl;
            transform: scale(-1);
            height: 300px;
        }

        #tnc_<?php echo $inverter; ?>_stringtest .table tbody tr td:first-child {
            text-align: center;
        }

        #tnc_<?php echo $inverter; ?>_stringtest .table tbody tr td {
            padding: 3px !important;
        }

    </style>
    <div class="well">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-6">
                    Device No. :
                    <label>INVERTER <?php echo $inverter; ?></label>
                </div>
                <div class="col-md-6">
                    Description : voc
                    <input type="hidden" name="stringtest[<?php echo $inverter; ?>][details][type]" value="1" placeholder="Customer name/number" required />
                </div>
            </div>
        </div>
    </div>
    <div class="well">
        <div class="row">
            <div class="col-md-4">
                Testing Area
                <input class="form-control inline" name="stringtest[<?php echo $inverter; ?>][details][testingarea]" value="<?php echo $stringtest['details']['testingarea'] ?? ''; ?>" placeholder="Testing Area..." style="border-bottom: 1.5px grey solid !important;" required />
            </div>
            <div class="col-md-4">
                Serial Number
                <input class="form-control inline" name="stringtest[<?php echo $inverter; ?>][details][serialnumber]" id="stt_inv_sn" value="<?php echo $stringtest['details']['serialnumber'] ?? ''; ?>" placeholder="Inverter Serial Number..." style="border-bottom: 1.5px grey solid !important;" required />
            </div>
            <div class="col-md-4">
                Date & Time
                <input type="datetime-local" class="form-control inline" name="stringtest[<?php echo $inverter; ?>][details][testdate]" value="<?php echo isset($stringtest['details']['testdate']) ? date('Y-m-d\TH:i',strtotime($stringtest['details']['testdate'])) : date('Y-m-d\TH:i'); ?>" placeholder="MM/DD/YYYY hh:mm AM/PM" style="border-bottom: 1.5px grey solid !important;" >
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                Testing Equipment
                <input class="form-control inline" name="stringtest[<?php echo $inverter; ?>][details][equipment]" id="stt_equipment" value="<?php echo $stringtest['details']['equipment'] ?? ''; ?>" placeholder="Testing Equipment..." style="border-bottom: 1.5px grey solid !important;" required />
            </div>
            <div class="col-md-4">
                Model
                <input class="form-control inline" name="stringtest[<?php echo $inverter; ?>][details][equipmentmodel]" id="stt_eqt_model" value="<?php echo $stringtest['details']['equipmentmodel'] ?? ''; ?>" placeholder="Equipment Model..." style="border-bottom: 1.5px grey solid !important;" required />
            </div>
            <div class="col-md-4">
                Serial Number
                <input class="form-control inline" name="stringtest[<?php echo $inverter; ?>][details][equipmentsn]" id="stt_eqt_sn" value="<?php echo $stringtest['details']['equipmentsn'] ?? ''; ?>" placeholder="Serial Number..." style="border-bottom: 1.5px grey solid !important;" required />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 bold">
            Note/Criteria: Voc should not deviate from Vtheory more than 5%, and Isc should not deviate from Itheory more than 10%
        </div>
    </div>
    <div class="row margin-top-15">
        <div class="col-md-12">
            <table class="table table-bordered" style="width: 100%">
                <?php
                $st_header_qry = $this->db->select('sysid, name, parent, col,datatype,symbol')
                    ->from('frm_tnc_datatypes')
                    ->where(['tnctype' => 4006,'subtype' => 1])
                    ->order_by('parent,col')->get();

                $main_header = [];
                $sub_header = [];

                if ($st_header_qry->num_rows() > 0) {
                    foreach ($st_header_qry->result_array() AS $header) {
                        if ($header['parent'] == 0) {
                            $main_header[$header['sysid']] = $header['name'];
                        } else {
                            $sub_header[$header['parent']][] = $header;
                        }
                    }

                    foreach ($sub_header AS &$sub) {
                        usort($sub, function ($a, $b) {
                            return $a['col'] <=> $b['col'];
                        });
                    }

                    unset($sub);
                }
                ?>
                <thead>
                <tr class="main-headers">
                    <th rowspan="2">String</th>
                    <?php
                    if (count($main_header) > 0) {
                        foreach ($main_header AS $main => $name) {
                            $subCnt = isset($sub_header[$main]) ? count($sub_header[$main]) : 1;
                            echo '<th colspan="'.$subCnt.'">'.$name.'</th>';
                        }
                    }
                    ?>
                    <th>Remark</th>
                </tr>
                <tr>
                    <?php
                    if (count($main_header) > 0) {
                        foreach (array_keys($main_header) AS $mainid) {
                            if (isset($sub_header[$mainid])) {
                                foreach ($sub_header[$mainid] AS $sub) {
                                    echo '<th>'.$sub['name'].'</th>';
                                }
                            } else {
                                echo '<th></th>';
                            }
                        }
                    }
                    ?>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php

                $strings = 20;
                for ($s = 0; $s < $strings; $s++) {
                    ?>
                    <tr>
                        <td><?php echo $s+1; ?></td>
                        <?php
                        foreach (array_keys($main_header) AS $mid) {
                            if (isset($sub_header[$mid])) {
                                foreach ($sub_header[$mid] AS $sub) {
                                    $childId = $sub['sysid'];
                                    $datatype = $sub['datatype'] ?? '';
                                    $symbol = $sub['symbol'] ?? '';
                                    $name = 'stringtest['.$inverter.'][strings]['.($s+1).']['.$childId.']';
                                    $value = $stringtest['strings'][$s+1][$childId] ?? false;

                                    if (substr($datatype,-2) === '_s') {
                                        $baseType = substr($datatype, 0, -2); // strip "_s"
                                        echo '<td>';
                                        echo '<div style="display: flex; align-items: center;">';
                                        echo '<input type="'.$baseType.'" name="'.$name.'" class="form-control inline" '.($value ? 'value="'.$value.'"' : '').' >';
                                        echo '<span style="font-size: x-small; margin-left: 1px !important;">'.htmlspecialchars($symbol).'</span>';
                                        echo '</div>';
                                        echo '</td>';
                                    } else {
                                        $class = ($datatype == 'checkbox' || $datatype == 'radio') ? 'icheck' : 'form-control inline';
                                        $checked = '';
                                        if ($value) {
                                            if ($datatype == 'checkbox') {
                                                $value = 'checked';
                                            } else {
                                                $value = 'value="'.$value.'"';
                                            }
                                        }
                                        echo '<td class="text-align-center">';
                                        echo '<input type="'.$datatype.'" name="'.$name.'" '.$value.' class="'.$class.'" >';
                                        echo '</td>';
                                    }
                                }
                            }
                        }
                        ?>
                        <td class="text-align-center"><input name="stringtest[<?php echo $inverter; ?>][strings][<?php echo $s+1; ?>][0]" value="<?php echo $stringtest['strings'][$s+1][0] ?? ''; ?>"  class="form-control inline"></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
