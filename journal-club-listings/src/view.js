jQuery(function ($) {
	$('#journal-listings').slick({
		slidesToShow: 4,
		slidesToScroll: 4,
		arrows: true,
		infinite: false,
		adaptiveHeight: true,
		responsive: [
			{
				breakpoint: 992, // lg breakpoint
				settings: {
					slidesToShow: 3,
					slidesToScroll: 3
				}
			},
			{
				breakpoint: 767, // md breakpoint
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
					dots: false
				}
			},
			{
				breakpoint: 576, // md breakpoint
				settings: {
					slidesToShow: 1,
					slidesToScroll: 1,
					dots: true,
					arrows: false
				}
			}
		]
	})

	$('.slick-slider>div').on('setPosition', function () {
		setEqualHeight()
	})

	$(window).on('resize', function () {
		setEqualHeight()
	})

	function setEqualHeight () {
		let maxHeight = 0

		// Find the tallest card inside the slick-track
		$('.slick-track .card').each(function () {
			let thisHeight = $(this).outerHeight()
			if (thisHeight > maxHeight) {
				maxHeight = thisHeight
			}
		})

		// Apply the height to all cards and the slick-track
		$('.slick-track').height(maxHeight)
		$('.slick-slide .card').height(maxHeight)
	}
})
