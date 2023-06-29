<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/jan-muller/polarsteps-integration
 * @since      1.0.0
 *
 * @package    Polarsteps_Integration
 * @subpackage Polarsteps_Integration/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Polarsteps_Integration
 * @subpackage Polarsteps_Integration/admin
 * @author     Jan Muller <jan@embite.nl>
 */
class Polarsteps_Integration_Admin
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
	 * @var Polarsteps_Integration_Data_Loader
	 */
	private $data_loader;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name    The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->data_loader = new Polarsteps_Integration_Data_Loader;
		$this->actions();
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/polarsteps-integration-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/polarsteps-integration-admin.js', array('jquery'), $this->version, false);
	}

	/**
	 * Registers the required Settings for Polarsteps Integration
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_settings()
	{
		register_setting('polarsteps_settings', 'polarsteps_trip_id', array(
			'show_in_rest' => true,
			'type'         => 'integer',
			'description'  => __('Trip Id from Polarsteps API.'),
			'default'      => 0,
		));

		register_setting('polarsteps_settings', 'polarsteps_max_number_of_steps_per_post', array(
			'show_in_rest' => true,
			'type'         => 'integer',
			'description'  => __('Maximum number of steps you want to have in one post.'),
			'default'      => 5,
		));

		register_setting('polarsteps_settings', 'polarsteps_day_of_week_to_generate', array(
			'show_in_rest' => true,
			'type'         => 'integer',
			'description'  => __('Day of the week you want to generate a post.'),
			'default'      => 0,
		));
	}

	/**
	 * Creates the submenu item and calls render-method to render the page
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_options_page()
	{

		add_options_page(
			'Polarsteps Integration Settings',
			'Polarsteps Settings',
			'manage_options',
			'polarsteps-settings',
			array($this, 'render')
		);

		add_options_page(
			'Polarsteps Steps',
			'Polarsteps Steps',
			'manage_options',
			'polarsteps-steps',
			array($this, 'render_steps')
		);
	}

	/**
	 * Render the contents of the settings page, if User has sufficient rights
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render()
	{

		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient rights to access this page.'));
		}

?>
		<div class="wrap">

			<h1>
				<?php
				_e('Polarsteps Integration Settings', 'polarsteps-integration');
				?>
			</h1>

			<form method="post" action="options.php">

				<?php
				settings_fields('polarsteps_settings');
				?>

				<h2><?php _e('How to get started', 'polarsteps-integration'); ?></h2>
				<ul>
					<li>- <?php _e('Go to polarsteps.com and select the trip you want to import.', 'polarsteps-integration'); ?></li>
					<li>- <?php _e('The trip visiblilty must be set to <b>Public</b>.', 'polarsteps-integration'); ?></li>
					<li>- <?php _e('Get the trip id from the URL. Only the numbers are needed.', 'polarsteps-integration'); ?></li>
					<li>- <?php _e('Eg. https://www.polarsteps.com/username/<b style="text-decoration:underline;">1234567</b>-trip-name', 'polarsteps-integration'); ?></li>
					<li>- <?php _e('Every hour all new posts will be imported.', 'polarsteps-integration'); ?></li>
					<li>- <?php _e('Once a week a blogpost will be generated including the new steps.', 'polarsteps-integration'); ?></li>
					<li>- <?php echo sprintf(
								__('Both actions can be triggerd manually on the %s polarsteps steps page %s', 'polarsteps-integration'),
								'<a href="' . get_admin_url() . 'options-general.php?page=polarsteps-steps">',
								'</a>'
							); ?></li>
				</ul>

				<hr>

				<table class="form-table">

					<tr>
						<th scope="row"><label for="polarsteps_trip_id"><?php _e('Polarsteps Trip ID', 'polarsteps-integration'); ?></label>
						</th>
						<td>
							<input name="polarsteps_trip_id" type="number" step="1" min="0" id="polarsteps_trip_id" value="<?php form_option('polarsteps_trip_id'); ?>" class="regular-text" />
						</td>
					</tr>


					<tr>
						<th scope="row"><label for="polarsteps_max_number_of_steps_per_post"><?php _e('Maximum number of steps you want to have in one post. (-1 means unlimited)', 'polarsteps-integration'); ?></label>
						</th>
						<td>
							<input name="polarsteps_max_number_of_steps_per_post" type="number" step="1" min="-1" id="polarsteps_max_number_of_steps_per_post" value="<?php form_option('polarsteps_max_number_of_steps_per_post'); ?>" class="regular-text" />
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="polarsteps_max_number_of_steps_per_post"><?php _e('Day of the week you want to generate a post.', 'polarsteps-integration'); ?></label>
						</th>
						<td>
							<select name="polarsteps_day_of_week_to_generate" id="polarsteps_day_of_week_to_generate">
								<option value="1" <?php if (get_option('polarsteps_day_of_week_to_generate') === '1') echo 'selected="selected"'; ?>><?php _e('Monday'); ?></option>
								<option value="2" <?php if (get_option('polarsteps_day_of_week_to_generate') === '2') echo 'selected="selected"'; ?>><?php _e('Tuesday'); ?></option>
								<option value="3" <?php if (get_option('polarsteps_day_of_week_to_generate') === '3') echo 'selected="selected"'; ?>><?php _e('Wednesday'); ?></option>
								<option value="4" <?php if (get_option('polarsteps_day_of_week_to_generate') === '4') echo 'selected="selected"'; ?>><?php _e('Thursday'); ?></option>
								<option value="5" <?php if (get_option('polarsteps_day_of_week_to_generate') === '5') echo 'selected="selected"'; ?>><?php _e('Friday'); ?></option>
								<option value="6" <?php if (get_option('polarsteps_day_of_week_to_generate') === '6') echo 'selected="selected"'; ?>><?php _e('Saturday'); ?></option>
								<option value="0" <?php if (get_option('polarsteps_day_of_week_to_generate') === '0') echo 'selected="selected"'; ?>><?php _e('Sunday'); ?></option>
							</select>
						</td>
					</tr>

					<tr>
						<td colspan="2">
							<p><?php _e('Time is fixed (for now) at 7pm. So the post will be generated at 7pm on the selected day.', 'polarsteps-integration'); ?></p>
							<p><?php echo sprintf(
									__('Or you can generate a post manually on the %s polarsteps steps page %s', 'polarsteps-integration'),
									'<a href="' . get_admin_url() . 'options-general.php?page=polarsteps-steps">',
									'</a>'
								); ?></p>
						</td>
					</tr>

				</table>
				<?php do_settings_fields('polarsteps_settings', 'default'); ?>

				<?php
				do_settings_sections('polarsteps_settings');

				submit_button();
				?>
			</form>
		</div>

	<?php

	}

	public function render_steps()
	{
		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient rights to access this page.'));
		}

		$steps = $this->data_loader->get_all_steps();
	?>
		<div class="wrap polarsteps-steps">

			<h1><?php _e('Polarsteps Steps', 'polarsteps-integration'); ?></h1>

			<div style="margin-bottom: 15px;">
				<a href="<?php admin_url(); ?>options-general.php?page=polarsteps-steps&action=get_steps" id="get_steps" class="button button-primary" value="<?php _e('Update steps from polarsteps', 'polarsteps-integration'); ?>"><?php _e('Update steps from polarsteps', 'polarsteps-integration'); ?></a>
				<a href="<?php admin_url(); ?>options-general.php?page=polarsteps-steps&action=generate_post" id="generate_post" class="button button-primary" value="<?php _e('Generate new post', 'polarsteps-integration'); ?>"><?php _e('Generate new post', 'polarsteps-integration'); ?></a>
			</div>

			<table class="widefat fixed" cellspacing="0">
				<thead>
					<tr>
						<!-- <th id="cb" class="manage-column column-cb check-column" scope="col"></th> -->
						<th id="location" class="manage-column column-location" scope="col">Location</th>
						<th id="in-post" class="manage-column column-in-post" scope="col">In post</th>
						<th id="date" class="manage-column column-date" scope="col">Date</th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<!-- <th class="manage-column column-cb check-column" scope="col"></th> -->
						<th id="location" class="manage-column column-location" scope="col">Location</th>
						<th id="in-post" class="manage-column column-in-post" scope="col">In post</th>
						<th id="date" class="manage-column column-date" scope="col">Date</th>
					</tr>
				</tfoot>

				<tbody>
					<?php
					$i = 0;
					foreach ($steps as $step) {
						$i++;
						echo '<tr class="' . ($i % 2 ? 'alternate' : '') . '">';
						// echo '<td>
						// 	<input id="cb-select-' . $step->id . '" type="checkbox" name="steps[]" value="' . $step->id . '">
						// </td>';
						echo "<td>$step->location_name ($step->location_country_code)</td>";
						if ($step->post_id === '0') {
							echo "<td>Skipped</td>";
						} else if ($step->post_id) {
							echo '<td class="has-row-actions">
									' . get_the_title($step->post_id) . '
									<div class="row-actions">
										<span class="edit"><a href="' . get_admin_url() . 'post.php?post=' . $step->post_id . '&action=edit">Bewerken</a> | </span>
										<span class="view"><a href="' . get_permalink($step->post_id) . '" rel="bookmark" target="_blank">Bekijken</a></span>
									</div>
								</td>';
						} else {
							echo '<td>
									No
									<div class="row-actions">
										<span class="skip"><a href="' . get_admin_url() . 'options-general.php?page=polarsteps-steps&step=' . $step->id . '&action=skip" aria-label="skip ">Skip</a></span>
									</div>
								</td>';
						}
						echo "<td>$step->start_time</td>";

						echo "</tr>";
					}
					?>
				</tbody>
			</table>

		</div>
<?php
	}

	/**
	 * Updates the Steps. It is been triggered from Admin-Context e.g. on update_option_polarsteps_trip_id
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function polarsteps_update_steps_from_admin()
	{
		// Remove the polarsteps_user_id from wp_options
		update_option('polarsteps_user_id', null);

		// Update Steps with new Options
		do_action('polarsteps_update_steps');
	}

	/**
	 * Validates the Settings for Trip Id
	 *
	 * @since 1.0.0
	 *
	 * @param string $new_value The newly set Trip Id
	 *
	 * @return bool
	 */
	public function polarsteps_validate_trip_id($new_value)
	{
		if (empty($new_value)) {
			add_settings_error(
				'polarsteps_trip_id',
				'-1',
				'Trip Id cannot be empty'
			);

			return false;
		}

		$is_trip_id_exists = apply_filters('polarsteps_validate_trip_id', $new_value);

		if ($is_trip_id_exists == false) {
			add_settings_error(
				'polarsteps_trip_id',
				'-1',
				sprintf('Trip Id "%s" does not exist on Polarsteps.com. Or the trip visibility is not set to public.', $new_value)
			);

			return false;
		}

		return $new_value;
	}

	/**
	 * Actions for the steps page
	 *
	 * @since 1.0.0
	 */
	private function actions()
	{

		if (isset($_GET['action']) && isset($_GET['step']) && $_GET['action'] == 'skip') {
			$this->data_loader->set_post_id(0, [$_GET['step']]);

			add_action('wp_loaded', function () {
				wp_redirect(admin_url('/options-general.php?page=polarsteps-steps'));
				exit;
			});
		}

		if (isset($_GET['action']) && $_GET['action'] == 'get_steps') {
			add_action('wp_loaded', function () {
				do_action('polarsteps_update_steps');
				wp_redirect(admin_url('/options-general.php?page=polarsteps-steps'));
				exit;
			});
		}

		if (isset($_GET['action']) && $_GET['action'] == 'generate_post') {
			add_action('wp_loaded', function () {
				do_action('polarsteps_generate_new_post', true);
				wp_redirect(admin_url('/options-general.php?page=polarsteps-steps'));
				exit;
			});
		}
	}

	/**
	 * Add action links to the plugins page.
	 *
	 * @since    1.0.0
	 */
	function plugin_action_links($actions)
	{
		error_log('plugin_action_links');

		$link = '<a href="' . admin_url('options-general.php?page=polarsteps-settings') . '">' . __('Settings', 'polarsteps-integration') . '</a>';
		array_push($actions, $link);

		return $actions;
	}
}
