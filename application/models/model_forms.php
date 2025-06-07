<?php

class Model_forms extends CI_Model {
    function tnc_app_lookup() {
        $data = array();
        $query = $this->input->get('query');

        $existing_qry = $this->db->select('m.appid')
            ->from('frm_tnc_main AS m')
            ->where('m.status',308)
            ->get();

        $existing = $this->db->last_query();;
        $not_in = '';
        if ($existing_qry->num_rows() > 0) {
            $not = array_column($existing_qry->result_array(),'appid');
            //$this->db->where_not_in('qs.sysid',$not_in);
            $not_in .= ' AND cd.sysid NOT IN (' . implode(',',$not) . ')';
        }

        $qry_details = $this->db->query("
            SELECT
                cd.sysid,
                cd.rateclassid,
                rmt.trnid,
                rmt.stageid,
                cd.essrno,
                CONCAT('PAE',LPAD(cd.essrno,6,0)) AS appnum,
                cd.datecreated,
                cd.personid,
                cd.STATUS,
                cd.apptype,
                CONCAT(p.lastname,', ',p.firstname,' ',p.middlename) AS customer,
                c.descs AS corp,
                cd.addrspec AS address
            FROM application_customers_details AS cd 
            LEFT JOIN person AS p ON cd.personid = p.sysid
            LEFT JOIN application_customers_corporation AS cp ON cp.appid = cd.sysid
            LEFT JOIN corporation AS c ON c.sysid = cp.corpid
            INNER JOIN transaction_request_main_trails AS rmt ON rmt.dataid = cd.sysid
            WHERE rmt.`status` = 1 AND cd.`status` = 1  AND rmt.stageid = 98
            $not_in
            AND (CONCAT(p.lastname,', ',p.firstname,' ',LEFT(p.lastname,1),'.') LIKE '%$query%' OR c.descs LIKE '%$query%' OR CONCAT('PAE',LPAD(cd.essrno,5,0)) LIKE '%$query%')
            GROUP BY cd.sysid, cd.rateclassid, rmt.trnid, rmt.stageid, cd.essrno, cd.datecreated, cd.personid, cd.apptype
        ");

        $listqry = $this->db->last_query();

        if ($qry_details->num_rows > 0) {
            foreach ($qry_details->result() as $row) {
                $data[] = array(
                    'sysid' => $row->sysid,
                    'appname' => ($row->apptype > 1) ? $row->corp : $row->customer,
                    'appnum' => $row->appnum,
                    'address' => $row->address
                );
            }
        }

        return json_encode($data);
    }

    function tnc_create() {
        $data = array();

        $qry = false;
        $msg = '';
        $func = '';
        $title = '';
        $html = '';
        $button = '';

        $appid = $this->input->post('appid');
        $projectname = $this->input->post('projectname');
        $location = $this->input->post('location');
        $buildtype = $this->input->post('buildtype');
        $company = $this->input->post('company');
        $companyacronym = $this->input->post('companyacronym');
        $partner = $this->input->post('partner');
        $partneracronym = $this->input->post('partneracronym');
        $client = $this->input->post('client');
        $clientacronym = $this->input->post('clientacronym');
        $clientholdings = $this->input->post('holdings');
        $invertercnt = $this->input->post('invertercount');
        $dateconducted = $this->input->post('dateconducted');

        $this->db->trans_begin();
        $new_tnc_arr = array(
            'appid' => $appid,
            'buildtype' => $buildtype,
            'projectname' => $projectname,
            'location' => $location,
            'company' => $company.(isset($companyacronym) ? ';'.$companyacronym : ''),
            'partner' => $partner.(isset($partneracronym) ? ';'.$partneracronym : ''),
            'client' => $client.(isset($clientacronym) ? ';'.$clientacronym : ''),
            'holdings' => $clientholdings,
            'invertercount' => $invertercnt,
            'dateconducted' => $dateconducted
        );

        $create_tnc = insert_db($this->db,'frm_tnc_main',$new_tnc_arr);

        if ($create_tnc->qry) {
            $this->db->trans_commit();
            $qry = true;
            $msg = 'T&C form has been created!';
            $func = 'success';
            $title = 'T&C Form';
            $button .= '<button type="button" class="btn btn-primary" id="btn_tnc_update" data-id="'.$create_tnc->insert_id.'"><i class="fa fa-save"></i> Update</button>';
            $button .= '<button type="button" class="btn btn-danger" id="btn_tnc_delete" data-id="'.$create_tnc->insert_id.'"><i class="fa fa-times"></i> Delete</button>';
            $html .= $this->load_tnc_form($buildtype,$appid);
            $data['tncID'] = $create_tnc->insert_id;
        } else {
            $this->db->trans_rollback();
            $msg = 'Error encountered while creating T&C form.';
            $func = 'error';
            $title = 'T&C Form';
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        $data['form'] = $html;
        $data['button'] = $button;

        return json_encode($data);
    }

    function tnc_update() {
        $data = array();

        $qry = false;
        $msg = '';
        $func = '';
        $title = '';
        $html = '';
        $button = '';
        $tnc = array();

        $tncid = $this->input->post('tncid');
        $appid = $this->input->post('appid');
        $projectname = $this->input->post('projectname');
        $location = $this->input->post('location');
        $buildtype = $this->input->post('buildtype');
        $dateconducted = $this->input->post('dateconducted');
        $company = $this->input->post('company');
        $companyacronym = $this->input->post('companyacronym');
        $partner = $this->input->post('partner');
        $partneracronym = $this->input->post('partneracronym');
        $client = $this->input->post('client');
        $clientacronym = $this->input->post('clientacronym');
        $clientholdings = $this->input->post('holdings');
        $invertercnt = $this->input->post('invertercnt');

        $this->db->trans_begin();

        //$create_tnc = insert_db($this->db,'frm_tnc_main',$new_tnc_arr);

        $tnc_qry = $this->db->select('projectname,location,dateconducted,company,partner,client,holdings,invertercount,buildtype')
            ->from('frm_tnc_main')
            ->where(array('sysid' => $tncid,'status' => 1))
            ->get()->row();

        if ($tnc_qry) {
            foreach ($tnc_qry as $key => $value) {
                if (strpos($value, ';') !== false) {
                    list($tnc[$key], $tnc[$key . 'acronym']) = explode(';', $value);
                } else {
                    $tnc[$key] = $value;
                }
            }
        }

        $post = $this->input->post();
        $update_tnc_arr = array();
        foreach ($tnc AS $tnc_key => $tnc_val) {
            //ADD SOMETHING FOR ACRONYMS BINDING VALUES WITH ";" AS SEPARATOR.
            if (isset($post[$tnc_key.'acronym']) && $post[$tnc_key.'acronym'] != '') {
                $post[$tnc_key] = $post[$tnc_key] . ';' . $post[$tnc_key.'acronym'];
            }

            if ($tnc_val != $post[$tnc_key]) {
                $update_tnc_arr[$tnc_key] = $post[$tnc_key];
            }
        }

        if (count($update_tnc_arr) > 0) {
            $update_tnc = update_db($this->db, 'frm_tnc_main', $update_tnc_arr, array('sysid' => $tncid));

            if ($update_tnc->qry) {
                $this->db->trans_commit();
                $qry = true;
                $msg = 'T&C form has been updated!';
                $func = 'success';
                $title = 'T&C Form';
                $button .= '<button type="button" class="btn btn-primary" id="btn_tnc_update" data-id="' . $tncid . '"><i class="fa fa-save"></i> Update</button>';
                $button .= '<button type="button" class="btn btn-danger" id="btn_tnc_delete" data-id="' . $tncid . '"><i class="fa fa-times"></i> Delete</button>';
                $html .= $this->load_tnc_form($buildtype, $appid);
            } else {
                $this->db->trans_rollback();
                $msg = 'Error encountered while updating T&C form.';
                $func = 'error';
                $title = 'T&C Form';
            }
        } else {
            $msg = 'No changes was submitted for update.';
            $func = 'error';
            $title = 'T&C Form';
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        $data['form'] = $html;
        $data['button'] = $button;

        return json_encode($data);
    }

    function load_tnc_form($buildtype = false,$appid = false) {
        $form = '';
        $button = '';
        $data = array();
        $postappid = $this->input->post('appid');
        if ($appid) {
            $data['appid'] = $appid;
        } else {
            $data['appid'] = $postappid;
        }

        $appid_ = $appid ?: $postappid;

        $tnc_qry = $this->db->select('sysid AS tncid,company,partner,client,holdings,invertercount,buildtype')
            ->from('frm_tnc_main')
            ->where(array('appid' => $appid_,'status' => 1))
            ->get()->row();

        if ($tnc_qry) {
            foreach ($tnc_qry AS $key => $value) {
                if (strpos($value,';') !== false) {
                    list($data[$key],$data[$key.'acronym']) = explode(';',$value);
                } else {
                    $data[$key] = $value;
                }

                if ($key == 'buildtype') {
                    $buildtype = $buildtype ?: $tnc_qry->buildtype;
                }
            }

            //query for existing responses and add to $data.

            if ($buildtype == 4006) {
                $form .= $this->load->view('admin/pages/modules/forms/tnc/tnc_mb',$data,true);
            }

            if ($buildtype == 4005) {
                $form .= '<h4 class="text-align-center"><i class="fa fa-warning text-warning"></i> Form for this build type is not available.</h4>';
            }

            if ($buildtype == 4004) {
                $form .= '<h4 class="text-align-center"><i class="fa fa-warning text-warning"></i> Form for this build type is not available.</h4>';
            }

            $button .= '<button type="button" class="btn btn-primary" id="btn_tnc_update" data-id="'.$tnc_qry->tncid.'"><i class="fa fa-save"></i> Update</button>';
            $button .= '<button type="button" class="btn btn-danger" id="btn_tnc_delete" data-id="'.$tnc_qry->tncid.'"><i class="fa fa-times"></i> Delete</button>';
        }

        if (!$appid) {
            $data['form'] = $form;
            $data['button'] = $button;
            return json_encode($data);
        } else {
            return $form;
        }
    }

    function tnc_save_checklist() {
        $data = array();
        $msg = '';
        $func = '';
        $title = '';
        $tncid = $this->input->post('dataid');
        $checklist = $this->input->post('checklist');
        $checklist_items = [];

        $this->db->trans_begin();
        if (count($checklist) > 0) {
            foreach ($checklist AS $itemid => $responses) {
                $checklist_response_arr = [
                    'tncid' => $tncid,
                    'itemid' => $itemid
                ];

                if (isset($responses['inputval'])) {
                    $inputval = $responses['inputval'];
                    if (is_array($inputval)) {
                        $checklist_response_arr['field'] = implode(';',$inputval);
                    } else {
                        $checklist_response_arr['field'] = $inputval;
                    }
                }

                if (isset($responses['checkval'])) {
                    $checkval = $responses['checkval'];
                    if (is_array($checkval)) {
                        $checklist_response_arr['check'] = implode(';',$checkval);
                    }
                }

                if (isset($responses['remarks']) && $responses['remarks'] != '') {
                    $checklist_response_arr['remarks'] = $responses['remarks'];
                }

                $item_qry = $this->db->select('tncid,itemid,field,check,remarks')
                    ->from('frm_tnc_checklist_responses')
                    ->where(array('tncid' => $tncid,'itemid' => $itemid,'status' => 1))
                    ->get()->row_array();

                $goAdd = true;
                if ($item_qry && $item_qry == $checklist_response_arr) {
                    $remove_item = update_db($this->db,'frm_tnc_checklist_responses',['status' => 0],['tncid' => $tncid,'itemid' => $itemid]);

                    if (!$remove_item->qry) {
                        $goAdd = false;
                    }
                }

                if ($goAdd) {
                    $add_item = insert_db($this->db, 'frm_tnc_checklist_responses', $checklist_response_arr);
                    if ($add_item->qry) {
                        $checklist_items[$itemid] = true;
                    } else {
                        $checklist_items[$itemid] = false;
                    }
                }
            }
        }

        if (count($checklist_items) > 0 && !in_array(false,$checklist_items)) {
            $this->db->trans_commit();
            $msg = 'Successfully saved checklist data.';
            $func = 'success';
            $title = 'Complete!';
        } else {
            $this->db->trans_rollback();
            $msg = 'Errors were encountered while saving checklist data.';
            $func = 'error';
            $title = 'Fail!!!';
            $data['checklists'] = $checklist_items;
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        return json_encode($data);
    }

    function tnc_save_stringtest() {
        $data = [];
        $stringtest = $this->input->post('stringtest');
        $tncid = $this->input->post('dataid');

        $msg = '';
        $func = '';
        $title = '';

        $process_status = [];

        $this->db->trans_begin();
        if (is_array($stringtest) && count($stringtest) > 0) {
            foreach ($stringtest AS $inv => $stt) {
                $details = $stt['details'] ?? false;
                $strings = $stt['strings'] ?? false;
                $details_id = false;

                if ($details) {
                    $stt_details_arr = [
                        'tncid' => $tncid,
                        'type' => $details['type'],
                        'inverter' => $inv,
                        'testingarea' => $details['testingarea'],
                        'invsn' => $details['serialnumber'],
                        'testdate' => $details['testdate'],
                        'equipment' => $details['equipment'],
                        'eqtmodel' => $details['equipmentmodel'],
                        'eqtsn' => $details['equipmentsn'],
                    ];

                    //LOOKUP FOR EXISTING CHECKLIST DETAILS FOR UPDATING
                    $details_qry = $this->db->select('sysid, testingarea, invsn, testdate, equipment, eqtmodel, eqtsn')
                        ->from('frm_tnc_form_details')
                        ->where(['tncid' => $tncid,'type' => $details['type'],'inverter' => $inv,'status' => 1])
                        ->get()->row();

                    if ($details_qry) {
                        $details_id = $details_qry->sysid;
                        unset($details_qry->sysid);
                        $stt_update_arr = [];
                        foreach ($details_qry AS $detail => $value) {
                            if ($stt_details_arr[$detail] != $value) {
                                $stt_update_arr[$detail] = $stt_details_arr[$detail];
                            }
                        }

                        if (count($stt_update_arr) > 0) {
                            $update = update_db($this->db,'frm_tnc_form_details',$stt_update_arr,['tncid' => $tncid,'type' => $details['type'],'inverter' => $inv,'status' => 1]);

                            if ($update->qry) {
                                $process_status[$inv]['update_detail'] = true;
                            } else {
                                $process_status[$inv]['update_detail'] = false;
                            }
                        }
                    } else {
                        $insert = insert_db($this->db,'frm_tnc_form_details',$stt_details_arr);
                        if ($insert->qry) {
                            $details_id = $insert->insert_id;
                            $process_status[$inv]['insert_detail'] = true;
                        } else {
                            $process_status[$inv]['insert_detail'] = false;
                        }
                    }
                }

                if ($strings && $details_id) {
                    foreach ($strings AS $strnum => $strdata) {
                        if (!empty(array_filter($strdata))) {
                            if ($strdata[0] == '') {
                                unset($strdata[0]);
                            }
                            foreach ($strdata AS $datatype => $stt_data) {
                                $stt_data_arr = [
                                    'detailsid' => $details_id,
                                    'string' => $strnum,
                                    'datatype' => $datatype,
                                    'value' => $stt_data
                                ];

                                $stt_data_qry = $this->db->select('sysid,value')
                                    ->from('frm_tnc_form_data')
                                    ->where(['detailsid' => $details_id, 'string' => $strnum, 'datatype' => $datatype,'status' => 1])
                                    ->get()->row();

                                if ($stt_data_qry && $stt_data_qry->value != $stt_data) {
                                    if (update_db($this->db,'frm_tnc_form_data',['status' => 0],['sysid' => $stt_data_qry->sysid])->qry) {
                                        $process_status[$inv]['remove_string_data_'.$strnum.'-'.$datatype] = true;
                                    } else {
                                        $process_status[$inv]['remove_string_data_'.$strnum.'-'.$datatype] = false;
                                    }
                                }

                                $new_str_data = insert_db($this->db,'frm_tnc_form_data',$stt_data_arr);

                                if ($new_str_data->qry) {
                                    $process_status[$inv]['add_string_data_'.$strnum.'-'.$datatype] = true;
                                } else {
                                    $process_status[$inv]['add_string_data_'.$strnum.'-'.$datatype] = false;
                                }
                            }
                        }
                    }
                }
            }
        }

        if (count($process_status) > 0) {
            $status_arr = [];
            foreach ($process_status AS $status) {
                if (in_array(false,$status)) {
                    $status_arr[] = false;
                } else {
                    $status_arr[] = true;
                }
            }

            if (!in_array(false,$status_arr)) {
                $this->db->trans_commit();

                //LOOKUP STRING TEST DATA, COUNT NUMBER OF STRING DATA PROVIDED.
                $strings_qry = $this->db->select('fd.inverter,stt.string as strnum')
                    ->from('frm_tnc_form_data AS stt')
                    ->join('frm_tnc_form_details AS fd', 'stt.detailsid = fd.sysid AND stt.`status` = 1', 'LEFT')
                    ->group_by(array('stt.string', 'fd.inverter'))
                    ->order_by('fd.inverter ASC, stt.string ASC')
                    ->get();

                if ($strings_qry->num_rows() > 0) {
                    foreach ($strings_qry->result() AS $string) {
                        //GET VALUES IF AVAILABLE BEFORE SENDING

                        $dci_rows = '';
                        $dci_rows .= '<tr>';
                        $dci_rows .= '<td>'.$string->strnum.'</td>';

                        $types_qry = $this->db->select('sysid,datatype,symbol')
                            ->from('frm_tnc_datatypes')
                            ->where(['subtype' => 5])
                            ->order_by('col ASC')
                            ->get();

                        if ($types_qry->num_rows() > 0) {
                            foreach ($types_qry->result() AS $row) {
                                $val_qry = $this->db->select('tid.value')
                                    ->from('frm_tnc_form_data AS tid')
                                    ->join('frm_tnc_form_details AS tfd','tfd.sysid = tid.detailsid','left')
                                    ->where(['tfd.tncid' => $tncid,'tid.datatype' => $row->sysid,'tfd.inverter' => $string->inverter,'tid.string' => $string->strnum,'tid.status' => 1])
                                    ->get()->row();

                                $value = ($val_qry && $val_qry->value) ? 'value="'.$val_qry->value.'"' : '';

                                $dci_rows .= '<td>';
                                if ($row->symbol) {
                                    $dci_rows .= '<div class="testing-wrapper">';
                                    $dci_rows .= '<input type="'.$row->datatype.'" class="form-control inline testing-vals" name="dci[inv]['.$string->inverter.'][str]['.$string->strnum.']['.$row->sysid.']" '.$value.' required><span class="testing-unit">'.$row->symbol.'</span>';
                                    $dci_rows .= '</div>';
                                } else {
                                    $dci_rows .= '<input type="'.$row->datatype.'" class="form-control inline testing-vals" name="dci[inv]['.$string->inverter.'][str]['.$string->strnum.']['.$row->sysid.']" '.$value.' required>';
                                }
                                $dci_rows .= '</td>';
                            }
                        }

                        $data['action'] = 'dci';
                        $data['dci'][$string->inverter][$string->strnum] = $dci_rows;
                    }

                }

                $msg = 'String Test data were successfully saved!';
                $func = 'success';
                $title = 'Saved!';
            } else {
                $this->db->trans_rollback();
                $msg = 'Something went wrong while saving String Test data.';
                $func = 'error';
                $title = 'Failed!';
            }
        } else {
            $msg = 'No data was added nor updated during this process.';
            $func = 'warning';
            $title = 'Nothing new!';
        }

        $data['process'] = $process_status;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        return json_encode($data);
    }

    function tnc_save_continuity_test() {
        $data = [];
        $conttest = $this->input->post('ctt');
        $tncid = $this->input->post('dataid');

        $msg = '';
        $func = '';
        $title = '';

        $process_status = [];

        $this->db->trans_begin();
        if (is_array($conttest) && count($conttest) > 0) {
            foreach ($conttest AS $inv => $ctt) {
                $details = $ctt['details'] ?? false;

                if ($details) {
                    $ctt_details_arr = [
                        'tncid' => $tncid,
                        'type' => $details['type'],
                        'inverter' => $inv,
                        'testingarea' => $details['testingarea'],
                        'temp' => $details['temp'],
                        'humidity' => $details['humidity'],
                        'testdate' => $details['testdate'],
                        'equipment' => $details['equipment'],
                        'eqtmodel' => $details['equipmentmodel'],
                        'eqtsn' => $details['equipmentsn'],
                        'accepted' => $details['accepted'],
                        'note' => $details['note'],
                    ];

                    //LOOKUP FOR EXISTING CTT DETAILS FOR UPDATING
                    $ctt_details_qry = $this->db->select('sysid, testingarea, temp, humidity, testdate, equipment, eqtmodel, eqtsn, accepted, note')
                        ->from('frm_tnc_form_details')
                        ->where(['tncid' => $tncid,'type' => $details['type'],'inverter' => $inv,'status' => 1])
                        ->get()->row();

                    if ($ctt_details_qry) {
                        unset($ctt_details_qry->sysid);
                        $ctt_update_arr = [];
                        foreach ($ctt_details_qry AS $detail => $value) {
                            if ($ctt_details_arr[$detail] != $value) {
                                $ctt_update_arr[$detail] = $ctt_details_arr[$detail];
                            }
                        }

                        if (count($ctt_update_arr) > 0) {
                            $update = update_db($this->db,'frm_tnc_form_details',$ctt_update_arr,['tncid' => $tncid,'type' => $details['type'],'inverter' => $inv,'status' => 1]);

                            if ($update->qry) {
                                $process_status[$inv]['update_detail'] = true;
                            } else {
                                $process_status[$inv]['update_detail'] = false;
                            }
                        }
                    } else {
                        $insert = insert_db($this->db,'frm_tnc_form_details',$ctt_details_arr);
                        if ($insert->qry) {
                            $process_status[$inv]['insert_detail'] = true;
                        } else {
                            $process_status[$inv]['insert_detail'] = false;
                        }
                    }
                }
            }
        }

        if (count($process_status) > 0) {
            $status_arr = [];
            foreach ($process_status AS $status) {
                if (in_array(false,$status)) {
                    $status_arr[] = false;
                } else {
                    $status_arr[] = true;
                }
            }

            if (!in_array(false,$status_arr)) {
                $this->db->trans_commit();
                $msg = 'Continuity Test details were successfully saved!';
                $func = 'success';
                $title = 'Saved!';
            } else {
                $this->db->trans_rollback();
                $msg = 'Something went wrong while saving Continuity Test details.';
                $func = 'error';
                $title = 'Failed!';
            }
        } else {
            $msg = 'No data changes were found during this process.';
            $func = 'warning';
            $title = 'Nothing new!';
        }

        $data['process'] = $process_status;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }

    function upload_tnc_pics() {
        $data = [];

        $msg = '';
        $func = '';
        $title = '';

        if(isset($_FILES["tncfiledrop"]) && !empty($_FILES["tncfiledrop"])) {
            $appid = $this->input->post('dataid');
            $upload_path = FCPATH . 'uploads/attachments/tnc/'.$appid;
            $files = $_FILES["tncfiledrop"];
            $file_count = count($files['name']);
            $extensions = [];
            $processedFile = [];
            for ($i = 0; $i < $file_count; $i++) {
                $_FILES['tnc']['name'] = $filename = $files['name'][$i];
                $_FILES['tnc']['type'] = $files['type'][$i];
                $_FILES['tnc']['tmp_name'] = $files['tmp_name'][$i];
                $_FILES['tnc']['error'] = $files['error'][$i];
                $_FILES['tnc']['size'] = $files['size'][$i];


                $upload = sys_upload_files('tnc',$upload_path,$filename);

                if (count($upload) > 0 && $upload['uploaded']) {
                    $data['uploaded'] = $upload;
                    $uploaded_file = $upload_path.'/'.$filename;
                    $extensions[] = $fileExtension = strtolower(pathinfo($uploaded_file,PATHINFO_EXTENSION));
                    if ($fileExtension == 'zip') {
                        $zip = new ZipArchive();

                        if ($zip->open($uploaded_file) === true) {
                            $extractedFiles = [];
                            for ($i = 0; $i < $zip->numFiles; $i++) {
                                $compressedName = $zip->getNameIndex($i);
                                $zippedFile = $upload_path . '/' . $compressedName;
                                $extractedFiles[] =  $zippedFile;

                                // Check if file already exists
                                if (file_exists($zippedFile)) {
                                    unlink($zippedFile);
                                }
                            }

                            // Extract all
                            $extracted = $zip->extractTo($upload_path);
                            $zip->close();

                            if ($extracted) {
                                $data['extracted'] = $extractedFiles;
                                if (file_exists($uploaded_file)) {
                                    unlink($uploaded_file);
                                }

                                $processedFile[] = true;

                                $zipMsg = 'File successfully uploaded and extracted';
                                $func = 'success';
                                $title = 'ZIP Uploaded';

                            } else {
                                $processedFile[] = false;
                                $msg = 'There were problems encountered while extracting files.';
                                $func = 'error';
                                $title = 'Failed Extraction!';
                            }
                        }
                    } else {
                        $msg = 'Image/s successfully uploaded';
                        $func = 'success';
                        $title = 'Uploaded';
                    }
                } else {
                    $msg = 'Failed to upload selected files.';
                    $func = 'error';
                    $title = 'Upload Failed!';
                }
            }
        }

        if (!in_array(false,$processedFile)) {
            if (in_array('zip',$extensions)) {

            }
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;


        return json_encode($data);
    }

    function tnc_save_insulation() {
        $data = [];
        $process = [];
        //$data['post'] = $this->input->post();
        $msg = 'Meh...';
        $func = 'warning';
        $title = 'No process!';

        $tncid = $this->input->post('dataid');
        $aci = $this->input->post('aci');
        $dci = $this->input->post('dci');

        //return json_encode($data);exit();
        $this->db->trans_begin();

        if (count($aci) > 0) {
            //1. Details
            $details_id = null;
            if (isset($aci['details']) && count($aci['details']) > 0) {
                $details_arr = $aci['details'];
                //lookup for save details
                $saved_select = array_merge(['sysid'],array_keys($aci['details']));

                $data['details'][] = $details_arr;
                $saved_qry = $this->db->select(implode(',',$saved_select))
                    ->from('frm_tnc_form_details')
                    ->where(['tncid' => $tncid,'type' => 4,'status' => 1])
                    ->get()->row();

                //$data['detailsQry'][] = $this->db->last_query();

                if ($saved_qry) {
                    $details_id = $saved_qry->sysid;
                    unset($saved_qry->sysid);
                    $update_arr = [];
                    foreach ($saved_qry AS $cols => $saved) {
                        if ($details_arr[$cols] != $saved) {
                            $update_arr[$cols] = $details_arr[$cols];
                        }
                    }

                    if (count($update_arr) > 0) {
                        $update = update_db($this->db,'frm_tnc_form_details',$update_arr,['sysid' => $details_id]);
                        if ($update->qry) {
                            $process['update_aci_details'] = true;
                        } else {
                            $process['update_aci_details'] = false;
                        }
                    }
                } else {
                    $details_arr['tncid'] = $tncid;
                    $details_arr['type'] = 4;
                    $new_details = insert_db($this->db,'frm_tnc_form_details',$details_arr);
                    if ($new_details->qry) {
                        $details_id = $new_details->insert_id;
                        $process['new_aci_details'] = true;
                    } else {
                        $process['new_aci_details'] = false;
                    }
                }
            }

            //2. Checklist
            if (isset($aci['cl']) && count($aci['cl']) > 0) {
                foreach ($aci['cl'] AS $itemid => $check) {
                    $checklist_arr = [
                        'tncid' => $tncid,
                        'itemid' => $itemid,
                        'check' => $check
                    ];

                    $saved_cl = $this->db->select('sysid,check')
                        ->from('frm_tnc_checklist_responses')
                        ->where(['tncid' => $tncid, 'itemid' => $itemid,'status' => 1])
                        ->get()->row();

                    if ($saved_cl) {
                        if ($check != $saved_cl->check) {
                            $remove_cl = update_db($this->db,'frm_tnc_checklist_responses',['status' => 0],['sysid' => $saved_cl->sysid]);
                            if (isset($remove_cl->updated) && $remove_cl->updated > 0) {
                                $new_checklist = insert_db($this->db, 'frm_tnc_checklist_responses', $checklist_arr);
                                if ($new_checklist->qry) {
                                    $process['update_aci_checklist_'.$itemid] = true;
                                } else {
                                    $process['update_aci_checklist_'.$itemid] = false;
                                }
                            }
                        }
                    } else {
                        $new_checklist = insert_db($this->db, 'frm_tnc_checklist_responses', $checklist_arr);
                        if ($new_checklist->qry) {
                            $process['add_aci_checklist_'.$itemid] = true;
                        } else {
                            $process['add_aci_checklist_'.$itemid] = false;
                        }
                    }
                }
            }

            //3. Data
            if (isset($aci['inv']) && count($aci['inv']) > 0) {
                //Consider Inverters as strings (D/N: Katalaka sagay ilis table columns)
                foreach ($aci['inv'] AS $inverter => $acidata) {
                    if (count($acidata) > 0) {
                        foreach ($acidata AS $aciDataType => $aciDataValue) {
                            $aci_data_arr = [
                                'detailsid' => $details_id,
                                'string' => $inverter,
                                'datatype' => $aciDataType,
                                'value' => $aciDataValue
                            ];

                            //Lookup existing with value
                            $saved_data = $this->db->select('sysid,value')
                                ->from('frm_tnc_form_data')
                                ->where(['detailsid' => $details_id, 'string' => $inverter, 'datatype' => $aciDataType,'status' => 1])
                                ->get()->row();


                            if ($saved_data) {
                                if ($saved_data->value != $aciDataValue) {
                                    $removeData = update_db($this->db, 'frm_tnc_form_data', ['status' => 0], ['sysid' => $saved_data->sysid]);
                                    if (isset($removeData->updated) && $removeData->updated > 0) {
                                        $newData = insert_db($this->db, 'frm_tnc_form_data', $aci_data_arr);
                                        if ($newData->qry) {
                                            $process['update_aci_data_inv' . $inverter . '_type' . $aciDataType] = true;
                                        } else {
                                            $process['update_aci_data_inv' . $inverter . '_type' . $aciDataType] = false;
                                        }
                                    } else {
                                        $process['remove_aci_data_inv' . $inverter . '_type' . $aciDataType] = false;
                                    }
                                }
                            } else {
                                $newData = insert_db($this->db,'frm_tnc_form_data',$aci_data_arr);
                                if ($newData->qry) {
                                    $process['add_aci_data_inv'.$inverter.'_type'.$aciDataType] = true;
                                } else {
                                    $process['add_aci_data_inv'.$inverter.'_type'.$aciDataType] = false;
                                }
                            }
                        }
                    }
                }
            }
        }

        if (count($dci) > 0) {
            if (isset($dci['inv']) && count($dci['inv']) > 0) {
                foreach ($dci['inv'] AS $inverter => $dciInvData) {
                    $details_id = null;
                    //1. Details
                    if (isset($dciInvData['details']) && count($dciInvData['details']) > 0) {
                        $details_arr = $dciInvData['details'];
                        $details_arr['testdate'] = $details_arr['date'].' '.date('H:i:s',strtotime($details_arr['time']));
                        unset($details_arr['date'],$details_arr['time']);
                        //lookup for save details
                        $saved_select = array_merge(['sysid'],array_keys($details_arr));

                        //$data['details'][] = $details_arr;

                        $saved_qry = $this->db->select(implode(',',$saved_select))
                            ->from('frm_tnc_form_details')
                            ->where(['tncid' => $tncid,'inverter' => $inverter,'type' => 5,'status' => 1])
                            ->get()->row();

                        //$data['detailsQry'][] = $this->db->last_query();

                        if ($saved_qry) {
                            $details_id = $saved_qry->sysid;
                            unset($saved_qry->sysid);
                            $update_arr = [];
                            foreach ($saved_qry AS $cols => $saved) {
                                if ($details_arr[$cols] != $saved) {
                                    $update_arr[$cols] = $details_arr[$cols];
                                }
                            }

                            if (count($update_arr) > 0) {
                                $data['details'][$inverter]['update'] = $update_arr;
                                $data['details'][$inverter]['saved'] = $saved_qry;
                                $update = update_db($this->db,'frm_tnc_form_details',$update_arr,['sysid' => $details_id]);
                                if ($update->qry) {
                                    $process['update_dci_inv'.$inverter.'_details'] = true;
                                } else {
                                    $process['update_dci_inv'.$inverter.'_details'] = false;
                                }
                            }
                        } else {
                            $details_arr['tncid'] = $tncid;
                            $details_arr['inverter'] = $inverter;
                            $details_arr['type'] = 5;
                            $new_details = insert_db($this->db,'frm_tnc_form_details',$details_arr);
                            if ($new_details->qry) {
                                $details_id = $new_details->insert_id;
                                $process['new_dci_inv'.$inverter.'_details'] = true;
                            } else {
                                $process['new_dci_inv'.$inverter.'_details'] = false;
                            }
                        }
                    }

                    //2. Checklist
                    if (isset($dciInvData['cl']) && count($dciInvData['cl']) > 0) {
                        foreach ($dciInvData['cl'] AS $itemid => $check) {
                            //inverter as remarks (D/N: Tak'an na ko mag-edit sang table.)
                            $checklist_arr = [
                                'tncid' => $tncid,
                                'itemid' => $itemid,
                                'check' => $check,
                                'remarks' => $inverter
                            ];

                            $data['checklistData'][$inverter][$itemid] = $checklist_arr;

                            $saved_cl = $this->db->select('sysid,check')
                                ->from('frm_tnc_checklist_responses')
                                ->where(['tncid' => $tncid, 'itemid' => $itemid,'remarks'=>$inverter, 'status' => 1])
                                ->get()->row();

                           $data['checklistQry'][$inverter][$itemid] = $this->db->last_query();

                            if ($saved_cl) {
                                if ($check != $saved_cl->check) {
                                    $remove_cl = update_db($this->db,'frm_tnc_checklist_responses',['status' => 0],['sysid' => $saved_cl->sysid]);
                                    if (isset($remove_cl->updated) && $remove_cl->updated > 0) {
                                        $new_checklist = insert_db($this->db, 'frm_tnc_checklist_responses', $checklist_arr);
                                        if ($new_checklist->qry) {
                                            $process['update_dci_inv'.$inverter.'_checklist_'.$itemid] = true;
                                        } else {
                                            $process['update_dci_inv'.$inverter.'_checklist_'.$itemid] = false;
                                        }
                                    }
                                }
                            } else {
                                $data['checklistResult'][$inverter][$itemid] = $saved_cl;
                                $new_checklist = insert_db($this->db, 'frm_tnc_checklist_responses', $checklist_arr);
                                if ($new_checklist->qry) {
                                    $process['add_dci_inv'.$inverter.'_checklist_'.$itemid] = true;
                                } else {
                                    $process['add_dci_inv'.$inverter.'_checklist_'.$itemid] = false;
                                }
                            }
                        }
                    }

                    //3. Data
                    if (isset($dciInvData['str']) && count($dciInvData['str']) > 0) {
                        foreach ($dciInvData['str'] AS $str => $strData) {
                            if (count($strData) > 0) {
                                foreach ($strData AS $strDataType => $strDataValue) {
                                    $dci_data_arr = [
                                        'detailsid' => $details_id,
                                        'string' => $str,
                                        'datatype' => $strDataType,
                                        'value' => $strDataValue
                                    ];

                                    //Lookup existing with value
                                    $saved_data = $this->db->select('sysid,value')
                                        ->from('frm_tnc_form_data')
                                        ->where(['detailsid' => $details_id, 'string' => $str, 'datatype' => $strDataType,'status' => 1])
                                        ->get()->row();

                                    if ($saved_data) {
                                        if ($saved_data->value != $strDataValue) {
                                            $removeData = update_db($this->db, 'frm_tnc_form_data', ['status' => 0], ['sysid' => $saved_data->sysid]);
                                            if (isset($removeData->updated) && $removeData->updated > 0) {
                                                $newData = insert_db($this->db, 'frm_tnc_form_data', $dci_data_arr);
                                                if ($newData->qry) {
                                                    $process['update_dci_inv' . $inverter . '_str' . $str . '_type' . $strDataType] = true;
                                                } else {
                                                    $process['update_dci_inv' . $inverter . '_str' . $str . '_type' . $strDataType] = false;
                                                }
                                            } else {
                                                $process['remove_dci_inv' . $inverter . '_str' . $str . '_type' . $strDataType] = false;
                                            }
                                        }
                                    } else {
                                        $newData = insert_db($this->db,'frm_tnc_form_data',$dci_data_arr);
                                        if ($newData->qry) {
                                            $process['add_dci_inv' . $inverter . '_str' . $str . '_type'.$strDataType] = true;
                                        } else {
                                            $process['add_dci_inv' . $inverter . '_str' . $str . '_type'.$strDataType] = false;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (count($process) > 0) {
            if (!in_array(false,$process)) {
                $this->db->trans_commit();
                $search = 'update';
                $updated = array_filter(array_keys($process), function($key) use ($search) {
                    return strpos($key, $search) !== false;
                });

                if (!empty($updated)) {
                    $msg = 'Details and/or Data has been updated!';
                    $title = 'Updated!';
                } else {
                    $msg = 'Details and data has been saved successfully';
                    $title = 'Saved!';
                }
                $func = 'success';
            } else {
                $this->db->trans_rollback();
                $msg = 'Errors were encountered while saving or updating form data.';
                $func = 'error';
                $title = 'Failed!';
            }
        } else {
            $msg = 'No changes were made or no data were processed.';
        }

        $data['process'] = $process;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        return json_encode($data);
    }

    function tnc_save_torquetest() {
        $data = [];

        $msg = 'Meh...';
        $func = 'warning';
        $title = '(-_-)zZZ';

        $tncid = $this->input->post('dataid');
        $tqt = $this->input->post();
        unset($tqt['dataid']);

        $find_tqt = $this->db->select('sysid,'.implode(',',array_keys($tqt)))
            ->from('frm_tnc_form_details')
            ->where(['tncid' => $tncid,'type' => 6,'status' => 1])
            ->get()->row();

        $this->db->trans_begin();
        if ($find_tqt) {
            $detailsId = $find_tqt->sysid;
            $updateArr = [];
            unset($find_tqt->sysid);
            foreach ($find_tqt AS $col => $detail) {
                $tqt[$col] = ($col == 'testdate') ? date('Y-m-d H:i:s',strtotime($tqt[$col])) : $tqt[$col];
                if ($tqt[$col] != $detail) {
                    $updateArr[$col] = ($tqt[$col] != '') ? $tqt[$col] : null;
                }
            }
            if (count($updateArr) > 0) {
                $updateTqt = update_db($this->db,'frm_tnc_form_details',$updateArr,['sysid' => $detailsId]);
                if ($updateTqt->qry) {
                    $this->db->trans_commit();
                    $msg = 'Torque Test Details has been updated!';
                    $title = 'Updated!';
                    $func = 'success';
                } else {
                    $msg = 'Failed to save Torque Test Details.';
                    $title = 'Fail!';
                    $func = 'error';
                }
            } else {
                $msg = 'No changes were made! Data is the same.';
                $title = 'Dupe!';
            }
        } else {
            $tqt['tncid'] = $tncid;
            $tqt['type'] = 6;
            $newTqt = insert_db($this->db,'frm_tnc_form_details',$tqt);
            if ($newTqt->qry) {
                $this->db->trans_commit();
                $msg = 'Torque Test Details has been saved!';
                $title = 'Saved!';
                $func = 'success';
            } else {
                $msg = 'Failed to save Torque Test Details.';
                $title = 'Fail!';
                $func = 'error';
            }
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        return json_encode($data);
    }

    function tnc_tabled_form() {
        $data = [];
        $tncid = $this->input->post('dataid');
        $eqttype = $this->input->post('eqttype');
        $typeName = [
            1 => 'Inverter',
            2 => 'DC Breaker Set',
            3 => 'AC Breaker Set',
            4 => 'RSD',
        ];

        if ($eqttype == 1) {
            $inverter_lookup = $this->db->select('tfd.inverter,thm.identifier,thm.energized,thm.remarks')
                ->from('frm_tnc_form_details as tfd')
                ->join('frm_tnc_thermal_scanning_data AS thm','tfd.inverter = thm.eqtnum AND thm.eqttype = '.$eqttype.' AND thm.tncid = tfd.tncid AND thm.status = 1','left')
                ->where(['tfd.tncid' => $tncid,'tfd.type' => 1,'tfd.status' => 1])
                ->order_by('tfd.inverter ASC')
                ->get();

            if ($inverter_lookup->num_rows() > 0) {
                foreach ($inverter_lookup->result() AS $thm) {
                    $temp = '<div class="testing-wrapper">';
                    //$temp .= '<input class="form-control inline testing-vals" name="thm[1]['.$thm->inverter.'][energized]" value="'.$thm->energized.'" style="width: 100% !important;" required><span class="testing-unit">°C</span>';
                    $temp .= dt_inline_input('thm[1]['.$thm->inverter.'][energized]',false,$thm->energized,['disabled' => false],'testing-vals',['width' => '100% !important']).'<span class="testing-unit">°C</span>';
                    $temp .= '</div>';
                    $data['rows'][] = [
                        'equipment' => $thm->inverter,
                        //'identifier' => '<input class="form-control inline" name="thm[1]['.$thm->inverter.'][identifier]" value="'.$thm->identifier.'">',
                        'identifier' => dt_inline_input('thm[1]['.$thm->inverter.'][identifier]',false,$thm->identifier,['disabled' => false],false,['width' => '100% !important']),
                        'energized' => $temp,
                        //'remarks' => '<input class="form-control inline" name="thm[1]['.$thm->inverter.'][remarks]" value="'.$thm->remarks.'">',
                        'remarks' => dt_inline_input('thm[1]['.$thm->inverter.'][remarks]',false,$thm->remarks,['disabled' => false],false,['width' => '100% !important']),
                    ];
                }
            }
        } else {
            $maxRows = 10;
            $nextRows = 0;
            $thm_qry = $this->db->select('eqtnum,identifier,energized,remarks')
                ->from('frm_tnc_thermal_scanning_data')
                ->where(['tncid' => $tncid,'eqttype' => $eqttype,'status' => 1])
                ->order_by('eqtnum ASC')
                ->get();

            if ($thm_qry->num_rows() > 0) {
                foreach ($thm_qry->result() AS $thm) {
                    $temp = '<div class="testing-wrapper">';
                    //$temp .= '<input class="form-control inline testing-vals" name="thm['.$eqttype.']['.$thm->eqtnum.'][energized]" value="'.$thm->energized.'" style="width: 100% !important;" required><span class="testing-unit">°C</span>';
                    $temp .= dt_inline_input('thm['.$eqttype.']['.$thm->eqtnum.'][energized]',false,$thm->energized,['disabled' => false],'testing-vals',['width' => '100% !important']).'<span class="testing-unit">°C</span>';
                    $temp .= '</div>';
                    $data['rows'][] = [
                        'equipment' => $thm->eqtnum,
                        'identifier' => dt_inline_input('thm['.$eqttype.']['.$thm->eqtnum.'][identifier]',false,$thm->identifier,['disabled' => false],false,['width' => '100% !important']),
                        'energized' => $temp,
                        'remarks' => dt_inline_input('thm['.$eqttype.']['.$thm->eqtnum.'][remarks]',false,$thm->remarks,['disabled' => false],false,['width' => '100% !important']),
                    ];

                    $maxRows--;
                    $nextRows++;
                }

                if ($maxRows > 0) {
                    $newRow = $nextRows+1;
                    for ($n = 0; $n < $maxRows;$n++) {
                        $eqtNum = $newRow+$n;
                        $temp = '<div class="testing-wrapper">';
                        $temp .= dt_inline_input('thm['.$eqttype.']['.$eqtNum.'][energized]',false,false,['disabled' => false],'testing-vals',['width' => '100% !important']).'<span class="testing-unit">°C</span>';
                        $temp .= '</div>';
                        $data['rows'][] = [
                            'equipment' => $eqtNum,
                            'identifier' => dt_inline_input('thm['.$eqttype.']['.$eqtNum.'][identifier]',false,false,['disabled' => false],false,['width' => '100% !important']),
                            'energized' => $temp,
                            'remarks' => dt_inline_input('thm['.$eqttype.']['.$eqtNum.'][remarks]',false,false,['disabled' => false],false,['width' => '100% !important']),
                        ];
                    }
                }

                //sort data[rows] by equipment ASC
            } else {
                for ($n = 0; $n < $maxRows;$n++) {
                    $eqtNum = $n+1;
                    $temp = '<div class="testing-wrapper">';
                    $temp .= dt_inline_input('thm['.$eqttype.']['.$eqtNum.'][energized]',false,false,['disabled' => false],'testing-vals',['width' => '100% !important']).'<span class="testing-unit">°C</span>';
                    $temp .= '</div>';
                    $data['rows'][] = [
                        'equipment' => $eqtNum,
                        'identifier' => dt_inline_input('thm['.$eqttype.']['.$eqtNum.'][identifier]',false,false,['disabled' => false],'testing-vals',['width' => '100% !important']),
                        'energized' => $temp,
                        'remarks' => dt_inline_input('thm['.$eqttype.']['.$eqtNum.'][remarks]',false,false,['disabled' => false],'testing-vals',['width' => '100% !important']),
                    ];
                }
            }
        }

        $data['cols'] = [
            dt_column_array('equipment',$typeName[$eqttype],'text-align-center','110px'),
            dt_column_array('identifier','Serial/Identification','','200px'),
            dt_column_array('energized','Energized Temp','text-align-center','200px'),
            dt_column_array('remarks','Remarks',''),
        ];

        return json_encode($data);
    }

    function tnc_save_thermal_test() {
        $data = [];

        $msg = 'Meh...';
        $func = 'warning';
        $title = '(-_-)zZZ';

        /*$data['post'] = $this->input->post();
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        return json_encode($data);
        exit();*/

        $tncid= $this->input->post('dataid');
        $thermalData = $this->input->post('thm');
        $details = $thermalData['details'];
        $process = [];
        unset($thermalData['details']);

        $detailsID = null;

        $this->db->trans_begin();
        $details_qry = $this->db->select('sysid,'.implode(',',array_keys($details)))
            ->from('frm_tnc_form_details')
            ->where(['tncid' => $tncid,'type' => 7,'status' => 1])->get()->row();

        if ($details_qry) {
            $detailsID = $details_qry->sysid;
            unset($details_qry->sysid);
            $updateArr = [];
            foreach ($details_qry AS $c => $detail) {
                if ($c == 'testdate') {
                    $details['testdate'] = date('Y-m-d H:i:s',strtotime($details['testdate']));
                }
                if ($details[$c] != $detail) {
                    $updateArr[$c] = $details[$c];
                }
            }

            if (count($updateArr) > 0) {
                $update = update_db($this->db,'frm_tnc_form_details',$updateArr,['sysid' => $detailsID]);
                $process['updateDetails'] = (isset($update->updated) && $update->updated > 0);
            }
        } else {
            //Insert details data
            $details['tncid'] = $tncid;
            $newDetails = insert_db($this->db,'frm_tnc_form_details',$details);
            $process['newDetails'] = $newDetails->qry;
            if ($newDetails->qry) {
                $detailsID = $newDetails->insert_id;
            }
        }

        if ($detailsID) {
            //database columns for select
            $columns = ['identifier', 'energized', 'engtime', 'off', 'offtime', 'remarks'];

            //Procedure to filter $columns for select vs array keys frmo $thermalData
            $goodKeys = [];
            foreach ($thermalData as $group) {
                foreach ($group as $item) {
                    $goodKeys = array_merge($goodKeys, array_keys($item));
                }
            }

            //Remove dupes
            $goodKeys = array_unique($goodKeys);

            $columns = array_values(array_filter($columns, function ($col) use ($goodKeys) {
                return in_array($col, $goodKeys);
            }));

            $selectCols = (count($columns) > 0) ? ',' . implode(',', $columns) : '';
            //LOOKUP SAVED THERMAL SCAN DATA
            $scan_qry = $this->db->select('sysid,eqtnum,eqttype' . $selectCols)
                ->from('frm_tnc_thermal_scanning_data')
                ->where(['tncid' => $tncid, 'status' => 1])
                ->get();

            $thm = [];
            if ($scan_qry->num_rows() > 0) {
                foreach ($scan_qry->result() as $scan) {
                    foreach ($columns as $col) {
                        $thm[$scan->eqttype][$scan->eqtnum][$col] = $scan->$col;
                    }

                    if ($thm[$scan->eqttype][$scan->eqtnum] != $thermalData[$scan->eqttype][$scan->eqtnum]) {
                        //REMOVE ROW
                        $remove = update_db($this->db,'frm_tnc_thermal_scanning_data',['status' => 0],['sysid' => $scan->sysid]);
                        $process['removeData_'.$scan->eqttype.'_'.$scan->eqtnum] = (isset($remove) && $remove->updated > 0);
                    }
                }
            }

            //REMOVE ARRAYS WITH SIMILAR DATA TO EXISTING
            if (count($thm) > 0) {
                foreach ($thm AS $thmtype => $thmnums) {
                    foreach ($thmnums as $thmnum => $thmData) {
                        $postData = $thermalData[$thmtype][$thmnum];
                        if ($postData == $thmData) {
                            unset($thermalData[$thmtype][$thmnum]);
                        }
                    }
                }
            }

            foreach ($thermalData AS $eqttype => $eqtnums) {
                foreach ($eqtnums AS $eqtnum => $eqtData) {
                    foreach ($eqtData AS $thmCol => $value) {
                        if (trim($value) === '') {
                            unset($eqtData[$thmCol]);
                        }
                    }
                    if (!empty($eqtData)) {
                        $eqtData['tncid'] = $tncid;
                        $eqtData['eqtnum'] = $eqtnum;
                        $eqtData['eqttype'] = $eqttype;
                        $newThm = insert_db($this->db, 'frm_tnc_thermal_scanning_data', $eqtData);
                        $process['newData_' . $eqttype . '_' . $eqtnum] = $newThm->qry;
                    }
                }
            }
        }



        if (count($process) > 0) {
            if (!in_array(false,$process)) {
                $this->db->trans_commit();
                $search = 'update';
                $updated = array_filter(array_keys($process), function($key) use ($search) {
                    return strpos($key, $search) !== false;
                });

                if (!empty($updated)) {
                    $msg = 'Thermal Scanning Details and/or Data has been updated!';
                    $title = 'Updated!';
                } else {
                    $msg = 'Thermal Scanning details and data has been saved successfully';
                    $title = 'Saved!';
                }
                $func = 'success';
            } else {
                $this->db->trans_rollback();
                $msg = 'Errors were encountered while saving or updating form data.';
                $func = 'error';
                $title = 'Failed!';
            }
        } else {
            $msg = 'No changes were made or no data were processed.';
        }


        $data['process'] = $process;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        return json_encode($data);
    }
}