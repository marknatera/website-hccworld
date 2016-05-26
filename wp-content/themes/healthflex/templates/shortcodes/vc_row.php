<?php
/**
 * VC Row override template
 */
$output = $after_output = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

wp_enqueue_script( 'wpb_composer_front_js' );

$el_class = trim($this->getExtraClass( $el_class ));

$css_classes = array(
	'vc_row',
	'wpb_row', 				// DEPRECATED
	'vc_row-fluid',
	$color_set,				// COLOR SET
	$align,					// TEXT ALIGN
    $transparent_overlay,   // TRANSPARENT OVERLAY
	$row_padding,			// ROW PADDING
	$cols_valign,			// COLUMNS VERTICAL ALIGN
	$cols_padding,			// COLUMNS PADDING
	$elevate,				// ELEVATE ROW
	$sep_top,
	$sep_bottom,
	$animation,
	$el_class,
	vc_shortcode_custom_css_class( $css ),
);
$wrapper_attributes = array();
// build attributes for wrapper
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"'; 	// ID
}

$css_classes[] = $full_width ==  1 ? 'full_width' : '';

$css_classes[] = $full_height == 1 ? $full_height_valign : '';	// Full Height

$css_classes[] = $background != 'color' ? $background : '';

$has_video_bg = ( $background === 'video' && ! empty( $video_bg_url ) && vc_extract_youtube_id( $video_bg_url ) );

if ( $background === 'color' ) {

  	$css_classes[] = $particles == 1 ? 'particles-js' : '';		// Particles

} elseif ( $background === 'bgimage' ) {

	$css_classes[]        = $parallax ? 'parallax-window' : '';
	$css_classes[]        = !empty($bgimage_valign) ? $bgimage_valign : '';
	$background_image     = (!empty($bgimage)) ? wp_get_attachment_image_src( $bgimage, 'full' ) : '';
	$wrapper_attributes[] = isset($background_image[0]) ? 'style="background-image: url(\''. esc_url( $background_image[0] ) .'\')"' : '';

} elseif ( $background === 'video' && $has_video_bg ) {

	$css_classes[]        = $parallax ? 'parallax-window' : '';
	$css_classes[]        = ' vc_video-bg-container';
	$wrapper_attributes[] = 'data-vc-video-bg="' . esc_attr( $video_bg_url ) . '"';
	wp_enqueue_script( 'vc_youtube_iframe_api_js' );

}
$css_class_arr = array_filter( $css_classes );
$css_class = preg_replace( '/\s+/', ' ', apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( $css_classes ) ), $this->settings['base'], $atts ) );
$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';

$output .= "\n";
$output .= '<section ' . implode( ' ', $wrapper_attributes ) . '>'. "\n";
$output .= apply_filters( 'plethora_section_open', '', $css_class_arr );
$output .= Plethora_Theme::get_layout() === 'no_sidebar' ? '	<div class="container">'. "\n" : '';
$output .= '		<div class="row">'. "\n";
$output .= wpb_js_remove_wpautop( $content );
$output .= '		</div>'. "\n";
$output .= Plethora_Theme::get_layout() === 'no_sidebar' ? '	</div>'. "\n" : '';
$output .= apply_filters( 'plethora_section_close', '', $css_class_arr );
$output .= '</section>'. "\n";
$output .= "\n";
$output .= $after_output;

echo $output;