<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

File Description: Media panel module ( Notice: should add)

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Mediapanel') ) {

	/**
	 */
	class Plethora_Module_Mediapanel {

		public static $feature_title         = "Media Panel Module";							// FEATURE DISPLAY TITLE
		public static $feature_description   = "Integration module for Plethora media panel";	// FEATURE DISPLAY DESCRIPTION
		public static $theme_option_control  = true;											// WILL THIS FEATURE BE CONTROLLED IN THEME OPTIONS PANEL 
		public static $theme_option_default  = true;											// DEFAULT ACTIVATION OPTION STATUS
		public static $theme_option_requires = array();											// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;											// DYNAMIC CLASS CONSTRUCTION ? 
		public static $dynamic_method        = false;											// Additional method invocation ( string/boolean | method name or false )
		
		#Strictly class related variables
		public $media_type;
		public $mediapanel_status = false;
		public $skincolor_support = true;
		public $image_support     = true;
		public $slider_support    = true;
		public $gmap_support      = true;
		public $revslider_support = false;
		public $video_support     = false;

		function __construct(){

			// Set metabox tab for all single and archive post types
			add_filter( 'plethora_metabox_singlepage', array( $this, 'get_metabox_single'), 20);
			add_filter( 'plethora_metabox_singlepost', array( $this, 'get_metabox_single'), 20);
			add_filter( 'plethora_metabox_singleproject', array( $this, 'get_metabox_single'), 20);
			add_filter( 'plethora_metabox_singleproduct', array( $this, 'get_metabox_single'), 20);
			add_filter( 'plethora_metabox_singleterminology', array( $this, 'get_metabox_single'), 20);
			// Set metabox tab for archives
			add_filter( 'plethora_metabox_archivepost', array( $this, 'get_metabox_single'), 20);
			add_filter( 'plethora_metabox_archiveproduct', array( $this, 'get_metabox_single'), 20);

			// Set some options on theme options > general > misc tab
			// add_filter( 'plethora_themeoptions_mediapanel', array( $this, 'get_themeoptions'), 20);
			add_filter( 'plethora_themeoptions_general_misc_fields', array( $this, 'themeoptions_general_misc_fields'), 20);


			// SCRIPT/STYLE REGISTRATIONS
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20);			// Enqueue scripts/styles // All features should enqueue on 20 priority
			// Hook template actions on init ( should be hooked on 'wp' for wp conditionals to take effect )
			add_action( 'wp', array( $this, 'template_hooks'), 20 );
		}

		public function template_hooks() {

		      $mediapanel = !is_404() ? Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-status', 0, 0, false) : Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-404-status', 1, 0, false);
		      if ( $mediapanel ) {                             // Media Panel Module template
				// Add this on 'plethora_header_after' / priority:5
		        add_action( 'plethora_header_after', array( 'Plethora_Module_Mediapanel', 'mediapanel'), 5);
		        add_action( 'plethora_mediapanel', array( 'Plethora_Module_Mediapanel', 'mediapanel'), 5);
		      }
		}

	    /**
	     * The main method...prepares all variables and loads the correct template part. 
	     * It's not triggered automaticaly, is called Plethora_Template class
	     * @return string
	     */
	    public function mediapanel() {

			// Get the media type to be displayed
			$media_type = is_404() ? '404' : Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel', 'skincolor');
			$this->$media_type = $media_type;
			// Prepare values that will be sent to template
			$module_atts             = array();
			$module_atts['title']    = $this->title();				// Title ( notice: not used in sliders )
			$module_atts['subtitle'] = $this->subtitle();			// Subtitle ( notice: not used in sliders )
			$module_atts['media']    = $this->media( $media_type );	// Media
			$module_atts['style']    = $this->style( $media_type );	// Style

			// Enqueue related scrips/styles according to settings
			add_action( 'wp_enqueue_scripts', array( 'Plethora_Module_Mediapanel', 'enqueue_scripts' ));

			// Set template to load according to the media type
			if ( $media_type === 'featuredimage' || $media_type === 'otherimage' || $media_type === '404') { 

				$template = 'image';
			} else { 

				$template = $media_type;
			}

	        // Transfer prepared values using the 'set_query_var' ( this will make them available via 'get_query_var' to the template part file )
	        set_query_var( 'module_atts', $module_atts );
	        // Get the template part ( according to media type )
			Plethora_WP::get_template_part('templates/modules/mediapanel', strtolower( $template ));
	      
	    }    

	    /**
	     * Enqueues scripts/styles according to settings
	     *
	     */
		public function enqueue_scripts() {

			// Get the media type to be displayed
			$media_type = is_404() ? '404' : Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel', '');
		
			if ( $media_type === 'featuredimage' || $media_type === 'otherimage' ) { 

				// OwlCarousel 2
				wp_enqueue_script( 'parallax' );

			} elseif ( $media_type === 'slider' || $media_type === 'video') { 

				// OwlCarousel 2
				wp_enqueue_style( 'owlcarousel2' );
				wp_enqueue_style( 'owlcarousel2-theme' );
				wp_enqueue_script( 'owlcarousel2' );
				// Add init script for OwlCarousel 2
				Plethora_Theme::enqueue_init_script( array(
										'handle' => 'owlcarousel2',
										'script' => $this->init_script_owlslider()
										));

			} elseif ( $media_type === 'googlemap') {

				// Google Maps
				wp_enqueue_script( ASSETS_PREFIX .'-gmap-init' ); // will load gmap too
				
				// PLEFIXME: temporary themeconfig workaround
				$map_vars['maps'][] = $this->gmap_options();
				Plethora_Theme::set_themeconfig('GOOGLE_MAPS', $map_vars); // Google Maps config for theme.js
			}
		}

       /** 
       * Returns alternatuve button selection for header media option, when Rev Slider plugin is installed
       *
       * @return array
       * @since 1.0
       *
       */
		public function media_types() {

			$media_types = array();
			if ( $this->skincolor_support ) {

				$media_types['skincolor'] = esc_html__('Color', 'plethora-framework');
			}

			if ( $this->image_support ) {

				$media_types['featuredimage'] = esc_html__('Featured Image', 'plethora-framework');
				$media_types['otherimage'] = esc_html__('Other Image', 'plethora-framework');
			}

			if ( $this->slider_support ) {

				$media_types['slider'] = esc_html__('Slider', 'plethora-framework');
			}

			if ( $this->gmap_support ) {

				$media_types['googlemap'] = esc_html__('Map', 'plethora-framework');
			}

			if ( $this->revslider_support && class_exists( 'RevSliderAdmin' ) ) {

				$media_types['revslider'] = esc_html__('Revolution Slider', 'plethora-framework');
			}

			if ( $this->video_support ) {

				$media_types['video'] = esc_html__('Video', 'plethora-framework');
			}
			

			$media_types = apply_filters( 'plethora_mediapanel_mediatypes', $media_types );
			return $media_types;
 		}

       /** 
       * Returns media panel title
       *
       * @return string
       * @since 1.0
       *
       */
	   public function title() { 

	      $title_behavior = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-titles-behavior', 'posttitle');

		  $title = '';
	      
	      if ( $title_behavior === 'posttitle' ) { 

		      $title = Plethora_Theme::get_title( array( 'tag' => '', 'force_display' => true ) );

	      } elseif ( $title_behavior === 'customtitle' ) {

		      $title = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-title-custom', '');
	      
	      } 
	      $title = apply_filters( 'plethora_mediapanel_title', $title );

	      return $title;
	    }

       /** 
       * Returns media panel subtitle
       *
       * @return string
       * @since 1.0
       *
       */
	   public function subtitle() { 

	      $title_behavior = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-titles-behavior', 'posttitle');
	      
	      if ( $title_behavior === 'posttitle' ) { 

		      $subtitle = Plethora_Theme::get_subtitle( array( 'tag' => '', 'force_display' => true ) );

	      } elseif ( $title_behavior === 'customtitle' ) {

		      $subtitle = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-subtitle-custom', '');
	      
	      } else {

		      $subtitle = '';
	      }

	      $subtitle = apply_filters( 'plethora_mediapanel_subtitle', $subtitle );
	      return $subtitle;
	    }

	     /**
	     * Returns media content according to the type
	     *
	     * @param $type ( 'skincolor', featuredimage', 'otherimage', '404', 'slider' )
	     * @return string
	     *
	     */
	    public function media( $type = 'skincolor' ) {

	      $media = '';

	      if ( $type === 'featuredimage' ) { 

	      	$postid = Plethora_Theme::get_this_page();
	        if ( has_post_thumbnail( $postid ) ) { 
				
				$image_size = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-image-size', 'full');
				$attachment = wp_get_attachment_image_src(get_post_thumbnail_id( $postid ), $image_size );
	          	$media['url'] = isset( $attachment[0] ) && !empty( $attachment[0] ) ? $attachment[0] : '' ;
	          	$media['parallax'] = Plethora_Theme::option( METAOPTION_PREFIX . 'mediapanel-parallax', 0);
	        }

	      } elseif ( $type === 'otherimage' ) { 

				$otherimage = Plethora_Theme::option( METAOPTION_PREFIX . 'mediapanel-otherimage');			
	          	$media['url'] = isset( $otherimage['url'] ) && !empty( $otherimage['url'] ) ? $otherimage['url'] : '' ;
	      
	      } elseif ( $type === 'video' ) { 

				$video = Plethora_Theme::option( METAOPTION_PREFIX . 'mediapanel-video');
	      		$media = array();
	          	$media['link'] = isset( $video ) && !empty( $video ) ? $video : '' ;
	          	$media['autoplay'] = Plethora_Theme::option( METAOPTION_PREFIX . 'mediapanel-video-autoplay');

	      } elseif ( $type === '404' ) { 

	      		$four04_image = Plethora_Theme::option( THEMEOPTION_PREFIX .'mediapanel-404-image');
	          	$media['url'] = isset( $four04_image['url'] ) && !empty( $four04_image['url'] ) ? $four04_image['url'] : '' ;

	      } elseif ( $type === 'slider') { 

				$sliderid = Plethora_Theme::option( METAOPTION_PREFIX . 'mediapanel-slider', 0);
				$slider   = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-slides', array(), $sliderid);
				$media = array();
			    if ( isset( $slider['slide_image'] ) ) { 
				    foreach ($slider['slide_image'] as $key => $slide ) {

				     if ( !empty($slide) ) { 
						$media[] = array(
							'image'                    => $slider['slide_image'][$key],
							'colorset'                 => $slider['slide_colorset'][$key],
							'transparentfilm'          => $slider['slide_transparentfilm'][$key],
							'caption_title'            => $slider['slide_caption_title'][$key],
							'caption_subtitle'         => $slider['slide_caption_subtitle'][$key],
							'caption_secondarytitle'   => $slider['slide_caption_secondarytitle'][$key],
							'caption_secondarytext'    => $slider['slide_caption_secondarytext'][$key],
							'caption_colorset'         => $slider['slide_caption_colorset'][$key],
							'caption_transparentfilm'  => $slider['slide_caption_transparentfilm'][$key],
							'caption_size'             => $slider['slide_caption_size'][$key],
							'caption_align'            => $slider['slide_caption_align'][$key],
							'caption_textalign'        => $slider['slide_caption_textalign'][$key],
							'caption_neutralizetext'   => $slider['slide_caption_neutralizetext'][$key],
							'caption_animation'        => $slider['slide_caption_animation'][$key],
							'caption_headingstyle'     => $slider['slide_caption_headingstyle'][$key],
							'caption_buttonlinktext'   => isset( $slider['slide_caption_buttonlinktext'] ) ? $slider['slide_caption_buttonlinktext'][$key] : "",
							'caption_buttonlinkurl'    => isset( $slider['slide_caption_buttonlinkurl'] ) ? $slider['slide_caption_buttonlinkurl'][$key] : "",
							'caption_buttonstyle'      => $slider['slide_caption_buttonstyle'][$key],
							'caption_buttonsize'       => $slider['slide_caption_buttonsize'][$key],
							'caption_buttonlinktarget' => $slider['slide_caption_buttonlinktarget'][$key],
						);  
				      } 
				    }
			    }

	      } elseif ( $type === 'revslider') {

	      	$media['revslider_alias'] = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-revslider', '');

	      } 

	      $media = apply_filters( 'plethora_mediapanel_media', $media );
	      return $media;
	    }

	    public function style( $media = 'skincolor' ) {

			// Get row option values
			$style['title_colorset']          = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-'. $media .'-title-colorset', 'skincolored_section' );
			$style['title_backgroundtype']    = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-'. $media .'-title-backgroundtype', 0 );
			$style['subtitle_colorset']       = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-'. $media .'-subtitle-colorset', 'dark_section' );
			$style['subtitle_backgroundtype'] = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-'. $media .'-subtitle-backgroundtype', 1 );
			$style['text_align']              = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-'. $media .'-text-align', 'text-left' );
			$style['full_height']             = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-'. $media .'-fullheight', 0 );
			$style['parallax']                = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-'. $media .'-parallax', '' );
			$style['imgvalign']               = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-'. $media .'-imgvalign', '' );

			// Configure values accordingly
			$diagonal	= Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-'. $media .'-headings-layout', 'diagonal' );
			if ( $diagonal == 'diagonal' ) {  
				
				$style['headings_layout']          = 'diagonal_headings_layout';
				$style['title_colorset']          = '';
				$style['title_backgroundtype']    = '';
				$style['subtitle_colorset']       = '';
				$style['subtitle_backgroundtype'] = '';
				$style['text_align']              = '';
				$style['diagonal_title_class']    = 'diagonal-bgcolor-trans';
				$style['diagonal_subtitle_class'] = 'body-bg_section';
			
			} else {

				$style['headings_layout']          = 'simple_headings_layout';
				$style['diagonal_title_class']     = '';
				$style['diagonal_subtitle_class']  = '';

			}

			$style['title_backgroundtype']    = $style['title_backgroundtype'] !== '' || $style['title_backgroundtype'] !== 'foo' ? $style['title_backgroundtype'] : '';
			$style['subtitle_backgroundtype'] = $style['subtitle_backgroundtype'] !== '' || $style['subtitle_backgroundtype'] !== 'foo' ? $style['subtitle_backgroundtype'] : '';

			// Return value
			$style = apply_filters( 'plethora_mediapanel_style', $style );
			return $style;
	    }


	    /**
	     * Returns slider options array
	     *
	     * @param $sliderid
	     * @return array
	     * @since 1.0
	     *
	     */
	    public function slider_options() {

			$sliderid       = Plethora_Theme::option( METAOPTION_PREFIX . 'mediapanel-slider', 0);
			$slider_options = array();

	        if ( $sliderid > 0 ) { 

				$slider_options['autoplay']           = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-autoplay', true, $sliderid );
				$slider_options['nav']                = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-nav', true, $sliderid );
				$slider_options['dots']               = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-dots', true, $sliderid );
				$slider_options['loop']               = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-loop', false, $sliderid );
				$slider_options['mousedrag']          = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-mousedrag', true, $sliderid );
				$slider_options['touchdrag']          = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-touchdrag', true, $sliderid );
				$slider_options['autoplaytimeout']    = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-autoplaytimeout', 5000, $sliderid );
				$slider_options['autoplayspeed']      = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-autoplayspeed', 1000, $sliderid );
				$slider_options['autoplayhoverpause'] = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-autoplayhoverpause', true, $sliderid );
				$slider_options['lazyload']           = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-lazyload', true, $sliderid );
				$slider_options['urltarget']          = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-urltarget', '_self', $sliderid );
				$slider_options['rtl']                = Plethora_Theme::option( METAOPTION_PREFIX . 'slider-rtl', false, $sliderid );
				$slider_options['video']              = true;
	        }

	        return $slider_options;
	    }


	    /**
	     * Returns google maps options array
	     *
	     * @param ( not needed...taken automatically depending on the page )
	     * @return array
	     * @since 1.0
	     *
	     */
	    public function gmap_options() {
	        
			$gmap                        = array();
			$gmap['id']                  = 'map';

			// Basic options
			$gmap['lat']  = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-lat');
			$gmap['lon']  = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-lon');
			$gmap['type'] = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-type', 'ROADMAP'); // "SATELLITE", ROADMAP", "HYBRID", "TERRAIN"
			$zoom         = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-zoom', 14 );
			$gmap['zoom'] = is_numeric( $zoom ) ? intval( $zoom ) : 14;
			// $gmap['streetView']          = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-streetview', 0); 
			// $gmap['streetView_position'] = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-streetview-position', 'LEFT_CENTER'); 

			// Marker Image settings
			$gmap['marker']            = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-marker', true ); 
			$gmap['markerTitle']       = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-markertitle', 'We are right here!');
			$gmap['infoWindow']        = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-markerwindow', '');
			$markerImage               = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-marker-customimage', array( 'url' => '', 'height' => 0, 'width' => 0 ));

			$gmap['markerImageSrc']    = $markerImage['url'];
			$gmap['markerImageWidth']  = $markerImage['width'];
			$gmap['markerImageHeight'] = $markerImage['height'];
			$gmap['markerAnchorX']     = $markerImage['width']; // not sure if this is correct 
			$gmap['markerAnchorY']     = $markerImage['height']; // not sure if this is correct 
			$gmap['markerType']  	   = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-markertype', "animated" );
			
			// ADVANCED MAP STYLING
			$gmap['type_switch']           = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-type-switch', true);  
			$gmap['type_switch_style']     = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-type-switch-style', 'DROPDOWN_MENU'); // "DROPDOWN_MENU", "HORIZONTAL_BAR", "DEFAULT"
			$gmap['type_switch_position']  = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-type-switch-position', 'TOP_RIGHT');  // POSITIONS: https://developers.google.com/maps/documentation/javascript/images/control-positions.png
			$gmap['pan_control']           = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-pan-control', true); 

			if ( $gmap['pan_control'] == "0" ) $gmap['pan_control'] = false;

			$gmap['pan_control_position']  = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-pan-control-position', 'RIGHT_CENTER'); // POSITIONS: https://developers.google.com/maps/documentation/javascript/images/control-positions.png
			$gmap['zoom_control']          = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-zoom-control', true ); 
			$gmap['zoom_control_style']    = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-zoom-control-style', 'SMALL' ); // "SMALL", "LARGE", "DEFAULT"
			$gmap['zoom_control_position'] = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-zoom-control-position', 'LEFT_CENTER' ); // POSITIONS: https://developers.google.com/maps/documentation/javascript/images/control-positions.png
			$gmap['scrollWheel']           = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-scrollwheel', false ); 
			$gmap['styles']                = Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-snazzy', false ) == true ? Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-snazzy-config', null) : null ; 

			// FIXED
			$gmap['disableDefaultUI'] = false; 
			$gmap['scale_control']    = false;  
			$gmap['animatedMarker']	  = ( Plethora_Theme::option( METAOPTION_PREFIX .'mediapanel-map-markertype', true ) == "animated" )? true : false;

	        return $gmap;
	    }

