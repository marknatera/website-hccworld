<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

Description: Inlcudes theme options, metaboxes and LESS configuration methods.

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists( 'Plethora_Themeoptions' ) ):

	class Plethora_Themeoptions {

	    public $args        = array();
	    public $sections    = array();
	    public $theme;
	    public $ReduxFramework;


		public function __construct() { 

	        if (!class_exists('ReduxFramework')) {
	            return;
	        }
	        add_action('init', array( $this, 'initSettings'), 20);
		}

		
		public function removeDemoModeLink() { 

		    if ( class_exists('ReduxFrameworkPlugin') ) {
		        remove_filter( 'plugin_row_meta', array( ReduxFrameworkPlugin::get_instance(), 'plugin_metalinks'), null, 2 );
		    }
		    if ( class_exists('ReduxFrameworkPlugin') ) {
		        remove_action('admin_notices', array( ReduxFrameworkPlugin::get_instance(), 'admin_notices' ) );    
		    }
		
		}		

		public function setArguments() {

			$theme = wp_get_theme(); // For use with some settings. Not necessary.

			$args = array();
			$args['opt_name']			= THEME_OPTVAR;				// Theme options name & the global variable in which data options are retrieved via code
			$args['display_name']	    = THEME_DISPLAYNAME;		// Set the title appearing at the top of the options panel 
			$args['display_version']	= 'ver.'. THEME_VERSION ;	// Set the version number that appears after the title at the top of the options panel.
			$args['menu_type']			= 'menu';					// Set whether or not the admin menu is displayed.  Accepts either menu (default) or submenu.
			$args['allow_sub_menu']		= true;						// Enable/disable labels display below the admin menu
			$args['menu_title']			= THEME_OPTIONSPAGEMENU; // Set the WP admin menu title 
			$args['page_title']		    = THEME_OPTIONSPAGETITLE ; // Set the WP admin page title (appearing on browsers page title)
	        // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth to generate a Google API key to use this feature.
			// $args['google_api_key']  	= 'AIzaSyAhCFO56k_xL212g8j2LK88wK0I_CRwzDE';	// Set an API key for Google Webfonts usage (more on: https://developers.google.com/fonts/docs/developer_api)
			$args['google_update_weekly'] = false;					// In case this is set to true, you HAVE to set your own private API key...I suppose that you don't want your website fail to display its fonts!  (more on: https://developers.google.com/fonts/docs/developer_api)
			$args['async_typography']  	= false;                    // Use a asynchronous font on the front end or font string
			$args['admin_bar']			= true;						// Enable/disable Plethora settings menu on admin bar
			$args['admin_bar_icon']		= 'dashicons-admin-generic';	// Set the icon appearing in the admin bar, next to the menu title
			$args['dev_mode']			= false;					// Enable/disable Dev Tab (view class settings / info in panel)
			$args['customizer']			= false;						// Enable/disable basic WordPress customizer support
			// $args['open_expanded']		= true;						// Allow you to start the panel in an expanded way initially.
			// $args['disable_save_warn']	= true;						// Disable the save warning when a user changes a field

		// ARGUMENTS --> EXTRA FEATURES
			$args['page_priority']		= '990'; 					// Set the order number specifying where the menu will appear in the admin area
			$args['page_parent']		= ''; 						// Set where the options menu will be placed on the WordPress admin sidebar. For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
			$args['page_permissions']	= 'manage_options';			// Set the permission level required to access the options panel.  For a complete list of roles and capabilities, please visit this page:  https://codex.wordpress.org/Roles_and_Capabilities
			$args['menu_icon']			= PLE_CORE_ASSETS_URI .'/images/plethora/plethora20x20.png'; 						// Set the WP admin menu icon 
			// $args['last_tab']			= '0';						// Set the default tab to open when the page is loaded
			$args['page_icon']			= 'icon-themes';			// Set the icon appearing in the admin panel, next to the menu title
			$args['page_slug']			= THEME_OPTIONSPAGE;		// Set the page slug (i.e. wp-admin/themes.php?page=plethora_settings)
			$args['save_defaults']		= true;						// Set whether or not the default values are saved to the database on load, before Save Changes is clicked
			$args['default_show']		= false;					// Enable/disable default value display by the field title.
			$args['default_mark']		= '*';						// Setup symbol to be displayed on default valued fields (e.g an asterisk *)
			$args['show_import_export']	= true;						// Enable/disable Import/Export Tab

		// ARGUMENTS --> ADMIN BAR LINKS
			$args['admin_bar_links'][] = array( 'id' => THEME_SLUG .'-demo', 'href' => 'http://plethorathemes.com/healthflex/', 'title' => esc_html__( 'Online demo pages', 'healthflex' ));
			$args['admin_bar_links'][] = array( 'id' => THEME_SLUG .'-documentation', 'href' => 'http://doc.plethorathemes.com/healthflex/', 'title' => esc_html__( 'Online documentation', 'healthflex' ));
			$args['admin_bar_links'][] = array( 'id' => THEME_SLUG .'-support', 'href' => 'http://plethorathemes.com/support/create-ticket/', 'title' => esc_html__( 'Create Support Ticket', 'healthflex' ));

		// ARGUMENTS --> ADVANCED FEATURES
			$args['transient_time']		= 60 * MINUTE_IN_SECONDS;	// Set the amount of time to assign to transient values used.
			$args['output']				= true;						// Enable/disable dynamic CSS output. When set to false, Google fonts are also disabled
	        $args['output_tag'] 		= true;                     // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
			$args['footer_credit']		= esc_html__('Plethora Theme Options panel. Based on Redux Framework', 'healthflex');						// Set the text to be displayed at the bottom of the options panel, in the footer across from the WordPress version (where it normally says 'Thank you for creating with WordPress') (HTML is allowed)

	    // NEW ARGUMENTS
			$args['ajax_save']     = true;                     
			$args['use_cdn']       = false;                    
			$args['update_notice'] = false;                    
			$args['disable_tracking'] = false;                    

		// ARGUMENTS --> FUTURE ( Not in use yet, but reserved or partially implemented. Use at your own risk. )
			// $args['database']			= '';						// possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
			// $args['system_info']		= false;					// Remove

			
		// ARGUMENTS --> PLETHORA SOCIAL ICONS (displayed in footer)

			$args['share_icons'][]   = array( 'url' => 'https://twitter.com/plethorathemes', 'title' => esc_html__('Follow Plethora on Twitter', 'healthflex'), 'icon' => 'el-icon-twitter' );
			$args['share_icons'][]   = array( 'url' => 'https://www.facebook.com/plethorathemes', 'title' => esc_html__('Find Plethora on Facebook', 'healthflex'), 'icon' => 'el-icon-facebook' );
			$args['share_icons'][]   = array( 'url' => 'https://www.youtube.com/channel/UCRk3LXfZj7CpEwTjaI0BLDQ', 'title' => esc_html__('Watch Plethora channel on YouTube', 'healthflex'), 'icon' => 'el-icon-youtube' );

		// ARGUMENTS --> HELP FEATURES CONFIGURATION
			// Wordpress Help Panel features
			$args['hints']				= array(
	                    'icon'          => 'icon-question-sign',
	                    'icon_position' => 'right',
	                    'icon_color'    => '#D7B574',
	                    'icon_size'     => 'normal',
	                    'tip_style'     => array(
	                        'color'         => 'light',
	                        'shadow'        => true,
	                        'rounded'       => false,
	                        'style'         => 'bootstrap',
	                    ),
	                    'tip_position'  => array(
	                        'my' => 'bottom right',
	                        'at' => 'top left',
	                    ),
	                    'tip_effect'    => array(
	                        'show'          => array(
	                            'effect'        => 'fade',
	                            'duration'      => '50',
	                            'event'         => 'mouseover',
	                        ),
	                        'hide'      => array(
	                            'effect'    => 'fade',
	                            'duration'  => '50',
	                            'event'     => 'click mouseleave',
	                        ),
	                    ),
	                );

			$this->args = $args;
				
		}

//// THEME OPTIONS PANEL CONFIGURATION BEGINS

		public function initSettings() {

			// ARGUMENTS --> GENERAL CONFIGURATION
			    $this->setArguments();

		    if (!isset($this->args['opt_name'])) { // No errors please
		                return;
			}

			$this->set_theme_options_tab_hooks(); // Always first in order for hook points to work
			$this->set_theme_options_hookpoints();
			$this->ReduxFramework = new ReduxFramework($this->sections, $this->args);

		}
	    
	// SET SECTION HOOKPOINTS -> START

		public function set_theme_options_tab_hooks() {

		// SECTIONS CONFIGURATION
		    // General Section ( adding filters applied to 'plethora_themeoptions_general')
		    add_filter( 'plethora_themeoptions_general', array($this, 'subsection_colorsets'), 10);			// Color sets subsection
		    add_filter( 'plethora_themeoptions_general', array($this, 'subsection_typography'), 10);		// Typography subsection
		    add_filter( 'plethora_themeoptions_general', array($this, 'subsection_favicons'), 998);			// Favicons subsection
		    add_filter( 'plethora_themeoptions_general', array($this, 'subsection_misc'), 999);			// Other subsection
		    // Header Section ( adding filters applied to 'plethora_themeoptions_header')
		    add_filter( 'plethora_themeoptions_header', array($this, 'subsection_headermain'), 10);		// Header Layout subsection
		    add_filter( 'plethora_themeoptions_header', array($this, 'subsection_headerlogo'), 10);				// Logo subsection
		    add_filter( 'plethora_themeoptions_header', array($this, 'subsection_headernav'), 10);			// Navigation subsection
		    add_filter( 'plethora_themeoptions_header', array($this, 'subsection_headersocialbar'), 10);	// Social Bar subsection
		    // Footer Section ( adding filters applied to 'plethora_themeoptions_footer')
		    add_filter( 'plethora_themeoptions_footer', array($this, 'subsection_footerlayout'), 10);		// Main Section
		    add_filter( 'plethora_themeoptions_footer', array($this, 'subsection_footerinfobar'), 20);		// Info Bar
		    // Content Section ( adding filters applied to 'plethora_themeoptions_content' / Use 1 or 2 digit priority for archives and 3-digit for singles )
		    add_filter( 'plethora_themeoptions_content', array($this, 'subsection_404'), 999);		// 404 page subsection
		    add_filter( 'plethora_themeoptions_content', array($this, 'subsection_search'), 999);		// 404 page subsection
		}

		public function set_theme_options_hookpoints() {

		// SECTIONS CONFIGURATION
			$sections = array();

			// GENERAL SECTION ( developers may hook here! )              
	    	if ( has_filter( 'plethora_themeoptions_general') ) {
				$sections[] = $this->section_general();		// Set General section tab first!
				$sections = apply_filters( 'plethora_themeoptions_general', $sections );
			}

			// HEADER SECTION ( developers may hook here! )              
	    	if ( has_filter( 'plethora_themeoptions_header') ) {
				$sections[] = $this->section_header();		// Set General section tab first!
				$sections = apply_filters( 'plethora_themeoptions_header', $sections );
			}

			// FOOTER SECTION ( developers may hook here! )              
	    	if ( has_filter( 'plethora_themeoptions_footer') ) {
				$sections[] = $this->section_footer();		// Set General section tab first!
				$sections = apply_filters( 'plethora_themeoptions_footer', $sections );
			}

			// CONTENT SECTION  ( developers may hook single post options here! )               
	    	if ( has_filter( 'plethora_themeoptions_content') ) {
				$sections[] = $this->section_content();		// Set content section tab first!
				$sections = apply_filters( 'plethora_themeoptions_content', $sections );
			}

			// ADD-ONS & MODULES SECTION  ( developers may hook plugin supports, APIs and modules here! )               
	    	if ( has_filter( 'plethora_themeoptions_modules') ) {
				$sections[] = $this->section_modules();		// Set supported APIs section tab first!
				$sections = apply_filters( 'plethora_themeoptions_modules', $sections );
			} 

			// ADVANCED SECTION  ( developers may hook here! )               
	    	if ( has_filter( 'plethora_themeoptions_advanced') ) {
				$sections[] = $this->section_advanced();		// Set advanced section tab first!
				$sections = apply_filters( 'plethora_themeoptions_advanced', $sections );
			}

			// HELP SECTION  ( developers may hook here! )               
	    	if ( has_filter( 'plethora_themeoptions_help') ) {
				$sections[] = $this->section_help();		// Set advanced section tab first!
				$sections = apply_filters( 'plethora_themeoptions_help', $sections );
			}

			$this->sections = $sections;

		}

		function section_general() { 

	    	$return = array(
				'title'      => esc_html__('General', 'healthflex'),
				'header'     => esc_html__('Style & typography options applied globally', 'healthflex'),
				'icon_class' => '',
				'icon'       => 'el-icon-globe-alt',
				);
	    	return $return;
	    }

		function section_header() { 

	    	$return = array(
				'title'      => esc_html__('Header', 'healthflex'),
				'header'     => esc_html__('Style & typography options applied globally', 'healthflex'),
				'icon_class' => '',
				'icon'       => 'el-icon-circle-arrow-up',
				);
	    	return $return;
	    }

		function section_footer() { 

	    	$return = array(
				'title'      => esc_html__('Footer', 'healthflex'),
				'header'     => esc_html__('Style & typography options applied globally', 'healthflex'),
				'icon_class' => '',
				'icon'       => 'el-icon-circle-arrow-down',
				);
	    	return $return;
	    }

		function section_content() { 

	    	$return = array(
				'title'      => esc_html__('Content', 'healthflex'),
				'icon'       => 'el-icon-folder-open',
				'icon_class' => ''
				);
	    	return $return;
	    }

		function section_modules() { 

	    	$return = array(
				'title'      => esc_html__('Add-ons & Modules', 'healthflex'),
				'icon'       => 'el-icon-puzzle',
				'icon_class' => ''
				);
	    	return $return;
	    }

		function section_advanced() { 

	    	$return = array(
				'title'      => esc_html__('Advanced', 'healthflex'),
				'icon'       => 'el-icon-cogs',
				'icon_class' => ''
				);
	    	return $return;
	    }

		function section_help() { 

			$return = array(
				'icon'       => 'el-icon-question',
				'title'      => esc_html__('Help', 'healthflex'),
				// 'heading'      => esc_html__('SEND A TICKET TO PLETHORA SUPPORT', 'healthflex'),
				// 'desc'       => self::get_system_info() ,
				);

	    	return $return;
	    }

	// SET SECTION HOOKPOINTS -> FINISH

	// SET THEME SPECIFIC OPTION TABS -> START   

	    function subsection_headerlogo( $sections ) { 

	    	$sections[] = array(
				'title'      => esc_html__('Main Section // Logo', 'healthflex'),
				'heading'	 => esc_html__('MAIN HEADER SECTION // LOGO OPTIONS', 'healthflex'),
				'subsection' => true,
				'fields'     => array(

					array(
						'id'       => METAOPTION_PREFIX .'logo',
						'type'     => 'switch', 
						'title'    => esc_html__('Display Logo', 'healthflex'),
						"default"  => 1,
						'on'       => esc_html__('Display', 'healthflex') ,
						'off'      => esc_html__('Hide', 'healthflex'),
						),
					array(
						'id'      => THEMEOPTION_PREFIX .'logo-layout',
						'required' => array( METAOPTION_PREFIX .'logo','=', 1),						
						'type'    => 'button_set',
						'title'   => esc_html__('Logo layout', 'healthflex'), 
						'options' => array(
							'1' => esc_html__('Image only', 'healthflex'), 
							'2' => esc_html__('Image + Subtitle', 'healthflex'), 
							'3' => esc_html__('Title + Subtitle', 'healthflex'), 
							'4' => esc_html__('Title only', 'healthflex')), 
						'default' => '1'
						),
					array(
						'id'       =>THEMEOPTION_PREFIX .'logo-img',
						'type'     => 'media', 
						'required' => array( THEMEOPTION_PREFIX .'logo-layout','=',array('1', '2')),	
						'url'      => true,			
						'title'    => esc_html__('Image', 'healthflex'),
						'default'  =>array('url'=> ''. PLE_THEME_ASSETS_URI .'/images/healthflex_logo_color.png'),
						),
					array(
						'id'        =>THEMEOPTION_PREFIX .'logo-title',
						'type'      => 'text',
						'required'  => array( THEMEOPTION_PREFIX .'logo-layout','=', array('3', '4')),						
						'title'     => esc_html__('Title', 'healthflex'),
						'default'   => esc_html__('HealthFlex', 'healthflex'),
						'translate' => true,
						),
					array(
						'id'       =>THEMEOPTION_PREFIX .'logo-subtitle',
						'type'     => 'text',
						'required' => array( THEMEOPTION_PREFIX .'logo-layout','=', array('2', '3')),						
						'title'    => esc_html__('Subtitle', 'healthflex'),
						'default'  =>  esc_html__('Call us Toll free +30 1234-567-890', 'healthflex'),
						'translate' => true,
						),
					array(
						'id'      => THEMEOPTION_PREFIX .'logo-subtitle-animation',
						'required' => array( THEMEOPTION_PREFIX .'logo-layout','=', array('2', '3')),						
						'type'    => 'switch', 
						'title'   => esc_html__('Subtitle Animation', 'healthflex'),
						'subtitle'   => esc_html__('Enable title text left to right animation', 'healthflex'),
						"default" => 0,
					),	

                  	array(
						'id'       => THEMEOPTION_PREFIX .'logo-subtitle-animation-type',
						'required' => array( THEMEOPTION_PREFIX .'logo-subtitle-animation','=', 1),						
						'type'     => 'select',
						'title'    => esc_html__('Animation', 'healthflex'),
						'default'  => 'slideInLeft',
						'options'  => class_exists( 'Plethora_Module_Style' ) ? Plethora_Module_Style::get_options_array( array( 'type' => 'animations', 'prepend_default' => true, 'default_title' => 'None' ) ) : array(),
                  	),

					array(
						'id'       => 'logo-layout-start',
						'required' => array( METAOPTION_PREFIX .'logo','=', 1),						
						'type'     => 'section',
						'title'    => esc_html__('Logo Position', 'healthflex'),
						'indent'   => true,
				     ),
						array(
							'id'       =>THEMEOPTION_PREFIX .'less-logo-vertical-margin',
							'type'     => 'dimensions',
							'title'    => esc_html__('Vertical Margin (large devices)', 'healthflex'),
							'subtitle' => esc_html__('default: 20px', 'healthflex'),
							'units'    => false,
							'width'   => false,
							'default'  => array('height' => '20', 'units'=>'px')
							),												
						array(
							'id'       =>THEMEOPTION_PREFIX .'less-logo-vertical-margin-sm',
							'type'     => 'dimensions',
							'title'    => esc_html__('Vertical Margin (small devices)', 'healthflex'),
							'subtitle' => esc_html__('default: 14px', 'healthflex'),
							'units'    => false,
							'width'   => false,
							'default'  => array('height' => '14', 'units'=>'px')
							),												
						array(
							'id'       =>THEMEOPTION_PREFIX .'less-logo-vertical-margin-xs',
							'type'     => 'dimensions',
							'title'    => esc_html__('Vertical Margin (extra small devices)', 'healthflex'),
							'subtitle' => esc_html__('default: 10px', 'healthflex'),
							'width'   => false,
							'units'    => false,
							'default'  => array('height' => '10', 'units'=>'px')
							),												
						array(
							'id'       =>THEMEOPTION_PREFIX .'less-logo-img-max-height',
							'type'     => 'dimensions',
							'required' => array( THEMEOPTION_PREFIX .'logo-layout','=',array('1', '2')),	
							'units'    => false,
							'title'    => esc_html__('Logo Image Max Height (large devices)',  'healthflex'),
							'subtitle' => esc_html__('default: 38px', 'healthflex'),
							'width'    => false,
							'default'  => array('height'=>'38', 'units'=>'px')
							),												

						array(
							'id'       =>THEMEOPTION_PREFIX .'less-logo-img-max-height-sm',
							'type'     => 'dimensions',
							'required' => array( THEMEOPTION_PREFIX .'logo-layout','=',array('1', '2')),	
							'units'    => false,
							'title'    => esc_html__('Logo Image Max Height (small devices)',  'healthflex'),
							'subtitle' => esc_html__('default: 34px', 'healthflex'),
							'width'    => false,
							'default'  => array('height'=>'34', 'units'=>'px')
							),												

						array(
							'id'       =>THEMEOPTION_PREFIX .'less-logo-img-max-height-xs',
							'type'     => 'dimensions',
							'required' => array( THEMEOPTION_PREFIX .'logo-layout','=',array('1', '2')),	
							'units'    => false,
							'title'    => esc_html__('Logo Image Max Height (extra small devices)',  'healthflex'),
							'subtitle' => esc_html__('default: 30px', 'healthflex'),
							'width'    => false,
							'default'  => array('height'=>'30', 'units'=>'px')
							),												

						array(
							'id'             => THEMEOPTION_PREFIX .'less-logo-font-size',
							'type'           => 'typography', 
							'title'          => esc_html__('Title Font Size (large devices)', 'healthflex'),
							'google'         => false, 
							'font-family'    => false,
							'font-style'     => false,
							'font-weight'    => false,
							'font-size'      => true,
							'line-height'    => false,
							'word-spacing'   => false,
							'letter-spacing' => false,
							'text-align'     => false,
							'text-transform' => false,
							'color'          => false,
							'subsets'        => false,
							'preview'        => false, 
							'all_styles'     => false, // import all google font weights
							'default'        => array( 'font-size' => '32px' ),
							),	
						array(
							'id'             => THEMEOPTION_PREFIX .'less-logo-font-size-sm',
							'type'           => 'typography', 
							'title'          => esc_html__('Title Font Size (small devices)', 'healthflex'),
							'google'         => false, 
							'font-family'    => false,
							'font-style'     => false,
							'font-weight'    => false,
							'font-size'      => true,
							'line-height'    => false,
							'word-spacing'   => false,
							'letter-spacing' => false,
							'text-align'     => false,
							'text-transform' => false,
							'color'          => false,
							'subsets'        => false,
							'preview'        => false, 
							'all_styles'     => false, // import all google font weights
							'default'        => array( 'font-size' => '28px' )
							),	
						array(
							'id'             => THEMEOPTION_PREFIX .'less-logo-font-size-xs',
							'type'           => 'typography', 
							'title'          => esc_html__('Title Font Size (extra small devices)', 'healthflex'),
							'google'         => false, 
							'font-family'    => false,
							'font-style'     => false,
							'font-weight'    => false,
							'font-size'      => true,
							'line-height'    => false,
							'word-spacing'   => false,
							'letter-spacing' => false,
							'text-align'     => false,
							'text-transform' => false,
							'color'          => false,
							'subsets'        => false,
							'preview'        => false, 
							'all_styles'     => false, // import all google font weights
							'default'        => array( 'font-size' => '22px' )
							),	
					array(
					    'id'     => 'logo-layout-end',
						'required' => array( METAOPTION_PREFIX .'logo','=', 1),						
					    'type'   => 'section',
					    'indent' => false,
					),						

					)
				);

			return $sections;
	    }

       function subsection_favicons( $sections ) { 

	    	$sections[] = array(
				'title'      => esc_html__('Favicons', 'healthflex'),
				'heading'	 => esc_html__('FAVICON OPTIONS', 'healthflex'),
				'desc'		 => esc_html__('Since 4.3 version, WordPress introduced a', 'healthflex') . ' <a href="'. admin_url('customize.php') .'" target="_blank">'. esc_html__('site identity feature', 'healthflex') . '</a>'. esc_html__(' where you can place a single 512x512 image and just let WP handle all your application icon displays. We suggest going along with this native WP feature, however we keep those options here for older WP versions', 'healthflex'),
				'subsection' => true,
				'fields'     => array(

					array(
						'id'       =>THEMEOPTION_PREFIX .'favicon',
						'type'     => 'media', 
						'title'    => esc_html__('Classic Favicon 16', 'healthflex'),
						'url'      => true,
						'subtitle' => esc_html__('Upload a 16px X 16px PNG/GIF/ICO image. This is the classic favicon used by all desktop browsers', 'healthflex'),
						'default'  =>array('url'=> ''. PLE_THEME_ASSETS_URI .'/images/favicons/favicon-16x16.png'),
						),

					array(
						'id'       =>THEMEOPTION_PREFIX .'favicon-32',
						'type'     => 'media', 
						'title'    => esc_html__('Classic Favicon 32', 'healthflex'),
						'url'      => true,
						'subtitle' => esc_html__('Upload a 32px X 32px PNG/GIF image.', 'healthflex'),
						'default'  =>array('url'=> ''. PLE_THEME_ASSETS_URI .'/images/favicons/favicon-32x32.png'),
						),

					array(
						'id'       =>THEMEOPTION_PREFIX .'favicon-96',
						'type'     => 'media', 
						'title'    => esc_html__('Classic Favicon 96', 'healthflex'),
						'url'      => true,
						'subtitle' => esc_html__('Upload a 96px X 96px PNG/GIF image.', 'healthflex'),
						'default'  =>array('url'=> ''. PLE_THEME_ASSETS_URI .'/images/favicons/favicon-96x96.png'),
						),
					)
				);

			return $sections;
	    }

	    function subsection_colorsets( $sections ) { 

			$sections[] = array(
				'title'      => esc_html__('Basic Colors & Sets', 'healthflex'),
				'heading'     => esc_html__('BASIC COLORS & COLOR SET OPTIONS', 'healthflex'),
				'subsection' => true,
				'fields'     => array(
					// Background & Body styling
					array(
				       'id' => 'body-section-start',
				       'type' => 'section',
				       'title' => esc_html__('Basic Colors', 'healthflex'),
				       'subtitle' => esc_html__('Basic color choices that affect several elements within the theme.', 'healthflex'),
				       'indent' => true,
				     ),

							array(
								'id'          => THEMEOPTION_PREFIX .'less-body-bg',
								'type'        => 'color',
								'title'       => esc_html__('Body Background Color', 'healthflex'), 
								'subtitle'    => esc_html__('default: #EFEFEF.', 'healthflex'),
								'default'     => '#EFEFEF',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-text-color',
								'type'        => 'color',
								'title'       => esc_html__('Text Color', 'healthflex'), 
								'subtitle'    => esc_html__('default: #323232.', 'healthflex'),
								'default'     => '#323232',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-link-color',
								'type'        => 'link_color',
								'title'       => esc_html__('Link Text Color', 'healthflex'), 
								'subtitle'    => esc_html__('defaults: #45aaff / #304770', 'healthflex'),
								'visited'     => false,
								'active'     => false,
							    'default'  => array(
							        'regular'  => '#45aaff', 
							        'hover'    => '#304770',
							    	),
							    'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-brand-primary',
								'type'        => 'color',
								'title'       => esc_html__('Primary Brand Color', 'healthflex'), 
								'subtitle'    => esc_html__('default: #45aaff.', 'healthflex'),
								'default'     => '#45aaff',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-brand-secondary',
								'type'        => 'color',
								'title'       => esc_html__('Secondary Brand Color', 'healthflex'), 
								'subtitle'    => esc_html__('default: #304770.', 'healthflex'),
								'default'     => '#304770',
								'transparent' => false,
								'validate'    => 'color',
								),

					array(
					    'id'     => 'body-section-end',
					    'type'   => 'section',
					    'indent' => false,
					),					

					// Primary Color Set
					array(
				       'id' => 'skin-section-start',
				       'type' => 'section',
				       'title' => esc_html__('Primary Color Set', 'healthflex'),
				       'subtitle' => esc_html__('Options for primary colored elements. Background & other design elements are colored according to chosen <strong>primary color</strong> ( check above ).', 'healthflex'),
				       'indent' => true,
				     ),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-skin-section-txtcolor',
								'type'        => 'color',
								'title'       => esc_html__('Text', 'healthflex'), 
								'subtitle'    => esc_html__('default: #ffffff.', 'healthflex'),
								'default'     => '#ffffff',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-skin-section-linkcolor',
								'type'        => 'link_color',
								'title'       => esc_html__('Link Text', 'healthflex'), 
								'subtitle'    => esc_html__('default: #304770/#fbc02d', 'healthflex'),
								'visited'     => false,
								'active'     => false,
							    'default'  => array(
							        'regular'  => '#304770', 
							        'hover'    => '#fbc02d',
							    	),
							    'validate'    => 'color',
								),
					array(
					    'id'     => 'skin-section-end',
					    'type'   => 'section',
					    'indent' => false,
					),					

					// Secondary Color Set
					array(
				       'id' => 'secondary-section-start',
				       'type' => 'section',
				       'title' => esc_html__('Secondary Color Set', 'healthflex'),
				       'subtitle' => esc_html__('Color options for secondary colored elements. Background & other design elements are colored according to chosen secondary color ( check above ).', 'healthflex'),
				       'indent' => true,
				     ),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-secondary-section-txtcolor',
								'type'        => 'color',
								'title'       => esc_html__('Text', 'healthflex'), 
								'subtitle'    => esc_html__('default: #ffffff.', 'healthflex'),
								'default'     => '#ffffff',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-secondary-section-linkcolor',
								'type'        => 'link_color',
								'title'       => esc_html__('Link Text', 'healthflex'), 
								'subtitle'    => esc_html__('default: #45aaff/#fbc02d', 'healthflex'),
								'visited'     => false,
								'active'     => false,
							    'default'  => array(
							        'regular'  => '#45aaff', 
							        'hover'    => '#fbc02d',
							    	),
							    'validate'    => 'color',
								),
					array(
					    'id'     => 'secondary-section-end',
					    'type'   => 'section',
					    'indent' => false,
					),		

					// Light Color Set
					array(
				       'id' => 'light-section-start',
				       'type' => 'section',
				       'title' => esc_html__('Light Color Set', 'healthflex'),
				       'subtitle' => esc_html__('Color options for light colored elements.', 'healthflex'),
				       'indent' => true,
				     ),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-light-section-bgcolor',
								'type'        => 'color',
								'title'       => esc_html__('Background', 'healthflex'), 
								'subtitle'    => esc_html__('default: #e5e5e5.', 'healthflex'),
								'default'     => '#e5e5e5',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-light-section-txtcolor',
								'type'        => 'color',
								'title'       => esc_html__('Text', 'healthflex'), 
								'subtitle'    => esc_html__('default: #323232.', 'healthflex'),
								'default'     => '#323232',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-light-section-linkcolor',
								'type'        => 'link_color',
								'title'       => esc_html__('Link Text', 'healthflex'), 
								'subtitle'    => esc_html__('default: #45aaff/#304770', 'healthflex'),
								'visited'     => false,
								'active'     => false,
							    'default'  => array(
							        'regular'  => '#45aaff', 
							        'hover'    => '#304770',
							    	),
							    'validate'    => 'color',
								),
					array(
					    'id'     => 'light-section-end',
					    'type'   => 'section',
					    'indent' => false,
					),		

					// Dark Color Set
					array(
				       'id' => 'dark-section-start',
				       'type' => 'section',
				       'title' => esc_html__('Dark Color Set', 'healthflex'),
				       'subtitle' => esc_html__('Color options for dark colored elements.', 'healthflex'),
				       'indent' => true,
				     ),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-dark-section-bgcolor',
								'type'        => 'color',
								'title'       => esc_html__('Background', 'healthflex'), 
								'subtitle'    => esc_html__('default: #222D3F.', 'healthflex'),
								'default'     => '#222D3F',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-dark-section-txtcolor',
								'type'        => 'color',
								'title'       => esc_html__('Text', 'healthflex'), 
								'subtitle'    => esc_html__('default: #ffffff.', 'healthflex'),
								'default'     => '#ffffff',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-dark-section-linkcolor',
								'type'        => 'link_color',
								'title'       => esc_html__('Link Text', 'healthflex'), 
								'subtitle'    => esc_html__('default: #45aaff/#fbc02d', 'healthflex'),
								'visited'     => false,
								'active'     => false,
							    'default'  => array(
							        'regular'  => '#45aaff', 
							        'hover'    => '#fbc02d',
							    	),
							    'validate'    => 'color',
								),
					array(
					    'id'     => 'dark-section-end',
					    'type'   => 'section',
					    'indent' => false,
					),						

					// White Color Set
					array(
				       'id' => 'white-section-start',
				       'type' => 'section',
				       'title' => esc_html__('White Color Set', 'healthflex'),
				       'subtitle' => esc_html__('Color options for white colored elements.', 'healthflex'),
				       'indent' => true,
				     ),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-white-section-bgcolor',
								'type'        => 'color',
								'title'       => esc_html__('Background', 'healthflex'), 
								'subtitle'    => esc_html__('default: #ffffff.', 'healthflex'),
								'default'     => '#ffffff',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-white-section-txtcolor',
								'type'        => 'color',
								'title'       => esc_html__('Text', 'healthflex'), 
								'subtitle'    => esc_html__('default: #323232.', 'healthflex'),
								'default'     => '#323232',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-white-section-linkcolor',
								'type'        => 'link_color',
								'title'       => esc_html__('Link', 'healthflex'), 
								'subtitle'    => esc_html__('default: #4eabf9/#304770', 'healthflex'),
								'visited'     => false,
								'active'     => false,
							    'default'  => array(
							        'regular'  => '#4eabf9', 
							        'hover'    => '#304770',
							    	),
							    'validate'    => 'color',
								),
					array(
					    'id'     => 'white-section-end',
					    'type'   => 'section',
					    'indent' => false,
					),						

					// Black colored sections styling
					array(
				       'id' => 'black-section-start',
				       'type' => 'section',
				       'title' => esc_html__('Black Color Set', 'healthflex'),
				       'subtitle' => esc_html__('Color options for black colored elements.', 'healthflex'),
				       'indent' => true,
				     ),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-black-section-bgcolor',
								'type'        => 'color',
								'title'       => esc_html__('Background', 'healthflex'), 
								'subtitle'    => esc_html__('default: #323232.', 'healthflex'),
								'default'     => '#323232',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-black-section-txtcolor',
								'type'        => 'color',
								'title'       => esc_html__('Text', 'healthflex'), 
								'subtitle'    => esc_html__('default: #ffffff.', 'healthflex'),
								'default'     => '#ffffff',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-black-section-linkcolor',
								'type'        => 'link_color',
								'title'       => esc_html__('Link', 'healthflex'), 
								'subtitle'    => esc_html__('default: #45aaff/#fbc02d', 'healthflex'),
								'visited'     => false,
								'active'     => false,
							    'default'  => array(
							        'regular'  => '#45aaff', 
							        'hover'    => '#fbc02d',
							    	),
							    'validate'    => 'color',
								),
					array(
					    'id'     => 'black-section-end',
					    'type'   => 'section',
					    'indent' => false,
					),						
				)
			);
			return $sections;

	    }

	    function subsection_typography( $sections ) { 

			$sections[] = array(
				'title'      => esc_html__('Typography', 'healthflex'),
				'heading'     => esc_html__('TYPOGRAPHY OPTIONS', 'healthflex'),
				'subsection' => true,
				'fields'     => array(

					array(
						'id'             => THEMEOPTION_PREFIX .'less-font-family-sans-serif',
						'type'           => 'typography', 
						'title'          => esc_html__('Primary Font', 'healthflex'),
						'desc'          => esc_html__('Primary font is used in content texts', 'healthflex'),
						'google'         => true, 
						'font-style'     => false,
						'font-weight'    => false,
						'font-size'      => false,
						'line-height'    => false,
						'word-spacing'   => false,
						'letter-spacing' => false,
						'text-align'     => false,
						'text-transform' => false,
						'color'          => false,
						'subsets'        => true,
						'preview'        => true, 
						'all_styles'     => true, // import all google font weights
						'default'        => array( 'font-family'=>'Lato', 'subsets'=> 'latin' ),
						),	
					array(
						'id'             => THEMEOPTION_PREFIX .'less-font-family-alternative',
						'type'           => 'typography', 
						'title'          => esc_html__('Secondary Font', 'healthflex'),
						'desc'           => esc_html__('Secondary font is used in headings, menu items and buttons', 'healthflex'),
						'google'         => true, 
						'font-style'     => false,
						'font-weight'    => false,
						'font-size'      => false,
						'line-height'    => false,
						'word-spacing'   => false,
						'letter-spacing' => false,
						'text-align'     => false,
						'text-transform' => false,
						'color'          => false,
						'subsets'        => true,
						'preview'        => true, 
						'all_styles'     => true, // import all google font weights
						'default'        => array( 'font-family'=>'Raleway', 'subsets'=> 'latin' ),
						),	

					array(
						'id'             => THEMEOPTION_PREFIX .'less-font-size-base',
						'type'           => 'typography', 
						'title'          => esc_html__('Primary Font Size Base', 'healthflex'),
						'desc'          => esc_html__('All text sizes for body & paragraph elements will be adjusted according to this base.', 'healthflex'),
						'google'         => false, 
						'font-family'    => false,
						'font-style'     => false,
						'font-weight'    => false,
						'font-size'      => true,
						'line-height'    => false,
						'word-spacing'   => false,
						'letter-spacing' => false,
						'text-align'     => false,
						'text-transform' => false,
						'color'          => false,
						'subsets'        => false,
						'preview'        => false, 
						'all_styles'     => false, // import all google font weights
						'default'        => array( 'font-size' => '15px' ),
						),	

					array(
						'id'             => THEMEOPTION_PREFIX .'less-font-size-alternative-base',
						'type'           => 'typography', 
						'title'          => esc_html__('Secondary Font Size Base', 'healthflex'),
						'desc'          => esc_html__('All text sizes for logo, menu, heading & buttons elements will be adjusted according to this base.', 'healthflex'),
						'google'         => false, 
						'font-family'    => false,
						'font-style'     => false,
						'font-weight'    => false,
						'font-size'      => true,
						'line-height'    => false,
						'word-spacing'   => false,
						'letter-spacing' => false,
						'text-align'     => false,
						'text-transform' => false,
						'color'          => false,
						'subsets'        => false,
						'preview'        => false, 
						'all_styles'     => false, // import all google font weights
						'default'        => array( 'font-size' => '15px' )
						),	
					array(
						'id'	=> THEMEOPTION_PREFIX .'less-headings-h1-sizemultiplier',
					    'type' => 'slider',
					    'title' => esc_html__('H1 Font Size Multiplier', 'healthflex'),
					    'subtitle' => esc_html__('Default: 2.6, min: 0.5, max: 4.0', 'healthflex'),
					    'desc' => esc_html__('This value, multiplied with \'Secondary Font Size Base\', will produce the font-size of the heading', 'healthflex'),
					    "default" => 2.6,
					    "min" => 0.5,
					    "step" => 0.05,
					    "max" => 4.0,
					    'resolution' => 0.01,
					    'display_value' => 'text'
					),
					array(
						'id'	=> THEMEOPTION_PREFIX .'less-headings-h2-sizemultiplier',
					    'type' => 'slider',
					    'title' => esc_html__('H2 Font Size Multiplier', 'healthflex'),
					    'subtitle' => esc_html__('Default: 1.8, min: 0.5, max: 4.0', 'healthflex'),
					    'desc' => esc_html__('This value, multiplied with \'Secondary Font Size Base\', will produce the font-size of the heading', 'healthflex'),
					    "default" => 1.8,
					    "min" => 0.5,
					    "step" => 0.05,
					    "max" => 4.0,
					    'resolution' => 0.01,
					    'display_value' => 'text'
					),
					array(
						'id'	=> THEMEOPTION_PREFIX .'less-headings-h3-sizemultiplier',
					    'type' => 'slider',
					    'title' => esc_html__('H3 Font Size Multiplier', 'healthflex'),
					    'subtitle' => esc_html__('Default: 1.6, min: 0.5, max: 4.0', 'healthflex'),
					    'desc' => esc_html__('This value, multiplied with \'Secondary Font Size Base\', will produce the font-size of the heading', 'healthflex'),
					    "default" => 1.6,
					    "min" => 0.5,
					    "step" => 0.05,
					    "max" => 4.0,
					    'resolution' => 0.01,
					    'display_value' => 'text'
					),
					array(
						'id'	=> THEMEOPTION_PREFIX .'less-headings-h4-sizemultiplier',
					    'type' => 'slider',
					    'title' => esc_html__('H4 Font Size Multiplier', 'healthflex'),
					    'subtitle' => esc_html__('Default: 1.2, min: 0.5, max: 4.0', 'healthflex'),
					    'desc' => esc_html__('This value, multiplied with \'Secondary Font Size Base\', will produce the font-size of the heading', 'healthflex'),
					    "default" => 1.2,
					    "min" => 0.5,
					    "step" => 0.05,
					    "max" => 4.0,
					    'resolution' => 0.01,
					    'display_value' => 'text'
					),
					array(
						'id'	=> THEMEOPTION_PREFIX .'less-headings-h5-sizemultiplier',
					    'type' => 'slider',
					    'title' => esc_html__('H5 Font Size Multiplier', 'healthflex'),
					    'subtitle' => esc_html__('Default: 1.0, min: 0.5, max: 4.0', 'healthflex'),
					    'desc' => esc_html__('This value, multiplied with \'Secondary Font Size Base\', will produce the font-size of the heading', 'healthflex'),
					    "default" => 1.0,
					    "min" => 0.5,
					    "step" => 0.05,
					    "max" => 4.0,
					    'resolution' => 0.01,
					    'display_value' => 'text'
					),
					array(
						'id'	=> THEMEOPTION_PREFIX .'less-headings-h6-sizemultiplier',
					    'type' => 'slider',
					    'title' => esc_html__('H6 Font Size Multiplier', 'healthflex'),
					    'subtitle' => esc_html__('Default: 0.85, min: 0.5, max: 4.0', 'healthflex'),
					    'desc' => esc_html__('This value, multiplied with \'Secondary Font Size Base\', will produce the font-size of the heading', 'healthflex'),
					    "default" => 0.85,
					    "min" => 0.5,
					    "step" => 0.05,
					    "max" => 4.0,
					    'resolution' => 0.01,
					    'display_value' => 'text'
					),
					array(
						'id'             => THEMEOPTION_PREFIX .'less-headings-text-transform',
						'type'           => 'typography', 
						'title'          => esc_html__('Heading Text Transform', 'healthflex'),
						'google'         => false, 
						'font-family'    => false,
						'font-style'     => false,
						'font-weight'    => false,
						'font-size'      => false,
						'line-height'    => false,
						'word-spacing'   => false,
						'letter-spacing' => false,
						'text-align'     => false,
						'text-transform' => true,
						'color'          => false,
						'subsets'        => false,
						'preview'        => false, 
						'all_styles'     => false, // import all google font weights
						'default'        => array( 'text-transform' => 'uppercase' )
						),	

					array(
						'id'             => THEMEOPTION_PREFIX .'less-headings-font-weight',
						'type'           => 'typography', 
						'title'          => esc_html__('Headings Font Weight', 'healthflex'),
						'google'         => false, 
						'font-family'    => false,
						'font-style'     => false,
						'font-weight'    => true,
						'font-size'      => false,
						'line-height'    => false,
						'word-spacing'   => false,
						'letter-spacing' => false,
						'text-align'     => false,
						'text-transform' => false,
						'color'          => false,
						'subsets'        => false,
						'preview'        => false, 
						'all_styles'     => false, // import all google font weights
						'default'        => array( 'font-weight' => '900' )
						),	

					array(
						'id'             => THEMEOPTION_PREFIX .'less-btn-text-transform',
						'type'           => 'typography', 
						'title'          => esc_html__('Buttons Text Transform', 'healthflex'),
						'google'         => false, 
						'font-family'    => false,
						'font-style'     => false,
						'font-weight'    => false,
						'font-size'      => false,
						'line-height'    => false,
						'word-spacing'   => false,
						'letter-spacing' => false,
						'text-align'     => false,
						'text-transform' => true,
						'color'          => false,
						'subsets'        => false,
						'preview'        => false, 
						'all_styles'     => false, // import all google font weights
						'default'        => array( 'text-transform' => 'uppercase' )
						),	

					)
				);
			return $sections;

	    }


	    function subsection_misc( $sections ) { 

	    	$misc_fields = array();
			// A hook for modules that want to add options to MISC tab              
	    	if ( has_filter( 'plethora_themeoptions_general_misc_fields') ) {

				$misc_fields = apply_filters( 'plethora_themeoptions_general_misc_fields', $misc_fields );
			}

			$sections[] = array(
				'title'      => esc_html__('Misc', 'healthflex'),
				'heading'     => esc_html__('MISCELLANEOUS ELEMENTS', 'healthflex'),
				'subsection' => true,
				'fields'     => array_merge( 
									array(
										array(
											'id'       => METAOPTION_PREFIX .'less-diagonal-gradient-angle',
											'type'     => 'spinner', 
											'title'    => esc_html__('Diagonal Design Angle (degrees)', 'healthflex'),
											'subtitle'    => esc_html__('Max: 360 degrees', 'healthflex'),
											"min"      => 1,
											"step"     => 1,
											"max"      => 360,
											"default"  => 45,
											),	
										array(
											'id'       => METAOPTION_PREFIX .'less-section-background-transparency',
											'type'     => 'spinner', 
											'title'    => esc_html__('Global Transparency Level', 'healthflex'),
											'desc'    => esc_html__('This is the transparency level for each transparency applied on color set backgrounds', 'healthflex'),
											"min"      => 1,
											"step"     => 1,
											"max"      => 100,
											"default"  => 50,
											),	
									),
									$misc_fields
								)
			);

			return $sections;
	    }

	    function subsection_headermain( $sections ) { 

	    	$sections[] = array(
				'title'      => esc_html__('Main Section', 'healthflex'),
				'heading'	 => esc_html__('MAIN HEADER SECTION', 'healthflex'),
				'subsection' => true,
				'fields'     => array(


						array(
							'id'      => METAOPTION_PREFIX .'header-background',
							'type'    => 'button_set', 
							'title'   => esc_html__('Bottom Separator', 'healthflex'),
							'desc'   => esc_html__('Add or not an angled separator on bottom', 'healthflex'),
							'options' => array(
								'color'      => esc_html__('None', 'healthflex'),
								'separator_bottom sep_angled_positive_bottom' => esc_html__('Angle Positive Bottom', 'healthflex'),
								),
							'default' => 'color',
						),	

						array(
							'id'       => METAOPTION_PREFIX .'header-background-transparentfilm',
							'type'     => 'switch', 
							'required' => array( 
											// array( METAOPTION_PREFIX .'header-colorset','!=', 'custom_section'),						
											array( METAOPTION_PREFIX .'header-background','=', 'color'),						
									 	  ),
							'title'   => esc_html__('Transparency', 'healthflex'),
							"default" => 0,
						),	

						array(
							'id'       =>THEMEOPTION_PREFIX .'less-header-background-transparency',
							'required' => array( 
											array( METAOPTION_PREFIX .'header-background','=', 'color'),						
											array( METAOPTION_PREFIX .'header-background-transparentfilm','=', 1),						
									 	  ),
							'type'          => 'slider',
							'title'         => esc_html__('Opacity Level', 'healthflex'), 
							'desc'          => esc_html__('Set the opacity level for the overlay film.', 'healthflex'), 
							'subtitle'      => esc_html__('default: 80', 'healthflex'),
							"default"       => 80,
							"min"           => 0,
							"step"          => 1,
							"max"           => 100,
							'display_value' => 'text'
						),

						array(
							'id'      => METAOPTION_PREFIX .'header-sticky',
							'type'    => 'switch', 
							'title'   => esc_html__('Sticky Header On Scroll', 'healthflex'),
							'desc'   => esc_html__('Set it to ON if you want your header to remain always on top when you scroll down a page', 'healthflex'),
							"default" => 1,
						),	

					// Header styling
					array(
				       'id' => 'header-section-start',
				       'type' => 'section',
				       'title' => esc_html__('Header Color Set', 'healthflex'),
				       'subtitle' => esc_html__('Color options for main header section.', 'healthflex'),
				       'indent' => true,
				     ),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-header-bgcolor',
								'type'        => 'color',
								'title'       => esc_html__('Background Color', 'healthflex'), 
								'subtitle'    => esc_html__('default: #efefef', 'healthflex'),
								'desc'    => esc_html__('The default background color', 'healthflex'),
								'default'     => '#efefef',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-header-txtcolor',
								'type'        => 'color',
								'title'       => esc_html__('Text Color', 'healthflex'), 
								'subtitle'    => esc_html__('default: #555555', 'healthflex'),
								'desc'    => esc_html__('Text color for non linked texts ( i.e. logo title/subtitle )', 'healthflex'),
								'default'     => '#555555',
								'transparent' => false,
								'validate'    => 'color',
								),
							array(
								'id'          => THEMEOPTION_PREFIX .'less-header-linkcolor',
								'type'        => 'link_color',
								'title'       => esc_html__('Link Color', 'healthflex'), 
								'desc'    => esc_html__('Color for navigation items and other link anchor texts', 'healthflex'),
								'subtitle'    => esc_html__('default: #444444 / #4eabf9', 'healthflex'),
								'visited'     => false,
								'active'     => false,
							    'default'  => array(
							        'regular'  => '#444444', 
							        'hover'    => '#4eabf9',
							    	),
							    'validate'    => 'color',
								),

					array(
					    'id'     => 'header-section-end',
					    'type'   => 'section',
					    'indent' => false,
					),	
				   )
				);
			return $sections;
	    }

	    function subsection_headernav( $sections ) { 

	    	$sections[] = array(
				'title'      => esc_html__('Main Section // Menu', 'healthflex'),
				'heading'	 => esc_html__('MAIN HEADER SECTION // NAVIGATION MENU OPTIONS', 'healthflex'),
				'subsection' => true,
				'fields'     => array(

					array(
						'id'       => METAOPTION_PREFIX .'navigation-main',
						'type'     => 'switch', 
						'title'    => esc_html__('Display Main Menu', 'healthflex'),
						"default"  => 1,
						'on'       => esc_html__('Display', 'healthflex') ,
						'off'      => esc_html__('Hide', 'healthflex'),
						),
					array(
						'id'       => METAOPTION_PREFIX .'navigation-main-location',
						'required' => array( METAOPTION_PREFIX .'navigation-main','=',1),						
						'type'     => 'select',
						'title'    => esc_html__('Main Menu Location', 'healthflex'), 
						'desc'     => esc_html__('Select the default location to be displayed as your main menu. You have the possibility to change the main navigation location for every page. ', 'healthflex'),
						'data'     => 'menu_locations',
						'default'  => 'primary',
					),
					array( 
						'id'       => METAOPTION_PREFIX .'navigation-main-behavior',
						'required' => array( METAOPTION_PREFIX .'navigation-main','=',1),						
						'type'     => 'button_set', 
						'title'    => esc_html__('Multi Level Menu Behavior', 'healthflex'),
						'description' => esc_html__('Choose action to trigger child menu items display', 'healthflex') ,
						"default"     => 'hover_menu',
						'options' => array(
											'hover_menu' => esc_html__('Mouse Hover', 'healthflex'),
											'click_menu'       => esc_html__('Click', 'healthflex'),
										),
						),

					array(
						'id'             => THEMEOPTION_PREFIX .'less-menu-font-size',
						'required'       => array( METAOPTION_PREFIX .'navigation-main','=',1),						
						'type'           => 'typography', 
						'title'          => esc_html__('Menu Item Font Size', 'healthflex'),
						'google'         => false, 
						'font-family'    => false,
						'font-style'     => false,
						'font-weight'    => false,
						'font-size'      => true,
						'line-height'    => false,
						'word-spacing'   => false,
						'letter-spacing' => false,
						'text-align'     => false,
						'text-transform' => false,
						'color'          => false,
						'subsets'        => false,
						'preview'        => false, 
						'all_styles'     => false, // import all google font weights
						'default'        => array( 'font-size' => '13px' )
						),	
					array(
						'id'       => THEMEOPTION_PREFIX .'less-menu-item-vertical-padding',
						'required' => array( METAOPTION_PREFIX .'navigation-main','=',1),						
						'type'     => 'dimensions',
						'units'    => false,
						'title'    => esc_html__('Menu Item Vertical Padding (large devices)',  'healthflex'),
						'subtitle' => esc_html__('default: 10px', 'healthflex'),
						'width'    => false,
						'default'  => array('height'=>'10', 'units'=>'px')
						),												
					array(
						'id'       => THEMEOPTION_PREFIX .'less-menu-item-vertical-padding-md',
						'required' => array( METAOPTION_PREFIX .'navigation-main','=',1),						
						'type'     => 'dimensions',
						'units'    => false,
						'title'    => esc_html__('Menu Item Vertical Padding (medium devices)',  'healthflex'),
						'subtitle' => esc_html__('default: 10px', 'healthflex'),
						'width'    => false,
						'default'  => array('height'=>'10', 'units'=>'px')
						),												
					array(
						'id'       => THEMEOPTION_PREFIX .'less-menu-item-vertical-padding-sm',
						'required' => array( METAOPTION_PREFIX .'navigation-main','=',1),						
						'type'     => 'dimensions',
						'units'    => false,
						'title'    => esc_html__('Menu Item Vertical Padding (small devices)',  'healthflex'),
						'subtitle' => esc_html__('default: 15px', 'healthflex'),
						'width'    => false,
						'default'  => array('height'=>'15', 'units'=>'px')
						),												
					array(
						'id'       => THEMEOPTION_PREFIX .'less-menu-item-horizontal-padding',
						'required' => array( METAOPTION_PREFIX .'navigation-main','=',1),						
						'type'     => 'dimensions',
						'units'    => false,
						'title'    => esc_html__('Menu Item Horizontal Padding (large devices)',  'healthflex'),
						'subtitle' => esc_html__('default: 15px', 'healthflex'),
						'height'   => false,
						'default'  => array('width'=>'15', 'units'=>'px')
						),												
					array(
						'id'       => THEMEOPTION_PREFIX .'less-menu-item-horizontal-padding-md',
						'required' => array( METAOPTION_PREFIX .'navigation-main','=',1),						
						'type'     => 'dimensions',
						'units'    => false,
						'title'    => esc_html__('Menu Item Horizontal Padding (medium devices)',  'healthflex'),
						'subtitle' => esc_html__('default: 10px', 'healthflex'),
						'height'   => false,
						'default'  => array('width'=>'10', 'units'=>'px')
						),												
					array(
						'id'       => THEMEOPTION_PREFIX .'less-menu-item-horizontal-padding-sm',
						'required' => array( METAOPTION_PREFIX .'navigation-main','=',1),						
						'type'     => 'dimensions',
						'units'    => false,
						'title'    => esc_html__('Menu Item Horizontal Padding (small devices)',  'healthflex'),
						'subtitle' => esc_html__('default: 15px', 'healthflex'),
						'height'   => false,
						'default'  => array('width'=>'15', 'units'=>'px')
						),												
					)
				);

			return $sections;
	    }



	    function subsection_headersocialbar( $sections ) {

	    	// Get social icons selector field & info
	    	$desc = '';
	    	$option = '';
	    	if ( method_exists( 'Plethora_Module_Social', 'get_icons_option') ) { 

	    		$selectorfield = Plethora_Module_Social::get_icons_option( array( METAOPTION_PREFIX .'socialbar','=','1'));
	    		$option = $selectorfield['option'];
	    		$desc = $selectorfield['status'];
	    	}

			// check that icon libraries are working normally...if not, produce a notice!
			$check_iconslibrary  = get_option( GENERALOPTION_PREFIX .'module_icons_diagnostics_wpremote', '' );
			if ( !empty( $check_iconslibrary ) ) {

				$desc .= '<br><strong style="color:red">';
			    $desc .= esc_html__('IMPORTANT NOTICE: Icon libraries are not working as expected. This might affect the functionality of this feature as well. ', 'plethora-framework');
			    $desc .= esc_html__('After resolving the icon libraries feature, visit THEME OPTIONS > GENERAL > SOCIAL ICONS and click on "Reset Section" button. This will recover the social icons list on this tab too!', 'plethora-framework');
				$desc .= '</strong>';
		    }

	    	$sections[] = array(
				'title'      => esc_html__('Main Section // Social Bar', 'healthflex'),
				'heading'	 => esc_html__('MAIN HEADER SECTION // SOCIAL BAR OPTIONS', 'healthflex'),
				'desc'	 	 => $desc,
				'subsection' => true,
				'fields'     => array(
					array(
						'id'       => METAOPTION_PREFIX .'socialbar',
						'type'     => 'switch', 
						'title'    => esc_html__('Floating Social Bar', 'healthflex'),
						'subtitle' => esc_html__('Display floating social icons bar.', 'healthflex'),
						"default"  => 1,
						'on'       => esc_html__('Display', 'healthflex') ,
						'off'      => esc_html__('Hide', 'healthflex'),
						),
					$option,
				)
			);
			return $sections;
	    }
	   
	    function subsection_footerlayout( $sections ) {

	    	$sections[] = array(
				'title'      => esc_html__('Main Section', 'healthflex'),
				'heading'      => esc_html__('MAIN SECTION', 'healthflex'),
				'subsection' => true,
				'fields'     => array(
					array(
						'id'       => METAOPTION_PREFIX .'footer-widgets',
						'type'     => 'switch', 
						'title'    => esc_html__('Main Footer Section', 'healthflex'),
						'subtitle' => esc_html__('Display/hide main footer section', 'healthflex'),
						"default"  => 1,
						'on'       => esc_html__('Display', 'healthflex'),
						'off'      => esc_html__('Hide', 'healthflex'),
						),

						array(
							'id'      => METAOPTION_PREFIX .'footer-background',
							'required'     => array( METAOPTION_PREFIX .'footer-widgets','=',1),						
							'type'    => 'button_set', 
							'title'   => esc_html__('Top Separator', 'healthflex'),
							'desc'    => esc_html__('Add or not an angled separator on top.', 'healthflex'),
							'options' => array(
                                'color'      => esc_html__('None', 'healthflex'),
                                'sep_angled_positive_top' => esc_html__('Angle Positive Top', 'healthflex'),
                                'sep_angled_negative_top' => esc_html__('Angle Negative Top', 'healthflex'),
                                ),
							'default' => 'sep_angled_positive_top',
						),	

					array(
						'id'       => METAOPTION_PREFIX .'footer-widgetslayout',
						'type'     => 'image_select',
						'required'     => array(METAOPTION_PREFIX .'footer-widgets','=','1'),						
						'title'    => esc_html__('Widget Columns layout', 'healthflex'), 
						'subtitle' => esc_html__('Click to the icon according to the desired footer layout. ', 'healthflex'),
						'desc'     => esc_html__('Edit content on <strong>Appearence > Widgets</strong> section as <i>Footer column #1</i>, <i>Footer column #2</i>, etc.', 'healthflex'),
						'options'  => array(
								1 => array('alt' => '1 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_1.png'),
								2 => array('alt' => '2 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2.png'),
								3 => array('alt' => '2 Column (2/3 + 1/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_8-4.png'),
								4 => array('alt' => '2 Column (1/3 + 2/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_4-8.png'),
								5 => array('alt' => '3 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3.png'),
								6 => array('alt' => '3 Column (1/4 + 1/4 + 2/4)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3_3-3-6.png'),
								7 => array('alt' => '3 Column (2/4 + 1/4 + 1/4)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3_6-3-3.png'),
								8 => array('alt' => '4 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_4.png'),
							),
						'default' => 7
						),
					 	array(
							'id'       => METAOPTION_PREFIX .'footer-sidebar-one',
							'type'     => 'select',
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets','=',1),
												array( METAOPTION_PREFIX .'footer-widgetslayout','is_larger_equal',1),
										  ),
							'title'    => esc_html__('Column 1 Widgets Area', 'healthflex'), 
							'data'	   => 'sidebars',
							'default'  => 'sidebar-footer-one'
						),
					 	array(
							'id'       => METAOPTION_PREFIX .'footer-sidebar-two',
							'type'     => 'select',
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets','=',1),
												array( METAOPTION_PREFIX .'footer-widgetslayout','is_larger_equal',2),
										  ),
							'title'    => esc_html__('Column 2 Widgets Area', 'healthflex'), 
							'data'	   => 'sidebars',
							'default'  => 'sidebar-footer-two'
						),
					 	array(
							'id'       => METAOPTION_PREFIX .'footer-sidebar-three',
							'type'     => 'select',
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets','=',1),
												array( METAOPTION_PREFIX .'footer-widgetslayout','is_larger_equal',5),
										  ),
							'title'    => esc_html__('Column 3 Widgets Area', 'healthflex'), 
							'data'	   => 'sidebars',
							'default'  => 'sidebar-footer-three'
						),
					 	array(
							'id'       => METAOPTION_PREFIX .'footer-sidebar-four',
							'type'     => 'select',
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets','=',1),
												array( METAOPTION_PREFIX .'footer-widgetslayout','=',8),
										  ),
							'title'    => esc_html__('Column 4 Widgets Area', 'healthflex'), 
							'data'	   => 'sidebars',
							'default'  => 'sidebar-footer-four'
						),

						// Footer styling
						array(
					       'id' => 'footer-section-start',
					       'type' => 'section',
					       'title' => esc_html__('Footer Color Set', 'healthflex'),
					       'subtitle' => esc_html__('Color options for footer section.', 'healthflex'),
					       'indent' => true,
					     ),
								array(
									'id'          => THEMEOPTION_PREFIX .'less-footer-bgcolor',
									'type'        => 'color',
									'title'       => esc_html__('Footer Background', 'healthflex'), 
									'subtitle'    => esc_html__('default: #2c4167.', 'healthflex'),
									'default'     => '#2c4167',
									'transparent' => false,
									'validate'    => 'color',
									),
								array(
									'id'          => THEMEOPTION_PREFIX .'less-footer-txtcolor',
									'type'        => 'color',
									'title'       => esc_html__('Footer Text', 'healthflex'), 
									'subtitle'    => esc_html__('default: #ffffff.', 'healthflex'),
									'default'     => '#ffffff',
									'transparent' => false,
									'validate'    => 'color',
									),
								array(
									'id'          => THEMEOPTION_PREFIX .'less-footer-linkcolor',
									'type'        => 'link_color',
									'title'       => esc_html__('Footer Link Text', 'healthflex'), 
									'subtitle'    => esc_html__('default: #03a9f4/#fbc02d', 'healthflex'),
									'visited'     => false,
									'active'     => false,
								    'default'  => array(
								        'regular'  => '#03a9f4', 
								        'hover'    => '#fbc02d',
								    	),
								    'validate'    => 'color',
									),
						array(
						    'id'     => 'footer-section-end',
						    'type'   => 'section',
						    'indent' => false,
						),	
					)
				);
			return $sections;
	    }

	    function subsection_footerinfobar( $sections ) {

	    	$sections[] = array(
				'title'      => esc_html__('Info Bar', 'healthflex'),
				'heading'      => esc_html__('INFO BAR', 'healthflex'),
				'subsection' => true,
				'fields'     => array(
					array(
						'id'       => METAOPTION_PREFIX .'footer-infobar',
						'type'     => 'switch', 
						'title'    => esc_html__('Footer info bar', 'healthflex'),
						'subtitle' => esc_html__('Display/hide bottom info bar', 'healthflex'),
						"default"  => 1,
						'on'       => esc_html__('Display', 'healthflex'),
						'off'      => esc_html__('Hide', 'healthflex'),
						),

					array(
						'id'=> METAOPTION_PREFIX .'footer-infobar-colorset',
						'type' => 'button_set',
						'required'       => array(METAOPTION_PREFIX .'footer-infobar','=','1'),						
						'title' => esc_html__( 'Color Set', 'healthflex' ),
						'options' => array( 'skincolored_section' => 'Primary', 'secondary_section' => 'Secondary', 'dark_section' => 'Dark', 'light_section' => 'Light', 'black_section' => 'Black', 'white_section' => 'White' ),
						'default' => 'dark_section',
					),

					array(
						'id'      => METAOPTION_PREFIX .'footer-infobar-transparentfilm',
						'type'    => 'switch', 
						'required'       => array(METAOPTION_PREFIX .'footer-infobar','=','1'),						
						'title'   => esc_html__('Transparency Film', 'healthflex'),
						"default" => 1,
					),	
					array(
						'id'        => METAOPTION_PREFIX .'footer-infobarcopyright',
						'type'      => 'textarea',
						'required'  => array(METAOPTION_PREFIX .'footer-infobar','=','1'),						
						'title'     => esc_html__('Copyright text', 'healthflex'), 
						'desc'      => esc_html__('HTML tags allowed', 'healthflex'),
						'default'   => esc_html__('Copyright &copy;2016 all rights reserved', 'healthflex'),
						'translate' => true,
						),
					array(
						'id'        => METAOPTION_PREFIX .'footer-infobarcreds',
						'type'      => 'textarea',
						'required'  => array(METAOPTION_PREFIX .'footer-infobar','=','1'),						
						'title'     => esc_html__('Credits text', 'healthflex'), 
						'desc'      => esc_html__('HTML tags allowed', 'healthflex'),
						'default'   => esc_html__('Designed by', 'healthflex') .' <a href="http://plethorathemes.com" target="_blank">Plethora Themes</a>',
						'translate' => true,
						),
					)
				);
			return $sections;
	    }

	    function subsection_404( $sections ) {

			$sections[] = array(
				'title'      => esc_html__('404 Page', 'healthflex'),
				'heading'      => esc_html__('404 PAGE OPTIONS', 'healthflex'),
				'subsection' => true,
				'fields'     => array(

					array(
						'id'       =>THEMEOPTION_PREFIX .'mediapanel-404-image',
						'type'     => 'media', 
						'title'    => esc_html__('Featured Image', 'healthflex'),
						'url'      => true,
						'default'  =>array('url'=> PLE_THEME_ASSETS_URI .'/images/404_alt.jpg'),
						),
					array(
						'id'      =>THEMEOPTION_PREFIX .'404-title-text',
						'type'    => 'text',
						'title'   => esc_html__('Title', 'healthflex'),
						'default' => esc_html__('OMG! ERROR 404', 'healthflex'),
						'translate' => true
					),
					array(
						'id'      =>THEMEOPTION_PREFIX .'404-subtitle-text',
						'type'    => 'text',
						'title'   => esc_html__('Subtitle', 'healthflex'),
						'default' => esc_html__('The requested page cannot be found!', 'healthflex'),
						'translate' => true
					),
					array(
						'id'      =>THEMEOPTION_PREFIX .'404-contenttitle',
						'type'    => 'text',
						'title'   => esc_html__('Additional Title On Content', 'healthflex'),
						'default' => esc_html__('ERROR 404 IS NOTHING TO REALLY WORRY ABOUT...', 'healthflex'),
						'translate' => true
					),
					array(
						'id'      =>THEMEOPTION_PREFIX .'404-content',
						'type'    => 'textarea',
						'title'   => esc_html__('Content', 'healthflex'), 
						'default' => esc_html__('You may have mis-typed the URL, please check your spelling and try again.', 'healthflex'), 
						'translate' => true
					),
					array(
						'id'      => THEMEOPTION_PREFIX .'404-search',
						'type'    => 'switch', 
						'title'   => esc_html__('Display search field', 'healthflex'),
						"default" => 1,
						'on'      => 'On',
						'off'     => 'Off',
						),	
					array(
						'id'      =>THEMEOPTION_PREFIX .'404-search-btntext',
						'required'     => array(THEMEOPTION_PREFIX .'404-search','=',1),						
						'type'    => 'text',
						'title'   => esc_html__('Search Button Text', 'healthflex'), 
						'default' => esc_html__('Search', 'healthflex'), 
						'translate' => true
					),
				)
			);
			return $sections;
	    }

	    function subsection_search( $sections ) {

			$sections[] = array(
				'title'      => esc_html__('Search Page', 'healthflex'),
				'heading'      => esc_html__('SEARCH PAGE OPTIONS', 'healthflex'),
				'subsection' => true,
				'fields'     => array(

		            array(
		                'id'        => METAOPTION_PREFIX .'search-layout',
		                'title'     => esc_html__( 'Page Layout', 'healthflex' ),
		                'type'      => 'image_select',
						'default'  => 'right_sidebar',
		                'options'   => array( 
								'right_sidebar'	=> ReduxFramework::$_url . 'assets/img/2cr.png',
								'left_sidebar'	=> ReduxFramework::$_url . 'assets/img/2cl.png',
		                )
		            ),
					array(
						'id'=>METAOPTION_PREFIX .'search-sidebar',
						'type' => 'select',
						'data' => 'sidebars',
						'multi' => false,
						'default'  => 'sidebar-default',
						'title' => esc_html__('Sidebar', 'healthflex'), 
					),
					array(
						'id'      => THEMEOPTION_PREFIX .'search-title',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Title On Content', 'healthflex'),
						"default" => 1,
						'on'      => 'On',
						'off'     => 'Off',
						),	
					array(
						'id'      =>THEMEOPTION_PREFIX .'search-title-text',
						'type'    => 'text',
						'title'   => esc_html__('Title Prefix', 'healthflex'),
						'desc'   => esc_html__('Will be displayed before search keyword', 'healthflex'),
						'default' => esc_html__('Search For:', 'healthflex'),
						'translate' => true,
					),
					array(
						'id'      => THEMEOPTION_PREFIX .'search-subtitle',
						'type'    => 'switch', 
						'title'   => esc_html__('Display Subtitle On Content', 'healthflex'),
						"default" => 1,
						'on'      => 'On',
						'off'     => 'Off',
						),	
					array(
						'id'      =>THEMEOPTION_PREFIX .'search-subtitle-text',
						'type'    => 'text',
						'title'   => esc_html__('Subtitle', 'healthflex'),
						'default' => esc_html__('This is the default search subtitle', 'healthflex')
					),
				)
			);
			return $sections;
	    }	    
	// SET THEME SPECIFIC OPTION TABS -> FINISH   

