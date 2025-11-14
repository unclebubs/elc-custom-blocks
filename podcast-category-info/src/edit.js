import {
	useBlockProps,
	RichText,
	InspectorControls
} from '@wordpress/block-editor'
import { PanelBody, Spinner, Notice, Button } from '@wordpress/components'
import { __ } from '@wordpress/i18n'
import ServerSideRender from '@wordpress/server-side-render'
import { useState } from '@wordpress/element'

const Loading = () => <Spinner />
const ErrorPlaceholder = () => (
	<Notice status='error' isDismissible={false}>
		Preview error. This will still render on the front end.
	</Notice>
)
const EmptyPlaceholder = () => (
	<Notice status='info' isDismissible={false}>
		No content yet. Adjust the block settings to see a preview.
	</Notice>
)

const Edit = ({ attributes, setAttributes, name }) => {
	const blockProps = useBlockProps()
	const [refreshKey, setRefreshKey] = useState(0)

	const onChangeContent = newContent => {
		setAttributes({ userContent: newContent })
	}

	// Gate SSR until required attrs exist (customize as needed)
	const hasRequired = true // e.g., !!attributes.startYear || !!attributes.endYear

	if (!hasRequired) {
		return (
			<div {...blockProps}>
				<EmptyPlaceholder />
				<InspectorControls>
					<PanelBody title={__('Content Settings', 'podcast-category-info')}>
						<RichText
							tagName='div'
							value={attributes.userContent}
							onChange={onChangeContent}
							placeholder='Enter user content here...'
						/>
					</PanelBody>
				</InspectorControls>
			</div>
		)
	}

	return (
		<div {...blockProps}>
			<div style={{ marginBottom: 8 }}>
				<Button variant='secondary' onClick={() => setRefreshKey(k => k + 1)}>
					Retry preview
				</Button>
			</div>
			<ServerSideRender
				key={refreshKey}
				block={name || 'elc/podcast-category-info'}
				attributes={attributes}
				httpMethod='POST'
				LoadingResponsePlaceholder={Loading}
				ErrorResponsePlaceholder={ErrorPlaceholder}
				EmptyResponsePlaceholder={EmptyPlaceholder}
			/>

			<InspectorControls>
				<PanelBody title={__('Content Settings', 'podcast-category-info')}>
					<RichText
						tagName='div'
						value={attributes.userContent}
						onChange={onChangeContent}
						placeholder='Enter user content here...'
					/>
				</PanelBody>
			</InspectorControls>
		</div>
	)
}

export default Edit
