<?php $current_user = wp_get_current_user(); ?>
<div class='login-outer'>
	<div class='login-container'>
		<div class='avatar' id='avatar'>
			<?php if (is_user_logged_in()): ?>
				<?php
				$profile_picture = get_avatar_url($current_user->ID, ['size' => 96]);
				?>
				<img src='<?php echo esc_url($profile_picture); ?>' alt='Profile Picture' />
			<?php else: ?>
				<i class='bi bi-person'></i>
			<?php endif; ?>
		</div>
		<div class='info'>
			<p class='info-name'>
				<?php if (is_user_logged_in()): ?>
					<?php echo esc_html($current_user->display_name); ?>
					<br>
					<?php echo esc_html($current_user->user_email); ?>
				<?php else: ?>
					My Account
				<?php endif; ?>
			</p>
			<div class='info-links-container'>
				<?php if (is_user_logged_in()): ?>
					<a class='info-links btn btn-sm btn-primary' href='<?php echo esc_url(um_get_core_page('account')); ?>'>Account</a>
					<a class='info-links btn btn-sm btn-primary' href='<?php echo esc_url(um_get_core_page('logout')); ?>'>Logout</a>
				<?php else: ?>
					<a class='info-links btn btn-sm btn-primary' href='<?php echo esc_url(um_get_core_page('register')); ?>'>Register</a>
					<a class='info-links btn btn-sm btn-primary' href='<?php echo esc_url(um_get_core_page('login')); ?>'>Sign In</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>