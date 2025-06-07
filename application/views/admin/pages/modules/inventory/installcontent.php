<?php
/*echo "<pre>";
print_r ($this->_ci_cached_vars);
echo "</pre>";*/

$appinfo = application_info($refid);

$trn_qry = $this->db->select('')
    ->from('inventory_transaction_reference')
    ->where('sysid', $trnref)->get()->row();

//COUNT INVERTERS WITH SIZE
$item_qry = $this->db->select('l.appid, i.sysid AS itemid, i.fulldescription AS name, l.qty, u.unit_code, u.unit_name ')
    ->from('installation_item_list as l')
    ->join('items_main_description AS i','l.itemid = i.sysid','left')
    ->join('prime_unit AS u','l.unitid = u.sysid','left')
    ->where(array('l.appid'=>$refid,'l.status'=>1,))
    ->like('i.fulldescription','inverter')
    ->get();

//echo $this->db->last_query();
$inverters = array();
if ($item_qry->num_rows() > 0) {
    foreach ($item_qry->result() as $item) {
        preg_match_all('/(\d+(\.\d+)?)\s*kW/i',$item->name,$sizes);
        foreach ($sizes[0] as $size) {
            $item->size = str_replace(' ','',$size).' Inverter';
        }
        $inverters[] = $item;
    }
}
//GET ASSIGNED TEAM
$teams = 'N/A';
$installation_details = $this->db->select('team')
    ->from('application_installation_dates')
    ->where(array('appid' => $refid))
    ->get()->row();

if ($installation_details && $installation_details->team) {
    $teams = explode(',', $installation_details->team);
    $teams_qry = $this->db->select('name')
        ->from('installation_team')
        ->where_in('sysid', $teams)
        ->get();

    $teamname = array();
    if ($teams_qry->num_rows() > 0) {
        foreach ($teams_qry->result() as $team) {
            $teamname[] = $team->name;
        }

        $teams = implode(' & ', $teamname);
    }
}
?>

