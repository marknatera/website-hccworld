<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2015

File Description: Button shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

/*** NESTED VC SC ***/
/*** SOURCE: https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524362 ***/
/*** SOURCE: https://gist.github.com/Webcreations907/ff0b068c5c364f63e45d ***/
if ( false && class_exists('WPBakeryShortCodesContainer') )
{
    class Plethora_Shortcode_ShortcodeGroup extends WPBakeryShortCodesContainer {}
}

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_ShortcodeGroup') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_ShortcodeGroup extends Plethora_Shortcode { 

    public static $feature_title         = "Shortcode Group";   // Feature display title  (string)
    public static $feature_description   = "";                  // Feature display description (string)
    public static $theme_option_control  = true;                // Will this feature be controlled in theme options panel ( boolean )
    public static $theme_option_default  = true;                // Default activation option status ( boolean )
    public static $theme_option_requires = array();             // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
    public static $dynamic_construct     = true;                // Dynamic class construction ? ( boolean )
    public static $dynamic_method        = false;               // Additional method invocation ( string/boolean | method name or false )
    public $wp_slug                      = 'shortcode_group';   // Script & style files. This should be the WP slug of the content element ( WITHOUT the prefix constant )
    public static $assets;
   
    public function __construct() {

        // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
        $map = array( 
                    'base'          => SHORTCODES_PREFIX . $this->wp_slug,
                    'name'          => esc_html__("Shortcode Group", 'plethora-framework'), 
                    'description'   => esc_html__('Container for multiple shortcodes', 'plethora-framework'), 
                    'class'         => '', 
                    'weight'        => 1, 
                    'category'      => 'Content', 
                    'icon'          => $this->vc_icon(), 
                    // 'custom_markup' => $this->vc_custom_markup( 'Button' ), 
                    'params'        => $this->params(), 

                    // LATEST
                    'as_parent'               => array( 'only' => 'vc_facebook, vc_tweetmeme, vc_googleplus, vc_pinterest, vc_video, vc_line_chart, vc_round_chart, vc_pie, vc_icon, vc_single_image, plethora_button, plethora_appstorebutton, vc_wp_text, vc_column_text'), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
                    'content_element'         => true,
                    'show_settings_on_create' => true,
                    "js_view"                 => 'VcColumnView'


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
                      'type'        => 'textfield',
                      'heading'     => esc_html__( 'Group Heading', 'plethora-framework' ),
                      'param_name'  => 'heading',
                      'description' => esc_html__( 'Heading will be displayed at the top of items', 'plethora-framework' ),
                  ),
                  array(
                      'type'        => 'textfield',
                      'heading'     => esc_html__( 'Extra Class', 'plethora-framework' ),
                      'param_name'  => 'extra_classes',
                      'description' => esc_html__( 'Extra classes (separated by space) that will be added to the Group container', 'plethora-framework' ),
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
                'heading'       => '', 
                'extra_classes' => ''
        ), $atts ) );

        // Place all values in 'shortcode_atts' variable
        $shortcode_atts = array (
                                  'extra_classes' => esc_attr($extra_classes),  
                                  'heading'       => $heading,
                                  'shortcode'     => do_shortcode( $content )
                               );

        return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );

    }

    }
    
 endif;