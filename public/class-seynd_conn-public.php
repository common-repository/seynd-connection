<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://seynd.com
 * @since      1.0.0
 *
 * @package    Seynd_conn
 * @subpackage Seynd_conn/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Seynd_conn
 * @subpackage Seynd_conn/public
 * @author     Seynd <Seynd>
 */
class Seynd_conn_Public {

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
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

	$this->plugin_name = $plugin_name;
	$this->version = $version;
    }
    
    public function seynd_add_seynd_js_css() {
	$seynd_css = get_option('seynd_css');
	$seynd_js = get_option('seynd_js');
	
	wp_enqueue_style('seynd-css', $seynd_css, array(), SEYND_CONN_VERSION, 'all');
	wp_enqueue_script('seynd-js', $seynd_js, array(), SEYND_CONN_VERSION, false);	
    }
        
    public function seynd_custom_content_after_body_open_tag() {
	$seynd_connection = get_option('seynd_connection');
	if ($seynd_connection) {
	    global $post;
	    $current_id = $post->ID;
	    if (is_front_page()) {
		$staticfront = get_option("page_on_front", TRUE);
		if ($staticfront <= 0) {
		    $current_id = 0;
		}
	    }
	    $seynd_html = get_option('seynd_html');
	    $seynd_page_type = get_option('seynd_page_type');
	    if ($seynd_page_type == 'selected_page') {
		$seynd_selected_page = get_option('seynd_selected_page');
		$seynd_selected_page_arr = explode(",", $seynd_selected_page);
		if (in_array($current_id, $seynd_selected_page_arr)) {
		    $this->seynd_add_seynd_js_css();
		    echo $seynd_html;
		}
	    } else {
			$this->seynd_add_seynd_js_css();
			echo $seynd_html;
	    }
	}
    }
}