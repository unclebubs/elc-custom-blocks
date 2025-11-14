<?php

function render_tab_navigation($attributes, $content)
{
	// Get all categories in the "paper_category" taxonomy
	$categories = get_terms([
		'taxonomy' => 'paper-category',
		'hide_empty' => false, // Include categories even if they have no posts
		'orderby' => 'parent', // Order by parent-child structure
		'order' => 'ASC',
		'fields' => 'all', // Ensure terms are returned as objects
	]);

	// Check if categories are retrieved successfully
	if (!is_wp_error($categories) && !empty($categories)) {
		// Get the current category (if on a category archive page)
		$current_category_slug = '';
		if (is_tax('paper-category')) {
			$current_category_slug = get_queried_object()->slug;
		}

		// Start rendering the nav-tabs bar
		ob_start();
?>
		<ul class="nav nav-tabs">
			<?php
			foreach ($categories as $category):
				// Ensure $category is an object
				if (is_object($category)) {
					// Calculate margin for child categories (simple indentation based on depth)
			?>
					<li class="nav-item">
						<a
							class="nav-link <?php echo ($current_category_slug === $category->slug) ? 'active' : ''; ?>"
							href="<?php echo esc_url(get_term_link($category)); ?>">
							<?php echo esc_html($category->name); ?>
						</a>
					</li>
			<?php
				}
			endforeach;
			?>
		</ul>
<?php
		return ob_get_clean();
	}
}
if (!defined('UNIT_TEST_ENV')) {
	echo render_tab_navigation($attributes, $content);
}
