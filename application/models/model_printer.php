<?php
class Model_printer extends CI_model
{
    function print_bill_tellerpay()
    {

        if(PHP_OS=='WINNT') {
            $computer_name = $this->printer_hostname();
            $connector = new WindowsPrintConnector("//SE/$computer_name");
        }else {
            $computer_name = $this->printer_hostname();
            $connector = new WindowsPrintConnector("smb://$computer_name/Receipt");
        }

        $printer = new Escpos($connector);
    }

    function printer_hostname(){
        if (!empty($computer_name)){
            return $computer_name;
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
            return "ITD-SE";
            //return exec("nmblookup -A $ip | grep '<00' | grep -v GROUP | awk '{print $1}'");//get the computer name of $ip, only works when server is Linux
        }
    }

    function print_or_payments() {
        $computer_name = $this->input->post('computer_name');
    }

    function docs_preview($id = false, $selected = false, $doctype = false, $print = false) {
        $this->load->helper('cad');
        $id = ($id) ? $id : $this->input->post('id') ;
        $sysid = $this->input->post('sysid') ;
        $doctype = ($doctype) ? $doctype : $this->input->post('doctype');
        $selected = ($selected) ? $selected : $this->input->post('selected');
        $data = array();
        $html = '';

        //$data['params'] = func_get_args();

        $type = get_types_name($doctype);

        if (!is_numeric($id) && strpos(strtolower($id),'pae') !== false) {
            $essrno = (int) filter_var($id, FILTER_SANITIZE_NUMBER_INT);
            $appid_lookup = $this->db->select('sysid')
                ->from('application_customers_details')
                ->where(array('essrno' => $essrno))
                ->get()->row();

            if ($appid_lookup) {
                $id = $appid_lookup->sysid;
            }
        }

        if ($doctype != 3436) {
            $saved = $this->db->select('sysid,html,signed')
                ->from('prime_documents_main')
                ->where(array('dataid' => $id, 'doctype' => $doctype, 'status' => 1))
                ->or_where('sysid',$sysid)
                ->get()->row();

            if ($saved) {
                //$html .= $saved->html;
                $newhtml = rehash_pdf_img($saved->html);
                if ($saved->signed) {
                    $domDoc = new DOMDocument();
                    $domDoc->loadHTML($newhtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING | LIBXML_NOERROR);
                    $xpath = new DOMXPath($domDoc);

                    $signpane = $xpath->query('//img[@class="signature"]');
                    $signature = $this->db->select('imgdata')
                        ->from('prime_user_signature')
                        ->where(array('userid' => $saved->signed, 'status' => 1))
                        ->get()->row();

                    if ($signature) {
                        foreach ($signpane as $sign) {
                            $sign->setAttribute('src',$signature->imgdata);
                            $signstyle = 'width: 25%; height: auto; position: absolute; margin-top: -50px; margin-left: -25%';
                            $sign->setAttribute('style',$signstyle);
                        }
                        $html .= $domDoc->saveHTML();
                    }
                } else {
                    $html .= $newhtml;
                }
            } else {
                $html .= '<h1 style="alignment: center">NOT FOUND</h1>';
                $html .= '<p>No document of this type has been published yet.</p>';
            }
        } else {
            $tssr = get_tssr_layout($id,$selected);
            //$data['tssr'] = $tssr;
            $html .= $tssr->html;
        }

        $data['html'] = $html;
        $data['title'] = ($type) ? $type->desc : '';
        $data['filename'] = ($type) ? $type->desc : '';
        $data['papersize'] = ($doctype == 3434) ? 'folio' : false;

        if ($print) {
            return json_encode($data);
        } else {
            return (object)$data;
        }
    }

    function print_po($ponumber = false) {
        $data = array();
        $data['ponumber'] = $ponumber;
        $html = '';
        $html .= $this->load->view('custom/templates/eprspo',$data,true);

        $rehash = rehash_pdf_img($html);
        $data['html'] = $rehash;
        return (object)$data;
        /*if ($ponumber == 'preview') {
            return (object)$data;
        } else {
            echo $this->load->view('custom/templates/eprspo');
        }*/
    }
}
?>