<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Installation extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_installation','installation');
    }

    function dtinstallinverters() {
        echo $this->installation->dt_install_inverters();
    }

    function dtinstallationsetup() {
        echo $this->installation->dt_installation_setup();
    }

    function getinstallationsystemsize() {
        echo $this->installation->get_installation_system_size();
    }

    function getinstallationdates() {
        echo $this->installation->get_installation_dates();
    }

    function savedate() {
        echo $this->installation->save_date();
    }

    function select2brand() {
        echo $this->installation->select2_brand();
    }

    function select2inverter() {
        echo $this->installation->select2_inverter();
    }

    function saveinverterdetails() {
        echo $this->installation->save_inverter_details();
    }

    function deleteinverterdetails() {
        echo $this->installation->delete_inverter_details();
    }

    function finalizecustomerapplication() {
        echo $this->installation->finalize_customer_application();
    }
}