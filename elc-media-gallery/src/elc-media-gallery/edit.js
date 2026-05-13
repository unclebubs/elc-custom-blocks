/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n'

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import {
	useBlockProps,
	MediaUpload,
	MediaUploadCheck
} from '@wordpress/block-editor'
import { Button } from '@wordpress/components'
import ServerSideRender from '@wordpress/server-side-render'

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss'

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit ({ attributes, setAttributes }) {
	const { imageIds } = attributes

	const onSelectImages = media => {
		const selectedImageIds = media.map(item => item.id)
		setAttributes({ imageIds: selectedImageIds })
	}

	const removeImage = index => {
		const newImageIds = [...imageIds]
		newImageIds.splice(index, 1)
		setAttributes({ imageIds: newImageIds })
	}

	return (
		<div {...useBlockProps()}>
			<div className='elc-media-gallery-editor'>
				<MediaUploadCheck>
					<MediaUpload
						onSelect={onSelectImages}
						allowedTypes={['image']}
						multiple={true}
						gallery={true}
						value={imageIds}
						render={({ open }) => (
							<Button onClick={open} variant='primary' className='mb-3'>
								{imageIds.length > 0
									? __('Edit Gallery', 'elc-media-gallery')
									: __('Add Images', 'elc-media-gallery')}
							</Button>
						)}
					/>
				</MediaUploadCheck>

				{imageIds.length === 0 && (
					<p className='text-muted'>
						{__(
							'No images selected. Click "Add Images" to get started.',
							'elc-media-gallery'
						)}
					</p>
				)}

				{imageIds.length > 0 && (
					<div className='elc-media-gallery-preview'>
						<ServerSideRender
							block='create-block/elc-media-gallery'
							attributes={attributes}
						/>
						<p className='text-muted mt-3'>
							{__(`${imageIds.length} image(s) selected`, 'elc-media-gallery')}
						</p>
					</div>
				)}
			</div>
		</div>
	)
}
