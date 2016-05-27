<?php
/**
 * VC Inner Column override template
 */
$output = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$width = wpb_translateColumnWidthToSpan( $width );
$width = vc_column_offset_class_merge( $offset, $width );

$css_classes = array(
    $this->getExtraClass( $el_class ),
    'wpb_column',
    'vc_column_container',
    $width,
    $color_set,             
    $transparent_overlay,   
    $margin,                
    $boxed,                 
    $align,                 
    $elevate,               
    $same_height_col,       
    $animation,               
    vc_shortcode_custom_css_class( $css ),
);

$wrapper_attributes = array();

if ( $background === 'bgimage' ) {

    $css_classes[]        = !empty($bgimage_valign) ? $bgimage_valign : '';
    $background_image     = (!empty($bgimage)) ? wp_get_attachment_image_src( $bgimage, 'full' ) : '';
    $wrapper_attributes[] = isset($background_image[0]) ? 'style="background-image: url(\''. esc_url( $background_image[0] ) .'\')"' : '';

}

$css_class = preg_replace( '/\s+/', ' ', apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( $css_classes ) ), $this->settings['base'], $atts ) );
$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';

$output .= '            <div ' . implode( ' ', $wrapper_attributes ) . '>'. "\n";
$output .= '                <div class="wpb_wrapper">'. "\n";
if( !empty( $heading ) ) {
    $output .= '                <h3 class="col_header '. esc_attr( $heading_align ) .'">'. esc_html( $heading ) .'</h3>'. "\n";
}
$output .= wpb_js_remove_wpautop( $content ). "\n";
$output .= '                </div>'. "\n";
$output .= '            </div>'. "\n";

echo $output;