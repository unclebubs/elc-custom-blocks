import ServerSideRender from '@wordpress/server-side-render'
import { useBlockProps } from '@wordpress/block-editor'
import { Spinner, Notice, Button } from '@wordpress/components'
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

export default function Edit ({ attributes, name }) {
	const blockProps = useBlockProps()
	const [refreshKey, setRefreshKey] = useState(0)

	// Gate SSR until required attrs exist (customize as needed)
	const hasRequired = true // e.g., !!attributes.category || !!attributes.perPage

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
				block={name || 'elc/journal-club-listings'}
				attributes={attributes}
				httpMethod='POST'
				LoadingResponsePlaceholder={Loading}
				ErrorResponsePlaceholder={ErrorPlaceholder}
				EmptyResponsePlaceholder={EmptyPlaceholder}
			/>
		</div>
	)
}
