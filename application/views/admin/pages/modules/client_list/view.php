<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Helper function for safe array access
if (!function_exists('safe')) {
    function safe($arr, $key, $default = '')
    {
        return isset($arr[$key]) && $arr[$key] !== null ? $arr[$key] : $default;
    }
}

// Load the model
$CI =& get_instance();
$CI->load->model('model_customerprofile', 'client', TRUE);

$details = $CI->client->get_customer_details($dataid);

$app_name = safe($details, 'app_name', 'Customer');
// get the first two words of the $app_name
$app_name = implode(' ', array_slice(explode(' ', $app_name), 0, 2));
$app_avatar_color = safe($details, 'is_corporate', 'NO') === 'YES' ? 'blue' : 'orange';
// echo '<pre>';
// print_r($details); // Debugging line to check the details array

// exit;
?>

<!-- Customer Profile - Modernized Bootstrap 3 Version -->
<style>
    .profile-hero {
        background: linear-gradient(90deg, #0A2342 10%, transparent 100%);
        color: #fff;
        border-radius: 0 0 24px 24px;
        padding: 2.5rem 1.5rem 2rem 1.5rem;
        margin-bottom: 2.5rem;
        margin-top: 20px;
        display: flex;
        align-items: center;
        gap: 2.5rem;
        flex-wrap: wrap;
    }

    .profile-hero .avatar {
        width: 110px;
        height: 110px;
        box-shadow: 0 2px 12px rgba(10, 35, 66, 0.13);
        object-fit: cover;
        background: #eaf2f8;
    }

    .profile-hero .profile-info {
        flex: 1 1 200px;
        min-width: 200px;
    }

    .profile-hero h1 {
        font-weight: 700;
        font-size: 2.1em;
        margin-bottom: 0.2em;
        color: #fff;
    }

    .profile-hero .status-badge {
        font-size: 0.5em;
        padding: 0.2em 1em;
        border-radius: 16px;
        background: #5cb85c;
        color: #fff;
        margin-left: 10px;
        vertical-align: middle;
        box-shadow: 0 2px 8px rgba(92, 184, 92, 0.10);
    }

    .profile-hero .meta {
        font-size: 1.1em;
        color: #eaf2f8;
        margin-top: 0.5em;
    }

    .card {
        border: none;
        border-radius: 18px;
        box-shadow: 0 4px 18px rgba(10, 35, 66, 0.10);
        margin-bottom: 28px;
        background: #fff;
        overflow: hidden;

        transition: box-shadow 0.18s;
    }

    .card:hover {
        box-shadow: 0 8px 32px rgba(10, 35, 66, 0.13);
    }

    .card-header {
        background: #eaf2f8;
        font-weight: 600;
        color: #0A2342;
        border-bottom: 2px solid #d4e6f1;
        font-size: 1.13em;
        padding: 1em 1.3em;
    }

    .card-body {
        padding: 1.3em 1.5em 1.2em 1.5em;
    }

    .list-group-item strong {
        color: #2471A3;
    }

    .list-group-item .glyphicon {
        color: #F39C12;
        margin-right: 6px;
    }

    .action-buttons .btn {
        margin-right: 10px;
        margin-bottom: 5px;
    }

    .notes-section textarea {
        border-radius: 12px;
        min-height: 90px;
        resize: vertical;
        box-shadow: 0 2px 8px rgba(36, 113, 163, 0.07);
        border: 1.5px solid #eaf2f8;
        transition: border 0.15s;
    }

    .notes-section textarea:focus {
        border: 1.5px solid #2471A3;
        outline: none;
    }

    .notes-section .btn-warning {
        border-radius: 50px;
        font-weight: 600;
        padding: 7px 22px;
        box-shadow: 0 2px 8px rgba(243, 156, 18, 0.10);
    }

    .table-responsive {
        border-radius: 12px;
        background: #f8fafc;
        padding: 0.5em 0.5em 0 0.5em;
    }

    .chart-placeholder {
        height: 220px;
        background: linear-gradient(120deg, #eaf2f8 60%, #f8fafc 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        border-radius: 12px;
        font-style: italic;
        font-size: 1.1em;
        box-shadow: 0 2px 8px rgba(36, 113, 163, 0.07);
    }

    .badge-success,
    .badge-warning,
    .badge-default {
        color: #fff;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.97em;
        padding: 0.4em 1em;
    }

    .badge-success {
        background: #5cb85c !important;
    }

    .badge-warning {
        background: #F39C12 !important;
    }

    .badge-default {
        background: #95a5a6 !important;
    }

    .sticky-card {
        position: -webkit-sticky;
        position: sticky;
        top: 24px;
        z-index: 2;
    }

    @media (max-width: 991px) {
        .profile-hero {
            flex-direction: column;
            align-items: flex-start;
            gap: 1.5rem;
        }

        .profile-hero .avatar {
            width: 80px;
            height: 80px;
        }
    }

    @media (max-width: 767px) {
        .profile-hero {
            padding: 1.2rem 0.5rem 1.2rem 0.5rem;
        }

        .card-header,
        .card-body {
            padding-left: 1em;
            padding-right: 1em;
        }

        .sticky-card {
            position: static;
            top: auto;
        }
    }

    .d-flex {
        display: flex;
    }

    .flex-grw-1 {
        flex-grow: 1;
    }

    .w-25 {
        width: 25%;
    }

    .w-30 {
        width: 30%;
    }

    .w-50 {
        width: 50%;
    }

    .w-75 {
        width: 75%;
    }

    .w-80 {
        width: 80%;
    }

    .w-100 {
        width: 100%;
    }

    .px-1 {
        padding-left: 0.25rem;
        padding-right: 0.25rem;
    }

    .px-2 {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }

    #chatbox {
        position: fixed !important;
        bottom: 0 !important;
        right: 30px !important;
        width: 340px;
        z-index: 99999 !important;
        box-shadow: 0 4px 18px rgba(10, 35, 66, 0.18);
        border-radius: 12px 12px 0 0;
        background: #fff;
        border: 1.5px solid #eaf2f8;
        transition: transform 0.35s cubic-bezier(.4, 0, .2, 1), opacity 0.25s;
        transform: translateY(100%);
        opacity: 0;
        pointer-events: none;
    }

    #chatbox.active {
        transform: translateY(0);
        opacity: 1;
        pointer-events: auto;
    }

    .dropdown-menu.dropdown-menu-right[aria-labelledby="projectDropdown"] {
        z-index: 300 !important;
    }
