<?php
if ($app->duid > 0 && $app->durate > 0) {
    $durate = $app->durate;

    $distutility = get_dist_utility_list($app->duid)->name;
    $pae_letter_head = FCPATH . 'assets/global/img/pae_letter_head.png';
    $pae_letter_foot = FCPATH . 'assets/global/img/pae_letter_foot.png';

    $outright = 0;
    $twoyrs = 0;
    $threeyrs = 0;
    $fiveyrs = 0;
    $tenyrs = 0;
    $monthlyave = 0;
    $summerave = 0;

    if (!isset($draft)) {
        if (isset($app->newsize)) {
            $app->systemsizeid = $app->newsize;
        }
        if ($app->systemtype == 1) {
            $get_system_rates = $this->db->select('s.descs as sizename,r.sysid,r.systemsizeid,r.outright,r.twoyrs,r.threeyrs,r.fiveyrs,r.tenyrs,r.monthlyave,r.summerave,r.buildtime')
                ->from('proposal_standard_system_rates AS r')
                ->join('customer_system_size AS s', 's.sysid = r.systemsizeid', 'left')
                ->where(array('r.systemsizeid' => $app->systemsizeid, 'r.status' => 1))
                ->get()->row();
        }

        if ($app->systemtype == 2) {
            $get_system_rates = $this->db->select('sg.appid,sg.desc as sizename,p.outright,p.twoyrs,p.threeyrs,p.fiveyrs,p.tenyrs,p.monthlyave,p.summerave,p.buildtime')
                ->from('customer_system_group AS sg')
                ->join('proposal_nonstandard_system_rates AS p','sg.sysid = p.systemsizeid AND p.`status` = 1','left')
                ->where(array('sg.appid' => $id,'sg.status' => 1))
                ->get()->row();
        }

        if ($get_system_rates) {
            $app->systemsizename = $get_system_rates->sizename;
            $outright = $get_system_rates->outright;
            $twoyrs = $get_system_rates->twoyrs;
            $threeyrs = $get_system_rates->threeyrs;
            $fiveyrs = $get_system_rates->fiveyrs;
            $monthlyave = $get_system_rates->monthlyave;
            $tenyrs = $get_system_rates->tenyrs;
            $summerave = $get_system_rates->summerave;
            $buildtime = $get_system_rates->buildtime;
        }
    } else {
        if ($app->systemtype == 1) {
            $get_system_rates = $this->db->select('s.descs as sizename,r.sysid,r.systemsizeid,r.outright,r.twoyrs,r.threeyrs,r.fiveyrs,r.tenyrs,r.monthlyave,r.summerave,r.buildtime')
                ->from('proposal_standard_system_rates AS r')
                ->join('customer_system_size AS s', 's.sysid = r.systemsizeid', 'left')
                ->where(array('r.systemsizeid' => $app->newsize, 'r.status' => 1))
                ->get()->row();

            if ($get_system_rates) {
                $app->systemsizename = $get_system_rates->sizename;
                $outright = $get_system_rates->outright;
                $twoyrs = $get_system_rates->twoyrs;
                $threeyrs = $get_system_rates->threeyrs;
                $fiveyrs = $get_system_rates->fiveyrs;
                $monthlyave = $get_system_rates->monthlyave;
                $tenyrs = $get_system_rates->tenyrs;
                $summerave = $get_system_rates->summerave;
                $buildtime = $get_system_rates->buildtime;
            }

        } else {

            $app->systemsizename = $app->newsize;
            $outright = $app->outright;
            $twoyrs = $app->twoyrs;
            $threeyrs = $app->threeyrs;
            $fiveyrs = $app->fiveyrs;
            $monthlyave = $app->monthlyave;
            $tenyrs = $app->tenyrs;
            $summerave = $app->summerave;
        }
    }

    if (!isset($app->address)) {
        $app->address = $app->addrspecific;
    }

    if (isset($app->corpname)) {
        $corpname = $app->corpname;
        $corpname .= (isset($app->corpbranch) && $app->corpbranch != '') ? ' (' . $app->corpbranch . ')' : '';
    }

    $monthlycost = $durate * $monthlyave;
    $peso = '<span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span>';
    $pvl = array();
    $mpp = array();
    if (isset($id)) {
        $pvdir = FCPATH . 'uploads/attachments/cad/applications/' . str_pad($id, 6, '0', STR_PAD_LEFT) . '/Assessment/Docs/';
        //$bullet = FCPATH . 'assets/global/img/check-list.png';
        $files = scandir($pvdir);

        foreach ($files as $file) {
            if (strpos(strtolower($file), 'pv_layout') !== false || strpos(strtolower($file), 'pv_roof') !== false) {
                $pvl[] = $pvdir . utf8_decode($file);
            }
            if (strpos(strtolower($file), 'mpp') !== false) {
                $mpp[] = $pvdir . utf8_decode($file);
            }
        }
    } else {
        if (isset($app->pv_img)) {
            foreach ($app->pv_img as $pvlayout) {
                $pvl[] = $pvlayout;
            }
        }

        if (isset($app->mp_img)) {
            foreach ($app->mp_img as $monthlyprod) {
                $mpp[] = $monthlyprod;
            }
        }
    }

    //$kw = str_replace('kWp Grid-Tied','',$app->systemsizename);
    $kwatt = 1;
    if (preg_match('/(\d+(?:\.\d+)?)\s*kWp/i', $app->systemsizename, $matches)) {
        $kwatt = (float)$matches[1];
    }
    $ishybrid = strpos(strtolower($app->systemsizename),'hybrid') !== false;
    $hasbattery = strpos(strtolower($app->systemsizename),'battery') !== false;
    $hassystem = strpos(strtolower($app->systemsizename),'system') !== false ? '' : ' System';

    $maxprop = ($tenyrs && $tenyrs > 0) ? $tenyrs : $fiveyrs;

    $bill = 0;
    $bill_qry = $this->db->select('avebill,bill')
        ->from('application_customers_details')
        ->where(array('sysid' => $id))
        ->get()->row();

    if ($bill_qry) {
        $bill = ($bill_qry->bill) ? round($bill_qry->bill,2) : round($bill_qry->avebill,2);
    }
    ?>
    <html>
    <head>
        <title></title>
        <style>
            html {
                margin-right: 48px;
                margin-left: 48px;
            }

            header {
                position: fixed;
                top: 0px;
                height: 50px;
                background-color: transparent;
                color: white;
                text-align: center;
                line-height: 35px;
            }

            ul.list {
                list-style: none outside none;
                font-family: Arial, Verdana, sans-serif;
                font-size: 13px;
                line-height: 15px;
                margin-left: 0em;
                padding-left: 1em;
            }

            ul.list > li:before {
                font-size: 14pt;
                font-family: DejaVu Sans;
                content: '\2714';
                color: #FF6700;
            }

            ul.list > li {
                padding-left: 2em;
                text-indent: -1.5em;
                padding-bottom: 0.25em
            }

            footer {
                position: fixed;
                bottom: 10px;
                height: 50px;
                background-color: transparent;
                color: white;
                text-align: center;
                line-height: 35px;
            }

            main {
                margin-top: 110px;
            }

            .page_break {
                page-break-before: always;
                margin-top: 120px;
            }

            .peso:before {
                font-family: DejaVu Sans;
                content: '\20B1';
                margin-left: .100em;
            }

            .peso {
                break-inside: avoid;
                white-space: nowrap;
            }

            i .peso:before {
                font-family: DejaVu Sans;
                content: '\0020\20B1';
                margin-left: .100em;
            }
        </style>
    </head>
    <body>
    <header>
        <img src="<?php echo $pae_letter_head; ?>" width="100%"/>
    </header>

    <main>
        <?php
        $ratesum = $outright + $twoyrs + $threeyrs + $fiveyrs + $tenyrs;
        if (
            $ratesum > 0 &&
            $monthlyave > 0
            //$tenyrs > 0 &&
            //$summerave > 0
        ) { ?>
            <div style="display: block;">
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 13px; text-align: justify; padding-top: 13px">
                    <?php
                    if (isset($app->firstname)) {
                        echo '<b>'.utf8_decode(ucwords(strtolower($app->firstname . ' ' . $app->lastname))).'</b><br>';
                        echo (isset($corpname) && $corpname !='') ? utf8_decode($corpname).'<br>' : '';
                    } else {
                        if (isset($corpname)) {
                            echo '<b>'.utf8_decode($corpname).'</b><br>';
                        }
                    }
                    echo utf8_decode($app->address);
                    ?>
                </p>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify"></p>
                <p> </p>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify; padding-top: 13px">Good day,</p>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify"></p>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify">
                    Following our assessment, we recommend a <b> <?php echo $app->systemsizename.$hassystem; ?></b> for your needs.
                    This system allows you to utilize solar energy for your daytime use and provides backup power for evening use or emergencies. When the battery depletes, your consumption will seamlessly switch to your electric utility provider.</p>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify"></p>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify;">
                    <?php
                    if ($kwatt < 20) {
                        $start = 'The ';
                    } else {
                        $start = 'If solar is utilized everyday and there are no days on non-operation, the ';
                    }
                    ?>
                    <?php echo $start.strstr($app->systemsizename, ' ', true); ?> system is projected to generate approximately
                    <b><?php echo number_format($monthlyave, 2); ?>kWh/month</b>.
                    Given a rate of <?php echo $peso . number_format($durate, 2); ?> per kWh,
                    this translates to monthly savings of <b><span class="peso"><?php echo number_format($monthlycost, 2); ?></span></b>,
                    or an annual savings of <b class="peso"><?php echo number_format($monthlycost * 12, 2); ?></b>.</p>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify"></p>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify">
                    The system costs <b><?php echo $peso . number_format($outright, 2); ?></b>, which includes materials, installation, and a monitoring app that allows you to track your solar power production.
                    We offer financing options with <b>ZERO down payment</b> of up to 5 years, with monthly installments as low as <b><?php echo $peso.number_format($maxprop, 2); ?>/month</b>.
                    Please see details below for all payment options.
                </p>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify"></p>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                    <tr>
                        <td></td>
                        <td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border-left: 1px solid #000; border-top: 1px solid #000; background: #c5d9f1; width: 150px; text-align: center;">
                            Outright Purchase
                        </td>
                        <td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border-left: 1px solid #000; border-top: 1px solid #000; background: #8db4e2; text-align: center;">
                            2 Years
                        </td>
                        <?php if (isset($threeyrs) && $threeyrs > 0) { ?>
                            <td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border-left: 1px solid #000; border-top: 1px solid #000; background: #fabf8f; border-right: 1px solid #000; text-align: center;">
                                3 Years
                            </td>
                        <?php } ?>
                        <td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border-left: 1px solid #000; border-top: 1px solid #000; background: #538dd5; text-align: center;">
                            5 Years
                        </td>
                        <?php if (isset($tenyrs) && $tenyrs > 0) { ?>
                        <td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border-left: 1px solid #000; border-top: 1px solid #000; background: #fabf8f; border-right: 1px solid #000; text-align: center;">
                            10 Years
                        </td>
                        <?php } ?>
                    </tr>
                    <td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000; background: #000; color: #fff; width: 125px; text-align: center;"><?php echo strstr($app->systemsizename, ' ', true); ?> System</td>
                    <td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000; background: #c5d9f1; text-align: center;"><?php echo $peso . number_format($outright, 2); ?></td>
                    <td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000; background: #8db4e2; text-align: center;"><?php echo $peso . number_format($twoyrs, 2); ?>/month</td>
                    <?php if (isset($threeyrs) && $threeyrs > 0) { ?>
                        <td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000; background: #fabf8f; border-right: 1px solid #000; text-align: center;"><?php echo $peso . number_format($threeyrs, 2); ?>/month</td>
                    <?php } ?>
                    <td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000; background: #538dd5; text-align: center;"><?php echo $peso . number_format($fiveyrs, 2); ?>/month</td>
                    <?php if (isset($tenyrs) && $tenyrs > 0) { ?>
                    <td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000; background: #fabf8f; border-right: 1px solid #000; text-align: center;"><?php echo $peso . number_format($tenyrs, 2); ?>/month</td>
                    <?php } ?>
                    </tr>
                    </tbody>
                </table>
                <br>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify"></p>
                <p style="font-weight: bold; font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px;">
                    Premium product comes with a superior warranty:</p>
                <ul class="list">
                    <li> 5-year replacement warranty for inverters. (applicable to outright purchase, 2-year plan, and 5-year plan)</li>
                    <li> 5-year replacement warranty on the battery.</li>
                    <?php if (isset($tenyrs) && $tenyrs > 0) { ?>
                        <li> Free inverter replacement for the entire duration of 10 years plan.</li>
                        <li> Free maintenance and Acts of God insurance for 10 years plan.</li>
                    <?php } ?>
                    <li> Premium panels are guaranteed to be at least 80% efficient or more for 25 years.</li>
                    <li> With very low degradation rate per annum compared to conventional panels.</li>
                    <li> FREE replacement of solar panels if efficiency rate falls below 80% within 25 years.</li>
                </ul>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify"></p>

                <?php if ($app->systemtype == 1 && $buildtime != '') {
                    $day = ((string)$buildtime == '1') ? ' day' : ' days';
                    ?>
                    <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify">
                        We look forward to your confirmation.
                        Upon signing an agreement, we will schedule your installation date.
                        <?php echo ($app->systemtype == 1 && $buildtime != '') ? 'The estimated time for completion of the system installation will be '.$buildtime.$day.'.' : '';?>
                    </p>
                    <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify">
                        If you require further details about your system,
                        please don’t hesitate to contact us at 09171460614,
                        Viber 09082685311, landline (033)321-0493,
                        and email us at sales@paenergy.ph.
                        You may also reach us through our Messenger account, PA Energy.</p>
                <?php } else {?>
                    <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify">
                        We look forward to your confirmation.
                        Upon signing an agreement, we will schedule your installation date.
                        If you require further details about your system,
                        please don’t hesitate to contact us at 09171460614,
                        Viber 09082685311, landline (033)321-0493,
                        and email us at sales@paenergy.ph.
                        You may also reach us through our Messenger account, PA Energy.</p>
                    </p>

                <?php }?>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify">Thank you for choosing PA Energy!</p>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify; height: auto; page-break-inside: avoid">
                    <br>
                    <?php if (!isset($corpname) || !isset($app->firstname)) {
                        echo '<br>';
                    } ?>
                </p>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px;">
                    <span style="display: inline-block; width: 25%; text-align: center; font-weight: bold; z-index: -1">MARCELO U. CACHO</span>
                    <img class="signature" src="">
                    <br>
                    <span style="display: inline-block; width: 25%; text-align: center; border-top: 1px solid #000; z-index: -1">General Manager</span>
                    <span style="display: inline-block; width: 45%;"> </span>
                    <span style="display: inline-block; width: 25%; text-align: center; border-top: 1px solid #000; z-index: -1">Conforme</span>
                </p>
            </div>
            <br>
            <br>
            <div class="page_break" style="display: block">
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify; font-weight: bold; color: #FF6700">
                    How do I calculate my solar payback period?
                </p>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify">
                    <i>
                        The cost to install a <b><?php echo strstr($app->systemsizename, ' ', true); ?> system
                            is <?php echo $peso . number_format($outright, 2); ?></b> and it only takes
                        <b><?php echo number_format($outright / ($monthlycost * 12), 1); ?> years to recover your
                            investment.</b>
                        We computed your recovery using the average <?php echo $distutility; ?> distribution
                        rate for your area of <?php echo $peso.number_format($durate, 2); ?>/kWh
                        multiplied by the yearly solar production of <?php echo number_format($monthlyave * 12, 2); ?>kWh. This
                        results in a yearly savings of <b><?php echo $peso.number_format($monthlycost * 12, 2); ?></b>.
                    </i>
                </p>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px; text-align: justify">
                    <i>
                        If you divide the yearly savings by the system cost you will get
                        a <?php echo number_format((($monthlycost * 12) / $outright) * 100, 2); ?>% yearly return on your
                        investment. Essentially, after <?php echo number_format($outright / ($monthlycost * 12), 1); ?> years,
                        anything generated will be <i>&#8220;free power.&#8221;</i>
                    </i>
                </p>

                <p style="font-weight: bold; font-family: Arial, Verdana, sans-serif; font-size: 16px; line-height: 15px; color: #FF6700">
                    Benefits of Hybrid Solar setup:</p>
                <ul class="list">
                    <li> <b>Energy Independence</b>: A solar hybrid system allows you to generate and store your own electricity, reducing reliance on the grid.</li>
                    <li> <b>Cost Savings</b>: By using solar power during the day and stored power at night, you can significantly reduce your electricity bills.</li>
                    <li> <b>Backup Power</b>: In the event of a power outage, a solar hybrid system can provide backup power, ensuring your essential appliances continue to run.</li>
                    <li> <b>Environmentally Friendly</b>: Solar power is a renewable energy source that reduces your carbon footprint.</li>
                </ul>
            </div>
            <div class="page_break">
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 21px; line-height: 15px; text-align: center; font-weight: bold; color: #FF6700">SOLAR PANEL ROOF LAYOUT</p>
                <br>
                <?php
                if (count($pvl) > 0) {
                    $pvwidth = (count($pvl) > 1) ? 95 / 2 : 100;
                    foreach ($pvl as $pvimg) {
                        echo '<p style="font-family: Arial, Verdana, sans-serif; font-size: 21px; line-height: 15px; text-align: center; font-weight: bold; color: #FF6700">';
                        echo '<img src="' . $pvimg . '" data-type="PVL" width="' . $pvwidth . '%"/>';
                        echo '</p>';
                    }
                } else {
                    echo '<p>PV Layout not found.</p>';
                }
                ?>

            </div>
            <?php if (count($mpp) > 0) {?>
                <div class="page_break" style="display: block">
                    <p style="font-family: Arial, Verdana, sans-serif; font-size: 21px; line-height: 15px; text-align: center; font-weight: bold; color: #FF6700">PROJECTED MONTHLY PRODUCTION</p>
                    <br>
                    <?php
                    $mpwidth = (count($mpp) > 1) ? 95/2 : 100;
                    foreach ($mpp as $mpimg) {
                        echo '<p style="font-family: Arial, Verdana, sans-serif; font-size: 21px; line-height: 15px; text-align: center; font-weight: bold; color: #FF6700">';
                        echo '<img src="' . $mpimg . '" data-type="PVL" width="' . $mpwidth . '%" style="max-height: 690px;"/>';
                        echo '</p>';
                    }
                    ?>

                </div>
            <?php } ?>
        <?php } else {
            echo '<h1>PLEASE SET PROPOSED SYSTEM RATES AND REFRESH THE PREVIEW!</h1>';
        } ?>
    </main>

    
    <footer>
        <img src="<?php echo $pae_letter_foot; ?>" width="100%"/>';
    </footer>
    </body>
    </html>
<?php } else { ?>
    <h1>Distribution Utility and/or Rate not set.</h1>';
    <h3>Kindly set DU and Rate and refresh page.</h3>';
<?php }