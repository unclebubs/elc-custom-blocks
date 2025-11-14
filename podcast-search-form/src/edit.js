import { useBlockProps } from '@wordpress/block-editor'
import { useEffect, useState } from '@wordpress/element'
import { Spinner, Notice, Button } from '@wordpress/components'
import ServerSideRender from '@wordpress/server-side-render'
import { __ } from '@wordpress/i18n'

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

const Edit = ({ attributes, name }) => {
	const blockProps = useBlockProps()
	const [subcategories, setSubcategories] = useState([])
	const [loading, setLoading] = useState(true)
	const [refreshKey, setRefreshKey] = useState(0)

	useEffect(() => {
		// Fetch subcategories from the REST API
		fetch(`${window.wpApiSettings.root}mytheme/v1/subcategories?parent=62`)
			.then(response => {
				if (!response.ok) {
					throw new Error('Failed to fetch subcategories.')
				}
				return response.json()
			})
			.then(data => {
				setSubcategories(data)
				setLoading(false)
			})
			.catch(error => {
				console.error('Error fetching subcategories:', error)
				setLoading(false)
			})
	}, [])

	// Gate SSR until required attrs exist (customize as needed)
	const hasRequired = true // e.g., !!attributes.startYear || !!attributes.endYear

	if (loading) {
		return (
			<div {...blockProps}>
				<Loading />
				<p>{__('Loading subcategories...', 'mytheme')}</p>
			</div>
		)
	}

	if (!hasRequired || subcategories.length === 0) {
		return (
			<div {...blockProps}>
				<EmptyPlaceholder />
				<p>{__('No subcategories found.', 'mytheme')}</p>
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
				block={name || 'elc/podcast-search-form'}
				attributes={attributes}
				httpMethod='POST'
				LoadingResponsePlaceholder={Loading}
				ErrorResponsePlaceholder={ErrorPlaceholder}
				EmptyResponsePlaceholder={EmptyPlaceholder}
			/>
		</div>
	)
}

export default Edit
