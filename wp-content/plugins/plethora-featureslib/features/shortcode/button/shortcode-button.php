<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Button shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Button') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_Button extends Plethora_Shortcode { 

    public static $feature_title         = "Button Shortcode";  // Feature display title  (string)
    public static $feature_description   = "";                  // Feature display description (string)
    public static $theme_option_control  = true;                // Will this feature be controlled in theme options panel ( boolean )
    public static $theme_option_default  = true;                // Default activation option status ( boolean )
    public static $theme_option_requires = array();             // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
    public static $dynamic_construct     = true;                // Dynamic class construction ? ( boolean )
    public static $dynamic_method        = false;               // Additional method invocation ( string/boolean | method name or false )
    public $wp_slug                      =  'button';           // Script & style files. This should be the WP slug of the content element ( WITHOUT the prefix constant )
    public static $assets;
   
    public function __construct() {

        // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
        $map = array( 
                    'base'          => SHORTCODES_PREFIX . $this->wp_slug,
                    'name'          => esc_html__("PL Button", 'plethora-framework'), 
                    'description'   => esc_html__('with icon and styling settings', 'plethora-framework'), 
                    'class'         => '', 
                    'weight'        => 1, 
                    'category'      => 'Content', 
                    'icon'          => $this->vc_icon(), 
                    // 'custom_markup' => $this->vc_custom_markup( 'Button' ), 
                    'params'        => $this->params(), 
                    );
        // Add the shortcode
        $this->add( $map );

    }

    /** 
    * Returns shortcode parameters for VC panel
    *
    * @return array
    * @since 2.0
    *
    */
    public function params() {

      $params = array(

            array(
              "param_name"       => "button_text",
              "type"             => "textfield",                                        
              "holder"           => "h3",                                               
              "class"            => "plethora_vc_title",                                                    
              "heading"          => esc_html__("Button text ( no HTML please )", 'plethora-framework'),
              "value"            => 'More',                                     
            ),
            array(
              "param_name"       => "button_link",
              "type"             => "vc_link",
              "holder"           => "",
              'edit_field_class' => 'vc_col-sm-6 vc_column',
              "class"            => "vc_hidden", 
              "heading"          => esc_html__("Button link", 'plethora-framework'),
              "value"            => '#',
            ),
            array(
              "param_name"       => "button_size",                                  
              "type"             => "dropdown",                                        
              "holder"           => "",                                               
              "class"            => "",                                          
              'edit_field_class' => 'vc_col-sm-6 vc_column',
              "heading"          => esc_html__("Button size", 'plethora-framework'),      
              "value"            => array(
                                      'Default'     =>'btn',
                                      'Large'       =>'btn btn-lg',
                                      'Small'       =>'btn btn-sm',
                                      'Extra Small' =>'btn btn-xs'
                                      ),
            ),
            array(
              "param_name"       => "button_align",                                  
              "type"             => "dropdown",                                        
              "holder"           => "",                                               
              "class"            => "",                                          
              'edit_field_class' => 'vc_col-sm-6 vc_column',
              "heading"          => esc_html__("Button align", 'plethora-framework'),      
              "value"            => array(
                                      'Left'   => 'text-left',
                                      'Center' => 'text-center',
                                      'Right'  => 'text-right'
                                      ),
            ),
            array(
              "param_name"       => "button_style",                                  
              "type"             => "dropdown",                                        
              "holder"           => "",                                               
              "class"            => "vc_hidden",                                         
              'edit_field_class' => 'vc_col-sm-6 vc_column',
              "heading"          => esc_html__("Button styling", 'plethora-framework'),      
              "value"            => array(
                                      'Default'   => 'btn-default',
                                      'Primary'   => 'btn-primary',
                                      'Secondary' => 'btn-secondary',
                                      'White'     => 'btn-white',
                                      'Success'   => 'btn-success',
                                      'Info'      => 'btn-info',
                                      'Warning'   => 'btn-warning',
                                      'Danger'    => 'btn-danger',
                                      'Text-Link' => 'btn-link',
                                      ),
            ),
            array(
              "param_name"       => "button_inline",                                  
              "type"             => "dropdown",                                        
              "holder"           => "",                                               
              "class"            => "vc_hidden",                                         
              'edit_field_class' => 'vc_col-sm-6 vc_column',
              "heading"          => esc_html__("Inline/Block placement", 'plethora-framework'),      
              "value"            => array(
                                      esc_html__('Inline-Block', 'plethora-framework') => '',
                                      esc_html__('Inline', 'plethora-framework')  => 'btn_inline',
                                      esc_html__('Block', 'plethora-framework')  => 'btn_block',
                                    ),
            ),
            array(
              "param_name"    => "button_with_icon",
              "type"          => "dropdown",
              "heading"       => esc_html__('Button icon', 'plethora-framework'),
              'edit_field_class' => 'vc_col-sm-6 vc_column',
              "value"         => array( 
                                    esc_html__('No', 'plethora-framework') => 0,
                                    esc_html__('Yes', 'plethora-framework')  => 'with-icon',
                                ),
            ),
            array(
              "param_name" => "button_icon",
              "type"       => "iconpicker",
              "holder"     => "",                                               
              "class"      => "", 
              "value"      => 'fa fa-ambulance',
              'group'      => esc_html__( 'Icon', 'plethora-framework' ),
              "heading"    => esc_html__('Select icon', 'plethora-framework'),
              'settings'   => array(
                                'type'         => 'plethora',
                                'iconsPerPage' => 56, // default 100, how many icons per/page to display
                              ),
              'dependency' => array( 
                                  'element' => 'button_with_icon', 
                                  'value'   => array('with-icon'),  
                                        )
            ),
            array(
              "param_name"  => "button_icon_align",
              "type"        => "dropdown",
              'group'       => esc_html__( 'Icon', 'plethora-framework' ),
              "heading"     => esc_html__('Button icon align', 'plethora-framework'),
              "description" => ' ',
              "value"       => array( 
                                    esc_html__('Right', 'plethora-framework')  => '',
                                    esc_html__('Left', 'plethora-framework') =>'icon-left',
                ),
              'dependency'  => array( 
                                    'element' => 'button_with_icon', 
                                    'value'   => array('with-icon'),  
                                )
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
    * Returns shortcode content OR content template
    *
    * @return array
    * @since 1.0
    *
    */
    public function content( $atts, $content = null ) {

        // Extract user input
        extract( shortcode_atts( array( 
          'button_text'       => esc_html__( 'More', 'plethora-framework' ),
          'button_link'       => '',
          'button_size'       => 'btn',
          'button_align'      => 'text-left',
          'button_style'      => 'btn-primary',
          'button_inline'     => '',
          'button_with_icon'  => '',
          'button_icon'       => '',
          'button_icon_align' => '',
          'css' => '',
        ), $atts ) );

        // Prepare final values that will be used in template
        $button_link        =  self::vc_build_link($button_link);
        $button_link['url'] = !empty( $button_link['url'] ) ? $button_link['url'] : '#';
        $button_with_icon   = $button_with_icon != '0' && !empty($button_with_icon) ? $button_with_icon : '';
        //$button_inline      = $button_inline != '0' && !empty($button_inline) ? $button_inline : '';

        // Place all values in 'shortcode_atts' variable
        $shortcode_atts = array (
                                'btn_text'       => esc_attr($button_text),  
                                'btn_url'        => esc_url( $button_link['url'] ),
                                'btn_title'      => esc_attr( $button_link['title'] ),
                                'btn_align'      => $button_align,
                                'btn_target'     => !empty( $button_link['target'] ) ? esc_attr( $button_link['target'] ) : '_self',
                                'btn_style'      => esc_attr($button_style), 
                                'button_inline'  => esc_attr($button_inline), 
                                'btn_size'       => esc_attr($button_size), 
                                'btn_with_icon'  => esc_attr($button_with_icon), 
                                'btn_icon'       => $button_icon,
                                'btn_icon_align' => $button_icon_align,
                                'css'            => esc_attr( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), SHORTCODES_PREFIX . $this->wp_slug, $atts ) ),
                               );

        if ( $button_with_icon === 'with-icon' ){
          if ( $button_icon_align === 'icon-left' ){
            $shortcode_atts["btn_icon_align_left"] = TRUE;
          } else {
            $shortcode_atts["btn_icon_align_right"] = TRUE;
          }
        }

        return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );

    }

	}
	
 endif;