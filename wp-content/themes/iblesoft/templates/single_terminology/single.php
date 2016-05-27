<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2015

File Description: Single Term Template Parts // Content
*/
if ( !class_exists('Plethora_Template_Single') ) {

  class Plethora_Template_Single { 

  	public function __construct() {
        
        // Note: terminology single post makes use of the global title/subtitle system 
        add_action( 'plethora_content', array( $this, 'wrapper_open'), 10);       // Post main wrapper opening
        add_action( 'plethora_content', array( $this, 'media'), 10);              // Post media
        add_action( 'plethora_content', array( $this, 'meta'), 10);               // Post meta
        add_action( 'plethora_content', array( $this, 'content'), 10);            // Post content
        add_action( 'plethora_content', array( $this, 'wrapper_close'), 10);      // Post main wrapper closing
  	}

    /**
     * Returns single post wrapper tag opening
     */
    public static function wrapper_open() {
      ?>
      <article id="terminology-<?php the_ID(); ?>" <?php post_class('post'); ?>>
      <?php
    }

    /**
     * Returns single post meta
     */
    public static function meta() {
      
      $show_categories = Plethora_Theme::option( METAOPTION_PREFIX .'terminology-categories', 1);
      $show_author     = Plethora_Theme::option( METAOPTION_PREFIX .'terminology-author', 1);
      $show_date       = Plethora_Theme::option( METAOPTION_PREFIX .'terminology-date', 1);
      
      if ( $show_categories || $show_author || $show_date ) {
        echo '<div class="post_figure_and_info">'; 
        $output = '';
        // date info
        if ( $show_date )   { $output .= '<span class="post_info post_date"><i class="fa fa-calendar"></i> '.get_the_date() .'</span>'; }
        // author info
        if ( $show_author ) { $output .= '<span class="post_info post_author">'. get_the_author() .'</span>'; }
        // categories info
        if ( $show_categories ) { 
          $categories = get_the_terms( get_the_id(), 'term-topic' );
          if ( $categories ) {
            foreach($categories as $key=>$category) {
              $link = get_term_link($category);
              $output .= '<span class="post_info post_categories">'. esc_html( $category->name ) .'</span>';
            }
          }
        }
        $output = '<div class="post_sub">'. $output .'</div>'; 
        echo $output;
        echo '</div>'; 
      }
    }

    /**
     * Returns single post media
     */
    public static function media() {

      $post_media_display = Plethora_Theme::option( METAOPTION_PREFIX .'terminology-mediadisplay', 1);
      if ( $post_media_display ) {

        $args = array(
              'stretch'      => true, 
              'link_to_post' => false,
              'force_display'=> false 
            );
        $output = Plethora_Theme::get_post_media( $args );
        if ( !empty( $output ) ) { 
          echo '<div class="post_figure_and_info">';
          echo $output;      
          echo '</div>';
        }
      }
    }

    /**
     * Returns single post content ( depending on format )
     */
    public static function content() {

      the_content();
    }

    /**
     * Returns single post wrapper tag closing
     */
    public static function wrapper_close() {

      echo '</article>';
    }
  } 
}    