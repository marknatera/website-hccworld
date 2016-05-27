<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2015

File Description: Single Post Template Parts / Tag labels
*/

$output = '';
$tags = get_the_tags();
if ( $tags ) {

  foreach($tags as $key=>$tag) {
    $output .= '<a href="'.get_tag_link( $tag->term_id ).'" title="' . esc_attr( sprintf( esc_html__( "View all posts tagged with: %s", 'healthflex' ), $tag->name ) ) . '" class="label skincolored_section post_tags"><strong>'. esc_html( $tag->name ) .'</strong></a>';
  }
}
$output = '<div id="tags" class="skincolored_section transparent">'. $output .'</div>'; 
echo $output;