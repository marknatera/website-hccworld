<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

Description: Inlcudes theme and third party configuration methods.

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Theme') && class_exists('Plethora') ) {

  class Plethora_Theme extends Plethora {

    function __construct( $slug = 'healthflex', $name = 'Plethora Boilerplate', $ver = '1.0.0' ) {

      # SET BASIC VARIABLES
      $this->theme_slug = $slug;
      $this->theme_name = $name;
      $this->theme_version = $ver;

      // Parent/Child Theme URIs
      define( 'PLE_THEME_ASSETS_URI',       PLE_THEME_URI . '/assets' );              // Theme assets folder
      define( 'PLE_THEME_JS_URI',           PLE_THEME_ASSETS_URI . '/js' );           // Assets JavaScript folder
      define( 'PLE_THEME_FEATURES_URI',     PLE_THEME_URI . '/features' );            // Theme features folder
      define( 'PLE_THEME_TEMPLATES_URI',    PLE_THEME_URI . '/templates' );           // Theme template parts folder 
      define( 'PLE_CHILD_URI',              get_stylesheet_directory_uri() );     // Child theme folder
      define( 'PLE_CHILD_ASSETS_URI',       PLE_CHILD_URI . '/assets' );              // Child theme assets folder
      define( 'PLE_CHILD_JS_URI',           PLE_CHILD_ASSETS_URI . '/js' );           // Child theme assets JavaScript folder
      define( 'PLE_CHILD_FEATURES_URI',     PLE_CHILD_URI . '/features' );            // Child theme includes folder
      define( 'PLE_CHILD_TEMPLATES_URI',    PLE_CHILD_URI . '/templates' );           // Child theme template parts folder 

      // Parent/Child Theme DIRs
      define( 'PLE_THEME_ASSETS_DIR',       PLE_THEME_DIR . '/assets' );              // Theme assets folder
      define( 'PLE_THEME_JS_DIR',           PLE_THEME_ASSETS_DIR . '/js' );           // Theme assets JavaScript folder
      define( 'PLE_THEME_FEATURES_DIR',     PLE_THEME_DIR . '/features' );            // Theme features folder
      define( 'PLE_THEME_TEMPLATES_DIR',    PLE_THEME_DIR . '/templates' );           // Theme template parts folder 
      define( 'PLE_CHILD_DIR',              get_stylesheet_directory() );         // Child theme folder
      define( 'PLE_CHILD_ASSETS_DIR',       PLE_CHILD_DIR . '/assets' );              // Child theme assets folder
      define( 'PLE_CHILD_JS_DIR',           PLE_CHILD_ASSETS_DIR . '/js' );           // Child theme assets JavaScript folder
      define( 'PLE_CHILD_FEATURES_DIR',     PLE_CHILD_DIR . '/features' );            // Child theme includes folder
      define( 'PLE_CHILD_TEMPLATES_DIR',    PLE_CHILD_DIR . '/templates' );           // Child theme template parts folder 


      # PRE-FRAMEWORK HOOKS ( theme actions/filters that must be declared before PF load )
      $this->framework_hooks();

      # LOAD FRAMEWORK
      $this->load_framework();

      # LOAD THEME CONFIGURATION ( theme actions/filters that must be declared after PF load )
      $this->load_theme();
      
      # CREATE TEMPLATE
      global $plethora_template;
      $plethora_template = new Plethora_Template();
    }

    /**
     * All framework related hooks should be set here!
     * NOTICE: don't use Plethora_WP intermediary methods! 
     * @since 1.0
     *
     */
    public function framework_hooks() {

      // Replace 'skincolored_section' with 'primary_section' color sets
      add_filter( 'plethora_module_style_color_sets', array( $this, 'modify_color_sets' ) );

      // Customize class output for VC
      add_filter( 'vc_shortcodes_css_class', array( $this, 'vc_grid_classes' ), 10, 2 );

      // Disable frontend editor for VC
      if ( function_exists( 'vc_disable_frontend' ) ) { vc_disable_frontend( true ); }

      // Disable native VC shortcodes
      if ( function_exists( 'vc_remove_element' ) ) { $this->vc_config();  }

      // Load VC default templates
      add_action('vc_load_default_templates_action', array( 'Plethora_Theme', 'vc_load_custom_templates' ) );          // Custom VC Templates               

      // CF7 element replacements
      add_filter( 'wpcf7_form_elements', array('Plethora_Theme', 'wpcf7_form_elements') );        // CF7 form markup & styling

      // Minor fix for soundclound embeds
      add_filter('oembed_dataparse', array( 'Plethora_Theme', 'soundcloud_oembed_filter'), 90, 3 );        // FIX for oEMBEDS ( soundcloud ) to comply with W3C validation
    }


    /**
    * CORE UPDATE COMPATIBILITY METHOD
    * Replaces 'primary_section' with  'skincolored_section' color set 
    * Hooked on 'plethora_module_style_color_sets' filter
    */
    public function modify_color_sets( $color_sets ) {

      if ( isset( $color_sets['primary']['value'] )  ) {
        
        $color_sets['primary']['value'] = 'skincolored_section'; 
      }

      return $color_sets;
    }

    /**
    * CORE UPDATE COMPATIBILITY METHOD
    * Mapping shortcode parameters for Visual Composer Panel ( statically )
    * @since 1.0
    */
    public static function vc_grid_classes( $class_string, $tag ) {
          
          // Basic grid columns
        $class_string = preg_replace( '/vc_col-lg-(\d{1,2})/', 'col-lg-$1', $class_string );
        $class_string = preg_replace( '/vc_col-md-(\d{1,2})/', 'col-md-$1', $class_string ); 
        $class_string = preg_replace( '/vc_col-sm-(\d{1,2})/', 'col-sm-$1', $class_string ); 
        $class_string = preg_replace( '/vc_col-xs-(\d{1,2})/', 'col-xs-$1', $class_string ); 
          // Offset
        $class_string = preg_replace( '/vc_col-lg-offset-(\d{1,2})/', 'col-lg-offset-$1', $class_string );
        $class_string = preg_replace( '/vc_col-md-offset-(\d{1,2})/', 'col-md-offset-$1', $class_string );
        $class_string = preg_replace( '/vc_col-sm-offset-(\d{1,2})/', 'col-sm-offset-$1', $class_string );
        $class_string = preg_replace( '/vc_col-xs-offset-(\d{1,2})/', 'col-xs-offset-$1', $class_string );
          // Pull
        $class_string = preg_replace( '/vc_col-lg-pull-(\d{1,2})/', 'col-lg-pull-$1', $class_string );
        $class_string = preg_replace( '/vc_col-md-pull-(\d{1,2})/', 'col-md-pull-$1', $class_string );
        $class_string = preg_replace( '/vc_col-sm-pull-(\d{1,2})/', 'col-sm-pull-$1', $class_string );
        $class_string = preg_replace( '/vc_col-xs-pull-(\d{1,2})/', 'col-xs-pull-$1', $class_string );
          // Push
        $class_string = preg_replace( '/vc_col-lg-push-(\d{1,2})/', 'col-lg-push-$1', $class_string );
        $class_string = preg_replace( '/vc_col-md-push-(\d{1,2})/', 'col-md-push-$1', $class_string );
        $class_string = preg_replace( '/vc_col-sm-push-(\d{1,2})/', 'col-sm-push-$1', $class_string );
        $class_string = preg_replace( '/vc_col-xs-push-(\d{1,2})/', 'col-xs-push-$1', $class_string );
        return $class_string; 
    }

    /**
    * Enable/disable Visual Composer elements
    * @since 1.3
    */
    static function vc_config() { 

        // VC Elements configuration
          // vc_remove_element( 'vc_accordion' );   // Deprecated after 4.9.1
          // vc_remove_element( 'vc_accordion_tab' );
          vc_remove_element( 'vc_button' );
          // vc_remove_element( 'vc_button2' );     // Deprecated after 4.9.1, but still has to be active due to a notice on Media grid shortcode
          vc_remove_element( 'vc_carousel' );
          // vc_remove_element( 'vc_column_text' );
          // vc_remove_element( 'vc_custom_heading' );  // Has to be active due to a notice on Media grid shortcode
          vc_remove_element( 'vc_cta_button' );
          vc_remove_element( 'vc_cta_button2' );
          // vc_remove_element( 'vc_facebook' );
          vc_remove_element( 'vc_flickr' );
          vc_remove_element( 'vc_gallery' );
          // vc_remove_element( 'vc_gmaps' );
          // vc_remove_element( 'vc_googleplus' );
          vc_remove_element( 'vc_images_carousel' );
          // vc_remove_element( 'vc_message' );
          // vc_remove_element( 'vc_pie' );
          // vc_remove_element( 'vc_pinterest' );
          vc_remove_element( 'vc_posts_grid' );
          vc_remove_element( 'vc_posts_slider' );
          vc_remove_element( 'vc_progress_bar' );
          // vc_remove_element( 'vc_raw_html' );
          // vc_remove_element( 'vc_raw_js' );
          // vc_remove_element( 'vc_separator' );
          // vc_remove_element( 'vc_single_image' );
          // vc_remove_element( 'vc_tab' );
          // vc_remove_element( 'vc_tabs' );      // Deprecated after 4.9.1
          vc_remove_element( 'vc_teaser_grid' );
          // vc_remove_element( 'vc_text_separator' );
          // vc_remove_element( 'vc_toggle' );
          vc_remove_element( 'vc_tour' );
          // vc_remove_element( 'vc_twitter' );
          // vc_remove_element( 'vc_tweetmeme' );
          // vc_remove_element( 'vc_video' );
          // vc_remove_element( 'vc_widget_sidebar' );
          
          // WP shortcodes by VC
          // vc_remove_element( 'vc_wp_archives' );
          // vc_remove_element( 'vc_wp_calendar' );
          // vc_remove_element( 'vc_wp_categories' );
          // vc_remove_element( 'vc_wp_custommenu' );
          // vc_remove_element( 'vc_wp_links' );
          // vc_remove_element( 'vc_wp_meta' );
          // vc_remove_element( 'vc_wp_posts' );
          // vc_remove_element( 'vc_wp_recentcomments' );
          // vc_remove_element( 'vc_wp_pages' );
          // vc_remove_element( 'vc_wp_rss' );
          // vc_remove_element( 'vc_wp_tagcloud' );
          // vc_remove_element( 'vc_wp_text' );
          // vc_remove_element( 'vc_wp_search' );
          
          // Third party 
          // vc_remove_element( 'contact-form-7' );
          // vc_remove_element( 'gravityform' );
          // vc_remove_element( 'layerslider_vc' );
          // vc_remove_element( 'rev_slider_vc' );

        // VC Elements Parameter Configuration
          vc_remove_param("vc_single_image", "img_link_target"); // Remove link target ( since we use prettyphoto by default )

    }

    /**
     * Loads custom VC templates
     * @return string
     * @since 1.0
     */
    static function vc_load_custom_templates() {

      /** Home Page Template */
      $data                 = array();
      $data['weight']       = 0;
      $data['name']         = esc_html__( '1. Home Page', 'healthflex' );
      $data['image_path']   = preg_replace( '/\s/', '%20', get_template_directory_uri() . '/assets/images/visualcomposer/home.jpg' ); 
      $data['custom_class'] = 'custom_template_for_vc_custom_template';
      $data['content']      = '[vc_row full_width="0" full_height="0" row_padding="no_padding" cols_padding="no_cols_padding" elevate="elevate" background="transparent" el_class="folded_section"][vc_column width="1/4" color_set="skincolored_section" background="transparent" offset="vc_col-xs-6" css=".vc_custom_1446619389066{background-color: #088eff !important;}"][plethora_teaserbox title="Departments" subtitle="The Backbone of our Clinic" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2F||" boxed_styling="boxed" image="1160" media_colorset="transparent" text_colorset="transparent" button="1" button_style="btn-secondary" same_height="same_height_col"][/plethora_teaserbox][/vc_column][vc_column width="1/4" color_set="skincolored_section" background="transparent" offset="vc_col-xs-6" css=".vc_custom_1447875610924{background-color: #269cff !important;}"][plethora_teaserbox title="Medical Services" subtitle="A list of all available" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fservices%2F||" boxed_styling="boxed" image="1163" media_colorset="transparent" text_colorset="transparent" button="1" button_style="btn-secondary" same_height="same_height_col"][/plethora_teaserbox][/vc_column][vc_column width="1/4" color_set="skincolored_section" offset="vc_col-xs-6" css=".vc_custom_1447875785935{background-color: #45aaff !important;}"][plethora_teaserbox title="Find a doctor" subtitle="All our staff by department" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Four-staff%2F||" boxed_styling="boxed" image="1175" media_colorset="transparent" text_colorset="transparent" button="1" button_style="btn-secondary" same_height="same_height_col"][/plethora_teaserbox][/vc_column][vc_column width="1/4" color_set="skincolored_section" background="transparent" offset="vc_col-xs-6" css=".vc_custom_1447875797423{background-color: #64b8ff !important;}"][plethora_teaserbox title="Request an appointment" subtitle="Call us or fill in a form" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fappointment-booking%2F||" boxed_styling="boxed" image="1171" media_colorset="transparent" text_colorset="transparent" button="1" button_style="btn-secondary" same_height="same_height_col"][/plethora_teaserbox][/vc_column][/vc_row][vc_row full_width="0" full_height="0" row_padding="no_top_padding" sep_bottom="separator_bottom sep_angled_positive_bottom" particles="0"][vc_column][plethora_headinggroup subtitle="We\' ve built a long standing relationship based on trust" align="text-left"] <h2>Welcome to Medicus Clinic</h2> [/plethora_headinggroup][/vc_column][vc_column width="2/3" heading_align="text-center" align="text-left" margin="margin_bottom_grid"][vc_column_text]Personalized patient care is what sets Medicus Medical Center apart. When you visit one of our four San Francisco campus locations you can expect to receive world class care. Expert physician specialists and caring clinical staff provide you with an exceptional health care experience. Personalized patient care is what sets Medicus Medical Center apart.[/vc_column_text][vc_text_separator title="MODERN MEDICAL FACILITIES" title_align="separator_align_left" align="align_right" color="black"][vc_column_text]Personalized patient care is what sets Medicus Medical Center apart. When you visit one of our four San Francisco campus locations you can expect to receive world class care. Expert physician specialists and caring clinical staff provide you with an exceptional health care experience.[/vc_column_text][plethora_button button_text="Checkout our Facilities" button_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fabout-us%2F%23facilities||" button_with_icon="with-icon" button_icon="fa fa-hospital-o" button_icon_align="icon-left"][/vc_column][vc_column width="1/3" align="text-left"][plethora_teaserbox title="Patient &amp; Visitor Guide" subtitle="Plan your visit to our Clinic" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fvisit-guide%2F||" image="516" media_ratio="stretchy_wrapper ratio_16-9" text_colorset="skincolored_section" text_boxed_styling="boxed" button="1" button_style="btn-secondary"][/plethora_teaserbox][/vc_column][/vc_row][vc_row color_set="secondary_section" full_width="0" full_height="0" particles="0"][vc_column][plethora_headinggroup subtitle="The Backbone of our Clinic" align="text-left"] <h2>Medical Departments</h2> [/plethora_headinggroup][/vc_column][vc_column heading_align="text-center" align="text-left" margin="margin_bottom_grid" offset="vc_col-md-8"][vc_row_inner][vc_column_inner width="1/3"][plethora_teaserbox title="Surgery" subtitle="Dr. Avis Stankovic" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2Fcosmetic-surgery%2F||" image="150" media_colorset="transparent" media_ratio="stretchy_wrapper ratio_4-3" text_colorset="light_section" text_boxed_styling="boxed_special" button="1" button_style="btn-primary"][/plethora_teaserbox][/vc_column_inner][vc_column_inner width="1/3"][plethora_teaserbox title="MICROBIOLOGY" subtitle="Dr. John Manios" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2Fcosmetic-surgery%2F||" image="386" media_colorset="transparent" media_ratio="stretchy_wrapper ratio_4-3" text_colorset="light_section" text_boxed_styling="boxed_special" button="1" button_style="btn-primary"][/plethora_teaserbox][/vc_column_inner][vc_column_inner width="1/3"][plethora_teaserbox title="PATHOLOGY" subtitle="Dr. Marie Curie" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2Fcosmetic-surgery%2F||" image="447" media_ratio="stretchy_wrapper ratio_4-3" text_colorset="light_section" text_boxed_styling="boxed_special" button="1" button_style="btn-primary"][/plethora_teaserbox][/vc_column_inner][/vc_row_inner][/vc_column][vc_column align="text-left" offset="vc_col-md-4"][vc_text_separator title="MODERN EQUIPMENT" title_align="separator_align_left" align="align_right" color="black"][vc_column_text]Personalized patient care is what sets Medicus Medical Center apart. When you visit one of our four San Francisco campus locations you can expect to receive world class care. Expert physician specialists and caring clinical staff provide you with an exceptional patient care is what sets Medicus Medical Center apart health care experience.[/vc_column_text][plethora_button button_text="Checkout All Departments" button_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2F||" button_inline="btn_block" button_with_icon="with-icon" button_icon="fa fa-sitemap" button_icon_align="icon-left"][/vc_column][/vc_row][vc_row color_set="light_section" full_width="0" full_height="0" sep_top="separator_top sep_angled_positive_top" sep_bottom="separator_bottom sep_angled_positive_bottom" particles="0"][vc_column][plethora_headinggroup subtitle="We cover a big variety of medical services" align="text-left"] <h2>Featured Services</h2> [/plethora_headinggroup][/vc_column][vc_column heading_align="text-center" align="text-left" margin="margin_bottom_grid" offset="vc_col-md-8"][vc_row_inner][vc_column_inner width="1/3"][plethora_teaserbox title="Free Checkup" subtitle="The basis of Wellness" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Ffree-checkup-offer%2F||" boxed_styling="boxed_special" image="193" media_colorset="transparent" text_colorset="transparent" button="1" button_style="btn-primary" same_height="same_height_col" media_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthx%2Fservices%2F||"][/plethora_teaserbox][/vc_column_inner][vc_column_inner width="1/3"][plethora_teaserbox title="Cardio Exam" subtitle="With High-End Equipment" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2Fcosmetic-surgery%2F||" boxed_styling="boxed_special" image="196" media_colorset="transparent" text_colorset="transparent" button="1" button_style="btn-primary" same_height="same_height_col" button_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthx%2Fservices%2F||"][/plethora_teaserbox][/vc_column_inner][vc_column_inner width="1/3"][plethora_teaserbox title="DNA Testing" subtitle="Accurate Results" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fservices%2F||" boxed_styling="boxed_special" image="195" media_colorset="transparent" text_colorset="transparent" button="1" button_style="btn-primary" same_height="same_height_col" button_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthx%2Fservices%2F||"][/plethora_teaserbox][/vc_column_inner][/vc_row_inner][vc_empty_space height="24px"][vc_column_text]Personalized patient care is what sets Medicus Medical Center apart. When you visit one of our four San Francisco campus locations you can expect to receive world class care. Expert physician specialists and caring clinical staff provide you with an exceptional health care experience.[/vc_column_text][plethora_button button_text="Checkout All Medical Services" button_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fservices%2F||" button_style="btn-primary" button_with_icon="with-icon" button_icon="wmi icon-i-pathology" button_icon_align="icon-left"][/vc_column][vc_column align="text-left" offset="vc_col-md-4"][vc_text_separator title="Working Hours" title_align="separator_align_left" align="align_right" color="black" el_class="x_bold"][vc_raw_html]JTNDZGl2JTIwY2xhc3MlM0QlMjJ0aW1ldGFibGUlMjIlM0UlMEElMjAlMjAlM0N0YWJsZSUyMGNsYXNzJTNEJTIydGltZXRhYmxlX2hvdXJzJTIyJTNFJTBBJTIwJTIwJTIwJTIwJTNDdHIlM0UlMEElMjAlMjAlMjAlMjAlMjAlMjAlM0N0ZCUzRU1PTkRBWSUzQyUyRnRkJTNFJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTNDdGQlM0UwOSUzQTAwLTIwJTNBMDAlM0MlMkZ0ZCUzRSUwQSUyMCUyMCUyMCUyMCUzQyUyRnRyJTNFJTBBJTIwJTIwJTIwJTIwJTNDdHIlM0UlMEElMjAlMjAlMjAlMjAlMjAlMjAlM0N0ZCUzRVRVRVNEQVklM0MlMkZ0ZCUzRSUwQSUyMCUyMCUyMCUyMCUyMCUyMCUzQ3RkJTNFMDklM0EwMC0yMSUzQTAwJTNDJTJGdGQlM0UlMEElMjAlMjAlMjAlMjAlM0MlMkZ0ciUzRSUwQSUyMCUyMCUyMCUyMCUzQ3RyJTNFJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTNDdGQlM0VXRURORVNEQVklM0MlMkZ0ZCUzRSUwQSUyMCUyMCUyMCUyMCUyMCUyMCUzQ3RkJTNFMDklM0EwMC0yMCUzQTAwJTNDJTJGdGQlM0UlMEElMjAlMjAlMjAlMjAlM0MlMkZ0ciUzRSUwQSUyMCUyMCUyMCUyMCUzQ3RyJTNFJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTNDdGQlM0VUSFVSU0RBWSUzQyUyRnRkJTNFJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTNDdGQlM0UyNC1IT1VSJTIwU0hJRlQlM0MlMkZ0ZCUzRSUwQSUyMCUyMCUyMCUyMCUzQyUyRnRyJTNFJTBBJTIwJTIwJTIwJTIwJTNDdHIlM0UlMEElMjAlMjAlMjAlMjAlMjAlMjAlM0N0ZCUzRUZSSURBWSUzQyUyRnRkJTNFJTBBJTIwJTIwJTIwJTIwJTIwJTIwJTNDdGQlM0UwOSUzQTAwLTIxJTNBMDAlM0MlMkZ0ZCUzRSUwQSUyMCUyMCUyMCUyMCUzQyUyRnRyJTNFJTBBJTIwJTIwJTIwJTIwJTNDdHIlM0UlMEElMjAlMjAlMjAlMjAlMjAlMjAlM0N0ZCUzRVNBVFVSREFZJTNDJTJGdGQlM0UlMEElMjAlMjAlMjAlMjAlMjAlMjAlM0N0ZCUzRTA5JTNBMDAtMTglM0EwMCUzQyUyRnRkJTNFJTBBJTIwJTIwJTIwJTIwJTNDJTJGdHIlM0UlMEElMjAlMjAlMjAlMjAlM0N0ciUzRSUwQSUyMCUyMCUyMCUyMCUyMCUyMCUzQ3RkJTNFU1VOREFZJTNDJTJGdGQlM0UlMEElMjAlMjAlMjAlMjAlMjAlMjAlM0N0ZCUzRTExJTNBMDAtMTklM0EwMCUzQyUyRnRkJTNFJTBBJTIwJTIwJTIwJTIwJTNDJTJGdHIlM0UlMEElMjAlMjAlM0MlMkZ0YWJsZSUzRSUwQSUzQyUyRmRpdiUzRQ==[/vc_raw_html][vc_text_separator title="Fees &amp; Insurance" title_align="separator_align_left" align="align_right" color="black" el_class="x_bold"][vc_column_text]For the convenience of our clients the <strong>Medicus Health Center</strong> and Hospital provides direct insurance billing with all the major international insurance providers and assistance companies.[/vc_column_text][/vc_column][/vc_row][vc_row color_set="secondary_section" align="text-center" full_width="0" full_height="0" cols_valign="vcenter" transparent_overlay="transparent_film" background="bgimage" bgimage="1158" parallax="0"][vc_column align="text-center"][plethora_headinggroup subtitle="Call Now (600) 123-4567 and receive Top Quality Healthcare for you and your Family" align="text-center"] <h2>Need a personal health plan?</h2> [/plethora_headinggroup][plethora_button button_text="Request a Plan" button_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fhealth-plans%2F||" button_align="text-center" button_style="btn-success" button_with_icon="with-icon" button_icon="fa fa-check"][/vc_column][/vc_row]';
      vc_add_default_templates( $data );

      /** About Us Page Template */
      $data                 = array();
      $data['weight']       = 1;
      $data['name']         = esc_html__( '2. About Us Page', 'healthflex' );
      $data['image_path']   = preg_replace( '/\s/', '%20', get_template_directory_uri() . '/assets/images/visualcomposer/about.jpg' ); 
      $data['custom_class'] = 'custom_template_for_vc_custom_template';
      $data['content']      = '[vc_row full_width="0" full_height="0" sep_bottom="separator_bottom sep_angled_positive_bottom" particles="0"][vc_column margin="margin_bottom_grid" offset="vc_col-md-6"][vc_column_text]Our team strives to achieve excellence in every aspect of the production process, from the preliminary hand-drawn sketches to the aftersales support. We carefully design our themes to be easy and flexible while making sure that most FAQ of our customers can be answered by our detailed documentation.[/vc_column_text][vc_text_separator title="Our Vision" title_align="separator_align_left"][vc_column_text]We focus on using the latest web standards and practices regarding UX guidelines and WordPress theme development and strongly encourage our customers to follow us on this. The use of a child theme and the plugin-based development of Plethora Framework ensure hassle-free updates and peace of mind. Personalized patient care is what sets California Pacific Medical Center apart. When you visit one of our four San Francisco campus locations you can expect to receive world class care. Expert physician specialists and caring clinical staff provide you with an exceptional health care experience.[/vc_column_text][/vc_column][vc_column offset="vc_col-md-6"][vc_video link="http://vimeo.com/67157062"][/vc_column][vc_column][vc_empty_space][/vc_column][vc_column align="text-left" color_set="white_section"][vc_empty_space][vc_column_text]<img class="alignnone size-full wp-image-1111" src="http://plethorathemes.com/healthflex/wp-content/uploads/2015/09/logo_abim.png" alt="logo_abim" width="218" height="75" />                <img class="alignnone wp-image-1110 size-full" src="http://plethorathemes.com/healthflex/wp-content/uploads/2015/09/logo_aap.png" alt="logo_aap" width="218" height="75" />                <img class="alignnone size-full wp-image-1109" src="http://plethorathemes.com/healthflex/wp-content/uploads/2015/09/logo_aafp.png" alt="logo_aafp" width="218" height="75" />        <img class="alignnone size-full wp-image-1112" src="http://plethorathemes.com/healthflex/wp-content/uploads/2015/09/logo_nrha.png" alt="logo_nrha" width="218" height="75" />.[/vc_column_text][vc_empty_space height="0px"][/vc_column][/vc_row][vc_row color_set="light_section" full_width="0" full_height="0" sep_bottom="separator_bottom sep_angled_positive_bottom" particles="0" el_id="facilities"][vc_column align="text-center"][plethora_headinggroup subtitle="We constantly invest in high-end equipment" align="text-left"] <h2 style="text-align: left;">Facilities</h2> [/plethora_headinggroup][/vc_column][vc_column margin="margin_bottom_grid" offset="vc_col-md-6"][plethora_fixedmedia image="516"][/vc_column][vc_column offset="vc_col-md-6"][vc_column_text]Our team strives to achieve excellence in every aspect of the production process, from the preliminary hand-drawn sketches to the aftersales support. We carefully design our themes to be easy and flexible while making sure that most FAQ of our customers can be answered by our detailed documentation. We focus on using the latest web standards and practices regarding UX guidelines and WordPress theme development and strongly encourage our customers to follow us on this. The use of a child theme and the plugin-based development of Plethora Framework ensure hassle-free updates and peace of mind. Personalized patient care is what sets California Pacific Medical Center apart. When you visit one of our four San Francisco campus locations you can expect to receive world class care. Expert physician specialists and caring clinical staff provide you with an exceptional health care experience.[/vc_column_text][/vc_column][vc_column][vc_empty_space][vc_media_grid gap="10" grid_id="vc_gid:1447235151968-2bd81c11-c15e-9" include="1179,1180,1181,1182,1183,1184"][/vc_column][/vc_row][vc_row color_set="skincolored_section" full_width="0" full_height="0" cols_valign="vcenter" transparent_overlay="transparent_film" background="bgimage" bgimage="1191" parallax="0"][vc_column][plethora_headinggroup subtitle="Testimonials Slider Feature" align="text-center"] <h2 style="text-align: center;">Talking about us</h2> [/plethora_headinggroup][/vc_column][vc_column offset="vc_col-md-2"][/vc_column][vc_column align="text-center" offset="vc_col-md-8"][plethora_testimonials testimonial_category="testimonials"][/vc_column][vc_column offset="vc_col-md-2"][/vc_column][/vc_row]';
      vc_add_default_templates( $data );

      /** Services Page Template */
      $data                 = array();
      $data['weight']       = 2;
      $data['name']         = esc_html__( '3. Services Page', 'healthflex' );
      $data['image_path']   = preg_replace( '/\s/', '%20', get_template_directory_uri() . '/assets/images/visualcomposer/services.jpg' ); 
      $data['custom_class'] = 'custom_template_for_vc_custom_template';
      $data['content']      = '[vc_row full_width="0" full_height="0" particles="0"][vc_column][vc_tta_accordion shape="square" color="white" spacing="10" c_icon="chevron" active_section="100" collapsible_all="true"][vc_tta_section title="Neurology" tab_id="neurology"][vc_row_inner][vc_column_inner][vc_empty_space][/vc_column_inner][vc_column_inner width="2/3"][vc_column_text] <h5><strong>Mamography</strong></h5> Specialized X-rays of the breast to aid in the early detection of breast cancer. A yearly mammogram is recommended for women age 40 and older. <h5><strong>Reproductive Endocrinology</strong></h5> Care and treatment for couples having difficulties conceiving a child. Conventional therapies such as medication and surgery can help your dreams of parenthood come true. Our partnership with Mayo Clinic allows us to provide referrals when advanced reproductive techniques are required. <h5><strong>Integrative Medicine</strong></h5> Alternative therapies stemming from the premise the mind, body and spirit function as one and addressing all promotes healing. <h5><strong>Chemical Dependency Treatment</strong></h5> Care for people with alcohol and other drug addictions. All treatment plans are individualized, ensuring that each person\'s unique needs are met to promote healing and recovery. Dignity and respect are foundational values that guide our work. <h5><strong>Physical Therapy</strong></h5> Therapeutic care to restore movement and function to people disabled by disease or injury. Care may include exercise, training in activities of daily living and education. Our team works in collaboration with other specialties to offer comprehensive care and quickly restore you to your optimal health.[/vc_column_text][vc_empty_space height="16px"][/vc_column_inner][vc_column_inner width="1/3"][plethora_teaserbox title="Neurology Department" subtitle="(+555) 959-595-959" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2Fcosmetic-surgery%2F||" boxed_styling="boxed" media_type="icon" text_colorset="skincolored_section" button="1" button_text="Visit Department" button_style="btn-secondary" icon="wmi icon-i-neurology"][/plethora_teaserbox][plethora_profilegrid columns="1" color_set="skincolored_section" excerpt="0" profiles="182"][/vc_column_inner][vc_column_inner][vc_empty_space][/vc_column_inner][/vc_row_inner][/vc_tta_section][vc_tta_section title="Pediatrics" tab_id="1447153984663-e49cf286-1878"][vc_row_inner][vc_column_inner][vc_empty_space][/vc_column_inner][vc_column_inner width="2/3"][vc_column_text] <h5><strong>Mamography</strong></h5> Specialized X-rays of the breast to aid in the early detection of breast cancer. A yearly mammogram is recommended for women age 40 and older. <h5><strong>Reproductive Endocrinology</strong></h5> Care and treatment for couples having difficulties conceiving a child. Conventional therapies such as medication and surgery can help your dreams of parenthood come true. Our partnership with Mayo Clinic allows us to provide referrals when advanced reproductive techniques are required. <h5><strong>Integrative Medicine</strong></h5> Alternative therapies stemming from the premise the mind, body and spirit function as one and addressing all promotes healing. <h5><strong>Chemical Dependency Treatment</strong></h5> Care for people with alcohol and other drug addictions. All treatment plans are individualized, ensuring that each person\'s unique needs are met to promote healing and recovery. Dignity and respect are foundational values that guide our work. <h5><strong>Physical Therapy</strong></h5> Therapeutic care to restore movement and function to people disabled by disease or injury. Care may include exercise, training in activities of daily living and education. Our team works in collaboration with other specialties to offer comprehensive care and quickly restore you to your optimal health.[/vc_column_text][vc_empty_space height="16px"][/vc_column_inner][vc_column_inner width="1/3"][plethora_teaserbox title="Pediatrics Department" subtitle="(+555) 959-595-959" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2Fcosmetic-surgery%2F||" boxed_styling="boxed" media_type="icon" text_colorset="skincolored_section" button="1" button_text="Visit Department" button_style="btn-secondary" icon="wmi icon-i-pediatrics"][/plethora_teaserbox][plethora_profilegrid columns="1" color_set="skincolored_section" excerpt="0" profiles="1288"][/vc_column_inner][vc_column_inner][vc_empty_space][/vc_column_inner][/vc_row_inner][/vc_tta_section][vc_tta_section title="Diagnostic Imaging" tab_id="1448268456536-722ece38-0b79"][vc_row_inner][vc_column_inner][vc_empty_space][/vc_column_inner][vc_column_inner width="2/3"][vc_column_text] <h5><strong>Mamography</strong></h5> Specialized X-rays of the breast to aid in the early detection of breast cancer. A yearly mammogram is recommended for women age 40 and older. <h5><strong>Reproductive Endocrinology</strong></h5> Care and treatment for couples having difficulties conceiving a child. Conventional therapies such as medication and surgery can help your dreams of parenthood come true. Our partnership with Mayo Clinic allows us to provide referrals when advanced reproductive techniques are required. <h5><strong>Integrative Medicine</strong></h5> Alternative therapies stemming from the premise the mind, body and spirit function as one and addressing all promotes healing. <h5><strong>Chemical Dependency Treatment</strong></h5> Care for people with alcohol and other drug addictions. All treatment plans are individualized, ensuring that each person\'s unique needs are met to promote healing and recovery. Dignity and respect are foundational values that guide our work. <h5><strong>Physical Therapy</strong></h5> Therapeutic care to restore movement and function to people disabled by disease or injury. Care may include exercise, training in activities of daily living and education. Our team works in collaboration with other specialties to offer comprehensive care and quickly restore you to your optimal health.[/vc_column_text][vc_empty_space height="16px"][/vc_column_inner][vc_column_inner width="1/3"][plethora_teaserbox title="Diagnostic Imaging" subtitle="(+555) 959-595-959" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2Fcosmetic-surgery%2F||" boxed_styling="boxed" media_type="icon" text_colorset="skincolored_section" button="1" button_text="Visit Department" button_style="btn-secondary" icon="wmi icon-i-radiology"][/plethora_teaserbox][plethora_profilegrid columns="1" color_set="skincolored_section" excerpt="0" profiles="184"][/vc_column_inner][vc_column_inner][vc_empty_space][/vc_column_inner][/vc_row_inner][/vc_tta_section][vc_tta_section title="Cardiology" tab_id="1448268485319-20bb3d67-9543"][vc_row_inner][vc_column_inner][vc_empty_space][/vc_column_inner][vc_column_inner width="2/3"][vc_column_text] <h5><strong>Mamography</strong></h5> Specialized X-rays of the breast to aid in the early detection of breast cancer. A yearly mammogram is recommended for women age 40 and older. <h5><strong>Reproductive Endocrinology</strong></h5> Care and treatment for couples having difficulties conceiving a child. Conventional therapies such as medication and surgery can help your dreams of parenthood come true. Our partnership with Mayo Clinic allows us to provide referrals when advanced reproductive techniques are required. <h5><strong>Integrative Medicine</strong></h5> Alternative therapies stemming from the premise the mind, body and spirit function as one and addressing all promotes healing. <h5><strong>Chemical Dependency Treatment</strong></h5> Care for people with alcohol and other drug addictions. All treatment plans are individualized, ensuring that each person\'s unique needs are met to promote healing and recovery. Dignity and respect are foundational values that guide our work. <h5><strong>Physical Therapy</strong></h5> Therapeutic care to restore movement and function to people disabled by disease or injury. Care may include exercise, training in activities of daily living and education. Our team works in collaboration with other specialties to offer comprehensive care and quickly restore you to your optimal health.[/vc_column_text][vc_empty_space height="16px"][/vc_column_inner][vc_column_inner width="1/3"][plethora_teaserbox title="Cardiology Department" subtitle="(+555) 959-595-959" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2Fcosmetic-surgery%2F||" boxed_styling="boxed" media_type="icon" text_colorset="skincolored_section" button="1" button_text="Visit Department" button_style="btn-secondary" icon="wmi icon-i-cardiology"][/plethora_teaserbox][plethora_profilegrid columns="1" color_set="skincolored_section" excerpt="0" profiles="1279"][/vc_column_inner][vc_column_inner][vc_empty_space][/vc_column_inner][/vc_row_inner][/vc_tta_section][vc_tta_section title="Cosmetic Surgery" tab_id="1448268507802-ddaf51d1-8acb"][vc_row_inner][vc_column_inner][vc_empty_space][/vc_column_inner][vc_column_inner width="2/3"][vc_column_text] <h5><strong>Mamography</strong></h5> Specialized X-rays of the breast to aid in the early detection of breast cancer. A yearly mammogram is recommended for women age 40 and older. <h5><strong>Reproductive Endocrinology</strong></h5> Care and treatment for couples having difficulties conceiving a child. Conventional therapies such as medication and surgery can help your dreams of parenthood come true. Our partnership with Mayo Clinic allows us to provide referrals when advanced reproductive techniques are required. <h5><strong>Integrative Medicine</strong></h5> Alternative therapies stemming from the premise the mind, body and spirit function as one and addressing all promotes healing. <h5><strong>Chemical Dependency Treatment</strong></h5> Care for people with alcohol and other drug addictions. All treatment plans are individualized, ensuring that each person\'s unique needs are met to promote healing and recovery. Dignity and respect are foundational values that guide our work. <h5><strong>Physical Therapy</strong></h5> Therapeutic care to restore movement and function to people disabled by disease or injury. Care may include exercise, training in activities of daily living and education. Our team works in collaboration with other specialties to offer comprehensive care and quickly restore you to your optimal health.[/vc_column_text][vc_empty_space height="16px"][/vc_column_inner][vc_column_inner width="1/3"][plethora_teaserbox title="Cosmetic Surgery" subtitle="(+555) 959-595-959" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2Fcosmetic-surgery%2F||" boxed_styling="boxed" media_type="icon" text_colorset="skincolored_section" button="1" button_text="Visit Department" button_style="btn-secondary" icon="wmi icon-i-waiting-area"][/plethora_teaserbox][plethora_profilegrid columns="1" color_set="skincolored_section" excerpt="0" profiles="1285"][/vc_column_inner][vc_column_inner][vc_empty_space][/vc_column_inner][/vc_row_inner][/vc_tta_section][vc_tta_section title="Microbiology Lab" tab_id="1448268511116-8714a91a-ea55"][vc_row_inner][vc_column_inner][vc_empty_space][/vc_column_inner][vc_column_inner width="2/3"][vc_column_text] <h5><strong>Mamography</strong></h5> Specialized X-rays of the breast to aid in the early detection of breast cancer. A yearly mammogram is recommended for women age 40 and older. <h5><strong>Reproductive Endocrinology</strong></h5> Care and treatment for couples having difficulties conceiving a child. Conventional therapies such as medication and surgery can help your dreams of parenthood come true. Our partnership with Mayo Clinic allows us to provide referrals when advanced reproductive techniques are required. <h5><strong>Integrative Medicine</strong></h5> Alternative therapies stemming from the premise the mind, body and spirit function as one and addressing all promotes healing. <h5><strong>Chemical Dependency Treatment</strong></h5> Care for people with alcohol and other drug addictions. All treatment plans are individualized, ensuring that each person\'s unique needs are met to promote healing and recovery. Dignity and respect are foundational values that guide our work. <h5><strong>Physical Therapy</strong></h5> Therapeutic care to restore movement and function to people disabled by disease or injury. Care may include exercise, training in activities of daily living and education. Our team works in collaboration with other specialties to offer comprehensive care and quickly restore you to your optimal health.[/vc_column_text][vc_empty_space height="16px"][/vc_column_inner][vc_column_inner width="1/3"][plethora_teaserbox title="Microbiology Lab" subtitle="(+555) 959-595-959" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2Fcosmetic-surgery%2F||" boxed_styling="boxed" media_type="icon" text_colorset="skincolored_section" button="1" button_text="Visit Department" button_style="btn-secondary" icon="wmi icon-i-laboratory"][/plethora_teaserbox][plethora_profilegrid columns="1" color_set="skincolored_section" excerpt="0" profiles="1291"][/vc_column_inner][vc_column_inner][vc_empty_space][/vc_column_inner][/vc_row_inner][/vc_tta_section][vc_tta_section title="Gynaecology &amp; Birth" tab_id="1448268512473-e695d66f-bd21"][vc_row_inner][vc_column_inner][vc_empty_space][/vc_column_inner][vc_column_inner width="2/3"][vc_column_text] <h5><strong>Mamography</strong></h5> Specialized X-rays of the breast to aid in the early detection of breast cancer. A yearly mammogram is recommended for women age 40 and older. <h5><strong>Reproductive Endocrinology</strong></h5> Care and treatment for couples having difficulties conceiving a child. Conventional therapies such as medication and surgery can help your dreams of parenthood come true. Our partnership with Mayo Clinic allows us to provide referrals when advanced reproductive techniques are required. <h5><strong>Integrative Medicine</strong></h5> Alternative therapies stemming from the premise the mind, body and spirit function as one and addressing all promotes healing. <h5><strong>Chemical Dependency Treatment</strong></h5> Care for people with alcohol and other drug addictions. All treatment plans are individualized, ensuring that each person\'s unique needs are met to promote healing and recovery. Dignity and respect are foundational values that guide our work. <h5><strong>Physical Therapy</strong></h5> Therapeutic care to restore movement and function to people disabled by disease or injury. Care may include exercise, training in activities of daily living and education. Our team works in collaboration with other specialties to offer comprehensive care and quickly restore you to your optimal health.[/vc_column_text][vc_empty_space height="16px"][/vc_column_inner][vc_column_inner width="1/3"][plethora_teaserbox title="Gynaecology Department" subtitle="(+555) 959-595-959" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2Fcosmetic-surgery%2F||" boxed_styling="boxed" media_type="icon" text_colorset="skincolored_section" button="1" button_text="Visit Department" button_style="btn-secondary" icon="wmi icon-i-womens-health"][/plethora_teaserbox][plethora_profilegrid columns="1" color_set="skincolored_section" excerpt="0" profiles="175"][/vc_column_inner][vc_column_inner][vc_empty_space][/vc_column_inner][/vc_row_inner][/vc_tta_section][/vc_tta_accordion][vc_empty_space][plethora_headinggroup subtitle="These are our special exams" align="text-left"] <h3>Featured Services</h3> [/plethora_headinggroup][/vc_column][vc_column width="1/2" offset="vc_col-md-3"][plethora_teaserbox title="Free Checkup" subtitle="The basis of Wellness" boxed_styling="boxed_special" image="193" media_colorset="transparent" text_colorset="transparent" button="1" button_style="btn-primary" media_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthx%2Fservices%2F||"][/plethora_teaserbox][/vc_column][vc_column width="1/2" offset="vc_col-md-3"][plethora_teaserbox title="DNA Testing" subtitle="Accurate Results" boxed_styling="boxed_special" image="195" media_colorset="transparent" text_colorset="transparent" button="1" button_style="btn-primary" button_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthx%2Fservices%2F||"][/plethora_teaserbox][/vc_column][vc_column width="1/2" offset="vc_col-md-3"][plethora_teaserbox title="Cardio Exam" subtitle="With High-End Equipment" boxed_styling="boxed_special" image="196" media_colorset="transparent" text_colorset="transparent" button="1" button_style="btn-primary" button_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthx%2Fservices%2F||"][/plethora_teaserbox][/vc_column][vc_column width="1/2" offset="vc_col-md-3"][plethora_teaserbox title="DNA Testing" subtitle="Accurate Results" boxed_styling="boxed_special" image="195" media_colorset="transparent" text_colorset="transparent" button="1" button_style="btn-primary" button_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthx%2Fservices%2F||"][/plethora_teaserbox][/vc_column][/vc_row][vc_row color_set="light_section" full_width="0" full_height="0" sep_top="separator_top sep_angled_positive_top" sep_bottom="separator_bottom sep_angled_positive_bottom" particles="0"][vc_column][plethora_headinggroup subtitle="Go ahead and use the cool Image Compare shortcode" align="text-left"] <h2>We specialize in Rhinoplasty</h2> [/plethora_headinggroup][/vc_column][vc_column offset="vc_col-md-6"][vc_column_text]Come and meet one of our four world renowned facial plastic and reconstructive surgeons, that can guide you through the process that will change your life. Reshape the appearance of body parts through cosmetic surgery. Our surgeons are specialized in reconstructing face, neck, ears, nose, eyes, breasts. We can also guide you through some our patients\' photos and their life changing stories.[/vc_column_text][vc_text_separator title="Integrative Medicine" title_align="separator_align_left"][vc_column_text]Alternative therapies stemming from the premise the mind, body and spirit function as one and addressing all promotes healing.[/vc_column_text][plethora_button button_text="Request an evaluation Meeting" button_style="btn-primary" button_with_icon="0"][/vc_column][vc_column offset="vc_col-md-6"][plethora_imagecompare before_image="1494" after_image="1495" default_offset="0.6"][vc_column_text css=".vc_custom_1448270905686{margin-top: 12px !important;}"]<small>Use the slider to compare the before and after photos</small>[/vc_column_text][/vc_column][/vc_row][vc_row color_set="secondary_section" full_width="0" full_height="0" transparent_overlay="transparent_film" background="bgimage" bgimage="1238" parallax="0"][vc_column][plethora_headinggroup subtitle="Call us at <b> (+555) 959-595-959</b> or fill in the appointment form..." align="text-center"] <h2>Want to schedule an appointment?</h2> [/plethora_headinggroup][plethora_button button_text="Appointment Form" button_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fappointment-booking%2F||" button_align="text-center" button_style="btn-success" button_with_icon="with-icon" button_icon="wmi icon-i-registration"][/vc_column][/vc_row]';
      vc_add_default_templates( $data );

      /** Departments Page Template */
      $data                 = array();
      $data['weight']       = 3;
      $data['name']         = esc_html__( '4. Departments Page', 'healthflex' );
      $data['image_path']   = preg_replace( '/\s/', '%20', get_template_directory_uri() . '/assets/images/visualcomposer/departments.jpg' ); 
      $data['custom_class'] = 'custom_template_for_vc_custom_template';
      $data['content']      = '[vc_row full_width="0" full_height="0" row_padding="no_padding" cols_valign="vbottom" particles="0"][vc_column width="1/2" color_set="secondary_section" background="bgimage" bgimage="1568" transparent_overlay="transparent_film" boxed="boxed_plus" same_height_col="same_height_col" offset="vc_hidden-xs"][/vc_column][vc_column width="1/2" boxed="boxed_plus" same_height_col="same_height_col"][vc_empty_space height="128px"][plethora_headinggroup subtitle="Specialized in Rhinoplasty" css=".vc_custom_1447353620312{margin-bottom: 16px !important;}"] <h3>Cosmetic Surgery Department</h3> [/plethora_headinggroup][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][plethora_button button_text="Visit Department" button_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2Fcosmetic-surgery%2F||" button_style="btn-secondary" button_with_icon="0"][vc_empty_space height="64px"][/vc_column][/vc_row][vc_row color_set="skincolored_section" full_width="0" full_height="0" row_padding="no_padding" cols_padding="no_cols_padding" particles="0" el_class="folded_section"][vc_column width="1/4" color_set="skincolored_section" background="transparent" boxed="boxed" same_height_col="same_height_col" offset="vc_col-xs-6" css=".vc_custom_1448045388925{background-color: #088eff !important;}"][vc_single_image image="1266" img_size="80x80" alignment="center" style="vc_box_circle" el_class="" css=".vc_custom_1448537500207{margin-bottom: 20px !important;}"][plethora_teaserbox title="Head of Department" subtitle="Dr. Ema Stankovic" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthx%2Fdepartments%2Fcosmetic-surgery%2F||" media_colorset="transparent" text_colorset="transparent" button="0"][/plethora_teaserbox][/vc_column][vc_column width="1/4" color_set="skincolored_section" background="transparent" boxed="boxed" same_height_col="same_height_col" offset="vc_col-xs-6" css=".vc_custom_1448096043985{background-color: #269cff !important;}"][plethora_teaserbox title="Cosmetic Surgery Dep." subtitle="Hall C, floor 4" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2Fcosmetic-surgery%2F||" media_type="icon" button="0" icon="fa fa-stethoscope"][/plethora_teaserbox][/vc_column][vc_column width="1/4" color_set="skincolored_section" boxed="boxed" same_height_col="same_height_col" offset="vc_col-xs-6" css=".vc_custom_1448045422740{background-color: #45aaff !important;}"][plethora_teaserbox title="Free Evaluation" subtitle="A meeting to discuss your case" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2Fcosmetic-surgery%2F||" media_type="icon" button="0" icon="wmi icon-i-family-practice"][/plethora_teaserbox][/vc_column][vc_column width="1/4" color_set="skincolored_section" background="transparent" boxed="boxed" same_height_col="same_height_col" offset="vc_col-xs-6" css=".vc_custom_1448045433542{background-color: #64b8ff !important;}"][plethora_teaserbox title="Direct Contact" subtitle="9am - 5pm Helpdesk" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2Fcosmetic-surgery%2F||" media_type="icon" button="0" icon="fa fa-phone"][/plethora_teaserbox][/vc_column][/vc_row][vc_row full_width="0" full_height="0" row_padding="no_padding" particles="0"][vc_column width="1/2" color_set="secondary_section" background="bgimage" bgimage="447" transparent_overlay="transparent_film" boxed="boxed_plus" same_height_col="same_height_col" offset="vc_hidden-xs"][/vc_column][vc_column width="1/2" boxed="boxed_plus" same_height_col="same_height_col"][vc_empty_space height="128px"][plethora_headinggroup subtitle="High-end equipment at your service" css=".vc_custom_1447351116557{margin-bottom: 16px !important;}"] <h3>Microbiology Lab</h3> [/plethora_headinggroup][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][plethora_button button_text="Visit Department" button_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2Fcosmetic-surgery%2F||" button_style="btn-secondary" button_with_icon="0"][vc_empty_space height="64px"][/vc_column][/vc_row][vc_row color_set="skincolored_section" full_width="0" full_height="0" row_padding="no_padding" cols_padding="no_cols_padding" particles="0" el_class="folded_section"][vc_column width="1/4" color_set="skincolored_section" background="transparent" offset="vc_col-xs-6" css=".vc_custom_1446619389066{background-color: #088eff !important;}"][plethora_teaserbox title="Departments" subtitle="The Backbone of our Clinic" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2F||" boxed_styling="boxed" image="1160" media_colorset="transparent" text_colorset="transparent" button="1" button_style="btn-secondary" same_height="same_height_col"][/plethora_teaserbox][/vc_column][vc_column width="1/4" color_set="skincolored_section" background="transparent" offset="vc_col-xs-6" css=".vc_custom_1447875610924{background-color: #269cff !important;}"][plethora_teaserbox title="Medical Services" subtitle="A list of all available" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fservices%2F||" boxed_styling="boxed" image="1163" media_colorset="transparent" text_colorset="transparent" button="1" button_style="btn-secondary" same_height="same_height_col"][/plethora_teaserbox][/vc_column][vc_column width="1/4" color_set="skincolored_section" offset="vc_col-xs-6" css=".vc_custom_1447875785935{background-color: #45aaff !important;}"][plethora_teaserbox title="Find a doctor" subtitle="All our staff by department" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Four-staff%2F||" boxed_styling="boxed" image="1175" media_colorset="transparent" text_colorset="transparent" button="1" button_style="btn-secondary" same_height="same_height_col"][/plethora_teaserbox][/vc_column][vc_column width="1/4" color_set="skincolored_section" background="transparent" offset="vc_col-xs-6" css=".vc_custom_1447875797423{background-color: #64b8ff !important;}"][plethora_teaserbox title="Request an appointment" subtitle="Call us or fill in a form" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fappointment-booking%2F||" boxed_styling="boxed" image="1171" media_colorset="transparent" text_colorset="transparent" button="1" button_style="btn-secondary" same_height="same_height_col"][/plethora_teaserbox][/vc_column][/vc_row][vc_row full_width="0" full_height="0" row_padding="no_padding" particles="0"][vc_column width="1/2" color_set="dark_section" background="bgimage" bgimage="1220" transparent_overlay="transparent_film" boxed="boxed_plus" same_height_col="same_height_col" offset="vc_hidden-xs"][/vc_column][vc_column width="1/2" boxed="boxed_plus" same_height_col="same_height_col"][vc_empty_space height="128px"][plethora_headinggroup subtitle="Specialized alternative care" css=".vc_custom_1447353980954{margin-bottom: 16px !important;}"] <h3>Gynaecology &amp; Birth</h3> [/plethora_headinggroup][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][plethora_button button_text="Visit Department" button_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2Fcosmetic-surgery%2F||" button_style="btn-secondary" button_with_icon="0"][vc_empty_space height="64px"][/vc_column][/vc_row]';
      vc_add_default_templates( $data );

      /** Our Staff Page Template */
      $data                 = array();
      $data['weight']       = 4;
      $data['name']         = esc_html__( '5. Our Staff Page', 'healthflex' );
      $data['image_path']   = preg_replace( '/\s/', '%20', get_template_directory_uri() . '/assets/images/visualcomposer/staff.jpg' ); 
      $data['custom_class'] = 'custom_template_for_vc_custom_template';
      $data['content']      = '[vc_row full_width="0" full_height="0" particles="0"][vc_column][vc_empty_space][vc_text_separator title="Cardiology Department"][plethora_profilegrid color_set="white_section" excerpt="0" profiles="1285,1279,1265,182"][vc_empty_space][plethora_button button_text="Visit Department" button_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2Fcosmetic-surgery%2F||" button_align="text-center" button_style="btn-primary" button_with_icon="0"][/vc_column][/vc_row][vc_row color_set="light_section" full_width="0" full_height="0" sep_top="separator_top sep_angled_positive_top" particles="0"][vc_column][vc_text_separator title="Pediatric Department"][plethora_profilegrid color_set="white_section" excerpt="0" profiles="1297,1288,184,175"][vc_empty_space][plethora_button button_text="Visit Department" button_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2Fcosmetic-surgery%2F||" button_align="text-center" button_style="btn-primary" button_with_icon="0"][/vc_column][/vc_row][vc_row full_width="0" full_height="0" sep_top="separator_top sep_angled_positive_top" sep_bottom="separator_bottom sep_angled_positive_bottom" particles="0"][vc_column][vc_text_separator title="Neurology Department"][plethora_profilegrid color_set="white_section" excerpt="0" profiles="1293,1291,1271,178"][vc_empty_space][plethora_button button_text="Visit Department" button_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fdepartments%2Fcosmetic-surgery%2F||" button_align="text-center" button_style="btn-primary" button_with_icon="0"][/vc_column][/vc_row][vc_row color_set="skincolored_section" align="text-center" full_width="0" full_height="0" cols_valign="vcenter" transparent_overlay="transparent_film" background="bgimage" bgimage="292" parallax="0"][vc_column][plethora_headinggroup subtitle="Checkout our A to Z Health Library!" align="text-center"] <h2 style="text-align: center;">CONFUSED ABOUT A MEDICAL TERM?</h2> [/plethora_headinggroup][plethora_button button_text="Visit our Library!" button_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fhealth-library%2F||" button_align="text-center" button_style="btn-success" button_with_icon="with-icon" button_icon="fa fa-check"][/vc_column][/vc_row]';
      vc_add_default_templates( $data );

      /** The Patient's & Visitor's Guide Page Template */
      $data                 = array();
      $data['weight']       = 5;
      $data['name']         = esc_html__( '6. The Patient\'s & Visitor\'s Guide', 'healthflex' );
      $data['image_path']   = preg_replace( '/\s/', '%20', get_template_directory_uri() . '/assets/images/visualcomposer/patients_guide.jpg' ); 
      $data['custom_class'] = 'custom_template_for_vc_custom_template';
      $data['content']      = '[vc_row color_set="secondary_section" full_width="0" full_height="0" transparent_overlay="transparent_film" background="bgimage" bgimage="516" parallax="0"][vc_column][plethora_headinggroup subtitle="Useful Information regarding your visit to our clinic!" align="text-center"] <h2>Patient &amp; Visitor Guide</h2> [/plethora_headinggroup][vc_row_inner el_class="folded_section"][vc_column_inner width="1/2" css=".vc_custom_1448964466367{background-color: #088eff !important;}"][plethora_teaserbox title="Find us on the Map" subtitle="PLUS useful contact Info" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fcontact%2F||" boxed_styling="boxed" media_type="icon" media_colorset="transparent" text_colorset="transparent" button="1" button_style="btn-secondary" same_height="same_height_col" icon="fa fa-street-view"][/plethora_teaserbox][/vc_column_inner][vc_column_inner width="1/2" css=".vc_custom_1448964485854{background-color: #64b8ff !important;}"][plethora_teaserbox title="FAQ" subtitle="Frequently Asked Questions" teaser_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Ffaq%2F||" boxed_styling="boxed" media_type="icon" media_colorset="transparent" text_colorset="transparent" button="1" button_style="btn-secondary" same_height="same_height_col" icon="fa fa-question-circle"][/plethora_teaserbox][/vc_column_inner][/vc_row_inner][vc_row_inner el_class="folded_section"][vc_column_inner width="1/4" color_set="dark_section"][plethora_teaserbox title="Patient Forms" teaser_link="url:%23pdf||" boxed_styling="boxed" media_type="icon" button="1" button_text="Download Forms" button_style="btn-primary" icon="wmi icon-i-administration"]Forms to download and completed prior to your appointment.[/plethora_teaserbox][/vc_column_inner][vc_column_inner width="1/4" color_set="secondary_section"][plethora_teaserbox title="Insurance Info" teaser_link="url:%23pdf||" boxed_styling="boxed" media_type="icon" button="1" button_text="Get Info" button_style="btn-primary" icon="wmi icon-i-medical-records"]Medicus Clinic insurance staff is available to answer your questions.[/plethora_teaserbox][/vc_column_inner][vc_column_inner width="1/4" color_set="skincolored_section"][plethora_teaserbox title="Prescription Delivery" teaser_link="url:%23pdf||" boxed_styling="boxed" media_type="icon" button="1" button_style="btn-secondary" icon="wmi icon-i-ambulance"]Arrange your prescription medicine to be sent at your home.[/plethora_teaserbox][/vc_column_inner][vc_column_inner width="1/4" color_set="black_section"][plethora_teaserbox title="Pay your Bill" teaser_link="url:%23pdf||" boxed_styling="boxed" media_type="icon" button="1" button_style="btn-primary" icon="wmi icon-i-billing"]Info about billing and how to pay your bills at our clinic hospital.[/plethora_teaserbox][/vc_column_inner][/vc_row_inner][/vc_column][/vc_row][vc_row color_set="secondary_section" full_width="0" full_height="0" row_padding="no_padding" sep_top="separator_top sep_angled_positive_top" sep_bottom="separator_bottom sep_angled_positive_bottom" particles="0"][vc_column width="1/3"][plethora_teaserbox title="Parking Spaces" subtitle="You can park your car just outside" boxed_styling="boxed" media_type="icon" button="0" icon="fa fa-car"][/plethora_teaserbox][/vc_column][vc_column width="1/3"][plethora_teaserbox title="Bus Routes" subtitle="Bus nr.23 leaves you just in front" boxed_styling="boxed" media_type="icon" button="0" icon="fa fa-bus"][/plethora_teaserbox][/vc_column][vc_column width="1/3"][plethora_teaserbox title="Accesibility" subtitle="We use ramps for easier access." boxed_styling="boxed" media_type="icon" button="0" icon="wmi icon-i-accessibility"][/plethora_teaserbox][/vc_column][/vc_row][vc_row color_set="secondary_section" full_width="0" full_height="0" transparent_overlay="transparent_film" background="bgimage" bgimage="1238" parallax="1"][vc_column][plethora_headinggroup subtitle="Call us at <b>(+555) 959-595-959</b> or fill in the appointment form..." align="text-center"] <h2>Want to schedule an appointment?</h2> [/plethora_headinggroup][plethora_button button_text="Appointment Form" button_link="url:http%3A%2F%2Fplethorathemes.com%2Fhealthflex%2Fappointment-booking%2F||" button_align="text-center" button_style="btn-success" button_with_icon="with-icon" button_icon="wmi icon-i-registration"][/vc_column][/vc_row]';
      vc_add_default_templates( $data );

      /** Contact Page Template */
      $data                 = array();
      $data['weight']       = 6;
      $data['name']         = esc_html__( '7. Contact Page', 'healthflex' );
      $data['image_path']   = preg_replace( '/\s/', '%20', get_template_directory_uri() . '/assets/images/visualcomposer/contact.jpg' ); 
      $data['custom_class'] = 'custom_template_for_vc_custom_template';
      $data['content']      = '[vc_row full_width="0" full_height="0" particles="0"][vc_column width="1/2" heading_align="text-center" margin="margin_bottom_grid"][contact-form-7 id="5" title="QUICK CONTACT"][/vc_column][vc_column width="1/2" heading_align="text-center"][vc_text_separator title="Contact Info &amp; Details" title_align="separator_align_left"][vc_column_text] <div class="wpb_text_column wpb_content_element "> <div class="wpb_wrapper"> Premium WordPress Theme mainly Medical Oriented but so flexible that lets you build various layouts for any Health &amp; Beauty related business! <strong>Working hours:</strong> 9am - 5pm on weekdays <strong>(+30) 210 1234567</strong> <strong>info@plethomedicalclinic.com</strong> <strong>79 Folsom Ave, San Francisco, CA 94107</strong> <small><a href="https://www.google.com/maps/dir//79+Folsom+St,+San+Francisco,+CA+94105/@37.79026,-122.3929651,17z/data=!4m13!1m4!3m3!1s0x8085807aad0a9e0b:0x378e593dff7a2ac3!2s79+Folsom+St,+San+Francisco,+CA+94105!3b1!4m7!1m0!1m5!1m1!1s0x8085807aad0a9e0b:0x378e593dff7a2ac3!2m2!1d-122.3907764!2d37.79026?hl=en">get directions</a></small> </div> </div> [/vc_column_text][vc_text_separator title="Appointment Request" title_align="separator_align_left"][vc_column_text]If you wish to book an appointment with a doctor, it is best that you visit the <strong><a href="#">Appointment Booking Page</a></strong> directly.[/vc_column_text][/vc_column][/vc_row][vc_row color_set="secondary_section" full_width="0" full_height="0" row_padding="no_padding" sep_top="separator_top sep_angled_positive_top" sep_bottom="separator_bottom sep_angled_positive_bottom" particles="0"][vc_column width="1/3"][plethora_teaserbox title="Parking Spaces" subtitle="You can park your car just outside" boxed_styling="boxed" media_type="icon" button="0" icon="fa fa-car"][/plethora_teaserbox][/vc_column][vc_column width="1/3"][plethora_teaserbox title="Bus Routes" subtitle="Bus nr.23 leaves you just in front" boxed_styling="boxed" media_type="icon" button="0" icon="fa fa-bus"][/plethora_teaserbox][/vc_column][vc_column width="1/3"][plethora_teaserbox title="Accesibility" subtitle="We use ramps for easier access." boxed_styling="boxed" media_type="icon" button="0" icon="wmi icon-i-accessibility"][/plethora_teaserbox][/vc_column][/vc_row][vc_row color_set="skincolored_section" align="text-center" full_width="0" full_height="0" transparent_overlay="transparent_film" background="bgimage" bgimage="1545" parallax="0"][vc_column][plethora_headinggroup subtitle="subscribe to our awesome <strong>mailchimp.com</strong> list" align="text-center"] <h2 style="text-align: center;">GRAB OUR <strong>HEALTH</strong> NEWSLETTER</h2> [/plethora_headinggroup][plethora_newsletterform][/vc_column][/vc_row]';
      $data['content']      = '[vc_row full_width="0" full_height="0" row_padding="no_padding" particles="0"][vc_column width="1/2" heading_align="text-center" color_set="secondary_section" boxed="boxed_plus" margin="margin_bottom_grid"][vc_empty_space height="64px"][contact-form-7 id="5" title="QUICK CONTACT"][vc_empty_space][/vc_column][vc_column width="1/2" heading_align="text-center" boxed="boxed_plus"][vc_empty_space height="64px"][vc_single_image image="1162" img_size="" el_class=""][vc_text_separator title="Contact Info &amp; Details" title_align="separator_align_left"][vc_column_text] <div class="wpb_text_column wpb_content_element "> <div class="wpb_wrapper"> Premium WordPress Theme mainly Medical Oriented but so flexible that lets you build various layouts for any “Health &amp; Beauty” related business! <strong>Working hours:</strong> 9am - 5pm on weekdays <strong>(+30) 210 1234567</strong> <strong>info@plethomedicalclinic.com</strong> <strong>79 Folsom Ave, San Francisco, CA 94107</strong> <small><a href="https://www.google.com/maps/dir//79+Folsom+St,+San+Francisco,+CA+94105/@37.79026,-122.3929651,17z/data=!4m13!1m4!3m3!1s0x8085807aad0a9e0b:0x378e593dff7a2ac3!2s79+Folsom+St,+San+Francisco,+CA+94105!3b1!4m7!1m0!1m5!1m1!1s0x8085807aad0a9e0b:0x378e593dff7a2ac3!2m2!1d-122.3907764!2d37.79026?hl=en">get directions</a></small> </div> </div> [/vc_column_text][vc_text_separator title="Appointment Request" title_align="separator_align_left"][vc_column_text]If you wish to book an appointment with a doctor, it is best that you visit the <strong><a href="http://plethorathemes.com/healthflex/appointment-booking/">Appointment Booking Page</a></strong> directly.[/vc_column_text][/vc_column][/vc_row][vc_row color_set="secondary_section" full_width="0" full_height="0" row_padding="no_padding" sep_top="separator_top sep_angled_positive_top" sep_bottom="separator_bottom sep_angled_positive_bottom" particles="0"][vc_column width="1/3"][plethora_teaserbox title="Parking Spaces" subtitle="You can park your car just outside" boxed_styling="boxed" media_type="icon" button="0" icon="fa fa-car"][/plethora_teaserbox][/vc_column][vc_column width="1/3"][plethora_teaserbox title="Bus Routes" subtitle="Bus nr.23 leaves you just in front" boxed_styling="boxed" media_type="icon" button="0" icon="fa fa-bus"][/plethora_teaserbox][/vc_column][vc_column width="1/3"][plethora_teaserbox title="Accesibility" subtitle="We use ramps for easier access." boxed_styling="boxed" media_type="icon" button="0" icon="wmi icon-i-accessibility"][/plethora_teaserbox][/vc_column][/vc_row][vc_row color_set="secondary_section" align="text-center" full_width="0" full_height="0" transparent_overlay="transparent_film" background="bgimage" bgimage="1545" parallax="0"][vc_column][plethora_headinggroup subtitle="subscribe to our awesome <strong>mailchimp.com</strong> list" align="text-center"] <h2 style="text-align: center;">GRAB OUR <strong>HEALTH</strong> NEWSLETTER</h2> [/plethora_headinggroup][plethora_newsletterform][/vc_column][/vc_row]';
      vc_add_default_templates( $data );

    }

    /**
    * Fix Contact Form 7 default markup and styling
    * @since 1.0
    *
    */
    static function wpcf7_form_elements( $content ) {
      // global $wpcf7_contact_form;

      $content = preg_replace( "/wpcf7-text/", "wpcf7-form-control form-control", $content );
      $content = preg_replace( "/wpcf7-email/", "wpcf7-form-control form-control", $content );
      $content = preg_replace( "/form-controlarea/", "wpcf7-form-control form-control", $content );
      $content = preg_replace( "/wpcf7-submit/", "wpcf7-submit btn btn-primary", $content );
      return $content;  
    }
 
    /**
     * Fix oEmbed W3C validation issues
     *
     * @since 1.0
     *
     */
    static function soundcloud_oembed_filter( $return, $data, $url ) {

      $style = '';

      if ( strpos($return, 'frameborder="0"') !== FALSE ){

        $style .= 'border:none;';
        $return = str_replace('frameborder="0"', '', $return);

      } elseif ( strpos($return, 'frameborder="no"') !== FALSE ) {

        $style .= 'border:none;';
        $return = str_replace('frameborder="no"', '', $return);
      }

      if ( strpos($return, 'scrolling="no"') !== FALSE ){

        $style .= 'overflow:hidden;';
        $return = str_replace('scrolling="no"', 'style="'. esc_attr( $style ) .'"', $return);

      } else {
        
        $return = str_replace('<iframe', '<iframe style="'. esc_attr( $style ) .'"', $return);
      }

      return $return;
    }

    /**
     * Load theme configuration
     *
     * @since 1.0
     *
     */
    public function load_theme() {

    /*** BASIC CONFIGURATION >>> ***/
        add_action('after_setup_theme', array( 'Plethora_Theme', 'textdomain'));                 // TEXTDOMAIN
        // THEME SUPPORTS
        add_theme_support( 'post-thumbnails', array( 'post', 'page', 'profile', 'terminology' ) );         // ADD POST THUMBNAILS
        add_theme_support( 'title-tag' );                                                    // ADD POST THUMBNAILS
        add_theme_support( 'post-formats', array( 'image', 'video', 'audio', 'link' ) );     // POST FORMATS SUPPORT
        add_theme_support( 'automatic-feed-links' );                                         // AUTOMATIC FEED LINKS SUPPORT       
        add_action( 'admin_notices', array( $this, 'admin_notice_wpless'), 20 );                 // PRODUCE A NOTICE IF WP LESS IS NOT PRESENT

        if ( ! isset( $content_width ) ) {  $content_width = 960; }                                       // SET $content_width VARIABLE
    /*** <<< END BASIC CONFIGURATION ***/

    /*** SCRIPT REGISTRATION & ENQUEUES >>> ***/
        add_action( 'wp_enqueue_scripts', array( $this, 'register_assets'), 1 );             // Theme assets registration ( register early )
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets'), 30 );             // Declare ALL assets ( scripts & styles ) - always enqueue on 30
        // Add svg modal workaround using Plethora_Theme::svgloader_modal() method
        add_action('wp_enqueue_scripts', array('Plethora_Theme', 'svgloader_modal'), 999);  // Must trigger this on wp_enqueue_scripts with latest priority
    /*** <<< SCRIPT REGISTRATION & ENQUEUES ***/
    }

//////////////// BASIC CONFIGURATION  ------> START

    public function admin_notice_wpless() {

      if ( ! class_exists('Plethora_Module_Wpless_Ext') && !empty( $_GET['page'] ) && $_GET['page'] === THEME_OPTIONSPAGE ) {

        $message = esc_html__('Custom functionality ( post type, taxonomies, shortcodes, widgets ) and styling related theme options ( color sets, typography, etc. ) WILL NOT be enabled until you activate the', 'healthflex');
        $message .= ' <strong>';
        $message .= 'Plethora Features Library';
        $message .= ' </strong> plugin';
        echo '<div class="error"><p>'. $message .'</p></div>';  
      }     
    }
//////////////// BASIC CONFIGURATION  <------ END
//////////////// SCRIPT REGISTRATION & ENQUEUES ------> START
    /**
    * Register global assets files 
    */
    public function register_assets(){ 

      // If production mode is on, then add to script files the .min suffix
      // $min_suffix = Plethora_Theme::is_developermode() ? '.min' : '';
      $min_suffix = '.min'; 

      # ASSET REGISTRATIONS
        // Register SCRIPTS used only in this theme ( remember...cross-theme scripts have been already registered )
        wp_register_script( 'boostrap', PLE_THEME_ASSETS_URI . '/js/libs/bootstrap'. $min_suffix .'.js',   array( 'jquery' ),  '', TRUE ); 
        wp_register_script( ASSETS_PREFIX . '-particles', PLE_THEME_ASSETS_URI . '/js/libs/particlesjs/particles' . $min_suffix . '.js',   array(),  '', TRUE ); 
        wp_register_script( ASSETS_PREFIX . '-init', PLE_THEME_ASSETS_URI . '/js/theme.js',   array(),  '', TRUE ); 
        // Register STYLES used only in this theme ( remember...cross-theme styles have been already registered )
        wp_register_style( ASSETS_PREFIX .'-custom-bootstrap',  PLE_THEME_ASSETS_URI . '/css/theme_custom_bootstrap.css'); 
        if ( class_exists( 'Plethora_Module_Wpless_Ext' ) ) {

          wp_register_style( ASSETS_PREFIX .'-style', get_stylesheet_uri(), array( ASSETS_PREFIX .'-dynamic-style' ) );  // LESS dynamic style.css

        } else {

          wp_register_style( ASSETS_PREFIX .'-default-style', PLE_THEME_ASSETS_URI.'/css/default_stylesheet.css', array( ASSETS_PREFIX .'-custom-bootstrap' ) ); // Default static style.css
        }
    }

    /**
    * Enqueue global assets files
    */
    public function enqueue_assets(){ 

      // If production mode is on, then add to script files the .min suffix
      // $min_suffix = Plethora_Theme::is_developermode() ? '.min' : '';
      $min_suffix = '.min'; // PLETODO: Kostas should check the .min suffix functionality sometime before pack

      # ASSET ENQUEUES
        // Enqueue SCRIPTS
        wp_enqueue_script( ASSETS_PREFIX . '-modernizr' );        
        wp_enqueue_script( 'boostrap' );        
        wp_enqueue_script( 'easing' );
        wp_enqueue_script( 'wow-animation-lib' );
        wp_enqueue_script( 'conformity' );
        wp_enqueue_script( ASSETS_PREFIX . '-particles' );
        wp_enqueue_script( 'parallax' );
        wp_enqueue_script( ASSETS_PREFIX . '-init' );
        // Enqueue STYLES
        wp_enqueue_style( 'animate');          // Animation library
        wp_enqueue_style( ASSETS_PREFIX .'-custom-bootstrap'); // Custom Bootstrap Base
        if ( class_exists( 'Plethora_Module_Wpless_Ext' ) ) {

          wp_enqueue_style( ASSETS_PREFIX .'-style');            // LESS dynamic style.css

        } else {

          wp_enqueue_style( ASSETS_PREFIX .'-default-style' );            // Default static style.css
        }

      # WP AJAX COMMENTS ( ajax handler for threaded comments...suggested by WP )
        $thread_comments = get_option('thread_comments');
        if ( is_singular() && comments_open() && $thread_comments ) { 

          wp_enqueue_script( 'comment-reply' ); 
        } 
    }
//////////////// SCRIPT REGISTRATION & ENQUEUES <------ END

//////////////// THIRD PARTY CONFIGURATION METHODS ----> START
    /** 
     * Will check if SVGLOADER script is about to load. If so, it will add the desired markup method
     */
    public static function svgloader_modal() {

      if ( wp_script_is( 'svgloader' ) ) {

        add_filter('plethora_wrapper_main_open', array( 'Plethora_Theme', 'addSVGloaderModal' ) );
      }
    }

    // ADD SVG LOADER REQUiRED MARKUP FOR THE AJAX LOADING ONLY WHEN SVGLOADER IS ENQUEUED FOR LOADING
    public static function addSVGloaderModal( $wrapper_main_open ) {

      $wrapper_main_open = 
      '<span class="progress_ball"><i class="fa fa-refresh"></i></span>

      <div class="loader-modal"></div>
       <div id="loader" data-opening="m -5,-5 0,70 90,0 0,-70 z m 5,35 c 0,0 15,20 40,0 25,-20 40,0 40,0 l 0,0 C 80,30 65,10 40,30 15,50 0,30 0,30 z" class="pageload-overlay">
        <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewbox="0 0 80 60" preserveaspectratio="none">
          <path d="m -5,-5 0,70 90,0 0,-70 z m 5,5 c 0,0 7.9843788,0 40,0 35,0 40,0 40,0 l 0,60 c 0,0 -3.944487,0 -40,0 -30,0 -40,0 -40,0 z"></path>
        </svg>
      </div>' . $wrapper_main_open;

        return $wrapper_main_open;
    }
//////////////// THIRD PARTY CONFIGURATION METHODS <---- END
  }
}