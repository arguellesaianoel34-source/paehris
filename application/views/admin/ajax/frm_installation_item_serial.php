<?php
$id = $this->input->post('ids');
//echo page_construction();
//QUERY FOR ITEM DETAILS INCLUDING APPLICATION DETAILS
$item_qry = $this->db->select('l.appid, i.sysid AS itemid, i.fulldescription AS name, l.qty, u.unit_code, u.unit_name ')
    ->from('installation_item_list as l')
    ->join('items_main_description AS i','l.itemid = i.sysid','left')
    ->join('prime_unit AS u','l.unitid = u.sysid','left')
    ->where(array('l.sysid'=>$id,'l.status'=>1))->get()->row();

$serials = array();
if ($item_qry) {
    $item = $item_qry;

    //lookup saved serial numbers...
    $serial_qry = $this->db->select('serialnumber')
        ->from('application_installation_material_details')
        ->where(array('appid'=>$item->appid,'itemid' => $item->itemid,'status' => 1))
        ->get();

    if ($serial_qry->num_rows() > 0) {

        foreach ($serial_qry->result() as $serial) {
            $serials[] = $serial->serialnumber;
        }

        $serialnumbers = implode(', ', $serials);
    }
}

$serialnumbers = implode(', ', $serials);
?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <?php echo isset($item) ? $item->name : 'N/A' ?> Serial Numbers <i class="fa fa-barcode fa-lg"></i>
                    </div>
                </div>
                <div class="portlet-body">
                    <form id="frm_add_serial" action="<?php echo base_url(); ?>inventory/saveserialnumber" method="post">
                        <input type="hidden" id="itemqty" value="<?php echo isset($item) ? floatval($item->qty) : 0 ?>" >
                        <i class="fa fa-info-circle"></i> Note: The count of serial numbers provided must coincide with the current quantity of this item in the list.
                        <textarea name="serials" id="sn_text" rows="3" class="form-control" wrap="soft" placeholder="Separate each numbers with comma or enter."><?php echo $serialnumbers; ?></textarea>
                        <div class="portlet-footer">
                            <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    INVENTORY.serial(<?php echo isset($item) ? $item->itemid : 0 ?>,<?php echo isset($item) ? $item->appid : 0 ?>);
</script>
