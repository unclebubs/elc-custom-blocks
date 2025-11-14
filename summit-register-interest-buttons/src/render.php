<?php

if (!function_exists('render_buttons')) {
	function render_buttons($attributes, $content)
	{
		/**
		 * Render callback for the custom block.
		 */

		// Ensure global access to the WordPress database
		global $wpdb;

		// Get the block attributes
		$event_id = get_the_ID();


		// Check if the user is logged in
		if (!is_user_logged_in()) {
			// User not logged in: Show the Login button
			$login_url = wp_login_url();
			$current_url = home_url(add_query_arg(null, null));
			$redirect_url = add_query_arg('redirect_to', $current_url, $login_url);
			$redirect_url = add_query_arg('show_success', true, $redirect_url);
			return '<a href="' . $redirect_url . '" class="btn">Login</a>';
		} else {

			// Get the current user ID
			$user_id = get_current_user_id();

			// Query the custom table to check if the user has registered interest
			$table_name = $wpdb->prefix . 'event_registrations';
			$is_registered = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND event_id = %d",
					$user_id,
					$event_id
				)
			);

			// Buttons for logged-in users
			$html = '';
			// Add the "Register Interest" button for logged-in users who haven't registered
			if (!$is_registered && !current_user_can('administrator')) {
				$html .= '<a href="#" class="btn register-interest" data-event-id="' . esc_attr($event_id) . '">Register Interest</a>';
			}

			// Add the "Read More" button for logged-in users who have registered
			if ($is_registered || current_user_can('administrator')) {
				$html .= '<a href="#summit-announcements" class="btn">
									Read More
								</a>';
			}

			return $html;
		}
	}
}
if (!defined('UNIT_TEST_ENV')) {
	echo render_buttons($attributes, $content);
}
