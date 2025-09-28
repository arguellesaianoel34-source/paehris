<?php

class Model_installation extends CI_Model
{

    function dt_install_inverters() {
        $data = array();
        $inverters = array();

        $appid = $this->input->post('appid');
        $details = get_application_details($appid);
        $data['sizeid'] = $sizeid = ($details->info->systemsizeid != null) ? $details->info->systemsizeid : false;

        //-- LOOKUP SAVED INVERTER DETAILS
        $inverter_details_qry = $this->db->select('inverters.*')
            ->from('application_installation_material_details AS inverters')
            ->join('items_main_description as imd','imd.sysid = inverters.itemid')
            ->where(array('inverters.appid' => $appid,'inverters.status' => 1))
            ->like('imd.fulldescription','inverter')
            ->get();

        if ($inverter_details_qry->num_rows() > 0) {
            foreach ($inverter_details_qry->result() AS $detail) {
                $brand = $this->db->select('codes')
                    ->from('prime_brands')
                    ->where(array('sysid' => $detail->brand))
                    ->get()->row();

                $inverters[$detail->itemid][] = array(
                    'sysid' => $detail->sysid,
                    'itemid' => $detail->itemid,
                    'brand' => ($brand) ? $brand->codes : 'N/A',
                    'brandid' => $detail->brand,
                    'sn' => $detail->serialnumber
                );
            }
        }

        //LOOKUP INVERTER QUANTITY SET IN SYSTEM SETUP
        $system_parts_qry = $this->db->select('csp.*,imd.fulldescription')
            ->from('customer_system_parts AS csp')
            ->join('items_main_description as imd','csp.itemid = imd.sysid AND imd.status = 1 AND imd.fulldescription LIKE "%inverter%"','inner')
            ->join('prime_unit as u','csp.unitid = u.sysid','left')
            ->where(array('csp.appid' => $appid,'csp.status !=' => 0))->get();

        $data['qry_assigned'] = $this->db->last_query();

        if ($system_parts_qry->num_rows() > 0) {
            foreach ($system_parts_qry->result() AS $inverter) {
                $inverter_desc = $inverter->fulldescription;

                if (preg_match("/{set}/i", strtolower($inverter_desc)) !== false) {
                    preg_match_all('!\d+\.*\d*!', $inverter->fulldescription, $numeric);

                    if (count($numeric) > 0) {
                        $inverter_desc = $numeric[0][0] . 'kWp';
                    }
                }

                for ($x = 0; $x < $inverter->qty; $x++) {
                    if (key_exists($inverter->itemid,$inverters) && count($inverters[$inverter->itemid]) && key_exists($x,$inverters[$inverter->itemid])) {
                        $inv = $inverters[$inverter->itemid][$x];
                        $control = '';
                        $control .= '<div class="btn-group pull-right" id="item_controls" style="width: 80px !important;">';
                        $control .= '<a href="javascript:;" class="btn btn-sm btn-primary inline" id="btn_edit_inverter"><i class="fa fa-edit"></i> </a>';
                        $control .= '<a href="javascript:;" class="btn btn-sm btn-danger inline" id="btn_remove_inverter" data-id="' . $inv['sysid'] . '"><i class="fa fa-times"></i> </a>';
                        $control .= '</div>';
                        $data['list'][] = array(
                            'inverter' => '<span class="text-primary bold ">'.$inverter_desc . '</span><input type="hidden" id="inverter_id" value="' . $inv['sysid'] . '">',
                            'brand' => '<span class="inverter_editable" data-id="select2brand" data-value="' . $inv['brandid'] . '">' . $inv['brand'] . '</span>',
                            'sn' => '<span class="inverter_editable" data-id="inverter_sn" data-value="' . $inv['sn'] . '">' . $inv['sn'] . '</span>',
                            'control' => $control
                        );
                    } else {
                        $control = '';
                        $control .= '<div class="btn-group pull-right" id="item_controls" style="width: 80px !important;">';
                        $control .= '<a href="javascript:;" class="btn btn-sm btn-primary inline" id="btn_save_inverter" data-item="'.$inverter->itemid.'"><i class="fa fa-save"></i> </a>';
                        $control .= '</div>';
                        $data['list'][] = array(
                            'inverter' => '<span class="text-primary bold ">'.$inverter_desc . '</span><input type="hidden" id="inverter_id" value="">',
                            'brand' => '<input class="form-control" id="select2brand" value="" style="width: 100% !important;" required>',
                            'sn' => '<input class="form-control" id="inverter_sn" value="" style="width: 100% !important;" required>',
                            'control' => $control
                        );
                    }
                }
            }
        } else {
            //IF NO SYSTEM SETUP WAS SAVED...
            //IF STANDARD: FETCH FROM TEMPLATE
            if ($details->info->systemtype == 1) {
                $system_parts_qry = $this->db->select('csp.*,imd.fulldescription,u.unit_code as unit')
                    ->from('customer_system_parts_template AS csp')
                    ->join('customer_system_group_template as csg','csp.groupid = csg.sysid','left')
                    ->join('items_main_description as imd','csp.itemid = imd.sysid AND imd.status = 1 AND imd.fulldescription LIKE "%inverter%"','inner')
                    ->join('prime_unit as u','csp.unitid = u.sysid','left')
                    ->where(array('csg.systypeid' => $sizeid,'csp.status !=' => 0))->get();

                if ($system_parts_qry->num_rows() > 0) {
                    foreach ($system_parts_qry->result() AS $inverter) {
                        $inverter_desc = $inverter->fulldescription;

                        if (preg_match("/{set}/i", strtolower($inverter_desc)) !== false) {
                            preg_match_all('!\d+\.*\d*!', $inverter->fulldescription, $numeric);

                            if (count($numeric) > 0) {
                                $inverter_desc = $numeric[0][0] . ' kWp';
                            }
                        }

                        for ($x = 0; $x < $inverter->qty; $x++) {
                            if (key_exists($inverter->itemid,$inverters) && count($inverters[$inverter->itemid]) && key_exists($x,$inverters[$inverter->itemid])) {

                                $inv = $inverters[$inverter->itemid][$x];
                                $control = '';
                                $control .= '<div class="btn-group pull-right" id="item_controls" style="width: 80px !important;">';
                                $control .= '<a href="javascript:;" class="btn btn-sm btn-primary inline" id="btn_edit_inverter"><i class="fa fa-edit"></i> </a>';
                                $control .= '<a href="javascript:;" class="btn btn-sm btn-danger inline" id="btn_remove_inverter" data-id="' . $inv['sysid'] . '"><i class="fa fa-times"></i> </a>';
                                $control .= '</div>';
                                $data['list'][] = array(
                                    'inverter' => $inverter_desc . '<input type="hidden" id="inverter_id" value="' . $inv['sysid'] . '">',
                                    'brand' => '<span class="inverter_editable" data-id="select2brand" data-value="' . $inv['brandid'] . '">' . $inv['brand'] . '</span>',
                                    'sn' => '<span class="inverter_editable" data-id="inverter_sn" data-value="' . $inv['sn'] . '">' . $inv['sn'] . '</span>',
                                    'control' => $control
                                );
                            } else {
                                $control = '';
                                $control .= '<div class="btn-group pull-right" id="item_controls" style="width: 80px !important;">';
                                $control .= '<a href="javascript:;" class="btn btn-sm btn-primary inline" id="btn_save_inverter" data-item="'.$inverter->itemid.'"><i class="fa fa-save"></i> </a>';
                                $control .= '</div>';
                                $data['list'][] = array(
                                    'inverter' => $inverter_desc . '<input type="hidden" id="inverter_id" value="">',
                                    'brand' => '<input class="form-control"  id="select2brand" value="" style="width: 100% !important;" required>',
                                    'sn' => '<input class="form-control" id="inverter_sn" value="" style="width: 100% !important;" required>',
                                    'control' => $control
                                );
                            }

                        }
                    }
                } else {
                    if (count($inverters) > 0) {
                        foreach ((object)$inverters AS $itemid => $inv) {
                            $inverter_desc = '';
                            $item_qry = $this->db->select('fulldescription')
                                ->from('items_main_description')
                                ->where('sysid',$itemid)
                                ->get()->row();

                            if ($item_qry) {
                                $inverter_desc = $item_qry->fulldescription;
                                if (preg_match("/{set}/i", strtolower($inverter_desc)) !== false) {
                                    preg_match_all('!\d+\.*\d*!', $item_qry->fulldescription, $numeric);

                                    if (count($numeric) > 0) {
                                        $inverter_desc = $numeric[0][0] . ' kWp';
                                    }
                                }
                            }

                            $control = '';
                            $control .= '<div class="btn-group pull-right" id="item_controls" style="width: 80px !important;">';
                            $control .= '<a href="javascript:;" class="btn btn-sm btn-primary inline" id="btn_edit_inverter"><i class="fa fa-edit"></i> </a>';
                            $control .= '<a href="javascript:;" class="btn btn-sm btn-danger inline" id="btn_remove_inverter" data-id="' . $inv['sysid'] . '"><i class="fa fa-times"></i> </a>';
                            $control .= '</div>';
                            $data['list'][] = array(
                                'inverter' => $inverter_desc . '<input type="hidden" id="inverter_id" value="' . $inv['sysid'] . '">',
                                'brand' => '<span class="inverter_editable" data-id="select2brand" data-value="' . $inv['brandid'] . '">' . $inv['brand'] . '</span>',
                                'sn' => '<span class="inverter_editable" data-id="inverter_sn" data-value="' . $inv['sn'] . '">' . $inv['sn'] . '</span>',
                                'control' => $control
                            );
                        }
                    } else {
                        $data['empty'] = 'No inverters were listed for this customer.';
                    }
                }
            } else {
                if (count($inverters) > 0) {
                    foreach ((object)$inverters AS $itemid => $inv) {
                        $inverter_desc = '';
                        $item_qry = $this->db->select('fulldescription')
                            ->from('items_main_description')
                            ->where('sysid',$itemid)
                            ->get()->row();

                        if ($item_qry) {
                            $inverter_desc = $item_qry->fulldescription;
                            if (preg_match("/{set}/i", strtolower($inverter_desc)) !== false) {
                                preg_match_all('!\d+\.*\d*!', $item_qry->fulldescription, $numeric);

                                if (count($numeric) > 0) {
                                    $inverter_desc = $numeric[0][0] . 'kWp';
                                }
                            }
                        }

                        foreach ($inv AS $row) {
                            $control = '';
                            $control .= '<div class="btn-group pull-right" id="item_controls" style="width: 80px !important;">';
                            $control .= '<a href="javascript:;" class="btn btn-sm btn-primary inline" id="btn_edit_inverter"><i class="fa fa-edit"></i> </a>';
                            $control .= '<a href="javascript:;" class="btn btn-sm btn-danger inline" id="btn_remove_inverter" data-id="' . $row['sysid'] . '"><i class="fa fa-times"></i> </a>';
                            $control .= '</div>';
                            $data['list'][] = array(
                                'inverter' => $inverter_desc . '<input type="hidden" id="inverter_id" value="' . $row['sysid'] . '">',
                                'brand' => '<span class="inverter_editable" data-id="select2brand" data-value="' . $row['brandid'] . '">' . $row['brand'] . '</span>',
                                'sn' => '<span class="inverter_editable" data-id="inverter_sn" data-value="' . $row['sn'] . '">' . $row['sn'] . '</span>',
                                'control' => $control
                            );
                        }
                    }
                } else {
                    $data['empty'] = 'No inverters were listed for this customer.';
                }
            }

        }

        $data['columns'] = array(
            dt_column_array('inverter','Inverter','text-primary bold','30%'),
            dt_column_array('brand','Brand','','40%'),
            dt_column_array('sn','Serial #','','40%'),
            dt_column_array('control','<i class="fa fa-wrench"></i>','','10%'),
        );

        //IF NO SYSTEM SETUP WAS SAVED...
        //IF STANDARD: FETCH FROM TEMPLATE
        //-- LOOKUP SAVED INVERTER DETAILS
        //IF NON-STANDARD: RETURN EMPTY

        return json_encode($data);
    }

