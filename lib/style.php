<?php defined('ABSPATH') or die();

/**
 * Enqueues 3dvt CSS in admin, only on 3dvt-tour post edit screen.
 * @param  string $hook The hook string
 * @return void
 */
function tdvt_load_admin_css($hook) {
	global $post;
	if ( $post->post_type !== '3dvt-tour')
		return;
	
	wp_enqueue_style( 'tdvt_wp_admin_css', plugins_url('../css/3dvt.css', __FILE__) );
}
add_action('admin_enqueue_scripts', 'tdvt_load_admin_css');