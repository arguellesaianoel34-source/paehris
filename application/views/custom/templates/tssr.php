<?php

if (isset($app)) {
    /*echo "<pre>";
    print_r ($app);
    echo "</pre>";*/

}
// GET CORP INFO
$qry_corp_app = $this->db->select()
    ->from('application_customers_corporation')
    ->where(array('appid' => $appid, 'types' => $app->apptype))
    ->get()->row();

if($qry_corp_app) {
    $corpgov = array();
    if($app->apptype == 2) {
        $corpgov = get_corporation_info($qry_corp_app->corpid);
        $pic_dir = 'corporation';
    } else {
        $corpgov = get_government_info($qry_corp_app->corpid);
        $pic_dir = 'government';
    }
    $pic_id = $qry_corp_app->corpid;
    if ($corpgov->qry) {
        $corpname = $corpgov->info->descs;


        if($app->apptype == 2) {
            $qry_branch = $this->db->select()
                ->from('corporation_branches')
                ->where(array('corpid' => $qry_corp_app->corpid, 'sysid' => $qry_corp_app->branchid))
                ->get()->row();
            if ($qry_branch) {
                $corpbranch = $qry_branch->names;
            }
        }else{
            $corpbranch = ($corpgov) ? $corpgov->info->names : '';
        }
    }
}

$logo = FCPATH . 'assets/global/img/logo/pae-small-logo.png';
$logo_img = convert_base64_img($logo,'png','50%','50%');

if (isset($info)) {
    $roofing = array(
        1 => 'Long Span',
        2 => 'GI Sheets',
        3 => 'GI Sheets (Corrugated)',
        4 => 'Ceramic Tiles',
        5 => 'Roof Deck',
        6 => 'Others',
    );
}

$survey_ = false;

if (isset($survey)) {
    if (is_array($survey) && count($survey) > 0) {
        $survey_ = true;
    }
    if (is_object($survey) && count((array)$survey) > 0) {
        $survey_ = true;
    }

    $voltage = array(
        'l1_l2_amt' => ($survey->l1l2 && $survey->l1l2 > 0) ? $survey->l1l2 : 0,
        'l1_l3_amt' => ($survey->l1l3 && $survey->l1l3 > 0) ? $survey->l1l3 : 0,
        'l2_l3_amt' => ($survey->l2l3 && $survey->l2l3 > 0) ? $survey->l2l3 : 0,
        'l1_g_amt' => ($survey->l1g && $survey->l1g > 0) ? $survey->l1g : 0,
        'l2_g_amt' => ($survey->l2g && $survey->l2g > 0) ? $survey->l2g : 0,
        'l3_g_amt' => ($survey->l3g && $survey->l3g > 0) ? $survey->l3g : 0,
    );

    $total_voltage = array_sum($voltage);
    $load = '';
    $loadunit = '';

    if(isset($survey->power) && $survey->power > 0) {
        $load = $survey->power;
    } else {
        if ($total_voltage > 0) {
            if ($survey->l1l3a == 0 || $survey->l2l3a == 0) {
                // Phase 1
                if ($survey->l1l2a > 0) {
                    $load = ($total_voltage * $survey->l1l2a);
                }
            } else {
                // 3 Phase
                $amp_higher = max($survey->l1l2a, $survey->l1l3a, $survey->l2l3a);
                $amp = array(
                    'l1' => $survey->l1l2a,
                    'l2' => $survey->l1l3a,
                    'l3' => $survey->l2l3a,
                );

                unset($voltage['l1_g_amt'], $voltage['l2_g_amt'], $voltage['l3_g_amt']);

                $voltage_cnt = count(array_filter($voltage, function ($x) {
                    return !empty($x);
                }));
                if ($voltage_cnt > 0) {
                    $volt_average = array_sum($voltage) / $voltage_cnt;
                }

                $amp_cnt = count(array_filter($amp, function ($x) {
                    return !empty($x);
                }));
                if ($amp_cnt > 0) {
                    $amp_average = array_sum($amp) / $amp_cnt;
                }

                if ($amp_higher > 0) {
                    $load = ($volt_average * $amp_average) * 1.732;
                }
            }
        }
    }

    $measuredload = ($load > 1000) ? number_format($load / 1000, 2) . 'kW' : number_format($load, 2) . 'W';

    /*echo "<pre>";
    print_r ($files);
    echo "</pre>";*/
}

