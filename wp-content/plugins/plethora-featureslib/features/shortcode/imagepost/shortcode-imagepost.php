<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				      (c) 2013-2015

File Description: Image Post Shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_ImagePost') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_ImagePost extends Plethora_Shortcode { 

      public static $feature_title         = "Image Post Shortcode";  // FEATURE DISPLAY TITLE 
      public static $feature_description   = "";                      // FEATURE DISPLAY DESCRIPTION 
      public static $theme_option_control  = true;                    // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL 
      public static $theme_option_default  = true;                    // DEFAULT ACTIVATION OPTION STATUS
      public static $theme_option_requires = array();                 // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct     = true;                    // DYNAMIC CLASS CONSTRUCTION ? 
      public static $dynamic_method        = false;                   // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )
      public $wp_slug                      = 'imagepost';             // SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT the prefix constant )
      public static $assets                = array(
                                                array( 'script' => 'svgloader-snap' ),  
                                                array( 'script' => 'svgloader' ),       
                                                array( 'script' => 'svgloader-init' )       
                                             );

      public function __construct() {

          // MAP SHORTCODE SETTINGS ACCORDING TO VC DOCUMENTATION ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
          $map = array( 
                      'base'          => SHORTCODES_PREFIX . $this->wp_slug,
                      'name'          => esc_html__('Image Post', 'plethora-framework'),
                      'description'   => esc_html__('Image/icon and content', 'plethora-framework'),
                      'class'         => '',
                      'weight'        => 1,
                      'category'      => esc_html__('Teasers & Info Boxes', 'plethora-framework'),
                      'icon'          => $this->vc_icon(), 
                      'params'        => $this->params(), 
                      );
          $this->add( $map );          // ADD SHORTCODE

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
                    "param_name"    => "title",                                  
                    "type"          => "textfield",                                        
                    "holder"        => "h4",                                               
                    "class"         => "plethora_vc_title",
                    "heading"       => esc_html__("Custom Title*", 'plethora-framework'),
                    "description"   => esc_html__("* no HTML please. Overrides Post Title", 'plethora-framework'),
                    "value"         => '',                                     
                    "admin_label"   => false,                                             
                ),
                array(
                      "param_name"    => "subtitle_option",
                      "type"          => "dropdown",
                      "heading"       => esc_html__("Subtitle", 'plethora-framework'),
                      "value"         => array(
                          esc_html__('Post Subtitle','plethora-framework')   => 'post_subtitle',
                          esc_html__('Custom Subtitle','plethora-framework') => 'custom_subtitle',
                          esc_html__('None','plethora-framework')            => 'none'
                        ),
                      "description"   => esc_html__("Display or override Post Subtitle", 'plethora-framework'),
                      "admin_label"   => false 
                ),
                array(
                    "param_name"    => "subtitle",                                  
                    "type"          => "textfield",                                        
                    "holder"        => "h4",                                               
                    "class"         => "plethora_vc_title",                                                    
                    "heading"       => esc_html__("Custom Subtitle*", 'plethora-framework'),
                    "description"   => esc_html__("* no HTML please", 'plethora-framework'),
                    "value"         => '',                                     
                    "admin_label"   => false,                                             
                    "dependency"    => array(
                        "element" => "subtitle_option",
                        "value"   => "custom_subtitle"

                    )
                ),
                array(
                    "param_name"    => "description",                                  
                    "type"          => "textfield",                                        
                    'admin_label' => false,
                    'holder'      => 'h3',                                               
                    'class'       => 'plethora_vc_title',                                                    
                    "heading"       => esc_html__("Custom Description*", 'plethora-framework'),
                    "description"   => esc_html__("* Accepts HTML. Overrides Post Excerpt.", 'plethora-framework'),
                    "value"         => '',
                ),
                // CUSTOM BACKGROUND IMAGE
                array(
                    "param_name"    => "image",                                  
                    "type"          => "attach_image",                                        
                    "holder"        => "img", 
                    "class"         => "vc_hidden", 
                    "heading"       => esc_html__("Custom Background Image", 'plethora-framework'),      
                    "description"   => esc_html__("This will override the post's featured image.", 'plethora-framework'),      
                    "value"         => '',
                    "admin_label"   => false,                                              
                ),
                array(
                      "param_name"    => "image_valign",
                      "type"          => "dropdown",
                      "heading"       => esc_html__("Image Vertical Align", 'plethora-framework'),
                      "value"         => Plethora_Module_Style::get_options_array( array( 
                                        'type'            =>'bgimage_valign',
                                        'use_in'          => 'vc'
                                        )), 
                      "description"   => esc_html__("Select from Top, Center, Bottom vertical alignment for the photo.", 'plethora-framework'),
                      "admin_label"   => false 
                ),
                array(
                      "param_name"    => "post_category",
                      "type"          => "dropdown",
                      "heading"       => esc_html__("Post Category", 'plethora-framework'),
                      "value"         => self::getCategories(), 
                      "description"   => esc_html__("Select the Category from where to fetch the post.", 'plethora-framework'),
                      "admin_label"   => false 
                ),
                array(
                      "param_name"    => "post_offset",
                      "type"          => "dropdown",
                      "heading"       => esc_html__("Post Offset", 'plethora-framework'),
                      "value"         => array(
                          esc_html__('Most Recent','plethora-framework')   => '1',
                          esc_html__('Last','plethora-framework')          => 'last',
                          esc_html__('Custom Offset','plethora-framework') => 'custom'
                        ), 
                      "description"   => esc_html__("Display most recent post, last post or use a custom offset.", 'plethora-framework'),
                      "admin_label"   => false 
                ),
                array(
                    "param_name"  => "post_offset_custom",                                  
                    "type"        => "textfield",                                        
                    'admin_label' => false,
                    'heading'     => esc_html__("Custom Post Offset", 'plethora-framework'),
                    'value'       => '',
                    'description' => esc_html__('Use inverse numbers to get last resulst: 2 will get the third post, -2 will get the third post from the end.','plethora-themes'),
                    'dependency'  => array(
                      'element' => 'post_offset',
                      'value'   => array('custom')
                      )
                ),
                array(
                    "param_name"    => "show_post_meta",
                    "type"          => "dropdown",
                    "holder"        => "",
                    "class"         => "vc_hidden",
                    "heading"       => esc_html__("Show Post Meta", 'plethora-framework'),
                    "value"         => array( 
                      esc_html__('Show Date and Author', 'plethora-framework') => 'date_author', 
                      esc_html__('Show Date', 'plethora-framework')            => 'date',
                      esc_html__('Show Author', 'plethora-framework')          => 'author',
                      esc_html__('None', 'plethora-framework')                 => ''
                      ),
                    "description"   => esc_html__("Show Post meta such as Date, Author, etc..", 'plethora-framework')
                ),
                array(
                    "param_name"    => "target_type",
                    "type"          => "dropdown",
                    "holder"        => "",
                    "class"         => "vc_hidden",
                    "heading"       => esc_html__("Select option for opening Post ", 'plethora-framework'),
                    "value"         => array( 
                      esc_html__('Open in new Winow', 'plethora-framework')            => '_blank', 
                      esc_html__('Open on same page', 'plethora-framework')            => '',
                      esc_html__('Open on same page using Ajax', 'plethora-framework') => 'ajax'
                      ),
                    "description"   => esc_html__("Open Image Post in a new window, on the same page or without page reload via Ajax.", 'plethora-framework')
                ),
                array(
                      "param_name"   => "media_ratio",
                      "type"         => "value_picker",
                      "heading"      => esc_html__('Media Display Ratio', 'plethora-framework'),
                      "picker_type"  => "single",  // Multiple or single class selection ( 'single'|'multiple' )
                      "picker_cols"  => "6",         // Picker columns for selections display ( 1, 2, 3, 4, 6 )                                       
                      "value"        => 'stretchy_wrapper ratio_1-1',     
                      "values_index" => Plethora_Module_Style::get_options_array( array( 
                                        'type'   => 'stretchy_ratios', 
                                        'use_in' => 'vc', 
                                        )),            
                ),
                array(
                        "param_name"    => "class",
                        "type"          => "value_picker",
                        "heading"       => esc_html__("Color Set", 'plethora-framework'),
                        "picker_cols"   => "4",         // Picker columns for selections display ( 1, 2, 3, 4, 6 )                                       
                        "values_index"  => Plethora_Module_Style::get_options_array( array( 
                                          'type'   => 'color_sets', 
                                          'use_in' => 'vc', 
                                          )),
                        "description"   => esc_html__("Choose a color setup for this element. Remember: all colors in above options can be configured via the theme options panel", 'plethora-framework'),
                        "admin_label"   => false, 
                ),
                array(
                    "param_name"    => "transparent_overlay",
                    "type"          => "dropdown",
                    "heading"       => esc_html__("Transparent Overlay", 'plethora-framework'),
                    "value"         => array( 
                                          esc_html__('No Transparent Overlay', 'plethora-framework')                  => '', 
                                          esc_html__('Transparent Overlay: Full', 'plethora-framework')               => 'transparent_film', 
                                          esc_html__('Transparent Overlay: Gradient To Top', 'plethora-framework')    => 'gradient_film_to_top', 
                                          esc_html__('Transparent Overlay: Gradient To Bottom', 'plethora-framework') => 'gradient_film_to_bottom',
                                        ),
                    "admin_label"   => false, 
                ),    

         );

          return $params;
       }

       public function getCategories(){

          $output = array( 'All' => '' );
          foreach ( Plethora_WP::categories() as $key => $value) $output[$key] = $value;
          return $output;

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
            'post_category'       => '',
            'post_offset'         => '1',
            'post_offset_custom'  => '1',
            'show_post_meta'      => 'date_author',
            'image'               => '',
            'image_valign'        => '',
            'media_ratio'         => 'stretchy_wrapper ratio_1-1',
            'icon'                => '',
            'target_type'         => '_blank',
            'title'               => '',
            'subtitle'            => '',
            'subtitle_option'     => 'post_subtitle',
            'description'         => '',
            'class'               => 'skincolored_section',
            'transparent_overlay' => ''
            ), $atts ) );

          // PREPARE TEMPLATE VALUES

          $image = (!empty($image)) ? wp_get_attachment_image_src( $image, 'full' ) : '';
          $image = isset($image[0]) ? $image[0] : '';

          $shortcode_atts = array (
                                  'class'               => esc_attr( $class ) . ' ' . $image_valign,  
                                  'transparent_overlay' => $transparent_overlay
                                 );

          /* QUERYING POSTS */

          $args = array(
            'posts_per_page'      => 1,
            'ignore_sticky_posts' => 1,
            'cat'                 => $post_category
          );

          switch ($post_offset) {

            case '1':
              $args['order'] = 'DESC';
              break;

            case 'last':
              $args['order'] = 'ASC';
              break;

            case 'custom':
              $post_offset_custom      = intval($post_offset_custom);
              $post_offset_custom_sign = ( $post_offset_custom > 0 ) - ( $post_offset_custom < 0 );
              $post_offset_order       = ( $post_offset_custom_sign == 0 )? 1 : $post_offset_custom_sign;  // 1, -1, 0
              $args['order']           = ( $post_offset_order == 1 )? 'DESC' : 'ASC';
              $args['offset']          = abs($post_offset_custom);
              break;
           
          }

          $post_query = new WP_Query($args);

          if ( $post_query->have_posts() ) {

              $post_query->the_post();
              $thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );  // CACHE get_the_ID() IF USED MORE THAN ONCE

              /* FILTERING VALUES FOR MUSTACHE TEMPLATE */

              $title                         = esc_attr( trim( $title ));
              $subtitle                      = esc_attr( trim( $subtitle ));
              $description                   = esc_attr( trim( $description ));
              $shortcode_atts['link_url']    = get_the_permalink();
              $shortcode_atts['link_title']  = get_the_title();
              $shortcode_atts['link_target'] = ( $target_type == '_blank' )? '_blank' : '';
              $shortcode_atts['ajax']        = ( $target_type == 'ajax' )? 'linkify' : '';
              $shortcode_atts['image']       = ( !empty($image) ) ? esc_url( $image ) : esc_url( $thumb_url[0] );
              $shortcode_atts['title']       = ( $title == "" )? get_the_title() : $title;

              switch ( $show_post_meta ) {
                case 'date_author':
                  $post_meta = get_the_date() . esc_html__( " By ", "plethora-theme" ) . get_the_author();
                  break;
                case 'date':
                  $post_meta = get_the_date();
                  break;
                case 'author':
                  $post_meta = esc_html__( "By ", "plethora-theme" ) . get_the_author();
                  break;
                case '':
                  $post_meta = "";
                  break;
              }

              $post_subtitle = get_post_meta ( get_the_ID(), "ple-post-subtitle-text", true );

              $shortcode_atts['post_meta']   = $post_meta;

                switch ( $subtitle_option ) {
                   case 'post_subtitle':
                    $shortcode_atts['subtitle'] = $post_subtitle;
                     break;
                   case 'custom_subtitle':
                     $shortcode_atts['subtitle'] = $subtitle;
                     break;
                   case 'none':
                     $shortcode_atts['subtitle'] = '';
                     break;
                 } 

              $shortcode_atts['description'] = ( $description == "") ? get_the_excerpt() : $description;

          } else {

              $shortcode_atts['title'] = esc_html__('Post not found','plethora-framework');

          } 

          $shortcode_atts['media_ratio'] = $media_ratio;

          wp_reset_postdata();    

          return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );

       }
	}
	
 endif;