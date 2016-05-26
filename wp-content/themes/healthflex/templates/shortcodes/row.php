<?php
/**
 * Row template
 */
// Get attributes sent by set_query_var
$atts = get_query_var( 'shortcode_atts' );
// Extract $atts to variables
if ( is_array( $atts ) ) { extract($atts); }

$output = "\n";
$output .= '<section class="' . esc_attr( implode( ' ', $class ) ) . '" style="'. esc_attr( implode( ' ', $class ) ) . '" id="'. esc_attr( $id ) .'">'. "\n";
$output .= apply_filters( 'plethora_section_open', '', $class );
$output .= Plethora_Theme::get_layout() === 'no_sidebar' ? '	<div class="container">'. "\n" : '';
$output .= '		<div class="row">'. "\n";
$output .= Plethora_Shortcode::remove_wpautop( $content );
$output .= '		</div>'. "\n";
$output .= Plethora_Theme::get_layout() === 'no_sidebar' ? '	</div>'. "\n" : '';
$output .= apply_filters( 'plethora_section_close', '', $class );
$output .= '</section>'. "\n";
$output .= "\n";

echo $output;