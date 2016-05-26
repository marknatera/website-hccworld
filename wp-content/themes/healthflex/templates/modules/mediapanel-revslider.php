<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

Media Panel Section / Revolution Slider display

*/

// Get attributes sent by module class
$atts = get_query_var( 'module_atts' );

$revslider = !empty( $atts['media']['revslider_alias'] ) ? $atts['media']['revslider_alias'] : false ;

if ( $revslider  ) {
  
  echo '<div class="rev_slider_wrapper header-introduction text-center">';
  	Plethora_Module_Revslider_Ext::get_slider_output( $revslider );
  echo '</div>';
}