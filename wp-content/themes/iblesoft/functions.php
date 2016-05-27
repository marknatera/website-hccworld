<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2015

File Description: Theme Functions file 

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS

class Plethora_Setup { 

    public $theme_ver     = '1.4.3';        // THEME VERSION
    public $framework_ver = '1.4.2';        // PLETHORA FRAMEWORK VERSION SUPPORTED
    public static $mode   = '';                      

    function __construct() {

        // Core DIRs
        define( 'PLE_THEME_DIR',              get_template_directory() );           // Theme folder
        define( 'PLE_THEME_INCLUDES_DIR',     PLE_THEME_DIR . '/includes' );            // Theme includes folder
        // Core URIs
        define( 'PLE_THEME_URI',              get_template_directory_uri() );       // Theme folder
        define( 'PLE_THEME_INCLUDES_URI',     PLE_THEME_URI . '/includes' );            // Theme includes folder
        
        // Basic Includes
        require_once( PLE_THEME_INCLUDES_DIR . '/core/plethora.php' );
        require_once( PLE_THEME_INCLUDES_DIR . '/theme.php' );
        require_once( PLE_THEME_INCLUDES_DIR . '/tgm.php' );

        // Perform some PHP version diagnostics check
        $php_version_diagnostics = false;
        if ( version_compare(PHP_VERSION, '5.4.0') >= 0 ) {
            
            $php_version_diagnostics = true;

        } elseif ( version_compare(PHP_VERSION, '5.3.0') >= 0 ) {

            $php_version_diagnostics = true;
            add_action( 'admin_notices', array( $this, 'php_version_53_notice' ) );

        } else {

            add_action( 'admin_notices', array( $this, 'php_version_52_notice' ) );
        }

        // Instantiate the theme class, if Plethora Framework is installed and PHP version diagnostics are fine
        if ( class_exists('Plethora') && $php_version_diagnostics ) {

            // Create the theme class
            $plethora = new Plethora_Theme( 'healthflex', 'HealthFlex', $this->theme_ver );
            // Tasks performed after theme update
            $this->after_update();

            // Theme adjustments if the library plugin is inactive
            if ( ! Plethora_Theme::is_library_active() ) {

                // Add support for post and page post types ( necessary for content to be displayed )
                add_filter('plethora_supported_post_types', array($this, 'add_basic_posttypes_support' ));

                // Enqueue Google fonts manually
                add_action( 'wp_enqueue_scripts', array($this, 'add_google_fonts_manually' ), 5);

                // Themeconfig for PARTICLES
                Plethora_Theme::set_themeconfig( "PARTICLES", array(
                        'enable'          => true,
                        'color'           => "#bcbcbc",
                        'opacity'         => 0.8,
                        'bgColor'         => "transparent",
                        'bgColorDark'     => "transparent",
                        'colorParallax'   => "#4D83C9",
                        'bgColorParallax' => "transparent",
                ));
            }

            // Temporary notices/fixes
            add_action( 'admin_notices', array( $this, 'vc_update_notice' ) );
        }
    }

    /**
    * Admin notice for 5.2.xx PHP versions
    */
    public function php_version_52_notice() {

        $output  = '<h4 style="margin:0 0 10px;">'. esc_html__( 'Your installation is running under PHP ', 'healthflex' ) ;
        $output .= '<strong>'. PHP_VERSION .'</strong> '.'</h4>';
        $output .= esc_html__( 'To continue working on your project, you have to upgrade your PHP to 5.4 or newer version.', 'healthflex' ) .'<br>';
        $output .= esc_html__( 'Unfortunately we cannot ignore the fact that this PHP version is considered obsolete, non secure and with poor overall performance.', 'healthflex' ) .'<br>';
        $output .= '<strong>'. esc_html__( 'Please help us to deliver high quality and secure products...contact your host and ask for a switch to PHP 5.4 or newer.', 'healthflex' ) .'</strong>' .'<br>';
        $output .= esc_html__( 'This is a simple procedure that any decent hosting company should provide hassles-free. This restriction will disappear after switching to PHP 5.4 or newer.', 'healthflex' );
        $output .= '<p>';
        $output .= '<a href="http://plethorathemes.com/blog/dropping-support-for-php-5-3-x/ " target="_blank">'. esc_html__( 'Read more on our blog', 'healthflex' ) .'</a> | ';
        $output .= '<a href="https://wordpress.org/about/requirements/" target="_blank">'. esc_html__( 'Read more on WordPress recommended host configuration', 'healthflex' ) .'</a>';
        $output .= '</p>';

        echo '<div class="notice notice-error is-dismissible"><p>'. $output .'</p></div>'; 
    }

