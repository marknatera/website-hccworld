<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2013

File Description: About Us Widget Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( class_exists('Plethora_Widget') && !class_exists('Plethora_Widget_Aboutus') ) {

	/**
	 * @package Plethora Framework
	 */
	class Plethora_Widget_Aboutus extends WP_Widget  {

		public static $feature_title          = "About Us";							// FEATURE DISPLAY TITLE
		public static $feature_description    = "Display your company information";	// FEATURE DISPLAY DESCRIPTION (STRING)
		public static $theme_option_control   = true;        						// WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL ( BOOLEAN )
		public static $theme_option_default   = true;        						// DEFAULT ACTIVATION OPTION STATUS ( BOOLEAN )
		public static $theme_option_requires  = array();        					// WHICH FEATURES ARE REQUIRED TO BE ACTIVE FOR THIS FEATURE TO WORK ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct      = false;        						// DYNAMIC CLASS CONSTRUCTION ? ( BOOLEAN )
		public static $dynamic_method         = false; 								// ADDITIONAL METHOD INVOCATION ( STRING/BOOLEAN | METHOD NAME OR FALSE ) | THIS A PARENT METHOD, FOR ADDING ACTION
		public static $wp_slug 				  =  'aboutus-widget';					// SCRIPT & STYLE FILES. THIS SHOULD BE THE WP SLUG OF THE CONTENT ELEMENT ( WITHOUT THE PREFIX CONSTANT )
		public static $assets;

		public function __construct() { 

            /* LEAVE INTACT ACROSS WIDGET CLASSES */

            $id_base     = WIDGETS_PREFIX . self::$wp_slug;
            $name        = '> PL | ' . self::$feature_title;
            $widget_ops  = array( 
              'classname'   => self::$wp_slug, 
              'description' => self::$feature_title 
              );
            $control_ops = array( 'id_base' => $id_base );

            parent::__construct( $id_base, $name, $widget_ops, $control_ops );      // INSTANTIATE PARENT OBJECT

            /* ADDITIONAL WIDGET CODE STARTS HERE */

		}

		function widget( $args, $instance ) {

			extract( $args );	// EXTRACT USER INPUT

	        // PACK DEFAULT TEMPLATE VALUES [ LEAVE INTACT ]
	        $widget_atts = array (
								'widget_id'     => $widget_id,  
								'before_widget' => $before_widget,  
								'after_widget'  => $after_widget,  
								'before_title'  => $before_title,  
								'after_title'   => $after_title
								);

	        // PACK ADDITIONAL TEMPLATE VALUES 
			$widget_atts = array_merge( $widget_atts, array(

								'logo'         => $instance['logo'],
								'title'        => apply_filters('widget_title', $instance['title'] ),  
								'description'  => $instance['description'],
								'address'      => $instance['address'],
								'googleMapURL' => $instance['googleMapURL'],
								'telephone'    => $instance['telephone'], 
								'email'        => $instance['email'], 
								'url'          => $instance['url'],
								'socials'      => $instance['socials'],
				));
			
			// Add websie socials
			if ( $instance['socials'] ) { 

				$site_socials = Plethora_Module_Social::get_icons('all');
				$socials = array();
				foreach ( $site_socials as $key => $social ) {

					$socials['social_items'][] = array( 
								'social_title' => esc_attr( $social['title'] ),
								'social_icon'  => esc_attr( $social['icon'] ),
								'social_url'   => esc_url( $social['url'] )
								);
				}
			} else {

				$socials['social_items'] = '';

			}

			$widget_atts = array_merge( $widget_atts, $socials );

            echo Plethora_WP::renderMustache( array( "data" => $widget_atts, "file" => __FILE__ ) );

		}

		function update( $new_instance, $old_instance ) {

			return $new_instance;

		}

		function form( $instance ) {

			$logo = PLE_THEME_ASSETS_URI .'/images/logo-white.png';
			$defaults = array(
				'logo' 		  => $logo,
				'title' 	  => '',
				'description' => esc_html__('Premium HTML Template mainly Medical Oriented but so flexible that it can fit any Business Site!', 'plethora-framework'),
				'address' 	  => esc_html__('79 Folsom Ave, San Francisco, CA 94107', 'plethora-framework'),
				'googleMapURL'=> '',
				'telephone'   => '(+30) 210 1234567',
				'email'		  => 'info@plethorathemes.com',
				'url'		  => 'http://plethorathemes.com',
				'socials'	  => 1,
			);

			$instance = wp_parse_args((array) $instance, $defaults); 

			foreach( $instance as $key => $value ){

				if ( $key === 'socials') {

					$checked0 = !$instance[$key] ? ' checked' : '';
					$checked1 = $instance[$key] ? ' checked' : '';
					echo '<p><div><label for="' . $this->get_field_id($key) . '">' . sprintf( esc_attr__( '%s', 'plethora-framework' ), ucfirst($key) ) . '</label></div>';
					echo '<div>';
					echo '<label><input type="radio" class="widefat" id="' . esc_attr( $this->get_field_id($key) ) . '" name="' . esc_attr( $this->get_field_name($key) ) .'" value="1" '.$checked1.' /> Yes </label>';
					echo '<label><input type="radio" class="widefat" id="' . esc_attr( $this->get_field_id($key) ) . '" name="' . esc_attr( $this->get_field_name($key) ) .'" value="0" '.$checked0.' style="margin-left:20px;" /> No </label>';
					echo '</div></p>';

				} elseif ( $key === 'logo') {
					
					echo '<p class="media-manager">';
					echo '  <label for="'. $this->get_field_id('logo') .'">'. sprintf( esc_attr__( '%s:', 'plethora-framework' ), "Logo Image" ) .'</label><br />';
					echo '  <img class="'. esc_attr( $this->id ).'_thumbnail" src="'.  esc_url($instance['logo']) .'" style="margin:0;padding:0;max-width:100px;float:left;display:inline-block" />';
					echo '  <input type="text" class="widefat '.esc_attr( $this->id ).'_url" name="'.  esc_attr( $this->get_field_name('logo') ) .'" id="'.  esc_attr( $this->get_field_id('logo') ) .'" value="'. esc_url( $instance[$key] ) .'">';
					echo '  <input type="button" value="'. esc_attr__('Upload Image', 'plethora-framework') .'" class="button custom_media_upload" id="'.esc_attr( $this->id ).'"/>';
					echo '</p>';
				
				} else {

					echo '<p><div><label for="' . esc_attr( $this->get_field_id($key) ) . '">' . sprintf( esc_html__( '%s', 'plethora-framework' ), ucfirst($key) ) . '</label></div>';
					echo '<div><input type="text" class="widefat" id="' . esc_attr( $this->get_field_id($key) ) . '" name="' . esc_attr( $this->get_field_name($key) ) .'" value="' . esc_attr( $instance[$key] ) . '" /></div></p>';
				}
			};
		}

	}
	
 }