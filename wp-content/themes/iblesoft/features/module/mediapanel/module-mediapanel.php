<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Media Panel Module Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Module_Mediapanel') && !class_exists('Plethora_Module_Mediapanel_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-framework/features/module/module-mediapanel.php
   */
  class Plethora_Module_Mediapanel_Ext extends Plethora_Module_Mediapanel { 

	public $revslider_support = true;


	public function template_hooks() {

	      $mediapanel = !is_404() ? Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-status', 0, 0, false) : Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-404-status', 1, 0, false);
	      if ( $mediapanel ) {                             // Media Panel Module template
			// Add this on 'plethora_header_after' / priority:5
	        add_action( 'plethora_header_after', array( $this, 'mediapanel'), 5);
	      	
	      	// Remove 'padding_top_half' from sidebar
	      	add_filter( 'plethora_wrapper_content_class', array( $this, 'remove_padding_top_half' ) , 99 );
	      }
	}

	public function remove_padding_top_half( $classes ) {

		if ( ($key = array_search('padding_top_half', $classes) ) !== false) {
    		
    		unset($classes[$key]);
		}

		return $classes;
	}
  }
}