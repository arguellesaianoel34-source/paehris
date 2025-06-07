<?php
$this->load->helper('forms');
$pae_letter_head = FCPATH . 'assets/global/img/logo/pae-nav-logo-blue-border.png';
$documentNumber = 0;
if ($tncid) {

    $tnc_qry = $this->db->select('sysid AS tncid,appid,company,partner,client,holdings,invertercount,buildtype,location,projectname,dateconducted')
        ->from('frm_tnc_main')
        ->where(array('sysid' => $tncid,'status' => 1))
        ->get()->row();

    $company_inspectors = array();
    if ($tnc_qry) {
        foreach ($tnc_qry AS $key => $value) {
            if (strpos($value,';') !== false) {
                list($$key,${$key . 'acronym'}) = explode(';',$value);
            } else {
                $$key = $value;
            }

            if ($key == 'buildtype') {
                $buildtype = $buildtype ?: $tnc_qry->buildtype;
            }
        }

        if (isset($partneracronym)) {
            $company_inspectors[] = $partneracronym;
        }
        if (isset($companyacronym)) {
            $company_inspectors[] = $companyacronym;
        }
    }

//Step 1: Get top-level rows with buildtype = $buildtype (4004,4005,4006)
//Step 2: Recursively get all children of those rows
    $sql = "
        WITH RECURSIVE items_cte AS (
        SELECT * FROM frm_tnc_checklist_items WHERE buildtype = $buildtype AND subtype = 8
    
        UNION ALL
    
        SELECT c.* 
        FROM frm_tnc_checklist_items AS c
        JOIN items_cte p ON c.parent = p.sysid
    )
    SELECT * FROM items_cte ORDER BY parent,`order`;
    
";
    $frm_query = $this->db->query($sql);

    $indexed = array();
    if ($frm_query->num_rows() > 0) {
        foreach ($frm_query->result() as $row) {
            $indexed[$row->sysid] = (array)$row;
        }
    }

// Build tree from top-level parent (0)
    $checklist_items = buildChecklistTree(0, $indexed);

    $directory = FCPATH . 'uploads/attachments/tnc/'.$appid;
    $map = directory_map($directory, false, true);
    $imgs = array();

    //CONVERT MAPPED TNC IMAGES INTO MODULAR
    if ($map && count($map) > 0) {
        foreach ($map as $file) {
            // Remove the file extension
            $basename = pathinfo($file, PATHINFO_FILENAME);

            // Split the filename by underscores
            $parts = explode('_', $basename);

            // Initialize reference to the main array
            $ref = &$imgs;

            // Process each part
            for ($i = 0; $i < count($parts); $i++) {
                $isLastPart = ($i === count($parts) - 1); // Check if this is the last part

                // Match alphabetic key and optional numeric index
                if ($matches = splitLettersNumbers($parts[$i])) {
                    $key = $matches[1]; // Alphabetic key
                    $index = isset($matches[2]) && $matches[2] !== '' ? (int)$matches[2] : null; // Numeric index if present

                    // If it's the last part and the key length is less than 2, keep it unchanged
                    if ($isLastPart && strlen($key) < 3) {
                        $key = $parts[$i]; // Keep the entire last part as the key (do not split)
                        $index = null; // No separate index
                    }

                    // Create nested structure
                    if ($index !== null) {
                        if (!isset($ref[$key])) {
                            $ref[$key] = [];
                        }
                        if (!isset($ref[$key][$index])) {
                            $ref[$key][$index] = [];
                        }
                        $ref = &$ref[$key][$index]; // Move reference
                    } else {
                        if (!isset($ref[$key])) {
                            $ref[$key] = [];
                        }
                        $ref = &$ref[$key]; // Move reference
                    }
                }
            }

            // Assign the filename to the last nested level
            $ref = FCPATH . 'uploads/attachments/tnc/'.$appid.'/' . $file;
        }
    }
    ?>
    <html>
    <meta charset="UTF-8">
    <head>
        <title></title>
        <style>
            .frm-checkbox:before {
                font-size: 20pt;
                font-family: DejaVu Sans;
                margin-right: .100em;
                margin-left: .100em;
                font-style: normal;
            }

            .frm-checkbox.unchecked:before {
                content: '\2610';
            }

            .frm-checkbox.checked:before {
                content: '\2611';
            }

            @page {
                size: 8.5in 13in;
                margin: 0.5in;
            }

            body {
                font-family: Arial, sans-serif, "DejaVu Sans";
                font-size: 10px;
                line-height: 1.2;
                margin: 0;
                padding: 0;
                color: #000;
            }

            .page {
                page-break-after: always;
                min-height: 12in;
            }

            .page:last-child {
                page-break-after: avoid;
            }

            .header {
                text-align: center;
                font-weight: bold;
                font-size: 14px;
                margin-bottom: 15px;
                border-bottom: 2px solid #000;
                padding-bottom: 5px;
            }

            .project-info {
                margin-bottom: 15px;
            }

            .project-info table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 10px;
            }

            .project-info td {
                border: 1px solid #000;
                padding: 4px;
                font-weight: bold;
            }

            .checklist-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 15px;
                page-break-inside: avoid;
            }

            .checklist-table th,
            .checklist-table td {
                border: 1px solid #000;
                padding: 4px;
                vertical-align: top;
                text-align: left;
            }

            .checklist-table th {
                background-color: #f0f0f0;
                font-weight: bold;
                text-align: center;
            }

            .item-number {
                width: 30px;
                text-align: center;
                font-weight: bold;
            }

            .component-desc {
                width: 45%;
            }

            .complied {
                width: 60px;
                text-align: center;
            }

            .remarks {
                width: 20%;
            }

            .representative {
                width: 12%;
                text-align: center;
            }

            .signature-section {
                margin-top: 20px;
                page-break-inside: avoid;
            }

            .signature-table {
                width: 100%;
                border-collapse: collapse;
            }

            .signature-table tr:last-child td {
                text-align: center;
            }

            .signature-table td {
                border: 1px solid #000;
                font-weight: bold;
            }

            .signature-row td{
                text-align: center;
                height: 20px;
            }

            .bullet-list {
                margin: 0;
                padding-left: 15px;
                list-style: none;
            }

            .bullet-list li {
                margin-bottom: 3px;
                position: relative;
            }

            .bullet-list li:before {
                content: "•";
                position: absolute;
                left: -10px;
            }

            .powered-by {
                position: fixed;
                bottom: 10px;
                right: 10px;
                font-size: 8px;
                color: #666;
            }

            .testing-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 8px;
            }

            .testing-table th,
            .testing-table td {
                border: 1px solid #000;
                padding: 2px;
                text-align: center;
            }

            .testing-header {
                background-color: #f0f0f0;
                font-weight: bold;
            }

            .form-field {
                display: inline-block;
                border-bottom: 1px solid #000;
                min-width: 100px;
                margin: 0 5px;
            }

            .checkbox {
                font-family: DejaVu Sans, sans-serif;
                font-size: 15pt;
                text-align: center;
            }

            .checkbox .checked::before {
                content: content: '\2611';
                font-size: 20px;
                font-weight: bold;
            }

            .checkbox .unchecked::before {
                content: content: '\2610';
                font-size: 20px;
                font-weight: bold;
            }

            .detailed-checklist-table tbody {
                page-break-inside: avoid;
            }

            .checklist-header {
                border: 1px solid #000;
            }

            .checklist-main {
                font-weight: bold;
                padding-left: 5px;
            }

            /*.checklist-item .bullet::before {
                content:  "•";
                font-size: 150%;
                padding-right: 5px;
                width: 15px;
            }*/

            .checklist-item .bullet {
                font-size: 150%;
                display: inline-block;
                vertical-align: top;
            }

            .checklist-item .content {
                display: inline-block;
                vertical-align: middle;
                padding-left: 5px;
                width: 90%;
            }

            .checkbox-row {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 3px;
            }

            .detailed-checklist-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 15px;
            }

            .detailed-checklist-table th {
                border: 1px solid #000;
                padding: 3px;
                vertical-align: top;
                font-size: 12px;
            }
            .detailed-checklist-table td {
                border-left: 1px solid #000;
                border-right: 1px solid #000;
                padding: 3px;
                vertical-align: top;
                font-size: 12px;
            }

            .detailed-checklist-table th {
                background-color: #f0f0f0;
                font-weight: bold;
                text-align: center;
            }

            .detailed-checklist-table tr:last-child td {
                border-bottom: 1px solid #000 !important;
            }

            .detailed-checklist-table tr:first-child td {
                border-top: 1px solid #000 !important;
            }

            .item-desc {
                width: 40%;
                text-align: left;
            }

            .check-col {
                width: 8%;
                text-align: center;
            }

            .remarks-col {
                width: 18%;
            }

            .rep-col {
                width: 15%;
                text-align: center;
            }

            /* Underline String Test detail if blank */
            .stringtest-details {
                /*display: flex;*/
            }
            .stringtest-details .sttLabel {
                font-weight: bold;
            }
            .stringtest-details .sttValue {
                border-bottom: 1px solid #000;
                min-height: 1em;
                text-align: center;
                width: 100%;
            }
        </style>
    </head>
    <body>
    <header>
        <img src="<?php //echo $pae_letter_head; ?>" width="100%"/>
    </header>
    <main>
        <!-- VOC Checklist -->
        <?php
        //CHECKLIST AREA
        $first = key($checklist_items);
        foreach ($checklist_items AS $clkey => $checklist) {
            $tbody = '';
            $trows = '';
            $documentNumber++;
            echo '<div class="page">';
            echo '<div style="padding-bottom: 10px"><img src="'.$pae_letter_head.'" /></div>';
            echo '<div class="header">PV SYSTEM INSPECTION CHECKLIST - '.strtoupper($checklist['values']['item']).'</div>';
            echo '<div class="project-info">';
            echo '<table>';
            echo '<tr>';
            echo '<td style="width: 60%;">PROJECT NAME: '.$projectname.'</td>';
            echo '<td style="width: 40%;">DATE CONDUCTED: '.$dateconducted.'</td>';
            echo '</tr>';
            echo '<td style="width: 60%;">LOCATION: '.$location.'</td>';
            echo '<td style="width: 40%;">DOCUMENT NUMBER: '.$documentNumber.'</td>';
            echo '</tr>';
            echo '</table>';
            echo '</div>';
            ?>
            <table class="detailed-checklist-table">
                <thead>
                <tr>
                    <th rowspan="2" class="item-desc">COMPONENTS</th>
                    <th colspan="2" class="check-col">COMPLIED</th>
                    <th rowspan="2" class="remarks-col">REMARKS</th>
                </tr>
                <tr>
                    <th class="rep-col"><?php echo (count($company_inspectors) > 0) ? implode(' / ',$company_inspectors) : ''; ?> <br>Representative</th>
                    <th class="rep-col"><?php echo $clientacronym ?? $client; ?> <br>Representative</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="4" class="checklist-header" style="font-weight: bold; text-align: center;"><?php echo strtoupper($checklist['values']['item']); ?></td>
                </tr>
                <?php
                $i = 1;
                foreach ($checklist['children'] AS $itemid => $item) {
                    if (isset($item['children'])) {
                        $values = $item['values'];
                        //echo '<tbody class="checklist-item-group" style="page-break-inside: avoid !important;">';
                        echo '<tr class="checklist-main"><td>'.$i.'. '.$values['item'].'</td><td></td><td></td><td></td></tr>';
                        foreach ($item['children'] AS $childid => $selections) {
                            $val = $selections['values'] ?? $selections;
                            $isChecked = [];
                            //Lookup for values
                            $response_qry = $this->db->select()
                                ->from('frm_tnc_checklist_responses')
                                ->where(['itemid' => $val['sysid'],'tncid' => $tncid,'status' => 1])
                                ->get()->row();

                            if ($response_qry) {
                                if (isset($val['hasinput']) && $val['hasinput']) {
                                    $val['inputval'] = $response_qry->field;
                                }
                                $check = explode(';',$response_qry->check);
                                foreach ($check AS $box => $checked) {
                                    //$isChecked[$box] = $checked ? '&#9745;' : '&#9744;';
                                    $isChecked[$box] = $checked ? 'checked' : 'unchecked';
                                }
                            }
                            ?>
                            <tr class="checklist-item">
                                <td style="padding-left: 15px;">
                                    <span class="bullet">&#8226;</span>
                                    <span class="content">
                                        <?php echo (isset($val['hasinput']) && $val['hasinput']) ? checklist_item_input($val,true) : htmlentities($val['item']); ?>
                                    </span>
                                </td>
                                <td class="checkbox"><i class="<?php echo $isChecked[0] ?? false; ?>"></i></td>
                                <td class="checkbox"><i class="<?php echo $isChecked[1] ?? false; ?>"></i></td>
                                <td><?php echo $response_qry ? $response_qry->remarks : false; ?></td>
                            </tr>
                        <?php }
                        //echo '</tbody>';
                    } else {
                        $isChecked = [];
                        //Lookup for values
                        $response_qry = $this->db->select()
                            ->from('frm_tnc_checklist_responses')
                            ->where(['itemid' => $itemid,'tncid' => $tncid,'status' => 1])
                            ->get()->row();

                        if ($response_qry) {
                            if (isset($item['hasinput']) && $item['hasinput']) {
                                $item['inputval'] = $response_qry->field;
                            }
                            $check = explode(';',$response_qry->check);
                            foreach ($check AS $box => $checked) {
                                $isChecked[$box] = $checked ? 'checked' : 'unchecked';
                            }
                        }

                        if ($item['type'] != 'external') {
                            $tbody .= '<tr class="'.($item['type'] != 'note' ? 'checklist-item' : '').'">';
                            if ($item['type'] == 'check') {
                                $tbody .= '<td style="padding-left: 15px; padding-top: 5px">';
                                $tbody .= '<span class="bullet">&#8226;</span><span class="content">';
                                $tbody .= ((isset($item['hasinput']) && $item['hasinput']) ? checklist_item_input($item,true) : $item['item']);
                                $tbody .= '</span></td>';
                                $tbody .= '<td class="checkbox"><i class="'.($isChecked[0] ?? false).'"></i></td>';
                                $tbody .= '<td class="checkbox"><i class="'.($isChecked[1] ?? false).'"></i></td>';
                                $tbody .= '<td>'.($response_qry ? $response_qry->remarks : false).'</td>';
                            } else {
                                $tbody .= (isset($item['hasinput']) && $item['hasinput']) ? checklist_item_input($item,true) : $item['item'];
                            }

                            $tbody .= '</tr>';
                        } else {
                            $trows .= '<tr class="item-desc">';
                            $trows .= (isset($item['hasinput']) && $item['hasinput']) ? checklist_item_input($item,true) : $item['item'];
                            $trows .= '</tr>';
                        }
                    }
                    $i++;
                }

                if ($tbody != '') {
                    //echo '<tbody class="checklist-item-group">';
                    echo $tbody;
                    //echo '</tbody>';
                }
                ?>
                </tbody>
            </table>
            <?php
            if ($trows != '') { ?>
                <table class="table table-condensed checklist-table">
                    <?php echo $trows; ?>
                </table>
            <?php } ?>
            <div class="signature-section">
                <table class="signature-table">
                    <tr>
                        <td style="width: 50%;">INSPECTED BY:</td>
                        <td style="width: 50%;">INSPECTED BY:</td>
                    </tr>
                    <tr class="signature-row">
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><?php echo $partner ?? false; ?></td>
                        <td><?php echo $company ?? false; ?></td>
                    </tr>
                </table>
            </div>

            <div class="signature-section">
                <table class="signature-table">
                    <tr>
                        <td style="width: 50%;">WITNESSED BY:</td>
                        <td style="width: 50%;">WITNESSED BY:</td>
                    </tr>
                    <tr class="signature-row">
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><?php echo $client ?? false; ?></td>
                        <td><?php echo $holdings ?? false; ?></td>
                    </tr>
                </table>
            </div>

            <?php
            echo '</div>';
        }
        ?>
        <!-- String Test Result -->
        <?php
        //FOR STRINGTEST DATA
        $stt = [];
        $stt_details_qry = $this->db->select(
                'sysid,
                inverter,
                testingarea,
                invsn AS serialnumber,
                testdate,
                equipment,
                eqtmodel AS equipmentmodel,
                eqtsn AS equipmentsn,
                temp,
                humidity,
                accepted,
                note
        ')
            ->from('frm_tnc_form_details')
            ->where(['tncid' => $tncid,'type' => 1,'status' => 1])
            ->get();

        if ($stt_details_qry->num_rows() > 0) {
            foreach ($stt_details_qry->result() AS $stt_detail) {

                $inverterN = $stt_detail->inverter;
                $details_id = $stt_detail->sysid;
                unset($stt_detail->inverter,$stt_detail->sysid);

                $stt[$inverterN]['details'] = (array)$stt_detail;

                $stt_strings_qry = $this->db->select('string,datatype,value')
                    ->from('frm_tnc_form_data')
                    ->where(['detailsid' => $details_id,'status' => 1])
                    ->get();

                if ($stt_strings_qry->num_rows() > 0) {
                    foreach ($stt_strings_qry->result() AS $string) {
                        $stt[$inverterN]['strings'][$string->string][$string->datatype] = $string->value;
                    }
                }
            }
        }

        if (isset($invertercount)) {
            for ($inv = 0; $inv < $invertercount; $inv++) {
                $documentNumber++;
                $inverter = $inv+1;

                $stt_accepted = $stt[$inverter]['details']['accepted'] ? '&#9745;' : '&#9744;';
                $stt_accepted_yes = '&#9744;';
                $stt_accepted_no = '&#9744;';

                if (isset($stt[$inverter]['details']['accepted'])) {
                    if ($stt[$inverter]['details']['accepted'] == 1) {
                        $stt_accepted_yes = '&#9745;';
                    } else {
                        $stt_accepted_no = '&#9745;';
                    }
                }
            ?>
            <div class="page">
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

                    .tnc-<?php echo $inverter; ?>-strtingtest-header {
                        border: 1px black solid;
                        text-align: center;
                        font-weight: bold;
                        font-size: 14px;
                        width: 50%
                    }

                    .tnc-<?php echo $inverter; ?>-strtingtest-details {
                        border: 0;
                        border-collapse: collapse;
                    }

                </style>
                <table collapse="0" style="border-collapse: collapse; width: 100%">
                    <tr>
                        <td class="tnc-<?php echo $inverter; ?>-strtingtest-header" style="width: 80%">
                            TESTING AND COMMISSIONING
                        </td>
                        <td class="tnc-<?php echo $inverter; ?>-strtingtest-header">
                            <img src="<?php echo $pae_letter_head; ?>" />
                        </td>
                    </tr>
                </table>

                <div class="project-info">
                    <table style="font-size: 9px;">
                        <tr>
                            <td style="width: 50%;">Project: <?php echo $projectname ?? false; ?></td>
                            <td style="width: 50%;">Form type: COMMISSIONING</td>
                        </tr>
                        <tr>
                            <td style="width: 50%;">Device No.: Inverter <?php echo $inverter ?? false; ?></td>
                            <td style="width: 50%;">Description: Voc</td>
                        </tr>
                        <tr>
                            <td style="width: 50%;">Location: <?php echo $location ?? false; ?></td>
                            <td style="width: 50%;" >Document No.: <?php echo $documentNumber ?? false; ?></td>
                        </tr>
                    </table>
                </div>
                <h3>PV MODULE STRING TEST</h3>

                <table class="tnc-<?php echo $inverter; ?>-strtingtest-details" style="width: 66.66%">
                    <tr class="stringtest-details">
                        <td class="sttLabel" style="width: 13%">Testing Area :</td>
                        <td class="sttValue" style="width: 18%"><?php echo $stt[$inverter]['details']['testingarea'] ?? false; ?></td>
                        <td class="sttLabel" style="width: 2%"><!--Space holder--></td>
                        <td class="sttLabel" style="width: 13%">Serial number :</td>
                        <td class="sttValue" style="width: 18%"><?php echo $stt[$inverter]['details']['serialnumber'] ?? false; ?></td>
                    </tr>
                </table>
                <table class="tnc-<?php echo $inverter; ?>-strtingtest-details" style="width: 66.66%; margin-top: 5px">
                    <tr class="stringtest-details">
                        <td class="sttLabel" style="width: 13%">Date :</td>
                        <td class="sttValue" style="width: 18%"><?php echo isset($stt[$inverter]['details']['testdate']) ? date('F j, Y',strtotime($stt[$inverter]['details']['testdate'])) : '&nbsp;'; ?></td>
                        <td class="sttLabel" style="width: 2%"><!--Space holder--></td>
                        <td class="sttLabel" style="width: 13%">Time :</td>
                        <td class="sttValue" style="width: 18%"><?php echo isset($stt[$inverter]['details']['testdate']) ? date('h:i A',strtotime($stt[$inverter]['details']['testdate'])) : '&nbsp;'; ?></td>
                    </tr>
                </table>
                <table class="tnc-<?php echo $inverter; ?>-strtingtest-details" style="width: 66.66%; margin-top: 5px">
                    <tr class="stringtest-details">
                        <td class="sttLabel" style="width: 13%">Ambient Temp :</td>
                        <td class="sttValue" style="width: 18%"><?php echo $stt[$inverter]['details']['temp'] ?? false; ?></td>
                        <td class="sttLabel" style="width: 2%"><!--Space holder--></td>
                        <td class="sttLabel" style="width: 13%">Humidity (%) :</td>
                        <td class="sttValue" style="width: 18%"><?php echo $stt[$inverter]['details']['humidity'] ?? false; ?></td>
                    </tr>
                </table>
                <table class="tnc-<?php echo $inverter; ?>-strtingtest-details" style="width: 100%; margin-top: 5px">
                    <tr class="stringtest-details">
                        <td class="sttLabel" style="width: 14%">Testing Equipment :</td>
                        <td class="sttValue" style="width: 18%"><?php echo $stt[$inverter]['details']['equipment'] ?? false; ?></td>
                        <td class="sttLabel" style="width: 1%"><!--Space holder--></td>
                        <td class="sttLabel" style="width: 13%">Model :</td>
                        <td class="sttValue" style="width: 18%"><?php echo $stt[$inverter]['details']['eqtuipmentmodel'] ?? false; ?></td>
                        <td class="sttLabel" style="width: 2%"><!--Space holder--></td>
                        <td class="sttLabel" style="width: 13%">Serial Number :</td>
                        <td class="sttValue" style="width: 18%"><?php echo $stt[$inverter]['details']['equipmentsn'] ?? false; ?></td>
                    </tr>
                </table>

                <div style="padding-top: 15px">
                    <span style="font-weight: bold;">Note/Criteria:</span> Voc should not deviate from Vtheory more than 5%, and Isc should not deviate from Itheory more than 10%.
                </div>
                <div id="tnc_<?php echo $inverter; ?>_stringtest" style="padding-top: 15px">
                    <table class="testing-table table">
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
                            <th rowspan="2"style="width: 5%">String</th>
                            <?php
                            if (count($main_header) > 0) {
                                foreach ($main_header AS $main => $name) {
                                    $subCnt = isset($sub_header[$main]) ? count($sub_header[$main]) : 1;
                                    echo '<th colspan="'.$subCnt.'">'.$name.'</th>';
                                }
                            }
                            ?>
                            <th style="width: 10%">Remark</th>
                        </tr>
                        <tr>
                            <?php
                            if (count($main_header) > 0) {
                                foreach (array_keys($main_header) AS $mainid) {
                                    if (isset($sub_header[$mainid])) {
                                        foreach ($sub_header[$mainid] AS $sub) {
                                            $thSub = textToImage($sub['name'],false,90,false,8,0,'right');

                                            echo '<th style="width: 6%; padding: unset; vertical-align: top">'.$thSub.'</th>';
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
                                            $value = $stt[$inverter]['strings'][$s+1][$childId] ?? false;

                                            if (substr($datatype,-2) === '_s') {
                                                $baseType = substr($datatype, 0, -2); // strip "_s"
                                                echo '<td>';
                                                echo $value ? $value.'<span style="font-family: DejaVu Sans">'.$symbol.'</span>' : '';
                                            } else {
                                                if ($datatype == 'checkbox' || $datatype == 'radio') {
                                                    //$class = $value ? 'checked' : 'unchecked';
                                                    $class = $value ? '&#10003;' : '&#9744;';
                                                    echo '<td class="checkbox">';
                                                    //echo '<span class="'.$class.'"></span>';
                                                    echo '<span style="font-size: 12px; line-height: 1; padding: 0; width: 12px; text-align: center;">'.$class.'</span>';
                                                } else {
                                                    echo '<td>';
                                                    echo $value ?? '';
                                                }
                                            }
                                            echo '</td>';
                                        }
                                    }
                                }
                                ?>
                                <td><?php echo $stt[$inverter]['strings'][$s+1][0] ?? ''; ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: 15px; font-size: 9px;">
                    <div style="margin-bottom: 10px;">
                        <strong>Check:</strong>
                        <span class="checkbox"><?php echo $stt_accepted_yes; ?></span> Accepted
                        <span class="checkbox"><?php echo $stt_accepted_no; ?></span> Not Accepted
                    </div>
                    <div style="margin-bottom: 15px;">
                        <strong>Noted:</strong> <span class="form-field" style="min-width: 300px;"><?php echo $stt[$inverter]['details']['note'] ?? false; ?></span>
                    </div>
                    <table style="width: 100%; border-collapse: collapse; font-size: 9px;">

                        <tr>
                            <th></th>
                            <th style="border: 0; padding: 5px; text-align: center; font-weight: bold;">Confirmed by</th>
                            <th style="border: 0; padding: 5px; text-align: center; font-weight: bold;">Checked by</th>
                            <th style="border: 0; padding: 5px; text-align: center; font-weight: bold;">Witnessed by</th>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;">Company</td>
                            <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;"><?php echo $partner ?? ''; ?></td>
                            <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;"><?php echo $company ?? ''; ?></td>
                            <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;"><?php echo $holdings ?? ''; ?></td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 5px; font-weight: bold;">Signature</td>
                            <td style="border: 1px solid #000; padding: 5px; height: 30px;"></td>
                            <td style="border: 1px solid #000; padding: 5px; height: 30px;"></td>
                            <td style="border: 1px solid #000; padding: 5px; height: 30px;"></td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 5px; font-weight: bold;">Name</td>
                            <td style="border: 1px solid #000; padding: 5px;"></td>
                            <td style="border: 1px solid #000; padding: 5px;"></td>
                            <td style="border: 1px solid #000; padding: 5px;"></td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 5px; font-weight: bold;">Date</td>
                            <td style="border: 1px solid #000; padding: 5px;"></td>
                            <td style="border: 1px solid #000; padding: 5px;"></td>
                            <td style="border: 1px solid #000; padding: 5px;"></td>
                        </tr>
                    </table>
                </div>
            </div>
                <?php
            }
        }
        ?>
            <!--<style>
                .table-container {
                    display: flex;
                    flex-direction: column;
                    width: 100%;
                    border: 2px solid #333;
                    font-family: Arial, sans-serif;
                }
                .table-row {
                    display: flex;
                    width: 100%;
                }
                .table-cell {
                    flex: 1;
                    /*padding: 10px;*/
                    border: 1px solid #999;
                    text-align: center;
                }
                .inverter-row .inverter-cell {
                    flex: 1 1 100%; /* Full width */
                    background-color: #ddd;
                    font-weight: bold;
                    text-align: center;
                    font-size: 18px;
                    padding: 10px;
                }
                .header-row .header-cell {
                    font-weight: bold;
                    background-color: #f4f4f4;
                }

                .table-cell.img {
                    flex: 1;
                    padding: 0; /* Remove padding for better image fitting */
                    border: 1px solid #999;
                    text-align: center;
                    height: 120px; /* Set a fixed height for uniformity */
                    position: relative;
                    overflow: hidden; /* Prevent overflow */
                }
                .table-cell img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover; /* Ensures image fully covers the cell */
                    display: block;
                }
            </style>-->
        <div id="pics_stt" class="page">
            <style>
                #pics_stt table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                #pics_stt td, #pics_stt th {
                    text-align: center;
                    padding: 5px;
                    font-size: 10px;
                }
                #pics_stt .inverter-title {
                    font-weight: bold;
                    background: #ccc;
                    padding: 5px;
                    text-align: center;
                    font-size: 12px;
                }
                #pics_stt img {
                    width: 100px;
                    height: auto;
                    border: 1px solid #aaa;
                }

                .tbl-stt-pics tr,
                .tbl-stt-pics td, {
                    border: 1px solid #000;
                }
            </style>
            <?php
            if (isset($imgs) && count($imgs) > 0) {

                $maxColumns = 5; // Maximum images per row
                if (isset($imgs['voc']['inv']) && count($imgs['voc']['inv']) > 0) {
                    echo '<h2>Pictures</h2>';
                    echo '<h3 class="bold">Voc Test</h3>';
                    $voc = [];
                    foreach ($imgs['voc']['inv'] AS $invNumber => $inverter) {
                        foreach ($inverter AS $str) {
                            foreach ($str AS $strNumber => $imgURL) {
                                $voc['Inverter ' . $invNumber . '-String ' . $strNumber] = $imgURL;
                            }
                        }
                    }

                    if (count($voc) > 0) {
                        $chunks = array_chunk($voc, $maxColumns, true);
                        echo '<table class="tbl-stt-pics" style="border-collapse: collapse; border: 1px solid #000">';
                        foreach ($chunks AS $chunk) {
                            $vocLabel = [];
                            $vocImage = [];
                            $closingCells = $maxColumns;
                            foreach ($chunk AS $label => $image) {
                                $vocLabel[] = $label;
                                $vocImage[] = $image;
                                $closingCells--;
                            }
                            if (count($vocLabel) > 0) {
                                echo '<tr>';
                                foreach ($vocLabel AS $picLabel) {
                                    echo '<td style="text-align: center; font-weight: bold">'.$picLabel.'</td>';
                                }
                                for ($cell = 0;$cell < $closingCells;$cell++) {
                                    echo '<td></td>';
                                }
                                echo '</tr>';
                                echo '<tr>';
                                foreach ($vocImage AS $picImage) {
                                    echo '<td style="text-align: center;"><img src="' . $picImage . '" alt="Image"></td>';
                                }
                                for ($cell = 0;$cell < $closingCells;$cell++) {
                                    echo '<td></td>';
                                }
                                echo '</tr>';
                            }

                        }
                        echo '</table>';
                    }
                }

                if (isset($imgs['pol']['inv']) && count($imgs['voc']['inv']) > 0) {
                    echo '<h2>Pictures</h2>';
                    echo '<h3 class="bold">Polarity Test</h3>';
                    $pol = [];
                    foreach ($imgs['pol']['inv'] AS $invNumber => $inverter) {
                        foreach ($inverter AS $str) {
                            foreach ($str AS $strNumber => $imgURL) {
                                $pol['Inverter ' . $invNumber . '-String ' . $strNumber] = $imgURL;
                            }
                        }
                    }

                    if (count($pol) > 0) {
                        $chunks = array_chunk($pol, $maxColumns, true);
                        echo '<table class="tbl-stt-pics" style="border-collapse: collapse; border: 1px solid #000">';
                        foreach ($chunks AS $chunk) {
                            $polLabel = [];
                            $polImage = [];
                            $closingCells = $maxColumns;
                            foreach ($chunk AS $label => $image) {
                                $polLabel[] = $label;
                                $polImage[] = $image;
                                $closingCells--;
                            }
                            if (count($polLabel) > 0) {
                                echo '<tr>';
                                foreach ($polLabel AS $picLabel) {
                                    echo '<td style="text-align: center; font-weight: bold">'.$picLabel.'</td>';
                                }
                                for ($cell = 0;$cell < $closingCells;$cell++) {
                                    echo '<td></td>';
                                }
                                echo '</tr>';
                                echo '<tr>';
                                foreach ($polImage AS $picImage) {
                                    echo '<td style="text-align: center;"><img src="' . $picImage . '" alt="Image"></td>';
                                }
                                for ($cell = 0;$cell < $closingCells;$cell++) {
                                    echo '<td></td>';
                                }
                                echo '</tr>';
                            }

                        }
                        echo '</table>';
                    }
                }


            }

            ?>
        </div>
        <!-- Continuity Testing Result -->
        <?php
        if (isset($invertercount)) {
            for ($cttInv = 0; $cttInv < $invertercount; $cttInv++) {
                $inverter = $cttInv + 1;
                $documentNumber++;
                ?>
                <div class="page">
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

                        .tnc-<?php echo $inverter; ?>-ctt-header {
                            border: 1px black solid;
                            text-align: center;
                            font-weight: bold;
                            font-size: 14px;
                            width: 50%
                        }

                        .tnc-<?php echo $inverter; ?>-ctt-details {
                            border: 0;
                            border-collapse: collapse;
                        }

                    </style>
                    <table collapse="0" style="border-collapse: collapse; width: 100%">
                        <tr>
                            <td class="tnc-<?php echo $inverter; ?>-ctt-header" style="width: 80%">
                                TESTING AND COMMISSIONING
                            </td>
                            <td class="tnc-<?php echo $inverter; ?>-ctt-header" style="height: 15px">
                                <img src="<?php echo $pae_letter_head; ?>" />
                            </td>
                        </tr>
                    </table>

                    <div class="project-info">
                        <table style="font-size: 9px;">
                            <tr>
                                <td style="width: 50%;">Project: <?php echo $projectname ?? false; ?></td>
                                <td style="width: 50%;">Form type: COMMISSIONING</td>
                            </tr>
                            <tr>
                                <td style="width: 50%;">Device No.: Inverter <?php echo $inverter ?? false; ?></td>
                                <td style="width: 50%;">Description: CONTINUITY TEST</td>
                            </tr>
                            <tr>
                                <td style="width: 50%;">Location: <?php echo $location ?? false; ?></td>
                                <td style="width: 50%;" >Document No.: <?php echo $documentNumber ?? false; ?></td>
                            </tr>
                        </table>
                    </div>
                    <h3>CONTINUITY TEST</h3>
                    <table class="tnc-<?php echo $inverter; ?>-strtingtest-details" style="width: 33.33%">
                        <tr class="stringtest-details">
                            <td class="sttLabel" style="width: 13%">Testing Area :</td>
                            <td class="sttValue" style="width: 18%"><?php echo $stt[$inverter]['details']['testingarea'] ?? false; ?></td>
                        </tr>
                    </table>
                    <table class="tnc-<?php echo $inverter; ?>-strtingtest-details" style="width: 66.66%; margin-top: 5px">
                        <tr class="stringtest-details">
                            <td class="sttLabel" style="width: 13%">Date :</td>
                            <td class="sttValue" style="width: 18%"><?php echo isset($stt[$inverter]['details']['testdate']) ? date('F j, Y',strtotime($stt[$inverter]['details']['testdate'])) : '&nbsp;'; ?></td>
                            <td class="sttLabel" style="width: 2%"><!--Space holder--></td>
                            <td class="sttLabel" style="width: 13%">Time :</td>
                            <td class="sttValue" style="width: 18%"><?php echo isset($stt[$inverter]['details']['testdate']) ? date('h:i A',strtotime($stt[$inverter]['details']['testdate'])) : '&nbsp;'; ?></td>
                        </tr>
                    </table>
                    <table class="tnc-<?php echo $inverter; ?>-strtingtest-details" style="width: 66.66%; margin-top: 5px">
                        <tr class="stringtest-details">
                            <td class="sttLabel" style="width: 13%">Ambient Temp :</td>
                            <td class="sttValue" style="width: 18%"><?php echo $stt[$inverter]['details']['temp'] ?? false; ?></td>
                            <td class="sttLabel" style="width: 2%"><!--Space holder--></td>
                            <td class="sttLabel" style="width: 13%">Humidity (%) :</td>
                            <td class="sttValue" style="width: 18%"><?php echo $stt[$inverter]['details']['humidity'] ?? false; ?></td>
                        </tr>
                    </table>
                    <table class="tnc-<?php echo $inverter; ?>-strtingtest-details" style="width: 100%; margin-top: 5px">
                        <tr class="stringtest-details">
                            <td class="sttLabel" style="width: 14%">Testing Equipment :</td>
                            <td class="sttValue" style="width: 18%"><?php echo $stt[$inverter]['details']['equipment'] ?? false; ?></td>
                            <td class="sttLabel" style="width: 1%"><!--Space holder--></td>
                            <td class="sttLabel" style="width: 13%">Model :</td>
                            <td class="sttValue" style="width: 18%"><?php echo $stt[$inverter]['details']['eqtuipmentmodel'] ?? false; ?></td>
                            <td class="sttLabel" style="width: 2%"><!--Space holder--></td>
                            <td class="sttLabel" style="width: 13%">Serial Number :</td>
                            <td class="sttValue" style="width: 18%"><?php echo $stt[$inverter]['details']['equipmentsn'] ?? false; ?></td>
                        </tr>
                    </table>
                    <div style="margin-bottom: 10px; margin-top: 15px">
                        <strong>Note/Criteria:</strong> Tester shall beep due to conduction.
                    </div>
                    <!-- Pics here -->
                    <table style="width: 100%; height: 350px">
                        <?php

                        if (isset($imgs['ctt']['inv'][$inverter])) {
                            $cttImg = $imgs['ctt']['inv'][$inverter];
                            $chunks = array_chunk($cttImg, 2, true);
                            foreach ($chunks AS $chunk) {
                                echo '<tr>';
                                foreach ($chunk AS $picLabel => $picURL) {
                                    echo '<td style="text-align: center; font-weight: bold; width: 50%;">';
                                    echo '<div style="margin-top: 15px">'.strtoupper($picLabel).'</div>';
                                    echo '<div><img src="' . $picURL . '" alt="Image"></div>';
                                    echo '</td>';
                                }
                                echo '</tr>';
                            }
                        }

                        ?>
                    </table>
                    <div style="margin-top: 15px; font-size: 9px;">
                        <div style="margin-bottom: 10px;">
                            <strong>Check:</strong>
                            <span class="checkbox"><?php echo $stt_accepted_yes; ?></span> Accepted
                            <span class="checkbox"><?php echo $stt_accepted_no; ?></span> Not Accepted
                        </div>
                        <div style="margin-bottom: 15px;">
                            <strong>Noted:</strong> <span class="form-field" style="min-width: 300px;"></span>
                        </div>
                        <table style="width: 100%; border-collapse: collapse; font-size: 9px;">

                            <tr>
                                <th></th>
                                <th style="border: 0; padding: 5px; text-align: center; font-weight: bold;">Confirmed by</th>
                                <th style="border: 0; padding: 5px; text-align: center; font-weight: bold;">Checked by</th>
                                <th style="border: 0; padding: 5px; text-align: center; font-weight: bold;">Witnessed by</th>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;">Company</td>
                                <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;"><?php echo $partner ?? ''; ?></td>
                                <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;"><?php echo $company ?? ''; ?></td>
                                <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;"><?php echo $holdings ?? ''; ?></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #000; padding: 5px; font-weight: bold;">Signature</td>
                                <td style="border: 1px solid #000; padding: 5px; height: 30px;"></td>
                                <td style="border: 1px solid #000; padding: 5px; height: 30px;"></td>
                                <td style="border: 1px solid #000; padding: 5px; height: 30px;"></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #000; padding: 5px; font-weight: bold;">Name</td>
                                <td style="border: 1px solid #000; padding: 5px;"></td>
                                <td style="border: 1px solid #000; padding: 5px;"></td>
                                <td style="border: 1px solid #000; padding: 5px;"></td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid #000; padding: 5px; font-weight: bold;">Date</td>
                                <td style="border: 1px solid #000; padding: 5px;"></td>
                                <td style="border: 1px solid #000; padding: 5px;"></td>
                                <td style="border: 1px solid #000; padding: 5px;"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <?php
            }
        }
        ?>
        <!-- VISUAL INSPECTION AND FUNCTION TEST: AC Insulation -->
        <div class="page">
            <?php $documentNumber+=1; ?>
            <table collapse="0" style="border-collapse: collapse; width: 100%">
                <tr>
                    <td class="tnc-<?php echo $inverter; ?>-ctt-header" style="width: 80%">
                        TESTING AND COMMISSIONING
                    </td>
                    <td class="tnc-<?php echo $inverter; ?>-ctt-header">
                        <img src="<?php echo $pae_letter_head; ?>" />
                    </td>
                </tr>
            </table>

            <div class="project-info">
                <table style="font-size: 9px;">
                    <tr>
                        <td style="width: 50%;">Project: <?php echo $projectname ?? false; ?></td>
                        <td style="width: 50%;">Form type: COMMISSIONING</td>
                    </tr>
                    <tr>
                        <td style="width: 50%;">Device No.: Inverter 1 - <?php echo $invertercount ?? false; ?></td>
                        <td style="width: 50%;">Description: AC Insulation</td>
                    </tr>
                    <tr>
                        <td style="width: 50%;">Location: <?php echo $location ?? false; ?></td>
                        <td style="width: 50%;" >Document No.: <?php echo $documentNumber ?? false; ?></td>
                    </tr>
                </table>
            </div>
            <table class="detailed-checklist-table">
                <thead>
                <tr>
                    <th rowspan="2" class="item-desc" width="60%">VISUAL INSPECTION AND FUNCTION TEST</th>
                    <th colspan="2" class="check-col">Check</th>
                </tr>
                <tr>
                    <th class="rep-col">Accepted</th>
                    <th class="rep-col">Not Accepted</th>
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

                        echo '<tr class="checklist-item">';
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

                                    //$isChecked = ($response_qry && $response_qry->check == $val) ? '&#10003;' : '&#9744;';
                                    $isChecked = ($response_qry && $response_qry->check == $val) ? 'checked' : 'unchecked';
                                    echo '<td class="checkbox">';
                                    echo '<i style="font-size: 12px; line-height: 1; padding: 0; width: 12px; text-align: center;" class="'.$isChecked.'"></span>';
                                    echo '</td>';
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
            <h3>INSULATION RESISTANCE MEASUREMENT</h3>
            <?php
            $aci = [];
            $aci_qry = $this->db->select('equipment,eqtmodel,note')
                ->from('frm_tnc_form_details')
                ->where(['tncid' => $tncid,'type' => 4,'status' => 1])
                ->get()->row();
            if ($aci_qry) {
                $aci = (array)$aci_qry;
            }
            ?>
            <table class="tnc-<?php echo $inverter; ?>-strtingtest-details" style="width: 66.66%; margin-top: 5px">
                <tr class="stringtest-details">
                    <td class="sttLabel" style="width: 14%">Testing Equipment :</td>
                    <td class="sttValue" style="width: 18%"><?php echo $aci['equipment'] ?? false; ?></td>
                    <td class="sttLabel" style="width: 1%"><!--Space holder--></td>
                    <td class="sttLabel" style="width: 13%; text-align: right !important;">Model :</td>
                    <td class="sttValue" style="width: 18%"><?php echo $aci['eqtmodel'] ?? false; ?></td>
                </tr>
            </table>

            <div style="margin-top: 15px;">
                <div style="height: 300px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                        <tr>
                        <th style="text-align: center; border: #000 1px solid">Inverter</th>
                        <th style="text-align: center; border: #000 1px solid">L1-G</th>
                        <th style="text-align: center; border: #000 1px solid">L2-G</th>
                        <th style="text-align: center; border: #000 1px solid">L3-G</th>
                        <th style="text-align: center; border: #000 1px solid">N-G</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        for ($i = 0; $i < $invertercount; $i++) {
                            $n = $i+1;
                            ?>
                            <tr>
                                <td style="text-align: center; border: #000 1px solid"><?php echo $n; ?></td>
                                <?php
                                $types_qry = $this->db->select('sysid,datatype,symbol')
                                    ->from('frm_tnc_datatypes')
                                    ->where(['subtype' => 4])
                                    ->order_by('col ASC')
                                    ->get();

                                $aciCols = [16 => 'l1g',17 => 'l2g',18 => 'l3g',19 => 'ng'];
                                if ($types_qry->num_rows() > 0) {
                                    foreach ($types_qry->result() AS $row) {
                                        $val_qry = $this->db->select('tid.value')
                                            ->from('frm_tnc_form_data AS tid')
                                            ->join('frm_tnc_form_details AS tfd','tfd.sysid = tid.detailsid','left')
                                            ->where(['tfd.tncid' => $tncid,'tid.datatype' => $row->sysid,'tid.string' => $n,'tfd.type' => 4,'tid.status' => 1])
                                            ->get()->row();

                                        $value = ($val_qry && $val_qry->value) ? $val_qry->value : '';

                                        echo '<td style="text-align: center; border: #000 1px solid">';
                                        if (strpos($row->datatype,'_s') !== false) {
                                            $dataType = substr($row->datatype,0,-2);
                                            echo '<div class="testing-wrapper">';
                                            echo $value.'<span style="font-family: DejaVu Sans">'.$row->symbol.'</span>';
                                            echo '</div>';

                                            $aci['inv'][$n][$aciCols[$row->sysid]] = $value.'<span style="font-family: DejaVu Sans">'.$row->symbol.'</span>';
                                        } else {
                                            echo $value;
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
                <div style="margin-top: 15px; font-size: 9px;">
                    <div style="margin-bottom: 10px;">
                        <strong>Check:</strong>
                        <span class="checkbox"><?php echo $stt_accepted_yes; ?></span> Accepted
                        <span class="checkbox"><?php echo $stt_accepted_no; ?></span> Not Accepted
                    </div>
                    <div style="margin-bottom: 15px;">
                        <strong>Noted:</strong> <span class="form-field" style="min-width: 300px;"><?php echo $aci['note'] ?? false; ?></span>
                    </div>
                    <table style="width: 100%; border-collapse: collapse; font-size: 9px;">

                        <tr>
                            <th></th>
                            <th style="border: 0; padding: 5px; text-align: center; font-weight: bold;">Confirmed by</th>
                            <th style="border: 0; padding: 5px; text-align: center; font-weight: bold;">Checked by</th>
                            <th style="border: 0; padding: 5px; text-align: center; font-weight: bold;">Witnessed by</th>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;">Company</td>
                            <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;"><?php echo $partner ?? ''; ?></td>
                            <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;"><?php echo $company ?? ''; ?></td>
                            <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;"><?php echo $holdings ?? ''; ?></td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 5px; font-weight: bold;">Signature</td>
                            <td style="border: 1px solid #000; padding: 5px; height: 30px;"></td>
                            <td style="border: 1px solid #000; padding: 5px; height: 30px;"></td>
                            <td style="border: 1px solid #000; padding: 5px; height: 30px;"></td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 5px; font-weight: bold;">Name</td>
                            <td style="border: 1px solid #000; padding: 5px;"></td>
                            <td style="border: 1px solid #000; padding: 5px;"></td>
                            <td style="border: 1px solid #000; padding: 5px;"></td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; padding: 5px; font-weight: bold;">Date</td>
                            <td style="border: 1px solid #000; padding: 5px;"></td>
                            <td style="border: 1px solid #000; padding: 5px;"></td>
                            <td style="border: 1px solid #000; padding: 5px;"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <!-- VISUAL INSPECTION AND FUNCTION TEST: DC Insulation -->
        <?php
        if (isset($invertercount)) {
            $dci = [];
            $dci_qry = $this->db->select('inverter,equipment,eqtmodel as equipmentmodel,note,testdate,humidity as weather')
                ->from('frm_tnc_form_details')
                ->where(['tncid' => $tncid,'type' => 5,'status' => 1])
                ->get();
            if ($dci_qry->num_rows() > 0) {
                foreach ($dci_qry->result() AS $row) {
                    $dci[$row->inverter]['details'] = (array)$row;
                }
            }

            for ($dciInv = 0; $dciInv < $invertercount; $dciInv++) {
                $inverter = $dciInv + 1;
                $documentNumber++;

                ?>
                <div class="page">
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

                        .tnc-<?php echo $inverter; ?>-ctt-header {
                            border: 1px black solid;
                            text-align: center;
                            font-weight: bold;
                            font-size: 14px;
                            width: 50%
                        }

                        .tnc-<?php echo $inverter; ?>-ctt-details {
                            border: 0;
                            border-collapse: collapse;
                        }

                    </style>
                    <table collapse="0" style="border-collapse: collapse; width: 100%">
                        <tr>
                            <td class="tnc-<?php echo $inverter; ?>-ctt-header" style="width: 80%">
                                TESTING AND COMMISSIONING
                            </td>
                            <td class="tnc-<?php echo $inverter; ?>-ctt-header" style="height: 15px">
                                <img src="<?php echo $pae_letter_head; ?>" />
                            </td>
                        </tr>
                    </table>

                    <div class="project-info">
                        <table style="font-size: 9px;">
                            <tr>
                                <td style="width: 50%;">Project: <?php echo $projectname ?? false; ?></td>
                                <td style="width: 50%;">Form type: COMMISSIONING</td>
                            </tr>
                            <tr>
                                <td style="width: 50%;">Device No.: Inverter <?php echo $inverter ?? false; ?></td>
                                <td style="width: 50%;">Description: VISUAL INSPECTION AND FUNCTION TEST</td>
                            </tr>
                            <tr>
                                <td style="width: 50%;">Location: <?php echo $location ?? false; ?></td>
                                <td style="width: 50%;" >Document No.: <?php echo $documentNumber ?? false; ?></td>
                            </tr>
                        </table>
                    </div>
                    <table class="detailed-checklist-table">
                        <thead>
                        <tr>
                            <th rowspan="2" class="item-desc" width="60%">VISUAL INSPECTION AND FUNCTION TEST</th>
                            <th colspan="2" class="check-col">Check</th>
                        </tr>
                        <tr>
                            <th class="rep-col">Accepted</th>
                            <th class="rep-col">Not Accepted</th>
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
                            foreach ($dci_query->result() AS $dciCL) {
                                //QUERY FOR VALUE FIRST

                                echo '<tr class="checklist-item">';
                                if (in_array($dciCL->type,['checkbox','radio'])) {
                                    echo '<td>'.$dciCL->item.'</td>';
                                    $dci_val = explode(',',$dciCL->default);
                                    if (count($dci_val) > 0) {
                                        $icheck = ($dciCL->type == 'radio') ? 'icheck-select' : 'icheck';
                                        foreach ($dci_val as $val) {
                                            $response_qry = $this->db->select()
                                                ->from('frm_tnc_checklist_responses')
                                                ->where(['itemid' => $dciCL->sysid,'tncid' => $tncid,'status' => 1])
                                                ->get()->row();

                                            //$isChecked = ($response_qry && $response_qry->check == $val) ? '&#10003;' : '&#9744;';
                                            $isChecked = ($response_qry && $response_qry->check == $val) ? 'checked' : 'unchecked';
                                            echo '<td class="checkbox">';
                                            echo '<i style="font-size: 12px; line-height: 1; padding: 0; width: 12px; text-align: center;" class="'.$isChecked.'"></span>';
                                            echo '</td>';
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
                    <h3>INSULATION RESISTANCE MEASUREMENT</h3>
                    <?php

                    ?>
                    <table class="tnc-<?php echo $inverter; ?>-strtingtest-details" style="width: 66.66%; margin-top: 5px">
                        <tr class="stringtest-details">
                            <td class="sttLabel" style="width: 14%">Testing Equipment :</td>
                            <td class="sttValue" style="width: 18%"><?php echo $dci[$inverter]['details']['equipment'] ?? false; ?></td>
                            <td class="sttLabel" style="width: 1%"><!--Space holder--></td>
                            <td class="sttLabel" style="width: 13%">Model :</td>
                            <td class="sttValue" style="width: 18%"><?php echo $dci[$inverter]['details']['eqtuipmentmodel'] ?? false; ?></td>
                        </tr>
                    </table>
                    <table class="tnc-<?php echo $inverter; ?>-strtingtest-details" style="width: 100%; margin-top: 5px">
                        <tr class="stringtest-details">
                            <td class="sttLabel" style="width: 14%">Date :</td>
                            <td class="sttValue" style="width: 18%"><?php echo isset($dci[$inverter]['details']['testdate']) ? date('F j, Y',strtotime($dci[$inverter]['details']['testdate'])) : false; ?></td>
                            <td class="sttLabel" style="width: 1%"><!--Space holder--></td>
                            <td class="sttLabel" style="width: 13%">Time :</td>
                            <td class="sttValue" style="width: 18%"><?php echo isset($dci[$inverter]['details']['testdate']) ? date('g:i A',strtotime($dci[$inverter]['details']['testdate'])) : false; ?></td>
                            <td class="sttLabel" style="width: 2%"><!--Space holder--></td>
                            <td class="sttLabel" style="width: 13%">Weather :</td>
                            <td class="sttValue" style="width: 18%"><?php echo $dci[$inverter]['details']['weather'] ?? false; ?></td>
                        </tr>
                    </table>

                    <div style="margin-top: 15px;">
                        <div style="height: 300px;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                <tr>
                                    <th style="text-align: center; border: #000 1px solid">String No.</th>
                                    <th style="text-align: center; border: #000 1px solid">Positive-Ground</th>
                                    <th style="text-align: center; border: #000 1px solid">Negative-Ground</th>
                                    <th style="text-align: center; border: #000 1px solid">Positive-Negative</th>
                                </tr>
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

                                if ($strings_qry->num_rows() > 0) {
                                    foreach ($strings_qry->result() AS $string) {
                                        echo '<tr>';
                                        echo '<td style="text-align: center; border: #000 1px solid">'.$string->strnum.'</td>';

                                        $types_qry = $this->db->select('sysid,datatype,symbol')
                                            ->from('frm_tnc_datatypes')
                                            ->where(['subtype' => 5])
                                            ->order_by('col ASC')
                                            ->get();

                                        if ($types_qry->num_rows() > 0) {
                                            foreach ($types_qry->result() as $row) {
                                                $val_qry = $this->db->select('tid.value')
                                                    ->from('frm_tnc_form_data AS tid')
                                                    ->join('frm_tnc_form_details AS tfd','tfd.sysid = tid.detailsid','left')
                                                    ->where(['tfd.tncid' => $tncid,'tid.datatype' => $row->sysid,'tfd.inverter' => $string->inverter,'tid.string' => $string->strnum,'tid.status' => 1])
                                                    ->get()->row();

                                                $value = ($val_qry && $val_qry->value) ? $val_qry->value : '';

                                                echo '<td style="text-align: center; border: #000 1px solid">';
                                                if (strpos($row->datatype, '_s') !== false) {
                                                    $dataType = substr($row->datatype, 0, -2);
                                                    echo '<div class="testing-wrapper">';
                                                    echo $value . '<span style="font-family: DejaVu Sans">' . $row->symbol . '</span>';
                                                    echo '</div>';
                                                } else {
                                                    echo $value;
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
                        <div style="margin-top: 15px; font-size: 9px;">
                            <div style="margin-bottom: 10px;">
                                <strong>Check:</strong>
                                <span class="checkbox"><?php echo $stt_accepted_yes; ?></span> Accepted
                                <span class="checkbox"><?php echo $stt_accepted_no; ?></span> Not Accepted
                            </div>
                            <div style="margin-bottom: 15px;">
                                <strong>Noted:</strong> <span class="form-field" style="min-width: 300px;"><?php echo $dci[$inverter]['details']['note'] ?? false; ?></span>
                            </div>
                            <table style="width: 100%; border-collapse: collapse; font-size: 9px;">

                                <tr>
                                    <th></th>
                                    <th style="border: 0; padding: 5px; text-align: center; font-weight: bold;">Confirmed by</th>
                                    <th style="border: 0; padding: 5px; text-align: center; font-weight: bold;">Checked by</th>
                                    <th style="border: 0; padding: 5px; text-align: center; font-weight: bold;">Witnessed by</th>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;">Company</td>
                                    <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;"><?php echo $partner ?? ''; ?></td>
                                    <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;"><?php echo $company ?? ''; ?></td>
                                    <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;"><?php echo $holdings ?? ''; ?></td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #000; padding: 5px; font-weight: bold;">Signature</td>
                                    <td style="border: 1px solid #000; padding: 5px; height: 30px;"></td>
                                    <td style="border: 1px solid #000; padding: 5px; height: 30px;"></td>
                                    <td style="border: 1px solid #000; padding: 5px; height: 30px;"></td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #000; padding: 5px; font-weight: bold;">Name</td>
                                    <td style="border: 1px solid #000; padding: 5px;"></td>
                                    <td style="border: 1px solid #000; padding: 5px;"></td>
                                    <td style="border: 1px solid #000; padding: 5px;"></td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #000; padding: 5px; font-weight: bold;">Date</td>
                                    <td style="border: 1px solid #000; padding: 5px;"></td>
                                    <td style="border: 1px solid #000; padding: 5px;"></td>
                                    <td style="border: 1px solid #000; padding: 5px;"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        ?>
        <!-- VIFT Pics -->
        <div class="page">
            <div id="form_vift_pics" class="row">
                <style>
                    #form_vift_pics table {
                        width: 100%;
                        border-collapse: collapse;
                        text-align: center;
                        margin-bottom: 20px;
                    }
                    #form_vift_pics th, #form_vift_pics td {
                        border: 1px solid black;
                        padding: 8px;
                        text-align: center;
                    }
                    #form_vift_pics img {
                        width: 150px; /* Adjust as needed */
                        height: auto;
                    }
                </style>
                <div style="margin-bottom: 15px; width: 100%;">
                    <?php  ?>
                    <table>
                        <tr>
                            <th colspan="4">AC Insulation Testing Pictures</th>
                        </tr>
                        <tr>
                            <th>L1-G</th>
                            <th>L2-G</th>
                            <th>L3-G</th>
                            <th>N-G</th>
                        </tr>

                        <?php
                        if (isset($imgs['aci']['inv']) && count($imgs['aci']['inv']) > 0) {
                            foreach ($imgs['aci']['inv'] as $invNum => $data) {
                                echo "<tr><th colspan='4'>Inverter $invNum</th></tr>";
                                echo "<tr>";
                                echo "<td><img src='{$data['l1g']}' alt='L1-G'></td>";
                                echo "<td><img src='{$data['l2g']}' alt='L2-G'></td>";
                                echo "<td><img src='{$data['l3g']}' alt='L3-G'></td>";
                                echo "<td><img src='{$data['ng']}' alt='N-G'></td>";
                                echo "</tr>";
                                echo "<tr>";
                                echo "<td id='res_l1g_$invNum'>".($aci['inv'][$invNum]['l1g'] ?? false)."</td>";
                                echo "<td id='res_l2g_$invNum'>".($aci['inv'][$invNum]['l2g'] ?? false)."</td>";
                                echo "<td id='res_l3g_$invNum'>".($aci['inv'][$invNum]['l3g'] ?? false)."</td>";
                                echo "<td id='res_ng_$invNum'>".($aci['inv'][$invNum]['ng'] ?? false)."</td>";
                                echo "</tr>";
                            }
                        } else { ?>
                            <tr>
                                <td colspan="4"><h4><i class="fa fa-warning text-warning"></i> No Pictures!</h4></td>
                            </tr>
                        <?php } ?>

                    </table>
                </div>
                <?php
                if (isset($imgs['dci']['inv']) && count($imgs['dci']['inv']) > 0) {
                    foreach ($imgs['dci']['inv'] as $invNum => $data) {
                        echo '<div class="col-md-12 margin-bottom-15">';
                        echo "<table style='page-break-inside: avoid'>";
                        echo "<tr><th colspan='4'>DC Insulation Testing Pictures - Inverter $invNum</th></tr>";
                        echo "<tr>";
                        echo "<th>String No.</th>";
                        echo "<th>Positive-Ground</th>";
                        echo "<th>Negative-Ground</th>";
                        echo "<th>Positive-Negative</th>";
                        echo "</tr>";

                        foreach ($data['str'] as $strNum => $values) {
                            echo "<tr>";
                            echo "<td>$strNum</td>";
                            echo "<td><img src='{$values['pg']}' alt='PG'></td>";
                            echo "<td><img src='{$values['ng']}' alt='NG'></td>";
                            echo "<td><img src='{$values['pn']}' alt='PN'></td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                        echo "</div>";
                    }
                } else {
                    echo '<div class="col-md-12 margin-bottom-15">';
                    echo "<table>";
                    echo '<tr><th><i class="fa fa-warning text-warning"></i> No DC Insulation Pictures Uploaded!</h4></th></tr>';
                    echo "</table>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>
        <!-- Torque Testing -->
        <div class="page">
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

                .tnc-<?php echo $inverter; ?>-ctt-header {
                    border: 1px black solid;
                    text-align: center;
                    font-weight: bold;
                    font-size: 14px;
                    width: 50%
                }

                .tnc-<?php echo $inverter; ?>-ctt-details {
                    border: 0;
                    border-collapse: collapse;
                }

            </style>
            <?php
            $tqt = [];
            $saved_qry = $this->db->select('testingarea,testdate,temp,humidity,equipment,eqtmodel,note')
                ->from('frm_tnc_form_details')
                ->where(['tncid' => $tncid,'type' => 6,'status' => 1])
                ->get()->row();

            if ($saved_qry) {
                foreach ($saved_qry AS $col => $value) {
                    $tqt['details'][$col] = $value;
                }
            }
            ?>
            <table collapse="0" style="border-collapse: collapse; width: 100%">
                <tr>
                    <td class="tnc-<?php echo $inverter; ?>-ctt-header" style="width: 80%">
                        TESTING AND COMMISSIONING
                    </td>
                    <td class="tnc-<?php echo $inverter; ?>-ctt-header" style="height: 15px">
                        <img src="<?php echo $pae_letter_head; ?>" />
                    </td>
                </tr>
            </table>

            <div class="project-info">
                <table style="font-size: 9px;">
                    <tr>
                        <td style="width: 50%;">Project: <?php echo $projectname ?? false; ?></td>
                        <td style="width: 50%;">Form type: COMMISSIONING</td>
                    </tr>
                    <tr>
                        <td style="width: 50%;">Device No.: Inverter 1 - <?php echo $invertercount ?? false; ?></td>
                        <td style="width: 50%;">Description: TORQUE TESTING</td>
                    </tr>
                    <tr>
                        <td style="width: 50%;">Location: <?php echo $location ?? false; ?></td>
                        <td style="width: 50%;" >Document No.: <?php echo $documentNumber ?? false; ?></td>
                    </tr>
                </table>
            </div>
            <h3>TORQUE TESTING</h3>
            <table class="tnc-<?php echo $inverter; ?>-strtingtest-details" style="width: 33.33%">
                <tr class="stringtest-details">
                    <td class="sttLabel" style="width: 13%">Testing Area :</td>
                    <td class="sttValue" style="width: 53%"><?php echo $tqt['details']['testingarea'] ?? false; ?></td>
                </tr>
            </table>
            <table class="tnc-<?php echo $inverter; ?>-strtingtest-details" style="width: 66.66%; margin-top: 5px">
                <tr class="stringtest-details">
                    <td class="sttLabel" style="width: 13%">Date :</td>
                    <td class="sttValue" style="width: 18%"><?php echo isset($tqt['details']['testdate']) ? date('F j, Y',strtotime($stt[$inverter]['details']['testdate'])) : '&nbsp;'; ?></td>
                    <td class="sttLabel" style="width: 2%"><!--Space holder--></td>
                    <td class="sttLabel" style="width: 13%">Time :</td>
                    <td class="sttValue" style="width: 18%"><?php echo isset($tqt['details']['testdate']) ? date('h:i A',strtotime($stt[$inverter]['details']['testdate'])) : '&nbsp;'; ?></td>
                </tr>
            </table>
            <table class="tnc-<?php echo $inverter; ?>-strtingtest-details" style="width: 66.66%; margin-top: 5px">
                <tr class="stringtest-details">
                    <td class="sttLabel" style="width: 13%">Ambient Temp :</td>
                    <td class="sttValue" style="width: 18%"><?php echo $tqt['details']['temp'] ?? false; ?></td>
                    <td class="sttLabel" style="width: 2%"><!--Space holder--></td>
                    <td class="sttLabel" style="width: 13%">Humidity (%) :</td>
                    <td class="sttValue" style="width: 18%"><?php echo $tqt['details']['humidity'] ?? false; ?></td>
                </tr>
            </table>
            <table class="tnc-<?php echo $inverter; ?>-strtingtest-details" style="width: 100%; margin-top: 5px">
                <tr class="stringtest-details">
                    <td class="sttLabel" style="width: 14%">Testing Equipment :</td>
                    <td class="sttValue" style="width: 18%"><?php echo $tqt['details']['equipment'] ?? false; ?></td>
                    <td class="sttLabel" style="width: 1%"><!--Space holder--></td>
                    <td class="sttLabel" style="width: 13%">Model :</td>
                    <td class="sttValue" style="width: 18%"><?php echo $tqt['details']['eqtuipmentmodel'] ?? false; ?></td>
                    <td class="sttLabel" style="width: 2%"><!--Space holder--></td>
                    <td class="sttLabel" style="width: 13%">Serial Number :</td>
                    <td class="sttValue" style="width: 18%"><?php echo $tqt['details']['equipmentsn'] ?? false; ?></td>
                </tr>
            </table>
            <div style="margin-bottom: 10px; margin-top: 15px">
                <strong>Note/Criteria: As per Manufacturer: </strong>  <span class="form-field" style="min-width: 300px;"><?php echo $tqt['details']['note'] ?? false; ?></span>
            </div>
            <div style="height: 500px"></div>

            <div style="margin-top: 15px; font-size: 9px;">

                <table style="width: 100%; border-collapse: collapse; font-size: 9px;">

                    <tr>
                        <th></th>
                        <th style="border: 0; padding: 5px; text-align: center; font-weight: bold;">Confirmed by</th>
                        <th style="border: 0; padding: 5px; text-align: center; font-weight: bold;">Checked by</th>
                        <th style="border: 0; padding: 5px; text-align: center; font-weight: bold;">Witnessed by</th>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;">Company</td>
                        <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;"><?php echo $partner ?? ''; ?></td>
                        <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;"><?php echo $company ?? ''; ?></td>
                        <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;"><?php echo $holdings ?? ''; ?></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 5px; font-weight: bold;">Signature</td>
                        <td style="border: 1px solid #000; padding: 5px; height: 30px;"></td>
                        <td style="border: 1px solid #000; padding: 5px; height: 30px;"></td>
                        <td style="border: 1px solid #000; padding: 5px; height: 30px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 5px; font-weight: bold;">Name</td>
                        <td style="border: 1px solid #000; padding: 5px;"></td>
                        <td style="border: 1px solid #000; padding: 5px;"></td>
                        <td style="border: 1px solid #000; padding: 5px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 5px; font-weight: bold;">Date</td>
                        <td style="border: 1px solid #000; padding: 5px;"></td>
                        <td style="border: 1px solid #000; padding: 5px;"></td>
                        <td style="border: 1px solid #000; padding: 5px;"></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="page" id="form_vift_pics">
            <style>
                #form_vift_pics table {
                    width: 100%;
                    border-collapse: collapse;
                    text-align: center;
                    margin-bottom: 20px;
                }
                #form_vift_pics th, #form_vift_pics td {
                    border: 1px solid black;
                    padding: 8px;
                    text-align: center;
                }
                #form_vift_pics img {
                    /*width: 150px; /* Adjust as needed */
                    height: auto;
                }
            </style>
            <div style="margin-bottom: 15px;">
                <?php if (isset($imgs['tqt'])) { ?>
                    <table style="width: 100%;">
                        <tr>
                            <th colspan="6" style="text-align: center;">Torque Testing Pictures</th>
                        </tr>

                        <?php
                        if (isset($imgs['tqt']['bk'])) {
                            $first = true;

                            foreach ($imgs['tqt']['bk']['inv'] as $invNum => $sides) {

                                echo "<tr><th colspan='6' style='text-align: center;'>Breaker Inverter {$invNum}</th></tr>";

                                if ($first) {
                                    // Show headers only once
                                    echo "<tr>";
                                    echo "<th colspan='3' style='text-align: center;'>LINE SIDE</th>";
                                    echo "<th colspan='3' style='text-align: center;'>LOAD SIDE</th>";
                                    echo "</tr>";
                                    echo "<tr>";
                                    echo "<th style='color: red;'>L1</th>";
                                    echo "<th style='color: orange;'>L2</th>";
                                    echo "<th style='color: blue;'>L3</th>";
                                    echo "<th style='color: red;'>L1</th>";
                                    echo "<th style='color: orange;'>L2</th>";
                                    echo "<th style='color: blue;'>L3</th>";
                                    echo "</tr>";
                                }

                                // Image row
                                echo "<tr>";

                                // LINE SIDE images
                                foreach (['l1', 'l2', 'l3'] as $phase) {
                                    $src = $sides['ln'][$phase] ?? '';
                                    echo "<td><img src='{$src}' style='width: 100px;'></td>";
                                    //echo "<td></td>";
                                }

                                // LOAD SIDE images
                                foreach (['l1', 'l2', 'l3'] as $phase) {
                                    $src = $sides['ld'][$phase] ?? '';
                                    echo "<td><img src='{$src}' style='width: 100px;'></td>";
                                    //echo "<td></td>";
                                }

                                echo "</tr>";

                                $first = false; // Only show headers once
                            }
                        } else {
                            echo '<tr><th><i class="fa fa-warning text-warning"></i> Torque Testing Pictures Uploaded for Breakers!</h4></th></tr>';
                        }
                        ?>
                    </table>
                    <table class="table table-bordered" style="width: 100%;">
                        <?php
                        if (isset($imgs['tqt']['inv'])) {
                            $first = true;

                            foreach ($imgs['tqt']['inv'] as $invNum => $phases) {
                                echo "<tr><th colspan='3' style='text-align: center;'>Inverter {$invNum}</th></tr>";

                                if ($first) {
                                    // Phase headers only for the first inverter
                                    echo "<tr>";
                                    echo "<th style='color: red;'>L1</th>";
                                    echo "<th style='color: orange;'>L2</th>";
                                    echo "<th style='color: blue;'>L3</th>";
                                    echo "</tr>";
                                }

                                // Image row
                                echo "<tr>";
                                foreach (['l1', 'l2', 'l3'] as $phase) {
                                    $src = $phases[$phase] ?? '';
                                    echo "<td><img src='{$src}' style='width: 220px;'></td>";
                                }
                                echo "</tr>";

                                $first = false;
                            }
                        } else {
                            echo '<tr><th><i class="fa fa-warning text-warning"></i> No Torque Testing Pictures Uploaded for Inverters!</h4></th></tr>';
                        }
                        ?>
                    </table>
                <?php } else {
                    echo '<div class="note note-info text-align-center"><h4 class="bold"><i class="fa fa-warning text-warning"></i> No Torque Testing Pictures Uploaded!</h4></div>';
                } ?>
            </div>
        </div>
        <div class="page" id="tnc_thm_frm">
            <style>
                #tnc_thm_frm .table .main-headers th {
                    text-align: center !important;
                    vertical-align: middle;
                }
                #tnc_thm_frm .table tr:not(.main-headers) th {
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

                #tnc_thm_frm .table tbody tr td:first-child {
                    text-align: center;
                }

                #tnc_thm_frm .table tbody tr td {
                    padding: 3px !important;
                }

                .tnc-<?php echo $inverter; ?>-ctt-header {
                    border: 1px black solid;
                    text-align: center;
                    font-weight: bold;
                    font-size: 14px;
                    width: 50%
                }

                .tnc-<?php echo $inverter; ?>-ctt-details {
                    border: 0;
                    border-collapse: collapse;
                }

            </style>
            <?php
            $documentNumber++;
            $tht = [];
            $saved_qry = $this->db->select('testingarea,testdate,temp,humidity,equipment,eqtmodel,note')
                ->from('frm_tnc_form_details')
                ->where(['tncid' => $tncid,'type' => 7,'status' => 1])
                ->get()->row();

            if ($saved_qry) {
                foreach ($saved_qry AS $col => $value) {
                    $tht['details'][$col] = $value;
                }
            }
            ?>
            <table collapse="0" style="border-collapse: collapse; width: 100%">
                <tr>
                    <td class="tnc-<?php echo $inverter; ?>-ctt-header" style="width: 80%">
                        TESTING AND COMMISSIONING
                    </td>
                    <td class="tnc-<?php echo $inverter; ?>-ctt-header" style="height: 15px">
                        <img src="<?php echo $pae_letter_head; ?>" />
                    </td>
                </tr>
            </table>

            <div class="project-info">
                <table style="font-size: 9px;">
                    <tr>
                        <td style="width: 50%;">Project: <?php echo $projectname ?? false; ?></td>
                        <td style="width: 50%;">Form type: COMMISSIONING</td>
                    </tr>
                    <tr>
                        <td style="width: 50%;">Device No.: Inverter 1 - <?php echo $invertercount ?? false; ?></td>
                        <td style="width: 50%;">Description: THERMAL SCANNING</td>
                    </tr>
                    <tr>
                        <td style="width: 50%;">Location: <?php echo $location ?? false; ?></td>
                        <td style="width: 50%;" >Document No.: <?php echo $documentNumber ?? false; ?></td>
                    </tr>
                </table>
            </div>
            <h3>THERMAL SCANNING</h3>
            <table class="tnc-<?php echo $inverter; ?>-strtingtest-details" style="width: 33.33%">
                <tr class="stringtest-details">
                    <td class="sttLabel" style="width: 13%">Testing Area :</td>
                    <td class="sttValue" style="width: 53%"><?php echo $tht['details']['testingarea'] ?? false; ?></td>
                </tr>
            </table>
            <table class="tnc-<?php echo $inverter; ?>-strtingtest-details" style="width: 66.66%; margin-top: 5px">
                <tr class="stringtest-details">
                    <td class="sttLabel" style="width: 13%">Date :</td>
                    <td class="sttValue" style="width: 18%"><?php echo isset($tht['details']['testdate']) ? date('F j, Y',strtotime($stt[$inverter]['details']['testdate'])) : '&nbsp;'; ?></td>
                    <td class="sttLabel" style="width: 2%"><!--Space holder--></td>
                    <td class="sttLabel" style="width: 13%">Time :</td>
                    <td class="sttValue" style="width: 18%"><?php echo isset($tht['details']['testdate']) ? date('h:i A',strtotime($stt[$inverter]['details']['testdate'])) : '&nbsp;'; ?></td>
                </tr>
            </table>
            <div style="height: 500px;">
                <table style="border-collapse: collapse; width: 100%; padding-top: 15px;">
                    <thead>
                    <tr>
                    <th style="text-align: center; border: #000 1px solid; width: 15%">Inverter</th>
                    <th style="text-align: center; border: #000 1px solid">Serial/Identification</th>
                    <th style="text-align: center; border: #000 1px solid">Energized Temperature</th>
                    <th style="text-align: center; border: #000 1px solid">Remarks</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $inverter_lookup = $this->db->select('tfd.inverter,thm.identifier,thm.energized,thm.remarks')
                        ->from('frm_tnc_form_details as tfd')
                        ->join('frm_tnc_thermal_scanning_data AS thm','tfd.inverter = thm.eqtnum AND thm.eqttype = 1 AND thm.tncid = tfd.tncid AND thm.status = 1','left')
                        ->where(['tfd.tncid' => $tncid,'tfd.type' => 1,'tfd.status' => 1])
                        ->order_by('tfd.inverter ASC')
                        ->get();

                    if ($inverter_lookup->num_rows() > 0) {
                        foreach ($inverter_lookup->result() as $thm) {
                            echo '<tr>';
                            echo '<td style="text-align: center; border: #000 1px solid">'.$thm->inverter.'</td>';
                            echo '<td style="text-align: center; border: #000 1px solid">'.$thm->identifier.'</td>';
                            echo '<td style="text-align: center; border: #000 1px solid">'.$thm->energized.' <span class="testing-unit">°C</span></td>';
                            echo '<td style="text-align: center; border: #000 1px solid">'.$thm->remarks.'</td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                    </tbody>
                </table>
                <table style="border-collapse: collapse; width: 100%; padding-top: 15px;">
                    <thead>
                    <tr>
                    <th style="text-align: center; border: #000 1px solid; width: 15%">DC Breaker Scan</th>
                    <th style="text-align: center; border: #000 1px solid">Serial/Identification</th>
                    <th style="text-align: center; border: #000 1px solid">Energized Temperature</th>
                    <th style="text-align: center; border: #000 1px solid">Remarks</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $thm_qry = $this->db->select('eqtnum,identifier,energized,remarks')
                        ->from('frm_tnc_thermal_scanning_data')
                        ->where(['tncid' => $tncid,'eqttype' => 2,'status' => 1])
                        ->order_by('eqtnum ASC')
                        ->get();

                    if ($inverter_lookup->num_rows() > 0) {
                        foreach ($inverter_lookup->result() as $thm) {
                            echo '<tr>';
                            echo '<td style="text-align: center; border: #000 1px solid">'.$thm->inverter.'</td>';
                            echo '<td style="text-align: center; border: #000 1px solid">'.$thm->identifier.'</td>';
                            echo '<td style="text-align: center; border: #000 1px solid">'.$thm->energized.' <span class="testing-unit">°C</span></td>';
                            echo '<td style="text-align: center; border: #000 1px solid">'.$thm->remarks.'</td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                    </tbody>
                </table>
                <table style="border-collapse: collapse; width: 100%; padding-top: 15px;">
                    <thead>
                    <tr>
                    <th style="text-align: center; border: #000 1px solid; width: 15%">AC Breaker Scan</th>
                    <th style="text-align: center; border: #000 1px solid">Serial/Identification</th>
                    <th style="text-align: center; border: #000 1px solid">Energized Temperature</th>
                    <th style="text-align: center; border: #000 1px solid">Remarks</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $thm_qry = $this->db->select('eqtnum,identifier,energized,remarks')
                        ->from('frm_tnc_thermal_scanning_data')
                        ->where(['tncid' => $tncid,'eqttype' => 3,'status' => 1])
                        ->order_by('eqtnum ASC')
                        ->get();

                    if ($inverter_lookup->num_rows() > 0) {
                        foreach ($inverter_lookup->result() as $thm) {
                            echo '<tr>';
                            echo '<td style="text-align: center; border: #000 1px solid">'.$thm->inverter.'</td>';
                            echo '<td style="text-align: center; border: #000 1px solid">'.$thm->identifier.'</td>';
                            echo '<td style="text-align: center; border: #000 1px solid">'.$thm->energized.' <span class="testing-unit">°C</span></td>';
                            echo '<td style="text-align: center; border: #000 1px solid">'.$thm->remarks.'</td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                    </tbody>
                </table>
                <table style="border-collapse: collapse; width: 100%; padding-top: 15px;">
                    <thead>
                    <tr>
                    <th style="text-align: center; border: #000 1px solid; width: 15%">RSD Scan</th>
                    <th style="text-align: center; border: #000 1px solid">Serial/Identification</th>
                    <th style="text-align: center; border: #000 1px solid">Energized Temperature</th>
                    <th style="text-align: center; border: #000 1px solid">Remarks</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $thm_qry = $this->db->select('eqtnum,identifier,energized,remarks')
                        ->from('frm_tnc_thermal_scanning_data')
                        ->where(['tncid' => $tncid,'eqttype' => 4,'status' => 1])
                        ->order_by('eqtnum ASC')
                        ->get();

                    if ($inverter_lookup->num_rows() > 0) {
                        foreach ($inverter_lookup->result() as $thm) {
                            echo '<tr>';
                            echo '<td style="text-align: center; border: #000 1px solid">'.$thm->inverter.'</td>';
                            echo '<td style="text-align: center; border: #000 1px solid">'.$thm->identifier.'</td>';
                            echo '<td style="text-align: center; border: #000 1px solid">'.$thm->energized.' <span class="testing-unit">°C</span></td>';
                            echo '<td style="text-align: center; border: #000 1px solid">'.$thm->remarks.'</td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 15px; font-size: 9px;">

                <div style="margin-bottom: 15px;">
                    <strong>Noted:</strong> <span class="form-field" style="min-width: 300px;"><?php echo $dci[$inverter]['details']['note'] ?? false; ?></span>
                </div>
                <table style="width: 100%; border-collapse: collapse; font-size: 9px;">

                    <tr>
                        <th></th>
                        <th style="border: 0; padding: 5px; text-align: center; font-weight: bold;">Confirmed by</th>
                        <th style="border: 0; padding: 5px; text-align: center; font-weight: bold;">Checked by</th>
                        <th style="border: 0; padding: 5px; text-align: center; font-weight: bold;">Witnessed by</th>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;">Company</td>
                        <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;"><?php echo $partner ?? ''; ?></td>
                        <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;"><?php echo $company ?? ''; ?></td>
                        <td style="border: 1px solid #000; padding: 5px; text-align: center; background: yellow;"><?php echo $holdings ?? ''; ?></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 5px; font-weight: bold;">Signature</td>
                        <td style="border: 1px solid #000; padding: 5px; height: 30px;"></td>
                        <td style="border: 1px solid #000; padding: 5px; height: 30px;"></td>
                        <td style="border: 1px solid #000; padding: 5px; height: 30px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 5px; font-weight: bold;">Name</td>
                        <td style="border: 1px solid #000; padding: 5px;"></td>
                        <td style="border: 1px solid #000; padding: 5px;"></td>
                        <td style="border: 1px solid #000; padding: 5px;"></td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 5px; font-weight: bold;">Date</td>
                        <td style="border: 1px solid #000; padding: 5px;"></td>
                        <td style="border: 1px solid #000; padding: 5px;"></td>
                        <td style="border: 1px solid #000; padding: 5px;"></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="page" id="thermal_pics">
            <style>
                #thermal_pics table {
                    width: 100%;
                    border-collapse: collapse;
                    text-align: center;
                    margin-bottom: 20px;
                }
                #thermal_pics th, #thermal_pics td {
                    border: 1px solid black;
                    padding: 8px;
                    text-align: center;
                }
                #thermal_pics img {
                    /*width: 150px; /* Adjust as needed */
                    height: auto;
                }
            </style>
            <div style="margin-bottom: 15px;">
                <?php if (isset($imgs['thm'])) { ?>
                    <table style="width: 100%;">
                        <tr>
                            <th colspan="3" style="text-align: center;">Thermal Scanning Pictures</th>
                        </tr>

                        <?php
                        if (isset($imgs['thm']['inv'])) {
                            $first = true;

                            foreach ($imgs['thm']['inv'] as $invNum => $sides) {

                                echo "<tr><th colspan='3' style='text-align: center;'>Inverter {$invNum}</th></tr>";

                                if ($first) {
                                    // Show headers only once
                                    echo "<tr>";
                                    echo "<th>L1</th>";
                                    echo "<th>L2</th>";
                                    echo "<th>L3</th>";
                                    echo "</tr>";
                                }

                                // Image row
                                echo "<tr>";

                                // LINE SIDE images
                                foreach (['ir', 'ne', 'rs'] as $phase) {
                                    $src = $sides[$phase] ?? '';
                                    echo "<td><img src='{$src}' style='width: 150px;'></td>";
                                    //echo "<td></td>";
                                }

                                echo "</tr>";

                                $first = false; // Only show headers once
                            }
                        } else {
                            echo '<tr><th><i class="fa fa-warning text-warning"></i> Torque Testing Pictures Uploaded for Breakers!</h4></th></tr>';
                        }
                        ?>
                    </table>
                    <table style="page-break-inside: avoid; width: 100%;">
                        <?php
                        $thmBK = [];
                        if (isset($imgs['thm']['bk']['inv'])) {
                            $thmBK['inv'] = $imgs['thm']['bk']['inv'];
                        } else {
                            $thmBK[] = $imgs['thm']['bk'];
                        }
                        $colSpan = '';
                        $imgWidth = 500;
                        if (count($thmBK) > 1) {
                            $numImages = count($thmBK);
                            $colSpan = 'colspan ="'.$numImages.'"';

                            $totalWidthPx = 7.5 * 96;
                            $totalOverhead = $numImages * 18;
                            $usableWidth = $totalWidthPx - $totalOverhead;

                            if ($usableWidth > 0) {
                                $imgWidth = floor($usableWidth/$numImages);
                            }
                        }
                        ?>
                        <tr>
                            <td>
                            <h2>AC Breakers</h2>
                            </td>
                        </tr>
                        <tr>
                            <td <?php echo $colSpan ?? ''; ?>>
                            <h2>Inverter 1 - <?php echo $invertercount ?? ''; ?></h2>
                            </td>
                        </tr>
                        <tr>
                            <?php
                            foreach ($thmBK AS $thmImg) {
                                echo "<td><img src='{$thmImg}' style='width: {$imgWidth}px;'></td>";
                            }
                            ?>
                        </tr>
                    </table>
                <?php } else {
                    echo '<div class="note note-info text-align-center"><h4 class="bold"><i class="fa fa-warning text-warning"></i> No Torque Testing Pictures Uploaded!</h4></div>';
                } ?>
            </div>
        </div>
    </main>
    </body>
    </html>
    <?php
}
?>