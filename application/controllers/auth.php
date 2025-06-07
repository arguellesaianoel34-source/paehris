<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Auth extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('model_auth', '', TRUE);
        session_start();

        // CHECK IF THIS PAGE IS NOT TRIGGER WITH AJAX THEN REDIRECT BACK TO HOME // 
        if (!$this->input->is_ajax_request()) {
            redirect(base_url());
        }
    }

    function index() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');
        if ($this->form_validation->run() == FALSE) {
            return false;
        } else {
            return true;
        }
    }

    function check_database() {
        $data = array();
        $field = false;
        //Field validation succeeded.  Validate against database
        $username = $this->input->post('username');
        $password = $this->input->post('password');

        if (!empty($username) || !empty($password) ) {
            $field = true;
        }

        //query the database
        if ($field == true) {
            $data = $this->model_auth->login($username, $password);
        } else {
            $data['message'] = return_message_ajax('warning', 'fa-warning', 'Username / Password is empty!');
            $data['num'] = 0;
        }
        echo json_encode($data);
    }

    function logout() {
        echo $this->model_auth->logout();
    }

    function lock() {
        echo json_encode($this->model_auth->lock_user_log());
    }

    function unlock() {
        echo $this->model_auth->unlock_user_log();
    }

    function destroysession() {

        $this->session->unset_userdata('logged_in');
        session_destroy();
    }

}
