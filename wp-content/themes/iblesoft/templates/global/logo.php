<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

File Description: Logo template part

*/
$logo_type      = Plethora_Theme::option( THEMEOPTION_PREFIX .'logo-layout', '1');         
$logo_img       = Plethora_Theme::option( THEMEOPTION_PREFIX .'logo-img', array('url'=> ''. PLE_THEME_ASSETS_URI .'/images/healthflex_logo_color.png') );         
$logo_title     = Plethora_Theme::option( THEMEOPTION_PREFIX .'logo-title', esc_html__('HealthFlex', 'healthflex') ); 
$logo_subtitle  = Plethora_Theme::option( THEMEOPTION_PREFIX .'logo-subtitle', esc_html__('Call us Toll free +30 1234-567-890', 'healthflex') ); 
$logo_anim      = Plethora_Theme::option( THEMEOPTION_PREFIX .'logo-subtitle-animation', 0); 
$logo_anim_type = Plethora_Theme::option( THEMEOPTION_PREFIX .'logo-subtitle-animation-type', 'slideInLeft'); 
?>
         <div class="logo">
            <a href="<?php echo esc_url( home_url() ); ?>" class="brand">
            <?php if (( $logo_type == '1' || $logo_type == '2') && ( !empty( $logo_img['url'] ) ) ) { ?>
              <img src="<?php echo esc_url( $logo_img['url'] ); ?>" alt="<?php echo esc_attr( $logo_title ); ?>">
            <?php } elseif (( $logo_type == '3' || $logo_type == '4') && ( !empty( $logo_title ) ) ) { 
              echo esc_html( $logo_title );
              } ?>
            </a>
            <?php if (( $logo_type == '2' || $logo_type == '3' ) && ( !empty( $logo_subtitle ) )) { ?>
            <?php 
              $anim_class = $logo_anim == '1' ? 'animated '. $logo_anim_type .'"' : ''; 
              ?>
            <p class="<?php echo esc_attr( $anim_class ); ?>"><?php echo esc_html( $logo_subtitle ); ?></p>
            <?php } ?>
          </div>