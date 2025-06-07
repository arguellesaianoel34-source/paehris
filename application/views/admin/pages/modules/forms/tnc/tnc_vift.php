<div class="tab-pane fade in " id="tnc_vift">
    <style type="text/css">
        .table-numbered tbody {
            counter-reset: row-counter;
        }
        .table-numbered tbody tr td:first-child:not(.note):before {
            content: counter(row-counter) ".";
            counter-increment: row-counter; /* Increment the counter */
            position: absolute;
            left: 5px;
        }
        .table-numbered tbody tr td:first-child {
            position: relative;
            padding-left: 20px;
            text-align: justify;
            text-justify: inter-word;
        }

        .selectlist td[colspan] {
            width: auto; /* Let it adapt dynamically */
        }

        table.selectlist {
            width: 100%;
            table-layout: fixed; /* Ensures equal distribution of columns */
        }

        .selectlist th,
        .selectlist td {
            text-align: center;
            padding: 8px;
        }
    </style>
    <div class="portlet light">
        <div class="portlet-title tabbable-line">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#form_vift_aci" data-toggle="tab"> AC Insulation</a>
                </li>
                <li class="">
                    <a href="#form_vift_dci" data-toggle="tab"> DC Insulation </a>
                </li>
                <li class="">
                    <a href="#form_vift_pics" data-toggle="tab"> VIFT Pictures </a>
                </li>
            </ul>
        </div>
        <div class="portlet-body">
            <form id="frm_tnc_vift" action="<?php echo base_url().'forms/tncsaveinsulation'; ?>" method="post" data-title="Insulation" data-text="Save Insulation Test data?">
                <div class="tab-content">
                    <?php $this->load->view('admin/pages/modules/forms/tnc/vift_aci'); ?>
                    <?php $this->load->view('admin/pages/modules/forms/tnc/vift_dci'); ?>
                    <?php $this->load->view('admin/pages/modules/forms/tnc/vift_pics'); ?>
                </div>
                <div class="portlet-footer margin-top-15">
                    <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>