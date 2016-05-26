<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

Media Panel Section / Skincolored Background / Simple Headings Layout

*/
// Get attributes sent by module class
$atts = get_query_var( 'module_atts' );

?>
<div class="head_panel skincolored_section <?php echo esc_attr( $atts['style']['diagonal_title_class'] ); ?>">
    <div class="<?php echo esc_attr( $atts['style']['headings_layout'] ); ?>">
      <div class="hgroup">
      <?php if ( !empty($atts['title']) ) { ?>
        <div class="title <?php echo esc_attr( $atts['style']['diagonal_title_class'] ); ?> <?php echo esc_attr( $atts['style']['diagonal_subtitle_class'] ); ?> <?php echo esc_attr( $atts['style']['text_align'] ); ?> <?php echo esc_attr( $atts['style']['title_colorset'] ); ?> <?php echo esc_attr( $atts['style']['title_backgroundtype'] ); ?>">
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
</div>