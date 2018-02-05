<?php
/**
 * Breadcrumb Trail By Path - A breadcrumb menu script for WordPress.
 *
 * 
 */

/**
 * Creates a breadcrumbs menu for the site based on the current page that's being viewed by the user.
 *
 * @since  0.6.0
 * @access public
 */

namespace Newcity\BTP;

class Generator {

	/**
	 * get a list of objects by splitting out the path
	 *
	 * @param string $path
	 * @return void
	 */
	public function getItems($path) {
		$url = parse_url($path);
		$path = untrailingslashit($url['path']);
		$items = [];

		while ($path != '') {
			$url = home_url($path);
			$id = url_to_postid($url);
			$item = null;

			if ($id > 0) {
				$post = get_post($id);
				$item = [
					'title' => $post->post_title,
					'url' => $url,
				];
			}

			// try a page
			if (is_null($item)) {
				$name = basename($url);
				$page = get_page_by_path($name);
				if (is_object($page)) {
					$item = [
						'title' => $page->post_title,
						'url' => $url,
					];
				}
			}

			// try a page with parent
			if (is_null($item)) {
				$name = basename($url);
				$parent_url = dirname($url);
				$parent_name = basename($parent_url);

				$page = get_page_by_path($parent_name . '/' . $name);
				if (is_object($page)) {
					$item = [
						'title' => $page->post_title,
						'url' => $url,
					];
				}
			}

			// try by the post name
			if (is_null($item)) {
				$posts = get_posts([
					'name' => basename($url),
					'post_type' => 'any',
					'post_status' => 'publish',
					'posts_per_page' => 1,
				]);
				if ($posts) {
					$item = [
						'title' => $posts[0]->post_title,
						'url' => $url,
					];
				}
			}

			// finally
			if (is_array($item)) {
				$items[] = $item;
			}

			// iterate
			$path = untrailingslashit(dirname($path));
		}

		$items[] = [
			'title' => 'Home',
			'url' => home_url('/'),
		];

		return array_reverse($items);
	}

	/**
	 * get a list of links based on the items
	 *
	 * @param [type] $items
	 * @return void
	 */
	public function getTrail($items) {
		$trail = [];
		$n = count($items);
		foreach ($items as $i => $item) {
			if ($item['url']) {
				$trail[] = '<a href="' . $item['url'] . '">' . $item['title'] . '</a>';
			}
			else {
				$trail[] = '<span>' . $item['title'] . '</span>';
			}
		}
		return $trail;
	}

	/**
	 * final markup for the breadcrumb list
	 *
	 * @param [type] $trail
	 * @return void
	 */
	public function getBreadcrumbs($trail) {
		$output = '';
		foreach ($trail as $item) {
			$output .= '<li>' . $item . '</li>';
		}
		return '<nav role="navigation" aria-label="Breadcrumbs" class="breadcrumb-trail breadcrumbs" itemprop="breadcrumb"><h2 class="trail-browse">Browse:</h2><ul>' . $output . '</ul></nav>';
	}
}
