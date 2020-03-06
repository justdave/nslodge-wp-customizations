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

use SimpleCalendar\Plugin;

// Look up events for chapter
function nslodge_ue_getelections($chapter) {
    if (is_admin()) {
        new SimpleCalendar\Assets();
    }
    $min_date = strtotime("2019-11-01");
    $calendar_id = [
        "all" => 1732,
        "a" => 1722,
        "b" => 1719,
        "c" => 1713,
        "d" => 1704,
        "e" => 1725,
    ];

    $calendar = simcal_get_calendar($calendar_id[$chapter]);
    $events = $calendar->get_events()->from($min_date);
    $elecscheds = [];
    while ($this_day = $events->get_first()) {
        $num = 0;
        $num_events = count($this_day);
        while ($num < $num_events) {
            $this_event = $this_day[$num];
            $title = $this_event->title;
            $start = $this_event->start;
            preg_match("/[Cc]hapter ([AaBbCcDdEe])/",$title,$matches);
            $this_chapter = $matches[1];
            preg_match("/([Tt]roop|[Cc]rew|[Ss]hip) (\d+)( ?[BbGg])?/",$title,$matches);
            $unit_type = ucfirst($matches[1]);
            $unit_num = $matches[2];
            $unit_desig = strtoupper(trim($matches[3]));
            $unit = "$unit_type $unit_num";
            if ($unit_desig) {
                $unit .= " " . $unit_desig;
            }
            if (!array_key_exists($this_chapter, $elecscheds)) {
                $elecscheds[$this_chapter] = [];
            }
            $elecscheds[$this_chapter][$unit] = date("m/d/Y",$start);
            $num += 1;
        }
    }
    return $elecscheds;
}

