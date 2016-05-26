<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

File Description: BOILERPLACE Shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Boilerplate') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_Boilerplate extends Plethora_Shortcode { 

      public $wp_slug                      = 'plethora-framework';                    // SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT the prefix constant )
      public static $feature_name          = "Boilerplate";                    // FEATURE DISPLAY TITLE 
      public static $feature_title         = "Boilerplate Shortcode";          // FEATURE DISPLAY TITLE 
      public static $feature_description   = "Display Boilerplate";            // FEATURE DISPLAY DESCRIPTION 
      public static $shortcode_category    = "Plethora Shortcodes";
      public static $theme_option_control  = true;                             // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL 
      public static $theme_option_default  = true;                             // DEFAULT ACTIVATION OPTION STATUS
      public static $theme_option_requires = array();                          // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct     = true;                             // DYNAMIC CLASS CONSTRUCTION ? 
      public static $dynamic_method        = false;                            // ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )
      public $vc_options                   = array( "design_options", "css_animation" ); // ADD EXTRA VC OPTIONS AND TABS
      public static $assets                = array(
                                                    array( 'script' => array( 'owlcarousel2')),       // Scripts files - wp_enqueue_script
                                                    array( 'style'  => array( 'owlcarousel2-theme')), // Style files - wp_register_style
                                                    array( 'style'  => array( 'owlcarousel2')),       // Style files - wp_register_style
                                            );

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
                      'custom_markup' => $this->vc_custom_markup( self::$feature_name ), 
                      'params'        => $this->params(), 
                      );
          $this->add( $map );         // ADD ΤΗΕ SHORTCODE

          if ( ! is_admin() ) {

            wp_register_script( 'newsletter_form', PLE_THEME_ASSETS_URI . '/js/newsletterform.js', $deps = array('jquery'), '1.0', true );
            wp_enqueue_script( 'newsletter_form' );

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

          $tax_terms = get_terms( "testimonial-category", array( 'hide_empty' => false ) );
          $available_cats = array( "--" => "--" );
          foreach ( $tax_terms as $term ) { $available_cats[$term->name] = $term->slug;  } // $term->term_id;

          $params = array(

                  /*** CHECKBOX >>> ***/

                  array(
                      "param_name"    => "enable_replies",
                      "type"          => "checkbox",
                      "heading"       => esc_html__( "Enable Replies", 'plethora-framework'),
                      "value"         => array( 'Enable' => 'enable' ),
                      "description"   => esc_html__("Enable replies in the Twitter feed.", 'plethora-framework')
                  ),

                  /*** DROPDOWN >>> ***/

                  array(
                      "param_name"    => "testimonial_category",                                  
                      "type"          => "dropdown",                                        
                      "holder"        => "h4",                                               
                      "class"         => "vc_hidden",                                                 
                      "heading"       => esc_html__("Testimonial Category", 'plethora-framework'),      
                      "description"   => esc_html__("Select Testimonial Category to choose specific testimonial posts or leave empty to get uncategorized testimonials.", 'plethora-framework'),       
                      "admin_label"   => false,                                              
                      "value"         => $available_cats
                  ),

                  /*** SWITCHER >>> ***/

                  array(
                      "param_name"    => "doublehelix",
                      "type"          => "switcher",
                      "holder"        => "",
                      "class"         => "vc_hidden",
                      "heading"       => esc_html__("Enable Double Helix Effect", 'plethora-framework'),
                      "value"         => array( 
                        esc_html__('Yes', 'plethora-framework') => '1', 
                        esc_html__('No', 'plethora-framework') => '0'),
                      "description"   => esc_html__("Enable the Double Helix effect on hover.", 'plethora-framework')
                  ),

                  /*** ICONPICKER >>> ***/

                  array(
                      "param_name" => "button_icon",
                      "type"       => "iconpicker",
                      "holder"     => "",                                               
                      "class"      => "", 
                      "value"      => 'fa fa-ambulance',
                      "heading"    => esc_html__('Select icon', 'plethora-framework'),
                      'settings'   => array(
                        'type'         => 'plethora',
                        'iconsPerPage' => 56, // default 100, how many icons per/page to display
                      ),
                      'dependency'    => array( 
                                          'element' => 'button_with_icon', 
                                          'value'   => array('with-icon'),  
                                      )
                    ),

                  /*** IMAGE ATTACHMENT >>> ***/

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

         $args = array(
          'post_type'      => 'testimonial',                    
          'posts_per_page' => 10,                 
          'order'          => 'ASC',                      
          'orderby'        => 'date', // ID, date, modified, rand
          'image'          => ''
         );

         // IMAGE ATTACHMENT
          $image = (!empty($image)) ? wp_get_attachment_image_src( $image, 'full' ) : '';
          $image = isset($image[0]) ? $image[0] : '';
          $image = ( !empty($image) ) ? esc_url( $image ) : '';

         if ( $atts['testimonial_category'] !== "--"){    // DISPLAY TESTIMONIALS FROM SELECTED CATEGORY

            $args['tax_query'] = array(
              array(
                'taxonomy'         => 'testimonial-category',                
                'field'            => 'slug', 
                'terms'            => array( $atts['testimonial_category'] ),
                'include_children' => true
                )
              );

         } else { // EXCLUDE TESTIMONIALS IN CATEGORIES AND JUST DISPLAY UNCATEGORIZED

            $tax_terms      = get_terms( "testimonial-category", array( 'hide_empty' => false ) );
            $available_cats = array();
            foreach ( $tax_terms as $term ) { array_push( $available_cats, $term->slug );  } // $term->term_id;

              $args['tax_query'] = array(
                array(
                  'taxonomy'         => 'testimonial-category',                
                  'field'            => 'slug', 
                  'terms'            => $available_cats,
                  'operator'         => 'NOT IN',
                  )
                );

         }

         $testimonial_posts = new WP_Query($args);   // ACCESS QUERY OBJET METHODS, PAGINATION, STICKY POSTS

         if ( $testimonial_posts->have_posts() ) { 

            $shortcode_atts['testimonials'] = array();

            while( $testimonial_posts->have_posts() ) { 

               $testimonial_posts->the_post();

               array_push( $shortcode_atts['testimonials'], array(
                 'title'     => get_the_title(),
                 'content'   => get_the_content(),     
                 'thumbnail' => get_the_post_thumbnail()
                  )
               );

            };

          return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );

         } 

      }
  }
  
 endif;