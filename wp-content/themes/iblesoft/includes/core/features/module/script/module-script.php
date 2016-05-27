<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Scripts manager

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Script') ) {

    /**
     * Manages all themes' scripts and styles functionality
     * @since 2.0
     */
	class Plethora_Module_Script {

		public static $feature_title        = "Scripts Manager";							// Feature display title  (string)
		public static $feature_description  = "Global & custom scripts & styles manager";	// Feature display description (string)
		public static $theme_option_control = false;										// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default	= false;										// Default activation option status ( boolean )
		public static $theme_option_requires= array();										// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct	= true;											// Dynamic class construction ? ( boolean )
		public static $dynamic_method		= false;										// Additional method invocation ( string/boolean | method name or false )

		function __construct(){

      		// GLOBAL SCRIPTS
      		add_action( 'wp_enqueue_scripts', array( $this, 'assets_global_libs_customized' ), 1);  	// Customized library assets registration ( register early )
      		add_action( 'wp_enqueue_scripts', array( $this, 'assets_global_libs_thirdparty' ), 1);  	// Third party library assets registration ( register early )
      		// SHORTCODE & WIDGETS SCRIPTS
			add_action( 'wp_enqueue_scripts', array( $this, 'assets_shortcodes' ), 20);  	// On demand shortcode assets ( always enqueue features on 20 )
			add_action( 'wp_enqueue_scripts', array( $this, 'assets_widgets' ), 20);     	// On demand widget assets ( always enqueue features on 20 )
      		// JS INITS
			add_action( 'wp_head', array( $this, 'assets_inits' ), 999 );					// Init scripts ( enqueued to header with Plethora_Theme::enqueue_init_script() )
			add_action( 'wp_footer', array( $this, 'assets_inits' ), 999 );              	// Init scripts ( enqueued to footer with Plethora_Theme::enqueue_init_script() )
			// CUSTOM CSS/JS/ANALYTICS
			add_filter( 'plethora_themeoptions_advanced', array( $this, 'theme_options_custom_scripts_tab'), 20);	// Set Custom JS theme options tab
			add_action( 'wp_head', array( $this, 'output_customcss'), 999999); 									// Custom CSS field export     
			add_action( 'wp_head', array( $this, 'output_analyticsjs'), 999999);						// Analytics JS output / before <head> close placement
			add_action( 'wp_footer', array( $this, 'output_customjs'));										// JS output
			add_action( 'wp_footer', array( $this, 'output_analyticsjs'));						// Analytics JS output / before <body> close placement
			// CUSTOM CSS

			// PLENOTE: Defer script tryout...seems to work fine
			// add_filter('script_loader_tag', array( $this, 'defer_scripts'), 10, 2);
		}

		public function defer_scripts($tag, $handle) {

				
			$prefix_len = strlen(ASSETS_PREFIX);
		    if ( substr( $handle, 0, $prefix_len) === ASSETS_PREFIX  || $handle === 'jquery') { 

		    	return str_replace( ' src', ' defer="defer" src', $tag );
		    }

		    return $tag;
		}

	   /**
	    * Register all global JS/CSS libraries. Each item using those, will enqueue just using the slug ( i.e. in shortcodes )
	    */
	    public function assets_global_libs_customized() {

		      $dir_js       = PLE_CORE_ASSETS_URI . '/js/';
		      $libdir_js    = PLE_CORE_ASSETS_URI . '/js/libs/';
		      $libdir_css   = PLE_CORE_ASSETS_URI . '/css/libs/';
		      $min_suffix   = Plethora_Theme::is_developermode() ? '' : '.min';

		    # JS LIBRARIES  
		      // One pager ( Initial use: HealthFlex )
		      wp_register_script( ASSETS_PREFIX .'-one-pager', $dir_js . '/onepager.js' ); 
		      // Modernizr ( Initial use: HealthFlex )
		      wp_register_script( ASSETS_PREFIX . '-modernizr', $libdir_js .'modernizr/modernizr.custom.48287.js', array('jquery'), '', FALSE ); 
		      // Twenty-twenty ( Initial use: HealthFlex )
		      wp_register_script( ASSETS_PREFIX .'-jquery-event-move', $libdir_js .'twentytwenty/js/jquery.event.move'.$min_suffix.'.js', array( 'jquery' ), false, true );
		      wp_register_script( ASSETS_PREFIX .'-twentytwenty', $libdir_js .'twentytwenty/js/jquery.twentytwenty'.$min_suffix.'.js', array( 'jquery' ), false, true );
		      wp_register_style( ASSETS_PREFIX .'-twentytwenty', $libdir_js .'twentytwenty/css/twentytwenty.css', array(), false, 'all' );
		      // To Top ( Initial use: HealthFlex )
		      wp_register_script( ASSETS_PREFIX .'-totop', $libdir_js .'totop/jquery.ui.totop.js', array( 'jquery' ), false, true  );
		      // Google Maps ( Initial use: HealthFlex )
		      wp_register_script( ASSETS_PREFIX .'-gmap-init', $libdir_js .'googlemaps/googlemaps' . $min_suffix . '.js', array( 'gmap', ASSETS_PREFIX . '-init'), NULL, true);
		      // SVG Modal Loader ( linkify ) ( Initial use: HealthFlex )
		      wp_register_script( ASSETS_PREFIX . '-svgloader-init', $libdir_js . 'svgloader/loader'.$min_suffix.'.js',   array( 'svgloader-snap', 'svgloader'),  '', TRUE ); 
		      wp_register_script( ASSETS_PREFIX . '-imagelightbox', $libdir_js . 'imagelightbox/imagelightbox'.$min_suffix.'.js',   array(),  '', TRUE ); 
		      // MailChimp Newsletter ( Initial use: HealthFlex )
		      wp_register_script( ASSETS_PREFIX .'-newsletter_form', $libdir_js . 'newsletter/newsletter'.$min_suffix.'.js', $deps = array('jquery', ASSETS_PREFIX . '-init' ), '1.0', true ); 
		      wp_register_script( ASSETS_PREFIX .'-newsletter_form_svg', $libdir_js . 'newsletter/newsletter-svg.js', $deps = array('jquery', ASSETS_PREFIX . '-init', ASSETS_PREFIX . '-newsletter_form'), '1.0', true ); 
		      // Animated headlines ( Initial use: Avoir, based on https://codyhouse.co/gem/css-animated-headlines/ ) 
		      wp_register_script( ASSETS_PREFIX .'-animated-headline', $libdir_js . 'animated-headline/animated-headline'.$min_suffix.'.js', $deps = array('jquery' ), '1.0', true ); 

		    # CSS LIBRARIES
		      // Image Lightbox
		      wp_register_style( ASSETS_PREFIX .'-imagelightbox', $libdir_js .'imagelightbox/imagelightbox'. $min_suffix .'.css' );                          
	    }

		public function assets_global_libs_thirdparty() {

		      $dir_js       = PLE_CORE_ASSETS_URI . '/js/';
		      $libdir_js    = PLE_CORE_ASSETS_URI . '/js/libs/';
		      $libdir_css   = PLE_CORE_ASSETS_URI . '/css/libs/';
		      $min_suffix   = Plethora_Theme::is_developermode() ? '' : '.min';

		      // Easing ( Initial use: HealthFlex )
		      wp_register_script( 'easing', $libdir_js .'easing/easing'. $min_suffix .'.js',   array(),  '', TRUE ); 
		      // Wow ( Initial use: HealthFlex )
		      wp_register_script( 'wow-animation-lib', $libdir_js .'wow/wow'. $min_suffix .'.js',   array(),  '', TRUE ); 
		      // Parallax ( Initial use: HealthFlex )
		      wp_register_script( 'parallax', $libdir_js .'parallax/parallax'. $min_suffix .'.js', array('jquery'),  '', TRUE ); 
		      // Conformity ( Initial use: HealthFlex )
		      wp_register_script( 'conformity', $libdir_js .'conformity/dist/conformity'. $min_suffix .'.js',   array(),  '', TRUE ); 
		      // Isotope ( Initial use: HealthFlex / current version: 2.2.2, updated 20/04/2016 )
		      wp_register_script( 'isotope', $libdir_js .'isotope/jquery.isotope'.$min_suffix.'.js', array( 'jquery' ), '2.2.2', true  );
		      // wp_register_style( 'isotope', $libdir_js .'isotope/css/style.css', array(), false, 'all' );
		      // OwlCarousel 2 ( Initial use: HealthFlex )
		      wp_register_style( 'owlcarousel2', $libdir_js .'owl.carousel.2.0.0-beta.2.4/css/owl.carousel.css' );                     // STYLE - Owl Carousel 2 main stylesheet
		      wp_register_style( 'owlcarousel2-theme', $libdir_js .'owl.carousel.2.0.0-beta.2.4/css/owl.theme.default.css' );          // STYLE - Owl Carousel 2 theme stylesheet
		      wp_register_script( 'owlcarousel2', $libdir_js .'owl.carousel.2.0.0-beta.2.4/owl.carousel.min.js', array(),  '2.4', TRUE  ); // SCRIPT - Owl Carousel 2
		      // SVG Modal Loader ( linkify ) ( Initial use: HealthFlex )
		      wp_register_script( 'svgloader-snap', $libdir_js . 'svgloader/snap.svg-min.js',   array(),  '', TRUE ); 
		      wp_register_script( 'svgloader', $libdir_js . 'svgloader/svgLoader'.$min_suffix.'.js',   array(),  '', TRUE ); 
		      // Google Maps ( Initial use: HealthFlex )
		      wp_register_script( 'gmap', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAhCFO56k_xL212g8j2LK88wK0I_CRwzDE', array(), NULL, true);                    // SCRIPT - Google Maps 
		      // Waypoint + Counter Up ( Initial use: Avoir )
		      wp_register_script( 'waypoint', $libdir_js .'waypoint/waypoint'.$min_suffix.'.js', array( 'jquery' ), '4.0', true  );
		      wp_register_script( 'counter-up', $libdir_js .'counter-up/jquery.counterup'.$min_suffix.'.js', array( 'jquery', 'waypoint' ), '1.0', true  );
			  // TweenMax Animation Lib by GSAP ( Initial use: Avoir / current version: 1.18.3, updated 20/04/2016 / https://greensock.com/docs/#/HTML5/GSAP/TweenMax/ )		    
		      wp_register_script( 'tweenmax', $libdir_js .'gsap/TweenMax'.$min_suffix.'.js', array( 'jquery' ), '1.18.3', false  );
		    # CSS LIBRARIES
		      // Animate
		      wp_register_style( 'animate', $libdir_css .'animate/animate'. $min_suffix .'.css' );                          // Animation library
		}
	  /**
	   * Dynamic registers & enqueues for shortcode...working only when a shortcode is present!
	   */
	   public function assets_shortcodes() {

	      if ( is_singular() ) { 
	        // Get page content
	        global $post;
	        $content = $post->post_content;

	        // Get a list with all shortcode asset triggers ( shortcode slugs )
	        $shortcode_features = Plethora_Theme::get_features( array('controller' => 'shortcode' ) );
            foreach ( $shortcode_features as $key => $feature) {
             // Prepare shortcode slug
              $shortcode_slug = SHORTCODES_PREFIX . $feature['wp_slug'];
              // Enqueue 'em!
              if ( !empty($shortcode_slug) && has_shortcode( $content,  $shortcode_slug )) {
                // Enqueue scripts
                $assets  = $feature['assets'];

                if ( !empty($assets) ) {
                  foreach ( $assets as $asset ) {

                  	$asset_type = key($asset);
                  	$handlers = $asset[$asset_type];
                  	$handlers = is_array( $handlers ) ? $handlers : array( $handlers ); // predict multi arrays

                  	foreach ( $handlers as $handler ) {

						if ( !empty( $handler ) ) { 
			                if ( $asset_type === 'script' && wp_script_is( $handler, 'registered' ) ) {

			                  wp_enqueue_script( $handler );

			                } elseif ( $asset_type === 'script' && wp_script_is( ASSETS_PREFIX .'-'. $handler, 'registered' ) ) {

			                  wp_enqueue_script( ASSETS_PREFIX .'-'. $handler );

			                } elseif ( $asset_type === 'style' && wp_style_is( $handler, 'registered' ) ) {

			                  wp_enqueue_style( $handler );

			                } elseif ( $asset_type === 'style' && wp_style_is( ASSETS_PREFIX .'-'. $handler, 'registered' ) ) {

			                  wp_enqueue_style( ASSETS_PREFIX .'-'. $handler );
			                }
		                }
                    }
                  }
                }
              }
            }
	      }      
	   }

	  /**
	   * Dynamic registers & enqueues for widgets...working only when a widget is present!
	   */
	   public function assets_widgets() {

		// Get a list with all widget asset triggers ( widget slugs )
		$widget_features = Plethora_Theme::get_features( array('controller' => 'widget' ) );

        foreach ( $widget_features as $key => $feature) {
         // Prepare shortcode slug
          $widget_slug = SHORTCODES_PREFIX . $feature['wp_slug'];
          // Enqueue 'em!
          if ( !empty($widget_slug) && ! is_active_widget( false, false, WIDGETS_PREFIX.$widget_slug , false ) ) {
            // Enqueue scripts
            $assets  = $feature['assets'];

            if ( !empty($assets) ) {
              foreach ( $assets as $asset ) {

              	$asset_type = key($asset);
              	$handlers = $asset[$asset_type];
              	$handlers = is_array( $handlers ) ? $handlers : array( $handlers ); // predict multi arrays

				foreach ( $handlers as $handler ) {

					if ( ! empty( $handler ) ) { 
		                if ( $asset_type === 'script' && wp_script_is( $handler, 'registered' ) ) {

		                  wp_enqueue_script( $handler );

		                } elseif ( $asset_type === 'script' && wp_script_is( ASSETS_PREFIX .'-'. $handler, 'registered' ) ) {

		                  wp_enqueue_script( ASSETS_PREFIX .'-'. $handler );

		                } elseif ( $asset_type === 'style' && wp_style_is( $handler, 'registered' ) ) {

		                  wp_enqueue_style( $handler );

		                } elseif ( $asset_type === 'style' && wp_style_is( ASSETS_PREFIX .'-'. $handler, 'registered' ) ) {

		                  wp_enqueue_style( ASSETS_PREFIX .'-'. $handler );
		                }
	                }
            	}
              }
            }
          }
        }
	   }

	  /**
	   * Echoes all init scripts given with Plethora_Theme::enqueue_init_script method
	   * Notice: all Plethora_Theme::enqueue_init_script calls aim to enqueue on header should be set
	   *         before wp_head action occurs
	   */
	   public function assets_inits() {

	      global $plethora_init_scripts;
	      if ( !empty( $plethora_init_scripts ) ) {

	        foreach ( $plethora_init_scripts as $handle => $inits ) {

	          foreach ( $inits as $key => $args ) {

	            $init_script = '';
	            
	            if ( current_filter() === 'wp_head' && $args['position'] === 'header' ) {

	              $init_script = $args['callback_type'] === 'function' ? call_user_func( $args['callback'] ) : $args['callback'];

	            } elseif ( current_filter() === 'wp_footer' && $args['position'] === 'footer' ) { 

	              $init_script = $args['callback_type'] === 'function' ? call_user_func( $args['callback'] ) : $args['callback'];
	            }
	            // Echo init script only if handle is enqueued in this page
	            if ( !empty( $init_script ) && wp_script_is( $handle ) ) {

	              echo Plethora_Theme::is_developermode() ? "\n". '<!-- START  /// INIT SCRIPT FOR HANDLE: '. $handle .' -->'."\n" : '';
	              echo $init_script;
	              echo Plethora_Theme::is_developermode() ? "\n". '<!-- FINISH /// INIT SCRIPT FOR HANDLE: '. $handle .' -->'."\n" : '';
	            
	            } elseif ( !empty( $init_script ) && wp_script_is( ASSETS_PREFIX .'-'. $handle ) ) {

	              echo Plethora_Theme::is_developermode() ? "\n". '<!-- START  /// INIT SCRIPT FOR HANDLE: '. ASSETS_PREFIX .'-'. $handle .' -->'."\n" : '';
	              echo $init_script;
	              echo Plethora_Theme::is_developermode() ? "\n". '<!-- FINISH /// INIT SCRIPT FOR HANDLE: '. ASSETS_PREFIX .'-'. $handle .' -->'."\n" : '';
	            }
	          }
	        }
	      }
	   }

// START: CUSTOM SCRIPT/STYLE OUTPUT METHODS

	    /**
	     * Adds custom JS tab to theme options
	     */
	    static function theme_options_custom_scripts_tab( $sections ) { 

			$adv_settings = array();

		    $adv_settings[] = array(
					'id'    =>'header-customcss-start',
					'type'  => 'section',
					'indent' => true,
					'title' =>  esc_html('Custom Style Options (custom CSS)', 'plethora-framework')
			);
		    $adv_settings[] = array(
						'id'          =>THEMEOPTION_PREFIX .'customcss',
						'type'        => 'textarea',
						'title'       => esc_html('Custom CSS', 'plethora-framework'), 
						'subtitle'    => esc_html('Paste your CSS code here.', 'plethora-framework'),
						'description' => '<span style="color:red;"><strong>'. esc_html('Do not use &lt;style&gt; tags.', 'plethora-framework') .'</strong></span>',
						'default'     => '',
						);

		    $adv_settings[] = array(
					'id'    =>'header-customcss-end',
					'type'  => 'section',
					'indent' => false,
			);
		    $adv_settings[] = array(
					'id'    =>'header-customjs-start',
					'type'  => 'section',
					'indent' => true,
					'title' =>  esc_html('Custom Javascript Options', 'plethora-framework')
			);
		    $adv_settings[] = array(
					'id'          =>THEMEOPTION_PREFIX .'customjs',
					'type'        => 'textarea',
					'title'       => esc_html('Custom JS (added on footer)', 'plethora-framework'),
					'subtitle'    => esc_html('Paste your JS code here.', 'plethora-framework'),
					'description' => '<span style="color:red;"><strong>'. esc_html('Do not use &lt;script&gt; tags.', 'plethora-framework') .'</strong>.</span>',
					'default'     => '',
			);

		    $adv_settings[] = array(
					'id'    =>'header-customjs-end',
					'type'  => 'section',
					'indent' => false,
			);

		    $adv_settings[] = array(
				'id'    =>'header-googleanalytics-start',
				'type'  => 'section',
				'indent' => true,
				'title' =>  esc_html('Google Analytics Options</center>', 'plethora-framework')
			);

		    $adv_settings[] = array(
					'id'       =>THEMEOPTION_PREFIX .'analyticsscript',
					'type'     => 'textarea',
					'title'    => esc_html('Analytics tracking code', 'plethora-framework'),
					'subtitle' => esc_html('Paste your Google Analytics or other code here.', 'plethora-framework'),
					'description' => '<span style="color:red;"><strong>'. esc_html('Do not use &lt;script&gt; tags.', 'plethora-framework') .'</strong>.</span>',
					'default'     => '',
			);
		    $adv_settings[] = array(
					'id'          =>THEMEOPTION_PREFIX .'analyticsposition',
					'type'        => 'button_set',
					'title'       => esc_html('Analytics tracking code placement', 'plethora-framework'),
					'options'     => array('header' => esc_html('Head', 'plethora-framework'),'footer' => esc_html('Footer', 'plethora-framework')),//Must provide key => value pairs for radio options
					'default'     => 'footer'
			);

		    $adv_settings[] = array(
					'id'    =>'header-googleanalytics-end',
					'type'  => 'section',
					'indent' => false,
			);

			$sections[] = array(
				'subsection' => true,
				'title'      => esc_html('Scripts & Styles', 'plethora-framework'),
				'heading'      => esc_html('SCRIPTS & STYLES', 'plethora-framework'),
				'fields'     => $adv_settings
				);

			return $sections;
	    }

	    /**
	     * Adds custom JS script to footer
	     *
	     * @param
	     * @return string
	     *
	     */
	    public static function output_customjs() {

			$custom_js = Plethora_Theme::option(THEMEOPTION_PREFIX .'customjs');
			if ( !empty( $custom_js ) ) { ?>
				<script type='text/javascript'>
					<?php echo trim($custom_js); ?>
				</script>
				<?php 
			} 
	    } 

	    /**
	     * Returns analytics script for header section
	     *
	     */
	    public static function output_analyticsjs() {

			$analytics_code_placement = Plethora_Theme::option(THEMEOPTION_PREFIX .'analyticsposition');

			$analytics_code = '';

			if ( current_filter() === 'wp_head' && $analytics_code_placement ===  'header' ) {

				$analytics_code = Plethora_Theme::option(THEMEOPTION_PREFIX .'analyticsscript');

			} elseif  ( current_filter() === 'wp_footer' && $analytics_code_placement ===  'footer' ) {

				$analytics_code = Plethora_Theme::option(THEMEOPTION_PREFIX .'analyticsscript');
			}
	        
			if ( !empty( $analytics_code ) ) { ?>
				<script type='text/javascript'>
					<?php echo trim($analytics_code); ?>
				</script>
				<?php 
			} 	        
	    } 

	    /**
	     * Adds custom CSS style field contents to head
	     *
	     */
	    public static function output_customcss() {

			$custom_css = Plethora_Theme::option(THEMEOPTION_PREFIX .'customcss');
			if ( !empty( $custom_css ) ) { ?>
			<!-- USER DEFINED IN-LINE CSS -->
			<style>
				<?php echo apply_filters( 'plethora_inline_css', trim($custom_css) ); ?>
			</style><?php
	    	}    
	    }
	    

// FINISH: CUSTOM SCRIPT/STYLE OUTPUT METHODS


/// JS INIT PATTERNS AND OTHER AUXILIARY SCRIPT PATTERNS RETURNED WITH Plethora_Theme::get_init_script() wrapper method


	}
}