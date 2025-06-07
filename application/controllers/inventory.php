<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Inventory extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->model('model_inventory', 'inventory', true);
        $this->load->model('model_assets', 'assets', true);


        //load library
        $this->load->library('zend');
        //load in folder Zend
        $this->zend->load('Zend/Barcode');
    }

    function tblgetdatainit() {
        echo $this->inventory->tbl_get_data_initialization();
    }
    function tblsuppliers() {
        echo $this->inventory->tbl_get_suppliers();
    }

    function dataaddinit() {
        echo $this->inventory->data_add_initialization();
    }

    function tblproducts() {
        echo $this->inventory->tbl_products();
    }

    function tblstocks(){
        echo $this->inventory->tbl_get_stocks();
    }

    function addstocks(){
        echo $this->inventory->add_stocks();
    }

    function tblgetstockin(){
        echo $this->inventory->tbl_get_stock_in();
    }

    function draftstockin(){
        echo $this->inventory->draft_stock_in();
    }

    function savestockin(){
        echo $this->inventory->save_stock_in();
    }

    function savestockout(){
        echo $this->inventory->query_stock_out();
    }

    function savestockreturn(){
        echo $this->inventory->query_stock_out();
    }

    function querystockout(){
        echo $this->inventory->query_stock_out();
    }
    function stockdetails(){
        echo $this->inventory->stock_details();
    }
    function generatebarcode($stockid = false, $codestart = false, $codecount = false){
        echo $this->inventory->generate_barcode($stockid, $codestart, $codecount);
    }
    function page($page = null){
        $data = array();
        $data['pagetitle'] = 'Inventory | ' . strtoupper($page);

        init_header_nonav($data);
        $this->load->view('admin/pages/modules/inventory/' . $page, $data);
        init_footer_nonav($data);
    }
    function materialrequest() {
        init_header_nonav();
        $this->load->view('admin/pages/modules/inventory/material_requets');
        init_footer_nonav();
    }

    function tblstocklist(){
        echo $this->inventory->tbl_stock_list();
    }

    function select2trntype() {
        echo get_types_select('INVTRNTYPE');
    }

    function dtreferencelist() {
        echo $this->inventory->dt_reference_list();
    }

    function createtrn() {
        echo $this->inventory->create_trn();
    }

    function canceltrn() {
        echo $this->inventory->cancel_trn();
    }

    function addreference() {
        echo $this->inventory->add_reference();
    }

    function polookup() {
        echo $this->inventory->po_lookup();
    }

    function activepolist() {
        echo $this->inventory->active_po_list();
    }

    function poinfo() {
        echo $this->inventory->po_info();
    }

    function dtinventorytrnitems() {
        echo $this->inventory->dt_inventory_trn_items();
    }

    function gettransactionitems() {
        echo $this->inventory->get_transaction_items();
    }

    function savetrnitemqty() {
        echo $this->inventory->save_trn_item_qty();
    }

    function removeinventoryitem() {
        echo $this->inventory->remove_inventory_item();
    }

    function submittrn() {
        echo $this->inventory->submit_trn();
    }

    function checkiventoryitems() {
        echo $this->inventory->check_iventory_items();
    }

    function deletetransactionreference() {
        echo $this->inventory->delete_transaction_reference();
    }

    function dtinventorytransactionlist() {
        echo $this->inventory->dt_inventory_transaction_list();
    }

    function cadlookup() {
        echo $this->inventory->cad_lookup();
    }

    function appinfo() {
        echo $this->inventory->app_info();
    }

    function select2installationtemplate() {
        echo $this->inventory->select2_installation_template();
    }

    function dtinstallationtemplateitems() {
        echo $this->inventory->dt_installation_template_items();
    }

    function addinstallationitem() {
        echo $this->inventory->add_installation_item();
    }

    function saveserialnumber() {
        echo $this->inventory->save_serial_number();
    }

    function uploadattachments() {
        echo $this->inventory->upload_attachments();
    }

    function gettrnattachments() {
        echo $this->inventory->get_trn_attachments();
    }

    function approvetrn() {
        echo $this->inventory->approve_trn();
    }

    function approvedtrnlist() {
        echo $this->inventory->approved_trn_list();
    }

}