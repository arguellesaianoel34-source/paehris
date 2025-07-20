<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Clients extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('ApplicationCustomersDetails', 'clients');
        $this->load->helper('url');
    }

    public function index() {
        Header('Content-Type: application/json');

        $data = $this->clients->get_applications_with_details();
        echo json_encode($data);
    }

    public function show($id) {
        Header('Content-Type: application/json');

        $data = $this->clients->get_customer_details($id);
        echo json_encode($data);
    }
}