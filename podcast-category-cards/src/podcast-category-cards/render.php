<?php
$taxonomy = 'podcast-category'; // adjust if needed

$terms = get_terms([
	'taxonomy'   => $taxonomy,
	'hide_empty' => false,
	'meta_key'   => 'order',
	'orderby'    => 'meta_value_num',
	'order'      => 'ASC'
]);


// Remove parent term (if any) from the results
$terms = array_filter($terms, function ($term) {
	return $term->parent !== 0;
});

if (empty($terms) || is_wp_error($terms)) {
	echo '<p>No terms found.</p>';
	return;
}

echo '<div class="row row-cols-1 row-cols-md-3 g-4">';
foreach ($terms as $term) {
	echo '<div class="col">';
	echo '<div class="hta-card card h-100">';
	echo '<div class="card-body">';
	echo '<h3 class="card-title text-elc-white">' . esc_html($term->name) . '</h3>';
	echo '<p class="card-text">' . esc_html($term->description) . '</p>';
	echo '<a class="white-solid-pill">Coming Soon</a>';
	echo '</div></div></div>';
}
echo '</div>';
