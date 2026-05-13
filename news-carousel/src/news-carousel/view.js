/**
 * Use this file for JavaScript code that you want to run in the front-end
 * on posts/pages that contain this block.
 *
 * When this file is defined as the value of the `viewScript` property
 * in `block.json` it will be enqueued on the front end of the site.
 *
 * Example:
 *
 * ```js
 * {
 *   "viewScript": "file:./view.js"
 * }
 * ```
 *
 * If you're not making any changes to this file because your project doesn't need any
 * JavaScript running in the front-end, then you should delete this file and remove
 * the `viewScript` property from `block.json`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#view-script
 */

/**
 * Initialize Slick Slider for news carousels
 */
document.addEventListener('DOMContentLoaded', function () {
	// Check if jQuery and Slick are available
	if (typeof jQuery === 'undefined') {
		console.error('News Carousel: jQuery is not loaded')
		return
	}

	if (typeof jQuery.fn.slick === 'undefined') {
		console.error('News Carousel: Slick Slider is not loaded')
		return
	}

	// Initialize all news carousels on the page
	jQuery('.news-carousel').each(function () {
		var $carousel = jQuery(this)

		// Check if already initialized
		if ($carousel.hasClass('slick-initialized')) {
			return
		}

		console.log('Initializing News Carousel with Slick Slider')

		// Initialize Slick Slider
		$carousel.slick({
			dots: true,
			infinite: true,
			speed: 500,
			slidesToShow: 1,
			slidesToScroll: 1,
			autoplay: true,
			autoplaySpeed: 5000,
			arrows: true,
			mobileFirst: true,
			adaptiveHeight: false,
			prevArrow:
				'<button type="button" class="slick-prev"><span aria-hidden="true">&lsaquo;</span></button>',
			nextArrow:
				'<button type="button" class="slick-next"><span aria-hidden="true">&rsaquo;</span></button>',
			responsive: [
				{
					breakpoint: 1200, // Bootstrap 5 xl breakpoint
					settings: {
						slidesToShow: 3,
						slidesToScroll: 1,
						adaptiveHeight: false
					}
				},
				{
					breakpoint: 768, // Bootstrap 5 md breakpoint
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1,
						adaptiveHeight: false
					}
				}
			]
		})

		console.log('News Carousel initialized successfully')
	})
})
