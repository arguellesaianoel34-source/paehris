<?php
$pae_letter_head = FCPATH . 'assets/global/img/pae_po_head.png';
$css = $this->load->view('custom/templates/templatecss',false,true);

$appinfo = application_info($refid);

$trn_qry = $this->db->select('')
    ->from('inventory_transaction_reference')
    ->where('sysid', $trnref)->get()->row();

//COUNT INVERTERS WITH SIZE
/*$item_qry = $this->db->select('l.appid, i.sysid AS itemid, i.fulldescription AS name, l.qty, u.unit_code, u.unit_name ')
    ->from('installation_item_list as l')
    ->join('items_main_description AS i','l.itemid = i.sysid','left')
    ->join('prime_unit AS u','l.unitid = u.sysid','left')
    ->where(array('l.appid'=>$refid,'l.status'=>1,))
    ->like('i.fulldescription','inverter')
    ->get();*/

$install_list = $this->db->select('i.fulldescription as name,list.qty,list.unitid,list.sysid as referenceitemid,i.sysid as itemid,list.itemtype')
    ->from('installation_item_list AS list')
    ->join('items_main_description AS i','list.itemid = i.sysid','left')
    ->where(array('list.appid' => $refid,'list.status' => 1))
    ->order_by('list.itemtype ASC,list.sysid ASC')
    ->get();

//echo $this->db->last_query();

$list = array();
$inverters = array();

if ($install_list->num_rows() > 0) {
    foreach ($install_list->result() AS $item) {
        $inventory_qry = $this->db->select('
            iti.sysid,
            MAX(CASE WHEN iti.type = 22 THEN iti.qty END) AS qty,
            MAX(CASE WHEN iti.type = 21 THEN iti.qty END) AS returned,
            MAX(CASE WHEN iti.type = 25 THEN iti.qty END) AS additional,
            GROUP_CONCAT(iti.remarks SEPARATOR ";") as remarks
            ')
            ->from('inventory_transaction_items AS iti')
            ->join('inventory_transaction_reference AS itr','iti.referenceid = itr.sysid','left')
            ->join('inventory_transaction_group AS itg','itg.sysid = itr.groupid','left')
            ->where(array('itr.referenceid' => $refid,'iti.itemid' => $item->itemid,'iti.referenceitemid' => $item->referenceitemid,'itg.sysid' => $trn_qry->groupid))
            ->where_in('iti.status',array(1,300))
            ->group_by('iti.referenceitemid')
            ->get()->row();

        //echo $this->db->last_query().'<hr>';

        $utilized = $item->qty + ($inventory_qry ? $inventory_qry->additional - $inventory_qry->returned : 0);
        $serial = '';
        if (preg_match('(solar panel|inverter|battery)', strtolower($item->name))) {
            $serial_qry = $this->db->select('serialnumber')
                ->from('application_installation_material_details')
                ->where(array('appid'=>$refid,'itemid' => $item->itemid,'status' => 1))
                ->get();

            if ($serial_qry->num_rows() > 0) {
                $serials = array();
                foreach ($serial_qry->result() AS $serial_item) {
                    $serials[] = $serial_item->serialnumber;
                }
                $serial .= implode(', ',$serials);
            }

            if (mb_strpos(strtolower($item->name), 'inverter') !== false) {
                preg_match_all('/(\d+(\.\d+)?)\s*kW/i',$item->name,$sizes);
                foreach ($sizes[0] as $size) {
                    $inverters[] = array(
                        'size' => str_replace(' ','',$size).' Inverter',
                        'qty' => $item->qty
                    );
                }
            }

            if (mb_strpos(strtolower($item->name), 'solar panel') !== false) {
                preg_match_all('/(\d+(\.\d+)?)\s*W/i',$item->name,$sizes);
                $item->name = '<b>Solar Panel '.implode(' ',$sizes[0]).'</b>';
            }
        }

        /*if ($utilized > 0) {*/
            $list[] = array(
                'desc' => $item->name,
                'unit' => unit_query($item->unitid)->code,
                'qty' => preg_replace("/\.?0+$/", "", $item->qty),
                'serial' => $serial,
                'additional' => ($inventory_qry && $inventory_qry->additional > 0) ? preg_replace("/\.?0+$/", "", $inventory_qry->additional) : '',
                'utilized' => $utilized,
                'returned' => ($inventory_qry && $inventory_qry->returned > 0) ? preg_replace("/\.?0+$/", "", $inventory_qry->returned) : '',
                'remarks' => ($inventory_qry ? $inventory_qry->remarks : ''),
                'type' => $item->itemtype,
            );
        /*}*/
    }
}
?>

<html>
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif !important;
            font-size: 11px;
        }
        @page {
            border: 5px solid black;
        }
        html {
            margin: 48px;
        }

        header {
            position: fixed;
            top: 0px;
            height: 60px;
            background-color: transparent;
            color: white;
            text-align: center;
            line-height: 35px;
        }

        main {
            margin-top: 80px;
        }

        .center {
            text-align: center;
        }

        table {
            border-spacing: 0;
            width: 100%;
        }
        table thead th {
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            height: 26px;
            font-size: 12px;
            border: 1px solid black;
        }

        table thead td {
            border: solid black 1px !important;
            font-weight: bold;
            text-align: center;
            background-color: #999999;
            vertical-align: middle;
            color: white;
            height: 26px;
        }

        table td {
            border: solid black 1px !important;
            padding-left: 3px;
        }

        .bold {
            font-weight: bold;
        }

        .number {
            text-align: right;
            padding-right: 3px;
        }

        .row {
            display: table;
        }

        .row:after {
            clear: both; }

        .col-25, .col-75, .col-50, .col-100 {
            position: relative;
            min-height: 1px;
            padding-left: 15px;
            padding-right: 15px;
            float: left;
        }

        .col-25 {
            width: 25%;
        }

        .col-50 {
            width: 50%;
        }

        .col-75 {
            width: 75%;
        }

        .col-100 {
            width: 100%;
        }
    </style>
    <?php //echo $css; ?>
    <link href="<?php echo FCPATH; ?>assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<table style="border-spacing: 0 !important; width: 100% !important;">
    <tr>
        <td colspan="7" style="border: 0px !important;">
            <div style="text-align: center; height: 50px;">
                <img src="<?php echo $pae_letter_head; ?>" width="100%" height="100%" />
            </div>
            <div class="center bold">MATERIALS MONITORING FORM</div>
        </td>
    </tr>
    <tr>
        <td>DATE: </td>
        <td colspan="6" class="center">
            <span style="display: inline-block; width: 50%; line-height: 5px;">2024-11-24</span>
            <span style="display: inline-block; width: 50%; line-height: 5px;">Team A</span>
        </td>
    </tr>
    <tr>
        <td>SYSTEM SIZE </td>
        <td colspan="6" class="center">
            <?php if (count($inverters) > 0) {
                $system = array();
                foreach ($inverters AS $inverter) {
                    $system[] = floatval($inverter['qty']).' x '.$inverter['size'];
                }
                echo implode(', ',$system);
            } ?>
        </td>
    </tr>
    <tr>
        <td>CUSTOMER NAME </td>
        <td colspan="6" class="center">
            <?php
            if ($appinfo->apptype > 1) {
                echo $appinfo->corpname.($appinfo->corpbranch ? '('.$appinfo->corpbranch.')' : '');
            } else {
                echo $appinfo->appname;
            }
            ?>
        </td>
    </tr>
    <tr>
        <td>LOCATION </td>
        <td colspan="6" class="center"><?php echo $appinfo->address; ?></td>
    </tr>
    <thead>
    <tr>
        <th rowspan="2" style="width: 30%">MATERIAS DESCRIPTION</th>
        <th colspan="3">QUANTITY WITHDRAWN</th>
        <th rowspan="2">ADDITIONAL<br>QTY</th>
        <th rowspan="2">QTY<br>UTILIZED</th>
        <th rowspan="2">QTY<br>RETURNED</th>
    </tr>
    <tr>
        <th>QTY</th>
        <th>UNIT</th>
        <th>SERIAL NUMBER</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if (count($list) > 0) {
        $itemtype = 1;
        foreach ($list AS $item) {
            $item = (object)$item;
            if ($item->type > $itemtype){
                $types = array('','','ACCESSORIES','SITUATIONAL MATERIALS','OTHERS');
                echo '<tr><td colspan="" class="bold"><br></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
                echo '<tr><td class="bold">'.$types[$item->type].'</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
            }
            if (mb_strpos(strtolower($item->desc),'inverter') !== false) {
                echo '<tr><td class="bold">INVERTER</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
            }
            echo '<tr>';
            echo '<td>'.$item->desc.'</td>';
            echo '<td class="center">'.$item->qty.'</td>';
            echo '<td>'.$item->unit.'</td>';
            echo '<td>'.$item->serial.'</td>';
            echo '<td class="center">'.$item->additional.'</td>';
            echo '<td class="center">'.$item->utilized.'</td>';
            echo '<td class="center">'.$item->returned.'</td>';
            echo '</tr>';
            $itemtype = $item->type;
        }
    }
    ?>
    </tbody>
