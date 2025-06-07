<?php
?>
<div class="tab-pane fade in " id="tnc_thermal">
    <div class="portlet light">
        <div class="portlet-title tabbable-line">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#thermal_frm" data-toggle="tab"> Thermal Scan Form</a>
                </li>
                <li class="">
                    <a href="#thermal_pics" data-toggle="tab"> Thermal Scan Pictures </a>
                </li>
            </ul>
        </div>
        <div class="portlet-body">

            <div class="tab-content">
                <?php $this->load->view('admin/pages/modules/forms/tnc/thermal_frm'); ?>
                <?php $this->load->view('admin/pages/modules/forms/tnc/thermal_pics'); ?>
            </div>

        </div>
    </div>

</div>
