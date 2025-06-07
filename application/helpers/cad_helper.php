<?php
if(!function_exists('address_name')) {
    function address_name($type,$id) {
        $ci = get_instance();
        $address = array(
            'brgy' => array('address_barangay','texts'),
            'dist' => array('address_districts','names'),
            'city' => array('address_city','descriptions')
        );

        $keys = array_keys($address);

        $desc = '';

        if (in_array($type,$keys)) {
            $tbl = $address[$type][0];
            $col = $address[$type][1];
            $qry = $ci->db->select($col)
                ->from($tbl)
                ->where('sysid', $id)->get()->row();

            if ($qry) {
                $desc = $qry->$col;
            }
            return $desc;
        } else {
            return false;
        }
    }
}

if(!function_exists('get_rate_type')) {
    function get_rate_type($id) {
        $ci = get_instance();
        $qry = $ci->db->select('classifications')->from('prime_system_rate_class_main')
            ->where(array('sysid' => $id))
            ->get()->row();

        $name = $qry->classifications;
        return $name;
    }
}

if(!function_exists('check_application_duplicate')) {
    function check_application_duplicate() {
        $ci = &get_instance();
        $apptype = $ci->input->post('apptype');
        $corpdesc = $ci->input->post('corpname');
        $corpbranch = $ci->input->post('corpbranch');
        $lastname = $ci->input->post('lastname');
        $firstname = $ci->input->post('firstname');
        $middlename = $ci->input->post('middlename');
        $suffix = $ci->input->post('suffix');

        $application = false;

        if ($apptype == 1) {
            $where = ($suffix) ? ' AND t.titleid = '.$suffix : '';

            $qry_person = $ci->db->query("
            SELECT p.* 
            FROM person AS p
            LEFT JOIN person_title as t on t.personid = p.sysid
            WHERE lastname = '$lastname'
            AND firstname = '$firstname'
            AND middlename LIKE '%$middlename%'
            ".$where)->row();

            $personid = ($qry_person) ? $qry_person->sysid : false;

            if ($personid) {
                $find_qry = $ci->db->select('*')
                    ->from('application_customers_details')
                    ->where(array('personid' => $personid, 'status' => 1))
                    ->get()->row();

                $application = ($find_qry) ? $find_qry : false;
            }
        }

        /*  @TODO: Create query to find Gov't and Commercial applications already exist. */

        if ($apptype == 2) {

            if ($corpbranch != '') {
                $ci->db->where('cb.names',$corpbranch);
            }

            $find_qry = $ci->db->select('acd.*')
                ->from('application_customers_details AS acd')
                ->join('application_customers_corporation AS acc','acc.appid = acd.sysid AND acc.status = 1','left')
                ->join('corporation AS c','acc.corpid = c.sysid','left')
                ->join('corporation_branches AS cb','c.sysid = cb.corpid','left')
                ->where(array('c.descs' => $corpdesc,'acd.status' => 1 ))
                ->get()->row();

            $application = ($find_qry) ? $find_qry : false;
        }

        if ($apptype == 3) {

            if ($corpbranch != '') {
                $ci->db->where('gb.names',$corpbranch);
            }

            $find_qry = $ci->db->select('acd.*')
                ->from('application_customers_details AS acd')
                ->join('application_customers_corporation AS acc','acc.appid = acd.sysid','left')
                ->join('government_main AS g','acc.corpid = g.sysid','left')
                ->join('government_main_branches AS gb','g.sysid = gb.govid','left')
                ->where(array('g.descs' => $corpdesc,'acc.status' => 1 ))
                ->get()->row();

            $application = ($find_qry) ? $find_qry : false;
        }

        return $application;
    }
}

if (!function_exists('get_tssr_layout')) {
    function get_tssr_layout($id,$selected = false) {
        $ci = &get_instance();
        $id = ($id) ? $id : $ci->input->post('id');
        //$inspectionid = ($selected) ? $selected : $ci->input->post('selected');

        $inspectionid = false;

        if ($selected) {
            $inspectionid = $selected;
        } else {
            if ($ci->input->post('selected')) {
                $inspectionid = $ci->input->post('selected');
            }
        }

        $data = array();
        $info = array();
        $info['appid'] = $id;
        $info['input'] = $ci->input->post();

        $app = application_info($id);
        $title = '';
        $data['docid'] = false;
        if ($app) {
            $info['app'] = $app;
            $title .= 'PAE'.str_pad($app->essrno, 5, "0", STR_PAD_LEFT).' - '.ucwords(strtolower($app->appname)).' TSSR';
        }

        $saved = $ci->db->select('sysid,html')
            ->from('prime_documents_main')
            ->where(array('dataid' => $id, 'doctype' => 3436, 'status' => 1))
            ->get()->row();

        $data['qry'][] = $ci->db->last_query();

        if ($saved) {
            $html = $saved->html;
            $data['docid'] = $saved->sysid;
        } else {
            if ($inspectionid) {
                $ci->db->where(array('sysid' => $inspectionid));
            } else {
                $ci->db->where(array('status' => 305));
            }

            $published_qry = $ci->db->select()
                ->from('application_customers_system_size')
                ->where(array('appid' => $id))
                ->get()->row();

            $data['qry'][] = $ci->db->last_query();

            if ($published_qry) {
                $survey = $published_qry;
                $info['survey'] = $survey;
                $creator = user_info($survey->createdby);
                $info['author'] = ucwords(strtolower($creator->firstname)) . (($creator->middlename) ? ' ' . $creator->middlename[0] . '.' : '') . ' ' . ucwords(strtolower($creator->lastname));
                $details_qry = $ci->db->select()
                    ->from('application_customers_survey_details')
                    ->where(array('logid' => $survey->sysid, 'status' => 1))
                    ->get();

                if ($details_qry->num_rows() > 0) {
                    foreach ($details_qry->result() as $details) {
                        $infotype = $details->infotype;
                        $details = (array)$details;
                        foreach ($details as $detkey => $detval) {
                            if ($detkey == 'measurements' || $detkey == 'remarks') {
                                $info['details'][$infotype][$detkey] = $detval;
                            }
                        }
                    }
                }

                $info_qry = $ci->db->select()
                    ->from('application_customers_survey_info')
                    ->where(array('logid' => $survey->sysid, 'status' => 1))
                    ->get()->row();

                if ($info_qry) {
                    $info['info'] = $info_qry;
                }

                $team_qry = $ci->db->select('empid')
                    ->from('application_customers_team_assignment')
                    ->where(array('appid' => $id, 'moduleid' => 36, 'status' => 1))
                    ->get();

                $data['teamqry'] = $ci->db->last_query();

                if ($team_qry->num_rows() > 0) {
                    foreach ($team_qry->result() as $row) {
                        if ($row->empid > 0) {
                            $person = get_employee_info($row->empid);
                            $info['team'][] = ucwords(strtolower($person->firstname)) . (($person->middlename) ? ' ' . $person->middlename[0] . '.' : '') . ' ' . ucwords(strtolower($person->lastname));
                        }
                    }
                }

                $file_directory = FCPATH . "uploads/attachments/cad/applications/" . str_pad($id, 6, "0", STR_PAD_LEFT) . "/Assessment/Survey/";
                $file_url = base_url() . "uploads/attachments/cad/applications/" . str_pad($id, 6, "0", STR_PAD_LEFT) . "/Assessment/Survey/";
                $map = directory_map($file_directory, FALSE, TRUE);
                $files = array();

                if ($map && count($map) > 0) {
                    foreach ($map as $file) {
                        $filename = explode('_', $file);
                        if (isset($filename[2])) {
                            $files[strtolower($filename[0])][] = $file_directory . $file;
                        } else {
                            $files[strtolower($filename[0])] = $file_directory . $file;
                        }
                    }
                }

                $info['files'] = $files;

                $html = $ci->load->view('custom/templates/tssr', $info, true);
            } else {
                $html = '<h1>No published TSSR Available!!!</h1>';
            }
        }
        //$data['title'] = $title;
        $data['info'] = $info;
        $data['params'] = func_get_args();
        $data['html'] = $html;
        return (object)$data;
    }
}

if (!function_exists('application_stages_layout')) {
    function application_stages_layout ($dataid,$module,$otherdata = array()) {
        $ci = &get_instance();
        $data = array(
            'dataid' => $dataid,
            'module' => $module,
            'otherdata' => $otherdata
        );

        $ci->load->view('admin/pages/modules/appview/stageviewer', $data);
    }
}

if (!function_exists('get_temp_info')) {
    function get_temp_info($appid,$field = '*') {
        $ci = &get_instance();
        $temp_qry = $ci->db->select($field)
            ->from('customer_temp_info')
            ->where(array('appid' => $appid, 'status' => 1))
            ->get()->row();

        return $temp_qry;
    }
}