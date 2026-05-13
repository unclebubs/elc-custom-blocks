/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */

/**
 * The save function is not used for dynamic blocks.
 * Dynamic rendering is handled by render.php.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 * @return {null} Returns null for dynamic blocks.
 */
export default function save () {
	return null
}
