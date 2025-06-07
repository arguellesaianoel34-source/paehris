<?php
?>
<div class="row margin-all-10">
    <div class="col-md-12">
        <table class="table table-bordered table-condensed">
            <thead>
            <th>Type</th>
            <th>Matrix</th>
            <th>Example</th>
            </thead>
            <tbody>
            <tr>
                <td>VOC</td>
                <td>voc_inv< inverter# >_str< string# ></td>
                <td>
                    <ol>
                        <li>
                            For String 8 of Inverter 2, filename should be <b>voc_inv2_str8</b>.
                        </li>
                        <li>
                            For String 3 of Inverter 7, filename should be <b>voc_inv7_str3</b>.
                        </li>
                    </ol>
                </td>
            </tr>
            <tr>
                <td>Polarity</td>
                <td>pol_inv< inverter# >_str< string# ></td>
                <td>
                    <ol>
                        <li>
                            For String 1 of Inverter 1, filename should be <b>pol_inv1_str1</b>.
                        </li>
                    </ol>
                </td>
            </tr>
            <tr>
                <td>Continuity Test</td>
                <td> ctt_inv< inverter# >_< line ></td>
                <td>
                    <ul>
                        <li>
                           File name: <b>ctt_inv1_l1</b>.
                        </li>
                        <li>
                            Where Line = L1, L2, L3 or N.
                        </li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>AC Insulation</td>
                <td>aci_inv< inverter# >_< line ></td>
                <td>
                    <ul>
                        <li>
                            File name: <b>aci_inv1_l1g</b>.
                        </li>
                        <li>
                            Where <b>line</b> is
                            <ul>
                                <li>l1g = L1-Ground</li>
                                <li>l2g = L2-Ground</li>
                                <li>l3g = L3-Ground</li>
                                <li>ng = N-Ground</li>
                            </ul>
                        </li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>DC Insulation</td>
                <td>dci_inv< inverter# >_str< string# >_< polarity ></td>
                <td>
                    <ul>
                        <li>
                            File name: <b>dci_inv1_str1_pg</b>.
                        </li>
                        <li>
                            Where <b>polarity</b> is
                            <ul>
                                <li>pg = Positive-Ground</li>
                                <li>ng = Negative-Ground</li>
                                <li>pn = Positive-Negative</li>
                            </ul>
                        </li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>Torque Testing (Breaker)</td>
                <td>tqt_bk_inv< inverter# >_< ln or ld >_< line # ></td>
                <td>
                    <ul>
                        <li>
                            File name: <b>tqt_bk_inv1_ln_l1</b>.
                        </li>
                        <li>
                            Where
                            <ul>
                                <li>BK = Torque Test for Breaker</li>
                                <li>LN = Line Side & LD = Load Side</li>
                                <li>Line # = L1, L2 or L3</li>
                            </ul>
                        </li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>Torque Testing (Inverter)</td>
                <td>tqt_inv< inverter# >_< line # ></td>
                <td>
                    <ul>
                        <li>
                            File name: <b>tqt_inv1_l1</b>.
                        </li>
                        <li>
                            Where Line # = L1, L2 or L3.
                        </li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>Thermal Scanning</td>
                <td>thm_inv< inverter# >_< ir, ne, rs ></td>
                <td>
                    <ul>
                        <li>
                            File name (Inverter): <b>thm_inv1_ir</b>.
                        </li>
                        <li>
                            File name (Breaker): <b>thm_bk</b>.
                        </li>
                        <li>
                            Where
                            <ul>
                                <li>IR = DC MCB / Inverter Running</li>
                                <li>NE = DC MCB Newly Energized</li>
                                <li>RS = RSD</li>
                            </ul>
                        </li>
                    </ul>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

</div>