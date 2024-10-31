<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://seynd.com
 * @since      1.0.0
 *
 * @package    Seynd_conn
 * @subpackage Seynd_conn/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Seynd_conn
 * @subpackage Seynd_conn/includes
 * @author     Seynd <Seynd>
 */
class Seynd_conn_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
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
	}

}
