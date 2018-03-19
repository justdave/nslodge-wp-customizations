<?php
/*
 * Plugin Name: NSLodge Custom Stuff
 * Plugin URI: https://nslodge.org/
 * Description: Wordpress plugin to house custom stuff for this website
 * Version: 1.0
 * Author: Dave Miller
 * Author URI: http://twitter.com/justdavemiller
 * Author Email: github@justdave.net
 * */

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

// All of the meat is in the includes directory, to keep it organized.
// Just pull it all in from here.
require_once("includes/election-calendar-utils.php");
require_once("includes/issues-widget.php");
require_once("includes/merge-candidates.php");
require_once("includes/approve-adults.php");
require_once("includes/election-dashboard.php");
require_once("includes/ajax-calls.php");

function ns_global_overrides() {
    wp_enqueue_style('nslodge-global-css', plugins_url('css/wp-overrides.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'ns_global_overrides');

function ns_devsite_admin_theme_style() {
    wp_enqueue_style('nslodge-admin-theme', plugins_url('css/wp-admin.css', __FILE__));
}

if (strpos(home_url(),"dev") !== false) {
    # if the code reaches here we're on the dev site. Load the admin css on the
    # admin pages so we can tell we're on the dev site.
    add_action('admin_enqueue_scripts', 'ns_devsite_admin_theme_style');
}
