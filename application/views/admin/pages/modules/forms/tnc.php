<?php
/*$data = array();
$directory = FCPATH.'uploads/attachments/tnc/test';
$map = directory_map($directory,false,true);
$imgs = array();

function splitLettersNumbers($input) {
    // Check if the input contains only letters
    if (ctype_alpha($input)) {
        return [$input,$input];
    }

    // Check if the input starts with letters followed by numbers
    if (preg_match('/^([a-zA-Z]+)(\d+)$/', $input, $matches)) {
        return $matches;
    }

    // If none of the above, return the input as a single element
    return [$input,$input];
}

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
        $ref = base_url() . 'uploads/attachments/tnc/test/' .$file;
    }
}
$data['imgs'] = $imgs;*/
?>

<link href="<?php echo base_url() ;?>assets/global/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url() ;?>assets/admin/pages/css/portfolio.css" rel="stylesheet" type="text/css"/>

<!-- END PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-editable/inputs-ext/address/address.css"/>

<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap-fileinput/themes/explorer/theme.css" media="all" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url() ?>assets/global/plugins/cubeportfolio/css/cubeportfolio.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url() ?>assets/pages/css/portfolio.min.css" rel="stylesheet" type="text/css" />

<style type="text/css">
    /*.nav-tabs.tabs-left {
        flex-wrap: nowrap;
        overflow-x: hidden;
        overflow-y:auto;
        min-height: 50vh;
        max-height: 75vh;
    }*/

    .nav-tabs:not(.tabs-left) {
        display: flex !important;
        flex-direction: row;
        flex-wrap: nowrap !important;
        overflow-x: auto;
        overflow-y: hidden;
        width: 100%;
    }

    .nav-tabs:not(.tabs-left) > li {
        white-space: nowrap;
    }

    /*.tab-pane ul {
        padding-left: 0;
        list-style-type: disc;
    }*/

    .tab-pane ul>hr,.tab-pane ol>li>hr  {
        margin: 10px 0 !important;
    }

    .tab-pane input.inline {
        text-align: center !important;
    }

    .testing-wrapper {
        display: flex;
        align-items: center;
        width: 100%;
    }

    .testing-unit {
        font-size: 1.2em;
        margin-left: 5px;
    }
</style>
<!--
Note: Use bloodhound to lookup application. On click, lookup appid from tnc table
If form responses exists:
 Load all data to inputs,
 Lock inputs except app name,
 Change "create" button to Delete.
 Load all related pages and responses.

If application does not have a form response:
 Create new form data,
 Leave inputs blank except for inputs with related data to the application,
 Load form pages based on build type.
-->
<div class="well">
    <form id="frm_tnc" action="<?php echo base_url(); ?>forms/tnccreate" method="post">
        <div class="row" style="display: flex;">
            <div class="col-md-2">
                Application Number <span class="required"></span>
                <!-- Bloodhound lookup -->
                <input class="form-control" id="tnc_app_number" placeholder="Customer number" required />
                <input type="hidden" name="appid" id="tnc_appid">
            </div>
            <div class="col-md-8">
                <!-- Auto-load Name from App Details on select of bloodhound. Can be manually changed in-case of a different location is defined. -->
                Project Name <span class="required"></span>
                <input class="form-control" name="projectname" id="tnc_project_name" placeholder="Customer/Project Name" required />
            </div>
            <div class="col-md-2">
                Build Type <span class="required"></span>
                <!-- Select2 code = "APPBUILDTYPE" -->
                <input class="form-control" name="buildtype" id="tnc_build_type" placeholder="Project Build Type..." required />
            </div>
        </div>
        <div class="row">
            <div class="col-md-10">
                Location <span class="required"></span>
                <!-- Auto-load Address from App Details on select of bloodhound. Can be manually changed in-case of a different location is defined. -->
                <input class="form-control" name="location" id="tnc_project_location" placeholder="Build Location" required />
            </div>
            <div class="col-md-2">
                Date Conducted <span class="required"></span>
                <!-- Auto-load Address from App Details on select of bloodhound. Can be manually changed in-case of a different location is defined. -->
                <input class="form-control" name="dateconducted" id="tnc_project_date" placeholder="Date Conducted" required />
            </div>
        </div>
        <hr>
        <span class="bold">Parties Involved in Inspection</span>
        <div class="row">
            <div class="col-md-4">
                Company <span class="required"></span>
                <input class="form-control" name="company" id="tnc_company" value="PA Energy Inc." placeholder="Customer name/number" required />
            </div>
            <div class="col-md-2">
                Acronym <span class="required"></span>
                <input class="form-control" name="companyacronym" id="tnc_company_acronym" value="PAEI" placeholder="Customer name/number" required />
            </div>
            <div class="col-md-4">
                Partner
                <div class="input-group">
                    <div class="input-group-addon">
                        <input type="checkbox" class="icheck optional" id="partner" data-for="tnc_partner">
                    </div>
                    <input class="form-control" name="partner" id="tnc_partner" placeholder="Commissioning Partner..." required disabled />
                </div>
            </div>
            <div class="col-md-2">
                Acronym <span class="required"></span>
                <input class="form-control" name="partneracronym" id="tnc_partner_acronym" placeholder="Partner's Acronym..." required disabled />
            </div>
            <div class="col-md-4">
                Client <span class="required"></span>
                <input class="form-control" name="client" id="tnc_client" placeholder="Client Name..." required />
            </div>
            <div class="col-md-2">
                Acronym <small>(For Mega Builds)</small>
                <div class="input-group">
                    <div class="input-group-addon">
                        <input type="checkbox" class="icheck optional" id="partner" data-for="tnc_client_acronym">
                    </div>
                    <input class="form-control" name="clientacronym" id="tnc_client_acronym" placeholder="Client Acronym..." disabled />
                </div>
            </div>
            <div class="col-md-4">
                Client Holdings Company
                <div class="input-group">
                    <div class="input-group-addon">
                        <input type="checkbox" class="icheck optional" id="holdings" data-for="tnc_client_holdings">
                    </div>
                    <input class="form-control" name="holdings" id="tnc_client_holdings" placeholder="Client's Primary Holdings..." required disabled />
                </div>
            </div>
            <div class="col-md-2">
                Inverter Count <span class="required"></span>
                <input class="form-control" type="number" name="invertercount" id="tnc_inverter_count" placeholder="# of Inverters Installed" required />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="btn-group margin-top-15">
                    <button type="button" class="btn btn-success" id="btn_print_tnc" style="display: none;" disabled><i class="fa fa-print"></i> Print</button>
                </div>
            </div>
            <div class="col-md-6">
                <div class="btn-group margin-top-15 pull-right">
                    <button type="submit" class="btn btn-primary" id="btn_create_tnc"><i class="fa fa-check-square-o"></i> Create</button>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="row" id="form_container">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption"><h3 class="bold">Testing and Commissioning</h3></div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                <div class="row" id="tnc_form_container">
                    <h4 class="text-align-center"><i class="fa fa-warning text-warning"></i> Create or Load form from above.</h4>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo file_versioning('assets/pages/forms/main.js'); ?>"></script>
<script type="text/javascript">
    FORMS.tnc();
</script>