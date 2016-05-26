<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2014

File Description: Footer Widgetized Areas template 

*/
$sidebar_one   = Plethora_Theme::option( METAOPTION_PREFIX .'footer-sidebar-one', 'sidebar-footer-one');
$sidebar_two   = Plethora_Theme::option( METAOPTION_PREFIX .'footer-sidebar-two', 'sidebar-footer-two');
$sidebar_three = Plethora_Theme::option( METAOPTION_PREFIX .'footer-sidebar-three', 'sidebar-footer-three');
$sidebar_four  = Plethora_Theme::option( METAOPTION_PREFIX .'footer-sidebar-four', 'sidebar-footer-four');
?>
<div class="col-sm-6 col-md-3"><?php dynamic_sidebar( $sidebar_one ); ?></div>
<div class="col-sm-6 col-md-3"><?php dynamic_sidebar( $sidebar_two ); ?></div>
<div class="col-sm-6 col-md-3"><?php dynamic_sidebar( $sidebar_three ); ?></div>
<div class="col-sm-6 col-md-3"><?php dynamic_sidebar( $sidebar_four ); ?></div>