// METABOX OPTIONS START

	    /**
	     * Returns metabox configuration for single page metabox tab
	     * Hooked on 'plethora_metabox_singlepage' filter
	     * @return array
	     *
	     */
		public function get_metabox_single( $sections ) { 

			$sections[] = $this->metabox_single();
			return $sections;
		}

	    public function metabox_single() {

			    $metabox_single = array(
			        'title'         => esc_html__('Media Panel', 'plethora-framework'),
			        'heading'		=> esc_html__('Media Panel', 'plethora-framework'),
			        'icon_class'    => 'icon-large',
			        'icon'          => 'el-icon-website',
			     // 'desc' 			=> ' <a href="'. admin_url('admin.php?page=cleanstart_options&tab=3') . '" target="_blank">'. esc_html__('Click here', 'plethora-framework') . '</a>'. esc_html__(' to edit default Media Panel options.', 'plethora-framework'),
			        'fields'        => array()
				);
				$metabox_single['fields'][] = array(
							'id'      => METAOPTION_PREFIX .'mediapanel-status',
							'type'    => 'switch', 
							'title'   => esc_html__('Display Media Panel', 'plethora-framework'),
							"on"      => 'Yes',
							"off"     => 'No',
				);
				$metabox_single['fields'][] = array(
							'id'       => 'media-start',
							'type'     => 'section',
							'required' => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
							'title'    => esc_html__('BACKGROUND SECTION', 'plethora-framework'),
							'indent'   => true 
				);
				$metabox_single['fields'][] = array(
							'id'      => METAOPTION_PREFIX .'mediapanel',
							'type'    => 'button_set',
							'title'   => esc_html__('Background Type', 'plethora-framework'), 
							'default' => 'skincolor',
							'options' => $this->media_types(),
				);
				$metabox_single['fields'] = array_merge($metabox_single['fields'], $this->metabox_single_colorbg_options() );
				$metabox_single['fields'] = array_merge($metabox_single['fields'], $this->metabox_single_featuredimg_options() );
				$metabox_single['fields'] = array_merge($metabox_single['fields'], $this->metabox_single_otherimg_options() );
				$metabox_single['fields'] = array_merge($metabox_single['fields'], $this->metabox_single_map_options() );
				$metabox_single['fields'] = array_merge($metabox_single['fields'], $this->metabox_single_slider_options() );
				$metabox_single['fields'] = array_merge($metabox_single['fields'], $this->metabox_single_revslider_options() );
				$metabox_single['fields'][] = array(
						'id'       => 'media-end',
						'required' => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
						'type'     => 'section',
						'indent'   => false 
				);
				$metabox_single['fields'][] = array(
						'id'       => 'headings-start',
						'type'     => 'section',
						'title'    => esc_html__('HEADINGS SECTION', 'plethora-framework'),
						'required' => array( 
										array( METAOPTION_PREFIX .'mediapanel','!=', array('slider')),
					    				array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
									  ),
				       'indent'    => true 
				);
				$metabox_single['fields'][] = array(
							'id'       => METAOPTION_PREFIX .'mediapanel-titles-behavior',
							'type'     => 'button_set', 
							'title'    => esc_html__('Title / Subtitle Behaviour', 'plethora-framework'),
							'options'  => array(
									'posttitle'   => 'Default Title / Subtitle',
									'customtitle' => 'Custom Title / Subtitle',
									'notitle'     => 'No Title / Subtitle'
								),
							'default' => 'posttitle',
							'required' => array( 
											array( METAOPTION_PREFIX .'mediapanel','!=', array('slider')),
											array( METAOPTION_PREFIX .'mediapanel','!=', array('revslider')),
										  ),
				);
				$metabox_single['fields'][] = array(
							'id'       => METAOPTION_PREFIX .'mediapanel-title-custom',
							'type'     => 'text', 
							'required' => array( 
											array( METAOPTION_PREFIX .'mediapanel','!=', array('slider')),
											array( METAOPTION_PREFIX .'mediapanel','!=', array('revslider')),
											array( METAOPTION_PREFIX .'mediapanel-titles-behavior','=', array('customtitle')),
										  ),
							'title' => esc_html__('Custom Title', 'plethora-framework'),
              				'translate' => true,
				);

				$metabox_single['fields'][] = array(
							'id'=> METAOPTION_PREFIX .'mediapanel-subtitle-custom',
							'required' => array( 
											array( METAOPTION_PREFIX .'mediapanel','!=', array('slider')),
											array( METAOPTION_PREFIX .'mediapanel','!=', array('revslider')),
											array( METAOPTION_PREFIX .'mediapanel-titles-behavior','=', array('customtitle')),
										  ),
							'type'  => 'text', 
							'title' => esc_html__('Custom Subtitle', 'plethora-framework'),
              				'translate' => true,
				);

				$metabox_single['fields'][] = array(
						'id'       => 'headings-end',
						'type'     => 'section',
						'required' => array( 
										array( METAOPTION_PREFIX .'mediapanel','!=', array('slider')),
					   					array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
									  ),
				       'indent' => false 
				);

				$metabox_single['fields'][] = array(
						'id'       => 'styling-start',
						'type'     => 'section',
						'required' => array( METAOPTION_PREFIX .'mediapanel-status','=', 1),						
						'title'    => esc_html__('STYLING OPTIONS', 'plethora-framework'),
						'subtitle' => esc_html__('Notice: styling options ( and their default values ) may be different according to the selected background media.', 'plethora-framework'),
						'indent'   => true 
				);

				// Needed to produce separate styling options for each mediapanel item
				$metabox_single['fields'] = array_merge($metabox_single['fields'], $this->metabox_single_style_options('skincolor'));
				$metabox_single['fields'] = array_merge($metabox_single['fields'], $this->metabox_single_style_options('featuredimage'));
				$metabox_single['fields'] = array_merge($metabox_single['fields'], $this->metabox_single_style_options('otherimage'));
				$metabox_single['fields'] = array_merge($metabox_single['fields'], $this->metabox_single_style_options('slider'));
				$metabox_single['fields'] = array_merge($metabox_single['fields'], $this->metabox_single_style_options('googlemap'));
				$metabox_single['fields'][] = array(
												'id'     => 'styling-end',
												'type'   => 'section',
												'indent' => false 
												);
				return $metabox_single;
		}


		public function metabox_single_colorbg_options() {

			return array();
		}

		public function metabox_single_featuredimg_options() {

			$featuredimg_options = array( 
								array(
								'id'       => METAOPTION_PREFIX .'mediapanel-image-size',
								'type'     => 'button_set', 
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('featuredimage')),
								'title'    => esc_html__('Image Size', 'plethora-framework'),
								'desc'     => esc_html__('For optimum page speed, we suggest that the original image file size should not exceed 500KBs', 'plethora-framework'),
								'options'  => array( 
									'full'  => 'Original Size', 
									'large' => 'Large Size ( optimized by WP )', 
									),
								'default'  => 'full',
								)
			);
			return $featuredimg_options;

		}

		public function metabox_single_otherimg_options() {

			$otherimg_options = array( 
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-otherimage',
								'type'     => 'media', 
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('otherimage')),
								'url'      => true,
								'title'    => esc_html__('Other Image', 'plethora-framework'),
								'desc'     => esc_html__('Upload an image other than your featured image. Note that media panel will display by default the original image size. For optimum page speed, we suggest that the original image file size should not exceed 500KB', 'plethora-framework'),
								)
			);
			return $otherimg_options;

		}

		public function metabox_single_map_options() {

			$map_options = array(
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-map-lat',
								'type'     => 'text', 
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),
								'title'    => esc_html__('Map Latitude', 'plethora-framework'),
								'desc'     => esc_html__('Example:', 'plethora-framework') .'<strong>51.50852</strong>. Use <a href="http://www.latlong.net/" target="_blank">LatLong</a> to find easily your location coords.',
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-map-lon',
								'type'     => 'text', 
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),
								'title'    => esc_html__('Map Longtitude', 'plethora-framework'),
								'desc'     => esc_html__('Example:', 'plethora-framework') .'<strong>-0.1254</strong>. Use <a href="http://www.latlong.net/" target="_blank">LatLong</a> to find easily your location coords.',
								),

							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-map-type',
								'type'     => 'button_set',
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),
								'title'    => esc_html__('Map Type', 'plethora-framework'),
								'options'  => array( 
									'ROADMAP'    => 'Roadmap', 
									'SATELLITE'  => 'Satellite', 
									'HYBRID'     => 'Hybrid', 
									'TERRAIN'    => 'Terrain',
									'STREETVIEW' => 'Streetview' 
									),
								'default'  => 'ROADMAP',
								),

							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-map-zoom',
								'type'     => 'slider', 
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),
								'title'    => esc_html__('Map Zoom', 'plethora-framework'),
								"default"  => 14,
								"min"      => 1,
								"step"     => 1,
								"max"      => 18,
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-map-marker',
								'type'     => 'switch', 
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),
								'title'    => esc_html__('Map Marker', 'plethora-framework'),
								'desc'     => esc_html__('Show a mark over the given location', 'plethora-framework'),
								"default"  => true
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-map-markertype',
								'type'     => 'select',
								'required' => array( 
									array( METAOPTION_PREFIX .'mediapanel-map-marker','=', true), 
									array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),  
									),						
								'title'    => esc_html__('Marker Type', 'plethora-framework'),
								'options'  => array(
									'animated' => 'Animated',
									'standard' => 'Standard',
									'image'    => 'Custom Image'
							    ),
							    'default'  => 'animated',
							),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-map-marker-customimage',
								'type'     => 'media', 
								'required' => array( 
									array( METAOPTION_PREFIX .'mediapanel-map-marker','=', 1), 
									array( METAOPTION_PREFIX .'mediapanel-map-markertype','=', 'image'), 
									array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),  
									),						
								'title'    => esc_html__('Map Marker Image (custom)', 'plethora-framework'),
								'desc'     => esc_html__('Use a custom image marker. Upload a PNG/GIF transparent image.', 'plethora-framework'),
								'url'      => false,
								),
							array(
								'id'       => THEMEOPTION_PREFIX .'mediapanel-map-markertitle',
								'type'     => 'text',
								'required' => array( 
									array( METAOPTION_PREFIX .'mediapanel-map-markertype','!=', array('image')),  
									array( METAOPTION_PREFIX .'mediapanel-map-markertype','!=', array('animated')),  
									array( METAOPTION_PREFIX .'mediapanel-map-marker','=', 1), 
									array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap'))
									),
								'title'    => esc_html__('Map Marker Hover Title', 'plethora-framework'),
								'default'  => esc_html__('We are right here!', 'plethora-framework'),
                  				'translate' => true,
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-map-markerwindow',
								'type'     => 'textarea',
								'required' => array( 
									array( METAOPTION_PREFIX .'mediapanel-map-markertype','!=', array('image')),  
									array( METAOPTION_PREFIX .'mediapanel-map-marker','=', 1), 
									array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap'))
									),						
								'title'    => esc_html__('Map Marker Click Window', 'plethora-framework'),
								'desc'     => esc_html__('Edit infromation window that appears on marker click ( HTML )', 'plethora-framework'),
                  				'translate' => true,
							),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-map-type-switch',
								'type'     => 'switch', 
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),
								'title'    => esc_html__('Map Type Control', 'plethora-framework'),
								'desc'     => esc_html__('Display map type selection control', 'plethora-framework'),
								"default"  => true
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-map-type-switch-position',
								'type'     => 'select',
								'required' => array( array( METAOPTION_PREFIX .'mediapanel-map-type-switch','=', 1), array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),  ),						
								'title'    => esc_html__('Map Type Control Position', 'plethora-framework'),
								'options'  => array(
									'TOP_LEFT'      => 'Top Left',
									'TOP_CENTER'    => 'Top Center',
									'TOP_RIGHT'     => 'Top Right',
									'LEFT_CENTER'   => 'Middle Left',
									'RIGHT_CENTER'  => 'Middle Right',
									'BOTTOM_LEFT'   => 'Bottom Left',
									'BOTTOM_CENTER' => 'Bottom Center',
									'BOTTOM_RIGHT'  => 'Bottom Right',
							    ),
							    'default'  => 'TOP_RIGHT',
							),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-map-type-switch-style',
								'type'     => 'select', 
								'required' => array( array( METAOPTION_PREFIX .'mediapanel-map-type-switch','=', 1), array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),  ),						
								'title'    => esc_html__('Map Type Control Style', 'plethora-framework'),
								'options'  => array(
									'DROPDOWN_MENU'  => 'Dropdown menu',
									'HORIZONTAL_BAR' => 'Horizontal bar',
							    ),
							    'default'  => 'DROPDOWN_MENU',
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-map-pan-control',
								'type'     => 'switch', 
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),
								'title'    => esc_html__('Map Pan Control', 'plethora-framework'),
								'desc'     => esc_html__('Display pan control', 'plethora-framework'),
								"default"  => true
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-map-pan-control-position',
								'type'     => 'select',
								'required' => array( array( METAOPTION_PREFIX .'mediapanel-map-pan-control','=', 1), array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),  ),						
								'title'    => esc_html__('Map Pan Control Position', 'plethora-framework'),
								'options'  => array(
									'TOP_LEFT'      => 'Top Left',
									'TOP_CENTER'    => 'Top Center',
									'TOP_RIGHT'     => 'Top Right',
									'LEFT_CENTER'   => 'Middle Left',
									'RIGHT_CENTER'  => 'Middle Right',
									'BOTTOM_LEFT'   => 'Bottom Left',
									'BOTTOM_CENTER' => 'Bottom Center',
									'BOTTOM_RIGHT'  => 'Bottom Right',
							    ),
							    'default'  => 'TOP_RIGHT',
							),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-map-zoom-control',
								'type'     => 'switch', 
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),
								'title'    => esc_html__('Map Zoom Control', 'plethora-framework'),
								'desc'     => esc_html__('Display zoom control', 'plethora-framework'),
								"default"  => true
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-map-zoom-control-position',
								'type'     => 'select',
								'required' => array( array( METAOPTION_PREFIX .'mediapanel-map-zoom-control','=', 1), array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),  ),						
								'title'    => esc_html__('Map Zoom Control Position', 'plethora-framework'),
								'options'  => array(
									'TOP_LEFT'      => 'Top Left',
									'TOP_CENTER'    => 'Top Center',
									'TOP_RIGHT'     => 'Top Right',
									'LEFT_CENTER'   => 'Middle Left',
									'RIGHT_CENTER'  => 'Middle Right',
									'BOTTOM_LEFT'   => 'Bottom Left',
									'BOTTOM_CENTER' => 'Bottom Center',
									'BOTTOM_RIGHT'  => 'Bottom Right',
							    ),
							    'default'  => 'TOP_RIGHT',
							),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-map-zoom-control-style',
								'type'     => 'select', 
								'required' => array( array( METAOPTION_PREFIX .'mediapanel-map-zoom-control','=', 1), array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),  ),						
								'title'    => esc_html__('Map Zoom Control Style', 'plethora-framework'),
								'options'  => array(
									'DROPDOWN_MENU'  => 'Dropdown menu',
									'HORIZONTAL_BAR' => 'Horizontal bar',
							    ),
							    'default'  => 'DROPDOWN_MENU',
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-map-scrollwheel',
								'type'     => 'switch', 
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),
								'title'    => esc_html__('Map Scrolling Wheel', 'plethora-framework'),
								'desc'     => esc_html__('Disable the default scrolling wheel zooming functionality', 'plethora-framework'),
								"default"  => false,
								"on"       => 'Enable',
								"off"      => 'Disable',
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-map-snazzy',
								'type'     => 'switch', 
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),
								'title'    => esc_html__('Snazzy Map Styling', 'plethora-framework'),
								'desc'     => esc_html__('Enable Snazzy map styling plugin. Check Snazzy maps:', 'plethora-framework') . ' <a href="https://snazzymaps.com/" target="_blank">https://snazzymaps.com/</a>',
								"default"  => false
								),
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-map-snazzy-config',
								'type'     => 'textarea',
								'required' => array( array( METAOPTION_PREFIX .'mediapanel-map-snazzy','=', 1), array( METAOPTION_PREFIX .'mediapanel','=', array('googlemap')),  ),						
								'title'    => esc_html__('Snazzy Map Style Array', 'plethora-framework'), 
								'desc'     => esc_html__('You can create your own Snazzy map style array here:', 'plethora-framework') . ' <a href="https://snazzymaps.com/editor" target="_blank">https://snazzymaps.com/editor</a>',
								'default'  => "[{'featureType':'water','stylers':[{'visibility':'on'},{'color':'#428BCA'}]},{'featureType':'landscape','stylers':[{'color':'#f2e5d4'}]},{'featureType':'road.highway','elementType':'geometry','stylers':[{'color':'#c5c6c6'}]},{'featureType':'road.arterial','elementType':'geometry','stylers':[{'color':'#e4d7c6'}]},{'featureType':'road.local','elementType':'geometry','stylers':[{'color':'#fbfaf7'}]},{'featureType':'poi.park','elementType':'geometry','stylers':[{'color':'#c5dac6'}]},{'featureType':'administrative','stylers':[{'visibility':'on'},{'lightness':33}]},{'featureType':'road'},{'featureType':'poi.park','elementType':'labels','stylers':[{'visibility':'on'},{'lightness':20}]},{},{'featureType':'road','stylers':[{'lightness':20}]}]",
							)
						);
			return $map_options;

		}

		public function metabox_single_slider_options() {

			$slider_options = array( 
							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-slider',
								'type'     => 'select',
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('slider')),
								'data'     => 'posts',
								'title'    => esc_html__('Select Slider', 'plethora-framework'), 
								'desc'     => esc_html__('Select a slider to be displayed. You should create one first! Slider settings will be applied here too!', 'plethora-framework'),
								'args'     => array(
									'posts_per_page'   => -1,
									'post_type'        => 'slider'),
									'suppress_filters' => true									 				
							)
			);
			return $slider_options;
		}

		public function metabox_single_revslider_options() {

			$revslider_options = array( 

							array(
								'id'       => METAOPTION_PREFIX .'mediapanel-revslider',
								'type'     => 'select',
								'required' => array( METAOPTION_PREFIX .'mediapanel','=', array('revslider')),
								'title'    => esc_html__('Select Slider', 'plethora-framework'), 
								'desc'     => esc_html__('Select a slider to be displayed. You should create one first! Slider settings will be applied here too!', 'plethora-framework'),
								'options'  => method_exists( 'Plethora_Module_Revslider_Ext', 'get_sliders_array' ) ? Plethora_Module_Revslider_Ext::get_sliders_array() : array(),
								)
			);

			return $revslider_options;
		}

		public function metabox_single_style_options( $media = 'skincolor') { 

			if ( empty($media) || $media === 'revslider' ) { return; }
			
			$style_options = array();
			
			if ( $media !== 'slider' ) { 

				$style_options[] = array(
									'id'       => METAOPTION_PREFIX .'mediapanel-'.$media.'-headings-layout',
									'type'     => 'button_set', 
									'required' => array( 
												array( METAOPTION_PREFIX .'mediapanel','=', array($media)),
												array( METAOPTION_PREFIX .'mediapanel-titles-behavior','=', array('customtitle', 'posttitle')),
										  ),
									'title'   => esc_html__('Headings Layout', 'plethora-framework'),
									"default" => 'diagonal',
									'options' => array( 'normal' => 'Simple', 'diagonal' => 'Diagonal' ),
							   		);
				$titles_required = array( METAOPTION_PREFIX .'mediapanel-'.$media.'-headings-layout', '!=', array('diagonal') );

	            $style_options[] = array(
									'id'       => METAOPTION_PREFIX .'mediapanel-'.$media.'-title-colorset',
									'required' => array( 
													array( METAOPTION_PREFIX .'mediapanel','=', array($media)),
													array( METAOPTION_PREFIX .'mediapanel-titles-behavior','=', array('customtitle', 'posttitle')),
													$titles_required,
											  ),
									'type'    => 'button_set',
									'title'   => esc_html__( 'Title Color Set', 'plethora-framework' ),
									'options' => array( 'foo_section' => 'None', 'skincolored_section' => 'Primary', 'secondary_section' => 'Secondary', 'dark_section' => 'Dark', 'light_section' => 'Light', 'black_section' => 'Black', 'white_section' => 'White' ),
									'default' => 'skincolored_section',
							   		);
	            $style_options[] = array(
									'id'       => METAOPTION_PREFIX .'mediapanel-'.$media.'-title-backgroundtype',
									'required' => array( 
													array( METAOPTION_PREFIX .'mediapanel','=', array($media)),
													array( METAOPTION_PREFIX .'mediapanel-titles-behavior','=', array('customtitle', 'posttitle')),
													$titles_required,
											  ),
									'type'    => 'button_set',
									'title'   => esc_html__( 'Title Background Type', 'plethora-framework' ),
									'options' => array( 'foo' => 'Title Color Set Background', 'transparent_film' => 'Transparent Film', 'transparent' => 'No background'),
									'default' => 'foo',
							   		);

	            $style_options[] = array(
									'id'       => METAOPTION_PREFIX .'mediapanel-'.$media.'-subtitle-colorset',
									'required' => array( 
													array( METAOPTION_PREFIX .'mediapanel','=', array($media)),
													array( METAOPTION_PREFIX .'mediapanel-titles-behavior','=', array('customtitle', 'posttitle')),
													$titles_required,
											  ),
									'type'    => 'button_set',
									'title'   => esc_html__( 'Subtitle Color Set', 'plethora-framework' ),
									'options' => array( 'foo_section' => 'None', 'skincolored_section' => 'Primary', 'secondary_section' => 'Secondary', 'dark_section' => 'Dark', 'light_section' => 'Light', 'black_section' => 'Black', 'white_section' => 'White' ),
									'default' => 'dark_section',
							   		);
	            $style_options[] = array(
									'id'       => METAOPTION_PREFIX .'mediapanel-'.$media.'-subtitle-backgroundtype',
									'required' => array( 
													array( METAOPTION_PREFIX .'mediapanel','=', array($media)),
													array( METAOPTION_PREFIX .'mediapanel-titles-behavior','=', array('customtitle', 'posttitle')),
													$titles_required,
											  ),
									'type'    => 'button_set',
									'title'   => esc_html__( 'Subtitle Background Type', 'plethora-framework' ),
									'options' => array( 'foo' => 'Subtitle Color Set Background', 'transparent_film' => 'Transparent Film', 'transparent' => 'No background'),
									'default' => 'foo',
							   		);

				$style_options[] = array(
									'id'       => METAOPTION_PREFIX .'mediapanel-'.$media.'-text-align',
									'type'     => 'button_set', 
									'title'    => esc_html__('Title/Subtitle Text Align', 'plethora-framework'),
									'required' => array( 
													array( METAOPTION_PREFIX .'mediapanel','=', array($media)),
													array( METAOPTION_PREFIX .'mediapanel-titles-behavior','=', array('customtitle', 'posttitle')),
													$titles_required,
											  ),
									'default'  => 'text-left',
									'options'  => array(
											'text-left'   => 'Left',
											'text-center' => 'Center',
											'text-right'  => 'Right'
										),
							   		);

				if ( $media == 'featuredimage' || $media == 'otherimage' ) { 
				
					$style_options[] = array(
										'id'       => METAOPTION_PREFIX .'mediapanel-'.$media.'-parallax',
										'type'     => 'switch', 
										'required' => array( 
														array( METAOPTION_PREFIX .'mediapanel','=', array($media)),
												  ),
										'title'   => esc_html__('Parallax Effect', 'plethora-framework'),
										"default" => 0,
										1         => 'Yes',
										0         => 'No',
								   		);

					$style_options[] = array(
										'id'       => METAOPTION_PREFIX .'mediapanel-'.$media.'-imgvalign',
										'type'     => 'button_set', 
										'required' => array( 
														array( METAOPTION_PREFIX .'mediapanel','=', array($media)),
														array( METAOPTION_PREFIX .'mediapanel-'.$media.'-parallax','=', 0),
												  ),
										'title'   => esc_html__('Image Vertical Align', 'plethora-framework'),
										"default" => 'bg_vcenter',
										'options'  => array(
											'bg_vtop'   => 'Top',
											'bg_vcenter' => 'Center',
											'bg_vbottom'  => 'Bottom'
											),
								   		);
				}

				if ( $media !== 'skincolor' ) { 
				$style_options[] = array(
									'id'       => METAOPTION_PREFIX .'mediapanel-'.$media.'-fullheight',
									'type'     => 'switch', 
									'required' => array( 
													array( METAOPTION_PREFIX .'mediapanel','=', array($media)),
											  ),
									'title'   => esc_html__('Full Height', 'plethora-framework'),
									'descr'   => esc_html__('This will produce an impressive full height display for the media panel', 'plethora-framework'),
									"default" => 0,
									1         => 'Yes',
									0         => 'No',
							   		);
				}

			} elseif ( $media === 'slider') { 

				$style_options[] = array(
									'id'       => METAOPTION_PREFIX .'mediapanel-'.$media.'-fullheight',
									'type'     => 'switch', 
									'required' => array( 
													array( METAOPTION_PREFIX .'mediapanel','=', array($media)),
											  ),
									'title'   => esc_html__('Full Height', 'plethora-framework'),
									'descr'   => esc_html__('This will produce an impressive full height display for the media panel', 'plethora-framework'),
									"default" => 0,
									1         => 'Yes',
									0         => 'No',
							   		);

			}
			return $style_options;

		}

