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
function nslodge_ue_merge( $attrs ) {

    $action = "list_unmerged";
    if (isset($_POST['ue_merge_action'])) {
        $action = $_POST['ue_merge_action'];
    }

    if ($action == "list_unmerged") { return nslodge_ue_list_unmerged(); }
    if ($action == "merge_troop") { return nslodge_ue_merge_troop(); }
    if ($action == "merge_candidates") { return nslodge_ue_merge_candidates(); }
    return "<h4>Unknown action: " . htmlspecialchars($action) . "</h4>\n";
}
add_shortcode( 'ue_candidate_merge', 'nslodge_ue_merge' );

function nslodge_ue_export() {
    $action = "list_unexported";
    if (isset($_POST['ue_export_action'])) {
        $action = $_POST['ue_export_action'];
    }
    if ($action == "list_unexported") { return nslodge_ue_list_unexported(); }
    if ($action == "export_candidates") { return nslodge_ue_export_candidates(); }
    return "<h4>Unknown action: " . htmlspecialchars($action) . "</h4>\n";
}
add_shortcode( 'ue_candidate_export', 'nslodge_ue_export' );

// Action code
function nslodge_ue_list_unmerged() {

    global $wpdb;
    $results = $wpdb->get_results("
SELECT 
    ChapterName, CAST(UnitNumber AS unsigned) AS UnitNumberInt, COUNT(*) AS NumReports, MAX(NumberElected) AS NumCandidates
FROM
    wp_oa_ue_troops
WHERE
    BINARY CONCAT(ChapterName, CAST(UnitNumber AS unsigned)) NOT IN (SELECT 
            BINARY CONCAT(ChapterName, CAST(UnitNumber AS unsigned))
        FROM
            wp_oa_ue_candidates_merged)
GROUP BY ChapterName , CAST(UnitNumber AS unsigned)
ORDER BY ChapterName , CAST(UnitNumber AS unsigned)
");

    $output = '
<div style="display:none;">
<form id="ue_merge_form" method="post">
<input type="hidden" name="ue_merge_action" value="merge_troop">
<input type="hidden" id="ue_merge_chapter" name="ue_merge_chapter" value="">
<input type="hidden" id="ue_merge_troop" name="ue_merge_troop" value="">
</form>
</div>
<script type="text/javascript"><!--
function ue_merge_submit(chapter, troop) {
  document.getElementById("ue_merge_chapter").value = chapter;
  document.getElementById("ue_merge_troop").value = troop;
  document.getElementById("ue_merge_form").submit();
  return true;
}
--></script>
<h2>Unprocessed Troops:</h2>
';
    $output .= "<table border=1><tr><th>Chapter</th><th>Troop</th><th>Reports</th><th>Candidates</th><th>Action</th></tr>\n";
    $count = 0;
    foreach ($results as $row) {
        $link = $permalink . "?ue_merge_action=merge_troop&troop=" . $row->ChapterName . "|" . $row->UnitNumberInt;
        $style = "";
        if ($row->NumCandidates == 0) { $style = ' style="color: gray; background-color: #ccc;"'; }
        $output .= "<tr$style><td>" . esc_html($row->ChapterName) . "</td><td>" . esc_html($row->UnitNumberInt) . '</td><td>' . esc_html($row->NumReports) . '</td><td>' . esc_html($row->NumCandidates) . '</td><td><a href="#" onClick="ue_merge_submit(' . "'" . esc_html($row->ChapterName) . "','" . esc_html($row->UnitNumberInt) . "'" . ');">Process this troop</a></td></tr>' . "\n";
        $count++;
    }
    $output .= "</table>\n";
    $output .= "<p>Number of unprocessed troops remaining: $count</p>\n";
    return $output;
}

function nslodge_ue_merge_troop() {
    global $wpdb, $post;
    if (!isset($_POST['ue_merge_chapter']) or !isset($_POST['ue_merge_troop'])) {
        return "<h4>Error: no unit specified</h4>\n";
    }
    $chapter = $_POST['ue_merge_chapter'];
    $troop = $_POST['ue_merge_troop'];
    $troop_columnlist = [
        'ChapterName',
        'UnitType',
        'CAST(UnitNumber AS unsigned) AS UnitNumberInt',
        'SubmitterType',
        'ElectionDate',
        'camp',
        'MeetingLocation',
        'UETeamNames',
        'RegActiveYouth',
        'YouthPresent',
        'NumberEligible',
        'NumberBallotsReturned',
        'NumberRequired',
        'NumberElected',
        'UnitLeaderName',
        'UnitLeaderEmail',
        'UnitLeaderPhone',
        'AdditionalInfo',
        'SubmitterName',
        'SubmitterEmail',
        'SubmitterPhone'
    ];
    $candidate_columnlist = [
        'Submitted',
        'SubmitterType',
        'ElectionDate',
        'ChapterName',
        'UnitType',
        'CAST(UnitNumber AS unsigned) AS UnitNumberInt',
        'NumberElected',
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
        'DateOfBirth',
        'SubmitterName'
    ];
    $troop_results = $wpdb->get_results($wpdb->prepare("SELECT " . join(', ',$troop_columnlist) . "
FROM
    wp_oa_ue_troops
WHERE
    ChapterName = %s AND CAST(UnitNumber AS unsigned) = %d
ORDER BY Submitted
", $chapter, $troop));
    if (!isset($troop_results)) { $output .= esc_html($wpdb->error); return $output; }
    $candidate_results = $wpdb->get_results($wpdb->prepare("SELECT " . join(', ',$candidate_columnlist) . "
FROM
    wp_oa_ue_candidates
WHERE
    ChapterName = %s AND CAST(UnitNumber AS unsigned) = %d
ORDER BY BSAMemberID, Submitted
", $chapter, $troop));
    if (!isset($candidate_results)) { $output .= esc_html($wpdb->error); return $output; }

    $output .= "<h2>" . htmlspecialchars($chapter) . " - Troop " . htmlspecialchars($troop) . "</h2>\n";
    $permalink = get_permalink($post->ID);
    $output .= '<a href="' . esc_url($permalink) . '">Back to troop list</a></p>';
    $output .= "<table border=1><tr>";
    foreach ($troop_columnlist as $column) {
        if ($column == 'CAST(UnitNumber AS unsigned) AS UnitNumberInt') { $column = 'UnitNumber'; }
        $output .= "<th>" . esc_html($column) . "</th>";
    } 
    foreach ($troop_results as $row) {
        $output .= "<tr>";
        foreach ($troop_columnlist as $column) {
            if ($column == 'CAST(UnitNumber AS unsigned) AS UnitNumberInt') { $column = 'UnitNumberInt'; }
            $output .= "<td>" . esc_html($row->$column) . "</td>";
        }
        $output .= "</tr>\n";
    }
    $output .= "</table>\n";

    $output .= '
<form id="ue_merge_form" method="post">
<input type="hidden" name="ue_merge_action" value="merge_candidates">
<input type="hidden" id="ue_merge_chapter" name="ue_merge_chapter" value="' . esc_html($chapter) . '">
<input type="hidden" id="ue_merge_troop" name="ue_merge_troop" value="' . esc_html($troop) . '">
';
    $candidates = [];
    foreach ($candidate_results as $row) {
        $candidates[$row->BSAMemberID][] = $row;
    }
    foreach (array_keys($candidates) as $bsaid) {
        $output .= "<table border=1><tr>";
        $output .= "<th>Keep</th>";
        foreach ($candidate_columnlist as $column) {
            if ($column == 'CAST(UnitNumber AS unsigned) AS UnitNumberInt') { $column = 'UnitNumber'; }
            $output .= "<th>" . esc_html($column) . "</th>";
        }
        //$selected = ' checked="checked"';
        $selected = '';
        foreach ($candidates[$bsaid] as $row) {
            $output .= "<tr>";
            $output .= '<td><input type="radio" name="candidate-' . esc_html($bsaid) . '" value="' . esc_html($row->Submitted) . '"' . $selected . '></td>';
            $selected = "";
            foreach ($candidate_columnlist as $column) {
                if ($column == 'CAST(UnitNumber AS unsigned) AS UnitNumberInt') { $column = 'UnitNumberInt'; }
                $output .= "<td>" . esc_html($row->$column) . "</td>";
            }
            $output .= "</tr>\n";
        }
        $output .= "</table>\n";
    }
    $output .= '<input type="submit" name="submit" value="Submit"></form>';
    return $output;
}

function nslodge_ue_merge_candidates() {
    global $wpdb, $post;
    if (!isset($_POST['ue_merge_chapter']) or !isset($_POST['ue_merge_troop'])) {
        return "<h4>Error: no unit specified</h4>\n";
    }
    $chapter = $_POST['ue_merge_chapter'];
    $troop = $_POST['ue_merge_troop'];
    $keys = array_keys($_POST);
    foreach ($keys as $key) {
        if (substr($key,0,10) == "candidate-") {
            $bsaid = substr($key,10);
            $submitted = $_POST[$key];
            $alreadyfiled = $wpdb->get_var($wpdb->prepare("SELECT BSAMemberID FROM wp_oa_ue_candidates_merged WHERE BSAMemberID = %d", $bsaid));
            if (isset($alreadyfiled)) {
                $output .= '<span style="color: red;">BSAID ' . esc_html($bsaid) . ' failed, record already exists. Move troop back to Processing first<br>';
            }
            else {
                $output .= "BSAID: $bsaid - rowid: $submitted<br>";
                $theresult = $wpdb->query($wpdb->prepare("INSERT INTO wp_oa_ue_candidates_merged (
`Submitted`, `SubmitterType`, `ElectionDate`, `ChapterName`, `UnitType`, `UnitNumber`,
`NumberElected`, `FirstName`, `MiddleName`, `LastName`, `Suffix`, `BSAMemberID`,
`HomeEmail`, `HomePhone`, `AddressLine1`, `AddressLine2`, `City`, `State`,
`ZipCode`, `DateOfBirth`, `SubmitterName`)
SELECT `Submitted`, `SubmitterType`, `ElectionDate`, `ChapterName`, `UnitType`, `UnitNumber`,
`NumberElected`, `FirstName`, `MiddleName`, `LastName`, `Suffix`, `BSAMemberID`, `HomeEmail`,
`HomePhone`, `AddressLine1`, `AddressLine2`, `City`, `State`, `ZipCode`, `DateOfBirth`, `SubmitterName`
FROM wp_oa_ue_candidates WHERE ChapterName = %s AND CAST(UnitNumber AS unsigned) = %d AND BSAMemberID = %d AND Submitted = %s", $chapter, $troop, $bsaid, $submitted));
                if (false === $theresult) { $output .= "SQL failed: " . $wpdb->last_error . "<br>"; };
            }
        }
    }
    $permalink = get_permalink($post->ID);
    $output .= '<a href="' . esc_url($permalink) . '">Back to troop list</a></p>';
    return $output;
}

function nslodge_ue_list_unexported() {
    global $wpdb;
    $output = "<h4>Units available to export:</h4>\n";
    $result = $wpdb->get_results("SELECT ChapterName, UnitNumber, COUNT(*) AS NumCandidates, MAX(RowExported) AS AlreadyExported FROM wp_oa_ue_candidates_merged GROUP BY ChapterName, UnitNumber ORDER BY ChapterName, UnitNumber");
    if (!isset($result)) {
        $output .= "<p>Nothing available to export</p>\n";
        return $output;
    }
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    $output .= '
<div style="display:none;">
<form id="ue_merge_form" method="post" action="/ue/process-troops">
<input type="hidden" name="ue_merge_action" value="merge_troop">
<input type="hidden" id="ue_merge_chapter" name="ue_merge_chapter" value="">
<input type="hidden" id="ue_merge_troop" name="ue_merge_troop" value="">
</form>
</div>
<script type="text/javascript"><!--
function ue_merge_submit(chapter, troop) {
  document.getElementById("ue_merge_chapter").value = chapter;
  document.getElementById("ue_merge_troop").value = troop;
  document.getElementById("ue_merge_form").submit();
  return true;
}
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
<button onclick="ue_select_all(0); return false;">Select all unexported</button>
<button onclick="ue_select_all(1); return false;">Select ALL</button>
';
    $output .= '<table border="1">' . "\n";
    $output .= "<tr><th>Select<br>Troop</th><th>Already<br>Exported</th><th>Chapter Name</th><th>Unit<br>Number</th><th>Candidates</th><th>Extra Stuff</th></tr>\n";
    foreach ($result as $row) {
        $row_token = str_replace(" ", "_", $row->ChapterName) . '-' . $row->UnitNumber;
        $output .= '<td style="text-align: center;"><input type="hidden" id="troop-' . esc_html($row_token) . '" name="troop-' . esc_html($row_token) . '" value="1"><input type="checkbox" id="select-' . esc_html($row_token) . '" name="select-' . esc_html($row_token) . '"></td>';
        $selected = "";
        if ($row->AlreadyExported > 0) {
            $selected = ' checked="checked"';
        }
        $output .= '<td style="text-align: center;"><input type="checkbox" disabled="disabled" id="exported-' . esc_html($row_token) . '" name="exported-' . esc_html($row_token) .  '"' . $selected . '></td>';
        $output .= '<td>' . esc_html($row->ChapterName) . '</td><td style="text-align: right;">' . esc_html($row->UnitNumber) . '</td><td style="text-align: center;">' . esc_html($row->NumCandidates) . '</td>';
        $output .= '<td><a href="#" onClick="ue_merge_submit(' . "'" . esc_html($row->ChapterName) . "','" . esc_html($row->UnitNumber) . "'" . ');">View reports</a></td>';
        $output .= "</tr>\n";
    }
    $output .= "</table>\n";
    $output .= '<br><input type="checkbox" id="markexported" name="markexported" checked="checked"><label for="markexported">Mark as exported after download</label><br>' . "\n";
    $output .= '<br><input type="submit" value="Export Selected Troops to OALM" name="Submit" target="_blank">' . "\n";
    $output .= '<input type="submit" value="Clear Exported flags on selected troops" name="Submit">' . "\n";
    $output .= '<input type="submit" value="Move Selected Troops back to Processing Queue" name="Submit">' . "\n";
    $output .= "</form>\n";
    return $output;
}

function nslodge_ue_export_candidates() {
    if (!isset($_POST['Submit'])) {
        return "<h4>Invalid Action, try again.</h4>\n";
    }
    $action = $_POST['Submit'];
    if (preg_match('/^export/i',$action)) { return nslodge_ue_do_cvs_export(); }
    if (preg_match('/^clear/i',$action)) { return nslodge_ue_do_clear_exports(); }
    if (preg_match('/^move/i',$action)) { return nslodge_ue_do_move_to_processing(); }
    return "<h4>Invalid Action, try again.</h4>\n";
}

function nslodge_ue_do_cvs_export() {
    global $wpdb, $post;
    $permalink = get_permalink($post->ID);
    ob_clean();
    header('Content-Type: text/csv; charset=UTF-8', true);
    header('Content-Disposition: attachment; filename="CandidatesForOALM.csv"', true);
    header('Content-Transfer-Encoding: binary', true);
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    echo '"Election Date","Chapter","Unit Type","Unit Number","First Name","Middle Name","Last Name","Suffix","BSA ID","Home Email Address","Home Phone","Home Street 1","Home Street 2","Home City","Home State","Home Zip Code","Date Of Birth"' . "\n";
    $candidate_columnlist = [
        'ElectionDate',
        'ChapterName',
        'UnitType',
        'UnitNumber',
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
        'DateOfBirth'
    ];
    $keys = array_keys($_POST);
    foreach ($keys as $key) {
        $matches = [];
        if (preg_match('/^select-(.*)-(\d+)$/', $key, $matches)) {
            $ChapterName = str_replace("_", " ", $matches[1]);
            $UnitNumber = $matches[2];
            $troop_rows = $wpdb->get_results($wpdb->prepare("SELECT `" . join('`, `',$candidate_columnlist) .
                "` FROM wp_oa_ue_candidates_merged WHERE ChapterName = %s AND UnitNumber = %d", $ChapterName, $UnitNumber));
            foreach ($troop_rows as $row) {
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
                $wpdb->query($wpdb->prepare('UPDATE wp_oa_ue_candidates_merged SET RowExported = 1 WHERE ChapterName = %s AND UnitNumber = %d', $ChapterName, $UnitNumber));
            }
        }
    }
    exit();
}

function nslodge_ue_do_clear_exports() {
    global $wpdb, $post;
    $permalink = get_permalink($post->ID);
    $output = "";
    $keys = array_keys($_POST);
    foreach ($keys as $key) {
        $matches = [];
        if (preg_match('/^select-(.*)-(\d+)$/', $key, $matches)) {
            $ChapterName = str_replace("_", " ", $matches[1]);
            $UnitNumber = $matches[2];
            $wpdb->query($wpdb->prepare("UPDATE wp_oa_ue_candidates_merged SET RowExported = 0 WHERE ChapterName = %s AND UnitNumber = %d", $ChapterName, $UnitNumber));
            $output .= "Chapter $ChapterName Troop $UnitNumber set back to unexported.<br><br>\n";
        }
    }
    $output .= '<a href="' . $permalink . '">Back to export queue</a>' . "\n";
    return $output;
}

function nslodge_ue_do_move_to_processing() {
    global $wpdb, $post;
    $permalink = get_permalink($post->ID);
    $output = "";
    $keys = array_keys($_POST);
    foreach ($keys as $key) {
        $matches = [];
        if (preg_match('/^select-(.*)-(\d+)$/', $key, $matches)) {
            $ChapterName = str_replace("_", " ", $matches[1]);
            $UnitNumber = $matches[2];
            $wpdb->query($wpdb->prepare("DELETE FROM wp_oa_ue_candidates_merged WHERE ChapterName = %s AND UnitNumber = %d", $ChapterName, $UnitNumber));
            $output .= "Chapter $ChapterName Troop $UnitNumber sent back to processing queue.<br><br>\n";
        }
    }
    $output .= '<a href="' . $permalink . '">Back to export queue</a>' . "\n";
    return $output;
}

