<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

Template part: Favicons
*/
$fav              = Plethora_Theme::option( THEMEOPTION_PREFIX .'favicon');
$fav_32           = Plethora_Theme::option( THEMEOPTION_PREFIX .'favicon-32');
$fav_96           = Plethora_Theme::option( THEMEOPTION_PREFIX .'favicon-96');
?>
<?php Plethora_Theme::dev_comment('FAVICONS', 'layout'); ?>
<?php if ( !empty( $fav['url'] )) { ?><link rel="icon" sizes="16x16" type="image/png" href="<?php echo esc_url( $fav['url'] ) ?>"><?php } ?>
<?php if ( !empty( $fav_32['url'] )) { ?><link rel="icon" sizes="32x32" type="image/png" href="<?php echo esc_url( $fav_32['url'] ) ?>"><?php } ?>
<?php if ( !empty( $fav_96['url'] )) { ?><link rel="icon" sizes="96x96" type="image/png" href="<?php echo esc_url( $fav_96['url'] ) ?>"><?php } ?>
<?php if ( !empty( $fav['url'] )) { ?><link rel="shortcut icon" href="<?php echo esc_url( $fav['url'] ) ?>"> <?php } ?>
<meta name="theme-color" content="#ffffff">
