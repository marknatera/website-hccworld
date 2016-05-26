<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2013

File Description: Latest News Widget Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Widget') && !class_exists('Plethora_Widget_LatestNews') ) {
 
      /**
      * @package Plethora Framework
      */
      class Plethora_Widget_LatestNews extends WP_Widget  {

          public static $feature_title          = "Latest Blog Posts";              // FEATURE DISPLAY TITLE (STRING)
          public static $feature_description    = "Display your latest blog posts"; // Feature display description (string)
          public static $theme_option_control   = true;                             // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL ( BOOLEAN )
          public static $theme_option_default   = true;                             // DEFAULT ACTIVATION OPTION STATUS ( BOOLEAN )
          public static $theme_option_requires  = array();                          // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
          public static $dynamic_construct      = false;                            // DYNAMIC CLASS CONSTRUCTION ? ( BOOLEAN )
          public static $dynamic_method         = false;                            // THIS A PARENT METHOD, FOR ADDING ACTION. ADDITIONAL METHOD INVOCATION ( STRING/BOOLEAN | METHOD NAME OR FALSE )
          public static $wp_slug =  'latestnews-widget';                            // SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT THE PREFIX CONSTANT )
          public static $assets;

          public function __construct() { 

            /* LEAVE INTACT ACROSS WIDGET CLASSES */

            $id_base     = WIDGETS_PREFIX . self::$wp_slug;
            $name        = '> PL | ' . self::$feature_title;
            $widget_ops  = array( 
              'classname'   => self::$wp_slug, 
              'description' => self::$feature_title 
              );
            $control_ops = array( 'id_base' => $id_base );

            parent::__construct( $id_base, $name, $widget_ops, $control_ops );      // INSTANTIATE PARENT OBJECT

            /* ADDITIONAL WIDGET CODE STARTS HERE */

          }

          function widget( $args, $instance ) {

            extract( $args ); // EXTRACT USER INPUT

            $category = ( ! empty( $instance['category'] ) ) ? $instance['category'] : '';
            $number   = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 10;
            if ( ! $number ){ $number = 10; }

             $ln_query_args = array( 
                  'posts_per_page'      => $number, 
                  'no_found_rows'       => true, 
                  'post_status'         => 'publish', 
                  'ignore_sticky_posts' => true 
             ); 

             if ( !empty($category)) { $ln_query_args['category_name'] = $category; }

             $ln_query_args = apply_filters( 'widget_posts_args', $ln_query_args );

             $custom_posts = get_posts( $ln_query_args );  
              // FORMAT POST VALUES
              foreach ( $custom_posts as $custom_post ) {
                  $custom_post->title         = $custom_post->post_title;
                  $custom_post->permalink     = get_permalink( $custom_post->ID );
                  $custom_post->thumbnail     = ( has_post_thumbnail( $custom_post->ID ))? wp_get_attachment_image_src( get_post_thumbnail_id( $custom_post->ID ) ) : false;
                  $custom_post->thumbnail_url = esc_url( $custom_post->thumbnail[0] );
                  $custom_post->content       = wp_trim_words( strip_shortcodes( $custom_post->post_content ), 10, '...' );
                  $date = new DateTime( $custom_post->post_date_gmt );
                  $custom_post->date          = $date->format('M j');
              }

              // PREPARE DATA FROM MUSTACHE TEMPLATE
              $widget_atts = array( 
                'before_widget' => $before_widget,
                'title'         => apply_filters('widget_title', $instance['title']),
                'after_widget'  => $after_widget,
                'posts'         => $custom_posts 
              );

             echo Plethora_WP::renderMustache( array( "data" => $widget_atts, "file" => __FILE__) );

          }

          function update( $new_instance, $old_instance ) {

               $instance             = $old_instance;
               $instance['title']    = strip_tags($new_instance['title']);
               $instance['category'] = strip_tags($new_instance['category']);
               $instance['number']   = (int) $new_instance['number'];
               $alloptions = wp_cache_get( 'alloptions', 'options' );
               if ( isset($alloptions['widget_latestnews_entries']) ){  delete_option('widget_latestnews_entries');  }
               return $instance;

          }

          function form( $instance ) {

           $title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
           $category  = isset( $instance['category'] ) ? esc_attr( $instance['category'] ) : 'Uncategorized';
           $number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;

           ?>

           <p>
             <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'plethora-framework' ); ?></label>
             <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
           </p>

           <p>
              <label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php esc_html_e( 'Category:', 'plethora-framework' ); ?></label>
             <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>">
             <option id="" value="">--</option>
             <?php 
             $cats = get_terms('category', array('hide_empty' => false));
             foreach ( $cats as $cat ) {
                  echo '<option id="' . esc_attr( $cat->name ) . '" value="' . esc_attr( $cat->name ) . '"' , ( $category == $cat->name ? ' selected="selected"' : '' ), '>' . $cat->name . '</option>';
             }
             ?>
             </select>
           </p>

           <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of posts to show:', 'plethora-framework' ); ?></label>
            <input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" />
          </p>

          <?php               
          }
     }
 }