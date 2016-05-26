<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M               (c) 2013-2015

File Description: Posts Grid shortcode

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_FilterableGrid')):

    /**
     * @package Plethora Framework
    **/
    class Plethora_Shortcode_FilterableGrid extends Plethora_Shortcode { 

        public $wp_slug                      = 'filterablegrid';                      // SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT the prefix constant )
        public static $feature_name          = "Filterable Grid";                     // FEATURE DISPLAY TITLE 
        public static $feature_title         = "Filterable Grid Shortcode";           // FEATURE DISPLAY TITLE 
        public static $feature_description   = "Displays a filterable grid of Posts"; // FEATURE DISPLAY DESCRIPTION 
        public static $theme_option_control  = true;                                  // WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL 
        public static $theme_option_default  = true;                                  // DEFAULT ACTIVATION OPTION STATUS
        public static $theme_option_requires = array();                               // WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
        public static $dynamic_construct     = true;                                  // DYNAMIC CLASS CONSTRUCTION ? 
        public static $dynamic_method        = false;                                 // Additional method invocation ( string/boolean | method name or false )
        public static $assets                = array(
                                                array( 'script' => array( 'isotope' ) ), 
                                                array( 'style'  => array( 'imagelightbox' ) ), 
                                                array( 'script' => array( 'imagelightbox' ) ),
                                                // LINKIFY
                                                array( 'script' => 'svgloader-snap' ),  
                                                array( 'script' => 'svgloader' ),       
                                                array( 'script' => 'svgloader-init' )       
                                                );

        public static $supported_posttypes   = array( "post", "product", "portfolio" );
        public static $shortcode_category    = "Posts Grids";

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
                      // 'custom_markup' => $this->vc_custom_markup( self::$feature_name ), 
                      'params'        => $this->params(), 
                      );
          $this->add( $map );         // ADD THE SHORTCODE
        }

      /**
        * Returns array of category filters
        * @param  [Array]  $options 
        * @return [Array]          
        */
        public function get_filtered_categories( $options ){

          $defaults = array();
          $options  = ( isset($options) && is_array($options) ) ? array_replace_recursive ( $defaults , $options ) : $defaults;

          $shortcode_atts_category_filter     = array( 'category_filter' => array() );
          $res_categories                     = ( is_null( $options["categories"] ) ) ? array() : $options["categories"];   
          $shortcode_atts["categories"]       = array_diff( $res_categories, $options['exclude'] );        // REMOVE EXCLUDED CATEGORIES

          foreach ( $shortcode_atts["categories"] as $categorykey => $categoryname ) { 

            array_push( $shortcode_atts_category_filter['category_filter'], array(

              "data-filter"   => esc_attr(strtolower(sanitize_html_class( $categoryname ))),
              "category_name" => $categoryname

            ));

          }

          return $shortcode_atts_category_filter;

        }

        /** 
        * Returns shortcode settings (compatible with Visual composer)
        * @return array
        * @since 1.0
        */
        public function params() {

          $params = array(

                  array(
                      "param_name"  => "post_type",
                      "type"        => "dropdown_post_types",
                      "args"        => array( 'include' =>  self::$supported_posttypes ),
                      "heading"     => esc_html__('Select Post Type', 'plethora-framework'),
                      "value"       => 'post',
                      "description" => esc_html__("Select the Post Type that will be displayed in the Grid.", 'plethora-framework'),
                      'admin_label' => false,
                      'holder'      => 'h3',                                               
                      'class'       => 'plethora_vc_title',                                                    
                  ),
                  array(
                      "param_name"    => "columns",
                      "type"          => "dropdown",
                      "heading"       => esc_html__("Grid columns", 'plethora-framework'),
                      "value"         => array(
                        esc_html__('2', 'plethora-framework') => '2',
                        esc_html__('3', 'plethora-framework') => '3',
                        esc_html__('4', 'plethora-framework') => '4'
                        ),
                      "description"   => esc_html__("Set the number of grid columns you need", 'plethora-framework')
                  ),
                  array(
                      "param_name"    => "results",
                      "type"          => "textfield",
                      "heading"       => esc_html__('Maximum post results', 'plethora-framework'),
                      "value"         => '12',
                      "description"   => esc_html__("Set the maximum results in grid. Leave empty or set it to zero to display all results", 'plethora-framework')
                  ),
                  array(
                      "param_name"    => "orderby",
                      "type"          => "dropdown",
                      "heading"       => esc_html__('Order by', 'plethora-framework'),
                      "value"         => array(esc_html__('Date', 'plethora-framework') =>'date', esc_html__('Random', 'plethora-framework')  => 'random'),
                      "description"   => esc_html__("Select order", 'plethora-framework')
                  ),
                  array(
                      "param_name"    => "category_filters",
                      "type"          => "dropdown",
                      "heading"       => esc_html__('Filter Menu', 'plethora-framework'),
                      "value"         => array( esc_html__('Display', 'plethora-framework') =>'yes', esc_html__('Hide', 'plethora-framework')  => 'no'),
                      "description"   => esc_html__("Display the categories menu", 'plethora-framework')
                  ),
                  array(
                      "param_name" => "layout",
                      "type"       => "dropdown",
                      "heading"    => esc_html__("Listing style", 'plethora-framework'),
                      "value"      => array(
                        esc_html__('Image Card', 'plethora-framework')   =>'classic', 
                        esc_html__('Classic Card', 'plethora-framework') =>'masonry'
                      ),
                      "description" => esc_html__("Select the desired post grid layout", 'plethora-framework'),
                      "dependency"  => array(
                        "element" => "post_type",
                        "value"   => array("post"),
                        )
                  ),
                  array(
                      "param_name"    => "masonry_layout",
                      "type"          => "dropdown",
                      "heading"       => esc_html__("Masonry layout style", 'plethora-framework'),
                      "value"         => array(
                        esc_html__('Blog Masonry', 'plethora-framework')    =>'blog', 
                        esc_html__('Gallery Masonry', 'plethora-framework') =>'gallery'
                      ),
                      "description"   => esc_html__("Select the desired masonry layout style", 'plethora-framework'),
                      "dependency"    => array(
                        "element" => "layout",
                        "value"   => array("masonry"),
                        )
                  ),
                  array(
                      "param_name"    => "masonry_blog_color_set",
                      "type"          => "dropdown",
                      "heading"       => esc_html__("Color Set", 'plethora-framework'),
                      "value"         => Plethora_Module_Style::get_options_array( array( 
                                        'type'            => 'color_sets', 
                                        'use_in'          => 'vc',
                                        'prepend_default' => true
                                        ) 
                      ),
                      "description"   => esc_html__("Choose a color setup for this section. Remember: all colors in above options can be configured via the theme options panel", 'plethora-framework'),
                      "admin_label"   => false, 
                      "dependency"    => array(
                        "element" => "layout",
                        "value"   => "masonry"
                      )
                  ),
                  array(
                      "param_name"    => "excerpt_setting",
                      "type"          => "dropdown",
                      "heading"       => esc_html__("Description Source", 'plethora-framework'),
                      "value"         => array(
                        esc_html__('Excerpt', 'plethora-framework')         => 'excerpt',
                        esc_html__('Content excerpt', 'plethora-framework') =>'content',
                        esc_html__('None', 'plethora-framework')               => 'none'
                        ),
                      "description"   => esc_html__("Select the desired content for the hover description", 'plethora-framework'),
                      "dependency"    => array(
                        "element" => "post_type",
                        "value"   => array( "post" ),
                        )
                  ),
                  array(
                      "param_name"    => "content_excerpt_count",
                      "type"          => "textfield",
                      "heading"       => esc_html__('Maximum characters', 'plethora-framework'),
                      "value"         => '150',
                      "description"   => esc_html__("This will truncate the content excerpt to the number of characters provided.", 'plethora-framework'),
                      "dependency"    => array(
                        "element" => "excerpt_setting",
                        "value"   => "content"
                      )
                  ),
                  array(
                        "param_name"   => "media_ratio",
                        "type"         => "value_picker",
                        "heading"      => esc_html__('Media Display Ratio', 'plethora-framework'),
                        "picker_type"  => "single",  // Multiple or single class selection ( 'single'|'multiple' )
                        "picker_cols"  => "6",         // Picker columns for selections display ( 1, 2, 3, 4, 6 )                                       
                        "value"        => 'stretchy_wrapper ratio_2-1',     
                        "values_index" => Plethora_Module_Style::get_options_array( array( 
                                          'type'   => 'stretchy_ratios', 
                                          'use_in' => 'vc', 
                                          )),            
                  ),
                  array(
                      "param_name"    => "linkify",
                      "type"          => "checkbox",
                      "heading"       => esc_html__("Enable AJAX", 'plethora-framework'),
                      "value"         => array( 'Enable' => 'enable' ),
                      "description"   => esc_html__("Enable content loading via AJAX", 'plethora-framework'),
                  ),
                  array(
                    "param_name"    => "button_style",                                  
                    "type"          => "dropdown",                                        
                    "holder"        => "",                                               
                    "class"         => "vc_hidden",                                         
                    "heading"       => esc_html__("Button styling", 'plethora-framework'),      
                    "value"         => array(
                      'Default'   => 'btn-default',
                      'Primary'   => 'btn-primary',
                      'Secondary' => 'btn-secondary',
                      'White'     => 'btn-white',
                      'Success'   => 'btn-success',
                      'Info'      => 'btn-info',
                      'Warning'   => 'btn-warning',
                      'Danger'    => 'btn-danger',
                      'Text-Link' => 'btn-link',
                      ),
                    "admin_label"   => false, 
                    "dependency"    => array(
                      "element" => "layout",
                      "value"   => array("masonry"),
                      )
                  ),
                  array(
                      "param_name"    => "showall",
                      "type"          => "dropdown",
                      "heading"       => esc_html__('Display SHOW ALL', 'plethora-framework'),
                      "value"         => array( esc_html__('Show', 'plethora-framework') => '1', esc_html__('Hide', 'plethora-framework')  => '0'),
                      "description"   => esc_html__("Display SHOW ALL Button", 'plethora-framework')
                  ),
                  array(
                      "param_name"    => "doublehelix",
                      "type"          => "checkbox",
                      "heading"       => esc_html__("Enable Double Helix Effect", 'plethora-framework'),
                      "value"         => array( 'Enable' => 'enable' ),
                      "description"   => esc_html__("Enable the Double Helix effect on hover. It does not work when Blog Masonry layout is selected.", 'plethora-framework'),
                      "dependency"    => array(
                        "element" => "layout",
                        "value"   => array("classic"),
                        )
                  ),
                  array(
                      "param_name"    => "exclude_categories",
                      "type"          => "checkbox",
                      "heading"       => esc_html__("Exclude categories", 'plethora-framework'),
                      "value"         => class_exists('Plethora_WP') ? Plethora_WP::categories(array('taxonomy'=>'category')) : Plethora_Helper::array_categories(array('taxonomy'=>'category')) ,
                      "description"   => esc_html__("Check every category that you don't want to be included in results", 'plethora-framework'),
                      "dependency"    => array(
                        "element" => "post_type",
                        "value"   => array("post")
                      )
                  )
          );

          return $params;
        }

        /**
        * Set bootstrap layout column values according to user setting
        * @param array $options [description]
        */
        public function set_columns_values( $args ){

          $default_args = array( 
                  'columns'   => '2',     
          );
          $args = wp_parse_args( $args, $default_args);          // MERGE GIVEN ARGS WITH DEFAULTS

          // SET COLUMNS VALUES
          switch ( $args["columns"] ) {
              case '2':
                $col = '6';
                break;
              case '3':
                $col = '4';
                break;
              case '4':
                $col = '3';
                break;
              default:
                $col = '3';
          }

          return array( "col" => $col, "thumbsize" => "large" );

        }

        public function get_product_price( $productID ){
          
          // GET PRODUCT PRICE(S)
          $has_sale_price = NULL;
          $product_meta   = get_post_meta( $productID );

          /*
          $product_meta['_min_variation_price'][0]
          $product_meta['_max_variation_price'][0]
          $product_meta['_min_variation_regular_price'][0]
          $product_meta['_max_variation_regular_price'][0]
          $product_meta['_min_variation_sale_price'][0]
          $product_meta['_max_variation_sale_price'][0]
          $product_regular_price = $product_meta['_regular_price'][0];
          */

          $currency = get_woocommerce_currency_symbol(); // get_woocommerce_currency() -> GBP

          // IF PRODUCT HAS A SALES PRICE
          if ( $product_meta['_sale_price'][0] !== "" ){ 
            $has_sale_price     = "TRUE"; 
            $product_price      = $currency . $product_meta['_regular_price'][0];  
            $product_sale_price = $currency . $product_meta['_sale_price'][0];
          } else {
            // PRODUCT HAS SINGLE PRICE
            if ( $product_meta['_min_variation_price'][0] == "" ){
              $product_price      = $currency . $product_meta['_price'][0];
            } else {
              // HAS VARIABLE PRICES FOR PRODUCT VARIETY 
              if ( $product_meta['_min_variation_price'][0] != $product_meta['_max_variation_price'][0] ){
                $product_price = $currency . $product_meta['_min_variation_price'][0] . " - " . $currency . $product_meta['_max_variation_price'][0];
              // HAS SAME PRICE FOR PRODUCT VARIETY 
              } else {
                $product_price = $currency . $product_meta['_min_variation_price'][0];
              }
            }
          }

          return array(
            'has_sale_price'     => $has_sale_price,
            'product_price'      => $product_price,
            'product_sale_price' => $product_sale_price
          );

        }

        public $product_categories_filter = array();  // WILL CONTAIN ALL WOO PRODUCTS CATEGORIES

        /**
         * Return product categories
         * @param  [type] $productID [description]
         * @return [Array]            
         */
        public function parse_product( $productID ){

            $singlecat                 = "";
            $product_categories_filter = array();
            $product_cats              = wp_get_post_terms( $productID, 'product_cat' );

            if ( $product_cats && ! is_wp_error ( $product_cats ) ){

              foreach ( $product_cats as $key => $value ) {  $singlecat .= $value->name . " / ";  } // $value->slug
              $singlecat    = preg_replace( "/\/\s$/", "", $singlecat );
              $single_cat   = array_shift( $product_cats );

              // SINGLE PRODUCT CATEGORIES
              if ( ! array_key_exists( $single_cat->slug, $product_categories_filter ) ){
                $product_categories_filter[ $single_cat->slug ] = $single_cat->name;
                $post_cat_classes .= $single_cat->slug . ' ';
              }

              // ALL PRODUCTS CATEGORIES
              if ( ! array_key_exists( $single_cat->slug, $this->product_categories_filter ) ){
                $this->product_categories_filter[ $single_cat->slug ] = $single_cat->name;
              }

            }

            return array(

              "singlecat"        => $singlecat,
              "post_cat_classes" => $post_cat_classes

            );

        }

        public function get_post_excerpt( $options ){

            if ( ! isset( $options['postID'] ) ) return "";

            $defaults = array( 
              "excerpt_setting"       => "excerpt",
              "content_excerpt_count" => "200"
            );

            $options  = ( isset( $options ) && is_array( $options ) ) ? array_replace_recursive ( $defaults , $options ) : $defaults;

            extract( $options, EXTR_OVERWRITE );

            $excerpt = "";

            switch ( $excerpt_setting ) {
              case 'excerpt':
                $excerpt = ( has_excerpt( $postID) ) ? get_the_excerpt() : "";
                break;
              case 'content':
                if ( has_excerpt( $postID) ){
                  $excerpt = get_the_excerpt();
                } else {
                  $excerpt = get_the_excerpt();
                  if ( $excerpt != "" ){
                    $excerpt = strip_tags( $excerpt );
                    $excerpt = substr( $excerpt, 0, $content_excerpt_count );
                    $excerpt .= " [...]";
                  }
                }
                break;
              case 'none':
                $excerpt = NULL;
                break;
              default:
                $excerpt = get_the_excerpt();
                break;
            }

            return $excerpt;

        }

        /** 
        * Prepares and filters shortcode content for use with template engine
        * @return array
        * @since 1.0
        */
        public function content( $atts, $content = null ) {

          // EXTRACT USER INPUT
          extract( shortcode_atts( array( 
            'post_type'              => 'post',
            'layout'                 => 'classic',
            'masonry_layout'         => 'blog',
            'button_style'           => 'btn-default',
            'masonry_blog_color_set' => '',
            'columns'                => '2',
            'excerpt_setting'        => 'excerpt',
            'content_excerpt_count'  => '200',
            'media_ratio'            => 'stretchy_wrapper ratio_2-1',
            'doublehelix'            => '',
            'linkify'                => '',
            'results'                => '-1',
            'orderby'                => 'date',
            'category_filters'       => 'yes',
            'exclude_categories'     => '',
            'showall'                => '1'
            ), $atts ) );

          $has_post_formats = false;

          // PREPARE FINAL VALUES THAT WILL BE USED IN TEMPLATE

          $layout_type     = $layout;
          $layout          = ( $layout == 'masonry' ) ? 'portfolio_masonry' : 'portfolio_strict'; // SET LAYOUT CLASS VALUE
          $blog_masonry    = ( $masonry_layout == "blog" )? TRUE : NULL;
          $gallery_masonry = ( $masonry_layout == "gallery" ) ? TRUE : NULL;

          $orderby         = ( $orderby == 'Random') ? 'rand' : 'date';                           // SET ORDERBY VALUE
          $layout_grid     = $this->set_columns_values( array( "columns" => $columns ) );         // GET BOOTSTRAP COL GRID VALUES
          $exclude         = array();

          if ( !empty( $exclude_categories ) ) {  $exclude = explode( ",", $exclude_categories );  }

          $results = is_numeric( $results ) && intval( $results ) > 0 ? intval( $results ) : -1 ; // CHECK THAT MAX RESULTS IS NUMERIC AND NOT 0 OR EMPTY

          // PACK TEMPLATE DATA INTO ARRAY VAR
          $shortcode_atts = array (
                                  'content'                => $content,  
                                  'layout'                 => $layout,
                                  'blog_masonry'           => $blog_masonry,
                                  'gallery_masonry'        => $gallery_masonry,
                                  'masonry_blog_color_set' => $masonry_blog_color_set,
                                  'col'                    => $layout_grid['col'], 
                                  'thumbsize'              => $layout_grid['thumbsize'], 
                                  'results'                => $results, 
                                  'orderby'                => $orderby, 
                                  'category_filters'       => $category_filters,
                                  'exclude'                => $exclude, 
                                  'showall'                => ( $showall == '1' ) ? esc_html__('Show All', 'plethora-framework') : "", 
                                  'results'                => $results,
                                  'postsgrid_id'           => $postsgrid_id = mt_rand( 1,5000 ) // VERY IMPORTANT! This generates a unique id for javascript inits
                                 );

          if ( $doublehelix == "enable" ) wp_enqueue_script( ASSETS_PREFIX . '-double_helix' ); 
          $shortcode_atts['double_helix'] = 'double_helix';

          // QUERY ARGUMENTS
          $args = array(
            'posts_per_page'      => $results,
            'ignore_sticky_posts' => 0,
            'post_type'           => $post_type,
            'orderby'             => $orderby,
            'tax_query'           => array(
                        array(
                          'taxonomy' => 'category',
                          'field'    => 'id',
                          'terms'    => $exclude,
                          'operator' => 'NOT IN'                          
                        )
            )        
          );

          $post_query = new WP_Query($args);

          /*** POSTS FILTERS SECTION >>> ***/

          // ASSIGN POST RESULTS TO A VARIABLE TO GET ACTIVE CATEGORIES ONLY
          if ( $post_query->have_posts() ) {

            $shortcode_atts["categories"]    = array();   // WILL CONTAIN POSTS CATEGORIES
            $shortcode_atts["section_posts"] = array();

            while ( $post_query->have_posts() ) { 

              $post_query->the_post();

              $postID = get_the_ID();
              $post_categories_array  = wp_get_object_terms( $postID, 'category' ); // GET POSTS CATEGORIES. wp_get_object_terms FOR CPT
              $post_cat_classes = '';
              $post_categories  = '';
              $post_format      = get_post_format( $postID );

              if ( !empty( $post_categories_array ) && !is_wp_error( $post_categories_array ) ) { 

                 // SET VARIABLE THAT WILL CONTAIN ALL CATEGORY SELECTOR CLASSES
                 foreach ( $post_categories_array as $category ) { 

                    $shortcode_atts["categories"][] = $category->name;
                    $post_cat_classes .= strtolower(sanitize_html_class( $category->name )) .' ';
                    $post_categories  .= $category->name .' ';

                  }
              } 

              // STORE POST CONTENT AND DATA FOR USE IN THE TEMPLATE LOOP

              $thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), $layout_grid['thumbsize'] );

              /* WOOCOMMERCE PRODUCT */

              if ( $post_type === "product" ){

                $is_product       = TRUE;
                $parse_product    = $this->parse_product( $postID );   // GET PRODUCT CATEGORIES
                $singlecat        = $parse_product['singlecat'];            // WILL CONTAIN WOO PRODUCT SINGLE CATEGORY
                $post_cat_classes = $parse_product['post_cat_classes'];
                $price            = $this->get_product_price( $postID );             
                $excerpt          = "";

              } else {

                // GET THE POST EXCERPT ACCORDING TO OPTIONS
                $excerpt = $this->get_post_excerpt( array(
                  "postID"                => $postID, 
                  "excerpt_setting"       => $excerpt_setting, 
                  "content_excerpt_count" => $content_excerpt_count 
                )); 

              }
              /* WOOCOMMERCE PRODUCT */

              if ( $post_format && $layout_type == 'classic' || $post_format && $masonry_layout != 'blog' ){ 

                $has_post_formats = true;
                switch ( $post_format ) {
                  case 'video':
                    $permalink = get_post_meta( $postID, "ple-content-video", true);
                    break;
                  case 'link':
                    $permalink = get_the_content();
                    break;
                  case 'image':
                    $permalink = esc_url( $thumb_url[0] );
                    break;
                  default:
                    $permalink = get_the_permalink();
                    break;
                }

              } else {

                $permalink = get_the_permalink(); 

              }

              if ( $layout_type == 'classic' ){ 

                $linkify_class = ( $linkify == "enable" && $post_format != "video" && $post_format != "image" )? "linkify" : "";

              } else {

                $linkify_class = ( $linkify == "enable" )? "linkify" : "";

              }

              $shortcode_atts["section_posts"][] = array(

                "is_product"         => isset( $is_product )? TRUE : "",                      // WOO: CHECK IF PRODUCT
                "product_categories" => isset( $singlecat )? $singlecat : "",                 // WOO: PRODUCT CATEGORIES
                "has_sale_price"     => isset( $price )? $price['has_sale_price'] : "",       // WOO: Does product has a sale price?
                "product_price"      => isset( $price )? $price['product_price'] : "",        // WOO: PRICE
                "product_sale_price" => isset( $price )? $price['product_sale_price'] : "",   // WOO: SALE PRICE
                "lightbox"           => ( $post_format == "video" || $post_format == "image" )? 'filterable_lightbox' : '',
                "post_cat_classes"   => $post_cat_classes,
                "col"                => $layout_grid['col'],
                "permalink"          => $permalink,
                "title"              => esc_attr(get_the_title()),
                "excerpt"            => $excerpt,
                "post_categories"    => $post_categories,
                "background_image"   => esc_url( $thumb_url[0] ),
                "image"              => esc_url( $thumb_url[0] ),
                "image_title"        => esc_attr( get_the_title() ),
                "media_ratio"        => $media_ratio,
                "linkify"            => $linkify_class  // ENABLE ON CLASSIC CARD AND IMAGE CARDS THAT ARE NOT LIGHTBOXED

              );

            }

          }

          /*** <<< POSTS FILTERS SECTION ***/

          /*** CATEGORY FILTERS SECTION >>> ***/

          if ( $category_filters == 'yes' && !empty( $shortcode_atts["categories"] ) ){   // POSTS CATEGORY FILTERING

              $category_filters_array = array_unique( $shortcode_atts["categories"] );    // GET UNIQUE CATEGORIES FOUND IN RESULTS

          } elseif ( $post_type === "product" ) {                                         // PRODUCT CATEGORY FILTERING

              $category_filters_array = $this->product_categories_filter;                       

          }

          if ( $category_filters == 'yes' ){
            
            $shortcode_atts['category_filters'] = $this->get_filtered_categories( array( 

              "categories" => $category_filters_array,
              "exclude"    => $exclude 

            ));

          } else {

            $shortcode_atts['category_filters'] = "";

          }

          /*** <<< CATEGORY FILTERS SECTION ***/

          $layout                         = str_replace( "portfolio_", "", $layout );
          $shortcode_atts[$layout]        = true;  // ACTIVATE PORTFOLIO TYPE
          $shortcode_atts['button_style'] = $button_style;

          wp_reset_postdata();    

          return Plethora_WP::renderMustache( array( "data" => $shortcode_atts, "file" => __FILE__ ) );

        }

    }
    
 endif;