<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2015

File Description: Single Profile Template Parts // Content
*/
if ( !class_exists('Plethora_Template_Single') ) {

  class Plethora_Template_Single { 

  	public function __construct() {
 
        // Special configuration for global wrappers
        add_filter( 'plethora_wrapper_content_class', array( $this, 'wrapper_content_class')); // Clor set configuration
        add_filter( 'plethora_wrapper_column_class', array( $this, 'wrapper_column_class'));
        add_filter( 'plethora_layout', array( $this, 'layout'));

        // Switch loading priorities for media and text columns according to settings
        $layout_content = Plethora_Theme::option( METAOPTION_PREFIX .'profile-layout-content', 'default'); 
        $priority_media = $layout_content === 'default' ? 10 : 20;
        $priority_text = $layout_content === 'default' ? 20 : 10;
        // Media column
        add_action( 'plethora_content', array( $this, 'media_wrapper_open'), $priority_media);  // Post media wrapper opening
        add_action( 'plethora_content', array( $this, 'media'), $priority_media);               // Post media
        add_action( 'plethora_content', array( $this, 'quote'), $priority_media);               // Post media
        add_action( 'plethora_content', array( $this, 'social'), $priority_media);              // Post media
        add_action( 'plethora_content', array( $this, 'authorposts'), $priority_media);        // Author's Posts
        add_action( 'plethora_content', array( $this, 'media_wrapper_close'), $priority_media); // Post media wrapper closing
        // Text column
        add_action( 'plethora_content', array( $this, 'text_wrapper_open'), $priority_text);    // Post main wrapper opening
        add_action( 'plethora_content', array( $this, 'title'), $priority_text);                // Post title
        add_action( 'plethora_content', array( $this, 'content'), $priority_text);              // Post content
        add_action( 'plethora_content', array( $this, 'text_wrapper_close'), $priority_text);   // Post main wrapper closing


  	}

    /**
     * Single profiles don't have a sidebar configuration,
     * so this will set 'no_sidebar' as default
     */
    public static function layout( $layout ) {

      return 'no_sidebar';
    }

    /**
     * Returns single profile media wrapper tag opening
     */
    public static function media_wrapper_open() {

      $size = Plethora_Theme::get_layout() === 'no_sidebar' ? '5' : '4';
      ?>
      <div class="profile_single_photo col-md-<?php echo esc_attr( $size ); ?>">
      <?php
    }

    /**
     * Returns single profile media
     */
    public static function media() {

      $args = array(
            'stretch'      => true, 
            'link_to_post' => false,
            'force_display'=> true 
          );
      echo Plethora_Theme::get_post_media( $args );
    }

    /**
     * Returns single profile quote
     */
    public static function quote() {
     $quote = Plethora_Theme::option( METAOPTION_PREFIX .'profile-quote', ''); 
      if ( !empty( $quote ) ) { 
      ?>  
        <blockquote>
          <p><i><?php echo wp_kses_post( $quote ); ?></i></p>
        </blockquote>
      <?php
      }
    }

    /**
     * Returns single profile socials
     */
    public static function social() {
      $socials  = Plethora_Theme::option( METAOPTION_PREFIX .'profile-social', array() );
      $socials_keys  = $socials['redux_repeater_data'];         
      $output = '';
      foreach ( $socials_keys as $key=>$foo ) { 

        if ( !empty($socials['social_icon'][$key]) && !empty($socials['social_url'][$key])  ) { 

          $escaped_url = substr($socials['social_url'][$key], 0, 7) == 'callto:' ? $socials['social_url'][$key] : esc_url( $socials['social_url'][$key] );
          $output .= '<a href="'. $escaped_url .'" title="'. esc_attr( $socials['social_title'][$key] ).'"><i class="fa '. esc_attr( $socials['social_icon'][$key] ).'"></i></a>';
        }
      }

      if ( !empty( $output ) ) { 
        ?>
        <div class="team_social show">
        <?php echo $output; // escaped on /templates/single_profile/single.php ?>
        </div>
        <?php
      }
    }

    /**
     * Returns single profile media wrapper tag closing
     */
    public static function media_wrapper_close() {
      ?>
      </div>
      <?php
    }

    /**
     * Returns single profile text wrapper tag opening
     */
    public static function text_wrapper_open() {

      $size = Plethora_Theme::get_layout() === 'no_sidebar' ? '7' : '8';
      ?>
      <div class="col-md-<?php echo esc_attr( $size ); ?>">
      <?php
    }

    /**
     * Returns single profile title
     */
    public static function title() {
      $title = Plethora_Theme::get_title( array( 'tag' => 'h1' ));
      $subtitle = Plethora_Theme::get_subtitle();
      if ( !empty($title) || !empty($subtitle) ){ 

        echo '<div class="section_header xbold">';
        echo Plethora_Theme::get_title( array( 'tag' => 'h1' ));
        echo Plethora_Theme::get_subtitle();
        echo '</div>';
      }
    }

    /**
     * Returns single profile content ( depending on format )
     */
    public static function content() {

      the_content();
    }

    /**
     * Returns single profile content ( depending on format )
     */
    public static function authorposts() {

        if ( Plethora_Theme::option( METAOPTION_PREFIX .'profile-authorposts', '0') == '1' ){

          // User info
          $author_id = Plethora_Theme::option( METAOPTION_PREFIX .'profile-user', 0 );
          // User posts query
          $author_postsperpage = Plethora_Theme::option( METAOPTION_PREFIX .'profile-authorposts-num', 5 );
          $args = array(
            'posts_per_page'      => intval($author_postsperpage) ,
            'ignore_sticky_posts' => 0,
            'post_type'           => 'post',
            'author'              => $author_id
          );
          $author_posts = get_posts( $args );  
          $title = Plethora_Theme::option( METAOPTION_PREFIX .'profile-authorposts-heading', 'Latest Posts' );
          foreach ( $author_posts as $author_post ) {
            
            $date                       = new DateTime( $author_post->post_date_gmt );
            $auth_post['title']         = $author_post->post_title;
            $auth_post['permalink']     = get_permalink( $author_post->ID );
            $thumbnail                  = ( has_post_thumbnail( $author_post->ID ))? wp_get_attachment_image_src( get_post_thumbnail_id( $author_post->ID ) ) : false;
            $auth_post['thumbnail_url'] = esc_url( $thumbnail[0] );
            $auth_post['content']       = wp_trim_words( strip_shortcodes( $author_post->post_content ), 20 );
            $auth_post['date']          = $date->format('M j');
            $auth_posts[]               = $auth_post;
          }
          wp_reset_postdata(); // Notice: this had to be here, otherwise is not working (!!)  

          if ( !empty( $auth_posts ) ) {
          ?>
           <div class="pl_latest_news_widget margin_top_half">
              <div class="section_header xbold margin_bottom_third">
            <h3><?php echo esc_html( $title ); ?></h3>
              </div>
            <ul class="media-list">
          <?php foreach ( $auth_posts as $post ) { ?>
              <li class="media">
                   <a href="<?php echo esc_url( $post['permalink'] ); ?>" class="media-photo" style="background-image:url(' <?php echo esc_url( $post['thumbnail_url'] ) ?> ')"></a>
                   <h5 class="media-heading"><a href="<?php echo esc_url( $post['permalink'] ); ?>"><?php echo esc_html( $post['title'] ); ?></a><small><?php echo esc_html( $post['date'] ) ?></small></h5>
                   <p><?php echo esc_html( $post['content'] ) ?></p>
              </li>
          <?php } ?>
            </ul>
           </div>
           <?php } 
        }
    }

    /**
     * Returns single profile text wrapper tag closing
     */
    public static function text_wrapper_close() {
      ?>
      </div>
      <?php
    }

    /**
     * Configures content wrapper to accept color set configuration for profile posts
     */
    public static function wrapper_content_class( $classes ) {
      $classes[] = 'full_height';
      $classes[] = 'vertical_center';
      return $classes;
    }

    /**
     * Configures column wrapper to remove 'col-md-12' class for profile posts
     */
    public static function wrapper_column_class( $classes ) {

      $key = array_search( 'col-md-12', $classes);
      if ( $key !== false ) {

        unset($classes[$key]);
      }
      return $classes;
    }
    
  } 
}    