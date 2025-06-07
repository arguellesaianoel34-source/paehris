<?php
$pae_letter_head = FCPATH . 'assets/global/img/pae_po_head.png';
/*echo "<pre>";
print_r ($this->_ci_cached_vars);
echo "</pre>";*/
$rfop_cnt = 0;

$css = $this->load->view('custom/templates/templatecss',false,true);

$supplier_details = $this->db->select('s.name, s.tin, sa.address, sod.name AS accountname, sod.bank, sod.accountnum, qs.exvat, qs.rfop, qs.exrate, qs.shipping, s.currency, qs.paytype, s.type')
    ->from('eprs_suppliers_main AS s')
    ->join('eprs_quotation_suppliers AS qs','s.sysid = qs.supplierid','left')
    ->join('eprs_suppliers_address AS sa','s.sysid = sa.supplierid','left')
    ->join('eprs_suppliers_online_details AS sod','s.sysid = sod.supplierid AND sod.status = 1','left')
    ->where(array('qs.sysid' => $ponumber,'s.status' => 1))
    ->get()->row();

//echo $this->db->last_query();

$po_details = $this->db->select('po.sysid as poid,po.ponumber,qd.sysid,qd.paytype,qd.payterm,qd.purpose,qd.notes')
    ->from('eprs_po_details as qd')
    ->join('eprs_po as po','qd.poid = po.sysid','left')
    ->where(array('qd.quotationid' => $ponumber,'qd.status' => 1))
    ->get()->row();

//echo $this->db->last_query();

$approved_qt = $this->db->select('i.fulldescription as name,eti.qty,eti.unitid,eqd.amount,eqd.status,qr.remarks')
    ->from('eprs_quotation_details AS eqd')
    ->join('eprs_transaction_items AS eti','eqd.prfitemid = eti.sysid','left')
    ->join('items_main_description AS i','eti.itemid = i.sysid','left')
    ->join('eprs_quotation_remarks AS qr','eti.sysid = qr.prfitemid AND qr.status = 1','left')
    ->where(array('eqd.quotationid' => $ponumber,'eqd.status ' => 301))
    ->get();

//echo $this->db->last_query();
if ($po_details) {
    $po_count = $this->db->select('COUNT(po.sysid) as cnt')
        ->from('eprs_po_details as po')
        ->where(array('po.sysid <=' => $po_details->sysid, 'po.status' => 1))
        ->get()->row();

    if (!$supplier_details->rfop) {
        $rfop_cnt = $this->db->select('COUNT(pod.sysid) as cnt')
            ->from('eprs_po_details as pod')
            ->join('eprs_po AS po','po.sysid = pod.poid')
            ->where(array('pod.sysid <=' => $po_details->sysid, 'pod.status' => 1, 'po.ponumber' => $po_details->ponumber))
            ->get()->row();
    }

    if (!$po_details->paytype && $supplier_details->paytype) {
        $po_details->paytype = $supplier_details->paytype;
    }
}

$amount = 0;
$items = array();
if ($approved_qt->num_rows() > 0) {
    foreach ($approved_qt->result() AS $qt) {
        $items[] = $qt;
        $amount += $qt->amount * $qt->qty;
    }
}

if ($po_details && $po_details->paytype != 1 && $supplier_details->currency == 83) {
    if ($supplier_details->exvat == 1) {
        $netvat = $amount;
        $vat = round($amount * 0.12, 2);
        $gross = $amount + $vat;
    } else {
        $vat = round($amount * 12 / 112, 2);
        $netvat = $amount - $vat;
        $gross = $amount;
    }
    $ewtrate = 0.01;
    $ewtxt = '1%';
    if ($supplier_details->type == 4002) {
        $ewtrate = 0.02;
        $ewtxt = '2%';
    }
    $ewt = round($netvat * $ewtrate, 2);
} else {
    //$po_count = (object)array('cnt' => 'TBA');
    $netvat = $amount;
    $gross = 0;
    $vat = 0;
    $ewt = 0;
    //$ponumber = 'TBA';
    //$rfop_cnt = 0;
}
//$buffer = round($gross*0.02,2);
$shipping = ($supplier_details) ? $supplier_details->shipping : 0;
$total = $gross - $ewt + $shipping;
$suppliertotal = ($po_details && $po_details->paytype != 1 && $supplier_details->currency == 83) ? $gross - $ewt : $amount;
$currency = ($supplier_details) ? get_currency($supplier_details->currency) : array();
$currency_u = (strlen($currency->name) - 1 == strrpos($currency->name,'s')) ? mb_substr($currency->name,0,strlen($currency->name) - 1) : $currency->name;
$rfop_cnt = ($rfop_cnt) ? $rfop_cnt : (($supplier_details && $supplier_details->rfop > 0) ? $supplier_details->rfop : 0);

