<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

Media Panel Section / simple image display

*/
// Get attributes sent by module class
$atts = get_query_var( 'module_atts' );

$module_atts = array();

$module_atts["media_url"]               = @esc_attr( $atts['media']['url'] );
$module_atts["imgvalign"]               = $atts['style']['imgvalign'];
$module_atts["parallax"]                = ( $atts['style']['parallax'] == 1 ) ? 'parallax-window' : '';
$module_atts["full_height"]             = ( $atts['style']['full_height'] ) ? ' full_height' : '';

$module_atts["title_subtitle"]          = ( !empty($atts['title']) || !empty($atts['subtitle']) ) ? true : false;
$module_atts["text_align"]              = esc_attr( $atts['style']['text_align'] );

$module_atts["title"]                   = ( !empty($atts['title']) ) ? true : false;
$module_atts["title_diagonal_class"]    = esc_attr( $atts['style']['diagonal_title_class'] );
$module_atts["title_colorset"]          = esc_attr( $atts['style']['title_colorset'] );
$module_atts["title_backgroundtype"]    = esc_attr( $atts['style']['title_backgroundtype'] );
$module_atts["title_text"]              = $atts['title'];

$module_atts["subtitle"]                = ( !empty($atts['subtitle']) ) ? true : false;
$module_atts["subtitle_diagonal_class"] = esc_attr( $atts['style']['diagonal_subtitle_class'] );
$module_atts["subtitle_colorset"]       = esc_attr( $atts['style']['subtitle_colorset'] );
$module_atts["subtitle_backgroundtype"] = esc_attr( $atts['style']['subtitle_backgroundtype'] );
$module_atts["subtitle_text"]           = $atts['subtitle'];

echo Plethora_WP::renderMustache( 
  array( 

    "data"     => $module_atts, 
    "file"     => __FILE__, 
    "module"   => true  

  ) 
);