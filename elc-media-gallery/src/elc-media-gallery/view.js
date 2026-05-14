/**
 * Frontend JavaScript for ELC Media Gallery
 * Handles Bootstrap modal interactions for image viewing
 */

/* global jQuery */

var $ = jQuery.noConflict()

/**
 * Initialize the media gallery
 */
function initMediaGallery () {
	// Track the card that opened the modal for focus management
	let lastFocusedCard = null
	let currentImageIndex = 0
	let images = []

	/**
	 * Populate modal with image data by index
	 */
	function populateModalByIndex ($modal, index) {
		if (!images || images.length === 0) return

		currentImageIndex = index
		const image = images[index]

		const $image = $modal.find('.elc-modal-image')
		const $sessionTitle = $modal.find('.elc-modal-session-title')
		const $speakers = $modal.find('.elc-modal-speakers')
		const $location = $modal.find('.elc-modal-location')
		const $date = $modal.find('.elc-modal-date')
		const $loader = $modal.find('.elc-image-loader')
		const $modalBody = $modal.find('.modal-body')
		const $prevBtn = $modal.find('.elc-nav-prev')
		const $nextBtn = $modal.find('.elc-nav-next')
		const $fullscreenBtn = $modal.find('.elc-fullscreen-btn')

		// Lock the current height to prevent jumping
		const currentHeight = $modalBody.outerHeight()
		$modalBody.css('height', currentHeight + 'px')

		// Fade out current content
		$image.css('opacity', '0')
		$sessionTitle.css('opacity', '0')
		$speakers.css('opacity', '0')
		$location.css('opacity', '0')
		$date.css('opacity', '0')

		// Hide navigation buttons and show loader
		$prevBtn.fadeOut(200)
		$nextBtn.fadeOut(200)
		$fullscreenBtn.fadeOut(200)
		$loader.fadeIn(200)

		// Wait a moment for fade out, then start preloading
		setTimeout(function () {
			// Create new image object to preload
			const imgElement = new Image()

			imgElement.onload = function () {
				// Update image
				$image.attr('src', image.large).attr('alt', image.alt)

				// Update session title
				if (image.session_title) {
					$sessionTitle.text(image.session_title).show()
				} else {
					$sessionTitle.empty().hide()
				}

				// Update speakers
				if (image.speakers) {
					$speakers.html('<strong>Speakers:</strong> ' + image.speakers).show()
				} else {
					$speakers.empty().hide()
				}

				// Update location
				if (image.location) {
					$location.html('<strong>Location:</strong> ' + image.location).show()
				} else {
					$location.empty().hide()
				}

				// Update date
				if (image.date) {
					$date.html('<strong>Date:</strong> ' + image.date).show()
				} else {
					$date.empty().hide()
				}

				// Function to handle height transition
				const doHeightTransition = function () {
					// Measure the new natural height
					$modalBody.css('height', 'auto')
					// Force reflow to ensure browser calculates the new auto height
					$modalBody[0].offsetHeight
					const newHeight = $modalBody.outerHeight()
					$modalBody.css('height', currentHeight + 'px')

					// Force a reflow to ensure the browser registers the initial height
					// This is necessary for the CSS transition to work
					$modalBody[0].offsetHeight

					// Animate to new height
					$modalBody.css('height', newHeight + 'px')

					// Hide loader and fade in new content
					$loader.fadeOut(200)
					$prevBtn.fadeIn(200)
					$nextBtn.fadeIn(200)
					$fullscreenBtn.fadeIn(200)
					setTimeout(function () {
						$image.css('opacity', '1')
						$sessionTitle.css('opacity', '1')
						$speakers.css('opacity', '1')
						$location.css('opacity', '1')
						$date.css('opacity', '1')
					}, 50)
				}

				// Wait for the DOM image to actually render the new dimensions
				if ($image[0].complete) {
					// Image is already loaded (cached), proceed immediately
					setTimeout(doHeightTransition, 10)
				} else {
					// Wait for image to load
					$image.one('load', doHeightTransition)
				}
			}

			imgElement.onerror = function () {
				// Hide loader on error and restore auto height
				$loader.fadeOut(200)
				$prevBtn.fadeIn(200)
				$nextBtn.fadeIn(200)
				$fullscreenBtn.fadeIn(200)
				$modalBody.css('height', 'auto')
				console.error('Failed to load image:', image.large)
			}

			// Start loading the image
			imgElement.src = image.large
		}, 150)

		// Update navigation buttons state
		updateNavigationButtons($modal)
	}

	/**
	 * Update navigation buttons (disable/enable based on position)
	 */
	function updateNavigationButtons ($modal) {
		const $prevBtn = $modal.find('.elc-nav-prev')
		const $nextBtn = $modal.find('.elc-nav-next')

		// Disable prev button if at start
		if (currentImageIndex === 0) {
			$prevBtn.prop('disabled', true).css('opacity', '0.3')
		} else {
			$prevBtn.prop('disabled', false).css('opacity', '0.9')
		}

		// Disable next button if at end
		if (currentImageIndex === images.length - 1) {
			$nextBtn.prop('disabled', true).css('opacity', '0.3')
		} else {
			$nextBtn.prop('disabled', false).css('opacity', '0.9')
		}

		// Hide navigation if only one image
		if (images.length <= 1) {
			$prevBtn.hide()
			$nextBtn.hide()
		} else {
			$prevBtn.show()
			$nextBtn.show()
		}
	}

	/**
	 * Open modal and track focus
	 */
	function openModal ($card) {
		lastFocusedCard = $card[0]

		const modalId = $card.data('bs-target')
		const $modal = $(modalId)
		const imageIndex = $card.data('image-index')

		// Load images array from modal data
		try {
			images = JSON.parse($modal.attr('data-images'))
		} catch (e) {
			console.error('Failed to parse images data:', e)
			images = []
		}

		// Set initial square aspect ratio for modal-body
		const $modalBody = $modal.find('.modal-body')
		const modalWidth = $modal.find('.modal-dialog').width()
		if (modalWidth) {
			$modalBody.css('height', modalWidth + 'px')
		}

		// Populate modal with the selected image
		populateModalByIndex($modal, imageIndex)

		const modalElement = document.querySelector(modalId)
		if (modalElement && window.bootstrap) {
			// Get or create Bootstrap modal instance
			let modal = bootstrap.Modal.getInstance(modalElement)
			if (!modal) {
				modal = new bootstrap.Modal(modalElement)
			}
			modal.show()
		}
	}

	// Handle card clicks to populate modal
	$('.elc-gallery-card').on('click', function () {
		openModal($(this))
	})

	// Handle keyboard navigation (Enter and Space keys)
	$('.elc-gallery-card').on('keydown', function (e) {
		// Enter key or Space key
		if (e.key === 'Enter' || e.key === ' ') {
			e.preventDefault()
			openModal($(this))
		}
	})

	// Handle previous button click
	$('.elc-nav-prev').on('click', function (e) {
		e.stopPropagation()
		const $modal = $(this).closest('.modal')
		if (currentImageIndex > 0) {
			populateModalByIndex($modal, currentImageIndex - 1)
		}
	})

	// Handle next button click
	$('.elc-nav-next').on('click', function (e) {
		e.stopPropagation()
		const $modal = $(this).closest('.modal')
		if (currentImageIndex < images.length - 1) {
			populateModalByIndex($modal, currentImageIndex + 1)
		}
	})

	// Handle fullscreen button click
	$('.elc-fullscreen-btn').on('click', function (e) {
		e.stopPropagation()
		const $modal = $(this).closest('.modal')
		const imageElement = $modal.find('.elc-modal-image')[0]

		if (!document.fullscreenElement) {
			// Enter fullscreen
			if (imageElement.requestFullscreen) {
				imageElement.requestFullscreen()
			} else if (imageElement.webkitRequestFullscreen) {
				imageElement.webkitRequestFullscreen()
			} else if (imageElement.msRequestFullscreen) {
				imageElement.msRequestFullscreen()
			}
		} else {
			// Exit fullscreen
			if (document.exitFullscreen) {
				document.exitFullscreen()
			} else if (document.webkitExitFullscreen) {
				document.webkitExitFullscreen()
			} else if (document.msExitFullscreen) {
				document.msExitFullscreen()
			}
		}
	})

	// Handle keyboard arrows in modal and trap focus
	$('.modal').on('keydown', function (e) {
		if ($(this).hasClass('show')) {
			// Handle arrow key navigation
			if (e.key === 'ArrowLeft') {
				e.preventDefault()
				$(this).find('.elc-nav-prev').click()
			} else if (e.key === 'ArrowRight') {
				e.preventDefault()
				$(this).find('.elc-nav-next').click()
			}

			// Handle focus trapping with Tab key
			if (e.key === 'Tab') {
				const $modal = $(this)
				const focusableElements = $modal.find(
					'button:visible, [href]:visible, input:visible, select:visible, textarea:visible, [tabindex]:not([tabindex="-1"]):visible'
				)

				if (focusableElements.length === 0) return

				const firstFocusable = focusableElements[0]
				const lastFocusable = focusableElements[focusableElements.length - 1]

				if (e.shiftKey) {
					// Shift+Tab: moving backwards
					if (document.activeElement === firstFocusable) {
						e.preventDefault()
						lastFocusable.focus()
					}
				} else {
					// Tab: moving forwards
					if (document.activeElement === lastFocusable) {
						e.preventDefault()
						firstFocusable.focus()
					}
				}
			}
		}
	})

	// Restore focus when modal is closed
	$('.modal').on('hidden.bs.modal', function () {
		if (lastFocusedCard) {
			lastFocusedCard.focus()
			lastFocusedCard = null
		}
	})

	console.log(
		'ELC Media Gallery: Card gallery initialized with navigation and fullscreen'
	)
}

// Initialize when DOM is ready
$(document).ready(function () {
	initMediaGallery()
})

// Re-initialize on AJAX loads (for dynamic content)
$(document).on('DOMContentLoaded', function () {
	initMediaGallery()
})
