<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

Description: Inlcudes theme and third party HTML markup.
Separated into the following sections:
- BASIC TEMPLATE CONSTRUCTION : Template parts setup & script/style enqueues for each page type
- GLOBAL TEMPLATE PARTS       : Calls for all common template parts
- ARCHIVE TEMPLATE PARTS      : Calls for all archive pages template parts
- SINGLE TEMPLATE PARTS       : Calls for all single pages template parts
- REUSABLE TEMPLATE METHODS   : Repo for methods that are called multiple times on template parts ( Well...most possibly those will be transfered to Plethora_Theme or in separate template part files )

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Template') ) {

  class Plethora_Template { 

////// BASIC TEMPLATE CONSTRUCTION ------> START

    function __construct() {

      // Declare ALL assets ( scripts & styles )
      add_action( 'wp_enqueue_scripts', array( $this, 'global_style_classes'), 5 );

      // Must hook them on 'wp'. That will allow native WP conditionals to work properly
      add_action( 'wp', array( $this, 'header_parts'));  // Template parts for head & header  header.php )
      add_action( 'wp', array( $this, 'footer_parts'));  // Template parts for footer ( footer.php )
      add_action( 'wp', array( $this, 'content_parts'));  // Template parts for content ( archive / single )
    }

    /**
    * Various global style classes
    */
    public function global_style_classes(){ 

      # CLASS FILTER HOOKS // Hooks 'body_class' ( native WP hook ) | 'plethora_header_class' | 'plethora_footer_class'
        add_filter( 'body_class', array( 'Plethora_Template', 'body_class'));                // Body class filter ( WP Hook )
        add_filter( 'plethora_header_class', array( 'Plethora_Template', 'header_class'));   // Header class filter 
        add_filter( 'plethora_footer_class', array( 'Plethora_Template', 'footer_class'));   // Footer class filter 
        add_filter( 'plethora_wrapper_main_class', array( 'Plethora_Template', 'main_class')); // Main content wrapper classes
        add_filter( 'plethora_wrapper_main_data_attrs', array( $this, 'main_data_attrs'));

    }

    /**
     * Action hooks for header.php 
     * Hooks: 'plethora_head_before' | 'plethora_body_open' | 'plethora_header_before' | 'plethora_header' | 'plethora_header_after'
     */
    public function header_parts() {

      // HEAD & HEADER HOOKS
      add_action( 'plethora_head_before', array( $this, 'head_meta'), 10);               // Meta settings
      add_action( 'plethora_head_before', array( $this, 'favicons'), 20);                // Favicons
      add_action( 'plethora_body_open', array( $this, 'wrapper_overflow_open'), 10);     // Overflow wrapper open
      
      // a workaround here, in order to avoid junk <header> markup
      $logo       = Plethora_Theme::option( METAOPTION_PREFIX .'logo', 1);
      $navigation = Plethora_Theme::option( METAOPTION_PREFIX .'navigation-main', 1);
      $social_bar = Plethora_Theme::option( METAOPTION_PREFIX .'socialbar', 1);
      if ( $logo || $navigation || $social_bar ) { 

        add_action( 'plethora_header', array( $this, 'main_bar'), 20);                     // Logo, main navigation and social bar
      }
      
      add_action( 'plethora_header_after', array( $this, 'brand_colors'), 5);            // Brand Colors?
      // Layout wrap opening tags + left sidebar
      add_action( 'plethora_header_after', array( $this, 'wrapper_main_open'), 10);      // Main wrapper open
      
      if ( ! Plethora_Theme::is_archive_page( 'product' ) ) { // we don't want global title on shop page
        add_action( 'plethora_header_after', array( $this, 'global_title'), 10);           // Global Title/Subtitle Implementation
      }
      add_action( 'plethora_header_after', array( $this, 'wrapper_content_open'), 15);     // Content wrapper open
      add_action( 'plethora_header_after', array( $this, 'sidebar_left'), 20);             // Left sidebar
      add_action( 'plethora_header_after', array( $this, 'main_column_open'), 25);         // Main column open

    }

    /**
     * Action hooks for footer.php
     * Hooks: 'plethora_footer_before' | 'plethora_footer' | 'plethora_footer_after' 
     */
    public function footer_parts() {

      // Layout wrap closing tags + right sidebar
      add_action( 'plethora_footer_before', array( $this, 'main_column_close'), 10);         // Main column close
      add_action( 'plethora_footer_before', array( $this, 'sidebar_right'), 15);             // Right sidebar
      add_action( 'plethora_footer_before', array( $this, 'wrapper_content_close'), 20);     // Content wrapper close
      add_action( 'plethora_footer_before', array( $this, 'wrapper_main_close'), 25);    // Main wrapper close
      // FOOTER HOOKS
      $footer_widgets = Plethora_Theme::option( THEMEOPTION_PREFIX .'footer-widgets', 1);
      if ( $footer_widgets ) { // a workaround here, in order to avoid junk <footer> markup
        add_action( 'plethora_footer', array( $this, 'footer_widgets'), 10);               // Footer widget areas
      }
      add_action( 'plethora_footer_after', array( $this, 'footer_infobar'), 10);               // Footer info bar
      add_action( 'plethora_footer_after', array( $this, 'wrapper_overflow_close'), 20); // Overflow wrapper close
      // SPECIAL DEV HOOK
      add_action( 'wp_footer', array( $this, 'show_page_template'), 99);       // Overflow wrapper close

    }

    /**
     * Action hooks for content parts that will be displayed on single.php or archive.php ( depending on request )
     */
    public function content_parts() { 

      $this->archive_content();
      $this->single_content();
    } 

    /**
     * Hooks for archives content ('plethora_single_before' | 'plethora_single_content' | 'plethora_single_after')
     */
    public function archive_content() { 

      if ( Plethora_Theme::is_archive_page() && ! is_search() ) {

        $post_type = get_post_type(); 

        $file_parent = get_template_directory() .'/templates/archive_'. $post_type .'/archive.php' ;
        $file_child  = get_stylesheet_directory() .'/templates/archive_'. $post_type .'/archive.php' ;

        if ( file_exists( $file_parent ) || file_exists( $file_child ) ) {

          Plethora_WP::get_template_part( 'templates/archive_'.$post_type.'/archive' );
        
        } else {

          // if for some reason, has_archive is enabled without a specific template set, then use default post templates
          Plethora_WP::get_template_part( 'templates/archive/archive' );
        }

      } elseif ( is_search() ) {

        Plethora_WP::get_template_part( 'templates/archive/archive' );
      }

      if ( class_exists('Plethora_Template_Archive')) {

        new Plethora_Template_Archive;
      }

    }

    /**
     * Hooks for singles ('plethora_single_before' | 'plethora_single_content' | 'plethora_single_after')
     */
    public function single_content() {

      if ( is_singular() ) { 

        $post_type = get_post_type(); 

        $file_parent = get_template_directory() .'/templates/single_'. $post_type .'/single.php' ;
        $file_child  = get_stylesheet_directory() .'/templates/single_'. $post_type .'/single.php' ;
        if ( file_exists( $file_parent ) || file_exists( $file_child ) ) {

          Plethora_WP::get_template_part( 'templates/single_'. $post_type .'/single' );
        
        } else {

          // if for some reason, has_archive is enabled without a specific template set, then use default post templates
          Plethora_WP::get_template_part( 'templates/single/single' );
        }
      }

      if ( is_404() ) {

        $template_index = PLE_THEME_TEMPLATES_DIR .'/404/404.php';
        if ( file_exists( $template_index )) { 
            Plethora_WP::get_template_part( 'templates/404/404' );
        }
      }
      
      if ( class_exists('Plethora_Template_Single')) {

        new Plethora_Template_Single;
     }
    }

////// BASIC TEMPLATE CONSTRUCTION ------> FINISH

////// GLOBAL TEMPLATE PARTS ------> START

    /**
     * Returns head meta settings
     * @hooked plethora_head_before_wphead - 10
     */
    public static function head_meta() {

      Plethora_WP::get_template_part( 'templates/global/meta' );
    }

    /**
     * Returns favicons
     * @hooked plethora_head_before_wphead - 20
     */
    public static function favicons() {

      if ( ! function_exists('wp_site_icon') ) { 

        Plethora_WP::get_template_part( 'templates/global/favicons' );
      }
    }

    /**
     * A filter for body_class, when menu is not sticky
     */
    public static function body_class( $classes ) { 

      $header_sticky = Plethora_Theme::option( THEMEOPTION_PREFIX .'header-sticky', 1, 0, false);
      $header_bcg_trans = Plethora_Theme::option( THEMEOPTION_PREFIX .'header-background-transparentfilm', 1, 0, false);
      $classes[] = $header_sticky ? 'sticky_header' : '';
      $classes[] = $header_bcg_trans ? 'transparent_header' : '';
      return $classes;

    }

    /**
     * Returns the initital overflow wrapper
     * @hooked plethora_body_open - 10
     */
    public static function wrapper_overflow_open() {

      Plethora_WP::get_template_part( 'templates/global/wrapper_overflow_open' );
    }

    /**
     * Ηeader classes
     */
    public static function header_class( $classes ) { 
      // Add transparent class if top bar OR main bar need a transparent film
      $transparent_class = Plethora_Theme::option( METAOPTION_PREFIX .'header-background-transparentfilm', 0, 0, false) == 1 ? 'transparent' : '';
      $transparent_class = Plethora_Theme::option( METAOPTION_PREFIX .'topbar-transparentfilm', 0, 0, false) == 1 ? 'transparent' : $transparent_class;
      $classes[] = $transparent_class;      
      return $classes; 
    }

    /**
     * Main content section classes
     */
    public static function main_class( $classes ) {

      $colorset = Plethora_Theme::get_content_colorset();
      if ( !empty( $colorset ) ) {

        $classes[] = Plethora_Theme::get_content_colorset();
      }
      return $classes;
    }

    /**
     * Main content section classes
     */
    public static function main_data_attrs( $data_attrs ) {
      
      $colorset = Plethora_Theme::get_content_colorset();
      if ( !empty( $colorset ) ) {

        $data_attrs['colorset'] = $colorset;
      }
      
      return $data_attrs;
    }

    /**
     * Returns Logo & main navigation template part
     */
    public static function main_bar() {

      Plethora_WP::get_template_part( 'templates/global/header' );
    }

    /**
     * Returns Brand colors markup
     */
    public static function brand_colors() {
    ?>
      <div class="brand-colors"> </div>
    <?php
    }

    /**
     * Returns the main wrapper opening div
     */
    public static function wrapper_main_open() {

      Plethora_WP::get_template_part( 'templates/global/wrapper_main_open' );
    }

    /**
     * Use a common title/subtitle implementation UNLESS this is a single post or single profile page
     */
    function global_title() {

      if ( ! is_singular( array('post', 'profile' ) ) && ! is_404()  && Plethora_Theme::content_has_titles() ) { 

        if ( Plethora_Theme::get_layout() === 'no_sidebar' && ! Plethora_Theme::content_has_sections() && Plethora_Theme::content_has_titles() ) {

          add_filter('plethora_wrapper_content_class', array($this, 'global_title_no_padding_to_content'));
        
        } elseif ( Plethora_Theme::get_layout() !== 'no_sidebar' && Plethora_Theme::content_has_titles() ) {

          add_filter('plethora_wrapper_content_class', array($this, 'global_title_no_padding_to_content'));
        }

        Plethora_WP::get_template_part( 'templates/global/title' );
      } 
    }

    /**
     * Class to add on 'plethora_wrapper_content_class' hook when some title conditions occur
     */
    function global_title_no_padding_to_content( $class ) {

      $class[] = 'no_top_padding';
      return $class;
    }   

    /**
     * Loads template part that returns the main content wrapper opening
     */
    public static function wrapper_content_open() {

        Plethora_WP::get_template_part( 'templates/global/wrapper_content_open' );
    }

    /**
     * Displays the left sidebar ( if is to be displayed )
     */
    public static function sidebar_left() {

      $layout   = Plethora_Theme::get_layout();
      if ( $layout === 'left_sidebar' ) { get_sidebar(); } // Left Sidebar 
    }

    /**
     * Displays the left sidebar ( if is to be displayed )
     */
    public static function main_column_open() {

         Plethora_WP::get_template_part( 'templates/global/wrapper_column_open' );
    }

    /**
     * Displays the main content column ( if is to be displayed )
     */
    public static function main_column_close() {

         Plethora_WP::get_template_part( 'templates/global/wrapper_column_close' );
    }

    /**
     * Displays the right sidebar ( if is to be displayed )
     */
    public static function sidebar_right() {

      $layout   = Plethora_Theme::get_layout();
      if ( $layout === 'right_sidebar' ) { get_sidebar(); } // Right Sidebar 
    }

    /**
     * Loads template part that returns the main content wrapper closing
     */
    public static function wrapper_content_close() {

         Plethora_WP::get_template_part( 'templates/global/wrapper_content_close' );
    }

    /**
     * A filter for footer class
     */
    public static function footer_class( $classes ) { 

      $background        = Plethora_Theme::option( METAOPTION_PREFIX .'footer-background', '', 0, false);
      $classes[] = $background != 'color' ? $background .' separator_top' : '';
      $transparent_class  = Plethora_Theme::option( METAOPTION_PREFIX .'footer-background-transparentfilm', 0, 0, false);
      $transparent_class = $transparent_class == 1 ? 'transparent transparent_film' : '';
      $classes[] = $transparent_class;  // Add transparent class      
      return $classes; 

    }

    /**
     * Returns the footer widgets section
     */
    public static function footer_widgets(){

        $atts['layout'] = Plethora_Theme::option( METAOPTION_PREFIX .'footer-widgetslayout', 5);
        set_query_var( 'atts', $atts );
        Plethora_WP::get_template_part('templates/global/footer-widgets');
    }

    /**
     * Returns the footer section
     */
    public static function footer_infobar(){

      $footer_infobar   = Plethora_Theme::option( METAOPTION_PREFIX .'footer-infobar', 1);
      if ( $footer_infobar ) {

        $atts['copyright']       = Plethora_Theme::option( METAOPTION_PREFIX .'footer-infobarcopyright', esc_html__('Copyright &copy;2016 all rights reserved', 'healthflex') );
        $atts['credits']         = Plethora_Theme::option( METAOPTION_PREFIX .'footer-infobarcreds', esc_html__('Designed by', 'healthflex') .' <a href="http://plethorathemes.com" target="_blank">Plethora Themes</a>' );
        $atts['colorset']        = Plethora_Theme::option( METAOPTION_PREFIX .'footer-infobar-colorset', 'dark_section');
        $atts['transparentfilm'] = Plethora_Theme::option( METAOPTION_PREFIX .'footer-infobar-transparentfilm', 1) == 1 ? 'transparent_film' : '';
        set_query_var( 'atts', $atts );
        Plethora_WP::get_template_part('templates/global/infobar');
      }  
    }

    /**
     * Returns the main wrapper closing tag
     */
    public static function wrapper_main_close(){

      Plethora_WP::get_template_part( 'templates/global/wrapper_main_close' );

    }
    /**
     * Returns the overflow wrapper closing tag
     */
    public static function wrapper_overflow_close(){

      Plethora_WP::get_template_part( 'templates/global/wrapper_overflow_close' );
    }

    /**
     * Returns comments template
     */
    public static function single_comments() {

      // Add comments template parts
      add_action( 'plethora_comments_list', array( 'Plethora_Template', 'comments_list'), 10);     // Overflow wrapper open
      add_action( 'plethora_comments_list', array( 'Plethora_Template', 'comments_paging'), 15);     // Overflow wrapper open
      add_action( 'plethora_comments_new', array( 'Plethora_Template', 'comments_new'), 10);     // Overflow wrapper open
      comments_template(); 
    }

    /**
     * Returns source comments regarding current page template info
     */
    public static function show_page_template() {

      echo Plethora_WP::showPageTemplate( array( "always" => true ) );
    }
////// GLOBAL TEMPLATE PARTS <------ FINISH

////// REUSABLE TEMPLATE METHODS ---> START


    /**
     * Returns comments list 
     */
    static function comments_list() {
        if ( have_comments() ) { ?>
          <div id="post_comments">
          <?php if ( Plethora_Theme::get_layout() === 'no_sidebar' ) { ?><div class="container"><?php } ?>
            <h4><?php echo esc_html__('Comments', 'healthflex') ?></h4>
            <!-- Comments List start -->
            <div class="comment">
                <?php
                  wp_list_comments( array(
                    'style'       => 'div',
                    'avatar_size' => 100,
                    'callback'    => array('Plethora_Template', 'comments_list_callback'),
                    'format'      =>'html5',
                  ));
                ?>
            </div>
          <?php if ( Plethora_Theme::get_layout() === 'no_sidebar' ) { ?></div><?php } ?>
          </div>
    <?php } 
    }

    /**
     * Returns comments paging 
     */
    static function comments_paging() {
    ?>
      <?php 
        if ( have_comments() ) { ?>
          <div id="comments_paging" class="no_padding_top no_padding_bottom">
          <?php if ( Plethora_Theme::get_layout() === 'no_sidebar' ) { ?><div class="container"><?php } ?>
          <?php 
            $page_comments = get_option('page_comments');
            if ( get_comment_pages_count() > 1 && $page_comments ) {  
            ?>
                <nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
                  <div class="row">
                    <div class="col-md-6 text-right"><div class="nav-previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'healthflex' ) ); ?></div></div>
                    <div class="col-md-6 text-left"><div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'healthflex' ) ); ?></div></div>
                  </div>
                </nav><!-- #comment-nav-below -->
            <?php
            } ?>
          <?php if ( Plethora_Theme::get_layout() === 'no_sidebar' ) { ?></div><?php } ?>
          </div>
    <?php } 
    }

    /**
     * Returns new comments form 
     */
    static function comments_new() {
      ?>

      <div id="new_comment">
      <?php if ( Plethora_Theme::get_layout() === 'no_sidebar' ) { ?><div class="container"><?php } ?>
        <div class="new_comment">
            <?php 

            $commenter = wp_get_current_commenter();
            $req = get_option( 'require_name_email' );
            $aria_req = ( $req ? ' aria-required="true" required="required" ' : '' );
     
            $new_comment_args = array( 
              'fields'               => apply_filters( 'comment_form_default_fields', array( 
                                          'author'=> '<div class="row"><div class="col-sm-6 col-md-4 comment-form-author"><input placeholder="'. esc_html__('Your name', 'healthflex') .'" type="text" class="form-control" id="author" name="author" value="'. esc_attr( $commenter['comment_author'] )  .'"' . $aria_req . '></div>', 
                                          'email' => '<div class="col-sm-6 col-md-4 comment-form-email"><input placeholder="'. esc_html__('Your email', 'healthflex') .'" type="text" class="form-control"  id="email" name="email" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" aria-describedby="email-notes"' . $aria_req . '></div></div>',
                                        )),
              'comment_field'        => '<div class="row"><div class="col-sm-12 col-md-8"><textarea rows="5" placeholder="'. esc_html__('Comments', 'healthflex') .'" class="form-control"  id="comment" name="comment" class="form-control" aria-required="true"></textarea></div></div>',
              'comment_notes_before' => '',
              'comment_notes_after'  => '<br>',
              'title_reply'          => esc_html__( 'Add Comment', 'healthflex' ),
              'title_reply_to'       => esc_html__( 'Reply to %s', 'healthflex' ),
              'cancel_reply_link'    => esc_html__( 'Cancel', 'healthflex' ),
              'label_submit'         => esc_html__( 'Add Comment', 'healthflex' ),
              'class_submit'         => 'btn send btn-primary'
            );
            comment_form( $new_comment_args );
            ?>
        </div>
      <?php if ( Plethora_Theme::get_layout() === 'no_sidebar' ) { ?></div><?php } ?>
      </div>

    <?php
    }
    /**
     * Callback function for wp_list_comments callback argument use 
     *
     * @param $comment, $args, $depth
     *
     */
    static function comments_list_callback( $comment, $args, $depth ) {
        $GLOBALS['comment'] = $comment;
        $figure_class = $depth > 1 ? 'col-sm-2 col-md-2 col-md-offset-' . ($depth - 1) : 'col-sm-2 col-md-2';
        $main_class = $depth > 1 ? 'col-sm-' . (11 - $depth) .' col-md-' . (11 - $depth) .'' : 'col-sm-10 col-md-10';
        if ( $depth > 1 ) { echo '<div class="col-sm-12 col-md-12"></div>'; }
        ?>

        <div <?php comment_class('row'); ?> id="comment-<?php comment_ID(); ?>">
          <figure class="<?php echo esc_attr( $figure_class ); ?>"><?php echo get_avatar( $comment, 100 ); ?> </figure>
          <div class="<?php echo esc_attr( $main_class ); ?>">
            <div class="comment_name"><?php comment_author($comment->comment_ID); ?> <?php comment_reply_link( array_merge( $args, array(  'reply_text' => esc_html__( 'Reply', 'healthflex' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?></div>
            <div class="comment_date"><i class="fa fa-clock-o"></i> <?php comment_date( '', $comment->comment_ID ) ?></div>
            <div class="the_comment">
              <?php comment_text(); ?>
            </div>
          </div>
        <?php
    }

    static function dev_comment_page_type() {

      $comment = Plethora_Theme::get_this_page( array( 'output' => 'desc' ) );
      $comment .= ' / ID: '. Plethora_Theme::get_this_page();
      if ( !empty( $comment ) ) { 

          print_r( "\n". '<!-- '. $comment .'  -->'."\n" );
      }
    }
////// REUSABLE TEMPLATE METHODS ---> FINISH

  } 
}
?>