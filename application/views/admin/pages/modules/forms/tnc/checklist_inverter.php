
<div class="tab-pane fade in " id="form_checklist_inverter">
    <div class="well">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-4">
                    Date Conducted
                    <input type="date" class="form-control" name="trntype" id="inv_trn_type" value="<?php echo date('Y-m-d'); ?>" placeholder="Customer name/number" required />
                </div>
                <div class="col-md-4">
                    Document Number : 1
                </div>
            </div>
        </div>
    </div>
    <div class="portlet light ">
        <div class="portlet-title">
            <div class="caption bold">INVERTER</div>
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
                    <td>Uses tox w/ screw in mounting of inverters and panel boards.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                <tr>
                    <td>Inverters are installed in accordance with the manufacturer’s guidelines.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                <tr>
                    <td>The area around the inverter has enough ventilation.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                <tr>
                    <td>The inverter’s incoming and outgoing cables are properly labelled and tagged.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                <tr>
                    <td>Do not use the top of the enclosure for pipe entry.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                <tr>
                    <td>Check for the alignment of the devices.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                <tr>
                    <td>Always maintain the required distance between devices.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                <tr>
                    <td>The devices are properly labeled.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                <tr>
                    <td>Racking installed in accordance with construction plans.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                <tr>
                    <td>Inverter installed at correct location.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                <tr>
                    <td>Proper fittings are installed and tightened.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                <tr>
                    <td>Thermal expansion joints installed and bounded per project specs.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                <tr>
                    <td>All steel has been rust-protected.</td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td class="text-align-center"><input type="checkbox" class="icheck"></td>
                    <td><input class="form-control"></td>
                </tr>
                <tr>
                    <td class="note row">
                        <div class="col-md-12">
                        <h4 class="bold">Note:</h4>
                        <label style="float: left">Inverter Location: </label>
                        <div style="overflow: hidden">
                            <input class="form-control inline" style="border-bottom: 1.5px grey solid !important; display: table-cell">
                        </div>
                        </div>
                        <div class="col-md-12">
                            Serial Number of each Inverters:
                            <textarea id="invertersn" class="form-control" rows="5"></textarea>
                        </div>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>