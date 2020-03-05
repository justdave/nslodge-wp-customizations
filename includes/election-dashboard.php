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

function ns_get_electdata($chapter = "all") {
    global $wpdb;
    $query = "
SELECT
    unit.chapter_num AS chapternum,
    chp.ChapterName AS chapter,
    unit.unit_type AS unit_type,
    unit.unit_num AS unit_num,
    unit.unit_desig AS unit_desig,
    IFNULL(rpts.num_reports,0) AS num_reports,
    IFNULL(rpts.electiondate,'') AS electiondate,
    IFNULL(sch.UnitCount,0) AS num_reqs,
    IFNULL(MAX(rpts.NumberElected),0) AS num_reported,
    IFNULL(m.NumCandidatesSubmitted,0) AS num_submitted,
    IFNULL(crt.num_certified,0) AS num_certified
FROM
    wp_oa_units AS unit
    LEFT JOIN wp_oa_chapters AS chp ON BINARY unit.chapter_num = BINARY chp.chapter_num
    LEFT OUTER JOIN (
        SELECT
            COUNT(UnitNumber) AS num_reports,
            MAX(ElectionDate) AS electiondate,
            NumberElected,
            ChapterName,
            UnitType,
            UnitNumber,
            UnitDesignator
        FROM wp_oa_ue_units
        GROUP BY ChapterName, UnitType, UnitNumber, UnitDesignator
        ) rpts
        ON BINARY rpts.ChapterName = BINARY chp.ChapterName
        AND BINARY rpts.UnitType = BINARY unit.unit_type
        AND BINARY rpts.UnitNumber = BINARY unit.unit_num
        AND BINARY rpts.UnitDesignator = BINARY unit.unit_desig
    LEFT JOIN (
        SELECT
            COUNT(UnitNum) as UnitCount,
            ChapterName,
            UnitType,
            UnitNum as UnitNumber,
            UnitDesignator
        FROM wp_oa_ue_schedules
        GROUP BY ChapterName, UnitType, UnitNumber, UnitDesignator
       ) sch
        ON BINARY sch.ChapterName = BINARY chp.ChapterName
        AND BINARY sch.UnitType = BINARY unit.unit_type
        AND BINARY sch.UnitNumber = BINARY unit.unit_num
        AND BINARY sch.UnitDesignator = BINARY unit.unit_desig
    LEFT JOIN (
        SELECT
            COUNT(DISTINCT BSAMemberID) AS num_certified,
            ChapterName,
            UnitType,
            UnitNumber,
            UnitDesignator
        FROM wp_oa_ue_candidates_merged
        GROUP BY ChapterName, UnitType, UnitNumber, UnitDesignator
        ) crt
        ON BINARY crt.ChapterName = BINARY chp.ChapterName
        AND BINARY crt.UnitType = BINARY unit.unit_type
        AND BINARY crt.UnitNumber = BINARY unit.unit_num
        AND BINARY crt.UnitDesignator = BINARY unit.unit_desig
    LEFT JOIN (
        SELECT
            COUNT(DISTINCT BSAMemberID) AS NumCandidatesSubmitted,
            ChapterName,
            UnitType,
            UnitNumber,
            UnitDesignator
        FROM wp_oa_ue_candidates
        GROUP BY ChapterName, UnitType, UnitNumber, UnitDesignator
        ) m
        ON BINARY m.ChapterName = BINARY chp.ChapterName
        AND BINARY m.UnitType = BINARY unit.unit_type
        AND BINARY m.UnitNumber = BINARY unit.unit_num
        AND BINARY m.UnitDesignator = BINARY unit.unit_desig
";
    if (!($chapter == "all")) {
        $query = $query . "WHERE chp.ChapterName = %s";
    }
    $query = $query . "
GROUP BY unit.chapter_num, unit.unit_type, unit.unit_num, unit.unit_desig
ORDER BY unit.chapter_num, unit.unit_type, unit.unit_num, unit.unit_desig
";
    if (!($chapter == "all")) {
        $q = $wpdb->prepare($query, Array( $chapter ) );
    } else {
        $q = $query;
    }
    $results = $wpdb->get_results($q);

    $completed = [];
    $scheduled = [];
    $notscheduled = [];
    $pastdue = [];
    $requested = [];
    $num_units = [];
    $data = [];
    $elecscheds = nslodge_ue_getelections('all');
    foreach ($results AS $row) {
        $unit = ns_format_unit($row->unit_type, $row->unit_num, $row->unit_desig);
        if (!array_key_exists($row->chapter, $completed)) {
            $completed[$row->chapter] = 0;
        }
        if (!array_key_exists($row->chapter, $scheduled)) {
            $scheduled[$row->chapter] = 0;
        }
        if (!array_key_exists($row->chapter, $notscheduled)) {
            $notscheduled[$row->chapter] = 0;
        }
        if (!array_key_exists($row->chapter, $pastdue)) {
            $pastdue[$row->chapter] = 0;
        }
        if (!array_key_exists($row->chapter, $requested)) {
            $requested[$row->chapter] = 0;
        }
        if (!array_key_exists($row->chapter, $num_units)) {
            $num_units[$row->chapter] = 0;
        }
        if (!array_key_exists($row->chapter, $data)) {
            $data[$row->chapter] = [];
        }
        if (!array_key_exists($unit, $data[$row->chapter])) {
            $data[$row->chapter][$unit] = [];
        }
        $data[$row->chapter][$unit]['num_reports'] = $row->num_reports;
        $data[$row->chapter][$unit]['num_reqs'] = $row->num_reqs;
        $data[$row->chapter][$unit]['num_reported'] = $row->num_reported;
        $data[$row->chapter][$unit]['num_submitted'] = $row->num_submited;
        $data[$row->chapter][$unit]['num_certified'] = $row->num_certified;
        $data[$row->chapter][$unit]['scheduled'] = 0;
        $data[$row->chapter][$unit]['election_date'] = '';
        if ((array_key_exists($row->chapter, $elecscheds)) &&
             (array_key_exists($unit, $elecscheds[$row->chapter])) ) {
            $data[$row->chapter][$unit]['scheduled'] = 1;
            $data[$row->chapter][$unit]['election_date'] = $elecscheds[$row->chapter][$unit];
        }
        if ($data[$row->chapter][$unit]['num_reports'] > 0) {
            if (($row->num_reported != $row->num_submitted) && ($row->num_certified == 0)) {
                $pastdue[$row->chapter] = $pastdue[$row->chapter] + 1;
                $data[$row->chapter][$unit]['status'] = 'pastdue';
            } else {
                $completed[$row->chapter] = $completed[$row->chapter] + 1;
                $data[$row->chapter][$unit]['status'] = 'completed';
            }
            $data[$row->chapter][$unit]['election_date'] = $row->electiondate;
        } else if ($data[$row->chapter][$unit]['scheduled'] > 0) {
            if (strtotime("+3 days",strtotime($data[$row->chapter][$unit]['election_date'])) < time()) {
                $pastdue[$row->chapter] = $pastdue[$row->chapter] + 1;
                $data[$row->chapter][$unit]['status'] = 'pastdue';
            } else {
                $scheduled[$row->chapter] = $scheduled[$row->chapter] + 1;
                $data[$row->chapter][$unit]['status'] = 'scheduled';
            }
        } else if ($data[$row->chapter][$unit]['num_reqs'] > 0) {
            $requested[$row->chapter] = $requested[$row->chapter] + 1;
            $data[$row->chapter][$unit]['status'] = 'requested';
        } else {
            $notscheduled[$row->chapter] = $notscheduled[$row->chapter] + 1;
            $data[$row->chapter][$unit]['status'] = 'notscheduled';
        }
        $num_units[$row->chapter] = $num_units[$row->chapter] + 1;
    }
    $elecdata = [
        "completed" => $completed,
        "notscheduled" => $notscheduled,
        "scheduled" => $scheduled,
        "requested" => $requested,
        "pastdue"   => $pastdue,
        "num_units" => $num_units,
        "data"      => $data
    ];
    #ob_start();
    #var_dump($elecdata);
    #error_log(ob_get_clean());
    return $elecdata;
}

