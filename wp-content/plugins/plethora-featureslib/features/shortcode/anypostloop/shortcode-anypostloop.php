<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2016

File Description: Projects Grid shortcode

*/

if ( ! defined( 'ABSPATH' )) exit; // NO ACCESS IF DIRECT OR TEAM POST TYPE NOT EXISTS


if ( class_exists('Plethora_Shortcode') && !class_exists('Plethora_Shortcode_Anypostloop') ):

  /**
   * @package Plethora Framework
   */

  class Plethora_Shortcode_Anypostloop extends Plethora_Shortcode { 

      public static $feature_title         = 'Post Loop Shortcodes';       // Feature display title  (string)
      public static $feature_description   = 'Manages all Post Loop shortcodes ( "Posts Loop", "Profiles Loop", etc )';                              // Feature display description (string)
      public static $theme_option_control  = true;                            // Will this feature be controlled in theme options panel ( boolean )
      public static $theme_option_default  = true;                            // Default activation option status ( boolean )
      public static $theme_option_requires = array();                         // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
      public static $dynamic_construct     = false;                            // Dynamic class construction ? ( boolean )
      public static $dynamic_method        = false;                           // Additional method invocation ( string/boolean | method name or false )
      public static $assets                = array(

                                                array( 'script' => array( 'isotope' ) ),
                                                array( 'script' => array( 'tweenmax' ) ), 
                                             );
      public $wp_slug;    // the shortcode slug
      public $post_type;  // post type object
      public $sc_name;    // shortcode name               
      public $sc_desc;    // shortcode description               

      public function __construct( $post_type_obj ) {

        // Set basic info
        $this->post_type = $post_type_obj;
        $this->wp_slug   = SHORTCODES_PREFIX . 'loop_'. $post_type_obj->name;
        $this->sc_name   = sprintf( esc_html__('%1$s Loop', 'plethora-framework'), $post_type_obj->label );
        $this->sc_desc   = sprintf( esc_html__('Create a grid/masonry/list with %1$s items', 'plethora-framework'), $post_type_obj->labels->singular_name );

        // Add CALLBACK filters for autocomplete fields
        add_filter( 'vc_autocomplete_'. $this->wp_slug.'_data_posts_selection_callback', array( $this, 'search_data_posts' ), 10 );
        add_filter( 'vc_autocomplete_'. $this->wp_slug.'_data_posts_exclude_callback', array( $this, 'search_data_posts' ), 10 );
        add_filter( 'vc_autocomplete_'. $this->wp_slug.'_data_tax_include_callback', array( $this, 'search_data_tax' ), 10 );
        add_filter( 'vc_autocomplete_'. $this->wp_slug.'_data_tax_exclude_callback', array( $this, 'search_data_tax' ), 10 );

        // Add RENDER filters for autocomplete fields
        add_filter( 'vc_autocomplete_'. $this->wp_slug.'_data_posts_selection_render', array( $this, 'render_data_posts' ), 10 );
        add_filter( 'vc_autocomplete_'. $this->wp_slug.'_data_posts_exclude_render', array( $this, 'render_data_posts' ), 10 );
        add_filter( 'vc_autocomplete_'. $this->wp_slug.'_data_tax_include_render', array( $this, 'render_data_tax' ), 10 );
        add_filter( 'vc_autocomplete_'. $this->wp_slug.'_data_tax_exclude_render', array( $this, 'render_data_tax' ), 10 );

        // Initialize mapping
        $this->init();

      }

      public function init() {

        // Map shortcode settings according to VC documentation ( https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332 )
        $map = array( 
                      'base'              => $this->wp_slug,
                      'name'              => $this->sc_name,
                      'description'       => $this->sc_desc,
                      'class'             => '',
                      'weight'            => 1,
                      'category'          => esc_html__('Plethora Shortcodes', 'plethora-framework'),
                      'admin_enqueue_js'  => array(), 
                      'admin_enqueue_css' => array(),
                      'icon'              => $this->vc_icon(), 
                      // 'custom_markup'     => $this->vc_custom_markup( 'Profiles Grid' ), 
                      'params'            => $this->params(), 
                      );
        // Add the shortcode
        $this->add( $map );

      }

       /** 
       * Returns shortcode settings (compatible with Visual composer)
       *
       * @return array
       * @since 1.0
       *
       */
       public function params() {

          $params = array(

                  array(
                      'param_name'  => 'data',
                      'type'        => 'dropdown',
                      'heading'     => esc_html__('Data Source', 'plethora-framework'),
                      'description' => esc_html__('Select if you want to get all items automatically, or you want a custom selection. Check "Data" tab for more options.', 'plethora-framework'),
                      'value'       => $this->get_datasource_options(),
                  ),                  

                  array(
                      'param_name'  => 'items_display',
                      'type'        => 'dropdown',
                      'heading'     => esc_html__('Items Display Type', 'plethora-framework'),
                      'description' => esc_html__('Select display type', 'plethora-framework'),
                      'value'       => array( 
                                          esc_html__('Grid View', 'plethora-framework')    => 'grid',
                                          esc_html__('Masonry View', 'plethora-framework') => 'masonry',
                                          esc_html__('List View', 'plethora-framework')    => 'list'
                                       )
                  ),                  

                  array(
                      'param_name'  => 'items_template_grid',
                      'type'        => 'dropdown',
                      'heading'     => esc_html__('Grid Template', 'plethora-framework'),
                      'description' => esc_html__('Select grid template to be applied. Check "Items Styling" tab for more options.', 'plethora-framework'),
                      'value'       => $this->get_supported_templates( 'grid' ),
                      'dependency'    => array( 
                                            'element'   => 'items_display', 
                                            'value' => array( 'grid' ),  
                      )
                  ), 
                  array(
                      'param_name'  => 'items_template_masonry',
                      'type'        => 'dropdown',
                      'heading'     => esc_html__('Masonry Template', 'plethora-framework'),
                      'description' => esc_html__('Select masonry template to be applied. Check "Items Styling" tab for more options.', 'plethora-framework'),
                      'value'       => $this->get_supported_templates( 'masonry' ),
                      'dependency'  => array( 
                                            'element'   => 'items_display', 
                                            'value' => array( 'masonry' ),  
                      )
                  ), 
                  array(
                      'param_name'  => 'items_template_list',
                      'type'        => 'dropdown',
                      'heading'     => esc_html__('List Template', 'plethora-framework'),
                      'description' => esc_html__('Select list template to be applied. Check "Items Styling" tab for more options.', 'plethora-framework'),
                      'value'       => $this->get_supported_templates( 'list' ),
                      'dependency'  => array( 
                                            'element'   => 'items_display', 
                                            'value' => array( 'list' ),  
                      )
                  ), 

                  array(
                      'param_name'  => 'filterbar',
                      'type'        => 'checkbox',
                      'heading'     => esc_html__('Filter Bar', 'plethora-framework'),
                      'description' => esc_html__('Enabled bar for filtering displayed items. Check "Filter" tab for more options.', 'plethora-framework'),
                      'value' => array( __( 'Yes', 'plethora-framework' ) => 1 ),
                      'std' => '0',
                  ),                  

                  // array(
                  //     'param_name'  => 'paging',
                  //     'type'        => 'dropdown',
                  //     'heading'     => esc_html__('Paging', 'plethora-framework'),
                  //     'description' => esc_html__('Select paging options', 'plethora-framework'),
                  //     'value'       => array( 
                  //                         esc_html__('Show All', 'plethora-framework')       => 'all',
                  //                         esc_html__('Numbered Pages', 'plethora-framework') => 'numbered_pages',
                  //                         // esc_html__('Auto Lazy Loading', 'plethora-framework')                  => 'lazy',
                  //                         // esc_html__('Lazy Loading With More Button', 'plethora-framework') => 'lazy_button'
                  //                      )
                  // ), 
                  // array(
                  //     'param_name'  => 'paging_perpage',
                  //     'type'        => 'textfield',
                  //     'heading'     => esc_html__('Items Per Page', 'plethora-framework'),
                  //     'description' => esc_html__('Set how many items will be displayed per page', 'plethora-framework'),
                  //     'std'         => '6',
                  //     'dependency'  => array( 
                  //                           'element'   => 'paging', 
                  //                           'value' => array( 'numbered_pages' ),  
                  //     )
                  // ),
                  array(
                      'param_name'  => 'el_class',
                      'type'        => 'textfield',
                      'heading'     => esc_html__('Extra Class', 'plethora-framework'),
                      'description' => esc_html__('Style particular content element differently - add a class name and refer to it in custom CSS.', 'plethora-framework'),
                  ),

                  // DATA TAB STARTS >>>>
                  array(
                      'param_name'  => 'data_tax_include',
                      'type'        => 'autocomplete',
                      'group'       => esc_html__('Data', 'plethora-framework'),                                              
                      'heading'     => sprintf( esc_html__('Filter %1$s By Term', 'plethora-framework'), $this->post_type->label ) ,
                      'description' => sprintf( esc_html__('Filter results by specific %1$s taxonomy term(s). Leave empty to show all.', 'plethora-framework'), $this->post_type->labels->singular_name ) ,
                      'settings' => array(
                                      'multiple'       => true,
                                      'min_length'     => 1,
                                      'groups'         => true,
                                      'display_inline' => true,
                                      'delay'          => 500,
                                      'auto_focus'     => true,
                                      'sortable'       => false,
                                    ),
                      'dependency'    => array( 
                                            'element'   => 'data', 
                                            'value' => array_values( Plethora_Theme::get_supported_post_types() ),  
                      )
                  ),                  

                  array(
                      'param_name'  => 'data_tax_exclude',
                      'type'        => 'autocomplete',
                      'group'       => esc_html__('Data', 'plethora-framework'),                                              
                      'heading'     => sprintf( esc_html__('Exclude %1$s By Term', 'plethora-framework'), $this->post_type->label ) ,
                      'description' => sprintf( esc_html__('Exclude results by specific %1$s taxonomy term(s). Leave empty to show all.', 'plethora-framework'), $this->post_type->labels->singular_name ) ,
                      'settings' => array(
                                      'multiple'       => true,
                                      'min_length'     => 1,
                                      'groups'         => true,
                                      'display_inline' => true,
                                      'delay'          => 500,
                                      'auto_focus'     => true,
                                      'sortable'       => false,
                                    ),
                      'dependency'    => array( 
                                            'element'   => 'data', 
                                            'value' => array_values( Plethora_Theme::get_supported_post_types() ),  
                      )
                  ),                  

                  array(
                      'param_name'  => 'data_posts_selection',
                      'type'        => 'autocomplete',
                      'group'       => esc_html__('Data', 'plethora-framework'),                                              
                      'heading'     => sprintf( esc_html__('%1$s Selection', 'plethora-framework'), $this->post_type->label ) ,
                      'description' => sprintf( esc_html__('Add %1$s by title', 'plethora-framework'), $this->post_type->labels->name ),
                      'settings' => array(
                                      'multiple'       => true,
                                      'min_length'     => 2,
                                      'groups'         => true,
                                      'unique_values'  => true,
                                      'display_inline' => false,
                                      'delay'          => 500,
                                      'auto_focus'     => true,
                                      'sortable'       => true,
                                    ),
                      'dependency'    => array( 
                                            'element' => 'data', 
                                            'value' => array( $this->post_type->name .'_selection' ),  
                      )
                  ),                  

                  array(
                      'param_name'  => 'data_posts_exclude',
                      'type'        => 'autocomplete',
                      'group'       => esc_html__('Data', 'plethora-framework'),                                              
                      'heading'     => sprintf( esc_html__('Exclude Specific %1$s', 'plethora-framework'), $this->post_type->label ) ,
                      'description' => sprintf( esc_html__('Exclude %1$s by title', 'plethora-framework'), $this->post_type->labels->name ),
                      'settings'    => array(
                                      'multiple'       => true,
                                      'min_length'     => 2,
                                      'groups'         => true,
                                      'unique_values'  => true,
                                      'display_inline' => false,
                                      'delay'          => 500,
                                      'auto_focus'     => true,
                                      'sortable'       => false,
                                    ),
                      'dependency'  => array( 
                                        'element'   => 'data', 
                                        'value' => array_values( Plethora_Theme::get_supported_post_types() ),  
                      )
                  ),                  

                  array(
                      'param_name'       => 'data_posts_per_page',
                      'type'             => 'textfield',
                      'group'            => esc_html__('Data', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Items Limit', 'plethora-framework'),
                      'description'      => esc_html__('Set max limit for displayed items or enter -1 to display all.', 'plethora-framework'),
                      'std'              => '12',    
                      'dependency'       => array( 
                                            'element'   => 'data', 
                                            'value' => array_values( Plethora_Theme::get_supported_post_types() ),  
                      )
                  ), 

                  array(
                      'param_name'       => 'data_offset',
                      'type'             => 'textfield',
                      'group'            => esc_html__('Data', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Offset', 'plethora-framework'),
                      'description'      => esc_html__('Number of items to displace or pass over ( ignored if there is no items limit ).', 'plethora-framework'),
                      'admin_label'      => false,    
                      'dependency'       => array( 
                                            'element'   => 'data', 
                                            'value' => array_values( Plethora_Theme::get_supported_post_types() ),  
                      )
                  ), 

                  array(
                      'param_name'       => 'data_orderby',
                      'type'             => 'dropdown',
                      'group'            => esc_html__('Data', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Order By', 'plethora-framework'),
                      'description'      => esc_html__('Select order type. If "Meta value" or "Meta value Number" is chosen then meta key is required.', 'plethora-framework'),
                      'value'            => array( 
                                              esc_html__('Date', 'plethora-framework')                => 'date',
                                              esc_html__('Post ID', 'plethora-framework')             => 'ID',
                                              esc_html__('Author', 'plethora-framework')              => 'author',
                                              esc_html__('Title', 'plethora-framework')               => 'title',
                                              esc_html__('Last Modified Date', 'plethora-framework')  => 'modified',
                                              esc_html__('Post/Page Parent ID', 'plethora-framework') => 'parent',
                                              esc_html__('Comments Number', 'plethora-framework')     => 'comment_count',
                                              esc_html__('Menu/Page Order', 'plethora-framework')     => 'menu_order',
                                              esc_html__('Meta Value', 'plethora-framework')          => 'meta_value',
                                              esc_html__('Meta Value (Number)', 'plethora-framework') => 'meta_value_num',
                                              esc_html__('Random Order', 'plethora-framework')        => 'rand',
                                              esc_html__('Posts Selection Order ( only for custom posts selection )', 'plethora-framework') => 'post__in',
                                            )
                  ), 

                  array(
                      'param_name'       => 'data_order',
                      'type'             => 'dropdown',
                      'group'            => esc_html__('Data', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Sort Order', 'plethora-framework'),
                      'description'      => esc_html__('Select descending/ascending order', 'plethora-framework'),
                      'value'            => array( 
                                          esc_html__('Descending', 'plethora-framework') => 'DESC',
                                          esc_html__('Ascending', 'plethora-framework')  => 'ASC',
                                       )
                  ), 

                  array(
                      'param_name'  => 'data_orderby_metakey',
                      'type'        => 'textfield',
                      'group'       => esc_html__('Data', 'plethora-framework'),                                              
                      'heading'     => esc_html__('Order By: Meta Key', 'plethora-framework'),
                      'description' => esc_html__('Input meta key for items ordering.', 'plethora-framework'),
                      'dependency'  => array( 
                                            'element' => 'data_orderby', 
                                            'value' => array( 'meta_value', 'meta_value_num' ),  
                      )
                  ), 

                  // <<<< DATA TAB ENDS

                  // DISPLAY ITEMS TAB STARTS >>>>
                  array(
                      'param_name'  => 'items_per_row',
                      'type'        => 'dropdown',
                      'group'       => esc_html__('Items Styling', 'plethora-framework'),                                              
                      'heading'     => esc_html__('Items Per Row', 'plethora-framework'),
                      'description' => esc_html__('Select number of single items ( columns ) per row.', 'plethora-framework'),
                      'std'         => 'col-md-4 col-sm-6',
                      'value'       => array( 
                                              esc_html__('6 items', 'plethora-framework') => 'col-md-2 col-sm-4',
                                              esc_html__('4 items', 'plethora-framework') => 'col-md-3 col-sm-4',
                                              esc_html__('3 items', 'plethora-framework') => 'col-md-4 col-sm-6',
                                              esc_html__('2 items', 'plethora-framework') => 'col-md-6 col-sm-6',
                                              esc_html__('1 items', 'plethora-framework') => 'col-md-12',
                                            ),
                      'dependency'   => array( 
                                            'element' => 'items_display', 
                                            'value'   => array( 'grid', 'masonry' ),  
                      )
                  ), 
                  array(
                      'param_name'       => 'items_featuredmedia',
                      'type'             => 'checkbox',
                      'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Display Featured Image/Media', 'plethora-framework'),
                      'description'      => esc_html__('Will display featured image or any other Plethora featured media set for each post', 'plethora-framework'),
                      'std'              => '1',
                      'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
                  ),
                  array(
                      'param_name'       => 'items_media_ratio',
                      'type'             => 'dropdown',
                      'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Featured image ratio', 'plethora-framework'),
                      'description'      => esc_html__('Choose a ratio for all images on the grid', 'plethora-framework'),
                      'std'              => '',
                      "value"            => Plethora_Module_Style::get_options_array( array( 
                                        'type' => 'stretchy_ratios', 
                                        'use_in' => 'vc' 
                                        )),
                      'dependency'       => array( 
                                            'element'   => 'items_display', 
                                            'value' => array( 'grid', 'list' ),
                      )                          
                  ),
                  array(
                      'param_name'       => 'items_hover_transparency',
                      'type'             => 'dropdown',
                      'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Items Hover Transparency', 'plethora-framework'),
                      'description'      => esc_html__('Choose the transparency style on hover, if supported in selected template.', 'plethora-framework'),
                      'std'              => '',
                      'value'            => array( 
                                              esc_html__('No Transparency', 'plethora-framework')   => '',
                                              esc_html__('Transparent Film', 'plethora-framework')  => 'transparent_film',
                                              esc_html__('Fully Transparent', 'plethora-framework') => 'transparent',
                                            ),
                      'dependency'       => array( 
                                            'element'   => 'items_display', 
                                            'value' => array( 'grid', 'masonry' ),
                      )                          
                  ),                 
                  array(
                      'param_name'       => 'items_title',
                      'type'             => 'checkbox',
                      'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Display Title', 'plethora-framework'),
                      'description'      => esc_html__('Will display post title', 'plethora-framework'),
                      'std'              => '1',
                      'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
                  ),                  
                  array(
                      'param_name'       => 'items_subtitle',
                      'type'             => 'checkbox',
                      'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Display Subtitle', 'plethora-framework'),
                      'description'      => esc_html__('Will display post subtitle, if supported in selected template.', 'plethora-framework'),
                      'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
                  ),                  
                  array(
                      'param_name'       => 'items_excerpt',
                      'type'             => 'checkbox',
                      'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Display Excerpt', 'plethora-framework'),
                      'description'      => esc_html__('Will display post excerpt, if supported in selected template.', 'plethora-framework'),
                      'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
                  ),                  
                  array(
                      'param_name'       => 'items_excerpt_trim',
                      'type'             => 'textfield',
                      'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Trim Excerpt', 'plethora-framework'),
                      'description'      => esc_html__('Trims excerpt text to a certain number of words ( default is 55, even if left empty or 0 ) ', 'plethora-framework'),
                      'std'              => '15',    
                      'dependency'       => array( 
                                            'element'   => 'items_excerpt', 
                                            'value' => array( '1' ),  
                      )
                  ),                  
                  array(
                      'param_name'       => 'items_date',
                      'type'             => 'checkbox',
                      'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Display Date', 'plethora-framework'),
                      'description'      => esc_html__('Will display post creation date, if supported in selected template.', 'plethora-framework'),
                      'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
                  ),                  
                  array(
                      'param_name'       => 'items_author',
                      'type'             => 'checkbox',
                      'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Display Author', 'plethora-framework'),
                      'description'      => esc_html__('Will display post author, if supported in selected template.', 'plethora-framework'),
                      'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
                  ),                  
                  array(
                      'param_name'       => 'items_commentscount',
                      'type'             => 'checkbox',
                      'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Display Comments Count', 'plethora-framework'),
                      'description'      => esc_html__('Will display post comments number, if supported in selected template.', 'plethora-framework'),
                      'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
                  ),                  
                  array(
                      'param_name'       => 'items_primarytax',
                      'type'             => 'checkbox',
                      'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Display Primary Taxonomy Terms', 'plethora-framework'),
                      'description'      => esc_html__('Will display primary taxonomy terms, if supported in selected template. Enable this to set primary taxonomy.', 'plethora-framework'),
                      'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
                  ),                  
                  array(
                      'param_name'       => 'items_primarytax_slug',
                      'type'             => 'dropdown',
                      'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Primary Taxonomy', 'plethora-framework'),
                      'description'      => esc_html__('Select primary taxonomy to be displayed', 'plethora-framework'),
                      'value'            => $this->get_supported_taxonomies(),
                      'std'              => 'category',
                      'dependency'       => array( 
                                            'element'   => 'items_primarytax', 
                                            'value' => array( '1' ),  
                      )
                  ),                  
                  array(
                      'param_name'       => 'items_secondarytax',
                      'type'             => 'checkbox',
                      'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Display Secondary Taxonomy Terms', 'plethora-framework'),
                      'description'      => esc_html__('Will display secondary taxonomy terms, if supported in selected template. Enable this to set secondary taxonomy.', 'plethora-framework'),
                      'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
                  ),                  
                  array(
                      'param_name'       => 'items_secondarytax_slug',
                      'type'             => 'dropdown',
                      'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Secondary Taxonomy', 'plethora-framework'),
                      'description'      => esc_html__('Select secondary taxonomy to be displayed', 'plethora-framework'),
                      'value'            => $this->get_supported_taxonomies(),
                      'std'              => 'post_tag',
                      'dependency'       => array( 
                                            'element'   => 'items_secondarytax', 
                                            'value' => array( '1' ),  
                      )
                  ),                  
                  array(
                      'param_name'       => 'items_woo_price',
                      'type'             => 'checkbox',
                      'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Display Price ( Woo Only )', 'plethora-framework'),
                      'description'      => esc_html__('Will display price, if supported in selected template.', 'plethora-framework'),
                      'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
                      'dependency'       => array( 
                                            'element'   => 'data', 
                                            'value' => array( 'product', 'product_selection' ),  
                      )
                  ),                  
                  array(
                      'param_name'       => 'items_woo_addtocart',
                      'type'             => 'checkbox',
                      'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Display Cart Button ( Woo Only )', 'plethora-framework'),
                      'description'      => esc_html__('Will display "Add To Cart" button, if supported in selected template.', 'plethora-framework'),
                      'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
                      'dependency'       => array( 
                                            'element'   => 'data', 
                                            'value' => array( 'product', 'product_selection' ),  
                      )
                  ),                  
                  array(
                      'param_name'       => 'items_woo_saleicon',
                      'type'             => 'checkbox',
                      'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Display Sale Icon ( Woo Only )', 'plethora-framework'),
                      'description'      => esc_html__('Will display sale icon, if supported in selected template.', 'plethora-framework'),
                      'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
                      'dependency'       => array( 
                                            'element'   => 'data', 
                                            'value' => array( 'product', 'product_selection' ),  
                      )
                  ),

                  array(
                      'param_name'       => 'items_socials',
                      'type'             => 'checkbox',
                      'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      "heading"          => esc_html__("Display Social Icons ", 'plethora-framework'),
                      'value'            => array( __( 'Yes', 'plethora-framework' ) => '1' ),
                      "description"      => esc_html__("Will display related social icons for each profile post, if supported in selected template.", 'plethora-framework'),
                      'dependency'       => array( 
                                            'element'   => 'data', 
                                            'value' => array( 'profile', 'profile_selection' ),  
                      )
                  ),

                  array(
                      'param_name'       => 'items_extraclass',
                      'type'             => 'textfield',
                      'group'            => esc_html__('Items Styling', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Item Extra Class', 'plethora-framework'),
                      'description'      => esc_html__('Add an special item class name and refer to it in custom CSS.', 'plethora-framework'),
                  ),
                  // <<<< DISPLAY ITEMS TAB ENDS

                  // FILTER BAR TAB STARTS >>>>
                  array(
                      'param_name'       => 'filterbar_tax',
                      'type'             => 'dropdown',
                      'group'            => esc_html__('Filter Bar', 'plethora-framework'),                                              
                      'heading'          => esc_html__('Term Filters By Taxonomy', 'plethora-framework'),
                      'description'      => esc_html__('Select category, tags or custom taxonomy. The selected taxonomy terms will be displayed as selections in the filters bar. Note that the terms should be associating with the displayed post type data source.', 'plethora-framework'),
                      'value'            => $this->get_supported_taxonomies(),
                      'std'              => 'category',
                      'dependency'       => array( 
                                            'element'   => 'filterbar', 
                                            'value' => array( '1' ),  
                      )
                  ),                  
                  array(
                      'param_name'  => 'filterbar_tax_exclude',
                      'type'        => 'autocomplete',
                      'group'       => esc_html__('Filter Bar', 'plethora-framework'),                                              
                      'heading'     => esc_html__('Exclude Terms From Filters', 'plethora-framework'),
                      'description' => esc_html__('Exclude specific categories, tags or custom terms from filter bar.', 'plethora-framework'),
                      'settings'    => array(
                                      'multiple'       => true,
                                      'min_length'     => 1,
                                      'groups'         => true,
                                      'display_inline' => true,
                                      'delay'          => 500,
                                      'auto_focus'     => true,
                                      'sortable'       => false,
                                    ),
                      'dependency'  => array( 
                                            'element'   => 'filterbar', 
                                            'value' => array( '1' ),  
                      )
                  ),                  

                  array(
                      'param_name'       => 'filterbar_orderby',
                      'type'             => 'dropdown',
                      'group'            => esc_html__('Filter Bar', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Order Filters By', 'plethora-framework'),
                      'description'      => esc_html__('Select filter order type.', 'plethora-framework'),
                      'value'            => array( 
                                              esc_html__('Name', 'plethora-framework')                                       => 'name',
                                              esc_html__('Description', 'plethora-framework')                                => 'description',
                                              esc_html__('Count ( how many posts include this term )', 'plethora-framework') => 'count',
                                              esc_html__('Term ID', 'plethora-framework')                                    => 'term_id',
                                            ),
                      'dependency'  => array( 
                                            'element'   => 'filterbar', 
                                            'value' => array( '1' ),  
                      )
                  ), 

                  array(
                      'param_name'       => 'filterbar_order',
                      'type'             => 'dropdown',
                      'group'            => esc_html__('Filter Bar', 'plethora-framework'),                                              
                      'edit_field_class' => 'vc_col-sm-6 vc_column',
                      'heading'          => esc_html__('Sort Filters Order', 'plethora-framework'),
                      'description'      => esc_html__('Select descending/ascending order', 'plethora-framework'),
                      'value'            => array( 
                                                esc_html__('Descending', 'plethora-framework') => 'DESC',
                                                esc_html__('Ascending', 'plethora-framework')  => 'ASC',
                                            ),
                      'dependency'        => array( 
                                            'element'   => 'filterbar', 
                                            'value' => array( '1' ),  
                      )
                  ), 

                  array(
                      'param_name'  => 'filterbar_resettitle',
                      'type'        => 'textfield',
                      'group'       => esc_html__('Filter Bar', 'plethora-framework'),                                              
                      'heading'     => esc_html__('Reset Button Title', 'plethora-framework'),
                      'description' => esc_html__('Set title for the reset button. If empty, button will not be displayed.', 'plethora-framework'),
                      'std'         => esc_html__('Show All', 'plethora-framework'),  
                      'dependency'  => array( 
                                            'element'   => 'filterbar', 
                                            'value' => array( '1' ),  
                      )
                  ),
                  // <<<< FILTER BAR TAB ENDS

                  // DESIGN OPTIONS TAB STARTS >>>>
                  array(
                      'param_name'  => 'css',
                      'type'        => 'css_editor',
                      'group'       => esc_html__('Design Options', 'plethora-framework'),                                              
                      'heading'     => esc_html__('Design Options', 'plethora-framework'),
                  ),
                  // <<<< DESIGN OPTIONS TAB ENDS
          );
          return $params;
       }

       /** 
       * Returns data source option values
       * @return array
       */
       public function get_datasource_options() {

          $values                      = array();
          $all_posts_label             = sprintf( esc_html__( 'All %1$s', 'plethora-framework' ), $this->post_type->label );  
          $custom_posts_label          = sprintf( esc_html__( '%1$s selection', 'plethora-framework' ), $this->post_type->label );  
          $values[$all_posts_label]    = $this->post_type->name;
          $values[$custom_posts_label] = $this->post_type->name .'_selection';
          return $values;
       }

       /** 
       * Returns supported taxonomies option value
       * @return array
       */
       public function get_supported_taxonomies() {

          $values = array();
          $taxonomies = get_object_taxonomies( $this->post_type->name, 'objects' );
          foreach ( $taxonomies as $tax_slug => $tax_obj ) {

            $values[$tax_obj->label .' ( '.$this->post_type->label .' )'] = $tax_slug;
          }
          $values = !empty( $values ) ? array_unique($values) : array();
          return $values;
       }

       /** 
       * Returns supported templates option value, according to template parts found on child/parent theme
       * @return array
       */
       public function get_supported_templates( $type = 'grid' ) {

        $templates                   = $this->locate_templates( $type );
        $no_selection_label          = esc_html__('No template selected', 'plethora-framework');
        $values[$no_selection_label] = '';
        foreach ( $templates as $label => $value ) {

          $values[$label] = $value;
        }

        $values = !empty( $values ) ? array_unique($values) : array();
        return $values;
       }

       /** 
       * Scan installation for files following the naming pattern: shortcode-postsgrid-{type}-{style}.php
       * @return array
       */
       public function locate_templates( $type = 'grid' ) {

          // if child exists, check its templates folder first
          $child_templates_dir = PLE_CHILD_TEMPLATES_DIR . '/shortcodes';
          $child_scandir = array();
          if ( is_child_theme() && file_exists( $child_templates_dir ) ) {

            $child_scandir = scandir( $child_templates_dir );
            $child_scandir = $child_scandir !== false ? $child_scandir : array();
          }

          // check parent templates now...
          $parent_templates_dir = PLE_THEME_TEMPLATES_DIR . '/shortcodes';
          $parent_scandir = array();
          if ( file_exists( $parent_templates_dir ) ) {

            $parent_scandir = scandir( $parent_templates_dir );
            $parent_scandir = $parent_scandir !== false ? $parent_scandir : array();
          }

          // merge both dirs
          $all_templates = array_merge( $parent_scandir, $child_scandir );

          // We got the full contents of both dirs. Now we filter the results
          $locate_templates = array();
          foreach ( $all_templates as $key => $template_file ) {

            if ( is_file( $parent_templates_dir .'/'. $template_file ) || is_file( $child_templates_dir .'/'. $template_file ) ) {

              $template_file = basename( $template_file );

              // get global templates ( will appear in all loop shortcodes )
              $global_templates_prefix = 'loop_' . $type .'-anypost-';
              $string_length = strlen( $global_templates_prefix );
              if ( substr( $template_file, 0, $string_length) === $global_templates_prefix ) {

                $locate_template = str_replace( $global_templates_prefix, '', $template_file );
                $locate_template = rtrim($locate_template, '.php');
                $template_label = str_replace('-', ' ', $locate_template);
                $locate_templates[ucwords($template_label)] = 'anypost-'. $locate_template;
              }

              // get post templates ( will appear only on selected loop shortcode )
              $posttype_templates_pattern = 'loop_' . $type .'-'. $this->post_type->name .'-';
              $string_length = strlen( $posttype_templates_pattern );
              if ( substr( $template_file, 0, $string_length) === $posttype_templates_pattern ) {

                $locate_template = str_replace( $posttype_templates_pattern, '', $template_file );
                $locate_template = rtrim($locate_template, '.php');
                $template_label = str_replace('-', ' ', $locate_template);
                $locate_templates[ucwords($template_label)] = $this->post_type->name .'-'. $locate_template;
              }
            }
          }
          return $locate_templates;

       }

       /** 
       * Searches and returns posts by user value ( 'Posts Selection' / 'Exclude Posts' fields )
       * Called using VC's autocomplete filter: vc_autocomplete_[shortcode_name]_[param_name]_callback
       * @return array
       */
       public function search_data_posts( $user_val ) {

          $values = array();
          $args = array(
                    'posts_per_page'   => -1,
                    'orderby'          => 'title',
                    'order'            => 'ASC',
                    'post_type'        => $this->post_type->name,
                  );
          $posts = get_posts( $args );
          foreach ( $posts as $post ) {

            // note: stripos MUST be checked against false with the identical comparison operator !==
            if ( stripos( $post->post_title, $user_val ) !== false || stripos( $post->post_name, $user_val ) !== false || stripos( $posttype->label, $user_val ) !== false || stripos( $posttype->name, $user_val ) !== false ) {
              $values[] = array(
                          'label' => $post->post_title, 
                          'value' => $post->ID, 
                          'group' => $post->post_type,
              );
            }
          }
          wp_reset_postdata();
          return $values;
       }

       /** 
       * Renders saved posts selection ( 'Posts Selection' / 'Exclude Posts' fields )
       * Called using VC's autocomplete filter: vc_autocomplete_[shortcode_name]_[param_name]_render
       * @return mixed ( array, bool )
       */
       public function render_data_posts( $value ) {
        
        $post = get_post( $value['value'] );
        return is_null( $post ) ? false : array(
          'label' => $post->post_title,
          'value' => $post->ID,
          'group' => $post->post_type,
        );
       }

       /** 
       * Returns taxonomy terms by user value ( 'Narrow Results By Term' field )
       * Called using VC's autocomplete filter: vc_autocomplete_[shortcode_name]_[param_name]_callback
       * @return array
       */
       public function search_data_tax( $user_val ) {

          $values = array();
          $post_taxonomies = get_object_taxonomies( $this->post_type->name, 'objects' );
          foreach ( $post_taxonomies as $tax_slug => $tax_obj ) {

            $post_taxonomy_terms = get_terms( $tax_slug );
            if ( ! is_wp_error( $post_taxonomy_terms ) ) {

              foreach ( $post_taxonomy_terms as $term  ) {
                // note: stripos MUST be checked against false with the identical comparison operator !==
                if ( stripos( $term->name, $user_val ) !== false || stripos( $term->slug, $user_val ) !== false || stripos( $tax_obj->label, $user_val ) !== false ) {
                  $values[] = array(
                              'label' => $term->name, 
                              'value' => $tax_slug .'|'. $term->term_id, 
                              'group' => $tax_obj->label
                  );
                }
              }
            }
          }
          return $values;
       }

       /** 
       * Renders saved taxonomy terms ( 'Narrow Results By Term' field )
       * Called using VC's autocomplete filter: vc_autocomplete_[shortcode_name]_[param_name]_render
       * @return mixed ( array, bool )
       */
      public function render_data_tax( $saved_terms ) {
        
          $value = false;
          $post_taxonomies = get_object_taxonomies( $this->post_type->name, 'objects' );
          foreach ( $post_taxonomies as $tax_slug => $tax_obj ) {

            $saved_value = !empty( $saved_terms['value'] ) ? explode('|', $saved_terms['value'] ) : array();
            $saved_value = !empty( $saved_value[1] ) ? $saved_value[1] : array() ;
            $terms_args = array(
                            'include'    => $saved_value,
                            'hide_empty' => false,
                          );
            $post_taxonomy_terms = get_terms( $tax_slug, $terms_args );
            if ( is_array( $post_taxonomy_terms ) && 1 === count( $post_taxonomy_terms ) ) {

              $term   = $post_taxonomy_terms[0];
              $value = array(
                          'label' => $term->name, 
                          'value' => $tax_slug .'|'. $term->term_id, 
                          'group' => $tax_obj->label
              );
            }
          }
          return $value;
      }


 
       /** 
       * Returns shortcode content
       * @return array
       */
       public function content( $atts, $content = null ) {

          // EXTRACT USER INPUT
          $atts = shortcode_atts( array(
            'data'                    => $this->post_type->name,
            'data_tax_include'        => '',
            'data_tax_exclude'        => '',
            'data_posts_selection'    => '',
            'data_posts_exclude'      => '',
            'data_posts_per_page'     => '12',
            'data_offset'             => '',
            'data_orderby'            => 'date',
            'data_orderby_metakey'    => '',
            'data_order'              => 'DESC',
            'items_display'           => 'grid',
            'items_template_grid'     => '',
            'items_template_masonry'  => '',
            'items_template_list'     => '',
            'items_per_row'           => 'col-md-4 col-sm-6',
            'items_featuredmedia'     => '1',
            'items_media_ratio'       => 'stretchy_wrapper ratio_16-9',
            'items_hover_transparency' => '',
            'items_title'             => '1',
            'items_subtitle'          => '0',
            'items_excerpt'           => '0',
            'items_excerpt_trim'      => '15',
            'items_date'              => '0',
            'items_author'            => '0',
            'items_commentscount'     => '0',
            'items_primarytax'        => '1',
            'items_primarytax_slug'   => 'category',
            'items_secondarytax'      => '0',
            'items_secondarytax_slug' => 'post_tag',
            'items_woo_price'         => '0',
            'items_woo_addtocart'     => '0',
            'items_woo_saleicon'      => '0',
            'items_socials'           => '0',
            'items_extraclass'        => '',
            'filterbar'               => '0',
            'filterbar_tax'           => 'category',
            'filterbar_tax_exclude'   => '',
            'filterbar_orderby'       => 'title',
            'filterbar_order'         => 'ASC',
            'filterbar_resettitle'    => esc_html__('Show All', 'plethora-framework'),
            'paging'                  => 'all',
            'pagperpageing_'          => '6',
            'css'                     => '',
            'el_class'                => '',
          ), $atts );

          extract( $atts );
          $return = '';

          // VC CSS WRAPPER START >>>
          $css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), $this->wp_slug, $atts );
          $return = '<div class="ple_anypostloop_shortcode wpb_content_element '. esc_attr( $el_class ) .' '. esc_attr( $css_class ) .'">';
          // Render filter bar template
          if ( $filterbar ) {

            $filterbar_atts = $this->prepare_atts_filterbar( $atts );
            $return  .= Plethora_WP::renderMustache( array( "data" => $filterbar_atts, "force_template_part" => array( 'templates/shortcodes/loop__filterbar' ) ) );
          }

          $main_atts = $this->prepare_atts( $atts );

          // Render main grid/list/masonry template
          switch ($items_display) {
            case 'grid':
            default:
              $template = $items_template_grid;
              break;
            case 'masonry':
              $template = $items_template_masonry;
              break;
            case 'list':
              $template = $items_template_list;
              break;
          }

         $return  .= !empty( $template ) ? Plethora_WP::renderMustache( array( "data" => $main_atts, "force_template_part" => array( 'templates/shortcodes/loop_'. $items_display .'', $template ) ) ) : '';

          // Render paging template
          if ( $paging !== 'all' ) {

            $paging_atts = $this->prepare_atts_paging( $atts );
            $return  .= Plethora_WP::renderMustache( array( "data" => $paging_atts, "force_template_part" => array( 'templates/shortcodes/loop__paging', $paging ) ) );
          }

          // VC CSS WRAPPER ENDS >>>
          $return  .= '</div>';
        return $return;
     
       }

       /** 
       * Prepares attributes for the main template file
       * @return array
       */
       public function prepare_atts( $atts ) {

        // Set item_col_cl
        $return['items_col_class'] = '';

        $return = array_merge( $return, $this->prepare_atts_items( $atts ) );
        return $return;
       }

       /** 
       * Prepares item attributes and paging for the main template file
       * @return array
       */
       public function prepare_atts_items( $atts ) {

          extract( $atts );

          // Build WP Query Arguments
          $args = array();

          if ( $data !== $this->post_type->name .'_selection' ) { // if not a custom selection

            $args['post_type'] = $data;
            $args['post__not_in']  = !empty( $data_posts_exclude ) ? explode(',', $data_posts_exclude ) : array();
            $args['tax_query'] = array( 'relation' => 'OR' );
            
            $data_tax_include = !empty( $data_tax_include ) ? explode(',', $data_tax_include ) : array();
            foreach ( $data_tax_include as $tax_term ) { // include posts with terms
            
              $tax_term = explode('|', $tax_term );
              $args['tax_query'][] = array( 'taxonomy' => $tax_term[0], 'field' => 'term_id', 'terms' => array( $tax_term[1] ) );
            }

            $data_tax_exclude = !empty( $data_tax_exclude ) ? explode(',', $data_tax_exclude ) : array();
            foreach ( $data_tax_exclude as $tax_term ) { // exclude posts with terms
            
              $tax_term = explode('|', $tax_term );
              $args['tax_query'][] = array( 'taxonomy' => $tax_term[0], 'field' => 'term_id', 'terms' => array( $tax_term[1] ), 'operator' => 'NOT IN' );
            }

          } else { // if custom selection

            $args['post_type'] = $this->post_type->name;
            $args['post__in']  = !empty( $data_posts_selection ) ? explode(',', $data_posts_selection ) : array();
          }

          $args['posts_per_page']      = $data_posts_per_page;
          $args['offset']              = $data_offset;
          $args['order']               = $data_order;
          $args['orderby']             = $data_orderby;
          $args['paged']               = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1 ;
          $args['ignore_sticky_posts'] = 1;
          if ( $data_orderby === 'meta_value' || $data_orderby === 'meta_value_num' ) {

            $args['meta_key'] = $data_orderby_metakey;
          }

          // Set column classes to items to create the grid ?
          if ( in_array( $atts['items_display'], array( 'grid', 'masonry') ) ) {

            $items_class = $atts['items_per_row'] ;
            
          }

          // Query args are ready...now we get the posts
          $items = array();
          $count = 1;
          $post_query = new WP_Query($args);

          if ( $post_query->have_posts() ) {

            while ( $post_query->have_posts() ) : $post_query->the_post();

              $item                             = array();
              $item_id                          = get_the_id();
              $item['item_count']               = $count++;
              $item['item_id']                  = $item_id;
              $item['item_attr_class']          = $items_class;
              $item['item_attr_extraclass']     = ' '. $items_extraclass;
              $item['items_media_ratio']        = $items_media_ratio;
              $item['items_hover_transparency'] = $items_hover_transparency;
              $item['item_post_type']           = get_post_type();
              $item['item_link']                = get_permalink( $item_id );
              $item['item_media']               = $items_featuredmedia ? Plethora_Theme::get_post_media( array( 'id' => $item_id, 'return' => 'url' ) ) : '';
              $item['item_title']               = $items_title ? get_the_title() : '';
              $item['item_subtitle']            = $items_subtitle ? Plethora_Theme::get_subtitle( array( 'id' => $item_id, 'listing' => true, 'force_display' => true, 'tag' => '' ) ) : '';
              $item['item_date_day_num']        = $items_date ? get_the_date( 'd' ) : '';
              $item['item_date_day_txt']        = $items_date ? get_the_date( 'D' ) : '';
              $item['item_date_month_num']      = $items_date ? get_the_date( 'm' ) : '';
              $item['item_date_month_txt']      = $items_date ? get_the_date( 'M' ) : '';
              $item['item_date_year_abr']       = $items_date ? get_the_date( 'y' ) : '';
              $item['item_date_year_full']      = $items_date ? get_the_date( 'Y' ) : '';
              $item['item_author_name']         = $items_author ? get_the_author() : '';
              $item['item_comments_number']     = $items_commentscount && get_comments_number() > 0 ? get_comments_number() : '';
              $item['item_comments_link']       = $items_commentscount && get_comments_number() > 0 ? get_comments_link() : '';
              $item['item_author_link']         = $items_author ? get_author_posts_url( get_the_author_meta( 'ID' ) ) : '';
              $item['item_excerpt']             = $items_excerpt ? wp_trim_words( get_the_excerpt(), $items_excerpt_trim, null ) : '';
              // primary tax terms
              $item['item_primarytax_terms'] = array();
              if ( $items_primarytax && !empty( $items_primarytax_slug ) ) {

                 $primarytax_terms = wp_get_post_terms( $item_id, $items_primarytax_slug );
                 $primarytax_terms = !is_wp_error( $primarytax_terms ) ? $primarytax_terms : array();
                 foreach ( $primarytax_terms as $term ) {

                    $item['item_primarytax_terms'][] = array( 
                                                      'term_id'       => $term->term_id,
                                                      'term_slug'     => $term->slug,
                                                      'term_link'     => get_term_link( $term->term_id ),
                                                      'term_name'     => $term->name,
                                                      'term_colorset' => esc_attr( get_term_meta( $term->term_id, TERMSMETA_PREFIX . $items_primarytax_slug .'-colorset', true ) ) ,
                                                     );
                 }
              }
              // secondary tax terms
              $item['item_secondarytax_terms'] = array();
              if ( $items_secondarytax && !empty( $items_secondarytax_slug ) ) {

                 $secondarytax_terms = wp_get_post_terms( $item_id, $items_secondarytax_slug, array( 'fields' => 'names' ) );
                 $secondarytax_terms = !is_wp_error( $secondarytax_terms ) ? $secondarytax_terms : array();
                 foreach ( $secondarytax_terms as $term ) {

                    $item['item_secondarytax_terms'][] = array( 
                                                      'term_id'       => $term->term_id,
                                                      'term_slug'     => $term->slug,
                                                      'term_link'     => get_term_link( $term->term_id ),
                                                      'term_name'     => $term->name,
                                                      'term_colorset' => esc_attr( get_term_meta( $term->term_id, TERMSMETA_PREFIX . $items_secondarytax_slug .'-colorset', true ) ) ,
                                                     );
                 }
              }

              // special woo fields
              $item['item_woo_price']          = '';
              $item['item_woo_price_currency'] = '';
              $item['item_woo_addtocart_url']  = '';
              $item['item_woo_addtocart_text'] = '';
              $item['item_woo_saleicon_class'] = '';
              $item['item_woo_saleicon_text']  = '';
              if ( class_exists( 'woocommerce' ) && $item['item_post_type'] === 'product' ) { 

                $product                         = wc_get_product( $item_id );
                $item['item_woo_price']          = $items_woo_price ? $product->get_price() : '';
                $item['item_woo_price_currency'] = $items_woo_price ? get_woocommerce_currency_symbol() : '';
                $item['item_woo_addtocart_url']  = $items_woo_addtocart ? $product->add_to_cart_url() : '';
                $item['item_woo_addtocart_text'] = $items_woo_addtocart ? $product->add_to_cart_text() : '';
                $item['item_woo_saleicon_class'] = $items_woo_saleicon && $product->is_on_sale() ? 'onsale' : '';
                $item['item_woo_saleicon_text']  = $items_woo_saleicon && $product->is_on_sale() ? __( 'Sale!', 'woocommerce' ) : '';
              }

              // special social icon fields
              $item['item_socials'] = array();
              if ( $items_socials ) {

                $socials = Plethora_Theme::option( METAOPTION_PREFIX .''.$this->post_type->name.'-social', array(), $item_id );
                if ( !empty( $socials['social_url'] ) ) {

                  foreach ( $socials['social_url'] as $key => $value) {

                    if ( $value != "" ){

                      $item['item_socials'][] = array( 
                                                'social_title'      =>  $socials["social_title"][$key],
                                                'social_url'        =>  $socials["social_url"][$key],
                                                'social_icon'       =>  $socials["social_icon"][$key],
                                                'social_url_target' =>  '_blank',
                                               );
                    }
                  }
                }
              }

              // add it to $items
              $items[] = $item;

            endwhile;
          }
          $return['items']  = $items;

          // paging
          $return['paging_previous_post_link'] = '';
          $return['paging_previous_post_text'] = '';
          $return['paging_pages']              = array();
          $return['paging_next_post_link']     = '';
          $return['paging_next_post_text']     = '';
          $output                              = '';
          $pages                               = '';
          $range                               = 5;
          $showitems                           = ($range * 2)+1;  
          
          $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1 ;
          if ( empty( $paged ) ) { $paged = 1; }
          if ( $pages == '' ) {

               $pages = $post_query->max_num_pages;
               if ( !$pages ) {

                   $pages = 1;
               }
           }   

          if ( $pages != 1 ) {
            
            $return['paging_previous_post_link']  = get_previous_posts_link( esc_html__('Prev', 'plethora-framework') );
            $return['paging_previous_post_text']  = esc_html__('Prev', 'plethora-framework');
            if ( $paged > 2 && $paged > $range+1 && $showitems < $pages ) { 

              $return['paging_pages'][] = array( 'number' => 1, 'link' => get_pagenum_link(1), 'text' => '&laquo;', 'active_class' => '' );

            }

            if ( $paged > 1 && $showitems < $pages ) { 

              $return['paging_pages'][] = array( 'number' => $paged - 1, 'link' => get_pagenum_link($paged - 1), 'text' => '&lsaquo;', 'active_class' => '' );
            }

            for ( $i=1; $i <= $pages; $i++ ) { 

              if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )) {

              $active_class             = ( $paged == $i ) ? 'active' : '';
              $return['paging_pages'][] = array( 'number' => $i, 'link' => get_pagenum_link($i), 'text' => $i, 'active_class' => $active_class );
              }
            }

            if ($paged < $pages && $showitems < $pages) {

              $return['paging_pages'][] = array( 'number' => $paged + 1, 'link' => get_pagenum_link($paged + 1), 'text' => '&rsaquo;', 'active_class' => '' );
            }

            if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) {

              $return['paging_pages'][] = array( 'number' => $pages, 'link' => get_pagenum_link($pages), 'text' => '&raquo;', 'active_class' => '' );
            }

            $return['paging_next_post_link']     = get_next_posts_link( esc_html__('Next', 'healthflex'),$post_query->max_num_pages );
            $return['paging_next_post_text'] = esc_html__('Next', 'healthflex');
          }
          
          wp_reset_postdata();    

        return $return;

       }

       /** 
       * Prepares attributes for the filterbar template file
       * @return array
       */
       public function prepare_atts_filterbar( $atts ) {

        $exclude_term_ids = explode('|', $atts['filterbar_tax_exclude'] );
        $exclude_term_ids = !empty( $exclude_term_ids[1] ) ? $exclude_term_ids[1] : '' ;

        $args = array(
            'orderby' => 'name',
            'order'   => 'ASC',
            'exclude' => !empty( $exclude_term_ids ) ? explode(',', $exclude_term_ids ) : array(),
          );
            
        $taxonomy_terms = get_terms( $atts['filterbar_tax'], $args );
        $filters = array();
        foreach ( $taxonomy_terms as $term_obj ) {

          $filters[] = array(
            'id'          => $term_obj->term_id,
            'slug'        => $term_obj->slug,
            'name'        => $term_obj->name,
            'taxonomy'    => $term_obj->taxonomy,
            'description' => $term_obj->description,
            'count'       => $term_obj->count,
          );
        }

        $return = array(
            'filters_tax' => $atts['filterbar_tax'],
            'resettitle' => $atts['filterbar_resettitle'],
            'filters' => $filters,
        );

        return $return;
       }

       /** 
       * Prepares attributes for the paging template file
       * @return array
       */
       public function prepare_atts_paging( $atts ) {


       }
  }
 endif;