// METABOX OPTIONS END

// MISC THEME OPTIONS START ( applied on HealthFlex )

	    public function themeoptions_general_misc_fields() {

			    $misc_fields = array(
						array(
							'id'     => 'misc-mediapanel-image-start',
							'type'   => 'section',
							'title'  => esc_html__('Media Panel // Image Background Configuration', 'plethora-framework'),
							'indent' => true 
						),
							array(
								'id'            => METAOPTION_PREFIX .'mediapanel-less-full-width-photo-min-panel-height',
								'type'          => 'spinner', 
								'title'         => esc_html__('Media Panel Height / Image (large devices)', 'plethora-framework'), 
								'desc'          => esc_html__('Panel height (in pixels) when a featured or other image is displayed', 'plethora-framework'), 
								'subtitle'      => esc_html__('default: 380px', 'plethora-framework'),
								"default"       => 380,
								"min"           => 1,
								"step"          => 1,
								"max"           => 1000,
								'display_value' => 'text'
								),	
							array(
								'id'            => METAOPTION_PREFIX .'mediapanel-less-full-width-photo-min-panel-height-sm',
								'type'          => 'spinner', 
								'title'         => esc_html__('Media Panel Height / Image (small devices)', 'plethora-framework'), 
								'desc'          => esc_html__('Panel height (in pixels) for small devices when a featured or other image is displayed', 'plethora-framework'), 
								'subtitle'      => esc_html__('default: 280px', 'plethora-framework'),
								"default"       => 280,
								"min"           => 1,
								"step"          => 1,
								"max"           => 1000,
								'display_value' => 'text'
								),	
							array(
								'id'            => METAOPTION_PREFIX .'mediapanel-less-full-width-photo-min-panel-height-xs',
								'type'          => 'spinner', 
								'title'         => esc_html__('Media Panel Height / Image (extra small devices)', 'plethora-framework'), 
								'desc'          => esc_html__('Panel height (in pixels) for extra small devices when a featured or other image is displayed', 'plethora-framework'), 
								'subtitle'      => esc_html__('default: 180px', 'plethora-framework'),
								"default"       => 180,
								"min"           => 1,
								"step"          => 1,
								"max"           => 1000,
								'display_value' => 'text'
								),	
						array(
							'id'     => 'misc-mediapanel-image-end',
							'type'   => 'section',
							'indent' => false 
						),
						array(
							'id'     => 'misc-mediapanel-map-start',
							'type'   => 'section',
							'title'  => esc_html__('Media Panel // Map Background Configuration', 'plethora-framework'),
							'subtitle' => esc_html__('Note: these options are applied only when "Map" background type is displayed.', 'plethora-framework'),
							'indent' => true 
						),
							array(
								'id'            => METAOPTION_PREFIX .'mediapanel-less-map-panel-height',
								'type'          => 'spinner', 
								'title'         => esc_html__('Media Panel Height / Map (large devices)', 'plethora-framework'), 
								'desc'          => esc_html__('Panel height (in pixels) when a map is displayed', 'plethora-framework'), 
								'subtitle'      => esc_html__('default: 480px', 'plethora-framework'),
								"default"       => 480,
								"min"           => 1,
								"step"          => 1,
								"max"           => 1000,
								'display_value' => 'text'
								),	
							array(
								'id'            => METAOPTION_PREFIX .'mediapanel-less-map-panel-height-sm',
								'type'          => 'spinner', 
								'title'         => esc_html__('Media Panel Height / Map (small devices)', 'plethora-framework'), 
								'desc'          => esc_html__('Panel height (in pixels) for small devices when a map is displayed', 'plethora-framework'), 
								'subtitle'      => esc_html__('default: 280px', 'plethora-framework'),
								"default"       => 280,
								"min"           => 1,
								"step"          => 1,
								"max"           => 1000,
								'display_value' => 'text'
								),	
							array(
								'id'            => METAOPTION_PREFIX .'mediapanel-less-map-panel-height-xs',
								'type'          => 'spinner', 
								'title'         => esc_html__('Media Panel Height / Map (extra small devices)', 'plethora-framework'), 
								'desc'          => esc_html__('Panel height (in pixels) for extra small devices when a map is displayed', 'plethora-framework'), 
								'subtitle'      => esc_html__('default: 180px', 'plethora-framework'),
								"default"       => 180,
								"min"           => 1,
								"step"          => 1,
								"max"           => 1000,
								'display_value' => 'text'
								),	
						array(
							'id'     => 'misc-mediapanel-map-end',
							'type'   => 'section',
							'indent' => false 
						),

				);

				return $misc_fields;

	    }