    function dt_installation_setup() {
        $data = array();
        $appid = $this->input->post('appid');
        $itemtype = $this->input->post('itemtype');

        $viewing = false;

        $comp = array();
        $acce = array();
        $cons = array();
        $option5y = 0;
        $option10y = 0;
        $count10yrs = 0;
        $count5yrs = 0;
        $msg = '';

        $empty = array(
            '','No system components loaded!','No system Accessories loaded!','No installation consumables loaded!'
        );

        $details = get_application_details($appid);
        $data['sizeid'] = $sizeid = ($details->info->systemsizeid != null) ? $details->info->systemsizeid : false;

        $data['columns'] = array(
            dt_column_array('num','#','number','30px'),
            dt_column_array('desc','Item Description',false,'280px'),
            dt_column_array('qty','Qty','number','85px'),
            dt_column_array('unit','Unit',false,'30px'),
            dt_column_array('serial','SN','number','85px'),
        );

        $install_list = $this->db->select('list.appid,list.qty,list.unitid,list.sysid as referenceitemid,list.itemid,list.itemtype AS type,imd.fulldescription AS name')
            ->from('installation_item_list AS list')
            ->join('items_main_description AS imd','imd.sysid = list.itemid')
            ->where(array('list.appid' => $appid,'list.status' => 1))
            ->get();

        $data['list_qry'] = $this->db->last_query();
        if ($install_list->num_rows() > 0) {
            $num = 1;
            foreach ($install_list->result() as $item) {
                //$data['items'][] = $item;
                if ($item->type == $itemtype) {
                    $inventory_qry = $this->db->select('
                        iti.sysid,
                        MAX(CASE WHEN iti.type = 22 THEN iti.qty END) AS qty,
                        MAX(CASE WHEN iti.type = 21 THEN iti.qty END) AS returned,
                        MAX(CASE WHEN iti.type = 25 THEN iti.qty END) AS additional,
                        GROUP_CONCAT(iti.remarks SEPARATOR ";") as remarks
                        ')
                        ->from('inventory_transaction_items AS iti')
                        ->join('inventory_transaction_reference AS itr', 'iti.referenceid = itr.sysid', 'left')
                        ->join('inventory_transaction_group AS itg', 'itg.sysid = itr.groupid', 'left')
                        ->where(array('itr.referenceid' => $item->appid, 'iti.itemid' => $item->itemid, 'iti.referenceitemid' => $item->referenceitemid))
                        ->where_in('iti.status', array(1, 300, 301))
                        ->group_by('iti.referenceitemid')
                        ->get()->row();

                    $data['inventory_qry'] = $this->db->last_query();
                    $utilized = $item->qty + ($inventory_qry ? $inventory_qry->additional - $inventory_qry->returned : 0);

                    $serial = '';
                    if (preg_match('(solar panel|inverter|battery)', strtolower($item->name))) {
                        $serial_qry = $this->db->select('serialnumber')
                            ->from('application_installation_material_details')
                            ->where(array('appid' => $appid, 'itemid' => $item->itemid, 'status' => 1))
                            ->get();

                        if ($serial_qry->num_rows() > 0) {
                            $serials = array();
                            foreach ($serial_qry->result() as $serial_item) {
                                $serials[] = $serial_item->serialnumber;
                            }
                            $serial .= ellipsis(implode(', ', $serials), 5);
                        }
                    }

                    if ($utilized > 0) {
                        $comp[] = array(
                            'num' => $num++,
                            'desc' => $item->name,
                            'unit' => unit_query($item->unitid)->code,
                            'serial' => $serial,
                            'qty' => rtrim(rtrim(number_format($utilized, 2), '0'), '.'),
                        );
                    }
                }
            }
        } else {
            $data['columns'] = array(
                dt_column_array('num',false,'number','10%'),
                dt_column_array('item',false,'text-primary bold','50%'),
                dt_column_array('qty',false,'number','5%'),
                dt_column_array('unit',false,false,'5%'),
                dt_column_array('control',false,false,'5%'),
            );

            $system_parts_qry = $this->db->select('csp.*,imd.fulldescription,u.unit_code as unit')
                ->from('customer_system_parts AS csp')
                ->join('items_main_description as imd', 'csp.itemid = imd.sysid and imd.status = 1', 'left')
                ->join('prime_unit as u', 'csp.unitid = u.sysid', 'left')
                ->where(array('csp.appid' => $appid, 'csp.status !=' => 0))->get();

            if ($system_parts_qry->num_rows() > 0) {
                $compn = 1;
                foreach ($system_parts_qry->result() as $parts) {
                    $control = '';
                    $totalprice = $parts->unitprice * $parts->qty;
                    $control .= '<div class="btn-group pull-right" id="item_controls" style="width: 80px !important;">';
                    $control .= '<a href="javascript:;" class="btn btn-sm btn-primary inline" id="btn_edit_item"><i class="fa fa-edit"></i> </a>';
                    $control .= '<a href="javascript:;" class="btn btn-sm btn-danger inline" id="btn_remove_item" data-id="' . $parts->sysid . '"><i class="fa fa-times"></i> </a>';
                    $control .= '</div>';
                    if ($parts->type == $itemtype) {
                        $comp[] = array(
                            'num' => '<input type="hidden" id="input_id" value="' . $parts->sysid . '" name="sysid" disabled>' . $compn++,
                            'item' => $parts->fulldescription,
                            'unit' => $parts->unit,
                            'qty' => dt_inline_input('qty', false, $parts->qty, false, 'input-md', array('width' => '50px !important')),
                            'price' => dt_inline_input('unitprice', false, number_format($parts->unitprice, 2), false, 'input-md', array('width' => '100px !important')),
                            'total' => number_format($totalprice, 2),
                            'control' => $control
                        );
                    }
                }
            } else {
                if ($details->info->systemtype == 1) {
                    $system_parts_qry = $this->db->select('csp.*,imd.fulldescription,u.unit_code as unit')
                        ->from('customer_system_parts_template AS csp')
                        ->join('customer_system_group_template as csg', 'csp.groupid = csg.sysid', 'left')
                        ->join('items_main_description as imd', 'csp.itemid = imd.sysid and imd.status = 1', 'left')
                        ->join('prime_unit as u', 'csp.unitid = u.sysid', 'left')
                        ->where(array('csg.systypeid' => $sizeid, 'csp.status !=' => 0))->get();

                    $data['template_qry'] = $this->db->last_query();

                    if ($system_parts_qry->num_rows() > 0) {
                        $num = 1;
                        $accen = 1;
                        $consn = 1;
                        foreach ($system_parts_qry->result() as $parts) {
                            $totalprice = $parts->unitprice * $parts->qty;
                            $control = '';
                            $control .= '<div class="btn-group pull-right" id="item_controls" style="width: 80px !important;">';
                            $control .= '<a href="javascript:;" class="btn btn-sm btn-primary inline" id="btn_edit_item"><i class="fa fa-edit"></i> </a>';
                            $control .= '<a href="javascript:;" class="btn btn-sm btn-danger inline" id="btn_remove_item" data-id="' . $parts->sysid . '"><i class="fa fa-times"></i> </a>';
                            $control .= '</div>';
                            //load only type
                            if ($parts->type == $itemtype) {
                                $comp[] = array(
                                    'num' => $num++,
                                    'item' => $parts->fulldescription,
                                    'unit' => $parts->unit,
                                    'qty' => $parts->qty,
                                    'price' => number_format($parts->unitprice, 2),
                                    'total' => number_format($totalprice, 2),
                                    'control' => $control
                                );
                            }


                        }

                    } else {
                        $msg = $empty[$itemtype];
                    }
                } else {
                    $msg = $empty[$itemtype];
                }
            }
        }

        $data['parts'] = $comp;
        $data['msg'] = $msg;

        return json_encode($data);
    }

    function get_installation_system_size($appid = false) {
        $data = array();
        $appid = ($appid) ?: $this->input->post('appid');
        $details = get_application_details($appid);
        $data['sizeid'] = $sizeid = ($details->info->systemsizeid != null) ? $details->info->systemsizeid : false;

        //COUNT ITEM TYPES
        $item_types = $this->db->select('
            SUM(CASE WHEN list.itemtype = 1 THEN 1 ELSE 0 END) as comp,
            SUM(CASE WHEN list.itemtype = 2 THEN 1 ELSE 0 END) as acce,
            SUM(CASE WHEN list.itemtype = 3 THEN 1 ELSE 0 END) as sitm,
            SUM(CASE WHEN list.itemtype = 4 THEN 1 ELSE 0 END) as othr
            ')
            ->from('installation_item_list AS list')
            ->where(array('list.appid' => $appid,'status' => 1))
            ->group_by('list.appid')->get()->row();

        if ($item_types) {
            $tabs = '';
            if ($item_types->comp > 0) {
                $tabs .= '<li class="active"><a href="#sps_components" data-toggle="tab" aria-expanded="true" data-id="1"> Components </a></li>';
            }
            if ($item_types->acce > 0) {
                $tabs .= '<li class=""><a href="#sps_accessories" data-toggle="tab" aria-expanded="true" data-id="2"> Accessories </a></li>';
            }
            if ($item_types->sitm > 0) {
                $tabs .= '<li class=""><a href="#sps_sitmats" data-toggle="tab" aria-expanded="true" data-id="3"> Situational </a></li>';
            }
            if ($item_types->othr > 0) {
                $tabs .= '<li class=""><a href="#sps_others" data-toggle="tab" aria-expanded="true" data-id="4"> Others </a></li>';
            }

            $data['tabs'] = $tabs;
        } else {

            $setup_qry = $this->db->select('csg.sysid,csg.systypeid,csg.sptypeid,spt.descs AS paneltype,csg.nop,csg.nos,csg.panelsperstring,csg.invertersize')
                ->from('customer_system_group AS csg')
                ->join('solar_panel_types AS spt', 'csg.sptypeid = spt.sysid')
                ->where(array('csg.appid' => $appid, 'csg.status' => 1))
                ->get()->row();

            if ($setup_qry) {
                $panel = explode(' ', $setup_qry->paneltype);
                $setup_qry->paneltype = $panel[0] . 'W';
                $data['details'] = $setup_qry;
            } else {
                //query for default associated system size
                if ($details->info->systemtype == 1) {

                    $template_qry = $this->db->select('t.sysid, t.name, css.descs as systemtype, spt.descs as paneltype, t.nop, t.nos, t.panelsperstring, t.invertersize')
                        ->from('customer_system_group_template as t')
                        ->join('customer_system_size as css', 't.systypeid = css.sysid', 'left')
                        ->join('solar_panel_types as spt', 't.sptypeid = spt.sysid AND spt.status = 1', 'left')
                        ->where(array('t.systypeid' => $sizeid, 't.status' => 1))->get()->row();

                    if ($template_qry) {
                        $template = $template_qry;
                        $panel = explode(' ', $template->paneltype);
                        $template->paneltype = $panel[0] . 'W';
                        $data['details'] = array(
                            'name' => $template->name,
                            'systemtype' => $template->systemtype,
                            'paneltype' => $template->paneltype,
                            'nop' => $template->nop,
                            'nos' => $template->nos,
                            'panelsperstring' => $template->panelsperstring,
                            'invertersize' => $template->invertersize,
                        );
                        $data['notes'] = '<i class="fa fa-warning"></i> No SP Setup found. Default Setup Loaded!';
                    } else {
                        $data['nosetup'] = '<i class="fa fa-warning"></i> No SP Setup was found for this customer.';
                    }
                } else {
                    $data['nosetup'] = '<i class="fa fa-warning"></i> No SP Setup was found for this customer.<br>Please ask Assessment\'s Components and Materials to add them.';
                }
            }
        }

        return json_encode($data);
    }

    function get_installation_dates() {
        $data = array();
        $appid = $this->input->post('appid');

        $installation =array();
        $energized =array();

        $installation['text'] = '<span class="text-primary bold hidden" id="date_installation" data-type="1" data-value=""></span>';
        $energized['text'] = '<span class="text-primary bold hidden" id="date_installation" data-type="2" data-value=""></span>';

        $dates_qry = $this->db->select('team,installed,energized')
            ->from('application_installation_dates')
            ->where(array('appid' => $appid,'status' => 1))
            ->get()->row();
            if ($dates_qry) {
                $date = $dates_qry;

                $buttons = '';
                $buttons .= '<div class="btn-group pull-right" id="date_controls" style="width: 75px !important;">';
                $buttons .= '<a href="javascript:;" class="btn btn-sm btn-primary inline" id="btn_edit_date"><i class="fa fa-edit"></i> </a>';
                $buttons .= '<a href="javascript:;" class="btn btn-sm btn-danger inline" id="btn_remove_date"><i class="fa fa-times"></i> </a>';
                $buttons .= '</div>';

                if ($date->installed) {
                    $installation['value'] = $date->installed;
                    $installation['text'] = '<span class="text-primary bold" id="date_installation" data-type="1" data-value="' . $date->installed . '">' . date('F j, Y', strtotime($date->installed)) . '</span>';
                    $installation['buttons'] = $buttons;
                }

                if ($date->energized) {
                    $energized['value'] = $date->energized;
                    $energized['text'] = '<span class="text-primary bold" id="date_energized" data-type="2" data-value="' . $date->energized . '">' . date('F j, Y', strtotime($date->energized)) . '</span>';
                    $energized['buttons'] = $buttons;
                }
            }

        $data['installation'] = $installation;
        $data['energized'] = $energized;

        return json_encode($data);
    }

    function save_date() {
        $data = array();
        $appid = $this->input->post('appid');
        $setdate = $this->input->post('setdate');
        $type = $this->input->post('type');

        $msg = '';
        $title = '';
        $func = '';
        $qry = false;
        $updated = '';

        $dupe = array();
        $errors = array();

        $datetype = ($type == 1) ? 'Installation Date' : 'Date Energized';

        //LOOKUP EXISTING DATES WITH SAME TYPE
        $date_qry = $this->db->select('sysid,setdate')
            ->from('application_installation_dates')
            ->where(array('appid' => $appid,'type' => $type,'status' => 1))
            ->get();

        $this->db->trans_begin();
        if ($date_qry->num_rows() > 0) {
            foreach ($date_qry->result() AS $date) {
                if ($date->setdate == $setdate) {
                    $dupe[] = true;
                }
            }

            if (!count($dupe)) {
                $delete = update_db($this->db, 'application_installation_dates', array('status' => 0), array('appid' => $appid, 'type' => $type, 'status' => 1));
                if ($delete->qry) {
                    $updated = $datetype . ' has been updated!';
                } else {
                    $errors['delete_past'] = true;
                }
            }
        }

        if (!count($dupe)) {
            $add_date = insert_db($this->db, 'application_installation_dates', $this->input->post());
            if (!$add_date->qry) {
                $errors['insert_new'] = true;
            }

            if (count($errors) > 0) {
                $this->db->trans_rollback();
                $msg = ($updated != '') ? 'Failed to update ' . $datetype : 'Failed to set ' . $datetype;
                $title = 'Failed!';
                $func = 'error';
            } else {
                $this->db->trans_commit();
                $data['text'] = date('F j, Y', strtotime($setdate));
                $data['value'] = $setdate;
                $msg = ($updated != '') ? $updated : $datetype . ' has been saved!';
                $title = ($updated != '') ? 'Updated!' : 'Success!';
                $func = 'success';
                $qry = true;
            }
        } else {
            $msg = 'Date submitted is the same as the current date set.';
            $title = 'Duplicate!';
            $func = 'warning';
        }

        $data['msg'] = $msg;
        $data['title'] = $title;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['errors'] = $errors;

        return json_encode($data);
    }

    function select2_brand($brandid = false) {
        $data = array();

        if ($brandid) {
            $brand_qry = $this->db->select()
                ->from('prime_brands')
                ->where(array('sysid' => $brandid,'status' => 1))
                ->get()->row();

            if ($brand_qry) {
                $data = $brand_qry;
            }
        } else {
            $brand_qry = $this->db->select()
                ->from('prime_brands')
                ->where('status',1)
                ->get();

            if ($brand_qry->num_rows() > 0) {
                foreach ($brand_qry->result() AS $brand) {
                    $data['list'][] = array(
                        'id' => $brand->sysid,
                        'text' => $brand->descs
                    );
                }
            }
        }

        return ($brandid) ? $data : json_encode($data);
    }

    function select2_inverter() {
        $data = array();
        $list = array();
        $list_ = array();
        $filtered = array();

        $pv_qry = $this->db->select()
            ->from('items_main_description')
            ->where('status',1)
            ->like('fulldescription','inverter')
            ->not_like('fulldescription','yrs')
            ->get();

        $data['qry'] = $this->db->last_query();

        if ($pv_qry->num_rows() > 0) {
            foreach ($pv_qry->result() AS $pv) {
                $inverter_desc = $pv->fulldescription;
                if (preg_match("/{set}/i", strtolower($inverter_desc)) !== false) {
                    preg_match_all('!\d+\.*\d*!', $pv->fulldescription, $numeric);

                    if (count($numeric) > 0) {
                        $inverter_desc = $numeric[0][0] . ' kWp';
                    }
                }
                $list[$pv->sysid] = $inverter_desc;
            }

            if (count($list) > 0) {
                foreach ($list AS $key => $value) {
                    $search = array_search($value,$list_);
                    if ($search > 0) {
                        if ($key > $search) {
                            unset($list_[$search]);
                            $list_[$key] = $value;
                        }
                    } else {
                        $list_[$key] = $value;
                    }
                }
            }

            if (count($list_) > 0) {
                foreach ($list_ AS $keys => $values) {
                    $filtered[] = array(
                        'id' => $keys,
                        'text' => $values
                    );
                }
            }
        }

        $data['list'] = $filtered;

        return json_encode($data);
    }

    function save_inverter_details() {
        $data = array();
        $id = $this->input->post('id');
        $appid = $this->input->post('appid');
        $itemid = $this->input->post('itemid');
        $brand = $this->input->post('brand');
        $serialnumber = $this->input->post('serialnumber');

        $msg = '';
        $title = '';
        $func = '';
        $qry = false;

        $blanks = array(
            'brand' => !(($brand && trim($brand) != '')),
            'serial #' => !(($serialnumber && trim($serialnumber) != '')),
        );

        if (count(array_filter($blanks, function($x) { return !empty($x); })) > 0) {
            $blank = '';
            foreach ($blanks as $key => $val) {
                if ($val == false) {
                    unset($blanks[$key]);
                }

                $keys = array_keys($blanks);

                $blank = implode(' and ',$keys);
            }
            $msg = ucfirst($blank.' '. (count($blanks) >  1 ? 'are' : 'is') .' blank.');
            $title = 'Blank detail submitted.';
            $func = 'warning';
        } else {
            //lookup for edit if id exists.
            if ($id) {
                $inverter_qry = $this->db->select()
                    ->from('application_installation_material_details')
                    ->where(array('sysid' => $id, 'status' => 1))
                    ->get()->row();

                if ($inverter_qry) {
                    $update_arr = array();
                    if ($brand != $inverter_qry->brand) {
                        $update_arr['brand'] = $brand;
                    }

                    if ($serialnumber != $inverter_qry->serialnumber) {
                        $update_arr['serialnumber'] = $serialnumber;
                    }

                    if (count($update_arr) > 0) {
                        $update = update_db($this->db, 'application_installation_material_details', $update_arr, array('sysid' => $id));

                        if ($update->qry) {
                            $branding = $this->select2_brand($brand);
                            $data['brand'] = '<span class="inverter_editable" data-id="select2brand" data-value="' . $brand . '">' . $branding->codes . '</span>';
                            $data['serialnumber'] = '<span class="inverter_editable" data-id="inverter_sn" data-value="' . $serialnumber . '">' . $serialnumber . '</span>';

                            $msg = 'Inverter details has been updated!';
                            $title = 'Updated!';
                            $func = 'success';
                            $qry = true;
                        } else {
                            $msg = 'Failed to update inverter details.';
                            $title = 'FAIL!';
                            $func = 'error';
                        }
                    } else {
                        $msg = 'Details submitted is the same as the existing details.';
                        $title = 'No Update!';
                        $func = 'warning';
                    }
                }
            } else {
                $post = $this->input->post();
                unset($post['id']);
                $new_inverter = insert_db($this->db, 'application_installation_material_details', $post);
                if ($new_inverter->qry) {
                    $branding = $this->select2_brand($brand);

                    $data = array(
                        'newid' => $new_inverter->insert_id,
                        'brand' => '<span class="inverter_editable" data-id="select2brand" data-value="' . $brand . '">' . $branding->codes . '</span>',
                        'serialnumber' => '<span class="inverter_editable" data-id="inverter_sn" data-value="' . $serialnumber . '">' . $serialnumber . '</span>',
                    );

                    $msg = 'Inverter details has been saved!';
                    $title = 'Saved!';
                    $func = 'success';
                    $qry = true;
                } else {
                    $msg = 'Inverter details was not saved.';
                    $title = 'FAIL!';
                    $func = 'error';
                }
            }
        }

        $data['msg'] = $msg;
        $data['title'] = $title;
        $data['func'] = $func;
        $data['qry'] = $qry;

        return json_encode($data);
    }

    function delete_inverter_details() {
        $data = array();
        $id = $this->input->post('id');

        $msg = '';
        $title = '';
        $func = '';
        $qry = false;

        $this->db->trans_begin();
        $remove = update_db($this->db,'application_installation_inverter_details',array('status' => 0),array('sysid' => $id));
        if ($remove->qry) {
            $this->db->trans_commit();
            $msg = 'Inverter\'s details has been removed.';
            $title = 'Done!';
            $func = 'success';
            $qry = true;
        } else {
            $this->db->trans_rollback();
            $msg = 'Inverter details were not removed.';
            $title = 'Fail!';
            $func = 'error';
        }

        $data['msg'] = $msg;
        $data['title'] = $title;
        $data['func'] = $func;
        $data['qry'] = $qry;

        return json_encode($data);
    }

    function finalize_customer_application() {
        $data = array();
        $appid = $this->input->post('appid');
        $this->load->helper('cad');

        $queries = array();

        $msg = '';
        $func = '';
        $qry = false;
        $title = '';

        //FETCH ALL APPLICATION INFORMATION.
        $appinfo = application_info($appid);

        $data['appinfo'] = $appinfo;
        $systemsize = substr($appinfo->systemsizename,0,strpos(strtolower($appinfo->systemsizename),'kwp'));
        $size = (int)filter_var($systemsize,FILTER_SANITIZE_NUMBER_INT);
        $this->db->trans_begin();

        // WRITE SPECIFIC DETAILS ON CUSTOMER TABLE.
        $customer_array = array(
            'sysid' => $appinfo->essrno,
            'appid' => $appid,
            'customertype' => $appinfo->apptype,
            'duid' => $appinfo->duid,
            'durate' => $appinfo->durate,
            'systemtype' => $appinfo->systemtype,
            'systemsizeid' => $appinfo->systemsizeid,
        );

        if ($appinfo->personid && $appinfo->personid > 0) {
            $customer_array['personid'] = $appinfo->personid;
        }

        if ($appinfo->apptype > 1) {
            $qry_corp_app = $this->db->select()
                ->from('application_customers_corporation')
                ->where(array('appid' => $appid, 'types' => $appinfo->apptype))
                ->get()->row();

            if ($qry_corp_app) {
                $customer_array['establishmentid'] = $qry_corp_app->corpid;
                $customer_array['branchid'] = $qry_corp_app->branchid;
            }

        }

        $create_customer = insert_db($this->db,'customer_accounts_main',$customer_array);

        if ($create_customer->qry) {
            $queries['create_customer'] = true;

            //ADD CUSTOMER ADDRESS
            $appdetails = get_application_details($appid);

            if ($appdetails->info) {
                $appdetails = $appdetails->info;

                $addr_arr = array(
                    'acctid' => $appdetails->essrno,
                    'district' => $appdetails->distid,
                    'city' => $appdetails->city,
                    'country' => $appdetails->country,
                    'addrspecific' => $appdetails->addrspec,
                    'geolink' => $appdetails->geolink
                );

                $account_address = insert_db($this->db,'customer_accounts_address',$addr_arr);

                if ($account_address->qry) {
                    $queries['customer_address'] = true;
                } else {
                    $queries['customer_address'] = false;
                }
            }

            //IF PANELTYPE IS SET: UPDATE CUSTOMER SYSTEM GROUP SPTYPEID
            if (isset($appinfo->paneltype) && $appinfo->systemtype == 2) {
                $update_panel = update_db($this->db,'customer_system_group',array('sptypeid' => $appinfo->paneltype),array('sysid' => $appinfo->systemsizeid));

                $billingstart = 0;
                $duedate = 0;
                $billingyear = 0;
                //GET ENERGIZED DATE
                $enegrized_qry = $this->db->select('setdate')
                    ->from('application_installation_dates')
                    ->where(array('appid' => $appid,'type' => 2,'status' => 1))
                    ->get()->row();

                if ($enegrized_qry) {
                    $enegrized = $enegrized_qry->setdate;
                    $appinfo->installdate = $enegrized;
                    list($year,$month,$day) = explode('-',$enegrized);

                    /*if ($day >= 28) {
                        $duedate = 2;
                        $billingstart = $month + 2;
                    } else {
                        $billingstart = $month + 1;
                        if ($day <= 10)  {
                            $duedate = 2;
                        } else {
                            $duedate = 17;
                        }
                    }*/

                    if ($size > 18) {
                        $duedate = $day;
                        $billingstart = $month + 1;
                    } else {
                        if ($day >= 11 && $day <= 27) {
                            $duedate = 17;
                            $billingstart = $month + 1;
                        } else {
                            $duedate = 2;
                            if ($day <= 10) {
                                $billingstart = $month + 1;
                            }

                            if ($day >= 28) {
                                $billingstart = $month + 2;
                            }
                        }
                    }
                }

                if ($billingstart > 12) {
                    $billingstart = $billingstart - 12;
                    $year = $year + 1;
                }

                $appinfo->billfrequency = $duedate;
                $appinfo->billingyear = $year;
                $appinfo->billingstart = $billingstart;

                //CREATE BILLING SEQUENCE IF NOT AVAILABLE.
                $customer_plan_details = $this->db->select()
                    ->from('customer_plan_details')
                    ->where(array('appid' => $appid,'status !=' => 0))
                    ->get()->row();

                if ($customer_plan_details) {
                    if ($customer_plan_details->standard) {
                        $plan_qry = $this->db->select()
                            ->from('customer_standard_system_rates')
                            ->where(array('sysid' => $customer_plan_details->rateid))
                            ->get()->row();
                    } else {
                        $plan_qry = $this->db->select()
                            ->from('customer_nonstandard_system_rates')
                            ->where(array('appid' => $appid, 'status' => 1))
                            ->get()->row();
                    }

                    $plan = $plan_qry;
                    $monthlyamt = $plan->monthlyamt;
                    $appinfo->years = $plan->years;

                    if ($plan->years > 0) {
                        $billing_arr = array(
                            'appid' => $appid,
                            'essr' => $appinfo->essrno,
                            'installdate' => $appinfo->installdate,
                            'billfrequency' => $appinfo->billfrequency,
                            'planid' => $appinfo->sysid,
                        );
                    }
                }

                if (isset($billing_arr)) {
                    $billing_create = insert_db($this->db, 'customer_billing_group', $billing_arr);

                    if ($billing_create->qry) {
                        $queries['billing_create'] = true;
                        $bills = 0;
                        $billingid = $billing_create->insert_id;
                        $month = $appinfo->billingstart;
                        $year = $appinfo->billingyear;
                        $months = $appinfo->years * 12;
                        $this->db->trans_begin();
                        for ($billno = 0; $billno < $months; $billno++) {
                            $bill_arr = array(
                                'groupid' => $billingid,
                                'billno' => $billno + 1,
                                'years' => $year,
                                'months' => $month,
                                'duedate' => date('Y-m-d', strtotime($year . '-' . str_pad($month, 1, '0') . '-' . str_pad($billing->billfrequency, 1, '0'))),
                                'amount' => $monthlyamt,
                                'createdby' => user_id()
                            );

                            if (insert_db($this->db,'customer_billing_trn', $bill_arr)) {
                                $bills += 1;
                            }
                            $month++;
                            if ($month > 12) {
                                $month = 1;
                                $year += 1;
                            }
                        }

                        if ($bills == $months) {
                            $queries['bills_create'] = true;
                        } else {
                            $queries['bills_create'] = false;
                        }
                    }
                }
            }
        } else {
            $queries['create_customer'] = false;
        }

        //OPEN DIRECTORY FOR MOUNTING
        $account = 'PAE'.str_pad($appinfo->essrno,6,'0',STR_PAD_LEFT);
        $file_directory = 'uploads/attachments/customers/'.$account.'/Docs/';

        if (!is_dir($file_directory)) {
            mkdir($file_directory, 0777, TRUE);
            chmod($file_directory, 0777);
        } else {
            chmod($file_directory, 0777);
        }

        //CONVERT ALL HTML DOCUMENTS INTO PDF.
        //CHECK TSSR
        $tssr_qry = $this->db->select()
            ->from('application_customers_system_size')
            ->where(array('appid' => $appid, 'status' => 305))
            ->get()->row();

        if ($tssr_qry) {
            $tssr = get_tssr_layout($appid);

            //$hashed = rehash_pdf_img($tssr->html);
            $customPaper = array(0, 0, 615, 930);
            $type = get_types_name(3436);
            $filename = $account.'_'.$type->names.'.pdf';

            $this->load->library('pdf');
            $dompdf = new Dompdf\Dompdf();
            $dompdf->loadHtml($tssr->html);
            $dompdf->setPaper('letter', $customPaper);
            $dompdf->render();
            // Add PDF Document Information
            $dompdf->add_info('Subject', $type->names);
            $dompdf->add_info('Author', user_info()->username);
            $dompdf->add_info('Creator', 'PAE');
            $dompdf->add_info('Keywords', '');
            $content = $dompdf->output();

            $flatten = file_put_contents(FCPATH.$file_directory.$filename, $content);

            if ($flatten !== false) {
                $doc_ins = array(
                    'acctid' => $appinfo->essrno,
                    'doctype' => 'app',
                    'typesid' => 3433,
                    'location' => $file_directory . $filename
                );
                $app_file = insert_db($this->db,'customer_accounts_docs',$doc_ins);

                if ($app_file->qry) {
                    $queries['docs_TSSR'] = true;
                } else {
                    $queries['docs_TSSR'] = false;
                }
            }
        }

        $docs_query = $this->db->select()
            ->from('prime_documents_main')
            ->where(array('dataid' => $appid,'status' => 1))
            ->get();

        if ($docs_query->num_rows() > 0) {
            foreach ($docs_query->result() AS $document) {
                $hashed = rehash_pdf_img($document->html);
                $customPaper = array(0, 0, 615, 930);
                $type = get_types_name($document->doctype);
                $filename = $account.'_'.$type->names.'.pdf';

                $this->load->library('pdf');
                $dompdf = new Dompdf\Dompdf();
                $dompdf->loadHtml($hashed);
                $dompdf->setPaper('letter', $customPaper);
                $dompdf->render();
                // Add PDF Document Information
                $dompdf->add_info('Subject', $type->names);
                $dompdf->add_info('Author', user_info()->username);
                $dompdf->add_info('Creator', 'PAE');
                $dompdf->add_info('Keywords', '');
                $content = $dompdf->output();

                $flatten = file_put_contents(FCPATH.$file_directory.$filename, $content);

                if ($flatten !== false) {
                    $doc_ins = array(
                        'acctid' => $appinfo->essrno,
                        'doctype' => 'app',
                        'typesid' => $document->doctype,
                        'location' => $file_directory . $filename
                    );
                    $app_file = insert_db($this->db,'customer_accounts_docs',$doc_ins);

                    if ($app_file->qry) {
                        $queries['docs_'.$type->names] = true;
                    } else {
                        $queries['docs_'.$type->names] = false;
                    }
                }
            }
        }

        $requirements = $this->db->select()
            ->from('prime_requirement_parameters')
            ->where('status',1)
            ->get();

        if ($requirements->num_rows() > 0) {
            $file_directory .= 'Requirements/';
            if (!is_dir($file_directory)) {
                mkdir($file_directory, 0777, TRUE);
                chmod($file_directory, 0777);
            } else {
                chmod($file_directory, 0777);
            }
            foreach ($requirements->result() AS $req) {
                $name = $req->shortname;
                $filename = $account.' - '.$name;
                $req_qry = $this->db->select('c.fileurl')
                    ->from('application_customers_requirements AS r')
                    ->join('application_customers_attachments AS c','c.attachmentid = r.sysid','left')
                    ->where(array('r.appid' => $appid,'r.reqid' => $req->sysid,'r.status' => 1))
                    ->order_by('c.fileurl ASC')
                    ->get();

                $complyCnt = $req_qry->num_rows();

                if ($complyCnt > 0) {
                    if ($complyCnt > 1) {
                        $this->load->library('fpdf');
                        $pdf = new FPDF();

                        foreach ($req_qry->result() as $img) {
                            $image = FCPATH.$img->fileurl;
                            if (!file_exists($image)) {
                                continue; // Skip if image file does not exist
                            }
                            list($width, $height, $type, $attr) = getimagesize($image);
                            $pdf->SetSize(($width / 2) + 10, ($height * 50 / 100)); //Custom function
                            $pdf->AddPage('', 'custom');
                            $pdf->Image($image, 0, 0, $width * 18 / 100, $height * 18 / 100);
                            $pdf->SetAutoPageBreak(true);
                        }

                        $flatten = $pdf->output(FCPATH.$file_directory.$filename.'.pdf','F',true);

                        if ($flatten !== false) {
                            $doc_ins = array(
                                'acctid' => $appinfo->essrno,
                                'doctype' => 'sup',
                                'typesid' => $req->sysid,
                                'location' => $file_directory . $filename . '.pdf'
                            );
                            $app_file = insert_db($this->db,'customer_accounts_docs',$doc_ins);

                            if ($app_file->qry) {
                                $queries['sup_'.$name] = true;
                            } else {
                                $queries['sup_'.$name] = false;
                            }
                        } else {
                            $queries['sup_'.$name] = false;
                        }
                    }/* else {
                        $file_row = $req_qry->row();
                        //$explode = preg_split('~[\\\\/]~', $file_row->fileurl);
                        //$filename =  end($explode);
                        $file_info = pathinfo(FCPATH.$file_row->fileurl);
                        $move = copy(FCPATH.$file_row->fileurl,FCPATH.$file_directory.$filename . '.' . $file_info['extension']);

                        if ($move !== false) {
                            $queries['move_sup_'.$name] = true;
                            $doc_ins = array(
                                'acctid' => $appinfo->essrno,
                                'doctype' => 'sup',
                                'typesid' => $req->sysid,
                                'location' => $file_directory . $filename . '.' . $file_info['extension']
                            );

                            $app_file = insert_db($this->db,'customer_accounts_docs',$doc_ins);
                            if ($app_file->qry) {
                                $queries['sup_'.$name] = true;
                            } else {
                                $queries['sup_'.$name] = false;
                            }
                        } else {
                            $queries['move_sup_'.$name] = false;
                        }
                    }*/
                }
            }
        }

        if (!in_array(false,$queries)) {
            $finalize = update_db($this->db,'application_customers_details',array('status' => 308),array('sysid' => $appid));
            if ($finalize->qry) {
                $queries['finalize'] = true;
                $audit_arr = array(
                    'dataid' => $appinfo->essrno,
                    'valueold' => 'AppID : ' . $appid,
                    'valuenew' => 'Account : ' . $account
                );

                $audit = audit_insert($audit_arr);
                if ($audit) {
                    $queries['audit_trail'] = true;
                    $this->db->trans_commit();
                    $msg = 'Customer account has been created!';
                    $func = 'success';
                    $qry = true;
                    $title = 'Account Created!';
                } else {
                    $queries['audit_trail'] = false;
                    $this->db->trans_rollback();
                    $msg = 'Failed to log customer creation!';
                    $func = 'error';
                    $title = 'Account creation FAILED!';
                }
            } else {
                $queries['finalize'] = false;
                $this->db->trans_rollback();
                $msg = 'Failed to finalize customer application!';
                $func = 'error';
                $title = 'Account creation FAILED!';
            }
        } else {
            $this->db->trans_rollback();
            $msg = 'There was an error during account creation!';
            $func = 'error';
            $title = 'Account creation FAILED!';
        }

        $data['queries'] = $queries;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['qry'] = $qry;
        $data['title'] = $title;

        return json_encode($data);
    }


    public function add_inverter_details() {
        return json_encode(array('status' => 'success', 'message' => 'Inverter details added successfully.'));
    }

    public function get_items() {
        $search = $this->input->get('term');
        $page = $this->input->get('page') ?: 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $this->db->select('sysid as id, fulldescription AS text');
        $this->db->from('items_main_description');
        $this->db->where('status', 1);

        if ($search) {
            $this->db->like('fulldescription', $search);
        }

        // Clone the query builder object to get the total count
        $count_db = clone $this->db;
        $total_count = $count_db->count_all_results();

        $this->db->limit($limit, $offset);
        $items_qry = $this->db->get();

        $items = [];
        if ($items_qry->num_rows() > 0) {
            $items = $items_qry->result_array();
        }

        $more = ($page * $limit) < $total_count;

        $data = [
            'results' => $items,
            'pagination' => [
                'more' => $more
            ]
        ];

        return json_encode($data);
    }
}