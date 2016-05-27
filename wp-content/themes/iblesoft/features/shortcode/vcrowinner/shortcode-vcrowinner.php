<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Inner Row shortcode

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Vcrowinner') ):

	/**
	 * @package Plethora
	 */

	class Plethora_Shortcode_Vcrowinner extends Plethora_Shortcode { 

        public static $feature_title         = "Inner Row Shortcode";       // Feature display title  (string)
        public static $feature_description   = "";                    // Feature display description (string)
        public static $theme_option_control  = false;                 // Will this feature be controlled in theme options panel ( boolean )
        public static $theme_option_default  = true;                  // Default activation option status ( boolean )
        public static $theme_option_requires = array();               // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
        public static $dynamic_construct     = true;                  // Dynamic class construction ? ( boolean )
        public static $dynamic_method        = false;                 // Additional method invocation ( string/boolean | method name or false )

        public function __construct() {

            if ( !function_exists('vc_map') ) { 

              // MAP SHORTCODE SETTINGS ACCORDING TO VC DOCUMENTATION ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
              $map = array( 
                          'base'          => 'vc_row_inner',
                          );
              $this->add( $map );          // ADD SHORTCODE

            }
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
              'extra_class'                     => '',
              'section_id'                      => ''
              ), $atts ) );

            // EXTRA CLASSES & STYLE
            $class[] = !empty($extra_class) ? $extra_class : '';

            // PREPARE VALUES FOR TEMPLATE
            $shortcode_atts['class']   = array_filter($class, 'esc_attr');
            $shortcode_atts['id']      = !empty($section_id) ? esc_attr( $section_id ) : '';
            $shortcode_atts['content'] = do_shortcode( $content );

            // Transfer prepared values using the 'set_query_var' ( this will make them available via 'get_query_var' to the template part file )
            set_query_var( 'shortcode_atts', $shortcode_atts );
            // Get the template part
            ob_start();
            Plethora_WP::get_template_part( 'templates/shortcodes/row_inner' );
            return ob_get_clean();       
       }
	}
	
 endif;