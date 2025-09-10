<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Helper function for safe array access
if (!function_exists('safe')) {
    function safe($arr, $key, $default = '') {
        return isset($arr[$key]) && $arr[$key] !== null ? $arr[$key] : $default;
    }
}

// Load the model
$CI =& get_instance();
$CI->load->model('model_customerprofile', 'client', TRUE);

$details = $CI->client->get_customer_details($dataid);

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
        border-radius: 50%;
        border: 4px solid #fff;
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
</style>
<div class="container-fluid">
    <div class="profile-hero" style="position: relative;">
        <img class="avatar" src="https://ui-avatars.com/api/?name=<?= urlencode(safe($details, 'app_name', 'Customer')) ?>&background=0A2342&color=fff&size=256" alt="Avatar">
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
            </div>
        </div>
        <div style="margin-left:auto; position: absolute; top: 10px; right: 10px;">
            <button class="btn btn-default"><span class="glyphicon glyphicon-comment"></span> SMS</button>
            <button class="btn btn-primary"><span class="glyphicon glyphicon-edit"></span> Edit Profile</button>
            <a href="/module/<?= $hashcode ?>/" class="btn btn-default"><span class="glyphicon glyphicon-file"></span> Back</a>
        </div>
    </div>
    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-4 col-md-5 col-sm-12 sticky-card" style="z-index: 100;">
            <!-- System Information -->
            <div class="card">
                <div class="card-header"><span class="glyphicon glyphicon-flash"></span> System Information</div>
                <ul class="list-group list-group-flush" style="margin-bottom: 0;">
                    
                    <li class="list-group-item d-flex">
                        <div class="w-50">L1-L2</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'l1_l2')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">L1-L3</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'l1_l3')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">L2-L3</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'l2_l3')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">L1-G</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'l1_g')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">L2-G</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'l2_g')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">L3-G</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'l3_g')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">L1-L2A</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'l1_l2_a')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">L1-L3A</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'l1_l3_a')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">L2-L3A</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'l2_l3_a')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">System Power</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'system_power')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">Number of Panels</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'number_of_panels')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">Rate Class</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'rate_class')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">Panel Type</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'panel_type')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">Roof Inclination</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'roof_inclination')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">Inspection Date</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'inspection_date')) ?></strong></div>
                    </li>
                    <li class="list-group-item d-flex">
                        <div class="w-50">System Size Remarks</div>
                        <div class="px-2">:</div>
                        <div class="flex-grow-1"><strong><?= htmlspecialchars(safe($details, 'system_size_remarks')) ?></strong></div>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Middle Column -->
        <div class="col-lg-5 col-md-7 col-sm-12">
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
            <!-- Billing & Payments -->
            <div class="card">
                <div class="card-header"><span class="glyphicon glyphicon-list-alt"></span> Billing & Payments</div>
                <div class="card-body" style="padding: 0 0;">

                    <div class="table-responsive">
                        <table class="table table-hover table-condensed">
                            <thead>
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>INV-2024-00123</td>
                                    <td>2024-06-25</td>
                                    <td>&#8369; 5,500.00</td>
                                    <td><span class="badge badge-success">Paid</span></td>
                                    <td><a href="#" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-eye-open"></span></a></td>
                                </tr>
                                <tr>
                                    <td>INV-2024-00115</td>
                                    <td>2024-05-25</td>
                                    <td>&#8369; 5,820.00</td>
                                    <td><span class="badge badge-success">Paid</span></td>
                                    <td><a href="#" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-eye-open"></span></a></td>
                                </tr>
                                <tr>
                                    <td>INV-2024-00108</td>
                                    <td>2024-04-25</td>
                                    <td>&#8369; 6,100.00</td>
                                    <td><span class="badge badge-success">Paid</span></td>
                                    <td><a href="#" class="btn btn-xs btn-info"><span class="glyphicon glyphicon-eye-open"></span></a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-right">
                        <button class="btn btn-link">View Full Payment History</button>
                    </div>
                </div>
            </div>
            <!-- Maintenance & Service History -->
            <div class="card">
                <div class="card-header"><span class="glyphicon glyphicon-time"></span> Maintenance & Service History</div>
                <div class="card-body" style="padding: 0 0;">
                    <div class="table-responsive">
                        <table class="table table-hover table-condensed" >
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
        <!-- Right Column: Documents & Contracts -->
        <div class="col-lg-3 col-md-12 col-sm-12 sticky-card" style="z-index: 100;">
            <!-- Notes -->
            <div class="card notes-section ">
                <div class="card-header">
                    <span class="glyphicon glyphicon-pencil"></span> Internal Notes
                    <div class="card-actions pull-right">
                        <button class="btn btn-warning btn-sm" >Save Note</button>
                    </div>
                </div>
                <div class="card-body">
                    <textarea class="form-control" rows="4" placeholder="Add notes here..."></textarea>
                </div>
            </div>
            <div class="card ">
                <div class="card-header"><span class="glyphicon glyphicon-folder-open"></span> Documents & Contracts</div>
                <div class="list-group list-group-flush" style="margin-bottom: 0;">
                    <?php if (!empty($details['documents']) && is_array($details['documents'])): ?>
                        <?php foreach ($details['documents'] as $doc): ?>
                            <a href="/<?= htmlspecialchars($doc['fileurl']) ?>" class="list-group-item clearfix" target="_blank">
                                <span class="glyphicon glyphicon-file"></span> 
                                <?= htmlspecialchars($doc['names'] ?: $doc['shortname'] ?: basename($doc['fileurl'])) ?>
                                <?php if (!empty($doc['desc'])): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($doc['desc']) ?></small>
                                <?php endif; ?>
                                <?php if (!empty($doc['comply']) && $doc['comply'] == 1): ?>
                                    <span class="glyphicon glyphicon-ok pull-right" style="color: #5cb85c;"></span>
                                <?php else: ?>
                                    <span class="glyphicon glyphicon-download-alt pull-right"></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="list-group-item text-muted">No documents found.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div></tr>


