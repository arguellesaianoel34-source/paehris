<?php
if ($app->duid > 0 && $app->durate > 0) {
    $durate = $app->durate;
    $netmetering = $app->netmetering ?? false;

    $distutility = get_dist_utility_list($app->duid)->name;
    $pae_letter_head = FCPATH . 'assets/global/img/pae_letter_head.png';
    $pae_letter_foot = FCPATH . 'assets/global/img/pae_letter_foot.png';

    $outright = 0;
    $twoyrs = 0;
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
                ->join('proposal_nonstandard_system_rates AS p', 'sg.sysid = p.systemsizeid AND p.`status` = 1', 'left')
                ->where(array('sg.appid' => $id, 'sg.status' => 1))
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
    //$kw = substr($app->systemsizename,0,strpos($app->systemsizename,'kWp'));
    if (preg_match('/(\d+(?:\.\d+)?)\s*kWp/i', $app->systemsizename, $matches)) {
        $kwatt = (float) $matches[1];
    }
    $ishybrid = strpos(strtolower($app->systemsizename), 'hybrid') !== false;
    $hasbattery = strpos(strtolower($app->systemsizename), 'battery') !== false;

    $maxprop = ($tenyrs && $tenyrs > 0) ? $tenyrs : $fiveyrs;

    if ($netmetering) {
        //GET VARIABLES FOR NET METERING
        $clamp = 0;
        $survey_qry = $this->db->select()
            ->from('application_customers_system_size')
            ->where(array('appid' => $id))
            ->get()->row();

        if ($survey_qry) {
            $clamp = ceil(($survey_qry->power / 1000) / 0.5) * 0.5;
        }

        $consuption = $app->aveusage;
        $aveprod = $app->monthlyprod;
        $gencharge = $app->generationcharge;

        $day_usage = round(($clamp / $kwatt) * $aveprod);
        $day_savings = round($day_usage * $durate, 2);

        $night_usage = $aveprod - $day_usage;
        $night_savings = round($night_usage * $gencharge, 2);

        $total_savings = $day_savings + $night_savings;

        //ROI
        $annual_savings = round($total_savings * 12, 2);
        $return_year = round($outright / $annual_savings, 2);
        $return_rate = round(($annual_savings / $outright) * 100, 2);

        //GET AVERAGE BILL
        $bill = 0;
        $bill_qry = $this->db->select('avebill,bill')
            ->from('application_customers_details')
            ->where(array('sysid' => $id))
            ->get()->row();

        if ($bill_qry) {
            $bill = ($bill_qry->bill) ? round($bill_qry->bill, 2) : round($bill_qry->avebill, 2);
        }
    }
?>
    <html>

    <head>
        <title></title>
        <style>
            /* Short bond paper size: 8.5 x 13 inches */
            @page {
                size: 8in 12in;
            }

            html,
            body {
                width: 8in;
                height: 12in;
                margin: 0;
                padding: 0;
            }
            
            body {
                padding: 48px;
            }

            main {
                margin-top: 100px;
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

            footer {
                position: fixed;
                bottom: 10px;
                height: 50px;
                background-color: transparent;
                color: white;
                text-align: center;
                line-height: 35px;
            }

            ul.list {
                padding-left: 20px;
                text-indent: 2px;
                list-style: none;
                list-style-position: outside;
                font-family: Arial, Verdana, sans-serif;
                font-size: 13px;
                line-height: 15px;
                margin-left: 0.5em
            }

            ul.list li:before {
                font-size: 14pt;
                font-family: DejaVu Sans;
                content: '\2714';
                margin-right: .100em;
                margin-left: .100em;
                color: #FF6700;
            }


            .page_break {
                page-break-before: always;
                margin-top: 105px;
            }

            .peso:before {
                font-family: DejaVu Sans;
                content: '\20B1';
                margin-left: .100em;
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
            <img src="<?php echo $pae_letter_head; ?>" width="100%" />
        </header>

        <footer>
            <img src="<?php echo $pae_letter_foot; ?>" width="100%" />';
        </footer>

        <main>
            <?php
            $ratesum = $outright + $twoyrs + $threeyrs + $fiveyrs + $tenyrs;
            if (
                $ratesum > 0 &&
                $monthlyave > 0 &&
                //$tenyrs > 0 &&
                $summerave > 0
            ) { ?>
                <div style="display: block;">
                    <p
                        style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify; padding-top: 13px">
                        <?php
                        if (isset($app->firstname)) {
                            echo '<b>' . utf8_decode(ucwords(strtolower($app->firstname . ' ' . $app->lastname))) . '</b><br>';
                            echo (isset($corpname) && $corpname != '') ? utf8_decode($corpname) . '<br>' : '';
                        } else {
                            if (isset($corpname)) {
                                echo '<b>' . utf8_decode($corpname) . '</b><br>';
                            }
                        }
                        echo utf8_decode($app->address);
                        ?>
                    </p>
                    <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify">
                    </p>
                    <p> </p>
                    <p
                        style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify; padding-top: 13px">
                        Good day,</p>
                    <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify">
                    </p>
                    <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify">
                        <?php if (!$netmetering) { ?>
                            Based on the result of our assessment, we estimate you would need a <b>
                                <?php echo $app->systemsizename; ?>
                                system</b>. This allows you to harness solar energy directly for your daytime use.
                            Your consumption is seamlessly switched to your electric utility provider for your
                            night time power use so you save on the cost of your daytime use.
                    </p>
                <?php } else { ?>
                    Following our assessment, we recommend a <b> <?php echo $app->systemsizename; ?> solar system</b> for your
                    needs.
                    With this setup, you'll directly tap into solar energy during the day, powering your home or business.
                    Plus, any surplus electricity generated can be sold back to your utility provider.
                    Come nighttime, your consumption seamlessly switches to the grid, ensuring cost savings on daytime usage.
                    It's like having the sun work for you around the clock!
                <?php } ?>
                </p>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify">
                </p>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify">
                    <?php
                    if ($kwatt < 20) {
                        $start = 'The ';
                    } else {
                        $start = 'If solar is utilized everyday and there are no days on non-operation, the ';
                    }
                    ?>

                    <?php if (!$netmetering) { ?>
                        <?php echo $start . '<b>' . strstr($app->systemsizename, ' ', true) . ' system</b>'; ?> will generate around
                        <b><?php echo number_format($monthlyave, 2); ?> kilowatt-hours (kWh)</b> per month.
                        Now, if we multiplied that by the local rate of <b><?php echo $peso . number_format($durate, 2); ?> per
                            kWh</b>,
                        you're looking at monthly savings of <b class="peso"><?php echo number_format($monthlycost, 2); ?></b>
                        &#8212; assuming you use all that energy during daylight hours.
                        And over the course of a year, that adds up to a conservative estimate of <b
                            class="peso"><?php echo number_format($monthlycost * 12, 2); ?></b> in savings.
                    <?php } else { ?>
                        <?php echo $start . '<b>' . strstr($app->systemsizename, ' ', true) . ' system</b>'; ?> will generate
                        approximately
                        <b><?php echo number_format($monthlyave, 2); ?> kilowatt-hours (kWh)</b> per month. With your daytime
                        usage, at <b><?php echo $day_usage; ?> kWh monthly</b>,
                        you'll save about <b><?php echo $peso . number_format($day_savings, 2); ?> monthly</b> at the local rate
                        of <b><?php echo $peso . number_format($durate, 2); ?> per kWh</b>.
                        The remaining <b><?php echo number_format($night_usage); ?> kWh</b> can be sold back to the grid at
                        <b><?php echo $peso . number_format($gencharge, 4); ?> per kWh buyback rate</b>,
                        earning you an additional <b><?php echo $peso . number_format($night_savings, 2); ?> monthly</b>.
                        In total, your monthly savings reach <b><?php echo $peso . number_format($total_savings, 2); ?></b>,
                        resulting in annual savings of <b><?php echo $peso . number_format($annual_savings, 2); ?></b>.
                        Note that all rates are subject to change monthly.
                    <?php } ?>
                </p>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify">
                </p>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify">
                    The cost of the system is <b><?php echo $peso . number_format($outright, 2); ?></b>
                    and can be financed via installment plans with <b>ZERO down payment</b> of up to 10 years for as low as
                    <b class="peso"><?php echo number_format($maxprop, 2); ?>/month</b>.
                    That cost includes the materials, installation and the monitoring app that allows you to track your
                    solar power production.
                    Please see details below for all payment options.
                </p>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify">
                </p>
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="">
                    <tbody>
                        <tr>
                            <td></td>
                            <td
                                style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border-left: 1px solid #000; border-top: 1px solid #000; background: #c5d9f1; width: 150px; text-align: center;">
                                Outright Purchase
                            </td>
                            <td
                                style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border-left: 1px solid #000; border-top: 1px solid #000; background: #8db4e2; text-align: center;">
                                2 Years
                            </td>
                            <td
                                style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border-left: 1px solid #000; border-top: 1px solid #000; background: #538dd5; text-align: center;">
                                5 Years
                            </td>
                            <?php if (isset($tenyrs) && $tenyrs > 0) { ?>
                                <td
                                    style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border-left: 1px solid #000; border-top: 1px solid #000; background: #fabf8f; border-right: 1px solid #000; text-align: center;">
                                    10 Years
                                </td>
                            <?php } ?>
                        </tr>
                        <td
                            style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000; background: #000; color: #fff; width: 125px; text-align: center;">
                            <?php echo strstr($app->systemsizename, ' ', true); ?> System</td>
                        <td
                            style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000; background: #c5d9f1; text-align: center;">
                            <?php echo $peso . number_format($outright, 2); ?></td>
                        <td
                            style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000; background: #8db4e2; text-align: center;">
                            <?php echo $peso . number_format($twoyrs, 2); ?>/month</td>
                        <td
                            style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000; background: #538dd5; text-align: center;">
                            <?php echo $peso . number_format($fiveyrs, 2); ?>/month</td>
                        <?php if (isset($tenyrs) && $tenyrs > 0) { ?>
                            <td
                                style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border-left: 1px solid #000; border-top: 1px solid #000; border-bottom: 1px solid #000; background: #fabf8f; border-right: 1px solid #000; text-align: center;">
                                <?php echo $peso . number_format($tenyrs, 2); ?>/month</td>
                        <?php } ?>
                        </tr>
                    </tbody>
                </table>
                <br>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify">
                </p>
                <p style="font-weight: bold; font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px;">
                    Premium product, better warranty:</p>
                <ul class="list">
                    <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.15em"> 5 years replacement warranty
                        for inverters. (Outright purchase, 2 years plan and 5 years plan)</li>
                    <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.15em"> Free inverter replacement for
                        the entire duration of 10 years plan.</li>
                    <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.15em"> Free maintenance and Acts of
                        God insurance for 10 years plan.</li>
                    <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.15em"> Premium panels are guaranteed
                        to be at least 80% efficient or more for 25 years.</li>
                    <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.15em"> With very low degradation
                        rate per annum compared to conventional panels with a major drop in efficiency yearly.</li>
                    <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.15em"> FREE replacement of solar
                        panels if efficiency rate falls below 80% within 25 years.</li>
                    <?php if ($netmetering) { ?>
                        <li style="font-size: 13px; padding-left: 0.25em; padding-bottom: 0.15em"> FREE net-metering processing
                            (submission of government and utility documents required).</li>
                    <?php } ?>
                </ul>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify">
                </p>

                <?php if ($app->systemtype == 1 && $buildtime != '') {
                    $day = ((string) $buildtime == '1') ? ' day' : ' days';
                ?>
                    <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify">
                        We will be waiting for your confirmation.
                        Once an agreement is signed, we will schedule the date of your installation.
                        <?php echo ($app->systemtype == 1 && $buildtime != '') ? 'The estimated time for completion of the system installation will be ' . $buildtime . $day . '.' : ''; ?>
                    </p>
                    <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify">
                        If you need any further details about your system, please feel free to contact us.</p>
                <?php } else { ?>
                    <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify">
                        We will be waiting for your confirmation.
                        Once an agreement is signed, we will schedule the date of your installation.
                        If you need any further details about your system, please feel free to contact us.
                    </p>

                <?php } ?>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify">
                    Thank you for choosing PA Energy!</p>
                <p
                    style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify; height: auto; page-break-inside: avoid">
                    <br>
                    <?php if (!isset($corpname) || !isset($app->firstname)) {
                        echo '<br>';
                    } ?>
                </p>
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px;">
                    <span
                        style="display: inline-block; width: 25%; text-align: center; font-weight: bold; z-index: -1">MARCELO
                        U. CACHO</span>
                    <img class="signature" src="">
                    <br>
                    <span
                        style="display: inline-block; width: 25%; text-align: center; border-top: 1px solid #000; z-index: -1">General
                        Manager</span>
                    <span style="display: inline-block; width: 45%;"> </span>
                    <span
                        style="display: inline-block; width: 25%; text-align: center; border-top: 1px solid #000; z-index: -1">Conforme</span>
                </p>
                </div>



                <!-- PAGE 2 -->
                <div class="page_break" style="display: block">
                    <p
                        style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify; font-weight: bold; color: #FF6700">
                        How do I calculate my solar payback period?
                    </p>
                    <?php if (!$netmetering) { ?>
                        <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify">
                            <i>
                                The cost to install a <b><?php echo strstr($app->systemsizename, ' ', true); ?> system
                                    is <?php echo $peso . number_format($outright, 2); ?></b> and it only takes
                                <b><?php echo number_format($outright / ($monthlycost * 12), 1); ?> years to recover your
                                    investment.</b> We computed your recovery using the average <?php echo $distutility; ?>
                                distribution
                                rate for your area of <span class="peso"></span><?php echo number_format($durate, 2); ?>/kWh
                                multiplied by the yearly solar production of <?php echo number_format($monthlyave * 12, 2); ?>kWh.
                                This
                                results in a yearly savings of <b
                                    class="peso"><?php echo number_format($monthlycost * 12, 2); ?></b>.
                            </i>
                        </p>
                        <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify">
                            <i>
                                If you divide the yearly savings by the system cost you will get
                                a <?php echo number_format((($monthlycost * 12) / $outright) * 100, 2); ?>% yearly return on your
                                investment. Essentially, after <?php echo number_format($outright / ($monthlycost * 12), 1); ?>
                                years,
                                anything generated will be <i>&#8220;free power.&#8221;</i>
                            </i>
                        </p>
                        <p
                            style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify; font-weight: bold; color: #FF6700">
                            Actual savings on a 10-year program during the rainy season.
                        </p>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tbody>
                                <tr>
                                    <th
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 8px; border: 1px solid #000; background: #92CDDC; text-align: center; line-height: 17px">
                                        Cost of Utility Purchased Power
                                    </th>
                                    <th
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 8px; border: 1px solid #000; background: #C3E4FB; text-align: center; line-height: 17px">
                                        *Utility Comparative Rate
                                    </th>
                                    <th
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 8px; border: 1px solid #000; background: #FFC299; text-align: center; line-height: 17px">
                                        Fixed Monthly Payment
                                    </th>
                                    <th
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 8px; border: 1px solid #000; background: #B8CCE4; text-align: center; line-height: 17px">
                                        PA Energy Comparative Rate
                                    </th>
                                    <th
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 10px; border: 1px solid #000; background: #FEA022; text-align: center; line-height: 17px">
                                        Actual Savings
                                    </th>
                                </tr>
                                <tr>
                                    <td
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #92CDDC; text-align: center;">
                                        <?php echo $peso . number_format($monthlycost, 2); ?>/month</td>
                                    <td
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #C3E4FB; text-align: center;">
                                        <?php echo $peso . number_format($durate, 2); ?>/kWh</td>
                                    <td
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #FFC299; text-align: center;">
                                        <?php echo $peso . number_format($maxprop, 2); ?>/month</td>
                                    <td
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #B8CCE4; text-align: center;">
                                        <?php echo $peso . number_format($maxprop / $monthlyave, 2); ?>/kWh</td>
                                    <td
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #FEA022; text-align: center;">
                                        <?php echo $peso . number_format($monthlycost - (round($maxprop / $monthlyave, 2, PHP_ROUND_HALF_UP) * $monthlyave), 2); ?>/month
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p
                            style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify; font-weight: bold; color: #FF6700">
                            Actual savings on a 10-year program during the summer season.
                        </p>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tbody>
                                <tr>
                                    <th
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 8px; border: 1px solid #000; background: #92CDDC; text-align: center; line-height: 17px">
                                        Cost of Utility Purchased Power
                                    </th>
                                    <th
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 8px; border: 1px solid #000; background: #C3E4FB; text-align: center; line-height: 17px">
                                        *Utility Comparative Rate
                                    </th>
                                    <th
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 8px; border: 1px solid #000; background: #FFC299; text-align: center; line-height: 17px">
                                        Fixed Monthly Payment
                                    </th>
                                    <th
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 8px; border: 1px solid #000; background: #B8CCE4; text-align: center; line-height: 17px">
                                        PA Energy Comparative Rate
                                    </th>
                                    <th
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 10px; border: 1px solid #000; background: #FEA022; text-align: center; line-height: 17px">
                                        Actual Savings
                                    </th>
                                </tr>
                                <tr>
                                    <td
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #92CDDC; text-align: center;">
                                        <?php echo $peso . number_format($summerave * $durate, 2); ?>/month</td>
                                    <td
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #C3E4FB; text-align: center;">
                                        <?php echo $peso . number_format($durate, 2); ?>/kWh</td>
                                    <td
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #FFC299; text-align: center;">
                                        <?php echo $peso . number_format($maxprop, 2); ?>/month</td>
                                    <td
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #B8CCE4; text-align: center;">
                                        <?php echo $peso . number_format($maxprop / $summerave, 2); ?>/kWh</td>
                                    <td
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #FEA022; text-align: center;">
                                        <?php echo $peso . number_format(($summerave * $durate) - (round($maxprop / $summerave, 2, PHP_ROUND_HALF_UP) * $summerave), 2); ?>/month
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p
                            style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify; font-weight: bold; color: #ff0000">
                            *Based on the average <?php echo $distutility; ?> distribution rate in your area
                            of <?php echo $peso . number_format($durate, 2); ?>/kWh. <?php echo $distutility; ?>
                            kwh rate is subject to change monthly.
                        </p>
                        <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify">
                            <i>
                                Your <?php echo strstr($app->systemsizename, ' ', true); ?> system can get an energy production of
                                up
                                to <?php echo number_format($summerave, 2); ?>kwh/month during summer. You also have a fixed
                                effective
                                kWh rate for the duration of the 10 years program. As such, the cost of energy produced by your
                                system
                                for both rainy and summer seasons is greater than your fixed monthly payment.
                            </i>
                        </p>
                        <p
                            style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify; color: #6A7BCD">
                            <i>
                                Note: Savings are based on consumption within the property, and are an annual average. Some months
                                are
                                lower and some months are higher.
                            </i>
                        </p>
                    <?php } else { ?>
                        <p
                            style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify; font-style: italic">
                            Your solar energy system is set to <b>produce <?php echo number_format($aveprod); ?> kWh per month</b>.
                            During the day, <b><?php echo number_format($day_usage); ?> kWh</b> (equivalent to
                            <b><?php echo number_format($clamp, 1); ?> kW of solar capacity</b> subject to actual daytime power
                            usage) will power your home,
                            <b>saving you <?php echo $peso . number_format($day_savings, 2); ?></b> per month at the current
                            distribution charge of <b><?php echo $peso . number_format($durate, 2); ?> per kWh</b>(subject to change
                            monthly).
                            The remaining <b><?php echo number_format($night_usage); ?> kWh</b> will be sold back to the grid at the
                            current net-metering buyback rate of <b><?php echo $peso . number_format($gencharge, 4); ?> per kWh</b>
                            (subject to change monthly),
                            resulting in an additional <b><?php echo $peso . number_format($night_savings, 2); ?></b> monthly.
                            Altogether, your monthly savings amount to <b><?php echo $peso . number_format($total_savings, 2); ?></b>.
                        </p>
                        <p
                            style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify; font-style: italic">
                            Now, let's talk return on investment (ROI).
                            With a system cost of <b><?php echo $peso . number_format($outright, 2); ?></b>, your annual savings of
                            <b><?php echo $peso . number_format($annual_savings, 2); ?></b> mean you'll recoup your investment in
                            approximately <b><?php echo number_format($return_year, 2); ?></b> years.
                            That's an impressive <b><?php echo number_format($return_rate, 2); ?>%</b> annual gain &#8212; far better
                            than any bank interest rate!
                        </p>
                        <p
                            style="font-weight: bold; font-family: Arial, Verdana, sans-serif; font-size: 16px; line-height: 17px; color: #FF6700">
                            Projected savings on a 10-year program.
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody>
                                <tr>
                                    <th
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #92CDDC; text-align: center; line-height: 17px">
                                        Current Utility Bill
                                    </th>
                                    <th
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #C3E4FB; text-align: center; line-height: 17px">
                                        Solar Savings
                                    </th>
                                    <th
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #FFC299; text-align: center; line-height: 17px">
                                        New Power Bill
                                    </th>
                                    <!--<th style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #B8CCE4; text-align: center; line-height: 17px">
                                10-Year Plan Monthly Payment
                            </th>
                            <th style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #FEA022; text-align: center; line-height: 17px">
                                Projected Savings Per Month
                            </th>-->
                                </tr>
                                <tr>
                                    <td
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #92CDDC; text-align: center;">
                                        <?php echo $peso . number_format($bill, 2); ?></td>
                                    <td
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #C3E4FB; text-align: center;">
                                        <?php echo $peso . number_format($total_savings, 2); ?></td>
                                    <td
                                        style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #FFC299; text-align: center;">
                                        <?php echo $peso . number_format($bill - $total_savings, 2); ?></td>
                                    <!--
                            <td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #B8CCE4; text-align: center;"><?php echo $peso . number_format($tenyrs, 2); ?></td>
                            <td style="font-family: Arial, Verdana, sans-serif; font-size: 13px; border: 1px solid #000; background: #FEA022; text-align: center;"><?php echo $peso . number_format($total_savings - $tenyrs, 2); ?></td>
                            -->
                                </tr>
                            </tbody>
                        </table>
                        </p>
                        <p
                            style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 17px; text-align: justify; font-weight: bold; color: #ff0000">
                            *Based on the average <?php echo $distutility; ?> distribution rate in your area
                            of <?php echo $peso . number_format($durate, 2); ?>/kWh. <?php echo $distutility; ?>
                            kwh rate is subject to change monthly.
                        </p>
                        <p
                            style="font-family: Arial, Verdana, sans-serif; color: #AC6100; font-size: 13px; line-height: 17px; text-align: justify; font-style: italic">
                            Disclaimer: Your solar system's monthly energy production can vary due to factors like weather, shading,
                            and panel efficiency.
                            While we've estimated <?php echo number_format($aveprod); ?> kWh per month, actual output may differ.
                            Keep an eye on seasonal fluctuations and consider regular maintenance for optimal performance.
                            Utility rates and net metering buyback rates can also change on a month-to-month basis, impacting your
                            savings.
                            However, over the course of the panel's 25-year warranty, savings are guaranteed.
                        </p>
                    <?php } ?>
                    <p style="font-weight: bold; font-family: Arial, Verdana, sans-serif; font-size: 14px; line-height: 10px; color: #FF6700; margin-bottom: 0.5em;"> Benefits of Going Solar:</p>
                    <ul class="list" style="margin-top: 0;">
                        <li> Drastically reduces your electric bills.</li>
                        <li> Protects your business against rising energy costs.</li>
                        <li> No worries about unpredictable rate increases.</li>
                        <li> Increases your property value.</li>
                        <li> Earn a great return on your investment.</li>
                        <li> Cools your house's temperature.</li>
                        <li> Very low maintenance. Less hassle.</li>
                        <li> Reduces your carbon footprint.</li>
                        <li> Environmentally friendly.</li>
                    </ul>
                </div>
                <!-- <div class="page_break">
                <p style="font-family: Arial, Verdana, sans-serif; font-size: 21px; line-height: 17px; text-align: center; font-weight: bold; color: #FF6700">SOLAR PANEL ROOF LAYOUT</p>
                <br>
                <?php
                /*
                if (count($pvl) > 0) {
                    $pvwidth = (count($pvl) > 1) ? 95 / 2 : 100;
                    foreach ($pvl as $pvimg) {
                        echo '<p style="font-family: Arial, Verdana, sans-serif; font-size: 21px; line-height: 17px; text-align: center; font-weight: bold; color: #FF6700">';
                        echo '<img src="' . $pvimg . '" data-type="PVL" width="' . $pvwidth . '%"/>';
                        echo '</p>';
                    }
                } else {
                    echo '<p>PV Layout not found.</p>';
                }
                    */
                ?>

            </div> -->

                <div class="page_break">
                    <p
                        style="font-family: Arial, Verdana, sans-serif; font-size: 21px; line-height: 17px; text-align: center; font-weight: bold; color: #FF6700">
                        SOLAR PANEL ROOF LAYOUT</p>
                    <br>
                    <div style="display: flex; flex-wrap: wrap; justify-content: center; align-items: flex-start; gap: 10px;">
                        <?php
                        if (count($pvl) > 0) {
                            $pvwidth = (count($pvl) > 1) ? '48%' : '100%';
                            foreach ($pvl as $pvimg) {
                                echo '<div style="flex: 1 1 48%; max-width: 48%; box-sizing: border-box; display: flex; flex-direction: column; align-items: center; margin-bottom: 10px;">';
                                echo '<img src="' . $pvimg . '" data-type="PVL" style="width: 100%; height: auto; max-width: 100%; border: none; box-shadow: none;" />';
                                echo '</div>';
                            }
                        } else {
                            echo '<div style="width:100%; text-align:center;"><p>PV Layout not found.</p></div>';
                        }
                        ?>
                    </div>
                </div>
                <?php if (count($mpp) > 0) { ?>
                    <div class="page_break" style="display: block">
                        <p
                            style="font-family: Arial, Verdana, sans-serif; font-size: 21px; line-height: 17px; text-align: center; font-weight: bold; color: #FF6700">
                            PROJECTED MONTHLY PRODUCTION</p>
                        <br>
                        <?php
                        $mpwidth = (count($mpp) > 1) ? 95 / 2 : 100;
                        foreach ($mpp as $mpimg) {
                            echo '<p style="font-family: Arial, Verdana, sans-serif; font-size: 21px; line-height: 17px; text-align: center; font-weight: bold; color: #FF6700">';
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
    </body>

    </html>
<?php } else { ?>
    <h1>Distribution Utility and/or Rate not set.</h1>';
    <h3>Kindly set DU and Rate and refresh page.</h3>';
<?php }
