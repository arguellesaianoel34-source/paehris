<?php
//Step 1: Get top-level rows with buildtype = $buildtype (4004,4005,4006)
//Step 2: Recursively get all children of those rows
$sql = "
    WITH RECURSIVE items_cte AS (
    SELECT * FROM frm_tnc_checklist_items WHERE buildtype = $buildtype AND subtype = 8

    UNION ALL

    SELECT c.* 
    FROM frm_tnc_checklist_items AS c
    JOIN items_cte p ON c.parent = p.sysid
)
SELECT * FROM items_cte ORDER BY parent,`order`;

";
$frm_query = $this->db->query($sql);

$indexed = array();
if ($frm_query->num_rows() > 0) {
    foreach ($frm_query->result() AS $row) {
        $indexed[$row->sysid] = (array)$row;
    }
}

// Build tree from top-level parent (0)
$checklist_items = buildChecklistTree(0, $indexed);

$tabs = array_column($checklist_items,'values');

usort($tabs, function($a, $b) {
    return $a['order'] <=> $b['order'];
});


$company_inspectors = array();
if (isset($partneracronym)) {
    $company_inspectors[] = $partneracronym;
}
if (isset($companyacronym)) {
    $company_inspectors[] = $companyacronym;
}

?>
<div class="tab-pane fade in active" id="tnc_checklist">
    <style type="text/css">
        .components tbody tr td:first-child:not(.note):before {
            content: "•";
            font-size: 150%;
            position: absolute;
            left: 5px;
        }
        .components tbody tr td:first-child {
            position: relative;
            padding-left: 20px;
            text-align: justify;
            text-justify: inter-word;
        }
    </style>
    <div class="portlet light">
        <div class="portlet-title tabbable-line">
            <ul class="nav nav-tabs">
                <?php
                foreach ($tabs AS $key => $tab) {
                    reset($tabs);
                    $active = ($key === key($tabs)) ? 'active' : '';

                    echo '<li class="'.$active.'">';
                    echo '<a href="#form_checklist_'.$tab['code'].'" data-toggle="tab"> '.strtoupper($tab['item']).'</a>';
                    echo '</li>';
                }
                ?>
            </ul>
        </div>
        <div class="portlet-body">
            <form id="frm_tnc_checklist" method="post" action="<?php echo base_url().'forms/tncsavechecklist'; ?>" data-title="Save checklist data?" data-text="Continue saving checklist data?">
                <div class="tab-content">
                    <?php
                    $first = key($checklist_items);
                    foreach ($checklist_items AS $clkey => $checklist) {


                        $active = ($clkey === key($checklist_items)) ? 'active' : '';
                        $data['items'] = $checklist['children'];
                        //$this->load->view('admin/pages/modules/forms/tnc/checklist_'.$checklist['values']['code'],$data);
                        $tbody = '';
                        $trows = '';
                        ?>
                        <div class="tab-pane fade in <?php echo $active; ?>" id="form_checklist_<?php echo $checklist['values']['code']; ?>">
                            <?php

                            $i = 1;
                            foreach ($checklist['children'] AS $itemid => $item) {
                                if (isset($item['children'])) {
                                    $values = $item['values'];
                                    ?>
                                    <div class="portlet light ">
                                        <div class="portlet-title">
                                            <div class="caption bold"><?php echo $i.'. '.$values['item']; ?></div>
                                        </div>
                                        <div class="portlet-body">
                                            <table class="table table-bordered table-condensed components"">
                                            <thead>
                                            <tr>
                                                <th rowspan="2" width="50%"></th>
                                                <th colspan="2" class="text-align-center">COMPLIED</th>
                                                <th rowspan="2" width="20%" class="text-align-center">REMARKS</th>
                                            </tr>
                                            <tr>
                                                <th class="text-align-center"><?php echo (count($company_inspectors) > 0) ? implode(' / ',$company_inspectors) : ''; ?> Representative</th>
                                                <th class="text-align-center"><?php echo $clientacronym ?? $client; ?> Representative</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($item['children'] AS $childid => $selections) {
                                                $val = $selections['values'] ?? $selections;
                                                $isChecked = [];
                                                //Lookup for values
                                                $response_qry = $this->db->select()
                                                    ->from('frm_tnc_checklist_responses')
                                                    ->where(['itemid' => $val['sysid'],'tncid' => $tncid,'status' => 1])
                                                    ->get()->row();

                                                if ($response_qry) {
                                                    if (isset($val['hasinput']) && $val['hasinput']) {
                                                        $val['inputval'] = $response_qry->field;
                                                    }
                                                    $check = explode(';',$response_qry->check);
                                                    foreach ($check AS $box => $checked) {
                                                        $isChecked[$box] = $checked ? 'checked' : '';
                                                    }
                                                }
                                                ?>
                                                <tr>
                                                    <td><?php echo (isset($val['hasinput']) && $val['hasinput']) ? checklist_item_input($val) : $val['item']; ?></td>
                                                    <td class="text-align-center"><input type="checkbox" name="checklist[<?php echo $childid; ?>][checkval][]" value="1" <?php echo $isChecked[0] ?? false; ?> class="icheck"></td>
                                                    <td class="text-align-center"><input type="checkbox" name="checklist[<?php echo $childid; ?>][checkval][]" value="1" <?php echo $isChecked[1] ?? false; ?> class="icheck"></td>
                                                    <td><input name="checklist[<?php echo $childid; ?>][remarks]" value="<?php echo $response_qry ? $response_qry->remarks : false; ?>" class="form-control"></td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php } else {
                                    $isChecked = [];
                                    //Lookup for values
                                    $response_qry = $this->db->select()
                                        ->from('frm_tnc_checklist_responses')
                                        ->where(['itemid' => $itemid,'tncid' => $tncid,'status' => 1])
                                        ->get()->row();

                                    if ($response_qry) {
                                        if (isset($item['hasinput']) && $item['hasinput']) {
                                            $item['inputval'] = $response_qry->field;
                                        }
                                        $check = explode(';',$response_qry->check);
                                        foreach ($check AS $box => $checked) {
                                            $isChecked[$box] = $checked ? 'checked' : '';
                                        }
                                    }

                                    if ($item['type'] != 'external') {
                                        $tbody .= '<tr>';
                                        if ($item['type'] == 'check') {
                                            $tbody .= '<td>' . ((isset($item['hasinput']) && $item['hasinput']) ? checklist_item_input($item) : $item['item']) . '</td>';
                                            $tbody .= '<td class="text-align-center"><input type="checkbox" name="checklist['.$itemid.'][checkval][1]" value="1" '.($isChecked[0] ?? false).' class="icheck"></td>';
                                            $tbody .= '<td class="text-align-center"><input type="checkbox" name="checklist['.$itemid.'][checkval][2]" value="1" '.($isChecked[1] ?? false).' class="icheck"></td>';
                                            $tbody .= '<td><input name="checklist['.$itemid.'][remarks]" value="'.($response_qry ? $response_qry->remarks : false).'" class="form-control"></td>';
                                        } else {
                                            $tbody .= (isset($item['hasinput']) && $item['hasinput']) ? checklist_item_input($item) : $item['item'];
                                        }

                                        $tbody .= '</tr>';
                                    } else {
                                        $trows .= '<tr>';
                                        $trows .= (isset($item['hasinput']) && $item['hasinput']) ? checklist_item_input($item) : $item['item'];
                                        $trows .= '</tr>';
                                    }
                                }
                                $i++;
                            }
                            if ($tbody != '') {

                                ?>
                                <div class="portlet light ">
                                    <div class="portlet-body">
                                        <table class="table table-bordered table-condensed components" style="table-layout: fixed !important;">
                                            <thead>
                                            <tr>
                                                <th rowspan="2" width="50%"></th>
                                                <th colspan="2" class="text-align-center">COMPLIED</th>
                                                <th rowspan="2" width="20%" class="text-align-center">REMARKS</th>
                                            </tr>
                                            <tr>
                                                <th class="text-align-center"><?php echo (count($company_inspectors) > 0) ? implode(' / ',$company_inspectors) : ''; ?> Representative</th>
                                                <th class="text-align-center"><?php echo $clientacronym ?? $client; ?> Representative</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php echo $tbody; ?>
                                            </tbody>
                                        </table>
                                        <?php if ($trows != '') { ?>
                                            <table class="table table-condensed">
                                                <?php echo $trows; ?>
                                            </table>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <div class="portlet-footer">
                    <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>