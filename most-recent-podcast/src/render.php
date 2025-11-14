<?php
// Fetch the most recent post in the 'podcast' category

use SebastianBergmann\CodeCoverage\Report\PHP;

$template_directory = defined('TEMPLATE_DIR') ? TEMPLATE_DIR : get_template_directory();
require_once $template_directory . '/includes/template-functions.php';

if (!function_exists('render_recent_podcast_block')) {

	function render_recent_podcast_block($attributes, $content)
	{
		// Fetch the parent category term ID
		$parent_term_id = 62; // Replace with your parent category term ID

		// Get all child categories (including subcategories of the parent)
		$child_terms = get_term_children($parent_term_id, 'podcast-category');

		// Include the parent category in the list of terms
		$all_terms = array_merge([$parent_term_id], is_array($child_terms) ? $child_terms : []);

		$query = new WP_Query(array(
			'tax_query'      => array(
				array(
					'taxonomy' => 'podcast-category',
					'field'    => 'term_id',
					'terms'    => $all_terms,
				),
			),
			'posts_per_page' => 1,
		));

		if ($query->have_posts()) {
			$query->the_post();

			// Assign variables
			$title   = get_the_title();
			$url     = get_permalink();
			$excerpt = get_the_excerpt();
			$episode_data = powerpress_get_enclosure_data(get_the_ID());
			$episode_number = $episode_data['episode_no'];
			$thumbnail = get_the_post_thumbnail(get_the_ID(), 'medium');
			$date = get_the_date('F j, Y');
			return (render_podcast_card(cardTitle: 'Most Recent Podcast', title: $title, excerpt: $excerpt, episode_number: $episode_number, thumbnail: $thumbnail, link: $url, date: $date));

			wp_reset_postdata();
		} else {
			return '<div class="alert alert-warning" role="alert">' . esc_html__('No recent podcast posts found.', 'mytheme') . '</div>';
		}
	}
}

if (!defined('UNIT_TEST_ENV')) {
	echo (render_recent_podcast_block($attributes, $content));
}
