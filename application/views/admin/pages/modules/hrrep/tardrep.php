<div class="row">
    <div class="portlet">
        <div class="portlet-title">
            <div class="caption">
                Monthly Tardiness Report
            </div>
        </div>
        <div class="portlet-body">

            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Month:</label>
                        <input type="text" name="month" id="month" class="form-control" />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Year:</label>
                        <input type="text" name="year" id="year" class="form-control" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <button style="margin-top: 26px !important;" class="btn btn-primary" id="searchtardibtn"><i class="fa fa-search"></i> Search</button>
                        <button style="margin-top: 26px !important;" class="btn btn-default" id="printmonthlytard"><i class="fa fa-print"></i> Print Summary</button>
                        <button style="margin-top: 26px !important;" class="btn btn-default" id="printmonthlytarddetails"><i class="fa fa-print"></i> Print with Details</button>
                    </div>
                </div>
            </div>
            <table class="table table-bordered table-responsive table-hover tbl-xs" id="tardinesstable">
                <thead>
                <th>#</th>
                <th>Employee</th>
                <th>Bio ID</th>
                <th>Total Lates</th>
                <th>Total Undertime</th>
                <th>Total for Deduction</th>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>




<script src="<?php echo base_url() ?>assets/pages/tardrep/tardrep.js"></script>

<script>
    TARDREP.init(true);
</script>