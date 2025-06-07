<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('create_form_breadcrumb')) {

    function create_form_breadcrumb($dataname = NULL) {
        $ci = & get_instance();
        $i = 2;
        $uri = $ci->uri->segment($i);
        $link = '<ul style="width: 98% !important;" class="page-breadcrumb">';
        $link .= '<li><a href="' . base_url() . 'forms"><i class="fa fa-home"></i> Forms</a></li>';

        if (!empty($dataname)) {
            $last_segment = count($ci->uri->segment_array());
            $link .= '<li><i class="fa fa-angle-right"></i>';
            $link .= '<a class="text-info" href="' . base_url('module/' . $ci->uri->segment(2) . '/' . $ci->uri->segment($last_segment)) . '">';
            $link .= '<b class="text-info">';
            $link .= ucfirst($dataname);
            $link .= '</b></a></li> ';
        } else {
            while ($uri != '') {
                $prep_link = '';
                for ($j = 1; $j <= $i; $j++) {
                    $prep_link .= $ci->uri->segment($j) . '/';
                }

                // GET MODULE NAME
                $qry_module = $ci->db->select('name, desc')->from('prime_module_navigations_main')->where('hashcode', $ci->uri->segment($i))->get()->row();

                if ($ci->uri->segment($i + 1) == '') {
                    $link .= '<li><i class="fa fa-angle-right"></i> <a href="' . base_url($prep_link) . '"><b class="text-info">';
                    $cname = ( $i == 2 ) ? '<b>' . $qry_module->name . '</b>' : $ci->uri->segment($i);
                    $link .= ucfirst($cname) . '</b></a></li> ';
                } else {
                    $link .= '<li><i class="fa fa-angle-right"></i> <a href="' . base_url($prep_link) . '">';
                    $cname = ( $i == 2 ) ? '<b>' . $qry_module->name . '</b>' : $ci->uri->segment($i);
                    $link .= ucfirst($cname) . '</a></li> ';
                }
                $i++;
                $uri = $ci->uri->segment($i);
            }
        }

        $link .= '</ul>';

        return $link;
    }

}

if (!function_exists('buildChecklistTree')) {
    function buildChecklistTree($parent_id, $indexed)
    {
        $children = [];

        foreach ($indexed as $id => $row) {
            if ($row['parent'] == $parent_id) {
                // Filter values: remove keys with blank/null values
                $values = array_filter($row, function ($v) {
                    return $v !== null && $v !== '';
                });

                $child_entry = [];
                if (!empty($values)) {
                    $child_entry['values'] = $values;
                }

                // Recurse for children
                $sub_children = buildChecklistTree($id, $indexed);
                if (!empty($sub_children)) {
                    $child_entry['children'] = $sub_children;
                } else {
                    $child_entry = $values;
                }

                // If there are no values (like deepest check), simplify structure
                if (empty($values)) {
                    $child_entry = array_filter($row, function ($v) {
                        return $v !== null && $v !== '';
                    });

                    // If this node has deeper children
                    $sub_children = buildChecklistTree($id, $indexed);
                    if (!empty($sub_children)) {
                        $child_entry['children'] = $sub_children;
                    } else {
                        $child_entry = $values;
                    }
                }

                $children[$id] = $child_entry;
            }
        }

        return $children;
    }
}

