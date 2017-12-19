<?php defined('ABSPATH') or die();

/**
 * Generates a HTML string buffer representing an iframe that
 * embeds a 3DVT tour by a given tour_id.
 * @param  int $tour_id The tour ID
 * @return string           The HTML string buffer.
 */
function tdvt_get_embed_tour_by_id($tour_id = null) {
	if ( $tour_id == null )
		return "";

	$uri = sprintf("%s/3dvt-tours/%s/index.html", content_url('uploads'), $tour_id);
	return sprintf('<iframe style="border-style:none;" src="%s" class="tdvt-tour"></iframe>', $uri);
}