</style>
<div class="container-fluid">
    <div class="profile-hero" style="position: relative;">
        <img class="avatar" src="<?php echo base_url(); ?>avatar/generate/<?= urlencode($app_name) ?>/<?= $app_avatar_color ?>" alt="Avatar">
        <div class="profile-info">
            <h1><?= htmlspecialchars(safe($details, 'app_name', 'Customer')) ?>
                <span class="status-badge">
                    <?= safe($details, 'status', 'Active') ?>
                </span>
            </h1>
            <div class="meta">
                <span class="glyphicon glyphicon-map-marker"></span> <?= htmlspecialchars(safe($details, 'address')) ?><br>
                <span class="glyphicon glyphicon-envelope"></span> <?= htmlspecialchars(safe($details, 'contact_email', '')) ?>
                &nbsp; <span class="glyphicon glyphicon-earphone"></span> <?= htmlspecialchars(safe($details, 'contact_phone')) ?>


                <?php
                $geo = safe($details, 'geo_location');
                $lat = safe($details, 'latitude');
                $lon = safe($details, 'longitude');
                if (!$geo && $lat && $lon) {
                    $geo = $lat . ',' . $lon;
                }
                ?>
                <?php if ($geo && $geo !== 'N/A'): ?>
                    <button class="btn btn-xs btn-info" style="margin-left:8px;" data-toggle="modal" data-target="#geoMapModal">
                        <span class="glyphicon glyphicon-map-marker"></span> View Map
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <div style="margin-left:auto; position: absolute; top: 10px; right: 10px; display: flex; gap: 8px   ;">
            <button class="btn btn-default" id="smsBtn"><span class="glyphicon glyphicon-comment"></span> SMS</button>
            <button class="btn btn-default" id="callBtn" data-toggle="modal" data-target="#callDialerModal"><span class="glyphicon glyphicon-earphone"></span> Call</button>
            <button class="btn btn-primary"><span class="glyphicon glyphicon-edit"></span> Edit Profile</button>
            <button class="btn btn-danger"><span class="glyphicon glyphicon-plus"></span> New Sale</button>
            <a href="/module/<?= $hashcode ?>/" class="btn btn-default"><span class="glyphicon glyphicon-file"></span> Back</a>
        </div>
        <!-- Project Dropdown Menu (bottom right of header) -->
        <!-- <div style="position: absolute; bottom: 18px; right: 10px; z-index: 10;">
            <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="projectDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="min-width: 240px; font-weight:600;">
                    <span id="selectedProject">Project 1: BKL27, LOT92, Lizandra</span>
                    <span class="caret"></span>
                    
                </button>
                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="projectDropdown" style="min-width: 240px;">
                    <li><a href="#" onclick="selectProject('Project 1: BKL27, LOT92, Lizandra'); return false;">Project 1: BKL27, LOT92, Lizandra</a></li>
                    <li><a href="#" onclick="selectProject('Project 2: BKL28, LOT93, Lizandra'); return false;">Project 2: BKL28, LOT93, Lizandra</a></li>
                    <li><a href="#" onclick="selectProject('Project 3: BKL29, LOT94, Lizandra'); return false;">Project 3: BKL29, LOT94, Lizandra</a></li>
                    <li><a href="#" onclick="selectProject('Project 4: BKL30, LOT95, Lizandra'); return false;">Project 4: BKL30, LOT95, Lizandra</a></li>
                </ul>
            </div>
        </div> -->
    </div>
    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-4 col-md-5 col-sm-12 sticky-card" >

            <!-- BEGIN: Solar System, Inverter & System Information (Combined) -->
            <div class="card">
                <div class="card-header"><span class="glyphicon glyphicon-flash"></span> Solar System, Inverter & System Information</div>
                <ul class="list-group list-group-flush" style="margin-bottom:0;">
                    <li class="list-group-item d-flex">
                        <div class="w-50">System Power / Size</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'system_power', safe($details, 'system_size', '5.0 kWp'))) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">Number of Panels</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'number_of_panels', 'N/A')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">Rate Class / System Type</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'system_type', safe($details, 'rate_class', 'Grid Tied'))) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">Panel Type</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'panel_type', 'N/A')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">Roof Inclination</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'roof_inclination', 'N/A')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">Inspection Date</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'inspection_date', 'N/A')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">System Size Remarks</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'system_size_remarks', 'N/A')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">Energization Date</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'energization_date', '2024-01-15')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">Plan Type</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'plan_type', 'Net Metering')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">Inverter Brand</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'inverter_brand', 'Huawei')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">Inverter Type</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'inverter_type', 'String')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">Inverter Size</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'inverter_size', '5kW')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">Inverter Quantity</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'inverter_quantity', '1')) ?></strong></div>
                    </li>
                </ul>
            </div>
            <!-- END: Solar System, Inverter & System Information -->
            <!-- Materials Used (moved under System Information) -->
            <div class="card">
                <div class="card-header"><span class="glyphicon glyphicon-wrench"></span> Materials Used</div>
                <div class="card-body" style="padding:0 0;">
                    <div class="table-responsive">
                        <table class="table table-hover table-condensed" style="margin-bottom:0;">
                            <thead>
                                <tr>
                                    <th style="width:40%;">Item</th>
                                    <th style="width:15%;" class="text-center">Qty</th>
                                    <th style="width:15%;" class="text-center">Unit</th>
                                    <th style="width:20%;" class="text-center">Status</th>
                                    <th style="width:10%;" class="text-center">#</th>
                                </tr>
                            </thead>
                            <tbody id="materials-used-body">
                                <tr>
                                    <td>Solar Panel 550W</td>
                                    <td class="text-center">12</td>
                                    <td class="text-center">pcs</td>
                                    <td class="text-center"><span class="badge badge-success">Installed</span></td>
                                    <td class="text-center"><a href="#" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-eye-open"></span></a></td>
                                </tr>
                                <tr>
                                    <td>Mounting Kit Set</td>
                                    <td class="text-center">12</td>
                                    <td class="text-center">set</td>
                                    <td class="text-center"><span class="badge badge-success">Installed</span></td>
                                    <td class="text-center"><a href="#" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-eye-open"></span></a></td>
                                </tr>
                                <tr>
                                    <td>DC Cable 4mm²</td>
                                    <td class="text-center">50</td>
                                    <td class="text-center">m</td>
                                    <td class="text-center"><span class="badge badge-default">Used</span></td>
                                    <td class="text-center"><a href="#" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-eye-open"></span></a></td>
                                </tr>
                                <tr>
                                    <td>Transformer</td>
                                    <td class="text-center">1</td>
                                    <td class="text-center">unit</td>
                                    <td class="text-center"><span class="badge badge-warning">Pending</span></td>
                                    <td class="text-center"><a href="#" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-eye-open"></span></a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Middle Column -->
        <div class="col-lg-5 col-md-7 col-sm-12">
            <!-- Tabs for Default, Notes, Billing & Payments, Documents -->
            <ul class="nav nav-tabs" role="tablist" style="margin-bottom:18px;">
                <li class="active"><a href="#defaultTab" role="tab" data-toggle="tab">Default</a></li>
                <li><a href="#notesTab" role="tab" data-toggle="tab">Notes</a></li>
                <li><a href="#billingTab" role="tab" data-toggle="tab">Billing & Payments</a></li>
                <li><a href="#documentsTab" role="tab" data-toggle="tab">Documents</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade in active" id="defaultTab">
                    <!-- Energy Production & Consumption -->
                    <div class="card">
                        <div class="card-header"><span class="glyphicon glyphicon-stats"></span> Energy Production & Consumption</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Monthly Energy Production (kWh)</h6>
                                    <div class="chart-placeholder">Chart Placeholder</div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Monthly Energy Consumption (kWh)</h6>
                                    <div class="chart-placeholder">Chart Placeholder</div>
                                </div>
                            </div>
                            <div class="text-center" style="margin-top:15px;">
                                <p>Total Energy Generated to Date: <strong>12,345 kWh</strong></p>
                            </div>
                        </div>
                    </div>
                    <!-- Finance Details -->
                    <div class="card">
                        <div class="card-header"><span class="glyphicon glyphicon-piggy-bank"></span> Finance Details</div>
                        <div class="card-body" style="padding-top:10px;">
                            <form id="finance-details-form" class="form-horizontal" onsubmit="return false;">
                                <div class="form-group" style="margin-bottom:12px;">
                                    <label class="col-sm-5 control-label" style="padding-top:4px;">Downpayment Amount</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control input-sm" name="downpayment_amount" value="<?= htmlspecialchars(safe($details, 'downpayment_amount', '₱ 50,000.00')) ?>" />
                                    </div>
                                </div>
                                <div class="form-group" style="margin-bottom:12px;">
                                    <label class="col-sm-5 control-label" style="padding-top:4px;">Lease Amount (Months)</label>
                                    <div class="col-sm-4">
                                        <input type="number" min="0" class="form-control input-sm" name="lease_months" value="<?= htmlspecialchars(safe($details, 'lease_months', '24')) ?>" />
                                    </div>
                                    <div class="col-sm-3" style="padding-top:5px;">
                                        <span class="text-muted">months</span>
                                    </div>
                                </div>
                                <div class="text-right" style="margin-top:5px;">
                                    <button class="btn btn-primary btn-sm" type="button" onclick="saveFinanceDetails()"><span class="glyphicon glyphicon-floppy-disk"></span> Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Maintenance & Service History -->
                    <div class="card">
                        <div class="card-header">
                            <span class="glyphicon glyphicon-time"></span> Maintenance & Service History
                            <button class="btn btn-xs btn-success pull-right" id="showMaintenanceModal" style="margin-left:10px;" data-toggle="modal" data-target="#maintenanceModal">
                                <span class="glyphicon glyphicon-plus"></span> Create
                            </button>
                        </div>
                        <div class="card-body" style="padding: 0 0;">
                            <div class="table-responsive">
                                <table class="table table-hover table-condensed">
                                    <thead>
                                        <tr>
                                            <th>Service ID</th>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>SRV-00451</td>
                                            <td>2024-01-20</td>
                                            <td>Annual Panel Cleaning & Inspection</td>
                                            <td><span class="badge badge-default">Completed</span></td>
                                        </tr>
                                        <tr>
                                            <td>SRV-00398</td>
                                            <td>2023-05-15</td>
                                            <td>Initial System Installation & Commissioning</td>
                                            <td><span class="badge badge-default">Completed</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="notesTab">
                    <!-- Notes Card moved here -->
                    <div class="card notes-section ">
                        <div class="card-header">
                            <span class="glyphicon glyphicon-pencil"></span> Internal Notes
                        </div>
                        <div class="card-body">
                            <form id="noteInputForm" onsubmit="return false;" style="display:flex; gap:8px;">
                                <textarea class="form-control" rows="4" placeholder="Add notes here..." style="flex:1;"></textarea>
                                <button type="submit" class="btn btn-success" style="height:40px; align-self:flex-end;">Save</button>
                            </form>
                        </div>
                    </div>
                    <!-- Mockup Notes Timeline -->

                    <div class="crm-notes-list">
                        <div class="crm-note-box" style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(36,113,163,0.07); border:1.5px solid #eaf2f8; margin-bottom:18px; padding:16px 18px; position:relative;">
                            <div style="position:absolute; top:16px; right:18px;">
                                <button class="btn btn-xs btn-default" title="Edit"><span class="glyphicon glyphicon-edit"></span></button>
                                <button class="btn btn-xs btn-danger" title="Delete"><span class="glyphicon glyphicon-trash"></span></button>
                            </div>
                            <div style="font-weight:600; color:#2471A3; font-size:1.08em; margin-bottom:4px;">2025-09-01 <span style="color:#95a5a6; font-size:0.95em;">by Admin</span></div>
                            <div style="color:#333;">Client called to request payment extension.</div>
                        </div>
                        <div class="crm-note-box" style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(36,113,163,0.07); border:1.5px solid #eaf2f8; margin-bottom:18px; padding:16px 18px; position:relative;">
                            <div style="position:absolute; top:16px; right:18px;">
                                <button class="btn btn-xs btn-default" title="Edit"><span class="glyphicon glyphicon-edit"></span></button>
                                <button class="btn btn-xs btn-danger" title="Delete"><span class="glyphicon glyphicon-trash"></span></button>
                            </div>
                            <div style="font-weight:600; color:#2471A3; font-size:1.08em; margin-bottom:4px;">2025-08-25 <span style="color:#95a5a6; font-size:0.95em;">by System</span></div>
                            <div style="color:#333;">Sent reminder for overdue bill.</div>
                        </div>
                        <div class="crm-note-box" style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(36,113,163,0.07); border:1.5px solid #eaf2f8; margin-bottom:18px; padding:16px 18px; position:relative;">
                            <div style="position:absolute; top:16px; right:18px;">
                                <button class="btn btn-xs btn-default" title="Edit"><span class="glyphicon glyphicon-edit"></span></button>
                                <button class="btn btn-xs btn-danger" title="Delete"><span class="glyphicon glyphicon-trash"></span></button>
                            </div>
                            <div style="font-weight:600; color:#2471A3; font-size:1.08em; margin-bottom:4px;">2025-08-10 <span style="color:#95a5a6; font-size:0.95em;">by Installer</span></div>
                            <div style="color:#333;">Client confirmed installation completed.</div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="billingTab">
                    <!-- Billing & Payments Card moved here -->
                    <div class="card" style="margin-bottom:18px;">
                        <div class="card-header"><span class="glyphicon glyphicon-stats"></span> Amortization Summary</div>
                        <div class="card-body" style="padding: 1.2em 1.5em;">
                            <div class="row text-center">
                                <div class="col-xs-6 col-sm-3">
                                    <div style="font-size:0.95em; color:#95a5a6;">Total Principal</div>
                                    <div style="font-weight:700; font-size:1.18em; color:#2471A3;">₱ 66,600.00</div>
                                </div>
                                <div class="col-xs-6 col-sm-3">
                                    <div style="font-size:0.95em; color:#95a5a6;">Total Interest</div>
                                    <div style="font-weight:700; font-size:1.18em; color:#2471A3;">₱ 3,360.00</div>
                                </div>
                                <div class="col-xs-6 col-sm-3">
                                    <div style="font-size:0.95em; color:#95a5a6;">Total Amount</div>
                                    <div style="font-weight:700; font-size:1.18em; color:#2471A3;">₱ 69,960.00</div>
                                </div>
                                <div class="col-xs-6 col-sm-3">
                                    <div style="font-size:0.95em; color:#95a5a6;">Paid</div>
                                    <div style="font-weight:700; font-size:1.18em; color:#5cb85c;">₱ 64,160.00</div>
                                </div>
                            </div>
                            <div class="row text-center" style="margin-top:12px;">
                                <div class="col-xs-12 col-sm-3 col-sm-offset-9">
                                    <div style="font-size:0.95em; color:#95a5a6;">Passed Due</div>
                                    <div style="font-weight:700; font-size:1.18em; color:#F39C12;">₱ 5,800.00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><span class="glyphicon glyphicon-list-alt"></span> Billing & Payments
                            <span style="float:right; font-weight:700; font-size:1.15em; color:#2471A3; margin-left:12px;">Principal: ₱ 520,000.00</span>
                        </div>
                        <div class="card-body" style="padding: 0 0;">
                            <!-- Amortization Table -->
                            <div class="table-responsive">
                                <table class="table table-hover table-condensed">
                                    <thead>
                                        <tr>
                                            <th>YEAR</th>
                                            <th>MONTH</th>
                                            <th>DUEDATE</th>
                                            <th>PRINCIPAL</th>
                                            <th>INTEREST</th>
                                            <th>AMOUNT</th>
                                            <th>STATUS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>2025</td>
                                            <td>September</td>
                                            <td>2025-09-25</td>
                                            <td>&#8369; 5,550.00</td>
                                            <td>&#8369; 250.00</td>
                                            <td>&#8369; 5,800.00</td>
                                            <td><span class="badge badge-warning">Passed Due</span></td>
                                        </tr>
                                        <tr>
                                            <td>2025</td>
                                            <td>August</td>
                                            <td>2025-08-25</td>
                                            <td>&#8369; 5,550.00</td>
                                            <td>&#8369; 260.00</td>
                                            <td>&#8369; 5,810.00</td>
                                            <td><span class="badge badge-success">Paid</span></td>
                                        </tr>
                                        <tr>
                                            <td>2025</td>
                                            <td>July</td>
                                            <td>2025-07-25</td>
                                            <td>&#8369; 5,550.00</td>
                                            <td>&#8369; 270.00</td>
                                            <td>&#8369; 5,820.00</td>
                                            <td><span class="badge badge-success">Paid</span></td>
                                        </tr>
                                        <tr>
                                            <td>2025</td>
                                            <td>June</td>
                                            <td>2025-06-25</td>
                                            <td>&#8369; 5,550.00</td>
                                            <td>&#8369; 280.00</td>
                                            <td>&#8369; 5,830.00</td>
                                            <td><span class="badge badge-success">Paid</span></td>
                                        </tr>
                                        <tr>
                                            <td>2025</td>
                                            <td>May</td>
                                            <td>2025-05-25</td>
                                            <td>&#8369; 5,550.00</td>
                                            <td>&#8369; 290.00</td>
                                            <td>&#8369; 5,840.00</td>
                                            <td><span class="badge badge-success">Paid</span></td>
                                        </tr>
                                        <tr>
                                            <td>2025</td>
                                            <td>April</td>
                                            <td>2025-04-25</td>
                                            <td>&#8369; 5,550.00</td>
                                            <td>&#8369; 300.00</td>
                                            <td>&#8369; 5,850.00</td>
                                            <td><span class="badge badge-success">Paid</span></td>
                                        </tr>
                                        <tr>
                                            <td>2025</td>
                                            <td>March</td>
                                            <td>2025-03-25</td>
                                            <td>&#8369; 5,550.00</td>
                                            <td>&#8369; 310.00</td>
                                            <td>&#8369; 5,860.00</td>
                                            <td><span class="badge badge-success">Paid</span></td>
                                        </tr>
                                        <tr>
                                            <td>2025</td>
                                            <td>February</td>
                                            <td>2025-02-25</td>
                                            <td>&#8369; 5,550.00</td>
                                            <td>&#8369; 320.00</td>
                                            <td>&#8369; 5,870.00</td>
                                            <td><span class="badge badge-success">Paid</span></td>
                                        </tr>
                                        <tr>
                                            <td>2025</td>
                                            <td>January</td>
                                            <td>2025-01-25</td>
                                            <td>&#8369; 5,550.00</td>
                                            <td>&#8369; 330.00</td>
                                            <td>&#8369; 5,880.00</td>
                                            <td><span class="badge badge-success">Paid</span></td>
                                        </tr>
                                        <tr>
                                            <td>2024</td>
                                            <td>December</td>
                                            <td>2024-12-25</td>
                                            <td>&#8369; 5,550.00</td>
                                            <td>&#8369; 340.00</td>
                                            <td>&#8369; 5,890.00</td>
                                            <td><span class="badge badge-success">Paid</span></td>
                                        </tr>
                                        <tr>
                                            <td>2024</td>
                                            <td>November</td>
                                            <td>2024-11-25</td>
                                            <td>&#8369; 5,550.00</td>
                                            <td>&#8369; 350.00</td>
                                            <td>&#8369; 5,900.00</td>
                                            <td><span class="badge badge-success">Paid</span></td>
                                        </tr>
                                        <tr>
                                            <td>2024</td>
                                            <td>October</td>
                                            <td>2024-10-25</td>
                                            <td>&#8369; 5,550.00</td>
                                            <td>&#8369; 360.00</td>
                                            <td>&#8369; 5,910.00</td>
                                            <td><span class="badge badge-success">Paid</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="documentsTab">
                    <div class="card">
                        <div class="card-header"><span class="glyphicon glyphicon-folder-open"></span> Documents & Contracts</div>
                        <div class="card-body" style="background:#f8fafc;">
                            <!-- Inline Dropzone Upload -->
                            <form id="documentUploadFormInline" enctype="multipart/form-data" method="post" action="#">
                                <input type="file" name="document" id="documentInputInline" style="display:none;" />
                                <div id="dragDropAreaInline" style="padding:2em; border:2px dashed #5cb85c; border-radius:12px; background:#fff; text-align:center; cursor:pointer;">
                                    <span class="glyphicon glyphicon-cloud-upload" style="font-size:2em; color:#5cb85c;"></span>
                                    <p style="margin-top:10px;">Drag & drop file here or <a href="#" onclick="document.getElementById('documentInputInline').click(); return false;">browse</a></p>
                                    <div id="fileNameInline" style="margin-top:10px; color:#2471A3;"></div>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm" style="margin-top:18px;">Upload</button>
                            </form>
                        </div>
                        <div class="list-group list-group-flush" style="margin-bottom: 0;">
                            <!-- Mockup Documents List -->
                            <a href="/uploads/contract_sample.pdf" class="list-group-item clearfix" target="_blank">
                                <span class="glyphicon glyphicon-file"></span>
                                Contract - Signed.pdf
                                <br><small class="text-muted">Signed contract for solar installation</small>
                                <span class="glyphicon glyphicon-ok pull-right" style="color: #5cb85c;"></span>
                            </a>
                            <a href="/uploads/id_sample.jpg" class="list-group-item clearfix" target="_blank">
                                <span class="glyphicon glyphicon-picture"></span>
                                Valid ID.jpg
                                <br><small class="text-muted">Government-issued ID</small>
                                <span class="glyphicon glyphicon-download-alt pull-right"></span>
                            </a>
                            <a href="/uploads/or_sample.pdf" class="list-group-item clearfix" target="_blank">
                                <span class="glyphicon glyphicon-file"></span>
                                Official Receipt.pdf
                                <br><small class="text-muted">Downpayment receipt</small>
                                <span class="glyphicon glyphicon-download-alt pull-right"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Right Column: Documents & Contracts -->
        <div class="col-lg-3 col-md-12 col-sm-12 sticky-card" >

            <!-- Legal Action -->
            <div class="card">
                <div class="card-header">
                    <span class="glyphicon glyphicon-briefcase"></span> Legal Action
                    <button class="btn btn-xs btn-danger pull-right" id="initiateLegalActionBtn" style="margin-left:10px;">
                        <span class="glyphicon glyphicon-flag"></span> Initiate
                    </button>
                </div>
                <div class="card-body" style="padding:1em 1.2em;">
                    <p style="margin:0 0 8px;">Status: <strong><?= htmlspecialchars(safe($details, 'legal_action_status', 'None')) ?></strong></p>
                    <p style="margin:0 0 12px;">Last Update: <strong><?= htmlspecialchars(safe($details, 'legal_action_updated', '—')) ?></strong></p>
                </div>
            </div>
            <!-- Blacklisted Notifier -->
            <div class="card">
                <div class="card-header">
                    <span class="glyphicon glyphicon-alert"></span> Blacklisted Notifier
                    <button class="btn btn-xs btn-default pull-right" id="toggleBlacklistBtn" style="margin-left:10px;">
                        <span class="glyphicon glyphicon-random"></span> Toggle
                    </button>
                </div>
                <div class="card-body" style="padding:1em 1.2em;">
                    <?php $is_blacklisted = strtolower(safe($details, 'blacklisted', 'no')) === 'yes'; ?>
                    <p style="margin:0 0 10px;">Customer Blacklisted:
                        <?php if ($is_blacklisted): ?>
                            <span class="badge badge-warning">YES</span>
                        <?php else: ?>
                            <span class="badge badge-success">NO</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <!-- Warranty Information -->
            <div class="card">
                <div class="card-header"><span class="glyphicon glyphicon-certificate"></span> Warranty Information</div>
                <div class="card-body" style="padding:0 0;">
                    <div class="table-responsive">
                        <table class="table table-condensed" style="margin-bottom:0;">
                            <thead>
                                <tr>
                                    <th>Component</th>
                                    <th class="text-center">Warranty</th>
                                    <th class="text-center">Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Panels</td>
                                    <td class="text-center">12 yrs</td>
                                    <td class="text-center">Product</td>
                                </tr>
                                <tr>
                                    <td>Panels (Performance)</td>
                                    <td class="text-center">25 yrs</td>
                                    <td class="text-center">80% Output</td>
                                </tr>
                                <tr>
                                    <td>Inverter</td>
                                    <td class="text-center">5 yrs</td>
                                    <td class="text-center">Extendable</td>
                                </tr>
                                <tr>
                                    <td>Workmanship</td>
                                    <td class="text-center">2 yrs</td>
                                    <td class="text-center">Installation</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Document Upload Modal -->
            <div class="modal fade" id="uploadDocModal" tabindex="-1" role="dialog" aria-labelledby="uploadDocModalLabel">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="uploadDocModalLabel"><span class="glyphicon glyphicon-upload"></span> Upload Document</h4>
                        </div>
                        <div class="modal-body" style="padding:2em;">
                            <form id="documentUploadFormModal" enctype="multipart/form-data" method="post" action="#">
                                <input type="file" name="document" id="documentInputModal" style="display:none;" />
                                <div id="dragDropAreaModal" style="padding:2em; border:2px dashed #5cb85c; border-radius:12px; background:#f8fafc; text-align:center; cursor:pointer;">
                                    <span class="glyphicon glyphicon-cloud-upload" style="font-size:2em; color:#5cb85c;"></span>
                                    <p style="margin-top:10px;">Drag & drop file here or <a href="#" onclick="document.getElementById('documentInputModal').click(); return false;">browse</a></p>
                                    <div id="fileNameModal" style="margin-top:10px; color:#2471A3;"></div>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm" style="margin-top:18px;">Upload</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Geolocation Modal -->
            <div class="modal fade" id="geoMapModal" tabindex="-1" role="dialog" aria-labelledby="geoMapModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="geoMapModalLabel">Location Map</h4>
                        </div>
                        <div class="modal-body" style="height:450px;">
                            <?php if ($lat && $lon): ?>
                                <iframe width="100%" height="100%" frameborder="0" style="border:0" allowfullscreen
                                    src="https://www.google.com/maps?q=<?= urlencode($lat) ?>,<?= urlencode($lon) ?>&hl=es;z=14&output=embed">
                                </iframe>
                            <?php else: ?>
                                <div class="alert alert-warning">No geolocation data available.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Maintenance Modal -->
            <div class="modal fade" id="maintenanceModal" tabindex="-1" role="dialog" aria-labelledby="maintenanceModalLabel">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="maintenanceModalLabel"><span class="glyphicon glyphicon-plus"></span> Add Maintenance Record</h4>
                        </div>
                        <div class="modal-body">
                            <form id="maintenanceForm" method="post" action="#">
                                <div class="form-group">
                                    <label for="serviceId">Service ID</label>
                                    <input type="text" class="form-control" id="serviceId" name="serviceId" placeholder="SRV-XXXXX">
                                </div>
                                <div class="form-group">
                                    <label for="serviceDate">Date</label>
                                    <input type="date" class="form-control" id="serviceDate" name="serviceDate">
                                </div>
                                <div class="form-group">
                                    <label for="serviceDesc">Description</label>
                                    <input type="text" class="form-control" id="serviceDesc" name="serviceDesc" placeholder="Description">
                                </div>
                                <div class="form-group">
                                    <label for="serviceStatus">Status</label>
                                    <select class="form-control" id="serviceStatus" name="serviceStatus">
                                        <option value="Completed">Completed</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Scheduled">Scheduled</option>
                                    </select>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-success">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$conversation_arr = [
    [
        'from' => 'user',
        'text' => 'Hi, this is PAE. We noticed your account has an overdue balance. Can you please update or settle your payment?'
    ],
    [
        'from' => 'client',
        'text' => 'Hello! Sorry for the delay. I will check and settle it by tomorrow.'
    ],
    [
        'from' => 'user',
        'text' => 'Thank you for your prompt response. Let us know if you need any assistance.'
    ],
    [
        'from' => 'client',
        'text' => 'Will do. Thank you!'
    ]
];
?>
<!-- Chatbox (hidden by default) -->
<div id="chatbox">
    <div style="background:#F39C12; color:#fff; padding:12px 16px; border-radius:12px 12px 0 0; display:flex; align-items:center; justify-content:space-between;">
        <span style="font-weight:600; font-size:1.1em;"><span class="glyphicon glyphicon-comment"></span> SMS Chat</span>
        <button type="button" class="close" id="closeChatbox" style="color:#fff !important; font-size:1.3em; background:none; border:none;">&times;</button>
    </div>
    <div id="chat-messages" style="height:260px; overflow-y:auto; padding:16px; background:#f8fafc;">
        <?php foreach ($conversation_arr as $msg): ?>
            <div style="margin-bottom:12px; text-align:<?= $msg['from'] === 'user' ? 'right' : 'left' ?>;">
                <div style="background:<?= $msg['from'] === 'user' ? '#d4e6f1' : '#eaf2f8' ?>; border-radius:8px; padding:8px 12px; max-width:80%; display:inline-block;">
                    <?= htmlspecialchars($msg['text']) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div style="padding:12px 16px; border-top:1px solid #eaf2f8; background:#fff;">
        <form id="chat-form" onsubmit="sendChatMessage(); return false;" style="display:flex; gap:8px;">
            <input type="text" id="chat-input" class="form-control input-sm" placeholder="Type a message..." autocomplete="off" style="flex:1; border-radius:8px;">
            <button type="submit" class="btn btn-primary btn-sm" style="border-radius:8px;">Send</button>
        </form>
    </div>
