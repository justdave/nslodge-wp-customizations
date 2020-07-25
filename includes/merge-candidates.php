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
    if ($action == "merge_unit") { return nslodge_ue_merge_unit(); }
    if ($action == "merge_candidates") { return nslodge_ue_merge_candidates(); }
    if ($action == "delete_report") { return nslodge_ue_delete_report(); }
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

    global $wpdb, $post;
    $permalink = get_permalink($post->ID);
    $results = $wpdb->get_results("
SELECT
    u.ChapterName AS ChapterName,
    u.UnitType AS UnitType,
    CAST(u.UnitNumber AS unsigned) AS UnitNumberInt,
    u.UnitDesignator AS UnitDesignator,
    COUNT(*) AS NumReports,
    MAX(u.NumberElected) AS NumCandidatesReported,
    m.NumCandidatesSubmitted AS NumCandidatesSubmitted
FROM
    wp_oa_ue_units u
    LEFT OUTER JOIN (
        SELECT
            COUNT(DISTINCT BSAMemberID) AS NumCandidatesSubmitted,
            ChapterName,
            UnitType,
            UnitNumber,
            UnitDesignator
        FROM wp_oa_ue_candidates
        GROUP BY ChapterName, UnitType, UnitNumber, UnitDesignator
        ) m
        ON m.ChapterName = u.ChapterName
        AND m.UnitType = u.UnitType
        AND m.UnitNumber = u.UnitNumber
        AND m.UnitDesignator = u.UnitDesignator
WHERE
    BINARY CONCAT(u.ChapterName, u.UnitType, CAST(u.UnitNumber AS unsigned), u.UnitDesignator) NOT IN (SELECT
            BINARY CONCAT(c.ChapterName, c.UnitType, CAST(c.UnitNumber AS unsigned), c.UnitDesignator)
        FROM
            wp_oa_ue_candidates_merged c)
GROUP BY u.ChapterName, u.UnitType, CAST(u.UnitNumber AS unsigned), u.UnitDesignator
ORDER BY u.ChapterName, u.UnitType, CAST(u.UnitNumber AS unsigned), u.UnitDesignator
");

    $output = '
<div style="display:none;">
<form id="ue_merge_form" method="post">
<input type="hidden" name="ue_merge_action" value="merge_unit">
<input type="hidden" id="ue_merge_chapter" name="ue_merge_chapter" value="">
<input type="hidden" id="ue_merge_unittype" name="ue_merge_unittype" value="">
<input type="hidden" id="ue_merge_unitnum" name="ue_merge_unitnum" value="">
<input type="hidden" id="ue_merge_unitdesig" name="ue_merge_unitdesig" value="">
</form>
</div>
<script type="text/javascript"><!--
function ue_merge_submit(chapter, unittype, unitnum, unitdesig) {
  document.getElementById("ue_merge_chapter").value = chapter;
  document.getElementById("ue_merge_unittype").value = unittype;
  document.getElementById("ue_merge_unitnum").value = unitnum;
  document.getElementById("ue_merge_unitdesig").value = unitdesig;
  document.getElementById("ue_merge_form").submit();
  return true;
}
--></script>
<h2>Unprocessed Units:</h2>
';
    $output .= "<table border=1><tr><th>Chapter</th><th>Unit</th><th>Number of<br>Reports<br>Submitted</th><th>Candidates<br>Reported<br>on Form</th><th>Candidates<br>with Scout Data<br>Submitted</th><th>Action</th></tr>\n";
    $count = 0;
    foreach ($results as $row) {
        $link = $permalink . "?ue_merge_action=merge_unit&unit=" . $row->ChapterName . "|" . $row->UnitType . "|" . $row->UnitNumberInt . "|" . $row->UnitDesignator;
        $style = "";
        $submitted = $row->NumCandidatesSubmitted;
        $reported = $row->NumCandidatesReported;
        if (!($submitted)) { $submitted = 0; }
        if ($submitted != $reported) { $style = ' style="background-color: red;"'; }
        if ($reported > 0) {
            $output .= "<tr$style><td>" . esc_html($row->ChapterName) . "</td><td>" . esc_html(ns_format_unit($row->UnitType, $row->UnitNumberInt, $row->UnitDesignator)) . '</td><td>' . esc_html($row->NumReports) . '</td><td>' . esc_html($reported) . '</td><td>' . esc_html($submitted) . '</td><td><a href="#" onClick="ue_merge_submit(' . "'" . esc_html($row->ChapterName) . "','" . esc_html($row->UnitType) . "','" . esc_html($row->UnitNumberInt) . "','" . esc_html($row->UnitDesignator) . "'" . ');">Process this unit</a></td></tr>' . "\n";
            $count++;
        }
    }
    $output .= "</table>\n";
    $output .= "<p>Number of unprocessed units remaining: $count</p>\n";
    $output .= "<h4>Reports with zero candidates:</h4>\n";
    $output .= "<table border=1><tr><th>Chapter</th><th>Unit</th><th>Number of<br>Reports<br>Submitted</th><th>Candidates<br>Reported<br>on Form</th><th>Candidates<br>with Scout Data<br>Submitted</th><th>Action</th></tr>\n";
    $count = 0;
    foreach ($results as $row) {
        $link = $permalink . "?ue_merge_action=merge_unit&unit=" . $row->ChapterName . "|" . $row->UnitType . "|" . $row->UnitNumberInt . "|" . $row->UnitDesignator;
        $style = "";
        $submitted = $row->NumCandidatesSubmitted;
        $reported = $row->NumCandidatesReported;
        if (!($submitted)) { $submitted = 0; }
        if ($reported == 0) {
            $output .= "<tr$style><td>" . esc_html($row->ChapterName) . "</td><td>" . esc_html(ns_format_unit($row->UnitType, $row->UnitNumberInt, $row->UnitDesignator)) . '</td><td>' . esc_html($row->NumReports) . '</td><td>' . esc_html($reported) . '</td><td>' . esc_html($submitted) . '</td><td><a href="#" onClick="ue_merge_submit(' . "'" . esc_html($row->ChapterName) . "','" . esc_html($row->UnitType) . "','" . esc_html($row->UnitNumberInt) . "','" . esc_html($row->UnitDesignator) . "'" . ');">Process this unit</a></td></tr>' . "\n";
            $count++;
        }
    }
    $output .= "</table>\n";
    $output .= "<p>Number of no-election units reported: $count</p>\n";
    return $output;
}

