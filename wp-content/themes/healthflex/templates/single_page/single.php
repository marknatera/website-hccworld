<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2015

File Description: Single Page Template Class
*/
if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Template_Single') ) {

  class Plethora_Template_Single { 

  	public function __construct() {

         // Main Post parts
        add_action( 'plethora_content', array( $this, 'content'), 10);  // Post editor content
        add_action( 'plethora_content', array( 'Plethora_Template', 'single_comments'), 10);         // Comments
  	}

    /**
     * Returns single page content ( depending on format )
     */
    public static function content() {

      the_content();  
      wp_link_pages(array(
               'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'healthflex' ) . '</span>',
               'after'       => '</div>',
               'link_before' => '<span>',
               'link_after'  => '</span>',
      )); 

    }
  } 
}    