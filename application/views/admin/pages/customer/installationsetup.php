<?php
$info = application_info($dataid);
?>
<div class="portlet grey box" id="recom_setup">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-gears"></i>
            <span class="caption-subject font-red-flamingo bold uppercase">Installation Setup</span>
        </div>
        <div class="btn-group pull-right" style="margin-top: 5px;">
            <!--Load Buttons if published-->
            <a href="javascript:" id="btn_reload_setup" class="btn btn-primary btn-sm inline"><i class="fa fa-refresh"></i> Refresh</a>
            <!--Load Preview if not published on save remove all icons-->
            <!--<a href="javascript:" id="btn_setup_preview" class="btn btn-primary btn-sm inline preview"><i class="fa fa-search"></i> Preview</a>-->
            <a href="javascript:" id="btn_setup_delete" class="btn btn-danger btn-sm inline"><i class="fa fa-trash"></i> Delete</a>
        </div>
    </div>
    <div class="portlet-body">
        <form id="frm_sps_setup" action="<?php echo base_url();?>inspection/createspssetup" method="post">
            <input type="hidden" name="appid" value="<?php echo $dataid; ?>">
            <div class="form-group row">
                <div class="col-md-4">
                    <label class="control-label font-red-flamingo bold uppercase">Panel Type</label> <span class="required"></span>
                    <input class="form-control" id="select2_paneltypes" name="sptypeid" placeholder="Panel Type..." required>
                </div>
                <div class="col-md-4">
                    <label class="control-label font-red-flamingo bold uppercase">Number of Panels</label> <span class="required"></span>
                    <input type="number" class="form-control" id="input_nop" name="nop" placeholder="Number of panels..." required>
                </div>
                <div class="col-md-4">
                    <label class="control-label font-red-flamingo bold uppercase">Number of Strings</label> <span class="required"></span>
                    <input type="number" class="form-control" id="input_nos" name="nos" placeholder="Number of strings..." required>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-5">
                    <!--<label class="control-label font-red-flamingo bold uppercase">Number of Panels per String</label>
                    <input class="form-control" id="input_panelsperstring" name="panelsperstring" placeholder="Panels per string...">-->
                    <label class="control-label font-red-flamingo bold uppercase" for="input_panelsperstring">Panels per String</label> <span class="required"></span>
                    <div class="input-icon right">
                        <input class="form-control input-reset" id="input_panelsperstring" placeholder="Panels per string..." name="panelsperstring" required />
                        <i class="fa fa-search"></i>
                    </div>
                </div>

                <div class="col-md-5">
                    <label class="control-label font-red-flamingo bold uppercase" for="input_invertersize">Inverter Size(s)</label> <span class="required"></span>
                    <div class="input-icon right">
                        <input class="form-control input-reset" id="input_invertersize" name="invertersize" placeholder="Inverter Size..." required>
                        <i class="fa fa-search"></i>
                    </div>
                </div>
                <?php if ($info->q && $info->apptype != 1) { ?>
                <div class="col-md-2">
                    <label class="control-label font-red-flamingo bold uppercase" for="input_invertersize"></label>
                    <button type="submit" class="btn btn-primary margin-top-20" id="btn_save_system_setup"><i class="fa fa-save"></i> Save</button>
                </div>
                <?php } ?>
            </div>
        </form>
        <hr>
        <div id="template_search_result"></div>

        <div class="tabbable-line">
            <ul class="nav nav-tabs ">
                <li class="active">
                    <a href="#sps_components" data-toggle="tab" aria-expanded="true" data-id="1"> Components </a>
                </li>
                <li class="">
                    <a href="#sps_accessories" data-toggle="tab" aria-expanded="true" data-id="2"> Accessories </a>
                </li>
                <li class="">
                    <a href="#sps_consumables" data-toggle="tab" aria-expanded="true" data-id="3"> Consumables </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade in active" id="sps_items">
                    <form id="frm_add_spsitem" action="<?php echo base_url()?>inspection/addspsitem" method="post">
                        <table class="table table-striped table-condensed table-bordered table-responsive" id="tbl_add_components">
                            <thead>
                            <th style="width: 50% !important;">Select Item</th>
                            <th style="width: 10% !important;">Qty</th>
                            <th style="width: 10% !important;">Unit</th>
                            <!--<th style="width: 15% !important;">Price</th>
                            <th style="width: 15% !important;">Total</th>-->
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <input class="form-control input-sm " id="select2_newitem" name="newitem" placeholder="Select item...">
                                </td>
                                <td class="number">
                                    <input class="form-control input-sm " id="input_new_item_qty" name="itemqty" placeholder="Qty...">
                                </td>
                                <td>
                                    <input type="hidden" name="itemtype" id="input_new_item_type">
                                    <input type="hidden" name="itemunit" id="input_new_item_unit">
                                    <span id="item_unit"></span>
                                </td>
                                <!--<td class="number">
                                    <input class="form-control input-sm " id="input_new_item_price" placeholder="Price...">
                                </td>
                                <td class="number">
                                    <span id="item_total">0.00</span>
                                </td>-->
                            </tr>

                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="5">
                                    <div class="btn-group" id="item_controls">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary pull-left" id="btn_save_new_item"><i class="fa fa-plus"></i> Add </button>
                                    <button type="reset" href="javascript:;" class="btn btn-sm btn-danger pull-right" id="btn_reset_new_item"><i class="fa fa-rotate-left"></i> Reset </button>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </form>
                    <hr>
                    <table class="table table-striped table-hover table-bordered table-condensed types" id="tbl_sps_components" width="100%">
                        <thead>
                        <th>#</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <!--<th>Price</th>
                        <th>Total</th>-->
                        <th><i class="fa fa-check-square-o"></i></th>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--<div class="portlet-footer">
            <div class="row">
                <div class="col-md-12">
                    <ul class="list-group summary column list-group-lg">
                        <li class="list-group-item">
                            <span class="col-md-6 label-name" style="padding: 10px 20px !important;">Total 10-Year Plan </span>
                            <span class="col-md-6 font-red-flamingo bold number" id="total_10years" style="padding: 10px 20px !important;"></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-6 label-name" style="padding: 10px 20px !important;">Total 5-Year Plan </span>
                            <span class="col-md-6 font-red-flamingo bold number" id="total_5years" style="padding: 10px 20px !important;"></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>-->
    </div>
</div>