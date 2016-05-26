<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

Layout page: Archive ( any post listings page )

*/
get_header();
/**
* plethora_content_before hook
*
* @hooked Plethora_Template::wrapper_content_open - 10 ( Content wrapper open div )
* @hooked Plethora_Template::sidebar_left() - 15 ( Left sidebar display )
* @hooked Plethora_Template::main_column_open() - 20 ( Main content column open div )
* 
*/
do_action('plethora_content_before'); 

		// Main Content Area
	if ( ! is_404() ) { 
		if ( have_posts() ) { 

			while ( have_posts() ) : the_post(); 

				/**
				* 'plethora_content' hook
				*/
				do_action('plethora_content'); 


			endwhile;
			 
		} else {

			Plethora_WP::get_template_part('templates/global/noposts' );
		}

	} else {

		/**
		* 'plethora_content' hook
		*/
		do_action('plethora_content'); 
	}


/**
* plethora_content_after hook
*
* @hooked Plethora_Template::main_column_close() - 10 ( Main column close )
* @hooked Plethora_Template::sidebar_right() - 15 ( Right sidebar )
* @hooked Plethora_Template::wrapper_content_close() - 30 ( Content wrapper close )
* 
*/
do_action('plethora_content_after'); 

get_footer();