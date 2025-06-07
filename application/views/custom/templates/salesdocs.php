<?php
$data = $this->_ci_cached_vars;
if ($doctype == 3433) {
    $ishybrid = strpos(strtolower($data['app']->systemsizename),'hybrid') !== false;
    $hasbattery = strpos(strtolower($data['app']->systemsizename),'battery') !== false;

    if ($ishybrid || $hasbattery) {
        echo $this->load->view('custom/templates/sales/proposalhybrid', $data, FALSE);
    } else {
        echo $this->load->view('custom/templates/sales/proposal', $data, FALSE);
    }
}

if ($doctype == 3435) {
    echo $this->load->view('custom/templates/sales/creditcheckform', $data, FALSE);
}

if ($doctype == 3434) {
    echo $this->load->view('custom/templates/sales/contract', $data, FALSE);
}