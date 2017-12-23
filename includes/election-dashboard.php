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

function ns_election_widget() {
    global $wpdb;
    // $results = $wpdb->get_results("select b.chapter_num as chapter, greatest(0, count(distinct a.UnitNumber)) as number_submitted, count(distinct c.unit_num) as num_troops from wp_oa_chapters b left join wp_oa_ue_troops a on binary concat(0, b.chapter_num, ' - ', b.ChapterName) = binary a.ChapterName left join wp_oa_troops c on b.chapter_num = c.chapter_num group by c.chapter_num order by c.chapter_num");
    if (is_admin()) {
        ?><a href="/ue/dashboard">Go to Unit Elections Dashboard</a><br><?php
    }
    $results = $wpdb->get_results("
SELECT
    trp.chapter_num AS chapter,
    trp.unit_num AS troop,
    COUNT(rpts.UnitNumber) AS num_reports,
    COUNT(sch.tnum) AS num_reqs
FROM
    wp_nslodge_org.wp_oa_troops AS trp
    LEFT JOIN wp_oa_chapters AS chp ON BINARY trp.chapter_num = BINARY chp.chapter_num
    LEFT JOIN wp_oa_ue_troops AS rpts ON BINARY CONCAT('0', chp.chapter_num, ' - ', chp.ChapterName) = BINARY rpts.ChapterName AND BINARY trp.unit_num = BINARY rpts.UnitNumber
    LEFT JOIN wp_oa_ue_schedules AS sch ON BINARY trp.chapter_num = BINARY sch.ChapterNumber AND BINARY trp.unit_num = BINARY sch.tnum
GROUP BY trp.chapter_num, trp.unit_num
ORDER BY trp.chapter_num, trp.unit_num
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
        if (!array_key_exists($row->troop, $data[$row->chapter])) {
            $data[$row->chapter][$row->troop] = [];
        }
        $data[$row->chapter][$row->troop]['num_reports'] = $row->num_reports;
        if ($row->num_reports > 1) {
            $completed[$row->chapter] = $completed[$row->chapter] + 1;
        }
        $data[$row->chapter][$row->troop]['num_reqs'] = $row->num_reqs;
        if ($row->num_reqs > 1) {
            $requested[$row->chapter] = $requested[$row->chapter] + 1;
        }
        $data[$row->chapter][$row->troop]['scheduled'] = 0;
        if ((array_key_exists($row->chapter, $elecscheds)) &&
             (array_key_exists($row->troop, $elecscheds[$row->chapter]))) {
            $data[$row->chapter][$row->troop]['scheduled'] = 1;
        }
        if ($data[$row->chapter][$row->troop]['num_reports'] > 0) {
            $completed[$row->chapter] = $completed[$row->chapter] + 1;
        } else if ($data[$row->chapter][$row->troop]['scheduled'] > 0) {
            $scheduled[$row->chapter] = $scheduled[$row->chapter] + 1;
        } else if ($data[$row->chapter][$row->troop]['num_reqs'] > 0) {
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
        labels: ["Chapter 1", "Chapter 2", "Chapter 3", "Chapter 4", "Chapter 5", "Chapter 6", "Chapter 7", "Entire Lodge"],
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

    if ($action == "list_chapters") { return nslodge_ue_dashboard_list_chapters(); }
    return "<h4>Unknown action: " . esc_html($action) . "</h4>\n";
}
add_shortcode( 'ue_dashboard', 'nslodge_ue_dashboard' );

function nslodge_ue_dashboard_list_chapters() {
    ob_start();
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
    ?>
    <div>
    <div id="ue_links" style="float: left; margin-right: 1em;">
    <h5><?php if ($election_committee) echo "Public "; ?>UE Links</h5>
    <ul>
    <?php if ($election_committee) { ?>
    <li><a href="https://nslodge.org/ue/report">Submit an election report</a>
    <li><a href="https://nslodge.org/ue/adultnomination">Submit an Adult Nomination</a>
    <?php } ?>
    <li><a href="https://nslodge.org/ue/request">Request an election for a troop</a>
    <li><a href="https://nslodge.org/ue/calendar">Lodge-wide Election Calendar</a>
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
    <li><a href="https://nslodge.org/ue/process-troops">Merge/Certify Election Results</a>
    <li><a href="https://nslodge.org/ue/process-adults">Process Adult Nominations</a>
    <li><a href="https://nslodge.org/ue/export-candidates">Export Youth Candidates to OALM</a>
    <li><a href="https://nslodge.org/ue/export-adults">Export Adult Candidates to OALM</a>
    </ul>
    <?php } ?>
    </div>
    <div id="ue_master_chart" style="width: 500px; float: right;">
    <h5 style="margin-top: 0px;">2017-2018 Unit Election Status</h5>
    <?php
    ns_election_widget();
    ?>
    Elections should be scheduled by March 31st.<br>Makeup elections should be completed by April 30th.<br>
    </div>
    <div style="clear: both;"></div>
    <?php if ($election_committee) { ?>
    <div id="ue_election_requests" style="margin-top: 1em;">
    <h5>Outstanding Unscheduled Election Requests</h5>
    <?php echo nslodge_ue_schedreqs(); ?>
    <div style="clear: both;"></div>
    <?php
    }
    return ob_get_clean();
}
