<?php
/*echo "<pre>";
print_r ($this->_ci_cached_vars);
echo "</pre>";*/
?>
<form id="frm_add_reference" action="<?php echo base_url(); ?>inventory/addreference" method="post">
    <input type="hidden" name="trngroupid" value="<?php echo $trnid; ?>">
    <input type="hidden" name="trntype" value="<?php echo $trntype; ?>">
    <input type="hidden" id="refid" name="refid">
    <div class="row margin-bottom-15">
        <div class="col-md-5">
            Select Application Number/Name
            <input class="form-control" name="appnumber" id="inv_reference_install" required>
        </div>
        <div class="col-md-4">
            Date
            <input type="date" class="form-control" name="trndate" id="inv_rr_date" max="<?php echo date('Y-m-d'); ?>" value="" placeholder="Receiving Date..." required />
        </div>
        <div class="col-md-3 align-text-bottom" style="height: 54px">
            <button type="submit" id="btn_add_installation" class="btn btn-primary" style="position: absolute; bottom: 0" disabled><i class="fa fa-download"></i> Add Installation</button>
        </div>
    </div>
    <hr>
    <div class="portlet light bordered">
        <div class="portlet-title">
            <div class="caption bold" style="width: 100%!important;">
                <div class="row">
                    <div class="col-md-9">
                        Customer: <span id="installation_customer_name" class="font-red-flamingo"></span>
                    </div>
                    <div class="col-md-3 pull-right">
                        Number: <span id="installation_customer_number" class="font-green-sharp bold"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="portlet-body">
            <div class="row margin-bottom-15">
                <div class="col-md-4">
                    <b>Address:</b>
                    <span id="installation_customer_address"></span>
                </div>
                <div class="col-md-4">
                    <b>System Build:</b>
                    <span id="installation_customer_build"></span>
                </div>
                <div class="col-md-4">
                    <b>Installation Team:</b>
                    <span id="installation_team"></span>
                    <div class="input-group hidden" id="installation_team_selection">
                        <?php

                        $teams_qry = $this->db->select('sysid,name')
                            ->from('installation_team')
                            ->where('status',1)->get();

                        if ($teams_qry->num_rows() > 0) {
                            echo '<div class="icheck-inline">';
                            foreach ($teams_qry->result() as $team) {
                                echo '<label><input type="checkbox" name="installteam[]" data-checkbox="icheckbox_square-blue" class="icheck" value="'.$team->sysid.'" disabled> '.$team->name.' </label>';
                            }
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>

            </div>
            <div class="row hidden" id="install_template_selection">
                <div class="col-md-2">
                    <span class="bold input-group-text">Use Template:</span>
                </div>
                <div class="col-md-8">
                    <input id="selec2_installation_template" autocomplete="off" class="form-control col-md-8" name="template" placeholder="Please select a template..." disabled required>
                </div>
            </div>

            <div class="tabbable-line hidden" id="template_item_list">
                <ul class="nav nav-tabs ">
                    <li class="active">
                        <a href="#installation_components" data-toggle="tab" aria-expanded="true" data-id="1"> Components </a>
                    </li>
                    <li class="">
                        <a href="#installation_accessories" data-toggle="tab" aria-expanded="true" data-id="2"> Accessories </a>
                    </li>
                    <li class="">
                        <a href="#installation_optional" data-toggle="tab" aria-expanded="true" data-id="3"> Situational </a>
                    </li>
                    <li class="">
                        <a href="#installation_others" data-toggle="tab" aria-expanded="true" data-id="4"> Others </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="installation_components">
                        <table class="table table-condensed table-bordered margin-top-10" style="width: 100%" id="tbl_installation_components">
                            <thead>
                            <th>#</th>
                            <th>Item Description</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade in" id="installation_accessories">
                        <table class="table table-condensed table-bordered margin-top-10" style="width: 100%" id="tbl_installation_accessories">
                            <thead>
                            <th>#</th>
                            <th>Item Description</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade in" id="installation_optional">
                        <table class="table table-condensed table-bordered margin-top-10" style="width: 100%" id="tbl_installation_optional">
                            <thead>
                            <th>#</th>
                            <th>Item Description</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade in" id="installation_others">
                        <table class="table table-condensed table-bordered margin-top-10" style="width: 100%" id="tbl_installation_others">
                            <thead>
                            <th>#</th>
                            <th>Item Description</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript" src="<?php echo file_versioning('assets/pages/inventory/main.js'); ?>"></script>
<script type="text/javascript">
    INVENTORY.references(<?php echo $trntype; ?>);
</script>
