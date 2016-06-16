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

add_action('wp_dashboard_setup', 'ns_add_issues_widget');
 
function ns_add_issues_widget() {
global $wp_meta_boxes;

add_meta_box('simpletracker_widget', 'Open Issues', 'ns_issues_widget', 'dashboard', 'side', 'high');
}

function ns_issues_widget() {
global $post;
$post = get_page_by_path('issues');
$tracker = new SimpleTracker;
echo $tracker->it_main_list(1,0);
}

