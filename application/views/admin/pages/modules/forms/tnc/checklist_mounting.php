<?php
echo "<pre>";
print_r ($items);
echo "</pre>";

?>
<div class="tab-pane fade in active" id="form_checklist_mounting">
    <div class="well">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-4">
                    Date Conducted
                    <input type="date" class="form-control" name="trntype" id="inv_trn_type" value="<?php echo date('Y-m-d'); ?>" placeholder="Customer name/number" required />
                </div>
            </div>
        </div>
    </div>
    <?php
    $i = 1;
    foreach ($items AS $item) {
        $values = $item['values'];
        if (isset($item['children']))
    ?>
        <div class="portlet light ">
            <div class="portlet-title">
                <div class="caption bold"><?php echo $i.'. '.$values['item']; ?></div>
            </div>
            <div class="portlet-body">
                <table class="table table-bordered table-condensed components">
                    <thead>
                    <tr>
                        <th rowspan="2" width="50%"></th>
                        <th colspan="2" class="text-align-center">COMPLIED</th>
                        <th rowspan="2" width="20%" class="text-align-center">REMARKS</th>
                    </tr>
                    <tr>
                        <th class="text-align-center">MLTRS / PAEI Representative</th>
                        <th class="text-align-center">SM Representative</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php ?>

                    <?php ?>

                    <tr>
                        <td>Ensure the complete accessories of L foot like bolt, nut, and washer etc.</td>
                        <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                        <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                        <td><input class="form-control"></td>
                    </tr>
                    <tr>
                        <td>Ensure the L footing is facing the right direction for mounting bars.</td>
                        <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                        <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                        <td><input class="form-control"></td>
                    </tr>
                    <tr>
                        <td>Ensure the equal level of L-footings before tightening the nuts.</td>
                        <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                        <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                        <td><input class="form-control"></td>
                    </tr>
                    <tr>
                        <td>Ensure tightness of all fittings</td>
                        <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                        <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                        <td><input class="form-control"></td>
                    </tr>
                    <tr>
                        <td>
                            <label style="float: left">Number of Hanger Bolts / L footings =</label>
                            <div style="overflow: hidden">
                                <input class="form-control inline" style="border-bottom: 1.5px grey solid !important; display: table-cell">
                            </div>
                        </td>
                        <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                        <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                        <td><input class="form-control"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?
    }
    ?>

    <div class="portlet light ">
        <div class="portlet-title">
            <div class="caption bold">2. Mounting Bars</div>
        </div>
        <div class="portlet-body">
            <table class="table table-bordered table-condensed components">
                <thead>
                <tr>
                    <th rowspan="2" width="50%"></th>
                    <th colspan="2" class="text-align-center">COMPLIED</th>
                    <th rowspan="2" width="20%" class="text-align-center">REMARKS</th>
                </tr>
                <tr>
                    <th class="text-align-center">MLTRS / PAEI Representative</th>
                    <th class="text-align-center">SM Representative</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td> Ensure the length of mounting bars for solar panels.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                <tr>
                    <td> Use rail joiner for mounting bars coupling.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                <tr>
                    <td>Clear any sharp edges of mounting bars that may damage the solar panels.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                <tr>
                    <td>Ensure equal level of mounting bars.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                <tr>
                    <td>Ensure tightness of bolts.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                <tr>
                    <td>
                        <label style="float: left">Number of Mounting Bars =</label>
                        <div style="overflow: hidden">
                            <input class="form-control inline" style="border-bottom: 1.5px grey solid !important; display: table-cell">
                        </div>
                    </td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="portlet light ">
        <div class="portlet-title">
            <div class="caption bold">3. Sealant</div>
        </div>
        <div class="portlet-body">
            <table class="table table-bordered table-condensed components">
                <thead>
                <tr>
                    <th rowspan="2" width="50%"></th>
                    <th colspan="2" class="text-align-center">COMPLIED</th>
                    <th rowspan="2" width="20%" class="text-align-center">REMARKS</th>
                </tr>
                <tr>
                    <th class="text-align-center">MLTRS / PAEI Representative</th>
                    <th class="text-align-center">SM Representative</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Apply sealant for each bolt hole before or after installing the bolt.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                <tr>
                    <td>Make sure the wrong holes are sealed with sealant.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="portlet light ">
        <div class="portlet-title">
            <div class="caption bold">4. Torque</div>
        </div>
        <div class="portlet-body">
            <table class="table table-bordered table-condensed components">
                <thead>
                <tr>
                    <th rowspan="2" width="50%"></th>
                    <th colspan="2" class="text-align-center">COMPLIED</th>
                    <th rowspan="2" width="20%" class="text-align-center">REMARKS</th>
                </tr>
                <tr>
                    <th class="text-align-center">MLTRS / PAEI Representative</th>
                    <th class="text-align-center">SM Representative</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>L bracket assembly installed with a torque 16 – 20 Nm.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                <tr>
                    <td>Rail Joint plate installed with a torque 10 – 12 Nm.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                <tr>
                    <td>
                        <label style="float: left">Distance between rails per PV module =</label>
                        <div style="overflow: hidden">
                            <input class="form-control inline" style="border-bottom: 1.5px grey solid !important; display: table-cell">
                        </div>
                    </td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>