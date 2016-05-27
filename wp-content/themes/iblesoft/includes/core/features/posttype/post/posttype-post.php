<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Post Type Feature Class. Post type is a native WP feature...however 
this class is used to create post/blog options not only for the native post type but also
for non Plethora CPTs created by third party tools, such as Custom Posts UI
*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Posttype') && !class_exists('Plethora_Posttype_Post') ) {  
 
	/**
	 * @package Plethora Framework
	 */

	class Plethora_Posttype_Post {

        // Plethora Index variables
		public static $feature_title         = "Native Post Type";								// Feature display title  (string)
		public static $feature_description   = "Contains all native post type configuration";	// Feature display description (string)
		public static $theme_option_control  = false;												// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = false;												// Default activation option status ( boolean )
		public static $theme_option_requires = array();												// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;												// Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;												// Additional method invocation ( string/boolean | method name or false )

        // Auxilliary variables
		public $custom_post_types;
		public $activated_custom_post_types;

        // Default option values ( for easy ext class overrides )
		public $archivepost_layout               = 'right_sidebar';
		public $archivepost_sidebar              = 'sidebar-default';
		public $archivepost_colorset             = 'foo';
		public $archivepost_title                = 1;
		public $archivepost_title_text           = 'The Blog';
		public $archivepost_title_tax            = 1;
		public $archivepost_title_author         = 1;
		public $archivepost_title_date           = 1;
		public $archivepost_subtitle             = 1;
		public $archivepost_subtitle_text        = 'Medical Articles & News';
		public $archivepost_tax_subtitle         = 1;
		public $archivepost_author_subtitle      = 1;
		public $archivepost_date_subtitle        = 0;
		public $archivepost_listtype             = 'classic';
		public $archivepost_mediadisplay         = 'inherit';
		public $archivepost_listing_content      = 'content';
		public $archivepost_listing_subtitle     = 0;
		public $archivepost_info_category        = 1;
		public $archivepost_info_author          = 1;
		public $archivepost_info_date            = 1;
		public $archivepost_info_comments        = 1;
		public $archivepost_show_linkbutton      = 1;
		public $archivepost_show_linkbutton_text = 'Read More';
		public $archivepost_noposts_title        = 'No posts where found!';
		public $archivepost_noposts_description  = 'Unfortunately, no posts were found! Please try again soon!';

		public $post_layout                      = 'right_sidebar';
		public $post_sidebar                     = 'sidebar-default';
		public $post_colorset                    = 'foo';
		public $post_title                       = 1;
		public $post_subtitle                    = 0;
		public $post_mediadisplay                = 1;
		public $post_media_stretch               = 'stretchy_wrapper ratio_2-1';
		public $post_categories                  = 1;
		public $post_tags                        = 1;
		public $post_author                      = 1;
		public $post_date                        = 1;
		public $post_comments                    = 1;


		public function __construct() {

			add_action( 'init', array( $this, 'add_custom_posts_support' ), 15 );
			add_action( 'init', array( $this, 'check_custom_posts_status' ), 16 );
			add_action( 'init', array( $this, 'init' ), 17 );

		}

		public function init() {

			// Built in posts archive/single theme options
			add_filter( 'plethora_themeoptions_content', array($this, 'archive_themeoptions'), 5);
			add_filter( 'plethora_themeoptions_content', array($this, 'single_themeoptions'), 110);
			add_filter( 'plethora_metabox_add', array($this, 'single_metabox'));		
			add_filter( 'plethora_metabox_add', array($this, 'single_metabox_audio'));		
			add_filter( 'plethora_metabox_add', array($this, 'single_metabox_video'));

			// Non Plethora CPTs archive/single theme options
			add_filter( 'plethora_themeoptions_content', array($this, 'archive_custompost_themeoptions'), 50);
			add_filter( 'plethora_themeoptions_content', array($this, 'single_custompost_themeoptions'), 150);
			add_filter( 'plethora_metabox_add', array($this, 'single_custompost_metabox'));		
			
		}

		public function add_custom_posts_support() {

			// Get all post types that have a single view on frontend
			$all_post_types    = Plethora_Theme::get_supported_post_types( array( 
										'type' => 'singles', 
										'plethora_only' => false, 
										'output' => 'objects' 
									));
			// Get all Plethora CPTs that have a single view on frontend
			$plethora_post_types  = Plethora_Theme::get_supported_post_types( array( 
										'type' => 'singles', 
										'plethora_only' => true, 
										'output' => 'objects' 
									));
			// Add support for non Plethora post types
			$this->custom_post_types = array_diff_key( $all_post_types, $plethora_post_types );
			add_filter( 'plethora_posttype_features_options', array( $this, 'posttype_features_options'), 10, 2 );			
		}

		public function posttype_features_options( $options, $controller ) {

			foreach ( $this->custom_post_types as $post_type => $post_type_obj ) {

				$options[] = array(
					'id'       => THEMEOPTION_PREFIX . $controller .'-'. $post_type .'-status',
					'type'     => 'switch',
					'title'    => $post_type_obj->labels->singular_name .' '. esc_html__( 'Post Type', 'plethora-framework'),
					'subtitle' => '<span style="color:red">'. esc_html__('Third Party Feature / Plugin', 'plethora-framework') .'</span>',
					'desc'     => sprintf( esc_html__('This is a third party plugin custom post type. This option will activate/deactivate %1$s frontend options support for this CPT. Deactivating support does NOT mean that the post type will not be still active.', 'plethora-framework'), THEME_DISPLAYNAME ),
					'on'       => esc_html__('Activated', 'plethora-framework'),
					'off'      => esc_html__('Deactivated', 'plethora-framework'),
					'default'  => 1,
				);
			}

			return $options;

		}

		public function check_custom_posts_status() {

			$custom_post_types = array();
			foreach ( $this->custom_post_types as $post_type => $post_type_obj ) {

				$is_activated = Plethora_Theme::option( THEMEOPTION_PREFIX .'posttype-'. $post_type .'-status', 1 );
				
				if ( $is_activated ) {

					$custom_post_types[$post_type] = $post_type_obj;
				}
			}

			$this->activated_custom_post_types = $custom_post_types;
		}


        public function archive_themeoptions( $sections  ) {

			$page_for_posts	= get_option( 'page_for_posts', 0 );
			$desc_1 = esc_html('These options affect your posts catalog display.', 'plethora-framework');
			$desc_2 = esc_html('These options affect your posts catalog display...however it seems that you', 'plethora-framework'); 
			$desc_2 .= ' <span style="color:red">';
			$desc_2 .= esc_html('have not set a static posts page yet!.', 'plethora-framework');
			$desc_2 .= '</span>';
			$desc_2 .= esc_html('You can go for it under \'Settings > Reading\'', 'plethora-framework');
			$desc = $page_for_posts === 0 || empty($page_for_posts) ? $desc_2 :  $desc_1 ;
			$desc .= '<br>'. esc_html('If you are using a speed optimization plugin, don\'t forget to <strong>clear cache</strong> after options update', 'plethora-framework');


            // Get permanent theme options configuration
            $fields_global  = $this->archive_themeoptions_global();

            // Get theme-specific theme options configuration
            $fields_theme = $this->archive_themeoptions_ext();

            // Merge fields and return $sections
            $fields = array_merge_recursive( $fields_global, $fields_theme );

	    	$sections[] = array(
				'title'      => esc_html('Blog', 'plethora-framework'),
				'heading'    => esc_html('BLOG OPTIONS', 'plethora-framework'),
				'desc'       => $desc,
				'icon_class' => 'icon-large',
				'subsection' => true,
				'fields'     => $fields
            );
            return $sections;
        }

        /** 
        * Returns global fields for the archive theme options configuration
        */
        public function archive_themeoptions_global() {

            $fields = array(
                    array(
						'id'     => 'archivepost-page-start',
						'type'   => 'section',
						'title'  => esc_html('Blog Page Options', 'plethora-framework'),
						'indent' => true,
				     ),
			            array(
							'id'      => METAOPTION_PREFIX .'archivepost-layout',
							'title'   => esc_html('Page Layout', 'plethora-framework' ),
							'type'    => 'image_select',
							'default' => $this->archivepost_layout,
							'options' => Plethora_Module_Style::get_options_array( array( 
																						'type'   => 'page_layouts',
																						'use_in' => 'redux',
																				   )
										 ),
			            ),
						array(
							'id'       => METAOPTION_PREFIX .'archivepost-sidebar',
							'required' => array(METAOPTION_PREFIX .'archivepost-layout','equals',array('right_sidebar','left_sidebar')),  
							'type'     => 'select',
							'data'     => 'sidebars',
							'multi'    => false,
							'default'  => $this->archivepost_sidebar,
							'title'    => esc_html('Sidebar', 'plethora-framework'), 
						),
						array(
							'id'      => METAOPTION_PREFIX .'archivepost-colorset',
							'type'    => 'button_set',
							'title'   => esc_html('Content Section Color Set', 'plethora-framework' ),
							'desc'    => esc_html('Will define text and background color on content section ( main column + sidebar ), according to selected color set configuration', 'plethora-framework' ),
							'default' => $this->archivepost_colorset,
							'options' => Plethora_Module_Style::get_options_array( array( 'type' 			=> 'color_sets',
																						  'use_in'          => 'redux',
																						  'prepend_options' => array(  'foo' => esc_html('Default', 'plethora-framework') ) ) ),
						),
						array(
							'id'      => METAOPTION_PREFIX .'archivepost-title',
							'type'    => 'switch', 
							'title'   => esc_html('Display Title On Content', 'plethora-framework'),
							'desc'    => esc_html('Will display title on content view', 'plethora-framework'),
							'default' => $this->archivepost_title,
							),	
						array(
							'id'        => METAOPTION_PREFIX .'archivepost-title-text',
							'type'      => 'text',
							'title'     => esc_html('Default Title', 'plethora-framework'), 
							'default'   => $this->archivepost_title_text,
							'translate' => true,
							),
						array(
							'id'      => METAOPTION_PREFIX .'archivepost-title-tax',
							'type'    => 'button_set', 
							'title'   => esc_html('Selected Taxonomy Title', 'plethora-framework'),
							'desc'    => esc_html('Title behavior when a category OR tag archive is displayed', 'plethora-framework'),
							'default' => $this->archivepost_title_tax,
							'options' => array(
											0 => esc_html('Default Title', 'plethora-framework'),
											1 => esc_html('Taxonomy Title', 'plethora-framework'),
										),
							),	
						array(
							'id'      => METAOPTION_PREFIX .'archivepost-title-author',
							'type'    => 'button_set', 
							'title'   => esc_html('Selected Author Title', 'plethora-framework'),
							'desc'    => esc_html('Title behavior when an author archive is displayed', 'plethora-framework'),
							'default' => $this->archivepost_title_author,
							'options' => array(
											0 => esc_html('Default Title', 'plethora-framework'),
											1 => esc_html('Author Display Name', 'plethora-framework'),
										),
							),	
						array(
							'id'      => METAOPTION_PREFIX .'archivepost-title-date',
							'type'    => 'button_set', 
							'title'   => esc_html('Selected Date Title', 'plethora-framework'),
							'desc'    => esc_html('Title behavior when a date view is selected', 'plethora-framework'),
							'default' => $this->archivepost_title_date,
							'options' => array(
											0 => esc_html('Default Title', 'plethora-framework'),
											1 => esc_html('Selected Month', 'plethora-framework'),
										),
							),	
						array(
							'id'      => METAOPTION_PREFIX .'archivepost-subtitle',
							'type'    => 'switch', 
							'title'   => esc_html('Display Subtitle On Content', 'plethora-framework'),
							'default' => $this->archivepost_subtitle,
							),	

						array(
							'id'        => METAOPTION_PREFIX .'archivepost-subtitle-text',
							'type'      => 'text',
							'title'     => esc_html('Default Subtitle', 'plethora-framework'), 
							'default'   => $this->archivepost_subtitle_text,
							'translate' => true,
							),

						array(
							'id'      => METAOPTION_PREFIX .'archivepost-tax-subtitle',
							'type'    => 'button_set', 
							'title'   => esc_html('Selected Taxonomy Subtitle', 'plethora-framework'),
							'desc'    => esc_html('Subtitle behavior when a category OR tag archive is displayed', 'plethora-framework'),
							'default' => $this->archivepost_tax_subtitle,
							'options' => array(
											0 => esc_html('Default Subtitle', 'plethora-framework'),
											1 => esc_html('Taxonomy Description', 'plethora-framework'),
										),
							),	
						array(
							'id'      => METAOPTION_PREFIX .'archivepost-author-subtitle',
							'type'    => 'button_set', 
							'title'   => esc_html('Selected Author Subtitle', 'plethora-framework'),
							'desc'    => esc_html('Subtitle behavior when an author archive is displayed', 'plethora-framework'),
							'default' => $this->archivepost_author_subtitle,
							'options' => array(
											0 => esc_html('Default Subtitle', 'plethora-framework'),
											1 => esc_html('Author Bio', 'plethora-framework'),
										),
							),	
						array(
							'id'      => METAOPTION_PREFIX .'archivepost-date-subtitle',
							'type'    => 'button_set', 
							'title'   => esc_html('Selected Date Subtitle', 'plethora-framework'),
							'desc'    => esc_html('Subtitle behavior when a date view is selected', 'plethora-framework'),
							'default' => $this->archivepost_date_subtitle,
							'options' => array(
											0 => esc_html('Default Subtitle', 'plethora-framework'),
											1 => esc_html('Empty', 'plethora-framework'),
										),
							),	

					array(
						'id'     => 'archivepost-page-end',
						'type'   => 'section',
						'indent' => false,
				     ),
					array(
						'id'     => 'archivepost-listings-start',
						'type'   => 'section',
						'title'  => esc_html('Posts Listings Options', 'plethora-framework'),
						'indent' => true,
				     ),
						array(
							'id'      => METAOPTION_PREFIX .'archivepost-listtype',
							'type'    => 'button_set', 
							'title'   => esc_html('Posts catalog type', 'plethora-framework'), 
							'default' => $this->archivepost_listtype,
							'options' => array(
								'classic' => esc_html('Classic', 'plethora-framework'), 
								'compact' => esc_html('Compact', 'plethora-framework'), 
							)
						),	
						array(
							'id'      => METAOPTION_PREFIX .'archivepost-mediadisplay',
							'type'    => 'button_set', 
							'title'   => esc_html('Featured Media Display', 'plethora-framework'),
							'default' => $this->archivepost_mediadisplay,
							'options' => array(
									'inherit'       => 'According To Post Format',
									'featuredimage' => 'Force Featured Image Display',
									'hide'          => 'Do Not Display',
									),
							),	

						array(
							'id'      => METAOPTION_PREFIX .'archivepost-listing-content',
							'type'    => 'button_set', 
							'title'   => esc_html('Content/Excerpt Display', 'plethora-framework'), 
							'desc'    => esc_html('Displaying content will allow you to display posts containing the WP editor "More" tag.', 'plethora-framework'),
							'default' => $this->archivepost_listing_content,
							'options' => array(
								'excerpt' => esc_html('Display Excerpt', 'plethora-framework'), 
								'content' => esc_html('Display Content', 'plethora-framework') 
							)
						),	
						array(
							'id'      => METAOPTION_PREFIX .'archivepost-listing-subtitle',
							'type'    => 'switch', 
							'title'   => esc_html('Display Subtitle', 'plethora-framework'),
							'default' => $this->archivepost_listing_subtitle,
							'options' => array(
											1 => esc_html('Display', 'plethora-framework'),
											0 => esc_html('Hide', 'plethora-framework'),
										),
							),	
						array(
							'id'      => METAOPTION_PREFIX .'archivepost-info-category',
							'type'    => 'switch', 
							'title'   => esc_html('Display Categories Info', 'plethora-framework'),
							"default" => $this->archivepost_info_category,
							),	

						array(
							'id'      => METAOPTION_PREFIX .'archivepost-info-author',
							'type'    => 'switch', 
							'title'   => esc_html('Display Author Info', 'plethora-framework'),
							"default" => $this->archivepost_info_author,
							),	
						array(
							'id'      => METAOPTION_PREFIX .'archivepost-info-date',
							'type'    => 'switch', 
							'title'   => esc_html('Display Date Info', 'plethora-framework'),
							"default" => $this->archivepost_info_date,
							),	
						array(
							'id'      => METAOPTION_PREFIX .'archivepost-info-comments',
							'type'    => 'switch', 
							'title'   => esc_html('Display Comments Count Info', 'plethora-framework'),
							"default" => $this->archivepost_info_comments,
							),	
						array(
							'id'      => METAOPTION_PREFIX .'archivepost-show-linkbutton',
							'type'    => 'switch', 
							'title'   => esc_html('Display "Read More" Button', 'plethora-framework'),
							"default" => $this->archivepost_show_linkbutton,
							),	
						array(
							'id'        =>METAOPTION_PREFIX .'archivepost-show-linkbutton-text',
							'type'      => 'text',
							'required'  => array(METAOPTION_PREFIX .'archivepost-show-linkbutton', '=', 1),
							'title'     => esc_html('Button Text', 'plethora-framework'),
							"default"   => $this->archivepost_show_linkbutton_text,
							'translate' => true,
						),	
						array(
							'id'        =>METAOPTION_PREFIX .'archivepost-noposts-title',
							'type'      => 'text', 
							'title'     => esc_html('No Posts Title', 'plethora-framework'),
							"default"   => $this->archivepost_noposts_title,
							'translate' => true,
						),	
						array(
							'id'        =>METAOPTION_PREFIX .'archivepost-noposts-description',
							'type'      => 'textarea', 
							'title'     => '<strong>'. esc_html('No Posts', 'plethora-framework') .'</strong> '. esc_html('Description', 'plethora-framework'),
							"default"   => $this->archivepost_noposts_description,
							'translate' => true,
						),	
					array(
				       'id' => 'archivepost-listings-end',
				       'type' => 'section',
				       'indent' => false,
				     ),
                );

            return $fields;
        }

        /** 
        * Returns theme-specific fields for the archive theme options configuration
        * Do not use in this class, for extension classes only
        */
        public function archive_themeoptions_ext() { return array(); }


        /** 
        * Returns theme options configuration. Collects global and theme-specific fields
        * Hooked @ 'plethora_themeoptions_content'
        */
        public function single_themeoptions( $sections ){

            // Get permanent theme options configuration
            $fields_global  = $this->single_themeoptions_global();

            // Get theme-specific theme options configuration
            $fields_theme = $this->single_themeoptions_ext();

            // Merge fields and return $sections
            $fields = array_merge_recursive( $fields_global, $fields_theme );

			$sections[] = array(
				'title'   => 'Posts',
				'heading' => esc_html('SINGLE POST OPTIONS', 'plethora-framework'),
				'desc'    => esc_html('These will be the default values for a new post you create. You have the possibility to override most of these settings on each post separately.', 'plethora-framework') . '<br><span style="color:red;">'. esc_html('Important: ', 'plethora-framework') . '</span>'. esc_html('changing a default value here will not affect options that were customized per post. In example, if you change a previously default "full width" to "right sidebar" layout this will switch all full width posts to right sidebar ones. However it will not affect those that were customized, per post, to display a left sidebar.', 'plethora-framework') ,
				'subsection' => true,
                'fields'     => $fields
            );
            return $sections;
        }

        /** 
        * Returns global fields for the theme options configuration
        */
        public function single_themeoptions_global() {

            $fields = array(

		            array(
						'id'         =>  METAOPTION_PREFIX . 'post-layout',
						'title'      => esc_html('Single Post Layout', 'plethora-framework' ),
						'desc'       => esc_html('Select main content and sidebar arrangement on single post view', 'plethora-framework' ),
						'default'    => $this->post_layout,
						'type'       => 'image_select',
						'customizer' => array(),
						'options'    => Plethora_Module_Style::get_options_array( array( 
																					'type'   => 'page_layouts',
																					'use_in' => 'redux',
																			   )
									 ),
		            ),
					array(
						'id'      => METAOPTION_PREFIX . 'post-sidebar',
						'type'    => 'select',
						'data'    => 'sidebars',
						'multi'   => false,
						'title'   => esc_html('Single Post Sidebar', 'plethora-framework'), 
						'desc'    => esc_html('If empty, the default sidebar will be used. Create as many sidebars you need on <strong>Advanced > Sidebars</strong>', 'plethora-framework'),
						'default' => $this->post_sidebar,
						),

					array(
						'id'      => METAOPTION_PREFIX . 'post-colorset',
						'type'    => 'button_set',
						'title'   => esc_html('Content Section Color Set', 'plethora-framework' ),
						'desc'    => esc_html('Will define text and background color on content section ( main column + sidebar ), according to selected color set configuration', 'plethora-framework' ),
						'default' => $this->post_colorset,
						'options' => Plethora_Module_Style::get_options_array( array( 'type' 			=> 'color_sets',
																					  'use_in'          => 'redux',
																						  'prepend_options' => array(  'foo' => esc_html('Default', 'plethora-framework') ) ) ),
					),
					array(
						'id'      => METAOPTION_PREFIX . 'post-title',
						'type'    => 'switch', 
						'title'   => esc_html('Display Title', 'plethora-framework'),
						'default' => $this->post_title,
						'options' => array(
										1 => esc_html('Display', 'plethora-framework'),
										0 => esc_html('Hide', 'plethora-framework'),
									),
						),	
					array(
						'id'      => METAOPTION_PREFIX . 'post-subtitle',
						'type'    => 'switch', 
						'title'   => esc_html('Display Subtitle', 'plethora-framework'),
						'default' => $this->post_subtitle,
						'options' => array(
										1 => esc_html('Display', 'plethora-framework'),
										0 => esc_html('Hide', 'plethora-framework'),
									),
						),	
					array(
						'id'      => METAOPTION_PREFIX . 'post-mediadisplay',
						'type'    => 'switch', 
						'title'   => esc_html('Display Feautured Media', 'plethora-framework'),
						'default' => $this->post_mediadisplay,
						),	
					array(
						'id'      => METAOPTION_PREFIX . 'post-media-stretch',
						'type'    => 'button_set', 
						'title'   => esc_html('Media Display Ratio', 'plethora-framework'),
						'desc'    => esc_html('Will be applied on single AND listing view', 'plethora-framework'),
						'default' => $this->post_media_stretch,
						'options' => Plethora_Module_Style::get_options_array( array( 
	                                        'type' => 'stretchy_ratios',
	                                        'prepend_options' => array( 'foo_stretch' => esc_html('Native Ratio', 'plethora-framework' ) ),
	                                        )),            
						),	
					array(
						'id'      => METAOPTION_PREFIX . 'post-categories',
						'type'    => 'switch', 
						'title'   => esc_html('Display Categories Info', 'plethora-framework'),
						"default" => $this->post_categories,
						),	
					array(
						'id'      => METAOPTION_PREFIX . 'post-tags',
						'type'    => 'switch', 
						'title'   => esc_html('Display Tags Info', 'plethora-framework'),
						"default" => $this->post_tags,
						),	
					array(
						'id'      => METAOPTION_PREFIX . 'post-author',
						'type'    => 'switch', 
						'title'   => esc_html('Display Author Info', 'plethora-framework'),
						"default" => $this->post_author,
						),	
					array(
						'id'      => METAOPTION_PREFIX . 'post-date',
						'type'    => 'switch', 
						'title'   => esc_html('Display Date Info', 'plethora-framework'),
						"default" => $this->post_date,
						),	
					array(
						'id'      => METAOPTION_PREFIX . 'post-comments',
						'type'    => 'switch', 
						'title'   => esc_html('Display Comments Count Info', 'plethora-framework'),
						"default" => $this->post_comments,
						)
                );

            return $fields;
        }

        /** 
        * Returns theme-specific fields for the theme options configuration
        * Do not use in this class, for extension classes only
        */
        public function single_themeoptions_ext() { return array(); }

        /** 
        * Returns single options configuration. Collects global and theme-specific fields
        * Hooked @ 'plethora_metabox_add'
        */
        public function single_metabox( $metaboxes ) {

            // Get permanent theme options configuration
            $fields_global  = $this->single_metabox_global();

            // Get theme-specific theme options configuration
            $fields_theme = $this->single_metabox_ext();

            // Merge fields and return $sections
            $fields = array_merge_recursive( $fields_global, $fields_theme );
	    	$sections_content = array(
				'title'      => esc_html('Content', 'plethora-framework'),
				'heading'    => esc_html('CONTENT OPTIONS', 'plethora-framework'),
				'icon_class' => 'icon-large',
				'icon'       => 'el-icon-lines',
                'fields'     => $fields
			);

			$sections = array();
			$sections[] = $sections_content;
			if ( has_filter( 'plethora_metabox_singlepost') ) {

				$sections = apply_filters( 'plethora_metabox_singlepost', $sections );
			}

		    $metaboxes[] = array(
		        'id'            => 'metabox-single-post',
		        'title'         => esc_html('Post Options', 'plethora-framework' ),
		        'post_types'    => array( 'post' ),
		        'position'      => 'normal', // normal, advanced, side
		        'priority'      => 'high', // high, core, default, low
		        'sections'      => $sections,
		    );

            return $metaboxes;
        }

        /** 
        * Returns global fields for the single options configuration
        */
        public function single_metabox_global() {

            $singleview_fields = array(
				array(
			       'id' => 'post-singleview-start',
			       'type' => 'section',
			       'title' => esc_html('Single Post View', 'plethora-framework'),
			       'subtitle' => esc_html('These options affect this post\'s display when displayed on its single page', 'plethora-framework'),
			       'indent' => true,
			     ),
		            array(
						'id'      =>  METAOPTION_PREFIX . 'post-layout',
						'title'   => esc_html('Select Layout', 'plethora-framework' ),
						'type'    => 'image_select',
						'options' => Plethora_Module_Style::get_options_array( array( 
																					'type'   => 'page_layouts',
																					'use_in' => 'redux',
																			   )
									 ),
		                ),
	                array(
	                    'id'=> METAOPTION_PREFIX . 'post-sidebar',
	                    'type' => 'select',
	                    'data' => 'sidebars',
	                    'multi' => false,
	                    'title' => esc_html('Select Sidebar', 'plethora-framework'), 
	                    ),
					array(
						'id'=> METAOPTION_PREFIX . 'post-colorset',
						'type' => 'button_set',
                        'title' => esc_html('Content Section Color Set', 'plethora-framework' ),
                    	'desc' => esc_html('Will define text and background color on content section ( main column + sidebar ), according to selected color set configuration', 'plethora-framework' ),
						'options' => Plethora_Module_Style::get_options_array( array( 'type' 			=> 'color_sets',
																					  'use_in'          => 'redux',
																					  'prepend_options' => array(  'foo' => esc_html('Default', 'plethora-framework') ) ) ),
					),
					array(
						'id'      => METAOPTION_PREFIX . 'post-title',
						'type'    => 'switch', 
						'title'   => esc_html('Display Title', 'plethora-framework'),
						'options' => array(
										1 => esc_html('Display', 'plethora-framework'),
										0 => esc_html('Hide', 'plethora-framework'),
									),
						),	
					array(
						'id'      => METAOPTION_PREFIX . 'post-subtitle',
						'type'    => 'switch', 
						'title'   => esc_html('Display Subtitle', 'plethora-framework'),
						'options' => array(
										1 => esc_html('Display', 'plethora-framework'),
										0 => esc_html('Hide', 'plethora-framework'),
									),
						),	
					array(
						'id'       => METAOPTION_PREFIX . 'post-subtitle-text',
						'type'     => 'text',
						'title'    => esc_html('Subtitle', 'plethora-framework'), 
						'translate' => true,
						),
					array(
						'id'      => METAOPTION_PREFIX . 'post-mediadisplay',
						'type'    => 'switch', 
						'title'   => esc_html('Display Featured Media', 'plethora-framework'),
						),	

					array(
						'id'      => METAOPTION_PREFIX . 'post-media-stretch',
						'type'    => 'button_set', 
						'title'   => esc_html('Media Display Ratio', 'plethora-framework'),
						'desc'   => esc_html('Will be applied on single AND listing view', 'plethora-framework'),
						'options' => Plethora_Module_Style::get_options_array( array( 
	                                        'type' => 'stretchy_ratios',
                                        	'prepend_options' => array( 'foo_stretch' => esc_html('Native Ratio', 'plethora-framework' ) ),
	                                        )),            
						),	
					array(
						'id'=> METAOPTION_PREFIX . 'post-categories',
						'type' => 'switch', 
						'title' => esc_html('Display Categories Info', 'plethora-framework'),
						),	
					array(
						'id'=> METAOPTION_PREFIX . 'post-tags',
						'type' => 'switch', 
						'title' => esc_html('Display Tags Info', 'plethora-framework'),
						),	
					array(
						'id'=> METAOPTION_PREFIX . 'post-author',
						'type' => 'switch', 
						'title' => esc_html('Display Author Info', 'plethora-framework'),
						),	
					array(
						'id'=> METAOPTION_PREFIX . 'post-date',
						'type' => 'switch', 
						'title' => esc_html('Display Date Info', 'plethora-framework'),
						),	
					array(
						'id'=> METAOPTION_PREFIX . 'post-comments',
						'type' => 'switch', 
						'title' => esc_html('Display Comments Count Info', 'plethora-framework'),
						),	
			);					
			$singleview_fields = array_merge_recursive( $singleview_fields, $this->single_metabox_singleview_ext() );

			$listview_fields = array(
				array(
			       'id' => 'post-listview-start',
			       'type' => 'section',
			       'title' => esc_html('Blog Listing View', 'plethora-framework'),
			       'subtitle' => esc_html('These options affect this post\'s display when displayed on post archive (blog)', 'plethora-framework'),
			       'indent' => true,
			     ),
					array(
						'id'=> METAOPTION_PREFIX .'archivepost-mediadisplay',
						'type' => 'button_set', 
						'title' => esc_html('Featured Media Display', 'plethora-framework'),
						'desc' => '<strong>'. esc_html('According To Post Format', 'plethora-framework') .'</strong> '. esc_html('will display the featured video/audio in posts list (according on its post format).', 'plethora-framework') . esc_html('You can set the post format on Format box on the right', 'plethora-framework'),
						'options' => array(
								'inherit' => 'According To Post Format',
								'featuredimage' => 'Force Featured Image Display',
								'hide' => 'Do Not Display',
								),
						),	

					array(
						'id'      => METAOPTION_PREFIX .'archivepost-listing-content',
						'type'    => 'button_set', 
						'title'    => esc_html('Content/Excerpt Display', 'plethora-framework'), 
						'descr' => esc_html('Displaying content will allow you to display posts containing the WP editor "More" tag.', 'plethora-framework'),
						'options' => array(
							'excerpt' => esc_html('Display Excerpt', 'plethora-framework'), 
							'content' => esc_html('Display Content', 'plethora-framework') 
						)
					),	
					array(
						'id'      => METAOPTION_PREFIX .'archivepost-listing-subtitle',
						'type'    => 'switch', 
						'title'   => esc_html('Display Subtitle', 'plethora-framework'),
						'options' => array(
										1 => esc_html('Display', 'plethora-framework'),
										0 => esc_html('Hide', 'plethora-framework'),
									),
						),	
		            array(
		              'id'      => METAOPTION_PREFIX .'archivepost-info-category',
		              'type'    => 'switch', 
		              'title'   => esc_html('Display Categories Info', 'plethora-framework'),
		              ), 
		            array(
		              'id'      => METAOPTION_PREFIX .'archivepost-info-tags',
		              'type'    => 'switch', 
		              'title'   => esc_html('Display Tag Info', 'plethora-framework'),
		              ),  
		            array(
		              'id'      => METAOPTION_PREFIX .'archivepost-info-author',
		              'type'    => 'switch', 
		              'title'   => esc_html('Display Author Info', 'plethora-framework'),
		              ),  
		            array(
		              'id'      => METAOPTION_PREFIX .'archivepost-info-date',
		              'type'    => 'switch', 
		              'title'   => esc_html('Display Date Info', 'plethora-framework'),
		              ),  
		            array(
		              'id'      => METAOPTION_PREFIX .'archivepost-info-comments',
		              'type'    => 'switch', 
		              'title'   => esc_html('Display Comments Count Info', 'plethora-framework'),
		              ),  
					array(
						'id'      => METAOPTION_PREFIX .'archivepost-show-linkbutton',
						'type'    => 'switch', 
						'title'   => esc_html('Display "Read More" Button', 'plethora-framework'),
						),	
        	);
			
			$fields = array_merge_recursive( $singleview_fields, $listview_fields );
			$fields = array_merge_recursive( $fields, $this->single_metabox_listview_ext() );
            return $fields;
        }

        /** 
        * Returns theme-specific fields for the single metabox configuration
        * Do not use in this class, for extension classes only
        */
        public function single_metabox_ext() { return array(); }

        /** 
        * Returns theme-specific fields for the single metabox configuration ( Single View options )
        * Do not use in this class, for extension classes only
        */
        public function single_metabox_singleview_ext() { return array(); }

        /** 
        * Returns theme-specific fields for the single metabox configuration ( List View options )
        * Do not use in this class, for extension classes only
        */
        public function single_metabox_listview_ext() { return array(); }


        public function single_metabox_audio( $metaboxes ) {

		    $sections = array();

		    $sections[] = array(
		        'icon_class'    => 'icon-large',
		        'icon'          => 'el-icon-website',
		        'fields'        => array(


					array(
						'id'=> METAOPTION_PREFIX .'content-audio',
						'type' => 'text', 
						'title' => esc_html('Audio Link', 'plethora-framework'),
						'desc' => esc_html('Enter audio url/share link from: <strong>SoundCloud | Spotify | Rdio </strong>', 'plethora-framework'),
						'validate' => 'url',
						),

		        )
		    );

		    $metaboxes[] = array(
		        'id'            => 'metabox-single-post-audio',
		        'title'         => esc_html('Featured Audio', 'plethora-framework' ),
		        'post_types'    => array( 'post'),
		        'post_format'    => array( 'audio'),
		        'position'      => 'side', // normal, advanced, side
		        'priority'      => 'low', // high, core, default, low
		        'sections'      => $sections,
		    );

		    return $metaboxes;
		}

        public function single_metabox_video( $metaboxes ) {

		    $sections = array();

		    $sections[] = array(
		        'icon_class'    => 'icon-large',
		        'icon'          => 'el-icon-website',
		        'fields'        => array(


					array(
						'id'=> METAOPTION_PREFIX .'content-video',
						'type' => 'text', 
						'title' => esc_html('Video Link', 'plethora-framework'),
						'desc' => esc_html('Enter video url/share link from: <strong>YouTube | Vimeo | Dailymotion | Blip | Wordpress.tv</strong>', 'plethora-framework'),
						'validate' => 'url',
						),

		        )
		    );

		    $metaboxes[] = array(
		        'id'            => 'metabox-single-post-video',
		        'title'         => esc_html('Featured Video', 'plethora-framework' ),
		        'post_types'    => array( 'post'),
		        'post_format'    => array( 'video'),
		        'position'      => 'side', // normal, advanced, side
		        'priority'      => 'low', // high, core, default, low
		        'sections'      => $sections,
		    );

		    return $metaboxes;
        }

        public function archive_custompost_themeoptions( $sections  ) {

			foreach ( $this->activated_custom_post_types as $post_type => $post_type_obj ) {

				if ( $post_type_obj->has_archive ) {

		        	$post_type_label = $post_type_obj->label;
		        	$post_type_label_singular = !empty( $post_type_obj->labels->singular_name ) ? $post_type_obj->labels->singular_name : ucfirst( $post_type_label ) ;

		        	$desc  = sprintf( esc_html( 
		        		'*IMPORTANT: this custom post type archive is registered via a third party plugin. This tab\'s options will help you configure its ARCHIVE view on %1$s frontend. If you just don\'t need frontend support for %2$s, you have the option to deactivate it on %3$s', 'plethora-framework' ), 
		        		THEME_DISPLAYNAME,
		        		'<strong>'. strtolower( $post_type_label ) .'</strong>',
		        		'<strong>Theme Options > Advanced > Features Library > Post Types Manager</strong>'
					);
			    	$sections[] = array(
						'title'      => $post_type_label . ' '. esc_html('Archive *', 'plethora-framework'),
						'heading'    => strtoupper( $post_type_label ) . ' '. esc_html('ARCHIVE OPTIONS', 'plethora-framework'),
						'desc'       => $desc,
						'icon_class' => 'icon-large',
						'subsection' => true,
						'fields'     => array(
							array(
						       'id' => 'archive'. $post_type .'-page-start',
						       'type' => 'section',
						       'title' => esc_html('Archive Page Options', 'plethora-framework'),
						       'indent' => true,
						     ),
					            array(
									'id'      => METAOPTION_PREFIX .'archive'. $post_type .'-layout',
									'title'   => esc_html('Page Layout', 'plethora-framework' ),
									'type'    => 'image_select',
									'default' => 'right_sidebar',
									'options' => Plethora_Module_Style::get_options_array( array( 
																								'type'   => 'page_layouts',
																								'use_in' => 'redux',
																						   )
												 ),
					            ),
								array(
									'id'=>METAOPTION_PREFIX .'archive'. $post_type .'-sidebar',
									'required' => array(METAOPTION_PREFIX .'archive'. $post_type .'-layout','equals',array('right_sidebar','left_sidebar')),  
									'type' => 'select',
									'data' => 'sidebars',
									'multi' => false,
									'default'  => 'sidebar-default',
									'title' => esc_html('Sidebar', 'plethora-framework'), 
								),
								array(
									'id'=> METAOPTION_PREFIX .'archive'. $post_type .'-colorset',
									'type' => 'button_set',
			                        'title' => esc_html('Content Section Color Set', 'plethora-framework' ),
		                        	'desc' => esc_html('Will define text and background color on content section ( main column + sidebar ), according to selected color set configuration', 'plethora-framework' ),
									'options' => Plethora_Module_Style::get_options_array( array( 'type' 			=> 'color_sets',
																								  'use_in'          => 'redux',
																								  'prepend_options' => array(  'foo' => esc_html('Default', 'plethora-framework') ) ) ),
									'default' => 'foo',
								),
								array(
									'id'      => METAOPTION_PREFIX .'archive'. $post_type .'-title',
									'type'    => 'switch', 
									'title'   => esc_html('Display Title On Content', 'plethora-framework'),
									'desc'    => esc_html('Will display title on content view', 'plethora-framework'),
									'default' => 1,
									),	
								array(
									'id'       => METAOPTION_PREFIX .'archive'. $post_type .'-title-text',
									'type'     => 'text',
									'title'    => esc_html('Default Title', 'plethora-framework'), 
									'default'  => $post_type_label . ' '. esc_html('Archive', 'plethora-framework'),
									'translate' => true,
									),
								array(
									'id'      => METAOPTION_PREFIX .'archive'. $post_type .'-title-tax',
									'type'    => 'button_set', 
									'title'   => esc_html('Selected Taxonomy Title', 'plethora-framework'),
									'desc'   => esc_html('Title behavior when a category OR tag archive is displayed', 'plethora-framework'),
									'default'  => 1,
									'options' => array(
													0 => esc_html('Default Title', 'plethora-framework'),
													1 => esc_html('Taxonomy Title', 'plethora-framework'),
												),
									),	
								array(
									'id'      => METAOPTION_PREFIX .'archive'. $post_type .'-subtitle',
									'type'    => 'switch', 
									'title'   => esc_html('Display Subtitle On Content', 'plethora-framework'),
									'default'  => 1,
									),	

								array(
									'id'       => METAOPTION_PREFIX .'archive'. $post_type .'-subtitle-text',
									'type'     => 'text',
									'title'    => esc_html('Default Subtitle', 'plethora-framework'), 
									'default'  => $post_type_label . ' '. esc_html('Archive subtitle', 'plethora-framework'),
									'translate' => true,
									),

								array(
									'id'      => METAOPTION_PREFIX .'archive'. $post_type .'-tax-subtitle',
									'type'    => 'button_set', 
									'title'   => esc_html('Selected Taxonomy Subtitle', 'plethora-framework'),
									'desc'   => esc_html('Subtitle behavior when a category OR tag archive is displayed', 'plethora-framework'),
									'default'  => 1,
									'options' => array(
													0 => esc_html('Default Subtitle', 'plethora-framework'),
													1 => esc_html('Taxonomy Description', 'plethora-framework'),
												),
									),	

							array(
						       'id' => 'archive'. $post_type .'-page-end',
						       'type' => 'section',
						       'indent' => false,
						     ),
							array(
						       'id' => 'archive'. $post_type .'-listings-start',
						       'type' => 'section',
						       'title' => $post_type_label . ' '. esc_html('Listings Options', 'plethora-framework'),
						       'indent' => true,
						     ),
								array(
									'id'      => METAOPTION_PREFIX .'archive'. $post_type .'-listtype',
									'type'    => 'button_set', 
									'title'    => $post_type_label . ' '. esc_html('Catalog Type', 'plethora-framework'), 
									'default'  => 'classic',
									'options' => array(
										'classic' => esc_html('Classic', 'plethora-framework'), 
										'compact' => esc_html('Compact', 'plethora-framework'), 
									)
								),	
								array(
									'id'=> METAOPTION_PREFIX .'archive'. $post_type .'-mediadisplay',
									'type' => 'button_set', 
									'title' => esc_html('Featured Media Display', 'plethora-framework'),
									'subtitle' => post_type_supports( $post_type, 'thumbnail' ) ? '<span style="color:green">'. esc_html('This post type supports feature image', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support featured image', 'plethora-framework') .'</div>',
									'default'  => 'inherit',
									'options' => array(
											'inherit' => 'According To Post Format',
											'featuredimage' => 'Force Featured Image Display',
											'hide' => 'Do Not Display',
											),
									),	

								array(
									'id'      => METAOPTION_PREFIX .'archive'. $post_type .'-listing-content',
									'type'    => 'button_set', 
									'title'    => esc_html('Content/Excerpt Display', 'plethora-framework'), 
									'subtitle' => post_type_supports( $post_type, 'editor' ) ? '<span style="color:green">'. esc_html('This post type supports editor content', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support editor content', 'plethora-framework') .'</div>',
									'desc' => esc_html('Displaying content will allow you to display posts containing the WP editor "More" tag.', 'plethora-framework'),
									'default'  => 'content',
									'options' => array(
										'excerpt' => esc_html('Display Excerpt', 'plethora-framework'), 
										'content' => esc_html('Display Content', 'plethora-framework') 
									)
								),	
								array(
									'id'      => METAOPTION_PREFIX .'archive'. $post_type .'-listing-subtitle',
									'type'    => 'switch', 
									'subtitle' => post_type_supports( $post_type, 'title' ) ? '<span style="color:green">'. esc_html('This post type supports title', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support title', 'plethora-framework') .'</div>',
									'title'   => esc_html('Display Subtitle', 'plethora-framework'),
									'default'  => 0,
									'options' => array(
													1 => esc_html('Display', 'plethora-framework'),
													0 => esc_html('Hide', 'plethora-framework'),
												),
									),	
								array(
									'id'      => METAOPTION_PREFIX.'archive'. $post_type .'-info-primarytax',
									'type'    => 'switch', 
									'title'   => sprintf( esc_html('Display Primary Taxonomy Info', 'plethora-framework'), ucfirst( $post_type_label_singular )),
									'desc'  => sprintf( esc_html('You may choose the primary taxonomy to be displayed on: %1sTheme Options > Content > %2s %3s', 'plethora-framework'), '<br><strong>', ucfirst( $post_type_label ), '</strong>'),
									"default" => 1,
									),	
								array(
									'id'      => METAOPTION_PREFIX.'archive'. $post_type .'-info-secondarytax',
									'type'    => 'switch', 
									'title'   => sprintf( esc_html('Display Secondary Taxonomy Info', 'plethora-framework'), ucfirst( $post_type_label_singular )),
									'desc'  => sprintf( esc_html('You may choose the secondary taxonomy to be displayed on: %1sTheme Options > Content > %2s %3s', 'plethora-framework'), '<br><strong>', ucfirst( $post_type_label ), '</strong>'),
									"default" => 1,
									),	

								array(
									'id'      => METAOPTION_PREFIX .'archive'. $post_type .'-info-author',
									'type'    => 'switch', 
									'title'   => esc_html('Display Author Info', 'plethora-framework'),
									'subtitle' => post_type_supports( $post_type, 'author' ) ? '<span style="color:green">'. esc_html('This post type supports authors', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support authors', 'plethora-framework') .'</div>',
									'desc'   => esc_html('Display a non linked author label', 'plethora-framework'),
									"default" => 1,
									),	
								array(
									'id'      => METAOPTION_PREFIX .'archive'. $post_type .'-info-date',
									'type'    => 'switch', 
									'title'   => esc_html('Display Date Info', 'plethora-framework'),
									'desc'   => esc_html('Display a non linked author label', 'plethora-framework'),
									"default" => 1,
									),	
								array(
									'id'      => METAOPTION_PREFIX .'archive'. $post_type .'-info-comments',
									'type'    => 'switch', 
									'title'   => esc_html('Display Comments Count Info', 'plethora-framework'),
									'subtitle' => post_type_supports( $post_type, 'comments' ) ? '<span style="color:green">'. esc_html('This post type supports comments', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support comments', 'plethora-framework') .'</div>',
									"default" => 1,
									),	
								array(
									'id'      => METAOPTION_PREFIX .'archive'. $post_type .'-show-linkbutton',
									'type'    => 'switch', 
									'title'   => esc_html('Display "Read More" Button', 'plethora-framework'),
									"default" => 1,
									),	
								array(
									'id'      =>METAOPTION_PREFIX .'archive'. $post_type .'-show-linkbutton-text',
									'type'    => 'text',
									'required' => array(METAOPTION_PREFIX .'archive'. $post_type .'-show-linkbutton', '=', 1),
									'title' => esc_html('Button Text', 'plethora-framework'),
									"default" => esc_html('Read More', 'plethora-framework'),
									'translate' => true,
								),	
								array(
									'id'      =>METAOPTION_PREFIX .'archive'. $post_type .'-noposts-title',
									'type'    => 'text', 
									'title' => sprintf( esc_html('No %1s Title Text', 'plethora-framework'), $post_type_label ),
									"default" => sprintf( esc_html('No %1s where found!', 'plethora-framework'), strtolower( $post_type_label ) ),
									'translate' => true,
								),	
								array(
									'id'      =>METAOPTION_PREFIX .'archive'. $post_type .'-noposts-description',
									'type'    => 'textarea', 
									'title' => sprintf( esc_html('No %1s Description Text', 'plethora-framework'), $post_type_label ),
									"default" => sprintf( esc_html('Unfortunately, no %1s where found! Please try again soon!', 'plethora-framework'), strtolower( $post_type_label ) ),
									'translate' => true,
								),	
							array(
						       'id' => 'archive'. $post_type .'-listings-end',
						       'type' => 'section',
						       'indent' => false,
						     ),
						)
					);
				}
			}

	    	return $sections;
        }

        public function single_custompost_themeoptions( $sections ) {

        	foreach ( $this->activated_custom_post_types as $post_type => $post_type_obj ) { 

	        	$post_type_label = $post_type_obj->label;
	        	$post_type_label_singular = !empty( $post_type_obj->labels->singular_name ) ? $post_type_obj->labels->singular_name : ucfirst( $post_type_label ) ;
	        	$desc  = sprintf( esc_html( 
	        		'*IMPORTANT: this custom post type is registered via a third party plugin. This tab\'s options will help you configure its single view on %1$s frontend. If you just don\'t need frontend support for %2$s, you have the option to deactivate it on %3$s', 'plethora-framework' ), 
	        		THEME_DISPLAYNAME,
	        		'<strong>'. strtolower( $post_type_label ) .'</strong>',
	        		'<strong>Theme Options > Advanced > Features Library > Post Types Manager</strong>'
				);

				$sections[] = array(
					'title'   => $post_type_label .' *',
					'heading' => esc_html('SINGLE', 'plethora-framework') .' '. strtoupper( $post_type_label_singular ) .' '. esc_html('OPTIONS', 'plethora-framework'),
					'desc'    => $desc,
					'subsection' => true,
					'fields'     => array(

			            array(
							'id'         =>  METAOPTION_PREFIX . $post_type .'-layout',
							'title'      => sprintf( esc_html('Single %1$s Layout', 'plethora-framework' ), ucfirst( $post_type_label_singular ) ),
							'desc'       => sprintf( esc_html('Select main content and sidebar arrangement on single %1$s view', 'plethora-framework' ), $post_type_label_singular ),
							'default'    => 'right_sidebar',
							'type'       => 'image_select',
							'customizer' => array(),
							'options'    => Plethora_Module_Style::get_options_array( array( 
																						'type'   => 'page_layouts',
																						'use_in' => 'redux',
																				   )
										 ),
			            ),
						array(
							'id'      => METAOPTION_PREFIX . $post_type .'-sidebar',
							'type'    => 'select',
							'data'    => 'sidebars',
							'multi'   => false,
							'title'   => sprintf( esc_html('Single %1$s Sidebar', 'plethora-framework' ), ucfirst( $post_type_label_singular ) ),
							'desc'    => esc_html('If empty, the default sidebar will be used. Create as many sidebars you need on Advanced > Sidebars', 'plethora-framework'),
							'default' => 'sidebar-default',
							),

						array(
							'id'=> METAOPTION_PREFIX . $post_type .'-colorset',
							'type' => 'button_set',
	                        'title' => esc_html('Content Section Color Set', 'plethora-framework' ),
	                        'desc' => esc_html('Will define text and background color on content section ( main column + sidebar ), according to selected color set configuration', 'plethora-framework' ),
							'options' => Plethora_Module_Style::get_options_array( array( 'type' 			=> 'color_sets',
																						  'use_in'          => 'redux',
																						  'prepend_options' => array(  'foo' => esc_html('Default', 'plethora-framework') ) ) ),
							'default' => 'foo',
						),

						array(
							'id'       => METAOPTION_PREFIX . $post_type .'-title',
							'type'     => 'switch', 
							'title'    => esc_html('Display Title', 'plethora-framework'),
							'subtitle' => post_type_supports( $post_type, 'title' ) ? '<span style="color:green">'. esc_html('This post type supports title', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support title', 'plethora-framework') .'</div>',
							'default'  => 1,
							'options'  => array(
											1 => esc_html('Display', 'plethora-framework'),
											0 => esc_html('Hide', 'plethora-framework'),
										),
							),

						array(
							'id'      => METAOPTION_PREFIX . $post_type .'-subtitle',
							'type'    => 'switch', 
							'title'   => esc_html('Display Subtitle', 'plethora-framework'),
							'subtitle' => post_type_supports( $post_type, 'title' ) ? '<span style="color:green">'. esc_html('This post type supports title', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support title', 'plethora-framework') .'</div>',
							'default' => 0,
							'options' => array(
											1 => esc_html('Display', 'plethora-framework'),
											0 => esc_html('Hide', 'plethora-framework'),
										),
							),

						array(
							'id'      => METAOPTION_PREFIX . $post_type .'-mediadisplay',
							'type'    => 'switch', 
							'title'   => esc_html('Display Feautured Media', 'plethora-framework'),
							'subtitle' => post_type_supports( $post_type, 'thumbnail' ) ? '<span style="color:green">'. esc_html('This post type supports featured image', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support featured image', 'plethora-framework') .'</div>',
							'default' => 1,
							),	
						array(
							'id'      => METAOPTION_PREFIX . $post_type .'-media-stretch',
							'type'    => 'button_set', 
							'title'   => esc_html('Media Display Ratio', 'plethora-framework'),
							'subtitle' => post_type_supports( $post_type, 'thumbnail' ) ? '<span style="color:green">'. esc_html('This post type supports featured image', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support featured image', 'plethora-framework') .'</div>',
							'desc'   => esc_html('Will be applied on single AND listing view', 'plethora-framework'),
							'default' => 'stretchy_wrapper ratio_2-1',
							'options' => Plethora_Module_Style::get_options_array( array( 
		                                        'type' => 'stretchy_ratios',
		                                        'prepend_options' => array( 'foo_stretch' => esc_html('Native Ratio', 'plethora-framework' ) ),
		                                        )),            
							),	
						array(
							'id'      => METAOPTION_PREFIX . $post_type .'-info-primarytax',
							'type'    => 'switch', 
							'title'   => sprintf( esc_html('Display Primary Taxonomy Info', 'plethora-framework'), ucfirst( $post_type_label_singular )),
							"default" => 1,
							),	
						array(
							'id'      => METAOPTION_PREFIX . $post_type .'-info-primarytax-slug',
							'required' => array( METAOPTION_PREFIX . $post_type .'-info-primarytax','=', 1),						
							'type'    => 'select', 
							'title'   => esc_html('Set Primary Taxonomy Label', 'plethora-framework'),
							'desc'   => esc_html('You should select a taxonomy that is associated with the specific post type. Naturally, non associated taxonomies will not be displayed.', 'plethora-framework'),
							'data' => 'taxonomies',
							'args' => array( 'public' => 1 ),
							"default" => 'category',
							),	
						array(
							'id'      => METAOPTION_PREFIX . $post_type .'-info-secondarytax',
							'type'    => 'switch', 
							'title'   => sprintf( esc_html('Display Secondary Taxonomy Info', 'plethora-framework'), ucfirst( $post_type_label_singular )),
							"default" => 1,
							),	
						array(
							'id'      => METAOPTION_PREFIX . $post_type .'-info-secondarytax-slug',
							'required' => array( METAOPTION_PREFIX . $post_type .'-info-secondarytax','=', 1),						
							'type'    => 'select', 
							'title'   => esc_html('Set Secondary Taxonomy Label', 'plethora-framework'),
							'desc'   => esc_html('You should select a taxonomy that is associated with the specific post type. Naturally, non associated taxonomies will not be displayed.', 'plethora-framework'),
							'data' => 'taxonomies',
							'args' => array( 'public' => 1 ),
							"default" => 'post_tag',
							),
						array(
							'id'      => METAOPTION_PREFIX . $post_type .'-author',
							'type'    => 'switch', 
							'title'   => esc_html('Display Author Info', 'plethora-framework'),
							'subtitle' => post_type_supports( $post_type, 'author' ) ? '<span style="color:green">'. esc_html('This post type supports authors', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support author', 'plethora-framework') .'</div>',
							'desc'   => esc_html('Display a non linked author label', 'plethora-framework'),
							"default" => 1,
							),		
						array(
							'id'      => METAOPTION_PREFIX . $post_type  .'-date',
							'type'    => 'switch', 
							'title'   => esc_html('Display Date Info', 'plethora-framework'),
							'desc'   => esc_html('Display a non linked date label', 'plethora-framework'),
							"default" => 1,
							),		
						 array(
							'id'      => METAOPTION_PREFIX . $post_type  .'-comments',
							'type'    => 'switch', 
							'title'   => esc_html('Display Comments Count Info', 'plethora-framework'),
							'subtitle' => post_type_supports( $post_type, 'comments' ) ? '<span style="color:green">'. esc_html('This post type supports comments', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support comments', 'plethora-framework') .'</div>',
							"default" => 1,
							),		
					)
				);
			}
			return $sections;
        }

        public function single_custompost_metabox( $metaboxes ) {

        	foreach ( $this->activated_custom_post_types as $post_type => $post_type_obj ) { 

	        	$post_type_label = $post_type_obj->label;
	        	$post_type_label_singular = !empty( $post_type_obj->labels->singular_name ) ? $post_type_obj->labels->singular_name : ucfirst( $post_type_label ) ;

		    	$sections_content = array(
			        'title' => esc_html('Content', 'plethora-framework'),
			        'heading' => esc_html('CONTENT OPTIONS', 'plethora-framework'),
			        'icon_class'    => 'icon-large',
					'icon'       => 'el-icon-lines',
			        'fields'        => array(

						array(
					       'id' => 'post-singleview-start',
					       'type' => 'section',
					       'title' => esc_html('Single Post View', 'plethora-framework'),
					       'subtitle' => esc_html('These options affect this post\'s display when displayed on its single page', 'plethora-framework'),
					       'indent' => true,
					     ),
				            array(
				                'id'        =>  METAOPTION_PREFIX . $post_type  .'-layout',
				                'title'     => esc_html('Select Layout', 'plethora-framework' ),
				                'type'      => 'image_select',
								'options' => Plethora_Module_Style::get_options_array( array( 
																							'type'   => 'page_layouts',
																							'use_in' => 'redux',
																					   )
											 ),
				                ),
			                array(
			                    'id'=> METAOPTION_PREFIX . $post_type  .'-sidebar',
			                    'type' => 'select',
			                    'data' => 'sidebars',
			                    'multi' => false,
			                    'title' => esc_html('Select Sidebar', 'plethora-framework'), 
			                    ),
							array(
								'id'=> METAOPTION_PREFIX . $post_type  .'-colorset',
								'type' => 'button_set',
		                        'title' => esc_html('Content Section Color Set', 'plethora-framework' ),
	                        	'desc' => esc_html('Will define text and background color on content section ( main column + sidebar ), according to selected color set configuration', 'plethora-framework' ),
								'options' => Plethora_Module_Style::get_options_array( array( 'type' 			=> 'color_sets',
																							  'use_in'          => 'redux',
																							  'prepend_options' => array(  'foo' => esc_html('Default', 'plethora-framework') ) ) ),
							),
							array(
								'id'      => METAOPTION_PREFIX . $post_type  .'-title',
								'type'    => 'switch', 
								'title'   => esc_html('Display Title', 'plethora-framework'),
								'subtitle' => post_type_supports( $post_type, 'title' ) ? '<span style="color:green">'. esc_html('This post type supports title/subtitle', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support title', 'plethora-framework') .'</div>',
								'options' => array(
												1 => esc_html('Display', 'plethora-framework'),
												0 => esc_html('Hide', 'plethora-framework'),
											),
								),	
							array(
								'id'      => METAOPTION_PREFIX . $post_type  .'-subtitle',
								'type'    => 'switch', 
								'title'   => esc_html('Display Subtitle', 'plethora-framework'),
								'subtitle' => post_type_supports( $post_type, 'title' ) ? '<span style="color:green">'. esc_html('This post type supports title/subtitle', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support title', 'plethora-framework') .'</div>',
								'options' => array(
												1 => esc_html('Display', 'plethora-framework'),
												0 => esc_html('Hide', 'plethora-framework'),
											),
								),	
							array(
								'id'       => METAOPTION_PREFIX . $post_type  .'-subtitle-text',
								'type'     => 'text',
								'title'    => esc_html('Subtitle', 'plethora-framework'), 
								'subtitle' => post_type_supports( $post_type, 'title' ) ? '<span style="color:green">'. esc_html('This post type supports title/subtitle', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support title', 'plethora-framework') .'</div>',
								'translate' => true,
								),
							array(
								'id'      => METAOPTION_PREFIX . $post_type  .'-mediadisplay',
								'type'    => 'switch', 
								'title'   => esc_html('Display Featured Media', 'plethora-framework'),
								'subtitle' => post_type_supports( $post_type, 'thumbnail' ) ? '<span style="color:green">'. esc_html('This post type supports featured image', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support featured image', 'plethora-framework') .'</div>',
								),	

							array(
								'id'      => METAOPTION_PREFIX . $post_type  .'-media-stretch',
								'type'    => 'button_set', 
								'title'   => esc_html('Media Display Ratio', 'plethora-framework'),
								'subtitle' => post_type_supports( $post_type, 'thumbnail' ) ? '<span style="color:green">'. esc_html('This post type supports featured image', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support featured image', 'plethora-framework') .'</div>',
								'desc'   => esc_html('Will be applied on single AND listing view', 'plethora-framework'),
								'options' => Plethora_Module_Style::get_options_array( array( 
			                                        'type' => 'stretchy_ratios',
		                                        	'prepend_options' => array( 'foo_stretch' => esc_html('Native Ratio', 'plethora-framework' ) ),
			                                        )),            
								),	

							array(
								'id'    => METAOPTION_PREFIX . $post_type .'-info-primarytax',
								'type'  => 'switch', 
								'title' => sprintf( esc_html('Display Primary Taxonomy Info', 'plethora-framework'), ucfirst( $post_type_label_singular )),
								'desc'  => sprintf( esc_html('You may choose the primary taxonomy to be displayed on: %1sTheme Options > Content > %2s %3s', 'plethora-framework'), '<br><strong>', ucfirst( $post_type_label ), '</strong>'),
								),	
							array(
								'id'      => METAOPTION_PREFIX . $post_type .'-info-secondarytax',
								'type'    => 'switch', 
								'title'   => sprintf( esc_html('Display Secondary Taxonomy Info', 'plethora-framework'), ucfirst( $post_type_label_singular )),
								'desc'  => sprintf( esc_html('You may choose the secondary taxonomy to be displayed on: %1sTheme Options > Content > %2s %3s', 'plethora-framework'), '<br><strong>', ucfirst( $post_type_label ), '</strong>'),
								),	

							array(
								'id'=> METAOPTION_PREFIX . $post_type  .'-author',
								'type' => 'switch', 
								'title' => esc_html('Display Author Info', 'plethora-framework'),
								'subtitle' => post_type_supports( $post_type, 'author' ) ? '<span style="color:green">'. esc_html('This post type supports authors', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support authors', 'plethora-framework') .'</div>',
								),	
							array(
								'id'=> METAOPTION_PREFIX . $post_type  .'-date',
								'type' => 'switch', 
								'title' => esc_html('Display Date Info', 'plethora-framework'),
								),	
							array(
								'id'=> METAOPTION_PREFIX . $post_type  .'-comments',
								'type' => 'switch', 
								'title' => esc_html('Display Comments Count Info', 'plethora-framework'),
								'subtitle' => post_type_supports( $post_type, 'comments' ) ? '<span style="color:green">'. esc_html('This post type supports comments', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support comments', 'plethora-framework') .'</div>',
								),	
						array(
						    'id'     => 'post-singleview-end',
						    'type'   => 'section',
						    'indent' => false,
						),					

						array(
					       'id' => 'post-listview-start',
					       'type' => 'section',
					       'title' => esc_html('Archive Listing View', 'plethora-framework'),
					       'subtitle' => $post_type_obj->has_archive ? esc_html('These options affect this post\'s display when displayed on post archive (blog)', 'plethora-framework') : '<span style="color:red">'. esc_html('This custom post type does not support archive view...so you should ignore those options here.', 'plethora-framework') .'</div>' ,
					       'indent' => true,
					     ),
							array(
								'id'=> METAOPTION_PREFIX .'archive'. $post_type  .'-mediadisplay',
								'type' => 'button_set', 
								'title' => esc_html('Featured Media Display', 'plethora-framework'),
								'subtitle' => post_type_supports( $post_type, 'thumbnail' ) ? '<span style="color:green">'. esc_html('This post type supports featured image', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support featured image', 'plethora-framework') .'</div>',
								'desc' => '<strong>'. esc_html('According To Post Format', 'plethora-framework') .'</strong> '. esc_html('will display the featured video/audio in posts list (according on its post format).', 'plethora-framework') . esc_html('You can set the post format on Format box on the right', 'plethora-framework'),
								'options' => array(
										'inherit' => 'According To Post Format',
										'featuredimage' => 'Force Featured Image Display',
										'hide' => 'Do Not Display',
										),
								),	

							array(
								'id'      => METAOPTION_PREFIX .'archive'. $post_type  .'-listing-content',
								'type'    => 'button_set', 
								'title'    => esc_html('Content/Excerpt Display', 'plethora-framework'), 
								'subtitle' => post_type_supports( $post_type, 'editor' ) ? '<span style="color:green">'. esc_html('This post type supports editor content', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support editor content', 'plethora-framework') .'</div>',
								'descr' => esc_html('Displaying content will allow you to display posts containing the WP editor "More" tag.', 'plethora-framework'),
								'options' => array(
									'excerpt' => esc_html('Display Excerpt', 'plethora-framework'), 
									'content' => esc_html('Display Content', 'plethora-framework') 
								)
							),	
							array(
								'id'      => METAOPTION_PREFIX .'archive'. $post_type  .'-listing-subtitle',
								'type'    => 'switch', 
								'title'   => esc_html('Display Subtitle', 'plethora-framework'),
								'subtitle' => post_type_supports( $post_type, 'title' ) ? '<span style="color:green">'. esc_html('This post type supports title/subtitle', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support title/subtitle', 'plethora-framework') .'</div>',
								'options' => array(
												1 => esc_html('Display', 'plethora-framework'),
												0 => esc_html('Hide', 'plethora-framework'),
											),
								),	
							array(
								'id'    => METAOPTION_PREFIX .'archive'. $post_type .'-info-primarytax',
								'type'  => 'switch', 
								'title' => sprintf( esc_html('Display Primary Taxonomy Info', 'plethora-framework'), ucfirst( $post_type_label_singular )),
								'desc'  => sprintf( esc_html('You may choose the primary taxonomy to be displayed on: %1sTheme Options > Content > %2s %3s', 'plethora-framework'), '<br><strong>', ucfirst( $post_type_label ), '</strong>'),
								),	
							array(
								'id'      => METAOPTION_PREFIX .'archive'. $post_type .'-info-secondarytax',
								'type'    => 'switch', 
								'title'   => sprintf( esc_html('Display Secondary Taxonomy Info', 'plethora-framework'), ucfirst( $post_type_label_singular )),
								'desc'  => sprintf( esc_html('You may choose the secondary taxonomy to be displayed on: %1sTheme Options > Content > %2s %3s', 'plethora-framework'), '<br><strong>', ucfirst( $post_type_label ), '</strong>'),
								),	
				            array(
				              'id'      => METAOPTION_PREFIX .'archive'. $post_type  .'-info-author',
				              'type'    => 'switch', 
							  'subtitle' => post_type_supports( $post_type, 'author' ) ? '<span style="color:green">'. esc_html('This post type supports authors', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support authors', 'plethora-framework') .'</div>',
				              'title'   => esc_html('Display Author Info', 'plethora-framework'),
				              ),  
				            array(
				              'id'      => METAOPTION_PREFIX .'archive'. $post_type  .'-info-date',
				              'type'    => 'switch', 
				              'title'   => esc_html('Display Date Info', 'plethora-framework'),
				              ),  
				            array(
				              'id'      => METAOPTION_PREFIX .'archive'. $post_type  .'-info-comments',
				              'type'    => 'switch', 
				              'title'   => esc_html('Display Comments Count Info', 'plethora-framework'),
							  'subtitle' => post_type_supports( $post_type, 'comments' ) ? '<span style="color:green">'. esc_html('This post type supports comments', 'plethora-framework') .'</div>' : '<span style="color:darkorange">'. esc_html('This post type does not support comments', 'plethora-framework') .'</div>',
				              ),  
							array(
								'id'      => METAOPTION_PREFIX .'archive'. $post_type  .'-show-linkbutton',
								'type'    => 'switch', 
								'title'   => esc_html('Display "Read More" Button', 'plethora-framework'),
								),	
						array(
						    'id'     => 'post-listview-end',
						    'type'   => 'section',
						    'indent' => false,
							),					
						)
		        	);

					$sections = array();
					$sections[] = $sections_content;
					if ( has_filter( 'plethora_metabox_single'. $post_type  .'') ) {

						$sections = apply_filters( 'plethora_metabox_single'. $post_type  .'', $sections );
					}

				    $metaboxes[] = array(
				        'id'            => 'metabox-single-'. $post_type  .'',
				        'title'         => $post_type_label_singular . ' '. esc_html('Post Options', 'plethora-framework' ),
				        'post_types'    => array( $post_type ),
				        'position'      => 'normal', // normal, advanced, side
				        'priority'      => 'high', // high, core, default, low
				        'sections'      => $sections,
				    );
			}
	    	return $metaboxes;
        }
	}
}