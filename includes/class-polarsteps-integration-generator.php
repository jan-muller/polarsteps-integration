<?php

/**
 * Generating posts based on polarstap steps
 *
 * @link       https://github.com/jan-muller/polarsteps-integration
 * @since      1.0.0
 *
 * @package    Polarsteps_Integration
 * @subpackage Polarsteps_Integration/includes
 */

/**
 * Generating posts based on polarstap steps.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Polarsteps_Integration
 * @subpackage Polarsteps_Integration/includes
 * @author     Jan Muller <jan@embite.nl>
 */
class Polarsteps_Integration_Generator
{
    /**
     * @var Polarsteps_Integration_Data_Loader
     */
    private $data_loader;

    /**
     * Polarsteps_Integration_Updater constructor.
     *
     * @param Polarsteps_Integration_Data_Loader $data_loader
     *
     * @since 1.0.0
     */
    public function __construct(Polarsteps_Integration_Data_Loader $data_loader)
    {
        $this->data_loader = $data_loader;
    }

    /**
     * Generate blog post based on polarsteps steps
     *
     * @since    1.0.0
     * 
     * @param bool $ignore_day_of_the_wheek
     * 
     * @return void
     */
    public function store($ignore_day_of_the_wheek = false)
    {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');

        $day_of_the_week = get_option('polarsteps_day_of_week_to_generate');
        $number_of_steps_per_post = get_option('polarsteps_max_number_of_steps_per_post');

        if (date('w') != $day_of_the_week && !$ignore_day_of_the_wheek) {
            return;
        }

        $latest_step = $this->data_loader->get_latests_posted_step();
        $post_number = ($latest_step[0]->post_number ?? 0) + 1;

        $new_steps = $this->data_loader->get_new_steps();
        if ($number_of_steps_per_post !== '-1') {
            $new_steps = array_slice($new_steps, 0, $number_of_steps_per_post);
        }

        $number_of_steps = count($new_steps);
        $added_steps = [];
        $i = 0;

        $post_title = '#' . $post_number . ' ';
        $post_content = '';

        foreach ($new_steps as $step) {
            $i++;

            if (!$step->description &&  empty(json_decode($step->media))) { // If step has no description and no media, skip it
                $this->data_loader->set_post_id(0, [$step->id]);
                $this->data_loader->set_post_number(0, [$step->id]);
                continue;
            }

            $post_title .= $step->location_name . ($i !== $number_of_steps ? ' | ' : '');

            $post_content .= '<article class="polarsteps-step">';
            $post_content .= '<h2>';
            $post_content .= $step->location_name;
            $post_content .= '</h2>';
            if ($step->description) {
                $post_content .= nl2br($step->description);
            }

            if (!empty(json_decode($step->media))) {
                $post_content .= '<div class="images">';
                foreach (json_decode($step->media) as $image) {
                    $post_content .= '<a href="' . $image . '" title="' . $step->location_name . '" class="images__item" >';
                    $post_content .= '<img src="' . $image . '" alt="' . $step->location_name . '" />';
                    $post_content .= '</a>';
                }
                $post_content .= '</div>';
            }
            $post_content .= '</article>';

            array_push($added_steps, $step->id);
        }

        $wordpress_post = array(
            'post_title' => $post_title,
            'post_content' => $post_content,
            'post_status' => 'publish',
            'post_category' => [get_option('default_category')],
            'post_author' => 1,
            'post_type' => 'post'
        );

        $post_id = wp_insert_post($wordpress_post);

        if (!is_wp_error($post_id)) {
            $this->download_and_set_featured_image($step, $post_id);
            $this->data_loader->set_post_id($post_id, $added_steps);
            $this->data_loader->set_post_number($post_number, $added_steps);
        } else {
            error_log($post_id->get_error_message());
            return;
        }

        if (is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {
            $this->duplicate_post_for_every_language($post_id);
        }
    }

    /**
     * Duplicate post for every language
     * 
     * @since 1.0.0
     * 
     * @param int $post_id
     * 
     * @return void
     */
    private function duplicate_post_for_every_language($post_id)
    {
        global $sitepress;

        $languages = apply_filters('wpml_active_languages', NULL);
        $defaultLanguage = $sitepress->get_current_language();

        foreach ($languages as $language) {
            if ($language['code'] != $defaultLanguage) {
                $sitepress->make_duplicate($post_id, $language['code']);
            }
        }
    }

    /**
     * Download and set featured image
     * 
     * @since 1.0.0
     * 
     * @param object $step
     * @param int $post_id
     * 
     * @return void
     */
    private function download_and_set_featured_image($step, $post_id)
    {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');


        if (!empty(json_decode($step->media))) {
            $image_url = json_decode($step->media)[0];
            $temp_file = download_url($image_url);

            if (is_wp_error($temp_file)) {
                return false;
            }

            $file = array(
                'name'     => basename($image_url),
                'type'     => mime_content_type($temp_file),
                'tmp_name' => $temp_file,
                'size'     => filesize($temp_file),
            );

            $sideload = wp_handle_sideload($file, array('test_form' => false));

            if (!empty($sideload['error'])) {
                return false;
            }

            $attachment_id = wp_insert_attachment(
                array(
                    'guid'              => $sideload['url'],
                    'post_mime_type'    => $sideload['type'],
                    'post_title'        => $step->location_name,
                    'post_content'      => '',
                    'post_status'       => 'inherit',
                ),
                $sideload['file']
            );

            if (is_wp_error($attachment_id) || !$attachment_id) {
                return false;
            }

            wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $sideload['file']));
            set_post_thumbnail($post_id, $attachment_id);
        }
    }
}
