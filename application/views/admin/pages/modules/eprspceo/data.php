<?php

$view = array(
    107 => 'rfq',
    110 => 'po',
    114 => 'rfp'
);

$this->load->view('admin/pages/modules/eprs/'.$view[$stageid],$this->_ci_cached_vars);
/*echo "<pre>";
print_r ($this->_ci_cached_vars);
echo "</pre>";*/




?>