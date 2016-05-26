<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2015

File Description: Single Post Template for user created CPTs
*/
if ( !class_exists('Plethora_Template_Single') ) {

  class Plethora_Template_Single { 

    public static $post_type;

  	public function __construct() {
        
        self::$post_type = get_post_type();

        // Special treatment for full page layout display 
        add_filter( 'plethora_wrapper_column_open', array( $this, 'wrapper_column_open')); 

        // Main Post parts
        add_action( 'plethora_content', array( $this, 'wrapper_open'), 10);           // Post main wrapper opening
        add_action( 'plethora_content', array( $this, 'title'), 10);                  // Post title
        add_action( 'plethora_content', array( $this, 'subtitle'), 10);               // Post subtitle

        $post_format = get_post_format();
        if ( $post_format === 'video' || $post_format === 'audio' ) { // just a hack for not mixing meta info with video/audio

          add_action( 'plethora_content', array( $this, 'media_wrapper_open'), 10);   // Post media wrapper opening
          add_action( 'plethora_content', array( $this, 'media'), 11);                // Post media
          add_action( 'plethora_content', array( $this, 'media_wrapper_close'), 12);  // Post media wrapper closing
          add_action( 'plethora_content', array( $this, 'media_wrapper_open'), 13);   // Post media wrapper opening
          add_action( 'plethora_content', array( $this, 'meta'), 14);                 // Post meta info
          add_action( 'plethora_content', array( $this, 'media_wrapper_close'), 15);  // Post media wrapper closing

        } else {

          add_action( 'plethora_content', array( $this, 'media_wrapper_open'), 10);   // Post media wrapper opening
          add_action( 'plethora_content', array( $this, 'media'), 11);                // Post media
          add_action( 'plethora_content', array( $this, 'meta'), 12);                 // Post meta info
          add_action( 'plethora_content', array( $this, 'media_wrapper_close'), 13);  // Post media wrapper closing
        }

        add_action( 'plethora_content', array( $this, 'content'), 20);                // Post content
        add_action( 'plethora_content', array( $this, 'wrapper_close'), 20);          // Post main wrapper closing
        add_action( 'plethora_content', array( 'Plethora_Template', 'single_comments'), 20);         // Comments ( common for all singles )
  	    
        // fix to avoid displaying global title on single view
        global $plethora_template;
        remove_action( 'plethora_header_after', array( $plethora_template, 'global_title'), 10);
    }


   /**
     * Returns single post wrapper tag opening
     */
    public static function wrapper_column_open( $wrapper_open ) {

      $layout   = Plethora_Theme::get_layout( self::$post_type );
      if ( $layout === 'no_sidebar' ) { 

        $wrapper_open = '<div class="col-md-8 col-md-offset-2">';
      }

      return $wrapper_open;
    }

   /**
     * Returns single post wrapper tag opening
     */
    public static function wrapper_open() {

      echo '<article id="post-'. get_the_id() .'" class="'. implode(' ', get_post_class( array( 'post', self::$post_type ) ) ) .'">';
    }

    /**
     * Returns single post title
     */
    public static function title() {

      echo Plethora_Theme::get_title( array( 'tag' => 'h1', 'post_type' => self::$post_type ) );
    }

    /**
     * Returns single post subtitle
     */
    public static function subtitle() {

      echo Plethora_Theme::get_subtitle( array( 'post_type' => self::$post_type ) );
    }

    /**
     * Returns single post media wrapper tag opening
     */
    public static function media_wrapper_open() {

      echo '<div class="post_figure_and_info">';
    }

    /**
     * Returns single post media
     */
    public static function media() {

      $post_media_display = Plethora_Theme::option( METAOPTION_PREFIX . self::$post_type .'-mediadisplay', 1);
      if ( $post_media_display ) {
        
        $post_format  = get_post_format();
        $args = array(
              'type'         => $post_format, 
              'stretch'      => true, 
              'link_to_post' => false,
              'force_display'=> true 
            );
        echo Plethora_Theme::get_post_media( $args );
      }    
    }

    /**
     * Returns single post meta
     */
    public static function meta() {
      
      $the_id             = get_the_id();
      $show_primary_tax   = Plethora_Theme::option( METAOPTION_PREFIX . self::$post_type .'-info-primarytax', 1, $the_id );
      $primary_tax        = Plethora_Theme::option( METAOPTION_PREFIX . self::$post_type .'-info-primarytax-slug', 'category', $the_id );
      $show_secondary_tax = Plethora_Theme::option( METAOPTION_PREFIX . self::$post_type .'-info-secondarytax', 1, $the_id );
      $secondary_tax      = Plethora_Theme::option( METAOPTION_PREFIX . self::$post_type .'-info-secondarytax-slug', 'post_tag', $the_id );
      $show_author        = Plethora_Theme::option( METAOPTION_PREFIX . self::$post_type .'-author', 1, $the_id );
      $show_date          = Plethora_Theme::option( METAOPTION_PREFIX . self::$post_type .'-date', 1, $the_id );
      $show_comments      = Plethora_Theme::option( METAOPTION_PREFIX . self::$post_type .'-comments', 1, $the_id );

      $output = '';
      // date info
      if ( $show_date )   { $output .= '<span class="post_info post_date"><i class="fa fa-calendar"></i> '.get_the_date() .'</span>'; }
      // author info
      if ( $show_author ) { $output .= '<a href="#" title="' . esc_attr( sprintf( get_the_author() )) . '"><span class="post_info post_author">'. get_the_author() .'</span></a>'; }
      // primary taxonomy info
      if ( $show_primary_tax && !empty( $primary_tax ) ) { 
        $terms = get_the_terms( $the_id, $primary_tax );
        if ( ! is_wp_error( $terms ) && !empty( $terms ) ) {
          foreach($terms as $key=>$term) {

            $output .= '<a href="'.get_term_link( $term ).'" title="' . esc_attr( sprintf( esc_html__( "View all in category: %s", 'healthflex' ), $term->name ) ) . '"><span class="post_info post_categories">'.$term->name.'</span></a>';
          }
        }
      }
      // secondary taxonomy info
      if ( $show_secondary_tax && !empty( $secondary_tax ) ) { 
        $terms = get_the_terms( $the_id, $secondary_tax );
        if ( ! is_wp_error( $terms ) && !empty( $terms ) ) {
          foreach($terms as $key=>$term) {

            $output .= '<a href="'.get_term_link( $term ).'" title="' . esc_attr( sprintf( esc_html__( "View all in category: %s", 'healthflex' ), $term->name ) ) . '"><span class="post_info post_tags">'.$term->name.'</span></a>';
          }
        }
      }

      // comments info
      if ( $show_comments && comments_open()  ) { 

          $num_comments = get_comments_number();
          if ( $num_comments > 0 ) {

            $output .= '<a href="'. esc_url( get_permalink() .'#post_comments').'"><span class="post_info post_comment"><i class="fa fa-comments"></i>'. $num_comments .' </span></a>' ;
          } 
      }
      $output = '<div class="post_sub">'. $output .'</div>'; 
      echo $output;
    }

    /**
     * Returns single post media wrapper tag closing
     */
    public static function media_wrapper_close() {
      
      echo '</div>';
    }

    /**
     * Returns single post content ( depending on format )
     */
    public static function content() {

      the_content();

      wp_link_pages(array(
               'before'      => '<div class="page-links post_pagination_wrapper"><span class="page-links-title">' . esc_html__( 'Pages:', 'healthflex' ) . '</span>',
               'after'       => '</div>',
               'link_before' => '<span class="post_pagination_page">',
               'link_after'  => '</span>',
      ));
    }

    /**
     * Returns single post wrapper tag closing
     */
    public static function wrapper_close() {

      echo '</article>';
    }
  } 
}    