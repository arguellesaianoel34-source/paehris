<div class="page-bar">
    <?php
    if ($this->uri->segment(1) == 'forms') {
        echo create_form_breadcrumb();
    } else {
        echo create_breadcrumb_modules();
    }
    ?>
</div>
<div class="page-content-wrapper">
    <div class="page-content  animated fadeInUp fast">
