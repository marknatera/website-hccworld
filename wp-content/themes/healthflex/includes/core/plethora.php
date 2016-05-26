<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2016

File Description: Contains Plethora abstract class methods. 
This class is extended by Plethora_Theme class...do not call directly!

*/
if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


/**
 * This class is the core theme class for every Plethora theme.
 */

abstract class Plethora {

    public static $framework_slug    = 'plethora';      // FRAMEWORK SLUG
    public static $framework_abbr    = 'ple';           // FRAMEWORK PREFIX
    public static $framework_name    = 'Plethora';      // FRAMEWORK DISPLAY TITLE
    public static $framework_version = '1.0.0';         // FRAMEWORK VERSION
    public $theme_slug;                                 // THEME SLUG ( MUST BE DEFINED ON PLETHORA_THEME )
    public $theme_name;                                 // THEME DISPLAY NAME ( MUST BE DEFINED ON PLETHORA_THEME )
    public $theme_version;                              // THEME VERSION  ( MUST BE DEFINED ON PLETHORA_THEME )
    private $index;                                     // FEATURES INDEX

    /**
     * It initiates all necessary framework methods for loading and initiating the theme. 
     * NOTICE: Always invoked from Plethora_Theme class
     *
     * @since 1.0
     */
    public function load_framework() {

      # FRAMEWORK VERSION
      define( 'PLETHORA_VERSION',          self::$framework_version );            // Plethora Version

      # GENERAL CONSTANTS
      define( 'THEME_SLUG',             $this->theme_slug);                               // Theme slug 
      define( 'THEME_VERSION',          $this->theme_version );                               // Theme version 
      define( 'THEME_TXTDOMAIN',        $this->theme_slug );                              // Theme textdomain 
      define( 'THEME_OPTVAR',           self::$framework_slug .'_options' );              // Theme options variable 
      define( 'THEME_OPTIONSPAGE',      self::$framework_slug .'_options' );              // Theme options page slug
      define( 'THEME_OPTIONSPAGETITLE', $this->theme_name . __(' Theme Options Panel', 'plethora-framework') );   // Theme options page title, displayed on browser title
      define( 'THEME_OPTIONSPAGEMENU',  __('Theme Options', 'plethora-framework') );                              // Theme options page menu title
      define( 'THEME_DISPLAYNAME',      $this->theme_name );                              // Theme display name

      # OPTION NAMES
      define( 'OPTNAME_FEATURES_INDEX',    self::$framework_slug .'_features_index' );              // Plethora features index option 
      define( 'OPTNAME_CORE_VER',     self::$framework_slug .'_framework_ver_installed' );     // Framework installed theme version
      define( 'OPTNAME_THEME_VER',     self::$framework_slug .'_theme_ver_installed' );     // Theme installed theme version

      # FILE & CLASS PREFIXES
      define( 'CC_PREFIX',                 ucfirst(self::$framework_slug ) .'_'); // Controller/Feature Classes Prefix
      define( 'CF_PREFIX',                 self::$framework_slug .'-' );          // Controller/Feature Classes Filename Prefix

      # OPTION PREFIXES
      define( 'GENERALOPTION_PREFIX',      self::$framework_slug .'_' );          // Prefix used for all helper options
      define( 'THEMEOPTION_PREFIX',        self::$framework_abbr .'-' );          // Prefix used for all theme options
      define( 'METAOPTION_PREFIX',         self::$framework_abbr .'-' );          // Prefix used for all meta options
      define( 'TERMSMETA_PREFIX',          self::$framework_abbr .'-' );          // Prefix used for all term meta options
      define( 'USEROPTION_PREFIX',         self::$framework_abbr .'-' );          // Prefix used for all user options
      define( 'SHORTCODES_PREFIX',         self::$framework_slug .'_' );          // Prefix used for dynamic shortcode slugs ( e.g. shortcodes )
      define( 'WIDGETS_PREFIX',            self::$framework_slug .'-' );          // Prefix used for dynamic widgets slugs
      define( 'ASSETS_PREFIX',             self::$framework_slug );               // Prefix used for dynamic widgets slugs

      # CORE URIs
      define( 'PLE_CORE_URI',             PLE_THEME_INCLUDES_URI . '/core' );   // CORE folder
      define( 'PLE_CORE_ASSETS_URI',      PLE_CORE_URI . '/assets' );           // Framework assets folder (scripts, styles & images)
      define( 'PLE_CORE_HELPERS_URI',     PLE_CORE_URI . '/helpers' );          // Framework helpers folder
      define( 'PLE_CORE_CONTROLLERS_URI', PLE_CORE_URI . '/controllers' );      // Framework controllers folder
      define( 'PLE_CORE_FEATURES_URI',    PLE_CORE_URI . '/features' );         // Framework features folder
      define( 'PLE_CORE_LIBS_URI',        PLE_CORE_URI . '/libs' );             // Framework library folder
      define( 'PLE_CORE_JS_URI',          PLE_CORE_ASSETS_URI . '/js' );        // Framework JavaScript folder

      # CORE DIRs
      define( 'PLE_CORE_DIR',             PLE_THEME_INCLUDES_DIR . '/core'  );          // Framework folder
      define( 'PLE_CORE_ASSETS_DIR',      PLE_CORE_DIR . '/assets' );            // Framework assets folder (scripts, styles & images)
      define( 'PLE_CORE_HELPERS_DIR',     PLE_CORE_DIR . '/helpers' );           // Framework library folder
      define( 'PLE_CORE_CONTROLLERS_DIR', PLE_CORE_DIR . '/controllers' );       // Framework controllers folder
      define( 'PLE_CORE_FEATURES_DIR',    PLE_CORE_DIR . '/features' );          // Framework features folder
      define( 'PLE_CORE_LIBS_DIR',        PLE_CORE_DIR . '/libs' );              // Framework library folder
      define( 'PLE_CORE_JS_DIR',          PLE_CORE_ASSETS_DIR . '/js' );        // Framework JavaScript folder
      define( 'PLE_CORE_JS_LIBS_DIR',     PLE_CORE_JS_DIR . '/libs' );          // Framework JavaScript Libraries folder

      # FEATURES LIBRARY PLUGIN URIs
      if ( Plethora_Theme::is_library_active() ) { // this makes it easier to track issues!

        # FEATURES LIBRARY PLUGIN URIs
        define( 'PLE_FLIB_URI',             WP_PLUGIN_URL .'/plethora-featureslib' );   // Framework folder
        define( 'PLE_FLIB_ASSETS_URI',      PLE_FLIB_URI . '/assets' );           // Framework assets folder (scripts, styles & images)
        define( 'PLE_FLIB_FEATURES_URI',    PLE_FLIB_URI . '/features' );         // Framework features folder
        define( 'PLE_FLIB_LIBS_URI',        PLE_FLIB_URI . '/libs' );               // Framework JavaScript folder
        define( 'PLE_FLIB_JS_URI',          PLE_FLIB_ASSETS_URI . '/js' );               // Framework JavaScript folder

        # FEATURES LIBRARY PLUGIN DIRs
        define( 'PLE_FLIB_DIR',             WP_PLUGIN_DIR .'/plethora-featureslib' );   // Framework folder
        define( 'PLE_FLIB_ASSETS_DIR',      PLE_FLIB_DIR . '/assets' );           // Framework assets folder (scripts, styles & images)
        define( 'PLE_FLIB_CONTROLLERS_DIR', PLE_FLIB_DIR . '/controllers' );         // Framework features folder
        define( 'PLE_FLIB_FEATURES_DIR',    PLE_FLIB_DIR . '/features' );         // Framework features folder
        define( 'PLE_FLIB_LIBS_DIR',        PLE_FLIB_DIR . '/libs' );              // Framework library folder
        define( 'PLE_FLIB_JS_DIR',          PLE_FLIB_ASSETS_DIR . '/js' );               // Framework JavaScript folder
      }

      # CORE INCLUDES
      require_once( PLE_CORE_HELPERS_DIR .'/plethora-wp.php' );
      require_once( PLE_CORE_HELPERS_DIR .'/plethora-index.php' );
      require_once( PLE_CORE_HELPERS_DIR .'/plethora-optionsframework.php' );
      require_once( PLE_CORE_HELPERS_DIR .'/plethora-system.php' );
      require_once( PLE_CORE_HELPERS_DIR .'/plethora-fields.php' );
      require_once( PLE_CORE_HELPERS_DIR .'/plethora-termsmeta.php' );

      # THEME INCLUDES
      require_once( PLE_THEME_INCLUDES_DIR . '/template.php' );

      # LOAD FEATURES AND GET THE INDEX INFO
      $index = new Plethora_Index();
      $index->load_features();
      global $plethora;
      $plethora['controllers'] = $index->controllers;
      $plethora['features']    = $index->get();

      // # ADMIN ASSETS REGISTRATION
      add_action( 'admin_enqueue_scripts', array( $this, 'assets_admin' ));         // Enqueue admin assets

      // # OPTIONS FRAMEWORK HOOK
      add_action( 'init', array( $this, 'load_options_framework' ), 5);        // Enqueue admin assets 

      // PLEFIXME: Temporary workaround for THEMECONFIG js variables. Should be replaced gradualy with normal init scripts ( added with Plethora_Theme::enqueue_init_script() method )  
      $this->set_themeconfig( "GENERAL", array('debug' => false));
      add_action( 'wp_footer', array( $this, 'localize_themeconfig' ));  // notice...priority should be 2, after theme.js registration  
  }

