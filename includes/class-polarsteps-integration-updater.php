<?php

/**
 * Updates Data into Polarsteps Table
 *
 * @link       https://github.com/jan-muller/polarsteps-integration
 * @since      1.0.0
 *
 * @package    Polarsteps_Integration
 * @subpackage Polarsteps_Integration/includes
 */

/**
 * Updates the Polarsteps Data from API.
 *
 * @package    Polarsteps_Integration
 * @subpackage Polarsteps_Integration/includes
 * @author     NPersonn <nick@personn.com>
 */
class Polarsteps_Integration_Updater
{
	/**
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
	 * @var Polarsteps_Integration_Connector
	 */
	private $connector;

	/**
	 * @var Polarsteps_Integration_Data_Loader
	 */
	private $data_loader;

	/**
	 * Polarsteps_Integration_Updater constructor.
	 *
	 * @param Polarsteps_Integration_Connector $connector
	 * @param Polarsteps_Integration_Data_Loader $data_loader
	 *
	 * @since 1.0.0
	 */
	public function __construct(Polarsteps_Integration_Connector $connector, Polarsteps_Integration_Data_Loader $data_loader)
	{

		global $wpdb;
		global $polarsteps_table_name;

		$this->wpdb        = $wpdb;
		$this->table_name  = $polarsteps_table_name;
		$this->connector   = $connector;
		$this->data_loader = $data_loader;
	}

	/**
	 * Update the steps from Polarsteps API
	 * If not already existing create new, if incomplete update incomplete
	 *
	 * @since    1.0.0
	 * 
	 * @return void
	 */
	public function update()
	{
		$steps          = $this->connector->polarsteps_get_step_data();
		$existing_steps = $this->data_loader->get_all_steps();

		if (!is_array($steps)) {
			return;
		}

		foreach ($steps as $step) {
			$exists = in_array($step['uuid'], array_column($existing_steps, 'uuid'));

			if ($exists) {
				$this->wpdb->update(
					$this->table_name,
					$step,
					['uuid' => $step['uuid']]
				);

				continue;
			}

			$this->wpdb->insert(
				$this->table_name,
				$step
			);
		}
	}
}
