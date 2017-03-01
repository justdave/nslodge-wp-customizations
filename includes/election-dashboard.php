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
    $results = $wpdb->get_results("select b.chapter_num as chapter, greatest(0, count(distinct a.UnitNumber)) as number_submitted, count(distinct c.unit_num) as num_troops from wp_oa_chapters b left join wp_oa_ue_troops a on binary concat(0, b.chapter_num, ' - ', b.ChapterName) = binary a.ChapterName left join wp_oa_troops c on b.chapter_num = c.chapter_num group by c.chapter_num order by c.chapter_num");
    if (is_admin()) {
        ?><a href="/ue/dashboard">Go to Unit Elections Dashboard</a><br><?php
    }
    ?>
<canvas id="nsElectionChart" width="200" height="100"></canvas>
<script type="text/javascript">
var ctx = document.getElementById("nsElectionChart");
var ue_chartconfig = {
    type: 'horizontalBar',
    data: {
        labels: ["Chapter 1", "Chapter 2", "Chapter 3", "Chapter 4", "Chapter 5", "Chapter 6", "Chapter 7", "Entire Lodge"],
        datasets: [{
            label: '% Complete',
            data: [<?php
            $completed_troops = 0;
            $total_troops = 0;
            foreach ($results as $row) {
                if ($total_troops > 0) { echo ","; };
                $this_completed = $row->number_submitted;
                $this_total = $row->num_troops;
                echo htmlspecialchars(ceil(($row->number_submitted / $row->num_troops) * 100));
                $completed_troops = $completed_troops + $row->number_submitted;
                $total_troops = $total_troops + $row->num_troops;
            }
            echo "," . htmlspecialchars(ceil(($completed_troops / $total_troops) * 100));
            ?>],
            backgroundColor: [
                'rgba(75, 192, 192, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(75, 128, 128, 0.2)'
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(75, 128, 128, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            xAxes: [{
                ticks: {
                    beginAtZero:true,
                    suggestedMax:100
                }
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
    alert("Foo!");
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
    ns_election_widget();
    ?>
<script type="text/javascript"><!--
jQuery("#nsElectionChart").on('click', function (e) {
var bars = ue_chart.getElementAtEvent(e);
var ttips = ue_chart.tooltip._lastActive[0]._index
alert(e.layerY);
if (bars.length == 0) return;
    var element = null;
    element = bars[0];
    if (element === null) return;
    var labelElement, dataElement;
    labelElement = ue_chartconfig.data.datasets[element._datasetIndex].label;
});
--></script>
    <?php
}