//// THEME OPTIONS PANEL CONFIGURATION ENDS

//// METABOXES CONFIGURATION BEGINS

	  /**
	   * A static method that returns metabox configuration
	   * It is hooked on early priority 'init' by Plethora_Optionsframework class
	   *
	   * @since 1.0
	   */
		public static function metabox_hooks() {

			// Add Header / Footer metaboxes to single post types
			$single_post_types = Plethora_Theme::get_supported_post_types();
      		foreach ( $single_post_types as $key=>$post_type) {

				add_filter( 'plethora_metabox_single'. $post_type, array('Plethora_Themeoptions', 'metabox_header_elements'), 99);
				add_filter( 'plethora_metabox_single'. $post_type, array('Plethora_Themeoptions', 'metabox_footer_elements'), 99);
      		}

			// Add Header / Footer metaboxes to archive post types
			$archive_post_types = Plethora_Theme::get_supported_post_types( array( 'type'=>'archives' ) );
     		foreach ( $archive_post_types as $key=>$post_type) {

				add_filter( 'plethora_metabox_archive'. $post_type, array('Plethora_Themeoptions', 'metabox_header_elements'), 99);
				add_filter( 'plethora_metabox_archive'. $post_type, array('Plethora_Themeoptions', 'metabox_footer_elements'), 99);
      		}
		}

	  /**
	   * Header elements tab for metaboxes
	   *
	   * @since 2.0
	   */
		public static function metabox_header_elements( $sections ) {

	    	// Get social icons selector field & info
	    	if ( method_exists( 'Plethora_Module_Social', 'get_icons_option') ) { 

	    		$selectorfield = Plethora_Module_Social::get_icons_option( array( METAOPTION_PREFIX .'socialbar','=','1'));
	    		$social_icon_option = $selectorfield['option'];
	    		$social_icon_desc = $selectorfield['status'];
	    	}

		    $sections['metabox_header_elements'] = array(
		        'title'         => esc_html__('Header Elements', 'healthflex'),
		        'heading'         => esc_html__('HEADER SECTION ELEMENTS', 'healthflex'),
		        'icon_class'    => 'icon-large',
		        'icon'          => 'el-icon-circle-arrow-up',
		        'fields'        => array(

					array(
				       'id' => 'header-layout-start',
				       'type' => 'section',
				       'title' => esc_html__('Layout Options', 'healthflex'),
				       'indent' => true,
				     ),
						array(
							'id'      => METAOPTION_PREFIX .'header-background',
							'type'    => 'button_set', 
							'title'   => esc_html__('Bottom Separator', 'healthflex'),
							'options' => array(
								'color'      => esc_html__('None', 'healthflex'),
								'separator_bottom sep_angled_positive_bottom' => esc_html__('Angle Positive Bottom', 'healthflex'),
								),
						),	

						array(
							'id'      => METAOPTION_PREFIX .'header-background-transparentfilm',
							'type'    => 'switch', 
							'required' => array( 
											// array( METAOPTION_PREFIX .'header-colorset','!=', 'custom_section'),						
											array( METAOPTION_PREFIX .'header-background','=', 'color'),						
									 	  ),
							'title'   => esc_html__('Transparency', 'healthflex'),
						),	

						array(
							'id'      => METAOPTION_PREFIX .'header-sticky',
							'type'    => 'switch', 
							'title'   => esc_html__('Sticky Header On Scroll', 'healthflex'),
						),	
					array(
				       'id' => 'header-layout-end',
				       'type' => 'section',
				       'indent' => false,
				     ),
					array(
				       'id' => 'header-logo-start',
				       'type' => 'section',
				       'title' => esc_html__('Logo Options', 'healthflex'),
				       'indent' => true,
				     ),
						array(
							'id'       => METAOPTION_PREFIX .'logo',
							'type'     => 'switch', 
							'title'    => esc_html__('Display Logo', 'healthflex'),
							'on'       => esc_html__('Display', 'healthflex') ,
							'off'      => esc_html__('Hide', 'healthflex'),
							),
					array(
				       'id' => 'header-logo-end',
				       'type' => 'section',
				       'indent' => false,
				     ),
					array(
				       'id' => 'header-navigation-start',
				       'type' => 'section',
				       'title' => esc_html__('Main Menu Options', 'healthflex'),
				       'indent' => true,
				     ),
						array(
							'id'       => METAOPTION_PREFIX .'navigation-main',
							'type'     => 'switch', 
							'title'    => esc_html__('Display Main Menu', 'healthflex'),
							'on'       => esc_html__('Display', 'healthflex') ,
							'off'      => esc_html__('Hide', 'healthflex'),
							),

						array(
							'id'      => METAOPTION_PREFIX .'navigation-main-location',
							'required' => array( METAOPTION_PREFIX .'navigation-main','=',1),						
							'type'    => 'select',
							'title'   => esc_html__('Main Menu Location', 'healthflex'), 
							'desc'    => esc_html__('Select the default location to be displayed as your main menu. You have the possibility to change the main navigation location for every page. ', 'healthflex'),
							'data'    => 'menu_locations',
						),
					array(
				       'id' => 'header-navigation-end',
				       'type' => 'section',
				       'indent' => false,
				     ),
					array(
				       'id' => 'header-social-start',
				       'type' => 'section',
				       'title' => esc_html__('Social Bar Options', 'healthflex'),
				       'desc' => $social_icon_desc,
				       'indent' => true,
				     ),
					array(
						'id'       => METAOPTION_PREFIX .'socialbar',
						'type'     => 'switch', 
						'title'    => esc_html__('Floating Social Bar', 'healthflex'),
						'on'       => esc_html__('Display', 'healthflex') ,
						'off'      => esc_html__('Hide', 'healthflex'),
						),
					array(
				       'id' => 'header-social-end',
				       'type' => 'section',
				       'indent' => false,
				     ),
		        )
		    );
				
			return apply_filters( 'plethora_themeoptions_metabox_header_elements', $sections );
		}

	  /**
	   * Header elements tab for metaboxes
	   *
	   * @since 2.0
	   */
		public static function metabox_footer_elements( $sections ) {

		    $sections['metabox_footer_elements'] = array(
				'title'      => esc_html__('Footer Elements', 'healthflex'),
				'heading'    => esc_html__('FOOTER SECTION ELEMENTS', 'healthflex'),
				'icon_class' => 'icon-large',
				'icon'       => 'el-icon-circle-arrow-down',
				'fields'     => array(

					array(
				       'id' => 'footer-main-start',
				       'type' => 'section',
				       'title' => esc_html__('Main Section Options', 'healthflex'),
				       'indent' => true,
				     ),
						array(
							'id'       => METAOPTION_PREFIX .'footer-widgets',
							'type'     => 'switch', 
							'title'    => esc_html__('Main Footer Section', 'healthflex'),
							'subtitle' => esc_html__('Display/hide main footer section', 'healthflex'),
							'on'       => esc_html__('Display', 'healthflex'),
							'off'      => esc_html__('Hide', 'healthflex'),
							),
						
						array(
							'id'      => METAOPTION_PREFIX .'footer-background',
							'required'     => array( METAOPTION_PREFIX .'footer-widgets','=',1),						
							'type'    => 'button_set', 
							'title'   => esc_html__('Top Separator', 'healthflex'),
							'options' => array(
                                'color'      => esc_html__('None', 'healthflex'),
                                'sep_angled_positive_top' => esc_html__('Angle Positive Top', 'healthflex'),
                                'sep_angled_negative_top' => esc_html__('Angle Negative Top', 'healthflex'),
                                ),
						),	

						array(
							'id'       => METAOPTION_PREFIX .'footer-widgetslayout',
							'type'     => 'image_select',
							'required'     => array(METAOPTION_PREFIX .'footer-widgets','=','1'),						
							'title'    => esc_html__('Widget Columns layout', 'healthflex'), 
							'subtitle' => esc_html__('Click to the icon according to the desired footer layout. ', 'healthflex'),
							'desc'     => esc_html__('Edit content on \'Appearence > Widgets\' section as', 'healthflex' ) .' <i>'. esc_html__( 'Footer column #1', 'healthflex') .'</i>,<i>' . esc_html__('Footer column #2', 'healthflex') .'</i> '. esc_html__('etc.', 'healthflex'),
							'options'  => array(
									'1' => array('alt' => '1 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_1.png'),
									'2' => array('alt' => '2 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2.png'),
									'3' => array('alt' => '2 Column (2/3 + 1/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_8-4.png'),
									'4' => array('alt' => '2 Column (1/3 + 2/3)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_2_4-8.png'),
									'5' => array('alt' => '3 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3.png'),
									'6' => array('alt' => '3 Column (1/4 + 1/4 + 2/4)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3_3-3-6.png'),
									'7' => array('alt' => '3 Column (2/4 + 1/4 + 1/4)', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_3_6-3-3.png'),
									'8' => array('alt' => '4 Column', 'img' => PLE_CORE_ASSETS_URI.'/images/redux/col_4.png'),
								),
							),

					 	array(
							'id'       => METAOPTION_PREFIX .'footer-sidebar-one',
							'type'     => 'select',
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets','=',1),
												array( METAOPTION_PREFIX .'footer-widgetslayout','is_larger_equal',1),
										  ),
							'title'    => esc_html__('Column 1 Widgets Area', 'healthflex'), 
							'data'	   => 'sidebars',
						),

					 	array(
							'id'       => METAOPTION_PREFIX .'footer-sidebar-two',
							'type'     => 'select',
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets','=',1),
												array( METAOPTION_PREFIX .'footer-widgetslayout','is_larger_equal',2),
										  ),
							'title'    => esc_html__('Column 2 Widgets Area', 'healthflex'), 
							'data'	   => 'sidebars',
						),
					 	array(
							'id'       => METAOPTION_PREFIX .'footer-sidebar-three',
							'type'     => 'select',
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets','=',1),
												array( METAOPTION_PREFIX .'footer-widgetslayout','is_larger_equal',5),
										  ),
							'title'    => esc_html__('Column 3 Widgets Area', 'healthflex'), 
							'data'	   => 'sidebars',
						),
					 	array(
							'id'       => METAOPTION_PREFIX .'footer-sidebar-four',
							'type'     => 'select',
							'required' => array(
												array( METAOPTION_PREFIX .'footer-widgets','=',1),
												array( METAOPTION_PREFIX .'footer-widgetslayout','=',8),
										  ),
							'title'    => esc_html__('Column 4 Widgets Area', 'healthflex'), 
							'data'	   => 'sidebars',
						),

					array(
				       'id' => 'footer-main-end',
				       'type' => 'section',
				       'indent' => false,
				     ),
					array(
				       'id' => 'footer-infobar-start',
				       'type' => 'section',
				       'title' => esc_html__('Info Bar Options', 'healthflex'),
				       'indent' => true,
				     ),

						array(
							'id'       =>METAOPTION_PREFIX .'footer-infobar',
							'type'     => 'switch', 
							'title'    => esc_html__('Footer info bar', 'healthflex'),
							'subtitle' => esc_html__('Display/hide bottom info bar', 'healthflex'),
							'on'       => esc_html__('Display', 'healthflex'),
							'off'      => esc_html__('Hide', 'healthflex'),
							),

						array(
							'id'=> METAOPTION_PREFIX .'footer-infobar-colorset',
							'type' => 'button_set',
							'required'       => array(METAOPTION_PREFIX .'footer-infobar','=','1'),						
							'title' => esc_html__( 'Color Set', 'healthflex' ),
							'options' => array( 'skincolored_section' => 'Primary', 'secondary_section' => 'Secondary', 'dark_section' => 'Dark', 'light_section' => 'Light', 'black_section' => 'Black', 'white_section' => 'White' ),
						),
						
						array(
							'id'      => METAOPTION_PREFIX .'footer-infobar-transparentfilm',
							'type'    => 'switch', 
							'required'       => array(METAOPTION_PREFIX .'footer-infobar','=','1'),						
							'title'   => esc_html__('Transparency Film', 'healthflex'),
						),	
					array(
				       'id' => 'footer-infobar-end',
				       'type' => 'section',
				       'indent' => false,
				     ),

		        )
		    );
			return $sections;
		}

