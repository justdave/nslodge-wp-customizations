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
    $pagelist = array(
    '/ue/report'            => [ 'pagejs' => 'js/report-unit.js', 'unitwidget' => TRUE ],
    '/ue/report-scoutentry' => [ 'pagejs' => 'js/report-scout.js' ],
    '/ue/report-complete'   => [ 'pagejs' => 'js/report-complete.js' ],
    '/ue/adultnomination'   => [ 'pagejs' => 'js/adult-nomination.js', 'unitwidget' => TRUE ],
    '/ue/request'           => [ 'pagejs' => 'js/unit-picker.js', 'unitwidget' => TRUE ],
    '/unit-contact-info-update' => [ 'pagejs' => 'js/unit-picker.js', 'unitwidget' => TRUE ]
    '/transfer'             => [ 'pagejs' => 'js/unit-picker.js', 'unitwidget' => TRUE ]
    );
    $pageuri = $_SERVER['REQUEST_URI'];
    if (array_key_exists($pageuri, $pagelist)) {
        $pagekey = preg_replace('@^/@','',$pageuri);
        $pagekey = preg_replace('@/@','-',$pagekey);
        ns_write_log("pagekey = $pagekey");
        $usesajax = FALSE;
        if (array_key_exists('unitwidget', $pagelist[$pageuri])) {
            wp_enqueue_script('jquery-ui-autocomplete', '', array('jquery-ui-widget', 'jquery-ui-position'), '1.8.6');
            wp_enqueue_style( 'ns-autocomplete-css', plugins_url('css/autocomplete.css', dirname(__FILE__)));
            $usesajax = TRUE;
        }
        wp_enqueue_script( $pagekey, plugins_url($pagelist[$pageuri]['pagejs'], dirname(__FILE__)), array( 'jquery', 'jquery-form', 'json2' ), false, true );
        if ($usesajax) {
            # this puts the ajax URL into nslodge_ajax.ajaxurl for the page javascript
            wp_localize_script( $pagekey, 'nslodge_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
        }
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
        SELECT unit_type, unit_num, ch.ChapterName AS chapter_name, SelectorName, district_name, unit_city, charter_org, ul_full_name, cc_full_name
        FROM wp_oa_units AS un
        LEFT JOIN wp_oa_chapters AS ch ON un.chapter_num = ch.chapter_num
        LEFT JOIN wp_oa_districts AS di ON un.district_num = di.district_num
        WHERE un.unit_num LIKE %s
        ORDER BY un.unit_num
    ", Array("%" . $term . "%")));
    wp_send_json($results);

    die();// wordpress may print out a spurious zero without this - can be particularly bad if using json
}
