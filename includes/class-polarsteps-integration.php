<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/jan-muller/polarsteps-integration
 * @since      1.0.0
 *
 * @package    Polarsteps_Integration
 * @subpackage Polarsteps_Integration/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Polarsteps_Integration
 * @subpackage Polarsteps_Integration/includes
 * @author     Jan Muller <jan@embite.nl>
 */
class Polarsteps_Integration
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Polarsteps_Integration_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('POLARSTEPS_INTEGRATION_VERSION')) {
			$this->version = POLARSTEPS_INTEGRATION_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'polarsteps-integration';

		$this->load_dependencies();
		$this->set_locale();
		$this->set_globals();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Polarsteps_Integration_Loader. Orchestrates the hooks of the plugin.
	 * - Polarsteps_Integration_i18n. Defines internationalization functionality.
	 * - Polarsteps_Integration_Admin. Defines all hooks for the admin area.
	 * - Polarsteps_Integration_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-polarsteps-integration-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-polarsteps-integration-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-polarsteps-integration-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-polarsteps-integration-public.php';

		/**
		 * The class responsible for defining Database Updating related
		 *
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-polarsteps-integration-updater.php';

		/**
		 * The class responsible for defining Database Loading related
		 *
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-polarsteps-integration-data-loader.php';

		/**
		 * The class responsible for connecting and receiving data from Polarsteps API
		 *
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-polarsteps-integration-connector.php';


		/**
		 * The class responsible for generation new blog posts based on polarsteps steps
		 *
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-polarsteps-integration-generator.php';

		$this->loader = new Polarsteps_Integration_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Polarsteps_Integration_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Polarsteps_Integration_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{
		$plugin_admin = new Polarsteps_Integration_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

		$this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
		$this->loader->add_action('admin_menu', $plugin_admin, 'add_options_page');

		$this->loader->add_filter('pre_update_option_polarsteps_trip_id', $plugin_admin, 'polarsteps_validate_trip_id');
		$this->loader->add_filter('pre_add_option_polarsteps_trip_id', $plugin_admin, 'polarsteps_validate_trip_id');
		$this->loader->add_filter('update_option_polarsteps_trip_id', $plugin_admin, 'polarsteps_update_steps_from_admin');
		$this->loader->add_filter('add_option_polarsteps_trip_id', $plugin_admin, 'polarsteps_update_steps_from_admin');

		$this->loader->add_filter('plugin_action_links_polarsteps-integration/polarsteps-integration.php', $plugin_admin, 'plugin_action_links');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{
		$plugin_public = new Polarsteps_Integration_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

		$this->loader->add_action('polarsteps_get_all_steps', $plugin_public, 'get_all_steps');
		$this->loader->add_action('polarsteps_update_steps', $plugin_public, 'update_steps');
		$this->loader->add_filter('polarsteps_validate_trip_id', $plugin_public, 'validate_trip_id');
		$this->loader->add_action('polarsteps_generate_new_post', $plugin_public, 'generate_new_post');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Polarsteps_Integration_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}

	/**
	 * Setting the main configs for the plugin
	 *
	 * @since   0.1.0
	 * @return void
	 */
	private function set_globals()
	{
		global $wpdb;
		global $polarsteps_db_version;
		global $polarsteps_table_name;

		$polarsteps_db_version = '1';
		$polarsteps_table_name = $wpdb->prefix . 'polarsteps';
	}
}
