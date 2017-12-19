<?php defined('ABSPATH') or die();

/**
 * Registers the 3D tour attachment metabox on 3dvt-tour post types.
 * @return void
 */
function tdvt_add_custom_meta_boxes() {
	global $post;
  add_meta_box(
      'wp_tour_attachment',
      '3DVT Tour',
      'tdvt_metabox_render',
      '3dvt-tour',
      'side'
  );
  $attachment = get_post_meta($post->ID, 'wp_custom_attachment', true);
  if ( $attachment ) {
	  add_meta_box(
	      'wp_tour_preview',
	      'Tour preview',
	      'tdvt_preview_render',
	      '3dvt-tour',
	      'normal'
	  );
	}
}
add_action('add_meta_boxes', 'tdvt_add_custom_meta_boxes');

/**
 * Renders the HTML buffer inside the meta box
 * @return void
 */
function tdvt_metabox_render() {
	global $post;

	wp_nonce_field(plugin_basename(__FILE__), 'wp_custom_attachment_nonce');
	$attachment = get_post_meta($post->ID, 'wp_custom_attachment', true);
	if (! $attachment ) {
	  echo '<p class="description">';
	  echo 'Upload your 3D tour here.';
	  echo '</p>';
	  echo '<input type="file" id="wp_custom_attachment" name="wp_custom_attachment" value="" size="25" />';
	} else {
		echo '<p class="description">';
		echo basename($attachment['file']);
		echo '</p>';
		echo sprintf('<a id="remove-tour" class="submitdelete deletion" href="#removetour" data-postid="%s">Remove tour</a>', htmlspecialchars($post->ID));

		echo '<p><label><strong>Shortcode</strong></label></p>';
		echo sprintf('<textarea id="tdvt-shortcode-text" readonly>[tdvt_tour id="%s"]</textarea>', $post->ID);
	}

}

/**
 * Verifies, saves, and processes uploaded tour files.
 * @param  int  $id The post ID
 * @return int
 */
function tdvt_save_custom_meta_data($id) {

  //Security verification.
  if(! wp_verify_nonce($_POST['wp_custom_attachment_nonce'], plugin_basename(__FILE__)))
    return $id;
  if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return $id;
	if(! current_user_can('edit_page', $id))
		return $id;

	if (! empty($_FILES['wp_custom_attachment']['name']))
		tdvt_process_files($id);
     
}
add_action('save_post', 'tdvt_save_custom_meta_data');

/**
 * Allows files to be uploaded on tdvt-tour posts
 * @return void
 */
function tdvt_update_edit_form() {
	echo ' enctype="multipart/form-data"';
}
add_action('post_edit_form_tag', 'tdvt_update_edit_form');

/**
 * Processes uploaded files and extracts them as necessary.
 * @param  int $id The post ID.
 * @return void
 */
function tdvt_process_files($id) {
	
	$supported_types = array('application/zip');
	$arr_file_type = wp_check_filetype(basename($_FILES['wp_custom_attachment']['name']));
	$uploaded_type = $arr_file_type['type'];

	if(in_array($uploaded_type, $supported_types)) {

		// Use the WordPress API to upload the file
		$upload = wp_upload_bits($_FILES['wp_custom_attachment']['name'], null, file_get_contents($_FILES['wp_custom_attachment']['tmp_name']));

		if(isset($upload['error']) && $upload['error'] != 0) {
			wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
		} else {
			
			if (! empty($upload['file']) && file_exists($upload['file'])) {

				//Before we do anything else, validate the archive.
				if (! tdvt_archive_is_valid($upload['file']))
					wp_die('There archive you uploaded was not a valid 3DVT tour project.');

				//Extract the archive to a directory.
				$dir = wp_upload_dir();
				$path = sprintf("%s/3dvt-tours/%s", $dir['basedir'], $id);
				
				//Delete anything already in there.
				if (file_exists($path) && is_dir($path))
					exec(sprintf("rm -rf %s", escapeshellarg($path)));

				//Recreate it.
				if (!wp_mkdir_p($path))
					wp_die('There was an error creating the directory ' . $path);

				//Extract it.
				if (! tdvt_extract_archive($upload['file'], $path))
					wp_die('There was an error extracting your tour to ' . $path);

				add_post_meta($id, 'wp_custom_attachment', $upload);
				update_post_meta($id, 'wp_custom_attachment', $upload);     

			} else {
				wp_die('There was an error uploading your file. Does the web server have write permissions for uploads?');
			}
		}
	} else {
		wp_die('Only ZIP archives can be added as a 3D tour.');
	}
}

/**
 * Tests whether a given ZIP archive is a valid 3DVT tour archive.
 * @param  string $fp The file path to the ZIP archive.
 * @return bool       If the archive is valid, true, otherwise false.
 */
function tdvt_archive_is_valid($fp) {
	$za = new ZipArchive();
	$za->open($fp);

	//Is there a data.js file?
	for( $i = 0; $i < $za->numFiles; $i++ ){ 
		$stat = $za->statIndex( $i ); 
    if (basename($stat['name']) == "data.js") {
    	$za->close();
    	return true;
    }
	}
	$za->close();
	return false;
}

/**
 * Extracts the given ZIP archive to the given destination directory.
 * @param  string $fp   The ZIP archive file path.
 * @param  string $dest The destination directory string.
 * @return bool         True if successfull, otherwise false.
 */
function tdvt_extract_archive($fp, $dest) {
	$zip = new ZipArchive();
	if (! $zip->open($fp))
		return false;
	$zip->extractTo($dest);
	$zip->close();

	if ( file_exists($dest . "/job-data/" . "data.js") )
		return true;
	return false;
}

/**
 * Renders the HTML inside the Tour preview meta box.
 * @return void
 */
function tdvt_preview_render() {
	global $post;
  $attachment = get_post_meta($post->ID, 'wp_custom_attachment', true);
  if (! $attachment )
  	return;

  echo tdvt_get_embed_tour_by_id($post->ID);
}