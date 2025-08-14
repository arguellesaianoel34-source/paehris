<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Purchasing extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_purchasing', 'purchasing', true);
        $this->load->model('model_bos');
        $this->load->model('model_eprs');
        //include Bos controller since some functions were needed and to minimize function duplicates.
        $this->load->library('../controllers/Bos');
    }
    /**
     * Get the account codes that were saved in the database.
     * @return array Retuns an array of data which contains the HTML of <option> to be used in account code selection front-end.
     */
    public function getAccountCodes()
    {
        $query = $this->model_purchasing->getAccountCodes();
        $html = '';
        foreach ($query->result() as $row) {
            $html .= '<option value="' . $row->sysid . '">' . $row->codes . ' - ' . $row->descs . '</option>';
        }
        $data = [
            'html' => $html
        ];
        echo json_encode($data);
    }
    /**
     * Get all the predefined units stored in the database.
     * @return array Returns the <option> HTML of the results to be used in the unit selection.
     */
    public function getUnit()
    {
        $unit = $this->model_bos->getUnitData();
        $html = '';
        foreach ($unit->result() as $row) {
            $html .= '<option value="' . $row->sysid . '">' . $row->unit_code . ' - ' . $row->unit_name . '</option>';
        }
        $data = [
            'html' => $html
        ];
        echo json_encode($data);
    }
    public function getCostCenters()
    {
        echo $this->bos->getCostCenterData();
    }
    //TODO UNFINISHED, please improve the logic in order for the ajax to receive the data for viewing.
    function getBtypeBudgets($btype_id, $cc_id, $year)
    {
        $data = array(
            'qry'           => false,
            'query_result'  => null,
            'msg'           => 'No queries has been made, please use the right budget type.'
        );
        if ($btype_id == 77) {
            $budget_data = $this->model_eprs->getCcOpexBudget($cc_id, $year);
            if ($budget_data) {
                $data['qry'] = true;
                $data['query_result'] = $budget_data;
                $data['msg'] = 'OPEX budget retrieval successful!';
            } else {
                #return an error message
                $data['msg'] = 'OPEX budget empty!';
            }
        } else if ($btype_id == 76 || $btype_id == 78) {
            $budget_data = $this->model_eprs->getCcCapexSpBudget($cc_id, $btype_id, $year);
            if ($budget_data) {
                $data['qry'] = true;
                $data['query_result'] = $budget_data;
                $data['msg'] = 'CAPEX/SP budget retrieval successful!';
            } else {
                $data['msg'] = 'CAPEX/SP budget empty!';
            }
        } else {
            $data['msg'] = 'Budget ID is invalid, please contact the administrator.';
        }
        echo json_encode($data);
    }

    function toggleItemPrsRequest()
    {
        $item_id = $this->input->post('itemId');
        $toggled = $this->model_eprs->toggleItemPrsRequest($item_id);
        $toggled_value = null;
        $qry = false;
        $information = '';
        $func = 'warning';
        $msg = 'toggleItemPrsRequest query has been failed.';
        if ($toggled) {
            $toggled_value = $toggled->row()->prs_request;
            if ($toggled_value == 1) {
                $msg = 'Item ADDED to PRS request.';
                $information = 'ITEM ADDITION';
            } else {
                $information = 'ITEM REMOVAL';
                $msg = 'Item REMOVED to PRS request.';
            }

            $qry = true;
            $func = 'success';
        }
        echo json_encode(
            array(
                'qry'       => $qry,
                'msg'       => $msg,
                'func'      => $func,
                'info'      => $information,
                'toggleVal' => $toggled_value
            )
        );
    }
    /**
     * Chooses which item toggle query to be used based on the action of the user on the prs checkbox.
     * @param int $budget_data_id The ID of the budget in `trn_budget_data`.
     * @param int $checked The action made by the user 1 for "checked", 0 for "unchecked".
     * @return array associative array of values.
     */
    function toggleBudgetItems($budget_data_id, $checked)
    {
        $data = array();
        if ($checked == 1) {
            $toggled = $this->model_eprs->togglePrsItemOne($budget_data_id);
            $data['qry'] = $toggled;
            $data['msg'] = 'Items within this budget will be added to the request.';
            $data['func'] = 'success';
        } else if ($checked == 0) {
            $toggled = $this->model_eprs->togglePrsItemZero($budget_data_id);
            $data['qry'] = $toggled;
            $data['msg'] = 'The budget and the items within were removed from PRS request.';
            $data['func'] = 'success';
        } else {
            $data['qry'] = false;
            $data['msg'] = 'Budget does not match any approved budget in the system.';
            $data['func'] = 'warning';
        }
        return $data;
    }
    function toggleBtypeRequest()
    {
        $budget_data_id     = $this->input->post('budgetId');
        $checked            = $this->input->post('checked');
        $budget_toggle_val = false;
        $toggle_stats = $this->toggleBudgetItems($budget_data_id, $checked);
        $budget_toggle = $this->model_eprs->toggleBudgetPrsRequest($budget_data_id);
        if ($budget_toggle) {
            $budget_toggle_val = $budget_toggle->row()->prs_request;
        }
        $data = array(
            'toggleData'        => $toggle_stats,
            'budgetToggleVal'   => $budget_toggle_val
        );
        echo json_encode($data);
    }
    #########################################################################################################################################################
    ################################################################## CODE TESTING #########################################################################
    #########################################################################################################################################################
    function testToggleItemPrs()
    {
        print_r($this->toggleItemPrsRequest(229));
    }
    function test()
    {
        $budgetid_arr = $this->input->post('budgeids');
        $num_of_budget_submited = 0;
        $budget_id_arr = array();
        foreach ($budgetid_arr as $row) {
            $budget_id_arr[] = $row;
            $num_of_budget_submited += 1;
        }
        $data['budgetid'] = $budget_id_arr;
        $data['budgetnum'] = $num_of_budget_submited;
        $data['input'] = $this->input->post();
        echo json_encode($data);
    }
    function testgetBtypeBudgets()
    {
        print_r($this->getBtypeBudgets(76, 14, 2017));
    }


    function tblsuppliers()
    {
        echo $this->purchasing->tbl_suppliers();
    }

    function addprfitem()
    {
        echo $this->purchasing->add_prf_item();
    }

    function dtprfitems()
    {
        echo $this->purchasing->dt_prf_items();
    }

    function saveprfdraft()
    {
        echo $this->purchasing->save_prf_draft();
    }

    function saveitemedit()
    {
        echo $this->purchasing->save_item_edit();
    }

    function removeprsitem()
    {
        echo $this->purchasing->remove_prs_item();
    }

    function discardprf()
    {
        echo $this->purchasing->discard_prf();
    }

    function sendprfapproval()
    {
        echo $this->purchasing->send_prf_approval();
    }

    function getprslist()
    {
        echo $this->purchasing->get_prs_list();
    }

    function getprfitemsforapproval()
    {
        echo $this->purchasing->get_prf_items_for_approval();
    }

    function showprfitemcomments()
    {
        echo $this->purchasing->show_prf_item_comments();
    }

    function showrfqitemcomments()
    {
        echo $this->purchasing->show_rfq_item_comments();
    }

    function disapproveprfitem()
    {
        echo $this->purchasing->disapprove_prf_item();
    }

    function approveprf()
    {
        echo $this->purchasing->approve_prf();
    }

    function returnprf()
    {
        echo $this->purchasing->return_prf();
    }

    function disapproveprf()
    {
        echo $this->purchasing->disapprove_prf();
    }

    function requoterfq()
    {
        echo $this->purchasing->requote_rfq();
    }

    function getrfqitemslist()
    {
        echo $this->purchasing->get_rfq_items_list();
    }

    function dtquotationitems()
    {
        echo $this->purchasing->dt_quotation_items();
    }

    function select2quotationsupplier()
    {
        echo $this->purchasing->select2_quotation_supplier();
    }

    function select2paytype()
    {
        echo get_item_type('PAYTYPE');
    }

    function addsupplierquotation()
    {
        echo $this->purchasing->add_supplier_quotation();
    }

    function saveprfquotation()
    {
        echo $this->purchasing->save_prf_quotation();
    }

    function getsuppliersummaryofcost()
    {
        echo $this->purchasing->get_supplier_summary_of_cost();
    }

    function computesummaryofcost()
    {
        echo $this->purchasing->compute_summary_of_cost();
    }

    function dtapproverremarks()
    {
        echo $this->purchasing->dt_approver_remarks();
    }

    function reviseitemqty()
    {
        echo $this->purchasing->revise_item_qty();
    }

    function deletesupplierquotation()
    {
        echo $this->purchasing->delete_supplier_quotation();
    }

    function select2routes()
    {
        $data = array();
        $route = $this->input->post('data');

        if ($route) {
            if (is_array($route)) {
                $this->db->where_in('levels', $route);
            } else {
                if ($route != 'false') {
                    $this->db->where('levels', $route);
                }
            }
        }

        $qry = $this->db->select()
            ->from('prime_transaction_flow_main_stages')
            ->where(array('flowid' => 3, 'status' => 1))
            ->order_by('levels')
            ->get();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->levels,
                    'text' => $row->levels . ' - ' . $row->desc
                );
            }
        }

        //$data['qry'] = $this->db->last_query();
        echo json_encode($data);
    }

    function dtposuppliers()
    {
        echo $this->purchasing->dt_po_suppliers();
    }

    function savepaymentrequest()
    {
        echo $this->purchasing->save_payment_request();
    }

    function generatepo()
    {
        echo $this->purchasing->generate_po();
    }

    function myprslist()
    {
        echo $this->purchasing->my_prs_list();
    }

    function myprsdraft()
    {
        echo $this->purchasing->my_prs_draft();
    }

    function editjustification()
    {
        echo $this->purchasing->edit_justification();
    }

    function newsuppliervalidation()
    {
        echo $this->purchasing->new_supplier_validation();
    }

    function savenewsupplier()
    {
        echo $this->purchasing->save_new_supplier();
    }

    function prslist()
    {
        echo $this->purchasing->prs_list();
    }

    function prfsubdetails()
    {
        echo $this->purchasing->prf_sub_details();
    }

    function loadprfitems()
    {
        echo $this->purchasing->load_prf_items();
    }

    function prsviewerlist()
    {
        echo $this->purchasing->prs_viewer_list();
    }

    function updatesupplierquotation()
    {
        echo $this->purchasing->update_supplier_quotation();
    }

    function updatesupplierdetails()
    {
        echo $this->purchasing->update_supplier_details();
    }

    function exportquotationsheet()
    {
        echo $this->purchasing->export_quotation_sheet();
    }

    function uploadpastpurchases()
    {
        echo $this->purchasing->upload_past_purchases();
    }

    function itemlastprice()
    {
        echo $this->purchasing->item_last_price();
    }

    function cancelpurchaserequest()
    {
        echo $this->purchasing->cancel_purchase_request();
    }

    function getsupplierpaymentdetails()
    {
        echo $this->purchasing->get_supplier_payment_details();
    }
}
