<?php


class Model_purchasing extends CI_Model
{
    function tbl_suppliers()
    {
        $data = array();
        $sql = $this->db->query(
            "SELECT
                s.sysid,
                s.descs,
                s.currency,
                esa.address,
                GROUP_CONCAT(
                CONCAT( esc.typesid, '-', esc.contact )) AS contact_arr 
            FROM
                eprs_suppliers_main AS s
                LEFT JOIN eprs_suppliers_address AS esa ON s.sysid = esa.supplierid 
                AND esa.`status` = 1
                LEFT JOIN eprs_suppliers_contact AS esc ON s.sysid = esc.supplierid 
            WHERE
                s.`status` = 1 
            GROUP BY
                s.sysid,
                s.descs,
                esa.address"
        );
        if ($sql->num_rows() > 0) {
            $num = 1;
            foreach ($sql->result() as $row) {
                $email = '';
                $phone = '';
                $contact_arr = explode(',', $row->contact_arr);
                if (is_array($contact_arr) & count($contact_arr) > 0) {
                    foreach ($contact_arr as $crow) {
                        $contact_arr_1 = explode('-', $crow);
                        if ($contact_arr_1[0] == 1053) { // EMAIL CODE
                            $email = $contact_arr_1[1];
                        } else {
                            $phone = $contact_arr_1[1];
                        }
                    }
                }

                //ADD VALUE TO PRODUCTS, QUANTITY AND AMOUNT
                $purchase_qry = $this->db->select('COUNT(item.sysid) AS products,SUM(eti.qty) AS qty,SUM(eti.qty * qd.amount) AS amt')
                    ->from('eprs_quotation_details AS qd')
                    ->join('eprs_transaction_items AS eti', 'qd.prfitemid = eti.sysid', 'left')
                    ->join('items_main_description AS item', 'eti.itemid = item.sysid', 'left')
                    ->join('eprs_quotation_suppliers AS qs', 'qs.sysid = qd.quotationid', 'left')
                    ->where(array('qs.supplierid' => $row->sysid, 'qd.status' => 301))
                    ->get()->row();

                $control = '';
                $control .= '<div class="btn-group">';
                $control .= '<a href="frm_edit_supplier" data-arr="' . $row->sysid . '" data-toggle="ajax-modal" title="Edit Supplier" class="btn btn-primary btn-sm inline"><i class="fa fa-edit"></i> </a>';
                $control .= '<button class="btn btn-danger btn-sm inline" id="prf_item_delete" data-id="' . $row->sysid . '"><i class="fa fa-times"></i></button>';
                $control .= '</div>';

                // updated 
                $data['list'][] = [
                    'expand' => $num++,
                    'name' => $row->descs,
                    'address' => $row->address,
                    'phone' => $phone,
                    'email' => $email,
                    'products' => ($purchase_qry) ? $purchase_qry->products : 0,
                    'purchasedqty' => ($purchase_qry) ? number_format($purchase_qry->qty, 0) : 0,
                    'purchasedamt' => ($purchase_qry) ? '<span class="pull-left">' . ((is_object(get_currency($row->currency))) ? get_currency($row->currency)->symbol : '') . '</span> ' . number_format($purchase_qry->amt, 2) : 0.00,
                    'control' => $control
                ];
            }
        }

        $data['columns'] = array(
            dt_column_array('expand', '#', false, '20px'),
            dt_column_array('name', 'Supplier\'s Name'),
            dt_column_array('address', 'Address', false, '25%'),
            dt_column_array('phone', 'Contact'),
            dt_column_array('email', 'Email Address'),
            dt_column_array('products', 'Products', 'number'),
            dt_column_array('purchasedqty', 'Purchased Qty', 'number'),
            dt_column_array('purchasedamt', 'Purchased Amt', 'number'),
            dt_column_array('control', 'Control', 'text-align-center controls'),
        );

        return json_encode($data);
    }

    function add_prf_item()
    {
        $data = array();
        $itemid = $this->input->post('itemid');
        $unitid = $this->input->post('unitid');
        $remarks = $this->input->post('remarks');
        $prfid = $this->input->post('prfid');
        $qty = $this->input->post('qty');
        $trnid = $this->input->post('trnid');
        $stages = array();

        //GET TRN HISTORY
        $stages_qry = $this->db->select('trmt.*,tfms.`desc`')
            ->from('transaction_request_main_trails as trmt')
            ->join('prime_transaction_flow_main_stages as tfms', 'trmt.stageid = tfms.sysid', 'left')
            ->where('trmt.dataid', $prfid)
            ->where('trmt.trnid', $trnid)
            ->order_by('trmt.datecreated', 'DESC')
            ->get();

        if ($stages_qry->num_rows() > 0) {
            $stages_result = $stages_qry->result_array(); //CONVERT RESULT AS ARRAY
            $stages = array_column($stages_result, 'stageid'); //GET STAGEID FROM ALL RESULTS IN ONE ARRAY
        }

        $this->db->trans_begin();

        $msg = '';
        $func = '';
        $title = '';
        $qry = '';
        $unit = unit_query($unitid);

        $item = array(
            'unit' => ($unit) ? (($unit->name == $unit->code) ? $unit->name : $unit->name . ' (' . $unit->code . ')') : 'unit',
            'remarks' => $remarks,
            'qty' => $qty
        );

        $findexistWhere = array('itemid' => $itemid, 'createdby' => user_id());

        if ($prfid) {
            $findexistWhere['prfid'] = $prfid;
            $findexistWhere['status'] = 300;
            //$this->db->where(array('prfid' => $prfid,'status' => 300));
        } else {
            //$this->db->where(array('prfid IS NULL' => null,'status' => 307));
            $findexistWhere['prfid IS NULL'] = null;
            $findexistWhere['status'] = 307;
            //$findexistWhere['createdby'] = user_id();
        }

        $findexist = $this->db->select()
            ->from('eprs_transaction_items')
            ->where($findexistWhere)
            ->get()->row();

        $data['existqry'] = $this->db->last_query();

        if ($findexist) {
            $msg = 'Item already listed. Edit or remove listed item.';
            $func = 'warning';
            $qry = false;
        } else {
            $items = $this->input->post();
            if ($prfid) {
                //CHECK IF ONE OR MORE ITEMS IS STATUS 305
                if (count($stages) > 0 && in_array(104, $stages)) {
                    $items['status'] = 305;
                } else {
                    $items['status'] = 300;
                }
            }
            unset($items['trnid']);
            $insertitem = insert_db($this->db, 'eprs_transaction_items', $items);
            if ($insertitem->qry) {
                $this->db->trans_commit();
                //$data['itemSaved'] = $items;
                $prsitemid = $insertitem->insert_id;
                $msg = 'Item successfully added to list.';
                $title = 'Item Added!';
                $func = 'success';
                $qry = true;

                $item_info = $this->db->select('fulldescription')
                    ->from('items_main_description')
                    ->where('sysid', $itemid)->get()->row();

                if ($item_info) {
                    $item['desc'] = $item_info->fulldescription;
                    $item['prsitem'] = '<input type="hidden" name="prsitemid" value="' . $prsitemid . '">';
                    $control = '';
                    $control .= '<div class="btn-group">';
                    $control .= '<button class="btn btn-primary inline" id="prf_item_edit" data-id="' . $itemid . '"><i class="fa fa-edit"></i> </button>';
                    $control .= '<button class="btn btn-danger inline" id="prf_item_delete" data-id="' . $itemid . '"><i class="fa fa-times"></i></button>';
                    $control .= '</div>';
                    $item['controls'] = $control;
                }
            } else {
                $this->db->trans_rollback();
                $msg = 'Error adding item to list.';
                $title = 'Not Added.';
                $func = 'error';
                $qry = false;
            }
        }

        $data['qry'] = $qry;
        $data['func'] = $func;
        $data['msg'] = $msg;
        $data['title'] = $title;
        $data['item'] = $item;

        return json_encode($data);
    }

    function dt_prf_items()
    {
        $data = array();
        $prfid = $this->input->post('prfid');

        if ($prfid) {
            $this->db->where('eti.prfid', $prfid);
        } else {
            $this->db->where('eti.prfid IS NULL', null);
            $this->db->where('eti.createdby', user_id());
        }

        $prf_qry = $this->db->select('eti.itemid,eti.sysid,eti.prfid,imd.fulldescription,eti.qty,eti.remarks,u.unit_name,u.unit_code,eti.unitid')
            ->from('eprs_transaction_items AS eti')
            ->join('items_main_description AS imd', 'eti.itemid = imd.sysid', 'left')
            ->join('prime_unit AS u', 'eti.unitid = u.sysid', 'left')
            ->where_in('eti.status', array(300, 305, 307))
            ->get();

        if ($prf_qry->num_rows() > 0) {
            $n = 1;
            foreach ($prf_qry->result() as $item) {
                $unit = unit_query($item->unitid);
                $control = '';
                $control .= '<div class="btn-group">';
                $control .= '<button class="btn btn-primary inline" id="prf_item_edit" data-id="' . $item->itemid . '"><i class="fa fa-edit"></i> </button>';
                $control .= '<button class="btn btn-danger inline" id="prf_item_delete" data-id="' . $item->itemid . '"><i class="fa fa-times"></i></button>';
                $control .= '</div>';

                $unitn = ($unit) ? (($unit->name == $unit->code) ? $unit->name : $unit->name . ' (' . $unit->code . ')') : 'unit';
                $data['itemlist'][] = array(
                    'num' => $n++ . '<input type="hidden" id="prf_item_id" name="prfitemid" value="' . $item->sysid . '">',
                    'item' => $item->fulldescription,
                    'qty' => '<span id="prf_qty">' . $item->qty . '</span>',
                    'unit' => '<span id="prf_unit_name">' . $unitn . '</span><input type="hidden" id="prf_item_unit" style="width: 100% !important;" class="form-control" name="prsunitid" value="' . $item->unitid . '">',
                    'remarks' => '<span id="prf_remarks">' . $item->remarks . '</span>',
                    'control' => $control
                );
            }
        }

        return json_encode($data);
    }

    function save_prf_draft()
    {
        $data = array();
        $title = '';
        $msg = '';
        $func = '';
        $qry = false;

        $justification = $this->input->post('justification');

        $this->db->trans_begin();
        $prf = insert_db($this->db, 'eprs_transaction', array('typesid' => 1026, 'justification' => $justification, 'status' => 307));
        if ($prf->qry) {
            $prsid = $prf->insert_id;
            $prfnum = 'PRF' . date('ym') . str_pad($prsid, 5, '0', STR_PAD_LEFT);
            $update = update_db($this->db, 'eprs_transaction_items', array('prfid' => $prsid), array('prfid IS NULL' => null, 'status' => 307));
            if ($update->qry) {
                $updated = $update->updated;
                $title = $prfnum;
                $msg = $updated . ' items has been saved as draft with PRF# <b>' . $prfnum . '</b>.';
                $func = 'success';
                $qry = true;
                $this->db->trans_commit();
            } else {
                $title = 'Fail!';
                $msg = 'Failed to save items to draft.';
                $func = 'error';
                $qry = false;
                $this->db->trans_rollback();
            }
        } else {
            $title = 'Fail!';
            $msg = 'Failed to create PRF for draft items.';
            $func = 'error';
            $qry = false;
            $this->db->trans_rollback();
        }

        $data['title'] = $title;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;

        return json_encode($data);
    }

