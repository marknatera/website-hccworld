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

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_FeatureSlider') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_FeatureSlider extends Plethora_Shortcode { 

    public $wp_slug                      = 'featureslider';     
    public static $feature_title         = "Features Slider";   // Feature display title  (string)
    public static $feature_description   = "";                  // Feature display description (string)
    public static $theme_option_control  = true;                // Will this feature be controlled in theme options panel ( boolean )
    public static $theme_option_default  = true;                // Default activation option status ( boolean )
    public static $theme_option_requires = array();             // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
    public static $dynamic_construct     = true;                // Dynamic class construction ? ( boolean )
    public static $dynamic_method        = false;               // Additional method invocation ( string/boolean | method name or false )
    public static $assets                = array(
                                                    array( 'style'  => array( 'shortcode-features-slider' ) ), 
                                            );

    public function __construct() {

        // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
        $map = array( 
                    'base'          => SHORTCODES_PREFIX . $this->wp_slug,
                    'name'          => esc_html__("Features Slider", 'plethora-framework'), 
                    // 'description'   => esc_html__('--- DESCRIPTION GOES HERE ---', 'plethora-framework'), 
                    'class'         => '', 
                    'weight'        => 1, 
                    'category'      => 'Content', 
                    'icon'          => $this->vc_icon(), 
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

                    // HOW MANY FEATURES TO BE DISPLAYED
                    array(
                      "param_name"    => "features_count",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "",                                               
                      "class"         => "",                                          
                      "heading"       => esc_html__("How many features to showcase?", 'plethora-framework'),      
                      "value"         => array(
                        'Two'   => '2',
                        'Three' => '3',
                        'Four'  => '4',
                        'Five'  => '5',
                        'Six'   => '6'
                        ),
                      "admin_label"   => false,                                              
                    ),
                    array(
                      "param_name"    => "alignment",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "",                                               
                      "class"         => "",                                          
                      "heading"       => esc_html__("Is the feature slider being displayed right or left?", 'plethora-framework'),      
                      "value"         => array(
                        'Right' => '',
                        'Left'  => 'left_aligned',
                        ),
                      "admin_label"   => false,                                              
                    ),

                    // FEATURE #1
                    array(
                      "param_name"    => "feature_1_title",
                      "type"          => "textfield",                                        
                      "holder"        => "h3",                                               
                      "class"         => "plethora_vc_title",                                                    
                      "heading"       => esc_html__("Feature #1 Title ( no HTML please )", 'plethora-framework'),
                      "value"         => 'More',                                     
                      "admin_label"   => false,                                             
                    ),
                    array(
                      "param_name" => "feature_1_icon",
                      "type"       => "iconpicker",
                      "holder"     => "",                                               
                      "class"      => "", 
                      "value"      => 'fa fa-cog',
                      "heading"    => esc_html__('Select icon', 'plethora-framework'),
                      'settings'   => array(
                        'type'         => 'plethora',
                        'iconsPerPage' => 56, // default 100, how many icons per/page to display
                      )
                    ),
                    array(
                      "param_name"    => "feature_1_text",
                      "type"          => "textfield",                                        
                      "class"         => "plethora_vc_title",                                                    
                      "heading"       => esc_html__("Feature #1 Description ( no HTML please )", 'plethora-framework'),
                      "value"         => 'Feature Description',                                     
                      "admin_label"   => false,                                             
                    ),
                    array(
                        "param_name"    => "feature_1_image",                                  
                        "type"          => "attach_image",                                        
                        "class"         => "vc_hidden", 
                        "heading"       => esc_html__("Feature screenshot or image", 'plethora-framework'),      
                        "value"         => '',
                        "admin_label"   => false,                                              
                    ),
                    array(
                      "param_name"    => "feature_1_link",
                      "type"          => "vc_link",
                      "holder"        => "",
                      "class"         => "vc_hidden", 
                      "heading"       => esc_html__("Feature #1 Link", 'plethora-framework'),
                      "value"         => '#',
                      "admin_label"   => false,                                               
                    ),

                    // FEATURE #2
                    array(
                      "param_name"    => "feature_2_title",
                      "type"          => "textfield",                                        
                      "holder"        => "h3",                                               
                      "class"         => "plethora_vc_title",                                                    
                      "heading"       => esc_html__("Feature #2 Title ( no HTML please )", 'plethora-framework'),
                      "value"         => 'More',                                     
                      "admin_label"   => false,                                             
                    ),
                    array(
                      "param_name" => "feature_2_icon",
                      "type"       => "iconpicker",
                      "holder"     => "",                                               
                      "class"      => "", 
                      "value"      => 'fa fa-cog',
                      "heading"    => esc_html__('Select icon', 'plethora-framework'),
                      'settings'   => array(
                        'type'         => 'plethora',
                        'iconsPerPage' => 56, // default 100, how many icons per/page to display
                      )
                    ),
                    array(
                      "param_name"    => "feature_2_text",
                      "type"          => "textfield",                                        
                      "class"         => "plethora_vc_title",                                                    
                      "heading"       => esc_html__("Feature #2 Description ( no HTML please )", 'plethora-framework'),
                      "value"         => 'Feature Description',                                     
                      "admin_label"   => false,                                             
                    ),
                    array(
                        "param_name"    => "feature_2_image",                                  
                        "type"          => "attach_image",                                        
                        "class"         => "vc_hidden", 
                        "heading"       => esc_html__("Feature screenshot or image", 'plethora-framework'),      
                        "value"         => '',
                        "admin_label"   => false,                                              
                    ),
                    array(
                      "param_name"    => "feature_2_link",
                      "type"          => "vc_link",
                      "holder"        => "",
                      "class"         => "vc_hidden", 
                      "heading"       => esc_html__("Feature #2 Link", 'plethora-framework'),
                      "value"         => '#',
                      "admin_label"   => false,                                               
                    ),

                    // FEATURE #3
                    array(
                      "param_name"    => "feature_3_title",
                      "type"          => "textfield",                                        
                      "holder"        => "h3",                                               
                      "class"         => "plethora_vc_title",                                                    
                      "heading"       => esc_html__("Feature #3 Title ( no HTML please )", 'plethora-framework'),
                      "value"         => '',                                     
                      "admin_label"   => false,                                             
                      'dependency'    => array( 
                                          'element' => 'features_count', 
                                          'value'   => array('3'),  
                                      )
                    ),
                    array(
                      "param_name" => "feature_3_icon",
                      "type"       => "iconpicker",
                      "holder"     => "",                                               
                      "class"      => "", 
                      "value"      => 'fa fa-cog',
                      "heading"    => esc_html__('Select icon', 'plethora-framework'),
                      'settings'   => array(
                        'type'         => 'plethora',
                        'iconsPerPage' => 56, // default 100, how many icons per/page to display
                      ),
                      'dependency'    => array( 
                                          'element' => 'features_count', 
                                          'value'   => array('3'),  
                                      )
                    ),
                    array(
                      "param_name"    => "feature_3_text",
                      "type"          => "textfield",                                        
                      "class"         => "plethora_vc_title",                                                    
                      "heading"       => esc_html__("Feature #3 Description ( no HTML please )", 'plethora-framework'),
                      "value"         => 'Feature Description',                                     
                      "admin_label"   => false,                                             
                      'dependency'    => array( 
                                          'element' => 'features_count', 
                                          'value'   => array('3'),  
                                      )
                    ),
                    array(
                        "param_name"    => "feature_3_image",                                  
                        "type"          => "attach_image",                                        
                        "class"         => "vc_hidden", 
                        "heading"       => esc_html__("Feature screenshot or image", 'plethora-framework'),      
                        "value"         => '',
                        "admin_label"   => false,                                              
                        'dependency'    => array( 
                                            'element' => 'features_count', 
                                            'value'   => array('3'),  
                                        )
                    ),
                    array(
                      "param_name"    => "feature_3_link",
                      "type"          => "vc_link",
                      "holder"        => "",
                      "class"         => "vc_hidden", 
                      "heading"       => esc_html__("Feature #3 Link", 'plethora-framework'),
                      "value"         => '#',
                      "admin_label"   => false,
                      'dependency'    => array( 
                                          'element' => 'features_count', 
                                          'value'   => array('3'),  
                                      )

                    ),
                    // FEATURE #4
                    array(
                      "param_name"    => "feature_4_title",
                      "type"          => "textfield",                                        
                      "holder"        => "h3",                                               
                      "class"         => "plethora_vc_title",                                                    
                      "heading"       => esc_html__("Feature #4 Title ( no HTML please )", 'plethora-framework'),
                      "value"         => '',                                     
                      "admin_label"   => false,                                             
                      'dependency'    => array( 
                                          'element' => 'features_count', 
                                          'value'   => array('4'),  
                                      )

                    ),
                    array(
                      "param_name" => "feature_4_icon",
                      "type"       => "iconpicker",
                      "holder"     => "",                                               
                      "class"      => "", 
                      "value"      => 'fa fa-cog',
                      "heading"    => esc_html__('Select icon', 'plethora-framework'),
                      'settings'   => array(
                        'type'         => 'plethora',
                        'iconsPerPage' => 56, // default 100, how many icons per/page to display
                      ),
                      'dependency'    => array( 
                                          'element' => 'features_count', 
                                          'value'   => array('4'),  
                                      )
                    ),
                    array(
                      "param_name"    => "feature_4_text",
                      "type"          => "textfield",                                        
                      "class"         => "plethora_vc_title",                                                    
                      "heading"       => esc_html__("Feature #4 Description ( no HTML please )", 'plethora-framework'),
                      "value"         => 'Feature Description',                                     
                      "admin_label"   => false,                                             
                      'dependency'    => array( 
                                          'element' => 'features_count', 
                                          'value'   => array('4'),  
                                      )
                    ),
                    array(
                        "param_name"    => "feature_4_image",                                  
                        "type"          => "attach_image",                                        
                        "class"         => "vc_hidden", 
                        "heading"       => esc_html__("Feature screenshot or image", 'plethora-framework'),      
                        "value"         => '',
                        "admin_label"   => false,                                              
                        'dependency'    => array( 
                                            'element' => 'features_count', 
                                            'value'   => array('4'),  
                                        )
                    ),
                    array(
                      "param_name"    => "feature_4_link",
                      "type"          => "vc_link",
                      "holder"        => "",
                      "class"         => "vc_hidden", 
                      "heading"       => esc_html__("Feature #4 Link", 'plethora-framework'),
                      "value"         => '#',
                      "admin_label"   => false,
                      'dependency'    => array( 
                                          'element' => 'features_count', 
                                          'value'   => array('4'),  
                                      )
                                     
                    ),
                    // FEATURE #5
                    array(
                      "param_name"    => "feature_5_title",
                      "type"          => "textfield",                                        
                      "holder"        => "h3",                                               
                      "class"         => "plethora_vc_title",                                                    
                      "heading"       => esc_html__("Feature #5 Title ( no HTML please )", 'plethora-framework'),
                      "value"         => '',                                     
                      "admin_label"   => false,                                             
                      'dependency'    => array( 
                                          'element' => 'features_count', 
                                          'value'   => array('5'),  
                                      )

                    ),
                    array(
                      "param_name" => "feature_5_icon",
                      "type"       => "iconpicker",
                      "holder"     => "",                                               
                      "class"      => "", 
                      "value"      => 'fa fa-cog',
                      "heading"    => esc_html__('Select icon', 'plethora-framework'),
                      'settings'   => array(
                        'type'         => 'plethora',
                        'iconsPerPage' => 56, // default 100, how many icons per/page to display
                      ),
                      'dependency'    => array( 
                                          'element' => 'features_count', 
                                          'value'   => array('5'),  
                                      )
                    ),
                    array(
                      "param_name"    => "feature_5_text",
                      "type"          => "textfield",                                        
                      "class"         => "plethora_vc_title",                                                    
                      "heading"       => esc_html__("Feature #5 Description ( no HTML please )", 'plethora-framework'),
                      "value"         => 'Feature Description',                                     
                      "admin_label"   => false,                                             
                      'dependency'    => array( 
                                          'element' => 'features_count', 
                                          'value'   => array('5'),  
                                      )
                    ),
                    array(
                        "param_name"    => "feature_5_image",                                  
                        "type"          => "attach_image",                                        
                        "class"         => "vc_hidden", 
                        "heading"       => esc_html__("Feature screenshot or image", 'plethora-framework'),      
                        "value"         => '',
                        "admin_label"   => false,                                              
                        'dependency'    => array( 
                                            'element' => 'features_count', 
                                            'value'   => array('5'),  
                                        )
                    ),
                    array(
                      "param_name"    => "feature_5_link",
                      "type"          => "vc_link",
                      "holder"        => "",
                      "class"         => "vc_hidden", 
                      "heading"       => esc_html__("Feature #5 Link", 'plethora-framework'),
                      "value"         => '#',
                      "admin_label"   => false,
                      'dependency'    => array( 
                                          'element' => 'features_count', 
                                          'value'   => array('5'),  
                                      )
                                     
                    ),
                    // FEATURE #6
                    array(
                      "param_name"    => "feature_6_title",
                      "type"          => "textfield",                                        
                      "holder"        => "h3",                                               
                      "class"         => "plethora_vc_title",                                                    
                      "heading"       => esc_html__("Feature #5 Title ( no HTML please )", 'plethora-framework'),
                      "value"         => '',                                     
                      "admin_label"   => false,                                             
                      'dependency'    => array( 
                                          'element' => 'features_count', 
                                          'value'   => array('6'),  
                                      )

                    ),
                    array(
                      "param_name" => "feature_6_icon",
                      "type"       => "iconpicker",
                      "holder"     => "",                                               
                      "class"      => "", 
                      "value"      => 'fa fa-cog',
                      "heading"    => esc_html__('Select icon', 'plethora-framework'),
                      'settings'   => array(
                        'type'         => 'plethora',
                        'iconsPerPage' => 56, // default 100, how many icons per/page to display
                      ),
                      'dependency'    => array( 
                                          'element' => 'features_count', 
                                          'value'   => array('6'),  
                                      )
                    ),
                    array(
                      "param_name"    => "feature_6_text",
                      "type"          => "textfield",                                        
                      "class"         => "plethora_vc_title",                                                    
                      "heading"       => esc_html__("Feature #5 Description ( no HTML please )", 'plethora-framework'),
                      "value"         => 'Feature Description',                                     
                      "admin_label"   => false,                                             
                      'dependency'    => array( 
                                          'element' => 'features_count', 
                                          'value'   => array('6'),  
                                      )
                    ),
                    array(
                        "param_name"    => "feature_6_image",                                  
                        "type"          => "attach_image",                                        
                        "class"         => "vc_hidden", 
                        "heading"       => esc_html__("Feature screenshot or image", 'plethora-framework'),      
                        "value"         => '',
                        "admin_label"   => false,                                              
                        'dependency'    => array( 
                                            'element' => 'features_count', 
                                            'value'   => array('6'),  
                                        )
                    ),
                    array(
                      "param_name"    => "feature_6_link",
                      "type"          => "vc_link",
                      "holder"        => "",
                      "class"         => "vc_hidden", 
                      "heading"       => esc_html__("Feature #5 Link", 'plethora-framework'),
                      "value"         => '#',
                      "admin_label"   => false,
                      'dependency'    => array( 
                                          'element' => 'features_count', 
                                          'value'   => array('6'),  
                                      )
                                     
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

          'features_count' => '2',
          'alignment'      => '',

          'feature_1_title' => 'Feature #1 Title',
          'feature_1_icon'  => 'fa fa-cog',
          'feature_1_text'  => 'Some awesome feature description can be placed here.',
          'feature_1_link'  => '',
          'feature_1_image' => '',

          'feature_2_title' => 'Feature #2 Title',
          'feature_2_icon'  => 'fa fa-cog',
          'feature_2_text'  => 'Some awesome feature description can be placed here.',
          'feature_2_link'  => '',
          'feature_2_image' => '',

          'feature_3_title' => 'Feature #3 Title',
          'feature_3_icon'  => 'fa fa-cog',
          'feature_3_text'  => 'Some awesome feature description can be placed here.',
          'feature_3_link'  => '',
          'feature_3_image' => '',

          'feature_4_title' => 'Feature #4 Title',
          'feature_4_icon'  => 'fa fa-cog',
          'feature_4_text'  => 'Some awesome feature description can be placed here.',
          'feature_4_link'  => '',
          'feature_4_image' => '',

          'feature_5_title' => 'Feature #5 Title',
          'feature_5_icon'  => 'fa fa-cog',
          'feature_5_text'  => 'Some awesome feature description can be placed here.',
          'feature_5_link'  => '',
          'feature_5_image' => '',

          'feature_6_title' => 'Feature #6 Title',
          'feature_6_icon'  => 'fa fa-cog',
          'feature_6_text'  => 'Some awesome feature description can be placed here.',
          'feature_6_link'  => '',
          'feature_6_image' => ''

        ), $atts ) );

        // PLACE ALL VALUES IN 'SHORTCODE_ATTS' VARIABLE
        $shortcode_atts = array( 
          'features'  => array() ,
          'uuid'      => mt_rand( 1,50000 ),
          'alignment' => $alignment
        );

        for ( $i = 1; $i <= intval( $features_count ) ; $i++ ){

          $active     = ( $i == 1)? "active" : "";  // 1st Tab should have an 'active' class
          $screenshot = ${'feature_'.$i.'_image'};
          $image      = (!empty( $screenshot )) ? wp_get_attachment_image_src( $screenshot, 'full' ) : '';
          $image      = isset($image[0]) ? esc_url($image[0]) : PLE_FLIB_FEATURES_URI . "/shortcode/featureslider/assets/screenshot.jpg";

          $button_link        =  self::vc_build_link(${'feature_'.$i.'_link'});
          $button_link['url'] = !empty( $button_link['url'] ) ? $button_link['url'] : '';

          array_push( $shortcode_atts['features'], array( 
            "index"       => $i,
            "title"       => ${'feature_'.$i.'_title'}, 
            "icon"        => ${'feature_'.$i.'_icon'},
            "desc"        => ${'feature_'.$i.'_text'},
            "link"        => $button_link['url'],
            "link_title"  => $button_link['title'],
            "link_target" => $button_link['target'],
            "image"       => $image,
            "active"      => $active
            ) 
          );

        }

        return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );

    }
	}
	
 endif;