<?php
$peso = '<span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span>';
$customer_plan_details = $this->db->select()
    ->from('customer_plan_details')
    ->where(array('appid' => $id,'status !=' => 0))
    ->get()->row();

//echo $this->db->last_query();

if ($customer_plan_details) {
    if ($customer_plan_details->standard) {
        $plan_qry = $this->db->select()
            ->from('customer_standard_system_rates')
            ->where(array('sysid' => $customer_plan_details->rateid))
            ->get()->row();

        //echo $this->db->last_query();
    } else {
        $plan_qry = $this->db->select()
            ->from('customer_nonstandard_system_rates')
            ->where(array('appid' => $id,'status' => 1))
            ->get()->row();
    }

    $plan = $plan_qry;
    if ($plan && $plan->years != 0) {
        $wifi = $customer_plan_details->wifiaccess ? 'Yes' : 'No';
        $monthly = 0;

        $pae_letter_head = FCPATH . 'assets/global/img/logo/peco-logo-login.png';
        $tick = iconv('UTF-8', 'Windows-1250', '&#10003;');
        $system_parts_qry = $this->db->select('csp.*,imd.fulldescription,u.unit_code as unit')
            ->from('customer_system_parts AS csp')
            ->join('items_main_description as imd', 'csp.itemid = imd.sysid and imd.status = 1', 'left')
            ->join('prime_unit as u', 'csp.unitid = u.sysid', 'left')
            ->where('imd.fulldescription REGEXP \'Inverter|Panel\'')
            ->where(array('appid' => $id, 'csp.status !=' => 0))->get();

        $panels = '';
        $inverter = array();
        if ($system_parts_qry->num_rows() > 0) {
            foreach ($system_parts_qry->result() AS $parts) {
                if (strpos($parts->fulldescription, 'Panel')) {
                    $panels = $parts->qty;
                }

                if (strpos($parts->fulldescription, 'Inverter')) {
                    $inverter[] = $parts->qty . 'x ' . $parts->fulldescription;
                }
            }
        }

        $appsize_qry = $this->db->select()
            ->from('application_customers_system_size')
            ->where(array('appid' => $id, 'status' => 305))
            ->get()->row();

        if ($appsize_qry) {
            $size = $appsize_qry;
            $ratesize = (($size->rateclass == 1) ? 'Single' : $size->rateclass) . ' Phase';
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

                .page_break {
                    page-break-before: always;
                    margin-top: 120px;
                }

                body {
                    font-family: Arial, Verdana, sans-serif;
                }

                span {
                    font-size: 11pt;
                }

                span.data {
                    font-weight: bold;
                    color: #2E74B5;
                }

                span.lead {
                    width: 20%;
                }

                p {
                    width: 100%;
                    margin-top: 0px;
                    margin-bottom: 5px;
                }

                li:before {
                    font-family: DejaVu Sans;
                    content: '\2714';
                    margin-right: .100em;
                }

                ul {
                    padding-left: 20px;
                    text-indent: 2px;
                    list-style: none;
                    list-style-position: outside;
                }
            </style>
        </head>
        <body>
        <p style="text-align: center; margin-bottom: -20px; margin-top: -20px;">
            <img src="<?php echo $pae_letter_head; ?>">
        </p>
        <p style="text-align: center; margin-top: -15px; font-size: 20pt">
            <u>CREDIT CHECK APPROVAL FORM</u>
        </p>
        <br>
        <p>
            <span class="lead">Application No.:</span>
            <span class="data"><?php echo 'PAE' . str_pad($app->essrno, 5, "0", STR_PAD_LEFT); ?></span>
        </p>
        <p>
            <span class="lead">Name of Applicant:</span>
            <span class="data"><?php echo ($app->apptype <= 1) ? $app->appname : $app->corpname.(isset($app->corpbranch) && $app->corpbranch != '' ? ' - '.$app->corpbranch : ''); ?></span>
        </p>
        <p>
            <span class="lead">Address:</span>
            <span class="data"><?php echo $app->address; ?></span>
        </p>
        <p>
            <span class="lead">Contact Number:</span>
            <span
                class="data"><?php echo ($app->mobile && ($app->mobile != 'N/A' || $app->mobile != '00000000000')) ? $app->mobile : $app->phone; ?></span>
        </p>
        <p>
            <span class="lead">Email Address:</span>
            <span class="data"><?php echo $app->email; ?></span>
        </p>
        <p>
            <span class="lead">System Size:</span>
            <span class="data"><?php echo $app->systemsizename; ?></span>
        </p>
        <p>
            <span class="lead">No. of Panels:</span>
            <span class="data"><?php echo $panels; ?></span>
        </p>
        <p>
            <span class="lead">Inverter:</span>
            <span class="data"><?php echo join(' + ', $inverter) . ' &#8211; ' . $ratesize; ?></span>
        </p>
        <p>
            <span class="lead">Wifi Access:</span>
            <span class="data"><?php echo $wifi; ?></span>
        </p>
        <p>
            <span class="lead">Installment Plan:</span>
            <span class="data"><?php echo $plan->years.' Years'; ?></span>
        </p>
        <p>
            <span class="lead">Monthly Amortization:</span>
            <span class="data"><?php echo $peso.number_format($plan->monthlyamt,2); ?></span>
        </p>
        <p>
            <span class="lead">Documents Submitted:</span>
        </p>
        <ul>
            <?php
            if ($app->reqres && count($app->reqres) > 0) {
                foreach ($app->reqres AS $req) {
                    echo '<li>'.get_requirement_name($req->reqid)->names.'</li>';
                }
            } else {
                echo '<li>PLEASE UPLOAD REQUIREMENTS WITH CORRESPONDING FILE NAME</li>';
            }
            ?>
        </ul>
        <p>
            <span style="font-weight: bold">Remarks: </span>
            <span style="font-style: italic"><?php echo $customer_plan_details->remarks; ?></span>
        </p>
        <br>
        <p>
            <i>Approved by:</i>
        </p>
        <br>
        <br>
        <br>
        <br>
        <p style="font-family: Arial, Verdana, sans-serif; font-size: 13px; line-height: 15px;">
            <span style="display: inline-block; text-align: center; width:25%; font-weight: bold; z-index: -1">MARCELO U. CACHO</span>
            <img class="signature" src="">
            <br>
            <span style="display: inline-block; width:25%; text-align: center; border-top: 1px solid #000; z-index: -1; padding-top: 5px">General Manager</span>
        </p>
        <?php
        $docs_qry = $this->db->select('
                prp.sysid AS PRPSYSID, 
                prp.codes AS PRPCODES, 
                prp.names AS PRPNAMES, 
                prp.desc AS PRPDESC, 
                cr.comply AS COMPLY,
                cr.sysid AS CRSYSID,
                ca.fileurl AS URL
                ')
            ->from('application_customers_requirements AS cr')
            ->join('prime_requirement_parameters AS prp', 'prp.sysid = cr.reqid','left')
            ->join('application_customers_attachments AS ca', 'cr.sysid = ca.attachmentid AND ca.status = 1','left')
            ->where(array('cr.appid' => $id, 'cr.status' => 1))
            ->order_by('PRPCODES ASC,URL ASC')
            ->get();
        if ($docs_qry->num_rows() > 0) {
            foreach ($docs_qry->result() as $doc) {
                echo '<p style="text-align: center"><img src="'.FCPATH.$doc->URL.'" style="width: 80%; height: auto"></p>';
            }
        }
        ?>
        </body>
        </html>
        <?php
    } else {
        echo '<h1>Outright Payment!!!</h1>';
        echo '<h4>Credit check information is not required.</h4>';
    }
} else {
    echo '<h1>Cannot find Customer Plan Details.</h1>';
    echo '<h4>Kindly set it before refreshing document preview.</h4>';
}