if (!function_exists('checklist_item_input')) {
    function checklist_item_input($item,$print = false) {
        //CONVERT <#INPUT> INTO ACTUAL INPUT FIELD
        if (is_array($item) && isset($item['item'])) {
            $html = '';
            $input_item = $item['item'];
            $input_val = $item['inputval'] ?? false;
            $itemtype = $item['type'];
            $disttype = false;

            $inputs = array($input_item);
            $vals = array($input_val);

            if (strpos($input_item, ';')) {
                $inputs = explode(';', $input_item);
                $disttype = 'cell';
            }

            if (strpos($input_item, '|')) {
                $inputs = explode('|', $input_item);
                $disttype = 'row';
            }

            if ($input_val && strpos($input_val, ';') !== false) {
                $vals = explode(';', $input_val);
            }

            $output = (count($inputs) > 1 && $itemtype == 'note') ? '<td class="' . $itemtype . ' row" >': '';
            foreach ($inputs as $i => $fields) {
                $field_val = $vals[$i] ?? false;
                if (preg_match('/<#(input|textarea)(?:_(.*))?>/', $fields, $matches)) {
                    $type = $matches[1];
                    $option = (isset($matches[2]) && $matches[2] != '') ? explode('_', $matches[2]) : false;
                    if ($option && count($option) > 0) {
                        $options = array();
                        foreach ($option as $attribute) {
                            if (strpos($attribute, ':') !== false) {
                                list($attr, $val) = explode(':', $attribute, 2);
                                $options[] = $attr . '="' . htmlspecialchars($val) . '"';
                            }
                        }

                        $option = implode(' ', $options);
                    }

                    $parts = explode('<#', $fields);
                    $prefix = $parts[0];
                    $suffix = substr($parts[1], strpos($parts[1], '>') + 1);

                    $multi = count($inputs) > 1 ? '[]' : '';
                    if ($type == 'input') {
                        $input_html = '<input ' . $option . ' name="checklist[' . $item['sysid'] . '][inputval]' . $multi . '" class="form-control inline" value="' . ($field_val ?? '') . '" style="border-bottom: 1.5px grey solid !important; display: table-cell" required >';
                        if ($print) {
                            $input_html = '<span class="form-field">'.$field_val.'</span>' ?? '';
                        }
                    }

                    if ($type == 'textarea') {
                        $input_html = '<textarea ' . $option . ' name="checklist[' . $item['sysid'] . '][inputval]' . $multi . '" class="form-control" rows="5" style="white-space: pre-line;" required >' . ($field_val ?? '') . '</textarea>';
                        if ($print) {
                            $input_html = '';
                            if ($field_val) {
                                $lines = preg_split('/\r\n|\r|\n/', $field_val);
                                $listItems = [];

                                foreach ($lines as $line) {
                                    if (preg_match('/^\s*\d+\.\s+(.*)$/', $line, $match)) {
                                        $listItems[] = trim($match[1]);
                                    } else {
                                        // Not a numbered list, return original text
                                        return $field_val;
                                    }
                                }

                                // If all lines are valid numbered items, wrap in <ol>
                                $input_html = "<ol>";
                                foreach ($listItems as $item) {
                                    $input_html .= "  <li>" . htmlspecialchars($item) . "</li>";
                                }
                                $input_html .= "</ol>";
                            }
                        }
                    }
                    $field = '';
                    if ($disttype == 'cell') {

                        if ($type == 'input') {
                            if ($print) {
                                $field .= $prefix.$input_html.$suffix;
                            } else {
                                $field .= '<label style="float: left">' . $prefix . '</label>';
                                $field .= '<div style="overflow: hidden;' . ($suffix ? ' float: left;' : '') . '">' . $input_html . '</div>';
                                $field .= ($suffix) ? '<label style="float: left">' . $suffix . '</label>' : '';
                            }
                        } else {
                            $field .= $prefix;
                            $field .= $input_html;
                        }

                        if (count($inputs) > 1) {

                            if ($itemtype == 'note' && strpos(strtolower($prefix), 'note:') !== false) {
                                $output .= '<h4 class="bold">Note:</h4>';
                            }
                            $output .= '<div class="col-md-12">';
                            $output .= $field;
                            $output .= '</div>';
                        } else {
                            $output .= $field;
                        }
                    }

                    if ($disttype == 'row') {
                        if ($print) {
                            $field .= $prefix.$input_html.$suffix;
                        } else {
                            $field .= '<label style="float: left">' . $prefix . '</label>';
                            $field .= '<div style="overflow: hidden;' . ($suffix ? ' float: left;' : '') . '">' . $input_html . '</div>';
                            $field .= ($suffix) ? '<label style="float: left">' . $suffix . '</label>' : '';
                        }

                        //$style = (count($inputs) > 1) ? 'style="width: '.(100/count($inputs)).'% !important;"' : 'style="width: 100% !important;"';

                        $output .= '<td>';
                        $output .= $field;
                        $output .= '</td>';
                    }

                    if (!$disttype) {
                        if ($print) {
                            $field .= $prefix.$input_html.$suffix;
                        } else {
                            $field .= '<label style="float: left">' . $prefix . '</label>';
                            $field .= '<div style="overflow: hidden;' . ($suffix ? ' float: left;' : '') . '">' . $input_html . '</div>';
                            $field .= ($suffix) ? '<label style="float: left">' . $suffix . '</label>' : '';
                        }

                        $output .= $field;
                    }
                }
            }
            if (count($inputs) > 1 && $itemtype == 'note') {
                $output .= '</td>';
                $output .= '<td></td><td></td><td></td>';
            }
            return $output;
        }
    }
}

if (!function_exists('splitLettersNumbers')) {
    //SOMEHOW THIS DOESN'T WORK ON TNC FILE. >:(
    function splitLettersNumbers($input) {
        // Check if the input contains only letters
        if (ctype_alpha($input)) {
            return [$input, $input];
        }

        // Check if the input starts with letters followed by numbers
        if (preg_match('/^([a-zA-Z]+)(\d+)$/', $input, $matches)) {
            return $matches;
        }

        // If none of the above, return the input as a single element
        return [$input, $input];
    }
}