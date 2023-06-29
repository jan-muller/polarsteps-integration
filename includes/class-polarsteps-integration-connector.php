<?php

/**
 * Connects to Polarsteps API
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
 * @since      1.0.0
 * @package    Polarsteps_Integration
 * @subpackage Polarsteps_Integration/includes
 * @author     Jan Muller <jan@embite.nl>
 */
class Polarsteps_Integration_Connector
{

	/**
	 * Uri to Polarsteps API
	 * @since 1.0.0
	 * @var string
	 */
	const POLARSTEPS_URI = 'https://api.polarsteps.com/';

	/**
	 * Gets data from Polarsteps API
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function polarsteps_get_step_data()
	{
		$trip_id = get_option('polarsteps_trip_id');
		$response = wp_remote_get(self::POLARSTEPS_URI . $this->buildQuery($trip_id));

		if (is_array($response) && !is_wp_error($response)) {
			$result = $response['body'];
		} else {
			error_log(json_encode($response));
			return [];
		}

		$user_data = json_decode($result);

		if (empty($user_data)) return [];

		$steps = is_array($user_data->all_steps) ? $user_data->all_steps : [];
		$result = [];

		foreach ($steps as $step) {
			if (!isset($step->location_id)) {
				continue;
			}

			$result_step = [];
			$result_step['legacy_id']    = $step->id;
			$result_step['uuid']         = $step->uuid;
			$result_step['start_time']   = gmdate('Y-m-d H:i:s', intval($step->start_time));
			$result_step['location_lat'] = $step->location->lat;
			$result_step['location_lon'] = $step->location->lon;
			$result_step['slug']         = $step->slug;
			$result_step['trip_id']      = $step->trip_id;

			if (!empty($step->location->name)) {
				$result_step['location_name'] = $step->name ? $step->name : $step->location->name;
			}
			if (!empty($step->location->country_code)) {
				$result_step['location_country_code'] = $step->location->country_code;
			}
			if (!empty($step->location->detail)) {
				$result_step['detail'] = $step->location->detail;
			}
			if (!empty($step->description)) {
				$result_step['description'] = $step->description;
			}
			if (!empty($step->media[0]->large_thumbnail_path)) {
				$result_step['thumbnail_path_large'] = $step->media[0]->large_thumbnail_path;
			}
			if (!empty($step->media[0]->small_thumbnail_path)) {
				$result_step['thumbnail_path_small'] = $step->media[0]->small_thumbnail_path;
			}

			$media = array_column($step->media, 'large_thumbnail_path');
			$result_step['media'] = json_encode($media);

			$result[] = $result_step;
		}

		return $result;
	}

	/**
	 * Checks, if trip id is valid
	 *
	 * @param string $trip_id
	 *
	 * @return bool
	 */
	public function polarsteps_get_trip_exists(string $trip_id)
	{
		$response = wp_remote_get(self::POLARSTEPS_URI . $this->buildQuery($trip_id));

		if (is_array($response) && !is_wp_error($response)) {
			$result = $response['body'];

			if ($result && $result != 'Unauthorized') {
				$result = json_decode($result);
				if (!isset($result->message)) {
					return true;
				}
			}
		}

		error_log(json_encode($response));

		return false;
	}

	/**
	 * Building a Query for search for Users
	 *
	 * @since 1.0.0
	 *
	 * @param string $trip_id
	 *
	 * @return string
	 */
	protected function buildQuery(string $trip_id): string
	{
		return 'trips/' . $trip_id;
	}
}
