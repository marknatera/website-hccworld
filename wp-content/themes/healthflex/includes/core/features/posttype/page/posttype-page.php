<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Page Type Feature Class. Page is a native WP feature...however 
this class is used to create page options and other customizations

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Posttype') && !class_exists('Plethora_Posttype_Page') ) {  
 
	/**
	 * @package Plethora Framework
	 */

	class Plethora_Posttype_Page {

		// Plethora Index variables
		public static $feature_title         = "Page Post Type";								// Feature display title  (string)
		public static $feature_description   = "Contains all page related post configuration";	// Feature display description (string)
		public static $theme_option_control  = false;											// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = false;											// Default activation option status ( boolean )
		public static $theme_option_requires = array();											// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;											// Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;											// Additional method invocation ( string/boolean | method name or false )

		// Auxilliary variables
		private $post_type_slug = 'page';
		public $page_archive_posttype;
		public $page_archive_posttype_object;

		// Default option values ( for easy ext class overrides )
		public $page_layout     = 'no_sidebar';
		public $page_sidebar    = 'sidebar-pages';
		public $page_title      = 1;
		public $page_subtitle   = 1;
		public $one_pager_speed = 300;		

		public function __construct() {

			// Single page Theme Options
			add_filter( 'plethora_themeoptions_content', array($this, 'single_themeoptions'), 100);

			// Single page Metaboxes. Hook on 'plethora_metabox_add' filter
			add_filter( 'plethora_metabox_add', array($this, 'single_metabox'));

			// Single page Metaboxes. Hook on 'plethora_metabox_add' filter
			add_action( 'wp_enqueue_scripts', array( $this, 'one_pager') );	  // ONE PAGER SCROLLING FUNCTIONALITY ( always enqueue features on 20 )
		}

		/** 
		* Returns theme options configuration. Collects global and theme-specific fields
		* Hooked @ 'plethora_themeoptions_content'
		*/
        public function single_themeoptions( $sections ) {

        	// Get permanent theme options configuration
            $fields_global  = $this->single_themeoptions_global();

         	// Get theme-specific theme options configuration
           	$fields_theme = $this->single_themeoptions_ext();

           	// Merge fields and return $sections
           	$fields = array_merge_recursive( $fields_global, $fields_theme );
			$sections[] = array(
					'title'      => esc_html('Pages', 'plethora-framework'),
					'heading'    => esc_html('PAGES OPTIONS', 'plethora-framework'),
					'desc'       => esc_html('These will be the default values for a new post you create. You have the possibility to override most of these settings on each post separately.', 'plethora-framework') . '<br><span style="color:red;">'. esc_html('Important: ', 'plethora-framework') . '</span>'. esc_html('changing a default value here will not affect options that were customized per post. In example, if you change a previously default "full width" to "right sidebar" layout this will switch all full width posts to right sidebar ones. However it will not affect those that were customized, per post, to display a left sidebar.', 'plethora-framework') ,
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
							'id'      =>  METAOPTION_PREFIX .'page-layout',
							'title'   => esc_html('Select Layout', 'plethora-framework' ),
							'type'    => 'image_select',
							'default' => $this->page_layout,
							'options' => Plethora_Module_Style::get_options_array( array( 
																						'type'   => 'page_layouts',
																						'use_in' => 'redux',
																				   )
										 ),
			                ),
		                array(
							'id'       => METAOPTION_PREFIX .'page-sidebar',
							'type'     => 'select',
							'required' => array(METAOPTION_PREFIX .'page-layout','equals',array('right_sidebar','left_sidebar')),  
							'data'     => 'sidebars',
							'multi'    => false,
							'default'  => $this->page_sidebar,
							'title'    => esc_html('Select Sidebar', 'plethora-framework'), 
		                    ),
						array(
							'id'=> METAOPTION_PREFIX .'page-colorset',
							'type' => 'button_set',
	                        'title' => esc_html('Content Section Color Set', 'plethora-framework' ),
	                        'desc' => esc_html('Will define text and background color on content section ( main column + sidebar ), according to selected color set configuration', 'plethora-framework' ),
							'options' => Plethora_Module_Style::get_options_array( array( 'type' 			=> 'color_sets',
																						  'use_in'          => 'redux',
																						  'prepend_options' => array(  'foo' => esc_html('Default', 'plethora-framework') ) ) ),
							'default' => 'foo',
						),
						array(
							'id'      => METAOPTION_PREFIX .'page-title',
							'type'    => 'switch', 
							'title'   => esc_html('Display Title', 'plethora-framework'),
							'default' => $this->page_title,
							'options' => array(
											1 => esc_html('Display', 'plethora-framework'),
											0 => esc_html('Hide', 'plethora-framework'),
										),
							),	
						array(
							'id'      => METAOPTION_PREFIX .'page-subtitle',
							'type'    => 'switch', 
							'title'   => esc_html('Display Subtitle', 'plethora-framework'),
							'default' => $this->page_subtitle,
							'options' => array(
											1 => esc_html('Display', 'plethora-framework'),
											0 => esc_html('Hide', 'plethora-framework'),
										),
							),	

						array(
							'id'       => METAOPTION_PREFIX .'one-pager-speed',
							'type'     => 'spinner', 
							'title'    => esc_html('One Page Scrolling Speed', 'plethora-framework'),
							"min"      => 100,
							"step"     => 100,
							"max"      => 4000,
							"default"  => $this->one_pager_speed,
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

			// Cannot use global $post, so we are getting the 'post' url parameter
			$postid = isset( $_GET['post'] ) && is_numeric( $_GET['post'] )  ? $_GET['post'] : 0;
			// Get page IDs for all supported archives
			$archive_page_ids = Plethora_Theme::get_archive_pages();
			// Normal page metabox should be displayed only if this is NOT an archive page
			if ( ! in_array( $postid, $archive_page_ids) || $postid === 0 ) {  

				$sections = array();
				$sections[] = $sections_content;
				if ( has_filter( 'plethora_metabox_singlepage') ) {

					$sections = apply_filters( 'plethora_metabox_singlepage', $sections );
				}

			    $metaboxes[] = array(
			        'id'            => 'metabox-single-page',
			        'title'         => esc_html('Page Options', 'plethora-framework' ),
			        'post_types'    => array( 'page' ),
			        'position'      => 'normal', // normal, advanced, side
			        'priority'      => 'high', // high, core, default, low
			        'sidebar'       => false, // enable/disable the sidebar in the normal/advanced positions
			        'sections'      => $sections,
			    );

			} else { // Display archive page metabox ( depending on the post )

				/* So, since this is an archive page, we have to add the archive metabox for this post
				   On archive pages, we just place an empty metabox for global metabox tabs hooking
				*/
	     		foreach ( $archive_page_ids as $post_type=>$page_id ) {

	     			if ( $postid === $page_id ) {

					    // Update variables for additional configuration
					    $posttype_object = get_post_type_object($post_type);
					    // print_r($posttype_object);
						$this->page_archive_posttype = $post_type;
						$this->page_options_text     = !empty( $posttype_object->has_archive ) ? ucfirst( $posttype_object->has_archive ) .' Page Options' : esc_html('Blog Page Options', 'plethora-framework');
						$this->page_view_text        = !empty( $posttype_object->has_archive ) ? 'View ' . ucfirst( $posttype_object->has_archive ) : esc_html('View Blog', 'plethora-framework');
						$this->page_tab_text         = !empty( $posttype_object->has_archive ) ? ucfirst( $posttype_object->has_archive ) : esc_html('Blog', 'plethora-framework');

					    // Create the archive metabox
						$sections = array();
						if ( has_filter( 'plethora_metabox_archive'. $post_type ) ) {

								$sections = apply_filters( 'plethora_metabox_archive'. $post_type, $sections );
						}

					    $metaboxes[] = array(
					        'id'            => 'metabox-archive-'. $post_type ,
						    'title'         => $this->page_options_text . ' <small style="color:red;">' . sprintf( esc_html('| For more advanced content options, please visit: Theme Options > Content > %s', 'plethora-framework'), $this->page_tab_text ) . '</small>',
					        'post_types'    => array( 'page' ),
					        'position'      => 'normal', // normal, advanced, side
					        'priority'      => 'high', // high, core, default, low
					        'sidebar'       => false, // enable/disable the sidebar in the normal/advanced positions
					        'sections'      => $sections,
					    );

				  		// Remove content editor
					    add_action( 'admin_init', array( $this, 'hide_editor'), 20);
				  		// Remove default page metaboxes
					    add_action( 'admin_menu', array( $this, 'remove_metaboxes'));
					    // Changing Edit screen labels
					    add_action( 'init', array( $this, 'change_admin_screen_texts'), 999);

				    }
				}
			}

	    	return $metaboxes;
        }

		/** 
		* Returns global fields for the single options configuration
		*/
        public function single_metabox_global() {

        	$fields = array(
		            	array(
							'id'      =>  METAOPTION_PREFIX .'page-layout',
							'title'   => esc_html('Select Layout', 'plethora-framework' ),
							'type'    => 'image_select',
							'options' => Plethora_Module_Style::get_options_array( array( 
																						'type'   => 'page_layouts',
																						'use_in' => 'redux',
																				   )
										 ),
		                ),

		                array(
							'id'       => METAOPTION_PREFIX .'page-sidebar',
							'type'     => 'select',
							'required' => array(METAOPTION_PREFIX .'page-layout','equals',array('right_sidebar','left_sidebar')),  
							'data'     => 'sidebars',
							'multi'    => false,
							'title'    => esc_html('Select Sidebar', 'plethora-framework'), 
						),

						array(
							'id'      => METAOPTION_PREFIX .'page-colorset',
							'type'    => 'button_set',
							'title'   => esc_html('Content Section Color Set', 'plethora-framework' ),
							'desc'    => esc_html('Will define text and background color on content section ( main column + sidebar ), according to selected color set configuration', 'plethora-framework' ),
							'options' => Plethora_Module_Style::get_options_array( array( 'type' 			=> 'color_sets',
																						  'use_in'          => 'redux',
																						  'prepend_options' => array(  'foo' => esc_html('Default', 'plethora-framework') ) ) ),
						),
						array(
							'id'      => METAOPTION_PREFIX .'page-title',
							'type'    => 'switch', 
							'title'   => esc_html('Display Title', 'plethora-framework'),
							'options' => array(
											1 => esc_html('Display', 'plethora-framework'),
											0 => esc_html('Hide', 'plethora-framework'),
										),
							),	
						array(
							'id'      => METAOPTION_PREFIX .'page-subtitle',
							'type'    => 'switch', 
							'title'   => esc_html('Display Subtitle', 'plethora-framework'),
							'options' => array(
											1 => esc_html('Display', 'plethora-framework'),
											0 => esc_html('Hide', 'plethora-framework'),
										),
							),	
						array(
							'id'        => METAOPTION_PREFIX .'page-subtitle-text',
							'type'      => 'text',
							'title'     => esc_html('Subtitle', 'plethora-framework'), 
							'translate' => true,
							),
						array(
							'id'       => METAOPTION_PREFIX .'one-pager-speed',
							'type'     => 'spinner', 
							'title'    => esc_html('One Page Scrolling Speed', 'plethora-framework'),
							"min"      => 100,
							"step"     => 100,
							"max"      => 4000,
							),	

				);

			return $fields;
        }

		/** 
		* Returns theme-specific fields for the single metabox configuration
		* Do not use in this class, for extension classes only
		*/
        public function single_metabox_ext() { return array(); }


 	# HELPER METHODS START ->
 	       
		/** 
		* Sets one pager JS variable configuration
		*/
        public function one_pager() {

      		Plethora_Theme::set_themeconfig( "GENERAL", array('onePagerScrollSpeed' => intval( Plethora_Theme::option( METAOPTION_PREFIX .'one-pager-speed', 300 ) ) ) );
	    }

		/** 
		* Removes main content editor ( used for archive metaboxes )
		*/
		public function hide_editor() { 

			if ( $this->page_archive_posttype === 'post' ) {

				remove_post_type_support('post', 'editor');
			}
		}


		/** 
		* Add main content editor ( used for archive metaboxes )
		*/
		public function display_editor() { 

			if ( $this->page_archive_posttype === 'post' ) {

				add_post_type_support('post', 'editor');
			}
		}

		/** 
		* Removes default page metaboxes
		*/
		public function remove_metaboxes() { 

			  // remove_meta_box('postexcerpt', 'page', 'normal');
			  // remove_meta_box('trackbacksdiv', 'page', 'normal');
			  // remove_meta_box('postcustom', 'page', 'normal');
			  // remove_meta_box('commentstatusdiv', 'page', 'normal');
			  // remove_meta_box('commentsdiv', 'page', 'normal');
			  // remove_meta_box('revisionsdiv', 'page', 'normal');
			  // remove_meta_box('authordiv', 'page', 'normal');
			  // remove_meta_box('sqpt-meta-tags', 'page', 'normal');
		}

		/**
		* Modify registered post type labels
		*/
		public function change_admin_screen_texts() {
			
			global $wp_post_types;
			$wp_post_types['page']->labels->edit_item = $this->page_options_text; 		
			$wp_post_types['page']->labels->view_item = $this->page_view_text;		
		} 	  

 	# HELPER METHODS END <-
	}
}	
