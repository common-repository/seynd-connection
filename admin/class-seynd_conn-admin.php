<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://seynd.com
 * @since      1.0.0
 *
 * @package    Seynd_conn
 * @subpackage Seynd_conn/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Seynd_conn
 * @subpackage Seynd_conn/admin
 * @author     Seynd <Seynd>
 */
class Seynd_conn_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

	$this->plugin_name = $plugin_name;
	$this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function seynd_enqueue_styles() {
	wp_enqueue_style('seynd_conn-admin-ui-css', plugin_dir_url(__FILE__) . 'css/jquery-ui.css', array(), $this->version, 'all');
	wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/seynd_conn-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function seynd_enqueue_scripts() {
	wp_enqueue_script('jquery-ui-dialog');
	wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/seynd_conn-admin.js', array('jquery'), $this->version, false);
	wp_localize_script($this->plugin_name, 'server', array('seynd_app_path' => SEYND_URL, 'ajax_url' => admin_url('admin-ajax.php')));
    }

    public function seynd_getpages_callback() {
	global $wpdb;

	$pages = get_pages();
	$staticfront = get_option("page_on_front", TRUE);
	if ($staticfront <= 0) {
	    array_unshift($pages, array('ID' => $staticfront, 'post_title' => "Front Page"));
	}

	$seynd_connection = get_option('seynd_connection');
	$seynd_selected_page_array = array();
	if ($seynd_connection) {
	    $seynd_page_type = get_option('seynd_page_type');
	    if ($seynd_page_type == 'selected_page') {
		$seynd_selected_page = get_option('seynd_selected_page');
		$seynd_selected_page_array = explode(",", $seynd_selected_page);
	    }
	}

	if (!empty($pages)) {
	    $result = array('status' => 1, 'pages' => $pages, 'staticfront' => $staticfront, 'seynd_selected_page_array' => $seynd_selected_page_array);
	} else {
	    $result = array('status' => 0);
	}
	echo json_encode($result);
	wp_die();
    }

    public function seynd_plugin_action_links($links) {
	$links[] = '<a href="' . menu_page_url('seynd-connection', false) . '">Settings</a>';
	return $links;
    }

    public function seynd_disconnect() {
	delete_option('seynd_connection');
	delete_option('seynd_css');
	delete_option('seynd_js');
	delete_option('seynd_html');
	delete_option('seynd_main_website_alias');
	delete_option('seynd_URLdomain');
	delete_option('seynd_page_type');
	delete_option('seynd_selected_page');
	delete_option('seynd_token');
	delete_option('seynd_sites_list');
	echo json_encode(array('status' => 1, 'message' => __('Disconnected successfully', 'seynd_conn')));
	wp_die();
    }

    private function getpopuphtml($website_alias, $subdomain) {
	$pushHTML = '<div style="display:none" id="popup" class="pushquery seynd-plugin">'
		. '<div class="popup-left-content"><img src="' . plugin_dir_url(dirname(__FILE__)) . 'admin/images/seynd-bell.png"/>'
		. '</div>'
		. '<div class="popup-right-content">' . wp_unslash($website_alias) . ' would like to send you notifications! Would you like to receive them? '
		. '</div>'
		. '<div class="popup-btm-content">'
		. '<span id="img-powered">Powered by ' . SEYND_NAME . '</span>'
		. '<div class="popup-all-btns">'
		. '<a id="popup-yes" data-name="' . wp_unslash($subdomain) . '" href="https://' . wp_unslash($subdomain) . '.sent-to.me">Continue</a>'
		. '<a id="popup-close">Dismiss</a>'
		. '</div>'
		. '</div>'
		. '</div>';
	return $pushHTML;
    }

    public function seynd_add_code() {

	// validation
	$validate = true;
	$message = "";
	if (!isset($_POST['page_type']) || empty($_POST['page_type'])) {
	    $validate = false;
	    $message = __('Page type is required', 'seynd_conn');
	} else if (!isset($_POST['website_alias']) || empty($_POST['website_alias'])) {
	    $validate = false;
	    $message = __('Website alias is required', 'seynd_conn');
	} else if (!isset($_POST['subdomain']) || empty($_POST['subdomain'])) {
	    $validate = false;
	    $message = __('Subdomain name required', 'seynd_conn');
	} else if (!isset($_POST['URLdomain']) || empty($_POST['URLdomain'])) {
	    $validate = false;
	    $message = __('Domain url required', 'seynd_conn');
	} else if (!isset($_POST['seynd_token']) || empty($_POST['seynd_token'])) {
	    $validate = false;
	    $message = __('Seynd Token is required', 'seynd_conn');
	} else if (!isset($_POST['sites_list']) || empty($_POST['sites_list'])) {
	    $validate = false;
	    $message = __('Sites list are required', 'seynd_conn');
	}

	if ($validate == false) {
	    echo json_encode(array('status' => 0, 'message' => $message));
	    exit();
	}

	$selected_page_str = '';
	if (isset($_POST['selected_page']) && !empty($_POST['selected_page'])) {
	    $selected_page_str = implode(",", $this->recursive_sanitize_text_field(wp_unslash($_POST['selected_page'])));
	}

	$scriptURL = SEYND_URL;
	$pushjs = $scriptURL . "/src/push.js";
	$pushcss = $scriptURL . "/src/push.css";

	$pushHTML = $this->getpopuphtml(sanitize_text_field($_POST['website_alias']), sanitize_text_field($_POST['subdomain']));
	add_option('seynd_connection', 1);
	add_option('seynd_css', $pushcss);
	add_option('seynd_js', $pushjs);
	add_option('seynd_html', $pushHTML);
	add_option('seynd_main_website_alias', sanitize_text_field($_POST['website_alias']));
	add_option('seynd_URLdomain', esc_url_raw($_POST['URLdomain']));
	add_option('seynd_page_type', sanitize_text_field($_POST['page_type']));
	add_option('seynd_selected_page', $selected_page_str);
	add_option('seynd_token', sanitize_text_field($_POST['seynd_token']));
	$sites_list = isset($_POST['sites_list']) ? $this->recursive_sanitize_text_field(wp_unslash($_POST['sites_list'])) : array();
	add_option('seynd_sites_list', $sites_list);
	echo json_encode(array('status' => 1, 'message' => __('Connection successfully established', 'seynd_conn'), 'selected_page_str' => $selected_page_str, 'sites_list' => $sites_list));
	wp_die();
    }

    public function seynd_edit_code() {
	// validation
	$validate = true;
	$message = "";
	if (!isset($_POST['page_type']) || empty($_POST['page_type'])) {
	    $validate = false;
	    $message = __('Page type is required', 'seynd_conn');
	}
	
	if ($validate == false) {
	    echo json_encode(array('status' => 0, 'message' => $message));
	    exit();
	}
	
	$selected_page_str = '';
	if (isset($_POST['selected_page']) && !empty($_POST['selected_page'])) {
	    $selected_page_str = implode(",", $this->recursive_sanitize_text_field(wp_unslash($_POST['selected_page'])));
	}

	update_option('seynd_page_type', sanitize_text_field($_POST['page_type']));
	update_option('seynd_selected_page', $selected_page_str);

	echo json_encode(array('status' => 1, 'message' => __('Updated Successfully', 'seynd_conn'), 'selected_page_str' => $selected_page_str));
	wp_die();
    }

    public function seynd_update_site() {
	// validation
	$validate = true;
	$message = "";
	
	if (!isset($_POST['website_alias']) || empty($_POST['website_alias'])) {
	    $validate = false;
	    $message = __('Website alias is required', 'seynd_conn');
	} else if (!isset($_POST['subdomain']) || empty($_POST['subdomain'])) {
	    $validate = false;
	    $message = __('Subdomain name required', 'seynd_conn');
	} else if (!isset($_POST['URLdomain']) || empty($_POST['URLdomain'])) {
	    $validate = false;
	    $message = __('Domain url required', 'seynd_conn');
	}
	
	if ($validate == false) {
	    echo json_encode(array('status' => 0, 'message' => $message));
	    exit();
	}
	
	update_option('seynd_main_website_alias', sanitize_text_field($_POST['website_alias']));
	update_option('seynd_URLdomain', esc_url_raw($_POST['URLdomain']));

	$pushHTML = $this->getpopuphtml(sanitize_text_field($_POST['website_alias']), sanitize_text_field($_POST['subdomain']));
	update_option('seynd_html', $pushHTML);
	echo json_encode(array('status' => 1, 'message' => __('Updated Successfully', 'seynd_conn')));
	wp_die();
    }

    public function seynd_update_site_list() {
	// validation
	$validate = true;
	$message = "";
	if (!isset($_POST['sites_list']) || empty($_POST['sites_list'])) {
	    $validate = false;
	    $message = __('Sites list are required', 'seynd_conn');
	}
	
	if ($validate == false) {
	    echo json_encode(array('status' => 0, 'message' => $message));
	    exit();
	}
	
	$sites_list = isset($_POST['sites_list']) ? $this->recursive_sanitize_text_field(wp_unslash($_POST['sites_list'])) : array();
	update_option('seynd_sites_list', $sites_list);
	echo json_encode(array('status' => 1, 'message' => __('Websites synchronized successfully', 'seynd_conn')));
	wp_die();
    }

    public function seynd_theme_options_panel() {
	add_options_page('Seynd Connection', 'Seynd Connection', 'manage_options', 'seynd-connection', array($this, 'seynd_plugin_func'));
    }

    public function seynd_plugin_func() {
	require SEYND_CONN_DIR . '/admin/partials/seynd-connection.php';
    }

    public function recursive_sanitize_text_field($array) {
	foreach ($array as $key => &$value) {
	    if (is_array($value)) {
		$value = $this->recursive_sanitize_text_field($value);
	    } else {
		$value = sanitize_text_field($value);
	    }
	}
	return $array;
    }

}
