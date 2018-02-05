<?php
/**
 * Plugin Name: Breadcrumb Trail by Path
 * Description: Build a breadcrumb list by walking through the URL path
 * Version:     0.2.0
 */
require_once( 'inc/Generator.php' );

/**
 * Shows a breadcrumb for all types of pages.  This is a wrapper function for the Breadcrumb_Trail_By_Path class,
 * which should be used in theme templates.
 *
 * @since  0.1.0
 * @access public
 * @param  array $args
 * @return void
 */
function breadcrumb_trail_by_path( $path = null ) {
	$btp = new \Newcity\BTP\Generator();

	if (is_null($path)) {
		$path = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
	}

	// remove the link for the item matching the path
	add_filter('breadcrumb_trail_by_path_items', function($items) use ($path) {
		foreach ($items as $i => $item) {
			if (untrailingslashit($item['url']) == untrailingslashit($path)) {
				$items[$i]['url'] = FALSE;
			}
		}
		return $items;
	});

	// a list of objects
	$items = $btp->getItems($path);
	$items = apply_filters('breadcrumb_trail_by_path_items', $items);

	// html for each item
	$trail = $btp->getTrail($items);
	$trail = apply_filters('breadcrumb_trail_by_path_trail', $trail);

	// list
	$output = $btp->getBreadcrumbs($trail);
	return apply_filters('breadcrumb_trail_by_path_breadcrumbs', $output);

	return $output;
}