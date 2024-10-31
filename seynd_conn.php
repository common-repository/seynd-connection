<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://seynd.com
 * @since             1.0.1
 * @package           Seynd_conn
 *
 * @wordpress-plugin
 * Plugin Name:       Seynd Connection
 * Plugin URI:        https://seynd.com/wordpress-plugin
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.1
 * Author:            Seynd
 * Author URI:        https://seynd.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       seynd_conn
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Plugin Constants
 */
define('SEYND_CONN_VERSION', '1.0.1');
define('SEYND_CONN_DIR', plugin_dir_path(__FILE__));
define('SEYND_PLUGIN_BASENAME', plugin_basename( __FILE__ ));
define('SEYND_NAME', 'Seynd');
define('SEYND_URL', 'https://pushnotifications.getinstafy.com');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-seynd_conn-activator.php
 */
function activate_seynd_conn() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-seynd_conn-activator.php';
    Seynd_conn_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-seynd_conn-deactivator.php
 */
function deactivate_seynd_conn() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-seynd_conn-deactivator.php';
    Seynd_conn_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_seynd_conn');
register_deactivation_hook(__FILE__, 'deactivate_seynd_conn');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-seynd_conn.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_seynd_conn() {    
    $plugin = new Seynd_conn();
    $plugin->run();
}

run_seynd_conn();
