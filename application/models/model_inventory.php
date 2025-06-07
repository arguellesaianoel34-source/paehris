<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_inventory extends CI_Model
{
    function tbl_get_data_initialization() {
        $codes = $this->input->post('codes');
        $sql = $this->db->select('sysid, names, desc')
            ->from('prime_types_parameter')
            ->where('codes', $codes)
            ->get();
        $data = array();
        if($sql->num_rows()>0) {
            foreach($sql->result() as $row) {
                $data['list'][] = array(
                    'expand' => $row->sysid,
                    'codes' => $row->names,
                    'descs' => $row->desc,
                    'ownership' => '',
                    'status' => '',
                    'control' => '',
                );
            }
        }

        return json_encode($data);
    }

    function data_add_initialization() {
        $input = $this->input->post();
        $table = $this->input->post('table');

        unset($input['table']);
        $this->db->trans_begin();
        $this->db->insert('prime_types_parameter', $input);
        $data = db_trans($this->db);
        $data['table'] = $table;
        return json_encode($data);
    }

    function tbl_get_products()
    {
        $data = array();
        $sql = $this->db->query("SELECT
                                    ip.sysid, 
                                    ii.descs, 
                                    tp.`desc` AS brand, 
                                    ip.remarks
                                FROM inventory_items AS ii
                                    LEFT JOIN inventory_products AS ip ON ii.sysid = ip.itemid
                                    LEFT JOIN inventory_brands AS ib ON ip.sysid = ib.prodid
                                    LEFT JOIN prime_types_parameter AS tp ON ib.typesid = tp.sysid
                                    WHERE ip.`status` = 1
                            ");
        if($sql->num_rows()>0) {
            foreach($sql->result() as $row) {
                $control = '';
                $control .= '<a class="btn btn-xs btn-danger inline" href="javascript:;" id="btn_delete"><i class="fa fa-times"></i></a>';
                $control .= '<a class="btn btn-xs btn-info inline" href="javascript:;" id=""><i class="fa fa-search"></i></a>';
                $data['list'][] = array(
                    'expand' => $row->sysid,
                    'product' => $row->descs,
                    'brand' => $row->brand,
                    'remarks' => $row->remarks,
                    'status' => 'Active',
                    'control' => $control
                );
            }
        }
        return json_encode($data);
    }

    function tbl_get_stocks() {
        $data = array();


        $sql_stocks = $this->db->query("
            SELECT
                ims.serials,
                ims.names AS `desc`,
                supp.descs AS supplier,
                sa.address,
                ism.itemid,
                ism.brandid,
                ism.qty,
                ism.price,
                ism.purchasedate,
                ism.sysid,
                pu.unit_code AS `units`
            FROM
                inventory_stocks_main AS ism
                LEFT JOIN items_main_spec AS ims ON ims.sysid = ism.itemid
                LEFT JOIN inventory_suppliers AS supp ON ism.suppid = supp.sysid
                LEFT JOIN inventory_suppliers_address AS sa ON sa.supplierid = supp.sysid
                LEFT JOIN prime_unit AS pu ON pu.sysid = ims.unitid
                WHERE ism.`status` = 1
                ORDER BY ism.datecreated DESC
        ");
        if($sql_stocks->num_rows()>0) {
            foreach($sql_stocks->result() as $row) {

                $re_order_point = 0.5;

                $sysid = $row->sysid;

                $brand_sql = get_types_name($row->brandid);
                $brand = ($brand_sql) ? $brand_sql->names : 'Unknown';

                $control = '';
                $control .= '<a href="#frm_inventory_edit_stocks" data-arr="'.$row->sysid.'" title="Edit Stocks" data-toggle="ajax-modal" class="btn btn-default inline btn-xs"><i class="fa fa-edit"></i></a>';
                $control .= '<a href="#frm_inventory_view_stocks" data-arr="'.$row->sysid.'" title="View Stocks ('.$sysid.' - '.$row->desc.' - ' . $brand. ')" data-toggle="ajax-modal" class="btn btn-primary inline btn-xs"><i class="fa fa-search"></i></a>';
                $control .= '<a href="#frm_inventory_request_stocks" data-arr="'.$row->sysid.'" title="Request Stocks" data-toggle="ajax-modal" class="btn btn-success inline btn-xs"><i class="fa fa-download"></i></a>';
                $control .= '<a href="#frm_inventory_stocks_out" data-arr="'.$row->sysid.'" title="Release Stocks" data-toggle="ajax-modal" class="btn btn-danger inline btn-xs"><i class="fa fa-sign-out"></i></a>';

                $serials = $row->serials;
                if(empty($row->serials)) {
                    $serials = '<i class="fa fa-refresh"></i> Generate';
                }

                // query stocks returned
                $sql_stocks_return = $this->db->query("SELECT SUM(qty) AS qty FROM inventory_stocks_return 
                                                    WHERE stockid = {$sysid} AND `status` = 1")->row();
                $return = ($sql_stocks_return) ? $sql_stocks_return->qty : 0;

                // query stocks out
                $sql_stocks_out = $this->db->query("SELECT SUM(qty) AS qty FROM inventory_stocks_out 
                                                    WHERE stockid = {$sysid} AND `status` = 1")->row();
                $released = ($sql_stocks_out) ? $sql_stocks_out->qty : 0;


                $qty = $row->qty;
                $onhand = (($qty - $released) + $return);
                $see_release = '';

                $row_bg = '';
                $status = '<span class="text-success"><i class="fa fa-check"></i> Sufficient</span>';
                $check_reorder_point = ($onhand / $qty);

                if($check_reorder_point <= $re_order_point) {
                    $status = '<span class="text-danger"><i class="fa fa-warning"></i> Reorder</span>';
                    $row_bg = 'row-danger';
                }

                if($released>0) {
                    $see_release = '<a title="Released List" href="#inventory_released_list" data-toggle="ajax-modal" class="btn btn-default btn-xs inline pull-left"><i class="fa fa-search"></i></a>';
                }

                $requested = ''; // QUERY FROM CAD

                $supplier = '';
                $supplier .= $row->supplier. '<br>';
                $supplier .= '<small class="font-red-flamingo">'.$row->address.'</small>';


                $product_image = '';
                $product_image .= '<img width=\'300px\' src=\''.base_url('uploads/attachments/inventory/products/00001/trina.jpg').'\'/>';

                $product = '';
                $product .= '<a  href="#" data-toggle="popover" data-trigger="hover" data-content="'.$product_image.'" class="popovers pull-right"><i class="fa fa-image"></i></a>';
                $product .= $row->desc;



                $data['list'][] = array(
                    'expand' => $sysid,
                    'stockid' => $sysid,
                    'serial' =>  $serials,
                    'storage' =>  'Main',
                    'supplier' => $supplier,
                    'product' => $product,
                    'brand' => $brand,
                    'qty' => $row->qty,
                    'requested' => $requested,
                    'released' => $see_release . $released,
                    'return' => $return,
                    'onhand' => $onhand,
                    'price' => number_format($row->price, 2),
                    'unit' => strtoupper($row->units),
                    'purchasedate' => $row->purchasedate,
                    'status' => $status,
                    'control' => $control,
                    'rowbg' => $row_bg
                );
            }
        }
        return json_encode($data);
    }

    function tbl_get_stock_in() {
        $data = array();
        $stockid = $this->input->post('stockid');
        $status = $this->input->post('status');


        $status_where = ($status) ? " AND status = $status " : " AND status > 0";
        $sql = $this->db->query("SELECT * FROM inventory_stocks_items WHERE stockid = $stockid $status_where");

        if($sql->num_rows()>0) {
            foreach($sql->result() as $row) {
                $controls = '';
                $controls .= '<a href="javascript:;" data-id="" class="btn btn-danger btn-xs inline" id="del_stock_in_item"><i class="fa fa-times"></i></a>';
                $data['list'][] = array(
                    'num' => $row->sysid,
                    'serials' => $row->serials,
                    'date' => $row->datecreated,
                    'status' => get_types_label_format($row->status),
                    'control' => $controls,
                );
            }
        }

        return json_encode($data);
    }

    function draft_stock_in() {
        $stockid = $this->input->post('stockid');
        $serials = $this->input->post('serials');


        $this->db->trans_begin();
        $ins_arr = array(
            'stockid' => $stockid,
            'serials' => $serials,
            'createdby' => user_id(),
            'updatedby' => user_id(),
            'status' => 307
        );
        $this->db->insert('inventory_stocks_items', $ins_arr);
        return json_encode(db_trans($this->db, false, false, false));
    }

    function save_stock_in() {
        $stockid = $this->input->post('stockid');

        $this->db->trans_begin();
        $this->db->update('inventory_stocks_items',
            array(
                'status' => 304,
                'updatedby' => user_id(),
            ),
            array(
                'stockid' => $stockid
            )
        );
        return json_encode(db_trans($this->db));
    }


    function query_stock_out() {
        $data = array();
        $save = $this->input->post('save');
        $input_return = $this->input->post('return');
        $codes = $this->input->post('codes');
        $save_qty = $this->input->post('qty');
        $row = $this->db->query("SELECT
                                    ism.sysid,
                                    ism.itemid,
                                    ims.`names`,
                                    isi.serials,
                                    ism.qty 
                                FROM
                                    inventory_stocks_items AS isi
                                    INNER JOIN inventory_stocks_main AS ism ON isi.stockid = ism.sysid
                                    INNER JOIN items_main_spec AS ims ON ism.itemid = ims.sysid 
                                WHERE
                                    isi.serials = '$codes'
                                    ")->row();
        $onhand = 0;
        if($row) {
            // query stocks out
            $sql_stocks_out = $this->db->query("SELECT SUM(qty) AS qty FROM inventory_stocks_out 
                                                    WHERE stockid = {$row->sysid} 
                                                    AND `status` = 1")->row();
            $released = ($sql_stocks_out) ? $sql_stocks_out->qty : 0;


            // query stocks returned
            $sql_stocks_return = $this->db->query("SELECT SUM(qty) AS qty FROM inventory_stocks_return 
                                                    WHERE stockid = {$row->sysid} AND `status` = 1")->row();
            $return = ($sql_stocks_return) ? $sql_stocks_return->qty : 0;

            $qty = $row->qty;
            $onhand = (($qty - $released) + $return);

            // save
            if($save) {
                $this->db->insert('inventory_stocks_out',
                    array(
                        'stockid' => $row->sysid,
                        'itemid' => $row->itemid,
                        'qty' => $save_qty
                    )
                );

                $data['title'] = 'PAE Inventory';
                $data['func'] = 'success';
                $data['msg'] = 'Stock out save!';
            }

            // return
            if($input_return) {
                $this->db->insert('inventory_stocks_return',
                    array(
                        'stockid' => $row->sysid,
                        'itemid' => $row->itemid,
                        'qty' => $save_qty
                    )
                );

                $data['title'] = 'PAE Inventory';
                $data['func'] = 'success';
                $data['msg'] = 'Stock returned save!';
            }
        } else {
            $data['msg'] = 'Not found!';
        }
        $data['qry'] = ($row) ? true : false;
        $data['qty'] = $onhand;
        $data['desc'] = ($row) ? $row->names : '';
        return json_encode($data);
    }

    function add_stocks() {
        $data = array();
        $itemid = $this->input->post('itemid');
        $supplierid = $this->input->post('supplierid');
        $qty = $this->input->post('qty');
        $brand = $this->input->post('brand');
        $price = $this->input->post('price');
        $date = $this->input->post('date');
        $this->db->trans_begin();
        $ins_arr = array(
            'itemid' => $itemid,
            'brandid' => $brand,
            'suppid' => $supplierid,
            'qty' => $qty,
            'price' => $price,
            'purchasedate' => $date,
            'createdby' => user_id(),
            'updatedby' => user_id()
        );
        $this->db->insert('inventory_stocks_main', $ins_arr);
        return json_encode(db_trans($this->db));
    }

    function tbl_products() {
        $data = array();
        $sql = $this->db->query("
             SELECT
                ims.serials,
                ims.names AS `desc`,
                supp.descs AS supplier,
                ism.itemid,
                ism.brandid,
                ism.qty,
                ism.sysid 
            FROM
                inventory_stocks_main AS ism
                INNER JOIN items_main_spec AS ims ON ims.sysid = ism.itemid
                INNER JOIN inventory_suppliers AS supp ON ism.suppid = supp.sysid
                WHERE ism.`status` = 1
				GROUP BY 
                ims.names,
                supp.descs
        ");
        if($sql->num_rows()>0) {
            foreach($sql->result() as $row) {


                // query stocks out
                $sql_stocks_out = $this->db->query("SELECT SUM(qty) AS qty FROM inventory_stocks_out 
                                                    WHERE stockid = {$row->sysid} 
                                                    AND `status` = 1")->row();
                $released = ($sql_stocks_out) ? $sql_stocks_out->qty : 0;


                // query stocks returned
                $sql_stocks_return = $this->db->query("SELECT SUM(qty) AS qty FROM inventory_stocks_return 
                                                    WHERE stockid = {$row->sysid} AND `status` = 1")->row();
                $return = ($sql_stocks_return) ? $sql_stocks_return->qty : 0;

                $qty = $row->qty;
                $onhand = (($qty - $released) + $return);

                $brand_sql = get_types_name($row->brandid);
                $brand = ($brand_sql) ? $brand_sql->names : 'Unknown';
                $data['list'][] = array(
                    'num' => $row->sysid,
                    'supplier' => $row->supplier,
                    'product' => $row->desc,
                    'brand' => $brand,
                    'qty' => $onhand,
                    'control' => ''
                );

            }
        }

        return json_encode($data);

    }

    function tbl_get_suppliers() {
        $data = array();
        $sql = $this->db->query("
                SELECT
                    supp.sysid,
                    supp.descs,
                    sa.address 
                FROM
                    inventory_suppliers AS supp
                    LEFT JOIN inventory_suppliers_address AS sa ON sa.supplierid = supp.sysid 
                WHERE
                    supp.`STATUS` = 1
            ");
        if($sql->num_rows() > 0) {
            foreach($sql->result() as $row) {
                $telephone = $this->get_supplier_contact($row->sysid, 1050);
                $cellphone = $this->get_supplier_contact($row->sysid, 1051);
                $email = $this->get_supplier_contact($row->sysid, 1053);
                $data['list'][] = array(
                    'num' => $row->sysid,
                    'name' => $row->descs,
                    'address' => $row->address,
                    'telephone' => $telephone,
                    'cellphone' => $cellphone,
                    'email' => $email,
                    'control' => ''
                );
            }
        }
        return json_encode($data);
    }

    function get_supplier_contact($suppid, $typesid) {
        $sql = $this->db->query("SELECT * FROM inventory_suppliers_contact WHERE supplierid = $suppid AND typesid = $typesid AND status = 1 ORDER BY sysid DESC")->row();
        return ($sql) ? $sql->contact : '';
    }


    function stock_details() {
        $data = array();


        $html = '';



        $html .= '<div class="row margin-bottom-5">';



        $html .= '<div class="col-md-3">';
        $html .= '<ul class="list-group summary column no-border list-group-sm">';
        $html .= '<li class="list-group-item">';
        $html .= '<span class="col-md-4 label-name">Last Update</span>';
        $html .= '<span class="col-md-8 label-default">'.date('Y-m-d').'</span>';
        $html .= '</li>';
        $html .= '<li class="list-group-item">';
        $html .= '<span class="col-md-4 label-name">Updated By</span>';
        $html .= '<span class="col-md-8 label-default">'.get_users_info(1)->username.'</span>';
        $html .= '</li>';
        $html .= '</ul>';
        $html .= '</div>';

        $html .= '<div class="col-md-3">';
        $html .= '<ul class="list-group summary column no-border list-group-sm">';
        $html .= '<li class="list-group-item">';
        $html .= '<span class="col-md-4 label-name">Verification</span>';
        $html .= '<span class="col-md-8 label-default"></span>';
        $html .= '</li>';
        $html .= '<li class="list-group-item">';
        $html .= '<span class="col-md-4 label-name">Verified</span>';
        $html .= '<span class="col-md-8 label-default"></span>';
        $html .= '</li>';
        $html .= '</ul>';
        $html .= '</div>';



        $data['html'] = $html;
        return json_encode($data);
    }

    function generate_barcode($stockid = false, $codestart = false, $codecount = false) {
        $data = array();
        $html = '';
        $msg = '';
        $stockid = $this->input->post('stockid');
        $codestart = $this->input->post('codestart');
        $codecount = $this->input->post('codecount');
        $type = $this->input->post('type');




        $sql_stocks = $this->db->query("
            SELECT
                ims.serials,
                ims.names AS `desc`,
                supp.descs AS supplier,
                sa.address,
                ism.itemid,
                ism.brandid,
                ism.qty,
                ism.price,
                ism.purchasedate,
                ism.sysid,
                pu.unit_code AS `units`
            FROM
                inventory_stocks_main AS ism
                LEFT JOIN items_main_spec AS ims ON ims.sysid = ism.itemid
                LEFT JOIN inventory_suppliers AS supp ON ism.suppid = supp.sysid
                LEFT JOIN inventory_suppliers_address AS sa ON sa.supplierid = supp.sysid
                LEFT JOIN prime_unit AS pu ON pu.sysid = ims.unitid
                WHERE ism.`sysid` = $stockid
                ORDER BY ism.datecreated DESC
        ")->row();
        $item_code = 'Unknown';
        $item_desc = ($sql_stocks) ? $sql_stocks->desc: false;
        $item_supp = ($sql_stocks) ? $sql_stocks->supplier : '';
        if($item_desc) {
            $item_code = $item_desc . $item_supp;
        } else {
            if($item_supp != '') {
                $item_code = $item_supp;
            }
        }

        // check if existing table
        $sql_codes = $this->db->query("SELECT * FROM inventory_serialcodes WHERE stockid = $stockid AND status = 1");


        if($type == 1) {
            $html .= '<div class="row">';
            if($sql_codes->num_rows()>0) {
                foreach($sql_codes->result() as $row) {
                    $html .= '<div class="text-align-center" style="position: relative; display: inline-block; width: 25%; margin-bottom: 15px;">';
                    $html .= '<span style="font-size: 9px;">'.$row->descs . '</span><br>';
                    $html .= '<img alt="' . $row->serialcode . '" style="min-height: 60px;" src="' . base_url() . 'barcode.php?text=' . $row->serialcode . '" /><br>';
                    $html .= '<span style="font-size: 20px;">PAE '.$row->serialcode.'</span>';
                    $html .= '</div>';
                }
            }else {
                if ($codecount && $codecount > 0) {
                    for ($i = 1; $i <= $codecount; $i++) {
                        $code = str_pad(($codestart + $i), 8, '0', STR_PAD_LEFT);
                        $html .= '<div class="text-align-center" style="position: relative; display: inline-block; width: 25%; margin-bottom: 15px;">';
                        $html .= '<span style="font-size: 9px;">'.$item_code . '</span><br>';
                        $html .= '<img alt="' . $code . '" style="min-height: 60px;" src="' . base_url() . 'barcode.php?text=' . $code . '" /><br>';
                        $html .= '<span style="font-size: 20px;">PAE '.$code.'</span>';
                        $html .= '</div>';
                    }
                }
            }
            $html .= '</div>';
            $msg = '<h3><i class="fa fa-print text-success"></i>Printing...</h3>';
        } else {
            if ($codecount && $codecount > 0) {
                $this->db->update('inventory_serialcodes', array('status' => 0), array('stockid' => $stockid));
                for ($i = 1; $i <= $codecount; $i++) {
                    $code = str_pad(($codestart + $i), 8, '0', STR_PAD_LEFT);
                    $sql_chk_codes = $this->db->query("SELECT * FROM inventory_serialcodes WHERE stockid = $stockid AND status = 1 AND serialcode = '$code'")->row();
                    if($sql_chk_codes == false) {
                        $this->db->insert('inventory_serialcodes', array(
                            'stockid' => $stockid,
                            'serialcode' => $code,
                            'descs' => $item_code
                        ));
                    }
                }
                $msg = '<h3><i class="fa fa-check text-success"></i>Serial codes table has been updated!</h3>';
            }else{
                $msg = '<h3><i class="fa fa-times text-warning"></i>Please review series.</h3>';
            }
        }

        $data['msg'] = $msg;
        $data['html'] = $html;
        return json_encode($data);

    }

    function set_barcode($code)
    {
        //generate barcode
        return Zend_Barcode::render('code128', 'image', array('text'=>$code), array());
    }

    function tbl_stock_list() {
        $data = array();
        $columns = array(
            dt_column_array('num','#','number',false),
            dt_column_array('id','ID','text-align-center',false),
            dt_column_array('item','Item',false,'30%'),
            dt_column_array('purchased','Purchased','number',false),
            dt_column_array('allocated','Allocated','number',false),
            dt_column_array('onhand','On Hand','number',false),
            dt_column_array('unit','Unit',false,'5%'),
            dt_column_array('lastpurchase','Last Purchase','text-align-center',false),
            dt_column_array('status','Status','text-align-center','5%'),
            dt_column_array('control','Actions','text-align-center',false),
        );

        $get_stock_list = $this->db->select('sysid, fulldescription, unitid')
            ->from('items_main_description')
            ->where('status',1)->get();

        if ($get_stock_list->num_rows() > 0) {
            $n = 1;
            foreach ($get_stock_list->result() AS $item) {
                $control = '';
                //$control .= '<a href="#frm_inventory_edit_stocks" data-arr="'.$item->sysid.'" title="Edit Stock (Pange is still Under Construction)" data-toggle="ajax-modal" class="btn btn-default inline btn-xs"><i class="fa fa-edit"></i></a>';
                $control .= '<a href="#frm_inventory_view_stocks" data-arr="'.$item->sysid.'" title="View Stock ('.$item->sysid.' - '.$item->fulldescription.')" data-toggle="ajax-modal" class="btn btn-primary inline btn-xs"><i class="fa fa-search"></i></a>';
                //$control .= '<a href="#frm_inventory_request_stocks" data-arr="'.$item->sysid.'" title="Request Stocks" data-toggle="ajax-modal" class="btn btn-success inline btn-xs"><i class="fa fa-download"></i></a>';
                //$control .= '<a href="#frm_inventory_stocks_out" data-arr="'.$item->sysid.'" title="Release Stocks" data-toggle="ajax-modal" class="btn btn-danger inline btn-xs"><i class="fa fa-sign-out"></i></a>';
                $unit = unit_query($item->unitid);


                //Query received POs.
                $item_po = $this->db->select('itr.referenceid')
                    ->from('inventory_transaction_reference AS itr')
                    ->join('inventory_transaction_items AS iti','iti.referenceid = itr.sysid AND iti.status != 0','left')
                    ->join('inventory_transaction_group AS itg','itr.groupid = itg.sysid','left')
                    ->where(array('iti.itemid' => $item->sysid,'itr.referenceid IS NOT NULL' => null,'itr.status' => 301,'itg.trntype' => 24))
                    ->get();

                $po_arr = array();
                if ($item_po->num_rows() > 0) {
                    $po_arr = array_column((array)$item_po->result(),'referenceid');
                    //reference = PAE-08012024-001,PAE-08012024-002
                    $this->db->where_not_in('po.ponumber',$po_arr);
                }

                //Query qty of item in all approved PO that has not been received.
                $purchased_cnt = $this->db->select('po.prfid,eqs.supplierid,eti.qty')
                    ->from('eprs_po AS po')
                    ->join('eprs_po_details AS pod','pod.poid = po.sysid','left')
                    ->join('eprs_quotation_suppliers AS eqs','eqs.sysid = pod.quotationid AND eqs.`status` = 301','left')
                    ->join('eprs_quotation_details AS eqd','eqs.sysid = eqd.quotationid AND eqd.`status` = 301','left')
                    ->join('eprs_transaction_items AS eti','eqd.prfitemid = eti.sysid','inner')
                    ->join('items_main_description AS item','eti.itemid = item.sysid','left')
                    ->where(array('item.sysid' => $item->sysid,'po.ponumber IS NOT NULL'=>null,'po.status' => 1))
                    ->get();

                $purchased_po = 0;
                if ($purchased_cnt->num_rows() > 0) {
                    foreach ($purchased_cnt->result() AS $purchased) {
                        $purchased_po += $purchased->qty;
                    }
                }

                //ALLOCATED: CHECKED-OUT ITEMS NOT YET APPROVED
                //GET UNAPPROVED INSTALLATION TRN
                $installation_qry = $this->db->select('SUM(ins.qty) AS qty')
                    ->from('installation_item_list AS ins')
                    ->join('inventory_transaction_reference AS itr','itr.referenceid = ins.appid','left')
                    ->where(array('ins.itemid' => $item->sysid,'itr.referenceid IS NOT NULL' => null,'itr.status' => 300))
                    ->get()->row();

                $allocated = 0;
                if ($installation_qry) {
                    $inventory_qry = $this->db->select('
                        iti.sysid,
                        MAX(CASE WHEN iti.type = 22 THEN iti.qty END) AS qty,
                        MAX(CASE WHEN iti.type = 21 THEN iti.qty END) AS returned,
                        MAX(CASE WHEN iti.type = 25 THEN iti.qty END) AS additional
                        ')
                        ->from('inventory_transaction_items AS iti')
                        ->join('inventory_transaction_reference AS itr', 'iti.referenceid = itr.sysid', 'left')
                        ->join('inventory_transaction_group AS itg', 'itg.sysid = itr.groupid', 'left')
                        ->where(array('iti.itemid' => $item->sysid,'iti.status' => 300))
                        ->group_by('iti.itemid')
                        ->get()->row();

                    $allocated = $installation_qry->qty + ($inventory_qry ? $inventory_qry->additional - $inventory_qry->returned : 0);
                }

                //echo $this->db->last_query();

                // QUERY ALL TRANSACTIONS 301 IF RR AND 1 FOR CHECK-IN/OUT
                // ADD QTY IF RR AND CHECK-IN, DEDUCT IF CHECK-OUT
                $onhand_qry = $this->db->select('qty,itemtype')
                    ->from('inventory_items_summary')
                    ->where(array('itemid' => $item->sysid,'status' => 1))
                    ->get();

                $onhand = 0;
                if ($onhand_qry->num_rows() > 0) {
                    foreach ($onhand_qry->result() AS $r_hand) {
                        if ($r_hand->itemtype == 21) {
                            $onhand += $r_hand->qty;
                            $purchased_po -= $r_hand->qty;
                        }
                        if ($r_hand->itemtype == 22) {
                            $onhand -= $r_hand->qty;
                        }

                    }
                }

                //LAST PURCHASE
                $lastbuy_qry = $this->db->select('qd.dateupdated')
                    ->from('eprs_transaction_items AS ti')
                    ->join('eprs_quotation_details AS qd','ti.sysid = qd.prfitemid','left')
                    ->where(array('ti.itemid' => $item->sysid,'qd.status' => 301))
                    ->order_by('qd.dateupdated DESC')->get()->row();

                $data['list'][] = array(
                    'num' => $n++,
                    'id' => $item->sysid,
                    'item' => $item->fulldescription,
                    'unit' => $unit->code ?? 'Missing Unit ID : '.$item->unitid,
                    'purchased' => $purchased_po, //Query qty of item in all approved PO that has not been received
                    'allocated' => $allocated, //Get all Allocated items in customer application
                    'onhand' => $onhand, //Sum of checked-in items less the sum of checked out
                    'lastpurchase' => $lastbuy_qry ? date('Y-m-d',strtotime($lastbuy_qry->dateupdated)) : '<code>N/A</code>',
                    'status' => ' <a href="javascript:;" title="To update after setting Re-Order Point"><i class="fa fa-question-circle-o"></i></a>', //TO DETERMINE RE-ORDER POINT
                    'control' => $control
                );
            }
        }

        $data['columns'] = $columns;

        return json_encode($data);
    }

    function dt_reference_list(){
        $data = array();
        $trntype = $this->input->post('trntype');

        //PO: PO# > SUPPLIER > ITEM COUNT > TOTAL QTY > CONTROL
        //CHECK-IN/OUT: PAE# > CUSTOMER NAME (SHORTENED) > BUILD NAME > ITEM COUNT > CONTROL
        $list = array();
        if ($trntype == 23) {
            $columns = array(
                dt_column_array('num','#','text-align-center'),
                dt_column_array('ponum','PO #','text-primary bold'),
                dt_column_array('supplier','Supplier','bold'),
                dt_column_array('itemcnt','Items','text-align-center'),
                //dt_column_array('qty','Qty','number'),
                dt_column_array('control','<i class="fa fa-check-square-o"></i>','text-align-center'),
            );

            $purhcase_order_qry = $this->db->select('
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
                ->join('eprs_suppliers_main AS sm','qs.supplierid = sm.sysid','left')
                ->join('eprs_quotation_details AS qd','qs.sysid = qd.quotationid','left')
                ->where(array(
                    'qd.status' => 301,
                    'qs.status' => 301
                ))->group_by('qd.quotationid')
                ->get();

            //$data['query'] = $this->db->last_query();

            if ($purhcase_order_qry->num_rows() > 0) {
                $num = 1;
                foreach ($purhcase_order_qry->result() AS $po) {

                    $po_details = $this->db->select('po.sysid as poid,po.ponumber,qd.sysid,qd.paytype,qd.payterm,qd.purpose,qd.notes')
                        ->from('eprs_po_details as qd')
                        ->join('eprs_po as po','qd.poid = po.sysid','left')
                        ->where(array('qd.quotationid' => $po->sysid,'qd.status' => 1))
                        ->get()->row();

                    $data['podetailsQuery'][$po->sysid] = $this->db->last_query();

                    if ($po_details) {
                        $po_count = $this->db->select('COUNT(po.sysid) as cnt')
                            ->from('eprs_po_details as po')
                            ->where(array('po.sysid <=' => $po_details->sysid, 'po.status' => 1))
                            ->get()->row();


                        $rfop_cnt = $this->db->select('COUNT(pod.sysid) as cnt')
                            ->from('eprs_po_details as pod')
                            ->join('eprs_po AS po','po.sysid = pod.poid')
                            ->where(array('pod.sysid <=' => $po_details->sysid, 'pod.status' => 1, 'po.ponumber' => $po_details->ponumber))
                            ->get()->row();
                    }

                    if ($po_details && $po_details->ponumber != null) {
                        $list[] = array(
                            'num' => $num++,
                            'ponum' => 'PAE-' . str_pad($po_details->ponumber, 8, '0', STR_PAD_LEFT) . '-' . str_pad($rfop_cnt->cnt, 3, '0', STR_PAD_LEFT),
                            'supplier' => $po->name,
                            'itemcnt' => $po->items,
                            'control' => '<input type="checkbox" class="icheck" id="inventory_trn_ref" name="referenceid[]" value="' . $po->sysid . '" /> ',
                        );
                    }
                }
            }
        }

        if ($trntype == 24) {
            $columns = array(
                dt_column_array('num','#','text-align-center'),
                dt_column_array('appnumber','Application #','text-danger bold'),
                dt_column_array('customer','Customer','text-primary bold'),
                dt_column_array('build','System Build','text-primary'),
                dt_column_array('itemcnt','Items','number'),
                dt_column_array('control','<i class="fa fa-check-square-o"></i>','text-align-center'),
            );


        }

        $data['columns'] = $columns;
        $data['list'] = $list;

        if (count($list) > 0) {
            if ($trntype == 23) {
                $btnText = 'PO Items';
            }
            if ($trntype == 24) {
                $btnText = 'Installation Items';
            }
            $data['btnSubmit'] = '<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-download"></i> Load '.$btnText.'</button>';
        }

        return json_encode($data);
    }

    function create_trn() {
        $data = array();
        $trntype = $this->input->post('trntype');
        $trndate = $this->input->post('trndate');
        $desc = $this->input->post('desc');
        $qry = false;
        $msg = '';
        $func = '';

        $this->db->trans_begin();
        $insert = insert_db($this->db,'inventory_transaction_group',$this->input->post());
        if ($insert->qry) {
            $this->db->trans_commit();
            $data['trnid'] = $insert->insert_id;
            $btn = '<button type="button" id="btn_cancel_inv_trn" class="btn btn-default margin-top-10" style="width: 100%" data-id="'.$insert->insert_id.'"> <i class="fa fa-times text-danger bold"></i> Cancel Transaction</button>';
            $data['btn'] = $btn;
            $qry = true;
        } else {
            $this->db->trans_rollback();
            $msg = 'Unable to create new Inventory Transaction.';
            $func = 'error';
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;

        return json_encode($data);
    }

    function cancel_trn() {
        $data = array();
        $trnid = $this->input->post('trnid');
        $qry = false;

        //Update all reference and items associated to this transaction.
        $this->db->trans_begin();
        $removetrn = update_db($this->db,'inventory_transaction_group',array('status' => 303),array('sysid' => $trnid));

        if ($removetrn->qry && $removetrn->updated > 0) {
            //find and remove references.
            $ref_qry = $this->db->select('sysid')
                ->from('inventory_transaction_reference')
                ->where(array('groupid' => $trnid, 'status' => 1))
                ->get();

            if ($ref_qry->num_rows() > 0) {
                $refids = array();
                foreach ($ref_qry->result() as $row) {
                    $refids[] = $row->sysid;
                }

                if (count($refids) > 0) {
                    $this->db->where_in('referenceid', $refids);
                    $removeitems = update_db($this->db,'inventory_transaction_items',array('status' => 303),array('status' => 1));
                    if ($removeitems->qry) {
                        $this->db->where_in('sysid', $refids);
                        $removerefs = update_db($this->db,'inventory_transaction_reference',array('status' => 303),array('status' => 1));
                        if ($removerefs->qry) {
                            $qry = true;
                            $this->db->trans_commit();
                        } else {
                            $this->db->trans_rollback();
                        }
                    } else {
                        $this->db->trans_rollback();
                    }
                }
            } else {
                $qry = true;
                $this->db->trans_commit();
            }
        } else {
            $this->db->trans_rollback();
        }

        if ($qry) {
            $audit_arr = array(
                'dataid' => $trnid,
                'moduleid' => 222,
                'valueold' => 'status=1',
                'valuenew' => 'status=303',
                'remarks' => 'Inventory Transaction cancelled by user.'
            );
            audit_insert($audit_arr);
        }

        $data['qry'] = $qry;

        return json_encode($data);
    }

    function check_iventory_items() {
        $data = array();
        $trnid = $this->input->post('trnid');
        $hasitems = array();

        $trn_qry = $this->db->select()
            ->from('inventory_transaction_group')
            ->where(array('sysid' => $trnid,))
            ->where_in('status',array(1,300))
            ->get()->row();

        if ($trn_qry) {
            $trn = $trn_qry;
            //CHECK EACH REFERENCE HAVE AT LEAST ONE ITEM
            $ref_qry = $this->db->select('sysid,referenceid')
                ->from('inventory_transaction_reference')
                ->where(array('groupid' => $trnid))
                ->where_in('status',array(1,300))
                ->get();



            if ($ref_qry->num_rows() > 0) {
                foreach ($ref_qry->result() as $row) {
                    if ($trn->trntype == 23) {
                        $items_qry = $this->db->select('COUNT(sysid) AS count')
                            ->from('inventory_transaction_items')
                            ->where(array('referenceid' => $row->sysid))
                            ->where_in('status',array(1,300))
                            ->get()->row();

                        if ($items_qry->count > 0) {
                            $hasitems[$row->sysid] = true;
                        } else {
                            $hasitems[$row->sysid] = false;
                        }
                    }

                    if ($trn->trntype == 24) {
                        $install_list = $this->db->select('list.appid,list.qty,list.unitid,list.sysid as referenceitemid,list.itemid')
                            ->from('installation_item_list AS list')
                            ->join('inventory_transaction_reference AS itr', 'itr.referenceid = list.appid', 'left')
                            ->where(array('itr.referenceid' => $row->referenceid, 'list.status' => 1))
                            ->get();

                        $data['install_qry'] = $this->db->last_query();

                        if ($install_list->num_rows() > 0) {
                            $itemscount = 0;
                            foreach ($install_list->result() as $install_item) {
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
                                    ->where(array('itr.referenceid' => $install_item->appid, 'iti.itemid' => $install_item->itemid, 'iti.referenceitemid' => $install_item->referenceitemid, 'itg.sysid' => $trnid))
                                    ->where_in('iti.status', array(1, 300))
                                    ->group_by('iti.referenceitemid')
                                    ->get()->row();

                                $utilized = $install_item->qty + ($inventory_qry ? $inventory_qry->additional - $inventory_qry->returned : 0);

                                if ($utilized > 0) {
                                    $itemscount++;
                                }
                            }

                            if ($itemscount > 0) {
                                $hasitems[$row->sysid] = true;
                            } else {
                                $hasitems[$row->sysid] = false;
                            }
                        }
                    }
                }
            }
        }

        $data['references'] = $hasitems;

        if (count($hasitems) > 0 && !in_array(false, $hasitems)) {
            $data['proceed'] = true;
        } else {
            $data['proceed'] = false;
        }

        return json_encode($data);
    }

    function submit_trn() {
        $data = array();
        //Transaction Flow IDs
        $invtrnid = $this->input->post('invtrnid');
        $flowid = $this->input->post('flowid');
        $stageid = $this->input->post('stageid');

        //Inventory DATA ID
        $trnid = $this->input->post('trnid');

        $qry = false;
        $msg = '';
        $func = '';
        $title = '';
        $this->db->trans_begin();
        if (!$invtrnid) {
            //UPDATE ALL ITEMS STATUS TO 300 FOR PENDING
            $updated = array();
            $update_group = update_db($this->db,'inventory_transaction_group',array('status' => 300),array('sysid' => $trnid));
            if ($update_group->qry) {
                $ref_ids = array();
                $ref_ids_qry = $this->db->select('sysid')
                    ->from('inventory_transaction_reference')
                    ->where(array('groupid' => $invtrnid, 'status' => 300))
                    ->get();

                if ($ref_ids_qry->num_rows() > 0) {
                    $ref_ids = array_column($ref_ids_qry->result(), 'sysid');
                }
                $update_ref = update_db($this->db,'inventory_transaction_reference',array('status' => 300),array('groupid' => $trnid,'status' => 1));
                if ($update_ref->qry) {
                    if ($update_ref->updated > 0 ) {
                        foreach ($ref_ids as $id) {
                            $update_items = update_db($this->db,'inventory_transaction_items',array('status' => 300),array('status' => 1,'referenceid' => $id));
                            if ($update_items->qry) {
                                $updated['item_'.$id] = true;
                            } else {
                                $updated['item_'.$id] = false;
                            }
                        }
                    } else {
                        //echo $this->db->last_query();
                        $updated['ref'] = false;
                    }
                } else {
                    $updated['ref'] = false;
                }
            } else {
                $updated['group'] = false;
            }

            if (!in_array(false,$updated)) {
                $desc_qry = $this->db->select('desc')->from('inventory_transaction_group')->where('sysid', $invtrnid)->get()->row();
                $desc = ($desc_qry) ? $desc_qry->desc : '';
                $invnum = 'INV-' . date('Ymd') . '-' . str_pad($invtrnid, 5, '0', STR_PAD_LEFT);
                $insert_trns_trail = create_transaction_trails('INV-NEW', $invnum, 222, $invtrnid);
                $data['new_trn'] = $insert_trns_trail;
                if ($insert_trns_trail) {
                    $audit_data = array(
                        'dataid' => $trnid,
                        'valuenew' => $invnum,
                        'moduleid' => 222,
                        'remarks' => 'New Inventory Transaction: ' . $desc . '.',
                        'createdby' => user_id()
                    );
                    audit_insert($audit_data);
                }

                //FORWARD TO APPROVAL SINCE STEP 1 IS STILL THE DATA ENTRY
                $trn_data = $this->db->select('trnid')
                    ->from('transaction_request_main_trails')
                    ->where(array('stageid' => 117, 'dataid' => $invtrnid, 'status' => 1))
                    ->order_by('datecreated DESC')->get()->row();

                if ($trn_data) {
                    $trail_arr = array(
                        'trnid' => $trn_data->trnid,
                        'stageid' => 118,
                        'dataid' => $trnid,
                        'createdby' => user_id(),
                    );

                    $send = task_ins_process($trail_arr, null, null);
                }
            }
            $data['updated'] = $updated;
        } else {

            $stage = get_stage_details($stageid);

            $nextroute_qry = $this->db->select('sysid')
                ->from('prime_transaction_flow_main_stages')
                ->where(array('flowid' => $flowid,'levels >' => $stage->levels,'status' => 1))
                ->order_by('levels ASC')
                ->get()->row();

            if ($nextroute_qry) {
                $trail_arr = array (
                    'trnid' => $invtrnid,
                    'stageid' => $nextroute_qry->sysid,
                    'dataid' => $trnid,
                    'createdby' => user_id(),
                    //'status' => $stats
                );

                $send = task_ins_process($trail_arr,null,null);
                //$typename = get_types_name($type);
                //$data['type'] = $typename->names;
            }
        }

        if (isset($send) && $send->qry) {
            $this->db->trans_commit();
            $qry = true;
            $msg = 'Inventory transaction has been forwarded for approval.';
            $func = 'success';
            $title = 'Sent for Approval!';
            //$url = base_url('module/49e3d046636e06b2d82ee046db8e6eb9a2e11e16/view/' . $prfid);
            //$data['url'] = $url;
        } else {
            $this->db->trans_rollback();
            $msg = 'Failed to send transaction for approval.';
            $func = 'error';
            $title = 'Sending Error!';

        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }

    function add_reference() {
        $data = array();
        $trntype = $this->input->post('trntype');
        $groupid = $this->input->post('trngroupid');
        $refid = $this->input->post('refid');
        $trndate = $this->input->post('trndate');

        if ($trntype == 23) {
            $referencename = $this->input->post('ponumber');
        }

        if ($trntype == 24) {
            $referencename = $this->input->post('appnumber');
        }

        $this->db->trans_begin();
        $ref_arr = array(
            'groupid' => $groupid,
            'referenceid' => $refid,
            'trndate' => $trndate
        );
        $add_reference = insert_db($this->db,'inventory_transaction_reference',$ref_arr);

        if ($add_reference->qry) {
            if ($trntype == 23) {
                $this->db->trans_commit();
            }

            if ($trntype == 24) {
                $saved = array();
                $template = $this->input->post('template');
                //QUERY TEMPLATE
                if ($template) {
                    $template_items_qry = $this->db->select('')
                        ->from('inventory_installation_template_items')
                        ->where(array('groupid' => $template))
                        ->get();

                    $items_arr = array();
                    if ($template_items_qry->num_rows() > 0) {
                        foreach ($template_items_qry->result() as $template_) {
                            $items_arr[] = array(
                                'appid' => $refid,
                                'itemid' => $template_->itemid,
                                'qty' => $template_->qty,
                                'unitid' => $template_->unitid,
                                'itemtype' => $template_->itemtype,
                            );
                        }
                    }

                    $insert = $this->db->insert_batch('installation_item_list', $items_arr);
                    if ($insert) {
                        $saved['template'] = true;
                    } else {
                        $saved['template'] = false;
                    }
                }

                $installationteam = $this->input->post('installteam');

                if ($installationteam) {
                    $teams = implode(',', $installationteam);
                    $installation_qry = $this->db->select()
                        ->from('application_installation_dates')
                        ->where(array('appid' => $refid,'status' => 1))
                        ->get()->row();

                    if ($installation_qry) {
                        $add_team = update_db($this->db,'application_installation_dates',array('team',$teams),array('sysid' => $installation_qry->sysid));
                        if ($add_team) {
                            $saved['team'] = true;
                        } else {
                            $saved['team'] = false;
                        }
                    } else {
                        $installation_date = array(
                            'appid' => $refid,
                            'team' => $teams,
                            'installed' => $trndate
                        );

                        $install_date = insert_db($this->db,'application_installation_dates',$installation_date);
                        if ($install_date) {
                            $saved['team'] = true;
                        } else {
                            $saved['team'] = false;
                        }
                    }
                }

                if (!in_array(false,$saved)) {
                    $this->db->trans_commit();
                } else {
                    $this->db->trans_rollback();
                }
            }

            $tabcontentid = str_replace('-','_',$referencename);
            $content_data['tabname'] = $referencename;
            $content_data['tabcontentid'] = $tabcontentid;
            //create function to create layout for the items
            $content_data['tableid'] = 'tbl_'.$tabcontentid;
            $data['tableid'] = 'tbl_'.$tabcontentid;
            if ($trntype == 24) {
                $data['tableid'] = array(
                    'tbl_'.$tabcontentid.'_1',
                    'tbl_'.$tabcontentid.'_2',
                    'tbl_'.$tabcontentid.'_3',
                    'tbl_'.$tabcontentid.'_4',
                );
            }
            $content_data['refid'] = $refid;
            $content_data['trntype'] = $trntype;
            $content_data['trndate'] = $trndate;
            $content_data['trnref'] = $add_reference->insert_id;
            $tab = '<li class="nav-item">';
            //$tab .= '<a href="#'.$tabcontentid.'" data-toggle="tab" aria-expanded="true">'.$referencename.'</a> ';
            $tab .= '<a href="#'.$tabcontentid.'" data-toggle="tab" aria-expanded="true" style="margin-right: 10px;">'.$referencename.' <i class="fa fa-times text-danger hidden close-tab" title="Close Tab" data-id="'.$add_reference->insert_id.'"></i> </a>';
            $tab .= '</li>';

            $data['tab'] = $tab;

            $tabcontent = '<div class="tab-pane fade in" id="'.$tabcontentid.'">';
            if ($trntype == 23) {
                $tabcontent .= $this->load->view('admin/pages/modules/inventory/rrcontent', $content_data, true);
            }
            if ($trntype == 24) {
                $tabcontent .= $this->load->view('admin/pages/modules/inventory/installcontent', $content_data, true);
            }
            $tabcontent .= '</div>';

            $data['content'] = $tabcontent;
        } else {
            $this->db->trans_rollback();
        }

        return json_encode($data);
    }

    function po_lookup() {
        $data = array();

        $query = $this->input->get('query');
        $not_in = array();

        //LOOKUP EXISTING POs
        $existing_qry = $this->db->select('r.referenceid')
            ->from('inventory_transaction_reference AS r')
            ->join('inventory_transaction_group AS g','r.groupid = g.sysid AND g.trntype = 23','left')
            ->where(array('g.status' => 1,'r.status' => 1))
            ->get();

        if ($existing_qry->num_rows() > 0) {
            $not_in = array_column($existing_qry->result_array(),'referenceid');
            $this->db->where_not_in('qs.sysid',$not_in);
        }

        $purhcase_order_qry = $this->db->select('
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
            ->join('eprs_suppliers_main AS sm','qs.supplierid = sm.sysid','left')
            ->join('eprs_quotation_details AS qd','qs.sysid = qd.quotationid','left')
            ->where(array(
                'qd.status' => 301,
                'qs.status' => 301
            ))
            ->group_by('qd.quotationid')
            ->get();

        //$data['query'] = $this->db->last_query();
        $list = array();
        if ($purhcase_order_qry->num_rows() > 0) {
            $num = 1;
            foreach ($purhcase_order_qry->result() AS $po) {

                $po_details = $this->db->select('po.sysid as poid,po.ponumber,qd.sysid,qd.paytype,qd.payterm,qd.purpose,qd.notes')
                    ->from('eprs_po_details as qd')
                    ->join('eprs_po as po','qd.poid = po.sysid','left')
                    ->where(array('qd.quotationid' => $po->sysid,'qd.status' => 1))
                    ->get()->row();

                $data['podetailsQuery'][$po->sysid] = $this->db->last_query();

                if ($po_details) {
                    $po_count = $this->db->select('COUNT(po.sysid) as cnt')
                        ->from('eprs_po_details as po')
                        ->where(array('po.sysid <=' => $po_details->sysid, 'po.status' => 1))
                        ->get()->row();


                    $rfop_cnt = $this->db->select('COUNT(pod.sysid) as cnt')
                        ->from('eprs_po_details as pod')
                        ->join('eprs_po AS po','po.sysid = pod.poid')
                        ->where(array('pod.sysid <=' => $po_details->sysid, 'pod.status' => 1, 'po.ponumber' => $po_details->ponumber))
                        ->get()->row();
                }

                if ($po_details && $po_details->ponumber != null) {
                    $list[] = array(
                        'num' => $num++,
                        'id' => $po->sysid,
                        'ponum' => 'PAE-' . str_pad($po_details->ponumber, 8, '0', STR_PAD_LEFT) . '-' . str_pad($rfop_cnt->cnt, 3, '0', STR_PAD_LEFT),
                        'supplier' => $po->name,
                        'itemcnt' => $po->items,
                        'control' => '<input type="checkbox" class="icheck" id="inventory_trn_ref" name="referenceid[]" value="' . $po->sysid . '" /> ',
                    );
                }
            }
        }

        $result = array_filter($list, function ($var) use ($query) {
            //return ($var['name'] == $query);
            return (strpos(strtolower($var['ponum']), strtolower($query)) !== false);
        });

        return json_encode($result);
    }

    function active_po_list() {
        //UNUSED FUNCTION. RETAINED FOR POSSIBLE FUTURE USE.
        $data = array();

        $purhcase_order_qry = $this->db->select('
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
            ->join('eprs_suppliers_main AS sm','qs.supplierid = sm.sysid','left')
            ->join('eprs_quotation_details AS qd','qs.sysid = qd.quotationid','left')
            ->where(array(
                'qd.status' => 301,
                'qs.status' => 301
            ))->group_by('qd.quotationid')
            ->get();

        //$data['query'] = $this->db->last_query();
        $list = array();
        if ($purhcase_order_qry->num_rows() > 0) {
            $num = 1;
            foreach ($purhcase_order_qry->result() AS $po) {

                $po_details = $this->db->select('po.sysid as poid,po.ponumber,qd.sysid,qd.paytype,qd.payterm,qd.purpose,qd.notes')
                    ->from('eprs_po_details as qd')
                    ->join('eprs_po as po','qd.poid = po.sysid','left')
                    ->where(array('qd.quotationid' => $po->sysid,'qd.status' => 1))
                    ->get()->row();

                $data['podetailsQuery'][$po->sysid] = $this->db->last_query();

                if ($po_details) {
                    $po_count = $this->db->select('COUNT(po.sysid) as cnt')
                        ->from('eprs_po_details as po')
                        ->where(array('po.sysid <=' => $po_details->sysid, 'po.status' => 1))
                        ->get()->row();


                    $rfop_cnt = $this->db->select('COUNT(pod.sysid) as cnt')
                        ->from('eprs_po_details as pod')
                        ->join('eprs_po AS po','po.sysid = pod.poid')
                        ->where(array('pod.sysid <=' => $po_details->sysid, 'pod.status' => 1, 'po.ponumber' => $po_details->ponumber))
                        ->get()->row();
                }

                if ($po_details && $po_details->ponumber != null) {
                    $list[] = array(
                        'num' => $num++,
                        'id' => $po->sysid,
                        'ponum' => 'PAE-' . str_pad($po_details->ponumber, 8, '0', STR_PAD_LEFT) . '-' . str_pad($rfop_cnt->cnt, 3, '0', STR_PAD_LEFT),
                        'supplier' => $po->name,
                        'itemcnt' => $po->items,
                        'control' => '<input type="checkbox" class="icheck" id="inventory_trn_ref" name="referenceid[]" value="' . $po->sysid . '" /> ',
                    );
                }
            }
        }

        $data['list'] = $list;

        return json_encode($data);
    }

    function po_info() {
        $data = array();
        $poid = $this->input->post('poid');
        $supplier_details = $this->db->select('s.name, s.tin, sa.address, sod.name AS accountname, sod.bank, sod.accountnum, qs.exvat, qs.rfop, qs.exrate, qs.shipping, s.currency, qs.paytype, s.type')
            ->from('eprs_suppliers_main AS s')
            ->join('eprs_quotation_suppliers AS qs','s.sysid = qs.supplierid','left')
            ->join('eprs_suppliers_address AS sa','s.sysid = sa.supplierid','left')
            ->join('eprs_suppliers_online_details AS sod','s.sysid = sod.supplierid AND sod.status = 1','left')
            ->where(array('qs.sysid' => $poid,'s.status' => 1))
            ->get()->row();

        $approved_qt = $this->db->select('i.fulldescription as name,eti.qty,eti.unitid,eqd.amount,eqd.status,qr.remarks')
            ->from('eprs_quotation_details AS eqd')
            ->join('eprs_transaction_items AS eti','eqd.prfitemid = eti.sysid','left')
            ->join('items_main_description AS i','eti.itemid = i.sysid','left')
            ->join('eprs_quotation_remarks AS qr','eti.sysid = qr.prfitemid AND qr.status = 1','left')
            ->where(array('eqd.quotationid' => $poid,'eqd.status ' => 301))
            ->get();

        $items = array();
        if ($approved_qt->num_rows() > 0) {
            foreach ($approved_qt->result() AS $qt) {
                $items[] = $qt;
            }
        }

        $data['supplier'] = ($supplier_details) ? $supplier_details->name : '';
        $data['address'] = ($supplier_details) ? $supplier_details->address : '';

        $itemscount = count($items);

        $data['items'] = ($itemscount > 0) ? $itemscount : '';

        if ($itemscount > 0) {
            $count = 1;
            foreach ($items AS $item) {
                $data['list'][] = array(
                    'num' => $count++,
                    'desc' => $item->name,
                    'qty' => number_format($item->qty),
                    'unit' => unit_query($item->unitid)->code,
                    'remarks' => $item->remarks
                );
            }
        }

        $data['columns'] = array(
            dt_column_array('num','SN#','number','5%'),
            dt_column_array('desc','Item Description',false,'50%'),
            dt_column_array('qty','Qty','number'),
            dt_column_array('unit','Unit',''),
            dt_column_array('remarks','Remarks',''),
        );

        return json_encode($data);
    }

    function dt_inventory_trn_items() {
        $data = array();
        $dataid = $this->input->post('dataid');
        $trntype = $this->input->post('datatype');
        $trnid = $this->input->post('trnid');
        $itemtype = $this->input->post('itemtype');
        $view = $this->input->post('view');
        $edit = true;

        $trn_qry = $this->db->select()
            ->from('inventory_transaction_group')
            ->where(array('sysid' => $trnid))
            ->get()->row();

        if ($trn_qry && in_array($trn_qry->status,array(300,301)) && $trn_qry->createdby != user_id()) {
            $edit = false;
        }

        if ($trntype == 23) {
            $approved_qt = $this->db->select('i.fulldescription as name,eti.qty,eti.unitid,eqd.status, eti.sysid as referenceitemid, i.sysid as itemid')
                ->from('eprs_quotation_details AS eqd')
                ->join('eprs_transaction_items AS eti','eqd.prfitemid = eti.sysid','left')
                ->join('items_main_description AS i','eti.itemid = i.sysid','left')
                ->join('eprs_quotation_remarks AS qr','eti.sysid = qr.prfitemid AND qr.status = 1','left')
                ->where(array('eqd.quotationid' => $dataid,'eqd.status ' => 301))
                ->get();

            //$data['po_qry'] = $this->db->last_query();

            if ($approved_qt->num_rows() > 0) {
                $num = 1;
                foreach ($approved_qt->result() AS $qt) {
                    //QUERY EXISTING ITEM QTY AND REMARKS ON INVENTORY
                    $inventory_qry = $this->db->select('iti.sysid,iti.qty,iti.remarks')
                        ->from('inventory_transaction_items AS iti')
                        ->join('inventory_transaction_reference AS itr','iti.referenceid = itr.sysid','left')
                        ->join('inventory_transaction_group AS itg','itg.sysid = itr.groupid','left')
                        ->where(array('itr.referenceid' => $dataid,'iti.itemid' => $qt->itemid,'iti.referenceitemid' => $qt->referenceitemid,'itg.sysid' => $trnid))
                        ->where_in('iti.status',array(1,300,301))
                        ->get()->row();

                    //$data['qty_qry'][] = $this->db->last_query();
                    //ADD NEW ROW IF REFERENCE EXIST IN PAST TRANSACTIONS. (TO RECEIVE)

                    $inventory_id = '<input type="hidden" name="inventoryitemid" autocomplete="off" id="inventory_item_id" value="'.($inventory_qry ? $inventory_qry->sysid : '').'">';

                    $control = '';
                    $control .= '<div class="btn-group">';
                    $control .= '<button id="btn_save_item" class="btn btn-primary btn-sm inline"><i class="fa fa-save"></i></button>';
                    $control .= '<button id="btn_clear_item" class="btn btn-danger btn-sm inline"><i class="fa fa-times"></i></button>';
                    $control .= '</div>';

                    if ($edit) {
                        $data['list'][] = array(
                            'num' => $num++ . '<input id="rcv_ref_item_id" type="hidden" name="refitemid" value="' . $qt->referenceitemid . '"><input id="rcv_item_id" name="itemid" type="hidden" value="' . $qt->itemid . '">' . $inventory_id,
                            'desc' => $qt->name,
                            'unit' => unit_query($qt->unitid)->code,
                            'ordqty' => number_format($qt->qty),
                            'rcvqty' => '<input id="input_qty" type="number" name="itemqty" autocomplete="off" class="form-control input-sm inline" value="' . (($inventory_qry && $inventory_qry->qty > 0) ? preg_replace("/\.?0+$/", "", $inventory_qry->qty) : '') . '" style="width: 100% !important;">',
                            'remarks' => '<textarea id="input_itemremarks" class="form-control inline" name="itemremarks" autocomplete="off" style="width: 100% !important; resize: none" rows="1">' . ($inventory_qry ? $inventory_qry->remarks : '') . '</textarea>',
                            'control' => $control
                        );
                    } else {
                        if ($inventory_qry) {
                            $data['list'][] = array(
                                'num' => $num++,
                                'desc' => $qt->name,
                                'unit' => unit_query($qt->unitid)->code,
                                'ordqty' => number_format($qt->qty),
                                'rcvqty' => preg_replace("/\.?0+$/", "", $inventory_qry->qty),
                                'remarks' => $inventory_qry->remarks
                            );
                        }
                    }
                }
            }

            $data['columns'] = array(
                dt_column_array('num','#','number','30px'),
                dt_column_array('desc','Item Description'),
                dt_column_array('unit','Unit',false,'50px'),
                dt_column_array('ordqty','Qty Ordered','number','85px'),
                dt_column_array('rcvqty','Qty Received','number','90px'),
                dt_column_array('remarks','Remarks',false,'215px')
            );
            if ($edit) {
                $data['columns'][] = dt_column_array('control','<i class="fa fa-cogs"></i>','text-align-center','60px');
            }
        }

        if ($trntype == 24) {
            //QRY INSTALLATION ITEM LIST
            $data['columns'] = array(
                dt_column_array('num','#','number','30px'),
                dt_column_array('desc','Item Description',false,'280px'),
                dt_column_array('unit','Unit',false,'30px'),
                dt_column_array('qty','Qty','number','15px'),
                dt_column_array('serial','SN','number','85px'),
                dt_column_array('additional','Additional Qty','number','60px'),
                dt_column_array('utilized','Utilized Qty','number','60px'),
                dt_column_array('returned','Returned Qty','number','60px'),
                dt_column_array('remarks','Remarks')
            );
            if ($edit) {
                $data['columns'][] = dt_column_array('control','<i class="fa fa-cogs"></i>','text-align-center','100px');
            }

            $install_list = $this->db->select('i.fulldescription as name,list.qty,list.unitid,list.sysid as referenceitemid,i.sysid as itemid,list.itemtype')
                ->from('installation_item_list AS list')
                ->join('items_main_description AS i','list.itemid = i.sysid','left')
                ->where(array('list.appid' => $dataid,'list.itemtype' => $itemtype,'list.status' => 1))
                ->get();

            if ($install_list->num_rows() > 0) {
                $num = 1;
                foreach ($install_list->result() AS $item) {
                    $inventory_qry = $this->db->select('
                    iti.sysid,
                    MAX(CASE WHEN iti.type = 22 THEN iti.qty END) AS qty,
                    MAX(CASE WHEN iti.type = 21 THEN iti.qty END) AS returned,
                    MAX(CASE WHEN iti.type = 25 THEN iti.qty END) AS additional,
                    GROUP_CONCAT(iti.remarks SEPARATOR ";") as remarks
                    ')
                        ->from('inventory_transaction_items AS iti')
                        ->join('inventory_transaction_reference AS itr','iti.referenceid = itr.sysid','left')
                        ->join('inventory_transaction_group AS itg','itg.sysid = itr.groupid','left')
                        ->where(array('itr.referenceid' => $dataid,'iti.itemid' => $item->itemid,'iti.referenceitemid' => $item->referenceitemid,'itg.sysid' => $trnid))
                        ->where_in('iti.status',array(1,300))
                        ->group_by('iti.referenceitemid')
                        ->get()->row();

                    //$data['qty_qry'][] = $this->db->last_query();
                    $inventory_id = '<input type="hidden" name="inventoryitemid" autocomplete="off" id="inventory_item_id" value="'.($inventory_qry ? $inventory_qry->sysid : '').'">';

                    $control = '';
                    $control .= '<div class="btn-group">';
                    $control .= '<button id="btn_save_item" class="btn btn-primary btn-sm inline"><i class="fa fa-save"></i></button>';
                    $control .= '<button id="btn_clear_item" class="btn btn-danger btn-sm inline"><i class="fa fa-times"></i></button>';
                    //$control .= '<button id="btn_delete_item" class="btn btn-danger btn-sm inline"><i class="fa fa-times"></i></button>';
                    $control .= '</div>';


                    $utilized = $item->qty + ($inventory_qry ? $inventory_qry->additional - $inventory_qry->returned : 0);

                    $serial = '';
                    if (preg_match('(solar panel|inverter|battery)', strtolower($item->name))) {
                        $serial_qry = $this->db->select('serialnumber')
                            ->from('application_installation_material_details')
                            ->where(array('appid'=>$dataid,'itemid' => $item->itemid,'status' => 1))
                            ->get();

                        if ($serial_qry->num_rows() > 0) {
                            $serials = array();
                            foreach ($serial_qry->result() AS $serial_item) {
                                $serials[] = $serial_item->serialnumber;
                            }
                            $serial .= ellipsis(implode(', ',$serials),15);
                        }

                        if ($edit) {
                            $serial .= '<a href="#frm_installation_item_serial" data-toggle="ajax-modal" data-arr="' . $item->referenceitemid . '" data-view="1" title="Item Serial Numbers"><i class="fa fa-info-circle"></i></a>';
                        }
                    }
                    if ($edit) {
                        $data['list'][] = array(
                            'num' => $num++ . '<input id="install_ref_item_id" type="hidden" name="refitemid" value="' . $item->referenceitemid . '"><input id="install_item_id" name="itemid" type="hidden" value="' . $item->itemid . '">' . $inventory_id,
                            'desc' => $item->name,
                            'unit' => unit_query($item->unitid)->code,
                            'qty' => dt_inline_input('qty','number',preg_replace("/\.?0+$/", "", $item->qty),array('autocomplete' => 'off','disabled' => false),false,array('width' => '100% !important')),
                            'serial' => $serial,
                            //'rcvqty' => '<input id="rcv_item_qty" type="number" name="itemqty" autocomplete="off" class="form-control input-sm" value="' . (($inventory_qry && $inventory_qry->qty > 0) ? preg_replace("/\.?0+$/", "", $inventory_qry->qty) : '') . '" style="width: 100% !important;">',
                            'additional' => dt_inline_input('additional','number',(($inventory_qry && $inventory_qry->additional > 0) ? preg_replace("/\.?0+$/", "", $inventory_qry->additional) : ''),array('autocomplete' => 'off','disabled' => false),false,array('width' => '100% !important')),
                            'utilized' => '<span id="item_utilized_qty">'.$utilized.'</span>',
                            'returned' => dt_inline_input('returned','number',(($inventory_qry && $inventory_qry->returned > 0) ? preg_replace("/\.?0+$/", "", $inventory_qry->returned) : ''),array('autocomplete' => 'off','disabled' => false),false,array('width' => '100% !important')),
                            //'remarks' => '<textarea id="rcv_item_remarks" class="form-control" name="itemremarks" autocomplete="off" style="width: 100% !important; resize: none" rows="1">' . ($inventory_qry ? $inventory_qry->remarks : '') . '</textarea>',
                            'remarks' => dt_inline_input('itemremarks','textarea',($inventory_qry ? $inventory_qry->remarks : ''),array('autocomplete' => 'off','rows' => 1,'disabled' => false),false,array('width' => '100% !important','resize' => 'none')),
                            'control' => $control
                        );
                    } else {
                        if ($utilized > 0) {
                            $data['list'][] = array(
                                'num' => $num++,
                                'desc' => $item->name,
                                'unit' => unit_query($item->unitid)->code,
                                'qty' => ($item->qty > 0) ? rtrim(rtrim(number_format($item->qty, 2), '0'), '.') : '',
                                'serial' => $serial,
                                'additional' => ($inventory_qry && $inventory_qry->additional > 0) ? preg_replace("/\.?0+$/", "", $inventory_qry->additional) : '',
                                'utilized' => rtrim(rtrim(number_format($utilized, 2), '0'), '.'),
                                'returned' => ($inventory_qry && $inventory_qry->returned > 0) ? preg_replace("/\.?0+$/", "", $inventory_qry->returned) : '',
                                'remarks' => ($inventory_qry ? $inventory_qry->remarks : '')
                            );
                        }
                    }
                }
            }
        }

        return json_encode($data);
    }

    function get_transaction_items() {
        $data = array();
        $trnid = $this->input->post('trnid');

        $tabs = array();
        $content = array();
        //LOOKUP TRN DETAILS
        $trn_qry = $this->db->select('itg.sysid as trn,itg.trntype,itr.sysid,itr.referenceid as id, itr.trndate')
            ->from('inventory_transaction_group AS itg')
            ->join('inventory_transaction_reference AS itr','itg.sysid = itr.groupid','left')
            ->where(array('itg.sysid' => $trnid))
            ->where_in('itr.status',array(1,300,301))
            ->get();

        if ($trn_qry->num_rows() > 0) {
            foreach ($trn_qry->result() AS $reference) {
                if ($reference->trntype == 23) {
                    $po_series_cnt = 0;
                    $po_number = '';
                    //GET PO NUMBER USING REFERENCE ID
                    $po_details = $this->db->select('po.sysid as poid,po.ponumber,qd.sysid,qd.paytype,qd.payterm,qd.purpose,qd.notes,s.descs AS supplier')
                        ->from('eprs_po_details as qd')
                        ->join('eprs_po as po','qd.poid = po.sysid','left')
                        ->join('eprs_quotation_suppliers AS qs','qs.sysid = qd.quotationid','left')
                        ->join('eprs_suppliers_main AS s','s.sysid = qs.supplierid','left')
                        ->where(array('qd.quotationid' => $reference->id,'qd.status' => 1))
                        ->get()->row();

                    if ($po_details) {
                        $po_cnt = $this->db->select('COUNT(pod.sysid) as cnt')
                            ->from('eprs_po_details as pod')
                            ->join('eprs_po AS po','po.sysid = pod.poid')
                            ->where(array('pod.sysid <=' => $po_details->sysid, 'pod.status' => 1, 'po.ponumber' => $po_details->ponumber))
                            ->get()->row();

                        if ($po_cnt) {
                            $po_series_cnt = $po_cnt->cnt;
                        }

                        $po_count = $this->db->select('COUNT(po.sysid) as cnt')
                            ->from('eprs_po_details as po')
                            ->where(array('po.sysid <=' => $po_details->sysid, 'po.status' => 1))
                            ->get()->row();

                        if ($po_count) {
                            $rfop = $po_count->cnt;
                        }

                        $len = 20 - strlen('...'.(isset($rfop) ? ' (#'.$rfop.')' : ''));

                        $supplier = (strlen($po_details->supplier) > $len) ? mb_substr($po_details->supplier, 0, $len) . '...' : $po_details->supplier;


                        $po_number = 'PAE-'.str_pad($po_details->ponumber,8,'0',STR_PAD_LEFT).'-'.str_pad($po_series_cnt,3,'0',STR_PAD_LEFT);
                    }

                    $tabcontentid = str_replace('-','_',$po_number);

                    $tab = '<li class="">';
                    $tab .= '<a href="#'.$tabcontentid.'" data-toggle="tab" data-short-name="'.(isset($rfop) ? 'RFOP #'.$rfop : '').'" aria-expanded="true" style="margin-right: 10px;" title="'.$po_details->supplier.'"><span>'.$po_number.(isset($rfop) ? ' (#'.$rfop.')' : '').'</span> <i class="fa fa-times text-danger hidden close-tab" title="Close Tab" data-id="'.$reference->sysid.'"></i> </a>';
                    $tab .= '</li>';

                    $tabs[] = $tab;

                    $content_data['tabname'] = $po_number;
                    $content_data['tabcontentid'] = $tabcontentid;
                    //create function to create layout for the items
                    $content_data['tableid'] = 'tbl_'.$tabcontentid;
                    $data['tableids'][] = 'tbl_'.$tabcontentid;
                    $content_data['refid'] = $reference->id;
                    $content_data['trntype'] = $reference->trntype;
                    $content_data['trndate'] = $reference->trndate;
                    $content_data['trnref'] = $reference->sysid;

                    $tabcontent = '<div class="tab-pane fade in" id="'.$tabcontentid.'">';
                    $tabcontent .= $this->load->view('admin/pages/modules/inventory/rrcontent', $content_data, true);
                    $tabcontent .= '</div>';

                    $content[] = $tabcontent;
                }

                if ($reference->trntype == 24) {
                    //GET CUSTOMER APPLICATION DETAILS
                    $appinfo = $this->app_info($reference->id);
                    if (is_string($appinfo)) {
                        $appinfo = (object)json_decode($appinfo);
                    }

                    if (is_array($appinfo)) {
                        $appinfo = (object)$appinfo;
                    }


                    $appnum = $appinfo->appnumber;
                    $tabcontentid = str_replace('-','_',$appnum);

                    $tab = '<li class="">';
                    $tab .= '<a href="#'.$tabcontentid.'" data-toggle="tab" aria-expanded="true" style="margin-right: 10px;" title="'.$appinfo->appname.'">'.$appnum.' <i class="fa fa-times text-danger hidden close-tab" title="Close Tab" data-id="'.$reference->sysid.'"></i> </a>';
                    $tab .= '</li>';

                    $tabs[] = $tab;

                    $content_data['tabname'] = $appnum;
                    $content_data['tabcontentid'] = $tabcontentid;
                    $content_data['tableid'] = 'tbl_'.$tabcontentid;
                    //create function to create layout for the items
                    $data['tableids'][] = array(
                        'tbl_'.$tabcontentid.'_1',
                        'tbl_'.$tabcontentid.'_2',
                        'tbl_'.$tabcontentid.'_3',
                        'tbl_'.$tabcontentid.'_4',
                    );
                    $content_data['refid'] = $reference->id;
                    $content_data['trntype'] = $reference->trntype;
                    $content_data['trndate'] = $reference->trndate;
                    $content_data['trnref'] = $reference->sysid;

                    $tabcontent = '<div class="tab-pane fade in" id="'.$tabcontentid.'">';
                    $tabcontent .= $this->load->view('admin/pages/modules/inventory/installcontent', $content_data, true);
                    $tabcontent .= '</div>';

                    $content[] = $tabcontent;
                }
            }
        }

        $data['tabs'] = $tabs;
        $data['contents'] = $content;

        return json_encode($data);
    }

    function save_trn_item_qty() {
        $data = array();
        $referenceid = $this->input->post('referenceid');
        $trnid = $this->input->post('trnid');
        $trntype = $this->input->post('trntype');
        $inventoryitemid = $this->input->post('inventoryitemid');
        $referenceitemid = $this->input->post('refitemid');
        $itemid = $this->input->post('itemid');
        $itemqty = $this->input->post('itemqty');
        $itemadd = $this->input->post('additional');
        $itemreturned = $this->input->post('returned');
        $itemremarks = $this->input->post('itemremarks');

        $error = array();
        $for_audit = array();

        $msg = '';
        $qry = false;
        $title = '';
        $func = 'error';

        $this->db->trans_begin();
        if ($trntype == 23) {
            if ($inventoryitemid > 0) {
                $update_arr = array();
                $item_qry = $this->db->select('qty,remarks')
                    ->from('inventory_transaction_items')
                    ->where(array('sysid' => $inventoryitemid))
                    ->get()->row();

                if ($item_qry) {
                    if ($item_qry->qty != $itemqty && $itemqty !== false) {
                        $update_arr['qty'] = $itemqty;
                    }

                    if ($item_qry->remarks != $itemremarks) {
                        $update_arr['remarks'] = $itemremarks;
                    }
                }

                if (count($update_arr) > 0) {
                    $update_item = update_db($this->db, 'inventory_transaction_items', $update_arr, array('sysid' => $inventoryitemid));
                    if ($update_item->qry) {
                        $this->db->trans_commit();
                        $data['update'] = $update_arr;
                        $msg = 'Item quantity updated successfully.';
                        $qry = true;
                        $title = 'Updated!';
                        $func = 'success';
                    } else {
                        $this->db->trans_rollback();
                        $msg = 'Failed to update item quantity.';
                        $title = 'Failed!';
                        $func = 'error';
                    }
                } else {
                    $msg = 'No changes was made on the item.';
                    $title = 'No change!';
                    $func = 'warning';
                }
            } else {
                //Get reference id
                $reference_qry = $this->db->select('itr.sysid')
                    ->from('inventory_transaction_reference AS itr')
                    ->join('inventory_transaction_group AS itg', 'itr.groupid = itg.sysid', 'left')
                    ->where(array('itr.referenceid' => $referenceid, 'itg.trntype' => $trntype, 'itr.status' => 1))
                    ->get()->row();

                //$data['ref_qry'] = $this->db->last_query();

                if ($reference_qry) {
                    $inventoryreferenceid = $reference_qry->sysid;
                    $item_arr = array(
                        'referenceid' => $inventoryreferenceid,
                        'itemid' => $itemid,
                        'referenceitemid' => $referenceitemid,
                        'qty' => $itemqty,
                        'remarks' => $itemremarks,
                    );

                    $insert_item = insert_db($this->db, 'inventory_transaction_items', $item_arr);
                    if ($insert_item->qry) {
                        $this->db->trans_commit();
                        $qry = true;
                        $msg = 'Item qty added successfully.';
                        $func = 'success';
                        $title = 'Saved!';
                        $data['newitemid'] = $insert_item->insert_id;
                    } else {
                        $this->db->trans_rollback();
                        $msg = 'Failed to update item quantity and/or remarks.';
                        $title = 'Failed!';
                    }
                } else {
                    $msg = 'Failed to update item quantity and/or remarks. No Reference ID found.';
                    $title = 'Failed!';
                }
            }
        }

        if ($trntype == 24) {
            //SELECT EACH TYPE
            //QUERY ITEM TYPE
            /*$inventory_qry = $this->db->select('
                iti.sysid,
                list.qty,
                MAX(CASE WHEN iti.type = 21 THEN iti.qty END) AS returned,
                MAX(CASE WHEN iti.type = 25 THEN iti.qty END) AS additional,
                GROUP_CONCAT(iti.remarks SEPARATOR ";") as remarks
            ')
                ->from('installation_item_list AS list')
                ->join('inventory_transaction_items AS iti', 'list.sysid = iti.referenceitemid AND iti.status IN (1,300)', 'LEFT')
                ->join('inventory_transaction_reference AS itr', 'iti.referenceid = itr.sysid', 'LEFT')
                ->join('inventory_transaction_group AS itg', 'itg.sysid = itr.groupid', 'LEFT')
                ->where(array('list.sysid' => 1))
                ->group_by('iti.referenceitemid')
                ->get()->row();*/
            $updated = array();
            $installation_qry = $this->db->select('qty')
                ->from('installation_item_list')
                ->where(array('sysid' => $referenceitemid,'status' => 1))
                ->get()->row();

            if ($installation_qry) {
                if ($installation_qry->qty != $itemqty && $itemqty > 0) {
                    $install_arr = array(
                        'qty' => $itemqty,
                    );

                    $update_qty = update_db($this->db,'installation_item_list', $install_arr, array('sysid' => $referenceitemid));

                    if (!$update_qty) {
                        $error['item_qty'] = true;
                    } else {
                        $updated['item_qty'] = true;
                    }
                }
            }

            $inventory_qry = $this->db->select('
                iti.referenceitemid,
                iti.referenceid,
                MAX(CASE WHEN iti.type = 21 THEN iti.qty END) AS returned,
                MAX(CASE WHEN iti.type = 25 THEN iti.qty END) AS additional,
                GROUP_CONCAT(iti.remarks SEPARATOR ";") as remarks
                ')
                ->from('inventory_transaction_items AS iti')
                ->join('inventory_transaction_reference AS itr','iti.referenceid = itr.sysid','left')
                ->join('inventory_transaction_group AS itg','itg.sysid = itr.groupid','left')
                ->where(array('itr.referenceid' => $referenceid,'iti.itemid' => $itemid,'iti.referenceitemid' => $referenceitemid))
                ->where_in('iti.status',array(1,300))
                ->group_by('iti.referenceitemid')
                ->get()->row();

            $data['inventoryitem'] = $inventory_qry;

            //$data['inventory_qry'] = $this->db->last_query();
            //Get reference id
            $reference_qry = $this->db->select('itr.sysid')
                ->from('inventory_transaction_reference AS itr')
                ->join('inventory_transaction_group AS itg', 'itr.groupid = itg.sysid', 'left')
                ->where(array('itr.referenceid' => $referenceid, 'itg.trntype' => $trntype, 'itr.status' => 1))
                ->get()->row();

            //$data['ref_qry'] = $this->db->last_query();

            if ($reference_qry) {
                $inventoryreferenceid = $reference_qry->sysid;
                if ($inventory_qry) {
                    if ($inventory_qry->returned > 0) {
                        if ($inventory_qry->returned != $itemreturned) {
                            $for_audit[] = array(
                                'dataid' => $trnid,
                                'valueold' => $inventory_qry->returned,
                                'valuenew' => $itemreturned,
                                'remarks' => 'Update'
                            );
                            $update_qry = update_db($this->db, 'inventory_transaction_items', array('status' => 0), array('referenceid' => $inventoryreferenceid, 'referenceitemid' => $referenceitemid, 'itemid' => $itemid, 'type' => 21,'status NOT IN (0,303)' => null));

                            if ($update_qry->qry) {
                                if ($itemreturned > 0) {
                                    $returned_arr = array(
                                        'referenceid' => $inventoryreferenceid,
                                        'referenceitemid' => $referenceitemid,
                                        'itemid' => $itemid,
                                        'type' => 21,
                                        'qty' => $itemreturned,
                                    );
                                    $returned = insert_db($this->db, 'inventory_transaction_items', $returned_arr);
                                    if (!$returned->qry) {
                                        $error['returned'] = true;
                                    } else {
                                        $updated['returned'] = true;
                                    }
                                }  else {
                                    $updated['returned'] = true;
                                }
                            } else {
                                $error['returned'] = true;
                            }
                        }
                    } else {
                        //INSERT RETURNED QTY
                        if ($itemreturned > 0) {
                            $returned_arr = array(
                                'referenceid' => $inventoryreferenceid,
                                'referenceitemid' => $referenceitemid,
                                'itemid' => $itemid,
                                'type' => 21,
                                'qty' => $itemreturned,
                            );
                            $returned = insert_db($this->db, 'inventory_transaction_items', $returned_arr);
                            if (!$returned->qry) {
                                $error['returned'] = true;
                            } else {
                                $updated['returned'] = true;
                            }
                        }
                    }

                    if ($inventory_qry->additional > 0) {
                        if ($inventory_qry->additional != $itemadd) {
                            $for_audit[] = array(
                                'dataid' => $trnid,
                                'valueold' => $inventory_qry->additional,
                                'valuenew' => $itemadd,
                                'remarks' => 'Update'
                            );
                            $update_qry = update_db($this->db, 'inventory_transaction_items', array('status' => 0), array('referenceid' => $inventoryreferenceid, 'referenceitemid' => $referenceitemid, 'itemid' => $itemid, 'type' => 25,'status NOT IN (0,303)' => null));

                            if ($update_qry->qry) {
                                if ($itemadd > 0) {
                                    $additional_arr = array(
                                        'referenceid' => $inventoryreferenceid,
                                        'referenceitemid' => $referenceitemid,
                                        'itemid' => $itemid,
                                        'type' => 25,
                                        'qty' => $itemadd
                                    );
                                    $additional = insert_db($this->db, 'inventory_transaction_items', $additional_arr);
                                    if (!$additional->qry) {
                                        $error['additional'] = true;
                                    } else {
                                        $updated['additional'] = true;
                                    }
                                } else {
                                    $updated['additional'] = true;
                                }
                            } else {
                                $error['additional'] = true;
                            }
                        }
                    } else {
                        //INSERT ADDITIONAL QTY
                        if ($itemadd > 0) {
                            $additional_arr = array(
                                'referenceid' => $inventoryreferenceid,
                                'referenceitemid' => $referenceitemid,
                                'itemid' => $itemid,
                                'type' => 25,
                                'qty' => $itemadd
                            );
                            $additional = insert_db($this->db, 'inventory_transaction_items', $additional_arr);
                            if (!$additional->qry) {
                                $error['additional'] = true;
                            } else {
                                $updated['additional'] = true;
                            }
                        }
                    }

                    if (is_string($inventory_qry->remarks) && mb_strlen(trim($inventory_qry->remarks)) > 0) {
                        if (trim($inventory_qry->remarks) != trim($itemremarks)) {
                            $update_qry = update_db($this->db, 'inventory_transaction_items', array('status' => 0), array('referenceid' => $inventoryreferenceid, 'referenceitemid' => $referenceitemid, 'itemid' => $itemid, 'type' => 22,'status NOT IN (0,303)' => null));
                            if ($update_qry->qry) {
                                if (mb_strlen(trim($itemremarks)) > 0) {
                                    $remarks_arr = array(
                                        'referenceid' => $inventoryreferenceid,
                                        'referenceitemid' => $referenceitemid,
                                        'itemid' => $itemid,
                                        'type' => 22,
                                        'remarks' => $itemremarks
                                    );

                                    $remarks = insert_db($this->db, 'inventory_transaction_items', $remarks_arr);
                                    if (!$remarks->qry) {
                                        $error['remarks'] = true;
                                    } else {
                                        $updated['remarks'] = true;
                                    }
                                } else {
                                    $updated['remarks'] = true;
                                }
                            }
                        }
                    }
                } else {
                    if ($itemreturned > 0) {
                        $returned_arr = array(
                            'referenceid' => $inventoryreferenceid,
                            'referenceitemid' => $referenceitemid,
                            'itemid' => $itemid,
                            'type' => 21,
                            'qty' => $itemreturned,
                        );
                        $returned = insert_db($this->db, 'inventory_transaction_items', $returned_arr);
                        if (!$returned->qry) {
                            $error['returned'] = true;
                        } else {
                            $updated['additional'] = true;
                        }
                    }

                    if ($itemadd > 0) {
                        $additional_arr = array(
                            'referenceid' => $inventoryreferenceid,
                            'referenceitemid' => $referenceitemid,
                            'itemid' => $itemid,
                            'type' => 25,
                            'qty' => $itemadd
                        );
                        $additional = insert_db($this->db, 'inventory_transaction_items', $additional_arr);
                        if (!$additional->qry) {
                            $error['additional'] = true;
                        } else {
                            $updated['additional'] = true;
                        }
                    }

                    if (mb_strlen(trim($itemremarks)) > 0) {
                        $remarks_arr = array(
                            'referenceid' => $inventoryreferenceid,
                            'referenceitemid' => $referenceitemid,
                            'itemid' => $itemid,
                            'type' => 22,
                            'remarks' => $itemremarks
                        );

                        $remarks = insert_db($this->db, 'inventory_transaction_items', $remarks_arr);
                        if (!$remarks->qry) {
                            $error['remarks'] = true;
                        } else {
                            $updated['remarks'] = true;
                        }
                    }
                }
            }

            if (count($error) > 0) {
                $this->db->trans_rollback();
                $msg = 'Failed to update item quantity and/or remarks.';
                $title = 'Failed!';
                $data['error'] = $error;
            } else {
                if (count($updated) > 0) {
                    $data['updated'] = $updated;
                    $this->db->trans_commit();
                    $qry = true;
                    $msg = 'Item updated successfully.';
                    $func = 'success';
                    $title = 'Saved!';
                    if (count($for_audit) > 0) {
                        foreach ($for_audit as $audit) {
                            audit_insert($audit);
                        }
                    }
                } else {
                    if ($installation_qry->qty != $itemqty && floatval($itemqty) == 0) {
                        $msg = 'Load-out qty was zero. Please provide quantity to continue update.';
                    } else {
                        $msg = 'No update was made. Submitted data was the same as current.';
                    }
                    $func = 'warning';
                    $title = 'No Update!';
                }
            }
        }

        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }

    function remove_inventory_item() {
        $data = array();
        $itemid = $this->input->post('inventoryitemid');
        $trntype = $this->input->post('trntype');
        $refitemid = $this->input->post('refitemid');
        $msg = '';
        $func = '';
        $title = '';
        $qry = false;

        $this->db->trans_begin();
        $updated = array();
        if ($trntype == 24) {
            $update_installation = update_db($this->db,'installation_item_list',array('status' => 0),array('sysid' => $refitemid));
            $updated[] = ($update_installation) ? $update_installation->qry : false;
            $update_item = update_db($this->db, 'inventory_transaction_items', array('status' => 0), array('sysid' => $itemid));
            $updated[] = ($update_item) ? $update_item->qry : false;
        }

        if ($trntype == 23) {
            $update_item = update_db($this->db, 'inventory_transaction_items', array('status' => 0), array('sysid' => $itemid));
            $updated[] = ($update_item) ? $update_item->qry : false;
        }
        if (count($updated) > 0 && !in_array(false,$updated)) {
            $this->db->trans_commit();
            $msg = 'Item removed successfully.';
            $func = 'success';
            $title = 'Removed!';
            $qry = true;
        } else {
            $this->db->trans_rollback();
            $msg = 'Failed to remove item.';
            $title = 'Failed!';
            $func = 'error';
        }

        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }

    function delete_transaction_reference() {
        $data = array();
        $referenceid = $this->input->post('referenceid');
        $name = $this->input->post('refname');
        $msg = '';
        $func = '';
        $title = '';
        $qry = false;

        $this->db->trans_begin();
        $remove_ref = update_db($this->db, 'inventory_transaction_reference', array('status' => 303), array('sysid' => $referenceid));
        if ($remove_ref->qry) {
            $remove_items = update_db($this->db, 'inventory_transaction_items', array('status' => 303), array('referenceid' => $referenceid));

            if ($remove_items->qry) {
                $msg = $name.' has been removed successfully.';
                $func = 'success';
                $title = 'Transaction Removed!';
                $qry = true;
                $this->db->trans_commit();
            } else {
                $msg = 'Failed to remove '.$name.' from this transaction.';
                $func = 'error';
                $title = 'Failed!';
                $this->db->trans_rollback();
            }
        } else {
            $msg = 'Failed to remove '.$name.' from this transaction.';
            $func = 'error';
            $title = 'Failed!';
            $this->db->trans_rollback();
        }

        $data['msg'] = $msg;
        $data['qry'] = $qry;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }

    function dt_inventory_transaction_list() {
        $data = array();

        $route = $this->input->post('route');

        $inv_flow_ids_arr = flow_id_arr('INVENTORY');
        $inv_flow_ids = ($inv_flow_ids_arr) ? implode(',', $inv_flow_ids_arr) : false;
        $where_trails_last = ($inv_flow_ids_arr) ? " AND rm.flowid IN ($inv_flow_ids) " : "";
        $where_stages = ($inv_flow_ids_arr) ? " AND flowid IN ($inv_flow_ids) " : "";
        $data['traillast'] = $where_trails_last;

        if($route && ((is_array($route) && count($route) > 0) || $route > 0)) {

            $levels = '';
            if (is_array($route)) {
                $levels = 'levels IN ('.implode(',',$route).')';
            } else {
                $levels = ($route > 0) ? 'levels = '.$route : 'levels = ""';
            }

            $sql_stages = $this->db->query("
                SELECT sysid
                FROM prime_transaction_flow_main_stages
                WHERE $levels AND `status` = 1 $where_stages
                ");

            if($sql_stages->num_rows()>0) {
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

            if($sql_stages->num_rows()>0) {
                foreach ($sql_stages->result() as $srow) {
                    $stages_ids[] = $srow->sysid;
                }
            }
            $stageids = implode(',', $stages_ids);
            $where = ' AND rmt.stageid IN (' . $stageids . ')';
        }

        $roles = json_decode(get_user_role(user_id()));
        /*if (user_id() != 1 || ($roles && !array_search(24,array_column((array)$roles,'id')))) {
            $where .= ' AND et.createdby = ' . user_id();
        }*/

        $qry_details = $this->db->query("
            SELECT
                itg.sysid,
                itg.trntype,
                rmt.trnid,
                rmt.stageid,
                trm.datecreated AS submitted,
                rmt.datecreated AS updated,
                COUNT( itg.sysid ) AS items,
                COUNT( itr.sysid ) AS `references`,
                itg.desc AS justification,
                itg.createdby,
                itg.datecreated,
                rmt.dateupdated,
                itg.`status`
            FROM
                inventory_transaction_group AS itg
                LEFT JOIN inventory_transaction_reference AS itr ON itr.groupid = itg.sysid AND itr.`status` IN ( 300, 305 )
                LEFT JOIN inventory_transaction_items AS iti ON iti.referenceid = itr.sysid AND iti.`status` IN ( 300, 305 )
                JOIN (SELECT MAX(sysid) AS sysid,trnid,stageid,dataid,MAX(datecreated) AS datecreated,`status`,MAX(dateupdated) AS dateupdated FROM transaction_request_main_trails GROUP BY trnid,stageid,dataid,`status` ORDER BY sysid ASC) AS rmt ON rmt.dataid = itg.sysid
                INNER JOIN transaction_request_main AS trm ON rmt.trnid = trm.sysid  
            WHERE 
                itg.`status` IN (1,300)
                $where 
                AND rmt.status = 1
            GROUP BY
                itg.sysid,
                rmt.trnid,
                -- rmt.stageid,
                itg.datecreated,
                itg.createdby
            ORDER BY
                itg.sysid ASC,
                rmt.datecreated DESC
        ");

        //$data['sql'] = $this->db->last_query();

        if ($qry_details->num_rows() > 0) {
            foreach ($qry_details->result() AS $row) {
                $invid = $row->sysid;
                $trnid = $row->trnid;
                $trntype = $row->trntype;
                $stageid = $row->stageid;
                $datesubmitted = $row->submitted;
                $justification = ellipsis($row->justification,50);
                $createdby = $row->createdby;
                $created = $row->datecreated;
                $items = $row->items;
                $refcnt = $row->references;

                /*if (strlen(trim($justification)) > 50) {
                    $jstr = ellipsis($justification,50);;
                    $justification = $jstr.' <a href="#" data-toggle="tooltip" class="tooltips" data-placement="right" data-attachement="body" title="'.$justification.'"><i class="fa fa-question-circle-o"></i></a>';
                }*/

                $creator = get_users_info($createdby);
                $requestor = '';

                if ($creator) {
                    $requestor = ucfirst($creator->firstname.' '.$creator->lastname);
                }

                $comment_cnt = '';
                $comment_msg = '';
                $qry_comments_cnt = $this->db->select('count(tc.trnid) AS cnt')
                    ->from('transaction_request_trails_comments AS tc')
                    ->where(array('tc.trnid' => $trnid, 'status' => 1))
                    ->get()->row();
                if($qry_comments_cnt && $qry_comments_cnt->cnt>0) {

                    $qry_comments_msg = $this->db->select('remarks')
                        ->from('transaction_request_trails_comments AS tc')
                        ->where(array('tc.trnid' => $trnid, 'status' => 1))
                        ->order_by('datecreated', 'desc')
                        ->get()->row();
                    $comment_msg = ($qry_comments_msg) ? $qry_comments_msg->remarks : '';
                    $max_length = 45;

                    if (strlen($comment_msg) > $max_length)
                    {
                        $offset = ($max_length - 3) - strlen($comment_msg);
                        $comment_msg = substr($comment_msg, 0, strrpos($comment_msg, ' ', $offset)) . ' ...';
                    }
                    $comment_cnt = '<span class="badge badge-danger pull-right" style="margin-left: 5px;">'.$qry_comments_cnt->cnt.'</span>';
                }

                $creation_date = '';
                $qry_trails_last = $this->db->query("
                    SELECT rm.sysid AS trnid, rmt.sysid, rmt.datecreated, rmt.createdby, rmt.stageid, rmt.dataid, rmt.datecreated AS logdate
                    FROM transaction_request_main_trails AS rmt
                    INNER JOIN transaction_request_main AS rm ON rm.sysid = rmt.trnid
                    WHERE rmt.dataid = $invid 
                    AND rmt.`status` = 1
                    $where_trails_last
                    ORDER BY rmt.datecreated DESC
                ")->row();

                //$data['traillast_qry'] = $this->db->last_query();
                $show = true;
                if($route && $route > 0) {
                    if($qry_trails_last && $qry_trails_last->stageid != $stageid) {
                        $show = false;
                    }
                }

                $trn_name = 'Unknown';
                $updated_date = 'None';
                $button = '';
                $from_created_by = 'None';


                if($qry_trails_last) {

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
                        $button .= '<a target="_blank" title="View Inventory Transaction." data-content="body" href="' . base_url('module/22d200f8670dbdb3e253a90eee5098477c95c23d/view/' . $invid) . '" class="btn btn-primary btn-xs inline tooltips"><i class="fa fa-search fa-fw"></i></a>';
                        $button .= '</div>';
                    }

                }

                $trn_elapse = time_elapsed_diff($creation_date, $updated_date, true);
                $ovr_elapse = time_elapsed_diff($creation_date, date('Y-m-d h:m:s'));

                $time = $datesubmitted . '<br><small class="text-info">' . timeago($row->datecreated, sql_time()->DATETIME).'</small>';
                $time_updated = $updated_date . '<br><small class="text-info">' . timeago($updated_date, sql_time()->DATETIME).'</small>';

                if($row->status==1) {
                    $status = 'Pending';
                }else{
                    $status = get_types_label_format($row->status);
                    if ((in_array($row->status,array(0,302,303)))) {
                        $time_updated = $row->dateupdated . '<br><small class="text-info">' . timeago($row->dateupdated, sql_time()->DATETIME).'</small>';
                    }
                }

                if($show) {
                    $invno = 'INV'.date('Ym',strtotime($created)).str_pad($invid,3,'0',STR_PAD_LEFT);
                    /*$po = $this->db->select('ponumber as number')
                        ->from('eprs_po')
                        ->where(array('prfid' => $prsid,'status' => 1))
                        ->get()->row();

                    if ($po) {
                        $ponum = 'PAE-'.str_pad($po->number,8,'0',STR_PAD_LEFT);
                        $hide = 'hidden';
                    } else {
                        $ponum = 'N/A';
                        $hide = '';
                    }*/

                    $data['list'][] = array(
                        'expand' => btn_expand($invid),
                        'invnum' => '<h4 class="text-danger bold" style="margin: 0px 0px;">' .$invno. ' </h4> ',
                        'submitted' => $time,
                        'from' => $from_created_by,
                        'updated' => $time_updated,
                        'type' => get_types_name($trntype)->names,
                        'refcnt' => $refcnt,
                        'justification' => $justification,
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
            dt_column_array('expand',false,'text-align-center','1%'),
            dt_column_array('invnum',false,'text-primary bold','10%'),
            dt_column_array('submitted',false,false,'10%'),
            dt_column_array('updated',false,false,'10%'),
            dt_column_array('type','TRN Type',''),
            dt_column_array('refcnt','TRN References','text-align-center'),
            dt_column_array('justification',false,false,'300px'),
            dt_column_array('trn',false,'text-danger','150px'),
            dt_column_array('remarks',false,'text-info','150px'),
            dt_column_array('status',false,'text-info'),
            dt_column_array('control',false,'controls','5%'),
        );

        return json_encode($data);
    }

    function cad_lookup() {
        $result = array();
        $query = $this->input->get('query');
        //$route = get_stage_specific(98)->levels;
        //LOOKUP APPLICATIONS IN INSTALLATION.
        //LOOKUP EXISTING POs
        $existing_qry = $this->db->select('r.referenceid')
            ->from('inventory_transaction_reference AS r')
            ->join('inventory_transaction_group AS g','r.groupid = g.sysid AND g.trntype = 24','left')
            ->where_in('g.status',array(1,300))
            ->where_in('r.status',array(1,300))
            ->get();

        $existing = $this->db->last_query();;
        $not_in = '';
        if ($existing_qry->num_rows() > 0) {
            $not = array_column($existing_qry->result_array(),'referenceid');
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
                $result[] = array(
                    'sysid' => $row->sysid,
                    'appname' => ($row->apptype > 1) ? $row->corp : $row->customer,
                    'appnum' => $row->appnum,
                    'address' => $row->address
                );
            }
        }

        return json_encode($result);
    }

    function app_info($applicationid = false) {
        $data = array();
        $appid = ($applicationid > 0) ? $applicationid : $this->input->post('appid');
        $info = application_info($appid);
        if ($info->q) {
            $data = (array)$info;
        }

        //LOOKUP IF APPLICATION HAS ITEMS SET
        if ($info->apptype > 1) {
            // GET CORP INFO
            $qry_corp_app = $this->db->select()
                ->from('application_customers_corporation')
                ->where(array('appid' => $appid, 'types' => $info->apptype))
                ->get()->row();

            if($qry_corp_app) {
                $corp_info = array();
                if($info->apptype == 2) {
                    $corp_info = get_corporation_info($qry_corp_app->corpid);
                    $pic_dir = 'corporation';
                } else {
                    $corp_info = get_government_info($qry_corp_app->corpid);
                    $pic_dir = 'government';
                }
                $pic_id = $qry_corp_app->corpid;
                if ($corp_info->qry) {
                    $corpname = $corp_info->info->descs;

                    $corpbranch = '';
                    if($info->apptype == 2) {
                        $qry_branch = $this->db->select()
                            ->from('corporation_branches')
                            ->where(array('corpid' => $qry_corp_app->corpid, 'sysid' => $qry_corp_app->branchid))
                            ->get()->row();
                        if ($qry_branch) {
                            $corpbranch = ' ('.$qry_branch->names.')';
                        }
                    }else{
                        $corpbranch = ($corp_info) ? ' ('.$corp_info->info->names.')' : '';
                    }
                    $data['appname'] = $corpname.$corpbranch;
                }
            } else {
                $data['appname'] = $info->appname;
            }

        } else {
            $data['appname'] = $info->appname;
        }

        $data['appnumber'] = 'PAE'.str_pad($info->essrno, 6, '0',  STR_PAD_LEFT);
        if (!$info->systemsizeid) {
            $data['systemsizename'] = 'Not Assigned';
        }

        //LOOKUP IF HAS ITEMS ALLOCATED
        $installation_items = $this->db->select('sysid')
            ->from('installation_item_list')
            ->where(array('appid' => $appid,'status' => 1))
            ->get();

        if ($installation_items->num_rows() > 0) {
            $data['installationitems'] = true;
        }

        //LOOKUP IF TEAMS HAVE BEEN ASSIGNED
        $installation_details = $this->db->select('team,installed')
            ->from('application_installation_dates')
            ->where(array('appid' => $appid))
            ->get()->row();

        if ($installation_details && $installation_details->team) {
            $teams = explode(',', $installation_details->team);
            $teams_qry = $this->db->select('name')
                ->from('installation_team')
                ->where_in('sysid', $teams)
                ->get();

            if ($teams_qry->num_rows() > 0) {
                $teams = array();
                foreach ($teams_qry->result() as $team) {
                    $teams[] = $team->name;
                }

                $data['installationteam'] = implode(' & ', $teams);
            }
        } else {
            $data['installationteam'] = false;
        }

        if ($installation_details && $installation_details->installed) {
            $data['installationdate'] = $installation_details->installed;
        } else {
            $data['installationdate'] = false;
        }

        if ($this->input->post()) {
            return json_encode($data);
        } else {
            return (object)$data;
        }
    }

    function select2_installation_template() {
        $data = array();

        $query = $this->db->select('sysid, name')
            ->from('inventory_installation_template_group')
            ->where(array('status' => 1))->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => $row->name
                );
            }
        }

        return json_encode($data);
    }

    function dt_installation_template_items() {
        $data = array();
        $templateid = $this->input->post('templateid');

        $items_qry = $this->db->select('ti.itemtype,ti.qty,imd.fulldescription as item,u.unit_code AS code, u.unit_name AS unit')
            ->from('inventory_installation_template_items AS ti')
            ->join('items_main_description AS imd','ti.itemid = imd.sysid','left')
            ->join('prime_unit AS u','ti.unitid = u.sysid','left')
            ->where(array('ti.groupid' => $templateid,'ti.status' => 1))
            ->get();

        if ($items_qry->num_rows() > 0) {
            $cnum = 0;
            $anum = 0;
            $snum = 0;
            $onum = 0;
            foreach ($items_qry->result() as $row) {
                $type = '';
                switch ($row->itemtype) {
                    case 1:
                        $type = 'componentlist';
                        $cnum++;
                        $num = $cnum;
                        break;
                    case 2:
                        $type = 'accessorylist';
                        $anum++;
                        $num = $anum;
                        break;
                    case 3:
                        $type = 'optionallist';
                        $snum++;
                        $num = $snum;
                        break;
                    case 4:
                        $type = 'otherlist';
                        $onum++;
                        $num = $onum;
                        break;
                }

                $qty = number_format($row->qty,2);

                $data[$type][] = array(
                    'num' => $num,
                    'item' => $row->item,
                    'qty' => preg_replace("/\.?0+$/", "", $qty),
                    'unit' => $row->unit.'('.$row->code.')'
                );
            }
        }

        $data['columns'] = array(
            dt_column_array('num',false,'number'),
            dt_column_array('item'),
            dt_column_array('qty',false,'number'),
            dt_column_array('unit'),
        );

        return json_encode($data);
    }

    function installation_item_lookup() {

    }

    function add_installation_item() {
        $data = array();
        $appid = $this->input->post('appid');
        $itemid = $this->input->post('itemid');
        $qty = $this->input->post('itemqty');
        $addqty = $this->input->post('addqty');
        $unit = $this->input->post('itemunit');
        $type = $this->input->post('itemtype');

        $qry = false;
        $msg = '';
        $func = '';
        $title = '';

        //ADD ITEM AND UNIT TO INSTALLATION_ITEM_LIST

        $item_qry = $this->db->select('sysid,itemtype')
            ->from('installation_item_list')
            ->where(array('itemid' => $itemid,'appid' => $appid, 'status' => 1))
            ->get()->row();

        if (!$item_qry) {
            $this->db->trans_begin();
            $new_item = array(
                'appid' => $appid,
                'itemid' => $itemid,
                'unitid' => $unit,
                'itemtype' => $type,
            );

            if ($qty) {
                $new_item['qty'] = $qty;
            }
            $add_item = insert_db($this->db,'installation_item_list',$new_item);

            if ($add_item->qry) {
                //GET REFERENCEITEMID FROM INSERT_ID AND ADD TO INVENTORY LIST WITH TYPE 25
                if ($addqty > 0) {
                    $referenceitemid = $add_item->insert_id;

                    $reference_qry = $this->db->select('sysid')
                        ->from('inventory_transaction_reference')
                        ->where(array('referenceid' => $appid))
                        ->where_in('status', array(1, 300))
                        ->get()->row();

                    if ($reference_qry) {
                        $inventory_item = array(
                            'referenceid' => $reference_qry->sysid,
                            'itemid' => $itemid,
                            'referenceitemid' => $referenceitemid,
                            'type' => 25,
                            'qty' => $qty,
                        );

                        $add_inventory_item = insert_db($this->db, 'inventory_transaction_items', $inventory_item);

                        if ($add_inventory_item->qry) {
                            $this->db->trans_commit();
                            $qry = true;
                            $msg = 'Item and quantities has been Added.';
                            $func = 'success';
                            $title = 'Add Item';
                        } else {
                            $this->db->trans_rollback();
                        }
                    }
                } else {
                    $this->db->trans_commit();
                    $qry = true;
                    $msg = 'Item has been added.';
                    $func = 'success';
                    $title = 'Add Item';
                }
            } else {
                $this->db->trans_rollback();
            }
        } else {
            $itemtype = array(
                1 => 'Components',
                2 => 'Accessories',
                3 => 'Situational Materials',
                4 => 'Others'
            );
            $msg = 'Item already existing in '.$itemtype[$item_qry->itemtype].'.';
            $func = 'warning';
            $title = 'Existing Item';
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;
        $data['itemtype'] = $type;

        return json_encode($data);
    }

    function save_serial_number() {
        $data = array();
        $appid = $this->input->post('appid');
        $itemid = $this->input->post('itemid');
        $serials = $this->input->post('serials');

        $qry = false;
        $msg = '';
        $func = '';
        $title = '';

        if (is_array($serials) && count($serials) > 0) {
            //lookup saved serial numbers...
            $serial_qry = $this->db->select('serialnumber')
                ->from('application_installation_material_details')
                ->where(array('appid'=>$appid,'itemid' => $itemid))
                ->get();

            $this->db->trans_begin();
            $saved = array();
            foreach ($serials as $serial) {
                $serial_det = array(
                    'appid' => $appid,
                    'itemid' => $itemid,
                    'serialnumber' => trim($serial)
                );

                $save_serial = insert_db($this->db,'application_installation_material_details',$serial_det);
                if ($save_serial->qry) {
                    $saved[$serial] = $serial_det;
                } else {
                    $saved[$serial] = false;
                }
            }

            if (count($saved) > 0 && !in_array(false, $saved)) {
                $this->db->trans_commit();
                $qry = true;
                $msg = 'Serial Numbers has been saved!';
                $func = 'success';
                $title = 'Saved!';
            } else {
                $this->db->trans_rollback();
                $msg = 'Unable to save Serial Numbers!';
                $func = 'error';
                $title = 'Fail!';
            }

            $data['saved'] = $saved;
        } else {
            //return sent serial numbers are invalid.
            $msg = 'Sent serial numbers are invalid or empty';
            $func = 'error';
            $title = 'Invalid!';
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }

    function upload_attachments() {
        $data = array();
        $qry = false;
        $msg = '';

        $this->load->helper('directory');
        $this->load->library('upload');

        $qry = false;

        if(isset($_FILES["appfiledrop"])) {
            $dataid = $this->input->post('dataid');

            $filename = $_FILES['appfiledrop']['name'];
            $fileinfo = pathinfo($filename);
            $file_name = $fileinfo['filename'];

            $file_directory = FCPATH . "uploads/attachments/inventory/transaction/" . str_pad($dataid, 6, "0", STR_PAD_LEFT) . "/";
            $upload = sys_upload_files('appfiledrop',$file_directory,$filename);
            $data['upload'] = $upload;

            if ($upload['uploaded']) {
                $msg = $file_name.' Uploaded!';
                $qry = true;
            } else{
                $msg = 'Drop the file again!';
            }

        }

        $data['msg'] = $msg;
        $data['qry'] = $qry;

        return json_encode($data);
    }

    function approve_trn() {
        $data = array();

        $dataid = $this->input->post('dataid');
        $trnid = $this->input->post('trnid');
        $flowid = $this->input->post('flowid');
        $stageid = $this->input->post('stageid');

        $qry = false;
        $msg = '';
        $title = '';
        $func = '';

        //GET TRN INFO
        $trn_qry = $this->db->select()
            ->from('inventory_transaction_group')
            ->where(array('sysid' => $dataid,'status' => 300))
            ->get()->row();

        if ($trn_qry) {
            $this->db->trans_begin();
            $saved = array();
            $trn = $trn_qry;

            if ($trn->trntype == 23) {
                $reference_item_qry = $this->db->select('iti.sysid, iti.itemid, eti.unitid, iti.qty, iti.type, itr.referenceid')
                    ->from('inventory_transaction_items AS iti')
                    ->join('inventory_transaction_reference AS itr','iti.referenceid = itr.sysid','left')
                    ->join('eprs_quotation_details AS eqd','iti.referenceitemid = eqd.sysid','left')
                    ->join('eprs_transaction_items AS eti','eqd.prfitemid = eti.sysid','left')
                    ->where(array('itr.groupid' => $dataid))
                    ->where_in('iti.status',array(1,300))
                    ->get();

                if ($reference_item_qry->num_rows() > 0) {
                    foreach ($reference_item_qry->result() as $item) {
                        $inventory_item = array(
                            'itemid' => $item->itemid,
                            'unitid' => $item->unitid,
                            'qty' => $item->qty,
                            'itemtype' => 21,
                            'trnid' => $dataid,
                            'referenceid' => $item->referenceid,
                        );

                        $insert = insert_db($this->db,'inventory_items_summary', $inventory_item);
                        if ($insert->qry) {
                            $approve = update_db($this->db,'inventory_transaction_items',array('status' => 301),array('sysid'=>$item->sysid));
                            if ($approve->qry) {
                                $saved[] = true;
                            } else {
                                $saved[] = false;
                            }
                        } else {
                            $saved[] = false;
                        }
                    }
                }
            }
            if ($trn->trntype == 24) {
                $install_list = $this->db->select('list.appid,list.qty,list.unitid,list.sysid as referenceitemid,list.itemid')
                    ->from('installation_item_list AS list')
                    ->join('inventory_transaction_reference AS itr','itr.referenceid = list.appid','left')
                    ->where(array('itr.groupid' => $dataid,'list.status' => 1))
                    ->get();

                if ($install_list->num_rows() > 0) {
                    foreach ($install_list->result() as $install_item) {
                        $inventory_qry = $this->db->select('
                            iti.sysid,
                            MAX(CASE WHEN iti.type = 22 THEN iti.qty END) AS qty,
                            MAX(CASE WHEN iti.type = 21 THEN iti.qty END) AS returned,
                            MAX(CASE WHEN iti.type = 25 THEN iti.qty END) AS additional,
                            GROUP_CONCAT(iti.remarks SEPARATOR ";") as remarks
                            ')
                            ->from('inventory_transaction_items AS iti')
                            ->join('inventory_transaction_reference AS itr','iti.referenceid = itr.sysid','left')
                            ->join('inventory_transaction_group AS itg','itg.sysid = itr.groupid','left')
                            ->where(array('itr.referenceid' => $install_item->appid,'iti.itemid' => $install_item->itemid,'iti.referenceitemid' => $install_item->referenceitemid,'itg.sysid' => $dataid))
                            ->where_in('iti.status',array(1,300))
                            ->group_by('iti.referenceitemid')
                            ->get()->row();

                        $utilized = $install_item->qty + ($inventory_qry ? $inventory_qry->additional - $inventory_qry->returned : 0);

                        if ($utilized > 0) {
                            $inventory_item = array(
                                'itemid' => $install_item->itemid,
                                'unitid' => $install_item->unitid,
                                'qty' => $utilized,
                                'itemtype' => 22,
                                'trnid' => $dataid,
                                'referenceid' => $install_item->appid,
                            );

                            $insert = insert_db($this->db,'inventory_items_summary', $inventory_item);
                            if ($insert->qry) {
                                $approve = update_db($this->db,'inventory_transaction_items',array('status' => 301),array('sysid'=>$item->sysid));
                                if ($approve->qry) {
                                    $saved[] = true;
                                } else {
                                    $saved[] = false;
                                }
                            } else {
                                $saved[] = false;
                            }
                        }
                    }
                }
            }

            if (count($saved) > 0 && !in_array(false, $saved)) {
                $approve_ref = update_db($this->db,'inventory_transaction_reference',array('status' => 301),array('groupid' => $dataid));
                if ($approve_ref->qry) {
                    $approve_trn = update_db($this->db,'inventory_transaction_group',array('status' => 301),array('sysid' => $dataid));
                    if ($approve_trn->qry) {
                        //CHANGE TRN STATUS TO 301 TO REMOVE FROM LIST
                        $approve_inv_trn = update_db($this->db,'transaction_request_main_trails',array('status' => 301),array('trnid' => $trnid,'status' => 1));
                        if ($approve_inv_trn->qry) {
                            $msg = 'Inventory Transaction has been Approved!';
                            $title = 'Approved!';
                            $func = 'success';
                            $qry = true;
                            $data['approved'] = '<ul class="list-group summary column no-border">
                                <li class="list-group-item">
                                    <span class="label-name col-md-4 bold">Approved</span>
                                    <span class="col-md-8 font-blue-steel ">'.date('F j, Y').'</span>
                                </li>
                            </ul>';
                            $this->db->trans_commit();
                        } else {
                            $msg = 'Inventory Transaction has been Approved!';
                            $title = 'Approval FAILED!!!';
                            $func = 'error';
                            $qry = false;
                            $this->db->trans_rollback();
                        }
                    }
                }
            } else {
                $msg = 'One or more records were not updated!';
                $title = 'Failed!';
                $func = 'error';
                $this->db->trans_rollback();
            }
        }

        $data['qry'] = $qry;
        $data['msg'] = $msg;
        $data['func'] = $func;
        $data['title'] = $title;

        return json_encode($data);
    }

    function get_trn_attachments() {
        $dataid = $this->input->post('dataid');
        $data = array();

        $file_directory = FCPATH . 'uploads/attachments/inventory/transaction/' . str_pad($dataid, 6, "0", STR_PAD_LEFT) . '/';
        $file_url = base_url() . 'uploads/attachments/inventory/transaction/' . str_pad($dataid, 6, "0", STR_PAD_LEFT) . '/';

        $map = directory_map($file_directory, FALSE, TRUE);

        if ($map && count($map) > 0) {
            $data['files'] = $map;
            $num = 1;
            foreach ($map AS $file) {
                if (is_array(@getimagesize($file_url . $file))) {
                    $view = '<a class="btn btn-primary inline preview" href="' . $file_url . $file . '" data-lightbox="'.$file.'" data-title="'.$file.'"><i class="fa fa-search"></i></a>';
                    $icon = '<i class="fa fa-image font-blue-sharp"></i>';
                } else {
                    $file_specs = draw_file_icon(basename($file));
                    $view = '<a href="' . $file_url . $file . '" class="btn btn-primary inline bold" target="_blank" data-title="'.basename($file).'" title="'.basename($file).'"><i class="fa fa-search"></i></a>';
                    $icon = '<i class="fa ' . $file_specs->icon . ' ' . $file_specs->color . '"></i>';
                }
                $data['list'][] = array(
                    'num' => $num++,
                    'filename' => $icon.' '.ellipsis(basename($file),15),
                    'view' => $view
                );
            }
        }

        $data['columns'] = array(
            dt_column_array('num','#','number','5px'),
            dt_column_array('filename','Files','bold'),
            dt_column_array('view','View','text-primary text-align-center','10px')
        );

        return json_encode($data);
    }

    function approved_trn_list() {
        $data = array();

        //APPROVED QRY
        $approved_qry = $this->db->select('itg.*,COUNT(itr.sysid) AS refs,type.names AS type')
            ->from('inventory_transaction_group AS itg')
            ->join('prime_types_parameter AS type','type.sysid = itg.trntype','left')
            ->join('inventory_transaction_reference AS itr','itr.groupid = itg.sysid AND itr.status NOT IN (0,303)','left')
            ->where(array('itg.status' => 301))
            ->group_by('itg.sysid')
            ->get();

        $data['query'] = $this->db->last_query();

        if ($approved_qry->num_rows() > 0) {
            $num = 1;
            foreach ($approved_qry->result() AS $row) {
                $invid = $row->sysid;
                $invno = 'INV'.date('Ym',strtotime($row->datecreated)).str_pad($invid,3,'0',STR_PAD_LEFT);
                $button = '<div class="btn-group btn-xs">';
                $button .= '<a target="_blank" title="View Inventory Transaction." data-content="body" href="' . base_url('module/cad06f3c4901bbcd4a396dd83c4544a146d6e3e8/view/' . $invid) . '" class="btn btn-primary btn-xs inline tooltips"><i class="fa fa-search fa-fw"></i></a>';
                $button .= '</div>';

                $data['list'][] = array(
                    'num' => $num++,
                    'invnum' => '<h4 class="text-danger bold" style="margin: 0px 0px;">' .$invno. ' </h4> ',
                    'encoded' => $row->datecreated,
                    'approved' => $row->dateupdated,
                    'refcnt' => $row->refs,
                    'type' => $row->type,
                    'desc' => $row->desc,
                    'control' => $button
                );
            }
        }

        $data['columns'] = array(
            dt_column_array('num',false,'text-align-center','1%'),
            dt_column_array('invnum',false,'text-primary bold','10%'),
            dt_column_array('encoded',false,false,'10%'),
            dt_column_array('approved','Approved',false,'10%'),
            dt_column_array('type','TRN Type','','10%'),
            dt_column_array('refcnt','# References','text-align-center','10%'),
            dt_column_array('desc','Desc/Note',''),
            dt_column_array('control',false,'controls','5%'),
        );

        return json_encode($data);
    }
}