  /**
   * Loads Options Framework, Options & Metabox configuration
   */
   public function load_options_framework() {

      if ( Plethora_Theme::is_library_active() ) { // this makes it easier to track issues!

        # LOAD OPTIONS FRAMEWORK ( notice: this class does not invoke theme options, rather than loading only the class file and some static methods)
        new Plethora_Optionsframework;

        # INSTANTIATE THEME OPTIONS CLASS ( class file already included on Plethora_Optionsframework class )
        global $plethora_options_config;
        $plethora_options_config = new Plethora_Themeoptions;
      }
   }

  /**
   * Register & Enqueue fixed admin only scripts/styles
   */
   public function assets_admin() {

        wp_register_style( 'plethora-admin', PLE_CORE_ASSETS_URI . '/admin.css' );
        wp_enqueue_style( 'plethora-admin' );

        wp_register_script( 'plethora-admin', PLE_CORE_ASSETS_URI . '/admin.js' );
        wp_enqueue_script('plethora-admin');
   }

/*
                                         _____  _____  ___      
                                        |  _  ||  __ \|   |
                                        |     ||   __/|   |
                                        |__|__||__|   |___|
*/
// PUBLIC REUSABLE STATIC METHODS ----> START

  /**
   * PUBLIC | Checks if the installation is in development mode ( set by user on theme options )
   * @since 1.0
   */
  public static function is_developermode() {

    // Notice...comments MUST be disabled in this option call, to avoid endless loop between methods
    $development = self::option( THEMEOPTION_PREFIX . 'dev', 0, 0, 0 );
    if ( $development == 1 ) { 

      return 1;
    }
    
    return 0;
  }

  /**
   * PUBLIC | Checks if the Plethora Library plugin is active
   * @since 1.0
   */
  public static function is_library_active() {

    if ( ! function_exists( 'is_plugin_active ') ) {

      include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }

    if ( is_plugin_active( 'plethora-featureslib/plethora_featureslib.php' ) ) {

      return true;
    }

    return false;
  }


  /**
   * PUBLIC | Returns supported feature controllers info
   * @since 1.0
   */
  public static function get_controllers() {

      global $plethora;
      $controllers = $plethora['controllers'];
      return $controllers;
  }

  /**
   * PUBLIC | Returns supported features info
   * @since 1.0
   */
  public static function get_features( $args ) {

      $default_args = array( 
              'controller'   => '',    // Controller slug returns all controller features...empty returns ALL features info
              'output'   => 'all'     // 'all', 'slugs' ( returns feature slugs )
      );
      $args = wp_parse_args( $args, $default_args);          // MERGE GIVEN ARGS WITH DEFAULTS
      extract( $args );

      global $plethora;
      $controllers = self::get_controllers();
      $all_features = $plethora['features'];
      $features = array();

      foreach ( $all_features as $key => $feature ) {

        if ( empty( $controller ) || ( !empty( $controller ) && array_key_exists( $controller, $controllers ) && $feature['controller'] === $controller ) ) {

            if ( $feature['verified'] ) {

              $features[$feature['slug']] = $output === 'slugs' ? $feature['slug'] : $feature;
            }
        }
      }

      return $features;
  }

  /**
   * PUBLIC | Returns supported features info
   * 'attr' argument can take any the following values:
   *    'assets'                        // returns JS / CSS asset registrations
   *    'base_class'                    // returns base class name
   *    'base_path'                     // returns base class file path
   *    'class'                         // returns class name
   *    'controller'                    // returns the feature's controller
   *    'dynamic_construct'             // returns TRUE if this feature's class is instanciated dynamically
   *    'dynamic_method'                // returns additional dynamic method name
   *    'feature_title'                 // returns feature's title
   *    'feature_description'           // returns feature's description
   *    'folder'                        // returns feature's folder path
   *    'plethora_supported'            // returns true if feature is Plethora, false if a custom one
   *    'path'                          // returns class file path
   *    'slug'                          // returns the slug
   *    'theme_option_control'          // returns TRUE if this is an option controlled feature
   *    'theme_option_control_config'   // returns theme options activation/deactivation field configuration
   *    'theme_option_default'          // returns initial activation status for option controlled features ( TRUE / FALSE )
   *    'theme_option_requires'         // returns array with other required features
   *    'theme_option_status'           // returns feature's activation status ( TRUE / FALSE )
   *    'verified'                      // returns 'true' if feature is working properly, or 'false' if not loaded for some reason
   *    'wp_slug'                       // returns wp_slug ( this is for shortcodes / widgets only )
   *   
   * @since 1.0
   */
  public static function get_feature( $args ) {

      $default_args = array( 
              'controller' => false,   // Controller slug filter ( MANDATORY )
              'feature'    => false,   // Feature slug filter ( MANDATORY )
              'attr'       => '',      // will return ONLY the specified feature's attribute value ( check method description above )
      );
      $args = wp_parse_args( $args, $default_args);          // MERGE GIVEN ARGS WITH DEFAULTS
      extract( $args );

      if ( $controller && $feature ) { 

        global $plethora;
        $features = $plethora['features'];

        if ( isset( $features[$controller .'-'. $feature ] ) && !empty( $attr ) && isset( $features[$controller .'-'. $feature ][$attr] ) ) {

          return $features[$controller .'-'. $feature ][$attr];
        
        } elseif ( isset( $features[$controller .'-'. $feature ] ) && empty( $attr ) ) {

          return $features[$controller .'-'. $feature ];
        }
      }

      return false;
  }

  /**
   * PUBLIC | Checks if the given feature is active
   * @param $group ( feature group slug ), $slug ( feature slug )
   * @since 1.0
   */
  public static function is_feature_activated( $controller, $feature ) {
    
    // no margin for errors!
    if ( empty( $controller ) || empty( $feature ) ) { return false ; }

    $feature = self::get_feature( array( 'controller' => $controller, 'feature' => $feature ) );
    if ( ! empty( $feature ) ) {

        return isset( $feature['theme_option_status'] ) ? $feature['theme_option_status'] : false;
    }

    return false;
  }

  /**
   * Load theme's gettext strings
   */
  public static function textdomain() { 

      load_theme_textdomain( THEME_TXTDOMAIN, get_template_directory() . '/languages' );
      
      if ( Plethora_Theme::is_library_active() ) { 
        
        load_plugin_textdomain('plethora-framework', false, PLE_FLIB_DIR . '/languages' );
      }
  }

