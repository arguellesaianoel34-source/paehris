<?php

?>
<div class="tab-pane fade in active" id="form_vift_dci">
    <style type="text/css">
        .components tbody tr td:first-child:not(.note):before {
            content: "•";
            font-size: 150%;
            position: absolute;
            left: 5px;
        }
        .components tbody tr td:first-child {
            position: relative;
            padding-left: 20px;
            text-align: justify;
            text-justify: inter-word;
        }

        #form_vift_dci .table.table-testing th, #form_vift_dci .table.table-testing td {
            text-align: center;
        }
    </style>
    <div class="portlet light">
        <div class="portlet-body">
            <div class="row">
                <div class="col-md-1">
                    <ul class="nav nav-tabs tabs-left">
                        <?php
                        for ($i = 0; $i < $inverters; $i++) {
                            ?>
                            <li class="<?php echo $i == 0 ? 'active' : ''?>">
                                <a href="#vift_<?php echo $i+1;?>_dci" data-toggle="tab"> <?php echo $i+1;?> </a>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
                <div class="col-md-11">
                    <div class="tab-content">
                        <?php
                        for ($i = 0; $i < $inverters; $i++) {
                            $st_data = array('inverter' => $i+1);
                            $this->load->view('admin/pages/modules/forms/tnc/vift_dci_frm', $st_data, FALSE);
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>