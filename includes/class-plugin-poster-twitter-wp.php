<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 4/06/18
 * Time: 09:30 PM
 */

if ( ! defined( 'ABSPATH' ) )
    exit;

class Plugin_Poster_Twitter_WP_PTWP
{
    /**
     * @var string
     */
    public $file;
    /**
     * @var string
     */
    public $version;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $plugin_path;
    /**
     * @var string
     */
    public $plugin_url;
    /**
     * @var string
     */
    public $includes_path;
    /**
     * @var string
     */
    public $vendor;
    /**
     * @var string
     */
    public $assets;
    /**
     * @var bool
     */
    private $_bootstrapped = false;
    /**
     * @var string
     */
    public $uploads;
    /**
     * @var string
     */
    public $file_debug;

    public function __construct($file, $version, $name)
    {
        $this->file = $file;
        $this->version = $version;
        $this->name = $name;
        $this->plugin_path   = trailingslashit( plugin_dir_path( $this->file ) );
        $this->plugin_url    = trailingslashit( plugin_dir_url( $this->file ) );
        $this->assets = $this->plugin_url . trailingslashit( 'assets');
        $this->includes_path = $this->plugin_path . trailingslashit( 'includes' );
        $this->vendor = $this->plugin_path . trailingslashit( 'vendor' );
        $this->uploads = wp_upload_dir();
        $this->uploads = trailingslashit( $this->uploads['basedir']);
        $this->file_debug = $this->uploads . $this->nameClean(true) . '.txt';

        if (current_user_can('cap_poster_twitter_wp') && is_user_logged_in() &&  in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' )))) {
            add_filter( 'woocommerce_prevent_admin_access', '__return_false' );
            add_filter( 'woocommerce_disable_admin_bar', '__return_false' );
        }
        add_filter( 'plugin_action_links_' . plugin_basename( $this->file ), array( $this, 'plugin_action_links' ) );
        add_filter( 'login_redirect', array($this,'poster_twitter_wp_dashboard_redirect' ),10,3);
        add_action('wp_ajax_poster_twitter_wp',array($this,'poster_twitter_wp_ajax'));
        add_action('wp_ajax_nopriv_poster_twitter_wp',array($this,'poster_twitter_wp_ajax'));
    }

    public function ptwp_run()
    {
        try{
            if ($this->_bootstrapped){
                throw new Exception( __( 'poster twitter wp can only be called once',  $this->nameClean(true)));
            }
            $this->_run();
            $this->_bootstrapped = true;
        }catch (Exception $e){
            if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
                do_action('notices_poster_twitter_wp_ptwp', 'Social Poster WP: ' . $e->getMessage());
            }
        }
    }

    protected function _run()
    {
        $this->_load_handlers();
    }

    protected function _load_handlers()
    {
        require_once ($this->includes_path . 'class-admin-poster-twitter-wp.php');
        require_once ($this->includes_path . 'class-admin-configuration-poster-twitter-wp.php');
        require_once ($this->includes_path . 'class-menu-role-config-poster-twitter-wp.php');
        require_once ($this->includes_path . 'class-connection-poster-twitter-wp.php');
        require_once ($this->includes_path . 'class-scheduled-poster-twitter-wp.php');
        require_once ($this->includes_path . 'class-spintax-poster-twitter-wp.php');
        require_once ($this->includes_path . 'class-debug-poster-twitter-wp.php');
        require_once ($this->vendor . 'autoload.php');

        $this->admin = new Admin_Poster_Twitter_WP_PTWP();
        $this->adminConfiguration = new Admin_Configuration_Poster_Twitter_WP_PTWP();
        $this->roleConfig = new Menu_Role_Config_Poster_Twitter_WP_PTWP();
        $this->conn = new Connection_Poster_Twitter_WP_PTWP();
        $this->scheduled = new Scheduled_Poster_Twitter_WP_PTWP();
        $this->debug = new Debug_Poster_Twitter_WP_PTWP();
    }

    public function plugin_action_links($links)
    {
        $plugin_links = array();
        $plugin_links[] = '<a href="'.admin_url('admin.php?page=config-postertwitterwp').'">' . esc_html__( 'Ajustes', $this->nameClean(true) ) . '</a>';
        return array_merge( $plugin_links, $links );
    }

    public function poster_twitter_wp_dashboard_redirect($url, $request, $user)
    {

        if( !is_wp_error($user) && is_array($user->roles) ){
            if( in_array('subscriber', $user->roles) && $user->has_cap( 'cap_poster_twitter_wp' )){
                $url = admin_url('admin.php?page='.'role-'. $this->nameClean());
            }
        }
        return $url;
    }

    public function poster_twitter_wp_ajax()
    {
        if(isset($_POST['poster-twitter-wp-consumer-key-ptwp'])){

            $id = $_POST['poster-twitter-wp-id-ptwp'];

            global $wpdb;
            $table_name = $wpdb->prefix . 'postertwitterwp';

            $wpdb->update(
                $table_name,
                array(
                    'consumer_key' => trim($_POST['poster-twitter-wp-consumer-key-ptwp']),
                    'consumer_key_secret' => trim($_POST['poster-twitter-wp-consumer-secret-ptwp']),
                    'is_data' =>  true

                ),
                array( 'id' => 1 )
            );

            echo poster_twitter_wp_ptwp()->conn->requestToken($id);
        }

        if (isset($_POST['login'])){
            $id = $_POST['iduser'];
            echo poster_twitter_wp_ptwp()->conn->requestToken($id, true);
        }

        if (isset($_POST['postertwitterwpmessage'])){
            $id = $_POST['iduser'];
            sleep(30);
            update_post_meta($id, 'poster-twitter-wp-ptwp-tweet', $_POST['postertwitterwpmessage']);
        }

        if(isset($_POST['reset'])){
            $id = $_POST['iduser'];
            sleep(30);
            update_post_meta($id, 'poster-twitter-wp-ptwp-token-user', '');
            update_post_meta($id, 'poster-twitter-wp-ptwp-tweet', '');
        }

        die();
    }

    public function get_data_table($is_data = false)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'postertwitterwp';

        $data = '';
        if ($is_data){
            $data = $wpdb->get_row( "SELECT is_data FROM $table_name WHERE id = 1" );
            $data = $data->is_data;
        }else{
            $data = $wpdb->get_results( "SELECT * FROM $table_name WHERE id = 1" );
            $data = $data[0];
        }

        return $data;

    }

    public function nameClean($domain = false)
    {
        $name = ($domain) ? str_replace(' ', '-', $this->name)  : str_replace(' ', '', $this->name);
        return strtolower($name);
    }
}