  /**
   * PUBLIC | Will enqueue a handle's init script only if this is present on page  
   * @since 1.0
  */
  public static function enqueue_init_script( $args = array() ) {

    $default_args = array( 
            'handle'   => '',       // Main script(s) handle(s) WITHOUT  PREFIX ( main script is the script that will be initialized )
            'function' => '',       // Function/Class method that returns the markup ( class method should be arrays )
            'script'   => '',       // Ready script to enqueue...useful for scripts that have variable values
            'header'   => false,    // If true, script will be included on header, otherwise on footer
            'multiple' => false     // If true, script will be allowed to enqueued more than once
    );

    $args = wp_parse_args( $args, $default_args);          // MERGE GIVEN ARGS WITH DEFAULTS

    // extract & verify arguments
    extract( $args );
    $handle = !is_array( $handle ) ? $handle : array( $handle );
    $callback = empty( $function ) ? $script : $function;                      // select between func or normal script
    if ( empty( $handle ) || ( empty( $callback )  ) ) { return false; }       // no point to continue

    // Add init script to global array variable 'plethora_init_scripts'
    global $plethora_init_scripts;
    $plethora_init_scripts = empty($plethora_init_scripts) ? array() : $plethora_init_scripts; // avoid php warning if empty
    if ( ! $multiple && array_key_exists($handle, $plethora_init_scripts ) ) { return true; }  // exit if multiple not allowed and script exists
    $plethora_init_scripts[$handle][] = array( 
                                        'callback_type' => empty( $function ) ? 'script' : 'function',
                                        'callback'      => $callback,
                                        'position'      => $header ? 'header' : 'footer',
                                      );
    return true;
  }


  /**
   * PUBLIC | Wrap method for any init pattern set on Plethora_Module_Script
   * @param $handle, $vars ( handle: non plethora prefixed script handle slug, args: variables to use in init script )  
   * @since 1.0
   */
  public static function get_init_script( $handle, $vars = array() ) {

      if ( method_exists( 'Plethora_Module_Script', $handle ) ) {

        $init_script = call_user_func( array( 'Plethora_Module_Script', $handle  ), $vars );
      }

      // add hook..this allows to override existing or add new init script patterns 
      $init_script = apply_filters( 'plethora_init_script_'. $handle, $init_script, $vars );

      return $init_script;
  }

  /**
   * PUBLIC | Similar to native get_the_id(), but will return the page id for any type of supported archive too
   * Notice: You should not treat this as an alternative of native WP get_the_id() function
   *
   */
  public static function get_this_page( $args = array() ) {

    $default_args = array( 
            'output' => 'ID',        // 'id' | 'desc'    
            );
    // Merge user given arguments with default
    $args = wp_parse_args( $args, $default_args);

    if ( ( is_front_page() && is_home() ))  { // This is not a desired output...users should select static pages for home/blog 

      return $args['output'] === 'ID' ? 'frontpage' : 'Native Latest Posts Archive';

    } elseif (  is_404() ) { 

      return $args['output'] === 'ID' ? '404page' : '404 Page';

    } elseif ( class_exists( 'Plethora_Module_WooCommerce' ) && ( is_shop() || (is_shop() && is_search()) || is_product_category() || is_product_tag() ) ) { // native support for Woo

      return $args['output'] === 'ID' ? get_option( 'woocommerce_shop_page_id', 0) : 'WooCommerce Shop Page';

    } elseif ( is_home() || is_search() || is_category() || is_tag() || is_author() || is_date() ) { 

      return $args['output'] === 'ID' ? get_option('page_for_posts') : 'Static Posts Page'; // native posts archive

    } elseif ( is_archive() || is_tax() ) { // custom archive
      $post_type = get_post_type();
      return $args['output'] === 'ID' ? 0 : 'Plethora Custom Archive Page ( '. $post_type .' )'; // native posts archive

    } else { 

      return $args['output'] === 'ID' ? get_queried_object_id() : 'Page or Post or Custom Post';
    }           
    return 0;
  }    

  /**
   * PUBLIC | This is a method for displaying post/pages override options. Checks post meta, if nothing found uses the default theme option
   * Notice: you should always give a specific postid when used in loop!
   * @since 1.0
   *
   */

  public static function option( $option_id, $user_value = '', $postid = 0, $comment = 1 ) {

    $postid          = $postid == 0 ? self::get_this_page() : $postid;                            // if no postid is given, use self::get_the_ID(). 
    $post_option_val = is_numeric( $postid ) ? get_post_meta( $postid, $option_id, true ) : '';   // If $postid is a number, then search first if post has saved a value for this option on its metaboxes

    // If nothing is found on this post meta, then check theme defaults for this option, otherwise use value that set on option call
    if ( ( is_array($post_option_val) && empty($post_option_val) ) || ( !is_array($post_option_val) && $post_option_val == '' )) { 

        $theme_options    = get_option( THEME_OPTVAR ); // Use this please...NOT SAFE TO USE the global redux option
        $theme_option_val = ( isset( $theme_options[$option_id] )) ? $theme_options[$option_id] : $user_value;
        $source           = ( isset( $theme_options[$option_id] )) ? 'Theme options value' : 'Value given on option call';
        $option_val       = $theme_option_val;

    } else { 

      $option_val = $post_option_val;
      $source     = 'Post meta value';

    }
    
    // Produce a comment
    if ( is_array( $option_val )) { $comment_option_val = json_encode($option_val); } else { $comment_option_val = $option_val; }

    if ( $comment == 1 ) { 
      self::dev_comment('Option called (postid|option|value|info): '. $postid .' | '. $option_id .' | '. $comment_option_val .' | '. $source .'', 'options');
    }
    // Return the value
    return $option_val;
  }

  /**
  * PUBLIC | Handles developer comments according to theme settings
  * @since 1.0
  *
  */
  public static function dev_comment( $comment = '', $commentgroup = '' ) {

    // Notice...comments MUST be disabled in this option call, to avoid endless loop between methods
    $commentgroup_status =  self::option( THEMEOPTION_PREFIX . 'dev-'. $commentgroup, 'disable', 0, 0);

    if ( !is_admin() && !is_feed() && did_action( 'get_header' ) && current_user_can('manage_options') && $commentgroup !== 'layout' ) { 

      if ( self::is_developermode() && !empty( $comment) && $commentgroup_status == 'enable' ) { 

          print_r( "\n". '<!-- '. $comment .'  -->'."\n" );
      }
    } 

    if ( !is_admin() && !is_feed() && $commentgroup === 'layout' && $commentgroup_status == 'enable' ) {

      if ( !empty( $comment ) ) { 

          print_r( "\n". '<!-- '. $comment .'  -->'."\n" );
      }
    }
  }

  /**
   * PUBLIC | Returns native, Plethora CPTs and any non Plethora CPT that has frontend archive/single views. 
   * Notice: Will work as expected after 'init' hook 
   * @since 1.0
   */
  public static function get_supported_post_types( $args = array() ) {
    
    $default_args = array( 
            'type'          => 'singles',   // 'singles', 'archives'
            'output'        => 'names',     // 'names' | 'objects'    
            'public'        => true,        // true/false for post types that have single post pages on frontend
            'exclude'       => array(),     // exclude those post types from output
            'plethora_only' => false,       // return only those that don't have Plethora frontend implementation ( this is checked according to Plethora_Posttype_ class )
            );

    // Merge user given arguments with default
    $args = wp_parse_args( $args, $default_args);
    $args['exclude'] = is_array($args['exclude']) ? $args['exclude'] : array($args['exclude']); // Make sure that this will be an array

    // get the built in first!
    $builtin_post_types['post'] = get_post_type_object( 'post' );
    $builtin_post_types['page'] = get_post_type_object( 'page' );
    // now we want the CPT ones
    $query_args = array( 'public' => $args['public'], '_builtin' => false );
    $unfiltered_results = get_post_types( $query_args, 'objects' );
    $unfiltered_results = array_merge( $builtin_post_types, $unfiltered_results );

    // Filtering according to arguments
    $supported_posttypes = array();
    foreach ( $unfiltered_results as $post_type => $post_type_obj ) {

      // Check if this is excluded
      if ( ! in_array( $post_type, $args['exclude'] ) ) {

        // Get singles / archives
        if ( $args['type'] === 'singles' || ( ( $args['type'] === 'archives' && !empty( $post_type_obj->has_archive ) && $post_type_obj->has_archive ) || $post_type === 'post' ) ) {

          // Get plethora / non plethora
          if ( $args['plethora_only'] && class_exists( 'Plethora_Posttype_'. ucfirst( $post_type ) ) ) {

            $supported_posttypes[$post_type] = $args['output'] === 'objects' ? $post_type_obj : $post_type;
          
          } elseif ( ! $args['plethora_only'] ) {

            $supported_posttypes[$post_type] = $args['output'] === 'objects' ? $post_type_obj : $post_type;
          }
        }
      }
    }

    return apply_filters( 'plethora_supported_post_types', $supported_posttypes, $args ) ;
  }

