<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/jan-muller/polarsteps-integration
 * @since      1.0.0
 *
 * @package    Polarsteps_Integration
 * @subpackage Polarsteps_Integration/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Polarsteps_Integration
 * @subpackage Polarsteps_Integration/includes
 * @author     Jan Muller <jan@embite.nl>
 */
class Polarsteps_Integration_Deactivator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
	{
		$timestamp = wp_next_scheduled('polarsteps_update_steps');
		wp_unschedule_event($timestamp, 'polarsteps_update_steps');

		$timestamp = wp_next_scheduled('polarsteps_generate_new_post');
		wp_unschedule_event($timestamp, 'polarsteps_generate_new_post');

		global $wpdb;
		global $polarsteps_table_name;
		$wpdb->query("DROP TABLE IF EXISTS {$polarsteps_table_name}");
	}
}
