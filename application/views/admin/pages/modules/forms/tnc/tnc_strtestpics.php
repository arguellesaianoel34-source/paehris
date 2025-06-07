
<div class="tab-pane fade in active" id="tnc_strtestpics">
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">PV MODULE STRING TEST PICTURES</div>
        </div>
        <div class="portlet-body">
            <div class="well">
                <h4 class="bold">Upload all your pictures here.</h4>
                Naming Mechanics: <a href="#tbl_tnc_naming" data-toggle="ajax-modal" title="TNC Naming Matrix">
                    Here <i class="fa fa-info-circle"></i>
                </a>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        Browse File
                        <input id="attfiledrop" placeholder="Browse file..." name="tncfiledrop[]" accept=".zip,image/*" data-upload-url="<?php echo base_url('forms/uploadtncpics'); ?>" class="file" type="file" data-show-preview="false" multiple  />
                    </div>
                </div>
                <div class="col-md-12">
                    <style>
                        .table-container {
                            display: flex;
                            flex-direction: column;
                            width: 100%;
                            border: 2px solid #333;
                            font-family: Arial, sans-serif;
                        }
                        .table-row {
                            display: flex;
                            width: 100%;
                        }
                        .table-cell {
                            flex: 1;
                            /*padding: 10px;*/
                            border: 1px solid #999;
                            text-align: center;
                        }
                        .inverter-row .inverter-cell {
                            flex: 1 1 100%; /* Full width */
                            background-color: #ddd;
                            font-weight: bold;
                            text-align: center;
                            font-size: 18px;
                            padding: 10px;
                        }
                        .header-row .header-cell {
                            font-weight: bold;
                            background-color: #f4f4f4;
                        }

                        .table-cell.img {
                            flex: 1;
                            padding: 0; /* Remove padding for better image fitting */
                            border: 1px solid #999;
                            text-align: center;
                            height: 120px; /* Set a fixed height for uniformity */
                            position: relative;
                            overflow: hidden; /* Prevent overflow */
                        }
                        .table-cell img {
                            width: 100%;
                            height: 100%;
                            object-fit: cover; /* Ensures image fully covers the cell */
                            display: block;
                        }
                    </style>
                    <div id="pics_stt">
                    <?php
                    if (isset($imgs) && count($imgs) > 0) {
                        if (isset($imgs['voc']['inv']) && count($imgs['voc']['inv']) > 0) {
                            echo '<h3 class="bold">Voc Test</h3>';
                            $maxColumns = 5; // Maximum images per row

                            echo '<div class="table-container">';

                            foreach ($imgs["voc"]["inv"] as $invNumber => $inverter) {
                                echo '<div class="table-row inverter-row"><div class="table-cell inverter-cell">Inverter ' . $invNumber . '</div></div>';

                                // Retrieve images and group into chunks of 5
                                $stringImages = $inverter["str"];
                                $chunks = array_chunk($stringImages, $maxColumns, true); // Preserve keys

                                foreach ($chunks as $chunk) {
                                    // Header Row (String Names)
                                    echo '<div class="table-row header-row">';
                                    foreach ($chunk as $stringNum => $img) {
                                        echo '<div class="table-cell header-cell">String ' . $stringNum . '</div>';
                                    }
                                    echo '</div>';

                                    // Image Row
                                    echo '<div class="table-row">';
                                    foreach ($chunk as $img) {
                                        echo '<div class="table-cell img"><img src="' . $img . '" alt="Image"></div>';
                                    }
                                    echo '</div>';
                                }
                            }

                            echo '</div>';
                        }

                        if (isset($imgs['pol']['inv']) && count($imgs['pol']['inv']) > 0) {

                            echo '<h3 class="bold">Polarity Test</h3>';
                            $maxColumns = 5; // Maximum images per row

                            echo '<div class="table-container">';

                            foreach ($imgs["pol"]["inv"] as $invNumber => $inverter) {
                                echo '<div class="table-row inverter-row"><div class="table-cell inverter-cell">Inverter ' . $invNumber . '</div></div>';

                                // Retrieve images and group into chunks of 5
                                $stringImages = $inverter["str"];
                                $chunks = array_chunk($stringImages, $maxColumns, true); // Preserve keys

                                foreach ($chunks as $chunk) {
                                    // Header Row (String Names)
                                    echo '<div class="table-row header-row">';
                                    foreach ($chunk as $stringNum => $img) {
                                        echo '<div class="table-cell header-cell">String ' . $stringNum . '</div>';
                                    }
                                    echo '</div>';

                                    // Image Row
                                    echo '<div class="table-row">';
                                    foreach ($chunk as $img) {
                                        echo '<div class="table-cell img"><img src="' . $img . '" alt="Image"></div>';
                                    }
                                    echo '</div>';
                                }
                            }

                            echo '</div>';
                        }
                    } else {
                        echo '<div class="note note-info text-align-center"><h4><i class="fa fa-warning text-warning"></i> Please refresh page to show uploaded images.</h4></div>';
                    }
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo file_versioning('assets/pages/attachements/main.js'); ?>"></script>
<script type="text/javascript">
    ATTACHEMENTS.forms(<?php echo $appid; ?>);
</script>
