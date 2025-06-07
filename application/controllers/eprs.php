<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
    session_start(); // STARTING SESSION DATA

class Eprs extends CI_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->model('model_purchasing','purchasing');
        $this->load->model('model_eprs','eprs');
    }

    function prf($prfnumber) {
        $data = array();

        if (strpos($prfnumber,'PRF') !== false) {
            $prfid = (int)ltrim(substr($prfnumber,7),'0');
        } else {
            $prfid = $prfnumber;
        }

        $data['prfid'] = $prfid;

        $this->load->view('admin/pages/modules/eprs/view',$data);
    }

}