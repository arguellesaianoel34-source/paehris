<?php
?>

<div class="tab-pane fade in" id="thermal_pics">
    <style>
        #thermal_pics table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            margin-bottom: 20px;
        }
        #thermal_pics th, #form_vift_pics td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        #thermal_pics img {
            width: 150px; /* Adjust as needed */
            height: auto;
        }
    </style>
    <div class="col-md-12 margin-bottom-15">
        <?php if (isset($imgs['thm'])) { ?>

            <table class="table table-bordered" style="width: 100%;">
                <?php
                if (isset($imgs['thm']['inv'])) {
                    $first = true;

                    foreach ($imgs['thm']['inv'] as $invNum => $phases) {
                        echo "<tr><th colspan='3' style='text-align: center;'>Inverter {$invNum}</th></tr>";

                        if ($first) {
                            // Phase headers only for the first inverter
                            echo "<tr>";
                            echo "<td>DC MCB / Inverter Running</td>";
                            echo "<td>DC MCB Newly Energized</td>";
                            echo "<td>RSD</td>";
                            echo "</tr>";
                        }

                        // Image row
                        echo "<tr>";
                        foreach (['ir', 'ne', 'rs'] as $phase) {
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
