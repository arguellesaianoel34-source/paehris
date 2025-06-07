<?php

$view = array(
    103 => 'prf',
    106 => 'rfq',
    113 => 'rfp'
);

$this->load->view('admin/pages/modules/eprs/'.$view[$stageid],$this->_ci_cached_vars);
/*echo "<pre>";
print_r ($this->_ci_cached_vars);
echo "</pre>";*/




?>