  /**
   * PUBLIC | Returns native, Plethora and any non Plethora archive page. 
   * Notice: A valid Plethora archive should be assigned on a page
   * Notice: Will work as expected after 'init' hook 
   * @since 1.0
   */
  public static function get_archive_page( $post_type, $args = array() ) {

    $default_args = array( 
            'output' => 'ID',     // 'ID' ( static page object ) | 'object' ( static page object )  | 'link' ( native OR static page link )   
            );

    // Merge user given arguments with default
    $args = wp_parse_args( $args, $default_args);

    // Get supported archives list
    $supported_archives = self::get_supported_post_types( array( 'type' => 'archives' ) );
    
    // Time to filter
    if ( in_array( $post_type, $supported_archives ) ) { 

      switch ($post_type) {
        case 'product':
          $page_id = get_option( 'woocommerce_shop_page_id', 0);
          $link = $page_id ? get_page_link( $page_id ) : get_post_type_archive_link( $post_type );
          break;
        default:
          $page_id = get_option( 'page_for_'. $post_type .'s', 0);
          $link = $page_id ? get_page_link( $page_id ) : get_post_type_archive_link( $post_type );
          break;
      }

      if ( $args['output'] === 'object' ) {

          $page_object = get_post( $page_id );
          return $page_object;

      } elseif ( $args['output'] === 'link' ) {

          $page_url = get_post_type_archive_link();

      } else {

          return $page_id;
      }

      return false;
    }
  }

  /**
   * PUBLIC | Returns native, Plethora and any non Plethora archive pages. 
   * Notice: Will work as expected after 'init' hook 
   * @since 1.0
   */
  public static function get_archive_pages( $args = array() ) {

    $default_args = array( 
            'output' => 'ID',       // 'ID' | 'object'    
            'exclude' => array(),   // array with exclude post type archives    
            );

    // Merge user given arguments with default
    $args = wp_parse_args( $args, $default_args);
    $args['exclude'] = is_array($args['exclude']) ? $args['exclude'] : array($args['exclude']); // Make sure that this will be an array

    $supported_archives = self::get_supported_post_types( array( 'type' => 'archives' ) );
    $supported_archives = array_diff( $supported_archives, $args['exclude'] ); // Excludes
    
    $archive_pages = array();
    foreach ( $supported_archives as $key=>$post_type ) {

      $archive_pages[$post_type] =  self::get_archive_page( $post_type, $args );
    }

    return $archive_pages;
  }

  /**
   * PUBLIC | Returns true if the current or given post_type is a Plethora supported archive page. 
   * Notice: A plethora archive page is any supported native/custom archive or any supported native/custom taxonomy
   * Notice: Will work as expected after 'init' hook 
   * @since 1.0
   */
  public static function is_archive_page( $post_type = '' ) {

    if ( !empty( $post_type ) ) {

      $pageid = self::get_archive_page( $post_type );
      if ( $pageid == get_the_id() ) { return true; }

    } else {

      if ( get_post_type() === 'post' && !is_singular() ) { return true; } // This is blog
      if ( is_post_type_archive() ) { return true; } // if is a CPT archive
      if ( is_tax() ) { return true; } // if any type of taxonomy
    }
    return false;
  }

  /**
   * PUBLIC | Passing script variables to theme.js file
   * @param $var_group ( variables group name
   * @param $vars ( array with values in key=>value format )
   * @since 1.0
   */
  public static function set_themeconfig( $var_group, $vars = array() ) {

      if ( ! empty( $var_group ) && ! empty( $vars ) ) {

          self::themeconfig( $var_group, $vars );
      }
  }

  /**
   * INTERNAL | Handles script variables for theme.js
   * @since 1.0
   */
  public static function themeconfig( $var_group, $vars ) { 

    global $plethora_themeconfig;
    if ( empty( $plethora_themeconfig ) ) { $plethora_themeconfig = array(); }
    
    if ( isset( $plethora_themeconfig[$var_group] ) ) { // merge if var group exists

      $vars = array_merge_recursive( $plethora_themeconfig[$var_group], $vars );
      $plethora_themeconfig[$var_group] = $vars;

    } else {

      $plethora_themeconfig[$var_group] = $vars; // add vars to new var group
    }

    ksort( $plethora_themeconfig );
  }

  /**
   * INTERNAL | Pasing script variables ( in CDATA format ) to theme.js
   * @since 1.0
   */
  public function localize_themeconfig() {

    global $plethora_themeconfig;
    $plethora_themeconfig = is_array( $plethora_themeconfig ) ? $plethora_themeconfig : array();
    wp_localize_script( ASSETS_PREFIX .'-init', 'themeConfig', $plethora_themeconfig );
  }

  /**
   * PUBLIC | Adds attributes ( ie. class, id, style, etc. ) to core layout container tags
   * @since 1.0
   */
  public static function add_container_attr( $container, $attr_name, $attr_values ) {

    if ( !empty( $container ) && !empty( $attr_name ) && !empty( $attr_values )  ) {

      // make sure that $attr_value is array
      $attr_values_arr = is_array( $attr_values ) ? $attr_values : array( $attr_values );
      
      // make sure that an array record exists on $plethora global
      global $plethora;
      if ( ! isset( $plethora['layout'][$container][$attr_name] ) ) {

        $plethora['layout'][$container][$attr_name] = array();
      }

      // add the user given values to the $plethora global
      foreach ( $attr_values_arr as $attr_value ) {

        $attr_value_key = sanitize_key( $attr_value );
        $plethora['layout'][$container][$attr_name][$attr_value_key] = $attr_value;
      }

      // finally add the filter
      return add_filter( 'plethora_container_'. $container .'_atts', array( 'Plethora_Theme', 'filter_container_attr' ) );
    }

    return false;
  }

  /**
   * PUBLIC | Remove attributes ( ie. class, id, style, etc. ) from core layout container tags
   * @since 1.0
   */
  public static function remove_container_attr( $container, $attr_name, $attr_values = array() ) {

    if ( !empty( $container ) && !empty( $attr_name )  ) {

      // make sure that $attr_value is array
      $attr_values_arr = is_array( $attr_values ) ? $attr_values : array( $attr_values );

      global $plethora;
      if ( !empty( $attr_values_arr ) ) {
        foreach ( $attr_values_arr as $attr_value ) {
           
           if ( !empty( $attr_value ) ) {

              $attr_value_key = sanitize_key( $attr_value );
              unset( $plethora['layout'][$container][$attr_name][$attr_value_key] );

              // if attribute is empty of values, remove it completely
              if ( empty( $plethora['layout'][$container][$attr_name] ) ) {

                unset( $plethora['layout'][$container][$attr_name] );
              }
           }
        }

      } else {

            unset( $plethora['layout'][$container][$attr_name] );
      }

      // add the filter
      return add_filter( 'plethora_container_'. $container .'_atts', array( 'Plethora_Theme', 'filter_container_attr' ), 20 );
    }

    return false;
  }

