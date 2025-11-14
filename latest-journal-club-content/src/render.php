<?php

function render_latest_journal_club($attributes, $content)
{
	// Current date in Ymd format
	$current_date = date('Ymd');

	// Query for the next upcoming journal-club post
	$args_upcoming = array(
		'post_type'      => 'journal-club',
		'posts_per_page' => 1,
		'meta_key'       => 'start_date',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
		'meta_query'     => array(
			array(
				'key'     => 'start_date',
				'value'   => $current_date, // Today's date in Ymd format
				'compare' => '>=',
				'type'    => 'DATE', // Ensures proper date comparison
			),
		),
	);

	$query_upcoming = new WP_Query($args_upcoming);

	// Check if upcoming posts exist
	if ($query_upcoming->have_posts()) {
		$active_query = $query_upcoming;
	} else {

		// Query the most recent past post
		$args_past = array(
			'post_type'      => 'journal-club',
			'posts_per_page' => 1,
			'meta_key'       => 'start_date',
			'orderby'        => 'meta_value',
			'order'          => 'DESC',
			'meta_query'     => array(
				array(
					'key'     => 'start_date',
					'value'   => $current_date, // Today's date in Ymd format
					'compare' => '<',
					'type'    => 'DATE', // Ensures proper date comparison
				),
			),
		);

		$query_past = new WP_Query($args_past);
		$active_query = $query_past; // Assign the past query to the same object
	}

	// Extract variables from the chosen query
	if ($active_query->have_posts()) {
		while ($active_query->have_posts()) {
			$active_query->the_post();

			// Debugging the fetched post ID

			// Fetch and filter content
			$content = get_the_content(); // Get the raw content
			$filtered_content = apply_filters('the_content', $content); // Apply filters

			// Output content
			return $filtered_content;
		}
		wp_reset_postdata(); // Clean up the query
	} else {
		// No posts found
		return '<h1>No content</h1>';
	}
}

if (!defined('UNIT_TEST_ENV')) {
	echo render_latest_journal_club($attributes, $content);
}
