<?php
function create_block_elc_media_gallery_block_init()
{
	// Load the asset file for dependencies and version
	$asset_file = include(__DIR__ . '/build/elc-media-gallery/view.asset.php');

	// Generate the script handle that WordPress would use
	$script_handle = 'create-block-elc-media-gallery-view-script';

	// Register the view script with jQuery dependency BEFORE registering the block
	wp_register_script(
		$script_handle,
		plugins_url('build/elc-media-gallery/view.js', __FILE__),
		array_merge($asset_file['dependencies'], array('jquery')), // Add jQuery to dependencies
		$asset_file['version'],
		true // Load in footer (simple boolean, no defer strategy)
	);

	// Register the block type and tell it to use our pre-registered script
	register_block_type(__DIR__ . '/build/elc-media-gallery', array(
		'view_script_handles' => array($script_handle)
	));
}
add_action('init', 'create_block_elc_media_gallery_block_init');