  /**
   * INTERNAL | Manages add_layout_attr filtering
   * @since 1.0
   */
  public static function filter_container_attr( $attrs ) {

    // get container name
    $this_filter = current_filter();
    $this_filter = str_replace('plethora_container_', '', $this_filter );
    $container     = str_replace('_atts', '', $this_filter );

    // make sure that attrs is an array
    $attrs = is_array( $attrs ) ? $attrs : array( $attrs );
    $filtered_attrs = array();

    global $plethora;
    if ( !empty( $plethora['layout'][$container] ) ) {
      
      $unfiltered_attrs = $plethora['layout'][$container];

      foreach ( $unfiltered_attrs as $attr_name => $attr_values ) {

        if ( !empty( $attr_values ) ) {

          if ( is_string( $attr_values ) ) {

             $attr_value_key = sanitize_key( $attr_values );
             $filtered_attrs[$attr_name][$attr_value_key] = $attr_values;
           
          } elseif ( is_array( $attr_values ) ) {

             $attr_values = array_unique( $attr_values );

             foreach ( $attr_values as $attr_value_key => $attr_value ) {

              $filtered_attrs[$attr_name][$attr_value_key] = $attr_value;
             }
          }
        }
      }
    }
    $attrs = array_merge( $filtered_attrs, $attrs );

    return $attrs;
  }

  /**
   * PUBLIC | Will return page layout settings according to given post type. 
   * Works with all WP native and Plethora created single/archive pages
   * Notice: you should follow the same layout option naming pattern for this to work with your CPTs
   * Notice: Will work after 'init' hook 
   * @since 1.0
  */
  public static function get_layout( $post_type = '' ) {

    $post_type = empty( $post_type ) ? get_post_type() : $post_type ;
    // if showing a single page/post/cpt
    if ( is_search() ) {

      $layout = Plethora_Theme::option( METAOPTION_PREFIX . 'search-layout', 'right_sidebar'); 

    } elseif ( is_singular() ) {

      $layout = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-layout', 'right_sidebar'); 

    } elseif ( self::is_archive_page() ) {

      $layout = Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-layout', 'right_sidebar');

    } elseif ( is_404() ) {

      $layout = 'no_sidebar'; // layout for 404 is full by default

    } else {

      $layout = Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-layout', 'right_sidebar');
    }
   
    // Filter and return layout key
    return apply_filters( 'plethora_layout', $layout );
  }

  /**
   * PUBLIC | Will return main page layout settings according to given post type. 
   * Works with all WP native and Plethora created single/archive pages
   * Notice: you should follow the same sidebar option naming pattern for this to work with your CPTs
   * Notice: Will work as expected after 'init' hook 
   * @since 1.0
   */
  public static function get_main_sidebar( $post_type = '' ) {

    $post_type = empty( $post_type ) ? get_post_type() : $post_type ;
    // if showing a single page/post/cpt
    if ( is_search() ) {

      $sidebar  = Plethora_Theme::option( METAOPTION_PREFIX .'search-sidebar', 'sidebar-default'); 

    } elseif ( is_singular() ) {

      $sidebar = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-sidebar', 'sidebar-default'); 
    
    } elseif ( self::is_archive_page() ) {

      $sidebar  = Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-sidebar', 'sidebar-default'); 

    } else {

      $sidebar = Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-sidebar', 'sidebar-default'); 
    }
    // Filter and return sidebar key
    return apply_filters( 'plethora_main_sidebar', $sidebar );
  }

  /**
   * PUBLIC | Will return archive grid class according to settings. 
   * Works with all WP native and Plethora created archive pages
   * Notice: you should follow the same grid type option naming pattern for this to work with your CPTs
   * Notice: Will work as expected after 'init' hook 
   * @since 1.0
   */
  public static function get_archive_list( $args = array() ) {

    $default_args = array( 
            'output'      => 'option',    // 'option' | 'class'    
            'add_classes' => array(),     // APPEND ADDITIONAL CLASSES WHEN 'output' == 'class'. In example, this will output a 'masonry boxed_children' class ( 'add_classes' => array( 'masonry' => array( 'boxed_children' ) ) ) 
            );

    $args   = wp_parse_args( $args, $default_args);  // MERGE GIVEN ARGS WITH DEFAULTS
    $output = '';

    if ( self::is_archive_page() ) {

      $post_type  = get_post_type();
      $list_type = Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-listtype', 'classic');
      
      if ( $args['output'] === 'class') {
        $output[] = $list_type;
        if ( isset($args['add_classes'][$list_type] ) ) {
          foreach ( $args['add_classes'][$list_type] as $key=>$addclass ) {

            $output[] = $addclass;  // output classes setup
          }
        }

      } else {

        $output = $list_type; // output the option value
      }
    } 

    // APPLY FILTER ONLY ON CLASSES OUTPUT !!! )
    if ( $args['output'] === 'class' ) {

        $output = apply_filters( 'plethora_archive_list_class', $output ); // apply filters ONLY on classes output
    }
    return $output;
  }

  /**
   * PUBLIC | Returns any Plethora post/archive title ( views: singular, archive, 404, search  )
   * @since 1.0
   */
  public static function get_title( $args = array() ) {

    $default_args = array( 
            'tag'           => 'h2',       // HTML tag ( leave empty for raw output )    
            'class'         => array('post_title'), // HTML tag class(es)    
            'id'            => '',         // HTML tag id    
            'listing'       => false,      // this MUST be set 'true' for listing view requests ( will add a link too )   
            'link'          => true,       // adds a post link ( requires 'listing' set to true )   
            'force_display' => false,      // force title display without checking for options    
            'post_type'     => '',         // force specific post type    
            );

    $args = wp_parse_args( $args, $default_args); // Merge user given arguments with default
    $post_type = empty( $args['post_type'] ) ? get_post_type() : $args['post_type'] ;
    $title = '';

    if ( is_404() ) {

      $title  = self::option( METAOPTION_PREFIX .'404-title-text', 1 );

    } elseif ( is_search() && !$args['listing']) {

      $display  = $args['force_display'] ? true : self::option( THEMEOPTION_PREFIX .'search-title', 1 );
      $title  = $display ? self::option( THEMEOPTION_PREFIX .'search-title-text', '' ) .' "'. get_search_query() .'"' : '' ;

    } elseif ( is_singular() || $args['listing'] ) {

      $display_single  = $args['force_display'] ? true : self::option( METAOPTION_PREFIX . $post_type .'-title', 1 );
      $display = self::is_archive_page() ? 1 : $display_single; // Special workaround for title display control ( different when in blog view )
      $title   = $display ? get_the_title() : '';

    } elseif ( self::is_archive_page() && ! $args['listing'] || !self::is_library_active() ) { // archive title ( contains a fix when PFL is inactive in order to display blog title out of the box )

      $display        = $args['force_display'] ? true : self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-title', 1 );
      $display_tax    = $args['force_display'] ? true : self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-title-tax', 1 );
      $display_author = $args['force_display'] ? true : self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-title-author', 1 );
      $display_date   = $args['force_display'] ? true : self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-title-date', 1 );

      if ( ( is_category() || is_tag() || is_tax() ) && $display_tax ) { // taxonomy term

        $title = $display ? strip_tags( single_term_title( '', false ) ) : '';

      } elseif ( is_author() && $display_author ) { // author display name

        $title = $display ? strip_tags( get_the_author() ) : '';

      } elseif ( is_date() && $display_date ) { // month title

        $title = $display ? strip_tags( single_month_title('', false ) ) : '';

      } else { // default title

        $title = $display ? self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-title-text', esc_html__('The Blog', 'plethora-framework' ) ) : '';
      }
    }

    if ( !empty( $title ) && !empty( $args['tag'] ) ) { 

      $class      = !empty($args['class']) ? ' class="'. esc_attr( implode(' ', $args['class']) ) .'"' : '';
      $id         = !empty($args['id']) ? ' id="'. esc_attr( $args['id'] ).'"' : '';
      $link_open  = $args['listing'] && $args['link'] ? '<a href="'. get_permalink() .'">' : '';
      $link_close = $args['listing'] && $args['link'] ? '</a>' : '';
      $title      = '<'. $args['tag'] . $class . $id .'>'. $link_open . esc_html( $title ) . $link_close .'</'. $args['tag'] .'>';
    }

    return $title;
  }