// MISC THEME OPTIONS END ( applied on HealthFlex )

// THEME OPTIONS START

	    /**
	     * Wrapper tag for returning theme options configuration for the media panel
	     * Hooked on 'plethora_themeoptions_mediapanel' filter
	     * @return array
	     *
	     */
		public function get_themeoptions( $sections ) { 

			$sections[] = array(
				'subsection' => true,
							'title'      => esc_html__('Media Panel', 'plethora-framework'),
							'heading'    => esc_html__('MEDIA PANEL CONFIGURATION', 'plethora-framework'),
							'fields'     => $this->themeoptions(),
						  );
			return $sections;
		}

	    /**
	     * Returns theme options configuration for the media panel
	     * @return array
	     */
	    public function themeoptions() {

	    		$themeoptions = array();

				$themeoptions[] = array(
						'id'       => 'mediapanel-basic-start',
						'type'     => 'section',
						'title'    => esc_html__('Default Setup For Basic Options', 'plethora-framework'),
						'subtitle' => esc_html__('Set the default values for the basic options of the Media Panel. All these values can be overriden per page. Note that there are additional configuration options per page.', 'plethora-framework'),
						'indent'   => true 
					);
				$themeoptions[] = array(
						'id'      => METAOPTION_PREFIX .'mediapanel-status',
						'type'    => 'switch', 
						'title'   => esc_html__('Default Display Status', 'plethora-framework'),
						"default" => $this->mediapanel_status,
						"on"      => 'Yes',
						"off"     => 'No',
				);
				$themeoptions[] = array(
						'id'       => METAOPTION_PREFIX .'mediapanel',
						'type'     => 'button_set',
						'title'    => esc_html__('Default Background Type', 'plethora-framework'), 
						'options'  => $this->media_types(),
						'default'  => 'skincolor'
				);
				$themeoptions[] = array(
						'id'       => METAOPTION_PREFIX .'mediapanel-titles-behavior',
						'type'     => 'button_set', 
						'title'    => esc_html__('Default Title / Subtitle Behaviour', 'plethora-framework'),
						'description' => esc_html__('Note: this option is NOT applied when a slider background type is selected.', 'plethora-framework'),
						'default'  => 'posttitle',
						'options'  => array(
								'posttitle'   => 'Default Title / Subtitle',
								'customtitle' => 'Custom Title / Subtitle',
								'notitle'     => 'No Title / Subtitle'
							),
				);
				$themeoptions[] = array(
						'id'          => METAOPTION_PREFIX .'mediapanel-colorset',
						'type'        => 'button_set', 
						'title'       => esc_html__( 'Color Set', 'plethora-framework' ),
						'description' => esc_html__('Color sets affect background and simple/linked text colors. You may edit them under "Theme Options > General > Color Sets" tab.', 'plethora-framework'),
						'default'     => 'primary_section',
						'options'     => Plethora_Module_Style_Ext::get_options_array( array( 'type'	=> 'color_sets', ) ),
				   		);
				$themeoptions[] = array(
						'id'       => METAOPTION_PREFIX .'mediapanel-bgcolor',
						'type'     => 'button_set', 
						'title'    => esc_html__( 'Background Color', 'plethora-framework' ),
						'description' => esc_html__('Note: this option is applied only when "Color" background type is selected.', 'plethora-framework'),
						'default'  => 'color_set',
						'options'  => array( 
										'color_set' => esc_html__( 'According To Color Set', 'plethora-framework' ), 
										'custom'    => esc_html__( 'Custom Background Color', 'plethora-framework' ), 
									  ),
						);
				$themeoptions[] = array(
						'id'      => METAOPTION_PREFIX .'mediapanel-transparentoverlay',
						'type'    => 'button_set', 
						'title'   => esc_html__('Transparent Overlay', 'avoir'),
						'default' => '',
						'options' => array( 
										'transparent_film' => esc_html__( 'Yes', 'plethora-framework' ), 
										''                 => esc_html__( 'No', 'plethora-framework' ), 
									  ),
						);
				$themeoptions[] = array(
						'id'       => METAOPTION_PREFIX .'mediapanel-fadeonscroll',
						'type'     => 'button_set', 
						'title'    => esc_html__( 'Fade Effect On Page Scroll', 'plethora-framework' ),
						'default' => '',
						'options' => array( 
										'fade_on_scroll' => esc_html__( 'Yes', 'plethora-framework' ), 
										''                 => esc_html__( 'No', 'plethora-framework' ), 
									  ),
						);
				$themeoptions[] = array(
						'id'     => 'mediapanel-basic-end',
						'type'   => 'section',
						'indent' => false 
					);

				$themeoptions = array_merge($themeoptions, $this->themeoptions_general_misc_fields() );
				
				return $themeoptions;
		}

