<?php
if (!function_exists('ns_write_log')) {
    function ns_write_log ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }
}
add_action( 'wp_enqueue_scripts', 'ns_ue_ajax_enqueue_scripts' );
ns_write_log(__LINE__);
function ns_ue_ajax_enqueue_scripts() {
    global $wp;
    if ($_SERVER['REQUEST_URI'] == '/ue/report') {
        wp_enqueue_script('jquery-ui-autocomplete', '', array('jquery-ui-widget', 'jquery-ui-position'), '1.8.6');
        wp_enqueue_script( 'ns-report-troop', plugins_url('js/report-troop.js', dirname(__FILE__)), array( 'jquery', 'jquery-form', 'json2' ), false, true );
        wp_enqueue_style( 'ns-autocomplete-css', plugins_url('css/autocomplete.css', dirname(__FILE__)));
        # this puts the ajax URL into nslodge_ajax.ajaxurl for the page javascript
        wp_localize_script( 'ns-report-troop', 'nslodge_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    }
    if ($_SERVER['REQUEST_URI'] == '/ue/adultnomination') {
        wp_enqueue_script('jquery-ui-autocomplete', '', array('jquery-ui-widget', 'jquery-ui-position'), '1.8.6');
        wp_enqueue_script( 'ns-adult-nomination', plugins_url('js/adult-nomination.js', dirname(__FILE__)), array( 'jquery', 'jquery-form', 'json2' ), false, true );
        wp_enqueue_style( 'ns-autocomplete-css', plugins_url('css/autocomplete.css', dirname(__FILE__)));
        # this puts the ajax URL into nslodge_ajax.ajaxurl for the page javascript
        wp_localize_script( 'ns-adult-nomination', 'nslodge_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    }
    if ($_SERVER['REQUEST_URI'] == '/ue/request') {
        wp_enqueue_script('jquery-ui-autocomplete', '', array('jquery-ui-widget', 'jquery-ui-position'), '1.8.6');
        wp_enqueue_script( 'ns-schedule-request', plugins_url('js/schedule-request.js', dirname(__FILE__)), array( 'jquery', 'jquery-form', 'json2' ), false, true );
        wp_enqueue_style( 'ns-autocomplete-css', plugins_url('css/autocomplete.css', dirname(__FILE__)));
        # this puts the ajax URL into nslodge_ajax.ajaxurl for the page javascript
        wp_localize_script( 'ns-schedule-request', 'nslodge_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    }
}
# handlers
add_action( 'wp_ajax_ns_get_troops_autocomplete', 'ns_get_troops_autocomplete' );
add_action( 'wp_ajax_nopriv_ns_get_troops_autocomplete', 'ns_get_troops_autocomplete' ); // need this to serve non logged in users
// THE FUNCTION
function ns_get_troops_autocomplete() {
    global $wpdb;
    $term = $_GET['term'];
    $term = intval($term);
    $results = $wpdb->get_results($wpdb->prepare("
        SELECT unit_num, ch.ChapterName AS chapter_name, SelectorName, district_name
        FROM wp_oa_troops AS tr
        LEFT JOIN wp_oa_chapters AS ch ON tr.chapter_num = ch.chapter_num
        LEFT JOIN wp_oa_districts AS di ON tr.district_num = di.district_num
        WHERE tr.unit_num LIKE %s
        ORDER BY tr.unit_num
    ", Array($term . "%")));
    wp_send_json($results);

    die();// wordpress may print out a spurious zero without this - can be particularly bad if using json
}
