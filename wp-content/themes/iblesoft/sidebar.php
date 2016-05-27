<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2014

File Description: Handles main left/right sidebars

*/
// Get the main sidebar ( you may filter this value using the 'plethora_main_sidebar' hook  )
$sidebar = Plethora_Theme::get_main_sidebar();
echo apply_filters( 'plethora_sidebar_wrapper_open', '<div id="sidebar" class="col-sm-4 col-md-4">' );
dynamic_sidebar( $sidebar );
echo apply_filters( 'plethora_sidebar_wrapper_close', '</div>' );