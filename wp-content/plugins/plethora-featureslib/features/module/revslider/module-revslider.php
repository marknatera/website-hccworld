<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Revolution Slider Plugin Support Module Base Class

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Module_Revslider') ) {

	class Plethora_Module_Revslider {
        
		public static $feature_title         = "Revolution Slider Support Module";							// Feature display title  (string)
		public static $feature_description   = "Adds support for Revolution Slider plugin to your theme";	// Feature display description (string)
		public static $theme_option_control  = true;													// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = true;											// Default activation option status ( boolean )
		public static $theme_option_requires = array();									// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;												// Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;											// Additional method invocation ( string/boolean | method name or false )
		
		public function __construct() {

			if ( function_exists( 'set_revslider_as_theme' ) ) {

				add_action( 'init', array( $this, 'set_revslider_as_theme' ) );
			}
		}


	    /**
	     * Set Revolution Slider to work as theme integration...means no update notices!
	     * @return void
	     *
	     */
		public function set_revslider_as_theme() {
		 
			set_revslider_as_theme();
		}

	    /**
	     * Get an array with the sliders for direct use in Redux Options
	     * @return array
	     *
	     */
	   	public static function get_sliders_array() {

	   		$sliders_array = array();

	   		if ( is_admin() && method_exists( 'RevSlider', 'getArrSliders' ) ) {

				$slider = new RevSlider();
				$sliders = $slider->getArrSliders( array('title' => 'ASC'));

				if ( !empty( $sliders ) ) {

		          foreach ( $sliders as $slider ) {

					$alias              = $slider->getAlias();
					$title              = $slider->getTitle();
					$sliders_array[$alias] = $title;
		          }

		          return $sliders_array;

		        } else {

	        		return $sliders_array[''] = esc_html__('No slider found...you should create some first!', 'plethora-framework') ;
		        }

	        } else {

	        	return $sliders_array[''] = is_admin() ? esc_html__('This is not a desired output, please contact Plethora Themes support', 'plethora-framework') : esc_html__('Well..this method was not designed for frontend use!', 'plethora-framework') ;

	        }
		}

	    /**
	     * Echoes the slider markup
	     * @return array
	     *
	     */
		public static function get_slider_output( $alias ) {

			putRevSlider( $alias );
		}
	}
}