<?php
$template_directory = defined('TEMPLATE_DIR') ? TEMPLATE_DIR : get_template_directory();
require_once $template_directory . '/includes/template-functions.php';

if (!function_exists('render_all_podcast_listings')) {


	function render_all_podcast_listings($attributes, $content)
	{
		global $wpdb;

		// Build the query arguments
		$query_args = array(
			'post_type'      => 'podcast',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'tax_query'      => array(
				'orderby'        => 'date', // Order by published date
				'order'          => 'DESC'
			),
		);


		// Execute the query
		$query = new WP_Query($query_args);
		$html = '';
		if (! $query->have_posts()) {
			$html .= '<div class="alert alert-warning" role="alert">' . esc_html__('No podcasts found in the specified subcategory.', 'mytheme') . '</div>';
		} else {
			$html .=  '<div id="podcast-listings" class="container">';
			$html .=  '<div class="row justify-content-start row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">';
		}
		while ($query->have_posts()) {
			$query->the_post();
			$post_id       = get_the_ID();
			$post_title = get_the_title();
			$post_url   = get_permalink();
			$post_excerpt  = get_the_excerpt($post_id);
			$date = get_the_date('F j, Y');
			$year = get_the_date('Y');
			$episode_data = powerpress_get_enclosure_data(get_the_ID());
			$episode_number = $episode_data['episode_no'];
			$post_thumbnail = get_the_post_thumbnail($post_id, 'medium', array('class' => 'podcast-thumbnail card-img-top'));
			$html .=  ('<div class="col item fade show">');
			$html .=  (render_podcast_card(title: $post_title, excerpt: $post_excerpt, episode_number: $episode_number, thumbnail: $post_thumbnail, link: $post_url, date: $date, year: $year));
			$html .=  ('</div>');
		}

		if ($query->have_posts()) {
			$html .=  ('</div>');
			$html .=  ('</div>');
		}
		wp_reset_postdata();
		return $html;
	}
}


if (!defined('UNIT_TEST_ENV')) {
	echo (render_all_podcast_listings($attributes, $content));
}