//// METABOXES CONFIGURATION ENDS

//// LESS CONFIGURATION BEGINS
		public static function less_variables( $vars ) { 

			// Color Sets > Basic
			$vars['wp-brand-primary']    = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-brand-primary', '#4EABF9', 0, false);
			$vars['wp-brand-secondary']  = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-brand-secondary', '#304770', 0, false);
			$vars['wp-body-bg']          = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-body-bg', '#EFEFEF', 0, false);
			$vars['wp-text-color']       = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-text-color', '#323232', 0, false);
			$link                        = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-link-color', array('regular'=>'#4EABF9', 'hover'=>'#2885D3'), 0, false);
			$vars['wp-link-color']       = $link['regular'];
			$vars['wp-link-hover-color'] = $link['hover'];
			
			// Color Sets > Primary
			$vars['wp-skin-section-txtcolor']        = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-skin-section-txtcolor', '#ffffff', 0, false);
			$link                                    = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-skin-section-linkcolor', array('regular'=>'#F8EB80', 'hover'=>'#B2B300'), 0, false);
			$vars['wp-skin-section-linkcolor']       = $link['regular'];
			$vars['wp-skin-section-linkcolor-hover'] = $link['hover'];

			// Color Sets > Secondary
			$vars['wp-secondary-section-txtcolor']        = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-secondary-section-txtcolor', '#ffffff', 0, false);
			$link                                         = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-secondary-section-linkcolor', array('regular'=>'#F8EB80', 'hover'=>'#B2B300'), 0, false);
			$vars['wp-secondary-section-linkcolor']       = $link['regular'];
			$vars['wp-secondary-section-linkcolor-hover'] = $link['hover'];

			// Color Sets > Light
			$vars['wp-light-section-bgcolor']         = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-light-section-bgcolor', '#aaaaaa', 0, false);
			$vars['wp-light-section-txtcolor']        = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-light-section-txtcolor', '#323232', 0, false);
			$link                                     = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-light-section-linkcolor', array('regular'=>'#1c92b9', 'hover'=>'#006C93'), 0, false);
			$vars['wp-light-section-linkcolor']       = $link['regular'];
			$vars['wp-light-section-linkcolor-hover'] = $link['hover'];

			// Color Sets > Dark
			$vars['wp-dark-section-bgcolor']         = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-dark-section-bgcolor', '#222D3F', 0, false);
			$vars['wp-dark-section-txtcolor']        = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-dark-section-txtcolor', '#ffffff', 0, false);
			$link                                    = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-dark-section-linkcolor', array('regular'=>'#FF6B10', 'hover'=>'#D94500'), 0, false);
			$vars['wp-dark-section-linkcolor']       = $link['regular'];
			$vars['wp-dark-section-linkcolor-hover'] = $link['hover'];

			// Color Sets > White
			$vars['wp-white-section-bgcolor']         = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-white-section-bgcolor', '#ffffff', 0, false);
			$vars['wp-white-section-txtcolor']        = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-white-section-txtcolor', '#323232', 0, false);
			$link                                     = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-white-section-linkcolor', array('regular'=>'#4EABF9', 'hover'=>'#006C93'), 0, false);
			$vars['wp-white-section-linkcolor']       = $link['regular'];
			$vars['wp-white-section-linkcolor-hover'] = $link['hover'];

			// Color Sets > Black
			$vars['wp-black-section-bgcolor']         = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-black-section-bgcolor', '#151515', 0, false);
			$vars['wp-black-section-txtcolor']        = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-black-section-txtcolor', '#ffffff', 0, false);
			$link                                     = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-black-section-linkcolor', array('regular'=>'#FF6B10', 'hover'=>'#FFFFA6'), 0, false);
			$vars['wp-black-section-linkcolor']       = $link['regular'];
			$vars['wp-black-section-linkcolor-hover'] = $link['hover'];

			// Typography
			$font_serif                            = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-font-family-sans-serif', array('font-family'=>'Lato'), 0, false);
			$font_alt                              = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-font-family-alternative', array('font-family'=>'Raleway'), 0, false);
			$font_size_base                        = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-font-size-base', array('font-size'=>'15px'), 0, false);
			$font_size_base_alt                    = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-font-size-alternative-base', array('font-size'=>'16px'), 0, false);
			$h1_multiplier                         = Plethora_Theme::option(THEMEOPTION_PREFIX .'headings-h1-sizemultiplier', '2.6', 0, false);
			$h2_multiplier                         = Plethora_Theme::option(THEMEOPTION_PREFIX .'headings-h2-sizemultiplier', '1.8', 0, false);
			$h3_multiplier                         = Plethora_Theme::option(THEMEOPTION_PREFIX .'headings-h3-sizemultiplier', '1.6', 0, false);
			$h4_multiplier                         = Plethora_Theme::option(THEMEOPTION_PREFIX .'headings-h4-sizemultiplier', '1.2', 0, false);
			$h5_multiplier                         = Plethora_Theme::option(THEMEOPTION_PREFIX .'headings-h5-sizemultiplier', '1.0', 0, false);
			$h6_multiplier                         = Plethora_Theme::option(THEMEOPTION_PREFIX .'headings-h6-sizemultiplier', '0.85', 0, false);
			$heading_trans                         = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-headings-text-transform', array('text-transform'=>'uppercase'), 0, false);
			$heading_weight                        = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-headings-font-weight', array('font-weight'=>'normal'), 0, false);
			$button_text_trans                     = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-btn-text-transform', array('text-transform'=>'uppercase'), 0, false);
			$vars['wp-font-family-sans-serif']     = $font_serif['font-family'];
			$vars['wp-font-family-alternative']    = $font_alt['font-family'];
			$vars['wp-font-size-base']             = $font_size_base['font-size'];
			$vars['wp-font-size-alternative-base'] = $font_size_base_alt['font-size'];
			$vars['wp-h1-multiplier']              = $h1_multiplier;
			$vars['wp-h2-multiplier']              = $h2_multiplier;
			$vars['wp-h3-multiplier']              = $h3_multiplier;
			$vars['wp-h4-multiplier']              = $h4_multiplier;
			$vars['wp-h5-multiplier']              = $h5_multiplier;
			$vars['wp-h6-multiplier']              = $h6_multiplier;
			$vars['wp-headings-text-transform']    = $heading_trans['text-transform'];
			$vars['wp-headings-font-weight']       = $heading_weight['font-weight'];
			$vars['wp-btn-text-transform']         = $button_text_trans['text-transform'];
			// Logo
			$logo_vert_margin       = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-logo-vertical-margin', array('height'=>'15'), 0, false);
			$logo_vert_margin_sm    = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-logo-vertical-margin-sm', array('height'=>'10'), 0, false);
			$logo_vert_margin_xs    = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-logo-vertical-margin-xs', array('height'=>'15'), 0, false);
			$logo_img_max_height    = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-logo-img-max-height', array('height'=>'32'), 0, false);
			$logo_img_max_height_sm = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-logo-img-max-height-sm', array('height'=>'28'), 0, false);
			$logo_img_max_height_xs = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-logo-img-max-height-xs', array('height'=>'24'), 0, false);
			$logo_font_size         = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-logo-font-size', array('font-size'=>'32px'), 0, false);
			$logo_font_size_sm      = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-logo-font-size-sm', array('font-size'=>'28px'), 0, false);
			$logo_font_size_xs      = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-logo-font-size-xs', array('font-size'=>'22px'), 0, false);
			$vars['wp-logo-vertical-margin']    = $logo_vert_margin['height'] . 'px';
			$vars['wp-logo-vertical-margin-sm'] = $logo_vert_margin_sm['height'] . 'px';
			$vars['wp-logo-vertical-margin-xs'] = $logo_vert_margin_xs['height'] . 'px';
			$vars['wp-logo-img-max-height']     = $logo_img_max_height['height'] . 'px';
			$vars['wp-logo-img-max-height-sm']  = $logo_img_max_height_sm['height'] . 'px';
			$vars['wp-logo-img-max-height-xs']  = $logo_img_max_height_xs['height'] . 'px';
			$vars['wp-logo-font-size']          = $logo_font_size['font-size'];
			$vars['wp-logo-font-size-sm']       = $logo_font_size_sm['font-size'];
			$vars['wp-logo-font-size-xs']       = $logo_font_size_xs['font-size'];

			// Misc > Media Panel
			$vars['wp-full-width-photo-min-panel-height']    = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-less-full-width-photo-min-panel-height', '380x', 0, false) .'px';
			$vars['wp-full-width-photo-min-panel-height-sm'] = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-less-full-width-photo-min-panel-height-sm', '280', 0, false) .'px';
			$vars['wp-full-width-photo-min-panel-height-xs'] = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-less-full-width-photo-min-panel-height-xs', '180', 0, false) .'px';
			$vars['wp-map-panel-height']                     = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-less-map-panel-height', '480', 0, false) .'px';
			$vars['wp-map-panel-height-sm']                  = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-less-map-panel-height-sm', '280', 0, false) .'px';
			$vars['wp-map-panel-height-xs']                  = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-less-map-panel-height-xs', '180', 0, false) .'px';

			// Header
			$vars['wp-header-background-transparency']                   = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-header-background-transparency', 100, 0, false);
			$vars['wp-header-diagonal-gradient-left-area-color']         = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-header-diagonal-gradient-left-area-color', '#4EABF9', 0, false);
			$vars['wp-header-diagonal-gradient-right-area-color']        = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-header-diagonal-gradient-right-area-color', '#304770', 0, false);
			$vars['wp-header-diagonal-gradient-left-area-percentage']    = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-header-diagonal-gradient-left-area-percentage', '30%', 0, false);
			$vars['wp-header-diagonal-gradient-left-area-percentage-md'] = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-header-diagonal-gradient-left-area-percentage-md', '30%', 0, false);
			$vars['wp-header-diagonal-gradient-left-area-percentage-sm'] = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-header-diagonal-gradient-left-area-percentage-sm', '50%', 0, false);
			$vars['wp-header-diagonal-gradient-left-area-percentage-xs'] = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-header-diagonal-gradient-left-area-percentage-xs', '50%', 0, false);
			$vars['wp-header-bgcolor']         = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-header-bgcolor', '#304770', 0, false);
			$vars['wp-header-txtcolor']        = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-header-txtcolor', '#ffffff', 0, false);
			$link_color                        = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-header-linkcolor', array('regular'=>'#ffffff', 'hover'=>'#4EABF9'), 0, false);
			$vars['wp-header-linkcolor']       = $link_color['regular'];
			$vars['wp-header-linkcolor-hover'] = $link_color['hover'];

			// Top Bar
			$vars['wp-topbar-bgcolor']                  = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-topbar-bgcolor', '#232323', 0, false);
			$vars['wp-topbar-txtcolor']                 = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-topbar-txtcolor', '#ffffff', 0, false);
			$link_color                                 = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-topbar-linkcolor', array('regular'=>'#F8EB80', 'hover'=>'#FCF6C9'), 0, false);
			$vars['wp-topbar-linkcolor']                = $link_color['regular'];
			$vars['wp-topbar-linkcolor-hover']          = $link_color['hover'];

			// Footer
			$vars['wp-footer-bgcolor']                  = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-footer-bgcolor', '#232323', 0, false);
			$vars['wp-footer-txtcolor']                 = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-footer-txtcolor', '#ffffff', 0, false);
			$link_color                                 = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-footer-linkcolor', array('regular'=>'#F8EB80', 'hover'=>'#FCF6C9'), 0, false);
			$vars['wp-footer-linkcolor']                = $link_color['regular'];
			$vars['wp-footer-linkcolor-hover']          = $link_color['hover'];

			// Main Navigation
			$menu_font_size    = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-menu-font-size', array('font-size'=>'13px'), 0, false);
			$menu_vert_padd    = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-menu-item-vertical-padding', array('height'=>'10'), 0, false);
			$menu_vert_padd_md = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-menu-item-vertical-padding-md', array('height'=>'10'), 0, false);
			$menu_vert_padd_sm = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-menu-item-vertical-padding-sm', array('height'=>'15'), 0, false);
			$menu_hor_padd     = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-menu-item-horizontal-padding', array('width'=>'15'), 0, false);
			$menu_hor_padd_md  = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-menu-item-horizontal-padding-md', array('width'=>'10'), 0, false);
			$menu_hor_padd_sm  = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-menu-item-horizontal-padding-sm', array('width'=>'15'), 0, false);
			$vars['wp-menu-font-size']                  = $menu_font_size['font-size'];
			$vars['wp-menu-item-vertical-padding']      = $menu_vert_padd['height'] . 'px';
			$vars['wp-menu-item-vertical-padding-md']   = $menu_vert_padd_md['height'] . 'px';
			$vars['wp-menu-item-vertical-padding-sm']   = $menu_vert_padd_sm['height'] . 'px';
			$vars['wp-menu-item-horizontal-padding']    = $menu_hor_padd['width'] . 'px';
			$vars['wp-menu-item-horizontal-padding-md'] = $menu_hor_padd_md['width'] . 'px';
			$vars['wp-menu-item-horizontal-padding-sm'] = $menu_hor_padd_sm['width'] . 'px';
			// Misc Global Options			
			$diagonal_gradient_angle	= Plethora_Theme::option(THEMEOPTION_PREFIX .'less-diagonal-gradient-angle', 45, 0, false);
			$vars['wp-diagonal-gradient-angle']         = $diagonal_gradient_angle .'deg';
			$vars['wp-section-background-transparency'] = Plethora_Theme::option(THEMEOPTION_PREFIX .'less-section-background-transparency', 50, 0, false);
			
			return $vars;

		}
//// LESS CONFIGURATION ENDS

	}
endif;