</table>
<br>
<br>
<br>
<br>
<br>
<div style="display: table; width: 105%;">
    <div style="width: 35%; display: table-cell;">
        <p style="width: 80%; height: 50px; border-bottom: 1px solid black;">
            Requested by:
        </p>
        <p style="font-size: 11px; margin-top: -10px">Install Team</p>
        <p style="width: 80%; height: 50px; border-bottom: 1px solid black;">
            Approved by:
        </p>
        <p style="font-size: 11px; margin-top: -10px"><br></p>
        <p style="width: 80%; height: 50px; border-bottom: 1px solid black;">
            Released by:
        </p>
        <p style="font-size: 11px; margin-top: -10px">Warehouse Custodian</p>
    </div>
    <div style="width: 35%; display: table-cell; vertical-align: bottom">
        <p style="width: 80%; height: 50px; border-bottom: 1px solid black;">
            Guard on Duty:
        </p>
        <p style="font-size: 11px; margin-top: -10px"><br></p>
    </div>
    <div style="width: 15%; display: table-cell; float: right;">
        <p style="width: 80%; height: 50px; border-bottom: 1px solid black;">
            Reported by:
        </p>
        <p style="font-size: 11px; margin-top: -10px">Team Leader</p>
        <p style="width: 80%; height: 50px;">

        </p>
        <p style="font-size: 11px; margin-top: -10px; vertical-align: bottom"><br></p>
        <p style="width: 80%; height: 50px; border-bottom: 1px solid black;">
            Noted by:
        </p>
        <p style="font-size: 11px; margin-top: -10px; vertical-align: bottom"><br></p>
    </div>
    <div style="width: 15%; display: table-cell; float: right;">
        <p style="width: 80%; height: 50px; border-bottom: 1px solid black;">
            Received by:
        </p>
        <p style="font-size: 11px; margin-top: -10px">Team Leader</p>
        <p style="width: 80%; height: 50px;">

        </p>
        <p style="font-size: 11px; margin-top: -10px; vertical-align: bottom"><br></p>
        <p style="width: 80%; height: 50px; border-bottom: 1px solid black;">
            Guard on Duty:
        </p>
        <p style="font-size: 11px; margin-top: -10px; vertical-align: bottom"><br></p>
    </div>
</div>
</body>
</html>