function nslodge_ue_getschedreqs($chapter) {
    global $wpdb;
    if ($chapter == 'all') {
    $results = $wpdb->get_results($wpdb->prepare("SELECT Chapter, UnitType, UnitNum, UnitDesignator, ReqDate, Priority FROM (
SELECT ChapterName AS Chapter, UnitType, UnitNum, UnitDesignator, `e-date-1` AS ReqDate, '1' AS Priority FROM wp_oa_ue_schedules
UNION
SELECT ChapterName AS Chapter, UnitType, UnitNum, UnitDesignator, `e-date-2` AS ReqDate, '2' AS Priority FROM wp_oa_ue_schedules
UNION
SELECT ChapterName AS Chapter, UnitType, UnitNum, UnitDesignator, `e-date-3` AS ReqDate, '3' AS Priority FROM wp_oa_ue_schedules
) AS sched
ORDER BY ReqDate, Priority", array($chapter)));
    } else {
    $results = $wpdb->get_results($wpdb->prepare("SELECT Chapter, UnitType, UnitNum, UnitDesignator, ReqDate, Priority FROM (
SELECT ChapterName AS Chapter, UnitType, UnitNum, UnitDesignator, `e-date-1` AS ReqDate, '1' AS Priority FROM wp_oa_ue_schedules
UNION
SELECT ChapterName AS Chapter, UnitType, UnitNum, UnitDesignator, `e-date-2` AS ReqDate, '2' AS Priority FROM wp_oa_ue_schedules
UNION
SELECT ChapterName AS Chapter, UnitType, UnitNum, UnitDesignator, `e-date-3` AS ReqDate, '3' AS Priority FROM wp_oa_ue_schedules
) AS sched
WHERE ChapterName=%s
ORDER BY ReqDate, Priority", array("Chapter $chapter")));
    }
    $schedreqs = [];

    foreach ($results as $row) {
        $unit = $row->UnitType . " " . $row->UnitNum;
        if ($row->UnitDesignator) {
            $unit .= " " . substr($row->UnitDesignator, 0, 1);
        }
        $schedreqs[$row->Chapter][$unit][$row->Priority] = $row->ReqDate;
    }
    return $schedreqs;
}

// Add Shortcode
function nslodge_ue_schedreqs( $attrs ) {

    extract( shortcode_atts(
        array(
            'chapter' => 'all',
        ), $attrs )
    );

    global $wpdb;
    if ($chapter == 'all') {
    $results = $wpdb->get_results("
SELECT Chapter, sched.UnitType, sched.UnitNum, sched.UnitDesignator, ReqDate, Priority, COUNT(rpts.UnitNumber) as Reports FROM (
SELECT ChapterName AS Chapter, UnitType, UnitNum, UnitDesignator, `e-date-1` AS ReqDate, '1' AS Priority FROM wp_oa_ue_schedules
UNION
SELECT ChapterName AS Chapter, UnitType, UnitNum, UnitDesignator, `e-date-2` AS ReqDate, '2' AS Priority FROM wp_oa_ue_schedules
UNION
SELECT ChapterName AS Chapter, UnitType, UnitNum, UnitDesignator, `e-date-3` AS ReqDate, '3' AS Priority FROM wp_oa_ue_schedules
) AS sched
LEFT JOIN wp_oa_chapters AS chp ON BINARY sched.Chapter = BINARY chp.SelectorName
LEFT JOIN wp_oa_ue_units AS rpts ON BINARY chp.ChapterName = BINARY rpts.ChapterName AND BINARY sched.UnitType = BINARY rpts.UnitType AND BINARY sched.UnitNum = BINARY rpts.UnitNumber AND BINARY sched.UnitDesignator = BINARY rpts.UnitDesignator
GROUP BY Chapter, UnitType, UnitNum, UnitDesignator, Priority
ORDER BY ReqDate, Priority");
    } else {
    $results = $wpdb->get_results($wpdb->prepare("
SELECT Chapter, sched.UnitType, sched.UnitNum, sched.UnitDesignator, ReqDate, Priority, COUNT(rpts.UnitNumber) as Reports FROM (
SELECT ChapterName AS Chapter, UnitType, UnitNum, UnitDesignator, `e-date-1` AS ReqDate, '1' AS Priority FROM wp_oa_ue_schedules
UNION
SELECT ChapterName AS Chapter, UnitType, UnitNum, UnitDesignator, `e-date-2` AS ReqDate, '2' AS Priority FROM wp_oa_ue_schedules
UNION
SELECT ChapterName AS Chapter, UnitType, UnitNum, UnitDesignator, `e-date-3` AS ReqDate, '3' AS Priority FROM wp_oa_ue_schedules
) AS sched
LEFT JOIN wp_oa_chapters AS chp ON BINARY sched.Chapter = BINARY chp.SelectorName
LEFT JOIN wp_oa_ue_units AS rpts ON BINARY chp.ChapterName = BINARY rpts.ChapterName AND BINARY sched.UnitType = BINARY rpts.UnitType AND BINARY sched.UnitNum = BINARY rpts.UnitNumber AND BINARY sched.UnitDesignator = BINARY rpts.UnitDesignator
WHERE Chapter=%d
GROUP BY Chapter, UnitType, UnitNum, UnitDesignator, Priority
ORDER BY ReqDate, Priority", array($chapter)));
    }

    $elecscheds = nslodge_ue_getelections($chapter);

    #$output = "<pre>" . var_dump_ret($troops) . "</pre>";
    $output = "";
    $output .= "<table border=1><tr>";
    if ($chapter == 'all') { $output .= "<th>Chapter</th>"; }
    $output .= "<th>Unit</th><th>Requested Date</th><th>Unit's Priority</th></tr>\n";

    foreach ($results as $row) {
        if ( $row->Reports == 0 ) {
            $unit = $row->UnitType . " " . $row->UnitNum;
            if ($row->UnitDesignator) {
                $unit .= . " " . substr($row->UnitDesignator, 0, 1);
            }
            if ( !(array_key_exists($row->Chapter, $elecscheds)) ||
               ( (array_key_exists($row->Chapter, $elecscheds) ) &&
                 (! array_key_exists($row->UnitType, $elecscheds[$row->Chapter])) &&
                 (! array_key_exists($row->UnitNum, $elecscheds[$row->Chapter][$unit]))
               )) {
                $color = "";
                if (strtotime($row->ReqDate) < time()) { $color = ' style="color: red;"'; }
                $output .= "<tr>";
                if ($chapter == 'all') { $output .= "<td>" . htmlspecialchars($row->Chapter) . "</td>"; }
                $output .= "<td>" . htmlspecialchars($row->UnitType) . " " . htmlspecialchars($row->UnitNum) . "</td><td$color>" . htmlspecialchars($row->ReqDate) . "</td><td>" . htmlspecialchars($row->Priority) . "</td></tr>\n";
            }
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
