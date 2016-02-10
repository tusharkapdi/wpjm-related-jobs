<?php
/*
Plugin Name: WPJM Related Jobs
Plugin URI: http://amplebrain.com/wpjm-related-jobs/
Description: WPJM Related Jobs is an addon of WP Job Manager plugin and its display related job list on job detail page. Its filtered jobs of Job Type and Company name fields.
Version: 1.0
Author: Tushar Kapdi
Author URI: http://amplebrain.com/
Text Domain: wpjmrelated
Domain Path: /languages/
Copyright: 2015 Tushar Kapdi
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WP Job Manager Related Jobs is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
WP Job Manager Related Jobs is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with WP Job Manager Related Jobs. If not, see http://www.gnu.org/licenses/gpl-2.0.html.
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WPJM_RELATED_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'WPJM_RELATED_PLUGIN_URI', plugins_url( '' , __FILE__ ) );

include_once( WPJM_RELATED_PLUGIN_DIR . '/includes/options-setting.php' );

/**
 * Run Shortcode
 *
 * @since 1.0
 */
function wpjm_related_jobs_init() {

        // Include shortcodes
        include_once( WPJM_RELATED_PLUGIN_DIR . '/includes/shortcodes.php' );

        // Related Jobs
        add_shortcode( 'WPJM_RELATED_JOBS', 'wpjm_related_jobs_shortcode' );
}
add_action( 'after_setup_theme', 'wpjm_related_jobs_init' );

/**
 * Register text domain for localization.
 *
 * @since 1.0
 */
function wpjm_related_jobs_textdomain() {
    load_plugin_textdomain( 'wpjmrelated', false, WPJM_RELATED_PLUGIN_DIR . '/languages' );
}
add_action( 'plugins_loaded', 'wpjm_related_jobs_textdomain' );

/**
 * Include Stylesheet
 *
 * @since 1.0
 */
function wpjm_related_jobs_scripts() {
    wp_enqueue_style('smpl_shortcodes', WPJM_RELATED_PLUGIN_URI .'/assets/css/style.css');
}
add_action( 'wp_enqueue_scripts', 'wpjm_related_jobs_scripts');

/**
 * Add Related Job In Job Detail Page
 *
 * @since 1.0
 */
function wpjm_related_jobs_the_content_hook($content) {
    global $post;
    
    if( !is_object($post) ) {
        return $content;
    }
    $options=get_option('wpjm_related_jobs');
    
    if ($options['is_job_detail']==1 && $content != "" && $post->post_type === 'job_listing' && (is_single() || is_feed())) {

        include_once( WPJM_RELATED_PLUGIN_DIR . '/includes/related-jobs.php' );

        if($options['position']==1)
            $content = wpjm_related_jobs_display($options) . $content;
        else
            $content = $content . wpjm_related_jobs_display($options);
    }
    return $content;
}
add_filter('the_content', 'wpjm_related_jobs_the_content_hook', 20);

/**
 * Check WP Job Manager Installed
 *
 * @since 1.0
 */
function wpjp_related_job_check() {
    if ( ! defined( 'JOB_MANAGER_VERSION' ) ) {
        ?><div class="error"><p><?php echo __( 'WP Job Manager Related Job plugin requires WP Job Manager to be installed', 'wpjmrelated' ); ?>!</p></div><?php
    }
}
add_action( 'admin_notices', 'wpjp_related_job_check' );

/**
 * Add custom settings link on plugin list page
 *
 * @since 1.0
 */
function wpjm_related_jobs_action_links( $links ) {

   $links[] = '<a href="'. esc_url( get_admin_url(null, 'options-general.php?page=wpjm-realted-jobs-setting') ) .'">'. __( 'Settings', 'wpjmrelated' ) .'</a>';
   
   return $links;

}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'wpjm_related_jobs_action_links' );

/**
 * Add custom donate link on plugin list page
 *
 * @since 1.0
 */
function wpjm_related_jobs_plugin_row_meta( $links, $file ) {

    if ( strpos( $file, 'wpjm-related-jobs.php' ) !== false ) {
        $new_links = array(
            '<a href="http://amplebrain.com/donate/" target="_blank" title="' . __( 'Did you enjoy this plugin? Please [donate to support ongoing development](http://amplebrain.com/donate/). Your contribution would be greatly appreciated.', 'wpjmrelated' ) . '">' . __( 'Donate', 'wpjmrelated' ) . '</a>'
        );
        
        $links = array_merge( $links, $new_links );
    }
    
    return $links;
}
add_filter( 'plugin_row_meta', 'wpjm_related_jobs_plugin_row_meta', 10, 2 );

/**
 * Plugin activation hook
 *
 * @since 1.0
 */
function wpjm_related_jobs_activation_hook() {
    
    $options=array(
        'is_job_detail'=>1,
        'position'=>0,
        'length'=>250,
        'limit'=>3,
        'columns'=>3
    );
    add_option( 'wpjm_related_jobs', $options );
}
register_activation_hook( __FILE__, 'wpjm_related_jobs_activation_hook' );

/**
 * Plugin deactivate hook
 *
 * @since 1.0
 */
function wpjm_related_jobs_deactivate_hook() {
    
    delete_option( 'wpjm_related_jobs' );
}
register_deactivation_hook(__FILE__, 'wpjm_related_jobs_deactivate_hook');