if ($survey_) {
    ?>
    <style type="text/css">
        div {
            font-family: Segoe UI, Calibri, Arial, Helvetica;
            padding: 0;
            margin: 0;
        }

        @media print {
            .pagebreak {
                page-break-before: always;
                page-break-inside: avoid;
            }

            /* page-break-after works, as well */
            .breakhere {
                page-break-after: always;
                page-break-inside: avoid;
            }
        }

    </style>

    <div id="page1" class="pagebreak">
        <div style="width: 100%; display: inline-block;">
            <div style="width: 40%; display: inherit !important; vertical-align: bottom">
                <img src="<?php echo $logo_img ?>">
                <div style="font-size: 8px">Emperor Cement Compound, Coastal Rd., Balabago, Jaro, Iloilo City</div>
            </div>
            <div style="width: 60%; display: inherit !important; font-size: 20px; font-weight: bold; text-align: left; vertical-align: top">
                TECHNICAL SITE SURVEY REPORT
            </div>
        </div>
        <div style="width: 40%; font-size: 11px">
            <span style="width: 50%; display: inline-block; border: 1px solid black; margin-right: -4px; font-weight: bold">APPLICATION NO.:</span>
            <span style="width: 50%; display: inline-block; text-align: center; border: 1px solid black; font-weight: bold; color: #2F75B5;"><?php echo 'PAE' . str_pad($app->essrno, 5, "0", STR_PAD_LEFT); ?></span>
        </div>
        <div style="width: 40%; font-size: 11px">
            <span style="width: 50%; display: inline-block; border: 1px solid black; margin-right: -4px; font-weight: bold">DATE:</span>
            <span style="width: 50%; display: inline-block; text-align: center; border: 1px solid black;"><?php echo date_format(date_create($survey->inspectiondate), 'F j, Y'); ?></span>
        </div>
        <div style="width: 100%; font-size: 11px">
            <span style="width: 30%; display: inline-block; border: 1px solid black; margin-right: -4px; font-weight: bold">CLIENT NAME:</span>
            <span style="width: 69%; display: inline-block; text-align: center; border: 1px solid black;"><?php echo ($app->apptype != 1) ? $corpname . ((isset($corpbranch) && $corpbranch != '') ? ' (' . $corpbranch . ')' : '') : $app->appname; ?></span>
        </div>
        <div style="width: 100%; font-size: 11px">
            <span style="width: 30%; display: inline-block; border: 1px solid black; margin-right: -4px; font-weight: bold">LOCATION:</span>
            <span style="width: 69%; display: inline-block; text-align: center; border: 1px solid black;"><?php echo $app->address; ?></span>
        </div>
        <div style="width: 100%; font-size: 11px">
            <span style="width: 30%; display: inline-block; border: 1px solid black; margin-right: -4px; font-weight: bold">ROOF ORIENTATION:</span>
            <span style="width: 69%; display: inline-block; text-align: center; border: 1px solid black;"><?php echo (isset($info->rooforientation) && $info->rooforientation != '') ? $info->rooforientation : '.'; ?></span>
        </div>
        <div style="width: 100%; font-size: 11px">
            <span style="width: 30%; display: inline-block; border: 1px solid black; margin-right: -4px; font-weight: bold">KIND OF ROOF:</span>
            <span style="width: 69%; display: inline-block; text-align: center; border: 1px solid black;"><?php echo (isset($info->rooftype) && $info->rooftype != '') ? $roofing[$info->rooftype] : '.'; ?></span>
        </div>
        <div style="width: 100%; font-size: 11px">
            <span style="width: 30%; display: inline-block; border: 1px solid black; margin-right: -4px; font-weight: bold">ROOF INCLINATION:</span>
            <span style="width: 69%; display: inline-block; text-align: center; border: 1px solid black;">
            <?php if (isset($info->roofinclination) && $info->roofinclination != '') {
                $ri = explode('-', $info->roofinclination);
                $deg = array();
                $symbol = '';
                foreach ($ri AS $inclination) {
                    if (!strpos($inclination, '°')) {
                        $symbol = '&#176';
                    }
                    $deg[] = trim($inclination) . $symbol;
                }
                echo implode('-', $deg);
                //$info->roofinclination.'&#176';
            }

            ?>
        </span>
        </div>
        <div style="width: 100%; font-size: 11px">
            <span style="width: 30%; display: inline-block; border: 1px solid black; margin-right: -4px; font-weight: bold">GRID SERVICE:</span>
            <span style="width: 69%; display: inline-block; text-align: center; border: 1px solid black;"><?php echo get_rate_class_select($survey->rateclass) ?></span>
        </div>
        <div style="width: 100%; font-size: 11px">
            <span style="width: 30%; display: inline-block; border: 1px solid black; margin-right: -4px; font-weight: bold">VOLTAGE DROP CONDITION:</span>
            <span style="width: 69%; display: inline-block; text-align: center; border: 1px solid black;"><?php echo (isset($info->voltdropcondition) && $info->voltdropcondition != '') ? $info->voltdropcondition : '.'; ?></span>
        </div>
        <div style="width: 100%; font-size: 11px">
            <span style="width: 30%; display: inline-block; border: 1px solid black; margin-right: -4px; font-weight: bold">GENERATOR RATING:</span>
            <span style="width: 69%; display: inline-block; text-align: center; border: 1px solid black;"><?php echo (isset($info->generatorrating) && $info->generatorrating != '') ? $info->generatorrating : '.'; ?></span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: -4px">
            <span style="width: 99.1%; display: inline-block; text-align: center; border: 1px solid black; font-weight: bold">VOLTAGE READING</span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 3px">
            <span style="width: 16.4%; display: inline-block; text-align: center; border: 1px solid black; margin-right: -4px; font-weight: bold">L1 - L2</span>
            <span style="width: 16.4%; display: inline-block; text-align: center; border: 1px solid black; margin-right: -4px; font-weight: bold">L1 - L3</span>
            <span style="width: 16.4%; display: inline-block; text-align: center; border: 1px solid black; margin-right: -4px; font-weight: bold">L2 - L3</span>
            <span style="width: 16.4%; display: inline-block; text-align: center; border: 1px solid black; margin-right: -4px; font-weight: bold">L1 - G</span>
            <span style="width: 16.4%; display: inline-block; text-align: center; border: 1px solid black; margin-right: -4px; font-weight: bold">L2 - G</span>
            <span style="width: 16.4%; display: inline-block; text-align: center; border: 1px solid black; margin-right: -4px; font-weight: bold">L3 - G</span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 0px; vertical-align: center">
            <span style="width: 16.4%; height: 20px; display: inline-block; text-align: center; border: 1px solid black; margin-right: -4px; font-weight: bold; line-height: 15px"><?php echo ($survey->l1l2 != '0.0000') ? (float)$survey->l1l2 . 'V' : ''; ?></span>
            <span style="width: 16.4%; height: 20px; display: inline-block; text-align: center; border: 1px solid black; margin-right: -4px; font-weight: bold; line-height: 15px"><?php echo ($survey->l1l3 != '0.0000') ? (float)$survey->l1l3 . 'V' : ''; ?></span>
            <span style="width: 16.4%; height: 20px; display: inline-block; text-align: center; border: 1px solid black; margin-right: -4px; font-weight: bold; line-height: 15px"><?php echo ($survey->l2l3 != '0.0000') ? (float)$survey->l2l3 . 'V' : ''; ?></span>
            <span style="width: 16.4%; height: 20px; display: inline-block; text-align: center; border: 1px solid black; margin-right: -4px; font-weight: bold; line-height: 15px"><?php echo ($survey->l1g != '0.0000') ? (float)$survey->l1g . 'V' : ''; ?></span>
            <span style="width: 16.4%; height: 20px; display: inline-block; text-align: center; border: 1px solid black; margin-right: -4px; font-weight: bold; line-height: 15px"><?php echo ($survey->l2g != '0.0000') ? (float)$survey->l2g . 'V' : ''; ?></span>
            <span style="width: 16.4%; height: 20px; display: inline-block; text-align: center; border: 1px solid black; margin-right: -4px; font-weight: bold; line-height: 15px"><?php echo ($survey->l3g != '0.0000') ? (float)$survey->l3g . 'V' : ''; ?></span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: -2px">
            <span style="width: 32.95%; display: inline-block; text-align: center; border: 1px solid black; margin-right: -4px; font-weight: bold">L1</span>
            <span style="width: 32.95%; display: inline-block; text-align: center; border: 1px solid black; margin-right: -4px; font-weight: bold">L2</span>
            <span style="width: 32.95%; display: inline-block; text-align: center; border: 1px solid black; margin-right: -4px; font-weight: bold">L3</span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 0px">
            <span style="width: 32.95%; height: 20px; display: inline-block; text-align: center; border: 1px solid black; margin-right: -4px; font-weight: bold; line-height: 15px"><?php echo ($survey->l1l2a != '0.0000') ? (float)$survey->l1l2a . 'A' : ''; ?></span>
            <span style="width: 32.95%; height: 20px; display: inline-block; text-align: center; border: 1px solid black; margin-right: -4px; font-weight: bold; line-height: 15px"><?php echo ($survey->l2l3a != '0.0000') ? (float)$survey->l2l3a . 'A' : ''; ?></span>
            <span style="width: 32.95%; height: 20px; display: inline-block; text-align: center; border: 1px solid black; margin-right: -4px; font-weight: bold; line-height: 15px"><?php echo ($survey->l1l3a != '0.0000') ? (float)$survey->l1l3a . 'A' : ''; ?></span>
        </div>
        <!--<div style="width: 100%; font-size: 11px; margin-top: -3px; margin-bottom: 3px">
        <span style="width: 30%; display: inline-block; border: 1px solid black; margin-right: -4px; font-weight: bold">MEASURED LOAD:</span>
        <span style="width: 69%; display: inline-block; text-align: center; border: 1px solid black;"><?php echo $measuredload; ?></span>
    </div>-->
        <div style="width: 100%; font-size: 11px; margin-top: -5px">
            <span style="width: 99.1%; display: inline-block; text-align: center; border: 1px solid black; font-weight: bold">MEASURED LOAD</span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 0px">
            <span style="width: 99.1%; display: inline-block; text-align: left; border: 1px solid black;text-align: center;"><?php echo $measuredload; ?></span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 0px">
            <span style="width: 99.1%; display: inline-block; text-align: center; border: 1px solid black; font-weight: bold">LOCATION OF TAPPING POINT</span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 0px">
            <span style="width: 99.1%; display: inline-block; text-align: left; border: 1px solid black; font-weight: bold">PICTURES:</span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 0px">
            <?php
            if (isset($files['tp'])) {
                if (is_array($files['tp'])) {
                    $filecnt = count($files['tp']);
                    $width = 99.1 / $filecnt;
                    echo '<span style="width: 99.1%; text-align: center; display: inline-block; height: 210px; margin-right: 4px; border: 1px solid black;">';
                    for ($cnt = 0; $filecnt > $cnt; $cnt++) {
                        ?>

                        <img src="<?php echo convert_base64_img($files['tp'][$cnt], 'png') ?>"
                             style="padding-top: 55px; max-height: 200px; object-position: center center">
                        <?php
                    }
                    echo '</span>';
                } else { ?>
                    <span style="width: 99.1%; text-align: center; display: inline-block; height: 210px; border: 1px solid black; margin-right: -4px;">
                    <img src="<?php echo convert_base64_img($files['tp'], 'png', '99.1%', '210') ?>"
                         style="padding-top: 5px; max-height: 200px;">
                </span>
                <?php }
            } else { ?>
                <span style="width: 99.1%; text-align: center; display: inline-block; height: 210px; border: 1px solid black; margin-right: -4px;"></span>
            <?php } ?>

        </div>
        <div style="width: 100%; font-size: 11px;margin-top: 3px">
            <span style="width: 20%; display: inline-block; border: 1px solid black; margin-right: -4px; font-weight: bold">MEASUREMENT:</span>
            <span style="width: 79%; display: inline-block; text-align: center; border: 1px solid black; font-weight: bold;"><?php echo (isset($details[3428]['measurements']) && $details[3428]['measurements'] != '') ? $details[3428]['measurements'] : '.'; ?></span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: -4px">
            <span style="width: 99.1%; display: inline-block; height: 50px; text-align: center; border: 1px solid black; line-height: 30px"><?php echo (isset($details[3428]['remarks']) && $details[3428]['remarks'] != '') ? $details[3428]['remarks'] : '.'; ?></span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 0px">
            <span style="width: 99.1%; display: inline-block; text-align: center; border: 1px solid black; font-weight: bold">LOCATION OF INVERTER</span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 0px">
            <span style="width: 99.1%; display: inline-block; text-align: left; border: 1px solid black; font-weight: bold">PICTURES:</span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 0px">
            <?php
            if (isset($files['li'])) {
                if (is_array($files['li'])) {
                    $filecnt = count($files['li']);
                    $width = 99.1 / $filecnt;
                    echo '<span style="width: 99.1%; text-align: center; display: inline-block; height: 210px; margin-right: 4px; border: 1px solid black;">';
                    for ($cnt = 0; $filecnt > $cnt; $cnt++) {
                        ?>

                        <img src="<?php echo convert_base64_img($files['li'][$cnt], 'png') ?>"
                             style="padding-top: 55px; max-height: 200px; object-position: center center">
                        <?php
                    }
                    echo '</span>';
                } else { ?>
                    <span style="width: 99.1%; text-align: center; display: inline-block; height: 210px; border: 1px solid black; margin-right: -4px;">
                    <img src="<?php echo convert_base64_img($files['li'], 'png', '99.1%', '210') ?>"
                         style="padding-top: 5px; max-height: 200px;">
                </span>
                <?php }
            } else { ?>
                <span style="width: 99.1%; text-align: center; display: inline-block; height: 210px; border: 1px solid black; margin-right: -4px;"></span>
            <?php } ?>
        </div>
        <div style="width: 100%; font-size: 11px;margin-top: 3px">
            <span style="width: 20%; display: inline-block; border: 1px solid black; margin-right: -4px; font-weight: bold">MEASUREMENT:</span>
            <span style="width: 79%; display: inline-block; text-align: center; border: 1px solid black; font-weight: bold;"><?php echo (isset($details[3429]['measurements']) && $details[3429]['measurements'] != '') ? $details[3429]['measurements'] : '.'; ?></span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: -4px">
            <span style="width: 99.1%; display: inline-block; height: 50px; text-align: center; border: 1px solid black; line-height: 30px"><?php echo (isset($details[3429]['remarks']) && $details[3429]['remarks'] != '') ? $details[3429]['remarks'] : '.'; ?></span>
        </div>
    </div>
    <div id="page2" class="pagebreak" style="page-break-inside: avoid !important;">
        <div style="width: 100%; font-size: 11px; margin-top: 0px">
            <span style="width: 99.1%; display: inline-block; text-align: center; border: 1px solid black; font-weight: bold">PV LOCATION</span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 0px">
            <span style="width: 99.1%; display: inline-block; text-align: left; border: 1px solid black; font-weight: bold">PICTURES:</span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 0px">
            <?php
            if (isset($files['pvl'])) {
                if (is_array($files['pvl'])) {
                    $filecnt = count($files['pvl']);
                    $width = 99.1 / $filecnt;
                    echo '<span style="width: 99.1%; text-align: center; display: inline-block; height: 210px; margin-right: 4px; border: 1px solid black;">';
                    for ($cnt = 0; $filecnt > $cnt; $cnt++) {
                        ?>

                        <img src="<?php echo convert_base64_img($files['pvl'][$cnt], 'png') ?>"
                             style="padding-top: 55px; max-height: 200px; object-position: center center">
                        <?php
                    }
                    echo '</span>';
                } else { ?>
                    <span style="width: 99.1%; text-align: center; display: inline-block; height: 210px; border: 1px solid black; margin-right: -4px;">
                    <img src="<?php echo convert_base64_img($files['pvl'], 'png', '99.1%', '210') ?>"
                         style="padding-top: 5px; max-height: 200px;">
                </span>
                <?php }
            } else { ?>
                <span style="width: 99.1%; text-align: center; display: inline-block; height: 210px; border: 1px solid black; margin-right: -4px;"></span>
            <?php } ?>
        </div>
        <div style="width: 100%; font-size: 11px;margin-top: 3px">
            <span style="width: 20%; display: inline-block; border: 1px solid black; margin-right: -4px; font-weight: bold">MEASUREMENT:</span>
            <span style="width: 79%; display: inline-block; text-align: center; border: 1px solid black; font-weight: bold;"><?php echo (isset($details[3430]['measurements']) && $details[3430]['measurements'] != '') ? $details[3430]['measurements'] : '.'; ?></span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: -4px">
            <span style="width: 99.1%; display: inline-block; height: 50px; text-align: center; border: 1px solid black; line-height: 30px"><?php echo (isset($details[3430]['remarks']) && $details[3430]['remarks'] != '') ? $details[3430]['remarks'] : '.'; ?></span>
        </div>
        <div class="" style="width: 100%; font-size: 11px; margin-top: 0px">
            <span style="width: 99.1%; display: inline-block; text-align: center; border: 1px solid black; font-weight: bold">DC STRING RUN WAY</span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 0px">
            <span style="width: 99.1%; display: inline-block; text-align: left; border: 1px solid black; font-weight: bold">PICTURES:</span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 0px;">
            <?php
            if (isset($files['dcs'])) {
                if (is_array($files['dcs'])) {
                    $filecnt = count($files['dcs']);
                    $width = 99.1 / $filecnt;
                    echo '<span style="width: 99.1%; text-align: center; display: inline-block; height: 210px; margin-right: 4px; border: 1px solid black;">';
                    for ($cnt = 0; $filecnt > $cnt; $cnt++) {
                        ?>

                        <img src="<?php echo convert_base64_img($files['dcs'][$cnt], 'png') ?>" style="padding-top: 55px; max-height: 200px; object-position: center center">
                        <?php
                    }
                    echo '</span>';
                } else { ?>
                    <span style="width: 99.1%; text-align: center; display: inline-block; height: 210px; border: 1px solid black; margin-right: -4px;">
                    <img src="<?php echo convert_base64_img($files['dcs'], 'png', '99.1%', '210') ?>" style="padding-top: 5px; max-height: 200px;">
                </span>
                <?php }
            } else { ?>
                <span style="width: 99.1%; text-align: center; display: inline-block; height: 210px; border: 1px solid black; margin-right: -4px;"></span>
            <?php } ?>
        </div>
        <div style="width: 100%; font-size: 11px;margin-top: 3px">
            <span style="width: 20%; display: inline-block; border: 1px solid black; margin-right: -4px; font-weight: bold">MEASUREMENT:</span>
            <span style="width: 79%; display: inline-block; text-align: center; border: 1px solid black; font-weight: bold;"><?php echo (isset($details[3431]['measurements']) && $details[3431]['measurements'] != '') ? $details[3431]['measurements'] : '.'; ?></span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: -4px">
            <span style="width: 99.1%; display: inline-block; height: 50px; text-align: center; border: 1px solid black; line-height: 30px"><?php echo (isset($details[3431]['remarks']) && $details[3431]['remarks'] != '') ? $details[3431]['remarks'] : '.'; ?></span>
        </div>
        <div style="width: 100%; font-size: 11px;margin-top: 3px">
            <span style="width: 20%; display: inline-block; border: 1px solid black; margin-right: -4px; font-weight: bold">REMARKS:</span>
            <span style="width: 79%; display: inline-block; text-align: center; border: 1px solid black; font-weight: bold;">.</span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: -4px">
            <span style="width: 99.1%; display: inline-block; height: 75px; text-align: center; border: 1px solid black; line-height: 30px"><?php echo ($survey->remarks != '') ? $survey->remarks : 'N/A'; ?></span>
        </div>
        <div style="width: 100%; font-size: 11px;margin-top: 7px">
            <span style="width: 20%; display: inline-block; height: 35px; border: 1px solid black; margin-right: -4px; font-weight: bold">SURVEYED BY:</span>
            <span style="width: 79%; display: inline-block; height: 35px; text-align: center; border: 1px solid black; line-height: 25px;"><?php echo isset($team) ? implode(', ', $team) : 'N/A'; ?></span>
        </div>
        <div style="width: 100%; font-size: 11px;margin-top: -4px">
            <span style="width: 20%; display: inline-block; border: 1px solid black; margin-right: -4px; font-weight: bold">PREPARED BY:</span>
            <span style="width: 79%; display: inline-block; text-align: center; border: 1px solid black;"><?php echo $author; ?></span>
        </div>
    </div>
    <div id="page3" class="pagebreak" style="page-break-before: always; page-break-inside: avoid;">
        <div style="width: 100%; font-size: 11px; margin-top: -4px">
            <span style="width: 99.1%; display: inline-block; text-align: center; border: 1px solid black; font-weight: bold">ADDITIONAL INFORMATION</span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 4px">
            <span style="width: 99.1%; display: inline-block; font-weight: bold">ROOF DIMENSIONS:</span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: -4px; height: 17%">
            <span style="width: 99.1%; display: inline-block; height: 100px; line-height: 30px"><?php echo (isset($info) && $info->roofdimensions != '') ? $info->roofdimensions : '.'; ?></span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 4px">
            <span style="width: 99.1%; display: inline-block; font-weight: bold">ELECTRICAL/STRUCTURAL PLANS:</span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 4px; height: 17%">
            <span style="width: 99.1%; display: inline-block; height: 100px; line-height: 30px"><?php echo (isset($info) && $info->electricalplan != '') ? $info->electricalplan : '.'; ?></span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 4px">
            <span style="width: 99.1%; display: inline-block; font-weight: bold">NORMAL LOADS OR FOR CLAMPING:</span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 4px; height: 17%">
            <span style="width: 99.1%; display: inline-block; height: 100px; line-height: 30px"><?php echo (isset($info) && $info->loadsforclamping != '') ? $info->loadsforclamping : '.'; ?></span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 4px">
            <span style="width: 99.1%; display: inline-block; font-weight: bold">METER # / BILLING DETAILS:</span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 4px; height: 17%">
            <span style="width: 99.1%; display: inline-block; height: 100px; line-height: 30px"><?php echo (isset($info) && $info->billingdetails != '') ? $info->billingdetails : '.'; ?></span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 4px">
            <span style="width: 99.1%; display: inline-block; font-weight: bold">DAYTIME APPLIANCES:</span>
        </div>
        <div style="width: 100%; font-size: 11px; margin-top: 4px; height: 17%">
            <span style="width: 99.1%; display: inline-block; height: 100px; line-height: 30px"><?php echo (isset($info) && $info->daytimeappliances != '') ? $info->daytimeappliances : '.'; ?></span>
        </div>
    </div>

    <div style="width: 100%; font-size: 11px; page-break-before: always !important; margin-top: -4px">
        <span style="width: 30%; display: inline-block; border: 1px solid black; margin-right: -4px; font-weight: bold">CLAMP RESULT PICTURES:</span>
    </div>
    <div style="width: 100%; font-size: 11px; margin-top: 70px;">
    <span style="width: 50%; text-align: center; display: inline-block; height: 350px; border: 1px solid black; margin-right: -4px;">
        <?php
        if (isset($files['amp'])) {
            if (is_array($files['amp'])) {
                $filecnt = (count($files['amp']) > 3) ? 3 : count($files['amp']);
                $width = 99.1 / $filecnt;
                echo '<span style="width: 99.1%; text-align: center; display: inline-block; height: 210px; margin-right: 4px; border: 1px solid black;">';
                for ($cnt = 0; $filecnt > $cnt; $cnt++) {
                    ?>

                    <img src="<?php echo convert_base64_img($files['amp'][$cnt], 'png',$width) ?>" style="padding-top: 55px; max-height: 200px; object-position: center center">
                    <?php
                }
                echo '</span>';
            } else { ?>
                <span style="width: 99.1%; text-align: center; display: inline-block; height: 210px; border: 1px solid black; margin-right: -4px;">
                    <img src="<?php echo convert_base64_img($files['amp'], 'png', '99.1%') ?>" style="padding-top: 5px; max-height: 200px;">
                </span>
            <?php }
        } else { ?>
            <span style="width: 99.1%; text-align: center; display: inline-block; height: 210px; border: 1px solid black; margin-right: -4px;"></span>
        <?php } ?>
    </span>
        <span style="width: 50%; text-align: center; display: inline-block; height: 350px; border: 1px solid black; margin-right: -4px;">
        <?php
        if (isset($files['volt'])) {
            if (is_array($files['volt'])) {
                $filecnt = (count($files['volt']) > 3) ? 3 : count($files['volt']);
                $width = 99.1 / $filecnt;
                echo '<span style="width: 99.1%; text-align: center; display: inline-block; height: 210px; margin-right: 4px; border: 1px solid black;">';
                for ($cnt = 0; $filecnt > $cnt; $cnt++) {
                    ?>

                    <img src="<?php echo convert_base64_img($files['volt'][$cnt], 'png',$width) ?>" style="padding-top: 55px; max-height: 200px; object-position: center center">
                    <?php
                }
                echo '</span>';
            } else { ?>
                <span style="width: 99.1%; text-align: center; display: inline-block; height: 210px; border: 1px solid black; margin-right: -4px;">
                    <img src="<?php echo convert_base64_img($files['volt'], 'png', '99.1%') ?>" style="padding-top: 5px; max-height: 200px;">
                </span>
            <?php }
        } else { ?>
            <span style="width: 99.1%; text-align: center; display: inline-block; height: 210px; border: 1px solid black; margin-right: -4px;"></span>
        <?php } ?>
    </span>
    </div>
    <div style="width: 100%; font-size: 11px; margin-top: -69px;">
        <span style="width: 50%; display: inline-block; text-align: center; border: 1px solid black; margin-right: -4px; font-weight: bold">AMPERE</span>
        <span style="width: 50%; display: inline-block; text-align: center; border: 1px solid black; margin-right: -4px; font-weight: bold">VOLTAGE</span>
    </div>
    <div style="width: 100%; font-size: 11px; margin-top: 0px">
        <span style="width: 30%; display: inline-block; border: 1px solid black; margin-right: -4px; font-weight: bold">PICTURE OF BILLS:</span>
    </div>
    <div style="width: 100%; font-size: 11px; margin-top: 0px;">
        <?php
        if (isset($files['bill'])) {
            if (is_array($files['bill'])) {
                $filecnt = count($files['bill']);
                $width = 99.1 / $filecnt;
                echo '<span style="width: 99.1%; text-align: center; display: inline-block; height: 210px; margin-right: 4px; border: 1px solid black;">';
                for ($cnt = 0; $filecnt > $cnt; $cnt++) {
                    ?>

                    <img src="<?php echo convert_base64_img($files['bill'][$cnt], 'png') ?>" style="padding-top: 55px; max-height: 200px; object-position: center center">
                    <?php
                }
                echo '</span>';
            } else { ?>
                <span style="width: 99.1%; text-align: center; display: inline-block; height: 210px; border: 1px solid black; margin-right: -4px;">
                    <img src="<?php echo convert_base64_img($files['bill'], 'png', '99.1%', '210') ?>" style="padding-top: 5px; max-height: 200px;">
                </span>
            <?php }
        } else { ?>
            <span style="width: 99.1%; text-align: center; display: inline-block; height: 210px; border: 1px solid black; margin-right: -4px;"></span>
        <?php } ?>
    </div>
    <div style="width: 100%; font-size: 11px; page-break-before: always !important; margin-top: -4px">
        <span style="width: 30%; display: inline-block; border: 1px solid black; margin-right: -4px; font-weight: bold">PICTURE(S) OF ROOF:</span>
    </div>
    <div style="width: 100%; font-size: 11px; margin-top: 70px;">
    <span style="width: 100%; text-align: center; display: inline-block; height: 350px; border: 1px solid black; margin-right: -4px; margin-top: -70px">
    <?php
    if (isset($files['roof'])) {
        if (is_array($files['roof'])) {
            $filecnt = count($files['roof']);
            $width = (100 / $filecnt);
            for ($bcnt = 0; $filecnt > $bcnt; $bcnt++) {
                $img = $files['roof'][$bcnt];
                ?>
                <img src="<?php echo convert_base64_img($files['roof'][$bcnt], 'png', $width . '%', '350px') ?>" style="padding-top: 5px; padding-left: 5px; max-width: 50%; max-height: 340px; margin-top: 50px">
                <?php
            }
        } else { ?>
            <img src="<?php echo convert_base64_img($files['roof'], 'png', '100%', '350px') ?>" style="padding-top: 5px; max-height: 340px;">
        <?php }
    } ?>
    </span>
    </div>
    <?php
} else {
    echo '<h3>NO SELECTED OR PUBLISHED TSSR SURVEY PROVIDED!</h3>';
    echo '<h4>Kindly return to Assessment for required data.</h4>';
}

?>