<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2013

File Description: Slider Post Type Feature Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Posttype') && !class_exists('Plethora_Posttype_Slider') ) {  
  /**
   * @package Plethora Framework
   */

  class Plethora_Posttype_Slider {

        
        public static $feature_title         = "Slider Post Type";                               // Feature display title  (string)
        public static $feature_description   = "Contains all slider related post configuration"; // Feature display description (string)
        public static $theme_option_control  = true;                                             // Will this feature be controlled in theme options panel ( boolean )
        public static $theme_option_default  = true;                                             // Default activation option status ( boolean )
        public static $theme_option_requires = array();                                          // Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
        public static $dynamic_construct     = true;                                             // Dynamic class construction ? ( boolean )
        public static $dynamic_method        = false;                                            // Additional method invocation ( string/boolean | method name or false )

        private $post_type_slug = 'slider';

        public function __construct() {

          // Create basic post type object
          $posttype = $this->create();

          // Single slider Metabox    
          add_filter( 'plethora_metabox_add', array($this, 'single_metabox'));   

          // Scripts/styles for post/post-new pages
          add_action('admin_print_styles-post.php'    , array( $this, 'admin_post_print_css')); 
          add_action('admin_print_styles-post-new.php', array( $this, 'admin_post_print_css')); 

        }

        public function create() {

            // Names
            $names = array(

              'post_type_name'  =>  $this->post_type_slug, 
              'slug'            =>  $this->post_type_slug, 
              'menu_item_name'  =>  esc_html__('Sliders', 'plethora-framework'),
              'singular'        =>  esc_html__('Slider', 'plethora-framework'),
              'plural'          =>  esc_html__('Sliders', 'plethora-framework'),

            );

            // Options
            $options = array(

              'enter_title_here' => 'Slider reference title', 
              'description'         => '',   
              'public'              => false,    
              'exclude_from_search' => true,    
              'publicly_queryable'  => false,    
              'show_ui'             => true,    
              'show_in_nav_menus'   => false,    
              'show_in_menu'        => true,    
              'show_in_admin_bar'   => true,    
              'menu_position'       => 5,       
              'menu_icon'           => 'dashicons-slides',
              'hierarchical'        => false,    
              'supports'        => array( 
                                'title', 
                               ), 
            );    

            $names    = apply_filters( 'plethora_posttype_'. $this->post_type_slug .'_names', $names );
            $options  = apply_filters( 'plethora_posttype_'. $this->post_type_slug .'_options', $options );
            $slider = new Plethora_Posttype( $names, $options );

            return $slider;
        }

        public static function single_metabox( $metaboxes ){
          $sections = array();
          $sections[] = array(
            'title'         => esc_html__('Slides', 'plethora-framework'),
            'icon_class'    => 'icon-large',
            'icon'          => 'el-icon-photo',
            'fields'        => array(
                array(
                  'id'          => METAOPTION_PREFIX .'slider-slides',
                  'type'       => 'repeater',
                  'title'      => esc_html__( 'Slides', 'plethora-framework' ),
                  'subtitle'    => esc_html__('Add as many slides as you need. You should be careful though, as too many slides with large sized images may cause slow page loading times', 'plethora-framework'),
                  'group_values' => true, // Group all fields below within the repeater ID
                  'item_name' => 'slide', // Add a repeater block name to the Add and Delete buttons
                  'bind_title' => 'slide_caption_title', // Bind the repeater block title to this field ID
                  //'static'     => 2, // Set the number of repeater blocks to be output
                  //'limit' => 2, // Limit the number of repeater blocks a user can create
                  'sortable' => true, // Allow the users to sort the repeater blocks or not
                  'translate' => true,
                  'fields'     => array(
                      array(
                        'id'       =>'slide_image',
                        'type'     => 'media', 
                        'title'    => esc_html__('Image', 'plethora-framework'),
                        'desc' => esc_html__( 'Slide will not be displayed, if left empty!', 'plethora-framework' ),
                        'url'      => false,
                        ),
                      array(
                          'id'          => 'slide_caption_title',
                          'type'        => 'text',
                          'title' => esc_html__( 'Main Caption Title', 'plethora-framework' ),
                          'placeholder' => esc_html__( 'Title', 'plethora-framework' ),
                      ),
                      array(
                          'id'          => 'slide_caption_subtitle',
                          'type'        => 'text',
                          'title' => esc_html__( 'Main Caption Subtitle', 'plethora-framework' ),
                          'placeholder' => esc_html__( 'Subtitle ( main headings section )', 'plethora-framework' ),
                      ),
                      array(
                          'id'          => 'slide_caption_secondarytitle',
                          'type'        => 'text',
                          'title' => esc_html__( 'Additional Caption Title', 'plethora-framework' ),
                      ),
                      array(
                          'id'          => 'slide_caption_secondarytext',
                          'type'        => 'textarea',
                          'title' => esc_html__( 'Additional Caption Text', 'plethora-framework' ),
                      ),
                      array(
                          'id'=> 'slide_colorset',
                          'type' => 'button_set',
                          'title' => esc_html__( 'Slide Color Set', 'plethora-framework' ),
                          'options' => array( '' => 'Default', 'skincolored_section' => 'Primary', 'secondary_section' => 'Secondary', 'dark_section' => 'Dark', 'light_section' => 'Light', 'black_section' => 'Black', 'white_section' => 'White' ),
                          'default' => '',
                        ),
                      array(
                          'id'=> 'slide_transparentfilm',
                          'type' => 'button_set',
                          'title' => esc_html__( 'Slide Background Transparency', 'plethora-framework' ),
                          "default" => '',
                          'options' => array( 'transparent_film' => 'Yes', '' => 'No'),
                        ),
                      array(
                          'id'=> 'slide_caption_colorset',
                          'type' => 'button_set',
                          'title' => esc_html__( 'Caption Color Set', 'plethora-framework' ),
                          'options' => array( '' => 'Default', 'skincolored_section' => 'Primary', 'secondary_section' => 'Secondary', 'dark_section' => 'Dark', 'light_section' => 'Light', 'black_section' => 'Black', 'white_section' => 'White' ),
                          'default' => '',
                        ),
                      array(
                          'id'=> 'slide_caption_transparentfilm',
                          'type' => 'button_set',
                          'title' => esc_html__( 'Caption Background Transparency', 'plethora-framework' ),
                          "default" => '',
                          'options' => array( 'transparent_film' => 'Yes', '' => 'No'),
                        ),
                      array(
                          'id'=> 'slide_caption_size',
                          'type' => 'button_set',
                          'title' => esc_html__( 'Caption Size', 'plethora-framework' ),
                          "default" => '',
                          'options' => array( '' => '50%', 'caption-full' => '80%'),
                        ),
                       array(
                          'id'=> 'slide_caption_align',
                          'type' => 'button_set',
                          'title' => esc_html__( 'Caption Container Align', 'plethora-framework' ),
                          'placeholder' => esc_html__( 'Title', 'plethora-framework' ),
                          'options' => array( 'caption_left' => 'Left', '' => 'Center', 'caption_right' => 'Right' ),
                          'default' => '',
                        ),
                       array(
                          'id'=> 'slide_caption_textalign',
                          'type' => 'button_set',
                          'title' => esc_html__( 'Caption Text Align', 'plethora-framework' ),
                          'placeholder' => esc_html__( 'Title', 'plethora-framework' ),
                          'options' => array( 'text-left' => 'Left', 'centered' => 'Center', 'text-right' => 'Right' ),
                          'default' => 'centered',
                        ),

                      array(
                        'id'       => 'slide_caption_neutralizetext',
                        'type'     => 'button_set',
                        'title'    => esc_html__('Neutralize Links ( links to be displayed as normal text )', 'plethora-framework'),
                        'options'  => array( 
                                      ''                 => 'No',
                                      'neutralize_links' => 'Yes',
                                      ),
                        'default' => '',
                      ),
                        array(
                          'id'=> 'slide_caption_headingstyle',
                          'type' => 'button_set',
                          'title' => esc_html__( 'Caption Text Style', 'plethora-framework' ),
                          'options' => array( 
                                      '' => 'Default', 
                                      'caption_flat' => 'Flat', 
                                      'caption_fancy' => 'Fancy', 
                                      'caption_elegant' => 'Elegant', 
                                      ),
                          'default' => 'caption_flat',
                      ),
                      array(
                          'id'=> 'slide_caption_animation',
                          'type'     => 'select',
                          'title'   => esc_html__('Caption Animation', 'plethora-framework'),
                          'options'  => Plethora_Module_Style::get_options_array( array( 
                                          'type'              => 'animations', 
                                          'use_in'            => 'redux',
                                          'title_alt'         => true,
                                          'prefix_all_values' => 'animated'
                                           )),
                          'default'  => '',
                      ),
                      array(
                        'id'      => 'slide_caption_buttonlinktext',
                        'type'    => 'text',
                        'title'   => esc_html__('Button Link Text ( not visible if empty )', 'plethora-framework'),
                        'default' => esc_html__('Learn More', 'plethora-framework')
                      ),
                      array(
                        'id'      => 'slide_caption_buttonlinkurl',
                        'type'    => 'text',
                        'title'   => esc_html__('Button Link URL ( not visible if empty or \'#\' )', 'plethora-framework'),
                        'default' => '#',
                        'validate'=> 'url'
                      ),
                      array(
                        'id'       => 'slide_caption_buttonstyle',
                        'type'     => 'button_set',
                        'title'    => esc_html__('Button Style', 'plethora-framework'),
                        'options'  => array( 
                                      'btn-link'      => 'Default',
                                      'btn-primary'   => 'Primary',
                                      'btn-secondary' => 'Secondary',
                                      'btn-white'     => 'White',
                                      'btn-success'   => 'Success',
                                      'btn-info'      => 'Info',
                                      'btn-warning'   => 'Warning',
                                      'btn-danger'    => 'Danger',
                                      ),
                        'default' => 'btn-link',
                      ),
                    array(
                        'id'      => 'slide_caption_buttonsize',
                        'type'    => 'button_set',
                        'title'   => esc_html__('Button Size', 'plethora-framework'),
                        'options' => array( 
                                      'btn'    => 'Default',
                                      'btn btn-sm' => 'Small',
                                      'btn btn-xs' => 'Extra Small',
                                      ),
                        'default' => 'btn',
                      ),
                      array(
                        'id'      => 'slide_caption_buttonlinktarget',
                        'type'    => 'button_set',
                        'title'   => esc_html__('Button Link URL Open', 'plethora-framework'),
                        'options' => array( '_self' => 'Same Window', '_blank' => 'New Window/Tab' ),
                        'default' => '_self',
                      ),

                  ),
                  'default' => ''
                )
            )
          );

          $sections[] = array(
            'title'      => esc_html__('Settings', 'plethora-framework'),
            'icon_class' => 'icon-large',
            'icon'       => 'el-icon-wrench-alt',
            'fields'     => array(
                array(
                  'id'      => METAOPTION_PREFIX .'slider-autoplay',
                  'type'    => 'switch', 
                  'title'   => esc_html__('Auto Play', 'plethora-framework'),
                  'default' => true,
                  ),  
                array(
                  'id'       => METAOPTION_PREFIX .'slider-autoplaytimeout',
                  'type'     => 'slider', 
                  'required' => array( METAOPTION_PREFIX .'slider-autoplay', '=', 1),
                  'title'    => esc_html__('Autoplay Interval Timeout', 'plethora-framework'),
                  'desc'     => esc_html__('Display time of this slide', 'plethora-framework'),
                  "min"      => 100,
                  "step"     => 100,
                  "max"      => 20000,
                  "default"  => 5000,
                  ),
                array(
                  'id'       => METAOPTION_PREFIX .'slider-autoplayspeed',
                  'type'     => 'slider', 
                  'required' => array( METAOPTION_PREFIX .'slider-autoplay', '=', 1),
                  'title'    => esc_html__('Autoplay Speed', 'plethora-framework'),
                  'desc'     => esc_html__('Time to switch to the next slide', 'plethora-framework'),
                  "min"      => 100,
                  "step"     => 100,
                  "max"      => 10000,
                  "default"  => 1000,
                  ),
                array(
                  'id'       => METAOPTION_PREFIX .'slider-autoplayhoverpause',
                  'type'     => 'switch', 
                  'required' => array( METAOPTION_PREFIX .'slider-autoplay', '=', 1),
                  'title'    => esc_html__('Pause On Mouse Hover', 'plethora-framework'),
                  'default'  => true,
                  ),
                array(
                  'id'      => METAOPTION_PREFIX .'slider-nav',
                  'type'    => 'switch', 
                  'title'   => esc_html__('Show navigation buttons', 'plethora-framework'),
                  'default' => true,
                  ),
                array(
                  'id'      => METAOPTION_PREFIX .'slider-dots',
                  'type'    => 'switch', 
                  'title'   => esc_html__('Show navigation bullets', 'plethora-framework'),
                  'default' => true,
                  ),
                array(
                  'id'      => METAOPTION_PREFIX .'slider-loop',
                  'type'    => 'switch', 
                  'title'   => esc_html__('Slideshow Loop', 'plethora-framework'),
                  'default' => false,
                  ),
                array(
                  'id'      => METAOPTION_PREFIX .'slider-mousedrag',
                  'type'    => 'switch', 
                  'title'   => esc_html__('Mouse drag', 'plethora-framework'),
                  'default' => true,
                  ),
                array(
                  'id'      => METAOPTION_PREFIX .'slider-touchdrag',
                  'type'    => 'switch', 
                  'title'   => esc_html__('Touch drag', 'plethora-framework'),
                  'default' => true,
                  ),
                array(
                  'id'      => METAOPTION_PREFIX .'slider-lazyload',
                  'type'    => 'switch', 
                  'title'   => esc_html__('Lazy Load Images', 'plethora-framework'),
                  'default' => true,
                  ),
                array(
                  'id'      => METAOPTION_PREFIX .'slider-rtl',
                  'type'    => 'switch', 
                  'title'   => esc_html__('Right To Left', 'plethora-framework'),
                  'desc'   => esc_html__('Change elements direction from Right to left', 'plethora-framework'),
                  'default' => false,
                  ),
              )
            );

            $metaboxes[] = array(
                'id'            => 'metabox-slider',
                'title'         => esc_html__( 'Slider Options', 'plethora-framework' ),
                'post_types'    => array( 'slider'),
                'position'      => 'normal', // normal, advanced, side
                'priority'      => 'high', // high, core, default, low
                'sidebar'       => false, // enable/disable the sidebar in the normal/advanced positions
                'sections'      => $sections,
            );

            if ( has_filter( 'plethora_posttype_slider_metabox') ) {

              $sections = apply_filters( 'plethora_posttype_slider_metabox', $sections );
            }
            
            return $metaboxes;
        }


        /** 
        * CSS fixes for slider-related admin pages
        *
        * @return array
        * @since 1.0
        *
        */
        function admin_post_print_css() {

            global $post_type;

            if ( $post_type == 'slider' ) {

              echo '<style type="text/css">#edit-slug-box { display: none !important; visibility: hidden; }</style>';
              echo '<style type="text/css">#post-preview { display: none !important; visibility: hidden; }</style>';
            }
        }
  }
}