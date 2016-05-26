<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

File Description: Top Bar

*/
Plethora_Theme::dev_comment('========================= TOP BAR ========================', 'layout'); 

// Get attributes sent by module class
$atts = get_query_var( 'module_atts' );
?>
<div class="topbar vcenter <?php echo esc_attr( implode( ' ', $atts['style'] ) ); ?>">
  <div class="container">
    <div class="row">
      <div class="col-md-<?php echo esc_attr( $atts['col1']['size'] ); ?> col-sm-<?php echo esc_attr( $atts['col1']['size'] ); ?> <?php echo esc_attr( $atts['col1']['visibility_classes'] ); ?> <?php echo esc_attr( $atts['col1']['align_class'] ); ?>">
       <?php 
       if ( $atts['col1']['content_type'] === 'menu' ) {  ?>
          <div class="top_menu_container">
          <?php
              wp_nav_menu( array(
                'container'       => false, 
                'menu_class'      => 'top_menu hover_menu', 
                'depth'           => 6,
                'theme_location' => $atts['col1']['content_menu'],
                'walker'          => ( class_exists( 'Plethora_Module_Navwalker_Ext' )) ? new Plethora_Module_Navwalker_Ext() : '',
              ));
           ?>
          </div>
          <?php

      } elseif ( $atts['col1']['content_type'] === 'text' ) { 
        
        echo wp_kses_post( $atts['col1']['content_text'] ); 

      } elseif ( $atts['col1']['content_type'] === 'customtext' ) { 

        echo wp_kses_post( $atts['col1']['content_customtext'] ); 

      } ?>
      </div>
<?php
if ( $atts['layout'] == '2' || $atts['layout'] == '3' || $atts['layout'] == '4'  ) {  ?>
      <div class="col-md-<?php echo esc_attr( $atts['col2']['size'] ); ?> col-sm-<?php echo esc_attr( $atts['col2']['size'] ); ?> <?php echo esc_attr( $atts['col2']['visibility_classes'] ); ?> <?php echo esc_attr( $atts['col2']['align_class'] ); ?>">
       <?php 
       if ( $atts['col2']['content_type'] === 'menu' ) {  ?>
          <div class="top_menu_container">
          <?php
              wp_nav_menu( array(
                'container'       => false, 
                'menu_class'      => 'top_menu hover_menu', 
                'depth'           => 6,
                'theme_location' => $atts['col2']['content_menu'],
                'walker'          => ( class_exists( 'Plethora_Module_Navwalker_Ext' )) ? new Plethora_Module_Navwalker_Ext() : '',
              ));
           ?>
          </div>
          <?php
      } elseif ( $atts['col2']['content_type'] === 'text' ) { 
        
        echo wp_kses_post( $atts['col2']['content_text'] ); 

      } elseif ( $atts['col2']['content_type'] === 'customtext' ) { 

        echo wp_kses_post( $atts['col2']['content_customtext'] ); 
        
      } ?>
      </div>
<?php } ?>      
    </div>
  </div>
</div>
<?php Plethora_Theme::dev_comment('END ========================= TOP BAR ========================', 'layout'); ?>
