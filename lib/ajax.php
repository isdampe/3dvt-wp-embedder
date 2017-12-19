<?php defined('ABSPATH') or die();

/**
 * Ajax handler for deleting tour files.
 * @return void
 */
function tdvt_remove_tour_files() {
	global $wpdb;
	
	if (empty($_POST['post_ID']))
		wp_die("No post_ID supplied.");

	$post_ID = stripslashes($_POST['post_ID']);

	//Extract the archive to a directory.
	$dir = wp_upload_dir();
	$path = sprintf("%s/3dvt-tours/%s", $dir['basedir'], $post_ID);
	
	//Delete the files.
	if (file_exists($path) && is_dir($path))
		exec(sprintf("rm -rf %s", escapeshellarg($path)));

	//Delete the archive.
	$attachment = get_post_meta($post_ID, 'wp_custom_attachment', true);
	if (! empty($attachment['file']))
		wp_delete_file($attachment['file']);

	//Delete the post meta.
	delete_post_meta($post_ID, 'wp_custom_attachment');

	wp_die();
}
add_action('wp_ajax_tdvt_remove_tour_files', 'tdvt_remove_tour_files');

/**
 * Injects JavaScript for ajax tour removal in post screen.
 * @return void
 */
function tdvt_remove_tour_files_js() { ?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {

		var removeTour = function(e) {
			if (typeof e !== 'undefined')
				e.preventDefault();

			if (! confirm('Are you sure you want to delete this tour and its files?'))
				return;

			jQuery('#remove-tour').attr('disabled', 'disabled');

			var data = {
				'action': 'tdvt_remove_tour_files',
				'post_ID': jQuery(this).attr('data-postid')
			};

			jQuery.post(ajaxurl, data, function(response) {
				window.location.reload();
			});

		};

		jQuery('#remove-tour').on('click', removeTour);

	});
	</script> <?php
}
add_action('admin_footer', 'tdvt_remove_tour_files_js');