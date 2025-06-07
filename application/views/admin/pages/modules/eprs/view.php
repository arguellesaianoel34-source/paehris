<?php
if (!isset($prfid)) {
    $prfid = $this->uri->segment(4);
}

$trn_qry = $this->db->select('et.sysid AS dataid,rmt.stageid,fms.flowid,fms.moduleid,fms.levels,et.status,trm.sysid AS trnflowid,trm.sysid AS trnid')
    ->from('eprs_transaction AS et')
    ->join('transaction_request_main_trails AS rmt','rmt.dataid = et.sysid','inner')
    ->join('transaction_request_main AS trm','rmt.trnid = trm.sysid','inner')
    ->join('prime_transaction_flow_main_stages AS fms','rmt.stageid = fms.sysid','left')
    ->where(array(
        'rmt.status' => 1,
        'et.status >' => 0,
        'et.sysid' => $prfid,
        'fms.flowid' => 3
    ))->get()->row();

if ($trn_qry) {
    $view = array(
        '','data','prf','rfq','rfq','rfq','rfq','po','po','po','po','rfp','rfp','rfp','rfp'
    );

    $trn = (array)$trn_qry;
    $trn['trnview'] = true;

    /*echo "<pre>";
    print_r ($trn);
    echo "</pre>";*/
    /*echo "<pre>";
    print_r ($this->_ci_cached_vars);
    echo "</pre>";*/

    $_ci_cached_vars = (object)array_merge((array)$this->_ci_cached_vars,$trn);

    $this->load->view('admin/pages/modules/eprs/' . $view[$trn_qry->levels], $trn);
}

$draft = $this->db->select('status')->from('eprs_transaction')->where('sysid',$prfid)->get()->row();

if ($draft && $draft->status == 307) {
    //$_ci_cached_vars = (object)array_merge((array)$this->_ci_cached_vars,array('prfid' => $prfid));
    $this->load->view('admin/pages/modules/eprs/draft', $this->_ci_cached_vars);
}

if (!$trn_qry && (!$draft || $draft->status != 307)) {
    page_file_notfound('My PRS','<b>Cannot find PRS/PRF transaction.</b>');
}
/*echo "<pre>";
print_r ($this->_ci_cached_vars);
echo "</pre>";*/




?>