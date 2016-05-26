<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2015

File Description: Controller class for post types

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Posttype') ) {
 
    /**
     * @package Plethora Controllers
     */
    class Plethora_Posttype {

        public static $controller_title             = 'Post Types Manager';         // CONTROLLER TITLE
        public static $controller_description       = 'Activate/deactivate any Plethora custom post type available. Notice that on deactivation, all dependent features will be deactivated automatically.';
        public static $controller_dynamic_construct = false;                        // DYNAMIC CLASS CONSTRUCTION ( TRUE / FALSE )
        public static $controller_dynamic_method    = false;                        // INVOKE ANY METHOD AFTER DYNAMIC CONSTRUCTION? ( METHOD NAME OR FALSE )
        public static $dynamic_features_loading     = true;                         // LOAD FEATURES DYNAMICALLY ( ALWAYS TRUE, FALSE IF STATED SO IN CONTROLLER VARIABLES )

        public $singular;
        public $plural;
        public $slug;
        public $options;
        public $taxonomies;
        public $filters; 
        public $columns;
        public $custom_populate_columns;
        public $sortable;
        public $menu_item_name;

        public $post_type_name;
        public $box_id;
        public $box_title;
        public $box_context;
        public $box_priority;
        public $box_fields;
        

        function __construct( $post_type_names, $options = array() ) {

            if( is_array( $post_type_names ) ) {
                
                $names = array(

                    'menu_item_name',
                    'singular',
                    'plural',
                    'slug'

                );

                $this->post_type_name = $post_type_names['post_type_name'];

                foreach( $names as $name ) {

                    if( isset( $post_type_names[$name] ) ) {

                        $this->$name = $post_type_names[$name];
                    
                    } else {

                        $method = 'get_'.$name;

                        $this->$name = $this->$method();
                    
                    }
                
                }

            } else {

                $this->post_type_name = $post_type_names;
                $this->slug           = $this->get_slug();
                $this->plural         = $this->get_plural();
                $this->singular       = $this->get_singular();

            }


            if ( ! post_type_exists( $this->post_type_name ) ) {

                $this->options = $options;      
                add_action( 'init', array( &$this, 'register_post_type' ), 0);
                add_filter( 'manage_edit-' . $this->post_type_name . '_columns', array( &$this, 'add_admin_columns' ));
                add_action( 'manage_' . $this->post_type_name . '_posts_custom_column', array( &$this, 'populate_admin_columns' ), 10, 2);

            }

            // $this->save();

        }


        /**
         * Returns class variable value
         * 
         * @param $var (variable name)
         * @return string|bool
         * 
         */
        function get( $var ) {

            if( $this->$var ) {

                return $this->$var;

            } else {

                return false;

            }
        }

        /**
         * Sets class variable value
         * 
         * @param $var (variable name) | $value (value to set)
         * @return string
         * 
         */
        function set( $var, $value ) {

            $reserved = array(
                'config',
                'post_type_name',
                'singular',
                'plural',
                'slug',
                'options',
                'taxonomies'
            );

            if( ! in_array( $var, $reserved ) ) {

                $this->$var = $value;
            
            }

        }


        /**
         * Merges default set options with custom options values. Returns the new options array
         * 
         * @param $defaults (default array) | $options (custom array)
         * @return array
         * 
         */
        function options_merge( $defaults, $options ) {
            
            $new_array = $defaults;

            foreach( $options as $key => $value ) {

                if( isset( $defaults[$key] ) ) {

                    if( is_array($defaults[$key]) ) {

                        $new_array[$key] = $this->options_merge($defaults[$key], $options[$key]);

                    } else {

                        $new_array[$key] = $options[$key];

                    }

                } else {

                    $new_array[$key] = $value;

                }

            }

            return $new_array;

        }


        /**
         * Returns any given word(s) as a proper WP slug. If empty, it returns the post type slug
         * 
         * @param $name (value to be returned as slug)
         * @return string
         * 
         */
        function get_slug($name = null) {

            if(!isset($name)) {

                $name = $this->post_type_name;

            }
                   $name = strtolower($name);
                   $name = str_replace(" ", "-", $name);
                   $name = str_replace("_", "-", $name);
            return $name;

        }


        /**
         * Returns menu button title. If empty, it returns the post type slug in plural ( just adding a final S )
         * 
         * @param $name (value to be returned as slug)
         * @return string
         * 
         */
        function get_menu_item_name( $name = null ) {

            if( ! isset( $name ) ) {  $name = $this->post_type_name;  }
            return $this->get_human_friendly($name) . 's';
            
        }


        /**
         * Returns plural title. If empty, it returns the post type slug in plural ( just adding a final S )
         * 
         * @param $name (value to be returned as slug)
         * @return string
         * 
         */
        function get_plural($name = null) {

            if( ! isset( $name ) ) {  $name = $this->post_type_name;  }
            return $this->get_human_friendly($name) . 's';
        }


        /**
         * Returns singular title. If empty, it returns the post type slug after making it human readable (see get_human_friendly() method )
         * 
         * @param $name (value to be returned as slug)
         * @return string
         * 
         */
        function get_singular($name = null) {

            if( ! isset( $name ) ) {  $name = $this->post_type_name;  }
            return $this->get_human_friendly($name);

        }


        /**
         * Returns slug like text in human friend form. If empty, it returns the post type slug after making it human readable ( removes dashes & hyphens)
         * 
         * @param $name (value to be returned as slug)
         * @return string
         * 
         */
        function get_human_friendly($name = null) {

            if( ! isset( $name ) ) {  $name = $this->post_type_name;  }
            return ucwords(strtolower(str_replace("-", " ", str_replace("_", " ", $name))));

        }


        /**
         * Prepares the post type registration for WP
         * 
         */
        function register_post_type() {
            
            $plural         = $this->plural;
            $menu_item_name = $this->menu_item_name;
            $singular       = $this->singular;
            $slug           = $this->slug;
            $labels         = array(

                'name'               => $plural,
                'singular_name'      => $singular,
                'menu_name'          => $menu_item_name,
                'all_items'          => $plural,
                'add_new'            => esc_html__( 'Add New', 'plethora-framework' ),
                'add_new_item'       => esc_html__( 'Add New', 'plethora-framework' ) .' '. $singular,
                'edit_item'          => esc_html__( 'Edit ', 'plethora-framework' ) .' '. $singular,
                'new_item'           => esc_html__( 'New', 'plethora-framework' ) .' '. $singular,
                'view_item'          => esc_html__( 'View', 'plethora-framework' ) .' '. $singular,
                'search_items'       => esc_html__( 'Search', 'plethora-framework' ) .' '. $plural,
                'not_found'          => esc_html__( 'No', 'plethora-framework' ) .' '. $plural .' ' . esc_html__( 'found', 'plethora-framework'),
                'not_found_in_trash' => esc_html__( 'No', 'plethora-framework' ) .' '. $plural .' ' . esc_html__( 'found in Trash', 'plethora-framework'),
                'parent_item_colon'  => esc_html__( 'Parent', 'plethora-framework' ) .' '. $singular .':'

            );

            $defaults = array(

                'labels'              => $labels,
                'public'              => true,
                'hierarchical'        => false,
                'menu_position'       => 5,
                'supports'            => array( 'title' ),
                'rewrite'             => array( 'slug' => $slug ),
                'has_archive'         => false,
                'capability_type'     => 'post',
                'menu_icon'           => null, // THEME_ASSETS_ADMIN_DIR . '/images/posttype-portfolio.png',
                'can_export'          => true,
                'query_var'           => true,
                'rewrite'             => true, // array( 'slug' => PLETHORA_REWRITE_PRICEPLANS, 'with_front' => true ),
                'enter_title_here'    => esc_html__( 'ENTER TITLE', 'plethora-framework' )

                // 'exclude_from_search' => false, // Default: value of the opposite of the public argument
                // 'show_ui' => true, // Default: value of public argument
                // 'publicly_queryable' => true, //Default: value of public argument

                );

            $options = $this->options_merge($defaults, $this->options);

            $this->options = $options;

            if(!post_type_exists($this->post_type_name)) {

                register_post_type($this->post_type_name, $options);

                add_filter( 'enter_title_here', array( &$this, 'enter_title_here' ), 1, 2 );

            }

        }

        /**
         *  Set placeholder text for title input field
         */
        public function enter_title_here( $text, $post ) {

            if ( $post->post_type == $this->slug ) {

                return sprintf( esc_html__( '%s', 'plethora-framework' ), $this->options["enter_title_here"] );
            }

            return $text;

        }

        /**
         * Prepares the taxonomy registration for WP
         * 
         */
        function register_taxonomy( $taxonomy_names, $options = array() ) {

            $post_type = $this->post_type_name;
            
            $names = array(
                'singular',
                'plural',
                'slug'
            );

            if(is_array($taxonomy_names)) {

                $taxonomy_name = $taxonomy_names['taxonomy_name'];

                foreach($names as $name) {

                    if(isset($taxonomy_names[$name])) {

                        $$name = $taxonomy_names[$name];
                    
                    } else {

                        $method = 'get_'.$name;

                        $$name = $this->$method($taxonomy_name);

                    }

                }

            } else  {
            
                $taxonomy_name = $taxonomy_names;
                $singular = $this->get_singular($taxonomy_name);
                $plural   = $this->get_plural($taxonomy_name);
                $slug     = $this->get_slug($taxonomy_name);
            
            }

            $labels = array(

                'name'                       => $plural,
                'singular_name'              => $singular,
                'menu_name'                  => $plural,
                'all_items'                  => esc_html__( 'All', 'plethora-framework') .' '. $plural ,
                'edit_item'                  => esc_html__( 'Edit', 'plethora-framework') .' ' . $singular, 
                'view_item'                  => esc_html__( 'View', 'plethora-framework') .' ' . $singular, 
                'update_item'                => esc_html__( 'Update', 'plethora-framework') .' ' . $singular, 
                'add_new_item'               => esc_html__( 'Add New', 'plethora-framework') .' ' . $singular, 
                'new_item_name'              => esc_html__( 'New', 'plethora-framework') .' ' . $singular .' '. esc_html__( 'Name', 'plethora-framework') , 
                'parent_item'                => esc_html__( 'Parent', 'plethora-framework') .' ' . $plural, 
                'parent_item_colon'          => esc_html__( 'Parent', 'plethora-framework') .' ' . $plural .':', 
                'search_items'               => esc_html__( 'Search', 'plethora-framework') .' ' . $plural,        
                'popular_items'              => esc_html__( 'Popular', 'plethora-framework') .' ' . $plural, 
                'separate_items_with_commas' => esc_html__( 'Seperate', 'plethora-framework') .' ' . $plural .' '. esc_html__('with commas', 'plethora-framework') , 
                'add_or_remove_items'        => esc_html__( 'Add or remove', 'plethora-framework') .' ' . $plural, 
                'choose_from_most_used'      => esc_html__( 'Choose from most used ', 'plethora-framework') .' ' . $plural, 
                'not_found'                  => esc_html__( 'No', 'plethora-framework') .' ' . $plural .' '. esc_html__('found', 'plethora-framework') , 

            );

            $defaults = array(
                'labels'       => $labels,
                'hierarchical' => true,
                'rewrite'      => array( 'slug' => $slug )
            );

            $options = $this->options_merge($defaults, $options);

            if(!taxonomy_exists($taxonomy_name)) {

                register_taxonomy($taxonomy_name, $post_type, $options);

                $this->taxonomies[] = $taxonomy_name;

            }

            add_filter('manage_edit-' . $post_type . '_columns', array( &$this, 'add_admin_columns') );
            add_action('manage_' . $post_type . '_posts_custom_column', array( &$this, 'populate_admin_columns'), 10, 2 );
            add_action('restrict_manage_posts', array( &$this, 'add_taxonomy_filters' ) );

        }


        function filters( $filters = array() ) {

            $this->filters = $filters;

        }



        function add_taxonomy_filters() {

            global $typenow;
            global $wp_query;

            if($typenow == $this->post_type_name){

                if(is_array($this->filters)) {

                    $filters = $this->filters;

                } else {

                    $filters = $this->taxonomies;

                }

                foreach($filters as $tax_slug) {

                    $tax = get_taxonomy($tax_slug);

                    $args = array(
                        'orderby' => 'name',
                        'hide_empty' => false
                    );

                    $terms = get_terms($tax_slug, $args);

                    if($terms) {

                        printf(' &nbsp;<select name="%s" class="postform">', $tax_slug);

                        printf('<option value="0">%s</option>', 'Show all ' . $tax->label);

                        foreach ($terms as $term) {
                            
                            if(isset($_GET[$tax_slug]) && $_GET[$tax_slug] === $term->slug) {

                                printf('<option value="%s" selected="selected">%s (%s)</option>', $term->slug, $term->name, $term->count);

                            } else {

                                printf('<option value="%s">%s (%s)</option>', $term->slug, $term->name, $term->count);

                            }

                        }

                        print('</select>&nbsp;');

                    }

                }

            }

        }

        function set_post_icon() {

            ?><style type="text/css" media="screen">
                #menu-posts-<?php print($this->post_type_name); ?> .wp-menu-image {
                   <?php 
                   if ( method_exists($this, 'menu_icon' ) && isset($this->menu_icon['menu'] )) { 

                       print($this->menu_icon['menu']); 
                   }

                   ?>
                }

                #menu-posts-<?php print($this->post_type_name); ?>:hover .wp-menu-image,
                #menu-posts-<?php print($this->post_type_name); ?>.wp-has-current-submenu .wp-menu-image {
                   <?php 
                   if ( method_exists($this, 'menu_icon')  && isset($this->menu_icon['hover'] )) { 

                       print($this->menu_icon['hover']); 

                   }
                   ?>
                }

                #icon-edit.icon32-posts-<?php print($this->post_type_name); ?> {
                    <?php 
                   if ( method_exists($this, 'menu_icon' ) && isset($this->menu_icon['edit'] )) { 

                       print($this->menu_icon['edit']); 

                   }
                   ?>
                }
            </style><?php

        }

        function sort_columns($vars) {

            foreach($this->sortable as $column => $values) {

                $meta_key = $values[0];

                if(isset($values[1]) && true === $values[1]) {

                    $orderby = 'meta_value_num';

                } else {

                    $orderby = 'meta_value';

                }

                if (isset($vars['post_type']) && $this->post_type_name == $vars['post_type']) {

                    if (isset($vars['orderby']) && $meta_key == $vars['orderby']) {

                        $vars = array_merge($vars,
                            array(
                                'meta_key' => $meta_key,
                                'orderby' => $orderby
                            )
                        );
                    }

                }

            }

            return $vars;
        }

        function add_admin_columns($columns) {

            if(!isset($this->columns)) {

                $columns = array(
                    'cb' => '<input type="checkbox" />',
                    'title' => esc_html__('Title', 'plethora-framework')
                );

                if(is_array($this->taxonomies)) {

                    foreach($this->taxonomies as $tax) {

                        $taxonomy_object = get_taxonomy($tax);
                        $columns[$tax]   = $taxonomy_object->labels->name;

                    }

                }

                if(post_type_supports($this->post_type_name, 'comments')) {

                    $columns['comments'] = '<img alt="Comments" src="'. site_url() .'/wp-admin/images/comment-grey-bubble.png">';

                }

                $columns['date'] = esc_html__('Date', 'plethora-framework');

            } else {

                $columns = $this->columns;

            }

            return $columns;

        }

        public $native_icons = array(
            'dashboard' => array(
                'menu' => 'background-position: -59px -33px !important;',
                'hover' => 'background-position: -59px -1px !important;',
                'edit' => 'background-position: -137px -5px !important;'
            ),
            'posts' => array(
                'menu' => 'background-position: -269px -33px !important;',
                'hover' => 'background-position: -269px -1px !important;',
                'edit' => 'background-position: -552px -5px !important;'
            ),
            'media' => array(
                'menu' => 'background-position: -119px -33px !important;',
                'hover' => 'background-position: -119px -1px !important;',
                'edit' => 'background-position: -251px -5px !important;'
            ),
            'links' => array(
                'menu' => 'background-position: -89px -33px !important;',
                'hover' => 'background-position: -89px -1px !important;',
                'edit' => 'background-position: -190px -5px !important;'
            ),
            'pages' => array(
                'menu' => 'background-position: -149px -33px !important;',
                'hover' => 'background-position: -149px -1px !important;',
                'edit' => 'background-position: -312px -5px !important;'
            ),
            'comments' => array(
                'menu' => 'background-position: -29px -33px !important;',
                'hover' => 'background-position: -29px -1px !important;',
                'edit' => 'background-position: -72px -5px !important;'
            ),
            'appearance' => array(
                'menu' => 'background-position: 1px -33px !important;',
                'hover' => 'background-position: 1px -1px !important;',
                'edit' => 'background-position: -11px -5px !important;'
            ),
            'plugins' => array(
                'menu' => 'background-position: -179px -33px !important;',
                'hover' => 'background-position: -179px -1px !important;',
                'edit' => 'background-position: -370px -5px !important;'
            ),
            'users' => array(
                'menu' => 'background-position: -300px -33px !important;',
                'hover' => 'background-position: -300px -1px !important;',
                'edit' => 'background-position: -600px -5px !important;'
            ),
            'tools' => array(
                'menu' => 'background-position: -209px -33px !important;',
                'hover' => 'background-position: -209px -1px !important;',
                'edit' => 'background-position: -432px -5px !important;'
            ),
            'settings' => array(
                'menu' => 'background-position: -239px -33px !important;',
                'hover' => 'background-position: -239px -1px !important;',
                'edit' => 'background-position: -492px -5px !important;'
            ),
            'cog' => array(
                'menu' => 'background-position: -330px -33px !important;',
                'hover' => 'background-position: -330px -1px !important;',
                'edit' => 'background-position: -708px -5px !important;'
            ),
            'keys' => array(
                'menu' => 'background-position: -361px -33px !important;',
                'hover' => 'background-position: -361px -1px !important;',
                'edit' => 'background-position: -661px -5px !important;'
            )
        );

        function populate_admin_columns($column, $post_id) {

            global $post;

            switch($column) {

                case (taxonomy_exists($column)) :

                    $terms = get_the_terms($post_id, $column);

                    if (!empty($terms)) {

                        $output = array();

                        foreach($terms as $term) {

                            $output[] = sprintf(

                                '<a href="%s">%s</a>',

                                esc_url(add_query_arg(array('post_type' => $post->post_type, $column => $term->slug), 'edit.php')),
                                esc_html(sanitize_term_field('name', $term->name, $term->term_id, $column, 'display'))

                            );

                        }

                        echo join(', ', $output);

                    } else {

                        $taxonomy_object = get_taxonomy($column);
                        echo esc_html__('No', 'plethora-framework') .' '. $taxonomy_object->labels->name;

                    }


                break;

                case 'post_id' : 

                    echo $post->ID;
                
                break;

                case (preg_match('/^meta_/', $column) ? true : false) :

                    $x    = substr($column, 5);
                    $meta = get_post_meta($post->ID, $x);

                    echo join(", ", $meta);

                break;

                case 'icon' : 

                    $link = esc_url(add_query_arg(array('post' => $post->ID, 'action' => 'edit'), 'post.php'));

                    if(has_post_thumbnail()) {

                        echo '<a href="'. $link .'">';
                            the_post_thumbnail(array(60, 60));
                        echo '</a>';

                    } else {

                        echo '<a href="'.$link.'"><img src="'. site_url('/wp-includes/images/crystal/default.png') .'" alt="'. $post->post_title .'" /></a>';
                    
                    }

                break;

                default : 

                    if(isset($this->custom_populate_columns) && is_array($this->custom_populate_columns)) {

                        if(isset($this->custom_populate_columns[$column]) && is_callable($this->custom_populate_columns[$column])) {

                            $this->custom_populate_columns[$column]($column, $post);

                        }

                    } 

                break;

            } 

        }

        function make_columns_sortable($columns) {

            foreach($this->sortable as $column => $values) {

                $sortable_columns[$column] = $values[0];

            }

            $columns = array_merge($sortable_columns, $columns);

            return $columns;

        } 

        function columns($columns) {

            if(isset($columns)) {

                $this->columns = $columns;
            
            }

        }


        function populate_column($column_name, $function) {

            $this->custom_populate_columns[$column_name] = $function;

        }


        function sortable($columns = array()) {
            
            $this->sortable = $columns;
            add_filter('manage_edit-' . $this->post_type_name . '_sortable_columns', array(&$this, 'make_columns_sortable'));
            add_action('load-edit.php', array(&$this, 'load_edit'));

        }

        function load_edit() {

            add_filter( 'request', array(&$this, 'sort_columns') );

        }

    }

}