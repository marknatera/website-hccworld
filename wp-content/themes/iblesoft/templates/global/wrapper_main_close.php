<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Handles main wrapper closing tags

*/
$wrapper_main_close = '</div>';

echo apply_filters( 'plethora_wrapper_main_close', $wrapper_main_close );

Plethora_Theme::dev_comment('   END ========================= MAIN WRAPPER FINISH ========================', 'layout');