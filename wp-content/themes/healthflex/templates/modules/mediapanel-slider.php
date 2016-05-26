<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

Media Panel Section / Slider display

*/

// Get attributes sent by module class
$atts = get_query_var( 'module_atts' );

$module_atts                = array( "slides" => array() );
$module_atts['full_height'] = ( $atts['style']['full_height'] == 0 ) ? false : true;

foreach ( $atts['media'] as $key => $slide ) { 

  if ( !empty($slide['image']['url']) ) {

    $slide_atts = array();

    $slide_atts["bg_image_url"]           = $slide['image']['url'];
    $slide_atts["full_height"]            = ( $atts['style']['full_height'] ) ? 'full_height' : '';
    $slide_atts["color_set"]              = esc_attr($slide['colorset']);
    $slide_atts["transparent_film"]       = esc_attr($slide['transparentfilm']);
    $slide_atts["caption_neutralizetext"] = esc_attr($slide['caption_neutralizetext']);
    $slide_atts["captions"]               = ( 
                                              !empty($slide['caption_title']) || 
                                              !empty($slide['caption_subtitle']) || 
                                              !empty($slide['caption_secondarytitle']) || 
                                              !empty($slide['caption_secondarytext']) 
                                            ) ? true : false;

    $slide_atts["caption_align"]           = esc_attr($slide['caption_align']);
    $slide_atts["caption_headingstyle"]    = esc_attr($slide['caption_headingstyle']);
    $slide_atts["caption_size"]            = esc_attr($slide['caption_size']);
    $slide_atts["caption_textalign"]       = esc_attr($slide['caption_textalign']);
    $slide_atts["caption_colorset"]        = esc_attr($slide['caption_colorset']);
    $slide_atts["caption_transparentfilm"] = esc_attr($slide['caption_transparentfilm']);
    $slide_atts["caption_animation"]       = ( !empty( $slide['caption_animation']) ) ? esc_attr($slide['caption_animation']) : '';

    $slide_atts["caption_title"]          = ( !empty($slide['caption_title']) )? $slide['caption_title']  : false;
    $slide_atts["caption_subtitle"]       = ( !empty($slide['caption_subtitle']) ) ? $slide['caption_subtitle'] : false;
    $slide_atts["caption_secondarytitle"] = ( !empty($slide['caption_secondarytitle']) ) ? $slide['caption_secondarytitle'] : false;
    $slide_atts["caption_secondarytext"]  = ( !empty($slide['caption_secondarytext']) ) ? $slide['caption_secondarytext'] : false;

    $slide_atts["caption_button"]        = ( !empty($slide['caption_buttonlinktext']) && !empty($slide['caption_buttonlinkurl']) && $slide['caption_buttonlinkurl'] !=='#' ) ? true : false;
    $slide_atts["caption_button_url"]    = esc_url( $slide['caption_buttonlinkurl'] );
    $slide_atts["caption_button_target"] = esc_attr( $slide['caption_buttonlinktarget'] );
    $slide_atts["caption_button_size"]   = esc_attr( $slide['caption_buttonsize'] );
    $slide_atts["caption_button_style"]  = esc_attr( $slide['caption_buttonstyle'] );
    $slide_atts["caption_button_text"]   = esc_html( $slide['caption_buttonlinktext'] );

    array_push( $module_atts["slides"], $slide_atts );

  }

}

echo Plethora_WP::renderMustache( 
  array( 

    "data"     => $module_atts, 
    "file"     => __FILE__, 
    "module"   => true  

  ) 
);

