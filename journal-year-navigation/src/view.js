jQuery(document).ready(function ($) {
	$('.carousel').on(
		'beforeChange',
		function (event, slick, currentSlide, nextSlide) {
			// Add a fade-out class to all slides before filtering
			$('.slick-slide').css('opacity', 0)
		}
	)

	console.log('In ready')
	// Add click event to filter links
	$('.year-col a').on('click', function (e) {
		console.log('got a click')

		e.preventDefault() // Prevent default link behaviour

		const selectedYear = $(this).data('year')
		console.log('selectedyear', selectedYear)

		// Highlight the selected filter link
		$('.year-col a').removeClass('active')
		$(this).addClass('active')

		// Re-apply fade-in effect
		$('.slick-slide').css({
			opacity: 0,
			transition: 'opacity 0.5s ease'
		})

		// Apply slickFilter or slickUnfilter
		if (selectedYear === 'all') {
			$('#journal-listings').slick('slickUnfilter') // Show all slides
		} else {
			$('#journal-listings').slick('slickUnfilter')

			$('#journal-listings').slick('slickFilter', function (index, slide) {
				console.log('Got a slide')
				return $(slide).find('.journal-card').data('year') === selectedYear // Match year
			})
		}

		setTimeout(() => {
			$('.slick-slide').css('opacity', 1)
		}, 50) // Timeout ensures transition applies correctly
	})
})
