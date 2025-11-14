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
import ServerSideRender from '@wordpress/server-side-render'
import { useBlockProps } from '@wordpress/block-editor'
import { Spinner, Notice, Button } from '@wordpress/components'
import { useState } from '@wordpress/element'

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss'

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

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit ({ attributes, name }) {
	const blockProps = useBlockProps()
	const [refreshKey, setRefreshKey] = useState(0)

	// Gate SSR until required attrs exist (customize as needed)
	const hasRequired = true // e.g., !!attributes.startYear || !!attributes.endYear

	if (!hasRequired) {
		return (
			<div {...blockProps}>
				<EmptyPlaceholder />
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
				block={name || 'elc/account-login'}
				attributes={attributes}
				httpMethod='POST'
				LoadingResponsePlaceholder={Loading}
				ErrorResponsePlaceholder={ErrorPlaceholder}
				EmptyResponsePlaceholder={EmptyPlaceholder}
			/>
		</div>
	)
}
