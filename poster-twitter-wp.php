<?php
/*
Plugin Name: Poster Twitter
Description: poster twitter, use hashtgas.
Version: 1.0.2
Author: Saul Morales Pacheco
Author URI: https://saulmoralespa.com
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Text Domain: poster-twitter
Domain Path: /languages
*/

if (!defined( 'ABSPATH' )) exit;

if(!defined('POSTER_TWITTER_WP_PTWP_VERSION')){
    define('POSTER_TWITTER_WP_PTWP_VERSION', '1.0.2');
}

add_action('plugins_loaded','poster_twitter_wp_ptwp_init',0);

function poster_twitter_wp_ptwp_init(){

    load_plugin_textdomain('poster-twitter-wp', FALSE, dirname(plugin_basename(__FILE__)) . '/languages');

    if (!requeriments_poster_twitter_wp_ptwp()){
        return;
    }

    poster_twitter_wp_ptwp()->ptwp_run();

    if(get_option('poster_twitter_wp_activation_redirect', false)){
        delete_option('poster_twitter_wp_activation_redirect');
        wp_redirect(admin_url('admin.php?page=config-postertwitterwp'));
    }

}

add_action('notices_poster_twitter_wp_ptwp', 'notices_alert_poster_twitter_wp_ptwp', 10, 1);
function notices_alert_poster_twitter_wp_ptwp($notice){
    ?>
    <div class="error notice">
        <p><?php echo $notice; ?></p>
    </div>
    <?php
}

function requeriments_poster_twitter_wp_ptwp()
{
    if ( version_compare( '5.6.0', PHP_VERSION, '>' ) ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            $php = __( 'Poster twitter WP: Requiere php versión 5.6.0 o superior.', 'poster-twitter-wp' );
            do_action('notices_poster_twitter_wp_ptwp', $php);
        }
        return false;
    }
    return true;
}

function poster_twitter_wp_ptwp(){
    static $plugin;
    if (!isset($plugin)){
        require_once('includes/class-plugin-poster-twitter-wp.php');
        $plugin = new Plugin_Poster_Twitter_WP_PTWP(__FILE__, POSTER_TWITTER_WP_PTWP_VERSION, 'Poster twitter wp');
    }

    return $plugin;
}

function activate_poster_twitter_wp_ptwp(){
    add_option('poster_twitter_wp_activation_redirect', true);
    poster_twitter_wp_ptwp_jal_install();
    poster_twitter_wp_ptwp_jal_install_data();
}

function deactivation_poster_twitter_wp_ptwp(){
    wp_clear_scheduled_hook( 'cron_time_poster_twitter_wp' );
}

function poster_twitter_wp_ptwp_jal_install(){

    global $wpdb;
    $table_name = $wpdb->prefix . 'postertwitterwp';

    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		consumer_key varchar(255) DEFAULT '',
	  	consumer_key_secret varchar(255) DEFAULT '', 
	  	is_data bit DEFAULT false,
		PRIMARY KEY  (id)
	) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    add_option( 'poster-twitter-wp-ptwp-version', POSTER_TWITTER_WP_PTWP_VERSION );
}

function poster_twitter_wp_ptwp_jal_install_data(){

    global $wpdb;
    $table_name = $wpdb->prefix . 'postertwitterwp';

    $result = $wpdb->get_results("SELECT * FROM $table_name");

    if (!$result){
        $wpdb->insert(
            $table_name,
            array(
                'consumer_key' => '',
                'consumer_key_secret' => '',
                'is_data' => false
            )
        );
    }
}

register_activation_hook(__FILE__,'activate_poster_twitter_wp_ptwp');
register_deactivation_hook( __FILE__, 'deactivation_poster_twitter_wp_ptwp' );