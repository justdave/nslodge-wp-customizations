<?php
/*
 * Copyright (C) 2018 David D. Miller
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

add_action( 'wp_enqueue_scripts', 'ns_ue_ballot_enqueue_scripts' );
function ns_ue_ballot_enqueue_scripts() {
    if ($_SERVER['REQUEST_URI'] == '/ue/ballot') {
        wp_enqueue_script( 'ns-ballot', plugins_url('js/ballot.js', dirname(__FILE__)), array( 'jquery', 'jquery-form', 'json2' ), false, true );
    }
}

add_shortcode( 'ue_ballotgen_form', 'nslodge_ue_ballotgen' );
function nslodge_ue_ballotgen() {
  ob_start();
  ?><form id="namesForm" method="POST" action="<?php echo plugins_url("ballot.php", dirname(__FILE__)) ?>">
    <div id="nameRows">
        <input type="button" onclick="javascript:;" id="addRow" value="Add Scout" style="margin-bottom: 10px;"><br />
        <input type="text" name="names[0][name]">
    </div>
<p>    <input type="submit" id="submit" value="Download Ballot" style="margin-top: 10px;" /><br />
</form><?php
  return ob_get_clean();
}