  /**
   * PUBLIC | Returns any Plethora post/archive subtitle ( views: singular, archive, 404, search )
   * @since 1.0
   */
  public static function get_subtitle( $args = array() ) {

    $default_args = array( 
            'tag'              => 'p',       // HTML tag ( leave empty for raw output )    
            'class'            => array('post_subtitle'),    // HTML tag class(es)    
            'id'               => '',         // HTML tag id    
            'listing'          => false,      // this MUST be set 'true' for listing view requests    
            'force_display'    => false,      // force title display without checking for options    
            'post_type'     => '',         // force specific post type    
            );

    // Merge user given arguments with default
    $args = wp_parse_args( $args, $default_args);
    $post_type = empty( $args['post_type'] ) ? get_post_type() : $args['post_type'] ;
    $subtitle = '';

    if ( is_404() ) {

      $subtitle  = self::option( METAOPTION_PREFIX .'404-subtitle-text', 1 );

    } elseif ( is_search() && !$args['listing'] ) {

      $display  = $args['force_display'] ? true : self::option( THEMEOPTION_PREFIX .'search-subtitle', 1 );
      $subtitle  = $display ? self::option( THEMEOPTION_PREFIX .'search-subtitle-text', '1' ) : '';

    } elseif ( is_singular() || $args['listing'] ) {

      $display_single  = $args['force_display'] ? true : self::option( METAOPTION_PREFIX . $post_type .'-subtitle', 1 );
      $display_archive = $args['force_display'] ? true : self::option( METAOPTION_PREFIX .'archive'. $post_type .'-listing-subtitle', 1, get_the_id() );
      $display         = $args['listing'] ? $display_archive : $display_single;
      $subtitle        = $display ? self::option( METAOPTION_PREFIX . $post_type .'-subtitle-text', '', get_the_id() ) : '';

    } elseif ( self::is_archive_page() && ! $args['listing'] ) { // archive subtitle

      $display        = $args['force_display'] ? true : self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-subtitle', 0 );
      $display_tax    = $args['force_display'] ? true : self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-subtitle-tax', 1 );
      $display_author = $args['force_display'] ? true : self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-subtitle-author', 1 );
      $display_date   = $args['force_display'] ? true : self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-subtitle-date', 0 );

      if ( ( is_category() || is_tag() ) && $display_tax ) { // taxonomy term description

        $subtitle = $display ? strip_tags( term_description() ) : '';

      } elseif ( is_author() && $display_author ) { // author bio

        $subtitle = $display ? strip_tags( get_the_author_meta('description') ) : '';

      } elseif ( is_date() && $display_date ) { // author bio

        $subtitle = $display ? self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-subtitle-text', '' ) : '';

      } else { // default subtitle
        
        if ( is_tax() && $display_tax ) {

          $subtitle = $display ? strip_tags( term_description() ) : '';
        
        } else {

          $subtitle = $display ? self::option( THEMEOPTION_PREFIX .'archive'. $post_type .'-subtitle-text', '' ) : '';
        }
      }
    }

    if ( !empty( $subtitle ) && !empty( $args['tag'] ) ) { 

      $class    = !empty($args['class']) ? ' class="'. esc_attr( implode( ' ', $args['class'] ) ) .'"' : '';
      $id       = !empty($args['id']) ? ' id="'. esc_attr( $args['id'] ) .'"' : '';
      $subtitle = '<'. $args['tag'] . $class . $id .'>'. esc_html( $subtitle ) .'</'. $args['tag'] .'>';
    }

    return $subtitle;
  }

  /**
   * PUBLIC | Returns any content section colorset
   * @since 1.0
   */
  public static function get_content_colorset( $post_type = '' ) {

    $post_type = empty( $post_type ) ? get_post_type() : $post_type ;
    $colorset = '';

    if ( is_singular() ) {

      $colorset = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-colorset'); 

    } elseif ( self::is_archive_page() ) {

      $colorset = Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-colorset');
    }

    return $colorset;
  }

  /**
   * PUBLIC | Returns Plethora listing content according to theme options
   * Notice: Will not work outside the loop 
   * @since 1.0
   */
  public static function get_listing_content( $args = array() ) {

    if ( ! in_the_loop() ) { return; } // not working outside loop

    // those will by applied only on excerpt output
    $default_args = array( 
            'listing'       => false,     // this MUST be set 'true' for listing view requests    
            'tag'           => 'p',       // HTML tag ( leave empty for raw output )    
            'class'         => array(),   // HTML tag class(es)    
            'id'            => '',        // HTML tag id    
            'force_display' => '',        // 'excerpt'/'content' force a view without checking for options    
            );

    // Merge user given arguments with default
    $args = wp_parse_args( $args, $default_args);
    $args['force_display'] = $args['force_display'] === 'excerpt' || $args['force_display'] === 'content' ? $args['force_display'] : '';
    
    $post_type = get_post_type();
    $option = empty( $args['force_display'] ) ? Plethora_Theme::option( METAOPTION_PREFIX .'archive'.$post_type.'-listing-content', 'content', get_the_id()) : $args['force_display']; // Display Excerpt or Content?   

    switch ( $option ) {
      case 'excerpt':
        $output = get_the_excerpt('');
        if ( !empty( $output ) && !empty( $args['tag'] ) ) { 

          $class    = !empty($args['class']) ? ' class="'. implode(' ', $args['class']) .'"' : '';
          $id       = !empty($args['id']) ? ' id="'. $args['id'] .'"' : '';
          $output = '<'. $args['tag'] . $class . $id .'>'. $output .'</'. $args['tag'] .'>';
        }
        return $output;
        break;
      
      default:
        $output = get_the_content();
        return apply_filters('the_content', $output );
        break;
    }
  }

  public static function get_post_content( $args = array() ) {

    if ( ! in_the_loop() ) { return; } // not working outside loop
    $default_args = array(
            'listing'                        => false,
            'tag'                            => 'p', // HTML tag ( leave empty for raw output )    
            'class'                          => array(),  // HTML tag class(es)    
            'id'                             => '',  // HTML tag id    
            'force_display'                  => '',  // 'excerpt'/'content' force a view without checking for options    
            'wp_link_pages'                  => true,
            'wp_link_pages_before'           => '<div class="page-links post_pagination_wrapper"><span class="page-links-title">' . esc_html__( 'Pages:', 'plethora-framework' ) . '</span>',
            'wp_link_pages_after'            => '</div>',
            'wp_link_pages_link_before'      => '<span class="post_pagination_page">',
            'wp_link_pages_link_after'       => '</span>',
            'wp_link_pages_next_or_number'   => 'number',
            'wp_link_pages_separator'        => ' ',
            'wp_link_pages_nextpagelink'     => esc_html__( 'Next page', 'plethora-framework' ),
            'wp_link_pages_previouspagelink' => esc_html__( 'Previous page', 'plethora-framework' ),
            'wp_link_pages_pagelink'         => '%',
            );
    // Merge user given arguments with default
    $args = wp_parse_args( $args, $default_args);
    $args['force_display'] = $args['force_display'] === 'excerpt' || $args['force_display'] === 'content' ? $args['force_display'] : '';
    
    extract($args);
    if ( $listing ) {

      return self::get_listing_content( $args );

    } else {

      $output = apply_filters( 'the_content', get_the_content() );
      if ( $wp_link_pages ) {

        $output .= wp_link_pages(array(
                 'before'           => $wp_link_pages_before,
                 'after'            => $wp_link_pages_after,
                 'link_before'      => $wp_link_pages_link_before,
                 'link_after'       => $wp_link_pages_link_after,
                 'next_or_number'   => $wp_link_pages_next_or_number,
                 'separator'        => $wp_link_pages_separator,
                 'nextpagelink'     => $wp_link_pages_nextpagelink,
                 'previouspagelink' => $wp_link_pages_previouspagelink,
                 'pagelink'         => $wp_link_pages_pagelink,
                 'echo'             => false,
        ));
      }

      return $output;
    }
  }