<div class="portlet light">
    <div class="portlet-title">
        <div class="caption" style="width: 100%">
            <div class="row margin-bottom-15">
                <div class="col-md-4">
                    <span class="">Date: <span class="bold"> <?php echo date('Y-m-d',strtotime($trndate)); ?></span></span>
                </div>
                <div class="col-md-4">
                    <span class="bold"><?php echo $teams; ?></span>
                </div>
                <div class="col-md-4">
                    <?php if ($trn_qry && $trn_qry->createdby == user_id()) {?>
                        <div class="pull-right">
                            <a id="btn_print_form" class="btn btn-primary inline btn-sm" data-id="<?php echo $trnref; ?>" data-ref="<?php echo $refid; ?>"><i class="fa fa-print" style="font-size: 18px !important;" data-id="<?php echo $trnref; ?>"></i></a>
                            <a id="btn_delete_reference" class="btn btn-danger inline btn-sm"><i class="fa fa-times" style="font-size: 18px !important;" data-id="<?php echo $trnref; ?>"></i></a>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="row margin-bottom-15">
                <div class="col-md-12">
                    <span class="">SYSTEM SIZE:</span>
                    <span class="margin-left-20 bold">
                        <?php if (count($inverters) > 0) {
                            $system = array();
                            foreach ($inverters AS $inverter) {
                                $system[] = floatval($inverter->qty).' x '.$inverter->size;
                            }
                            echo implode(', ',$system);
                        } ?>
                    </span>
                </div>
            </div>
            <div class="row margin-bottom-15">
                <div class="col-md-12">
                    CUSTOMER NAME:
                    <span class="margin-left-20 bold">
                        <?php
                        if ($appinfo->apptype > 1) {
                            echo $appinfo->corpname.($appinfo->corpbranch ? '('.$appinfo->corpbranch.')' : '');
                        } else {
                            echo $appinfo->appname;
                        }
                        ?>
                    </span>
                </div>
            </div>
            <div class="row margin-bottom-15">
                <div class="col-md-12">
                    CUSTOMER ADDRESS: <span class="margin-left-20 bold"><?php echo $appinfo->address; ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="portlet-body">
        <?php if ($trn_qry && $trn_qry->createdby == user_id()) {?>
            <div class="row">
                <div class="col-md-12">
                    <form id="<?php echo $tableid; ?>_add_spsitem" action="<?php echo base_url()?>inventory/addinstallationitem" method="post" data-tbl="<?php echo $tableid; ?>">
                        <input type="hidden" name="itemid" id="itemid" required>
                        <table class="table table-condensed table-bordered table-responsive" id="tbl_add_components">
                            <thead>
                            <th style="width: 40% !important;">Select Item</th>
                            <th style="width: 15% !important;">Qty</th>
                            <th style="width: 15% !important;">Additional Qty</th>
                            <th style="width: 15% !important;">Unit</th>
                            <th style="width: 15% !important;">Type</th>
                            <!--<th style="width: 15% !important;">Price</th>
                            <th style="width: 15% !important;">Total</th>-->
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <input class="form-control input-sm " id="input_newitem" name="newitem" placeholder="Select item..." required>
                                </td>
                                <td class="number">
                                    <input class="form-control input-sm " id="input_new_item_qty" name="itemqty" placeholder="Qty..." required>
                                </td>
                                <td class="number">
                                    <input class="form-control input-sm " id="input_add_item_qty" name="addqty" placeholder="Additional Qty...">
                                </td>
                                <td>
                                    <input class="form-control input-sm " name="itemunit" id="input_new_item_unit" required>
                                </td>
                                <td class="number">
                                    <select class="form-control input-sm " name="itemtype" id="input_new_item_type" required>
                                        <option></option>
                                        <option value="1">Components</option>
                                        <option value="2">Accessories</option>
                                        <option value="3">Situational Materials</option>
                                        <option value="4">Others</option>
                                    </select>
                                </td>
                            </tr>

                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="5">
                                    <div class="btn-group" id="item_controls">
                                    </div>
                                    <button type="reset" href="javascript:;" class="btn btn-sm btn-danger pull-left" id="btn_reset_new_item"><i class="fa fa-rotate-left"></i> Reset </button>
                                    <button type="submit" class="btn btn-sm btn-primary pull-right" id="btn_save_new_item"><i class="fa fa-plus"></i> Add </button>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
        <?php } ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="panel-title bold">Components</h3>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-primary pull-right inline btn-sm btn_refresh_items_table" data-table="<?php echo $tableid; ?>_1"><i class="fa fa-refresh"></i> Refresh </button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-condensed" id="<?php echo $tableid; ?>_1" data-id="<?php echo $refid; ?>" data-type="<?php echo $trntype; ?>" data-trn="<?php echo $trn_qry->groupid; ?>" data-itemtype="1" style="width: 100% !important;">
                            <thead>
                            <th>#</th>
                            <th>Item Description</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Serial Number</th>
                            <th>Additional Qty</th>
                            <th>Utilized Qty</th>
                            <th>Returned Qty</th>
                            <th>Remarks</th>
                            <?php if ($trn_qry && $trn_qry->createdby == user_id()) {?>
                            <th>Controls</th>
                            <?php } ?>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="panel-title bold">Accessories</h3>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-primary pull-right inline btn-sm btn_refresh_items_table" data-table="<?php echo $tableid; ?>_2"><i class="fa fa-refresh"></i> Refresh </button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-condensed" id="<?php echo $tableid; ?>_2" data-id="<?php echo $refid; ?>" data-type="<?php echo $trntype; ?>" data-trn="<?php echo $trn_qry->groupid; ?>" data-itemtype="2" style="width: 100% !important;">
                            <thead>
                            <th>#</th>
                            <th>Item Description</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Serial Number</th>
                            <th>Additional Qty</th>
                            <th>Utilized Qty</th>
                            <th>Returned Qty</th>
                            <th>Remarks</th>
                            <?php if ($trn_qry && $trn_qry->createdby == user_id()) {?>
                                <th>Controls</th>
                            <?php } ?>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel panel-warning hidden">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="panel-title bold">Situational Materials</h3>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-primary pull-right inline btn-sm btn_refresh_items_table" data-table="<?php echo $tableid; ?>_3"><i class="fa fa-refresh"></i> Refresh </button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-condensed" id="<?php echo $tableid; ?>_3" data-id="<?php echo $refid; ?>" data-type="<?php echo $trntype; ?>" data-trn="<?php echo $trn_qry->groupid; ?>" data-itemtype="3" style="width: 100% !important;">
                            <thead>
                            <th>#</th>
                            <th>Item Description</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Serial Number</th>
                            <th>Additional Qty</th>
                            <th>Utilized Qty</th>
                            <th>Returned Qty</th>
                            <th>Remarks</th>
                            <?php if ($trn_qry && $trn_qry->createdby == user_id()) {?>
                                <th>Controls</th>
                            <?php } ?>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel panel-danger hidden">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="panel-title bold">Others</h3>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-primary pull-right inline btn-sm btn_refresh_items_table" data-table="<?php echo $tableid; ?>_4"><i class="fa fa-refresh"></i> Refresh </button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered table-condensed" id="<?php echo $tableid; ?>_4" data-id="<?php echo $refid; ?>" data-type="<?php echo $trntype; ?>" data-trn="<?php echo $trn_qry->groupid; ?>" data-itemtype="4" style="width: 100% !important;">
                            <thead>
                            <th>#</th>
                            <th>Item Description</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Serial Number</th>
                            <th>Additional Qty</th>
                            <th>Utilized Qty</th>
                            <th>Returned Qty</th>
                            <th>Remarks</th>
                            <?php if ($trn_qry && $trn_qry->createdby == user_id()) {?>
                                <th>Controls</th>
                            <?php } ?>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    INVENTORY.itemForm('<?php echo $tableid; ?>',<?php echo $refid; ?>);
    $('#btn_print_form',document)
</script>
