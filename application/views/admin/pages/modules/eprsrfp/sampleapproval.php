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
                    <i class="fa fa-check font-green-haze"></i>
                    <span class="caption-subject bold font-green-haze uppercase"> Request for Payment Approval </span>
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
                                <li class="list-group-item">
                                    <span class="label-name col-md-4 bold">PO #</span>
                                    <span class="col-md-8 font-blue-steel ">PO230118001</span>
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
                                <button class="btn btn-danger"><i class="fa fa-times"></i> Disaprove</button>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-striped" id="tbl_po_items">
                    <thead>
                        <th>#</th>
                        <th>Supplier</th>
                        <th>Payment Type</th>
                        <th>Amt</th>
                        <th>Payee</th>
                        <th width="30%">Purpose</th>
                        <th width="150px" class="text-align-center">Control</th>
                    </thead>
                    <tbody>
                    <tr>
                        <td>1</td>
                        <td>RJLP</td>
                        <td>Online</td>
                        <td class="number">3,280.00</td>
                        <td>RJLP Marketing</td>
                        <td>Purchase of PV materials replenishment.</td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <a href="#frm_create_payment_request" title="Payment Request Details" data-toggle="ajax-modal" data-arr="" class="btn btn-primary inline"><i class="fa fa-search"></i> Details</a>
                                <a href="#frm_create_payment_request" title="PO Details" data-toggle="ajax-modal" data-arr="" class="btn btn-success inline"><i class="fa fa-search"></i> PO</a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Wine Guard</td>
                        <td>Check</td>
                        <td class="number">23,814.00</td>
                        <td>Jane Denila</td>
                        <td>Purchase of PV materials replenishment.</td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <a href="#frm_create_payment_request" title="Payment Request Details" data-toggle="ajax-modal" data-arr="" class="btn btn-primary inline"><i class="fa fa-search"></i> Details</a>
                                <a href="#frm_create_payment_request" title="PO Details" data-toggle="ajax-modal" data-arr="" class="btn btn-success inline"><i class="fa fa-search"></i> PO</a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Benson</td>
                        <td>Online</td>
                        <td class="number">7,566.00</td>
                        <td>BENSON ELECTRICAL SUPPLY</td>
                        <td>Payment for PV Materials replenishment.</td>
                        <td class="text-align-center">
                            <div class="btn-group">
                                <a href="#frm_create_payment_request" title="Payment Request Details" data-toggle="ajax-modal" data-arr="" class="btn btn-primary inline"><i class="fa fa-search"></i> Details</a>
                                <a href="#frm_create_payment_request" title="PO Details" data-toggle="ajax-modal" data-arr="" class="btn btn-success inline"><i class="fa fa-search"></i> PO</a>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <h4 class="bold">Approval Status</h4>
                <div class="well">
                    <div class="row">
                        <div class="col-md-4">
                            <ul class="list-group summary column no-border">
                                <li class="list-group-item">
                                    <span class="label-name col-md-4 bold">Audit</span>
                                    <span class="col-md-8 font-blue-steel ">
                                        <span class="label label-success" style="padding: 2px 5px !important; width: auto!important;"><i class="fa fa-check"></i> Approved</span>
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <ul class="list-group summary column no-border">
                                <li class="list-group-item">
                                    <span class="label-name col-md-4 bold">General Manager</span>
                                    <span class="col-md-8 font-blue-steel ">
                                        <span class="label label-warning" style="padding: 2px 5px !important; width: auto!important;">Current: <i class="fa fa-warning"></i> Pending</span>
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <ul class="list-group summary column no-border">
                                <li class="list-group-item">
                                    <span class="label-name col-md-4 bold">PCEO</span>
                                    <span class="col-md-8 font-blue-steel ">
                                        <span class="label label-warning" style="padding: 2px 5px !important; width: auto!important;"><i class="fa fa-warning"></i> Pending</span>
                                    </span>
                                </li>
                            </ul>
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
    });
</script>

