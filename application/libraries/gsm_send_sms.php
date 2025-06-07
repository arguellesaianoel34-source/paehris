<?php


//Send SMS via serial SMS modem
class gsm_send_sms {

    public $port = 'COM1';
    public $baud = 115200;
    public $debug = false;

    private $fp;
    private $buffer;
    private $device = '\Device\000000d5';
    private $_device;

    //Setup COM port
    public function init() {

        $this->debugmsg("Setting up port: \"{$this->port} @ \"{$this->baud}\" baud");

        //exec("MODE {$this->port}: BAUD={$this->baud} PARITY=N DATA=8 STOP=1", $output, $retval);

        exec("mode " . $this->port . " xon=on BAUD=9600",$output,$retval);

        $this->debugmsg(implode("\n", $output));

        if (preg_match("@^COM(\\d+):?$@i", $this->port, $matches) and $retval === 0) {
            //$this->device = "COM" . $matches[1];
            $this->_device = "\\.com" . $matches[1];
        } else {
            throw new Exception('Unable to setup COM port, check it is correct');
        }

        $this->debugmsg("Opening port");

        //Open COM port
        $this->fp = fopen($this->device . ':', 'r+');

        //Check port opened
        if (!$this->fp) {
            throw new Exception("Unable to open port \"{$this->port}\"");
        }

        $this->debugmsg("Port opened");
        $this->debugmsg("Checking for responce from modem");

        //Check modem connected
        fputs($this->fp, "AT\r");

        //Wait for ok
        $status = $this->wait_reply("OK\r\n", 5);

        if (!$status) {
            throw new Exception('Did not receive responce from modem');
        }

        $this->debugmsg('Modem connected');

        //Set modem to SMS text mode
        $this->debugmsg('Setting text mode');
        fputs($this->fp, "AT+CMGF=1\r");

        $status = $this->wait_reply("OK\r\n", 5);

        if (!$status) {
            throw new Exception('Unable to set text mode');
        }

        $this->debugmsg('Text mode set');

    }

    //Wait for reply from modem
    private function wait_reply($expected_result, $timeout) {

        $this->debugmsg("Waiting {$timeout} seconds for expected result");

        //Clear buffer
        $this->buffer = '';

        //Set timeout
        $timeoutat = time() + $timeout;

        //Loop until timeout reached (or expected result found)
        do {

            $this->debugmsg('Now: ' . time() . ", Timeout at: {$timeoutat}");

            $buffer = fread($this->fp, 1024);
            $this->buffer .= $buffer;

            usleep(200000);//0.2 sec

            $this->debugmsg("Received: {$buffer}");

            //Check if received expected responce
            if (preg_match('/'.preg_quote($expected_result, '/').'$/', $this->buffer)) {
                $this->debugmsg('Found match');
                return true;
                //break;
            } else if (preg_match('/\+CMS ERROR\:\ \d{1,3}\r\n$/', $this->buffer)) {
                return false;
            }

        } while ($timeoutat > time());

        $this->debugmsg('Timed out');

        return false;

    }

    //Print debug messages
    private function debugmsg($message) {

        if ($this->debug == true) {
            $message = preg_replace("%[^\040-\176\n\t]%", '', $message);
            echo $message . "\n";
        }

    }

    //Close port
    public function close() {

        $this->debugmsg('Closing port');
        var_dump($this->fp);
        fclose($this->fp);

    }

    //Send message
    public function send($tel, $message) {
        //Filter tel
        $tel = preg_replace("%[^0-9\+]%", '', $tel);

        //Filter message text
        //$message = preg_replace("%[^\040-\176\r\n\t]%", '', $message);

        $this->debugmsg("Sending message \"{$message}\" to \"{$tel}\"");

        //Start sending of message
        fputs($this->fp, "AT+CMGS=\"{$tel}\"\r");

        //Wait for confirmation
        $status = $this->wait_reply("\r\n> ", 5);

        if (!$status) {
            //throw new Exception('Did not receive confirmation from modem');
            $this->debugmsg('Did not receive confirmation from modem');
            return false;
        }

        //Send message text
        fputs($this->fp, $message);


        //Send message finished indicator
        fputs($this->fp, chr(26));

        //Wait for confirmation
        $status = $this->wait_reply("OK\r\n", 180);

        if (!$status) {
            //throw new Exception('Did not receive confirmation of messgage sent');
            $this->debugmsg('Did not receive confirmation of messgage sent');
            return false;
        }

        $this->debugmsg("Message sent");

        return true;

    }

    public function _exec($cmd, &$out = null)
    {
        $desc = array(
            1 => array("pipe", "w"),
            2 => array("pipe", "w")
        );

        $proc = proc_open($cmd, $desc, $pipes);

        $ret = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $retVal = proc_close($proc);

        if (func_num_args() == 2) $out = array($ret, $err);
        return $retVal;
    }

}