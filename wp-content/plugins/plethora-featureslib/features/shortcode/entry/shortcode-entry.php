<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2013

File Description: Entry shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Entry') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_Entry extends Plethora_Shortcode { 

      public static $feature_title         = "Entry Shortcode"; // FEATURE DISPLAY TITLE 
      public static $feature_description   = "";                // FEATURE DISPLAY DESCRIPTION 
      public static $theme_option_control  = true;              // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL 
      public static $theme_option_default  = true;              // DEFAULT ACTIVATION OPTION STATUS
      public static $theme_option_requires = array();           // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct     = true;              // DYNAMIC CLASS CONSTRUCTION ? 
      public static $dynamic_method        = false;             // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )
      public $wp_slug                      = 'entry';           // SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT the prefix constant )
      public static $assets;

      public function __construct() {

          // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
          $map = array( 
                      'base'          => SHORTCODES_PREFIX . $this->wp_slug,
                      'name'          => esc_html__('Entry', 'plethora-framework'),
                      'description'   => esc_html__('Image/icon and content', 'plethora-framework'),
                      'class'         => '',
                      'weight'        => 1,
                      'category'      => esc_html__('Teasers & Info Boxes', 'plethora-framework'),
                      'icon'          => $this->vc_icon(), 
                      // 'custom_markup' => $this->vc_custom_markup( 'Entry' ), 
                      'params'        => $this->params(), 
                      );
          // Add the shortcode
          $this->add( $map );
    	}

       /** 
       * Returns shortcode settings (compatible with Visual composer)
       *
       * @return array
       * @since 1.0
       *
       */
       public function params() {

          $params = array(

                   array(
                      "param_name"    => "content",                                  
                      "type"          => "textfield",                                        
                      'admin_label' => false,
                      'holder'      => 'h3',                                               
                      'class'       => 'plethora_vc_title',                                                    
                      "heading"       => esc_html__("Title ( accepts HTML )", 'plethora-framework'),
                      "value"         => '',                                     
                  ),

                   array(
                      "param_name"  => "media_type",
                      "type"        => "dropdown",
                      'admin_label' => false,
                      "holder"      => "",                                               
                      "class"       => "plethora_vc_image", 
                      "heading"     => esc_html__('Select Media Type', 'plethora-framework'),
                      "value" => array( 
                          esc_html__('Image', 'plethora-framework') =>'image',
                          esc_html__('Icon', 'plethora-framework')  => 'icon'
                          ),
                    ),
                    /*** ICON PICKER ***/
                    array(
                        "param_name"    => "icon",
                        "type"          => "iconpicker",
                        "holder"        => "",                                               
                        "class"         => "vc_hidden",
                        "heading"       => '',
                        "description"   => esc_html__("Select icon to display.", 'plethora-framework'),
                        'settings'   => array(
                          'type'         => 'plethora',
                          'iconsPerPage' => 56, // default 100, how many icons per/page to display
                        ),
                        'dependency'    => array( 
                                            'element' => 'media_type', 
                                            'value'   => array('icon'),  
                        )
                    ),
                    array(
                      "param_name"    => "image",                                  
                      "type"          => "attach_image",                                        
                      "holder"        => "img",                                               
                      "class"         => "vc_hidden", 
                      "heading"       => '',      
                      "value"         => '',
                      "admin_label"   => false,                                              
                      'dependency'    => array( 
                                            'element' => 'media_type', 
                                            'value'   => array('image'),  
                                        )
                  ),
                  array(
                      "param_name"    => "link",
                      "type"          => "vc_link",
                      "holder"        => "",
                      "class"         => "vc_hidden", 
                      "heading"       => esc_html__("Link", 'plethora-framework'),
                      "value"         => '#',
                      "admin_label"   => false                                               
                 ),

                  array(
                      "param_name"    => "media_ratio",
                      "type"          => "value_picker",
                      "heading"       => esc_html__('Media Display Ratio (for images)', 'plethora-framework'),
                      "picker_type"   => "single",  // Multiple or single class selection ( 'single'|'multiple' )
                      "picker_cols"   => "6",         // Picker columns for selections display ( 1, 2, 3, 4, 6 )                                       
                      "value" => 'stretchy_wrapper ratio_16-9',     
                      "values_index" => Plethora_Module_Style::get_options_array( array( 
                                        'type'   => 'stretchy_ratios', 
                                        'use_in' => 'vc', 
                                        )),            
                  ),
                  array(
                        "param_name"    => "image_valign",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Media Vertical Align", 'plethora-framework'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            =>'bgimage_valign',
                                          'use_in'          => 'vc',
                                          )), 
                        "admin_label"   => false, 
                        'dependency'    => array( 
                                            'element' => 'media_type', 
                                            'value'   => array('image'),  
                                        )
                  ),

                  array(
                      "param_name"    => "class",
                      "type"          => "value_picker",
                      "heading"       => esc_html__("Color Set", 'plethora-framework'),
                      "picker_cols"   => "4",         // Picker columns for selections display ( 1, 2, 3, 4, 6 )  
                      "value"         => 'skincolored_section',                            
                      "values_index" => Plethora_Module_Style::get_options_array( array( 
                                        'type'   => 'color_sets', 
                                        'use_in' => 'vc', 
                                        ) ),            // Title=>value array with all values to display
                      "description"   => esc_html__("Choose a color setup for this element. Remember: all colors in above options can be configured via the theme options panel", 'plethora-framework'),
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

          // Extract user input
          extract( shortcode_atts( array( 
            'media_type'   => 'image',
            'image'        => '',
            'icon'         => '',
            'link'         => '',
            'title'        => '',
            'class'        => 'skincolored_section',
            'media_ratio'  => 'stretchy_wrapper ratio_16-9',
            'image_valign' => 'bg_vcenter',
            ), $atts ) );

          // Prepare final values that will be used in template
          $image = (!empty($image)) ? wp_get_attachment_image_src( $image, 'full' ) : '';
          $image = isset($image[0]) ? $image[0] : '';
          $link  = self::vc_build_link($link);

          // Place all values in 'shortcode_atts' variable
          $shortcode_atts = array (
                                  'media_type'  => $media_type, 
                                  'title'       => $title,
                                  'class'       => esc_attr( $class ),  // FILTERING VALUE FOR MUSTACHE TEMPLATE
                               // 'class'       => $class,  
                               // 'content'     => $content, 
                               // 'image'       => $image, 
                               // 'icon'        => $icon, 
                               // 'link'        => $link, 
                               // 'media_ratio' => $media_ratio, 
                                 );


          /* FILTERING VALUES FOR MUSTACHE TEMPLATE */

          if ( !empty( $link['url'] ) ) { 

            $shortcode_atts['link'] = array(

              'url'    => esc_url( $link['url'] ),
              'target' => !empty( $link['target'] ) ? esc_attr( trim( $link['target']) ) : '_self',
              'title'  => esc_attr( trim( $link['title']) )

            );

          }

          if ( $media_type === 'image' && !empty($image) ) { 
              
            $shortcode_atts['image']        = esc_url( $image );
            $shortcode_atts['media_ratio']  = esc_attr( $media_ratio );
            $shortcode_atts['image_valign'] = esc_attr( $image_valign );

          } elseif ( $media_type === 'icon' && !empty( $icon ) ) { 

            $shortcode_atts['icon']        = esc_attr( $icon );
            $shortcode_atts['media_ratio'] = esc_attr( $media_ratio );

          };

         if ( !empty( $content ) ) {

          $shortcode_atts['content'] = $content;

         };

          return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );

       }
	}
	
 endif;