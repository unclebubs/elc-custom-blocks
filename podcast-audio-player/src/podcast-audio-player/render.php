<?php

/**
 * Dynamic render for Audio-only block (PowerPress enclosure -> WP audio player).
 */

if (! defined('ABSPATH')) {
	exit;
}

$post_id = get_the_ID();
if (! $post_id) {
	return '';
}

// Feed slug: change if yours isn't 'podcast'
$feed_slug = 'podcast';

$mp3_url = '';
if (function_exists('powerpress_get_enclosure_data')) {
	$enclosure = powerpress_get_enclosure_data($post_id, $feed_slug);
	if (is_array($enclosure) && ! empty($enclosure['url'])) {
		$mp3_url = esc_url($enclosure['url']);
	}
}


$wrapper_attributes = get_block_wrapper_attributes(
	[
		'class' => 'elc-audio-player',
	]
);

ob_start();
?>
<div <?php echo $wrapper_attributes; ?>>
	<audio controls preload="metadata" class="w-100">
		<source src="<?php echo $mp3_url; ?>" type="audio/mpeg">
	</audio>
</div>
<?php
echo ob_get_clean();
