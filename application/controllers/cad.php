<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
session_start(); // STARTING SESSION DATA

class Cad extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_admin');
        $this->load->model('model_query');
        $this->load->model('model_cad', 'cad');
        $this->load->helper('cad_helper');
        if (check_user_lock()) {
            redirect(base_url(), 'refresh');
        }
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
            ->where(array('flowid' => 2, 'status' => 1))
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

        $data['qry'] = $this->db->last_query();
        echo json_encode($data);
    }

    function getinstallationteam()
    {
        echo $this->cad->get_installation_team();
    }

    function getapplicaitonsubdetails()
    {
        echo $this->cad->get_application_details();
    }

    function getnewconnectionlist()
    {
        echo $this->cad->get_new_connection_lists();
    }

    function setstatus()
    {
        echo $this->cad->set_status();
    }

    function savenewaccount()
    {
        echo $this->cad->save_new_application();
    }

    function saveonlineaccount()
    {
        echo $this->cad->save_online_application();
    }

    function getapplications()
    {
        echo $this->cad->get_applications_report();
    }

    function getapplicationinfo()
    {
        echo $this->cad->get_application_info();
    }

    function serviceslastname()
    {
        echo $this->cad->get_services_lastname();
    }

    function servicestart()
    {
        echo $this->cad->get_services_starts();
    }

    function submiteditable()
    {
        echo $this->cad->submit_editable();
    }

    function searchrequirements()
    {
        $term = $this->input->post('term');
        $q = $this->db->select('prp.sysid AS PRPSYSID, prp.names AS PRPNAMES, prp.desc AS PRPDESC')
            ->from('requirements_parameters AS pcar')
            ->join('prime_requirement_parameters AS prp', 'prp.sysid = pcar.reqid')
            ->group_by('prp.sysid')
            ->like('prp.desc', $term)->get();
        $res_num = $q->num_rows();
        $qry = false;
        if ($res_num > 0 && $term != '') {
            foreach ($q->result() as $row) {
                $data['list'][] = array('id' => $row->PRPSYSID, 'text' => $row->PRPNAMES);
            }
            $qry = true;
        }
        $data['qry'] = $qry;
        $data['res'] = $res_num;
        $data['input'] = $this->input->post();
        echo json_encode($data);
    }


    function updategdlb()
    {
        $data = array();
        $msg = '';
        $func = 'error';
        $qry = false;
        $appid = $this->input->post('appid');
        $gdlbid = $this->input->post('gdlbid');
        $this->db->trans_begin();

        $qry_gdlb_final = $this->db->select()->from('gdlb_main')->where('sysid', $gdlbid)->get()->row();
        $qry_app_details = $this->db->select()->from('application_customers_details')->where('sysid', $appid)->get()->row();

        // GET SEQUENCE
        $qry_seq = $this->db->select_max('sysid')->from('application_customers_sequence')->get()->row();
        $new_seq = ($qry_seq) ? $qry_seq->sysid + 1 : 1;

        if ($qry_gdlb_final->d == 5) {
            $gdlb_final_dcode = 'S';
        } else {
            $gdlb_final_dcode = get_district_name($qry_gdlb_final->d) ? get_district_name($qry_gdlb_final->d)[0] : '';
        }

        // SERVNO
        $servno = $gdlb_final_dcode . str_pad($new_seq, 6, "0", STR_PAD_LEFT);
        // INSERT SEQUENCE
        $this->db->insert("application_customers_sequence", array('appid' => $appid, 'createdby' => user_id()));
        $data['err'] = $this->db->_error_message();


        $this->db->where('sysid', $appid);
        $this->db->update('application_customers_details', array(
            'gdlbid' => $gdlbid,
            'servno' => $servno
        ));


        $audit_ins_arr = array(
            'dataid' => $appid,
            'moduleid' => 35,
            'valueold' => $qry_app_details->servno,
            'valuenew' => $servno,
            'createdby' => user_id(),
            'remarks' => 'CAD - GDLB Change'
        );
        $audit_ins = audit_insert($audit_ins_arr);

        if ($this->db->trans_status() && $audit_ins == true) {
            $this->db->trans_commit();
            $qry = true;
            $msg = 'GDLB is now updated!';
            $func = 'success';
        } else {
            $this->db->trans_rollback();
            $msg = 'Error GDLB SQL Update';
        }
        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['servno'] = $servno;
        echo json_encode($data);
    }

    function getacctrequirements($appid = false)
    {
        if (!$appid) {
            $id = $this->input->post('id');
        } else {
            $id = $appid;
        }

        $html = '';

        $q = $this->db->select('CR.sysid AS REQID , CR.comply, prp.sysid AS PRPSYSID, prp.names AS PRPNAMES, prp.desc AS PRPDESC')
            ->from('prime_requirement_parameters AS prp')
            ->join('application_customers_requirements AS CR', 'CR.reqid = prp.sysid')
            ->where(array('CR.appid' => $id))
            ->group_by('CR.sysid , CR.comply, prp.sysid, prp.names, prp.desc')
            ->get();

        $html .= peco_print_header(user_id(), "Application Requirements", "", false);

        $html .= '<table class="table table-bordered table-condensed tbl-sm">';
        $html .= '<thead>';
        $html .= '<th>ID</th>';
        $html .= '<th>Requirement</th>';
        $html .= '<th>Status</th>';
        $html .= '</thead>';
        $html .= '<tbody>';


        $res_num = $q->num_rows();
        $res_comp = 0;

        if ($res_num > 0) {
            $num = 1;
            foreach ($q->result() as $row) {
                $status = 'INCOMPLETE';
                if ($row->comply == 1) {
                    $status = 'COMPLETE';
                }
                $html .= '<tr>';
                $html .= '<td>' . $num++ . '</td>';
                $html .= '<td>' . $row->REQID . ' - ' . $row->PRPNAMES . '</td>';
                $html .= '<td>' . $status . '</td>';
                $html .= '</tr>';

                $stat = '';
                $data['list'][] = array(
                    'id' => $row->PRPSYSID,
                    'text' => $row->REQID . ' - ' . $row->PRPNAMES,
                    'stat' => ''
                );
            }
        }
        $html .= '</tbody>';
        $html .= '</table>';

        $data['html'] = $html;
        $data['comp'] = $res_comp;
        $data['res'] = $res_num;
        $data['input'] = $this->input->post();
        //print_r($html);
        echo json_encode($data);
    }

    function getselectednamematched()
    {
        $data = array();
        $accountexists = false;
        $personid = $this->input->post('personid');

        $qry_person = $this->db->select()->from('person')
            ->where(array('sysid' => $personid, 'status' => 1))
            ->get()->row();

        $qry_accounts = $this->db->select()->from('customer_accounts_owners')
            ->where(array('ownerid' => $personid, 'status' => 1))
            ->get()->row();

        $lastname = $qry_person->lastname;
        $firstname = $qry_person->firstname;

        $qry_accoutns_legacy = $this->db->select('nl.sysid, nl.name, gm.d')
            ->from('customer_accounts_name_legacy AS nl')
            ->join('customer_accounts_main AS am', 'am.ownerid = nl.sysid')
            ->join('gdlb_main AS gm', 'gm.sysid = am.gdlb')
            ->like('nl.name', $lastname)->get();


        if ($qry_accoutns_legacy->num_rows() > 0) {
            foreach ($qry_accoutns_legacy->result() as $row) {
                $first_array[] = array('sysid' => $row->sysid, 'firstname' => $row->name, 'middlename' => $row->name, 'district' => $row->d);
                $data['personarr'] = $first_array;
            }
            $search_firstname = array_contains_key($first_array, 'firstname', 'sysid', $firstname);
            $search_firstname_cnt = count($search_firstname);
            $data['searchfirstname'] = $search_firstname;
            if ($search_firstname_cnt) {
                $person_exists = true;
            }
        }

        $html_cont = false;
        $html_normalized = '';
        $html_legacy = '';

        if ($qry_accounts) {
            $html_cont = true;
            $html_normalized .= $qry_accounts->accountid;
        }


        $data['personarr'] = ($qry_person) ? $qry_person : false;
        $data['input'] = $this->input->post();
        echo json_encode($data);
    }

    function getcustomerrequirements()
    {
        echo $this->cad->get_customer_requirements();
    }

    function getrequirementsres()
    {
        $ids = $this->input->post('ids');
        //$ids = '1,2,3,4,5,6,7,8,24,25,26,27';
        $req_sysids_arr = explode(',', $ids);
        $q = $this->db->select('prp.sysid AS PRPSYSID, prp.names AS PRPNAMES, prp.desc AS PRPDESC')
            ->from('requirements_parameters AS pcar')
            ->join('prime_requirement_parameters AS prp', 'prp.sysid = pcar.reqid')
            ->where_in('pcar.reqid', $req_sysids_arr)
            ->group_by('prp.sysid')->get();
        $res_num = $q->num_rows();

        if ($res_num > 0) {
            foreach ($q->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->PRPSYSID,
                    'text' => $row->PRPNAMES,
                );
            }
        }
        $data['res'] = $res_num;
        $data['input'] = $this->input->post();
        echo json_encode($data);
    }

    function getadditionalrequirements()
    {
        echo $this->cad->get_additional_requirements();
    }

    function addrequirement()
    {
        echo $this->cad->add_requirement();
    }

    function deleterequirement()
    {
        echo $this->cad->delete_requirement();
    }

    function cadcontract($appid)
    {
        $data['appid'] = $appid;

        //@TODO create checking of contract if already generated and uploaded
        // then do not allow to create another pdf instead create message showing that the contract is already created
        // use this function : page_data_notfound_full('Contract is already created please refer to evaluation admin.');
        // if(contract_is_exists) { // <---- QUERY CHECK
        //      page_data_notfound_full('Contract is already created please refer to evaluation admin.');
        // }else{

        $html = $this->load->view('custom/templates/cadcontract', $data, true);

        //echo $html;
        //exit();

        //GET ESSR#
        $appinfo = get_application_details($appid)->info;
        $essr_no = $appinfo->essrno;


        //check if contract file already exist

        $upload_path = FCPATH . 'uploads/attachments/cad/applications/' . str_pad($appid, 6, '0', STR_PAD_LEFT) . '/';

        $filename = 'CONTRACT_' . $essr_no . '.pdf';

        $attachment_id = $this->db->select('acr.sysid')
            ->from('application_customers_requirements AS acr')
            ->join('prime_requirement_parameters AS prp', 'acr.reqid = prp.sysid', 'inner')
            ->join('application_customers_details AS acd', 'acr.appid = acd.sysid', 'inner')
            ->where(array('prp.codes' => 'CONTRACT', 'acd.sysid' => $appid))->get()->row();

        if ($attachment_id) {
            $existing_cont = $this->db->select('sysid , fileurl')
                ->from('application_customers_attachments')
                ->where(array('attachmentid' => $attachment_id->sysid, 'status' => 1))
                ->get()->row();

            if ($existing_cont) {
                $contents = file_get_contents(FCPATH . $existing_cont->fileurl);
                $url = explode('/', $existing_cont->fileurl);
                $filename = end($url);
                //print_r(FCPATH . $existing_cont->fileurl);
                //echo '<br>';
                //print_r($filename);
            } else {
                if (!file_exists($upload_path . $filename)) {

                    // LUCKY WAS HERE 1/20/2020
                    // CREATE DIRECTORY IF NOT EXISTS
                    if (!is_dir($upload_path)) {
                        mkdir($upload_path, 0775, true);
                        if (!is_dir($upload_path)) {
                            mkdir($upload_path, 0775, true);
                        } else {
                            chmod($upload_path, 0777);
                        }
                    } else {
                        //   chmod($upload_path, 0777);
                        if (!is_dir($upload_path)) {
                            mkdir($upload_path, 0775, true);
                        } else {
                            chmod($upload_path, 0777);
                        }
                    }

                    // Load library
                    $this->load->library('pdf');
                    $dompdf = new Dompdf\Dompdf();
                    $dompdf->loadHtml($html);
                    $customPaper = array(0, 0, 610, 910);
                    $dompdf->setPaper($customPaper, 'portrate');
                    $dompdf->render();
                    // Add PDF Document Information
                    $dompdf->add_info('Subject', 'Contract for Electric Current Service');
                    $dompdf->add_info('Author', 'Panay Electric Company, Inc.');
                    $dompdf->add_info('Creator', 'CAD');
                    $dompdf->add_info('Keywords', 'Contract');

                    $output = $dompdf->output();
                    file_put_contents($upload_path . $filename, $output);
                }
                $contents = file_get_contents($upload_path . $filename);
            }
        } else {
            if (!file_exists($upload_path . $filename)) {
                // Load library
                $this->load->library('pdf');
                $dompdf = new Dompdf\Dompdf();
                $dompdf->loadHtml($html);
                $customPaper = array(0, 0, 610, 910);
                $dompdf->setPaper($customPaper, 'portrate');
                $dompdf->render();
                // Add PDF Document Information
                $dompdf->add_info('Subject', 'Contract for Electric Current Service');
                $dompdf->add_info('Author', 'Panay Electric Company, Inc.');
                $dompdf->add_info('Creator', 'CAD');
                $dompdf->add_info('Keywords', 'Contract');


                // LUCKY WAS HERE 1/20/2020
                // CREATE DIRECTORY IF NOT EXISTS
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0775, true);
                    if (!is_dir($upload_path)) {
                        mkdir($upload_path, 0775, true);
                    } else {
                        chmod($upload_path, 0777);
                    }
                } else {
                    //   chmod($upload_path, 0777);
                    if (!is_dir($upload_path)) {
                        mkdir($upload_path, 0775, true);
                    } else {
                        chmod($upload_path, 0777);
                    }
                }


                $output = $dompdf->output();
                file_put_contents($upload_path . $filename, $output);
            }
            $contents = file_get_contents($upload_path . $filename);
        }

        $this->load->helper('download');

        force_download($filename, $contents);
    }

    function getrequirements()
    {
        echo $this->cad->get_requirements_list();
    }

    function getapplicationparam()
    {
        echo $this->cad->get_application_param();
    }

    function addcustomercharges()
    {
        echo $this->cad->add_customer_charges();
    }

    function checkonlineticketstatus()
    {
        echo $this->cad->check_online_ticket_status();
    }

    function submitonlinerowdata()
    {
        echo $this->cad->submit_online_row_data();
    }


    function clearcadtrans()
    {
        $data = array();
        $msg = '';
        $func = 'error';
        $err = array();
        $this->db->query("TRUNCATE TABLE application_customers_charges");
        $this->db->query("TRUNCATE TABLE application_customers_details");
        $this->db->query("TRUNCATE TABLE application_customers_equipments");
        $this->db->query("TRUNCATE TABLE application_customers_exemptions");
        $this->db->query("TRUNCATE TABLE application_customers_gdr_logs");
        $this->db->query("TRUNCATE TABLE application_customers_geodata");
        $this->db->query("TRUNCATE TABLE application_customers_near_meters");
        $this->db->query("TRUNCATE TABLE application_customers_requirements");
        $this->db->query("TRUNCATE TABLE application_customers_sequence");
        $this->db->query("TRUNCATE TABLE application_customers_subscriptions");
        $this->db->query("TRUNCATE TABLE application_encoding_stats");
        $this->db->query("TRUNCATE TABLE application_customer_inspection_logs");
        $this->db->query("TRUNCATE TABLE application_customers_referrals");
        $this->db->query("TRUNCATE TABLE application_customers_referrals_main");
        $this->db->query("TRUNCATE TABLE application_customers_referrals_person");
        $this->db->query("TRUNCATE TABLE application_customers_system_size");
        $this->db->query("TRUNCATE TABLE application_customers_team_assignment");
        $this->db->query("TRUNCATE TABLE application_customers_referrals_trn");

        $err['TRUNC'][] = $this->db->_error_message();
        $flowid = 2;

        $qry_flows = $this->db->select()->from('transaction_request_main')
            ->where(array('flowid' => $flowid))->get();
        if ($qry_flows->num_rows() > 0) {
            foreach ($qry_flows->result() as $row) {

                $data['trnid'][] = $row->sysid;
                $qry_flow_stages = $this->db->select()
                    ->from('transaction_request_main_trails')
                    ->where('trnid', $row->sysid)
                    ->get()->row();
                if ($qry_flow_stages) {
                    $stage_id = $qry_flow_stages->sysid;
                    $data['TRAILID'][] = $stage_id;
                    $this->db->where('trailid', $stage_id);
                    $this->db->delete('transaction_request_trails_logs');
                    $err['LOGS'][] = $this->db->_error_message();

                }

                $this->db->where('trnid', $row->sysid);
                $this->db->delete('transaction_request_main_trails');
                $err['TRAILS'][] = $this->db->_error_message();

            }
            $this->db->where('flowid', $flowid);
            $this->db->delete('transaction_request_main');
            $err['MAIN'][] = $this->db->_error_message();
        }
        $this->db->trans_commit();
        $msg = 'C.A.D. Transactions cleared!';
        $func = 'success!';

        $data['err'] = $err;
        $data['msg'] = $msg;
        $data['func'] = $func;
        echo json_encode($data);
    }

    function getcustomerservices()
    {
        echo $this->cad->get_application_services();
    }

    function selectservicematerials()
    {
        $data = array();
        $query = $this->db->select('a.sysid, c.codes, c.descs')
            ->from('customer_charges AS a')
            ->join('prime_chart_of_accounts AS c', 'a.acctid = c.sysid')
            ->where(array('a.status' => 1))->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->codes . ' - ' . $row->descs,
                );
            }
        }
        echo json_encode($data);
    }

    function addservicefee()
    {
        echo json_encode($this->cad->insert_services_fee());
    }
    function delservicesfee()
    {
        echo $this->cad->del_services_fee();
    }
    function processar()
    {
        echo $this->cad->process_account_receivable();
    }
    function customerslistbasic()
    {
        echo $this->cad->get_customers_lists();
    }
    function getrangecustomerlist()
    {
        echo $this->cad->get_ranged_customers_list();
    }
    function setcookiecustomerlistup()
    {
        echo $this->cad->setcookie_customer_lists_up();
    }
    function updatecustomerinfo()
    {
        echo $this->cad->update_customer_info();
    }
    function deletecookie($mode = '')
    {
        $offset = $_COOKIE['cust_list_offset'];

        echo 'Offset: ' . $offset;

        if ($mode == 'del') {
            setcookie('cust_list_offset', -1, time() + (86400 * 30), "/"); // 86400 = 1 day
        }
    }

    function executeaccomplishment()
    {
        echo $this->cad->execute_accomplishement();
    }
    function getjobtype()
    {
        $data = array();
        $sql = $this->db->select("sysid,names,desc")
            ->from("prime_types_parameter")
            ->where(array('codes' => 'APPJOBTYPE'))->get();
        if ($sql->num_rows() > 0) {
            foreach ($sql->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->names . ' - ' . $row->desc,
                );
            }
        }
        echo json_encode($data);
    }

    function getappmeterconn()
    {
        echo $this->cad->get_applicaton_meter_connections();
    }

    function unreleasemeter()
    {
        echo $this->cad->unrelease_meter();
    }

    function releasethismeter()
    {
        echo $this->cad->release_this_meter();
    }

    function uploadreqdata()
    {
        $data = array();
        $reqid = $this->input->post('reqid');
        $dataid = $this->input->post('appid');
        $filename = '';
        $msg = '';
        $qry = false;
        $func = '';


        if (isset($_FILES["uploadfiles"])) {
            $new_name = $_FILES["uploadfiles"]['name'];

            $file_directory = FCPATH . "uploads/attachments/cad/applications/" . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/";
            //  $file_directory = "net use z:\\\\172.20.224.15cad\\attachedments\\" . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/";

            // ###############################################
            // CREATE DIRECTORY
            $config['upload_path'] = $file_directory;
            $config['allowed_types'] = 'gif|jpg|png|pdf|doc|docx';
            $config['max_size'] = 10000;
            $config['max_width'] = 5000;
            $config['max_height'] = 8000;
            $config['encrypt_name'] = TRUE;
            $config['file_name'] = $new_name;
            $this->load->library('upload', $config);
            $location = 'uploads/attachments/cad/applications/' . str_pad($dataid, 6, "0", STR_PAD_LEFT) . '/' . $new_name;
            // ###############################################
            // CREATE DIRECTORY
            if (!is_dir($file_directory)) {
                mkdir($file_directory, 0777, TRUE);
                chmod($file_directory, 0777);
            } else {
                chmod($file_directory, 0777);
            }
            // ###############################################

            if (!$this->upload->do_upload('uploadfiles')) {
                $msg = array('error' => $this->upload->display_errors());
            } else {
                $msg = array('upload_data' => $this->upload->data());

                $this->db->trans_begin();

                $datenow = date("Y-m-d h:i:s");

                $updatearr = array(
                    'comply' => 1,
                    'complydate' => $datenow,
                    'complyby' => user_id(),
                    'fileurl' => $location
                );
                $this->db->where(array("sysid" => $reqid, "appid" => $dataid, "status" => 1));
                $sql = $this->db->update("application_customers_requirements", $updatearr);
                $data['errorquery'] = $this->db->_error_message();
                if ($this->db->trans_status() == true && $sql) {
                    $this->db->trans_commit();
                    $qry = true;
                    $msg = 'Data has been uploaded.';
                    $func = 'success';
                } else {
                    $this->db->trans_rollback();
                    $msg = 'Failed to upload data.';
                    $func = 'error';
                    $qry = false;
                }
            }
        } else {
            $msg = 'Drop the file again!';
        }


        $data['appid'] = $dataid;
        $data['reqid'] = $reqid;
        $data['loc'] = $location;
        $data['filename'] = $filename;
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;
        echo json_encode($data);
    }

    function removeattachment()
    {
        $data = array();
        $dataid = $this->input->post('dataid');

        $this->db->trans_begin();

        $updatearr = array(
            'comply' => 0,
            'complydate' => null,
            'complyby' => null,
            'fileurl' => null
        );
        $this->db->where(array("sysid" => $dataid));
        $sql = $this->db->update("application_customers_requirements", $updatearr);
        if ($this->db->trans_status() == true && $sql) {
            $this->db->trans_commit();
            $msg = 'Attachment has been removed.';
            $func = 'success';
            $qry = true;
        } else {
            $this->db->trans_status();
            $msg = 'Failed to remove attachement';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function submitcustomeratt()
    {
        $data = array();
        $ids = $this->input->post('ids');
        $attachments = $this->input->post('checkimg');

        $this->db->trans_begin();

        if (empty($attachments)) {
            $this->db->trans_status();
            $msg = 'No attachment selected!';
            $func = 'info';
            $qry = false;
        } else {
            $updatearr = array(
                'comply' => 1
            );
            $this->db->where(array("comply" => 0, "sysid" => $ids));
            $updaterequirements = $this->db->update("application_customers_requirements", $updatearr);

            foreach ($attachments as $img) {
                $insarr = array(
                    'attachmentid' => $ids,
                    'fileurl' => $img,
                    'complydate' => date('Y-m-d h:i:s'),
                    'complyby' => user_id(),
                    'createdby' => user_id(),
                    'updatedby' => user_id()
                );

                $addattachments = $this->db->insert("application_customers_attachments", $insarr);
            }
            if ($this->db->trans_status() == true && $addattachments && $updaterequirements) {
                $this->db->trans_commit();
                $msg = 'Attachment has been added.';
                $func = 'success';
                $qry = true;
            } else {
                $this->db->trans_status();
                $msg = 'Failed to add attachement';
                $func = 'error';
                $qry = false;
            }

        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }
    function deleteattachment()
    {
        $data = array();
        $dataid = $this->input->post('dataid');
        $appid = $this->input->post('appid');
        $data['dataid'] = $dataid;
        $this->db->trans_begin();
        $filecount = 0;
        $updatearr = array(
            'status' => 0
        );
        $this->db->where(array("sysid" => $dataid, "status" => 1));
        $sql = $this->db->update("application_customers_attachments", $updatearr);

        $checkfilecount = $this->db->select("COUNT(sysid) as filecount")->from("application_customers_attachments")
            ->where(array("attachmentid" => $appid, "status" => 1))->get()->row();
        if ($checkfilecount) {
            $filecount = $checkfilecount->filecount;
            if ($filecount == 0) {
                $data['filecount'] = $filecount;
                $data['sysid'] = $appid;
                $applicationarr = array(
                    'comply' => 0
                );
                $this->db->where(array("sysid" => $appid));
                $this->db->update("application_customers_requirements", $applicationarr);
            }
        }

        if ($this->db->trans_status() == true && $sql) {
            $this->db->trans_commit();
            $msg = 'Attachment has been remove.';
            $func = 'success';
            $qry = true;
        } else {
            $this->db->trans_status();
            $msg = 'Failed to remove attachement';
            $func = 'error';
            $qry = false;
        }
        $data['filecount'] = $filecount;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        echo json_encode($data);
    }

    function autoassignrequirements()
    {
        echo $this->cad->auto_assign_requirements();
    }

    function editinfo()
    {
        $input = $this->input->post();
        $inputname = $input['name'];
        $dataid = $input['pk'];
        $val = $input['value'];

        $this->db->trans_begin();
        $sql = '';
        if ($inputname == 'seniorvaliid') {
            $update = array(
                'seniorid' => $val
            );
            $this->db->where(array("sysid" => $dataid));
            $sql = $this->db->update("application_customers_details", $update);

        }
        if ($inputname == 'seniordatefrom') {
            $update = array(
                'seniordatefrom' => $val
            );
            $this->db->where(array("sysid" => $dataid));
            $sql = $this->db->update("application_customers_details", $update);
        }
        if ($inputname == 'seniordateto') {
            $update = array(
                'seniordateto' => $val
            );
            $this->db->where(array("sysid" => $dataid));
            $sql = $this->db->update("application_customers_details", $update);
        }

        if ($inputname == 'essrnoprofile') {
            $update = array(
                'essrno' => $val
            );
            $this->db->where(array("sysid" => $dataid));
            $sql = $this->db->update("application_customers_details", $update);
        }

        if ($this->db->trans_status() == true && $sql) {
            $this->db->trans_commit();
            $qry = true;
        } else {
            $this->db->trans_status();
            $qry = false;
        }

        $data['qry'] = $qry;
        $data['input'] = $this->input->post();
        echo json_encode($data);
    }
    function getbarangays()
    {
        $data = array();
        $distid = $this->input->post('data');
        if ($distid > 0) {
            $this->db->where(array("distid" => $distid));
        }
        $sql = $this->db->select("sysid , texts")->from("address_barangay")->get();
        if ($sql->num_rows() > 0) {
            foreach ($sql->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->texts . ' - ' . ''
                );
            }
        }

        echo json_encode($data);
    }

    function editowner()
    {
        echo $this->cad->edit_owner();
    }

    function dtsubowners()
    {
        echo $this->cad->dt_sub_owners();
    }

    function removesubowner()
    {
        echo $this->cad->remove_subowner();
    }

    function loadcsv()
    {
        echo $this->cad->load_csv();
    }

    function uploadonlineapplication()
    {
        echo $this->cad->upload_online_application();
    }

    function address($type, $id)
    {
        echo address_name($type, $id);
        print_r($this->db->last_query());
    }

    function select2rateclass()
    {
        echo get_rate_class_select();
    }

    function addrequirementlist()
    {
        echo $this->cad->add_requirement_list();
    }

    function sendfinalrequirementlist()
    {
        echo $this->cad->send_final_list_requirements();
    }

    function printrequirements($appid)
    {
        echo $this->cad->print_requirements($appid);
    }

    function printinspectionlist()
    {
        echo $this->cad->print_inspection_list();
    }

    function summaryofcost($id = false)
    {
        $data = array();
        if ($id == false) {
            $id = $this->input->post('appid');

            $data['id'] = $id;

            $html = $this->load->view('custom/templates/soctemplate.php', $data, true);

            $data['html'] = $html;

            echo json_encode($data);
        } else {
            $data['id'] = $id;

            echo $this->load->view('custom/templates/soctemplate.php', $data, true);
        }
    }

    function overrideamt()
    {
        echo $this->cad->override_amt();
    }
    function getrefdetails()
    {
        echo $this->cad->get_referrals_details();
    }
    function getassessmentdetails()
    {
        echo $this->cad->get_assessment_details();
    }
    function getproposalpdf($id = false)
    {
        $proposal = $this->cad->get_proposal_pdf($id);


        $html = $proposal->html;
        $filename = 'PROPOSAL | ' . $proposal->name;

        $this->load->library('pdf');
        $dompdf = new Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $customPaper = array(0, 0, 615, 930);
        $dompdf->setPaper('letter', 'portrate');
        $dompdf->render();
        // Add PDF Document Information
        $dompdf->add_info('Subject', 'PROPOSAL | ' . $proposal->name);
        $dompdf->add_info('Author', 'PA Energy, Inc.');
        $dompdf->add_info('Creator', 'PAE');
        $dompdf->add_info('Keywords', 'PROPOSAL');
        $dompdf->stream($filename, array('Attachment' => false));
    }

    function aurepform()
    {
        $appid = $this->input->post('appid');
        $type = $this->input->post('type'); // edit or add
        $data = array();




        if ($type == 'edit') {
            $appinfo = get_application_details();
        }
    }

    function getproposal($id = false)
    {
        $id = ($id) ? $id : $this->input->post('id');
        $prop = $this->cad->get_proposal_pdf($id);
        echo json_encode($prop);
    }

    function getproposallayout($id = false)
    {
        $id = ($id) ? $id : $this->input->post('id');
        $prop = $this->cad->get_proposal_pdf($id);
        echo $prop->html;
    }

    function select2du()
    {
        echo $this->cad->select2_du();
    }

    function updatedistutility()
    {
        echo $this->cad->update_dist_utility();
    }

    function saveproposedsystemrates()
    {
        echo $this->cad->save_proposed_system_rates();
    }

    function finalizedocument()
    {
        echo $this->cad->finalize_document();
    }

    function deletedocument()
    {
        echo $this->cad->delete_document();
    }

    function updatereplaceowner()
    {
        echo $this->cad->update_replace_owner();
    }

    function initownerinfo()
    {
        $id = $this->input->post('id');
        $info = get_application_details($id);
        echo json_encode($info->info);
    }

    function printtest($id)
    {
        $proposal = $this->cad->get_proposal_pdf($id);
        $png = ($proposal) ? rehash_pdf_img($proposal->html) : array();

        echo "<pre>";
        print_r($png);
        echo "</pre>";


    }

    function retrieveapplicationinfo()
    {
        echo $this->cad->retrieve_application_info();
    }

    function degdec()
    {
        echo "<pre>";
        print_r(convert_degrees_to_decimal('10deg43\'08.6"N 122deg34\'29.1"E'));
        echo "</pre>";

    }

    function getapplicationbasicinfo()
    {
        echo $this->cad->get_application_basic_info();
    }

    function updateapplicationownerinfo()
    {
        echo $this->cad->update_application_owner_info();
    }

    function uploadrequirements()
    {
        echo $this->cad->upload_requirements();
    }

    function extractexceltssr($dataid = false, $print = false)
    {
        echo $this->cad->extract_excel_tssr($dataid, $print);
    }

    function exceltest()
    {
        $this->load->library('excel');
        $xls = PHPExcel_IOFactory::load(FCPATH . 'uploads/temp/tssr.xlsx');
        $xls->setActiveSheetIndex(0);


        //Getting Imges
        $drawings = $xls->getActiveSheet()->getDrawingCollection();
        $i = 0;
        $image_fields = [];
        foreach ($drawings as $drawing) {
            if ($drawing instanceof PHPExcel_Worksheet_Drawing) {
                $zipReader = fopen($drawing->getPath(), 'r');
                $imageContents = '';
                while (!feof($zipReader)) {
                    $imageContents .= fread($zipReader, 1024);
                }
                fclose($zipReader);
                $extension = $drawing->getExtension();
                $myFileName = 'questions/questions_' . ++$i . time() . '.' . $extension;
                $image_fields[$drawing->getCoordinates()] = '<img src="data:image/jpeg;base64,' . base64_encode($imageContents) . '">';
                //$path = Storage::put($myFileName, $imageContents);
                //$image_fields[$drawing->getCoordinates()] = '<img src="'.asset('storage/'.$myFileName).'">';
            }
            if ($drawing instanceof PHPExcel_Worksheet_MemoryDrawing) {
                ob_start();
                call_user_func(
                    $drawing->getRenderingFunction(),
                    $drawing->getImageResource()
                );
                $imageContents = ob_get_contents();
                ob_end_clean();

                $image_fields[$drawing->getCoordinates()] = '<img src="data:image/jpeg;base64,' . base64_encode($imageContents) . '"><br>' . $drawing->getCoordinates() . '<br>';
            }
        }

        //Finding and fetching data from cell
        for ($row = 1; $row <= 500; $row++) {
            $val = $xls->getActiveSheet()->getCell('A' . $row)->getValue();
            if ($val == 'LOCATION OF TAPPING POINT') {
                $tp = array(
                    'pictures' => $xls->getActiveSheet()->getCell('B' . ($row + 1))->getValue(),
                );

                for ($imgrow = $row; $imgrow <= 33; $imgrow++) {
                    $columns = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');
                    foreach ($columns as $col) {
                        $cellVal = $image_fields[$col . $row];
                        if ($cellVal != '') {
                            $tp['img'][] = $cellVal;
                        }
                    }
                }
            }
        }
    }

    function getpdfimages()
    {
        $file = FCPATH . 'uploads/temp/cca.pdf';
        $pdfimages = FCPATH . 'exec/bin/pdfimages';
        $target = FCPATH . 'uploads/temp/cca/';
        exec($pdfimages . ' -j ' . $file . ' ' . $target . 'images');
        $extracted = directory_map($target, FALSE, TRUE);
        if ($extracted && count($extracted) > 0) {
            foreach ($extracted as $sub => $file) {
                if (is_array($file)) {

                }
            }
        }
    }

    function getdoctype()
    {
        echo $this->cad->get_doctype();
    }

    function getdocumentpreview()
    {

        if (user_info()->sysid == 1) {
            echo print_r($this->cad->get_document_preview());
        }

        $layout = $this->cad->get_document_layout();
        $papersize = $layout->papersize;
        $html = $layout->html;
        $title = $layout->title;
        $filename = $layout->filename;

        $this->load->library('pdf');
        $dompdf = new Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $customPaper = ($papersize && $papersize != '') ? $papersize : 'letter';

        $dompdf->setPaper($customPaper, 'portrate');
        $dompdf->render();
        // Add PDF Document Information
        $dompdf->add_info('Subject', $title);
        $dompdf->add_info('Author', user_info()->username);
        $dompdf->add_info('Creator', 'ITD');
        $dompdf->add_info('Keywords', $title);
        $dompdf->stream($filename, array('Attachment' => false));
    }

    function getdocumentlayout($dataid = false, $doctype = false)
    {
        $data = $this->cad->get_document_layout($dataid, $doctype);
        if ($dataid && $doctype) {
            echo $data->html;
        } else {
            echo json_encode($data);
        }

    }

    function savecustomerplan()
    {
        echo $this->cad->save_customer_plan();
    }
    function select2planduration()
    {
        $data = array();
        $dataid = $this->input->post('data');
        $amount_lookup = $this->db->select('sg.appid,sg.desc as sizename,p.outright,p.twoyrs,p.threeyrs,p.fiveyrs,p.tenyrs,p.monthlyave,p.summerave,p.buildtime')
            ->from('customer_system_group AS sg')
            ->join('proposal_nonstandard_system_rates AS p', 'sg.sysid = p.systemsizeid AND p.`status` = 1', 'left')
            ->where(array('sg.appid' => $dataid, 'sg.status' => 1))
            ->get()->row();

        if ($amount_lookup) {
            $id = 0;
            foreach ($amount_lookup as $key => $value) {
                if ($key == 'outright') {
                    $data['list'][] = array('id' => 0, 'text' => 'Outright');
                }

                if (strpos($key, 'yrs') && $value > 0) {
                    $yrs = str_replace('yrs', '', $key);
                    switch (trim($yrs)) {
                        case 'one':
                            $id = 1;
                            break;
                        case 'two':
                            $id = 2;
                            break;
                        case 'three':
                            $id = 3;
                            break;
                        case 'four':
                            $id = 4;
                            break;
                        case 'five':
                            $id = 5;
                            break;
                        case 'six':
                            $id = 6;
                            break;
                        case 'seven':
                            $id = 7;
                            break;
                        case 'eigh':
                            $id = 8;
                            break;
                        case 'nine':
                            $id = 9;
                            break;
                        case 'ten':
                            $id = 10;
                            break;
                    }

                    $data['list'][] = array('id' => $id, 'text' => $id . ' Years');
                }
            }
        }

        /*$data['list'] = array(
            array('id' => 0,'text' => 'Outright'),
            array('id' => 2,'text' => '2 Years'),
            array('id' => 5,'text' => '5 Years'),
            array('id' => 10,'text' => '10 Years'),
        );*/

        echo json_encode($data);
    }

    function applicationinfo($id)
    {
        $info = application_info($id);

        echo "<pre>";
        print_r($info);
        echo "</pre>";

    }


    function createproposaldraft()
    {
        echo $this->cad->create_proposal_draft();
    }

    function getsignedproposallist()
    {
        echo $this->cad->get_signed_proposal_list();
    }

    function updateapplicationcustomerinfo()
    {
        echo $this->cad->update_application_customer_info();
    }

    function saveextractedtssrdata()
    {
        echo $this->cad->save_extracted_tssr_data_ii();
    }

    function getselectedplanamount()
    {
        echo $this->cad->get_selected_plan_amount();
    }

    function select2apptransactions()
    {
        $stageid = $this->input->post('stageid');
        if (!super_admin()) {
            $stageids = explode(',', $this->input->post('data')) ?? false;
        }

        $data = array();

        $stages = array(
            92 => array(
                'url' => base_url() . 'inspection/uploadsurveypics',
                'location' => get_stage_specific(92)->desc,
                'description' => get_stage_specific(92)->desc
            ),
            93 => array(
                'url' => base_url() . 'inspection/uploadsurveypics',
                'location' => get_stage_specific(92)->desc,
                'description' => get_stage_specific(93)->desc
            ),
            95 => array(
                'url' => base_url() . 'cad/uploadrequirements',
                'location' => get_stage_specific(95)->desc,
                'description' => get_stage_specific(95)->desc
            ),
            100 => array(
                'url' => base_url() . 'cad/uploadrequirements',
                'location' => get_stage_specific(95)->desc,
                'description' => get_stage_specific(100)->desc
            ),
        );

        if ($stageid !== false) {
            if ($stageid > 0) {
                $data = $stages[$stageid];
            } else {
                $data = array(
                    'url' => base_url() . 'admin/uploadapplicationfiles',
                    'location' => '',
                    'description' => ''
                );
            }
        } else {
            if (isset($stageids)) {
                foreach ($stageids as $stage) {
                    $data['list'][] = array(
                        'id' => $stage,
                        'text' => $stages[$stage]['description']
                    );
                }
            } else {
                foreach ($stages as $stage => $set) {
                    $data['list'][] = array(
                        'id' => $stage,
                        'text' => $set['description']
                    );
                }
            }
        }

        echo json_encode($data);
    }

    function getassigendso()
    {
        echo $this->cad->get_assigend_so();
    }

    function select2salesofficer()
    {
        echo $this->cad->select2_sales_officer();
    }

    function assignsalesofficer()
    {
        echo $this->cad->assign_sales_officer();
    }

    function deletesalesofficer()
    {
        echo $this->cad->delete_sales_officer();
    }

    function appinfo($appid)
    {
        $appdetails = application_info($appid);
        echo "<pre>";
        print_r($appdetails);
        echo "</pre>";

    }

    function savetempinfo()
    {
        echo $this->cad->save_temp_info();
    }

    function getapplicationdocumentslist()
    {
        echo $this->cad->get_application_documents_list();
    }

    function savetempdocs()
    {
        echo $this->cad->save_temp_docs();
    }

    function deletetempdoc()
    {
        echo $this->cad->delete_temp_doc();
    }

    function addnewdocument()
    {
        echo $this->cad->add_new_document();
    }

    function select2doctypes()
    {
        echo $this->cad->select2_doctypes();
    }

    function select2requirecodes()
    {
        echo $this->cad->select2_requirecodes();
    }

    function processcustomerapplication()
    {
        echo $this->cad->process_customer_application();
    }

    function cancelcustomerapplication()
    {
        echo $this->cad->cancel_customer_application();
    }

    function getcancelledapplications()
    {
        echo $this->cad->get_cancelled_applications();
    }

}
