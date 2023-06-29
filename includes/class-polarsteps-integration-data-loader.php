<?php

/**
 * Loads Polarsteps Data from Wordpress DB
 *
 * @link       https://github.com/jan-muller/polarsteps-integration
 * @since      1.0.0
 *
 * @package    Polarsteps_Integration
 * @subpackage Polarsteps_Integration/includes
 */

/**
 * Loading Trip Data
 *
 * @since      1.0.0
 * @package    Polarsteps_Integration
 * @subpackage Polarsteps_Integration/includes
 * @author     Jan Muller <jan@embite.nl>
 */
class Polarsteps_Integration_Data_Loader
{

	/**
	 * @since 1.0.0
	 * @var wpdb
	 */
	private $wpdb;

	/**
	 * Tablename of Trips in Wpdb
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $table_name;

	/**
	 * Polarsteps_Integration_Data_Loader constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct()
	{

		global $wpdb;
		global $polarsteps_table_name;

		$this->wpdb       = $wpdb;
		$this->table_name = $polarsteps_table_name;
	}

	/**
	 * Getting all steps from wordpress db
	 * 
	 * @since 1.0.0
	 * @return array
	 */
	public function get_all_steps()
	{
		return $this->wpdb->get_results(sprintf('SELECT * FROM %s ORDER BY id DESC', $this->table_name));
	}

	/**
	 * Get all steps that are not posted yet
	 * 
	 * @since 1.0.0
	 * @return array
	 */
	public function get_new_steps()
	{
		return $this->wpdb->get_results(sprintf('SELECT * FROM %s WHERE `post_id` IS NULL ORDER BY id ASC', $this->table_name));
	}

	/**
	 * Set post id for steps
	 * 
	 * @since 1.0.0
	 * @param int $post_id
	 * @param array $ids
	 * 
	 * @return array
	 */
	public function set_post_id($post_id, $ids)
	{
		$ids = implode(',', $ids);
		return $this->wpdb->query(sprintf('UPDATE %s SET `post_id` = %d WHERE `id` IN (%s)', $this->table_name, $post_id, $ids));
	}

	/**
	 * Set post number for steps
	 * 
	 * @since 1.0.0
	 * @param int $post_number
	 * @param array $ids
	 * 
	 * @return array
	 */
	public function set_post_number($post_number, $ids)
	{
		$ids = implode(',', $ids);
		return $this->wpdb->query(sprintf('UPDATE %s SET `post_number` = %d WHERE `id` IN (%s)', $this->table_name, $post_number, $ids));
	}

	/**
	 * Get all steps that are not posted yet
	 * 
	 * @since 1.0.0
	 * @param int $numer_of_posts
	 * 
	 * @return array
	 */
	public function get_latests_posted_step($numer_of_posts = 1)
	{
		return $this->wpdb->get_results(sprintf('SELECT * FROM %s WHERE `post_number` IS NOT NULL ORDER BY id DESC LIMIT %d', $this->table_name, $numer_of_posts));
	}
}
