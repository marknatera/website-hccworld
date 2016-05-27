<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Row shortcode

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Vcrow') ):

	/**
	 * @package Plethora
	 */

	class Plethora_Shortcode_Vcrow extends Plethora_Shortcode { 

        public static $feature_title         = "Row Shortcode";       // Feature display title  (string)
        public static $feature_description   = "";                    // Feature display description (string)
        public static $theme_option_control  = false;                 // Will this feature be controlled in theme options panel ( boolean )
        public static $theme_option_default  = true;                  // Default activation option status ( boolean )
        public static $theme_option_requires = array();               // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
        public static $dynamic_construct     = true;                  // Dynamic class construction ? ( boolean )
        public static $dynamic_method        = false;                 // Additional method invocation ( string/boolean | method name or false )

        public function __construct() {

            if ( function_exists('vc_remove_param') && function_exists('vc_add_params') ) { 
                // Remove native parameters setup
                vc_remove_param ( 'vc_row', 'gap' );                // VC 4.9.1 param remove temporarily
                vc_remove_param ( 'vc_row', 'equal_height' );       // VC 4.9.1 param remove temporarily
                vc_remove_param ( 'vc_row', 'columns_placement' );  // VC 4.9.1 param remove temporarily
                vc_remove_param ( 'vc_row', 'full_width' );
                vc_remove_param ( 'vc_row', 'full_height' );
                vc_remove_param ( 'vc_row', 'content_placement' );
                vc_remove_param ( 'vc_row', 'video_bg' );
                vc_remove_param ( 'vc_row', 'video_bg_url' );
                vc_remove_param ( 'vc_row', 'video_bg_parallax' );
                vc_remove_param ( 'vc_row', 'parallax' );
                vc_remove_param ( 'vc_row', 'parallax_image' );
                // Add Plethora parameters setup
                vc_add_params( 'vc_row', $this->params() );
                
            } else {

              // MAP SHORTCODE SETTINGS ACCORDING TO VC DOCUMENTATION ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
              $map = array( 
                          'base'          => 'vc_row',
                          );
              $this->add( $map );          // ADD SHORTCODE

            }
 
            // PLEFIXME: temporary themeconfig workaround
            Plethora_Theme::set_themeconfig( "PARTICLES", array(
                    'enable' => true,
                    'color' => "#bcbcbc",
                    'opacity' => 0.8,
                    'bgColor' => "transparent",
                    'bgColorDark' => "transparent",
                    'colorParallax' => "#4D83C9",
                    'bgColorParallax' => "transparent",
            ));
            add_action('wp_insert_post', array($this, 'content_has_sections'), 999, 3);
      }


       /** 
       * Returns shortcode settings (compatible with Visual composer)
       *
       * @return array
       * @since 1.0
       *
       */
       public function params() {

          $params =  array(

                    array(
                        "param_name"    => "color_set",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Color Set", 'healthflex'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'color_sets', 
                                          'use_in'          => 'vc',
                                          'prepend_default' => true
                                           )),
                        "description"   => esc_html__("Choose a color setup for this section. Remember: all colors in above options can be configured via the theme options panel", 'healthflex'),
                        "admin_label"   => false, 
                    ),

                    // Start -> Layout Options
                    array(
                        "param_name"    => "align",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Text Align", 'healthflex'),
                        "value"         => array( 
                                              esc_html('Default', 'healthflex')     =>'',
                                              esc_html('Left', 'healthflex')     =>'text-left',
                                              esc_html('Centered', 'healthflex') => 'text-center',
                                              esc_html('Right', 'healthflex')    => 'text-right',
                                              esc_html('Justify', 'healthflex')  => 'text-justify',
                          ),
                        "admin_label"   => false, 
                    ),

                    array(
                        "param_name"    => "full_width",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Full Width", 'healthflex'),
                        "value"         => array(
                                                esc_html('No', 'healthflex')  => 0, 
                                                esc_html('Yes', 'healthflex') => 1, 
                                            ),
                        "description"   => esc_html__("Expand row content to capture the entire screen width.", 'healthflex'),
                        "admin_label"   => false 
                    ),

                    array(
                        "param_name"    => "full_height",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Full Height", 'healthflex'),
                        "value"         => array(
                                                esc_html('No', 'healthflex') => 0, 
                                                esc_html('Yes', 'healthflex') => 1, 
                                            ),
                        "description"   => esc_html__("Full height will set the minimum row height same to the screen viewport", 'healthflex'),
                        "admin_label"   => false 
                    ),
                    array(
                        "param_name"    => "full_height_valign",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Full Height Content Vertical Align", 'healthflex'),
                        "value"         => array(
                                                esc_html('Vertical Top', 'healthflex') => 'full_height', 
                                                esc_html('Vertical Center', 'healthflex') => 'full_height vertical_center', 
                                                esc_html('Vertical Bottom', 'healthflex') => 'full_height vertical_bottom', 
                                            ),
                        "admin_label"   => false, 
                        "dependency"    => array(
                                                "element" => "full_height",
                                                "value"   => array('1')
                                           ),
                    ),
                    array(
                        "param_name"    => "row_padding",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Row Padding", 'healthflex'),
                        "value"         => array(
                                                esc_html('Default padding', 'healthflex') => '', 
                                                esc_html('No padding', 'healthflex') => 'no_padding', 
                                                esc_html('No top padding', 'healthflex') => 'no_top_padding', 
                                                esc_html('No bottom padding', 'healthflex') => 'no_bottom_padding', 
                                            ),
                        "description"   => esc_html__("Affects the row's vertical spacings", 'healthflex'),
                        "admin_label"   => false, 
                    ),
                    array(
                        "param_name"    => "cols_valign",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Columns Vertical Align", 'healthflex'),
                        "value"         => array(
                                                esc_html('Align columns to their vertical top', 'healthflex') => '', 
                                                esc_html('Align columns to their vertical centers', 'healthflex') => 'vcenter', 
                                                esc_html('Align columns to their vertical bottom', 'healthflex') => 'vbottom', 
                                            ),
                        "admin_label"   => false, 
                    ),
                     array(
                        "param_name"    => "cols_padding",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Columns Padding", 'healthflex'),
                        "value"         => array(
                                                esc_html('Yes', 'healthflex') => '', 
                                                esc_html('No', 'healthflex') => 'no_cols_padding', 
                                            ),
                        "description"   => esc_html__("Can disable horizontal padding for the columns of this row", 'healthflex'),
                        "admin_label"   => false, 
                    ),

                    array(
                        "param_name"    => "elevate",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Elevate Row", 'healthflex'),
                        "value"         => array(
                                                esc_html('No', 'healthflex') => '', 
                                                esc_html('Yes', 'healthflex') => 'elevate', 
                                            ),
                        "description"   => esc_html__("Will elevate this row higher", 'healthflex'),
                        "admin_label"   => false, 
                    ),
                    array(
                        "param_name"    => "sep_top",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Top Separator", 'healthflex'),
                        "value"         => array(
                                                esc_html('No', 'healthflex') => '', 
                                                esc_html('Angled Positive', 'healthflex') => 'separator_top sep_angled_positive_top',
                                                esc_html('Angled Negative', 'healthflex') => 'separator_top sep_angled_negative_top',
                                            ),
                        "description"   => esc_html__("Will put an angled separator on top of the section", 'healthflex'),
                        "admin_label"   => false, 
                    ),
                    array(
                        "param_name"    => "sep_bottom",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Bottom Separator", 'healthflex'),
                        "value"         => array(
                                                esc_html('No', 'healthflex') => '', 
                                                esc_html('Angled Positive', 'healthflex') => 'separator_bottom sep_angled_positive_bottom',
                                                esc_html('Angled Negative', 'healthflex') => 'separator_bottom sep_angled_negative_bottom',
                                            ),
                        "description"   => esc_html__("Will put an angled separator on the bottom of the section", 'healthflex'),
                        "admin_label"   => false, 
                    ),
                    // End -> Layout Options

                    // Start -> Color Set & Image Background Options
                    array(
                        "param_name"    => "transparent_overlay",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Transparent Overlay", 'healthflex'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'transparent_overlay', 
                                          'use_in'          => 'vc',
                                          'prepend_default' => true
                                           )),
                        "description"   => esc_html__("The transparency percentage can be configured on theme options panel", 'healthflex'),
                        "admin_label"   => false, 
                    ),                    
                    // End -> Color Set & Image Background Options

                    // BACKGROUND PICKER
                    array(
                        "param_name"    => "background",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Background Type", 'healthflex'),
                        "value"         => array( 
                                              esc_html('Solid Color Set Background ( requires a color set selection above )', 'healthflex') => 'color', 
                                              // esc_html('Gradient Angled Background ( primary/secondary colors )', 'healthflex') => 'gradient', 
                                              esc_html('Image', 'healthflex') => 'bgimage', 
                                              esc_html('Video', 'healthflex') => 'video', 
                                              esc_html('Transparent', 'healthflex') => 'transparent' ,
                                          ),
                        "description"   => esc_html__("For image backgrounds, please PREFER THIS option rather the one available on 'Design' tab", 'healthflex'),
                        "admin_label"   => false 
                    ),
                    // END BACKGROUND PICKER

                    // Start -> Solid Color Background Options

                    array(
                        "param_name"    => "particles",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Particles Effect", 'healthflex'),
                        "value"         => array(
                                                esc_html('No', 'healthflex') => 0, 
                                                esc_html('Yes', 'healthflex') => 1, 
                                            ),
                        "admin_label"   => false, 
                        "dependency"    => array(
                                                "element" => "background",
                                                "value"   => array("color")
                                           ),
                    ),
                    // End -> Solid Color Background Options

                    // Start -> Image Background Options

                    array(
                        "param_name"    => "bgimage",
                        "type"          => "attach_image",
                        "heading"       => esc_html__("Background Image", 'healthflex'),
                        "value"         => '',
                        "description"   => esc_html__("Upload/select a background image for this section", 'healthflex'),
                        "admin_label"   => false, 
                        "dependency"    => array(
                                                "element" => "background",
                                                "value"   => array("bgimage")
                                           ),
                    ),

                    array(
                        "param_name"    => "parallax",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Image Parallax", 'healthflex'),
                        "value"         => array(
                                                esc_html('No', 'healthflex') => 0, 
                                                esc_html('Yes', 'healthflex') => 1, 
                                            ),
                        "admin_label"   => false, 
                        "dependency"    => array(
                                                "element" => "background",
                                                "value"   => array( 'bgimage')
                                           ),
                    ),                    

                    array(
                        "param_name"    => "bgimage_valign",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Background Image Vertical Align", 'healthflex'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'bgimage_valign', 
                                          'use_in'          => 'vc',
                                           )),

                        "admin_label"   => false, 
                        "dependency"    => array(
                                                "element" => "background",
                                                "value"   => array( 'bgimage')
                                           ),
                    ),                    

                    // End -> Image Background Options

                    // Start -> Video Background Options

                    array(
                        "param_name"    => "video_bg_url",
                        "type"          => "textfield",
                        "heading"       => esc_html__("YouTube link", 'healthflex'),
                        "admin_label"   => false, 
                        "dependency"    => array(
                                                "element" => "background",
                                                "value"   => array('video')
                                           ),
                    ),
                    // End -> Video Background Options

                    array(
                        "param_name"    => "animation",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Animation", 'healthflex'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'animations', 
                                          'use_in'          => 'vc',
                                          'prefix_all_values' => 'wow',
                                          'prepend_default' => true,
                                          'default_title'   => esc_html('None', 'healthflex')
                                           )),
                    ),


          );

          return $params;
       }

       /** 
       * Returns shortcode content
       *
       * @return array
       * @since 1.0
       *
       */
       public function content( $atts, $content = null ) {

            extract( shortcode_atts( array( 
              'color_set'           => '',
              'align'               => '',
              'full_width'          => 0,
              'full_height'         => 0,
              'full_height_valign'  => 'full_height',
              'row_padding'         => '',
              'cols_valign'         => '',
              'cols_padding'        => '',
              'elevate'             => 0,
              'sep_top'             => '',
              'sep_bottom'          => '',
              'transparent_overlay' => '',
              'background'          => 'color',
              'bgimage'             => '',
              'parallax'            => 0,
              'bgimage_valign'      => 0,
              'video_bg_url'        => '',
              'particles'           => 0,
              'animation'           => 0,
              'extra_class'         => '',
              'section_id'          => ''
              ), $atts ) );

            $style          = array();
            $class          = array();
            $shortcode_atts = array();

            $class[] = $color_set;
            $class[] = $align;
            $class[] = $full_width ==  1 ? 'full_width' : '';
            $class[] = $full_height == 1 ? $full_height_valign : '';
            $class[] = $elevate == 1 ? 'elevate' : ''; // PLENOTE: Remove this if we are OK with putting the elevate class in the inner .row
            $class[] = !empty($row_padding) ? $row_padding : '';
            $class[] = !empty($cols_valign) ? $cols_valign : '';
            $class[] = !empty($cols_padding) ? $cols_padding : '';

            // BACKGROUND CLASSES
            $class[] = $background != 'color' ? $background : '';
            // if background is image
            if ( $background === 'color' ) {

              $class[] = $particles == 1 ? 'particles-js' : '';
              $class[] = !empty($transparent_overlay) ? $transparent_overlay : '';

            // if background is image
            } elseif ( $background === 'transparent' ) {

              $class[] = !empty($transparent_overlay) ? $transparent_overlay : '';

            } elseif ( $background === 'bgimage' ) {

              $class[] = !empty($parallax) ? 'parallax-window' : '';
              $class[] = !empty($transparent_overlay) ? $transparent_overlay : '';
              $background_image = (!empty($background_image)) ? wp_get_attachment_image_src( $background_image, 'full' ) : '';
              $style[] = isset($background_image[0]) ? "background-image: url('". esc_url( $background_image[0] ) ."')" : '';

            } elseif ( $background === 'gradient' && $angles_separation === 'diagonal' ) {
              
              $class[] = $angles_placement === 'invert' ? 'gradient-invert' : '';
              $diagonal_class = $angles_separation .'-'. $angles_proportion ;
              $diagonal_class .= $angles_reverse === 'minusangle' ? '-'. $angles_reverse : '';
              $diagonal_class .= $angles_placement === 'invert' ? '-'. $angles_placement : '';
              $class[] = $diagonal_class;

            // if background is colored angles and right angle
            } elseif ( $background === 'gradient' && $angles_separation !== 'diagonal' ) {
              
              $class[] = $angles_placement === 'invert' ? 'gradient-invert' : '';
            }

            // EXTRA CLASSES & STYLE
            $class[] = !empty($extra_class) ? $extra_class : '';
            $style   = !empty($style) ? $style : array();

            // PREPARE VALUES FOR TEMPLATE
            $shortcode_atts['class']   = array_filter($class, 'esc_attr');
            $shortcode_atts['style']   = array_filter($style, 'esc_attr');
            $shortcode_atts['id']      = !empty($section_id) ? esc_attr( $section_id ) : '';
            $shortcode_atts['content'] = do_shortcode( $content );

            // Transfer prepared values using the 'set_query_var' ( this will make them available via 'get_query_var' to the template part file )
            set_query_var( 'shortcode_atts', $shortcode_atts );
            // Get the template part
            ob_start();
            Plethora_WP::get_template_part( 'templates/shortcodes/row' );
            return ob_get_clean();       
       }




       /** 
       * Save the 'content_has_sections' post meta option, that affects the markup exported. 
       *
       * @return array
       * @since 1.0
       *
       */
       public function content_has_sections( $post_id, $post, $update ) {

      // If this is a revision, get real post ID
        if ( $parent_id = wp_is_post_revision( $post_id ) ) {

                $post_id = $parent_id;
        }

        if ( $post->post_type === 'post' || $post->post_type === 'page'  ) {
            
            $content = ( !empty( $post->post_content ) ) ? $post->post_content : '' ;
            
            // delete first, to avoid duplicate values that might be created from import process
            delete_post_meta( $post_id, METAOPTION_PREFIX .'content_has_sections' );
            // VERY IMPORTANT: Will use our own has_shortcode implementation, as we want to make a check
            // even if the shortcode has not been registered yet ( i.e. this happens during import process)
            if ( Plethora_Shortcode::has_shortcode( $content, 'vc_row' )) { 

              update_post_meta( $post_id, METAOPTION_PREFIX .'content_has_sections', true );

            } else {

              update_post_meta( $post_id, METAOPTION_PREFIX .'content_has_sections', false );
            }
        }
       }
	}
	
 endif;