<?php
$trnid = $this->uri->segment(4);
$qry_trn = $this->db->select()->from('transaction_request_main_trails')->where('sysid', $trnid)->get()->row();
$qry_stg = $this->db->select()->from('prime_transaction_flow_main_stages')->where(array('sysid' => $qry_trn->stageid))->get()->row();
$flowid = $qry_stg->flowid;

?>
<!-- DATEPICKER CSS START!-->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-datepicker/css/datepicker3.css">
<!-- DATEPICKER CSS END!-->
<style>
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>

<div class="row tab-pane fade in">
    <div class="col-md-6">
        <?php
        $appdetails = get_application_details($dataid);
        $rateclassid = (isset($appdetails->rateclassid)) ? $appdetails->rateclassid : false;
        customer_application_basicinfo($dataid, true, false);
        ?>
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-red-flamingo bold uppercase">Other Missing Details</span>
                </div>
            </div>
            <div class="portlet-body">
                <form id="frm_application_missing_details" action="<?php echo base_url()?>billing/savecontractdetails" method="post">
                    <input type="hidden" value="<?php echo $dataid; ?>" name="appid">
                    <table class="table table-hover table-striped table-bordered">
                        <tbody>
                        <tr style="margin: 15px !important;">
                            <td>
                                <label class="col-md-3 control-label person-name bold" for="name"><i class="fa fa-calendar"></i> Date</span></label>
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="col-md-4 control-label person-name bold">Installation</span></label>
                                            <div class="col-md-8">
                                                <input type="date" class="form-control" name="installdate" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="col-md-4 control-label person-name bold"> Billing</span></label>
                                            <div class="col-md-8">
                                                <input class="form-control" id="select_billdate" name="billdate" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php if ($appdetails->info->systemsizeid == '') {?>
                            <tr style="margin: 15px !important;" id="systemtype_row">
                                <td>
                                    <label class="col-md-3 control-label person-name bold" for="name"><i class="fa fa-check-square-o"></i> System Type</span></label>
                                    <div class="col-md-9">
                                        <div class="col-md-6 icheck-inline">
                                            <input class="icheck" data-target="#standardsize" data-radio="iradio_square-red" id="standardtype" name="systemtype" type="radio" checked value="1" required> <label class="bold uppercase" for="standardtype">Standard</label>
                                        </div>
                                        <div class="col-md-6 icheck-inline">
                                            <input class="icheck" data-target="#nonstandardsize" data-radio="iradio_square-red" id="nonstandardtype" name="systemtype" type="radio" aria-label="" value="2" required> <label class="bold uppercase" for="nonstandardtype">Non-standard</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr style="margin: 15px !important;">
                                <td>
                                    <label class="col-md-3 control-label person-name bold" for="name"><i class="fa fa-line-chart"></i> System Size</span></label>
                                    <div class="col-md-9">
                                        <div class="row margin-bottom-5 " id="standardsize">
                                            <div class="col-md-12">
                                                <input class="form-control" id="select2_systemsize" name="newsize" required>
                                            </div>
                                        </div>
                                        <div class="row margin-bottom-5 " id="nonstandardsize">
                                            <div class="col-md-12">
                                                <input class="form-control" id="newsystemsize" name="newsize" placeholder="Build Name..." disabled required>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php
                        $customer_plan_details = $this->db->select()
                            ->from('customer_plan_details')
                            ->where(array('appid' => $dataid,'status !=' => 0))
                            ->get()->row();

                        if (!$customer_plan_details) {
                        ?>
                            <tr>
                                <td>
                                    <div class="col-md-3 bold">
                                        <i class="fa fa-clock-o"></i> Installment Plan
                                    </div>
                                    <div id="installmentplan">
                                        <div class="col-md-4">
                                            <input class="form-control" id="select2_plantype" name="plantype" placeholder="Plan Duration...">
                                            <!--<div class="row" id="installmentplan">
                                                <div class="col-md-3 icheck-inline">
                                                    <input class="icheck" data-radio="iradio_square-blue" id="outrightpay" name="plantype" type="radio" checked value="0"> <label class="bold " for="outrightpay">Outright</label>
                                                </div>
                                                <div class="col-md-3 icheck-inline">
                                                    <input class="icheck" data-radio="iradio_square-blue" id="payment2years" name="plantype" type="radio" aria-label="" value="2"> <label class="bold " for="payment2years">2 Years</label>
                                                </div>
                                                <div class="col-md-3 icheck-inline">
                                                    <input class="icheck" data-radio="iradio_square-blue" id="payment5years" name="plantype" type="radio" aria-label="" value="5"> <label class="bold " for="payment5years">5 Years</label>
                                                </div>
                                                <div class="col-md-3 icheck-inline">
                                                    <input class="icheck" data-radio="iradio_square-blue" id="payment10years" name="plantype" type="radio" aria-label="" value="10"> <label class="bold " for="payment10years">10 Years</label>
                                                </div>
                                            </div>-->
                                        </div>
                                        <div class="col-md-5">
                                            <?php $disabled = ($appdetails->info->systemtype == 1) ? 'disabled' : ''; ?>
                                            <input type="number" class="form-control" id="systemprice" name="price" placeholder="Monthly Amortization..." <?php echo $disabled; ?> required>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td>
                                <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-red-flamingo bold uppercase"> Preview</span>
                </div>
                <div class="tools">
                    <div class="btn-group">
                        <a href="javascript:" id="btn_reload_preview" class="btn btn-primary btn-sm inline"><i class="fa fa-refresh"></i> Refresh</a>
                        <a href="javascript:" id="btn_open_preview" class="btn btn-primary btn-sm inline"><i class="fa fa-search"></i> Open in Tab</a>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <div id="iframe_box" data-id="3434">

                </div>
                <div class="portlet-footer btn-group" id="preview_actions">
                    <div id="generate_contract">
                        <div class="col-md-4">
                            <input class="form-control" id="select2_billingstart" name="billingstart">
                        </div>
                        <button type="button" class="btn btn-primary pull-right" id="btn_finalize_contract"><i class="fa fa-save"></i> Finalize Contract and Create Billing Sequence</button>
                    </div>
                    <div id="delete_contract" class="hidden">
                        <button type="button" class="btn btn-danger pull-right" id="btn_regenerate_document"><i class="fa fa-undo"></i> Regenerate</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/pages/cad/newaccount.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/pages/sales/main.js" type="text/javascript"></script>

<script type="text/javascript">
    //CAD.profile(<?php echo $dataid; ?>, <?php echo $flowid;?>);
    SALES.contract(<?php echo $dataid; ?>);
    CAD.requirements(<?php echo $dataid; ?>,true);
    //PECO.select2Basic($('#select_billdate',document),'billing/select2billingdate','Billing date...',false,false,false);
    //PECO.select2Basic($('#select2_billingstart',document),'systems/select2month','Select start of billing series...',false,false,false);
</script>