import { useBlockProps, InspectorControls } from '@wordpress/block-editor'
import {
	ToggleControl,
	PanelBody,
	Spinner,
	Notice,
	Button
} from '@wordpress/components'
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

export default function Edit ({ attributes, setAttributes, name }) {
	const { showRegisterButton, showWatchNowButton } = attributes
	const blockProps = useBlockProps()
	const [refreshKey, setRefreshKey] = useState(0)

	// Gate SSR until required attrs exist (customize as needed)
	const hasRequired = true // e.g., !!attributes.startYear || !!attributes.endYear

	if (!hasRequired) {
		return (
			<div {...blockProps}>
				<EmptyPlaceholder />
				<InspectorControls>
					<PanelBody title={__('Button Visibility', 'journal-club-header')}>
						<ToggleControl
							label={__('Show Register Button', 'journal-club-header')}
							checked={showRegisterButton}
							onChange={value => setAttributes({ showRegisterButton: value })}
						/>
						<ToggleControl
							label={__('Show Watch Now Button', 'journal-club-header')}
							checked={showWatchNowButton}
							onChange={value => setAttributes({ showWatchNowButton: value })}
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
				block={name || 'elc/journal-club-header'}
				attributes={attributes}
				httpMethod='POST'
				LoadingResponsePlaceholder={Loading}
				ErrorResponsePlaceholder={ErrorPlaceholder}
				EmptyResponsePlaceholder={EmptyPlaceholder}
			/>

			<InspectorControls>
				<PanelBody title={__('Button Visibility', 'journal-club-header')}>
					<ToggleControl
						label={__('Show Register Button', 'journal-club-header')}
						checked={showRegisterButton}
						onChange={value => setAttributes({ showRegisterButton: value })}
					/>
					<ToggleControl
						label={__('Show Watch Now Button', 'journal-club-header')}
						checked={showWatchNowButton}
						onChange={value => setAttributes({ showWatchNowButton: value })}
					/>
				</PanelBody>
			</InspectorControls>
		</div>
	)
}
