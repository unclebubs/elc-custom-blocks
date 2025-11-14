jQuery(document).ready(function ($) {
	// Add click event to filter links
	$('.year-col a').on('click', function (e) {
		console.log('got a click')

		e.preventDefault() // Prevent default link behaviour

		const selectedYear = $(this).data('year')
		console.log('selectedyear', selectedYear)

		// Highlight the selected filter link
		$('.year-col a').removeClass('active')
		$(this).addClass('active')

		const cards = document.querySelectorAll('#podcast-listings .card')

		cards.forEach(card => {
			const cardYear = card.getAttribute('data-year')
			const col = card.closest('.col')

			if (selectedYear === 'all' || cardYear == selectedYear) {
				col.classList.add('show')
				col.classList.remove('d-none')
			} else {
				col.classList.remove('show')

				setTimeout(() => {
					col.classList.add('d-none')
				}, 100) // Add fade class before hiding
			}
		})

		// Scroll to the element with id 'scroller'
		const scroller = document.getElementById('podcast-listings')
		if (scroller) {
			scroller.scrollIntoView({ behavior: 'smooth' })
		}
	})
})
