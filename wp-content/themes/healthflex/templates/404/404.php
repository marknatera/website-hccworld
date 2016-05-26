<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2015

File Description: 404 Page Template Class
*/
if ( !class_exists('Plethora_Template_Single') ) {

  class Plethora_Template_Single { 

  	public function __construct() {

        add_action( 'plethora_content', array( $this, 'content')); // 404 content
  	}

    /**
     * Returns 404 page content ( depending on format )
     */
    public static function content() {
      $atts['title']          = Plethora_Theme::option( THEMEOPTION_PREFIX .'404-contenttitle', esc_html('Error 404 is nothing to really worry about...', 'healthflex'));
      $atts['content']        = Plethora_Theme::option( THEMEOPTION_PREFIX .'404-content', esc_html('You may have mis-typed the URL, please check your spelling and try again.', 'healthflex'));
      $atts['search']         = Plethora_Theme::option( THEMEOPTION_PREFIX .'404-search', 1);
      $atts['search_btntext'] = Plethora_Theme::option( THEMEOPTION_PREFIX .'404-search-btntext', esc_html('Search', 'healthflex'));
      set_query_var( 'atts', $atts );
      Plethora_WP::get_template_part( 'templates/404/content' );
    }
  } 
}    