<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Handles content wrapper opening tags

*/
// Get the layout ( you may filter layout value using the 'plethora_layout' hook )
$layout   = Plethora_Theme::get_layout();
switch ( $layout ) {
case 'no_sidebar' :
  $wrapper_content_open = ! Plethora_Theme::content_has_sections() ? '<section class="vc_off sidebar_off '. esc_attr( implode( ' ', apply_filters( 'plethora_wrapper_content_class', array() ) ) ).'" '. implode( ' ', Plethora_WP::apply_data_attrs( 'plethora_wrapper_content_data_attrs' ) ) .'><div class="container"><div class="row">': '';
  break;
case 'right_sidebar' :
  $wrapper_content_open = '<section class="sidebar_on '. esc_attr( implode( ' ', apply_filters( 'plethora_wrapper_content_class', array( 'padding_top_half' ) ) ) ).'" '. implode( ' ', Plethora_WP::apply_data_attrs( 'plethora_wrapper_content_data_attrs' ) ) .'><div class="container"><div class="row">';
  break;
case 'left_sidebar' :
  $wrapper_content_open = '<section class="sidebar_on '. esc_attr( implode( ' ', apply_filters( 'plethora_wrapper_content_class', array( 'padding_top_half' ) ) ) ).'" '. implode( ' ', Plethora_WP::apply_data_attrs( 'plethora_wrapper_content_data_attrs' ) ) .'><div class="container"><div class="row">';
  break;
default:
  $wrapper_content_open = ! Plethora_Theme::content_has_sections() ? '<section class="'. esc_attr( implode( ' ', apply_filters( 'plethora_wrapper_content_class', array() ) ) ).'" '. implode( ' ', Plethora_WP::apply_data_attrs( 'plethora_wrapper_content_data_attrs' ) ) .'><div class="container"><div class="row">': '';
  break;
}
Plethora_Theme::dev_comment('   >> START ========================= CONTENT AREA ========================', 'layout');
echo apply_filters( 'plethora_wrapper_content_open', $wrapper_content_open );