    function save_item_edit()
    {
        $data = array();

        $prsitemid = $this->input->post('id');
        $unitid = $this->input->post('unit');
        $remarks = $this->input->post('remarks');
        $qty = $this->input->post('qty');

        $msg = '';
        $func = '';
        $qry = false;
        $title = '';

        $update_arr = array();
        $updated = array();

        $prsitem = $this->db->select('unitid,qty,remarks')
            ->from('eprs_transaction_items')
            ->where(array('sysid' => $prsitemid, 'status !=' => 0))
            ->get()->row();

        if ($prsitem) {
            if ($prsitem->unitid != $unitid) {
                $update_arr['unitid'] = $unitid;
                $unit = unit_query($unitid);
                $unitn = ($unit) ? (($unit->name == $unit->code) ? $unit->name : $unit->name . ' (' . $unit->code . ')') : 'unit';
                $updated['prf_unit_name'] = $unitn;
            }
            if ($prsitem->qty != $qty) {
                $update_arr['qty'] = $qty;
                $updated['prf_qty'] = $qty;
            }
            if ($prsitem->remarks != $remarks) {
                $update_arr['remarks'] = $remarks;
                $updated['prf_remarks'] = $remarks;
            }
        }

        $this->db->trans_begin();
        $update = update_db($this->db, 'eprs_transaction_items', $update_arr, array('sysid' => $prsitemid));

        if ($update && $update->qry == true) {
            $this->db->trans_commit();
            $msg = 'Item request details has been updated.';
            $func = 'success';
            $qry = true;
            $title = 'Updated!';
            $data['updated'] = $updated;
        } else {
            $this->db->trans_rollback();
            $msg = 'Failed to update item request details.';
            $func = 'error';
            $qry = true;
            $title = 'Failed!';
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['title'] = $title;

        return json_encode($data);
    }

    function remove_prs_item()
    {
        $data = array();

        $prsitemid = $this->input->post('itemid');

        $msg = '';
        $func = '';
        $qry = false;
        $title = '';

        $this->db->trans_begin();
        $update = update_db($this->db, 'eprs_transaction_items', array('status' => 0), array('sysid' => $prsitemid));
        if ($update && $update->qry == true) {
            $this->db->trans_commit();
            $msg = 'Item has been removed from the list.';
            $func = 'success';
            $qry = true;
            $title = 'Removed!';
        } else {
            $this->db->trans_rollback();
            $msg = 'Failed to remove from the list.';
            $func = 'error';
            $qry = true;
            $title = 'Failed!';
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['title'] = $title;

        return json_encode($data);
    }

    function discard_prf()
    {
        $prfid = $this->input->post('prfid');

        $data = array();

        $msg = '';
        $func = '';
        $qry = false;
        $title = '';

        $dbwhere = array();

        $dbwhere['status'] = 307;

        $this->db->trans_begin();
        if ($prfid && $prfid != 0) {
            $updateprf = update_db($this->db, 'eprs_transaction', array('status' => 0), array('sysid' => $prfid));

            if ($updateprf->qry) {
                $dbwhere['prfid'] = $prfid;
                $updateitems = update_db($this->db, 'eprs_transaction_items', array('status' => 0), $dbwhere);

                if ($updateitems->qry && $updateitems->updated > 0) {
                    $this->db->trans_commit();
                    $msg = 'PRF and associated items were successfully discarded.';
                    $func = 'success';
                    $qry = false;
                    $title = 'PRF Discarded!';
                } else {
                    $this->db->trans_rollback();
                    $msg = 'Failed to remove PRF and associated items.';
                    $func = 'error';
                    $qry = false;
                    $title = 'Failed!';
                }
            } else {
                $this->db->trans_rollback();
                $msg = 'Failed to remove PRF and associated items.';
                $func = 'error';
                $qry = false;
                $title = 'Failed!';
            }
        } else {
            $dbwhere['prfid IS NULL'] = null;
            $dbwhere['createdby'] = user_id();
            $updateitems = update_db($this->db, 'eprs_transaction_items', array('status' => 0), $dbwhere);

            if ($updateitems->qry && $updateitems->updated > 0) {
                $this->db->trans_commit();
                $msg = 'All items has been discarded!';
                $func = 'success';
                $qry = true;
                $title = 'Removed!';
                $data['query'] = $updateitems->query;
            } else {
                $this->db->trans_rollback();
                $msg = 'Failed to discard PRF list.';
                $func = 'error';
                $qry = false;
                $title = 'Failed!';
            }
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['title'] = $title;

        return json_encode($data);
    }

    function send_prf_approval()
    {
        $prfid = $this->input->post('prfid');
        $justification = $this->input->post('justification');
        $justification = ($justification == false || $justification == '') ? 'N/A' : $justification;

        $remarks = $this->input->post('remarks');
        $type = $this->input->post('type');
        $flowid = $this->input->post('flowid');
        $stageid = $this->input->post('stageid');
        $trnid = $this->input->post('trnid');

        $data = array();

        $msg = '';
        $func = '';
        $qry = false;
        $title = '';

        $dbwhere = array();

        $errors = array();

        //$dbwhere['status'] = 307;
        //$dbwhere['createdby'] = user_id();

        $this->db->trans_begin();
        if (!$prfid) {
            $prf = insert_db($this->db, 'eprs_transaction', array('typesid' => 1026, 'justification' => $justification, 'status' => 300));
            if ($prf->qry) {
                $prfid = $prf->insert_id;
                $prfnum = 'PRF' . date('ym') . str_pad($prfid, 5, '0', STR_PAD_LEFT);
                $update = update_db($this->db, 'eprs_transaction_items', array('prfid' => $prfid, 'status' => 300), array('prfid IS NULL' => null, 'status' => 307, 'createdby' => user_id()));
                if ($update->qry) {
                    $updated = $update->updated;
                    $title = $prfnum;
                    $msg = $updated . ' items has been sent for GM\'s approval with PRF# <b>' . $prfnum . '</b>.';
                    $func = 'success';
                    $qry = true;
                    $this->db->trans_commit();
                } else {
                    $errors['update_prf_items'] = true;
                    $title = 'Fail!';
                    $msg = 'Failed to send items for approval.';
                    $func = 'error';
                    $qry = false;
                    $this->db->trans_rollback();
                }
            } else {
                $errors['insert_prf'] = true;
                $title = 'Fail!';
                $msg = 'Failed to create PRF!';
                $func = 'error';
                $qry = false;
                $this->db->trans_rollback();
            }
        } else {
            if (!$trnid) {
                $dbwhere['status'] = 307;
                $dbwhere['prfid'] = $prfid;
                //$dbwhere['prfid IS NULL'] = null;
                //$dbwhere['createdby'] = user_id();
                $prfnum = $this->input->post('prfnum');
                $update_prf = update_db($this->db, 'eprs_transaction', array('status' => 300), array('sysid' => $prfid));
                if ($update_prf->qry) {
                    $update = update_db($this->db, 'eprs_transaction_items', array('status' => 300), $dbwhere);
                    if ($update->qry && $update->updated > 0) {
                        $msg = '<b>' . $prfnum . '</b> has been sent for GM\'s approval.';
                        $func = 'success';
                        $qry = true;
                        $this->db->trans_commit();
                    } else {
                        $errors['update_draft_prf_items'] = true;
                        $title = 'Fail!';
                        $msg = 'Failed to send items for approval.';
                        $func = 'error';
                        $qry = false;
                        $this->db->trans_rollback();
                    }
                } else {
                    $errors['update_draft_prf'] = true;
                    $title = 'Fail!';
                    $msg = 'Failed to send items for approval.';
                    $func = 'error';
                    $qry = false;
                    $this->db->trans_rollback();
                }
            } else {
                if ($type == 1206) {
                    $approve_trn_items = update_db($this->db, 'eprs_transaction_items', array('status' => 305), array('prfid' => $prfid, 'status' => 300));
                    if ($approve_trn_items->qry) {
                        $qry = true;
                    } else {
                        $errors['approve_trn_items'] = true;
                    }
                } else {
                    $qry = true;
                }
            }
        }

        if ($qry) {
            if (!$trnid) {
                $insert_trns_trail = create_transaction_trails('PRF-NEW', $prfnum, 26, $prfid);
                $data['new_trn'] = $insert_trns_trail;
                if ($insert_trns_trail) {
                    $audit_data = array(
                        'dataid' => $prfid,
                        'valuenew' => $prfnum,
                        'moduleid' => 26,
                        'remarks' => 'Created new PRF for: ' . $justification . '.',
                        'createdby' => user_id()
                    );
                    audit_insert($audit_data);
                }

                //FORWARD TO APPROVAL SINCE STEP 1 IS STILL THE REQUEST FORM
                $trn_data = $this->db->select('trnid')
                    ->from('transaction_request_main_trails')
                    ->where(array('stageid' => 102, 'dataid' => $prfid, 'status' => 1))
                    ->order_by('datecreated DESC')->get()->row();

                if ($trn_data) {
                    $trail_arr = array(
                        'trnid' => $trn_data->trnid,
                        'stageid' => 103,
                        'dataid' => $prfid,
                        'createdby' => user_id(),
                    );

                    task_ins_process($trail_arr, null, null);
                }
            } else {

                $stage = get_stage_details($stageid);

                $nextroute_qry = $this->db->select('sysid')
                    ->from('prime_transaction_flow_main_stages')
                    ->where(array('flowid' => $flowid, 'levels >' => $stage->levels, 'status' => 1))
                    ->get()->row();

                if ($nextroute_qry) {
                    $trail_arr = array(
                        'trnid' => $trnid,
                        'stageid' => $nextroute_qry->sysid,
                        'dataid' => $prfid,
                        'createdby' => user_id(),
                        //'status' => $stats
                    );

                    $forward = task_ins_process($trail_arr, null, null);
                    $typename = get_types_name($type);
                    $data['type'] = $typename->names;
                    if ($forward->qry) {
                        $this->db->trans_commit();
                        $qry = true;
                        $msg = 'You have approved this ' . $typename->names . ' and is forwarded to the next stage.';
                        $func = 'success';
                        $title = $typename->names . ' Approved!';
                        $url = base_url('module/49e3d046636e06b2d82ee046db8e6eb9a2e11e16/view/' . $prfid);
                        $data['url'] = $url;
                    } else {
                        $this->db->trans_rollback();
                        $qry = true;
                        $msg = 'Failed to approve this ' . $typename->names . '.';
                        $func = 'error';
                        $title = $typename->names . ' Approval Failed!';
                    }
                }
            }
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['title'] = $title;
        $data['errors'] = $errors;

        return json_encode($data);
    }

    function get_prs_list()
    {
        $data = array();

        $route = $this->input->post('route');

        $app_flow_ids_arr = flow_id_arr('EPRS');
        $app_flow_ids = ($app_flow_ids_arr) ? implode(',', $app_flow_ids_arr) : false;
        $where_trails_last = ($app_flow_ids_arr) ? " AND rm.flowid IN ($app_flow_ids) " : "";
        $where_stages = ($app_flow_ids_arr) ? " AND flowid IN ($app_flow_ids) " : "";
        $data['traillast'] = $where_trails_last;

        if ($route && ((is_array($route) && count($route) > 0) || $route > 0)) {

            $levels = '';
            if (is_array($route)) {
                $levels = 'levels IN (' . implode(',', $route) . ')';
            } else {
                $levels = ($route > 0) ? 'levels = ' . $route : 'levels = ""';
            }

            $sql_stages = $this->db->query("
                SELECT sysid
                FROM prime_transaction_flow_main_stages
                WHERE $levels AND `status` = 1 $where_stages
                ");

            if ($sql_stages->num_rows() > 0) {
                foreach ($sql_stages->result() as $srow) {
                    $stages_ids[] = $srow->sysid;
                }
            }
            $stageids = implode(',', $stages_ids);
            $where = ' AND rmt.stageid IN (' . $stageids . ')';
        } else {
            $sql_stages = $this->db->query("
                SELECT sysid
                FROM prime_transaction_flow_main_stages
                WHERE `status` = 1 $where_stages
                ");

            if ($sql_stages->num_rows() > 0) {
                foreach ($sql_stages->result() as $srow) {
                    $stages_ids[] = $srow->sysid;
                }
            }
            $stageids = implode(',', $stages_ids);
            $where = ' AND rmt.stageid IN (' . $stageids . ')';
        }

        $qry_details = $this->db->query("
            SELECT
                et.sysid,
                rmt.trnid,
                rmt.stageid,
                trm.datecreated AS submitted,
                rmt.datecreated AS updated,
                COUNT( eti.sysid ) AS items,
                et.justification,
                et.createdby,
                et.datecreated,
                et.`status`
            FROM
                eprs_transaction AS et
                LEFT JOIN eprs_transaction_items AS eti ON eti.prfid = et.sysid AND eti.`status` IN (300,305)
                INNER JOIN transaction_request_main_trails AS rmt ON rmt.dataid = et.sysid
                INNER JOIN transaction_request_main AS trm ON rmt.trnid = trm.sysid 
            WHERE
                rmt.`status` = 1 
                AND et.`status` IN (300,301,305) 
                $where 
            GROUP BY
                et.sysid,
                rmt.trnid,
                rmt.stageid,
                et.datecreated,
                et.createdby,
                et.typesid
        ");

        //$data['sql'] = $this->db->last_query();

        if ($qry_details->num_rows() > 0) {
            foreach ($qry_details->result() as $row) {
                $prsid = $row->sysid;
                $trnid = $row->trnid;
                $stageid = $row->stageid;
                $datesubmitted = $row->submitted;
                $justification = $row->justification;
                $createdby = $row->createdby;
                $created = $row->datecreated;
                $items = $row->items;

                $creator = get_users_info($createdby);
                $requestor = '';

                if ($creator) {
                    $requestor = ucfirst($creator->firstname . ' ' . $creator->lastname);
                }

                $comment_cnt = '';
                $comment_msg = '';
                $qry_comments_cnt = $this->db->select('count(tc.trnid) AS cnt')
                    ->from('transaction_request_trails_comments AS tc')
                    ->where(array('tc.trnid' => $trnid, 'status' => 1))
                    ->get()->row();
                if ($qry_comments_cnt && $qry_comments_cnt->cnt > 0) {

                    $qry_comments_msg = $this->db->select('remarks')
                        ->from('transaction_request_trails_comments AS tc')
                        ->where(array('tc.trnid' => $trnid, 'status' => 1))
                        ->order_by('datecreated', 'desc')
                        ->get()->row();
                    $comment_msg = ($qry_comments_msg) ? $qry_comments_msg->remarks : '';
                    $max_length = 45;

                    if (strlen($comment_msg) > $max_length) {
                        $offset = ($max_length - 3) - strlen($comment_msg);
                        $comment_msg = substr($comment_msg, 0, strrpos($comment_msg, ' ', $offset)) . ' ...';
                    }
                    $comment_cnt = '<span class="badge badge-danger pull-right" style="margin-left: 5px;">' . $qry_comments_cnt->cnt . '</span>';
                }

                $creation_date = '';
                $qry_trails_last = $this->db->query("
                    SELECT rm.sysid AS trnid, rmt.sysid, rmt.datecreated, rmt.createdby, rmt.stageid, rmt.dataid, rmt.datecreated AS logdate
                    FROM transaction_request_main_trails AS rmt
                    INNER JOIN transaction_request_main AS rm ON rm.sysid = rmt.trnid
                    WHERE rmt.dataid = $prsid 
                    AND rmt.`status` = 1
                    $where_trails_last
                    ORDER BY rmt.datecreated DESC
                ")->row();

                //$data['traillast_qry'] = $this->db->last_query();
                $show = true;
                if ($route && $route > 0) {
                    if ($qry_trails_last && $qry_trails_last->stageid != $stageid) {
                        $show = false;
                    }
                }

                $trn_name = 'Unknown';
                $updated_date = 'None';
                $button = '';
                $from_created_by = 'None';


                if ($qry_trails_last) {
                    $creation_date = $row->datecreated;
                    $updated_date = $qry_trails_last->datecreated;

                    $user_info = get_users_info($qry_trails_last->createdby);
                    $from_created_by = ($user_info) ? $user_info->lastname . ', ' . $user_info->firstname : '';


                    $trn_name = '<a href="javascript:;" title="Current" class="label label-info">C</a> ' . get_trail_name($qry_trails_last->stageid);
                    $button .= '<div class="btn-group btn-xs">';
                    $button .= btn_view_trn($qry_trails_last->sysid, $qry_trails_last->dataid, false, '_blank');
                    //$button .= btn_view_trn($qry_trails_last->sysid, $qry_trails_last->dataid, 'task', '_blank');
                    $button .= btn_view_trn($qry_trails_last->sysid, $qry_trails_last->dataid, 'profile', '_blank');
                    $button .= btn_view_trn($qry_trails_last->sysid, $qry_trails_last->dataid, 'comments', '_blank');
                    $button .= btn_view_trn($qry_trails_last->sysid, $qry_trails_last->dataid, 'back');
                    $button .= btn_view_trn($qry_trails_last->sysid, $qry_trails_last->dataid, 'send');

                    $button .= '</div>';
                }

                $trn_elapse = time_elapsed_diff($creation_date, $updated_date, true);
                $ovr_elapse = time_elapsed_diff($creation_date, date('Y-m-d h:m:s'));

                $time = $datesubmitted . '<br><small class="text-info">' . timeago($row->datecreated, sql_time()->DATETIME) . '</small>';
                $time_updated = $updated_date . '<br><small class="text-info">' . timeago($updated_date, sql_time()->DATETIME) . '</small>';

                if ($row->status == 1) {
                    $status = 'Pending';
                } else {
                    $status = get_types_label_format($row->status);
                }

                if ($show) {
                    $prfno = 'PRF' . date('ym', strtotime($created)) . str_pad($prsid, 5, '0', STR_PAD_LEFT);

                    $po = $this->db->select('ponumber as number')
                        ->from('eprs_po')
                        ->where(array('prfid' => $prsid, 'status' => 1))
                        ->get()->row();

                    if ($po) {
                        $ponum = 'PAE-' . str_pad($po->number, 8, '0', STR_PAD_LEFT);
                        $hide = 'hidden';
                    } else {
                        $ponum = 'N/A';
                        $hide = '';
                    }
                    $data['list'][] = array(
                        'expand' => btn_expand($prsid),
                        'prfno' => '<h4 class="text-danger bold" style="margin: 0px 0px;">' . $prfno . ' </h4> ',
                        'pono' => $ponum,
                        'submitted' => $time,
                        'from' => $from_created_by,
                        'updated' => $time_updated,
                        'items' => $items,
                        'justification' => $justification,
                        'requestor' => $requestor,
                        'dataid' => '',
                        'origid' => '',
                        'control' => $button,
                        'trn' => $trn_name,
                        'status' => $status,
                        'remarks' => $comment_msg . $comment_cnt
                    );
                }
            }
        }

        $data['columns'] = array(
            dt_column_array('expand', false, 'text-align-center', '1%'),
            dt_column_array('prfno', false, 'text-primary bold', '10%'),
            dt_column_array('pono', false, 'text-primary bold', '10%'),
            dt_column_array('submitted', false, false, '10%'),
            dt_column_array('updated', false, false, '10%'),
            dt_column_array('items', false, 'number'),
            dt_column_array('justification', false, false, '300px'),
            dt_column_array('trn', false, 'text-danger', '150px'),
            dt_column_array('remarks', false, 'text-info', '150px'),
            dt_column_array('status', false, 'text-info'),
            dt_column_array('status', false, 'controls', '13%'),
        );

        return json_encode($data);
    }

    function get_prf_items_for_approval()
    {
        $data = array();
        $prfid = $this->input->post('prfid');

        /*if ($prfid) {
            $this->db->where('eti.prfid',$prfid);
        } else {
            $this->db->where('eti.prfid IS NULL',null);
            $this->db->where('eti.createdby',user_id());
        }*/

        $request = $this->db->select()
            ->from('eprs_transaction')
            ->where('sysid', $prfid)
            ->get()->row();

        $prf_qry = $this->db->select('eti.itemid,eti.sysid,eti.prfid,imd.fulldescription,eti.qty,eti.remarks,u.unit_name,u.unit_code,eti.unitid')
            ->from('eprs_transaction_items AS eti')
            ->join('items_main_description AS imd', 'eti.itemid = imd.sysid', 'left')
            ->join('prime_unit AS u', 'eti.unitid = u.sysid', 'left')
            ->where_in('eti.status', array(300, 305))
            ->where('eti.prfid', $prfid)
            ->get();

        //$data['query'] = $this->db->last_query();

        if ($prf_qry->num_rows() > 0) {
            $n = 1;
            foreach ($prf_qry->result() as $item) {
                $comments = $this->db->select('COUNT(messages) AS cnt')
                    ->from('comments')
                    ->where(array(
                        'types' => 3438,
                        'moduleid' => 214,
                        'dataid' => $item->sysid,
                        'stageid' => 103,
                        'status' => 1
                    ))->get()->row();
                $unit = unit_query($item->unitid);
                $control = '';
                $control .= '<div class="btn-group">';
                $control .= btn_comment($item->sysid, $comments->cnt);
                if ($request && !in_array($request->status, array(302, 303))) {
                    $control .= '<button class="btn btn-primary inline" id="prf_item_edit" data-id="' . $item->itemid . '"><i class="fa fa-edit"></i> </button>';
                    $control .= '<button class="btn btn-danger inline" id="prf_item_disapprove" data-id="' . $item->itemid . '"><i class="fa fa-times"></i></button>';
                }
                $control .= '</div>';

                $unitn = ($unit) ? (($unit->name == $unit->code) ? $unit->name : $unit->name . ' (' . $unit->code . ')') : 'unit';
                $data['itemlist'][] = array(
                    'num' => $n++ . '<input type="hidden" id="prf_item_id" name="prfitemid" value="' . $item->sysid . '">',
                    'item' => $item->fulldescription,
                    'qty' => '<span id="prf_qty">' . $item->qty . '</span>',
                    'unit' => '<span id="prf_unit_name">' . $unitn . '</span><input type="hidden" id="prf_item_unit" class="form-control" name="prsunitid" value="' . $item->unitid . '">',
                    'remarks' => '<span id="prf_remarks">' . ellipsis($item->remarks, 20) . '</span>',
                    'control' => $control
                );
            }
        }

        return json_encode($data);
    }

    function show_prf_item_comments()
    {
        $data = array();
        $dataid = $this->input->post('id');

        $data['html'] = comment_section(3438, 214, $dataid, 103);
        return json_encode($data);
    }

    function show_rfq_item_comments()
    {
        $data = array();
        $dataid = $this->input->post('id');


        $data['html'] = comment_section(3438, 193, $dataid, 104);
        return json_encode($data);
    }

    function disapprove_prf_item()
    {
        $data = array();
        $itemid = $this->input->post('itemid');
        $remarks = $this->input->post('remarks');
        $qry = false;

        $update_arr = array(
            'status' => 302
        );

        if ($remarks) {
            $update_arr['remarks'] = $remarks;
        }

        $this->db->trans_begin();
        $disapprove = update_db($this->db, 'eprs_transaction_items', $update_arr, array('sysid' => $itemid));
        if ($disapprove->qry) {
            $this->db->trans_commit();
            $qry = true;
            //NOTIFY CREATOR AFTER SUCCESSFUL UPDATE
        } else {
            $this->db->trans_rollback();
            $qry = false;
        }

        $data['qry'] = $qry;

        return json_encode($data);
    }

    function approve_prf()
    {
        //add remarks for approval if available and add to PRF logs.
        //Create same behavior as transferring to next stage.
        $data = array();
        $qry = false;
        $msg = '';
        $func = '';
        $title = '';
        $url = '';

        $prfid = $this->input->post('prfid');
        $remarks = $this->input->post('remarks');
        $type = $this->input->post('type');
        $flowid = $this->input->post('flowid');
        $stageid = $this->input->post('stageid');
        $trnid = $this->input->post('trnid');

        $stage = get_stage_details($stageid);

        $transactions = array();

        $this->db->trans_begin();
        $eprs_log = array(
            'prsid' => $prfid,
            'typesid' => $type,
            'remarks' => $remarks,
            'moduleid' => $stage->moduleid,
            'statusid' => 301,
        );

        $insert_log = insert_db($this->db, 'eprs_transaction_logs', $eprs_log);

        if ($insert_log->qry) {
            $typename = get_types_name($type);
            $data['type'] = $typename->names;
            $table = '';
            $set = array();
            $where = array();

            //IF TYPE IS PRF
            if ($type == 1206) {
                //change all item status to active 305
                $table = 'eprs_transaction_items';
                $set = array(
                    'status' => 305
                );
                $where = array(
                    'prfid' => $prfid,
                    'status' => 300
                );
            }

            //IF TYPE IS RFQ AND PCEO APPROVED
            if ($type == 1207 && $stage->moduleid == 215) {
                $quotations = $this->save_quotation();
                $transactions = array_merge($transactions, $quotations->trn);
                //UPDATE ALL 305 TO 301
                //lookup items associated with PRF id and update status 305 to 301 of corresponding prf items
                $table = 'eprs_quotation_details';
                $set = array(
                    'status' => 301
                );

                $quotations_qry = $this->db->select('eqd.sysid,eqd.quotationid')
                    ->from('eprs_quotation_details AS eqd')
                    ->join('eprs_quotation_suppliers AS eqs', 'eqd.quotationid = eqs.sysid', 'left')
                    ->where(array(
                        'eqd.status' => 305,
                        'eqs.prfid' => $prfid,
                    ))->get();

                $wherein = '';
                if ($quotations_qry->num_rows() > 0) {
                    $in = array();
                    $supp = array();
                    foreach ($quotations_qry->result() as $quote) {
                        $in[] = $quote->sysid;
                        $supp[] = $quote->quotationid;
                    }
                    $wherein .= implode(',', $in);
                }

                $where = array(
                    'sysid IN (' . $wherein . ')' => null
                );
                $supplier = implode(',', array_unique($supp));

                update_db($this->db, 'eprs_quotation_suppliers', array('status' => 301), array('sysid IN (' . $supplier . ')' => null));
            }

            if ($table != '' && count($set) > 0 && count($where) > 0) {
                $approve_trn_items = update_db($this->db, $table, $set, $where);

                if ($approve_trn_items->qry) {
                    if ($type == 1207 && $stage->moduleid == 215) {
                        $update_prf_status = update_db($this->db, 'eprs_transaction', array('status' => 301), array('sysid' => $prfid));
                        if ($update_prf_status->qry) {
                            //$data['approveqry'] = $approve_trn_items->query;
                            $transactions['approve'] = true;
                        } else {
                            $transactions['approve'] = false;
                        }
                    } else {
                        //$data['approveqry'] = $approve_trn_items->query;
                        $transactions['approve'] = true;
                    }
                } else {
                    $transactions['approve'] = false;
                }
            }

            if (trim($remarks) != '') {
                $comments_arr = array(
                    'trnid' => $trnid,
                    'trailid' => $stageid,
                    'remarks' => $remarks
                );
                insert_db($this->db, 'transaction_request_trails_comments', $comments_arr);
            }

            $nextroute_qry = $this->db->select('sysid')
                ->from('prime_transaction_flow_main_stages')
                ->where(array('flowid' => $flowid, 'levels >' => $stage->levels, 'status' => 1))
                ->get()->row();

            if ($nextroute_qry) {
                $trail_arr = array(
                    'trnid' => $trnid,
                    'stageid' => $nextroute_qry->sysid,
                    'dataid' => $prfid,
                    'createdby' => user_id(),
                    //'status' => $stats
                );

                $forward = task_ins_process($trail_arr, null, null);
                if ($forward->qry && !in_array(false, $transactions)) {
                    $this->db->trans_commit();
                    $qry = true;
                    $msg = 'You have approved this ' . $typename->names . ' and is forwarded to the next stage.';
                    $func = 'success';
                    $title = $typename->names . ' Approved!';
                    $url = base_url('module/49e3d046636e06b2d82ee046db8e6eb9a2e11e16/view/' . $prfid);
                } else {
                    $this->db->trans_rollback();
                    $qry = true;
                    $msg = 'Failed to approve this ' . $typename->names . '.';
                    $func = 'error';
                    $title = $typename->names . ' Approval Failed!';
                }
            }
        }

        $data['url'] = $url;
        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }

    function disapprove_prf()
    {
        $data = array();
        $prfid = $this->input->post('prfid');
        $remarks = $this->input->post('remarks');
        $type = $this->input->post('type');
        $flowid = $this->input->post('flowid');
        $stageid = $this->input->post('stageid');
        $trnid = $this->input->post('trnid');

        $msg = '';
        $func = '';
        $title = '';
        $qry = false;

        //CHANGE STATUS OF QUOTATIONS TO 302
        if (in_array($type, array(1206, 1207))) {
            $this->db->trans_begin();
            $disapprove = update_db($this->db, 'eprs_transaction', array('status' => 302), array('sysid' => $prfid));

            if ($disapprove->qry) {
                update_db($this->db, 'transaction_request_main_trails', array('status' => 0), array('dataid' => $prfid, 'trnid' => $trnid, 'status' => 1));
                $this->db->trans_commit();
                $msg = 'This PRF has been disapproved!';
                $func = 'success';
                $title = 'Disapproved!';
                $qry = true;
            } else {
                $this->db->trans_rollback();
                $msg = 'Failed to disapprove item!';
                $func = 'error';
                $title = 'FAIL!';
            }
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }

    function requote_rfq()
    {
        $data = array();
        $prfid = $this->input->post('prfid');
        $remarks = $this->input->post('remarks');
        $type = $this->input->post('type');
        $flowid = $this->input->post('flowid');
        $stageid = $this->input->post('stageid');
        $trnid = $this->input->post('trnid');

        $logged = false;
        $url = '';

        //Remove previous logs and
        $update_logs = update_db($this->db, 'eprs_transaction_logs', array('status' => 0), array('prsid' => $prfid, 'typesid' => $type, 'status' => 1));
        if ($update_logs->qry) {
            $stage = get_stage_details($stageid);

            $this->db->trans_begin();
            $eprs_log = array(
                'prsid' => $prfid,
                'typesid' => $type,
                'remarks' => $remarks,
                'moduleid' => $stage->moduleid,
                'statusid' => 302,
                'status' => 0
            );

            $insert_log = insert_db($this->db, 'eprs_transaction_logs', $eprs_log);
            if ($insert_log->qry) {
                $logged = true;
            }
        }

        //Return to RFQ. Allow Purchasing to bypass qty.

        $trail_arr = array(
            'trnid' => $trnid,
            'stageid' => 104,
            'dataid' => $prfid,
            'createdby' => user_id(),
            //'status' => $stats
        );

        $sendback = task_ins_process($trail_arr, null, null);
        if ($logged && $sendback->qry) {
            $this->db->trans_commit();
            $qry = true;
            $msg = 'You have returned this transaction to Purchasing for requotation.';
            $func = 'success';
            $title = 'Items\' Quotation Returned!';
            $url = base_url('module/49e3d046636e06b2d82ee046db8e6eb9a2e11e16/view/' . $prfid);
        } else {
            $this->db->trans_rollback();
            $qry = true;
            $msg = 'Failed to return this transaction o purchasing.';
            $func = 'error';
            $title = 'Transaction Return Failed!';
        }

        $data['url'] = $url;
        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }

    function return_prf()
    {
        $data = array();
        $prfid = $this->input->post('prfid');
        $remarks = $this->input->post('remarks');
        $type = $this->input->post('type');
        $flowid = $this->input->post('flowid');
        $stageid = $this->input->post('stageid');
        $trnid = $this->input->post('trnid');

        $logged = false;
        $url = '';

        //Return to RFQ. Allow Purchasing to bypass qty.

        $trail_arr = array(
            'trnid' => $trnid,
            'stageid' => 102,
            'dataid' => $prfid,
            'createdby' => user_id(),
            //'status' => $stats
        );

        $sendback = task_ins_process($trail_arr, null, null);
        if ($sendback->qry) {
            $this->db->trans_commit();
            $qry = true;
            $msg = 'You have returned this transaction to the requestor.';
            $func = 'success';
            $title = 'Items\' Request Returned!';
            $url = base_url('module/49e3d046636e06b2d82ee046db8e6eb9a2e11e16/view/' . $prfid);
        } else {
            $this->db->trans_rollback();
            $qry = false;
            $msg = 'Failed to return this transaction to the requestor.';
            $func = 'error';
            $title = 'Transaction Return Failed!';
        }

        $data['url'] = $url;
        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }

    function get_rfq_items_list()
    {
        $data = array();
        $columns = array(
            dt_column_array('num', '#', 'text-align-right', '25px'),
            dt_column_array('item', 'Items', false, '18%'),
            dt_column_array('lastprice', 'Last Price', 'text-primary bold number', '100px'),
            dt_column_array('qty', 'Qty', 'number', '80px'),
            dt_column_array('unit', 'Unit', 'number', '80px'),
        );

        $itemlist = array();

        $roles = get_users_roles_matrix_id_arr();


        $prfid = $this->input->post('prfid');
        //$approval = $this->input->post('approval');

        //GET FLOW STAGES
        $stages_qry = $this->db->select()
            ->from('prime_transaction_flow_main_stages')
            ->where(array('flowid' => 3, 'status' => 1))
            ->get();

        $approval = false;

        if ($stages_qry->num_rows() > 0) {
            $stages = $stages_qry->result();

            $stageids = array_column($stages, 'sysid');
            $isApproval = array();

            $current_qry = $this->db->select()
                ->from('transaction_request_main_trails')
                ->where(array('dataid' => $prfid, 'status' => 1))
                ->where_in('stageid', $stageids)
                ->order_by('datecreated DESC')
                ->get()->row();

            $current = $current_qry->stageid;

            foreach ($stages as $stage) {
                $isApproval[$stage->sysid] = (bool) strpos(strtolower($stage->desc), 'approval');
            }

            $approval = $isApproval[$current];
        }

        if (!is_bool($approval)) {
            $approval = filter_var($approval, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        $suppliers_qry = $this->db->select('eqs.sysid, eqs.supplierid, s.descs AS name, s.codes, s.currency, eqs.exrate')
            ->from('eprs_suppliers_main as s')
            ->join('eprs_quotation_suppliers as eqs', 'eqs.supplierid = s.sysid', 'left')
            ->where(array('eqs.prfid' => $prfid, 'eqs.status' => 1))
            ->get();

        $item_quotations = array();
        $suppliers = array();
        $suppliers_curr = array();
        if ($suppliers_qry->num_rows() > 0) {
            foreach ($suppliers_qry->result() as $supplier) {
                //add edit modal button on column header
                $supplier_currrency = get_currency($supplier->currency);
                $suppliers_curr[] = $supplier->currency;
                $supplier_buttons = '';
                if (in_array(24, $roles) || super_admin()) {
                    $supplier_buttons .= '<div class="btn-group">';
                    $supplier_buttons .= ' <a href="frm_supplier_quotations" data-arr="' . $prfid . ',' . $supplier->sysid . '" data-toggle="ajax-modal" title="Edit Supplier Quotations" class="btn btn-primary inline"><i class="fa fa-edit"></i> </a>';
                    $supplier_buttons .= (!$approval) ? ' <button type="button" id="btn_delete_supplier_quote" data-id="' . $supplier->sysid . '" title="Delete Supplier Quotations" class="btn btn-danger inline"><i class="fa fa-trash"></i> </button>' : '';
                    $supplier_buttons .= '</div>';
                }
                if ($supplier->currency != 83) {
                    $columns[] = dt_column_array(strtolower($supplier->codes . '_c'), $supplier->name . ' (' . $supplier_currrency->name . ')' . $supplier_buttons, '', '100px');
                    $columns[] = dt_column_array(strtolower($supplier->codes . '_p'), $supplier->name . ' (Peso)', '', '100px');
                    $data['suppliers'][] = dt_column_array(strtolower($supplier->codes . '_c'), $supplier->name . '(' . $supplier_currrency->name . ')' . $supplier_buttons, '', '100px');
                    $data['suppliers'][] = dt_column_array(strtolower($supplier->codes . '_p'), $supplier->name . '(Peso)' . $supplier_buttons, '', '100px');
                    $suppliers[] = strtolower($supplier->codes . '_c');
                    $suppliers[] = strtolower($supplier->codes . '_p');
                } else {
                    $columns[] = dt_column_array(strtolower($supplier->codes), $supplier->name . $supplier_buttons, '', '100px');
                    $data['suppliers'][] = dt_column_array(strtolower($supplier->codes), $supplier->name . $supplier_buttons, '', '100px');
                    $suppliers[] = strtolower($supplier->codes);
                }

                //Query items quoted under this supplier
                $item_quotation_qry = $this->db->select('')
                    ->from('eprs_quotation_details as eqd')
                    ->where(array('eqd.quotationid' => $supplier->sysid, 'eqd.status != ' => 0))
                    ->get();

                if ($item_quotation_qry->num_rows() > 0) {
                    foreach ($item_quotation_qry->result() as $quotation) {
                        if ($supplier->currency != 83) {
                            $item_quotations[strtolower($supplier->codes) . '_c'][$quotation->prfitemid] = array(
                                'id' => $quotation->sysid,
                                'supplierid' => $supplier->sysid,
                                'amount' => $quotation->amount,
                                'status' => $quotation->status,
                                'currency' => get_currency($supplier->currency)
                            );
                            $item_quotations[strtolower($supplier->codes) . '_p'][$quotation->prfitemid] = array(
                                'id' => $quotation->sysid,
                                'supplierid' => $supplier->sysid,
                                'amount' => $quotation->amount * ceil($supplier->exrate),
                                'status' => $quotation->status,
                                'currency' => get_currency(83)
                            );
                        } else {
                            $item_quotations[strtolower($supplier->codes)][$quotation->prfitemid] = array(
                                'id' => $quotation->sysid,
                                'supplierid' => $supplier->sysid,
                                'amount' => $quotation->amount,
                                'status' => $quotation->status,
                                'currency' => get_currency($supplier->currency)
                            );
                        }
                    }
                }
            }
        } else {
            $columns[] = dt_column_array('suppliers', 'Suppliers\' Quotations', '', '');
        }
        $total_cols = array();
        $cur_totals = array();
        $cur_subtotals = array();
        if (count($suppliers_curr) > 0) {
            $non_peso = array();
            foreach ($suppliers_curr as $currency) {
                if ($currency != 83 && !in_array($currency, $non_peso)) {
                    $non_peso[] = $currency;
                }
            }

            if (count($non_peso) > 0) {
                foreach ($non_peso as $cur) {
                    $curr_vals = get_currency($cur);
                    $code = strtolower($curr_vals->code);
                    $total_cols[] = dt_column_array($code . '_total', 'Total (' . $curr_vals->code . ')', 'totals number', '100px');
                    $cur_totals[] = $code . '_total';
                    $cur_subtotals[] = $code . '_subtotal_amt';
                }
                $total_cols[] = dt_column_array('php_total', 'Total (Converted)', 'totals number', '100px');
                $cur_totals[] = 'php_total';
                $cur_subtotals[] = 'php_subtotal_amt';

                if (in_array(83, $suppliers_curr)) {
                    $total_cols[] = dt_column_array('total', 'Total (PHP)', 'totals number', '100px');
                    $cur_totals[] = 'total';
                    $cur_subtotals[] = 'subtotal_amt';
                }
            } else {
                $total_cols[] = dt_column_array('total', 'Total', 'totals number', '100px');
                $cur_totals[] = 'total';
                $cur_subtotals[] = 'subtotal_amt';
            }
        } else {
            $total_cols[] = dt_column_array('total', 'Total', 'totals number', '100px');
            $cur_totals[] = 'total';
            $cur_subtotals[] = 'subtotal_amt';
        }

        $data['total_cols'] = $total_cols;
        $data['subtotals'] = $cur_subtotals;

        $end_cols = array(
            dt_column_array('remarks', 'Remarks', '', '20%'),
            dt_column_array('control', 'Control', 'text-align-center', '10%'),
        );

        $columns = array_merge($columns, $total_cols, $end_cols);
        /*if ($approval) {
            $columns[] = dt_column_array('control','Control','text-align-center','10%');
        }*/

        $prf_qry = $this->db->select('eti.itemid,eti.sysid,eti.prfid,imd.fulldescription,eti.qty,eti.remarks,u.unit_name,u.unit_code,eti.unitid,eqr.remarks AS rfqremarks,et.status')
            ->from('eprs_transaction_items AS eti')
            ->join('eprs_transaction AS et', 'eti.prfid = et.sysid', 'left')
            ->join('items_main_description AS imd', 'eti.itemid = imd.sysid', 'left')
            ->join('eprs_quotation_remarks AS eqr', 'eqr.prfitemid = eti.sysid AND eqr.status = 1', 'left')
            ->join('prime_unit AS u', 'eti.unitid = u.sysid', 'left')
            ->where('eti.status', 305)
            ->where('eti.prfid', $prfid)
            ->get();

        //$data['item_qry'] = $this->db->last_query();

        if ($prf_qry->num_rows() > 0) {
            $n = 1;
            foreach ($prf_qry->result() as $item) {
                $unit = unit_query($item->unitid);
                $control = '';
                $control .= '<div class="btn-group">';
                $control .= ((in_array(24, $roles) && !in_array($item->status, array(0, 302, 303))) || super_admin()) ? '<button type="button" class="btn btn-danger inline" id="btn_edit_rfq_qty" data-id="' . $item->sysid . '"><i class="fa fa-edit"></i></button>' : '';

                $comments = $this->db->select('COUNT(messages) AS cnt')
                    ->from('comments')
                    ->where(array(
                        'types' => 3439,
                        'moduleid' => 193,
                        'dataid' => $item->sysid,
                        'stageid' => 104,
                        'status' => 1
                    ))->get()->row();
                $control .= btn_comment($item->sysid, $comments->cnt);
                $control .= '</div>';

                $unitn = ($unit) ? (($unit->name == $unit->code) ? $unit->name : $unit->name . ' (' . $unit->code . ')') : 'unit';

                //QUERY LAST ITEM PRICE STATUS 301
                $lastprice_qry = $this->db->select('qd.amount')
                    ->from('eprs_transaction_items AS ti')
                    ->join('eprs_quotation_details AS qd', 'ti.sysid = qd.prfitemid', 'left')
                    ->where(array('ti.itemid' => $item->itemid, 'qd.status' => 301))
                    ->order_by('qd.datecreated DESC')->get()->row();

                //$data['lastprice_qry'][] = $this->db->last_query();

                $lastprice = ($lastprice_qry && $lastprice_qry->amount > 0) ? $lastprice_qry->amount : 0;
                $itemremarks = ($item->remarks && $item->remarks != '') ? preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank" rel="nofollow">Link <i class="fa fa-link"></i></a>', $item->remarks) : '';

                $itemlist = array(
                    'num' => $n++,
                    'item' => $item->fulldescription . (($itemremarks != '') ? ' (' . (strpos($itemremarks, 'href') !== false ? $itemremarks : ellipsis($itemremarks, 15)) . ')' : ''),
                    'lastprice' => '<a href="#tbl_item_last_price" class="margin-left-10" data-toggle="ajax-modal" data-arr="' . $item->itemid . '" title="' . $item->fulldescription . ' Last Price"><i class="fa fa-history pull-left"></i> ' . number_format($lastprice, 2) . '</a>',
                    'qty' => '<span id="prf_qty">' . $item->qty . '</span>',
                    'unit' => '<span id="prf_unit_name" data-id="' . $item->unitid . '">' . $unitn . '</span>',
                    'remarks' => ($approval || in_array($item->status, array(0, 302, 303))) ? $item->rfqremarks : '<textarea id="input_remarks" value="" name="item_remarks[' . $item->sysid . ']" rows="1" style="width: 100% !important;" class="form-control" maxlength="255">' . $item->rfqremarks . '</textarea>',
                    'control' => $control,
                );

                if (count($cur_totals) > 0) {
                    foreach ($cur_totals as $total_val) {
                        $itemlist[$total_val] = '<span class="est_total_amt" data-currency="' . $total_val . '">-</span>';
                    }
                }

                if (count($suppliers) > 0) {
                    $active_currencies = array_unique($suppliers_curr);
                    foreach ($suppliers as $supp) {
                        if (key_exists($item->sysid, $item_quotations[$supp])) {
                            //if last price supplier = supp, generate total
                            //Add inline checkbox
                            //STATUS CHECKED IF STATUS 305
                            //Find last price based on $item->itemid.
                            $qt = $item_quotations[$supp][$item->sysid];

                            $qt_currency = $qt['currency'];
                            $qt_amt = $qt['amount'];
                            $qt_id = $qt['id'];
                            $qt_sid = $qt['supplierid'];
                            $checked = ($qt['status'] == 305 && ($qt_currency->sysid == 83 || strpos($supp, '_c'))) ? 'checked' : '';
                            $highlight = ($qt['status'] == 305 && $qt_currency->sysid == 83) ? 'bold' : '';
                            $currency_symbol = (strpos($supp, '_c') || strpos($supp, '_p')) ? $qt_currency->symbol : '';
                            $qt_damount = ($qt_currency->sysid != 83) ? $currency_symbol . number_format($qt['amount'], 2) : $currency_symbol . number_format($qt_amt, 2);
                            $qt_total = (strpos($supp, '_c') || strpos($supp, '_p')) ? strtolower($qt_currency->code) . '_total' : 'total';

                            $icon = ($checked == 'checked') ? '<i class="quoted fa fa-check text-primary"></i>' : '';
                            if ($approval) {
                                $itemlist[$supp] = $icon . '<span id="rfq_item_price" data-price="' . $qt_amt . '" data-supplier="" data-value="' . $qt_id . '" data-item="' . $item->sysid . '" class="pull-right ' . $highlight . '">' . $qt_damount . '</span>';
                            } else {
                                $radio = (strpos($supp, '_c') || ($qt_currency->sysid == 83 && !strpos($supp, '_p'))) ? '<input type="radio" class="icheck" name="amount[' . $item->sysid . ']" id="icheck_input" data-currency="' . $qt_total . '" value="' . $qt_id . '" ' . $checked . ' required/>' : '';
                                $itemlist[$supp] = (!in_array($item->status, array(0, 302, 303)) ? $radio : $icon) . '<span id="rfq_item_price" data-price="' . $qt_amt . '" data-supplier="' . $qt_sid . '" class="pull-right ' . $highlight . '">' . $qt_damount . '</span>';
                            }
                            if ($qt['status'] == 305) {
                                if (count($cur_totals) > 0) {
                                    foreach ($cur_totals as $total_val) {
                                        if ($qt_total == $total_val) {
                                            $itemlist[$total_val] = '<span class="est_total_amt" data-currency="' . $total_val . '">' . number_format($item->qty * $qt_amt, 2) . '</span>';
                                        }
                                    }
                                }
                                //$itemlist['total'] = '<span id="est_total_amt">' . number_format($item->qty * $qt_amt, 2) . '</span>';
                            }
                        } else {
                            $itemlist[$supp] = '<span id="rfq_item_price" class="pull-right">-</span>';
                        }
                    }
                } else {
                    $itemlist['suppliers'] = '-';
                }

                $data['itemlist'][] = $itemlist;
            }
        }

        $data['columns'] = $columns;

        return json_encode($data);
    }

    function dt_quotation_items()
    {
        $data = array();

        $dataid = $this->input->post('dataid');
        $supplier = $this->input->post('supplier');

        /*
         * LOOKUP IF QUOTATION FOR SUPPLIER EXISTS DISPLAY PRICE AS VALUE.
         * DO NOT DISPLAY STATUS ZERO (0) QUOTATIONS IN LAST PRICE
         * LOOKUP LAST PRICE NOT EQUAL TO CURRENT QOTATION
         */

        if ($supplier && $supplier > 0) {
            //CHECK IF SUPPLIERID IS THE QUOTATIONID OR SUPPLIER SYSID
            $supplier_qry = $this->db->select('esm.currency,eqs.exrate')
                ->from('eprs_quotation_suppliers as eqs')
                ->join('eprs_suppliers_main as esm', 'esm.sysid = eqs.supplierid')
                ->where(array('eqs.sysid' => $supplier, 'eqs.prfid' => $dataid))
                ->get()->row();

            if (!$supplier_qry) {
                $supplier_qry = $this->db->select('*')
                    ->from('eprs_suppliers_main')
                    ->where('sysid', $supplier)->get()->row();
            }

            if ($supplier_qry->currency != 83) {
                $currency = get_currency($supplier_qry->currency);
                $data['exchange_rate'] = '<span class="pull-left">Conversion: ' . $currency->fullname . ' to Peso</span> <span class="col-md-3"><input type="number" name="exrate" value="' . ($supplier_qry->exrate ?? $currency->conversion) . '" class="form-control input-small" step="any"></span>';
            }

            $prf_qry = $this->db->select('eti.itemid,eti.remarks,eti.sysid,imd.fulldescription,u.unit_name,u.unit_code,eti.unitid')
                ->from('eprs_transaction_items AS eti')
                ->join('items_main_description AS imd', 'eti.itemid = imd.sysid', 'left')
                ->join('prime_unit AS u', 'eti.unitid = u.sysid', 'left')
                ->where('eti.status', 305)
                ->where('eti.prfid', $dataid)
                ->get();

            if ($prf_qry->num_rows() > 0) {
                $n = 1;
                foreach ($prf_qry->result() as $item) {
                    $current_amount = '';
                    //LOOKUP CURRENT ACTIVE PRICE
                    $current_quote = $this->db->select('eqd.amount')
                        ->from('eprs_quotation_details as eqd')
                        ->where(array('eqd.prfitemid' => $item->sysid, 'eqd.quotationid' => $supplier))
                        ->where_in('eqd.status', array(1, 305))
                        ->get()->row();

                    //$data['current_quote'][$item->itemid]['qry'] = $this->db->last_query();
                    //$data['current_quote'][$item->itemid]['val'] = ($current_quote && $current_quote->amount > 0) ? $current_quote->amount : false;

                    if ($current_quote && $current_quote->amount > 0) {
                        $current_amount = $current_quote->amount;
                    }

                    $itemremarks = ($item->remarks && $item->remarks != '') ? preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '', $item->remarks) : '';

                    $itemlist = array(
                        'num' => '<input type="checkbox" class="icheck" id="prf_item_id" name="prfitemid[]" value="' . $item->sysid . '" /> ', //.$n++, //.'<input type="hidden" id="prf_item_id" name="prfitemid[]" value="'.$item->sysid.'" disabled>',
                        'item' => $item->fulldescription . (($itemremarks != '') ? ' (' . $itemremarks . ')' : ''),
                        //'amount' => dt_inline_input('amount',false,false,false,false,false,true) //CREATE TEXTBOX FOR NEW QUOTATION
                        'amount' => '<input type="number" id="input_amount" value="' . $current_amount . '" name="amount[]" step="any" class="form-control" style="width: 100px !important;" disabled>',
                        'remarks' => '<input id="input_remarks" value="" name="remarks[]" class="form-control" disabled>',
                    );

                    //QUERY LATEST QUOTATION FROM ITEMS QUOTE OF THE SAME SUPPLIER
                    $lastquote_qry = $this->db->select('eqd.amount')
                        ->from('eprs_quotation_details as eqd')
                        ->join('eprs_quotation_suppliers as eqs', 'eqd.quotationid = eqs.sysid', 'left')
                        ->join('eprs_transaction_items AS eti', 'eqd.prfitemid = eti.sysid', 'left')
                        ->where(array(
                            'eti.itemid' => $item->itemid,
                            'eqs.supplierid' => $supplier,
                        ))
                        ->where_not_in('eqd.status', array(0, 303))
                        ->order_by('eqd.sysid DESC')->get()->row();

                    if ($lastquote_qry) {
                        $itemlist['lastquote'] = number_format($lastquote_qry->amount, 2);

                        $itemlist['sameamount'] = '<input type="checkbox" class="icheck" name="amount[]" id="same_amount" value="' . $lastquote_qry->amount . '" disabled />';
                    } else {
                        $itemlist['lastquote'] = 'N/A';

                        $itemlist['sameamount'] = '<i class="fa fa-times-rectangle-o text-danger"></i>';
                    }

                    $data['itemlist'][] = $itemlist;
                }
            }
        }

        $data['columns'] = array(
            dt_column_array('num', '<input type="checkbox" class="icheck" id="select_all" />', 'number', '10px'),
            dt_column_array('item', 'Items', '', '300px'),
            dt_column_array('lastquote', 'Last Quote', 'number', '50px'),
            dt_column_array('amount', 'Amount', '', '80px'),
            dt_column_array('sameamount', 'Same Amt', 'text-align-center', '35px'),
            dt_column_array('remarks', 'Remarks', 'text-align-center', '50px'),
        );

        return json_encode($data);
    }

    function select2_quotation_supplier()
    {
        $data = array();
        $prfid = $this->input->post('data');

        $existing_supplier = $this->db->select('supplierid')
            ->from('eprs_quotation_suppliers')
            ->where(array('prfid' => $prfid))
            ->where_not_in('status', array(0, 303))
            ->get();

        $ids = array();
        if ($existing_supplier->num_rows() > 0) {
            foreach ($existing_supplier->result() as $quotation) {
                $ids[] = $quotation->supplierid;
            }
            $this->db->where_not_in('esm.sysid', $ids);
        }

        $supplier_qry = $this->db->select('esm.sysid,esm.descs')
            ->from('eprs_suppliers_main as esm')
            ->where(array(
                'esm.status' => 1
            ))
            ->get();

        if ($supplier_qry->num_rows() > 0) {
            foreach ($supplier_qry->result() as $supplier) {
                $data['list'][] = array(
                    'id' => $supplier->sysid,
                    'text' => $supplier->descs
                );
            }
        }

        return json_encode($data);
    }

    function add_supplier_quotation()
    {
        $data = array();
        $supplierid = $this->input->post('supplier');
        $rfop = $this->input->post('rfop');
        $appid = $this->input->post('appid');
        $prfitemid = $this->input->post('prfitemid');
        $amount = $this->input->post('amount');
        $remarks = $this->input->post('remarks');
        $exvat = $this->input->post('exvat');
        $exrate = $this->input->post('exrate');
        $paytype = $this->input->post('paytype');
        $payterm = $this->input->post('paymentterm');
        $paypurpose = $this->input->post('purpose');
        $ponotes = $this->input->post('ponotes');
        $poid = 0;
        $msg = '';
        $func = '';
        $qry = false;
        $title = '';

        $trn = array();

        $this->db->trans_begin();
        //CREATE OR FIND PRS/PO FOR PRF
        $prs_qry = $this->db->select()
            ->from('eprs_po')
            ->where(array('prfid' => $appid, 'status' => 1))
            ->get()->row();

        if ($prs_qry) {
            //SET PO ID
            $poid = $prs_qry->sysid;
        } else {
            $ponum = date('mdY');
            $new_po = insert_db($this->db, 'eprs_po', array('prfid' => $appid, 'ponumber' => $ponum));

            if ($new_po->qry) {
                $poid = $new_po->insert_id;
                $data['ponum'] = 'PAE-' . str_pad($ponum, 8, '0', STR_PAD_LEFT);
                ;
            }
        }

        //LOOKUP EXISTING QUOTATION FOR SUPPLIER
        $quotation_qry = $this->db->select('sysid')
            ->from('eprs_quotation_suppliers')
            ->where(array('prfid' => $appid, 'supplierid' => $supplierid))
            ->where_not_in('status', array(303, 302, 0))
            ->get()->row();

        $quotationid = 0;
        if ($quotation_qry) {
            $quotationid = $quotation_qry->sysid;
        } else {
            //CREATE QUOTATION FOR SUPPLIER
            $supp_arr = array(
                'prfid' => $appid,
                'supplierid' => $supplierid,
                'rfop' => $rfop,
                'exvat' => $exvat,
                'exrate' => $exrate,
                'paytype' => $paytype,
                'quotationhash' => sha1($appid . '-' . $supplierid . '-' . date('Y-m-d H:i:s', time()))
            );
            $supplier_q = insert_db($this->db, 'eprs_quotation_suppliers', $supp_arr);
            if ($supplier_q->qry) {
                $quotationid = $supplier_q->insert_id;
            }
        }

        if ($quotationid > 0) {
            $trn['newquote'] = true;
            if (is_array($prfitemid) && count($prfitemid) > 0 && is_array($amount) && count($amount) > 0) {
                foreach ($prfitemid as $index => $itemid) {
                    if ($amount[$index] != '' && $amount[$index] > 0) {
                        //LOOKUP EXISTING ITEM QUOTATION
                        $item_qry = $this->db->select()
                            ->from('eprs_quotation_details')
                            ->where(array('quotationid' => $quotationid, 'prfitemid' => $itemid))
                            ->where_not_in('status', array(303, 302, 0))
                            ->get()->row();

                        $amount_q = 0;
                        $remarks_q = '';
                        $item_qid = 0;
                        if ($item_qry) {
                            $amount_q = $item_qry->amount;
                            $remarks_q = $item_qry->remarks;
                            $item_qid = $item_qry->sysid;
                        }

                        if ($amount_q != floatval($amount[$index])) {
                            update_db($this->db, 'eprs_quotation_details', array('status' => 0), array('quotationid' => $quotationid, 'prfitemid' => $itemid));
                            $item_arr = array(
                                'quotationid' => $quotationid,
                                'prfitemid' => $itemid,
                                'amount' => $amount[$index],
                                'remarks' => $remarks[$index]
                            );
                            $item_q = insert_db($this->db, 'eprs_quotation_details', $item_arr);

                            if ($item_q->qry) {
                                $trn['itemquote_' . $index] = true;
                            } else {
                                $trn['itemquote_' . $index] = false;
                            }
                        } else {
                            if (trim($remarks_q) != trim($remarks[$index])) {
                                $item_q = update_db($this->db, 'eprs_quotation_details', array('remarks' => $remarks[$index]), array('sysid' => $item_qid));
                                if ($item_q->qry) {
                                    $trn['itemquote_' . $index] = true;
                                } else {
                                    $trn['itemquote_' . $index] = false;
                                }
                            }
                        }
                    }
                }
            } else {
                $trn['newquote'] = false;
            }

            //ADD SUPPLIER TO PO
            $rfop_arr = array(
                'poid' => $poid,
                'quotationid' => $quotationid,
                'paytype' => $paytype,
                'payterm' => $payterm,
                'purpose' => $paypurpose,
                'notes' => $ponotes
            );
            $new_rfop = insert_db($this->db, 'eprs_po_details', $rfop_arr);

            if ($new_rfop->qry) {
                $trn['rfop'] = true;
            } else {
                $trn['rfop'] = false;
            }
        } else {
            $trn['newquote'] = false;
        }

        if ($exrate && $exrate > 0) {
            $supplier_qry = $this->db->select('*')
                ->from('eprs_suppliers_main')
                ->where('sysid', $supplierid)->get()->row();

            $currency = get_currency($supplier_qry->currency);

            if ($currency->conversion != $exrate) {
                $currency_update = update_db($this->db, 'currency', array('conversion' => $exrate), array('sysid' => $supplier_qry->currency));
                if ($currency_update->qry) {
                    $trn['currency_update'] = true;
                } else {
                    $trn['currency_update'] = false;
                }
            }
        }

        if (count($trn) > 0 && !in_array(false, $trn)) {
            $this->db->trans_commit();
            $msg = 'Item quotations saved!';
            $func = 'success';
            $qry = true;
            $title = 'Saved!';
        } else {
            $this->db->trans_rollback();
            $msg = 'Item quotations not saved!';
            $func = 'error';
            $qry = false;
            $title = 'Fail!';
        }

        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['title'] = $title;

        return json_encode($data);
    }

    function save_prf_quotation()
    {
        $data = array();
        $prfid = $this->input->post('prfid');
        $amount = $this->input->post('amount');
        $remarks = $this->input->post('item_remarks');
        $rfqremarks = $this->input->post('rfqremarks');
        $shipping = $this->input->post('shipping');
        $trnid = $this->input->post('trnid');
        $stageid = $this->input->post('stageid');
        $flowid = $this->input->post('flowid');

        $qry = false;
        $msg = '';
        $func = '';
        $title = '';
        $url = '';

        $trn = array();

        $this->db->trans_begin();
        $quotations = $this->save_quotation();
        $trn = array_merge($trn, $quotations->trn);

        //INSERT SHIPPING COST TO SUPPLIER
        if (is_array($shipping) && count($shipping) > 0) {
            foreach ($shipping as $quotationid => $shippingamount) {
                if ($shippingamount > 0) {
                    update_db($this->db, 'eprs_quotation_suppliers', array('shipping' => $shippingamount), array('sysid' => $quotationid));
                }
            }
        }

        if (trim($rfqremarks) != '') {
            $comments_arr = array(
                'trnid' => $trnid,
                'trailid' => $stageid,
                'remarks' => $rfqremarks
            );
            insert_db($this->db, 'transaction_request_trails_comments', $comments_arr);
        }

        $stage = get_stage_details($stageid);

        //Insert trn to logs

        $logs_arr = array(
            'prsid' => $prfid,
            'moduleid' => $stage->moduleid,
            'typesid' => 1207,
            'statusid' => 305,
            'remarks' => $rfqremarks
        );

        insert_db($this->db, 'eprs_transaction_logs', $logs_arr);

        $nextroute_qry = $this->db->select('sysid')
            ->from('prime_transaction_flow_main_stages')
            ->where(array('flowid' => $flowid, 'levels >' => $stage->levels, 'status' => 1))
            ->order_by('levels ASC')
            ->get()->row();

        if ($nextroute_qry) {
            $trail_arr = array(
                'trnid' => $trnid,
                'stageid' => $nextroute_qry->sysid,
                'dataid' => $prfid,
                'createdby' => user_id(),
                //'status' => $stats
            );

            $forward = task_ins_process($trail_arr, null, null);
            if ($forward->qry && !in_array(false, $trn)) {
                $this->db->trans_commit();
                $qry = true;
                $msg = 'You have forwarded quotations for approval.';
                $func = 'success';
                $title = 'RFQ Sent!';
                $url = base_url('module/49e3d046636e06b2d82ee046db8e6eb9a2e11e16/view/' . $prfid);
            } else {
                $this->db->trans_rollback();
                $qry = true;
                $msg = 'Unable to forward quotations for approval.';
                $func = 'error';
                $title = 'Fail!';
            }
        }

        $data['url'] = $url;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['title'] = $title;

        return json_encode($data);
    }

    function save_quotation()
    {
        $data = array();
        $prfid = $this->input->post('prfid');
        $amount = $this->input->post('amount');
        $remarks = $this->input->post('item_remarks');

        $trn = array();

        if (is_array($amount) && count($amount) > 0) {
            foreach ($amount as $itemid => $itemquote) {

                //LOOKUP PREVIOUSLY SUBMITTED QUOTATIONS OF THE SAME PRF AND ITEM
                $find_previous = $this->db->select('sysid')
                    ->from('eprs_quotation_details')
                    ->where(array(
                        'prfitemid' => $itemid,
                        'sysid !=' => $itemquote,
                        'status' => 305
                    ))->get();

                if ($find_previous->num_rows() > 0) {
                    foreach ($find_previous->result() as $rollback) {
                        $set = array(
                            'status' => 1
                        );
                        $where = array(
                            'sysid' => $rollback->sysid
                        );

                        update_db($this->db, 'eprs_quotation_details', $set, $where);
                    }
                }

                $set = array(
                    'status' => 305
                );
                $where = array(
                    'sysid' => $itemquote
                );

                $for_approval = update_db($this->db, 'eprs_quotation_details', $set, $where);

                $trn['update_' . $itemquote] = $for_approval->qry;

                //INSERT REMARKS IF AVAILABLE
                if ($remarks[$itemid] != '') {
                    $values = array(
                        'prfid' => $prfid,
                        'prfitemid' => $itemid,
                        'remarks' => $remarks[$itemid]
                    );

                    //REMOVE PREVIOUS REMARKS
                    update_db($this->db, 'eprs_quotation_remarks', array('status' => 0), array('prfid' => $prfid, 'prfitemid' => $itemid));
                    $add_remarks = insert_db($this->db, 'eprs_quotation_remarks', $values);
                    $trn['remarks_' . $itemquote] = $add_remarks->qry;
                }
            }
        }
        $data['trn'] = $trn;
        if (in_array(false, $trn)) {
            $data['qry'] = true;
        } else {
            $data['qry'] = false;
        }

        return (object) $data;
    }

    function get_supplier_summary_of_cost()
    {
        $data = array();
        $id = $this->input->post('id');
        $approval = $this->input->post('approval');
        $stotal = 0;
        $grosstotal = 0;
        $gtotal = array();
        $ctotal = array();
        $s_currency = array();

        $prf_qry = $this->db->select()
            ->from('eprs_transaction')
            ->where('sysid', $id)
            ->get()->row();

        $suppliers_qry = $this->db->select('eqs.sysid, eqs.exvat, eqs.shipping, eqs.supplierid, eqs.paytype, s.descs AS name, s.codes,s.currency,s.type')
            ->from('eprs_suppliers_main as s')
            ->join('eprs_quotation_suppliers as eqs', 'eqs.supplierid = s.sysid', 'left')
            ->where(array('eqs.prfid' => $id, 'eqs.status' => 1))
            ->get();

        if ($suppliers_qry->num_rows() > 0) {
            //query checked or quoted supplier and sum each with item qty
            $suppliers_result = $suppliers_qry->result();
            $active_currencies = array_column($suppliers_result, 'currency');
            $filtered_currencies = array_unique($active_currencies);
            $data['currencies'] = $filtered_currencies;
            foreach ($suppliers_result as $supplier) {
                $supp_total = 0;
                $netvat = 0;
                $vat = 0;
                $gross = 0;
                $ewt = 0;
                $total = 0;
                $total_c = 0;
                $total_php = 0;
                $currency = get_currency($supplier->currency);
                $s_currency[] = $supplier->currency;
                $price_qry = $this->db->select('eti.qty,eqd.amount,eqd.status')
                    ->from('eprs_quotation_details AS eqd')
                    ->join('eprs_transaction_items AS eti', 'eqd.prfitemid = eti.sysid', 'left')
                    ->where(array('eqd.quotationid' => $supplier->sysid, 'eqd.status != ' => 0))
                    ->get();

                if ($price_qry->num_rows() > 0) {
                    foreach ($price_qry->result() as $item) {
                        if ($item->status == 305) {
                            if ($supplier->currency != 83) {
                                $convert = $currency->conversion;
                                if (!isset($ctotal[$supplier->currency])) {
                                    $ctotal[$supplier->currency] = $item->qty * $item->amount;
                                } else {
                                    $ctotal[$supplier->currency] += $item->qty * $item->amount;
                                }

                                $c_amt = ceil($convert) * $item->amount;
                                if (!isset($ctotal['c'])) {
                                    $ctotal['c'] = $item->qty * $c_amt;
                                } else {
                                    $ctotal['c'] += $item->qty * $c_amt;
                                }
                            } else {
                                if (!isset($ctotal[83])) {
                                    $ctotal[83] = $item->qty * $item->amount;
                                } else {
                                    $ctotal[83] += $item->qty * $item->amount;
                                }
                            }

                            $supp_total += $item->qty * $item->amount;
                        }
                    }
                }

                $stotal += $supp_total;

                //SUMMARY OF COST COMPUTATION
                if ($supplier->currency == 83) {
                    if ($supplier->paytype && ($supplier->paytype != 1 || $supplier->paytype < 1)) {
                        if ($supplier->exvat == 1) {
                            $netvat = $supp_total;
                            $vat = round($supp_total * 0.12, 2);
                            $gross = $supp_total + $vat;
                        } else {
                            $vat = round($supp_total * 12 / 112, 2);
                            $netvat = $supp_total - $vat;
                            $gross = $supp_total;
                        }

                        if ($supplier->type < 0) {
                            $ewt = 0;
                        } else {
                            $ewtrate = 0.01;
                            if ($supplier->type == 4002) {
                                $ewtrate = 0.02;
                            }

                            $ewt = round($netvat * $ewtrate, 2);
                        }
                        $total = $gross - $ewt + $supplier->shipping;
                    } else {
                        $netvat = $supp_total;
                        $vat = 0.12;
                        $gross = 0;
                        $ewt = 0;
                        $total = $netvat + $supplier->shipping;
                    }
                    //$gtotal += $total;
                    $grosstotal += $gross;
                } else {
                    $netvat = $supp_total;
                    $vat = 0;
                    $gross = 0;
                    $ewt = 0;
                    $total = $netvat;
                    $total_php = $supplier->shipping;
                }

                //COMPUTE BASED OF EVERY CURRENCY

                $icon = (count($filtered_currencies) > 1) ? '<span class="pull-left">' . $currency->symbol . '</span>' : '';

                $supplist = array(
                    'supplier' => $supplier->name,
                    'netvat' => $icon . '<span id="supplier_netvat" data-id="' . $supplier->sysid . '">' . number_format($netvat, 2) . '</span>',
                    'vat' => '<span id="supplier_vat" data-id="' . $supplier->sysid . '">' . number_format($vat, 2) . '</span>',
                    'gross' => '<span id="supplier_gross" data-id="' . $supplier->sysid . '">' . number_format($gross, 2) . '</span>',
                    'ewt' => '<span id="supplier_ewt" data-id="' . $supplier->sysid . '">' . number_format($ewt, 2) . '</span>',
                    'shipping' => (!$approval && !($prf_qry && in_array($prf_qry->status, array(0, 302, 303)))) ? '<input type="number" id="supplier_ship" value="' . $supplier->shipping . '" name="shipping[' . $supplier->sysid . ']" step=".01" class="form-control" data-id="' . $supplier->sysid . '" >' : '<span id="supplier_ship" data-id="' . $supplier->sysid . '">' . number_format($supplier->shipping, 2) . '</span>',
                    //'total' => '<span id="supplier_soc" data-id="'.$supplier->sysid.'">'.number_format($total,2).'</span>'
                );

                $total_cols = array();

                if (count($filtered_currencies) > 1) {
                    $currencies = $filtered_currencies;
                    if ($key = array_search(83, $currencies)) {
                        $total_cols[83] = 'total';
                        unset($currencies[$key]);
                        $total_cols['c'] = 'php_total';
                    }

                    foreach ($currencies as $total_c) {
                        $cr = get_currency($total_c);
                        $cr_c = strtolower($cr->code);
                        $total_cols[$total_c] = $cr_c . '_total';
                    }

                    if (count($total_cols) > 0) {
                        //$data['total_cols'] = $total_cols;
                        foreach ($total_cols as $i => $col) {
                            //$data['total_cols'][$supplier->sysid][$col] = $total;
                            $cur = '';
                            if ($i != 83) {
                                [$cur, $p] = explode('_', $col);
                                $cur .= '_';
                            }

                            $gtotal[$col][] = $total;
                            $supplist[$col] = '<span id="' . $cur . 'supplier_soc" data-id="' . $supplier->sysid . '">0.00</span>';

                            if ($i == $supplier->currency) {
                                $supplist[$col] = '<span id="' . $cur . 'supplier_soc" data-id="' . $supplier->sysid . '">' . number_format($total, 2) . '</span>';
                            }

                            if ($i == 'c') {
                                $total_c = $total * ceil($currency->conversion);
                                $gtotal['php_total'][] = $total_c;
                                $supplist['php_total'] = '<span id="php_supplier_soc" data-id="' . $supplier->sysid . '">' . number_format($total_c, 2) . '</span>';
                            }

                            if ($supplier->currency == 83) {
                                $supplist['php_total'] = '<span id="php_supplier_soc" data-id="' . $supplier->sysid . '">0.00</span>';
                                $supplist['total'] = '<span id="supplier_soc" data-id="' . $supplier->sysid . '">' . number_format($total, 2) . '</span>';
                            } else {
                                $gtotal['total'][] = $total_php;
                                $total_c = $total * ceil($currency->conversion);
                                $supplist['php_total'] = '<span id="php_supplier_soc" data-id="' . $supplier->sysid . '">' . number_format($total_c, 2) . '</span>';
                                $total_php = $total_c + $supplier->shipping;
                                $supplist['total'] = '<span id="supplier_soc" data-id="' . $supplier->sysid . '">' . number_format($total_php, 2) . '</span>';
                            }
                        }
                    }
                } else {
                    $currencies = $filtered_currencies[0];
                    if ($currencies != 83) {
                        $cr = get_currency($currencies);
                        $cr_c = strtolower($cr->code);
                        $col = $cr_c . '_total';
                        $supplist[$col] = '<span id="' . $cr_c . '_supplier_soc" data-id="' . $supplier->sysid . '">' . number_format($total, 2) . '</span>';
                        $total_c = $total * ceil($currency->conversion);
                        $total_php = $total_c + $supplier->shipping;
                        $supplist['php_total'] = '<span id="php_supplier_soc" data-id="' . $supplier->sysid . '">' . number_format($total_php, 2) . '</span>';
                    } else {
                        $supplist['total'] = '<span id="supplier_soc" data-id="' . $supplier->sysid . '">' . number_format($total, 2) . '</span>';
                    }
                }

                $data['supplist'][] = $supplist;
            }
        }

        $columns = array(
            dt_column_array('supplier', 'Supplier', false, false),
            dt_column_array('netvat', 'Net of VAT', 'number', false),
            dt_column_array('vat', '12% VAT', 'number', false),
            dt_column_array('gross', 'Gross', 'number', false),
            dt_column_array('ewt', 'EWT', 'number', false),
            dt_column_array('shipping', 'Shipping (Estimate)', 'number', false),
            //dt_column_array('total','Total','number',false)
        );

        $s_currency = array_unique($s_currency);
        $totals = array();
        if (count($s_currency) > 0) {
            if (count($s_currency) > 1) {
                foreach ($s_currency as $supplier_c) {
                    if ($supplier_c != 83) {
                        $crc = get_currency($supplier_c);
                        $crc_code = strtolower($crc->code);
                        $columns[] = dt_column_array($crc_code . '_total', 'Total (' . $crc->code . ')', 'number', false);
                    }
                }
                $columns[] = dt_column_array('php_total', 'Total (Conv)', 'number', false);
                if (in_array(83, $s_currency)) {
                    $columns[] = dt_column_array('total', 'Total (PHP)', 'number', false);
                }
            } else {
                $supplier_c = $s_currency[0];
                if ($supplier_c != 83) {
                    $crc = get_currency($supplier_c);
                    $crc_code = strtolower($crc->code);
                    $columns[] = dt_column_array($crc_code . '_total', 'Total (' . $crc->code . ')', 'number', false);
                    $columns[] = dt_column_array('php_total', 'Total (Conv)', 'number', false);
                } else {
                    $columns[] = dt_column_array('total', 'Total', 'number', false);
                }
            }
        }


        $data['columns'] = $columns;
        //$data['subtotal'] = number_format($stotal,2);
        if (count($ctotal) > 0) {
            foreach ($ctotal as $c => $sub) {
                if ($c != 83) {
                    $cid = ($c == 'c') ? 83 : $c;
                    $cr = get_currency($cid);
                    $shortcr = strtolower($cr->code);
                    $data['subtotals'][$shortcr . '_subtotal_amt'] = number_format($sub, 2);
                } else {
                    $data['subtotals']['subtotal_amt'] = number_format($sub, 2);
                }
            }
        }
        //$data['buffer'] = number_format($gtotal*0.02,2);
        //$data['grandtotal'] = number_format($gtotal+($gtotal*0.02),2);
        $data['gtotal'] = $gtotal;


        return json_encode($data);
    }

    function compute_summary_of_cost()
    {
        $data = array();
        $quote = $this->input->post('amount');
        $shipping = $this->input->post('shipping');
        $id = $this->input->post('id');
        $cost = array();
        $netvat = array();
        $vat = array();
        $gross = array();
        $ewt = array();
        $suptotal = array();
        $ctotal = array();
        $active_c = array();
        $stotal = array();

        if (count($quote) > 0) {
            foreach ($quote as $itemid => $qt) {
                $amout_qry = $this->db->select('eti.qty,eqd.quotationid,eqd.amount,esm.currency')
                    ->from('eprs_quotation_details AS eqd')
                    ->join('eprs_transaction_items AS eti', 'eqd.prfitemid = eti.sysid', 'left')
                    ->join('eprs_quotation_suppliers AS eqs', 'eqd.quotationid = eqs.sysid', 'left')
                    ->join('eprs_suppliers_main as esm', 'eqs.supplierid = esm.sysid', 'left')
                    ->where(array('eqd.sysid' => $qt, 'eti.sysid' => $itemid))
                    ->get()->row();

                //$data['query'] = $this->db->last_query();

                if ($amout_qry) {
                    if ($amout_qry->currency != 83) {
                        $currency = get_currency($amout_qry->currency);
                        $convert = $currency->conversion;
                        $amount = ceil($convert) * $amout_qry->amount;
                        $ctotal[$amout_qry->currency][] = $amout_qry->amount * $amout_qry->qty;
                        $ctotal['c'][] = $amount * $amout_qry->qty;
                        $stotal[$amout_qry->quotationid]['a'] = $amout_qry->amount * $amout_qry->qty;
                        $stotal[$amout_qry->quotationid]['c'] = $amount * $amout_qry->qty;
                    } else {
                        $amount = $amout_qry->amount;
                        $ctotal[83][] = $amount * $amout_qry->qty;
                    }

                    $amount = $amout_qry->amount;

                    $total = $amout_qry->amount * $amout_qry->qty;
                    if (isset($cost[$amout_qry->quotationid])) {
                        $cost[$amout_qry->quotationid] += $total;
                    } else {
                        $cost[$amout_qry->quotationid] = $total;
                    }
                }
            }
        }

        //LOOKUP IF VAT-EX OR VAT-IN
        foreach ($cost as $quotationid => $totalamt) {
            $supplier = $this->db->select('eqs.exvat,eqs.exrate,s.currency,s.type')
                ->from('eprs_quotation_suppliers AS eqs')
                ->join('eprs_suppliers_main AS s', 'eqs.supplierid = s.sysid', 'left')
                ->where('eqs.sysid', $quotationid)
                ->get()->row();

            if ($supplier->currency == 83) {
                if ($supplier->exvat == 1) {
                    $netvat[$quotationid] = $totalamt;
                    $vat[$quotationid] = round($totalamt * 0.12, 2);
                    $gross[$quotationid] = $totalamt + $vat[$quotationid];
                } else {
                    $vat[$quotationid] = round($totalamt * 12 / 112, 2);
                    $netvat[$quotationid] = $totalamt - $vat[$quotationid];
                    $gross[$quotationid] = $totalamt;
                }
                $ewtrate = 0.01;

                if ($supplier->type < 0) {
                    $ewtrate = 0;
                } else{
                    if ($supplier->type == 4002) {
                        $ewtrate = 0.02;
                    }
                }

                $ewt[$quotationid] = ($supplier->exrate <= 1) ? round($netvat[$quotationid] * $ewtrate, 2) : 0;

                $ship = ($shipping && $shipping[$quotationid] != '') ? $shipping[$quotationid] : 0;

                $suptotal[$quotationid]['p'] = $gross[$quotationid] - $ewt[$quotationid] + $ship;
            } else {
                $c = get_currency($supplier->currency);
                $netvat[$quotationid] = $totalamt;

                $ewtrate = 0.01;

                if ($supplier->type < 0) {
                    $ewtrate = 0;
                } else{
                    if ($supplier->type == 4002) {
                        $ewtrate = 0.02;
                    }
                }

                $ewt[$quotationid] = ($supplier->exrate <= 1) ? round($netvat[$quotationid] * $ewtrate, 2) : 0;
                $vat[$quotationid] = 0;
                $gross[$quotationid] = 0;

                $ship = ($shipping && $shipping[$quotationid] != '') ? $shipping[$quotationid] : 0;
                $suptotal[$quotationid][strtolower($c->code)] = $totalamt;
                $suptotal[$quotationid]['php'] = $totalamt * ceil($supplier->exrate);
                $suptotal[$quotationid]['p'] = $ship;
            }
        }

        /*if (count($ctotal) > 0) {
            foreach ($ctotal AS $c => $sub) {
                if ($c != 83) {
                    $cid = ($c == 'c') ? 83 : $c;
                    $cr = get_currency($cid);
                    $shortcr = strtolower($cr->code);
                    $data['subtotals'][$shortcr.'_subtotal_amt'] = number_format($sub,2);
                } else {
                    $data['subtotals']['subtotal_amt'] = number_format($sub,2);
                }

            }
        }*/

        $supplier_qry = $this->db->select('s.currency')
            ->from('eprs_quotation_suppliers as eqs')
            ->join('eprs_suppliers_main as s', 's.sysid = eqs.supplierid', 'left')
            ->where(array('eqs.prfid' => $id))->get();

        if ($supplier_qry->num_rows() > 0) {
            foreach ($supplier_qry->result() as $sup) {
                $active_c[] = $sup->currency;
            }

            $ac = array_unique($active_c);
            if (count($ac) > 0) {
                if (count($ac) > 1) {
                    $ac[] = 'c';
                    foreach ($ac as $subTs) {
                        if ($subTs == 'c') {
                            $data_index = 'php_subtotal_amt';
                        } else {
                            if ($subTs != 83) {
                                $crc = get_currency($subTs);
                                $crc_code = strtolower($crc->code);
                                $data_index = $crc_code . '_subtotal_amt';
                            } else {
                                $data_index = 'subtotal_amt';
                            }
                        }

                        $data['subtotals'][$data_index] = isset($ctotal[$subTs]) ? number_format(array_sum($ctotal[$subTs]), 2) : '0.00';
                    }
                } else {
                    if (in_array(83, $ac)) {
                        $data['subtotals']['subtotal_amt'] = isset($ctotal[83]) ? number_format(array_sum($ctotal[83]), 2) : '0.00';
                    } else {
                        $crc = get_currency($ac[0]);
                        $crc_code = strtolower($crc->code);
                        $data['subtotals'][$crc_code . '_subtotal_amt'] = isset($ctotal[$ac[0]]) ? number_format(array_sum($ctotal[$ac[0]]), 2) : '0.00';
                        $data['subtotals']['php_subtotal_amt'] = isset($ctotal['c']) ? number_format(array_sum($ctotal['c']), 2) : '0.00';
                    }
                }
            }
        }

        $data['subtotal'] = number_format(array_sum($cost), 2);
        $data['buffer'] = number_format(array_sum($suptotal) * 0.02, 2);

        $data['soc'] = $cost;
        $data['netvat'] = $netvat;
        $data['vat'] = $vat;
        $data['gross'] = $gross;
        $data['ewt'] = $ewt;
        $data['suptotal'] = $suptotal;
        $data['gtotal'] = number_format(array_sum($suptotal) + (array_sum($suptotal) * 0.02), 2);
        ;

        return json_encode($data);
    }

    function dt_approver_remarks()
    {
        $data = array();
        $prsid = $this->input->post('id');
        $typesid = $this->input->post('typesid');

        $approvers = array(
            213 => 'Finance Manager',
            214 => 'General Manager',
            215 => 'PCEO'
        );

        $logs_qry = $this->db->select('moduleid,datecreated,statusid,remarks')
            ->from('eprs_transaction_logs')
            ->where(array(
                'prsid' => $prsid,
                'typesid' => $typesid,
                //'status' => 1
            ))->where_not_in('moduleid', array(193))->get();

        if ($logs_qry->num_rows() > 0) {
            foreach ($logs_qry->result() as $log) {
                $data['list'][] = array(
                    'approver' => $approvers[$log->moduleid],
                    'date' => date('Y-m-d H:i A', strtotime($log->datecreated)),
                    'status' => get_types_name($log->statusid)->names,
                    'remarks' => ($log->remarks != '') ? $log->remarks : 'N/A'
                );
            }
        }

        $data['columns'] = array(
            dt_column_array('approver', false, '', '20%'),
            dt_column_array('date', false, '', '15%'),
            dt_column_array('status', false, '', '10%'),
            dt_column_array('remarks', false, '', '50%'),
        );
        return json_encode($data);
    }

    function revise_item_qty()
    {
        $data = array();
        $itemid = $this->input->post('itemid');
        $itemqty = $this->input->post('itemqty');
        $itemunit = $this->input->post('itemunit');

        $msg = '';
        $qry = false;
        $title = '';
        $func = '';

        $item_qry = $this->db->select()
            ->from('eprs_transaction_items')
            ->where(array(
                'sysid' => $itemid,
                'status !=' => 0
            ))->get()->row();

        //$data['query'] = $this->db->last_query();
        $old_val = array();
        $new_val = array();

        if ($item_qry && ($item_qry->qty != $itemqty || $item_qry->unitid != $itemunit)) {
            $set = array();
            if ($item_qry->qty != $itemqty) {
                $set['qty'] = $itemqty;
                $old_val['qty'] = $item_qry->qty;
                $new_val['qty'] = $itemqty;
            }

            if ($item_qry->unitid != $itemunit) {
                $set['unitid'] = $itemunit;
                $old_val['unit'] = unit_query($item_qry->unitid)->name;
                $new_val['unit'] = unit_query($itemunit)->name;
            }
            $update = update_db($this->db, 'eprs_transaction_items', $set, array('sysid' => $itemid));

            if ($update->qry) {
                $audit_data = array(
                    'dataid' => $item_qry->prfid,
                    'valueold' => http_build_query($old_val),
                    'valuenew' => http_build_query($new_val),
                    'moduleid' => 193,
                    'remarks' => 'Revised EPRS item quantity/unit.',
                    'createdby' => user_id()
                );
                audit_insert($audit_data);

                $msg = 'Item quantity updated!';
                $qry = true;
                $title = 'Updated!';
                $func = 'success';
                $data['qty'] = $itemqty;
                $data['unit'] = $itemunit;
                $data['unitname'] = unit_query($itemunit)->name;
            } else {
                $msg = 'Unable to update item quantity!';
                $qry = false;
                $title = 'Fail!';
                $func = 'error';
            }
        } else {
            $msg = 'New quantity is the same as the old one.';
            $qry = false;
            $title = 'No change!';
            $func = 'warning';
        }

        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['title'] = $title;
        $data['func'] = $func;

        return json_encode($data);
    }

    function delete_supplier_quotation()
    {
        $data = array();
        $quotationid = $this->input->post('id');
        $qry = false;
        $msg = '';
        $func = '';
        $title = '';

        //get supplier details
        $supplier = $this->db->select('sm.descs')
            ->from('eprs_suppliers_main as sm')
            ->join('eprs_quotation_suppliers as qs', 'sm.sysid = qs.supplierid', 'left')
            ->where(array('qs.sysid' => $quotationid))
            ->where_not_in('qs.status', array(0, 303))
            ->get()->row();

        if ($supplier) {
            $this->db->trans_begin();
            //Remove quotation supplier status 303
            $remove_supplier = update_db($this->db, 'eprs_quotation_suppliers', array('status' => 303), array('sysid' => $quotationid));

            if ($remove_supplier->qry) {
                $remove_quotations = update_db($this->db, 'eprs_quotation_details', array('status' => 303), array('quotationid' => $quotationid));
                if ($remove_quotations->qry) {
                    $this->db->trans_commit();
                    $qry = true;
                    $msg = $supplier->descs . ' quotations has been successfully removed.';
                    $func = 'success';
                    $title = $supplier->descs . ' Removed!';
                } else {
                    $this->db->trans_rollback();
                    $qry = false;
                    $msg = 'Failed to remove ' . $supplier->descs . ' quotations.';
                    $func = 'error';
                    $title = $supplier->descs . ' not Removed!';
                }
            } else {
                $this->db->trans_rollback();
                $qry = false;
                $msg = 'Unable to remove ' . $supplier->descs . ' from quotations.';
                $func = 'error';
                $title = $supplier->descs . ' not Removed!';
            }
        } else {
            $qry = false;
            $msg = 'Supplier not found!';
            $func = 'error';
            $title = 'Not found!';
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }

    function dt_po_suppliers()
    {
        $data = array();
        $prfid = $this->input->post('id');
        $view = $this->input->post('view');

        //query suppliers sum total cost of each supplier
        //QUERY APPROVED SUPPLIERS WITH COUNT OF ITEMS
        $currency_a = array();
        $list = array();
        $suppliers_qry = $this->db->select('
                qs.sysid,
                qs.supplierid,
                sm.descs AS `name`,
                sm.currency,
                COUNT( qd.sysid ) AS items,
                qs.shipping,
                qs.paytype,
                qs.exrate,
                qs.exvat
            ')
            ->from('eprs_quotation_suppliers AS qs')
            ->join('eprs_suppliers_main AS sm', 'qs.supplierid = sm.sysid', 'left')
            ->join('eprs_quotation_details AS qd', 'qs.sysid = qd.quotationid', 'left')
            ->where(array(
                'qd.status' => 301,
                'qs.status' => 301,
                'qs.prfid' => $prfid
            ))->group_by('qd.quotationid')
            ->get();

        if ($suppliers_qry->num_rows() > 0) {
            $num = 1;
            foreach ($suppliers_qry->result() as $supplier) {
                //GET APPROVED QUOTATIONS WITH QUOTATIONID AND ITEM COUNT
                $currency = get_currency($supplier->currency);
                $currency_a[] = $currency;
                $icon = ($supplier->currency != 83) ? $currency->symbol : '';
                $amount = 0;
                $approved_qt = $this->db->select('eti.qty,eqd.amount,eqd.status')
                    ->from('eprs_quotation_details AS eqd')
                    ->join('eprs_transaction_items AS eti', 'eqd.prfitemid = eti.sysid', 'left')
                    ->where(array('eqd.quotationid' => $supplier->sysid, 'eqd.status ' => 301))
                    ->get();

                if ($approved_qt->num_rows() > 0) {
                    foreach ($approved_qt->result() as $qt) {
                        $amount += $qt->amount * $qt->qty;
                    }
                }

                if ($supplier->paytype != 1 && !$supplier->exrate) {
                    $gross = ($supplier->exvat == 1) ? round($amount + ($amount * 0.12), 2) : $amount;
                    $ewt = ($supplier->exvat == 1) ? round($amount * 0.01, 2) : round(($amount - round($amount * 12 / 112, 2)) * 0.01, 2);
                    $buffer = round($gross * 0.02, 2);
                    $total = $gross - $ewt + $buffer + $supplier->shipping;
                } else {
                    $gross = $amount;
                    $ewt = 0;
                    $buffer = 0;
                    $total = $gross;
                    $c_info = get_currency(83);
                    $p_icon = ($supplier->shipping > 0) ? $c_info->symbol : '';
                }

                $control = '';
                $control .= ($view == 'false') ? '<a href="#frm_create_payment_request" title="Payment Request Details" data-toggle="ajax-modal" data-arr="' . $supplier->sysid . '" class="btn btn-success btn-sm inline"><i class="fa fa-list-ul"></i> Details</a>' : '';
                $control .= '<a href="javasrcipt:;" title="PO Preview" id="btn_po_preview" data-id="' . $supplier->sysid . '" class="btn btn-primary btn-sm inline"><i class="fa fa-search"></i> Preview</a>';
                $list_a = array(
                    'num' => $num++,
                    'name' => $supplier->name,
                    'items' => $supplier->items,
                    'gross' => '<span class="pull-left">' . $icon . '</span>' . number_format($gross, 2),
                    'buffer' => ($buffer > 0 ? '<span class="pull-left">' . $icon . '</span>' : '') . number_format($buffer, 2),
                    'ewt' => ($ewt > 0 ? '<span class="pull-left">' . $icon . '</span>' : '') . number_format($ewt, 2),
                    'shipping' => (($supplier->currency != 83) ? '<span class="pull-left">' . $p_icon . '</span>' : '') . number_format($supplier->shipping, 2),
                    'total' => ($supplier->currency != 83) ? number_format($supplier->shipping, 2) : number_format($total, 2),
                    'control' => $control
                );
                if ($supplier->currency != 83) {
                    $code = strtolower($currency->code);
                    $list_a[$code . '_total'] = number_format($total, 2);
                }
                $list[] = $list_a;
            }
        }

        $columns = array(
            dt_column_array('num', '#', 'number', '1%'),
            dt_column_array('name', 'Name', '', '15%'),
            dt_column_array('items', 'Items', 'number', '5%'),
            dt_column_array('gross', 'Gross', 'number', '10%'),
            dt_column_array('buffer', 'Buffer 2%', 'number', '8%'),
            dt_column_array('ewt', 'EWT', 'number', '8%'),
            dt_column_array('shipping', 'Shipping (Est.)', 'number', '8%'),
            //dt_column_array('total','Total','number','10%'),
            //dt_column_array('control','Control','text-align-center','10%'),
        );

        $a_currency = array_column($currency_a, 'sysid');
        $u_currency = array_unique($a_currency);
        if (count($u_currency) > 1) {
            if (in_array(83, $u_currency)) {
                //$key = array_search(83, array_map("unserialize", array_unique(array_map("serialize", $currency_a))));
                foreach ($currency_a as $k => $v) {
                    if (in_array(83, (array) $v)) {
                        unset($currency_a[$k]);
                    }
                }
            }
            foreach ($currency_a as $c) {
                $code = strtolower($c->code);
                $columns[] = dt_column_array($code . '_total', 'Total (' . $c->code . ')', 'number', '10%');
            }
            $columns[] = dt_column_array('total', 'Total (PHP)', 'number', '10%');
        } else {
            if ($u_currency[0] != 83) {
                $c = $currency_a[0];
                $code = strtolower($c->code);
                $columns[] = dt_column_array($code . '_total', 'Total (' . $c->code . ')', 'number', '10%');
                $columns[] = dt_column_array('total', 'Total (PHP)', 'number', '10%');
            } else {
                $columns[] = dt_column_array('total', 'Total', 'number', '10%');
            }
        }

        $columns[] = dt_column_array('control', 'Control', 'text-align-center', '10%');

        $data['columns'] = $columns;

        if (count($list) > 0) {
            foreach ($list as $n => $v) {
                $cols = array_column($columns, 'data');
                foreach ($cols as $col) {
                    if (!array_key_exists($col, $list[$n])) {
                        $list[$n][$col] = '0.00';
                    }
                }
            }

            $data['list'] = $list;
        }

        return json_encode($data);
    }

    function save_payment_request()
    {
        $data = array();
        $quotedsupplier = $this->input->post('quotedsupplier');
        $paymenttype = $this->input->post('paymenttype');
        $paymentterm = $this->input->post('paymentterm');
        $purpose = $this->input->post('purpose');
        $accountname = $this->input->post('accountname');
        $accountbank = $this->input->post('accountbank');
        $accountnumber = $this->input->post('accountnumber');
        $suppliertin = $this->input->post('suppliertin');
        $ponotes = $this->input->post('ponotes');

        $msg = '';
        $qry = false;
        $func = '';
        $title = '';

        $trn = array();

        $this->db->trans_begin();
        //ADD SUPPLIER ONLINE-BANKING DETAILS AND TIN
        $supplier = $this->db->select('supplierid as id')
            ->from('eprs_quotation_suppliers')
            ->where(array('sysid' => $quotedsupplier, 'status' => 301))
            ->get()->row();

        if ($accountname && $accountname != '') {
            if ($supplier) {
                //ADD ONLINE-BANKING
                $online_array = array(
                    'supplierid' => $supplier->id,
                    'name' => $accountname,
                    'bank' => $accountbank,
                    'accountnum' => $accountnumber
                );

                $add_online_details = insert_db($this->db, 'eprs_suppliers_online_details', $online_array);
                if ($add_online_details->qry) {
                    $trn['add_online_details'] = true;
                    $msg .= 'Online Details has been updated."\n"';
                } else {
                    $trn['add_online_details'] = false;
                    $msg .= 'Unable to update online details."\n"';
                }
            }
        }

        $tin_qry = $this->db->select('tin')
            ->from('eprs_suppliers_main')
            ->where(array('sysid' => $supplier->id))
            ->get()->row();
        //ADD TIN
        if ($tin_qry && $tin_qry->tin != $suppliertin) {
            $add_supplier_tin = update_db($this->db, 'eprs_suppliers_main', array('tin' => $suppliertin), array('sysid' => $supplier->id));
            if ($add_supplier_tin->qry) {
                $trn['add_supplier_tin'] = true;
                $msg .= 'Supplier\'s TIN has been updated."\n"';
            } else {
                $trn['add_supplier_tin'] = false;
            }
        }

        //INSERT PO DETAILS
        $po_details = array(
            'quotationid' => $quotedsupplier,
            'paytype' => $paymenttype,
            'payterm' => $paymentterm,
            'purpose' => $purpose,
            'notes' => $ponotes
        );

        $po_details_lookup = $this->db->select()
            ->from('eprs_po_details')
            ->where($po_details)
            ->where('status', 1)
            ->get()->row();

        $existing = array();
        if ($po_details_lookup) {
            foreach ($po_details as $key => $detail) {
                $existing[$key] = ($po_details_lookup->$key == $po_details[$key]);
            }
            if ($po_details_lookup->poid) {
                $po_details['poid'] = $po_details_lookup->poid;
            }
        }

        if (count($existing) > 0 && !in_array(false, $existing)) {
            $msg .= 'Submitted details already exists. Make some changes and try again.';
        } else {
            update_db($this->db, 'eprs_po_details', array('status' => 0), array('quotationid' => $quotedsupplier));

            $add_po_dets = insert_db($this->db, 'eprs_po_details', $po_details);

            if ($add_po_dets->qry) {
                $trn['add_po_dets'] = true;
            } else {
                $trn['add_po_dets'] = false;
            }
        }

        if (!in_array(false, $trn)) {
            $this->db->trans_commit();
            $msg = 'PO Details has been updated!';
            $qry = true;
            $func = 'success';
            $title = 'Success!';
        } else {
            $data['trn'] = $trn;
            if (in_array(true, $trn)) {
                $this->db->trans_commit();
                $func = 'warning';
                $title = 'Updated with Errors';
            } else {
                $this->db->trans_rollback();
                $msg .= 'Failed to update PO Details.';
                $func = 'error';
                $title = 'Fail!';
            }
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }

    function generate_po()
    {
        $data = array();
        $requestid = $this->input->post('id');
        $forpogen = array();
        $nodets = array();
        $msg = '';
        $qry = false;
        $func = '';

        $suppliers_qry = $this->db->select('
                qs.sysid,
                qs.supplierid,
                sm.descs AS `name`
            ')
            ->from('eprs_quotation_suppliers AS qs')
            ->join('eprs_suppliers_main AS sm', 'qs.supplierid = sm.sysid', 'left')
            ->where(array(
                'qs.status' => 301,
                'qs.prfid' => $requestid
            ))->get();

        if ($suppliers_qry->num_rows() > 0) {
            foreach ($suppliers_qry->result() as $supplier) {
                //Check if PO details are saved for each supplier.
                $details_qry = $this->db->select()
                    ->from('eprs_po_details')
                    ->where(array('quotationid' => $supplier->sysid, 'status' => 1))
                    ->get()->row();

                if ($details_qry) {
                    $forpogen[] = $details_qry->sysid;
                } else {
                    $nodets[] = $supplier->name;
                }
            }
        }

        $nodets_cnt = count($nodets);
        if ($nodets_cnt > 0) {
            $suppliers = '';
            $last = end($nodets);
            if ($nodets_cnt > 1) {
                unset($nodets[$nodets_cnt - 1]);
                $suppliers .= implode(', ', $nodets) . ' and ' . $last;
            } else {
                $suppliers .= $last;
            }
            $msg = $suppliers . ' has no saved PO details. Kindly provide details for the following supplier(s) and try again.';
            $func = 'error';
        } else {
            //CREATE PO NUMBER AND UPDATE PO DETAILS
            $ponum = date('mdY');
            $new_po = insert_db($this->db, 'eprs_po', array('prfid' => $requestid, 'ponumber' => $ponum));
            if ($new_po->qry) {
                $poid = $new_po->insert_id;
                $update_details = array();
                foreach ($forpogen as $details) {
                    $update_details[] = update_db($this->db, 'eprs_po_details', array('poid' => $poid), array('sysid' => $details))->qry;
                }

                if (!in_array(false, $update_details)) {
                    $qry = true;
                    $msg = 'POs has been created for all suppliers!';
                    $func = 'success';
                }
                $data['ponumber'] = $ponum;
            }
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;

        return json_encode($data);
    }

    function my_prs_list()
    {
        $data = array();

        $route = $this->input->post('route');

        $app_flow_ids_arr = flow_id_arr('EPRS');
        $app_flow_ids = ($app_flow_ids_arr) ? implode(',', $app_flow_ids_arr) : false;
        $where_trails_last = ($app_flow_ids_arr) ? " AND rm.flowid IN ($app_flow_ids) " : "";
        $where_stages = ($app_flow_ids_arr) ? " AND flowid IN ($app_flow_ids) " : "";
        $data['traillast'] = $where_trails_last;

        if ($route && ((is_array($route) && count($route) > 0) || $route > 0)) {

            $levels = '';
            if (is_array($route)) {
                $levels = 'levels IN (' . implode(',', $route) . ')';
            } else {
                $levels = ($route > 0) ? 'levels = ' . $route : 'levels = ""';
            }

            $sql_stages = $this->db->query("
                SELECT sysid
                FROM prime_transaction_flow_main_stages
                WHERE $levels AND `status` = 1 $where_stages
                ");

            if ($sql_stages->num_rows() > 0) {
                foreach ($sql_stages->result() as $srow) {
                    $stages_ids[] = $srow->sysid;
                }
            }
            $stageids = implode(',', $stages_ids);
            $where = ' AND rmt.stageid IN (' . $stageids . ')';
        } else {
            $sql_stages = $this->db->query("
                SELECT sysid
                FROM prime_transaction_flow_main_stages
                WHERE `status` = 1 $where_stages
                ");

            if ($sql_stages->num_rows() > 0) {
                foreach ($sql_stages->result() as $srow) {
                    $stages_ids[] = $srow->sysid;
                }
            }
            $stageids = implode(',', $stages_ids);
            $where = ' AND rmt.stageid IN (' . $stageids . ')';
        }

        $roles = json_decode(get_user_role(user_id()));
        if (user_id() != 1 || ($roles && !array_search(24, array_column((array) $roles, 'id')))) {
            $where .= ' AND et.createdby = ' . user_id();
        }

        $qry_details = $this->db->query("
            SELECT
                et.sysid,
                rmt.trnid,
                rmt.stageid,
                trm.datecreated AS submitted,
                rmt.datecreated AS updated,
                COUNT( eti.sysid ) AS items,
                et.justification,
                et.createdby,
                et.datecreated,
                et.`status`
            FROM
                eprs_transaction AS et
                LEFT JOIN eprs_transaction_items AS eti ON eti.prfid = et.sysid AND eti.`status` IN (300,305)
                INNER JOIN transaction_request_main_trails AS rmt ON rmt.dataid = et.sysid
                INNER JOIN transaction_request_main AS trm ON rmt.trnid = trm.sysid 
            WHERE
                rmt.`status` = 1 
                AND et.`status` > 0 
                $where 
            GROUP BY
                et.sysid,
                rmt.trnid,
                rmt.stageid,
                et.datecreated,
                et.createdby,
                et.typesid
        ");

        $data['sql'] = $this->db->last_query();

        if ($qry_details->num_rows() > 0) {
            foreach ($qry_details->result() as $row) {
                $prsid = $row->sysid;
                $trnid = $row->trnid;
                $stageid = $row->stageid;
                $datesubmitted = $row->submitted;
                $justification = $row->justification;
                $createdby = $row->createdby;
                $created = $row->datecreated;
                $items = $row->items;

                $creator = get_users_info($createdby);
                $requestor = '';

                if ($creator) {
                    $requestor = ucfirst($creator->firstname . ' ' . $creator->lastname);
                }

                $comment_cnt = '';
                $comment_msg = '';
                $qry_comments_cnt = $this->db->select('count(tc.trnid) AS cnt')
                    ->from('transaction_request_trails_comments AS tc')
                    ->where(array('tc.trnid' => $trnid, 'status' => 1))
                    ->get()->row();
                if ($qry_comments_cnt && $qry_comments_cnt->cnt > 0) {

                    $qry_comments_msg = $this->db->select('remarks')
                        ->from('transaction_request_trails_comments AS tc')
                        ->where(array('tc.trnid' => $trnid, 'status' => 1))
                        ->order_by('datecreated', 'desc')
                        ->get()->row();
                    $comment_msg = ($qry_comments_msg) ? $qry_comments_msg->remarks : '';
                    $max_length = 45;

                    if (strlen($comment_msg) > $max_length) {
                        $offset = ($max_length - 3) - strlen($comment_msg);
                        $comment_msg = substr($comment_msg, 0, strrpos($comment_msg, ' ', $offset)) . ' ...';
                    }
                    $comment_cnt = '<span class="badge badge-danger pull-right" style="margin-left: 5px;">' . $qry_comments_cnt->cnt . '</span>';
                }

                $creation_date = '';
                $qry_trails_last = $this->db->query("
                    SELECT rm.sysid AS trnid, rmt.sysid, rmt.datecreated, rmt.createdby, rmt.stageid, rmt.dataid, rmt.datecreated AS logdate
                    FROM transaction_request_main_trails AS rmt
                    INNER JOIN transaction_request_main AS rm ON rm.sysid = rmt.trnid
                    WHERE rmt.dataid = $prsid 
                    AND rmt.`status` = 1
                    $where_trails_last
                    ORDER BY rmt.datecreated DESC
                ")->row();

                $data['traillast_qry'] = $this->db->last_query();
                $show = true;
                if ($route && $route > 0) {
                    if ($qry_trails_last && $qry_trails_last->stageid != $stageid) {
                        $show = false;
                    }
                }

                $trn_name = 'Unknown';
                $updated_date = 'None';
                $button = '';
                $from_created_by = 'None';


                if ($qry_trails_last) {
                    $creation_date = $row->datecreated;
                    $updated_date = $qry_trails_last->datecreated;

                    $user_info = get_users_info($qry_trails_last->createdby);
                    $from_created_by = ($user_info) ? $user_info->lastname . ', ' . $user_info->firstname : '';


                    $trn_name = '<a href="javascript:;" title="Current" class="label label-info">C</a> ' . get_trail_name($qry_trails_last->stageid);
                    $button .= '<div class="btn-group btn-xs">';
                    $button .= '<a target="_blank" title="View PRF." data-content="body" href="' . base_url('module/bc33ea4e26e5e1af1408321416956113a4658763/view/' . $prsid) . '" class="btn btn-primary btn-xs inline tooltips"><i class="fa fa-search fa-fw"></i></a>';
                    $button .= '</div>';
                }

                $trn_elapse = time_elapsed_diff($creation_date, $updated_date, true);
                $ovr_elapse = time_elapsed_diff($creation_date, date('Y-m-d h:m:s'));

                $time = $datesubmitted . '<br><small class="text-info">' . timeago($row->datecreated, sql_time()->DATETIME) . '</small>';
                $time_updated = $updated_date . '<br><small class="text-info">' . timeago($updated_date, sql_time()->DATETIME) . '</small>';

                if ($row->status == 1) {
                    $status = 'Pending';
                } else {
                    $status = get_types_label_format($row->status);
                }

                if ($show) {
                    $prfno = 'PRF' . date('ym', strtotime($created)) . str_pad($prsid, 5, '0', STR_PAD_LEFT);
                    $po = $this->db->select('ponumber as number')
                        ->from('eprs_po')
                        ->where(array('prfid' => $prsid, 'status' => 1))
                        ->get()->row();

                    if ($po) {
                        $ponum = 'PAE-' . str_pad($po->number, 8, '0', STR_PAD_LEFT);
                        $hide = 'hidden';
                    } else {
                        $ponum = 'N/A';
                        $hide = '';
                    }

                    $data['list'][] = array(
                        'expand' => btn_expand($prsid),
                        'prfno' => '<h4 class="text-danger bold" style="margin: 0px 0px;">' . $prfno . ' </h4> ',
                        'pono' => $ponum,
                        'submitted' => $time,
                        'from' => $from_created_by,
                        'updated' => $time_updated,
                        'items' => $items,
                        'justification' => $justification,
                        'requestor' => $requestor,
                        'dataid' => '',
                        'origid' => '',
                        'control' => $button,
                        'trn' => $trn_name,
                        'status' => $status,
                        'remarks' => $comment_msg . $comment_cnt
                    );
                }
            }
        }

        $data['columns'] = array(
            dt_column_array('expand', false, 'text-align-center', '1%'),
            dt_column_array('prfno', false, 'text-primary bold', '10%'),
            dt_column_array('pono', false, 'text-primary bold', '10%'),
            dt_column_array('submitted', false, false, '10%'),
            dt_column_array('updated', false, false, '10%'),
            dt_column_array('items', false, 'number'),
            dt_column_array('justification', false, false, '300px'),
            dt_column_array('trn', false, 'text-danger', '150px'),
            dt_column_array('remarks', false, 'text-info', '150px'),
            dt_column_array('status', false, 'text-info'),
            dt_column_array('control', false, 'controls', '5%'),
        );

        return json_encode($data);
    }

    function my_prs_draft()
    {
        $data = array();

        $drafts_lookup = $this->db->select('et.*,COUNT(eti.sysid) AS items')
            ->from('eprs_transaction as et')
            ->join('eprs_transaction_items as eti', 'et.sysid = eti.prfid AND eti.status = 307', 'left')
            ->where(array('et.createdby' => user_id(), 'et.status' => 307))
            ->group_by('et.sysid')
            ->get();

        if ($drafts_lookup->num_rows() > 0) {
            $num = 1;
            foreach ($drafts_lookup->result() as $draft) {
                $prfno = 'PRF' . date('ym', strtotime($draft->datecreated)) . str_pad($draft->sysid, 5, '0', STR_PAD_LEFT);
                $time = $draft->datecreated . '<br><small class="text-info">' . timeago($draft->datecreated, sql_time()->DATETIME) . '</small>';
                $time_updated = $draft->dateupdated . '<br><small class="text-info">' . timeago($draft->dateupdated, sql_time()->DATETIME) . '</small>';

                $button = '';
                $button .= '<div class="btn-group btn-xs">';
                $button .= '<a target="_blank" title="View PRF Draft." data-content="body" href="' . base_url('module/bc33ea4e26e5e1af1408321416956113a4658763/view/' . $draft->sysid) . '" class="btn btn-primary btn-xs inline tooltips"><i class="fa fa-search fa-fw"></i></a>';
                $button .= '</div>';

                $data['list'][] = array(
                    'num' => $num++,
                    'prfno' => '<h4 class="text-danger bold" style="margin: 0px 0px;">' . $prfno . ' </h4> ',
                    'created' => $time,
                    'updated' => $time_updated,
                    'items' => $draft->items,
                    'justification' => $draft->justification,
                    'control' => $button,
                );
            }
        }

        $data['columns'] = array(
            dt_column_array('num', false, 'text-align-center', '1%'),
            dt_column_array('prfno', false, 'text-primary bold', '10%'),
            dt_column_array('created', false, false, '10%'),
            dt_column_array('updated', false, false, '10%'),
            dt_column_array('items', false, 'number'),
            dt_column_array('justification', false, false, '300px'),
            dt_column_array('control', false, 'controls text-align-center', '5%'),
        );

        return json_encode($data);
    }

    function edit_justification()
    {
        $data = array();
        $prfid = $this->input->post('prfid');
        $justification = $this->input->post('justification');

        $qry = false;
        $msg = '';
        $func = '';

        $this->db->trans_begin();
        $update = update_db($this->db, 'eprs_transaction', array('justification' => $justification), array('sysid' => $prfid));

        if ($update->qry) {
            $this->db->trans_commit();
            $qry = true;
            $msg = 'Justification has been updated!';
            $func = 'success';
        } else {
            $this->db->trans_rollback();
            $msg = 'Failed to update PRF justification!';
            $func = 'error';
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;

        return json_encode($data);
    }

    function new_supplier_validation()
    {
        $data = array();
        $value = $this->input->post('value');
        $field = $this->input->post('field');
        $type = $this->input->post('type');
        $location = $this->input->post('location');

        $msg = '';
        $qry = false;
        $func = '';
        $icon = '';
        if ($value && $value != '') {
            if ($location == 'main') {
                if ($field == 'codes') {
                    $value = strtoupper($value);
                }
                $lookup_qry = $this->db->select($field)
                    ->from('eprs_suppliers_main')
                    ->where($field, $value)
                    ->get()->row();

                if ($lookup_qry) {
                    $msg = 'Value provided for this field already exists. Please choose another value.';
                } else {
                    $qry = true;
                }
            }

            if ($location == 'contact') {

                $lookup_qry = $this->db->select($field)
                    ->from('eprs_suppliers_contact')
                    ->where(array('contact' => $value, 'typesid' => $type))
                    ->get()->row();

                if ($lookup_qry) {
                    $msg = 'Value provided for this field already exists. Please choose another value.';
                } else {
                    $qry = true;
                }
            }

            if ($qry) {
                $func = 'success';
                $icon = 'fa fa-check';
            } else {
                $func = 'danger';
                $icon = 'fa fa-times';
            }
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['icon'] = $icon;
        $data['field'] = $field;
        $data['value'] = $value;


        return json_encode($data);
    }

    function save_new_supplier()
    {
        $data = array();
        $suppliercode = strtoupper($this->input->post('suppliercode'));
        $suppliername = $this->input->post('suppliername');
        $supplierdesc = $this->input->post('supplierdesc');
        $suppliercurrency = $this->input->post('suppliercurrency');
        $supplieraddress = $this->input->post('supplieraddress');
        $suppliertin = $this->input->post('suppliertin');
        $accountname = $this->input->post('accountname');
        $accountbank = $this->input->post('accountbank');
        $accountnumber = $this->input->post('accountnumber');
        $supplierphone = $this->input->post('supplierphone');
        $suppliermobile = $this->input->post('suppliermobile');
        $supplieremail = $this->input->post('supplieremail');

        $msg = '';
        $qry = false;
        $func = '';
        $conflict = true;
        $result = array();

        //CHECK IF TIN EXIST
        if ($suppliertin && $suppliertin != '') {
            $this->db->or_where('tin', $suppliertin);
        }
        $check_qry = $this->db->select('')
            ->from('eprs_suppliers_main')
            ->or_where('codes', $suppliercode)
            ->or_where('name', $suppliername)
            ->or_where('descs', $supplierdesc)
            ->where('status', 1)
            ->get()->row();

        if ($check_qry) {
            $msg = 'One or more existing suppliers has the same data that you provided. Please check fields and provide proper corrections.';
            $func = 'error';
        } else {
            if ($supplierphone && $supplierphone != '') {
                $this->db->or_where('contact', $supplierphone);
            }
            if ($suppliermobile && $suppliermobile != '') {
                $this->db->or_where('contact', $suppliermobile);
            }
            if ($supplieremail && $supplieremail != '') {
                $this->db->or_where('contact', $supplieremail);
            }
            $contact_qry = $this->db->select('')
                ->from('eprs_suppliers_contact as sm')
                ->where('status', 1)
                ->get()->row();

            if ($contact_qry) {
                $msg = 'One or more contact information was already provided by another supplier. Please check fields and provide proper corrections.';
                $func = 'error';
            } else {
                if ($accountname != '' && $accountbank != '' && $accountnumber != '') {
                    $account_qry = $this->db->select('')
                        ->from('eprs_suppliers_online_details as sm')
                        ->or_where('name', $accountname)
                        ->or_where('accountnum', $accountnumber)
                        ->where('status', 1)
                        ->get()->row();

                    if ($account_qry) {
                        $msg = 'One or more Online Payment details was already provided by another supplier. Please check fields and provide proper corrections.';
                        $func = 'error';
                    } else {
                        $conflict = false;
                    }
                } else {
                    $conflict = false;
                }
            }
        }

        if (!$conflict) {
            //INSERT DATA
            $this->db->trans_begin();
            $insert_info = array(
                'codes' => $suppliercode,
                'name' => $suppliername,
                'descs' => $supplierdesc,
                'currency' => $suppliercurrency
            );

            if ($suppliertin && $suppliertin != '') {
                $insert_info['tin'] = $suppliertin;
            }

            $supplier = insert_db($this->db, 'eprs_suppliers_main', $insert_info);

            if ($supplier->qry) {
                $result['supplier'] = true;
                $supplier_id = $supplier->insert_id;

                //INSERT ADDRESS
                $address_arr = array(
                    'supplierid' => $supplier_id,
                    'address' => $supplieraddress
                );
                $address = insert_db($this->db, 'eprs_suppliers_address', $address_arr);
                $result['address'] = $address->qry;

                //INSERT CONTACT
                if ($supplierphone && $supplierphone != '') {
                    $phone_arr = array(
                        'supplierid' => $supplier_id,
                        'contact' => $supplierphone,
                        'typesid' => 1050
                    );

                    $phone = insert_db($this->db, 'eprs_suppliers_contact', $phone_arr);
                    $result['phone'] = $phone->qry;
                }

                if ($suppliermobile && $suppliermobile != '') {
                    $mobile_arr = array(
                        'supplierid' => $supplier_id,
                        'contact' => $suppliermobile,
                        'typesid' => 1051
                    );

                    $mobile = insert_db($this->db, 'eprs_suppliers_contact', $mobile_arr);
                    $result['mobile'] = $mobile->qry;
                }

                if ($supplieremail && $supplieremail != '') {
                    $email_arr = array(
                        'supplierid' => $supplier_id,
                        'contact' => $supplieremail,
                        'typesid' => 1053
                    );

                    $email = insert_db($this->db, 'eprs_suppliers_contact', $email_arr);
                    $result['email'] = $email->qry;
                }

                //INSERT ONLINE DETAILS
                if ($accountname != '' && $accountbank != '' && $accountnumber != '') {
                    $account_arr = array(
                        'supplierid' => $supplier_id,
                        'name' => $accountname,
                        'bank' => $accountbank,
                        'accountnum' => $accountnumber
                    );

                    $account = insert_db($this->db, 'eprs_suppliers_online_details', $account_arr);
                    $result['account'] = $account->qry;
                }
            } else {
                $result['supplier'] = false;
            }
            if (!in_array(false, $result)) {
                $this->db->trans_commit();
                $msg = 'Supplier details has been saved!';
                $qry = true;
                $func = 'success';
            } else {
                $this->db->trans_rollback();
                $msg = 'Supplier details was not successfully saved. Please check your fields and try again.';
                $qry = false;
                $func = 'error';
            }
        }

        $data['result'] = $result;
        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;

        return json_encode($data);
    }

    function prs_list()
    {
        $data = array();

        //LOOKUP ALL NON-DELETED PRS
        $prs_qry = $this->db->select('t.sysid,t.datecreated,t.justification,COUNT(i.sysid) AS items,t.status ')
            ->from('eprs_transaction AS t')
            ->join('eprs_transaction_items AS i', 't.sysid = i.prfid AND i.status != 0', 'left')
            ->where(array('t.status !=' => 0))
            ->group_by('t.sysid,t.justification,t.status')
            ->get();

        if ($prs_qry->num_rows() > 0) {
            $n = 1;
            foreach ($prs_qry->result() as $prs) {
                $control = '<button id="btn_load_items" class="btn btn-primary inline" data-id="' . $prs->sysid . '"><i class="fa fa-download"></i></button>';
                $data['list'][] = array(
                    'num' => btn_expand($prs->sysid) . ' ' . $n++,
                    'prs' => '<span class="text-danger bold">PRF' . date('ym', strtotime($prs->datecreated)) . str_pad($prs->sysid, 5, '0', STR_PAD_LEFT) . '</span>',
                    'items' => $prs->items,
                    'justification' => $prs->justification,
                    'status' => get_types_label_format($prs->status),
                    'control' => $control
                );
            }
        }

        $data['columns'] = array(
            dt_column_array('num', '#', 'number', '10px'),
            dt_column_array('prs', 'PRF#', 'text-primary bold', '25px'),
            dt_column_array('items', 'Items', 'number', '15px'),
            dt_column_array('justification', 'Justification', '', '300px'),
            dt_column_array('status', 'Status', 'text-align-center', '25px'),
            dt_column_array('control', '<i class="fa fa-wrench"></i>', 'text-align-center', '15%'),
        );

        return json_encode($data);
    }

    function prf_sub_details()
    {
        $data = array();
        $prfid = $this->input->post('id');

        $html = '';
        $row = '';

        //LOOKUP ALL PRF ITEMS
        $items_qry = $this->db->select('eti.itemid,eti.sysid,eti.prfid,imd.fulldescription,eti.qty,eti.remarks,u.unit_name,u.unit_code,eti.unitid')
            ->from('eprs_transaction_items AS eti')
            ->join('items_main_description AS imd', 'eti.itemid = imd.sysid', 'left')
            ->join('prime_unit AS u', 'eti.unitid = u.sysid', 'left')
            ->where('eti.status !=', 0)
            ->where('eti.prfid', $prfid)
            ->get();

        //$data['query'] = $this->db->last_query();

        if ($items_qry->num_rows() > 0) {
            $n = 1;
            foreach ($items_qry->result() as $item) {
                $unit = unit_query($item->unitid);
                $unitn = ($unit) ? (($unit->name == $unit->code) ? $unit->name : $unit->name . ' (' . $unit->code . ')') : 'unit';
                $row .= '<tr>';
                $row .= '<td class="number">' . $n++ . '</td>';
                $row .= '<td class="">' . $item->fulldescription . '</td>';
                $row .= '<td class="number">' . $item->qty . '</td>';
                $row .= '<td class="">' . $unitn . '</td>';
                $row .= '<td class="">' . $item->remarks . '</td>';
                $row .= '</tr>';
            }
        }

        $html .= '<table class="table table-bordered table-condensed table-hover table-striped" width="100%">';
        $html .= '<thead>';
        $html .= '<th width="25px">#</th>';
        $html .= '<th>Item</th>';
        $html .= '<th width="25px">Qty</th>';
        $html .= '<th width="50px">Unit</th>';
        $html .= '<th width="30%">Remarks/Specs</th>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $html .= $row;
        $html .= '</tbody>';
        $html .= '</table>';

        $data['html'] = $html;

        return json_encode($data);
    }

    function load_prf_items()
    {
        $data = array();
        $id = $this->input->post('id');

        $result = array();

        $msg = '';
        $qry = false;
        $func = '';
        $title = '';

        //FIND AND INSERT ALL ITEMS OF CURRENT PRF ITEMS
        $this->db->trans_begin();
        $prf_item_qry = $this->db->select('itemid,qty,unitid,remarks')
            ->from('eprs_transaction_items')
            ->where(array('prfid' => $id, 'status !=' => 0))
            ->get();

        if ($prf_item_qry->num_rows() > 0) {
            foreach ($prf_item_qry->result() as $item) {
                $additem = insert_db($this->db, 'eprs_transaction_items', (array) $item);

                $result[] = $additem->qry;
            }
        }

        if (in_array(false, $result)) {
            $this->db->trans_rollback();
            $msg = 'Error adding items from selected PRF.';
            $qry = false;
            $func = 'error';
            $title = 'FAIL!';
        } else {
            $this->db->trans_commit();
            $msg = 'Items from PRF has been added to list.';
            $qry = true;
            $func = 'success';
            $title = 'ITEMS ADDED!';
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }

    function prs_viewer_list()
    {
        $data = array();

        $route = $this->input->post('route');
        $status = $this->input->post('status');

        $this->load->helper('text');

        $app_flow_ids_arr = flow_id_arr('EPRS');
        $app_flow_ids = ($app_flow_ids_arr) ? implode(',', $app_flow_ids_arr) : false;
        $where_trails_last = ($app_flow_ids_arr) ? " AND rm.flowid IN ($app_flow_ids) " : "";
        $where_stages = ($app_flow_ids_arr) ? " AND flowid IN ($app_flow_ids) " : "";
        $data['traillast'] = $where_trails_last;

        if ($route && ((is_array($route) && count($route) > 0) || $route > 0)) {

            $levels = '';
            if (is_array($route)) {
                $levels = 'levels IN (' . implode(',', $route) . ')';
            } else {
                $levels = ($route > 0) ? 'levels = ' . $route : 'levels = ""';
            }

            $sql_stages = $this->db->query("
                SELECT sysid
                FROM prime_transaction_flow_main_stages
                WHERE $levels AND `status` = 1 $where_stages
                ");

            if ($sql_stages->num_rows() > 0) {
                foreach ($sql_stages->result() as $srow) {
                    $stages_ids[] = $srow->sysid;
                }
            }
            $stageids = implode(',', $stages_ids);
            $where = ' AND rmt.stageid IN (' . $stageids . ')';
        } else {
            $sql_stages = $this->db->query("
                SELECT sysid
                FROM prime_transaction_flow_main_stages
                WHERE `status` = 1 $where_stages
                ");

            if ($sql_stages->num_rows() > 0) {
                foreach ($sql_stages->result() as $srow) {
                    $stages_ids[] = $srow->sysid;
                }
            }
            $stageids = implode(',', $stages_ids);
            $where = ' AND rmt.stageid IN (' . $stageids . ')';
        }

        $status_ = '';
        if ($status) {
            if (is_array($status)) {
                $status_ = ' et.`status` IN (' . implode(',', $status) . ') ';
            } else {
                $status_ = ' et.`status` = ' . $status . ' ';
            }
        } else {
            $status_ = ' et.`status` > 0 ';
        }

        $roles = json_decode(get_user_role(user_id()));
        /*if (user_id() != 1 || ($roles && !array_search(24,array_column((array)$roles,'id')))) {
            $where .= ' AND et.createdby = ' . user_id();
        }*/

        $qry_details = $this->db->query("
            SELECT
                et.sysid,
                rmt.trnid,
                rmt.stageid,
                trm.datecreated AS submitted,
                rmt.datecreated AS updated,
                COUNT( eti.sysid ) AS items,
                et.justification,
                et.createdby,
                et.datecreated,
                et.dateupdated,
                et.`status`
            FROM
                eprs_transaction AS et
                LEFT JOIN eprs_transaction_items AS eti ON eti.prfid = et.sysid AND eti.`status` IN ( 300, 305 )
                JOIN (SELECT MAX(sysid) AS sysid,trnid,stageid,dataid,MAX(datecreated) AS datecreated,`status`,MAX(dateupdated) AS dateupdated FROM transaction_request_main_trails WHERE `status` != 0 GROUP BY trnid,stageid,dataid,`status` ORDER BY sysid ASC) AS rmt ON rmt.dataid = et.sysid
                INNER JOIN transaction_request_main AS trm ON rmt.trnid = trm.sysid  
            WHERE
                $status_ 
                $where 
            GROUP BY
                et.sysid,
                rmt.trnid,
                -- rmt.stageid,
                et.datecreated,
                et.createdby,
                et.typesid
            ORDER BY
                et.sysid ASC,
                rmt.datecreated DESC
        ");

        //$data['sql'] = $this->db->last_query();

        if ($qry_details->num_rows() > 0) {
            foreach ($qry_details->result() as $row) {
                $prsid = $row->sysid;
                $trnid = $row->trnid;
                $stageid = $row->stageid;
                $datesubmitted = $row->submitted;
                $justification = ellipsis($row->justification, 50);
                $createdby = $row->createdby;
                $created = $row->datecreated;
                $items = $row->items;

                /*if (strlen(trim($justification)) > 50) {
                    $jstr = ellipsis($justification,50);;
                    $justification = $jstr.' <a href="#" data-toggle="tooltip" class="tooltips" data-placement="right" data-attachement="body" title="'.$justification.'"><i class="fa fa-question-circle-o"></i></a>';
                }*/

                $creator = get_users_info($createdby);
                $requestor = '';

                if ($creator) {
                    $requestor = ucfirst($creator->firstname . ' ' . $creator->lastname);
                }

                $comment_cnt = '';
                $comment_msg = '';
                $qry_comments_cnt = $this->db->select('count(tc.trnid) AS cnt')
                    ->from('transaction_request_trails_comments AS tc')
                    ->where(array('tc.trnid' => $trnid, 'status' => 1))
                    ->get()->row();
                if ($qry_comments_cnt && $qry_comments_cnt->cnt > 0) {

                    $qry_comments_msg = $this->db->select('remarks')
                        ->from('transaction_request_trails_comments AS tc')
                        ->where(array('tc.trnid' => $trnid, 'status' => 1))
                        ->order_by('datecreated', 'desc')
                        ->get()->row();
                    $comment_msg = ($qry_comments_msg) ? $qry_comments_msg->remarks : '';
                    $max_length = 45;

                    if (strlen($comment_msg) > $max_length) {
                        $offset = ($max_length - 3) - strlen($comment_msg);
                        $comment_msg = substr($comment_msg, 0, strrpos($comment_msg, ' ', $offset)) . ' ...';
                    }
                    $comment_cnt = '<span class="badge badge-danger pull-right" style="margin-left: 5px;">' . $qry_comments_cnt->cnt . '</span>';
                }

                $creation_date = '';
                $qry_trails_last = $this->db->query("
                    SELECT rm.sysid AS trnid, rmt.sysid, rmt.datecreated, rmt.createdby, rmt.stageid, rmt.dataid, rmt.datecreated AS logdate
                    FROM transaction_request_main_trails AS rmt
                    INNER JOIN transaction_request_main AS rm ON rm.sysid = rmt.trnid
                    WHERE rmt.dataid = $prsid 
                    -- AND rmt.`status` = 1
                    $where_trails_last
                    ORDER BY rmt.datecreated DESC
                ")->row();

                //$data['traillast_qry'] = $this->db->last_query();
                $show = true;
                if ($route && $route > 0) {
                    if ($qry_trails_last && $qry_trails_last->stageid != $stageid) {
                        $show = false;
                    }
                }

                $trn_name = 'Unknown';
                $updated_date = 'None';
                $button = '';
                $from_created_by = 'None';


                if ($qry_trails_last) {

                    $creation_date = $row->datecreated;
                    $updated_date = $qry_trails_last->datecreated;

                    $user_info = get_users_info($qry_trails_last->createdby);
                    $from_created_by = ($user_info) ? $user_info->lastname . ', ' . $user_info->firstname : '';

                    $trn_name = '<a href="javascript:;" title="Current" class="label label-info">C</a> ' . get_trail_name($qry_trails_last->stageid);

                    //GET MODULEID FROM TRAIL
                    $stage = get_stage_details($qry_trails_last->stageid);
                    if (check_user_nav_access($stage->moduleid)) {
                        $button .= '<div class="btn-group btn-xs">';
                        $button .= btn_view_trn($qry_trails_last->sysid, $qry_trails_last->dataid, false, '_blank');
                        $button .= '</div>';
                    } else {
                        $button .= '<div class="btn-group btn-xs">';
                        $button .= '<a target="_blank" title="View PRF." data-content="body" href="' . base_url('module/49e3d046636e06b2d82ee046db8e6eb9a2e11e16/view/' . $prsid) . '" class="btn btn-primary btn-xs inline tooltips"><i class="fa fa-search fa-fw"></i></a>';
                        $button .= '</div>';
                    }
                }

                $trn_elapse = time_elapsed_diff($creation_date, $updated_date, true);
                $ovr_elapse = time_elapsed_diff($creation_date, date('Y-m-d h:m:s'));

                $time = $datesubmitted . '<br><small class="text-info">' . timeago($row->datecreated, sql_time()->DATETIME) . '</small>';
                $time_updated = $updated_date . '<br><small class="text-info">' . timeago($updated_date, sql_time()->DATETIME) . '</small>';

                if ($row->status == 1) {
                    $status = 'Pending';
                } else {
                    $status = get_types_label_format($row->status);
                    if ((in_array($row->status, array(0, 302, 303)))) {
                        $time_updated = $row->dateupdated . '<br><small class="text-info">' . timeago($row->dateupdated, sql_time()->DATETIME) . '</small>';
                    }
                }

                if ($show) {
                    $prfno = 'PRF' . date('ym', strtotime($created)) . str_pad($prsid, 5, '0', STR_PAD_LEFT);
                    $po = $this->db->select('ponumber as number')
                        ->from('eprs_po')
                        ->where(array('prfid' => $prsid, 'status' => 1))
                        ->get()->row();

                    if ($po) {
                        $ponum = 'PAE-' . str_pad($po->number, 8, '0', STR_PAD_LEFT);
                        $hide = 'hidden';
                    } else {
                        $ponum = 'N/A';
                        $hide = '';
                    }

                    $data['list'][] = array(
                        'expand' => btn_expand($prsid),
                        'prfno' => '<h4 class="text-danger bold" style="margin: 0px 0px;">' . $prfno . ' </h4> ',
                        'pono' => $ponum,
                        'submitted' => $time,
                        'from' => $from_created_by,
                        'updated' => $time_updated,
                        'items' => $items,
                        'justification' => $justification,
                        'requestor' => $requestor,
                        'dataid' => '',
                        'origid' => '',
                        'control' => $button,
                        'trn' => $trn_name,
                        'status' => $status,
                        'remarks' => $comment_msg . $comment_cnt
                    );
                }
            }
        }

        $data['columns'] = array(
            dt_column_array('expand', false, 'text-align-center', '1%'),
            dt_column_array('prfno', false, 'text-primary bold', '10%'),
            dt_column_array('pono', false, 'text-primary bold', '10%'),
            dt_column_array('submitted', false, false, '10%'),
            dt_column_array('updated', false, false, '10%'),
            dt_column_array('items', false, 'number'),
            dt_column_array('justification', false, false, '300px'),
            dt_column_array('trn', false, 'text-danger', '150px'),
            dt_column_array('remarks', false, 'text-info', '150px'),
            dt_column_array('status', false, 'text-info'),
            dt_column_array('control', false, 'controls', '5%'),
        );

        return json_encode($data);
    }

    function update_supplier_quotation()
    {
        /*
         * LOOKUP EACH SUBMITTED CHANGES IF ITEM HAS EXISTING ACTIVE QUOTATION.
         * IF EXIST, UPDATE TO STATUS 0.
         * INSERT NEW QUOTATION.
         */
        $data = array();
        $supplierid = $this->input->post('supplier');
        $rfop = $this->input->post('rfop');
        $prfid = $this->input->post('appid');
        $prfitemid = $this->input->post('prfitemid');
        $amounts = $this->input->post('amount');
        $exvat = $this->input->post('exvat');
        $exrate = $this->input->post('exrate');
        $remarks = $this->input->post('remarks');

        $msg = '';
        $qry = false;
        $func = '';
        //GET INITIAL SUPPLIER DETAILS
        $supplier_details = $this->db->select()
            ->from('eprs_quotation_suppliers')
            ->where('sysid', $supplierid)
            ->get()->row();

        //LOOP ALL PRF ITEMS SUBMITTED
        $transproc = array();
        $this->db->trans_begin();
        $supplier_set = array();
        if ($rfop && $rfop > 0 && $rfop != $supplier_details->rfop) {
            $supplier_set['rfop'] = $rfop;
        }

        if ($supplier_details->exvat != $exvat) {
            $supplier_set['exvat'] = $exvat;
        }

        if (count($supplier_set) > 0) {
            $update_supplier = update_db($this->db, 'eprs_quotation_suppliers', $supplier_set, array('sysid' => $supplierid));
            if (!$update_supplier->qry) {
                $transproc['updateSupplier'] = $update_supplier->error;
            }
        }

        if ($exrate && $exrate > 0) {
            //check exchange rate
            $supplier_qry = $this->db->select('esm.currency')
                ->from('eprs_quotation_suppliers as eqs')
                ->join('eprs_suppliers_main as esm', 'eqs.supplierid = esm.sysid', 'left')
                ->where(array('eqs.sysid' => $supplierid))
                ->get()->row();

            if ($supplier_qry) {
                $current_exrate = get_currency($supplier_qry->currency)->conversion;
                if ($current_exrate != $exrate) {
                    //UPDATE ON-DATE EXCHANGE RATE
                    $update_exrate = update_db($this->db, 'eprs_quotation_suppliers', array('exrate' => $exrate), array('sysid' => $supplierid));

                    if ($update_exrate->qry) {
                        //UPDATE TO-DATE EXCHANGE RATE
                        $update_currency = update_db($this->db, 'currency', array('conversion' => $exrate), array('sysid' => $supplier_qry->currency));
                        if (!$update_currency->qry) {
                            $transproc['updateExchangeRate'] = $update_currency->error;
                        }
                    } else {
                        $transproc['updateExchangeRate'] = $update_exrate->error;
                    }
                }
            }
        }

        if (is_array($prfitemid) && count($prfitemid) > 0) {
            foreach ($prfitemid as $index => $items) {
                $new_quotation = array(
                    'quotationid' => $supplierid,
                    'prfitemid' => $items,
                    'amount' => $amounts[$index],
                    'remarks' => $remarks[$index]
                );

                //LOOKUP EXISTING QUOTATIONS FROM SUPPLIER
                $past_quotation = $this->db->select('sysid,status')
                    ->from('eprs_quotation_details')
                    ->where(array('quotationid' => $supplierid, 'prfitemid' => $items, 'status !=' => 0))
                    ->get()->row();

                //REMOVE OLD QUOTATION AND ADD NEW ONE
                if ($past_quotation) {
                    $new_quotation['status'] = $past_quotation->status;
                    $x_quote = update_db($this->db, 'eprs_quotation_details', array('status' => 0), array('sysid' => $past_quotation->sysid, ));
                    if (!$x_quote->qry) {
                        $transproc['removeQuote'][$items] = $x_quote->error;
                    }
                }

                $add_quote = insert_db($this->db, 'eprs_quotation_details', $new_quotation);

                if (!$add_quote->qry) {
                    $transproc['addQuote'][$items] = $add_quote->error;
                }
            }
        }

        if (count($transproc) > 0) {
            $this->db->trans_rollback();
            $msg = 'One of more operations failed to execute.';
            $func = 'error';
        } else {
            $this->db->trans_commit();
            $msg = 'Successfully updated supplier quotations!';
            $qry = true;
            $func = 'success';
        }

        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;
        $data['title'] = 'Quotation Update';
        $data['errors'] = $transproc;

        return json_encode($data);
    }

    function update_supplier_details()
    {
        $data = array();

        $supplierid = $this->input->post('supplierid');
        $suppliercode = strtoupper($this->input->post('suppliercode'));
        $suppliername = $this->input->post('suppliername');
        $supplierdesc = $this->input->post('supplierdesc');
        $suppliercurrency = $this->input->post('suppliercurrency');
        $supplieraddress = $this->input->post('supplieraddress');
        $suppliertin = $this->input->post('suppliertin');
        $accountname = $this->input->post('accountname');
        $accountbank = $this->input->post('accountbank');
        $accountnumber = $this->input->post('accountnumber');
        $supplierphone = $this->input->post('supplierphone');
        $suppliermobile = $this->input->post('suppliermobile');
        $supplieremail = $this->input->post('supplieremail');

        $msg = '';
        $qry = false;
        $func = '';

        $main_arr = array();
        $online_arr = array();
        $address_arr = array();
        $contact_arr = array();

        //LOOKUP DETAILS
        $supplier_qry = $this->db->select('esm.codes, esm.name, esm.descs, esm.currency, esm.tin, esod.name AS accountname, esod.bank, esod.accountnum, esa.address')
            ->from('eprs_suppliers_main AS esm')
            ->join('eprs_suppliers_online_details AS esod', 'esm.sysid = esod.supplierid AND esod.status = 1', 'left')
            ->join('eprs_suppliers_address AS esa', 'esm.sysid = esa.supplierid AND esa.status = 1', 'left')
            ->where(array('esm.sysid' => $supplierid, 'esm.status' => 1))->get()->row();

        if ($supplier_qry) {
            $supplier = $supplier_qry;
            if ($supplier->codes != $suppliercode) {
                $main_arr['codes'] = $suppliercode;
            }

            if ($supplier->name != $suppliername) {
                $main_arr['name'] = $suppliername;
            }

            if ($supplier->descs != $supplierdesc) {
                $main_arr['descs'] = $supplierdesc;
            }

            if ($supplier->currency != $suppliercurrency) {
                $main_arr['currency'] = $suppliercurrency;
            }

            if ($supplier->tin != $suppliertin) {
                $main_arr['tin'] = $suppliertin;
            }

            if ($supplier->address != $supplieraddress) {
                $address_arr['address'] = $supplieraddress;
            }

            if ($supplier->accountname != $accountname || $supplier->bank != $accountbank || $supplier->accountnum != $accountnumber) {
                $online_arr = array(
                    'name' => $accountname,
                    'bank' => $accountbank,
                    'accountnum' => $accountnumber
                );
            }
        }

        $contact_qry = $this->db->select('typesid AS type,contact AS info')
            ->from('eprs_suppliers_contact')
            ->where(array('supplierid' => $supplierid, 'status' => 1))
            ->get();

        //QUERY EACH CONTACT
        $contact_types = array(
            1050 => $supplierphone,
            1051 => $suppliermobile,
            1053 => $supplieremail
        );

        foreach ($contact_types as $type => $value) {
            $contact_qry = $this->db->select('contact AS info')
                ->from('eprs_suppliers_contact')
                ->where(array('supplierid' => $supplierid, 'typesid' => $type, 'status' => 1))
                ->get()->row();

            if ($contact_qry) {
                if ($contact_qry->info != $value) {
                    $contact_arr[] = array(
                        'typesid' => $type,
                        'contact' => $value
                    );
                }
            } else {
                $contact_arr[] = array(
                    'typesid' => $type,
                    'contact' => $value
                );
            }
        }

        $data['arrays'] = array(
            'main' => $main_arr,
            'online' => $online_arr,
            'address' => $address_arr,
            'contact' => $contact_arr
        );

        if (count($main_arr) > 0 || count($online_arr) > 0 || count($address_arr) > 0 || count($contact_arr) > 0) {
            $this->db->trans_begin();
            if (count($main_arr) > 0) {
                $update_main = update_db($this->db, 'eprs_suppliers_main', $main_arr, array('sysid' => $supplierid));
                if (!$update_main->qry) {
                    $trn_err['updateMain'] = false;
                }
            }

            if (count($address_arr) > 0) {
                $remove_current_add = update_db($this->db, 'eprs_suppliers_address', array('status' => 0), array('supplierid' => $supplierid, 'status' => 1));
                if (!$remove_current_add->qry) {
                    $trn_err['removeAddress'] = false;
                }

                $address_arr['supplierid'] = $supplierid;
                $insert_new_add = insert_db($this->db, 'eprs_suppliers_address', $address_arr);
                if (!$insert_new_add->qry) {
                    $trn_err['newAddress'] = false;
                }
            }

            if (count($online_arr) > 0) {
                $remove_current_online = update_db($this->db, 'eprs_suppliers_online_details', array('status' => 0), array('supplierid' => $supplierid, 'status' => 1));
                if (!$remove_current_online->qry) {
                    $trn_err['removeOnline'] = false;
                }

                $online_arr['supplierid'] = $supplierid;
                $insert_new_online = insert_db($this->db, 'eprs_suppliers_online_details', $online_arr);
                if (!$insert_new_online->qry) {
                    $trn_err['newOnline'] = false;
                }
            }

            if (count($contact_arr) > 0) {
                foreach ($contact_arr as $contact) {
                    $remove_current_cont = update_db($this->db, 'eprs_suppliers_contact', array('status' => 0), array('supplierid' => $supplierid, 'typesid' => $contact['typesid'], 'status' => 1));
                    if (!$remove_current_cont->qry) {
                        $trn_err['removeContact'] = false;
                    }

                    if ($contact['contact'] != '') {
                        $contact['supplierid'] = $supplierid;
                        $insert_new_contact = insert_db($this->db, 'eprs_suppliers_contact', $contact);
                        if (!$insert_new_contact->qry) {
                            $trn_err['newContact'] = false;
                        }
                    }
                }
            }

            if (isset($trn_err) && count($trn_err) > 0) {
                $this->db->trans_rollback();
                $msg = 'One or more operations failed to execute.';
                $func = 'error';
            } else {
                $this->db->trans_commit();
                $msg = 'Supplier\'s information has been updated!';
                $func = 'success';
                $qry = true;
            }
        } else {
            $msg = 'No changes was made for this supplier.';
            $func = 'warning';
        }

        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;
        $data['title'] = 'Supplier Update';
        $data['errors'] = $trn_err ?? false;

        return json_encode($data);
    }

    function export_quotation_sheet()
    {
        $dataid = $this->input->post('dataid');
        $supplier = $this->input->post('supplier');
        $items = $this->input->post('items');
        $item_cnt = count($items);
        $supplier_name = '';
        $item_details = array();

        $data = array();

        //GET SUPPLIER DETAILS
        $supplier_qry = $this->db->select('esm.name,esm.descs')
            ->from('eprs_suppliers_main as esm')
            ->where('esm.sysid', $supplier)->get()->row();

        if ($supplier_qry) {
            $supplier_name = $supplier_qry->name;
        }

        //GET SUPPLIER ITEMS
        $items_qry = $this->db->select('eti.itemid,eti.remarks,eti.qty,eti.sysid,imd.fulldescription,u.unit_name,u.unit_code,eti.unitid')
            ->from('eprs_transaction_items AS eti')
            ->join('items_main_description AS imd', 'eti.itemid = imd.sysid', 'left')
            ->join('prime_unit AS u', 'eti.unitid = u.sysid', 'left')
            ->where('eti.status', 305)
            ->where('eti.prfid', $dataid)
            ->where_in('eti.sysid', $items)
            ->get();

        //$data['items_qry'] = $this->db->last_query();
        //$data['items'] = $items_qry->result();

        if ($items_qry->num_rows() > 0) {
            foreach ($items_qry->result() as $item) {
                $item_details[] = $item;
            }
        }


        $this->load->library('excel');

        $file = FCPATH . 'assets/templates/quotations.xlsx';

        $xls = PHPExcel_IOFactory::load($file);


        //$xls->setActiveSheetIndex();

        $sheet = $xls->getActiveSheet();
        $sheet->setTitle($supplier_qry->descs);
        $sheet->getStyle('B2')->getAlignment();
        $sheet->setCellValue('B2', $supplier_name);
        $row = 4;

        $styleB = $sheet->getStyle('B4');
        $styleC = $sheet->getStyle('C4');
        $styleD = $sheet->getStyle('D4');
        $styleE = $sheet->getStyle('E4');
        $styleF = $sheet->getStyle('F4');
        $styleG = $sheet->getStyle('G4');

        foreach ($sheet->getRowDimensions() as $rd) {
            $rd->setRowHeight(-1);
        }

        if ($item_cnt > 0) {
            foreach ($item_details as $detail) {
                $sheet->SetCellValue('B' . $row, $detail->fulldescription . ($detail->remarks ? '(' . $detail->remarks . ')' : ''));
                $sheet->SetCellValue('C' . $row, $detail->qty);
                $sheet->SetCellValue('D' . $row, $detail->unit_code);
                $sheet->SetCellValue('F' . $row, '=C' . $row . '*E' . $row);
                $row++;
                $sheet->insertNewRowBefore($row);
            }
            //$sheet->duplicateStyle($styleB,'B5:B'.$row);
            //$sheet->duplicateStyle($styleC,'C5:C'.$row);
            //$sheet->duplicateStyle($styleD,'D5:D'.$row);
            //$sheet->duplicateStyle($styleE,'E5:E'.$row);
            //$sheet->duplicateStyle($styleF,'F5:F'.$row);
            //$sheet->duplicateStyle($styleG,'G5:G'.$row);
            $sheet->getStyle('C4:C' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('E4:E' . $row)->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* \(#,##0.00\);_(* "-"??_);_(@_)');
            $sheet->getStyle('F4:F' . $row)->getNumberFormat()->setFormatCode('_(* #,##0.00_);_(* \(#,##0.00\);_(* "-"??_);_(@_)');
            $sheet->SetCellValue('B' . $row, 'Total')->getStyle()->getFont()->setBold(true);
            $sheet->SetCellValue('F' . $row, '=SUM(F4:F' . ($row - 1) . ')');
        }

        //$data['supplier'] = $supplier_name;
        $xlsSave = PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
        $fileName = $supplier_name . ' - ' . date('m.d.Y') . '.xlsx';

        ob_start();
        $xlsSave->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        $data['xls'] = array(
            'filename' => $fileName,
            'file' => 'data:application/vnd.ms-excel;base64,' . base64_encode($xlsData)
        );

        return json_encode($data);
    }

    function upload_past_purchases()
    {
        $data = array();
        $qry = false;
        $msg = '';
        $hascontract = false;

        $this->load->helper('directory');
        $this->load->library('upload');

        if (isset($_FILES["appfiledrop"])) {
            $dataid = $this->input->post('dataid');
            $stageid = $this->input->post('stageid');

            $filename = $_FILES['appfiledrop']['name'];
            $fileinfo = pathinfo($filename);

            //$location = get_stage_specific($stageid)->desc;
            $file_directory = FCPATH . 'uploads/attachments/eprs/pastpurchases/' . str_pad($dataid, 6, "0", STR_PAD_LEFT) . '/';

            $file_name = $fileinfo['filename'];
            $extract = explode('_', $file_name);

            $filetype = (is_array($extract) && count($extract) > 0) ? $extract[0] : $file_name;
            $count = (is_array($extract) && count($extract) > 0) ? ((isset($extract[1]) && ($extract[1] != '')) ? '_' . $extract[1] : '') : '';

            $data['filetype'] = $filetype;

            $upload = sys_upload_files('appfiledrop', $file_directory, $filename);
            $data['upload'] = $upload;

            if ($upload) {
                $msg = 'Files Uploaded!';
                $qry = true;
            }
        } else {
            $msg = 'Drop the file again!';
        }
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['contract'] = $hascontract;

        return json_encode($data);
    }

    function item_last_price()
    {
        $data = array();
        $itemid = $this->input->post('itemid');

        $lastprice_qry = $this->db->select('ti.prfid,po.ponumber,s.descs AS supplier,qd.amount,qs.sysid as quoteid,ti.remarks,po.sysid AS poid,prf.datecreated as prfdate,s.currency')
            ->from('eprs_transaction_items AS ti')
            ->join('eprs_transaction AS prf', 'prf.sysid = ti.prfid', 'left')
            ->join('eprs_quotation_details AS qd', 'ti.sysid = qd.prfitemid', 'left')
            ->join('eprs_quotation_suppliers AS qs', 'qd.quotationid = qs.sysid', 'left')
            ->join('eprs_suppliers_main AS s', 'qs.supplierid = s.sysid', 'left')
            ->join('eprs_po_details As pd', 'pd.quotationid = qs.sysid', 'left')
            ->join('eprs_po As po', 'pd.poid = po.sysid', 'inner')
            ->where(array('ti.itemid' => $itemid, 'qd.status' => 301))
            ->order_by('qd.datecreated DESC')->get();

        if ($lastprice_qry->num_rows() > 0) {
            $n = 1;
            foreach ($lastprice_qry->result() as $lp) {
                $currency = ($lp->currency != 83) ? '<span class="pull-left">' . get_currency($lp->currency)->symbol . '</span> ' : '';
                $view = '<a href="javasrcipt:;" title="PO Preview" id="btn_view_po" data-id="' . $lp->quoteid . '" class="btn btn-primary btn-sm inline"><i class="fa fa-search"></i> </a>';
                $data['lastprice'][] = array(
                    'num' => $n++,
                    'prf' => 'PRF' . date('ym', strtotime($lp->prfdate)) . str_pad($lp->prfid, 5, '0', STR_PAD_LEFT),
                    'po' => 'PAE-' . str_pad($lp->ponumber, 8, '0', STR_PAD_LEFT),
                    'supplier' => $lp->supplier,
                    'amount' => $currency . number_format($lp->amount, 2),
                    'remarks' => ($lp->remarks) ?: 'N/A',
                    'view' => $view
                );
            }
        }

        $columns = array(
            dt_column_array('num', '#', 'text-align-center', '1%'),
            dt_column_array('prf', 'PRF #', 'text-primary bold', ''),
            dt_column_array('po', 'PO #', 'text-primary bold', ''),
            dt_column_array('supplier', 'Supplier', '', '30%'),
            dt_column_array('amount', 'Quoted Amount', 'number', '10%'),
            dt_column_array('remarks', 'Spec/Remarks', '', '25%'),
            dt_column_array('view', 'View PO', 'text-align-center', '5%'),
        );

        $data['columns'] = $columns;

        return json_encode($data);
    }

    function cancel_purchase_request()
    {
        $data = array();
        $prfid = $this->input->post('prfid');
        $remarks = $this->input->post('remarks');
        $type = $this->input->post('type');
        $flowid = $this->input->post('flowid');
        $stageid = $this->input->post('stageid');
        $trnid = $this->input->post('trnid');

        $msg = '';
        $func = '';
        $title = '';
        $qry = false;

        //CHANGE STATUS OF QUOTATIONS TO 302
        if (in_array($type, array(1206, 1207))) {
            $this->db->trans_begin();
            $disapprove = update_db($this->db, 'eprs_transaction', array('status' => 303), array('sysid' => $prfid));

            if ($disapprove->qry) {
                update_db($this->db, 'transaction_request_main_trails', array('status' => 0), array('dataid' => $prfid, 'trnid' => $trnid, 'status' => 1));
                if (trim($remarks) != '') {
                    $comments_arr = array(
                        'trnid' => $trnid,
                        'trailid' => $stageid,
                        'remarks' => $remarks
                    );
                    insert_db($this->db, 'transaction_request_trails_comments', $comments_arr);
                }
                $this->db->trans_commit();
                $msg = 'This purchase request has been discontinued!';
                $func = 'success';
                $title = 'Cancelled!';
                $qry = true;
            } else {
                $this->db->trans_rollback();
                $msg = 'Failed to cancel PRF!';
                $func = 'error';
                $title = 'FAIL!';
            }
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }

    function get_supplier_payment_details()
    {
        $data = array();
        $supplierid = $this->input->post('supplierid');
        $prfid = $this->input->post('prfid');

        //CHECK IF SUPPLIERID IS THE QUOTATIONID OR SUPPLIER SYSID
        $supplier_qry = $this->db->select('esm.currency,eqs.exrate,esm.sysid AS id')
            ->from('eprs_quotation_suppliers as eqs')
            ->join('eprs_suppliers_main as esm', 'esm.sysid = eqs.supplierid')
            ->where(array('eqs.sysid' => $supplierid, 'eqs.prfid' => $prfid))
            ->get()->row();

        if (!$supplier_qry) {
            $supplier_qry = $this->db->select('sysid AS id')
                ->from('eprs_suppliers_main')
                ->where('sysid', $supplierid)->get()->row();
        }

        $supplier_details = $this->db->select('s.name, s.tin, sa.address, sod.name AS accountname, sod.bank, sod.accountnum')
            ->from('eprs_suppliers_main AS s')
            ->join('eprs_suppliers_address AS sa', 's.sysid = sa.supplierid', 'left')
            ->join('eprs_suppliers_online_details AS sod', 's.sysid = sod.supplierid AND sod.status = 1', 'left')
            ->where(array('s.sysid' => $supplier_qry->id, 's.status' => 1))
            ->get()->row();

        if ($supplier_details) {
            $data['payment_name'] = $supplier_details->name;
            $data['supplier_tax_no'] = $supplier_details->tin;
            $data['payment_address'] = $supplier_details->address;
            $data['online_account_name'] = $supplier_details->accountname;
            $data['online_account_bank'] = $supplier_details->bank;
            $data['online_account_number'] = $supplier_details->accountnum;
        }

        //GET QUOTATION ID
        if ($prfid > 0) {
            $po_details = $this->db->select('po.paytype,po.payterm,po.purpose,po.notes')
                ->from('eprs_po_details As po')
                ->join('eprs_quotation_suppliers AS s', 's.sysid = po.quotationid', 'left')
                ->where(array('s.prfid' => $prfid, 's.supplierid' => $supplier_qry->id, 'po.status' => 1))
                ->get()->row();

            if ($po_details) {
                $data['select2_paytype'] = $po_details->paytype;
                $data['rfp_payment_term'] = $po_details->payterm;
                $data['rfp_purpose'] = $po_details->purpose;
                $data['rfp_notes'] = $po_details->notes;
            }
        }

        return json_encode($data);
    }
}
