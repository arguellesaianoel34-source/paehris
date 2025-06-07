<style>
    #backToTop {
        display: none;
        position: fixed !important;
        bottom: 50px;
        right: 30px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 50% !important;
        cursor: pointer;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
        z-index: 9999; /* Ensures it stays above other elements */
    }

    #backToTop:hover {
        background: #0056b3;
        transform: scale(1.1);
    }

    backToTop.show {
        display: block;
    }
</style>
<?php if (isset($formtitle)) {
    $form_qry = $this->db->select('')
        ->from('system_forms_main AS form')
        ->where(array('hashcode' => $dataid, 'status' => 1))
        ->get()->row();

    if ($form_qry) {
        if (file_exists(FCPATH . 'application/views/admin/pages/modules/forms/'.$form_qry->pagefile.'.php')) {
            echo '<div class="form-content">';
            $this->load->view('admin/pages/modules/forms/'.$form_qry->pagefile, $form_qry);
            //echo '<a id="backToTop" class="btn btn-primary"><i class="fa fa-arrow-up"></i> </a> <!-- Up arrow symbol -->';
            echo '</div>';
        }
    }
    ?>

    <script type="text/javascript">
        var bc_last = $('.page-breadcrumb:last-child b.text-info',document);
        bc_last.text('<?php echo $formtitle; ?>')
        $('.page-content').after('<a id="backToTop" class="btn btn-primary"><i class="fa fa-arrow-up"></i> </a>');

        var backToTop = $("#backToTop",document);
        // Show button when scrolling down 100px
        $('.page-content').scroll(function () {
            //console.log('Scrolling: ' + $(this).scrollTop())
            if ($(this).scrollTop() > 100) {
                backToTop.addClass('show');
            } else {
                backToTop.removeClass('show');
            }
        });

        // Scroll to top when button is clicked
        backToTop.click(function () {
            $('.page-content').animate({ scrollTop: 0 }, "fast");
        });
    </script>
<?php } else {
    redirect(base_url() . 'module/cfe21c6800c88f06d7d0683b1535821c75c954ad/list', 'refresh');
}?>