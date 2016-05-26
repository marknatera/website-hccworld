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

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Teaserimage') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_Teaserimage extends Plethora_Shortcode { 

      public $wp_slug                      = 'teaserimage';             // This should be the WP slug of the content element ( WITHOUT the prefix constant )
      public static $feature_title         = "Teaser Image Shortcode";  // FEATURE DISPLAY TITLE 
      public static $feature_description   = "";                        // FEATURE DISPLAY DESCRIPTION
      public static $theme_option_control  = true;                      // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL 
      public static $theme_option_default  = true;                      // DEFAULT ACTIVATION OPTION STATUS 
      public static $theme_option_requires = array();                   // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct     = true;                      // DYNAMIC CLASS CONSTRUCTION ? ( boolean )
      public static $dynamic_method        = false;                     // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )
      public static $assets;                                            // SCRIPT & STYLE FILES

      public function __construct() {

          // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
          $map = array( 
                      'base'          => SHORTCODES_PREFIX . $this->wp_slug,
                      'name'          => esc_html__('Teaser Image', 'plethora-framework'),
                      'description'   => esc_html__('Image with floating title', 'plethora-framework'),
                      'class'         => '',
                      'weight'        => 1,
                      'category'      => esc_html__('Teasers & Info Boxes', 'plethora-framework'),
                      'icon'          => $this->vc_icon(), 
                      // 'custom_markup' => $this->vc_custom_markup( 'Teaser Image' ), 
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
                      "param_name"    => "image",                                  
                      "type"          => "attach_image",                                        
                      "holder"        => "img",                                               
                      "class"         => "", 
                      "heading"       => esc_html__("Image", 'plethora-framework'),      
                      "value"         => '',
                    ),

                    array(
                        "param_name"    => "image_ratio",
                        "type"          => "dropdown",
                        "heading"       => esc_html__('Image Display Ratio', 'plethora-framework'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'   => 'stretchy_ratios',
                                          'use_in' => 'vc', 
                                          )),            
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
                      "param_name"    => "title",                                  
                      "type"          => "textfield",                                        
                      "holder"        => "h3",                                               
                      "class"         => "",                                                    
                      "heading"       => esc_html__("Title ( no HTML please )", 'plethora-framework'),
                      "value"         => ''                               
                    ),

                    array(
                      "param_name"    => "icon_enable",
                      "type"          => "dropdown",
                      "heading"       => esc_html__('Add title icon', 'plethora-framework'),
                      "value"         => array( 
                                            esc_html__('Yes', 'plethora-framework') => 1,
                                            esc_html__('No', 'plethora-framework')  => 0,
                        ),
                    ),

                    array(
                        "param_name"    => "title_icon",
                        "type"          => "iconpicker",
                        "holder"        => "",                                               
                        "class"         => "vc_hidden",
                        "heading"       => esc_html__('Select Icon', 'plethora-framework'),
                        'settings'   => array(
                          'type'         => 'plethora',
                          'iconsPerPage' => 56, // default 100, how many icons per/page to display
                        ),
                        'dependency'    => array( 
                                            'element' => 'icon_enable', 
                                            'value'   => array('1'),  
                                        )
                    ),
                    array(
                        "param_name"    => "title_class",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Title color set ( applied to background strip )", 'plethora-framework'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'           => 'color_sets', 
                                          'use_in'         => 'vc', 
                                          'append_options' => array( 'transparent' => esc_html__('Transparent', 'plethora-framework') )
                                           )),
                        "description"   => esc_html__("Choose a color setup for this element. Remember: all colors in above options can be configured via the theme options panel", 'plethora-framework'),
                        "admin_label"   => false, 
                    ),

                    array(
                        "param_name"    => "title_transparent",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Title transparency  ( applied to background strip )", 'plethora-framework'),
                        "value"         => array( 
                                              esc_html__('No', 'plethora-framework')  => '', 
                                              esc_html__('Yes', 'plethora-framework') => 'transparent transparent_film', 
                                            ),
                        "description"   => esc_html__("The transparency percentage can be configured on theme options panel", 'plethora-framework'),
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
            'image'             => '',
            'image_ratio'       => 'stretchy_wrapper ratio_16-9',
            'link'              => '#',
            'title'             => '',
            'icon_enable'       => '1',
            'title_icon'        => '',
            'title_class'       => 'skincolored_section',
            'title_transparent' => '',
            ), $atts ) );

          // Prepare final values that will be used in template
          $image = (!empty($image)) ? wp_get_attachment_image_src( $image, 'full' ) : '';
          $image = isset($image[0]) ? $image[0] : '';
          $link = self::vc_build_link($link);

          // Place all values in 'shortcode_atts' variable
          $shortcode_atts = array (
                                  'image'             => esc_attr($image), 
                                  'image_ratio'       => $image_ratio, 
                                  'link'              => $link, 
                                  'title'             => $title, 
                                  'icon_enable'       => ( $icon_enable == 1 ) ? TRUE : "", 
                                  'title_icon'        => esc_attr($title_icon),
                                  'title_class'       => esc_attr($title_class),  
                                  'title_transparent' => esc_attr($title_transparent), 
                                  'url'               => !empty($link['url']) ? esc_attr( $link['url'] ) : '#'
                                 );

          return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );

       }
	}
	
 endif;