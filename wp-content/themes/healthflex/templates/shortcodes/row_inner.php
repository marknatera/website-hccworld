<?php
/**
 * Inner Row template
 */
// Get attributes sent by set_query_var
$atts = get_query_var( 'shortcode_atts' );
// Extract $atts to variables
if ( is_array( $atts ) ) { extract($atts); }

$output = '';
$output .= '    <div class="row '. esc_attr( implode( ' ', $class ) ) .'">'. "\n";
$output .= Plethora_Shortcode::remove_wpautop( $content );
$output .= '    </div>'. "\n";
echo $output;