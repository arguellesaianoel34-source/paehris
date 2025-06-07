<?php
$data = array();
if (isset($appid)) {
    $data['appid'] = $appid;
    $data['inverters'] = $invertercount;
    $data['tncid'] = $tncid;
    $directory = FCPATH . 'uploads/attachments/tnc/'.$appid;
    $map = directory_map($directory, false, true);
    $imgs = array();

    //CONVERT MAPPED TNC IMAGES INTO MODULAR 
    if ($map && count($map) > 0) {
        foreach ($map as $file) {
            // Remove the file extension
            $basename = pathinfo($file, PATHINFO_FILENAME);

            // Split the filename by underscores
            $parts = explode('_', $basename);

            // Initialize reference to the main array
            $ref = &$imgs;

            // Process each part
            for ($i = 0; $i < count($parts); $i++) {
                $isLastPart = ($i === count($parts) - 1); // Check if this is the last part

                // Match alphabetic key and optional numeric index
                if ($matches = splitLettersNumbers($parts[$i])) {
                    $key = $matches[1]; // Alphabetic key
                    $index = isset($matches[2]) && $matches[2] !== '' ? (int)$matches[2] : null; // Numeric index if present

                    // If it's the last part and the key length is less than 2, keep it unchanged
                    if ($isLastPart && strlen($key) < 3) {
                        $key = $parts[$i]; // Keep the entire last part as the key (do not split)
                        $index = null; // No separate index
                    }

                    // Create nested structure
                    if ($index !== null) {
                        if (!isset($ref[$key])) {
                            $ref[$key] = [];
                        }
                        if (!isset($ref[$key][$index])) {
                            $ref[$key][$index] = [];
                        }
                        $ref = &$ref[$key][$index]; // Move reference
                    } else {
                        if (!isset($ref[$key])) {
                            $ref[$key] = [];
                        }
                        $ref = &$ref[$key]; // Move reference
                    }
                }
            }

            // Assign the filename to the last nested level
            $ref = base_url() . 'uploads/attachments/tnc/'.$appid.'/' . $file;
        }
    }
    $data['imgs'] = $imgs;
}
?>

<div class="col-md-3 padding-right-0">
    <input type="hidden" id="tncid" value="<?php echo $tncid ?? ''; ?>" >
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption bold">Pages</div>
        </div>
        <div class="portlet-body">
            <ul class="nav nav-tabs tabs-left" id="main-nav">
                <li class="active">
                    <a href="#tnc_checklist" data-toggle="tab"> PV SYSTEM INSPECTION CHECKLIST </a>
                </li>
                <li>
                    <a href="#tnc_stringtest" data-toggle="tab"> PV MODULE STRING TEST </a>
                </li>
                <li>
                    <a href="#tnc_strtestpics" data-toggle="tab"> PICTURES </a>
                </li>
                <li>
                    <a href="#tnc_ctt" data-toggle="tab"> CONTINUITY TEST </a>
                </li>
                <li>
                    <a href="#tnc_vift" data-toggle="tab"> VISUAL INSPECTION AND FUNCTION TEST </a>
                </li>
                <li>
                    <a href="#tnc_tqtest" data-toggle="tab"> TORQUE TEST </a>
                </li>
                <li>
                    <a href="#tnc_thermal" data-toggle="tab"> THERMAL SCANNING </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="col-md-9 padding-left-0">
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption bold" id="form-title">< Page Title here... ></div>
        </div>
        <div class="portlet-body">
            <div id="main_content" class="tab-content">
                <?php $this->load->view('admin/pages/modules/forms/tnc/tnc_checklist',$data); ?>
                <?php $this->load->view('admin/pages/modules/forms/tnc/tnc_stringtest',$data); ?>
                <?php $this->load->view('admin/pages/modules/forms/tnc/tnc_strtestpics',$data); ?>
                <?php $this->load->view('admin/pages/modules/forms/tnc/tnc_ctt',$data); ?>
                <?php $this->load->view('admin/pages/modules/forms/tnc/tnc_vift',$data); ?>
                <?php $this->load->view('admin/pages/modules/forms/tnc/tnc_tqtest',$data); ?>
                <?php $this->load->view('admin/pages/modules/forms/tnc/tnc_thermal',$data); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    PECO.handleriCheckForm($('#form_container',document),'minimal','blue');
    FORMS.tnc('mb');
</script>