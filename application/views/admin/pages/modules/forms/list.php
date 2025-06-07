<h3 class=" font-red-flamingo bold "><i class="fa <?php echo $pageicon; ?>"></i> <?php echo $pagetitle; ?>
    <span class="pull-right small"><?php echo $pagedesc; ?></span>
</h3>
<hr>
<div class="row">
    <div class="col-md-12">
        <div class="row" id="">
            <?php
            $qry_forms = $this->db->select()->from('system_forms_main')
                ->where(array('status' => 1,'parent IS NULL' => null))
                ->get();

            if ($qry_forms->num_rows() > 0) {
                foreach ($qry_forms->result() AS $row) {
                    $link = base_url('module/cfe21c6800c88f06d7d0683b1535821c75c954ad/form/' . $row->hashcode);

                    echo '<div class="col-lg-3 col-md-4 col-xs-12" id="">';
                    echo '<div class="mt-element-ribbon bg-grey-steel">';
                    echo '<div class="ribbon ribbon-shadow ribbon-color-success uppercase">';
                    echo '<a target="_blank " href="' . $link . '" class="btn btn-default btn-xs inline pull-right "><i class="fa fa-file-pdf-o fa-fw"></i> ' . $row->name . '</a>';
                    echo '</div>';
                    echo '<p class="ribbon-content small" style="padding-top: 8px !important; padding-bottom: 5px !important;">';
                    echo $row->desc;
                    echo '</p>';
                    echo '<p class="ribbon-content small font-blue" style="min-height: 50px; padding-top: 8px !important; padding-bottom: 5px !important;">';

                    echo '</p>';
                    echo '</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
</div>