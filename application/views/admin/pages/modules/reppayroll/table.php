<div class="row">
    <div class="col-md-12">
        <div class="portlet light table">
            <div class="portlet-head">
                <div class="caption" style="padding: 10px 20px;">
                    <h4 class="font-blue-madison col-md-4">Payroll Reports</h4>

                    <div class="col-md-8 pull-right">

                        <form id="frm_get_payroll_rep" action="<?php echo base_url('reports/getpayrollreports'); ?>" method="post">

                                <div class="input-group">
                                    <input type="hidden" id="formpost" name="formpost" value="1">
                                    <input name="reptype" class="form-control" id="reporttype" placeholder="Report Type" />
                                    <span class="input-group-btn" style="width:0px;"></span>
                                    <input name="year" class="form-control" id="year" placeholder="Year" onclick="this.select()" value="<?php echo date('Y');?>"/>
                                    <span class="input-group-btn" style="width:0px;"></span>
                                    <input name="month" class="form-control" id="month" placeholder="Month" />
                                    <span class="input-group-btn" style="width:0px;"></span>
                                    <input name="costcenter" class="form-control" id="costcenter" placeholder="Cost Center" />
                                    <span class="input-group-btn" style="width:0px;"></span>
                                    <select name="payclass" class="form-control" id="payclass">
                                        <option></option>
                                        <option value="1">Confi/SA</option>
                                        <option value="2">RF/Tiered</option>
                                    </select>
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn btn-default"><i class="fa fa-sign-out text-primary"></i> GET</button>
                                        <button type="button" id="btn_download_excel" class="btn btn-default"><i class="fa fa-file-excel-o text-success"></i> DOWNLOAD</button type="button">
                                    </span>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <table id="tbl_payroll_reports" class="table table-hover table-bordered table-condensed">
                    <thead>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/pages/reports/payroll.js" type="text/javascript"></script>

<script>
    PAYROLL.admin();
</script>