    /**
    * Admin notice for 5.3.xx PHP versions
    */
    public function php_version_53_notice() {

        if ( isset( $_GET['plethora_php_version_53_notice'] ) && sanitize_key( $_GET['plethora_php_version_53_notice'] ) === 'hide' ) {

            update_option( GENERALOPTION_PREFIX .'php_version_53_notice', 0 );
        }

        $notice_status = get_option( GENERALOPTION_PREFIX .'php_version_53_notice', 1 );

        if ( $notice_status ) {

            $output  = '<h4 style="margin:0 0 10px;">'. esc_html__( 'Your installation is running under PHP ', 'healthflex' ) ;
            $output .= '<strong>'. PHP_VERSION .'</strong> '.'</h4>';
            $output .= esc_html__( 'You may continue working on your project, but keep in mind that', 'healthflex' );
            $output .= '<strong> '.esc_html__( 'Plethora Themes will not provide support for 5.3.x related issues.', 'healthflex' ) .'</strong><br>';
            $output .= esc_html__( 'Unfortunately we cannot ignore the fact that this PHP version is considered obsolete, non secure and with poor overall performance.', 'healthflex' ) .'<br>';
            $output .= '<strong>'. esc_html__( 'Please help us to deliver high quality and secure products...contact your host and ask for a switch to PHP 5.4 or newer.', 'healthflex' ) .'</strong>' .'<br>';
            $output .= esc_html__( 'This is a simple procedure that any decent hosting company should provide hassles-free. This message will disappear after switching to PHP 5.4 or newer.', 'healthflex' );
            $output .= '<p>';
            $output .= '<a href="http://plethorathemes.com/blog/dropping-support-for-php-5-3-x/ " target="_blank">'. esc_html__( 'Read more on our blog', 'healthflex' ) .'</a> | ';
            $output .= '<a href="https://wordpress.org/about/requirements/" target="_blank">'. esc_html__( 'Read more on WordPress recommended host configuration', 'healthflex' ) .'</a> | ';
            $output .= '<a href="'.admin_url( 'admin.php/?page='.THEME_OPTVAR.'') .'&plethora_php_version_53_notice=hide">'. esc_html__( 'Dismiss this notice', 'plethora-framework' ) .'</a>';
            $output .= '</p>';
            echo '<div class="notice notice-error is-dismissible"><p>'. $output .'</p></div>'; 
        }
    }

    /**
    * Admin notice for Visual Composer auto-update issue
    */
    public function vc_update_notice() {

        $plugins = get_plugins( '');
        if ( !empty($plugins['js_composer/js_composer.php']['Version']) && $plugins['js_composer/js_composer.php']['Version'] != '4.11.2.1' ) {

            $output = '<p>'. sprintf( esc_html__( '%sNOTICE%s: on occassion, we experienced failure updating automatically Visual Composer. If this is the case here, please do the following:', 'healthflex' ), '<strong>', '</strong>' ) .'</p>';
            $output .= '<ul>';
            $output .= '<li>'. esc_html__( '1. Deactivate and delete existing Visual Composer plugin files. Note that at this point, this message will disappear.', 'healthflex' ) .'</li>';
            $output .= '<li>'. sprintf( esc_html__( '2. A %sThis theme requires the following plugin%s notice for WPBakery Visual Composer will appear. Click on the %sBegin installing plugin%s link to initiate the plugin installation/activation procedure', 'healthflex' ), '<strong style="color:#0073aa">', '</strong>', '<strong style="color:#0073aa">', '</strong>' ) .'</li>';
            $output .= '<li>'. esc_html__( '3. That\'s it...you are done!', 'healthflex' ) .'</li>';
            $output .= '</ul>';
            echo '<div class="notice notice-warning is-dismissible">'. $output .'</div>';
        }
    }

    /**
    * Enqueue Google fonts manually
    */
    public function add_google_fonts_manually() {

        wp_enqueue_style( 'roboto', 'http://fonts.googleapis.com/css?family=Raleway:400,300,700,800,900,500', false ); 
        wp_enqueue_style( 'lato', 'http://fonts.googleapis.com/css?family=Lato:300,300italic,700,700italic,900,900italic', false ); 
    }


    /**
    * Add support for post/page if the library plugin is inactive
    */
    public function add_basic_posttypes_support( $posttypes ) {

      $posttypes[] = 'post';
      $posttypes[] = 'page';
      array_unique($posttypes);
      return $posttypes;
    }

    /**
    * The method compares theme saved version with this one running. 
    * If different, it executes all actions set right after theme update
    *
    * @since 1.0
    *
    */
    public function after_update() { 

      $theme_version_db = get_option( OPTNAME_THEME_VER );
      if ( $theme_version_db != $this->theme_ver ) { 

        // Recovers TGM notices, even if the user has dismissed this. 
        // MUST be done on every theme update, to make sure the current user gets a notice about the Plethora Framework plugin update
        update_user_meta( get_current_user_id(), 'tgmpa_dismissed_notice', 0 );

        /** 
        * A version update fix added since 1.3.1
        * Remove top bar default text option from all post metaboxes.
        * This is necessary to avoid conflicts with non used saved meta options
        */
        $args = array(
                'posts_per_page'   => -1,
                'meta_key' => METAOPTION_PREFIX .'topbar-col2-text',
                'post_type' => array( 'post', 'page', 'product', 'terminology', 'profile' )
            );
        $posts = get_posts( $args );
        foreach ( $posts as $post ) {

            delete_post_meta( $post->ID, METAOPTION_PREFIX .'topbar-col1-text' );
            delete_post_meta( $post->ID, METAOPTION_PREFIX .'topbar-col2-text' );
        }
        wp_reset_postdata();

        /** 
        * This is a notice for customers that
        * Remove top bar default text option from all post metaboxes.
        * This is necessary to avoid conflicts with non used saved meta options
        */

        // Notice forversion_compare(PHP_VERSION, '5.3.0') >= 0
        
        // After done with all actions, we update saved theme version
        $is_updated = update_option( OPTNAME_THEME_VER, $this->theme_ver );
      }
    }
}

$setup = new Plethora_Setup;