  // PLEDEV: Working on this
  public static function get_post_media( $args = array() ) {

    $default_args = array( 
        'post_id'       => get_the_id(),  // Post id...default use inside loop
        'type'          => 'image',       // Media type ( 'image', 'video', 'audio' )
        'return'        => 'html',        // Return url OR HTML ( 'url', 'html' )
        'stretch'       => false,         // Apply streching wrapper technique options ( only for HTML returns )
        'link_to_post'  => false,         // Return image wrapped in a tag ( only for HTML image returns )
        'force_display' => false,         // If set to true, it will ignore featured media display options
        'post_type'     => '',         // force specific post type    
    );

    // Merge user given arguments with default and extract
    $args = wp_parse_args( $args, $default_args);
    extract($args);
    $post_type = empty( $args['post_type'] ) ? get_post_type() : $args['post_type'] ;
    $type = empty( $type ) ? 'image' : $type;
    // Apply featured media display options
    if ( ! $force_display ) {

      $mediadisplay = Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-mediadisplay', 'inherit', $post_id );
      if ( $mediadisplay === 'hide' ) { return null; }              // if set to hide, no need to continue
      $type = $mediadisplay === 'featuredimage' ? 'image' : $type;  // display featured image or according to post format
    }
    // Start working with the output
    $output = '';
    switch ($type) {
      case 'video':

        $video_url  = Plethora_Theme::option( METAOPTION_PREFIX .'content-video', '', $post_id );
        if ( !empty( $video_url )) {   

          $the_url = $video_url;
          $output .= $return === 'html' ? wp_oembed_get( $video_url ) : $video_url; 

        } else {

          return null;
        }   
        break;

      case 'audio':

        $audio_url  = Plethora_Theme::option( METAOPTION_PREFIX .'content-audio', '', get_the_id() );
        if ( !empty( $audio_url )) {   

          $the_url = $audio_url;
          $output .= $return === 'html' ? wp_oembed_get( $audio_url ) : $audio_url; 

        } else {

          return null;
        }   

        break;
      case 'image':

        if ( has_post_thumbnail( $post_id ) ) { 

          $featured_image_src = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
          $the_url = $featured_image_src[0];
          if ( !empty( $featured_image_src )) {

            $output .= $return === 'html' ? '<img alt="'. esc_attr( get_the_title() ) .'" src="'. esc_url( $the_url ) .'">' : $the_url ;
          }
        } else {

          return null;
        }   
        break;
      
      default:
        return null;
    }
    // Apply stretching wrapper classes
    $classes   = array();
    $styles   = array();

    if ( $stretch ) {

      $strech_class = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-media-stretch', '', get_the_id()); 
      if ( !empty( $strech_class ) && $strech_class !== 'foo_stretch' ) {

          $classes[] = $strech_class;
          $styles[]  = $type === 'image' ? 'background-image: url(\''. esc_url( $the_url ).'\')' : '';
      }
   }
    // Prepare the HTML
    if ( $return === 'html' ) {

        $classes[] = $type !== 'image' ? 'video_iframe' : '';
        $output    = '<figure class="'. esc_attr( implode(' ', array_filter( $classes )) ).'" style="'. esc_attr( implode('; ', array_filter( $styles )) ).'">' . $output .'</figure>';
    }
    // Apply a tag
    if ( $link_to_post && $return === 'html' && $type === 'image' ) {

        $output = '<a href="'. get_permalink( get_the_ID() ) .'" title="'. esc_attr( get_the_title()) .'">'. $output .'</a>';
    }
    // Ready!
    return $output;

  }

  /**
   * PUBLIC | Returns 'Read More' link for archive listings
   * @since 1.3.1
   */
  public static function get_post_infolabel( $args = array() ) {

    $default_args = array( 
            'post_type'    => get_post_type(),   // Post type
            'post_id'      => get_the_id(),      // Post id...default use inside loop
            'type'         => '',                // 'categories', 'tags', 'author', 'date', 'comments' 
            'listing'      => false,             // this MUST be set 'true' for listing view requests    
            'link'         => true,              // adds the proper link, depending on the info type   
            'sep'          => ', ',              // separator for taxonomy displays   
            'tag'          => 'span',            // HTML tag ( leave empty for raw output )    
            'class'        => array(),           // HTML tag class(es)    
            'id'           => '',                // HTML tag id
            'prepend_html' => '',
            'append_html'  => ''
            );
    $args = wp_parse_args( $args, $default_args);
    extract($args);
    if ( empty( $type ) || ! in_array( $type, array( 'categories', 'tags', 'author', 'date', 'comments' ) ) ) { return ''; }

    // common configuration
    $output    = '';
    $class     = is_array( $class ) ? $class : array( $class );
    $tag_attrs = !empty( $class ) ? ' class="'. implode(' ', $class ) .'"' : '';
    $tag_attrs .= !empty( $id ) ? ' id="'. $id .'"' : '';

    // native post categories
    if ( $type === 'categories' && $post_type === 'post' ) {

      $show_categories = $listing ? Plethora_Theme::option( METAOPTION_PREFIX .'archive'.$post_type.'-info-category', 1, $post_id ) : Plethora_Theme::option( METAOPTION_PREFIX . $post_type.'-info-category', 1, $post_id );
      if ( $show_categories ) {

        $count = 0;
        $categories = get_the_category();
        foreach( $categories as $key => $category ) {

          $count   = $count + 1;
          $output .= $count > 1 ? $sep : '';
          $output .= !empty( $tag ) ? '<'.$tag.$tag_attrs.'>' : '';
          $output .= $link ? '<a href="'.get_category_link( $category->term_id ).'" title="' . esc_attr( sprintf( esc_html__( "View all posts in category: %s", 'plethora-framework' ), $category->name ) ) . '">' : '';
          $output .= $category->cat_name;
          $output .= $link ? '</a>' : '';
          $output .= !empty( $tag ) ? '</'.$tag.'>' : '';
        }
      }

    // primary taxonomy for CPTs
    } elseif ( $type === 'categories' && $post_type !== 'post' ) {

      $show_primary_tax = $listing ? Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-info-primarytax', 1, $post_id ) : Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-info-primarytax', 1, $post_id );
      $primary_tax      = Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-info-primarytax-slug', 'category', $post_id );
      if ( $show_primary_tax && !empty( $primary_tax ) ) { 

        $terms = get_the_terms( $post_id, $primary_tax );

        if ( ! is_wp_error( $terms ) && !empty( $terms ) ) {

          $count = 0;
          foreach( $terms as $key => $term ) {

            $count   = $count + 1;
            $output .= $count > 1 ? $sep : '';
            $output .= !empty( $tag ) ? '<'.$tag.$tag_attrs.'>' : '';
            $output .= $link ? '<a href="'.get_term_link( $term ).'" title="' . esc_attr( sprintf( esc_html__( "View all in category: %s", 'plethora-framework' ), $term->name ) ) . '">' : '';
            $output .= $term->name;
            $output .= $link ? '</a>' : '';
            $output .= !empty( $tag ) ? '</'.$tag.'>' : '';
          }
        }
      }

    // native post tags
    } elseif ( $type === 'tags' && $post_type === 'post' ) {

      $show_tags = $listing ? Plethora_Theme::option( METAOPTION_PREFIX .'archivepost-info-tags', 1, $post_id ) : Plethora_Theme::option( METAOPTION_PREFIX .'post-tags', 1);
      if ( $show_tags ) { 

        $tags = get_the_tags();
        if ( $tags ) {

          $count = 0;
          foreach( $tags as $key => $the_tag ) {

            $count   = $count + 1;
            $output .= $count > 1 ? $sep : '';
            $output .= !empty( $tag ) ? '<'.$tag.$tag_attrs.'>' : '';
            $output .= $link ? '<a href="'.get_tag_link( $the_tag->term_id ).'" title="' . esc_attr( sprintf( esc_html__( "View all posts tagged with: %s", 'plethora-framework' ), $the_tag->name ) ) . '">' : '';
            $output .= $the_tag->name;
            $output .= $link ? '</a>' : '';
            $output .= !empty( $tag ) ? '</'.$tag.'>' : '';
          }
        }
      }

    // secondary taxonomy for CPTs
    } elseif ( $type === 'tags' && $post_type !== 'post' ) {

      $show_secondary_tax = $listing ? Plethora_Theme::option( METAOPTION_PREFIX .'archive'.$post_type.'-info-secondarytax', 1, $post_id ) : Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-info-secondarytax', 1, $post_id );
      $secondary_tax      = Plethora_Theme::option( METAOPTION_PREFIX .$post_type.'-info-secondarytax-slug', 'post_tag', $post_id );
      if ( $show_secondary_tax && !empty( $secondary_tax ) ) { 

        $terms = get_the_terms( $post_id, $secondary_tax );

        if ( ! is_wp_error( $terms ) && !empty( $terms ) ) {

          $count = 0;
          foreach($terms as $key=>$term) {

            $count   = $count + 1;
            $output .= $count > 1 ? $sep : '';
            $output .= !empty( $tag ) ? '<'.$tag.$tag_attrs.'>' : '';
            $output .= $link ? '<a href="'.get_term_link( $term ).'" title="' . esc_attr( sprintf( esc_html__( "View all in category: %s", 'plethora-framework' ), $term->name ) ) . '">' : '';
            $output .= $term->name;
            $output .= $link ? '</a>' : '';
            $output .= !empty( $tag ) ? '</'.$tag.'>' : '';
          }
        }
      }

    // author for native posts and CPTs
    } elseif ( $type === 'author' ) {

      $show_author  = $listing ? Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-info-author', 1, $post_id ) : Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-author', 1, $post_id );
      if ( $show_author ) {

        $output .= !empty( $tag ) ? '<'.$tag.$tag_attrs.'>' : '';
        $output .= $link ? '<a href="'. get_author_posts_url( get_the_author_meta( 'ID' ) ).'" title="' . esc_attr( sprintf( get_the_author() )) . '">' : '';
        $output .= get_the_author();
        $output .= $link ? '</a>' : '';
        $output .= !empty( $tag ) ? '</'.$tag.'>' : '';
      }

