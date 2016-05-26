<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Advanced theme options tab

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Developertools') ) {

	/**
	 */
	class Plethora_Module_Developertools {


		public static $feature_title         = "Developer Tools";					// FEATURE DISPLAY TITLE
		public static $feature_description   = "Integration module for developer tools & options";	// FEATURE DISPLAY DESCRIPTION 
		public static $theme_option_control  = false;											// WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL? 
		public static $theme_option_default	 = false;											// DEFAULT ACTIVATION OPTION STATUS
		public static $theme_option_requires = array();											// WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct	 = true;											// DYNAMIC CLASS CONSTRUCTION? 
		public static $dynamic_method		 = false;											// ADDITIONAL METHOD INVOCATION ( string/boolean | method name or false )

		function __construct(){

		  if ( is_admin() ) { 

		      // Set theme options tab for media panel
		      add_filter( 'plethora_themeoptions_advanced', array( $this, 'theme_features_options_tab'), 998);
		      add_filter( 'plethora_themeoptions_advanced', array( $this, 'theme_devcomments_options_tab'), 999);
	      }
		}

		public static function get_features_options(){

			$controllers = Plethora_Theme::get_controllers();
			
			$config = array();
			foreach ( $controllers as $key=>$controller ) {

			    $config[] = array(
			              'id'=>'header-features-'.$controller['slug'].'-start',
			              'type' => 'section',
				      	  'indent' => true,
			              'title' => $controller['title'],
			              'subtitle' => $controller['description']
			            );
			    $features = Plethora_Theme::get_features( array( 'controller' => $controller['slug'] ) );
			    foreach ( $features as $key=>$feature ) {

			      	if ( !empty($feature['theme_option_control_config'])) {

			        	$config[] = $feature['theme_option_control_config'];
			    	}
			    }
			    $config = apply_filters( 'plethora_'.$controller['slug'].'_features_options', $config, $controller['slug'] );
			    $config[] = array(
			              'id'=>'header-features-'.$controller['slug'].'-end',
			              'type' => 'section',
				      	  'indent' => false,
			            );

			}
			return apply_filters( 'plethora_features_options', $config );
		}


	    static function theme_devcomments_options_tab( $sections ) { 


			$adv_settings = array();

			$adv_settings[] = array(
			              'id'=>'dev-options-start',
			              'type' => 'section',
				      	  'indent' => true,
			              'title' => 'Development Options',
			            );
		    $adv_settings[] = array(
				'id'          => THEMEOPTION_PREFIX . 'dev',
				'type'        => 'button_set',
				'title'       => esc_html__('Development Mode', 'plethora-framework'),
				'options'     => array( 1 => esc_html__('Enable', 'plethora-framework'), 0 => esc_html__('Disable', 'plethora-framework')),
				'desc'    => esc_html__('<strong style="color:red">NOTICE: Don\'t forget to switch back to Production mode after the final launch of the website!</span>', 'plethora-framework'),
				'default'     => 0
			);

			$adv_settings[] = array(
				'id'      => THEMEOPTION_PREFIX . 'dev-options',
				'type'    => 'button_set', 
				'required'=> array( THEMEOPTION_PREFIX .'dev','=', 1),						
				'title'   => esc_html__('HTML Comments // Options', 'plethora-framework'),
				'desc'    => esc_html__('Enable options information comments. This will help you understand how options applied and how affect several components display.', 'plethora-framework'),
				'default' => 'enable',
				'options' => array(
						'enable'  => esc_html__('Enable', 'plethora-framework'),
						'disable' => esc_html__('Disable', 'plethora-framework'),
						),
				);

			$adv_settings[] = array(
				'id'      => THEMEOPTION_PREFIX . 'dev-templateparts',
				'type'    => 'button_set', 
				'required'=> array( THEMEOPTION_PREFIX .'dev','=', 1),						
				'title'   => esc_html__('HTML Comments // Template Parts Files', 'plethora-framework'),
				'desc'    => esc_html__('Enable template part loading information. This will help you understand how the template system works.', 'plethora-framework'),
				'default' => 'enable',
				'options' => array(
						'enable'  => esc_html__('Enable', 'plethora-framework'),
						'disable' => esc_html__('Disable', 'plethora-framework'),
						),
				);
			$adv_settings[] = array(
				'id'      => THEMEOPTION_PREFIX . 'dev-layout',
				'type'    => 'button_set', 
				'title'   => esc_html__('HTML Comments // Layout Checkpoints', 'plethora-framework'),
				'desc'    => esc_html__('Enable start/end layout checkpoints information. This will help you separate easily the most important parts of the page on the html source view!', 'plethora-framework'),
				'default' => 'enable',
				'options' => array(
						'enable'  => esc_html__('Enable', 'plethora-framework'),
						'disable' => esc_html__('Disable', 'plethora-framework'),
						),
				);
			$adv_settings[] = array(
			              'id'=>'dev-options-end',
			              'type' => 'section',
				      	  'indent' => false,
			            );


    		$desc = esc_html__('Those tools will help the developer to:' , 'plethora-framework');
    		$desc .= '<ol>';
    		$desc .= '<li style="margin-left:15px; line-height:24px;">'. esc_html__('Produce unminified JS & CSS output when developer mode is enabled' , 'plethora-framework') . '</li>';
    		$desc .= '<li style="margin-left:15px; line-height:24px;">'. esc_html__('Understand faster how options and template parts work on this theme by enabling related comments inside HTML output of each page' , 'plethora-framework') . '</li>';
    		$desc .= '</ol>';
    		$desc .= esc_html__('Note that for security reasons, developer comments will be output only to logged users with options editing capabilities' , 'plethora-framework');

    		$devmode_on = Plethora_Theme::is_developermode() ? ' <span style="color:aqua;" title="Developer mode is enabled!">DEV MODE ENABLED</span>' : '';
			$sections[] = array(
				'subsection' => true,
				'title'      => esc_html__('Developer Tools', 'plethora-framework') . $devmode_on,
				'desc'       => $desc,
				'heading'    => esc_html__('DEVELOPER TOOLS', 'plethora-framework'),
				'fields'     => $adv_settings
				);
			return $sections;
	    }

	    static function theme_features_options_tab( $sections ) { 

		    $adv_settings = self::get_features_options();
    		$desc = esc_html__('Deliver a light installation website by safely disabling several functionality that you will not actually use.' , 'plethora-framework') . '</li>';
    		$desc .= '<br><br>'. esc_html__('Please note the following' , 'plethora-framework') . '</li>';
    		$desc .= '<ol>';
    		$desc .= '<li style="margin-left:15px; line-height:24px;">'. esc_html__('Deactivating a post type doesn\'t mean that its posts will be deleted from the database too. When you activate the post type again, your posts will be present!' , 'plethora-framework') . '</li>';
    		$desc .= '<li style="margin-left:15px; line-height:24px;">'. esc_html__('The Post Types manager will display custom post types created from third party plugins ( i.e. the "Custom Post Type UI" plugin). Deactivating third party CPTs will just remove the related archive/single view configuration tabs on THEME OPTIONS > CONTENT . In simple words, it will just remove any frontend display configuration, not the post type itself.' , 'plethora-framework') . '</li>';
    		$desc .= '<li style="margin-left:15px; line-height:24px;">'. esc_html__('Deactivating a shortcode doesn\'t mean that all of its instances will be removed too. Unfortunately you will have to remove manualy those shortcodes on each page they are displayed.' , 'plethora-framework') . '</li>';
    		$desc .= '<li style="margin-left:15px; line-height:24px;">'. esc_html__('Deactivating some features might cause the deactivation of other features affected by it. In example, deactivating the \'Profile Post Type\' will force the deactivation of the \'Profiles Grid\' shortcode too.' , 'plethora-framework') . '</li>';
    		$desc .= '</ol>';

			$sections[] = array(
				'subsection' => true,
				'title'      => esc_html__('Features Library', 'plethora-framework'),
				'desc'       => $desc,
				'heading'    => esc_html__('PLETHORA FEATURES LIBRARY ACTIVATION / DEACTIVATION', 'plethora-framework'),
				'fields'     => $adv_settings
				);

			return $sections;
	    }
	}
}