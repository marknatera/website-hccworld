<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2013

File Description: Inner Column shortcode

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Vccolumninner') ):

	/**
	 * @package Plethora
	 */

	class Plethora_Shortcode_Vccolumninner extends Plethora_Shortcode { 

      public static $feature_title          = "Inner Column Shortcode";   // FEATURE DISPLAY TITLE
      public static $feature_description    = "";                         // FEATURE DISPLAY DESCRIPTION 
      public static $theme_option_control   = false;                      // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL 
      public static $theme_option_default   = true;                       // DEFAULT ACTIVATION OPTION STATUS 
      public static $theme_option_requires  = array();                    // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct      = true;                       // DYNAMIC CLASS CONSTRUCTION ? 
      public static $dynamic_method         = false;                      // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )

    	
      public function __construct() {

        if ( function_exists('vc_add_params') ) { 
          // Add Plethora parameters setup
          vc_add_params( 'vc_column_inner', $this->params() );
        
        }else {

          // MAP SHORTCODE SETTINGS ACCORDING TO VC DOCUMENTATION ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
          $map = array( 
                      'base'          => 'vc_column_inner',
                      );
          $this->add( $map );          // ADD SHORTCODE

        }
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
                        "param_name"    => "heading",
                        "type"          => "textfield",
                        "heading"       => esc_html__("Heading Title", 'healthflex'),
                        "admin_label"   => false, 
                    ),
                    array(
                        "param_name"    => "heading_align",
                        "type"          => "dropdown",
                        "heading"       => esc_html('Heading Align', 'healthflex'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'text_align', 
                                          'use_in'          => 'vc',
                                           )),
                    ),
                    array(
                        "param_name"    => "align",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Content Align", 'healthflex'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'text_align', 
                                          'use_in'          => 'vc',
                                          'prepend_default' => true,
                                          'default_title'   => esc_html('Inherit', 'healthflex')
                                           )),
                        "admin_label"   => false, 
                    ),

                    array(
                        "param_name"    => "color_set",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Color Set", 'healthflex'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'color_sets', 
                                          'use_in'          => 'vc',
                                          'prepend_default' => true
                                           )),
                        "description"   => esc_html__("Color setup affects text, link & background color. Those colors can be configured on theme options panel", 'healthflex'),
                        "admin_label"   => false, 
                    ),
                    array(
                        "param_name"    => "background",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Background Type", 'healthflex'),
                        "value"         => array( 
                                              esc_html('Color Set Background', 'healthflex') => '', 
                                              esc_html('Image', 'healthflex') => 'bgimage', 
                                              esc_html('Transparent', 'healthflex') => 'transparent' ,
                                          ),
                        // "description"   => esc_html__("You may customize the background color as you like on <strong>'Design Options'</strong> tab ", 'healthflex'),
                        "admin_label"   => false 
                    ),

                    // Start -> Image Background Options

                    array(
                        "param_name"    => "bgimage",
                        "type"          => "attach_image",
                        "heading"       => esc_html__("Background Image", 'healthflex'),
                        "value"         => '',
                        "description"   => esc_html__("Upload/select a background image for this column", 'healthflex'),
                        "admin_label"   => false, 
                        "dependency"    => array(
                                                "element" => "background",
                                                "value"   => array("bgimage")
                                           ),
                    ),

                    array(
                        "param_name"    => "bgimage_valign",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Bacground Image Vertical Align", 'healthflex'),
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

                   array(
                        "param_name"    => "transparent_overlay",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Transparent Overlay", 'healthflex'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'transparent_overlay', 
                                          'use_in'          => 'vc',
                                          'prepend_default' => true,
                                          'default_title'   => esc_html('None', 'healthflex')
                                           )),
                        "description"   => esc_html__("The transparency percentage can be configured on theme options panel", 'healthflex'),
                        "admin_label"   => false, 
                    ),                    

                    array(
                        "param_name"    => "boxed",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Boxed Design", 'healthflex'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'boxed', 
                                          'use_in'          => 'vc',
                                          'prepend_default' => true,
                                          'default_title'   => esc_html('No', 'healthflex')
                                           )),
                        "description"   => esc_html__("Boxed designs will add an inner padding and some additional styling features", 'healthflex'),
                        "admin_label"   => false, 
                    ),
                    array(
                        "param_name"    => "margin",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Responsive Margin (SM)", 'healthflex'),
                        "value"         => array(
                                                esc_html('No Margin', 'healthflex') => '', 
                                                esc_html('Top Margin', 'healthflex') => 'margin_top_grid', 
                                                esc_html('Bottom Margin', 'healthflex') => 'margin_bottom_grid', 
                                                esc_html('Top & Bottom Margin', 'healthflex') => 'margin_top_grid margin_bottom_grid', 
                                            ),
                        "description"   => esc_html__("Will put a margin on the column at the small responsive state", 'healthflex'),
                        "admin_label"   => false, 
                    ),
                     array(
                        "param_name"    => "elevate",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Elevate Column", 'healthflex'),
                        "value"         => array(
                                                esc_html('No', 'healthflex') => '', 
                                                esc_html('Yes', 'healthflex') => 'elevate', 
                                            ),
                        "description"   => esc_html__("Will elevate this column axis higher than the rest of this row", 'healthflex'),
                        "admin_label"   => false, 
                    ),
                    array(
                        "param_name"    => "same_height_col",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Equal Height Column", 'healthflex'),
                        "value"         => array(
                                                esc_html('No', 'healthflex') => '', 
                                                esc_html('Yes', 'healthflex') => 'same_height_col', 
                                            ),
                        "description"   => esc_html__("Will make equalize the height of this column with all the rest row columns that have the same setting", 'healthflex'),
                        "admin_label"   => false, 
                    ),
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
                    array(
                        "param_name"    => "el_class",                                  
                        "type"          => "textfield",                                    
                        "heading"       => esc_html__("Extra class(es)", 'healthflex'),       
                        "description"   => esc_html__("Separate classes ONLY with space", 'healthflex'),
                        "value"         => '',                                   
                        "admin_label"   => false,                                           
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
                'heading'             => '',
                'heading_align'       => 'centered',
                'width'               => '1/1',
                'color_set'           => '',
                'background'          => '',
                'background_image'    => '',
                'transparent_overlay' => '',
                'boxed'               => '',
                'align'               => '',
                'elevate'             => '',
                'same_height_col'     => '',
                'extra_class'         => '',
            ), $atts ) );

            # PREPARE VALUES FOR TEMPLATE

            // Calculate column size ( according to given width )
            $fraction = array('whole' => 0, 'numerator'=> 0, 'denominator'=> 0);
            preg_match('/^((?P<whole>\d+)(?=\s))?(\s*)?(?P<numerator>\d+)\/(?P<denominator>\d+)$/', $width, $fraction);
            $sum_numdenom = ( $fraction['numerator'] != 0 ) ? $fraction['numerator'] / $fraction['denominator'] : 0;
            $decimal_width = $fraction['whole'] + $sum_numdenom;

            // if background is image, then add background-image inline style
            $class[] = $background != 'color' ? $background : '';
            if ( $background === 'bgimage' ) {
              $background_image = (!empty($background_image)) ? wp_get_attachment_image_src( $background_image, 'full' ) : '';
              $style[] = isset($background_image[0]) ? "background-image: url('". esc_url( $background_image[0] ) ."')" : '';
              $style[] = 'height: auto';
            }

            $class = array();
            $class[] = 'col-md-' . floor( $decimal_width * 12 );
            $class[] = 'col-sm-' . floor( $decimal_width * 12 );  // PLENOTE: What about col-xs-xx here?
            $class[] = $color_set;
            $class[] = $transparent_overlay;
            $class[] = $boxed;
            $class[] = $align;
            $class[] = $elevate;
            $class[] = $same_height_col;
            $class[] = $extra_class; // EXTRA CLASSES & STYLE
            $style   = !empty($style) ? $style : array();

            $shortcode_atts = array();
            $shortcode_atts['class'] = array_filter($class, 'esc_attr');
            $shortcode_atts['style'] = array_filter($style, 'esc_attr');
            $shortcode_atts['heading']['title'] = $heading;
            $shortcode_atts['heading']['align'] = esc_attr( $heading_align );
            $shortcode_atts['content'] = do_shortcode( $content );

            // Transfer prepared values using the 'set_query_var' ( this will make them available via 'get_query_var' to the template part file )
            set_query_var( 'shortcode_atts', $shortcode_atts );
            // Get the template part
            ob_start();
            Plethora_WP::get_template_part( 'templates/shortcodes/column_inner' );
            return ob_get_clean();       

       }

  }
  
 endif;