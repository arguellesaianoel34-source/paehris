<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// ############################################
// AUTHOR : LUCKY JOHN FADERON - SE
Class Model_admin extends CI_Model {

    function get_user_login_info($sessid) {
        $query = $this->db->select('su.sysid, su.username, su.type, su.idletime, suim.firstname, suim.lastname, suim.middlename, suim.gender')
            ->from('prime_system_users su')
            ->join('prime_system_users_info_main AS suim', 'suim.userid = su.sysid', 'left')
            ->join('prime_system_users_info_img AS suii', 'suii.userid = su.sysid', 'left')
            ->where(array('suim.status' => 1, 'su.sysid' => $sessid))
            ->group_by('su.sysid, su.username, su.type, su.idletime, suim.firstname, suim.lastname, suim.middlename, suim.gender')->get()->row();
        return ( $query ) ? $query : false;
    }

    function get_user_login_access() {
        return get_users_info_navigation_ids();
    }

    public function get_users_select2() {
        $term = $this->input->post('term');
        $this->db->like('firstname', $term);
        $q = $this->db->select('sysid, firstname')->from('prime_system_users')->get();
        $data = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data['list'][] = array('id' => $row->sysid, 'text' => $row->firstname);
            }
        }
        $data['input'] = $this->input->post();
        return json_encode($data);
    }
    public function get_services_select2() {
        $term = $this->input->post('term');
        $this->db->like('codes', $term);
        $this->db->like('descs', $term);
        $q = $this->db->select()->from('prime_chart_of_accounts')
            ->where(array('types' => 1, 'status' => 1, 'groups' => 3))
            ->get();
        $data = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data['list'][] = array('id' => $row->sysid, 'text' => $row->codes . ' - ' .$row->descs);
            }
        }
        $data['input'] = $this->input->post();
        return json_encode($data);
    }

    function get_user_login_access_navigation() {
        return get_users_info_navigation_ids();
    }

    function in_array_r($needle, $haystack, $strict = false) {
        foreach ($haystack as $item) {
            if (( $strict ? $item === $needle : $item == $needle ) || ( is_array($item) && $this->in_array_r($needle, $item, $strict) )) {
                return true;
            }
        }
        return false;
    }

    function select_modules() {
        $query = $this->db->select('sysid, code, name, desc, icon, type')->from('prime_module_main')->order_by('sorting', 'asc')->get();
        return ( $query->num_rows() > 0 ) ? $query->result() : false;
    }

    function select_modules_navigation($mid) {
        $query = $this->db->select('sysid, code, name, desc, icon, htmlclass, pagefile, type, hashcode')->from('prime_module_navigations_main')->where(array('parent' => $mid))->order_by('sorting', 'asc')->get();
        return ( $query->num_rows() > 0 ) ? $query->result() : false;
    }

    function check_modules_navigation($mid) {
        $query = $this->db->select('sysid')->from('prime_module_navigations_main')->where(array('parent' => $mid))->order_by('sorting', 'asc')->get();
        return ( $query->num_rows() > 0 ) ? true : false;
    }

    function array_module_navigations() {
        $modules = array();
        $navid = get_users_info_navigation_ids();
        if($navid) {
            foreach ($navid as $row) {
                $modules[] = $this->get_navigation_specific_navhash($row);
            }
        }
        return $modules;
    }

    function get_user_dashboard_access($hash) {
        if(get_users_roles_matrix_id_arr()) {
            $role_arr = get_users_roles_matrix_id_arr();

            $navid_public = $this->db->select('n.sysid')
                ->from('prime_module_navigations_main AS n')
                ->join('prime_module_navigations_public As np', 'np.navid = n.sysid')
                ->where(array('n.hashcode' => $hash, 'n.status' => 1))
                ->get()->row();
            if($navid_public) {
                $navid = $navid_public;
            }else {
                $navid = $this->db->select('n.sysid')
                    ->from('prime_module_navigations_main AS n')
                    ->join('prime_system_roles_dashboards AS d', 'd.navids = n.sysid AND d.status = 1')
                    ->where(array('n.hashcode' => $hash, 'n.status' => 1))
                    ->where_in('d.roleid', $role_arr)
                    ->get()->row();
            }
            return $navid;
        }else{
            return true;
        }
    }

    function get_navigation_general_parent($hash) {
        $query = $this->db->select('sysid')->from('prime_module_navigations_main')->where('hashcode', $hash)->get()->row();
        return ( $query ) ? $this->get_navigation_general_parent_fn($query->sysid) : false;
    }

    function get_navigation_general_parent_fn($id) {
        $query = $this->db->select('sysid, parent AS PARENT')->from('prime_module_navigations_main')->where('sysid', $id)->get()->row();

        if($query) {
            $parent = $query->PARENT;
            // GET PARENT IF ZERO
            $query_parent = $this->db->select('parent AS PARENT')->from('prime_module_navigations_main')->where('sysid', $parent)->get()->row();
            if ($query_parent->PARENT == 0) {
                return $parent;
            } else {
                return $this->get_navigation_general_parent_fn($parent);
            }
        }else{
            return false;
        }
    }

    function get_navigation_details($id) {
        $query = $this->db->select()
            ->from('prime_module_navigations_main')
            ->where(array('status' => 1, 'sysid' => $id))
            ->get()->row();
        return ( $query ) ? $query : false;
    }

    function get_navigation_specific_navhash($id) {
        $query = $this->db->select('hashcode')->from('prime_module_navigations_main')->where(array('status' => 1, 'sysid' => $id))->get()->row();
        return ( $query ) ? $query : false;
    }

    function get_navigation_specific_details($hash) {
        $query = $this->db->select('sysid, name AS pname, parent AS PARENT, desc, pagefile, icon, htmlclass, hashcode')
            ->from('prime_module_navigations_main')
            ->where(array('status' => 1, 'hashcode' => $hash))
            ->get()
            ->row();
        return ( $query ) ? $query : false;
    }

    function get_active_navigation_specific_details($hash) {
        $query = $this->db->select('nm.sysid AS pageid, nm.name AS pname, nm.desc, nm.pagefile, mm.sysid AS moduleid, COUNT(fms.levels) AS levels')->from('prime_module_navigations_main nm')->join('prime_module_main mm', 'mm.sysid = nm.parent')->join('prime_transaction_flow_main_stages fms', 'fms.moduleid = nm.sysid', 'left')->join('prime_transaction_flow_main fm', 'fm.sysid = fms.flowid', 'left')->where(array('nm.status' => 1, 'nm.hashcode' => $hash))->get()->row();
        return ( $query ) ? $query : false;
    }

    function init_navigation_info($hash) {
        if (!empty($hash)) {
            $query = $this->db->select('sysid, name AS pname, desc, pagefile')->from('prime_module_navigations_main')->where(array('status' => 1, 'hashcode' => $hash))->get()->row();
            return ( $query ) ? 'active' : '';
        } else {
            return '';
        }
    }

    function init_navigation_module_active_link($hash, $moduleid) {
        if (!empty($hash)) {
            $query = $this->db->select('name AS pname, desc, pagefile')->from('prime_module_navigations_main')->where(array('status' => 1, 'hashcode' => $hash, 'parent' => $moduleid))->get()->row();
            return ( $query ) ? 'active' : '';
        } else {
            return '';
        }
    }

    function init_navigation_active_link($hash, $navid) {
        if (!empty($hash)) {
            $query = $this->db->select('sysid')->from('prime_module_navigations_main')->where(array('status' => 1, 'hashcode' => $hash, 'sysid' => $navid))->get()->row();
            return ( $query ) ? 'active open' : '';
        } else {
            return '';
        }
    }

    function init_navigation_open_sub($hash, $parent) {
        $data = array();
        $class = '';
        $mode = '';
        if (!empty($hash)) {
            $query = $this->db->select('sysid')->from('prime_module_navigations_main')->where(array('status' => 1, 'hashcode' => $hash, 'parent' => $parent))->get()->row();
            if ($query) {
                $class = 'active open';
                $mode = 'open';
            } else {
                $query = $this->db->select('sysid')->from('prime_module_navigations_main')->where(array('status' => 1, 'hashcode' => $hash))->get()->row();
                if ($query) {
                    $int = $this->init_navigation_open_sub_fn($query->sysid);
                    if ($int == $parent) {
                        $class = 'active open';
                        $mode = 'open';
                    }
                }
            }
        }
        $data['class'] = $class;
        $data['mode'] = $mode;
        return (object) $data;
    }

    function init_navigation_open_sub_fn($id) {
        $query = $this->db->select('sysid, parent AS PARENT')->from('prime_module_navigations_main')->where('sysid', $id)->get()->row();
        $parent = $query->PARENT;
        // GET PARENT IF ZERO
        if($parent!=0) {
            $query_parent = $this->db->select('parent AS PARENT')->from('prime_module_navigations_main')->where('sysid', $parent)->get()->row();
            if ( $query_parent && $query_parent->PARENT == 0 ) {
                return $parent;
            } else {
                return $this->get_navigation_general_parent_fn($parent);
            }
        }else{
            return $parent;
        }
    }

    function get_module_flow_start($moduleid) {
        $query = $this->db->select("pt_fm_s.sysid AS stageid")->from('prime_transaction_flow_main_stages pt_fm_s')->where(array('pt_fm_s.moduleid' => $moduleid))->order_by('pt_fm_s.levels', 'asc')->get()->row();
        return ( $query ) ? $query->stageid : false;
    }

    function insert_asset_data($assetdata) {
        return $this->db->insert('transaction_request_main', $trndata);
    }
    function save_manual_earnings(){
        $data = array();
        $gross = $this->input->post('gross');
        $tax = $this->input->post('tax');
        $deduction = $this->input->post('deduction');
        $typesid = $this->input->post('typesid');
        $empid = $this->input->post('empid');

        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $paytype = $this->input->post('paytype');


        $this->db->trans_begin();

        $checkforexistingvalue = $this->db->select("sysid")->from("payroll_manual_earnings")
            ->where(array("typesid" => $typesid , "empid" => $empid , "status" => 307,"month" => $month , "year" => $year , "paytype" => $paytype))
            ->get()->row();
        if($checkforexistingvalue){
            $updatearr = array(
                'status' => 0,
                'updatedby' => user_id()
            );
            $this->db->where(array("sysid" => $checkforexistingvalue->sysid));
            $this->db->update("payroll_manual_earnings" , $updatearr);
        }
        $insarr = array(
            'typesid' => $typesid,
            'empid' => $empid,
            'gross' => $gross,
            'tax' => $tax,
            'deduction' => $deduction,
            'month' => $month,
            'year' => $year,
            'paytype' => $paytype,
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 307
        );
        $sql = $this->db->insert("payroll_manual_earnings" , $insarr);
        $data['insert'] = $this->db->last_query();

        if($this->db->trans_status() == true && $sql){
            $this->db->trans_commit();
            $msg = 'Transaction has been saved.';
            $func = 'success';
            $qry = true;
        }else{
            $this->db->trans_status();
            $msg = 'Failed to save transaction';
            $func = 'error';
            $qry = false;
        }
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        return json_encode($data);
    }

    function populate_requirement_list() {
        $data = array();

        // $info = get_application_details($dataid);
        $dataid = $this->input->post('dataid');
        $origin = $this->input->post('origin');
        $sql = $this->db->select("sysid, reqid, appid, comply, status")
            ->from("application_customers_requirements")
            ->where(array('appid' => $dataid, 'status' => 1))
            ->get();
        $num_rows = $sql->num_rows();
        if($num_rows>0) {
            $num =1;
            $stat = '<span class="stat label label-danger"><i class="fa fa-times"></i></span>';
            foreach ($sql->result() as $row) {

                $control = '';
                if($row->reqid == 8){
                    $sqlcheck = $this->db->select("(SUM(totalamt) + SUM(franchisetax)) AS totalamt")
                        ->from("transaction_payments_logs")
                        ->where(array("payforacctno" => 163 , "dataid"=> $dataid , "moduleid" => $origin, 'status' => 1))
                        ->get()->row();

                    if($sqlcheck && $sqlcheck->totalamt > 0){
                        $stat= '<span class="stat label label-success"><i class="fa fa-check"></i></span>';
                    }else{
                        $stat= '<span class="stat label label-danger"><i class="fa fa-times"></i></span>';
                    }
                }else{
                    if($row->comply == 1){
                        $stat= '<span class="stat label label-success"><i class="fa fa-check"></i></span>';
                        $location = '';
                        $control = '<a  href="#form_view_cad_attachments" data-toggle="ajax-modal" data-view="'.$dataid.'" data-arr="'.$row->sysid.'" data-title="'.get_requirement_name($row->reqid)->names.'" data-id="'.$row->sysid.'"  class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>';
                    }else if($row->comply == 0){
                        $stat= '<span class="stat label label-danger"><i class="fa fa-times"></i></span>';
                        // $control = '<button id="assignfilebtn" data-title="'.get_requirement_name($row->reqid)->names.'" data-id="'.$row->sysid.'"  class="assignfilebtn btn btn-default btn-xs"><i class="fa fa-search"></i>Assign File</button>';
                        // $control = '<a  href="#form_assignfile" data-toggle="ajax-modal" data-view="'.$dataid.'" data-arr="'.$row->sysid.'" data-title="'.get_requirement_name($row->reqid)->names.'" data-id="'.$row->sysid.'"  class="assignfilebtn btn btn-default btn-xs"><i class="fa fa-search"></i>Assign File</a>';
                        $control = '<a  href="#form_assignfile" data-toggle="ajax-modal" data-view="'.$dataid.'" data-arr="'.$row->sysid.'" data-title="'.get_requirement_name($row->reqid)->names.'" data-id="'.$row->sysid.'"  class="btn btn-default btn-xs"><i class="fa fa-search"></i>Assign File</a>';

                    }
                }
                $data['requirementslist'][] = array(
                    'num' => $num++,
                    'requirements' => get_requirement_name($row->reqid)->names,
                    'comply' => $stat,
                    'control' => $control
                );
            }
        }
        return json_encode($data);
    }

    private function get_all_files($data, &$file_paths) {
        if (is_array($data) || is_object($data)) {
            foreach ($data as $value) {
                if (is_string($value)) {
                    $file_paths[] = $value;
                } elseif (is_array($value) || is_object($value)) {
                    $this->get_all_files($value, $file_paths);
                }
            }
        }
    }

    function dt_docs_list() {
        $this->load->helper('directory');
        $this->load->helper('file');
        $file_directory = './uploads/attachments/' . $this->input->post('type') . '/' . $this->input->post('id') . '/';
        $file_url = base_url() . 'uploads/attachments/' . $this->input->post('type') . '/' . $this->input->post('id') . '/';

        $map = directory_map($file_directory, FALSE, TRUE);
        $files = array();
        
        $file_paths = array();
        if (is_array($map)) {
            $this->get_all_files($map, $file_paths);
        }

        if (!empty($file_paths)) {
            $count = 0;
            foreach ($file_paths as $filename) {
                if(empty($filename)) continue;

                $control = '';
                $count++;
                $icon = '';
                //$icon = draw_file_icon(basename($filename));
                if (@is_array(getimagesize($file_url . $filename))) {
                    $link = '<a class="btn btn-primary btn-sm preview" href="' . $file_url . $filename . '"><i class="icon-magnifier"></i></a>';
                    $target = '';
                } else {
                    $link = '<a href="'.$file_url . $filename.'" class="btn btn-primary btn-sm preview iframe" target="_blank"><i class="icon-magnifier"></i></a>';
                    $target = 'target="_blank"';
                }
                $control .= '<div class="btn-group center" id="item_controls" style="width: 80px !important;">';
                $control .= '<a href="' . $file_url . $filename . '" class="btn btn-sm btn-primary inline preview" id="btn_view_item" '.$target.'><i class="fa fa-search"></i> </a>';
                $control .= '<a class="btn btn-danger btn-sm delete" href="#" data-file="' . $filename . '"><i class="fa fa-trash-o"></i></a>';
                $control .= '</div>';

                $files[] = array(
                    $count,
                    $icon . ' ' . $filename,
                    $control
                );
            }
        }

        $results = array(
            'draw' => intval($this->input->post('draw')),
            'recordsTotal' => count($files),
            'recordsFiltered' => count($files),
            'data' => $files
        );

        return json_encode($results);
    }

    function delete_doc_list_file() {
        $file = $this->input->post('file');
        $fullpath = FCPATH.'uploads/attachments/'.$file;

        $data = array();
        $qry = false;
        $msg = '';
        $func = '';
        $title = '';

        if (unlink($fullpath)) {
            $qry = true;
            $msg = 'File has been deleted successfully!';
            $func = 'success';
            $title = 'Deleted!';
        } else {
            $msg = 'Failed to delete file!';
            $func = 'error';
            $title = 'Fail!!!';
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }

    function view_doc_list_pdf_file() {
        $file = $this->input->post('file');
        $path = str_replace(base_url(),'',$file);
        $fullpath = FCPATH.$path;
        $this->load->helper('file');

        /*$pdf = file_get_contents($fullpath);
        header('Content-Type: application/pdf');
        header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
        header('Pragma: public');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Content-Length: '.strlen($pdf));
        header('Content-Disposition: inline; filename="'.basename($fullpath).'";');
        ob_clean();
        flush();
        echo $pdf;*/

        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename='.basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fullpath));
        readfile($fullpath);
        exit;
    }

    function lookup_docs_otp() {
        $data = array();
        $dataid = $this->input->post('id');
        $doctype = $this->input->post('doctype');

        $docid = false;
        $otpid = false;

        $docs_qry = $this->db->select('sysid')
            ->from('prime_documents_main')
            ->where(array('dataid' => $dataid, 'doctype' => $doctype, 'status' => 1))
            ->get()->row();

        if ($docs_qry) {
            $data = $this->input->post();
            $docid = $docs_qry->sysid;

            $otp_qry = $this->db->select('sysid')
                ->from('prime_documents_signature_otp')
                ->where(array('docid' => $docs_qry->sysid, 'status' => 1))
                ->get()->row();

            if ($otp_qry) {
                $otpid = $otp_qry->sysid;
            }
        }

        $data['docid'] = $docid;
        $data['otpid'] = $otpid;

        return json_encode($data);
    }

    function generate_docs_otp() {
        $data = array();
        $dataid = $this->input->post('id');
        $docid = $this->input->post('docid');
        $existing = array();

        $otp_qry = $this->db->select('otpvalue')
            ->from('prime_documents_signature_otp')
            ->where(array('status !=' => 0))
            ->get();

        if ($otp_qry->num_rows() > 0) {
            foreach ($otp_qry->result() as $otp) {
                $existing[] = $otp->otpvalue;
            }
        }

        

        return json_encode($data);
    }

    function sign_document() {
        $data = array();
        $id = $this->input->post('id');
        $doctype = $this->input->post('doctype');

        $msg = '';
        $title = '';
        $func = '';
        $qry = false;
        $name = '';

        $signature = $this->db->select('imgdata')
            ->from('prime_user_signature')
            ->where(array('userid' => user_id(), 'status' => 1))
            ->get()->row();

        if ($signature) {
            $find_doc = $this->db->select('d.sysid,d.doctype,t.names,t.desc,d.signed')
                ->from('prime_documents_main as d')
                ->join('prime_types_parameter as t', 'd.doctype = t.sysid', 'left')
                ->where(array('d.status' => 1, 'dataid' => $id, 'doctype' => $doctype))
                ->get()->row();

            if ($find_doc) {
                $name = strtolower($find_doc->names);
                $data = (array)$find_doc;
                if ($this->db->update('prime_documents_main', array('signed' => 1), array('sysid' => $find_doc->sysid))) {
                    $msg = 'Your signature has been applied in ' . $find_doc->names . '.';
                    $func = 'success';
                    $title = 'Signed!';
                    $qry = true;
                } else {
                    $msg = 'Failed to apply signature in ' . $find_doc->names . '.';
                    $func = 'error';
                    $title = 'Failed!';
                    $qry = false;
                }
            }
            $data['signature'] = true;
        } else {
            $data['signature'] = false;
            $msg = 'You have requested to apply your signature. But, no sample has been found. Kindly update your signature in your account profile.';
            $func = 'warning';
            $title = 'Signature not found!';
            $qry = false;
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        $data['qry'] = $qry;
        $data['name'] = $name;

        return json_encode($data);
    }

    function add_trn_comment() {
        $data = array();
        $types = $this->input->post('types');
        $moduleid = $this->input->post('moduleid');
        $dataid = $this->input->post('dataid');
        $stageid = $this->input->post('stageid');
        $quotedid = $this->input->post('quotedid');
        $messages = $this->input->post('messages');
        $qry = false;

        $this->db->trans_begin();

        $comment_arr = array(
            'types' => $types,
            'moduleid' => $moduleid,
            'dataid' => $dataid,
            'stageid' => $stageid,
            'messages' => $messages,
            'userid' => user_id()
        );
        $add_comment = insert_db($this->db,'comments',$comment_arr);
        if ($add_comment->qry) {
            $uniqueid = substr(md5(time()),-8).str_pad($add_comment->insert_id,2,'0',STR_PAD_LEFT);
            $reply = '<span id="reply_btn" title="Reply..." class="hidden"><i id="reply_comment" data-id="'.$add_comment->insert_id.'" class="fa fa-reply fa-sm"></i></span>';
            if ($quotedid) {
                $add_reply = insert_db($this->db,'comment_reply',array('quotedid' => $quotedid,'replyid' => $add_comment->insert_id));
                if ($add_reply->qry) {
                    $qry = true;
                    $this->db->trans_commit();
                    $get_quoted_message = $this->db->select('userid,messages,datecreated')
                        ->from('comments')
                        ->where(array('sysid' => $quotedid,'status' => 1))
                        ->get()->row();

                    if ($get_quoted_message) {
                        $quoted_uid = substr(md5(strtotime($get_quoted_message->datecreated)),-8).str_pad($quotedid,2,'0',STR_PAD_LEFT);
                        if ($get_quoted_message->userid == user_id()) {
                            $name = 'yourself';
                        } else {
                            $user = user_info($get_quoted_message->userid);
                            $name = ($user) ? ucfirst($user->firstname.' '.$user->lastname) : 'N/A';
                        }
                        $new_comment = '<div id="comment_row" class="col-md-12 '.$uniqueid.'">';
                        $new_comment .= '<div class="comment-you">';
                        $new_comment .= '<span class="bold" id="commenter">You</span> <span>replied to '.$name.'.</span>';
                        $new_comment .= '<div class="quoted-content" href=".'.$quoted_uid.'">';
                        $new_comment .= '<p>' . $get_quoted_message->messages . '<br></p>';
                        $new_comment .= '</div>';
                        $new_comment .= '<div class="comment-content">';
                        $new_comment .= '<p>' . $messages . '</p>';
                        $new_comment .= '<span class="font-xs bold">' . date('Y-m-d g:i A', time()) . '</span> <span class="pull-right">'.$reply.'<i id="delete_comment" data-id="'.$add_comment->insert_id.'" class="fa fa-trash text-danger"></i></span>';
                        $new_comment .= '</div>';
                        $new_comment .= '</div>';
                        $new_comment .= '</div>';
                        $data['newcomment'] = $new_comment;
                        $comment_qry = $this->db->select('sysid,userid,messages,datecreated')
                            ->from('comments')
                            ->where(array(
                                'sysid' => $add_comment->insert_id
                            ))->get()->row();

                        $data['comment'] = $comment_qry;
                    }
                } else {
                    $this->db->trans_rollback();
                    $qry = false;
                }
            } else {
                $this->db->trans_commit();
                $qry = true;
                $new_comment = '<div id="comment_row" class="col-md-12 '.$uniqueid.'">';
                $new_comment .= '<div class="comment-you">';
                $new_comment .= '<span class="bold">You</span>';
                $new_comment .= '<div class="comment-content">';
                $new_comment .= '<p>' . $messages . '</p>';
                $new_comment .= '<span class="font-xs bold">' . date('Y-m-d g:i A', time()) . '</span> <span class="pull-right">'.$reply.'<i id="delete_comment" data-id="'.$add_comment->insert_id.'" class="fa fa-trash text-danger"></i></span>';
                $new_comment .= '</div>';
                $new_comment .= '</div>';
                $new_comment .= '</div>';
                $data['newcomment'] = $new_comment;
                $comment_qry = $this->db->select('sysid,userid,messages,datecreated')
                    ->from('comments')
                    ->where(array(
                        'sysid' => $add_comment->insert_id
                    ))->get()->row();

                $data['comment'] = $comment_qry;
            }
        } else {
            $this->db->trans_rollback();
            $qry = false;
        }

        $data['qry'] = $qry;
        return json_encode($data);
    }

    function delete_trn_comment() {
        $data = array();
        $commentid = $this->input->post('commentid');
        $qry = true;

        $this->db->trans_begin();
        $delete = update_db($this->db,'comments',array('status' => 0),array('sysid' => $commentid));

        if ($delete->qry) {
            $details_qry = $this->db->select('*')->from('comments')->where('sysid',$commentid)->get()->row();
            if ($details_qry) {
                $comment_type = get_types_name($details_qry->types);
                $data = array(
                    'dataid' => $commentid,
                    'moduleid' => $details_qry->moduleid,
                    'valueold' => '1 - Active',
                    'valuenew' => '0 - Deleted',
                    'remarks' => 'User removed a comment from '.$comment_type->names.' ID: '.$details_qry->dataid.'.',
                    'createdby' => user_id()
                );
            }
            $this->db->trans_commit();
            $qry = true;
            audit_insert($data);
        } else {
            $this->db->trans_rollback();
            $qry = false;
        }

        $data['qry'] = $qry;
        return json_encode($data);
    }

    function get_trn_comments() {
        $data = array();
        $types = $this->input->post('types');
        $moduleid = $this->input->post('moduleid');
        $dataid = $this->input->post('dataid');
        $stageid = $this->input->post('stageid');
        $sysid = $this->input->post('sysid');

        //$data['post'] = $this->input->post();

        $comments = array();
        $commentlogs = array();
        $cmtid = array();
        $trnid = array();

        if (is_array($sysid) && count($sysid) > 0) {
            foreach ($sysid AS $ids) {
                if ($ids['source'] == 'cmt') {
                    $cmtid[] = $ids['sysid'];
                } else {
                    $trnid[] = $ids['sysid'];
                }
            }
        }

        if (count($cmtid) > 0) {
            $this->db->where_not_in('sysid',$cmtid);
        }

        $comment_qry = $this->db->select('sysid,userid,messages,datecreated')
            ->from('comments')
            ->where(array(
                'types' => $types,
                //'moduleid' => $moduleid,
                'dataid' => $dataid,
                //'stageid' => $stageid,
                'status' => 1
            ))->get();

        if ($comment_qry->num_rows() > 0) {
            foreach ($comment_qry->result() AS $comment) {
                $comment->source = 'cmt';
                $commentlogs[] = $comment;
                //$uniqueid = substr(md5(strtotime($comment->datecreated)),-8).str_pad($comment->sysid,2,'0',STR_PAD_LEFT);
                /*$uniqueid = comment_hash($comment->sysid,$comment->datecreated);
                //Add reply buttons to comment
                $reply = '<span id="reply_btn" title="Reply..." class="hidden"><i id="reply_comment" data-id="'.$comment->sysid.'" class="fa fa-reply fa-sm"></i></span>';
                $html = '';
                $reply_to = '';
                $reply_message = '';
                $get_quoted_message = $this->db->select('r.quotedid,c.messages,c.userid,c.datecreated')
                    ->from('comments as c')
                    ->join('comment_reply as r','r.quotedid = c.sysid','left')
                    ->where(array('r.replyid' => $comment->sysid,'c.status' => 1))
                    ->get()->row();

                if ($get_quoted_message) {
                    $comment_style = '';
                    //$quoted_uid = substr(md5(strtotime($get_quoted_message->datecreated)),-8).str_pad($get_quoted_message->quotedid,2,'0',STR_PAD_LEFT);
                    $quoted_uid = comment_hash($get_quoted_message->quotedid,$get_quoted_message->datecreated);
                    if ($get_quoted_message->userid == user_id()) {
                        if ($comment->userid == user_id()) {
                            $replied_name = 'yourself';
                        } else {
                            $replied_name = 'you';
                        }

                        $comment_style = 'style="float: right !important;"';
                    } else {
                        if ($comment->userid == $get_quoted_message->userid) {
                            $replied_name = 'themself';
                        } else {
                            $replied = user_info($get_quoted_message->userid);
                            $replied_name = ($replied) ? ucfirst($replied->firstname.' '.$replied->lastname) : 'N/A';
                        }
                    }

                    $reply_to = ' <span>replied to '.$replied_name.'.</span>';
                    $reply_message = '<div class="quoted-content" href=".'.$quoted_uid.'">';
                    $reply_message .= '<p>' . $get_quoted_message->messages . '</p>';
                    $reply_message .= '</div>';
                }

                if ($comment->userid == user_id()) {
                    $html .= '<div id="comment_row" class="col-md-12 margin-top-10 '.$uniqueid.'">';
                    $html .= '<div class="comment-you">';
                    $html .= '<span class="bold" id="commenter">You</span> '.$reply_to;
                    $html .= $reply_message;
                    $html .= '<div class="comment-content">';
                    $html .= '<p>'.$comment->messages.'</p>';
                    $html .= '<span class="font-xs bold">'.date('Y-m-d g:i A',strtotime($comment->datecreated)).'</span> <span class="pull-right">'.$reply.'<i id="delete_comment" data-id="'.$comment->sysid.'" class="fa fa-trash text-danger"></i></span>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                } else {
                    $user = user_info($comment->userid);
                    $name = ($user) ? ucfirst($user->firstname.' '.$user->lastname) : 'N/A';

                    $html .= '<div id="comment_row" class="col-md-12 margin-top-10 '.$uniqueid.'">';
                    $html .= '<div class="comment-them">';
                    $html .= '<span class="bold" id="commenter">'.$name.'</span> '.$reply_to;
                    $html .= $reply_message;
                    $html .= '<div class="comment-content">';
                    $html .= '<p>'.$comment->messages.'</p>';
                    $html .= '<span class="font-xs bold">'.date('Y-m-d g:i A',strtotime($comment->datecreated)).'</span> <span class="pull-right">'.$reply.'</span>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                }
                $comments[] = $html;
                log_comment_view($comment->sysid);*/
            }
        }

        //GET TRN LOG COMMENTS
        if (count($trnid) > 0) {
            $this->db->where_not_in('trnc.sysid',$trnid);
        }
        $trn_comments = $this->db->select('trnc.sysid,trnc.createdby AS userid,trnc.remarks AS messages,trnc.datecreated')
            ->from('transaction_request_trails_comments AS trnc')
            ->join('transaction_request_main_trails AS trl','trl.trnid = trnc.trnid','left')
            ->where(array('trl.dataid' => $dataid))
            ->group_by('trnc.sysid')
            ->order_by('trnc.datecreated ASC')->get();

        if ($trn_comments->num_rows() > 0) {
            foreach ($trn_comments->result() AS $comment ) {
                $comment->source = 'trn';
                $commentlogs[] = $comment;
            }
        }

        if (count($commentlogs) > 0) {
            array_multisort(array_column($commentlogs,'datecreated'));

            foreach ($commentlogs AS $message) {
                $uniqueid = comment_hash($message->sysid,$message->datecreated);
                //Add reply buttons to comment
                $reply = ($message->source == 'cmt') ? '<span id="reply_btn" title="Reply..." class="hidden"><i id="reply_comment" data-id="'.$message->sysid.'" class="fa fa-reply fa-sm"></i></span>' : '';
                $html = '';
                $reply_to = '';
                $reply_message = '';
                if ($message->source == 'cmt') {
                    $get_quoted_message = $this->db->select('r.quotedid,c.messages,c.userid,c.datecreated')
                        ->from('comments as c')
                        ->join('comment_reply as r', 'r.quotedid = c.sysid', 'left')
                        ->where(array('r.replyid' => $message->sysid, 'c.status' => 1))
                        ->get()->row();

                    if ($get_quoted_message) {
                        $comment_style = '';
                        //$quoted_uid = substr(md5(strtotime($get_quoted_message->datecreated)),-8).str_pad($get_quoted_message->quotedid,2,'0',STR_PAD_LEFT);
                        $quoted_uid = comment_hash($get_quoted_message->quotedid, $get_quoted_message->datecreated);
                        if ($get_quoted_message->userid == user_id()) {
                            if ($message->userid == user_id()) {
                                $replied_name = 'yourself';
                            } else {
                                $replied_name = 'you';
                            }

                            $comment_style = 'style="float: right !important;"';
                        } else {
                            if ($message->userid == $get_quoted_message->userid) {
                                $replied_name = 'themself';
                            } else {
                                $replied = user_info($get_quoted_message->userid);
                                $replied_name = ($replied) ? ucfirst($replied->firstname . ' ' . $replied->lastname) : 'N/A';
                            }
                        }

                        $reply_to = ' <span>replied to ' . $replied_name . '.</span>';
                        $reply_message = '<div class="quoted-content" href=".' . $quoted_uid . '">';
                        $reply_message .= '<p>' . $get_quoted_message->messages . '</p>';
                        $reply_message .= '</div>';
                    }
                }

                if ($message->userid == user_id()) {
                    $html .= '<div id="comment_row" class="col-md-12 margin-top-10 '.$uniqueid.'">';
                    $html .= '<div class="comment-you">';
                    $html .= '<span class="bold" id="commenter">You</span>'.$reply_to;
                    $html .= $reply_message;
                    $html .= '<div class="comment-content">';
                    $html .= '<p>'.$message->messages.'</p>';
                    $html .= '<span class="font-xs bold">'.date('Y-m-d g:i A',strtotime($message->datecreated)).'</span> <span class="pull-right">'.$reply.'<i id="delete_comment" data-id="'.$message->sysid.'" class="fa fa-trash text-danger"></i></span>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                } else {
                    $user = user_info($message->userid);
                    $name = ($user) ? ucfirst($user->firstname.' '.$user->lastname) : 'N/A';

                    $html .= '<div id="comment_row" class="col-md-12 margin-top-10 '.$uniqueid.'">';
                    $html .= '<div class="comment-them">';
                    $html .= '<span class="bold" id="commenter">'.$name.'</span>'.$reply_to;
                    $html .= $reply_message;
                    $html .= '<div class="comment-content">';
                    $html .= '<p>'.$message->messages.'</p>';
                    $html .= '<span class="font-xs bold">'.date('Y-m-d g:i A',strtotime($message->datecreated)).'</span> <span class="pull-right">'.$reply.'</span>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                }
                $comments[] = $html;
                if ($message->source == 'cmt') {
                    log_comment_view($message->sysid);
                }
            }
        }

        $data['comments'] = $comments;
        $data['commentlogs'] = $commentlogs;

        return json_encode($data);
    }

    function upload_application_files(){
        $data = array();
        $qry = false;
        $msg = '';
        $hascontract = false;

        $this->load->helper('directory');
        $this->load->library('upload');

        if(isset($_FILES["appfiledrop"])) {
            $dataid = $this->input->post('dataid');
            $stageid = $this->input->post('stageid');

            if ($stageid && $stageid != '') {
                $filename = $_FILES['appfiledrop']['name'];
                $fileinfo = pathinfo($filename);

                $appinfo = get_application_details($dataid)->info;
                //$name = str_replace(' ','_',$appinfo->firstname) . '_' . str_replace(' ','_',$appinfo->lastname);

                //$type_name = ($filetype && trim($filetype) != '') ? '_TYPE-'. $filetype : '';
                $location = get_stage_specific($stageid)->desc;
                if ($stageid == 93) {
                    $location = get_stage_specific(92)->desc;
                }
                if ($stageid == 100) {
                    $location = get_stage_specific(95)->desc;
                }
                $file_directory = FCPATH . "uploads/attachments/cad/applications/" . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/" . $location . '/Survey';

                $file_name = $fileinfo['filename'];
                $extract = explode('_', $file_name);
                $isreq = strpos($file_name, 'REQ') === false ? false : true;
                $iscca = strpos($file_name, 'CCA');
                $data['splitname'] = $extract;

                $data['isreq'] = $isreq;

                $filetype = (is_array($extract) && count($extract) > 0) ? $extract[0] : $file_name;
                $count = (is_array($extract) && count($extract) > 0) ? ((isset($extract[1]) && ($extract[1] != '')) ? '_' . $extract[1] : '') : '';

                $data['filetype'] = $filetype;
                if (strpos($filetype, 'PAE') === false) {
                    $filename = strtoupper(strtolower($filetype)) . '_PAE' . str_pad($appinfo->essrno, 6, "0", STR_PAD_LEFT) . $count . '.' . strtolower($fileinfo['extension']);
                    if ($isreq) {
                        $file_directory = FCPATH . "uploads/attachments/cad/applications/" . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/" . $location . '/Docs';
                    }
                } else {
                    $file_directory = FCPATH . "uploads/attachments/cad/applications/" . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/" . $location . '/Docs';
                }

                $upload = sys_upload_files('appfiledrop', $file_directory, $filename);
                $data['upload'] = $upload;

                if ($upload) {
                    /*$upload_data = $upload['upload_data'];
                    if (strpos(strtolower($upload_data['raw_name']), 'pv_layout') !== false && ($upload_data['image_width'] > 1600 || $upload_data['image_height'] > 1600)) {
                        $resize = resize_img($upload_data['full_path'],1600);
                        $data['resize'] = $resize;
                    }*/
                    if ($isreq) {
                        $req_qry = $this->db->select('sysid')
                            ->from('prime_requirement_parameters')
                            ->where('codes', $filetype)->get()->row();

                        if ($req_qry) {
                            $req_arr = array(
                                'appid' => $dataid,
                                'reqid' => $req_qry->sysid,
                                'comply' => 1,
                            );
                            if ($this->db->insert('application_customers_requirements', $req_arr)) {
                                $att_id = $this->db->insert_id();
                                $data['attid'] = $att_id;
                                $att_arr = array(
                                    'attachmentid' => $att_id,
                                    'fileurl' => strstr($upload['upload_data']['full_path'], 'upload'),
                                    'complyby' => user_id(),
                                    'createdby' => user_id()
                                );

                                if ($this->db->insert('application_customers_attachments', $att_arr)) {
                                    $data['fileid'] = $this->db->insert_id();
                                    $msg = 'Files Uploaded!';
                                    $qry = true;
                                }
                            }
                        }
                    } else {
                        $msg = 'Files Uploaded!';
                        $qry = true;
                    }
                }
            } else {
                $msg = 'Please select a transaction to upload.';
            }
        } else {
            $msg = 'Drop the file again!';
        }
        $data['response']['msg'] = $msg;
        $data['qry'] = $qry;
        $data['contract'] = $hascontract;
        echo json_encode($data);

    }

}

?>
