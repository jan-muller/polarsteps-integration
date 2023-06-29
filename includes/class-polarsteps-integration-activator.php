<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/jan-muller/polarsteps-integration
 * @since      1.0.0
 *
 * @package    Polarsteps_Integration
 * @subpackage Polarsteps_Integration/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Polarsteps_Integration
 * @subpackage Polarsteps_Integration/includes
 * @author     Jan Muller <jan@embite.nl>
 */
class Polarsteps_Integration_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		global $wpdb;
		global $polarsteps_db_version;
		global $polarsteps_table_name;

		$charset_collate = $wpdb->get_charset_collate();

		$installed_version = get_option('polarsteps_db_version');

		if ($installed_version != $polarsteps_db_version) {

			$sql = "CREATE TABLE IF NOT EXISTS $polarsteps_table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				post_id bigint,
				post_number bigint,
				uuid text NOT NULL,
				start_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				location_name VARCHAR(155),
				detail VARCHAR(155),
				location_lat FLOAT NOT NULL, 
				location_lon FLOAT NOT NULL,
				location_country_code VARCHAR(5),
				legacy_id mediumint(9),
				description blob,
				slug VARCHAR (55),
				trip_id mediumint(9),
				thumbnail_path_small VARCHAR(155),		 
				thumbnail_path_large VARCHAR(155),	
				media json,	 
				PRIMARY KEY  (id)
		) $charset_collate;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);

			add_option('polarsteps_db_version', $polarsteps_db_version);
		}

		add_option('polarsteps_trip_id');
		add_option('polarsteps_max_number_of_steps_per_post');
		add_option('polarsteps_day_of_week_to_generate');

		if (!wp_next_scheduled('polarsteps_update_steps')) {
			wp_schedule_event(time(), 'hourly', 'polarsteps_update_steps');
		}

		if (!wp_next_scheduled('polarsteps_generate_new_post')) {
			wp_schedule_event(strtotime('7pm'), 'daily', 'polarsteps_generate_new_post');
		}
	}
}
