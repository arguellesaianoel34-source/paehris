<?php
/**
 * Created by PhpStorm.
 * User: DUDEZ
 * Date: 7/23/2018
 * Time: 4:32 PM
 */

class Mobile extends CI_Controller
{
    function __construct() {
        parent::__construct();
    }
    function index()
    {
        if (user_id() > 0) {
            $data['pagetitle'] = 'Mobile';
            init_frontend_header($data);
            $this->load->view('mobile/common/navs');

            $this->load->view('mobile/pages/home');
            $this->load->view('mobile/common/scripts');
            $this->load->view('mobile/common/footer');

            init_frontend_footer($data);
        } else {
            $data['pagetitle'] = 'Login';
            init_frontend_header($data);
            $this->load->helper(array('form'));
            $data['mobileview'] = true;
            $this->load->view('redirects/forms/view_login', $data);
            $this->load->view('admin/common/scripts');
            init_frontend_footer($data);
        }
    }

    function sendsms($number,$message) {

        //error_reporting(E_ALL);

        //Example
        /*$this->load->library('gsm_send_sms');
        $gsm_send_sms = new gsm_send_sms();
        $gsm_send_sms->debug = false;
        $gsm_send_sms->port = 'COM3';
        $gsm_send_sms->baud = 19200;
        $gsm_send_sms->init();

        $status = $gsm_send_sms->send($number, $message);
        if ($status) {
            echo "Message sent\n";
        } else {
            echo "Message not sent\n";
        }

        $gsm_send_sms->close();*/

        $this->load->library('PhpSerial');

        $serial = new PhpSerial();

        $serial->init();

        $serial->deviceSet('COM3');
        $serial->deviceOpen();

        //stream_set_timeout($serial->_dHandle, 10);

        $serial->sendMessage("AT+CMGF=1\n\r");
        $serial->sendMessage("AT+cmgs=\"{$number}\"\n\r");
        $serial->sendMessage("{$message}\n\r");
        $serial->sendMessage(chr(26));

        //var_dump($serial->readPort());

        // If you want to change the configuration, the device must be closed
        sleep(7);
        $read=$serial->readPort();
        $serial->deviceClose();

        echo $read;
    }
}