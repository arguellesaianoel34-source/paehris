<?php
if (isset($inverter) && $inverter > 0) {
    $active = ($inverter == 1) ? 'active' : 0;
}

if (isset($ctt['details']['accepted'])) {
    if ($ctt['details']['accepted'] == 1) {
        $ctt_accepted_yes = 'checked';
    }
    if ($ctt['details']['accepted'] == 0) {
        $ctt_accepted_no = 'checked';
    }
}

?>
<div class="tab-pane fade in <?php echo $active; ?>" id="tnc_<?php echo $inverter; ?>_ctt">
    <style>
        #tnc_<?php echo $inverter; ?>_stringtest .table .main-headers th {
            text-align: center !important;
            vertical-align: middle;
        }
        #tnc_<?php echo $inverter; ?>_stringtest .table tr:not(.main-headers) th {
            vertical-align: center;
            /*-webkit-transform: rotate(-90deg);
            -moz-transform: rotate(-90deg);
            -o-transform: rotate(-90deg);
            -ms-transform: rotate(-90deg);
            transform: rotate(-90deg);*/
            text-align: right;
            writing-mode: vertical-rl;
            transform: scale(-1);
            height: 300px;
        }

        #tnc_<?php echo $inverter; ?>_stringtest .table tbody tr td:first-child {
            text-align: center;
        }

        /*#tnc_<?php echo $inverter; ?>_stringtest .table tr:not(.main-headers) th {
            transform: scale(-1);
        }*/

    </style>
    <div class="well">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-4">
                    Device No. :
                    <label>INVERTER <?php echo $inverter; ?></label>
                </div>
                <div class="col-md-4">
                    Description : Continuity Test
                    <input type="hidden" name="ctt[<?php echo $inverter; ?>][details][type]" value="3" />
                </div>
            </div>
        </div>
    </div>
    <h4>CONTINUITY TEST</h4>
    <div class="well">
        <div class="row">
            <div class="col-md-6">
                Testing Area
                <input class="form-control inline" name="ctt[<?php echo $inverter; ?>][details][testingarea]" id="inv_trn_type" value="Electrical Room" placeholder="Customer name/number" style="border-bottom: 1.5px grey solid !important;" required />
            </div>
            <div class="col-md-6">
                Date & Time
                <input type="datetime-local" class="form-control inline" name="ctt[<?php echo $inverter; ?>][details][testdate]" value="<?php echo isset($ctt['details']['testdate']) ? date('Y-m-d\TH:i',strtotime($ctt['details']['testdate'])) : date('Y-m-d\TH:i'); ?>" placeholder="MM/DD/YYYY hh:mm AM/PM" style="border-bottom: 1.5px grey solid !important;" >
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                Ambient Temperature
                <input class="form-control inline" name="ctt[<?php echo $inverter; ?>][details][temp]" value="<?php echo $ctt['details']['temp'] ?? ''; ?>" placeholder="Temperature..." style="border-bottom: 1.5px grey solid !important;"  />
            </div>
            <div class="col-md-4">
                Humidity (%)
                <div class="testing-wrapper">
                    <input type="number" class="form-control input-sm inline testing-vals" name="ctt[<?php echo $inverter; ?>][details][humidity]" value="<?php echo $ctt['details']['humidity'] ?? ''; ?>" placeholder="Humidity..." style="border-bottom: 1.5px grey solid !important;" >
                    <span class="testing-unit" style="border-bottom: 1.5px grey solid !important; background: white;margin-left: unset;padding: 2.5px 0;">%</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                Testing Equipment
                <input class="form-control inline" name="ctt[<?php echo $inverter; ?>][details][equipment]" id="stt_equipment" value="<?php echo $ctt['details']['equipment'] ?? ''; ?>" placeholder="Testing Equipment..." style="border-bottom: 1.5px grey solid !important;" required />
            </div>
            <div class="col-md-4">
                Model
                <input class="form-control inline" name="ctt[<?php echo $inverter; ?>][details][equipmentmodel]" id="stt_eqt_model" value="<?php echo $ctt['details']['equipmentmodel'] ?? ''; ?>" placeholder="Equipment Model..." style="border-bottom: 1.5px grey solid !important;" required />
            </div>
            <div class="col-md-4">
                Serial Number
                <input class="form-control inline" name="ctt[<?php echo $inverter; ?>][details][equipmentsn]" id="stt_eqt_sn" value="<?php echo $ctt['details']['equipmentsn'] ?? ''; ?>" placeholder="Serial Number..." style="border-bottom: 1.5px grey solid !important;" required />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 bold">
            Note: Tester shall beep due to conduction
        </div>
    </div>
    <div class="row margin-top-15">
        <div class="col-md-12">
            <?php if (isset($img) && is_array($img) && count($img) > 0) { ?>
                <style>
                    .ctt-img-container {
                        display: grid;
                        grid-template-columns: repeat(2, 1fr); /* Two images per row */
                        gap: 10px; /* 10px spacing between images */
                        justify-content: center;
                        place-items: center; /* Center each item */
                    }
                    .ctt-img-box {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        width: 200px; /* Adjust as needed */
                        margin-bottom: 10px;
                    }
                    .ctt-img-box img {
                        width: 200px;
                        height: 200px;
                        object-fit: cover; /* Ensures images fit without distortion */
                        border: 1px solid #ddd;
                        border-radius: 5px;
                    }
                    .ctt-img-label {
                        font-weight: bold;
                        margin-bottom: 5px;
                    }
                </style>

                <div class="ctt-img-container">
                    <?php
                    foreach ($img as $key => $src) {
                        echo "<div class='ctt-img-box'>
                    <div class='ctt-img-label uppercase bold'>$key</div>
                    <img src='$src' alt='$key'>
                  </div>";
                    }
                    ?>
                </div>
            <?php } ?>
        </div>
        <div class="form-group col-md-12 margin-top-15">
            Check:
            <div class="icheck-inline ctt-accept">
                <label for="ctt_accepted_yes">Accepted</label> <input type="radio" name="ctt[<?php echo $inverter; ?>][details][accepted]" value="1" id="ctt_accepted_yes" class="icheck-select" <?php echo $ctt_accepted_yes ?? ''; ?> required>
                <label for="ctt_accepted_no">Not Accepted</label> <input type="radio" name="ctt[<?php echo $inverter; ?>][details][accepted]" value="0" id="ctt_accepted_no" class="icheck-select" <?php echo $ctt_accepted_no ?? ''; ?> required>
            </div>
        </div>
        <div class="col-md-12">
            Noted:
            <textarea id="ctt_note" class="form-control" rows="5" name="ctt[<?php echo $inverter; ?>][details][note]" value="<?php echo $ctt['details']['note'] ?? ''; ?>"></textarea>
        </div>
    </div>
</div>
