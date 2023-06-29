<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             1.0.0
 * @package           Polarsteps_Integration
 *
 * @wordpress-plugin
 * Plugin Name:       Polarsteps integration
 * Plugin URI: 		  https://github.com/jan-muller/polarsteps-integration
 * Description:       Import steps from Polarsteps.com and convert them into a blogpost.
 * Version:           1.0.0
 * Author:            Jan Muller
 * Author URI:        https://github.com/jan-muller
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       polarsteps-integration
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('POLARSTEPS_INTEGRATION_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-polarsteps-integration-activator.php
 */
function activate_polarsteps_integration()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-polarsteps-integration-activator.php';
	Polarsteps_Integration_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-polarsteps-integration-deactivator.php
 */
function deactivate_polarsteps_integration()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-polarsteps-integration-deactivator.php';
	Polarsteps_Integration_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_polarsteps_integration');
register_deactivation_hook(__FILE__, 'deactivate_polarsteps_integration');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-polarsteps-integration.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_polarsteps_integration()
{
	$plugin = new Polarsteps_Integration();
	$plugin->run();
}
run_polarsteps_integration();
