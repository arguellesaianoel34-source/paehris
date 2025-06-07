<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
session_start(); // STARTING SESSION DATA

class Systems extends CI_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->model('model_admin');
        $this->load->model('model_settings');
        $this->load->model('model_systems');
    }

    function select2year(){
        $data = array();

        for($year = 2018;$year <= 2080;$year++){
            $data['list'][] = array(
                'id' => $year,
                'text' => $year.' - '
            );
        }

        echo json_encode($data);
    }
    // ######## SELECT2 MONTH w/ CODES ###############
    function select2month()
    {
        $data = array();
        for ($i = 1; $i <= 12; $i++) {
            $dt = DateTime::createFromFormat('!m', $i);
            $mname = $dt->format('F');
            $mcode = $dt->format('M');
            $data['list'][] = array(
                'id' => $i,
                'text' => strtoupper($mcode) . ' - ' . $mname
            );
        }
        echo json_encode($data);
    }
    // ######## SELECT2 PAYCLASS ################
    function select2payclass()
    {
        $data = array();
        $sql = $this->db->select("sysid,desc")->from("prime_types_parameter")
            ->where(array("status" => 1))->where_in('sysid',array(1,128,3077,3078))->get();
        if($sql->num_rows() > 0){
            foreach ($sql->result() as $row){
                if ($row->sysid == 1){
                    $desc = 'CONFIDENTIALS';
                }else{
                    $desc = $row->desc;
                }
                $data['list'][] = array(
                    'id' => $row->sysid,
                    'text' => ''.$desc
                );
            }
        }
        echo json_encode($data);
    }


    // BILLING DATA MIGRATION

    function getbillingratefromlegacy() {
        $data = array();
        $qry = false;
        $msg = '';
        $month = $this->input->post('month');
        $year = $this->input->post('year');

        $this->db->trans_begin();


        if($month > 0 && $year > 0) {
            if (pecoapps_conn()) {
                $conn = $this->load->database('pecoapps', TRUE);
                $conn->initialize();
                $qry_from_legacy = $conn->select()
                    ->from('rates')
                    ->where(array('yr' => $year, 'mo' => $month))
                    ->order_by('refcode', 'asc')
                    ->get();
                $x = 0;
                if ($qry_from_legacy->num_rows() > 0) {
                    $this->db->where(array('year' => $year, 'month' => $month));
                    $this->db->delete('trn_billing_rates');

                    $tax_arr = array(13, 14, 15);
                    $class_arr = array(1 => 'resi', 2 => 'comm', 3 => 'power', 4 => 'interm', 5 => 'stlights', 6 => 'city', 7 => 'other');

                    foreach ($qry_from_legacy->result() as $row) {

                        $data['legacylist'][] = $row;

                        $legacy_id = $row->refcode;
                        if ($legacy_id == 5 || $legacy_id == 6) {
                            $x = 5;
                        } else if ($legacy_id == 7 || $legacy_id == 8) {
                            $x = 6;
                        } else {
                            $x++;
                        }
                        for ($rr = 1; $rr <= 7; $rr++) {

                            $qry_from_legacy = $conn->select()
                                ->from('rates')
                                ->where(array('refcode' => $legacy_id, 'yr' => $year, 'mo' => $month))
                                ->get()->row();

                            $col_name = $class_arr[$rr];
                            $rate = $qry_from_legacy->$col_name;
                            if (trim($qry_from_legacy->unit) == '') {
                                $units = 103;
                            } else {
                                if (trim($qry_from_legacy->unit) == '/customer') {
                                    $units = 102;
                                } else {
                                    $units = (trim($qry_from_legacy->unit) == '/kwhr') ? 100 : 101;
                                }
                            }
                            if (in_array($legacy_id, $tax_arr)) {
                                $rate = $rate / 100;
                            }
                            $ins_arr = array(
                                'brateid' => $x,
                                'classid' => $rr,
                                'rates' => $rate,
                                'units' => $units,
                                'year' => $year,
                                'month' => $month,
                                'createdby' => 2,
                            );


                            $ins_arr_data = array(
                                'brateid' => $x,
                                'classid' => $rr,
                                'legacyid' => $legacy_id,
                                'classname' => $class_arr[$rr],
                                'rates' => $rate,
                                'units' => $units,
                                'year' => $year,
                                'month' => $month,
                                'createdby' => 2,
                            );

                            $data['ins'][] = $ins_arr_data;
                            $this->db->insert('trn_billing_rates', $ins_arr);
                        }
                    }

                    if ($this->db->trans_status() == true) {
                        $this->db->trans_commit();
                        $qry = true;
                    } else {
                        $this->db->trans_rollback();
                    }
                } else {
                    $msg = 'No billing rates found!';
                }
            }else{
                $msg = 'Cannot connect to server!';
            }
        }else{
            $msg = 'Please provide year and month!';
        }
        $data['msg'] = $msg;
        $data['qry'] = $qry;
        echo json_encode($data);
    }


    // ################################################


    function getusernotifications() {
        echo $this->model_systems->get_user_notifications();
    }
    function getuserinbox() {
        echo $this->model_systems->get_user_inbox();
    }
    function getusertask() {
        echo $this->model_systems->get_user_task();
    }
    function getmoduletaggs() {
        echo $this->model_systems->get_module_tagging();
    }
    function taggthis() {
        echo $this->model_systems->tag_this_module();
    }
    function gettaggingtable() {
        echo $this->model_systems->get_tagging_table();
    }
    function select2banklist() {
        echo $this->model_systems->select2_bank_list();
    }
    function getparameters() {
        echo $this->model_systems->get_tbl_parameters();
    }
    function select2icons() {
        echo $this->model_systems->select2_icons();
    }
    function updateparameterrow() {
        echo $this->model_systems->update_parameter_row();
    }
    function deleteparameters() {
        echo $this->model_systems->delete_parameters();
    }
    function deleteparameterspermanent() {
        echo $this->model_systems->delete_parameters_permanent();
    }
    function addparementer() {
        echo $this->model_systems->add_parameters();
    }

    function quicklaunchlist() {
        echo $this->model_systems->quick_launch_list();
    }



    function serverstats() {
        $ram_free = $this->humanFileSize( $this->model_systems->getRamFree() );
        $ram_total = $this->humanFileSize( $this->model_systems->getRamTotal() );
        $cpu_load = $this->model_systems->getCpuLoadPercentage();
        echo 'RAM: '. $ram_free . ' of '. $ram_total;
        echo '<br>CPU Load: ' . $cpu_load .'%';
        echo '<br><pre>';
    }

    function getcpuusage() {

    }

    function getramusage() {
        echo 'RAM: '.$this->humanFileSize($this->model_systems->getRamFree()). ' / '.$this->humanFileSize($this->model_systems->getRamTotal());
    }

    function getdiskusage() {

    }

    function gettempusage() {


    }

    function getServerLoad() {

    }

    function tblusersaccess() {
        echo $this->model_systems->get_users_list_access();
    }

    function humanFileSize($size, $unit = "") {
        if ((!$unit && $size >= 1 << 30) || $unit == "GB")
            return number_format($size / (1 << 30), 2) . "GB";
        if ((!$unit && $size >= 1 << 20) || $unit == "MB")
            return number_format($size / (1 << 20), 2) . "MB";
        if ((!$unit && $size >= 1 << 10) || $unit == "KB")
            return number_format($size / (1 << 10), 2) . "KB";
        return number_format($size) . " bytes";
    }

    function tblroleslist() {
        echo $this->model_systems->tbl_roles_list();
    }

    function updaterolescolor() {
        echo $this->model_systems->update_roles_color();
    }

    function tblnavlist() {
        echo $this->model_systems->tbl_nav_list();
    }

    function updatenavmatrix() {
        echo $this->model_systems->update_nav_matrix();
    }

    function select2types() {
        echo $this->model_systems->select2_types_option();
    }

    function getcommentnotifications() {
        echo $this->model_systems->get_comment_notifications();
    }

    function select2currency() {
        echo get_currency();
    }

    function phpInfo() {
        echo phpinfo();
    }

    function usernavigations() {
        //$user_nav = get_users_info_navigation_ids();
        $details = application_info(210);
        //echo user_id();
        /*echo "<pre>";
        print_r ($user_nav);
        echo "</pre>";*/
        //echo join(',',$user_nav);
        echo "<pre>";
        print_r ($details);
        echo "</pre>";


    }

    function functionchecker() {
        /*$amout = '17:10:36';
        $roundedspecout = '18:00:00';
        $roundedout = date('H:i:s', floor(strtotime($amout)/60)*60);
        $timeoutDiff = strtotime($roundedout) - strtotime($roundedspecout);
        echo $timeoutDiff.'<br>';
        if ($timeoutDiff < 0) {
            $logs['lessthanzero'] = 'true';
            $diff = -$timeoutDiff;
            //$logs['diff'] = $diff;
            $hours = floor($diff / 3600);
            if (strtotime($amout) < strtotime('13:00:00')) {
                $hours = $hours - 1;
            }
            $mins = floor($diff / 60 % 60);
            $hrundertime = str_pad($hours, 2, "0", STR_PAD_LEFT) . ':' . str_pad($mins, 2, "0", STR_PAD_LEFT) . ':00';
            echo $hrundertime;

        }*/

        /*$old = array(
            'qty' => 20,
            'unit' => 'asdf angs'
        );

        $output = implode(', ', array_map(
            function ($v, $k) { return sprintf("%s='%s'", $k, $v); },
            $old,
            array_keys($old)
        ));

        echo $output;*/

        /*$img = FCPATH.'uploads/attachments/cad/applications/000211/Assessment/Docs/PAE00814___PV_layout___Russ_Sio_16kWp_.jpg';
        $resize = resize_img($img,1024,true);
        $size = getimagesize($img);
        echo "<pre>";
        print_r ($size);
        echo "</pre>";

        echo "<pre>";
        print_r ($resize);
        echo "</pre>";
        echo "<pre>";
        print_r (getimagesize($resize->img));
        echo "</pre>";

        //convert image into Binary data
        $img_data = fopen($resize->img, 'rb');
        $img_size = filesize($resize->img);
        $binary_image = fread($img_data, $img_size);
        fclose($img_data);
        unlink($resize->img);
        //Build the src string to place inside your img tag
        $img_src = "data:" . $resize->mime . ";base64," . str_replace("\n", "", base64_encode($binary_image));
        echo '<img src="'.$img_src.'" />';*/

        /*$values_arr = array(
            array(
                'bioid' => 18822,
                'logtime' => date("H:i:s"),
                'logdate' => date("Y-m-d"),
            ),
            array(
                'bioid' => 18822,
                'logtime' => '16:41:54',
                'logdate' => '2024-11-14',
            )
        );

        $insert = $this->db->insert_batch('test', $values_arr);

        echo "<pre>";
        print_r ($this->db);
        echo "</pre>";*/
        //echo mb_strlen('');
        //$layout = $this->cad->get_document_layout();
        $data = array();
        $html = $this->load->view('custom/templates/inventory/installation', $data, true);
        echo $html;
        exit();
        $html = rehash_pdf_img($html);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $papersize = 'letter';
        $title = 'Test Layout';
        $filename = 'Materials Monitoring Form';

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
        $dompdf->stream($filename,array('Attachment' => false));
    }

    function testview() {
        $this->load->view('custom/testview');
    }

    function files($file) {
        $file_ = base64_decode($file);

        $file_root = FCPATH;
        $file_location = str_replace(base_url(),'',$file_);;
        $file_path = $file_root.$file_location;
        $headers = get_headers($file_);

        $file_contents = file_get_contents($file_path);

        $file_info = pathinfo($file_path);
        $basefile = $file_info['basename'];

        foreach ($headers as $header) {
            header($header);
        }
        header('Content-Disposition: inline; filename="'.$basefile.'"');
        echo $file_contents;


    }

}