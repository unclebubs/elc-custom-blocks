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
		const $caption = $modal.find('.elc-modal-caption')
		const $description = $modal.find('.elc-modal-description')

		// Fade out current content
		$image.css('opacity', '0')
		$caption.css('opacity', '0')
		$description.css('opacity', '0')

		// Wait for fade out, then update content
		setTimeout(function () {
			// Update image
			$image.attr('src', image.large).attr('alt', image.alt)

			// Update caption
			if (image.caption) {
				$caption.html('<strong>' + image.caption + '</strong>').show()
			} else {
				$caption.empty().hide()
			}

			// Update description
			if (image.description) {
				$description.text(image.description).show()
			} else {
				$description.empty().hide()
			}

			// Fade in new content
			setTimeout(function () {
				$image.css('opacity', '1')
				$caption.css('opacity', '1')
				$description.css('opacity', '1')
			}, 50)
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

	// Handle keyboard arrows in modal
	$('.modal').on('keydown', function (e) {
		if ($(this).hasClass('show')) {
			if (e.key === 'ArrowLeft') {
				e.preventDefault()
				$(this).find('.elc-nav-prev').click()
			} else if (e.key === 'ArrowRight') {
				e.preventDefault()
				$(this).find('.elc-nav-next').click()
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
