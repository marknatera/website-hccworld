<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Handles the content column wrapper opening tags

*/
// Get the layout ( you may filter layout value using the 'plethora_layout' hook )
$layout   = Plethora_Theme::get_layout();
switch ( $layout ) {
	case 'no_sidebar' :
	  $wrapper_column_open = Plethora_Theme::content_has_sections() ? '' : '<div class="'. esc_attr( implode( ' ', apply_filters( 'plethora_wrapper_column_class', array('col-md-12')) ) ).'">';
	  break;
	case 'right_sidebar' :
	  $wrapper_column_open = '<div class="col-sm-8 col-md-8 main_col '. esc_attr( implode( ' ', apply_filters( 'plethora_wrapper_column_class', array()) ) ).'">';
	  break;
	case 'left_sidebar' :
	  $wrapper_column_open = '<div class="col-sm-8 col-md-8 main_col '. esc_attr( implode( ' ', apply_filters( 'plethora_wrapper_column_class', array()) ) ).'">';
	  break;
	default:
	  $wrapper_column_open = Plethora_Theme::content_has_sections() ? '' : '<div class="col-md-12 '. esc_attr( implode( ' ', apply_filters( 'plethora_wrapper_column_class', array()) ) ).'">';
	  break;
}
Plethora_Theme::dev_comment('  START >> ========================= MAIN COLUMN ========================', 'layout');
echo apply_filters( 'plethora_wrapper_column_open', $wrapper_column_open );