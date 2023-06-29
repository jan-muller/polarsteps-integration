<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/jan-muller/polarsteps-integration
 * @since      1.0.0
 *
 * @package    Polarsteps_Integration
 * @subpackage Polarsteps_Integration/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Polarsteps_Integration
 * @subpackage Polarsteps_Integration/public
 * @author     Jan Muller <jan@embite.nl>
 */
class Polarsteps_Integration_Public
{

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
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Polarsteps_Integration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Polarsteps_Integration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/polarsteps-integration-public.css', array(), $this->version, 'all');
		wp_enqueue_style('simpleLightbox', plugin_dir_url(__FILE__) . 'lib/simple-lightbox/dist/simpleLightbox.min.css', array(), $this->version, false);
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Polarsteps_Integration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Polarsteps_Integration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script('simple-lightbox', plugin_dir_url(__FILE__) . 'lib/simple-lightbox/dist/simpleLightbox.min.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/polarsteps-integration-public.js', array('jquery'), $this->version, false);
	}

	/**
	 * Get all persisted steps from Wordpress Db
	 *
	 * @since 0.1.0
	 * @return array
	 */
	public function get_all_steps()
	{
		$data_loader = new Polarsteps_Integration_Data_Loader();

		return $data_loader->get_all_steps();
	}

	/**
	 * Updating Steps in Wordpress Db from Polarsteps API
	 * @since 0.1.0
	 * @return void
	 */
	public function update_steps()
	{
		$step_updater = new Polarsteps_Integration_Updater(
			new Polarsteps_Integration_Connector(),
			new Polarsteps_Integration_Data_Loader()
		);

		$step_updater->update();
	}

	/**
	 * Validating if a trip id exists on Polarsteps API
	 *
	 * @since 0.3.4
	 *
	 * @param string $trip_id
	 *
	 * @return bool
	 */
	public function validate_trip_id($trip_id)
	{
		$connector = new Polarsteps_Integration_Connector();
		return $connector->polarsteps_get_trip_exists($trip_id);
	}

	/**
	 * Generating a new blogpost
	 * @since 0.1.0
	 * @return void
	 */
	public function generate_new_post($force = false)
	{
		$generator = new Polarsteps_Integration_Generator(
			new Polarsteps_Integration_Data_Loader()
		);

		$generator->store($force);
	}
}