</div>
<!-- Call Dialer Modal -->
<div class="modal fade" id="callDialerModal" tabindex="-1" role="dialog" aria-labelledby="callDialerModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="callDialerModalLabel"><span class="glyphicon glyphicon-earphone"></span> Dialer</h4>
            </div>
            <div class="modal-body" style="text-align:center;">
                <div style="font-size:1.1em; margin-bottom:10px;">Call this number?</div>
                <div style="font-size:1.5em; font-weight:700; color:#2471A3; margin-bottom:18px;">
                    <?= htmlspecialchars(safe($details, 'contact_phone', 'N/A')) ?>
                </div>
                <button class="btn btn-success btn-lg" style="width:100%;"><span class="glyphicon glyphicon-earphone"></span> Call Now</button>
            </div>
        </div>
    </div>
</div>
<script>
    function saveFinanceDetails() {
        // Placeholder JS handler; integrate AJAX as needed.
        var form = document.getElementById('finance-details-form');
        if (!form) return;
        var dp = form.downpayment_amount.value;
        var lease = form.lease_months.value;
        console.log('Saving finance details', {
            downpayment: dp,
            lease_months: lease
        });
        // Display a lightweight feedback (could be replaced by toast)
        if (window.jQuery) {
            jQuery(form).find('button.btn-primary').prop('disabled', true).text('Saved');
            setTimeout(function() {
                jQuery(form).find('button.btn-primary').prop('disabled', false).html('<span class="glyphicon glyphicon-floppy-disk"></span> Save');
            }, 1500);
        }
    }

    // Show/hide upload box
    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('showUploadBox');
        var box = document.getElementById('uploadBox');
        if (btn && box) {
            btn.onclick = function() {
                box.style.display = (box.style.display === 'none') ? 'block' : 'none';
            };
        }
        var dragDrop = document.getElementById('dragDropArea');
        var input = document.getElementById('documentInput');
        if (dragDrop && input) {
            dragDrop.addEventListener('click', function() {
                input.click();
            });
            dragDrop.addEventListener('dragover', function(e) {
                e.preventDefault();
                dragDrop.style.background = '#eaf2f8';
            });
            dragDrop.addEventListener('dragleave', function(e) {
                e.preventDefault();
                dragDrop.style.background = '';
            });
            dragDrop.addEventListener('drop', function(e) {
                e.preventDefault();
                dragDrop.style.background = '';
                if (e.dataTransfer.files.length) {
                    input.files = e.dataTransfer.files;
                    document.getElementById('fileName').textContent = input.files[0].name;
                }
            });
            input.addEventListener('change', function() {
                if (input.files.length) {
                    document.getElementById('fileName').textContent = input.files[0].name;
                }
            });
        }
    });

    // Document upload modal script
    document.addEventListener('DOMContentLoaded', function() {
        var dragDropModal = document.getElementById('dragDropAreaModal');
        var inputModal = document.getElementById('documentInputModal');
        var fileNameModal = document.getElementById('fileNameModal');
        if (dragDropModal && inputModal) {
            dragDropModal.addEventListener('click', function() {
                inputModal.click();
            });
            dragDropModal.addEventListener('dragover', function(e) {
                e.preventDefault();
                dragDropModal.style.background = '#eaf2f8';
            });
            dragDropModal.addEventListener('dragleave', function(e) {
                e.preventDefault();
                dragDropModal.style.background = '#f8fafc';
            });
            dragDropModal.addEventListener('drop', function(e) {
                e.preventDefault();
                dragDropModal.style.background = '#f8fafc';
                if (e.dataTransfer.files.length) {
                    inputModal.files = e.dataTransfer.files;
                    fileNameModal.textContent = e.dataTransfer.files[0].name;
                }
            });
            inputModal.addEventListener('change', function() {
                if (inputModal.files.length) {
                    fileNameModal.textContent = e.dataTransfer.files[0].name;
                }
            });
        }
    });

    // Show chatbox when SMS button is clicked with animation
    document.addEventListener('DOMContentLoaded', function() {
        var smsBtn = document.querySelector('button.btn-default .glyphicon-comment');
        var chatbox = document.getElementById('chatbox');
        var closeBtn = document.getElementById('closeChatbox');
        if (smsBtn && chatbox) {
            smsBtn.parentElement.onclick = function(e) {
                e.preventDefault();
                chatbox.classList.add('active');
            };
        }
        if (closeBtn && chatbox) {
            closeBtn.onclick = function() {
                chatbox.classList.remove('active');
            };
        }
    });
    // Simple chat message append
    function sendChatMessage() {
        var input = document.getElementById('chat-input');
        var messages = document.getElementById('chat-messages');
        if (input && messages && input.value.trim()) {
            var msgDiv = document.createElement('div');
            msgDiv.style.marginBottom = '12px';
            msgDiv.style.textAlign = 'right';
            msgDiv.innerHTML = '<div style="background:#d4e6f1; border-radius:8px; padding:8px 12px; max-width:80%; display:inline-block;">' + input.value + '</div>';
            messages.appendChild(msgDiv);
            messages.scrollTop = messages.scrollHeight;
            input.value = '';
        }
    }

    function selectProject(projectName) {
        document.getElementById('selectedProject').textContent = projectName;
        // TODO: Add AJAX or page reload logic here to load the selected project's data
        // Example: window.location.href = '/module/view?project=' + encodeURIComponent(projectName);
    }
</script>