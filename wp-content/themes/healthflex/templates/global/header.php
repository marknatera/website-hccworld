<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

Template part: Header wrapper & inner parts
*/

$logo       = Plethora_Theme::option( METAOPTION_PREFIX .'logo', 1 );
$navigation = Plethora_Theme::option( METAOPTION_PREFIX .'navigation-main', 1 );
$social_bar = Plethora_Theme::option( METAOPTION_PREFIX .'socialbar', 1 );

if ( $logo || $navigation || $social_bar ) { // if any of those sections is enabled, go on!

  $color_set  = Plethora_Theme::option( METAOPTION_PREFIX .'header-colorset', '', 0, false);
  $background = Plethora_Theme::option( METAOPTION_PREFIX .'header-background', 'gradient', 0, false);
  $classes[]  = $color_set;
  $classes[]  = $background;
  ?>
  <div class="mainbar <?php echo esc_attr( implode( ' ',  $classes ) ); ?>">
    <div class="container">
      <?php if ( $logo ) { Plethora_WP::get_template_part('templates/global/logo'); } ?>
      <?php if ( $social_bar ) { Plethora_WP::get_template_part('templates/global/socialbar'); } ?>
      <?php if ( $navigation ) { Plethora_WP::get_template_part('templates/global/navigation'); } ?>
    </div>
  </div>

<?php } ?>