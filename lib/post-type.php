<?php defined('ABSPATH') or die();

/**
 * Registers a custom post type for 3dvt-tour
 * @return void
 */
function tdvt_create_post_type() {
  register_post_type('3dvt-tour',
    array(
      'labels' => array(
        'name' => __( '3D Tours' ),
        'singular_name' => __( '3D Tour' )
      ),
      'public' => false,
      'show_ui' => true,
      'has_archive' => false,
      'supports' => array('title'),
      'exclude_from_search' => true,
      'show_in_nav_menus' => false
    )
  );
}
add_action( 'init', 'tdvt_create_post_type' );

/**
 * Removes Yoast SEO box from 3dvt-tour posts.
 * @return void
 */
function tdvt_remove_wp_seo_meta_box() {
	remove_meta_box('wpseo_meta', '3dvt-tour', 'normal');
}
add_action('add_meta_boxes', 'tdvt_remove_wp_seo_meta_box', 100);
