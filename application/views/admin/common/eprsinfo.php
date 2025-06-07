<?php

$requestor = get_users_info($trnqry->createdby);

$requested_by = $requestor->firstname.' '.$requestor->lastname;

$prf_qry = $this->db->select('justification')
    ->from('eprs_transaction')
    ->where('sysid',$dataid)
    ->get()->row();

if ($prf_qry) {
    $justification = $prf_qry->justification;
}

?>
<div class="portlet light bordered" data-toggle="editable">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-info-circle"></i>
            <span class="caption-subject font-green-sharp bold uppercase">PRF Info</span>
        </div>
    </div>
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-12">
                <ul class="list-group summary column no-border">
                    <li class="list-group-item">
                        <span class="label-name col-md-4 bold">PRF #</span>
                        <span class="label-default col-md-8"><?php echo $trnqry->descs; ?></span>
                    </li>
                    <li class="list-group-item">
                        <span class="label-name col-md-4 bold">Requested by</span>
                        <span class="label-default col-md-8"><?php echo $requested_by; ?></span>
                    </li>
                    <li class="list-group-item">
                        <span class="label-name col-md-4 bold">Request Date</span>
                        <span class="label-default col-md-8"><?php echo date('F j, Y',strtotime($trnqry->datecreated)); ?></span>
                    </li>
                    </li>
                    <li class="list-group-item">
                        <span class="label-name col-md-4 bold">Justification</span>
                        <span class="label-default col-md-8"><?php echo $justification; ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

