<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2015

File Description: Testimonial Post Type Feature Class
Hooks > Filters

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Posttype') && !class_exists('Plethora_Posttype_Testimonial') ) {  
 
    /**
     * @package Plethora Framework
     */

    class Plethora_Posttype_Testimonial {

        /*** SETUP: Configure your Custom Post Type here ***/

        private $post_type_slug              = 'testimonial';
        public static $feature_title         = "Testimonial";                                         // FEATURE DISPLAY TITLE  (STRING)
        public static $feature_description   = "Contains all testimonial related post configuration"; // FEATURE DISPLAY DESCRIPTION (STRING)
        public static $feature_icon          = "dashicons-format-chat";                               // SIDEBAR ICON [https://developer.wordpress.org/resource/dashicons/]
        public static $theme_option_control  = true;                                                  // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL ( BOOLEAN )
        public static $theme_option_default  = true;                                                  // DEFAULT ACTIVATION OPTION STATUS ( BOOLEAN )
        public static $theme_option_requires = array();                                               // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK? array( $controller_slug => $feature_slug )
        public static $dynamic_construct     = true;                                                  // DYNAMIC CLASS CONSTRUCTION ? ( BOOLEAN )
        public static $dynamic_method        = false;                                                 // ADDITIONAL METHOD INVOCATION ( STRING/BOOLEAN | METHOD NAME OR FALSE )
        // TAXONOMY (Leave empty to disable)
        public static $taxonomy_singular     = "Testimonial Category";
        public static $taxonomy_plural       = "Testimonial Categories";

        /*** SETUP ***/

        public function __construct() {

            $posttype = $this->create();                                                                        // CREATE BASIC POST TYPE OBJECT

            // ADD TAXONOMIES TO OBJECT
            if ( self::$taxonomy_singular !== "" && self::$taxonomy_plural ){
                $posttype = $this->add_taxonomies( $posttype );                                                     
            }

            $posttype->sortable( array( $this->post_type_slug => array( $this->post_type_slug, true )));        // MAKE CLIENT AND TYPE COLUMNS SORTABLE

          // Single testimonial Metabox    
          add_filter( 'plethora_metabox_add', array($this, 'single_metabox'));   

        }

        public function create() {

            // GET USER DEFINED URL REWRITE OPTION
            $rewrite = Plethora_Theme::option( THEMEOPTION_PREFIX . $this->post_type_slug . '-urlrewrite', $this->post_type_slug );
            $names   = array(

                'post_type_name' =>  $this->post_type_slug, 
                'slug'           =>  $this->post_type_slug, 
                'menu_item_name' =>  sprintf(esc_html__( '%s', 'plethora-framework' ), self::$feature_title ), 
                'singular'       =>  sprintf(esc_html__( '%s Item', 'plethora-framework' ), self::$feature_title ), 
                'plural'         =>  sprintf(esc_html__( '%s Items', 'plethora-framework' ), self::$feature_title ), 

            );

            $options = array(

                'enter_title_here'      => self::$feature_title . ' title', // TITLE PROMPT TEXT 
                'description'           => '',                              // SHORT POST TYPE DESCRIPTION 
                'public'                => false,                            // AVAILABLE FOR PUBLICLY FOR FRONT-END OR ADMIN INTERFACE ONLY (default: false)
                'exclude_from_search'   => true,                            // EXCLUDE CPT POSTS FROM FRONT END SEARCH RESULTS ( default: value of the opposite of the public argument)
                'publicly_queryable'    => false,                            // Whether queries can be performed on the front end as part of parse_request() ( default: value of public argument)
                'show_ui'               => true,                            // Whether to generate a default UI for managing this post type in the admin ( default: value of public argument )
                'show_in_nav_menus'     => false,                            // Whether post_type is available for selection in navigation menus ( default: value of public argument )
                'show_in_menu'          => true,                            // Where to show the post type in the admin menu. show_ui must be true ( default: value of show_ui argument )
                'show_in_admin_bar'     => true,                            // Whether to make this post type available in the WordPress admin bar ( default: value of the show_in_menu argument )
                'menu_position'         => 5,                               // The position in the menu order the post type should appear. show_in_menu must be true ( default: null )
                'menu_icon'             => self::$feature_icon, // The url to the icon to be used for this menu or the name of the icon from the iconfont ( default: null - defaults to the posts icon ) Check http://melchoyce.github.io/dashimages/icons/ for icon info
                'hierarchical'          => false,                           // Whether the post type is hierarchical (e.g. page). Allows Parent to be specified. The 'supports' parameter should contain 'page-attributes' to show the parent select box on the editor page. ( default: false )
                'has_archive'           => false,                            // Enables post type archives. Will use $post_type as archive slug by default (default: false)
                'query_var'             => true,                            // Sets the query_var key for this post type.  (Default: true - set to $post_type )
                'can_export'            => true,                            // Can this post_type be exported. ( Default: true )
             // 'taxonomies'            => array(),                         // An array of registered taxonomies like category or post_tag that will be used with this post type. This can be used in lieu of calling register_taxonomy_for_object_type() directly. Custom taxonomies still need to be registered with register_taxonomy(). 
                'supports'              => array( 
                                                'title', 
                                                'editor', 
                                                // 'thumbnail',    
                                                // 'excerpt',  
                                                // 'comments',     
                                                // 'author',    
                                                // 'trackbacks',    
                                                // 'custom-fields',     
                                                // 'revisions',     
                                                // 'page-attributes',   
                                                // 'post-formats'   
                                             ), // An alias for calling add_post_type_support() directly. Boolean false can be passed as value instead of an array to prevent default (title and editor) behavior. 
            );

            // HOOKS TO APPLY
            $names            = apply_filters( 'plethora_posttype_'. $this->post_type_slug .'_names', $names );
            $options          = apply_filters( 'plethora_posttype_'. $this->post_type_slug .'_options', $options );
            $custom_post_type = new Plethora_Posttype( $names, $options );     // CREATE THE POST TYPE

            return $custom_post_type;
        }

        function add_taxonomies( $custom_post_type ) {

            // TAXONOMY LABELS
            $labels = array(

                'name'                       => sprintf( esc_html__( '%s', 'plethora-framework' ), self::$taxonomy_plural ),
                'singular_name'              => sprintf( esc_html__( '%s', 'plethora-framework' ), 'Testimonial Category' ),
                'menu_name'                  => sprintf( esc_html__( '%s', 'plethora-framework' ), self::$taxonomy_plural ),
                'all_items'                  => sprintf( esc_html__( 'All %s', 'plethora-framework' ), self::$taxonomy_plural ),
                'edit_item'                  => sprintf( esc_html__( 'Edit %s', 'plethora-framework' ), 'Testimonial Category' ),
                'view_item'                  => sprintf( esc_html__( 'View %s', 'plethora-framework' ), 'Testimonial Category' ),
                'update_item'                => sprintf( esc_html__( 'Update %s', 'plethora-framework' ), 'Testimonial Category' ),
                'add_new_item'               => sprintf( esc_html__( 'Add New %s', 'plethora-framework' ), 'Testimonial Category' ),
                'new_item_name'              => sprintf( esc_html__( 'New %s Name', 'plethora-framework' ), 'Testimonial Category' ),
                'parent_item'                => sprintf( esc_html__( 'Parent %s', 'plethora-framework' ), 'Testimonial Category' ),
                'parent_item_colon'          => sprintf( esc_html__( 'Parent %s:', 'plethora-framework' ), 'Testimonial Category' ),
                'search_items'               => sprintf( esc_html__( 'Search %s', 'plethora-framework' ), self::$taxonomy_plural ),     
                'popular_items'              => sprintf( esc_html__( 'Popular %s', 'plethora-framework' ), self::$taxonomy_plural ),
                'separate_items_with_commas' => sprintf( esc_html__( 'Seperate %s with commas', 'plethora-framework' ), self::$taxonomy_plural ),
                'add_or_remove_items'        => sprintf( esc_html__( 'Add or remove %s', 'plethora-framework' ), self::$taxonomy_plural ),
                'choose_from_most_used'      => sprintf( esc_html__( 'Choose from most used %s', 'plethora-framework' ), self::$taxonomy_plural ),
                'not_found'                  => sprintf( esc_html__( 'No %s found', 'plethora-framework' ), self::$taxonomy_plural )

            );

            // TAXONOMY OPTIONS
            $options = array(
     
                'labels'            => $labels,
                'public'            => true,    // (boolean) (optional) If the taxonomy should be publicly queryable. ( default: true )
                'show_ui'           => true,    // (boolean) (optional) Whether to generate a default UI for managing this taxonomy. (Default: if not set, defaults to value of public argument.)
                'show_in_nav_menus' => false,    // (boolean) (optional) true makes this taxonomy available for selection in navigation menus. ( Default: if not set, defaults to value of public argument )
                'show_tagcloud'     => false,   // (boolean) (optional) Whether to allow the Tag Cloud widget to use this taxonomy. (Default: if not set, defaults to value of show_ui argument )
                'show_admin_column' => true,    // (boolean) (optional) Whether to allow automatic creation of taxonomy columns on associated post-types table ( Default: false )
                'hierarchical'      => false,    // (boolean) (optional) Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags. ( Default: false )
                'query_var'         => true,    // (boolean or string) (optional) False to disable the query_var, set as string to use custom query_var instead of default which is $taxonomy, the taxonomy's "name". ( Default: $taxonomy )
             // 'sort'              => true,    // (boolean) (optional) Whether this taxonomy should remember the order in which terms are added to objects. ( default: None )
                'rewrite'           => array( 
                                        'slug'          => 'testimonial-category', // Used as pretty permalink text (i.e. /tag/) - defaults to $taxonomy (taxonomy's name slug) 
                                        'with_front'    => true,    // allowing permalinks to be prepended with front base - defaults to true 
                                        'hierarchical'  => true,    // true or false allow hierarchical urls (implemented in Version 3.1) - defaults to false 
                                       ),       // (boolean/array) (optional) Set to false to prevent automatic URL rewriting a.k.a. "pretty permalinks". Pass an $args array to override default URL settings for permalinks as outlined above (Default: true )
                // 'capabilities'       => array( 
                                    //  'manage_terms'  => 'manage_projecttypes',
                                    //  'edit_terms'    => 'manage_projecttypes',
                                    //  'delete_terms'  => 'manage_projecttypes',
                                    //  'assign_terms'  => 'edit_portfolios',
                                    //    ),        // (array) (optional) An array of the capabilities for this taxonomy. ( Default: None )
            );

            // REGISTER TAXONOMY
            $options    = apply_filters( 'plethora_posttype_taxonomy_testimonial-category_options', $options );
            $custom_post_type->register_taxonomy( 'testimonial-category', $options );

            return $custom_post_type;
        }


        public static function single_metabox( $metaboxes ){
          $sections = array();
          $sections[] = array(
            'icon_class' => 'icon-large',
            'icon'       => 'el-icon-wrench-alt',
            'fields'     => array(
                array(
                  'id'      => METAOPTION_PREFIX .'testimonial-person-name',
                  'type'    => 'text', 
                  'title'   => esc_html__('Person Name', 'plethora-framework'),
                  'desc'   => esc_html__('The name of the person who gave this testimonial', 'plethora-framework'),
                  'default' => '',
                  'translate' => true,
                  ),  
                array(
                  'id'       => METAOPTION_PREFIX .'testimonial-person-role',
                  'type'    => 'text', 
                  'title'    => esc_html__('Person Role', 'plethora-framework'),
                  'desc'   => esc_html__('The role of the person who gave this testimonial', 'plethora-framework'),
                  'default' => '',
                  'translate' => true,
                  ),
                )
            );

            $metaboxes[] = array(
                'id'            => 'metabox-testimonial',
                'title'         => esc_html__( 'Testimonial Options', 'plethora-framework' ),
                'post_types'    => array( 'testimonial'),
                'position'      => 'normal', // normal, advanced, side
                'priority'      => 'high', // high, core, default, low
                'sidebar'       => false, // enable/disable the sidebar in the normal/advanced positions
                'sections'      => $sections,
            );

            if ( has_filter( 'plethora_posttype_testimonial_metabox') ) {

              $sections = apply_filters( 'plethora_posttype_testimonial_metabox', $sections );
            }
            
            return $metaboxes;
        }
        /* static function single_themeoptions( $sections ) { SEE posttype-portfolio.php FOR REFERENCE } */
        /* static function single_metabox( $metaboxes ) { SEE posttype-portfolio.php FOR REFERENCE } */

    }
}   
