<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

Media Panel Section / Google Map

*/
// Get attributes sent by module class
$atts = get_query_var( 'module_atts' );
$full_height = $atts['style']['full_height'] ? ' full_height' : ''
?>
<div class="head_panel">
	<div id="map" class="map<?php echo esc_attr( $full_height ); ?>"></div>
      <div class="hgroup">
      <?php if ( !empty($atts['title']) ) { ?>
        <div class="title <?php echo esc_attr( $atts['style']['diagonal_title_class'] ); ?><?php echo esc_attr( $atts['style']['title_colorset'] ); ?> <?php echo esc_attr( $atts['style']['title_backgroundtype'] ); ?> <?php echo esc_attr( $atts['style']['text_align'] ); ?>">
          <div class="container">
            <h1><?php echo esc_html( $atts['title'] ); ?></h1>
          </div>
        </div>
      <?php } ?>
        <div class="subtitle <?php echo esc_attr( $atts['style']['diagonal_subtitle_class'] ); ?> <?php echo esc_attr( $atts['style']['subtitle_colorset'] ); ?> <?php echo esc_attr( $atts['style']['subtitle_backgroundtype'] ); ?> <?php echo esc_attr( $atts['style']['text_align'] ); ?>">
          <div class="container">   
            <p><?php echo esc_html( $atts['subtitle'] ); ?></p>
          </div>
        </div>
      </div>
</div>