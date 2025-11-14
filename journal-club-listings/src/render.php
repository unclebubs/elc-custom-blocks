<?php


function render_listings($attributes, $content)
{
	global $wpdb;


	// Build the query arguments
	$query_args = array(
		'post_type'      => 'journal-club',
		'posts_per_page' => -1, // Get all posts
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'     => 'start_date',
				'compare' => 'EXISTS',
				'type'    => 'DATE',
			),
			array(
				'key'     => 'start_time',
				'compare' => 'EXISTS',
				'type'    => 'TIME',
			),
		),
		'orderby' => array(
			'start_date' => 'ASC', // Order by start date first
			'start_time' => 'ASC', // Then by start time
		),
	);

	// Execute the query
	$query = new WP_Query($query_args);

	if (! $query->have_posts()) {
		return '<div class="alert alert-warning" role="alert">' . esc_html__('No Journals found in the specified subcategory.', 'mytheme') . '</div>';
	} else {
		$html =  '<div id="journal-listings" class="container journal-listing-container slick-slider">';

		while ($query->have_posts()) {
			$query->the_post();
			$post_id       = get_the_ID();
			$post_title = get_the_title();
			$post_url   = get_permalink();
			$post_excerpt  = get_the_excerpt($post_id);
			$date = get_field('start_date');
			$date_object = DateTime::createFromFormat('Ymd', $date);
			$year = '';
			if ($date) {
				$year = $date_object->format('Y'); // Extract the year (e.g., 2024)
			}
			$month = '';
			if ($date) {
				$month = $date_object->format('F'); // Extract the year (e.g., 2024)
			}
			$post_thumbnail = get_the_post_thumbnail($post_id, 'medium', array('class' => 'podcast-thumbnail'));

			$html .= '<div>';
			$html .= ("<div class='journal-card card' data-year='" . $year . "'>");
			$html .= $post_thumbnail;
			$html .= ("<div class='card-body'>");
			if (isset($post_url) && !empty($post_url)) {
				$html .= ("<a href='" . esc_url($post_url) . "' class='offset-btn'>");
				$html .= ("<i class='bi bi-play-fill'></i>");
				$html .= ("</a>");
			}
			if (isset($date) && !empty($date)) {
				$html .= '<h2>' . $month . '</h2>';
			}
			if (isset($post_excerpt) && !empty($post_excerpt)) {
				$html .= ("<p class='card-text'>" . $post_excerpt . "</p>");
			}
			if (isset($post_url) && !empty($post_url)) {
				$html .= ("<a href='" . $post_url . "#more' class='papers-btn'>Papers Here</a>");
			}

			$html .= ('</div>');
			$html .= ('</div>');
			$html .= ('</div>');
		}
		$html .= ('</div>');
		wp_reset_postdata();
		return $html;
	}
}

if (!defined('UNIT_TEST_ENV')) {
	echo render_listings($attributes, $content);
}
