<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Handles main wrapper opening tags

*/
Plethora_Theme::dev_comment('   ========================= MAIN WRAPPER START ========================', 'layout');

$wrapper_main_open = '<div class="'. esc_attr( implode( ' ', apply_filters( 'plethora_wrapper_main_class', array('main')) ) ).'" '. implode( ' ', Plethora_WP::apply_data_attrs( 'plethora_wrapper_main_data_attrs' ) ) .'>';

echo apply_filters( 'plethora_wrapper_main_open', $wrapper_main_open );