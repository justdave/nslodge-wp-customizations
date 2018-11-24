<?php
/*
 * Copyright (C) 2017 David D. Miller
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

add_action('wp_dashboard_setup', 'ns_add_election_widget');

function ns_add_election_widget() {
    global $wp_meta_boxes;
    add_meta_box('oa_election_widget', 'Unit Election Status', 'ns_election_widget', 'dashboard', 'side', 'high');
}

add_action( 'admin_enqueue_scripts', 'my_enqueue' );
add_action( 'wp_enqueue_scripts', 'my_enqueue' );
function my_enqueue($hook) {
    wp_enqueue_script( 'Chart-js', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.bundle.min.js', __FILE__ );
}

add_action( 'init', 'ns_ue_permalinks' );
function ns_ue_permalinks() {
    add_rewrite_tag('%chapter%','([^&]+)');
    add_rewrite_rule(
        'ue/dashboard/chapter-([abcde])/?$',
        'index.php?page_id=1999&chapter=$matches[1]',
        'top'
    );
}
#add_filter( 'query_vars', 'ns_ue_query_vars' );
#function ns_ue_query_vars( $query_vars ) {
#    $query_vars[] = 'chapter';
#    return $query_vars;
#}

function ns_election_widget() {
    global $wpdb;
    if (is_admin()) {
        ?><a href="/ue/dashboard">Go to Unit Elections Dashboard</a><br><?php
    }
    $results = $wpdb->get_results("
SELECT
    unit.chapter_num AS chapter,
    unit.unit_type AS unit_type,
    unit.unit_num AS unit_num,
    COUNT(rpts.UnitNumber) AS num_reports,
    COUNT(sch.UnitNum) AS num_reqs
FROM
    wp_oa_units AS unit
    LEFT JOIN wp_oa_chapters AS chp ON BINARY unit.chapter_num = BINARY chp.chapter_num
    LEFT JOIN wp_oa_ue_troops AS rpts ON BINARY CONCAT('0', chp.chapter_num, ' - ', chp.ChapterName) = BINARY rpts.ChapterName AND BINARY unit.unit_num = BINARY rpts.UnitNumber
    LEFT JOIN wp_oa_ue_schedules AS sch ON BINARY chp.ChapterName = BINARY sch.ChapterName AND BINARY unit.unit_type = BINARY sch.UnitType AND BINARY unit.unit_num = BINARY sch.UnitNum
GROUP BY unit.chapter_num, unit.unit_num
ORDER BY unit.chapter_num, unit.unit_num
");

    $completed = [];
    $scheduled = [];
    $requested = [];
    $num_troops = [];
    $data = [];
    $elecscheds = nslodge_ue_getelections('all');
    foreach ($results AS $row) {
        if (!array_key_exists($row->chapter, $completed)) {
            $completed[$row->chapter] = 0;
        }
        if (!array_key_exists($row->chapter, $scheduled)) {
            $scheduled[$row->chapter] = 0;
        }
        if (!array_key_exists($row->chapter, $requested)) {
            $requested[$row->chapter] = 0;
        }
        if (!array_key_exists($row->chapter, $num_troops)) {
            $num_troops[$row->chapter] = 0;
        }
        if (!array_key_exists($row->chapter, $data)) {
            $data[$row->chapter] = [];
        }
        if (!array_key_exists($row->unit_type, $data[$row->chapter])) {
            $data[$row->chapter][$row->unit_type] = [];
        }
        if (!array_key_exists($row->unit_num, $data[$row->chapter][$row->unit_type])) {
            $data[$row->chapter][$row->unit_type][$row->unit_num] = [];
        }
        $data[$row->chapter][$row->unit_type][$row->unit_num]['num_reports'] = $row->num_reports;
        $data[$row->chapter][$row->unit_type][$row->unit_num]['num_reqs'] = $row->num_reqs;
        $data[$row->chapter][$row->unit_type][$row->unit_num]['scheduled'] = 0;
        if ((array_key_exists($row->chapter, $elecscheds)) &&
             (array_key_exists($row->unit_type, $elecscheds[$row->chapter])) &&
             (array_key_exists($row->unit_num, $elecscheds[$row->chapter][$row->unit_type]))) {
            $data[$row->chapter][$row->unit_type][$row->unit_num]['scheduled'] = 1;
        }
        if ($data[$row->chapter][$row->unit_type][$row->unit_num]['num_reports'] > 0) {
            $completed[$row->chapter] = $completed[$row->chapter] + 1;
        } else if ($data[$row->chapter][$row->unit_type][$row->unit_num]['scheduled'] > 0) {
            $scheduled[$row->chapter] = $scheduled[$row->chapter] + 1;
        } else if ($data[$row->chapter][$row->unit_type][$row->unit_num]['num_reqs'] > 0) {
            $requested[$row->chapter] = $requested[$row->chapter] + 1;
        }
        $num_troops[$row->chapter] = $num_troops[$row->chapter] + 1;
    }

    ?>
<canvas id="nsElectionChart" width="200" height="100"></canvas>
<script type="text/javascript">
var ctx = document.getElementById("nsElectionChart");
var ue_chartconfig = {
    type: 'horizontalBar',
    data: {
        labels: ["Chapter A", "Chapter B", "Chapter C", "Chapter D", "Chapter E", "Entire Lodge"],
        datasets: [
        {
            label: 'Complete',
            data: [<?php
            $completed_troops = 0;
            $total_troops = 0;
            foreach (array_keys($completed) AS $key) {
                if ($total_troops > 0) { echo ","; };
                $this_completed = $completed[$key];
                $this_total = $num_troops[$key];
                echo htmlspecialchars(ceil(($completed[$key] / $num_troops[$key]) * 100));
                $completed_troops = $completed_troops + $completed[$key];
                $total_troops = $total_troops + $num_troops[$key];
            }
            echo "," . htmlspecialchars(ceil(($completed_troops / $total_troops) * 100));
            ?>],
            backgroundColor: 'rgba(0, 224, 255, 0.2)',
            borderColor: 'rgba(0, 224, 255, 1)',
            borderWidth: 1
        },
        {
            label: "Scheduled",
            data: [<?php
            $scheduled_troops = 0;
            $total_troops = 0;
            foreach (array_keys($scheduled) AS $key) {
                if ($total_troops > 0) { echo ","; };
                $this_scheduled = $scheduled[$key];
                $this_total = $num_troops[$key];
                echo htmlspecialchars(ceil(($scheduled[$key] / $num_troops[$key]) * 100));
                $scheduled_troops = $scheduled_troops + $scheduled[$key];
                $total_troops = $total_troops + $num_troops[$key];
            }
            echo "," . htmlspecialchars(ceil(($scheduled_troops / $total_troops) * 100));
            ?>],
            backgroundColor: 'rgba(0, 220, 0, 0.2)',
            borderColor: 'rgba(0, 220, 0, 1)',
            borderWidth: 1
        },
        {
            label: "Requested",
            data: [<?php
            $requested_troops = 0;
            $total_troops = 0;
            foreach (array_keys($requested) AS $key) {
                if ($total_troops > 0) { echo ","; };
                $this_requested = $requested[$key];
                $this_total = $num_troops[$key];
                echo htmlspecialchars(ceil(($requested[$key] / $num_troops[$key]) * 100));
                $requested_troops = $requested_troops + $requested[$key];
                $total_troops = $total_troops + $num_troops[$key];
            }
            echo "," . htmlspecialchars(ceil(($requested_troops / $total_troops) * 100));
            ?>],
            backgroundColor: 'rgba(255, 105, 9, 0.2)',
            borderColor: 'rgba(255, 105, 9, 0.2)',
            borderWidth: 1
        }
        ]
    },
    options: {
        tooltips: {
            callbacks: {
                label: function(tooltipitem, data) {
                    return data.datasets[tooltipitem.datasetIndex].label + ": " + tooltipitem.xLabel + "%";
                }
            }
        },
        scales: {
            xAxes: [{
                stacked: true,
                ticks: {
                    beginAtZero:true,
                    suggestedMax:100
                }
            }],
            yAxes: [{
                stacked: true
            }]
        }
    }
};
var ue_chart = new Chart(ctx, ue_chartconfig);
</script>
    <?php
}

if ( is_admin() ) {
    add_action( 'wp_ajax_ns_election_chartdata', 'ns_election_chartdata' );
}

function ns_election_chartdata() {
    // alert("Foo!");
}

// Add Shortcodes
function nslodge_ue_dashboard( $attrs ) {

    $action = "list_chapters";
    if (isset($_POST['ue_dashboard_action'])) {
        $action = $_POST['ue_dashboard_action'];
    }
    if (get_query_var('chapter')) {
        return nslodge_ue_dashboard_chapter();
    }

    if ($action == "list_chapters") { return nslodge_ue_dashboard_list_chapters(); }
    return "<h4>Unknown action: " . esc_html($action) . "</h4>\n";
}
add_shortcode( 'ue_dashboard', 'nslodge_ue_dashboard' );

function nslodge_ue_dashboard_chapter() {
    global $wpdb;
    $user = wp_get_current_user();
    $election_committee = 0;
    $election_admin = 0;
    if (( in_array( 'election_manager', (array) $user->roles ) ) ||
        ( in_array( 'administrator',    (array) $user->roles ) )) {
        $election_committee = 1;
    }
    if ( in_array( 'administrator',    (array) $user->roles ) ) {
        $election_admin = 1;
    }
    ob_start();
    $chapterletter = get_query_var('chapter');
    $chaptername = $wpdb->get_var($wpdb->prepare("SELECT SelectorName FROM wp_oa_chapters WHERE ChapterName = %s", Array($chapterletter)));
    $chapter = $wpdb->get_var($wpdb->prepare("SELECT chapter_num FROM wp_oa_chapters WHERE ChapterName = %s", Array($chapterletter)));
    echo "<h2>Election information for Chapter " . htmlspecialchars($chapterletter) . " - " . htmlspecialchars($chaptername) . "</h2>\n";
    if (!$election_committee) {
        echo "<p>Please log in to an account with permission to view this data.</p>\n";
    } else {
        echo '<p><a href="/ue/dashboard">&larr; Back to Lodge view</a></p>';
        $results = $wpdb->get_results($wpdb->prepare("
SELECT
    id,
    COUNT(rpts.UnitNumber) AS reports,
    MAX(rpts.ElectionDate) AS election_date,
    COUNT(sched.UnitNum) AS requests,
    district_name,
    unit_type,
    unit_num,
    unit_city,
    charter_org,
    ul_full_name,
    ul_phone_number,
    ul_email,
    cc_full_name,
    cc_phone_number,
    cc_email,
    adv_full_name,
    adv_phone_number,
    adv_email,
    rep_full_name,
    rep_phone_number,
    rep_email
FROM
    wp_oa_units AS un
        LEFT JOIN
    wp_oa_chapters AS ch ON un.chapter_num = ch.chapter_num
        LEFT JOIN
    wp_oa_districts AS di ON un.district_num = di.district_num
        LEFT JOIN
    wp_oa_ue_troops AS rpts ON BINARY ch.ChapterName = BINARY rpts.ChapterName
        AND BINARY un.unit_type = BINARY rpts.UnitType
        AND BINARY un.unit_num = BINARY rpts.UnitNumber
        LEFT JOIN
    wp_oa_ue_schedules AS sched ON BINARY ch.SelectorName = BINARY sched.ChapterName AND BINARY un.unit_type = BINARY sched.UnitType AND BINARY un.unit_num = BINARY sched.UnitNum
WHERE
    un.chapter_num = %d
GROUP BY un.district_num, un.unit_type, un.unit_num
ORDER BY un.unit_type, un.unit_num, un.district_num
    ",
        Array($chapter)));
        $elecscheds = nslodge_ue_getelections($chapter);
        echo '<table class="wp_table oa_chapter_info">';
        echo "\n<tr><th>Status</th><th>Reports Filed</th><th>District</th><th colspan='2'>Unit</th><th>City</th><th>Election Date</th><th>Scoutmaster</th><th>Committee Chair</th><th>OA Rep</th></tr>\n";
        foreach ($results as $row) {
            $status = 'Not Scheduled';
            $rowcolor = '#f22';
            $election_date = '';
            if ($row->requests > 0) {
                $status = 'Requested';
                $rowcolor = '#f82';
            }
            if ((array_key_exists($chapter, $elecscheds)) &&
                 (array_key_exists($row->unit_num, $elecscheds[$chapter]))) {
                $status = 'Scheduled';
                $rowcolor = '#0c7';
                $election_date = $elecscheds[$chapter][$row->unit_num];
            }
            if (($status == 'Scheduled') && (strtotime($election_date) < time())) {
                $status = 'Missing Paperwork';
                $rowcolor = '#f0f';
            }
            if ($row->reports > 0) {
                $status = 'Completed';
                $rowcolor = 'cyan';
                $election_date = $row->election_date;
            }
            echo '<tr style="background-color: ' . $rowcolor . '">';
            echo "<td>" . htmlspecialchars($status) . "</td>";
            #elseif ($rowcolor == 'orange') { echo '<td>Requested</td>'; }
            #elseif ($rowcolor == 'purple') { echo '<td>Past Schedule</td>'; }
            if ($election_date) {
                $election_date = date("Y-m-d",strtotime($election_date));
            }
            $row->election_date = $election_date;
            foreach (Array('reports','district_name','unit_type','unit_num','unit_city','election_date') as $item) {
                echo "<td>" . htmlspecialchars($row->$item) . "</td>\n";
            }
            echo "<td>" . htmlspecialchars($row->ul_full_name) . "<br>" . htmlspecialchars($row->ul_email) . "<br>" . htmlspecialchars($row->ul_phone_number) . "</td>\n";
            echo "<td>" . htmlspecialchars($row->cc_full_name) . "<br>" . htmlspecialchars($row->cc_email) . "<br>" . htmlspecialchars($row->cc_phone_number) . "</td>\n";
            echo "<td>" . htmlspecialchars($row->rep_full_name) . "<br>" . htmlspecialchars($row->rep_email) . "<br>" . htmlspecialchars($row->rep_phone_number) . "</td>\n";
            echo "</tr>\n";
        }
        echo "</table>\n";
    }
    return ob_get_clean();
}

function nslodge_ue_dashboard_list_chapters() {
    ob_start();
    $user = wp_get_current_user();
    $homeurl = home_url();
    $election_committee = 0;
    $election_admin = 0;
    if (( in_array( 'election_manager', (array) $user->roles ) ) ||
        ( in_array( 'administrator',    (array) $user->roles ) )) {
        $election_committee = 1;
    }
    if ( in_array( 'administrator',    (array) $user->roles ) ) {
        $election_admin = 1;
    }
    ?>
    <div>
    <div id="ue_links" style="float: left; margin-right: 1em;">
    <h5><?php if ($election_committee) echo "Public "; ?>UE Links</h5>
    <ul>
    <?php if ($election_committee) { ?>
    <li><a href="<?php echo htmlspecialchars($homeurl) ?>/ue/report">Submit an election report</a>
    <li><a href="<?php echo htmlspecialchars($homeurl) ?>/ue/adultnomination">Submit an Adult Nomination</a>
    <?php } ?>
    <li><a href="<?php echo htmlspecialchars($homeurl) ?>/ue/request">Request an election for a troop</a>
    <li><a href="<?php echo htmlspecialchars($homeurl) ?>/ue/calendar">Lodge-wide Election Calendar</a>
    </ul>
    <?php if (!$election_committee) { ?>
    If you are a chapter chief or a<br>
    member of the Lodge election team<br>
    you can
    <a href="<?php echo wp_login_url( get_permalink() ); ?>" title="Login">Login</a>
    to see more information.<br>
    <?php } ?>
    <?php if ($election_admin) { ?>
    <h5>Administrative UE Links</h5>
    <ul>
    <li><a href="<?php echo htmlspecialchars($homeurl) ?>/ue/process-troops">Merge/Certify Election Results</a>
    <li><a href="<?php echo htmlspecialchars($homeurl) ?>/ue/process-adults">Process Adult Nominations</a>
    <li><a href="<?php echo htmlspecialchars($homeurl) ?>/ue/export-candidates">Export Youth Candidates to OALM</a>
    <li><a href="<?php echo htmlspecialchars($homeurl) ?>/ue/export-adults">Export Adult Candidates to OALM</a>
    </ul>
    <?php } ?>
    </div>
    <div id="ue_master_chart" style="width: 500px; float: right;">
    <h5 style="margin-top: 0px;">2018-2019 Unit Election Status</h5>
    <p>This page is under construction to remodel for the new election year and new chapters. Please check back later!</p>
    <?php
    ns_election_widget();
    ?>
    Elections should be scheduled by March 31st.<br>Makeup elections should be completed by April 30th.<br>
    </div>
    <div style="clear: both; margin-bottom: 1em;">
    </div>
    <?php if ($election_committee) { ?>
    <p style="text-align: center;">Chapter details:
    <a href="<?php echo htmlspecialchars($homeurl) ?>/ue/dashboard?chapter=a">Chapter A</a>
    <a href="<?php echo htmlspecialchars($homeurl) ?>/ue/dashboard?chapter=b">Chapter B</a>
    <a href="<?php echo htmlspecialchars($homeurl) ?>/ue/dashboard?chapter=c">Chapter C</a>
    <a href="<?php echo htmlspecialchars($homeurl) ?>/ue/dashboard?chapter=d">Chapter D</a>
    <a href="<?php echo htmlspecialchars($homeurl) ?>/ue/dashboard?chapter=e">Chapter E</a>
    </p>
    <?php
    } ?>
    <p style="text-align: center;">The code behind this page lives on <a href="https://github.com/justdave/nslodge-wp-customizations/">GitHub</a> - <a href="https://github.com/justdave/nslodge-wp-customizations/commits/master">Change Log</a> - <a href="https://github.com/justdave/nslodge-wp-customizations/issues">Bug Reports/Feature Requests</a></p>
    <?php if ($election_committee) { ?>
    <div id="ue_election_requests" style="margin-top: 1em;">
    <h5>Outstanding Unscheduled Election Requests</h5>
    <?php echo nslodge_ue_schedreqs(Array("chapter" => "all")); ?>
    <div style="clear: both;"></div>
    <?php
    }
    return ob_get_clean();
}
