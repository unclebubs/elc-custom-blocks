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
import { useBlockProps, InspectorControls } from '@wordpress/block-editor'
import { PanelBody, SelectControl, Notice, Button } from '@wordpress/components'
import ServerSideRender from '@wordpress/server-side-render'
import { useSelect } from '@wordpress/data'
import { useState } from '@wordpress/element'

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss'

const Loading = () => <div>Loading preview...</div>
const ErrorPlaceholder = () => (
	<Notice status='error' isDismissible={false}>
		Preview error. This will still render on the front end.
	</Notice>
)
const EmptyPlaceholder = () => (
	<Notice status='info' isDismissible={false}>
		Select a category to display news articles.
	</Notice>
)

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit ({ attributes, setAttributes, name }) {
	const { categoryId } = attributes
	const [refreshKey, setRefreshKey] = useState(0)

	// Fetch categories from WordPress
	const categories = useSelect(select => {
		return select('core').getEntityRecords('taxonomy', 'category', {
			per_page: -1,
			orderby: 'name',
			order: 'asc'
		})
	}, [])

	const blockProps = useBlockProps()

	// Prepare category options for SelectControl
	const categoryOptions = [
		{ label: __('Select a category', 'news-carousel'), value: 0 }
	]

	if (categories) {
		categories.forEach(category => {
			categoryOptions.push({
				label: category.name,
				value: category.id
			})
		})
	}

	const hasRequired = categoryId > 0

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Settings', 'news-carousel')} initialOpen={true}>
					<SelectControl
						label={__('Select Category', 'news-carousel')}
						value={categoryId}
						options={categoryOptions}
						onChange={value => setAttributes({ categoryId: parseInt(value) })}
						help={__(
							'Choose which category of news articles to display in the carousel.',
							'news-carousel'
						)}
					/>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				{!hasRequired ? (
					<EmptyPlaceholder />
				) : (
					<>
						<div style={{ marginBottom: 8 }}>
							<Button
								variant='secondary'
								onClick={() => setRefreshKey(k => k + 1)}
							>
								{__('Retry preview', 'news-carousel')}
							</Button>
						</div>
						<ServerSideRender
							key={refreshKey}
							block={name || 'create-block/news-carousel'}
							attributes={attributes}
							httpMethod='POST'
							LoadingResponsePlaceholder={Loading}
							ErrorResponsePlaceholder={ErrorPlaceholder}
							EmptyResponsePlaceholder={EmptyPlaceholder}
						/>
					</>
				)}
			</div>
		</>
	)
}
