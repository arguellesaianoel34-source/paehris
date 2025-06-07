<?php

?>
<div class="tab-pane fade in " id="tnc_tqtest">
    <div class="portlet light">
        <div class="portlet-title tabbable-line">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tqt_frm" data-toggle="tab"> Torque Test Form</a>
                </li>
                <li class="">
                    <a href="#tqt_pics" data-toggle="tab"> Torque Test Pictures </a>
                </li>
            </ul>
        </div>
        <div class="portlet-body">

                <div class="tab-content">
                    <?php $this->load->view('admin/pages/modules/forms/tnc/tqt_frm'); ?>
                    <?php $this->load->view('admin/pages/modules/forms/tnc/tqt_pics'); ?>
                </div>

        </div>
    </div>

</div>