function ns_chapter_pie_chart( $chapter = "all" ) {
    $elecdata = ns_get_electdata( $chapter );
    $completed = $elecdata['completed'];
    $scheduled = $elecdata['scheduled'];
    $notscheduled = $elecdata['notscheduled'];
    $pastdue = $elecdata['pastdue'];
    $requested = $elecdata['requested'];
    $num_units = $elecdata['num_units'];
    $chapter = strtoupper($chapter);
?>
<canvas id="nsChapterPieAll" width="200" height="200"></canvas>
<div style="display:none;">
<!-- this hidden div is to ensure we have something on the page with these
     classes so we can pull the colors out of them to use in the charts, and
     then the colors can be changed in the CSS and they're consistent
     everywhere we use them -->
<div class="elec_completed"></div>
<div class="elec_pastdue"></div>
<div class="elec_scheduled"></div>
<div class="elec_requested"></div>
<div class="elec_notscheduled"></div>
</div>
<script type="text/javascript">
    var $j = jQuery.noConflict();
    var ctx = $j("#nsChapterPieAll");
    var ue_chartconfig = {
        type: 'pie',
        data: {
            datasets: [{
                data: [
                    <?php echo htmlspecialchars($completed[$chapter]) ?>,
                    <?php echo htmlspecialchars($pastdue[$chapter]) ?>,
                    <?php echo htmlspecialchars($scheduled[$chapter]) ?>,
                    <?php echo htmlspecialchars($requested[$chapter]) ?>,
                    <?php echo htmlspecialchars($notscheduled[$chapter]) ?>
                ],
                backgroundColor: [
                    $j(".elec_completed").css("background-color"),
                    $j(".elec_pastdue").css("background-color"),
                    $j(".elec_scheduled").css("background-color"),
                    $j(".elec_requested").css("background-color"),
                    $j(".elec_notscheduled").css("background-color")
                ]
            }],
            labels: [
                'Completed',
                'Missing Paperwork',
                'Scheduled',
                'Requested',
                'Not Scheduled'
            ]
        },
        options: {
            legend: {
                display: false
            }
        }
    }
    var ue_chart = new Chart(ctx, ue_chartconfig);
</script>
<?php
}

