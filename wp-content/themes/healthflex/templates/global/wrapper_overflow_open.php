<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Handles the overflow wrapper opening tags

*/
Plethora_Theme::dev_comment('   ========================= OVERFLOW WRAPPER START ========================', 'layout');

$wrapper_overflow_open = '<div class="overflow_wrapper">';

echo apply_filters( 'plethora_wrapper_overflow_open', $wrapper_overflow_open );