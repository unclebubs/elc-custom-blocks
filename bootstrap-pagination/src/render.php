<?php

if (! function_exists('render_bootstrap_pagination')) {
	function render_bootstrap_pagination($attributes, $content)
	{
		global $wp_query;



		$html = '';
		// Bootstrap 5 pagination wrapper
		$pagination_links = paginate_links(array(
			'base'      => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
			'format'    => '/page/%#%',
			'current'   => max(1, get_query_var('paged')),
			'total'     => $wp_query->max_num_pages,
			'prev_text' => __('<i class="bi bi-arrow-left"></i>'),
			'next_text' => __('<i class="bi bi-arrow-right"></i>'),
			'type'      => 'array', // Generate an array of links
		));

		if ($pagination_links) {


			$html .= '<nav aria-label="Page navigation">';
			$html .= '<ul class="search-pagination">';

			foreach ($pagination_links as $link) {
				// Add Bootstrap-specific classes
				$class = strpos($link, 'current') !== false ? 'page-item active' : 'page-item';
				$link = preg_replace('/href="([^"]+)"/', 'href="$1#podcast-listings"', $link);


				$link = str_replace('page-numbers', 'page-link', $link);
				$html .= '<li class="' . $class . '">';
				$html .= $link; // Replace WP classes with Bootstrap classes
				$html .= '</li>';
			}

			$html .= '</ul>';
			$html .= '</nav>';
			return $html;
		}

		return 'no content';
	}
}

if (!defined('UNIT_TEST_ENV')) {
	echo render_bootstrap_pagination($attributes, $content);
}
