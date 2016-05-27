<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Handles the content column wrapper closing tags

*/
// Get the layout ( you may filter layout value using the 'plethora_layout' hook )
$layout   = Plethora_Theme::get_layout();
switch ( $layout ) {
	case 'no_sidebar' :
	  $wrapper_column_close = Plethora_Theme::content_has_sections() ? '' : '</div>';
	  break;
	case 'right_sidebar' :
	  $wrapper_column_close = '</div>';
	  break;
	case 'left_sidebar' :
	  $wrapper_column_close = '</div>';
	  break;
	default:
	  $wrapper_column_close = '';
	  break;
}

echo apply_filters( 'plethora_wrapper_column_close', $wrapper_column_close );
Plethora_Theme::dev_comment('   << END ========================= MAIN COLUMN ========================', 'layout'); ?>