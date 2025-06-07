<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
session_start(); // STARTING SESSION DATA
class Forms extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_forms','forms');
        $this->load->helper('forms');

        if(check_user_lock()) {
            redirect(base_url(), 'refresh');
        }

        if(!user_id()) {
            if ($this->uri->total_segments() > 0) {
                $currentURL = explode('?',current_url());

                redirect(base_url().'?redirect='.$currentURL[1], 'refresh');
            } else {
                redirect(base_url(), 'refresh');
            }
        }
    }

    function index() {
        init_header_nonav();
        init_page_wrapper_top();
        echo '<h1>Hello world!</h1>';
        init_page_wrapper_bottom();
    }

    function formslist() {

    }

    function tncapplookup() {
        echo $this->forms->tnc_app_lookup();
    }

    function tnccreate() {
        /*
         * If application does not have a form response:
         * - Create new form data,
         * - Leave inputs blank except for inputs with related data to the application,
         * - Load form pages based on build type.
         * - Change button to Update and add Delete button
         */

        echo $this->forms->tnc_create();
    }

    function tncupdate() {
        /*
         * If application does not have a form response:
         * - Create new form data,
         * - Leave inputs blank except for inputs with related data to the application,
         * - Load form pages based on build type.
         * - Change button to Update and add Delete button
         */

        echo $this->forms->tnc_update();
    }

    function loadtncform() {
        echo $this->forms->load_tnc_form();
    }

    function tncsavechecklist() {
        echo $this->forms->tnc_save_checklist();
    }

    function tncsavestringtest() {
        echo $this->forms->tnc_save_stringtest();
    }

    function tncsavecontinuitytest() {
        echo $this->forms->tnc_save_continuity_test();
    }

    function uploadtncpics() {
        echo $this->forms->upload_tnc_pics();
    }

    function tncsaveinsulation() {
        echo $this->forms->tnc_save_insulation();
    }

    function tncsavetorquetest() {
        echo $this->forms->tnc_save_torquetest();
    }

    function tncsavethermaltest() {
        echo $this->forms->tnc_save_thermal_test();
    }

    function tnctabledform() {
        echo $this->forms->tnc_tabled_form();
    }
}