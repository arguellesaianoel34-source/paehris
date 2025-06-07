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
        /*padding-left: 25px !important;*/
        float: right !important;
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
                    <i class="fa fa-check font-green-haze"></i>
                    <span class="caption-subject bold font-green-haze uppercase"> Purchase Request Approval </span>
                    <span class="caption-helper">more details..</span>
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
                                <button class="btn btn-primary"><i class="fa fa-check"></i> Approve</button>
                                <button class="btn btn-danger"><i class="fa fa-times"></i> Disapprove</button>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-hover table-striped" id="tbl_po_items">
                    <thead>
                    <th width="25px">#</th>
                    <th>Item</th>
                    <th width="80px">Qty</th>
                    <th width="50px">Unit</th>
                    <th width="30%">Remarks</th>
                    <th width="100px">Controls</th>
                    </thead>
                    <tbody>
                    <tr>
                        <td>1</td>
                        <td>Eye Terminal 5.5-5</td>
                        <td class="number">448</td>
                        <td>Pc(s)</td>
                        <td></td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                                <button class="btn btn-danger inline"><i class="fa fa-times"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr class="bg-info">
                        <td colspan="6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="col-md-12">
                                        <div class="comment-you">
                                            <span class="bold">You</span>
                                            <div class="comment-content">
                                                <p>The quick old brown fox jumps over the lazy dog beside the river bank.</p>
                                                <span class="font-xs bold">2023-01-10 10:51</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="comment-them">
                                            <span class="bold">Mark Sejob</span>
                                            <div class="comment-content">
                                                <p>Their comment/reply here....</p>
                                                <span class="font-xs bold">2023-01-10 10:51</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--<div class="col-md-7 col-md-offset-5">
                            <div class="mt-element-ribbon bg-blue-madison-opacity" style="margin-bottom: 10px;">
                                <div class="ribbon ribbon-shadow ribbon-color-success uppercase" style="padding: 0em 0.5em !important;">
                                    <i class="fa fa-user text-primary fa-fw"></i> You
                                </div>
                                <p class="ribbon-content " style="padding-top: 8px !important; padding-bottom: 5px !important;">
                                    Your comment here...
                                </p>
                                <p class="ribbon-content small font-red-flamingo" style="padding-top: 0px !important; padding-bottom: 5px !important;">
                                    2023-01-10 10:51
                                </p>
                            </div>
                            </div>
                            <br>
                            <div class="col-md-7">
                            <div class="mt-element-ribbon bg-grey-salsa" style="margin-bottom: 10px">
                                <div class="ribbon ribbon-shadow ribbon-color-danger uppercase" style="padding: 0em 0.5em !important;">
                                    <i class="fa fa-user text-primary fa-fw"></i> Mark Sejob
                                </div>
                                <p class="ribbon-content " style="padding-top: 8px !important; padding-bottom: 5px !important;">
                                    His comment here...
                                </p>
                                <p class="ribbon-content small font-red-flamingo" style="padding-top: 0px !important; padding-bottom: 5px !important;">
                                    2023-01-10 11:15
                                </p>
                            </div>
                            </div>
                            <br>
                            <div class="col-md-7">
                            <div class="mt-element-ribbon bg-grey-cararra-opacity" style="margin-bottom: 10px">
                                <div class="ribbon ribbon-shadow ribbon-color-danger uppercase" style="padding: 0em 0.5em !important;">
                                    <i class="fa fa-user text-primary fa-fw"></i> Mark Sejob
                                </div>
                                <p class="ribbon-content " style="padding-top: 8px !important; padding-bottom: 5px !important;">
                                    His comment here too...
                                </p>
                                <p class="ribbon-content small font-red-flamingo" style="padding-top: 0px !important; padding-bottom: 5px !important;">
                                    2023-01-10 11:54
                                </p>
                            </div>
                            </div>-->
                        </td>                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Tox No.6</td>
                        <td class="number">1,404</td>
                        <td>Pc(s)</td>
                        <td></td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                                <button class="btn btn-danger inline"><i class="fa fa-times"></i></button>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>1 1/4" Gypsum screw, Wood</td>
                        <td class="number">1,404</td>
                        <td>Pc(s)</td>
                        <td></td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                                <button class="btn btn-danger inline"><i class="fa fa-times"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>1 1/4" Gypsum screw, Metal</td>
                        <td class="number">600</td>
                        <td>Pc(s)</td>
                        <td></td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                                <button class="btn btn-danger inline"><i class="fa fa-times"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Tox No.8 w/ 1 1/2" Metal screw</td>
                        <td class="number">500</td>
                        <td>Pc(s)</td>
                        <td></td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                                <button class="btn btn-danger inline"><i class="fa fa-times"></i></button>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>Stainless Steel Bolt, Nut 3/16 & Flat Washer 3/16</td>
                        <td class="number">410</td>
                        <td>Pc(s)</td>
                        <td></td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                                <button class="btn btn-danger inline"><i class="fa fa-times"></i></button>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>Plastic cable tie,6" Black, Heavy Duty</td>
                        <td class="number">364</td>
                        <td>Pc(s)</td>
                        <td></td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                                <button class="btn btn-danger inline"><i class="fa fa-times"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>Sealant, All purpose</td>
                        <td class="number">23</td>
                        <td>Tube(s)</td>
                        <td></td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                                <button class="btn btn-danger inline"><i class="fa fa-times"></i></button>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>Metal Clamp 1/2, single hole</td>
                        <td class="number">813</td>
                        <td>Pc(s)</td>
                        <td></td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                                <button class="btn btn-danger inline"><i class="fa fa-times"></i></button>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td>3.5 mm2 THHN Copper Wire (Blue) (3.5kW)</td>
                        <td class="number">680</td>
                        <td>Pc(s)</td>
                        <td>4 Boxes @ 150m/box</td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                                <button class="btn btn-danger inline"><i class="fa fa-times"></i></button>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td>11</td>
                        <td>3.5 mm2 THHN Copper Wire (Red) (3.5kW)</td>
                        <td class="number">606</td>
                        <td>Pc(s)</td>
                        <td>4 Boxes @ 150m/box</td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                                <button class="btn btn-danger inline"><i class="fa fa-times"></i></button>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td>12</td>
                        <td>3.5 mm2 THHN Copper Wire (Green) (3.5kW & 5.5kW)</td>
                        <td class="number">499</td>
                        <td>Pc(s)</td>
                        <td>2 Boxes @ 150m/box</td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                                <button class="btn btn-danger inline"><i class="fa fa-times"></i></button>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td>13</td>
                        <td>5.5 mm2 THHN Copper Wire (Blue) (5.5kW)</td>
                        <td class="number">637</td>
                        <td>Pc(s)</td>
                        <td>4 Boxes @ 150m/box</td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                                <button class="btn btn-danger inline"><i class="fa fa-times"></i></button>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td>14</td>
                        <td>5.5 mm2 THHN Copper Wire (Red) (5.5kW)</td>
                        <td class="number">638</td>
                        <td>Pc(s)</td>
                        <td>4 Boxes @ 150m/box</td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                                <button class="btn btn-danger inline"><i class="fa fa-times"></i></button>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td>15</td>
                        <td>Speaker Wire No. 18</td>
                        <td class="number">280</td>
                        <td>Pc(s)</td>
                        <td>2 Spool @ 150m/spool</td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <button class="btn btn-primary inline"><i class="fa fa-comment"></i></button>
                                <button class="btn btn-danger inline"><i class="fa fa-times"></i></button>
                            </div>

                        </td>
                    </tr>
                    </tbody>
                </table>
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
    /*$('#tbl_po_items', document).DataTable({
        bPaginate: false,
    });*/
</script>