    // date for native posts and CPTs
    } elseif ( $type === 'date' ) {

      $show_date = $listing ? Plethora_Theme::option( METAOPTION_PREFIX .'archive'. $post_type .'-info-date', 1, $post_id ) : Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-date', 1, $post_id);
      if ( $show_date ) {

        $output .= !empty( $tag ) ? '<'.$tag.$tag_attrs.'>' : '';
        $output .= get_the_date();
        $output .= !empty( $tag ) ? '</'.$tag.'>' : '';
      }

    // comments count for native posts and CPTs
    } elseif ( $type === 'comments' ) {

      $show_comments = $listing ? Plethora_Theme::option( METAOPTION_PREFIX .'archive'.$post_type.'-info-comments', 1, $post_id ) : Plethora_Theme::option( METAOPTION_PREFIX . $post_type .'-comments', 1, $post_id );
      if ( $show_comments && comments_open()  ) { 

        $num_comments = get_comments_number( $post_id );
        if ( $num_comments > 0 ) {

          $output .= !empty( $tag ) ? '<'.$tag.$tag_attrs.'>' : '';
          $output .= $link ? '<a href="'. esc_url( get_permalink() .'#post_comments').'">' : '' ;
          $output .= $num_comments ;
          $output .= $link ? '</a>' : '';
          $output .= !empty( $tag ) ? '</'.$tag.'>' : '';
        } 
      }
    }

    // Prepend / append HTML
    $output = ! empty( $output ) ? $prepend_html . $output . $append_html : '';
    return $output;
  }

  /**
   * PUBLIC | Returns 'Read More' link for archive listings
   * @since 1.3.1
   */
  public static function get_post_linkbutton( $args = array() ) {

    $default_args = array( 
            'post_type'    => get_post_type(),        // Post type
            'post_id'      => get_the_id(),           // Post id...default use inside loop
            'class'        => array( 'btn-primary' ),  // HTML tag class(es)    
            'id'           => '',                     // HTML tag id
            'prepend_html' => '',
            'append_html'  => ''
            );
    $args = wp_parse_args( $args, $default_args);
    extract($args);

    $output    = '';
    $blog_linkbutton = Plethora_Theme::option( METAOPTION_PREFIX .'archive'.$post_type.'-show-linkbutton', 1, $post_id ); // Show Post Link Button
    $blog_linktext   = Plethora_Theme::option( METAOPTION_PREFIX .'archive'.$post_type.'-show-linkbutton-text', esc_html__('Read More', 'plethora-framework'), $post_id ); // Link Button Text
    if ( $blog_linkbutton ) {

      $class     = is_array( $class ) ? $class : array( $class );
      $tag_attrs = !empty( $class ) ? ' class="'. implode(' ', $class ) .'"' : '';
      $tag_attrs .= !empty( $id ) ? ' id="'. $id .'"' : '';

      $output .= '<a href="'. get_permalink( $post_id ) .'"'.$tag_attrs.'>';
      $output .= wp_strip_all_tags( $blog_linktext );
      $output .= '</a>';
    }

    // Prepend / append HTML
    $output = ! empty( $output ) ? $prepend_html . $output . $append_html : '';
    return $output;
  }

  public static function get_pagination( $args = array() ) {

    $default_args = array( 
            'post_type'      => get_post_type(),    // Post type
            'post_id'        => get_the_id(),       // Post id...default use inside loop
            'range'          => 5,       // Post id...default use inside loop
            'class'          => 'pagination pagination-centered',   // Class for previous page    
            'class_previous' => 'pagination-btn',   // Class for previous page    
            'class_next'     => 'pagination-btn',   // Class for next page 
            'class_number'   => 'number',           // Class for page  
            'prepend_html'   => '',
            'append_html'    => ''
            );
    $args = wp_parse_args( $args, $default_args);
    extract($args);

    $pages = '';
    $showitems = ($range * 2)+1;  
    global $paged;
    $paged = empty( $paged ) ? 1 : $paged;

    if ( empty( $pages ) ) {

         global $wp_query;
         $pages = $wp_query->max_num_pages;
         $pages = !$pages ? 1 : $pages;
    }

    $output = '';
    if ( $pages != 1 ) {

        $output .= '  <ul class="'.$class.'">';
        $output .= '    <li class="'.$class_previous.'">'. get_previous_posts_link( esc_html__('Prev', 'plethora-framework') ).'</li>';
        
        if ( $paged > 2 && $paged > $range+1 && $showitems < $pages ) { 

          $output .= '    <li class="'.$class_previous.'"><a href="'.get_pagenum_link(1).'">&laquo;</a></li>'; 
        }

        if ( $paged > 1 && $showitems < $pages ) { 

          $output .= '    <li class="'.$class_previous.'"><a href="'.get_pagenum_link($paged - 1).'">&lsaquo;</a></li>'; 
        }

        for ( $i=1; $i <= $pages; $i++ ) { 

          if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )) {

            $active_class = $paged == $i ? ' active' : '';
            $output .= '    <li class="'.$class_number.$active_class.'"><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
          }
        }

        if ($paged < $pages && $showitems < $pages) {

          $output .= '    <li class="'.$class_next.'"><a href="' .get_pagenum_link($paged + 1). '">&rsaquo;</a></li>';  
        }

        if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) {

          $output .= '    <li class="'.$class_next.'"><a href="' .get_pagenum_link($pages).'">&raquo;</a></li>';
        }
        
        $output .= '    <li class="'.$class_next.'">'. get_next_posts_link( esc_html__('Next', 'plethora-framework') ).'</li>';
        $output .= '  </ul>';
    }

    // Prepend / append HTML
    $output = ! empty( $output ) ? $prepend_html . $output . $prepend_html : '';
    return $output;
  }

  /**
   * PUBLIC | Returns true/false if current page has/has not VC sections in content
   * Does nothing more than returning the post meta 'content_has_sections', a value
   * saved by Plethora_Shortcode_Vcrow class produced on each post's edit screen.
   * @since 1.0
   */
  public static function content_has_sections() {

    if ( self::is_library_active() ) { // should be checked ONLY when PFL plugin is active

      $content_has_sections = self::option( METAOPTION_PREFIX . 'content_has_sections', 0);
    
    } else { // if PFL is inactive, then always return false

      $content_has_sections = false;
    }
    return $content_has_sections; 
  }

  /**
   * PUBLIC | Returns true/false if current page has a title OR subtitle text
   * @since 1.0
   */
  public static function content_has_titles() {

    $content_has_titles = self::get_title() != "" || self::get_subtitle() != "" ? 1 : 0;

    return $content_has_titles; 
  }

// THEME RELATED STATIC METHODS <---- FINISH

}