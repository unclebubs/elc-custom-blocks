<form
	class='podcast-search d-flex flex-row '
	role='search'
	method='get'
	action='/'>
	<input type='hidden' name='post_type' value='podcast' />
	<label
		class='wp-block-search__label d-none'
		for='wp-block-search__input-1'>
		Search
	</label>
	<input
		class='form-control form-control-sm my-2 '
		id='wp-block-search__input-1'
		type='search'
		name='s'
		placeholder='Search podcasts'
		value='<?php echo get_search_query(); ?>' />
	<button aria-label='Go' type='submit' class='btn btn-sm btn-elc-green-light'>
		Go
	</button>
</form>