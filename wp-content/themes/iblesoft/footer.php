<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

File Description: Footer - most of @hooked methods are included in theme-html.php file 
*/

	 /**
	 * 'plethora_footer_before' hook
	 *
	 * @hooked Plethora_Template::wrapper_main_close() - 10 ( Main wrapper close )
	 * 
	 */
	 do_action('plethora_footer_before'); 
	 if ( has_filter( 'plethora_footer' ) ) { 
		 ?>
		<footer class="<?php echo esc_attr( implode( ' ', apply_filters( 'plethora_footer_class', array() ) ) ); ?>">
		 <?php

		 /**
		 * 'plethora_footer' hook
		 *
		 * @hooked Plethora_Template::footer_widgets() - 10  ( footer widgets section  )
		 * @hooked Plethora_Template::footer_infobar() - 20  ( footer info bar  )
		 * 
		 */
		 do_action('plethora_footer'); 
		?>
		</footer>
		<?php
	}
	 /**
	 * 'plethora_footer_after' hook
	 *
	 * @hooked Plethora_Template::wrapper_overflow_close() - 20  ( overflow wrapper closing div )
	 * 
	 */
	 do_action('plethora_footer_after'); 

	 // Call wp_footer
	 wp_footer(); ?>
</body>
</html>