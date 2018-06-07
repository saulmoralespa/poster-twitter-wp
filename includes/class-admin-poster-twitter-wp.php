<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 4/06/18
 * Time: 09:48 PM
 */

class Admin_Poster_Twitter_WP_PTWP
{
    public function __construct()
    {
        $this->name = poster_twitter_wp_ptwp()->name;
        $this->plugin_url = poster_twitter_wp_ptwp()->plugin_url;
        $this->version = poster_twitter_wp_ptwp()->version;
        $this->assets = poster_twitter_wp_ptwp()->assets;
        add_action('admin_init', array($this, 'poster_twitter_wp_admin_menu_subscriber'));
        add_action('admin_menu', array($this, 'poster_twitter_wp_admin_menu_ptwp'));
    }

    public function poster_twitter_wp_admin_menu_subscriber()
    {
        $role = get_role( 'subscriber' );
        if (!$role->has_cap( 'cap_poster_twitter_wp' ))
            $role->add_cap( 'cap_poster_twitter_wp' );
    }

    public function poster_twitter_wp_admin_menu_ptwp()
    {
        $configuration =  poster_twitter_wp_ptwp()->adminConfiguration;
        $menuRoleConfig = poster_twitter_wp_ptwp()->roleConfig;

        add_menu_page($this->name, $this->name, 'manage_options', 'menus'. poster_twitter_wp_ptwp()->nameClean(), array($this,'menu'. poster_twitter_wp_ptwp()->nameClean()), $this->assets .'img/favicon.ico');
        $menuRoleConfig = add_menu_page($this->name, $this->name, 'cap_poster_twitter_wp', 'role-'. poster_twitter_wp_ptwp()->nameClean(), array($menuRoleConfig, 'content'), $this->assets .'img/favicon.ico');
        $config = add_submenu_page('menus' . poster_twitter_wp_ptwp()->nameClean(), __('Configuración', poster_twitter_wp_ptwp()->nameClean(true)), __('Configuración', poster_twitter_wp_ptwp()->nameClean(true)), 'manage_options', 'config-' . poster_twitter_wp_ptwp()->nameClean(), array($configuration,'content'));
        remove_submenu_page('menus'. poster_twitter_wp_ptwp()->nameClean(), 'menus' . poster_twitter_wp_ptwp()->nameClean());
        add_action( 'admin_footer', array( $this, 'enqueue_scripts_admin' ) );
        add_action('admin_print_styles-'.$menuRoleConfig, array($this,'poster_twitter_wp_admin_css'));
        add_action('admin_print_styles-'.$config, array($this,'poster_twitter_wp_admin_css'));

        add_action('admin_print_scripts-'.$config, array($this,'poster_twitter_wp_admin_js'));
        add_action('admin_print_scripts-'.$menuRoleConfig, array($this,'poster_twitter_wp_admin_js'));
    }

    public function enqueue_scripts_admin()
    {
        wp_enqueue_script( 'poster-twitter-wp-ptwp-admin', $this->plugin_url . 'assets/js/admin.js', array( 'jquery' ), $this->version, true );
        wp_localize_script( 'poster-twitter-wp-ptwp-admin', 'postertwitterwp', array(
            'consumerSecret' => __('Revise que esta ingresando correctamente Consumer Secret (API Secret)', poster_twitter_wp_ptwp()->nameClean(true)),
            'consumerKey' => __('Revise que esta ingresando correctamente Consumer Key (API Key)', poster_twitter_wp_ptwp()->nameClean(true)),
            'loadSave' => __('Guardando parametros de aplicación', poster_twitter_wp_ptwp()->nameClean(true)),
            'successSave' => __('Cambios guardados', poster_twitter_wp_ptwp()->nameClean(true)),
            'msgError' => __('Ha sugido un error, intente de nuevo', poster_twitter_wp_ptwp()->nameClean(true)),
            'urlAdmin' => admin_url().'admin.php?page=config-' . poster_twitter_wp_ptwp()->nameClean(),
            'urlUser' => admin_url().'admin.php?page=role-' . poster_twitter_wp_ptwp()->nameClean(),
            'redirectTwitter' => __('Redireccionando a twitter ...', poster_twitter_wp_ptwp()->nameClean(true)),
            'msgPostLength' => __('El mensaje debe ser más largo', poster_twitter_wp_ptwp()->nameClean(true)),
            'msgPostCoincidence' => __('El mensaje debe contener al menos 3 spintax. Esto significa que dentro de llaves contendra palabras con sinónimos, entre más coloques va ser de gran ayuda para evitar el spam ejemplo: Este es un {mensaje|anuncio}, espero hayas {aprendido|entendido} {chao|hasta luego}', poster_twitter_wp_ptwp()->nameClean(true)),
            'successSaveMsj' => __('El mensaje se ha guardado y pronto se empezará a publicar', poster_twitter_wp_ptwp()->nameClean(true)),
            'loadSaveMsj' => __('Guardando mensaje ...', poster_twitter_wp_ptwp()->nameClean(true)),
            'resetConfirm' => __('¿Seguro quiere resetear?', poster_twitter_wp_ptwp()->nameClean(true)),
            'resetLoad' => __('Reseteando ...', poster_twitter_wp_ptwp()->nameClean(true))
        ));
    }

    public function poster_twitter_wp_admin_css()
    {
        wp_enqueue_style('poster-twitter-wp-materialize', $this->plugin_url . 'assets/css/materialize.min.css', array(), $this->version, null);
        wp_enqueue_style('poster-twitter-wp-materialize-icons', 'https://fonts.googleapis.com/icon?family=Material+Icons', array(), $this->version, null);
    }

    public function poster_twitter_wp_admin_js()
    {
        wp_enqueue_script( 'poster-twitter-wp-materialize', $this->plugin_url . 'assets/js/materialize.min.js', array( 'jquery' ), $this->version, true );
        wp_enqueue_script( 'poster-twitter-wp-jquery-validation', $this->plugin_url . 'assets/js/jquery.validate.min.js', array( 'jquery' ), $this->version, true );
    }
}