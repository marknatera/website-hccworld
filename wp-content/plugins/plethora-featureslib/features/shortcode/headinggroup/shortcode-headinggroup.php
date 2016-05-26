<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015 - 2016

File Description: Entry shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Headinggroup') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_Headinggroup extends Plethora_Shortcode { 

      public static $feature_name          = "Heading Group";             // FEATURE DISPLAY TITLE 
      public static $feature_title         = "Heading Group Shortcode";   // FEATURE DISPLAY TITLE 
      public static $feature_description   = "Display Heading Group";     // FEATURE DISPLAY DESCRIPTION 
      public static $theme_option_control  = true;                        // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL 
      public static $theme_option_default  = true;                        // DEFAULT ACTIVATION OPTION STATUS
      public static $theme_option_requires = array();                     // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct     = true;                        // DYNAMIC CLASS CONSTRUCTION ? 
      public static $dynamic_method        = false;                       // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )
      public static $assets                = array();                     // ENQUEUE STYLES AND SCRIPTS
      public $wp_slug                      = 'headinggroup';              // SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT the prefix constant )

      public static $shortcode_category    = "Content";

      public function __construct() {

          // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
          $map = array( 
                      'base'          => SHORTCODES_PREFIX . $this->wp_slug,
                      'name'          => sprintf( esc_html__( '%s', 'plethora-framework' ), self::$feature_name ),
                      'description'   => sprintf( esc_html__( '%s', 'plethora-framework'), self::$feature_description ),
                      'class'         => '',
                      'weight'        => 1,
                      'category'      => sprintf( esc_html__( '%s', 'plethora-framework'), self:: $shortcode_category ),
                      'icon'          => $this->vc_icon(), 
                      // 'custom_markup' => $this->vc_custom_markup( self::$feature_name ), 
                      'params'        => $this->params(), 
                      );
          $this->add( $map );         // ADD ΤΗΕ SHORTCODE
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
                      "type"          => "textarea_html",                                        
                      "holder"        => "div",                                               
                      "class"         => "plethora_vc_title",                                                 
                      "heading"       => esc_html__("Heading Title", 'plethora-framework'),      
                      "value"         => '<h3>'. esc_html__("Heading Title", 'plethora-framework') .'</h3>',
                      "description"   => esc_html__("Set the heading title. Accepts HTML.", 'plethora-framework'),       
                      "admin_label"   => false
                  ),

                  array(
                      "param_name"    => "subtitle",                                  
                      "type"          => "textfield",                                        
                      "holder"        => "h4",                                               
                      "class"         => "plethora_vc_title",                                                 
                      "heading"       => esc_html__("Heading Subtitle", 'plethora-framework'),      
                      "value"         => '',
                      "description"   => esc_html__("Set the heading subtitle", 'plethora-framework'),       
                      "admin_label"   => false,                                              
                  ),

                  array(
                      "param_name"       => "type",
                      "type"             => "value_picker",
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "heading"          => esc_html__('Heading Type', 'plethora-framework'),
                      "picker_type"      => "single",  // Multiple or single class selection ( 'single'|'multiple' )
                      "picker_cols"      => "3",         // Picker columns for selections display ( 1, 2, 3, 4, 6 )                                       
                      "value"            => 'fancy',     
                      "values_index"     => array(        
                                            esc_html__('Default', 'plethora-framework')    => '',
                                            esc_html__('Fancy', 'plethora-framework')      => 'fancy',
                                            esc_html__('Elegant', 'plethora-framework')    => 'elegant',
                                            esc_html__('Extra Bold', 'plethora-framework') => 'xbold',
                                            esc_html__('Thin', 'plethora-framework')       => 'thin',
                                        ),            // Title=>value array with all values to display
                  ),

                  array(
                      "param_name"       => "align",
                      "type"             => "value_picker",
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "heading"          => esc_html__('Heading Subtitle Align', 'plethora-framework'),
                      "picker_type"      => "single",  // Multiple or single class selection ( 'single'|'multiple' )
                      "picker_cols"      => "3",         // Picker columns for selections display ( 1, 2, 3, 4, 6 )                                       
                      "value"            => 'text-center',     
                      "values_index"     => array(        
                                            esc_html__('Left', 'plethora-framework')     => 'text-left',
                                            esc_html__('Centered', 'plethora-framework') => 'text-center',
                                            esc_html__('Right', 'plethora-framework')    => 'text-right',
                                        ),            // Title=>value array with all values to display
                  ),

                  array(
                      "param_name"    => "subtitle_position",
                      "type"          => "dropdown",
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "heading"       => esc_html__('Subtitle Position', 'plethora-framework'),
                      "value"         => array( 
                                        'Bottom'  => 'bottom', 
                                        'Top'     => 'top',
                                      ),
                      "description"   => esc_html__('Choose whether you want the subtitle to be displayed above or below the title.', 'plethora-framework'),       
                  ),

                  array(
                      "param_name"    => "extra_class",                                  
                      "type"          => "textfield",                                        
                      "holder"        => "h4",                                               
                      "class"         => "plethora_vc_title",                                                 
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "heading"       => esc_html__("Add Extra CSS Class to this element", 'plethora-framework'),      
                      "value"         => '',
                      "admin_label"   => false,                                              
                  ),
                  array(
                    "param_name"    => "css",
                    "type"          => "css_editor",
                    'group'         => esc_html__( 'Design options', 'plethora-framework' ),
                    "heading"       => esc_html__('CSS box', 'plethora-framework'),
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

        // EXTRACT USER INPUT
        extract( shortcode_atts( array( 
          'subtitle'          => '',
          'type'              => 'fancy',
          'align'             => 'text-center',
          'subtitle_position' => 'bottom',
          'extra_class'       => '',
          'css'               => '',
        ), $atts ) );

        $content = $this->remove_wpautop( $content );
        $subtitle_top = ( $subtitle_position == "top" )? TRUE : FALSE;

        $shortcode_atts = array(
                'title'             => $content,
                'subtitle_position' => "subtitle_" . $subtitle_position,
                'subtitle'          => $subtitle,
                'subtitle_top'      => $subtitle_top,
                'type'              => $type,
                'align'             => $align,
                'extra_class'       => $extra_class,
                'css'               => esc_attr( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), SHORTCODES_PREFIX . $this->wp_slug, $atts ) )
        );

        return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );

      }
  }
  
 endif;