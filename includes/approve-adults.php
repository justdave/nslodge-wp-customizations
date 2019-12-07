<?php
/*
 * Copyright (C) 2016 David D. Miller
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Add Shortcodes
function nslodge_ue_process_adults( $attrs ) {

    $action = "list_nominations";
    if (isset($_POST['ue_adult_action'])) {
        $action = $_POST['ue_adult_action'];
    }

    if ($action == "list_nominations") { return nslodge_ue_list_nominations(); }
    if ($action == "view_nomination") { return nslodge_ue_view_nomination(); }
    if ($action == "adult_approval") { return nslodge_ue_adult_approval(); }
    return "<h4>Unknown action: " . esc_html($action) . "</h4>\n";
}
add_shortcode( 'ue_process_adults', 'nslodge_ue_process_adults' );

function nslodge_ue_exportadults() {
    $action = "list_unexported";
    if (isset($_POST['ue_export_action'])) {
        $action = $_POST['ue_export_action'];
    }
    if ($action == "list_unexported") { return nslodge_ue_list_unexported_adults(); }
    if ($action == "export_candidates") { return nslodge_ue_export_adults(); }
    return "<h4>Unknown action: " . esc_html($action) . "</h4>\n";
}
add_shortcode( 'ue_adult_export', 'nslodge_ue_exportadults' );

// Action code
function nslodge_ue_list_nominations() {

    global $wpdb;
    $nomination_columns = [
        'Submitted',
        'FirstName',
        'MiddleName',
        'LastName',
        'Suffix',
        'ChapterName',
        'UnitType',
        'UnitNumber',
        'UnitDesignator',
        'CurrentPosition',
        'AddressLine1',
        'AddressLine2',
        'City',
        'State',
        'ZipCode',
        'Gender',
        'DateOfBirth',
        'BSAMemberID',
        'HomeEmail',
        'HomePhone',
        'numyears',
        'training',
        'prevposition',
        'RankAsYouth',
        'commactivities',
        'employment',
        'camping',
        'abilities',
        'purpose',
        'rolemodel',
        'recommendation',
        'accept',
        'smname',
        'ccpname',
        'submitter-name',
        'submitter-email',
        'submitter-phone'
    ]; 
    $units = [];

    $results = $wpdb->get_results("SELECT `ChapterName`, `UnitType`, `UnitNumber`, `UnitDesignator`, COUNT(*) AS NumElected FROM wp_oa_ue_candidates_merged GROUP BY `ChapterName`, `UnitType`, `UnitNumber`, `UnitDesignator` ORDER BY `ChapterName`, `UnitType`, `UnitNumber`, `UnitDesignator`");
    foreach ($results as $row) {
        $unit_token = ns_get_unit_token($row->ChapterName, $row->UnitType, $row->UnitNumber, $row->UnitDesignator);
        $units[$unit_token]['chapter'] = $row->ChapterName;
        $units[$unit_token]['unittype'] = $row->UnitType;
        $units[$unit_token]['unitnum'] = $row->UnitNumber;
        $units[$unit_token]['unitdesig'] = $row->UnitDesignator;
        $units[$unit_token]['youth_elected'] = $row->NumElected;
        $units[$unit_token]['nominations'] = [];
    }
    $results = $wpdb->get_results("SELECT `ChapterName`, `UnitType`, `UnitNumber`, `UnitDesignator`, COUNT(*) AS ReportsSubmitted, MAX(NumberElected) AS NumElected FROM wp_oa_ue_units GROUP BY `ChapterName`, `UnitType`, `UnitNumber`, `UnitDesignator` ORDER BY `ChapterName`, `UnitType`, `UnitNumber`, `UnitDesignator`");
    foreach ($results as $row) {
        $unit_token = ns_get_unit_token($row->ChapterName, $row->UnitType, $row->UnitNumber, $row->UnitDesignator);
        if (!isset($units[$unit_token])) {
            $units[$unit_token]['chapter'] = $row->ChapterName;
            $units[$unit_token]['unittype'] = $row->UnitType;
            $units[$unit_token]['unitnum'] = $row->UnitNumber;
            $units[$unit_token]['unitdesig'] = $row->UnitDesignator;
            $units[$unit_token]['youth_elected'] = 0;
            $units[$unit_token]['nominations'] = [];
            if ($row->NumElected > 0) {
                $units[$unit_token]['report_pending'] = 1;
            }
        }
    }

    $results = $wpdb->get_results("SELECT `" . join ("`, `",$nomination_columns) . "` FROM wp_oa_ue_adults WHERE `recommendation` LIKE 'Unit%'");
    foreach ($results as $row) {
        $unit_token = ns_get_unit_token($row->ChapterName, $row->UnitType, $row->UnitNumber, $row->UnitDesignator);
        if (!isset($units[$unit_token])) {
            $units[$unit_token]['chapter'] = $row->ChapterName;
            $units[$unit_token]['unittype'] = $row->UnitType;
            $units[$unit_token]['unitnum'] = $row->UnitNumber;
            $units[$unit_token]['unitdesig'] = $row->UnitDesignator;
            $units[$unit_token]['youth_elected'] = 0;
            $units[$unit_token]['no_report_filed'] = 1;
        }
        if (isset($units[$unit_token]['nominations'][$row->BSAMemberID])) {
            $units[$unit_token]['warnings'][] = "multiple nominations for same BSAID";
        }
        $units[$unit_token]['nominations'][$row->BSAMemberID] = $row;
    }
    $output = '
<div style="display:none;">
<form id="ue_adult_form" method="post">
<input type="hidden" name="ue_adult_action" value="view_nomination">
<input type="hidden" id="ue_adult_chapter" name="ue_adult_chapter" value="">
<input type="hidden" id="ue_adult_unittype" name="ue_adult_unittype" value="">
<input type="hidden" id="ue_adult_unitnum" name="ue_adult_unitnum" value="">
<input type="hidden" id="ue_adult_unitdesig" name="ue_adult_unitdesig" value="">
<input type="hidden" id="ue_adult_bsaid" name="ue_adult_bsaid" value="">
</form>
</div>
<script type="text/javascript"><!--
function ue_adult_submit(chapter, unittype, unitnum, unitdesig, bsaid) {
  document.getElementById("ue_adult_chapter").value = chapter;
  document.getElementById("ue_adult_unittype").value = unittype;
  document.getElementById("ue_adult_unitnum").value = unitnum;
  document.getElementById("ue_adult_unitdesig").value = unitdesig;
  document.getElementById("ue_adult_bsaid").value = bsaid;
  document.getElementById("ue_adult_form").submit();
  return false;
}
--></script>
<style type="text/css"><!--
.adult_outside {
    border: 2px solid black;
    border-spacing: 0px;
}
.adult_outside tr th {
    border: 2px solid black;
}
.adult_outside tr.oa_unit td {
    border-top: 3px solid black;
    border-left: 1px solid black;
    border-right: 1px solid black;
    border-bottom: 1px solid gray;
}
.adult_outside tr.nominations td {
    border-top: 0px;
    border-left: 1px solid black;
    border-right: 1px solid black;
    border-bottom: 1px solid black;
    padding: 2px;
}
.adult_inside {
    border-spacing: 0px;
    border: 0px;
    width: 100%;
}
.adult_inside tr td {
    border-top: 0px;
    border-left: 1px solid black;
    border-right: 1px solid black;
    border-bottom: 1px solid gray;
}

--></style>
<h2>Adult Nominations:</h2>
';
    $output .= '<table class="adult_outside"><tr><th>Chapter</th><th>Unit</th><th>Youth<br>Elected</th><th>Adults<br>Allowed<br><span style="font-size: xx-small;">green = adjusted<br>for scoutmaster</span></th><th>Nominations<br>Submitted<br><span style="font-size: xx-small;">red = too many<br>submitted<br>green = still<br>allowed more</span></th></tr>' . "\n";
    ksort($units);
    foreach ($units as $unit) {
        $style = "";
        $elected = $unit['youth_elected'];
        $allowed = ceil($elected/3);
        if ((0 == $elected) && (isset($unit['report_pending']))) {
            $elected = "(report pending)";
        }
        if ((0 == $elected) && (isset($unit['no_report_filed']))) {
            $elected = "(no report filed)";
        }
        $nominations = count($unit['nominations']);
        $nomination_style = "";
        $allowed_style = "";
        $scoutmaster_nominated = 0;
        foreach ($unit['nominations'] as $nomination) {
            if ($nomination->CurrentPosition == "Scoutmaster") {
                $scoutmaster_nominated = 1;
            }
        }
        if ($scoutmaster_nominated && $allowed) {
            $allowed++;
            $allowed_style = ' style="background-color: lightgreen;"';
        }
        if ($nominations > $allowed) {
            $nomination_style = ' style="background-color: red;"';
        }
        if ($nominations < $allowed) {
            $nomination_style = ' style="background-color: lightgreen;"';
        }
        $output .= sprintf('<tr class="oa_unit"><td>%s</td><td>%s</td><td>%s</td><td' . $allowed_style .'>%s</td><td' . $nomination_style . '>%s</td></tr>' . "\n",
            esc_html($unit['chapter']), esc_html(ns_format_unit($unit['unittype'], $unit['unitnum'], $unit['unitdesig'])), esc_html($elected), esc_html($allowed), esc_html($nominations));
        $output .= '<tr class="nominations"><td colspan="5"><table class="adult_inside">';
        foreach ($unit['nominations'] as $nomination) {
            $name = sprintf("%s %s %s %s", $nomination->FirstName, $nomination->MiddleName, $nomination->LastName, $nomination->Suffix);
            $name = ltrim(rtrim(str_replace("  ", " ", $name)));
            $jsclick = '<a href="#" onClick="ue_adult_submit(' . "'%s','%s','%s','%s','%s'" . '); return false;">View</a>';
            $link = sprintf($jsclick, esc_html($unit['chapter']), esc_html($unit['unittype']), esc_html($unit['unitnum']), esc_html($unit['unitdesig']), esc_html($nomination->BSAMemberID));
            $approved = $wpdb->get_var($wpdb->prepare("SELECT Approved FROM wp_oa_ue_adult_nominations WHERE BSAMemberId = %s", $nomination->BSAMemberID));
            $status = '<span style="color: #b80;">Pending</span>';
            if (isset($approved)) {
                if ($approved) {
                    $status = '<span style="color: green;">Approved</span>';
                } else {
                    $status = '<span style="color: red;">Rejected</span>';
                }
            }
            $output .= sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",
                esc_html(date("Y-m-d H:m:s", $nomination->Submitted)), $name, esc_html($nomination->CurrentPosition), $status, $link);
        }
        $output .= '</table></td></tr>';
    }
    $output .= "</table>\n";
    #$output .= "<pre>" . esc_html(var_dump_ret($units)) . "</pre>";
    $distresults = $wpdb->get_results("SELECT `" . join ("`, `",$nomination_columns) . "` FROM wp_oa_ue_adults WHERE `recommendation` LIKE 'District%'");
    $output .= '<table class="adult_outside"><tr><th>District/Council Recommendations</th></tr>' . "\n";
    $distreqs = [];
    foreach ($distresults as $row) {
        $distreqs[$row->BSAMemberID] = $row;
    }
    $output .= '<tr class="nominations"><td><table class="adult_inside">';
    foreach ($distreqs as $nomination) {
        $name = sprintf("%s %s %s %s", $nomination->FirstName, $nomination->MiddleName, $nomination->LastName, $nomination->Suffix);
        $name = ltrim(rtrim(str_replace("  ", " ", $name)));
        $jsclick = '<a href="#" onClick="ue_adult_submit(' . "'%s','%s','%s','%s','%s'" . '); return false;">View</a>';
        $link = sprintf($jsclick, esc_html($nomination->ChapterName), esc_html($nomination->UnitType), esc_html($nomination->UnitNumber), esc_html($nomination->UnitDesignator), esc_html($nomination->BSAMemberID));
        $approved = $wpdb->get_var($wpdb->prepare("SELECT Approved FROM wp_oa_ue_adult_nominations WHERE BSAMemberId = %s", $nomination->BSAMemberID));
        $status = '<span style="color: #b80;">Pending</span>';
        if (isset($approved)) {
            if ($approved) {
                $status = '<span style="color: green;">Approved</span>';
            } else {
                $status = '<span style="color: red;">Rejected</span>';
            }
        }
        $output .= sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",
            esc_html(date("Y-m-d H:m:s", $nomination->Submitted)), $name, esc_html($nomination->CurrentPosition), $status, $link);
    }
    $output .= '</table></td></tr>';
    $output .= '</table>';
    return $output;
}

function nslodge_ue_view_nomination() {
    global $wpdb, $post;
    $permalink = get_permalink($post->ID);
    if (!isset($_POST['ue_adult_chapter']) or !isset($_POST['ue_adult_unittype']) or !isset($_POST['ue_adult_unitnum'])) {
        return "<h4>Error: no unit specified</h4>\n";
    }
    if (!isset($_POST['ue_adult_bsaid'])) {
        return "<h4>Error: no member ID specified</h4>\n";
    }
    $chapter = $_POST['ue_adult_chapter'];
    $unittype = $_POST['ue_adult_unittype'];
    $unitnum = $_POST['ue_adult_unitnum'];
    $unitdesig = $_POST['ue_adult_unitdesig'];
    $bsaid = $_POST['ue_adult_bsaid'];
    $nomination_columns = [
        'Submitted',
        'FirstName',
        'MiddleName',
        'LastName',
        'Suffix',
        'ChapterName',
        'UnitType',
        'UnitNumber',
        'UnitDesignator',
        'CurrentPosition',
        'AddressLine1',
        'AddressLine2',
        'City',
        'State',
        'ZipCode',
        'Gender',
        'DateOfBirth',
        'BSAMemberID',
        'HomeEmail',
        'HomePhone',
        'numyears',
        'training',
        'prevposition',
        'RankAsYouth',
        'commactivities',
        'employment',
        'camping',
        'abilities',
        'purpose',
        'rolemodel',
        'recommendation',
        'accept',
        'smname',
        'ccpname',
        'submitter-name',
        'submitter-email',
        'submitter-phone'
    ]; 

    $output = '
<style type="text/css"><!--
.nomination_form {
    border-spacing: 0px;
    margin-bottom: 10px;
}
.nomination_form th,td {
    border: 1px solid black;
    padding: .25em;
}
.nomination_form th {
    background-color: lightgray;
    text-align: left;
}
#ue_approve {
    background-color: green;
    margin: 10px;
}
#ue_reject {
    background-color: red;
    margin: 10px;
}
#ue_pending {
    background-color: #eb2;
    margin: 10px;
}
--></style>
<form id="ue_adult_form" method="post">
<input type="hidden" name="ue_adult_action" value="adult_approval">
<input type="hidden" id="ue_adult_chapter" name="ue_adult_chapter" value="' . esc_html($chapter) . '">
<input type="hidden" id="ue_adult_unittype" name="ue_adult_unittype" value="' . esc_html($unittype) . '">
<input type="hidden" id="ue_adult_unitnum" name="ue_adult_unitnum" value="' . esc_html($unitnum) . '">
<input type="hidden" id="ue_adult_unitdesig" name="ue_adult_unitdesig" value="' . esc_html($unitdesig) . '">
<input type="hidden" id="ue_adult_bsaid" name="ue_adult_bsaid" value="' . esc_html($bsaid) . '">
';
    $nominations = $wpdb->get_results($wpdb->prepare("SELECT `" . join ("`, `",$nomination_columns) . "` FROM wp_oa_ue_adults WHERE ChapterName = %s AND UnitType = %s AND CAST(UnitNumber AS UNSIGNED) = %s AND UnitDesignator = %s AND BSAMemberID = %s", $chapter, $unittype, $unitnum, $unitdesig, $bsaid));
    foreach ($nominations as $nomination) {
        $output .= '<table class="nomination_form">' . "\n";
        foreach ($nomination_columns as $column) {
            $output .= "<tr><th>" . esc_html($column) . "</th><td>" . esc_html($nomination->$column) . "</td></tr>\n";
        }
        $output .= "</table>\n";
    }
    $output .= '<input type="submit" name="submit" id="ue_approve" value="Set to Approved">';
    $output .= '<input type="submit" name="submit" id="ue_reject" value="Set to Rejected">';
    $output .= '<input type="submit" name="submit" id="ue_pending" value="Set to Pending">';
    $output .= '<a href="' . $permalink . '">Back to list</a>';
    $output .= '</form>';
    return $output;
}

function nslodge_ue_adult_approval() {
    global $wpdb, $post;
    if (!isset($_POST['ue_adult_chapter']) or !isset($_POST['ue_adult_unittype']) or !isset($_POST['ue_adult_unitnum'])) {
        return "<h4>Error: no unit specified</h4>\n";
    }
    if (!isset($_POST['ue_adult_bsaid'])) {
        return "<h4>Error: no member ID specified</h4>\n";
    }
    if (!isset($_POST['submit'])) {
        return "<h4>Error: no action specified</h4>\n";
    }
    $chapter = $_POST['ue_adult_chapter'];
    $unittype = $_POST['ue_adult_unittype'];
    $unitnum = $_POST['ue_adult_unitnum'];
    $unitdesig = $_POST['ue_adult_unitdesig'];
    $bsaid = $_POST['ue_adult_bsaid'];
    $action = $_POST['submit'];
    $nomination_columns = [
        'Submitted',
        'ChapterName',
        'UnitType',
        'UnitNumber',
        'UnitDesignator',
        'FirstName',
        'MiddleName',
        'LastName',
        'Suffix',
        'BSAMemberID',
        'HomeEmail',
        'HomePhone',
        'AddressLine1',
        'AddressLine2',
        'City',
        'State',
        'ZipCode',
        'Gender',
        'DateOfBirth',
    ]; 
    $error = "";
    if ($action == "Set to Pending") {
        $error = $wpdb->delete("wp_oa_ue_adult_nominations", ["BSAMemberID" => $bsaid]);
    } else {
        $nomination = $wpdb->get_row($wpdb->prepare("SELECT `" . join ("`, `",$nomination_columns) . "` FROM wp_oa_ue_adults WHERE ChapterName = %s AND UnitType = %s AND UnitNumber = %s AND UnitDesignator = %s AND BSAMemberID = %s", $chapter, $unittype, $unitnum, $unitdesig, $bsaid));
        $approved = 0;
        if ($action == "Set to Approved") {
            $approved = 1;
        }
        $nomination->Approved = $approved;
        $nomination->ElectionDate = date("Y-m-d", $nomination->Submitted);
        $error = $wpdb->replace("wp_oa_ue_adult_nominations", json_decode(json_encode($nomination), true));
    }
    $permalink = get_permalink($post->ID);
    header("Location: $permalink", true);
    $output .= '<a href="' . esc_url($permalink) . '">Back to unit list</a></p>';
    return $output;
}

function nslodge_ue_list_unexported_adults() {
    global $wpdb;
    $output = "<h4>Adults available to export:</h4>\n";
    $result = $wpdb->get_results("SELECT BSAMemberID, ChapterName, UnitType, UnitNumber, UnitDesignator, FirstName, MiddleName, LastName, Suffix, RowExported FROM wp_oa_ue_adult_nominations WHERE Approved = 1 ORDER BY ChapterName, UnitType, UnitNumber, UnitDesignator");
    if (!isset($result)) {
        $output .= "<p>Nothing available to export</p>\n";
        return $output;
    }
    $output .= '
<script type="text/javascript"><!--
function ue_select_all(reallyall) {
    var inputs = document.getElementsByTagName("input");
    for (var i=0; i < inputs.length; i++) {
        thematch = inputs[i].id.match(/^select-(.*)$/);
        if (thematch) {
            row_token = thematch[1];
            newchecked = "";
            if (reallyall) { newchecked = "checked"; }
            else {
                export_box = document.getElementById("exported-" + row_token);
                if (export_box && !export_box.checked) {
                    newchecked = "checked";
                }
            }
            inputs[i].checked = newchecked;
        }
    }
    return false;
}
--></script>
<form id="ue_merge_form" method="post">
<input type="hidden" name="ue_export_action" value="export_candidates">
<button class="oa_ue_button" onclick="ue_select_all(0); return false;">Select all unexported</button>
<button class="oa_ue_button" onclick="ue_select_all(1); return false;">Select ALL</button>
';
    $output .= '<table border="1">' . "\n";
    $output .= "<tr><th>Select<br>Adult</th><th>Already<br>Exported</th><th>Chapter Name</th><th>Unit</th><th>CandidateName</th></tr>\n";
    foreach ($result as $row) {
        $name = sprintf("%s %s %s %s", $row->FirstName, $row->MiddleName, $row->LastName, $row->Suffix);
        $name = ltrim(rtrim(str_replace("  ", " ", $name)));
        $output .= '<td style="text-align: center;"><input type="checkbox" id="select-' . esc_html($row->BSAMemberID) . '" name="select-' . esc_html($row->BSAMemberID) . '"></td>';
        $selected = "";
        if ($row->RowExported > 0) {
            $selected = ' checked="checked"';
        }
        $output .= '<td style="text-align: center;"><input type="checkbox" disabled="disabled" id="exported-' . esc_html($row->BSAMemberID) . '" name="exported-' . esc_html($row->BSAMemberID) .  '"' . $selected . '></td>';
        $output .= '<td>' . esc_html($row->ChapterName) . '</td><td style="text-align: right;">' . esc_html(ns_format_unit($row->UnitType, $row->UnitNumber, $row->UnitDesignator)) . '</td><td style="text-align: center;">' . esc_html($name) . '</td>';
        $output .= "</tr>\n";
    }
    $output .= "</table>\n";
    $output .= '<br><input type="checkbox" id="markexported" name="markexported" checked="checked"><label for="markexported">Mark as exported after download</label><br>' . "\n";
    $output .= '<br><input type="submit" value="Export Selected Adults to OALM" name="Submit" target="_blank">' . "\n";
    $output .= '<input type="submit" value="Clear Exported flags on selected adults" name="Submit">' . "\n";
    $output .= "</form>\n";
    return $output;
}

function nslodge_ue_export_adults() {
    if (!isset($_POST['Submit'])) {
        return "<h4>Invalid Action, try again.</h4>\n";
    }
    $action = $_POST['Submit'];
    if (preg_match('/^export/i',$action)) { return nslodge_ue_do_adult_cvs_export(); }
    if (preg_match('/^clear/i',$action)) { return nslodge_ue_do_clear_adult_exports(); }
    return "<h4>Invalid Action, try again.</h4>\n";
}

function nslodge_ue_do_adult_cvs_export() {
    global $wpdb, $post;
    $permalink = get_permalink($post->ID);
    ob_clean();
    header('Content-Type: text/csv; charset=UTF-8', true);
    header('Content-Disposition: attachment; filename="CandidatesForOALM.csv"', true);
    header('Content-Transfer-Encoding: binary', true);
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    echo '"Election Date","Chapter","Unit Type","Unit Number","Unit Designation","First Name","Middle Name","Last Name","Suffix","BSA ID","Home Email Address","Home Phone","Home Street 1","Home Street 2","Home City","Home State","Home Zip Code","Gender","Date Of Birth"' . "\n";
    $candidate_columnlist = [
        'ElectionDate',
        'SelectorName',
        'UnitType',
        'UnitNumber',
        'UnitDesignator',
        'FirstName',
        'MiddleName',
        'LastName',
        'Suffix',
        'BSAMemberID',
        'HomeEmail',
        'HomePhone',
        'AddressLine1',
        'AddressLine2',
        'City',
        'State',
        'ZipCode',
        'Gender',
        'DateOfBirth'
    ];
    $keys = array_keys($_POST);
    foreach ($keys as $key) {
        $matches = [];
        if (preg_match('/^select-(\d+)$/', $key, $matches)) {
            $bsaid = $matches[1];
            $adult_rows = $wpdb->get_results($wpdb->prepare("SELECT `" . join('`, `',$candidate_columnlist) .
                "` FROM wp_oa_ue_adult_nominations AS n" .
                " LEFT JOIN wp_oa_chapters AS c ON n.ChapterName = c.ChapterName WHERE BSAMemberID = %d", $bsaid));
            foreach ($adult_rows as $row) {
                $comma = "";
                foreach ($candidate_columnlist as $column) {
                    $value = $row->$column;
                    if ($column == "HomePhone") {
                        // strip all non-digit from phone numbers
                        $value = preg_replace('/[^0-9]/','',$value);
                    }
                    if ($column == "DateOfBirth") {
                        // enforce all dates are YYYY-MM-DD
                        $matches = [];
                        if (preg_match('/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{1,2})$/', $value, $matches)) {
                            $year = $matches[3] + 0; // coerce to integer
                            if ($year > 20) { $year = $year + 1900; } else { $year = $year + 2000; }
                            $value = sprintf("%04d-%02d-%02d", $year, $matches[1], $matches[2]);
                        } // 4/8/98 (2 digit year last)
                        elseif (preg_match('/^((?:19|20)\d{2})(\d{2})(\d{2})$/', $value, $matches)) {
                            $value = sprintf("%04d-%02d-%02d", $matches[1], $matches[2], $matches[3]);
                        } // 19980408 (8 digits, year first)
                        elseif (preg_match('/^(\d{2})(\d{2})((?:19|20)\d{2})$/', $value, $matches)) {
                            $value = sprintf("%04d-%02d-%02d", $matches[3], $matches[1], $matches[2]);
                        } // 04081998 (8 digits, year last)
                        elseif (preg_match('/^(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})$/', $value, $matches)) {
                            $value = sprintf("%04d-%02d-%02d", $matches[1], $matches[2], $matches[3]);
                        } // 1998/04/08 (any grouping of 4 digits, 2 digits, 2 digits, year first)
                        elseif (preg_match('/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})$/', $value, $matches)) {
                            $value = sprintf("%04d-%02d-%02d", $matches[3], $matches[1], $matches[2]);
                        } // 04/08/1998 (any grouping of 2 digits, 2 digits, 4 digits, year last)
                    }
                    echo $comma . '"' . str_replace('"','""',$value) . '"';
                    $comma = ",";
                }
                echo "\n";
            }
            if (isset($_POST['markexported'])) {
                $wpdb->query($wpdb->prepare('UPDATE wp_oa_ue_adult_nominations SET RowExported = 1 WHERE BSAMemberID = %d', $bsaid));
            }
        }
    }
    exit();
}

function nslodge_ue_do_clear_adult_exports() {
    global $wpdb, $post;
    $permalink = get_permalink($post->ID);
    $output = "";
    $keys = array_keys($_POST);
    foreach ($keys as $key) {
        $matches = [];
        if (preg_match('/^select-(\d+)$/', $key, $matches)) {
            $bsaid = $matches[1];
            $wpdb->query($wpdb->prepare("UPDATE wp_oa_ue_adult_nominations SET RowExported = 0 WHERE BSAMemberID = %d", $bsaid));
            $output .= "BSA ID " . esc_html($bsaid) . " set back to unexported.<br><br>\n";
        }
    }
    $output .= '<a href="' . $permalink . '">Back to export queue</a>' . "\n";
    return $output;
}
