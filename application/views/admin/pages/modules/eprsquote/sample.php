<?php
?>
<style type="text/css">
    .comment-content {
        border-radius: 5px 15px 15px 15px !important;
        padding: 5px;
        min-width: 200px;
        max-width: 400px;
        min-height: 30px;
        /*line-height: 25px;*/
    }

    .comment-you {
        padding-left: 25px !important;
    }

    .comment-you .comment-content {
        background-color: rgba(10, 182, 255, 0.5);
    }

    .comment-them .comment-content {
        background-color: rgba(117, 114, 114, 0.5);
    }

    .comment-content p {
        margin: 0px !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="fa fa-quote-left font-green-haze"></i>
                    <span class="caption-subject bold font-green-haze uppercase"> PRF Quotations </span>
                    <span class="caption-helper"></span>
                </div>
            </div>
            <div class="portlet-body ">
                <div class="well">
                    <div class="row">
                        <div class="col-md-4">
                            <ul class="list-group summary column no-border">
                                <li class="list-group-item">
                                    <span class="label-name col-md-4 bold">PRF #</span>
                                    <span class="col-md-8 font-blue-steel ">PRF230109001</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <ul class="list-group summary column no-border">
                                <li class="list-group-item">
                                    <span class="label-name col-md-4 bold">Requested by</span>
                                    <span class="col-md-8 font-blue-steel ">Mark Sejob</span>
                                </li>
                                <li class="list-group-item">
                                    <span class="label-name col-md-4 bold">Justification</span>
                                    <span class="col-md-8 font-blue-steel ">Replenishment of consumable items</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <ul class="list-group summary column no-border">
                                <li class="list-group-item">
                                    <span class="label-name col-md-4 bold">Request Date</span>
                                    <span class="col-md-8 font-blue-steel ">Jan. 9, 2023</span>
                                </li>
                            </ul>
                            <div class="btn-group">

                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-striped" id="tbl_po_items">
                    <thead>
                    <tr>
                        <th width="25px">#</th>
                        <th width="30%">Item</th>
                        <th width="100px">Last Price</th>
                        <th width="80px">Qty</th>
                        <th width="50px">Unit</th>
                        <th width="150px">RJLP</th>
                        <th width="150px">Wine Guard</th>
                        <th width="150px">Benson</th>
                        <th width="30px" class="center"><button class="btn btn-primary btn-round-xs"><i class="fa fa-plus"></i> </button> </th>
                        <th width="100px">Total</th>
                        <th width="20%">Remarks</th>
                        <th width="100px">Comment</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>1</td>
                        <td>Eye Terminal 5.5-5</td>
                        <td class="number"><a href="javascript:"><i class="fa fa-history pull-left"></i> 5.50</a></td>
                        <td class="number">448</td>
                        <td>Pc(s)</td>
                        <td class="number">-</td>
                        <td class="number">12.00</td>
                        <td class="number bg-yellow-lemon"><i class="fa fa-check text-success pull-left"></i> 6.00</td>
                        <td></td>
                        <td class="number">2,688.00</td>
                        <td></td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Tox No.6</td>
                        <td class="number">-</td>
                        <td class="number">1,404</td>
                        <td>Pc(s)</td>
                        <td class="number">-</td>
                        <td class="number bg-yellow-lemon"><i class="fa fa-check text-success pull-left"></i> 1.00</td>
                        <td class="number">2.00</td>
                        <td></td>
                        <td class="number">1,404.00</td>
                        <td></td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>1 1/4" Gypsum screw, Wood</td>
                        <td class="number">-</td>
                        <td class="number">1,404</td>
                        <td>Pc(s)</td>
                        <td class="number">-</td>
                        <td class="number bg-yellow-lemon"><i class="fa fa-check text-success pull-left"></i> 1.50</td>
                        <td class="number">-</td>
                        <td></td>
                        <td class="number">2,106.00</td>
                        <td></td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>1 1/4" Gypsum screw, Metal</td>
                        <td class="number">-</td>
                        <td class="number">600</td>
                        <td>Pc(s)</td>
                        <td class="number">-</td>
                        <td class="number bg-yellow-lemon"><i class="fa fa-check text-success pull-left"></i> 1.50</td>
                        <td class="number">-</td>
                        <td></td>
                        <td class="number">900.00</td>
                        <td></td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Metal Clamp 1/2, single hole</td>
                        <td class="number">-</td>
                        <td class="number">813</td>
                        <td>Pc(s)</td>
                        <td class="number">-</td>
                        <td class="number">15.00</td>
                        <td class="number bg-yellow-lemon"><i class="fa fa-check text-success pull-left"></i> 6.00</td>
                        <td></td>
                        <td class="number">4,878.00</td>
                        <td></td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>Stainless Steel Bolt, Nut 3/16 x ½ & Flat Washer 3/16 x ½</td>
                        <td class="number">-</td>
                        <td class="number">410</td>
                        <td>Set(s)</td>
                        <td class="number bg-yellow-lemon"><i class="fa fa-check text-success pull-left"></i> 8.00</td>
                        <td class="number">-</td>
                        <td class="number">-</td>
                        <td></td>
                        <td class="number">3,280.00</td>
                        <td></td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>3.5 mm2 THHN Copper Wire (Blue) (3.5kW)</td>
                        <td class="number">-</td>
                        <td class="number">4</td>
                        <td>Box(s)</td>
                        <td class="number">-</td>
                        <td class="number bg-yellow-lemon"><i class="fa fa-check text-success pull-left"></i> 4,851.00</td>
                        <td class="number">4,855.00</td>
                        <td></td>
                        <td class="number">19,404.00</td>
                        <td>Brand: Unicon</td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div class="row">
                    <div class="col-md-3">
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    Summary of Cost
                                </div>
                            </div>
                            <div class="portlet-body">
                                <table class="table table-bordered table-striped" id="tbl_po_items">
                                    <thead>
                                    <th>Supplier</th>
                                    <th>Total</th>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>RJLP</td>
                                        <td class="number">3,280.00</td>
                                    </tr>
                                    <tr>
                                        <td>Wine Guard</td>
                                        <td class="number">23,814.00</td>
                                    </tr>
                                    <tr>
                                        <td>Benson</td>
                                        <td class="number">7,566.00</td>
                                    </tr>
                                    <tr>
                                        <td class="bold">Grand Total</td>
                                        <td class="number bold">34,660.00</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        Notes/Remarks:
                        <div class="form-group">
                            <textarea class="form-control" rows="2" placeholder="Notes/Remarks..."></textarea>
                        </div>
                        <div class="btn-group pull-right">
                            <button class="btn btn-primary "><i class="fa fa-check"></i> Submit</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/pages/eprs/new.js"></script>
<script>
    /**
     * LOGIC: load the budgets approved so please refer to these tables `bos_capex_sp` and `bos_opex`. You will use the `transaction_id` if you want to
     * refer to the whole transaction or the `budget_data_id` if you want to refer to a budget only and the `job_order_id` if you want to refer to the permanent
     * job order of the budget.
     * Difference from the BOS new:
     * 1. There's no add budget.
     * 2. all available budgets and items were there but they're uneditable for CAPEX.
     * 3. An ability to add items inside a budget when using SP.
     * 4. An ability to add items inside an OPEX budget.
     * 5. An ability to delete newly added budgets.
     * Procedure:
     * 1. Load the approved budgets based on the chosen budget-type.
     */
    $('#tbl_po_items', document).DataTable({
        bPaginate: false,
        bSort: false
    });
</script>
