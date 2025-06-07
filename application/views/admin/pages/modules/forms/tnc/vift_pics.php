
<div class="tab-pane fade in active" id="form_vift_pics">
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
                    <?php  ?>
                    <table>
                        <tr>
                            <th colspan="4">AC Insulation Testing Pictures</th>
                        </tr>
                        <tr>
                            <th>L1-G</th>
                            <th>L2-G</th>
                            <th>L3-G</th>
                            <th>N-G</th>
                        </tr>

                        <?php
                        if (isset($imgs['aci']['inv']) && count($imgs['aci']['inv']) > 0) {
                            foreach ($imgs['aci']['inv'] as $invNum => $data) {
                                echo "<tr><th colspan='4'>Inverter $invNum</th></tr>";
                                echo "<tr>";
                                echo "<td><img src='{$data['l1g']}' alt='L1-G'></td>";
                                echo "<td><img src='{$data['l2g']}' alt='L2-G'></td>";
                                echo "<td><img src='{$data['l3g']}' alt='L3-G'></td>";
                                echo "<td><img src='{$data['ng']}' alt='N-G'></td>";
                                echo "</tr>";
                                echo "<tr>";
                                echo "<td id='res_l1g_$invNum'></td>";
                                echo "<td id='res_l2g_$invNum'></td>";
                                echo "<td id='res_l3g_$invNum'></td>";
                                echo "<td id='res_ng_$invNum'></td>";
                                echo "</tr>";
                            }
                        } else { ?>
                            <tr>
                                <td colspan="4"><h4><i class="fa fa-warning text-warning"></i> No Pictures!</h4></td>
                            </tr>
                        <?php } ?>

                    </table>
                </div>
                <?php
                if (isset($imgs['dci']['inv']) && count($imgs['dci']['inv']) > 0) {
                    foreach ($imgs['dci']['inv'] as $invNum => $data) {
                        echo '<div class="col-md-12 margin-bottom-15">';
                        echo "<table>";
                        echo "<tr><th colspan='4'>DC Insulation Testing Pictures - Inverter $invNum</th></tr>";
                        echo "<tr>";
                        echo "<th>String No.</th>";
                        echo "<th>Positive-Ground</th>";
                        echo "<th>Negative-Ground</th>";
                        echo "<th>Positive-Negative</th>";
                        echo "</tr>";

                        foreach ($data['str'] as $strNum => $values) {
                            echo "<tr>";
                            echo "<td>$strNum</td>";
                            echo "<td><img src='{$values['pg']}' alt='PG'></td>";
                            echo "<td><img src='{$values['ng']}' alt='NG'></td>";
                            echo "<td><img src='{$values['pn']}' alt='PN'></td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                        echo "</div>";
                    }
                } else {
                    echo '<div class="col-md-12 margin-bottom-15">';
                    echo "<table>";
                    echo '<tr><th><i class="fa fa-warning text-warning"></i> No DC Insulation Pictures Uploaded!</h4></th></tr>';
                    echo "</table>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    </div>
</div>
