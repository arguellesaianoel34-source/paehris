<?php
?>

<div class="tab-pane fade in " id="tqt_pics">
    <div class="portlet light">
        <div class="portlet-body">
            <div class="row">
                <style>
                    #form_vift_pics table {
                        width: 100%;
                        border-collapse: collapse;
                        text-align: center;
                        margin-bottom: 20px;
                    }
                    #form_vift_pics th, #form_vift_pics td {
                        border: 1px solid black;
                        padding: 8px;
                        text-align: center;
                    }
                    #form_vift_pics img {
                        width: 150px; /* Adjust as needed */
                        height: auto;
                    }
                </style>
                <div class="col-md-12 margin-bottom-15">
                    <?php if (isset($imgs['tqt'])) { ?>
                    <table class="table table-bordered" style="width: 100%;">
                        <tr>
                            <th colspan="7" style="text-align: center;">Torque Testing Pictures</th>
                        </tr>

                        <?php
                        if (isset($imgs['tqt']['bk'])) {
                            $first = true;

                            foreach ($imgs['tqt']['bk']['inv'] as $invNum => $sides) {
                                echo "<tr><th colspan='7' style='text-align: center;'>Breaker Inverter {$invNum}</th></tr>";

                                if ($first) {
                                    // Show headers only once
                                    echo "<tr>";
                                    echo "<th colspan='3' style='text-align: center;'>LINE SIDE</th>";
                                    echo "<th colspan='3' style='text-align: center;'>LOAD SIDE</th>";
                                    echo "</tr>";
                                    echo "<tr>";
                                    echo "<td style='color: red;'>L1</td>";
                                    echo "<td style='color: orange;'>L2</td>";
                                    echo "<td style='color: blue;'>L3</td>";
                                    echo "<td style='color: red;'>L1</td>";
                                    echo "<td style='color: orange;'>L2</td>";
                                    echo "<td style='color: blue;'>L3</td>";
                                    echo "</tr>";
                                }

                                // Image row
                                echo "<tr>";

                                // LINE SIDE images
                                foreach (['l1', 'l2', 'l3'] as $phase) {
                                    $src = $sides['ln'][$phase] ?? '';
                                    echo "<td><img src='{$src}' width='100'></td>";
                                }

                                // LOAD SIDE images
                                foreach (['l1', 'l2', 'l3'] as $phase) {
                                    $src = $sides['ld'][$phase] ?? '';
                                    echo "<td><img src='{$src}' width='100'></td>";
                                }

                                echo "</tr>";

                                $first = false; // Only show headers once
                            }
                        } else {
                            echo '<tr><th><i class="fa fa-warning text-warning"></i> Torque Testing Pictures Uploaded for Breakers!</h4></th></tr>';
                        }
                        ?>
                    </table>
                    <table class="table table-bordered" style="width: 100%;">
                        <?php
                        if (isset($imgs['tqt']['inv'])) {
                            $first = true;

                            foreach ($imgs['tqt']['inv'] as $invNum => $phases) {
                                echo "<tr><th colspan='4' style='text-align: center;'>Inverter {$invNum}</th></tr>";

                                if ($first) {
                                    // Phase headers only for the first inverter
                                    echo "<tr>";
                                    echo "<td style='color: red;'>L1</td>";
                                    echo "<td style='color: orange;'>L2</td>";
                                    echo "<td style='color: blue;'>L3</td>";
                                    echo "</tr>";
                                }

                                // Image row
                                echo "<tr><td></td>";
                                foreach (['l1', 'l2', 'l3'] as $phase) {
                                    $src = $phases[$phase] ?? '';
                                    echo "<td><img src='{$src}' width='100'></td>";
                                }
                                echo "</tr>";

                                $first = false;
                            }
                        } else {
                            echo '<tr><th><i class="fa fa-warning text-warning"></i> No Torque Testing Pictures Uploaded for Inverters!</h4></th></tr>';
                        }
                        ?>
                    </table>
                    <?php } else {
                        echo '<div class="note note-info text-align-center"><h4 class="bold"><i class="fa fa-warning text-warning"></i> No Torque Testing Pictures Uploaded!</h4></div>';
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>
