<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://seynd.com
 * @since      1.0.0
 *
 * @package    Seynd_conn
 * @subpackage Seynd_conn/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Seynd_conn
 * @subpackage Seynd_conn/includes
 * @author     Seynd <Seynd>
 */
class Seynd_conn {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Seynd_conn_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;
    
    static $basename = null;
    
    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {	
	if (defined('SEYND_CONN_VERSION')) {
	    $this->version = SEYND_CONN_VERSION;
	} else {
	    $this->version = '1.0.0';
	}
	$this->plugin_name = 'seynd_conn';

	$this->load_dependencies();
	$this->set_locale();
	$this->define_admin_hooks();
	$this->define_public_hooks();
	$this->basename = plugin_basename(__FILE__);
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Seynd_conn_Loader. Orchestrates the hooks of the plugin.
     * - Seynd_conn_i18n. Defines internationalization functionality.
     * - Seynd_conn_Admin. Defines all hooks for the admin area.
     * - Seynd_conn_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

	/**
	 * The class responsible for orchestrating the actions and filters of the
	 * core plugin.
	 */
	require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-seynd_conn-loader.php';

	/**
	 * The class responsible for defining internationalization functionality
	 * of the plugin.
	 */
	require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-seynd_conn-i18n.php';

	/**
	 * The class responsible for defining all actions that occur in the admin area.
	 */
	require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-seynd_conn-admin.php';

	/**
	 * The class responsible for defining all actions that occur in the public-facing
	 * side of the site.
	 */
	require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-seynd_conn-public.php';

	$this->loader = new Seynd_conn_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Seynd_conn_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

	$plugin_i18n = new Seynd_conn_i18n();

	$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

	$plugin_admin = new Seynd_conn_Admin($this->get_plugin_name(), $this->get_version());
	
	// Filter Hook for plugin setting menu	
	$basename = SEYND_PLUGIN_BASENAME; 	
	$prefix = is_network_admin() ? 'network_admin_' : '';	
	$this->loader->add_filter("{$prefix}plugin_action_links_$basename", $plugin_admin, 'seynd_plugin_action_links', 10, 4);
	
	// Action Hooks
	if( sanitize_text_field( $_GET['page'] ) == "seynd-connection" ){	
	    $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'seynd_enqueue_styles');
	    $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'seynd_enqueue_scripts');
	}	
	
	$this->loader->add_action('wp_ajax_getpages', $plugin_admin, 'seynd_getpages_callback');
	$this->loader->add_action('wp_ajax_seynd_disconnect', $plugin_admin, 'seynd_disconnect');
	$this->loader->add_action('wp_ajax_add_code', $plugin_admin, 'seynd_add_code');
	$this->loader->add_action('wp_ajax_edit_code', $plugin_admin, 'seynd_edit_code');
	$this->loader->add_action('wp_ajax_update_site', $plugin_admin, 'seynd_update_site');
	$this->loader->add_action('wp_ajax_update_site_list', $plugin_admin, 'seynd_update_site_list');
	$this->loader->add_action('admin_menu', $plugin_admin, 'seynd_theme_options_panel');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
	$plugin_public = new Seynd_conn_Public($this->get_plugin_name(), $this->get_version());
	$this->loader->add_action('wp_footer', $plugin_public, 'seynd_custom_content_after_body_open_tag');	
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
	$this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
	return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Seynd_conn_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
	return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
	return $this->version;
    }

}
