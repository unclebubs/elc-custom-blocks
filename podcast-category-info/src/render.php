<?php
$template_directory = defined('TEMPLATE_DIR') ? TEMPLATE_DIR : get_template_directory();
require_once $template_directory . '/includes/template-functions.php';

if (!function_exists('render')) {
	function render($attributes, $content)
	{
		// Get the current term object
		$term = get_queried_object();
		if (!is_wp_error($term)) {
			$user_content = isset($attributes['userContent']) ? $attributes['userContent'] : '';
			// Check if the current category is the parent "Podcasts"
			if ($term->parent === 0 && strtolower($term->slug) === 'podcast') {
				// Get the default child category
				$child_categories = get_terms([
					'taxonomy'   => $term->taxonomy,
					'parent'     => $term->term_id,
					'meta_key'   => 'order', // The ACF field key
					'orderby'    => 'meta_value_num', // Use 'meta_value_num' for numeric values or 'meta_value' for strings
					'order'      => 'ASC',  // Order direction
					'hide_empty' => true,   // Only show categories with posts
				]);

				if (!empty($child_categories)) {
					// Use the first child category as default
					$term = $child_categories[0];
				} else {
					// No child categories found, display a fallback message
					$html = '<div class="alert alert-warning" role="alert">' . esc_html__('No child categories available.', 'mytheme') . '</div>';
					return $html;
				}
			}

			// Retrieve term data
			$title = $term->name; // Term name 
			$description = term_description($term->term_id, $term->taxonomy); // Term description
			$acf_image = get_field('image', $term);
			$img = wp_get_attachment_image($acf_image, 'medium');
			$html = "<div class=''>";
			// $html .= $img;
			$html .= "<div class='card-body'>";
			$html .= wp_kses_post($user_content);
			$html .= "</p>";
			if (isset($title) && !empty($title)) {
				$html .= "<h2 class='card-title text-white'>" . $title . "</h2>";
			}

			if (isset($description) && !empty($description)) {
				$html .= "<div class='mt-3 mb-4 card-text text-white'>" . $description . "</div>";
			}

			$html .= '<a 
				class="btn black-solid-pill "
				>
				Coming soon
				<i class="bi-arrow-down-circle-fill ms-3 align-middle d-none" style="font-size: 12px;"></i>
			</a>';
			$html .= "</div></div>";
			return $html;
		} else {
			$html = '<div class="alert alert-warning" role="alert">' . esc_html__('No child categories available.', 'mytheme') . '</div>';
			return $html;
		}
	}
}

if (!defined('UNIT_TEST_ENV')) {
	echo (render($attributes, $content));
}