function nslodge_ue_delete_report() {
    global $wpdb, $post;
    if (!isset($_POST['submission_id'])) {
        return "<h4>Error: no unit specified</h4>\n";
    }
    $submission_id = $_POST['submission_id'];
    $wpdb->query($wpdb->prepare("DELETE FROM wp_cf7dbplugin_submits WHERE submit_time = %s", $submission_id));
    $permalink = get_permalink($post->ID);
    $output = "<h4>Unit Report Deleted</h4>\n";
    $output .= "<p>Submission ID: " . esc_html($submission_id) . "</p>";
    $output .= '<a href="' . esc_url($permalink) . '">Back to unit list</a></p>';
    return $output;
}

function nslodge_ue_merge_unit() {
    global $wpdb, $post;
    if (!isset($_POST['ue_merge_chapter']) or !isset($_POST['ue_merge_unittype']) or !isset($_POST['ue_merge_unitnum'])) {
        return "<h4>Error: no unit specified</h4>\n";
    }
    $output = "";
    $chapter = $_POST['ue_merge_chapter'];
    $unittype = $_POST['ue_merge_unittype'];
    $unitnum = $_POST['ue_merge_unitnum'];
    $unitdesig = $_POST['ue_merge_unitdesig'];
    $unit_columnlist = [
        "DATE_FORMAT(FROM_UNIXTIME(Submitted), '%b %e, %Y  %l:%i %p') AS Submitted",
        'ChapterName',
        'UnitType',
        'CAST(UnitNumber AS unsigned) AS UnitNumberInt',
        'UnitDesignator',
        'NumberElected',
        'SubmitterType',
        'ElectionDate',
        'camp',
        'notification',
        'MeetingLocation',
        'UETeamNames',
        'RegActiveYouth',
        'YouthPresent',
        'NumberEligible',
        'NumberBallotsReturned',
        'NumberRequired',
        'UnitLeaderName',
        'UnitLeaderEmail',
        'UnitLeaderPhone',
        'AdditionalInfo',
        'SubmitterName',
        'SubmitterEmail',
        'SubmitterPhone'
    ];
    $candidate_columnlist = [
        "DATE_FORMAT(FROM_UNIXTIME(Submitted), '%b %e, %Y  %l:%i %p') AS Submitted",
        'SubmitterType',
        'ElectionDate',
        'ChapterName',
        'UnitType',
        'CAST(UnitNumber AS unsigned) AS UnitNumberInt',
        'UnitDesignator',
        'NumberElected',
        'FirstName',
        'MiddleName',
        'LastName',
        'Suffix',
        'BSAMemberID',
        'Gender',
        'HomeEmail',
        'ParentEmail',
        'HomePhone',
        'AddressLine1',
        'AddressLine2',
        'City',
        'State',
        'ZipCode',
        'DateOfBirth',
        'SubmitterName'
    ];
    $unit_results = $wpdb->get_results($wpdb->prepare("SELECT Submitted AS submit_time, " . join(', ',$unit_columnlist) . "
FROM
    wp_oa_ue_units
WHERE
    ChapterName = %s AND UnitType = %s AND CAST(UnitNumber AS unsigned) = %d AND UnitDesignator = %s
ORDER BY Submitted
", $chapter, $unittype, $unitnum, $unitdesig));
    if (!isset($unit_results)) { $output .= esc_html($wpdb->last_error); return $output; }
    $candidate_results = $wpdb->get_results($wpdb->prepare("SELECT Submitted AS submit_time, " . join(', ',$candidate_columnlist) . "
FROM
    wp_oa_ue_candidates
WHERE
    ChapterName = %s AND UnitType = %s AND CAST(UnitNumber AS unsigned) = %d AND UnitDesignator = %s
ORDER BY BSAMemberID, Submitted
", $chapter, $unittype, $unitnum, $unitdesig));
    if (!isset($candidate_results)) { $output .= esc_html($wpdb->last_error); return $output; }

    $output .= "<h2>" . htmlspecialchars($chapter) . " - " . htmlspecialchars($unittype) . " " . htmlspecialchars($unitnum) . "</h2>\n";
    $permalink = get_permalink($post->ID);
    $output .= '<a href="' . esc_url($permalink) . '">Back to unit list</a></p>';
    $output .= '<h3>Unit Information Section</h3>';
    $output .= '<div class="horiz_scroll oa_unit_table"><table><tr>';
    foreach ($unit_columnlist as $column) {
        if ($column == 'CAST(UnitNumber AS unsigned) AS UnitNumberInt') { $column = 'UnitNumber'; }
        if ($column == "DATE_FORMAT(FROM_UNIXTIME(Submitted), '%b %e, %Y  %l:%i %p') AS Submitted") { $column = 'Submitted'; }
        $output .= "<th>" . esc_html($column) . "</th>";
    } 
    $output .= "<th>Actions</th></tr>";
    foreach ($unit_results as $row) {
        $output .= "<tr>";
        foreach ($unit_columnlist as $column) {
            if ($column == 'CAST(UnitNumber AS unsigned) AS UnitNumberInt') { $column = 'UnitNumberInt'; }
            if ($column == "DATE_FORMAT(FROM_UNIXTIME(Submitted), '%b %e, %Y  %l:%i %p') AS Submitted") { $column = 'Submitted'; }
            $output .= "<td>" . esc_html($row->$column) . "</td>";
        }
        $output .= '<td>
        <form method="post">
        <input type="hidden" name="ue_merge_action" value="delete_report">
        <input type="hidden" name="submission_id" value="' . esc_html($row->submit_time) . '">
        <input type="submit" name="submit" value="Delete this report">
        </form></td>';
        $output .= "</tr>\n";
    }
    $output .= "</table></div>\n";

    $output .= '
<form id="ue_merge_form" method="post">
<input type="hidden" name="ue_merge_action" value="merge_candidates">
<input type="hidden" id="ue_merge_chapter" name="ue_merge_chapter" value="' . esc_html($chapter) . '">
<input type="hidden" id="ue_merge_unittype" name="ue_merge_unittype" value="' . esc_html($unittype) . '">
<input type="hidden" id="ue_merge_unitnum" name="ue_merge_unitnum" value="' . esc_html($unitnum) . '">
<input type="hidden" id="ue_merge_unitdesig" name="ue_merge_unitdesig" value="' . esc_html($unitdesig) . '">
';
    $candidates = [];
    foreach ($candidate_results as $row) {
        $candidates[$row->BSAMemberID][] = $row;
    }
    $output .= '<h3>Candidates</h3><p>Pick the ones to keep on the certified results:</p>';
    foreach (array_keys($candidates) as $bsaid) {
        $output .= '<div class="horiz_scroll oa_unit_table"><table><tr>';
        $output .= "<th>Keep</th>";
        foreach ($candidate_columnlist as $column) {
            if ($column == 'CAST(UnitNumber AS unsigned) AS UnitNumberInt') { $column = 'UnitNumber'; }
            if ($column == "DATE_FORMAT(FROM_UNIXTIME(Submitted), '%b %e, %Y  %l:%i %p') AS Submitted") { $column = 'Submitted'; }
            $output .= "<th>" . esc_html($column) . "</th>";
        }
        //$selected = ' checked="checked"';
        $selected = '';
        foreach ($candidates[$bsaid] as $row) {
            $output .= "<tr>";
            $output .= '<td><input type="radio" name="candidate-' . esc_html($bsaid) . '" value="' . esc_html($row->submit_time) . '"' . $selected . '></td>';
            $selected = "";
            foreach ($candidate_columnlist as $column) {
                if ($column == 'CAST(UnitNumber AS unsigned) AS UnitNumberInt') { $column = 'UnitNumberInt'; }
                if ($column == "DATE_FORMAT(FROM_UNIXTIME(Submitted), '%b %e, %Y  %l:%i %p') AS Submitted") { $column = 'Submitted'; }
                $coldata = $row->$column;
                $output .= "<td>" . esc_html($coldata) . "</td>";
            }
            $output .= "</tr>\n";
        }
        $output .= "</table></div>\n";
    }
    $output .= '<input type="submit" name="submit" value="Submit"></form>';
    return $output;
}

function nslodge_ue_merge_candidates() {
    global $wpdb, $post;
    if (!isset($_POST['ue_merge_chapter']) or !isset($_POST['ue_merge_unittype']) or !isset($_POST['ue_merge_unitnum'])) {
        return "<h4>Error: no unit specified</h4>\n";
    }
    $chapter = $_POST['ue_merge_chapter'];
    $unittype = $_POST['ue_merge_unittype'];
    $unitnum = $_POST['ue_merge_unitnum'];
    $unitdesig = $_POST['ue_merge_unitdesig'];
    $keys = array_keys($_POST);
    $output = "";
    foreach ($keys as $key) {
        if (substr($key,0,10) == "candidate-") {
            $bsaid = substr($key,10);
            $submitted = $_POST[$key];
            $alreadyfiled = $wpdb->get_var($wpdb->prepare("SELECT BSAMemberID FROM wp_oa_ue_candidates_merged WHERE BSAMemberID = %d", $bsaid));
            if (isset($alreadyfiled)) {
                $output .= '<span style="color: red;">BSAID ' . esc_html($bsaid) . ' failed, record already exists. Move unit back to Processing first<br>';
            }
            else {
                $output .= esc_html("Chapter $chapter " . ns_format_unit($unittype, $unitnum, $unitdesig) . " BSAID: $bsaid - rowid: $submitted") . "<br>";
                $theresult = $wpdb->query($wpdb->prepare("INSERT INTO wp_oa_ue_candidates_merged (
`Submitted`, `SubmitterType`, `ElectionDate`, `ChapterName`, `UnitType`, `UnitNumber`, `UnitDesignator`,
`NumberElected`, `FirstName`, `MiddleName`, `LastName`, `Suffix`, `BSAMemberID`,
`Gender`, `HomeEmail`, `ParentEmail`, `HomePhone`, `AddressLine1`, `AddressLine2`, `City`, `State`,
`ZipCode`, `DateOfBirth`, `SubmitterName`)
SELECT `Submitted`, `SubmitterType`, `ElectionDate`, `ChapterName`, `UnitType`, `UnitNumber`, `UnitDesignator`,
`NumberElected`, `FirstName`, `MiddleName`, `LastName`, `Suffix`, `BSAMemberID`, `Gender`, `HomeEmail`,
`ParentEmail`, `HomePhone`, `AddressLine1`, `AddressLine2`, `City`, `State`, `ZipCode`, `DateOfBirth`, `SubmitterName`
FROM wp_oa_ue_candidates WHERE ChapterName = %s AND UnitType = %s AND CAST(UnitNumber AS unsigned) = %d AND UnitDesignator = %s AND BSAMemberID = %d AND Submitted = %s", $chapter, $unittype, $unitnum, $unitdesig, $bsaid, $submitted));
                if (false === $theresult) { $output .= "SQL failed: " . $wpdb->last_error . "<br>"; };
            }
        }
    }
    $permalink = get_permalink($post->ID);
    $output .= '<a href="' . esc_url($permalink) . '">Back to unit list</a></p>';
    return $output;
}

function nslodge_ue_list_unexported() {
    global $wpdb;
    $output = "<h4>Units available to export:</h4>\n";
    $result = $wpdb->get_results("SELECT ChapterName, UnitType, UnitNumber, UnitDesignator, COUNT(*) AS NumCandidates, MAX(RowExported) AS AlreadyExported FROM wp_oa_ue_candidates_merged GROUP BY ChapterName, UnitType, UnitNumber, UnitDesignator ORDER BY ChapterName, UnitType, UnitNumber, UnitDesignator");
    if (!isset($result)) {
        $output .= "<p>Nothing available to export</p>\n";
        return $output;
    }
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    $output .= '
<div style="display:none;">
<form id="ue_merge_form" method="post" action="/ue/process-troops">
<input type="hidden" name="ue_merge_action" value="merge_unit">
<input type="hidden" id="ue_merge_chapter" name="ue_merge_chapter" value="">
<input type="hidden" id="ue_merge_unittype" name="ue_merge_unittype" value="">
<input type="hidden" id="ue_merge_unitnum" name="ue_merge_unitnum" value="">
<input desig="hidden" id="ue_merge_unitdesig" name="ue_merge_unitdesig" value="">
</form>
</div>
<script type="text/javascript"><!--
function ue_merge_submit(chapter, unittype, unitnum, unitdesig) {
  document.getElementById("ue_merge_chapter").value = chapter;
  document.getElementById("ue_merge_unittype").value = unittype;
  document.getElementById("ue_merge_unitnum").value = unitnum;
  document.getElementById("ue_merge_unitdesig").value = unitdesig;
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
<button class="oa_ue_button" onclick="ue_select_all(0); return false;">Select all unexported</button>
<button class="oa_ue_button" onclick="ue_select_all(1); return false;">Select ALL</button>
';
    $output .= '<table border="1">' . "\n";
    $output .= "<tr><th>Select<br>Unit</th><th>Already<br>Exported</th><th>Chapter Name</th><th>Unit</th><th>Candidates</th><th>Extra Stuff</th></tr>\n";
    foreach ($result as $row) {
        $row_token = str_replace(" ", "_", $row->ChapterName) . '-' . $row->UnitType . '-' . $row->UnitNumber . '-' . $row->UnitDesignator;
        $output .= '<td style="text-align: center;"><input type="hidden" id="unit-' . esc_html($row_token) . '" name="unit-' . esc_html($row_token) . '" value="1"><input type="checkbox" id="select-' . esc_html($row_token) . '" name="select-' . esc_html($row_token) . '"></td>';
        $selected = "";
        if ($row->AlreadyExported > 0) {
            $selected = ' checked="checked"';
        }
        $output .= '<td style="text-align: center;"><input type="checkbox" disabled="disabled" id="exported-' . esc_html($row_token) . '" name="exported-' . esc_html($row_token) .  '"' . $selected . '></td>';
        $output .= '<td>' . esc_html($row->ChapterName) . '</td><td style="text-align: right;">' . esc_html(ns_format_unit($row->UnitType, $row->UnitNumber, $row->UnitDesignator)) . '</td><td style="text-align: center;">' . esc_html($row->NumCandidates) . '</td>';
        $output .= '<td><a href="#" onClick="ue_merge_submit(' . "'" . esc_html($row->ChapterName) . "','" . esc_html($row->UnitType) . "','" . esc_html($row->UnitNumber) . "','" . esc_html($row->UnitDesignator) . "'" . ');">View reports</a></td>';
        $output .= "</tr>\n";
    }
    $output .= "</table>\n";
    $output .= '<br><input type="checkbox" id="markexported" name="markexported" checked="checked"><label for="markexported">Mark as exported after download</label><br>' . "\n";
    $output .= '<br><input type="submit" value="Export Selected Units to OALM" name="Submit" target="_blank">' . "\n";
    $output .= '<input class="oa_ue_button" type="submit" value="Clear Exported flags on selected units" name="Submit">' . "\n";
    $output .= '<input class="oa_ue_button" type="submit" value="Move Selected Units back to Processing Queue" name="Submit">' . "\n";
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
    echo '"Election Date","Chapter","Unit Type","Unit Number","Unit Designation","First Name","Middle Name","Last Name","Suffix","BSA ID","Gender","Home Email Address","Parent Email Address","Home Phone","Home Street 1","Home Street 2","Home City","Home State","Home Zip Code","Date Of Birth"' . "\n";
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
        'Gender',
        'HomeEmail',
        'ParentEmail',
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
        if (preg_match('/^select-([^-]+)-([^-]+)-(\d+)-([^-]*)$/', $key, $matches)) {
            $ChapterName = str_replace("_", " ", $matches[1]);
            $UnitType = $matches[2];
            $UnitNumber = $matches[3];
            $UnitDesignator = $matches[4];
            $unit_rows = $wpdb->get_results($wpdb->prepare("SELECT `" . join('`, `',$candidate_columnlist) .
                "` FROM wp_oa_ue_candidates_merged AS m
                   LEFT JOIN wp_oa_chapters AS c ON m.ChapterName = c.ChapterName
                   WHERE m.ChapterName = %s AND UnitType = %s AND UnitNumber = %d AND UnitDesignator = %s", $ChapterName, $UnitType, $UnitNumber, $UnitDesignator));
            foreach ($unit_rows as $row) {
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
                $wpdb->query($wpdb->prepare('UPDATE wp_oa_ue_candidates_merged SET RowExported = 1 WHERE ChapterName = %s AND UnitNumber = %d AND UnitDesignator = %s', $ChapterName, $UnitNumber, $UnitDesignator));
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
        if (preg_match('/^select-([^-]+)-([^-]+)-(\d+)-([^-]*)$/', $key, $matches)) {
            $ChapterName = str_replace("_", " ", $matches[1]);
            $UnitType = $matches[2];
            $UnitNumber = $matches[3];
            $UnitDesignator = $matches[4];
            $wpdb->query($wpdb->prepare("UPDATE wp_oa_ue_candidates_merged SET RowExported = 0 WHERE ChapterName = %s AND UnitType = %s AND UnitNumber = %d AND UnitDesignator = %s", $ChapterName, $UnitType, $UnitNumber, $UnitDesignator));
            $output .= "Chapter $ChapterName $UnitType $UnitNumber $UnitDesignator set back to unexported.<br><br>\n";
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
        if (preg_match('/^select-([^-]+)-([^-]+)-(\d+)-([^-]*)$/', $key, $matches)) {
            $ChapterName = str_replace("_", " ", $matches[1]);
            $UnitType = $matches[2];
            $UnitNumber = $matches[3];
            $UnitDesignator = $matches[4];
            $wpdb->query($wpdb->prepare("DELETE FROM wp_oa_ue_candidates_merged WHERE ChapterName = %s AND UnitType = %s AND UnitNumber = %d AND UnitDesignator = %s", $ChapterName, $UnitType, $UnitNumber, $UnitDesignator));
            $output .= "Chapter $ChapterName $UnitType $UnitNumber $UnitDesignator sent back to processing queue.<br><br>\n";
        }
    }
    $output .= '<a href="' . $permalink . '">Back to export queue</a>' . "\n";
    return $output;
}

