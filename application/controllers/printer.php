<?php

class Printer extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        //init_printer_escpos();
        $this->load->model('model_receipt');
        $this->load->model('model_printer','printer',true);
    }

    function index()
    {

    }

    function testprint() {
        $data['compname'] = $this->model_receipt->printer_hostname();
        $this->load->view('printing/test', $data);
    }

    function printpaybill() {
        $data['compname'] = $this->model_receipt->printer_hostname();
        $this->load->view('printing/test', $data);
    }

    function preview() {
        $html = $this->input->post('html');
        $title = $this->input->post('title');
        $this->load->library('PDF_HTML');
        //set_magic_quotes_runtime(false);
        $pview = new PDF_HTML();

        $txt = ($html) ? $html : 'No data.';

        $pview->AliasNbPages();

        //add page automatically for its true parameter

        $pview->SetAutoPageBreak(true, 15);

        $pview->AddPage();

        //add logo image here

        //$pdf->Image('images/logo.png',18,13,33);

        //set font style

        $pview->SetFont('Arial','B',14);

        $pview->AddPage();

        $pview->WriteHTML($txt);

        $pview->output('I',$title);

    }

    function PDFview() {
        $html = $this->input->post('html');
        $title = $this->input->post('title');
        $filename = $this->input->post('filename');
        $papersize = $this->input->post('papersize');

        /*echo $html;
        echo "<pre>";
        print_r ($this->input->post());
        echo "</pre>";
        exit();*/

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

    function docspreview($id = false,$selected=false,$doctype = false,$print = false) {
        $print = ($print) ? $print : $this->input->post('print');
        $doc = $this->printer->docs_preview($id,$selected,$doctype,$print);

        if ($print) {
            /*echo "<pre>";
            print_r (json_decode($doc));
            echo "</pre>";
            exit();*/
            echo $doc;
        } else {
            $html = $doc->html;
            $title = $doc->title;
            $filename = $doc->filename;
            $papersize = isset($doc->papersize) ? $doc->papersize : false;
            /*echo "<pre>";
            print_r ($doc);
            echo "</pre>";
            echo $html;
            exit();*/

            $this->load->library('pdf');
            $dompdf = new Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $customPaper = array(0, 0, 615, 930);
            if ($papersize) {
                $dompdf->setPaper($papersize, 'portrate');
            } else {
                $dompdf->setPaper('letter', 'portrate');
            }
            $dompdf->render();
            // Add PDF Document Information
            $dompdf->add_info('Subject', $title);
            $dompdf->add_info('Author', user_info()->username);
            $dompdf->add_info('Creator', 'PAE');
            $dompdf->add_info('Keywords', $title);
            $dompdf->stream($filename, array('Attachment' => false));
        }
    }

    function testimg() {
        //$img = FCPATH.'uploads/attachments/cad/applications/000193/Assessment/Survey/LI_PAE000269.png';
        $img = FCPATH.'assets/global/img/pae_letter_head.png';
        //$img = convert_base64_img($img);
        //echo '<img src="'.$img_.'">';

        echo filesize($img)/1024;
        echo "<pre>";
        print_r (getimagesize($img));
        echo "</pre>";

    }

    function printpo($ponumber = false,$preview = false) {
        $ponumber = ($ponumber) ? $ponumber : $this->input->post('suppid');
        if ($ponumber) {
            $data = array('ponumber' => $ponumber);
            $html = $this->load->view('custom/templates/eprspo',$data,true);
            $hashed = rehash_pdf_img($html);
            $customPaper = array(0, 0, 615, 930);

            $domDoc = new DOMDocument();
            $domDoc->loadHTML($hashed, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING | LIBXML_NOERROR);
            $xpath = new DOMXPath($domDoc);

            //GET PRF ID AND TO GET LOGS
            $logs_qry = $this->db->select('log.statusid,log.moduleid,log.createdby')
                ->from('eprs_transaction_logs AS log')
                ->join('eprs_quotation_suppliers AS eqs','eqs.prfid = log.prsid AND log.`status` = 1 AND log.moduleid IN (213,214,215)','left')
                ->where('eqs.sysid',$ponumber)->get();

            $signature = array();
            if ($logs_qry->num_rows() > 0) {
                foreach ($logs_qry->result() AS $log) {
                    $signature_qry = $this->db->select('imgdata')
                        ->from('prime_user_signature')
                        ->where(array('userid' => $log->createdby, 'status' => 1))
                        ->get()->row();

                    if ($signature_qry) {
                        //$imginfo = getimagesize($signature_qry->imgdata);
                        $signature[$log->moduleid] = array(
                            'sign' => $signature_qry->imgdata,
                            'width' => (433 * 0.25)
                        );
                        $signatures[$log->moduleid] = array(
                            //'imginfo' => getimagesize($signature_qry->imgdata),
                            'signed' => $log->createdby
                        );
                    }
                }
                $signpane = $xpath->query('//img[@class="signature"]');

                foreach ($signpane AS $sign) {
                    $id = $sign->getAttribute('data-id');
                    $sign->setAttribute('src', $signature[$id]['sign']);
                    $signstyle = 'width: '.$signature[$id]['width'].'; height: auto; position: absolute; margin-top: -35px; display: block; margin-left: auto; margin-right: auto;';
                    $sign->setAttribute('style', $signstyle);
                }
                $hashed = $domDoc->saveHTML();
            }

            if ($preview) {
                echo $hashed;
                exit();
            }

            if ($this->input->post()) {

                $data['signatures'] = $signatures;

                $data['html'] = $hashed;

                $po_details = $this->db->select('po.sysid as poid,po.ponumber,qd.sysid,qd.paytype,qd.payterm,qd.purpose,qd.notes')
                    ->from('eprs_po_details as qd')
                    ->join('eprs_po as po','qd.poid = po.sysid','left')
                    ->where(array('qd.quotationid' => $ponumber,'qd.status' => 1))
                    ->get()->row();
                if ($po_details) {
                    $rfop_cnt = $this->db->select('COUNT(sysid) as cnt')
                        ->from('eprs_po_details')
                        ->where(array('sysid <=' => $po_details->sysid, 'status' => 1, 'poid' => $po_details->poid))
                        ->get()->row();

                    $data['title'] = ($rfop_cnt) ? 'PAE-' . str_pad($po_details->ponumber, 8, '0', STR_PAD_LEFT) . '-' . str_pad($rfop_cnt->cnt, 3, '0', STR_PAD_LEFT) : 'TBA';
                    $data['filename'] = ($rfop_cnt) ? 'PAE-' . str_pad($po_details->ponumber, 8, '0', STR_PAD_LEFT) . '-' . str_pad($rfop_cnt->cnt, 3, '0', STR_PAD_LEFT) : 'TBA';
                } else {
                    $data['title'] = 'Temp-PO';
                    $data['filename'] = 'Temp-PO';
                }
                //$data['papersize'] = $customPaper;

                echo json_encode($data);
            } else {
                $this->load->library('pdf');
                $dompdf = new Dompdf\Dompdf();
                $dompdf->loadHtml($hashed);
                $dompdf->setPaper('letter', $customPaper);
                $dompdf->render();
                // Add PDF Document Information
                $dompdf->add_info('Subject', 'PO');
                $dompdf->add_info('Author', user_info()->username);
                $dompdf->add_info('Creator', 'PAE');
                $dompdf->add_info('Keywords', '');
                $dompdf->stream('PO', array('Attachment' => false));
            }
        } else {
            echo '<h1>NO PO NUMBER PROVIDED!!!</h1>';
        }
    }

    function images2pdf($images = array(),$filename = false) {
        $this->load->library('fpdf');
        $pdf = new FPDF();


        if (count($images) > 0) {
            foreach ($images as $img) {
                list($width, $height, $type, $attr) = getimagesize($img);
                $pdf->SetSize(($width / 2) + 10, ($height * 50 / 100)); //Custom function
                $pdf->AddPage('', 'custom');
                $pdf->Image($img, 0, 0, $width * 18 / 100, $height * 18 / 100);
                $pdf->SetAutoPageBreak(true);
            }
        } else {
            $pdf->AddPage('p','letter');
            $pdf->Cell( 116, 7, utf8_decode( 'No images were found.' ), 0, 0, 'L' );
        }

        echo $pdf->output('',$filename);

    }


    function printarchivingdocs() {
        $data = array();
        $type = $this->input->post('type');
        $appid = $this->input->post('appid');
        $doctype = $this->input->post('doctype');
        $filename = $this->input->post('filename');

        if ($type == 'doctype') {
            $this->docspreview($appid,false,$doctype);
        }

        if ($type == 'requirecode') {
            $img_qry = $this->db->select('c.fileurl')
                ->from('application_customers_requirements AS r')
                ->join('application_customers_attachments AS c','c.attachmentid = r.sysid','left')
                ->where(array('r.appid' => $appid,'r.reqid' => $doctype,'r.status' => 1))
                ->order_by('c.fileurl ASC')
                ->get();

            if ($img_qry->num_rows() > 0) {
                $images = array();
                foreach ($img_qry->result() AS $img) {
                    $images[] = FCPATH.$img->fileurl;
                }
                $this->images2pdf($images,$filename);
            } else {
                echo '<h1>No files associated to requirement.</h1>';
            }
        }
    }

    function inventoryform() {
        $trnid = $this->input->post('trnid');
        $refid = $this->input->post('refid');
        $data = array();

        $vars = array(
            'trnref' => $trnid,
            'refid' => $refid,
        );

        $info = application_info($refid);
        $title = ($info->apptype > 1 ? $info->corpname.($info->corpbranch ? '('.$info->corpbranch.')' : '') : $info->appname).' Materials Monitoring Form';
        $data['title'] = $title;

        $flat = $this->load->view('custom/templates/inventory/installation', $vars, true);
        $html = rehash_pdf_img($flat);
        $data['html'] = $html;

        $data['papersize'] = 'letter';


        echo json_encode($data);
    }

}