function ns_election_widget() {
    if (is_admin()) {
        ?><a href="/ue/dashboard">Go to Unit Elections Dashboard</a><br><?php
    }
    $elecdata = ns_get_electdata();
    $completed = $elecdata['completed'];
    $scheduled = $elecdata['scheduled'];
    $notscheduled = $elecdata['notscheduled'];
    $pastdue = $elecdata['pastdue'];
    $requested = $elecdata['requested'];
    $num_units = $elecdata['num_units'];
    ?>
<canvas id="nsElectionChart" width="200" height="100"></canvas>
<div style="display:none;">
<!-- this hidden div is to ensure we have something on the page with these
     classes so we can pull the colors out of them to use in the charts, and
     then the colors can be changed in the CSS and they're consistent
     everywhere we use them -->
<div class="elec_completed"></div>
<div class="elec_pastdue"></div>
<div class="elec_scheduled"></div>
<div class="elec_requested"></div>
<div class="elec_notscheduled"></div>
</div>
<script type="text/javascript">
var $j = jQuery.noConflict();
var ctx = $j("#nsElectionChart");
function fixalpha(color, newalpha) {
    var pat = /^rgba?\((\d+),\s*(\d+),\s*(\d+)/;
    var m = pat.exec(color);
    return "rgba(" + m[1] + ", " + m[2] + ", " + m[3] + ", " + newalpha + ")";
}
var ue_chartconfig = {
    type: 'horizontalBar',
    data: {
        labels: ["Chapter A", "Chapter B", "Chapter C", "Chapter D", "Chapter E", "Entire Lodge"],
        datasets: [
        {
            label: 'Complete',
            data: [<?php
            $completed_units = 0;
            $total_units = 0;
            foreach (array_keys($completed) AS $key) {
                if ($total_units > 0) { echo ","; };
                echo htmlspecialchars(($completed[$key] / $num_units[$key]) * 100);
                $completed_units = $completed_units + $completed[$key];
                $total_units = $total_units + $num_units[$key];
            }
            echo "," . htmlspecialchars(($completed_units / $total_units) * 100);
            ?>],
            backgroundColor: fixalpha($j(".elec_completed").css("background-color"), 0.2),
            borderColor: fixalpha($j(".elec_completed").css("background-color"), 1),
            borderWidth: 1
        },
        {
            label: "Past Due",
            data: [<?php
            $pastdue_units = 0;
            $total_units = 0;
            foreach (array_keys($pastdue) AS $key) {
                if ($total_units > 0) { echo ","; };
                echo htmlspecialchars(($pastdue[$key] / $num_units[$key]) * 100);
                $pastdue_units = $pastdue_units + $pastdue[$key];
                $total_units = $total_units + $num_units[$key];
            }
            echo "," . htmlspecialchars(($pastdue_units / $total_units) * 100);
            ?>],
            backgroundColor: fixalpha($j(".elec_pastdue").css("background-color"), 0.2),
            borderColor: fixalpha($j(".elec_pastdue").css("background-color"), 1),
            borderWidth: 1
        },
        {
            label: "Scheduled",
            data: [<?php
            $scheduled_units = 0;
            $total_units = 0;
            foreach (array_keys($scheduled) AS $key) {
                if ($total_units > 0) { echo ","; };
                echo htmlspecialchars(($scheduled[$key] / $num_units[$key]) * 100);
                $scheduled_units = $scheduled_units + $scheduled[$key];
                $total_units = $total_units + $num_units[$key];
            }
            echo "," . htmlspecialchars(($scheduled_units / $total_units) * 100);
            ?>],
            backgroundColor: fixalpha($j(".elec_scheduled").css("background-color"), 0.2),
            borderColor: fixalpha($j(".elec_scheduled").css("background-color"), 1),
            borderWidth: 1
        },
        {
            label: "Requested",
            data: [<?php
            $requested_units = 0;
            $total_units = 0;
            foreach (array_keys($requested) AS $key) {
                if ($total_units > 0) { echo ","; };
                echo htmlspecialchars(($requested[$key] / $num_units[$key]) * 100);
                $requested_units = $requested_units + $requested[$key];
                $total_units = $total_units + $num_units[$key];
            }
            echo "," . htmlspecialchars(($requested_units / $total_units) * 100);
            ?>],
            backgroundColor: fixalpha($j(".elec_requested").css("background-color"), 0.2),
            borderColor: fixalpha($j(".elec_requested").css("background-color"), 1),
            borderWidth: 1
        },
        {
            label: "Not Scheduled Yet",
            data: [<?php
            $notscheduled_units = 0;
            $total_units = 0;
            foreach (array_keys($notscheduled) AS $key) {
                if ($total_units > 0) { echo ","; };
                echo htmlspecialchars(($notscheduled[$key] / $num_units[$key]) * 100);
                $notscheduled_units = $notscheduled_units + $notscheduled[$key];
                $total_units = $total_units + $num_units[$key];
            }
            echo "," . htmlspecialchars(($notscheduled_units / $total_units) * 100);
            ?>],
            backgroundColor: fixalpha($j(".elec_notscheduled").css("background-color"), 0.2),
            borderColor: fixalpha($j(".elec_notscheduled").css("background-color"), 1),
            borderWidth: 1
        }
        ]
    },
    options: {
        tooltips: {
            callbacks: {
                label: function(tooltipitem, data) {
                    return data.datasets[tooltipitem.datasetIndex].label + ": " + (Math.round(tooltipitem.xLabel * 10) / 10) + "%";
                }
            }
        },
        legend: {
            labels: {
                boxWidth: 20
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
    if (( in_array( 'administrator',    (array) $user->roles ) ) ||
        ( in_array( 'process_nominations', (array) $user->roles ) )) {
        $election_admin = 1;
    }
    ob_start();
    $chapter = get_query_var('chapter');
    $chaptername = $wpdb->get_var($wpdb->prepare("SELECT SelectorName FROM wp_oa_chapters WHERE ChapterName = %s", Array($chapter)));
    $chapternum = $wpdb->get_var($wpdb->prepare("SELECT chapter_num FROM wp_oa_chapters WHERE ChapterName = %s", Array($chapter)));
    echo "<h2>Election information for Chapter " . strtoupper(htmlspecialchars($chapter)) . " - " . htmlspecialchars($chaptername) . "</h2>\n";
    if (!$election_committee) {
        echo '<p>Please <a href="' . wp_login_url( get_permalink() ) . '">log in</a> to an account with permission to view this data.</p>' . "\n";
    } else {
        echo '<p><a href="/ue/dashboard">&larr; Back to Lodge view</a></p>';
        $results = $wpdb->get_results($wpdb->prepare("
SELECT
    id,
    ch.ChapterName AS chapter,
    district_name,
    unit_type,
    unit_num,
    unit_desig,
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
WHERE
    un.chapter_num = %d
GROUP BY un.district_num, un.unit_type, un.unit_num, un.unit_desig
ORDER BY un.unit_type, un.unit_num, un.unit_desig, un.district_num
    ",
        Array($chapternum)));
        $elecdata = ns_get_electdata($chapter);
        $data = $elecdata['data'];
        ?>
        <div style="height: 200px; width: 200px; float: left;"><?php
        ns_chapter_pie_chart($chapter);
        ?></div>
        <div style="float: left; margin: 5px;"><table id="elec_table">
        <tr><th class="elec_notscheduled">Not Scheduled</th><td><?php echo htmlspecialchars($elecdata['notscheduled'][strtoupper($chapter)]); ?></td></tr>
        <tr><th class="elec_requested">Requested</th><td><?php echo htmlspecialchars($elecdata['requested'][strtoupper($chapter)]); ?></td></tr>
        <tr><th class="elec_scheduled">Scheduled</th><td><?php echo htmlspecialchars($elecdata['scheduled'][strtoupper($chapter)]); ?></td></tr>
        <tr><th class="elec_pastdue">Missing Paperwork</th><td><?php echo htmlspecialchars($elecdata['pastdue'][strtoupper($chapter)]); ?></td></tr>
        <tr><th class="elec_completed">Completed</th><td><?php echo htmlspecialchars($elecdata['completed'][strtoupper($chapter)]); ?></td></tr>
        <tr><th>Total Units</th><td><?php echo htmlspecialchars($elecdata['num_units'][strtoupper($chapter)]); ?></td></tr>
        </table>
        </div>
        <div style="clear: both;"></div><?php
        echo '<p style="font-size: large;">If you have corrections or additions for troop contact info, <a href="/unit-contact-info-update">submit it here</a>.</p>';
        echo '<div class="oa_chapter_info_wrapper">';
        echo '<table class="wp_table oa_chapter_info">';
        echo "\n<tr><th>Status</th><th>Reports Filed</th><th>Candidates Reported</th><th>District</th><th>Unit</th><th>City</th><th>Election Date</th><th>Unit Leader</th><th>Committee Chair</th><th>OA Rep</th></tr>\n";
        $statusname = [
            "notscheduled" => "Not Scheduled",
            "requested"    => "Requested",
            "completed"    => "Completed",
            "scheduled"    => "Scheduled",
            "pastdue"      => "Missing Paperwork"
        ];
        foreach ($results as $row) {
            $unit = ns_format_unit($row->unit_type, $row->unit_num, $row->unit_desig);
            echo '<tr class="elec_' . $data[$row->chapter][$unit]['status'] . '" style="color: black;">';
            echo "<td>" . htmlspecialchars($statusname[$data[$row->chapter][$unit]['status']]) . "</td>";
            echo "<td>" . htmlspecialchars($data[$row->chapter][$unit]['num_reports']) . "</td>\n";
            if ($data[$row->chapter][$unit]['num_certified']>0) {
                echo "<td>" . htmlspecialchars($data[$row->chapter][$unit]['num_certified']) . "</td>\n";
            } else {
                 echo "<td>" . htmlspecialchars($data[$row->chapter][$unit]['num_submitted']) . "</td>\n";
            }
            echo "<td>" . htmlspecialchars($row->district_name) . "</td>\n";
            echo "<td>" . htmlspecialchars($unit) . "</td>\n";
            echo "<td>" . htmlspecialchars($row->unit_city) . "</td>\n";
            $election_date = $data[$row->chapter][$unit]['election_date'];
            if ($election_date) {
                $election_date = date("Y-m-d",strtotime($election_date));
            }
            echo "<td>" . htmlspecialchars($election_date) . "</td>\n";
            echo "<td>" . htmlspecialchars($row->ul_full_name) . "<br>" . htmlspecialchars($row->ul_email) . "<br>" . htmlspecialchars($row->ul_phone_number) . "</td>\n";
            echo "<td>" . htmlspecialchars($row->cc_full_name) . "<br>" . htmlspecialchars($row->cc_email) . "<br>" . htmlspecialchars($row->cc_phone_number) . "</td>\n";
            echo "<td>" . htmlspecialchars($row->rep_full_name) . "<br>" . htmlspecialchars($row->rep_email) . "<br>" . htmlspecialchars($row->rep_phone_number) . "</td>\n";
            echo "</tr>\n";
        }
        echo "</table></div>\n";
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
    if (( in_array( 'administrator',    (array) $user->roles ) ) ||
        ( in_array( 'process_nominations', (array) $user->roles ) )) {
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
    <li><a href="<?php echo htmlspecialchars($homeurl) ?>/ue/request">Request an election for a unit</a>
    <li><a href="<?php echo htmlspecialchars($homeurl) ?>/ue/calendar">Lodge-wide Election Calendar</a>
    </ul>
    <?php if (!$election_committee) { ?>
    If you are a chapter chief or a<br>
    member of the Lodge election team<br>
    you can
    <a href="<?php echo wp_login_url( get_permalink() ); ?>" title="Login">Login</a>
    to see more information.<br>
    <?php } ?>
    <?php if (($election_admin) || ($election_committee)) { ?>
    <h5>Administrative UE Links</h5>
    <ul>
    <li><a href="<?php echo htmlspecialchars($homeurl) ?>/ue/evalresults">View Election Evaluations</a>
    <?php if ($election_admin) { ?>
    <li><a href="<?php echo htmlspecialchars($homeurl) ?>/ue/process-troops">Merge/Certify Election Results</a>
    <li><a href="<?php echo htmlspecialchars($homeurl) ?>/ue/process-adults">Process Adult Nominations</a>
    <li><a href="<?php echo htmlspecialchars($homeurl) ?>/ue/export-candidates">Export Youth Candidates to OALM</a>
    <li><a href="<?php echo htmlspecialchars($homeurl) ?>/ue/export-adults">Export Adult Candidates to OALM</a>
    <?php } ?>
    </ul>
    <?php } ?>
    </div>
    <div id="ue_master_chart" style="width: 500px; float: right;">
    <h5 style="margin-top: 0px;">2019-2020 Unit Election Status</h5>
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
