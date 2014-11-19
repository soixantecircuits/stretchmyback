<?php
/**
 * Stretch my back
 *
 * Simple strecth fullscreen in background for everybody
 *
 * @package   Stretch_my_back
 * @author    gabrielstuff <gabriel@soixantecircuits.fr>
 * @license   GPL-2.0+
 * @link      http://soixantecircuits.fr
 * @copyright 2014 gabrielstuff
 *
 * @wordpress-plugin
 * Plugin Name:       Stretch my back
 * Plugin URI:        http://soixantecircuits.fr
 * Description:       Simple strecth fullscreen in background for everybody
 * Version:           0.0.1
 * Author:            gabrielstuff
 * Author URI:        http://soixantecircuits.fr
 * Text Domain:       strechmyback
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: 
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-strechmyback.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'Stretch_my_back', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Stretch_my_back', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Stretch_my_back', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-strechmyback-admin.php' );
	add_action( 'plugins_loaded', array( 'Stretch_my_back_Admin', 'get_instance' ) );

}