// THEME OPTIONS END

	   	// Owl Slider js init output
	   	public function init_script_owlslider() {
			
			$slider = $this->slider_options();
			$output = '
<script>
jQuery(function($) {

    "use strict";

    var $owl = $("#head_panel_slider");			  
    $owl.owlCarousel({
			items              : 1,
			autoplay           : _p.checkBool('. $slider["autoplay"] .'),
			autoplayTimeout    : '.  $slider["autoplaytimeout"] .',
			autoplaySpeed      : '.  $slider["autoplayspeed"] .',
			autoplayHoverPause : _p.checkBool('.  $slider["autoplayhoverpause"] .'),
			nav                : _p.checkBool('.  $slider["nav"] .'),
			dots               : _p.checkBool('.  $slider["dots"] .'),
			loop               : _p.checkBool('.  $slider["loop"] .'),
			mousedrag		   : _p.checkBool('.  $slider["mousedrag"] .'),
			touchdrag		   : _p.checkBool('.  $slider["touchdrag"] .'),
			lazyload      	   : _p.checkBool('.  $slider["lazyload"] .'),
			rtl      	   	   : _p.checkBool('.  $slider["rtl"] .'),
    });
    var $headPanelSliderOwlCarousel = $("#head_panel_slider.owl-carousel");
    $headPanelSliderOwlCarousel.find(".item .container .caption .inner").addClass("hide pause_animation");
    $headPanelSliderOwlCarousel.find(".active .item .container .caption .inner").removeClass("hide pause_animation");
    $owl.on("translated.owl.carousel", function(event) {
        $headPanelSliderOwlCarousel.find(".item .container .caption .inner").addClass("hide pause_animation");
        $headPanelSliderOwlCarousel.find(".active .item .container .caption .inner").removeClass("hide pause_animation");
    })
});
</script>';
			return $output;
	   }
	}
}