
<style type="text/css">
    .dataTables_scrollBody{
        scrollbar-width: none !important;
    }

    ::-webkit-scrollbar {
        width: 0px;
    }
</style>

<div class="portlet light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#trn_flow_maintenance" data-toggle="tab"> TRN Maintenance </a>
            </li>
            <li class="">
                <a href="#trn_role_access" data-toggle="tab" aria-expanded="true"> TRN Access </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body">
        <div class="tab-content">
            <div class="tab-pane fade in active" id="trn_flow_maintenance">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-exchange"></i>
                                    <span class="caption-subject bold uppercase">Rearrange Transaction Flow</span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <table class="table table-condensed table-bordered table-hover table-hover tbl-sm" id="trnflowstagestbl_details">
                                    <thead>
                                    <th>Level</th>
                                    <th>Descriptions</th>
                                    <th>Module ID</th>
                                    <th>Move</th>
                                    <th>Control</th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade in " id="trn_role_access">
                <div class="row">
                    <div class="col-md-6">
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-exchange"></i>
                                    <span class="caption-subject bold uppercase">User Roles</span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <table width="100%" class="table table-condensed tbl-sm table-bordered" id="roles_list">
                                    <thead>
                                    <th></th>
                                    <th>CODE</th>
                                    <th>DESCRIPTION</th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-list-ul"></i>
                                    <span class="caption-subject bold uppercase">Transaction Stages</span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="row">
                                    <form id="frm_add_sp_access" method="post" action="<?php echo base_url('settings/addtrnspaccess');?>">
                                        <input type="hidden" name="roleid" id="input_roleid">
                                        <div class="col-md-8">
                                            <input class="form-control" name="stages" id="select2_stages" placeholder="Select stage to add..." style="width: 100% !important;">
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Assign Access</button>
                                        </div>
                                    </form>
                                </div>
                                <br>
                                <table width="100%" class="table table-condensed table-bordered" id="stage_list">
                                    <thead>
                                    <th>Level</th>
                                    <th>Descriptions</th>
                                    <th>Module</th>
                                    <th>Access</th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/pages/settings/trnflow.js"></script>
<script type="text/javascript">
    TRANSACTIONSFLOW.stages($('#trnflowstagestbl_details',document),<?php echo $id;?>);
    TRANSACTIONSFLOW.spAccess(<?php echo $id;?>);
</script>