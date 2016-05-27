<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Handles content wrapper closing tags

*/
// Get the layout ( you may filter layout value using the 'plethora_layout' hook )
$layout   = Plethora_Theme::get_layout();
switch ( $layout ) {
case 'no_sidebar' :
  $wrapper_content_close = ! Plethora_Theme::content_has_sections() ? '</div></div></section>': '';
  break;
case 'right_sidebar' :
  $wrapper_content_close = '</div></div></section>';
  break;
case 'left_sidebar' :
  $wrapper_content_close = '</div></div></section>';
  break;
default:
  $wrapper_content_close = ! Plethora_Theme::content_has_sections() ? '</div></div></section>': '';
  break;
}
echo apply_filters( 'plethora_wrapper_content_close', $wrapper_content_close );
 
Plethora_Theme::dev_comment('   << END ========================= CONTENT AREA ========================', 'layout');