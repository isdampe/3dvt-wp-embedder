<?php defined('ABSPATH') or die();

/**
 * Renders an embedded tour if it exists
 * @param  array $atts Array of shortcode attributes
 * @return string      The HTML string buffer.
 */
function tdvt_shortcode_embed($atts){
	if (empty($atts['id']))
		return "No tour ID specified";
	$post_ID = $atts['id'];
	$attachment = get_post_meta($post_ID, 'wp_custom_attachment', true);
	if (! $attachment )
		return "The tour ID specified could not be found.";

	return tdvt_get_embed_tour_by_id($post_ID);
}
add_shortcode('tdvt_tour', 'tdvt_shortcode_embed');