//GET PRF
$prf_qry = $this->db->select('dateupdated,status')
    ->from('eprs_quotation_suppliers')
    ->where('sysid',$ponumber)
    ->get()->row();

?>
<html>
<head>
    <title></title>
    <style>

        html {
            -webkit-print-color-adjust:exact !important;
            print-color-adjust:exact !important;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
        }

        body * {
            font-size: 10px;
        }

        @page {
            margin: 24px;
        }
        body { margin: 24px; }

        header {
            position: fixed;
            top: 50px;
            height: 70px;
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

        footer {
            position: fixed;
            bottom: 0px;
            height: 50px;
            background-color: transparent;
            color: white;
            text-align: center;
            line-height: 35px;
        }

        main {
            margin-top: 30px;
        }

        hr {
            height: 0;
        }

        .page_break {
            break-inside: avoid !important;
            display: block;
            page-break-before: auto; !important;
            page-break-inside: avoid !important;
        }

        @media print {
            .page_break {
                break-inside: avoid !important;
                display: block;
            }
        }

        .title {
            width: 100%;
            font-weight: bold;
            display: block;
        }

        .ponum {
            display: table-cell;
            padding-left: 50%;
            width: 50%;
            height: 42px;
            vertical-align: bottom;
            text-align: right;
        }
        .ponumber {
            display: inline-block;
            width: 60%;
            border-bottom: 1px solid black;
            text-align: center;
        }

        .blue {
            color: #0070C0;
        }
        .bg-blue {
            background-color: #3333FF;
        }
        .bg-grey {
            background-color: #C0C0C0;
        }
        .bg-white {
            background-color: white;
        }
        .white {
            color: white;
        }

        .bold {
            font-weight: bold;
        }
        .center {
            text-align: center;
        }
        .number {
            text-align: right;
        }
        .border-dotted {
            border: 1px dotted black;
        }
        .border-double-line {
            border: double black;
            box-sizing: border-box;
        }
        table {
            border-spacing: 0;
            width: 100%;
        }
        table thead th {
            border: double black;
            font-weight: bold;
            text-align: center;
            background-color: #999999;
            vertical-align: middle;
            color: white;
            height: 26px;
            font-size: 12px;
        }

        table thead td {
            border: double black;
            font-weight: bold;
            text-align: center;
            background-color: #999999;
            vertical-align: middle;
            color: white;
            height: 26px;
        }

        table tbody tr.double td {
            border: double black;
            /*padding: 3px;*/
        }

        table tbody tr.dotted td:first-child {
            border: dotted 0.5px black;
        }

        table tbody tr.dotted td {
            border-right: dotted 0.5px black;
            border-top: dotted 0.5px black;
            border-bottom: dotted 0.5px black;
            padding: 3px;
            font-size: 8px;
        }

        table tbody tr {
            border: 0px;
        }

        table tbody td {
            font-size: 10px;
        }

        table.double-border {
            border-spacing: 1px !important;
        }

        table.double-border, .double-border th, .double-border td {
            border: 1px solid black !important;
        }

        .amt_details td {
            padding: 3px !important;
        }

        .content-center {
            display: table-cell !important;
            float: unset !important;
            align-content: center !important;
            vertical-align: middle !important;
        }

        .break {
            height: 3px;
            display: block;
        }

        .sign_area {
            width: 80%;
            margin-left: 10%;
            border-bottom: double black;
            margin-top: 20px;
            text-align: center;
        }

        table.condensed td {
            height: 12px !important;
        }
    </style>
    <?php echo $css; ?>
</head>
<body>
    <div style="text-align: center;">
        <img src="<?php echo $pae_letter_head; ?>" style="width: 100%;"/>
    </div>
    <div class="title center" style="font-size: 9pt; margin-top: 13px">Panay Alternative Energy Inc.</div>
    <div class="title center" style="font-size: 11pt">REQUEST FOR PO/ ONLINE PAYMENT</div>
    <div class="row">
        <div class="col-md-4 col-md-offset-8" style="font-size: 7pt; width: 45%; float: right">PO/RFOP No.: <?php echo (isset($po_count)) ? $po_count->cnt : 'TBA' ?> <div class="ponumber blue bold"><?php echo ($rfop_cnt) ? 'PAE-'.str_pad($po_details->ponumber,8,'0',STR_PAD_LEFT).'-'.str_pad($rfop_cnt->cnt,3,'0',STR_PAD_LEFT) : 'TBA'; ?></div></div>
    </div>
    <div class="row">
        <div class="col-md-12 bg-blue bold white center middle" style="font-size: 9pt; margin-top: 5px; height: 13px; padding: 3px 0px">REQUESTOR</div>
    </div>
    <table class="double-border" cellspacing="2" style=" width: 100%;">
        <tr>
            <td width="50%">
                <div class="col-md-12 bold">TIN :</div>
                <div class="col-md-12 center" style="height: 20px"><i><?php echo ($supplier_details) ? $supplier_details->tin : 'N/A'; ?></i></div>
            </td>
            <td>
                <div class="col-md-12 bold ">Date Requested :</div>
                <div class="col-md-12 center" style="height: 20px"><i><?php echo ($prf_qry && $prf_qry->status == 301) ? date('F d, Y',strtotime($prf_qry->dateupdated)) : date('F d, Y'); ?></i></div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="col-md-12 bold">Payee :</div>
                <div class="col-md-12 center" style="height: 20px">
                    <?php echo ($supplier_details) ?
                        (
                            ($po_details && $po_details->paytype != 1)
                                ? $supplier_details->name
                                : 'Marcelo M. Cacho'
                        )
                        : 'N/A';
                    ?>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="col-md-12 bold">Address :</div>
                <div class="col-md-12 center" style="height: 20px; text-align: center">
                    <?php echo ($supplier_details) ?
                        (
                        ($po_details && $po_details->paytype != 1)
                            ? $supplier_details->address
                            : 'Empress Trucking Warehouse, Coastal Road, Balabago, Jaro, Iloilo City'
                        )
                        : 'N/A';
                    ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="col-md-12 bold">Amount in Words :</div>
                <div class="col-md-12 center" style="height: 30px; font-size: 10px">**<?php echo strtoupper(amountInWords($suppliertotal,$currency_u)); ?>***</div>
            </td>
            <td>
                <div class="col-md-12 bold">GROSS AMOUNT :</div>
                <div class="col-md-12 center" style="height: 30px; font-size: 12px"><i><?php echo number_format($suppliertotal,2)?></i></div>
            </td>
        </tr>
    </table>
    <table width="100%" border="collapsed" class="items" style="border-spacing: 0 !important; width: 100% !important;">
        <thead>
        <tr>
            <th style="width: 3.67%">SN#</th>
            <th style="width: 33.33%">Item Description</th>
            <th style="width: 10%">Unit Price</th>
            <th style="width: 8%">Qty</th>
            <th style="width: 8%">Unit</th>
            <?php if ($supplier_details->currency != 83) { ?>
                <th style="width: 12%">Total (<?php echo $currency->code; ?>)</th>
                <th style="width: 12%">Total (PHP)</th>
            <?php } else { ?>
                <th style="width: 12%">Total</th>
            <?php } ?>
            <th style="width: 25%">Remarks</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $itemcount = count($items);
        $extendrow = ($itemcount < 5) ?  5-$itemcount : 0;

        $count = 0;
        foreach ($items as $item) {
            $itemAmount = $items[$count]->amount * $items[$count]->qty;
            $conAmount = $itemAmount * ceil($supplier_details->exrate);
            $currSymbol = $currency->symbol;
            ?>
            <tr class="dotted">
                <td class="center"><?php echo $count+1;?></td>
                <td style="font-size: 33.33vw"><?php echo utf8_decode($items[$count]->name);?></td>
                <td class="number" style="font-size: 10vw"><?php echo number_format($items[$count]->amount,2);?></td>
                <td class="number" style="font-size: 8vw"><?php echo $items[$count]->qty;?></td>
                <td style="font-size: 8vw"><?php echo unit_query($items[$count]->unitid)->code;?></td>
                <?php if ($supplier_details->currency != 83) { ?>
                    <td class="number" style="font-size: 12vw"><?php echo '<span style="float: left !important;">'.$currSymbol.'</span>'.number_format($itemAmount,2);?></td>
                    <td class="number" style="font-size: 12vw"><?php echo number_format($conAmount,2);?></td>
                <?php } else { ?>
                    <td class="number" style="font-size: 12vw"><?php echo number_format($itemAmount,2);?></td>
                <?php } ?>
                <td style="font-size: 25vw"><?php echo $items[$count]->remarks;?></td>
            </tr>
            <?php
            $count++;
        }

        if ($extendrow > 0) {
            for ($extend = 0; $extend < $extendrow; $extend++) {
                ?>
                <tr class="dotted">
                    <td class="center"><?php echo $count+1;?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <?php if ($supplier_details->currency != 83) { ?>
                        <td></td>
                        <td></td>
                    <?php } else { ?>
                        <td></td>
                    <?php } ?>
                </tr>
                <?php
                $count++;
            }
        }
        ?>

        <tr class="dotted">
            <td colspan="5" class="bold" style="font-size: 63vw">GRAND TOTAL :</td>
            <?php if ($supplier_details->currency != 83) {
                $conAmount = $amount * ceil($supplier_details->exrate);
                $currSymbol = $currency->symbol;
            ?>
                <td class="number bold" style="font-size: 12vw"><?php echo '<span style="float: left !important;">'.$currSymbol.'</span>'.number_format($amount,2);?></td>
                <td class="number" style="font-size: 12vw"><?php echo number_format($conAmount,2);?></td>
            <?php } else { ?>
                <td class="number" style="font-size: 12vw"><?php echo number_format($amount,2);?></td>
            <?php } ?>
            <td class="center" style="font-size: 25vw">All prices are VAT <?php echo ($supplier_details->exvat == 1) ? 'EX' : 'INC'; ?></td>
        </tr>
        </tbody>
    </table>
    <table style="width: 100%; border: 0px">
        <tr>
            <td class="bold border-dotted content-center" style="height: 26px; width: 25%; border-right: 0px !important;"> Terms of Payment :</td>
            <td class="border-dotted content-center" style="height: 26px;  border-left: 0px !important;"><?php echo ($po_details) ? get_acct_type($po_details->paytype) : 'N/A'; ?></td>
            <td colspan="2" class="center border-dotted content-center" style="height: 26px"><?php echo ($po_details && $po_details->paytype != 1) ? (($supplier_details) ? $supplier_details->accountname : 'N/A') : ''; ?></td>
        </tr>
        <tr>
            <td colspan="2" class="center border-dotted content-center" style="height: 26px;"><?php echo ($po_details && $po_details->paytype != 1) ? (($po_details) ? $po_details->payterm : 'N/A'):''; ?></td>
            <td colspan="2" class="border-dotted content-center" style="height: 26px">Bank :   <?php echo ($po_details && $po_details->paytype != 1) ? (($supplier_details) ? $supplier_details->bank : 'N/A'):''; ?>  Account No. :  <?php echo ($po_details && $po_details->paytype != 1) ? (($supplier_details) ? $supplier_details->accountnum : 'N/A'):''; ?></td>
        </tr>
        <tr>
            <td colspan="4">
                <div class="bg-grey" style="font-size: 3pt">.</div>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="border-dotted">
                <div class="col-100 bold" style="height: 17px">Payment Description :</div>
                <div class="col-100 center" style="height: 33px"><?php echo ($po_details) ? $po_details->purpose . (($po_details->paytype == 1) ? '<br>('.$supplier_details->name.')' : '') : 'N/A'; ?></div>
            </td>
        </tr>
    </table>
    <div class="bg-grey page_break amt_details" style="border: solid 2px black; padding: 5px 30px">
        <table class="bg-white" style="width: 100%">
            <tr class="double">
                <td rowspan="3" width="50%" style="padding-top: 0px">
                    <div class="page_break" style="height: 5px"></div>
                    <div class="center sign_area" style="margin-top: 20px !important;"> </div>
                    <div class="center">Supplier Conforme</div>
                </td>
                <td class="center" width="25%" >Total Vat Ex :</td>
                <td class="center bold" width="25%"><?php echo ($supplier_details->currency != 83 ? $currency->symbol.' ' : '').number_format($netvat,2);?></td>
            </tr>
            <?php if ($supplier_details->currency != 83) { ?>
                <tr class="double">
                    <td class="center bold">Sub-Total :</td>
                    <td class="center bold"><?php echo $currency->symbol.' '.number_format($netvat,2);?></td>
                </tr>
                <tr class="double">
                    <td class="center">est.Shipping Fee :</td>
                    <td class="center"><?php echo number_format($shipping,2);?></td>
                </tr>
            <?php } else { ?>
                <tr class="double">
                    <td class="center">est.Shipping Fee :</td>
                    <td class="center"><?php echo number_format($shipping,2);?></td>
                </tr>
                <tr class="double">
                    <td class="center bold">Sub-Total :</td>
                    <td class="center bold"><?php echo number_format($netvat + $shipping,2);?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="break page_break"></div>
    <div class="bg-grey page_break" style="border: solid 2px black; padding: 5px 30px;">
        <table class="bg-white condensed amt_details" style="width: 100%; font-size: 12px;">
            <tr class="double">
                <?php
                $rowspan = 2;
                if ($po_details && $po_details->paytype != 1) {
                    $rowspan = 5;
                }

                if ($supplier_details->currency != 83) {
                    $rowspan = 3;
                }
                ?>
                <td rowspan="<?php echo $rowspan; ?>" width="37.5%">
                    <div class="col-100 center">For Accounting Reference</div>
                </td>
                <td class="center" width="37.5%">NET OF VAT</td>
                <td class="center" width="25%"><?php echo ($supplier_details->currency != 83 ? $currency->symbol.' ' : '').number_format($netvat,2);?></td>
            </tr>
            <?php if ($po_details && $po_details->paytype != 1 && $supplier_details->currency ==  83) { ?>
            <tr class="double">
                <td class="center">VAT (12%)</td>
                <td class="center"><?php echo number_format($vat,2);?></td>
            </tr>
            <tr class="double">
                <td class="center bold">TOTAL VAT INC</td>
                <td class="center bold"><?php echo number_format($gross,2);?></td>
            </tr>
            <tr class="double">
                <td class="center">Less EWT <span style="color: red"><?php echo $ewtxt; ?></span></td>
                <td class="center"><?php echo number_format($ewt,2);?></td>
            </tr>
            <?php } ?>
            <?php if ($supplier_details->currency !=  83) {
                $conTotal = $suppliertotal * ceil($supplier_details->exrate);
            ?>
                <tr class="double" style="background-color: #FFFF00">
                    <td class="center bold">Amt to be received by Supplier</td>
                    <td class="center bold"><?php echo $currency->symbol.' '.number_format($suppliertotal,2);?></td>
                </tr>
                <tr class="double">
                    <td class="center bold">Peso Conversion (<?php echo ceil($supplier_details->exrate) ?> PHP)</td>
                    <td class="center bold"><?php echo number_format($conTotal,2);?></td>
                </tr>
            <?php } else { ?>
                <tr class="double" style="background-color: #FFFF00">
                    <td class="center bold">Amt to be received by Supplier</td>
                    <td class="center bold"><?php echo number_format($suppliertotal,2);?></td>
                </tr>
            <?php } ?>

        </table>
    </div>
    <div class="break page_break"></div>
    <div class="bg-grey page_break" style="border: solid 2px black; padding: 5px 30px; font-size: 11px">
        <div class="bg-white border-double-line center" style="height: 65px;">
            <div class="bold">Note</div>
            <div class="col-100" style="color: red"><?php echo ($po_details) ? nl2br($po_details->notes) : 'N/A'; ?></div>
        </div>
    </div>
    <div class="break page_break"></div>
    <p class="page_break">
    <?php
    /*if (($po_details && $po_details->paytype != 1) || $itemcount > 8) {
        $signstyle = 'width: 80%; height: auto; position: absolute; margin-top: -35px;';
    } else {
        $signstyle = 'width: 25%; height: auto; position: absolute; margin-top: -35px;';
    }*/
    ?>
        <div class="bg-grey page_break" style="border: solid 2px black; padding: 5px 30px;">
            <table class="bg-white condensed amt_details" style="width: 100%; font-size: 12px;">
                <tr class="double">
                    <td>
                        <div class="bold">Verified by</div>
                        <div class="col-100 sign_area ">
                            <img class="signature" data-id="213" src="" style="<?php //echo $signstyle; ?>" />
                            APRIL ROSE I. ALMODIENTE
                        </div>
                        <div class="col-100 center" style="padding-top: 5px">IAH</div>
                    </td>
                    <td>
                        <div class="bold">Recommending Approval</div>
                        <div class="col-100 sign_area ">
                            <img class="signature" data-id="214" src="" style="<?php //echo $signstyle; ?>" />
                            MARCELO U. CACHO
                        </div>
                        <div class="col-100 center" style="padding-top: 5px">GM</div>
                    </td>
                    <td>
                        <div class="bold">Approved by</div>
                        <div class="col-100 sign_area ">
                            <img class="signature" data-id="215" src="" style="<?php //echo $signstyle; ?>" />
                            LUIS MIGUEL A. CACHO
                        </div>
                        <div class="col-100 center" style="padding-top: 5px">PCEO</div>
                    </td>
                </tr>
            </table>
        </div>
    </p>
    <div class="break page_break"></div>
</main>

</body>
</html>