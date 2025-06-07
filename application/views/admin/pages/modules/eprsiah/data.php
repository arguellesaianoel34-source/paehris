<?php

$view = array(
    105 => 'rfq',
    109 => 'po',
    112 => 'rfp'
);

$this->load->view('admin/pages/modules/eprs/'.$view[$stageid],$this->_ci_cached_vars);
/*echo "<pre>";
print_r ($this->_ci_cached_vars);
echo "</pre>";*/




?>