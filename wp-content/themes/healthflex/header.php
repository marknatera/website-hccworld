<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

File Description: Header - most of @hooked methods are included in /templates/template.php  

*/
?><!doctype html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<?php 
	/**
	 * 'plethora_head_before' hook
	 *
	 * @hooked Plethora_Template::head_meta() - 10 ( Meta settings )
	 * @hooked Plethora_Template::favicons() - 20 ( Favicons )
	 */
	 do_action('plethora_head_before');  

	  // Call wp_head
	 wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php
	 /**
	 * 'plethora_body_open' hook
	 *
	 * @hooked Plethora_Template::wrapper_overflow_open() - 10 ( overflow wrapper opening div )
	 * 
	 */
	 do_action('plethora_body_open'); 
	 /**
	 * 'plethora_header_before' hook
	 * 
	 */
	 do_action('plethora_header_before'); 

	 if ( has_filter( 'plethora_header' ) ) { ?>

		<div class="<?php echo esc_attr( implode( ' ', apply_filters( 'plethora_header_class', array('header')) ) ); ?>"><?php
		 /**
		 * 'plethora_header' hook
		 *
		 * @hooked Plethora_Template::topbar() - 10  ( Header top bar )
		 * @hooked Plethora_Template::social_icons() - 20  ( Header social icons bar )
		 * @hooked Plethora_Template::navigation() - 30  ( Main navigation  )
		 * 
		 */
		 do_action('plethora_header'); 
		?>
		</div>

	<?php } 
	 /**
	 * 'plethora_header_after' hook
	 *
	 * @hooked Plethora_Template::wrapper_main_open() - 10  ( main wrapper openening div )
	 * 
	 */
	 do_action('plethora_header_after'); 
	?>