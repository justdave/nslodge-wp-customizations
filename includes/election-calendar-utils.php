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

// Add Shortcode
function nslodge_ue_schedreqs( $attrs ) {

    $min_date = strtotime("2015-11-01");
    $calendar_id = [
        "all" => 1732,
        1 => 1722,
        2 => 1719,
        3 => 1713,
        4 => 1704,
        5 => 1725,
        6 => 1727,
        7 => 1730
    ];
    extract( shortcode_atts(
        array(
            'chapter' => 'all',
        ), $attrs )
    );

    global $wpdb;
    $results = $wpdb->get_results($wpdb->prepare("SELECT Chapter, Troop, ReqDate, Priority FROM (
SELECT ChapterNumber AS Chapter, tnum AS Troop, `e-date-1` AS ReqDate, '1' AS Priority FROM wp_oa_ue_schedules
UNION
SELECT ChapterNumber AS Chapter, tnum AS Troop, `e-date-2` AS ReqDate, '2' AS Priority FROM wp_oa_ue_schedules
UNION
SELECT ChapterNumber AS Chapter, tnum AS Troop, `e-date-3` AS ReqDate, '3' AS Priority FROM wp_oa_ue_schedules
) AS sched
WHERE Chapter=%d
ORDER BY ReqDate, Priority", array($chapter)));

    $calendar = simcal_get_calendar($calendar_id[$chapter]);
    $events = $calendar->get_events()->from($min_date);
    $troops = [];
    while ($this_day = $events->get_first()) {
        $num = 0;
        $num_events = count($this_day);
        while ($num < $num_events) {
            $this_event = $this_day[$num];
            $title = $this_event->title;
            $start = $this_event->start;
            preg_match("/[Tt]roop (\d+)/",$title,$matches);
            $troop = $matches[1];
            $troops[$troop] = date("m/d/Y",$start);
            $num += 1;
        }
    }
    #$output = "<pre>" . var_dump_ret($troops) . "</pre>";
    $output = "";
    $output .= "<table border=1><tr><th>Troop</th><th>Requested Date</th><th>Troop's Priority</th></tr>\n";
    foreach ($results as $row) {
        if (! in_array($row->Troop, array_keys($troops))) {
            $color = "";
            if (strtotime($row->ReqDate) < time()) { $color = ' style="color: red;"'; }
            $output .= "<tr><td>" . htmlspecialchars($row->Troop) . "</td><td$color>" . htmlspecialchars($row->ReqDate) . "</td><td>" . htmlspecialchars($row->Priority) . "</td></tr>\n";
        }
    }
    $output .= "</table>\n";
    return $output;
}
add_shortcode( 'ue_schedule_requests', 'nslodge_ue_schedreqs' );

function var_dump_ret($mixed = null) {
  ob_start();
  var_dump($mixed);
  $content = ob_get_contents();
  ob_end_clean();
  return $content;
}
