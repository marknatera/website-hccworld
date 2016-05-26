<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

File Description: Features Teaser shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Teaserbox') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_Teaserbox extends Plethora_Shortcode { 

      public static $feature_title          = "Teaser Box Shortcode"; // FEATURE DISPLAY TITLE 
      public static $feature_description    = "";                     // FEATURE DISPLAY DESCRIPTION 
      public static $theme_option_control   = true;                   // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL 
      public static $theme_option_default   = true;                   // DEFAULT ACTIVATION OPTION STATUS 
      public static $theme_option_requires  = array();                // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct      = true;                   // DYNAMIC CLASS CONSTRUCTION ? 
      public static $dynamic_method         = false;                  // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )
      public $wp_slug                       = 'teaserbox';            // SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT the prefix constant )
      public static $assets;

      public function __construct() {

        // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
        $map = array( 
                      'base'             => SHORTCODES_PREFIX . $this->wp_slug,
                      'name'              => esc_html__('Teaser Box', 'plethora-framework'),
                      'description'       => esc_html__('Image/icon, title, subtitle & content', 'plethora-framework'),
                      'class'             => '',
                      'weight'            => 1,
                      'category'          => esc_html__('Teasers & Info Boxes', 'plethora-framework'),
                      'admin_enqueue_js'  => array(), 
                      'icon'          => $this->vc_icon(), 
                      // 'custom_markup' => $this->vc_custom_markup( 'Teaser Box' ), 
                      'params'        => $this->params(), 
                      );
        // Add the shortcode
        $this->add( $map );
      }

     /** 
     * Returns shortcode parameters for VC panel
     *
     * @return array
     * @since 1.0
     *
     */
     public function params() {

          $params = array(

                   array(
                      "param_name"    => "title",                                  
                      "type"          => "textfield",                                        
                      "holder"        => "h3",                                               
                      "class"         => "plethora_vc_title",                                                    
                      "heading"       => esc_html__("Title ( no HTML please )", 'plethora-framework'),
                      "value"         => '',                                     
                      "admin_label"   => false,                                             
                  ),
                   array(
                      "param_name"    => "subtitle",                                  
                      "type"          => "textfield",                                        
                      "holder"        => "h4",                                               
                      "class"         => "plethora_vc_title",                                                    
                      "heading"       => esc_html__("Subtitle ( no HTML please )", 'plethora-framework'),
                      "value"         => '',                                     
                      "admin_label"   => false,                                             
                  ),
                   array(
                      "param_name"    => "content",                                  
                      "type"          => "textarea",                                        
                      "holder"        => "",                                               
                      "class"         => "vc_hidden",                                                    
                      "heading"       => esc_html__("Paragraph ( may use basic HTML elements )", 'plethora-framework'),
                      "value"         => '',                                     
                      "admin_label"   => false,                                             
                  ),
                   array(
                        "param_name"    => "teaser_link",
                        "type"          => "vc_link",
                        "holder"        => "",
                        "class"         => "vc_hidden", 
                        "heading"       => esc_html__("Teaser Link", 'plethora-framework'),
                        "value"         => '#',
                        "admin_label"   => false                                               
                  ),
                   array(
                      "param_name"    => "boxed_styling",
                      "type"          => "dropdown",
                      "heading"       => esc_html__('Boxed styling', 'plethora-framework'),
                      "value"         => array( 
                                            esc_html__('No boxed styling', 'plethora-framework') =>'',
                                            esc_html__('Normal boxed', 'plethora-framework')  => 'boxed',
                                            esc_html__('Special boxed', 'plethora-framework')  => 'boxed_special',
                        ),
                      "description"   => esc_html__("Depending on the selection, it affects padding and or border lines of the whole box", 'plethora-framework'),
                    ),
                   array(
                        "param_name"    => "media_type",
                        "type"          => "dropdown",
                        "heading"       => esc_html__('Select media type', 'plethora-framework'),
                        "holder"        => "",                                               
                        "class"         => "vc_hidden", 
                        "admin_label"   => false,                                             
                        "value"         => array( 
                          esc_html__('Image', 'plethora-framework') =>'image',
                          esc_html__('Icon', 'plethora-framework')  => 'icon'
                          ),
                    ),
                    array(
                        "param_name"    => "icon",
                        "type"          => "iconpicker",
                        "holder"        => "",                                               
                        "class"         => "vc_hidden", 
                        "admin_label"   => false,                                             
                        "heading"       => esc_html__('Select icon', 'plethora-framework'),
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
                      "holder"        => "",                                               
                      "class"         => "vc_hidden", 
                      "heading"       => esc_html__("Image", 'plethora-framework'),      
                      "value"         => '',
                      "admin_label"   => false,                                              
                      'dependency'    => array( 
                                            'element' => 'media_type', 
                                            'value'   => array('image'),  
                                        )
                    ),
                    array(
                        "param_name"    => "image_hover_effect",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Enable Image Hover Effect", 'plethora-framework'),
                        "value"         => array( 
                                          'Disabled' => 'disabled', 
                                          'Enabled'  => 'enabled'
                                           ),
                        "description"   => esc_html__("Enable a subtle opacity change and vertical movement effect when hovered", 'plethora-framework'),
                        "admin_label"   => false, 
                    ),
                    array(
                        "param_name"    => "media_colorset",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Media section color set", 'plethora-framework'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'color_sets', 
                                          'use_in'          => 'vc', 
                                          'prepend_default' => true, 
                                          'append_options'  => array( 'transparent' => esc_html__('Transparent', 'plethora-framework') )
                                           )),
                        "description"   => esc_html__("Choose a color setup ONLY for the icon section. Remember: all color sets above can be configured via the theme options panel", 'plethora-framework'),
                        "admin_label"   => false, 
                    ),
                    array(
                      "param_name"    => "media_ratio",
                      "type"          => "dropdown",
                      "heading"       => esc_html__('Media display ratio', 'plethora-framework'),
                      "value"         => Plethora_Module_Style::get_options_array( array( 
                                        'type' => 'stretchy_ratios', 
                                        'use_in' => 'vc', 
                                        'prepend_options' => array( 'boxed' => esc_html__('Do not apply', 'plethora-framework')) 
                                        )),            
                    ),
                    array(
                        "param_name"    => "text_colorset",
                        "type"          => "dropdown",
                        "heading"       => esc_html__("Text section color set", 'plethora-framework'),
                        "value"         => Plethora_Module_Style::get_options_array( array( 
                                          'type'            => 'color_sets', 
                                          'use_in'          => 'vc', 
                                          'prepend_default' => true, 
                                          'append_options'  => array( 'transparent' => esc_html__('Transparent', 'plethora-framework') )
                                           )),
                        "description"   => esc_html__("Choose a color setup for this element. Remember: all color sets above can be configured via the theme options panel", 'plethora-framework'),
                        "admin_label"   => false, 
                    ),
                    array(
                      "param_name"    => "text_boxed_styling",
                      "type"          => "dropdown",
                      "heading"       => esc_html__('Text section boxed styling', 'plethora-framework'),
                      "value"         => array( 
                                            esc_html__('No boxed styling', 'plethora-framework') =>'',
                                            esc_html__('Boxed', 'plethora-framework')  => 'boxed',
                                            esc_html__('Boxed Special', 'plethora-framework')  => 'boxed_special',
                        ),
                      "description"   => esc_html__("Depending on the selection, it affects inner padding of the text section of the box", 'plethora-framework'),
                    ),
                    array(
                      "param_name"    => "text_align",
                      "type"          => "dropdown",
                      "heading"       => esc_html__('Contents align', 'plethora-framework'),
                      "value"         => array( 
                                              esc_html__('Centered', 'plethora-framework') => 'text-center',
                                              esc_html__('Left', 'plethora-framework')     =>'text-left',                                              
                                              esc_html__('Right', 'plethora-framework')    => 'text-right',
                                              esc_html__('Inherit', 'plethora-framework')  =>'',
                        ),
                    ),
                    array(
                      "param_name"    => "button_display",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "h4",                                               
                      "class"         => "vc_hidden",                                         
                      "heading"       => esc_html__("Display button on bottom", 'plethora-framework'),      
                      "value"         => array('No'=>0,'Yes'=>1),
                      "admin_label"   => false,                                              
                    ),
                    array(
                      "param_name"    => "button_text",
                      "type"          => "textfield",                                        
                      "holder"        => "",                                               
                      "class"         => "vc_hidden",                                                    
                      "heading"       => esc_html__("Button text ( no HTML please )", 'plethora-framework'),
                      "value"         => 'More',                                     
                      "admin_label"   => false,                                             
                       'dependency'    => array( 
                                          'element' => 'button_display',  
                                          'value'   => array('1'),   
                                          )
                    ),
                    array(
                      "param_name"    => "button_style",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "h4",                                               
                      "class"         => "vc_hidden",                                         
                      "heading"       => esc_html__("Button styling", 'plethora-framework'),      
                      "value"         => array(
                        'Default'   => 'btn-default',
                        'Primary'   => 'btn-primary',
                        'Secondary' => 'btn-secondary',
                        'White'     => 'btn-white',
                        'Success'   => 'btn-success',
                        'Info'      => 'btn-info',
                        'Warning'   => 'btn-warning',
                        'Danger'    => 'btn-danger',
                        'Inverse'    => 'btn-inverse',
                        ),
                      "admin_label"   => false,                                              
                      'dependency'    => array( 
                                          'element' => 'button_display', 
                                          'value'   => array('1'),   
                                      )
                    ),

                    array(
                        "param_name"    => "same_height",
                        "type"          => "dropdown",
                        "heading"       => esc_html__('Same Height', 'plethora-framework'),
                        "holder"        => "",                                               
                        "class"         => "vc_hidden", 
                        "admin_label"   => false,                                             
                        "value"         => array( 
                          esc_html__('No', 'plethora-framework') =>'',
                          esc_html__('Yes', 'plethora-framework')  => 'same_height_col'
                          ),
                        "description"   => esc_html__("Turn this to Yes if you want this box to be of equal height to any other box in its row that has this also turned to Yes.", 'plethora-framework'),
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

          // EXTRACT USER INPUT
          extract( shortcode_atts( array( 
            'title'              => '',
            'subtitle'           => '',
            'media_type'         => 'image',
            'icon'               => '',
            'image'              => '',
            'image_hover_effect' => 'disabled',
            'teaser_link'        => '',
            'media_colorset'     => '',
            'media_ratio'        => '',
            'text_colorset'      => '',
            'text_align'         => 'text-center',
            'button_display'     => '1',
            'button_text'        => 'More',
            'button_style'       => 'btn-default',
            // 'button_size'     => 'btn',
            'boxed_styling'      => '',
            'same_height'        => '',
            'text_boxed_styling' => '',
            ), $atts ) );

          // Prepare final values that will be used in template
          $image       = (!empty($image)) ? wp_get_attachment_image_src( $image, 'full' ) : '';
          $image       = isset($image[0]) ? $image[0] : '';
          $teaser_link  = !empty($teaser_link) ? self::vc_build_link($teaser_link) : array();
          //$button_link = !empty($button_link) ? self::vc_build_link($button_link) : '#';
          $button_display = isset( $atts['button'] ) ? $atts['button'] : $button_display;
          // Place all values in 'shortcode_atts' variable
          $shortcode_atts = array (
                                  'content'        => $content, 
                                  'title'          => $title, 
                                  'subtitle'       => $subtitle, 
                                  'icon'           => esc_attr( $icon ), 
                                  'image'          => esc_url( $image ),
                                  'image_hover'    => ( $image_hover_effect == "enabled" )? "image_hover" : "",
                                  'media_colorset' => $media_colorset, 
                                  'media_ratio'    => $media_ratio, 
                                  'text_colorset'  => $text_colorset, 
                                  'text_align'     => $text_align, 
                                  'button_text'    => $button_text,  
                                  'button_style'   => $button_style, 
                                  // 'button_size'    => $button_size, 
                                  'boxed_styling'  => $boxed_styling,
                                  'same_height'    => $same_height,
                                  'text_boxed_styling' => $text_boxed_styling,
                                  'figure_classes' => 'figure ' . $media_colorset . ' ' . ( ( preg_match( "/boxed/", $boxed_styling ) && $media_ratio == "boxed" ) ? "" : $media_ratio ),
                                 );

          if ( $media_type === 'image' && $image !== "" ) {

            $shortcode_atts["media_type_image"] = TRUE; 
              
          } elseif ( $media_type === 'icon' && $icon !== "" ) {

            $shortcode_atts["media_type_icon"] = TRUE; 

          }

          if ( $media_ratio !== '' ) {

            $shortcode_atts["aplied_media_ratio"] = TRUE; 
              
          } else {

            $shortcode_atts["no_media_ratio"] = TRUE; 
              
          }

          if ( !empty( $teaser_link['url'] ) ) {

            $shortcode_atts["teaser_link_url"]    = esc_url( $teaser_link['url'] );
            $shortcode_atts["teaser_link_title"]  = esc_attr( trim( $teaser_link['title']) );
            $shortcode_atts["teaser_link_target"] = esc_attr( trim( $teaser_link['target']) );

          } 

          if ( $button_display == 1 ){

            $shortcode_atts["button"]        = TRUE;
            //$shortcode_atts["btn_url"]       = isset($button_link['url']) ? esc_url($button_link['url']) : '#';
            //$shortcode_atts["btn_urltitle"]  = isset($button_link['title']) ? ' title="'. esc_attr( $button_link['title'] ) .'"' : '';
            //$shortcode_atts["btn_urltarget"] = isset($button_link['target']) ?' target="'. esc_attr( $button_link['target'] ) .'"' : '';

          }

          return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );

       }
  }
  
 endif;