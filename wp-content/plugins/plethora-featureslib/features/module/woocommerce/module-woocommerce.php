<?php

/**
 * Woocommerce functionality
 * 
 */
if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 

if ( !class_exists('Plethora_Module_Woocommerce') && class_exists('woocommerce') ) {

	class Plethora_Module_Woocommerce {
        
        public static $feature_title        = "WooCommerce Support Module";							// Feature display title  (string)
        public static $feature_description  = "Adds support for WooCommerce plugin to your theme";	// Feature display description (string)
        public static $theme_option_control = true;													// Will this feature be controlled in theme options panel ( boolean )
        public static $theme_option_default       = true;											// Default activation option status ( boolean )
        public static $theme_option_requires             = array();									// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
        public static $dynamic_construct       = true;												// Dynamic class construction ? ( boolean )
        public static $dynamic_method           = false;											// Additional method invocation ( string/boolean | method name or false )

		public function __construct() {

		// WooCommerce support
	        add_action( 'after_setup_theme', array( $this, 'support' ) );										// Primary WC support declaration
	        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 20);  // Style enqueing - keep priority to 20 to make sure that it will be loaded after Woo defaults
	        add_filter( 'plethora_supported_post_types', array( $this, 'add_product_to_supported_post_types'), 10, 2 ); // declare frontend support manually ( this is mandatory, since there is not Plethora_Posttype_Product class )
	        self::remove_hooks();																							// Remove hooks that will be replaced later       

		// Options & Metabox
			add_filter( 'plethora_themeoptions_content', array($this, 'archive_themeoptions'), 10);			// Theme Options // Archive
			add_filter( 'plethora_themeoptions_content', array($this, 'single_themeoptions'), 120);			// Theme Options // Single Post
			add_filter( 'plethora_metabox_add', array($this, 'single_metabox'));								// Metabox // Single Post	
		// Map VC settings for native WC shortocodes, in order to be operational in VC edit panel
			add_action( 'init',	array( $this, 'map_native_shortcodes_to_vc' ));		
	        add_filter( 'woocommerce_shortcode_products_query', array( $this, 'shortcode_products_orderby' ));	// Advanced Orderby settings for WC shortcodes
		// Wrappers
			add_action( 'plethora_wrapper_column_class', array( $this, 'wrapper_column_class'), 10 );			// Main Wrapper Start
		// Catalog controls ( before loop )
	        add_action( 'woocommerce_before_main_content', array( $this, 'catalog_breadcrumbs' ), 5);			// Catalog: Breadcrums
			add_filter( 'woocommerce_show_page_title', array( $this, 'catalog_title_display' ) );						// Catalog: Title display
			add_filter( 'woocommerce_page_title', array( $this, 'catalog_title' ) );						// Catalog: Title display
			add_action( 'woocommerce_archive_description', array( $this, 'catalog_categorydescription' ), 1);	// Catalog: Category description display
			add_action( 'woocommerce_before_shop_loop', array( $this, 'catalog_resultscount'), 1);				// Catalog: Results count display
			add_action( 'woocommerce_before_shop_loop', 	array( 'Plethora_Module_Woocommerce', 'catalog_orderby'), 1);				// Catalog: order by field
		// Catalog controls ( on loop )
	        add_filter( 'loop_shop_per_page', array( $this, 'catalog_perpage' ), 20);							// Loop: Products per page        
	        add_filter( 'loop_shop_columns', array( $this, 'catalog_columns' ));								// Loop: Columns 
			add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'catalog_rating' ), 1);		// Loop: Rating display
			add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'catalog_price' ), 1);			// Loop: Price display 
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'catalog_addtocart' ), 1);			// Loop: Add-to-cart display 
			add_action( 'woocommerce_before_shop_loop_item_title',array( $this, 'catalog_salesflash' ), 1);	// Loop: Sales flash icon display 
		// Single product controls 
	        add_action( 'woocommerce_before_main_content', array( $this, 'single_breadcrumbs' ), 5);			// Single: Breadcrums
	        add_action( 'woocommerce_before_single_product_summary',array( $this, 'single_salesflash' ), 1);	// Single: Sales flash icon display 
			add_action( 'woocommerce_single_product_summary', array( $this, 'single_title' ) , 1);				// Single: Title display
			add_action( 'woocommerce_single_product_summary', array( $this, 'single_rating' ), 1 );			// Single: Rating display
			add_action( 'woocommerce_single_product_summary', array( $this, 'single_price' ), 1 );				// Single: Price display
			add_action( 'woocommerce_single_product_summary', array( $this, 'single_addtocart' ), 1 );			// Single: add-to-cart display
			add_action( 'woocommerce_single_product_summary', array( $this, 'single_meta' ), 1 );				// Single: Meta display
			add_filter( 'woocommerce_product_tabs', array( $this, 'single_tab_description' ), 98 );			// Single: Description tab display
			add_filter( 'woocommerce_product_tabs', array( $this, 'single_tab_reviews' ), 98 );				// Single: Reviews tab display
			add_filter( 'woocommerce_product_tabs', array( $this, 'single_tab_attributes' ), 98 );				// Single: Additional info tab display
	        add_filter( 'woocommerce_related_products_args', array( $this, 'single_related' ), 10);			// Single: Related products config
	        add_filter( 'woocommerce_output_related_products_args', array( $this, 'single_related_config' ));	// Single: Related products status
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );		// Single: Upsell products ( remove default )
			add_action( 'woocommerce_after_single_product_summary', array( $this, 'single_upsell'), 15); 		// Single: Upsell products display ( "You May Also Like...")
		}

		// ok
		public function support() {

    		add_theme_support( 'woocommerce' );
		}

		public function add_product_to_supported_post_types( $supported, $args ) {

          // Add this only when the call asks for plethora_only post types
          if ( $args['plethora_only'] ) {

            $supported['product'] = $args['output'] === 'objects' ? get_post_type_object( 'product' ) : 'product' ;
          }

          return $supported;
		}

		public function remove_hooks() { 
			
			// Remove global wrappers and sidebar
			remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 ); 
			remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
			remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
			// Disable stylesheet enqueues 
			add_filter( 'woocommerce_enqueue_styles', '__return_false' );
		}

		public function enqueue() {

    		wp_register_style( 'plethora-woocommerce', PLE_THEME_ASSETS_URI . '/css/woocommerce.css');
            wp_enqueue_style( 'plethora-woocommerce' );
		}

		public static function wrapper_column_class( $classes ) {

			if ( is_product() || self::is_shop_catalog() ) {
			
				$classes[] = 'plethora-woo';
			}

			if ( self::is_shop_catalog() ) {

				$classes[] = 'plethora-woo-shop-grid-'. Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-columns', 4);
			
			} elseif ( is_product() ) {

				$classes[] = 'plethora-woo-related-grid-'. Plethora_Theme::option( METAOPTION_PREFIX .'product-related-columns', 4);
			}

			return $classes; 
		}

		public function archive_themeoptions( $sections ) {

			$page_for_shop	= get_option( 'woocommerce_shop_page_id', 0 );
			$desc_1 = esc_html__('These options affect ONLY shop catalog display.', 'plethora-framework');
			$desc_2 = esc_html__('These options affect ONLY shop catalog display...however it seems that you', 'plethora-framework'); 
			$desc_2 .= ' <span style="color:red">';
			$desc_2 .= esc_html__('have not set a shop page yet!', 'plethora-framework'); 
			$desc_2 .= '</span>';
			$desc_2 .= esc_html__('You can go for it under \'WooCommerce > Settings > Products > Display\'.', 'plethora-framework');
			$desc = $page_for_shop === 0 || empty($page_for_shop) ? $desc_2 :  $desc_1 ;
			$desc .= '<br>'. esc_html__('If you are using a speed optimization plugin, don\'t forget to <strong>clear cache</strong> after options update', 'plethora-framework');

		    $sections[] = array(
				'title'      => esc_html__('Shop', 'plethora-framework'),
				'heading'      => esc_html__('SHOP OPTIONS', 'plethora-framework'),
				'desc'      => $desc,
				'subsection' => true,
				'fields'     => array(
			            array(
			                'id'        =>  METAOPTION_PREFIX .'archiveproduct-layout',
			                'title'     => esc_html__( 'Catalog Layout', 'plethora-framework' ),
			                'default'   => 'right_sidebar',
			                'type'      => 'image_select',
							'options' => array( 
									'full'         => ReduxFramework::$_url . 'assets/img/1c.png',
									'right_sidebar'         => ReduxFramework::$_url . 'assets/img/2cr.png',
									'left_sidebar'         => ReduxFramework::$_url . 'assets/img/2cl.png',
				                )
			            ),
						array(
							'id'=> METAOPTION_PREFIX .'archiveproduct-sidebar',
							'required'=> array( METAOPTION_PREFIX .'archiveproduct-layout','!=', 'full' ),
							'type' => 'select',
							'data' => 'sidebars',
							'multi' => false,
							'title' => esc_html__('Catalog Sidebar', 'plethora-framework'), 
							'default'  => 'sidebar-shop',
						),
						array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-perpage',
							'type'        => 'slider',
							'title'       => esc_html__('Products Displayed Per Page', 'plethora-framework'), 
						    "default" => 12,
						    "min" => 4,
						    "step" => 4,
						    "max" => 240,
						    'display_value' => 'text'
							),
						array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-title',
							'type'    => 'switch', 
							'title'   => esc_html__('Display Title On Content', 'plethora-framework'),
							'desc'    => esc_html__('Will display title on content view', 'plethora-framework'),
							'default' => 0,
							),	
						array(
							'id'       => METAOPTION_PREFIX .'archiveproduct-title-text',
							'type'     => 'text',
							'title'    => esc_html__('Default Title', 'plethora-framework'), 
							'default'  => esc_html__('Shop Title', 'plethora-framework'),
							'translate' => true,
							),
						array(
							'id'       => METAOPTION_PREFIX .'archiveproduct-subtitle-text',
							'type'     => 'text',
							'title'    => esc_html__('Default Subtitle', 'plethora-framework'), 
							'desc'    => esc_html__('This is used ONLY as default subtitle for the headings section of the Media Panel', 'plethora-framework'), 
							'default'  => esc_html__('Shop subtitle here', 'plethora-framework'),
							'translate' => true,
							),
						array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-columns',
							'type'        => 'slider',
							'title'       => esc_html__('Products Grid Columns', 'plethora-framework'), 
						    "default" => 3,
						    "min" => 2,
						    "step" => 1,
						    "max" => 4,
						    'display_value' => 'text'
							),
						array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-categorydescription',
							'type'    => 'button_set',
							'title'   => esc_html__('Category Description', 'plethora-framework'),
							'desc'   => esc_html__('By default, category description ( if exists ) is displayed right after shop title.', 'plethora-framework'),
							"default" => 'hide',
							'options' => array(
									'display' => esc_html__('Display', 'plethora-framework'),
									'hide'   => esc_html__('Hide', 'plethora-framework'),
									),
							),
						array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-breadcrumbs',
							'type'    => 'button_set',
							'title'   => esc_html__('Breadcrumbs ( Catalog View )', 'plethora-framework'),
							"default" => 'hide',
							'options' => array(
									'display' => esc_html__('Display', 'plethora-framework'),
									'hide'   => esc_html__('Hide', 'plethora-framework'),
									),
							),
						array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-resultscount',
							'type'    => 'button_set',
							'title'   => esc_html__('Results Count Info', 'plethora-framework'),
							"default" => 'hide',
							'options' => array(
									'display' => esc_html__('Display', 'plethora-framework'),
									'hide'   => esc_html__('Hide', 'plethora-framework'),
									),
							),
						array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-orderby',
							'type'    => 'button_set',
							'title'   => esc_html__('Order Dropdown Field', 'plethora-framework'),
							"default" => 'hide',
							'options' => array(
									'display' => esc_html__('Display', 'plethora-framework'),
									'hide'   => esc_html__('Hide', 'plethora-framework'),
									),
							),
						array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-rating',
							'type'    => 'button_set',
							'title'   => esc_html__('Ratings ( Catalog View )', 'plethora-framework'),
							"default" => 'hide',
							'options' => array(
									'display' => esc_html__('Display', 'plethora-framework'),
									'hide'   => esc_html__('Hide', 'plethora-framework'),
									),
							),
						array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-price',
							'type'    => 'button_set',
							'title'   => esc_html__('Prices ( Catalog View )', 'plethora-framework'),
							"default" => 'display',
							'options' => array(
									'display' => esc_html__('Display', 'plethora-framework'),
									'hide'   => esc_html__('Hide', 'plethora-framework'),
									),
							),
						array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-addtocart',
							'type'    => 'button_set',
							'title'   => esc_html__('"Add To Cart" Button ( Catalog View )', 'plethora-framework'),
							"default" => 'display',
							'options' => array(
									'display' => esc_html__('Display', 'plethora-framework'),
									'hide'   => esc_html__('Hide', 'plethora-framework'),
									),
							),
						array(
							'id'      => METAOPTION_PREFIX .'archiveproduct-salesflash',
							'type'    => 'button_set',
							'title'   => esc_html__('"Sale!" Icon ( Catalog View )', 'plethora-framework'),
							"default" => 'display',
							'options' => array(
									'display' => esc_html__('Display', 'plethora-framework'),
									'hide'   => esc_html__('Hide', 'plethora-framework'),
									),
							),
		        )
		    );

			return $sections;
		}

		public function single_themeoptions( $sections ) {

			$page_for_shop	= get_option( 'woocommerce_shop_page_id', 0 );
			$desc_1 = __('It seems that you <span style="color:red">have not set a shop page yet!</span>. You can go for it under <strong>WooCommerce > Settings > Products > Display</strong>.<br>', 'plethora-framework');
			$desc = $page_for_shop === 0 || empty($page_for_shop) ? $desc_1 :  '' ;
            $desc .=  esc_html__('These will be the default values for a new post you create. You have the possibility to override most of these settings on each post separately.', 'plethora-framework') . '<br><span style="color:red;">'. esc_html__('Important: ', 'plethora-framework') . '</span>'. esc_html__('changing a default value here will not affect options that were customized per post. In example, if you change a previously default "full width" to "right sidebar" layout this will switch all full width posts to right sidebar ones. However it will not affect those that were customized, per post, to display a left sidebar.', 'plethora-framework');
	    	$sections[] = array(
				'title'      => esc_html__('Products', 'plethora-framework'),
                'heading' => esc_html__('SINGLE PRODUCT POSTS OPTIONS', 'plethora-framework'),
				'subsection' =>  true,
				'desc' =>  $desc,
				'fields'     => array(

		            array(
		                'id'        =>  METAOPTION_PREFIX .'product-layout',
		                'title'     => esc_html__( 'Product Post Layout', 'plethora-framework' ),
		                'default'   => 'right_sidebar',
		                'type'      => 'image_select',
		                'customizer'=> array(),
						'options' => array( 
								'full'          => ReduxFramework::$_url . 'assets/img/1c.png',
								'right_sidebar' => ReduxFramework::$_url . 'assets/img/2cr.png',
								'left_sidebar'  => ReduxFramework::$_url . 'assets/img/2cl.png',
			                )
		            ),
					array(
						'id'=> METAOPTION_PREFIX .'product-sidebar',
						'required'=> array( METAOPTION_PREFIX .'product-layout','!=', 'full' ),
						'type' => 'select',
						'data' => 'sidebars',
						'multi' => false,
						'title' => esc_html__('Product Post Sidebar', 'plethora-framework'), 
						'default'  => 'sidebar-shop',
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-wootitle',
						'type'    => 'switch', 
						'title'   => esc_html__('Display WooCommerce Title', 'plethora-framework'),
						'desc'   => esc_html__('Display the classic WooCommerce product title next to product image', 'plethora-framework'),
						'default'  => 1,
						'options' => array(
										1 => esc_html__('Display', 'plethora-framework'),
										0 => esc_html__('Hide', 'plethora-framework'),
									),
						),	
					array(
						'id'      => METAOPTION_PREFIX .'product-breadcrumbs',
						'type'    => 'button_set',
						'title'   => esc_html__('Breadcrumbs ( Product Page )', 'plethora-framework'),
						"default" => 'display',
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-rating',
						'type'    => 'button_set',
						'title'   => esc_html__('Ratings ( Product Page )', 'plethora-framework'),
						"default" => 'display',
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-price',
						'type'    => 'button_set',
						'title'   => esc_html__('Price  ( Product Page )', 'plethora-framework'),
						"default" => 'display',
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-addtocart',
						'type'    => 'button_set',
						'title'   => esc_html__('"Add To Cart" Button ( Product Page )', 'plethora-framework'),
						"default" => 'display',
						'options' => array(
								'display'		=> esc_html__('Display', 'plethora-framework'),
								'hide'	=> esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-meta',
						'type'    => 'button_set',
						'title'   => esc_html__('Product Categories', 'plethora-framework'),
						"default" => 'display',
						'options' => array(
								'display'		=> esc_html__('Display', 'plethora-framework'),
								'hide'	=> esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-sale',
						'type'    => 'button_set',
						'title'   => esc_html__('"Sale" Icon ( Product Page )', 'plethora-framework'),
						"default" => 'display',
						'options' => array(
								'display'		=> esc_html__('Display', 'plethora-framework'),
								'hide'	=> esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-tab-description',
						'type'    => 'button_set',
						'title'   => esc_html__('Description Tab', 'plethora-framework'),
						"default" => 'display',
						'options' => array(
								'display'		=> esc_html__('Display', 'plethora-framework'),
								'hide'	=> esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-tab-reviews',
						'type'    => 'button_set',
						'title'   => esc_html__('Reviews Tab', 'plethora-framework'),
						"default" => 'display',
						'options' => array(
								'display'		=> esc_html__('Display', 'plethora-framework'),
								'hide'	=> esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-tab-attributes',
						'type'    => 'button_set',
						'title'   => esc_html__('Additional Information Tab', 'plethora-framework'),
						'descr'   => esc_html__('Remember that this tab is NOT displayed by defaul if product has no attributes', 'plethora-framework'),
						"default" => 'display',
						'options' => array(
								'display'		=> esc_html__('Display', 'plethora-framework'),
								'hide'	=> esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-related',
						'type'    => 'button_set',
						'title'   => esc_html__('Related Products', 'plethora-framework'),
						"default" => 'display',
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-upsell',
						'type'    => 'button_set',
						'title'   => esc_html__('Upsell Products', 'plethora-framework'),
						"default" => 'display',
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
						),

					array(
						'id'            => METAOPTION_PREFIX .'product-related-number',
						'type'          => 'slider',
						'title'         => esc_html__('Related/Upsell Products Max Results', 'plethora-framework'), 
						"default"       => 3,
						"min"           => 2,
						"step"          => 1,
						"max"           => 36,
						'display_value' => 'text'
						),

					array(
						'id'      => METAOPTION_PREFIX .'product-related-columns',
						'type'        => 'slider',
						'title'       => esc_html__('Related/Upsell Products Columns', 'plethora-framework'), 
					    "default" => 3,
					    "min" => 2,
					    "step" => 1,
					    "max" => 4,
					    'display_value' => 'text'
						),
				),	
			);
			
			return $sections;
		}

		// ok
		public function single_metabox( $metaboxes ) {

	    	$sections_content = array(
		        'title' => esc_html__('Content', 'plethora-framework'),
                'heading' => esc_html__('CONTENT OPTIONS', 'plethora-framework'),
		        'icon_class'    => 'icon-large',
				'icon'       => 'el-icon-lines',
		        'fields'        => array(
		            array(
		                'id'        =>  METAOPTION_PREFIX .'product-layout',
		                'title'     => esc_html__( 'Product Post Layout', 'plethora-framework' ),
		                'type'      => 'image_select',
		                'customizer'=> array(),
						'options' => array( 
								'full'         => ReduxFramework::$_url . 'assets/img/1c.png',
								'right_sidebar'         => ReduxFramework::$_url . 'assets/img/2cr.png',
								'left_sidebar'         => ReduxFramework::$_url . 'assets/img/2cl.png',
			                )
		            ),
					array(
						'id'=> METAOPTION_PREFIX .'product-sidebar',
						'required'=> array( METAOPTION_PREFIX .'product-layout','!=', 'full' ),
						'type' => 'select',
						'data' => 'sidebars',
						'multi' => false,
						'title' => esc_html__('Product Post Sidebar', 'plethora-framework'), 
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-wootitle',
						'type'    => 'switch', 
						'title'   => esc_html__('Display WooCommerce Title', 'plethora-framework'),
						'desc'    => esc_html__('Display the classic WooCommerce product title next to product image', 'plethora-framework'),
						'options' => array(
										1 => esc_html__('Display', 'plethora-framework'),
										0 => esc_html__('Hide', 'plethora-framework'),
									),
						),	
					array(
						'id'       => METAOPTION_PREFIX .'product-subtitle-text',
						'type'     => 'text',
						'title'    => esc_html__('Subtitle', 'plethora-framework'), 
						'desc'    => esc_html__('This is used ONLY as default subtitle for the headings section of the Media Panel', 'plethora-framework'),
						'translate' => true,
						),

					array(
						'id'      => METAOPTION_PREFIX .'product-breadcrumbs',
						'type'    => 'button_set',
						'title'   => esc_html__('Breadcrumbs ( Product Page )', 'plethora-framework'),
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-rating',
						'type'    => 'button_set',
						'title'   => esc_html__('Ratings ( Product Page )', 'plethora-framework'),
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-price',
						'type'    => 'button_set',
						'title'   => esc_html__('Price  ( Product Page )', 'plethora-framework'),
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-addtocart',
						'type'    => 'button_set',
						'title'   => esc_html__('"Add To Cart" Button ( Product Page )', 'plethora-framework'),
						'options' => array(
								'display'		=> esc_html__('Display', 'plethora-framework'),
								'hide'	=> esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-meta',
						'type'    => 'button_set',
						'title'   => esc_html__('Product Categories', 'plethora-framework'),
						'options' => array(
								'display'		=> esc_html__('Display', 'plethora-framework'),
								'hide'	=> esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-sale',
						'type'    => 'button_set',
						'title'   => esc_html__('"Sale" Icon ( Product Page )', 'plethora-framework'),
						'options' => array(
								'display'		=> esc_html__('Display', 'plethora-framework'),
								'hide'	=> esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-tab-description',
						'type'    => 'button_set',
						'title'   => esc_html__('Description Tab', 'plethora-framework'),
						'options' => array(
								'display'		=> esc_html__('Display', 'plethora-framework'),
								'hide'	=> esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-tab-reviews',
						'type'    => 'button_set',
						'title'   => esc_html__('Reviews Tab', 'plethora-framework'),
						'options' => array(
								'display'		=> esc_html__('Display', 'plethora-framework'),
								'hide'	=> esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-tab-attributes',
						'type'    => 'button_set',
						'title'   => esc_html__('Additional Information Tab', 'plethora-framework'),
						'descr'   => esc_html__('Remember that this tab is NOT displayed by defaul if product has no attributes', 'plethora-framework'),
						'options' => array(
								'display'		=> esc_html__('Display', 'plethora-framework'),
								'hide'	=> esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-related',
						'type'    => 'button_set',
						'title'   => esc_html__('Related Products', 'plethora-framework'),
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
						),
					array(
						'id'      => METAOPTION_PREFIX .'product-upsell',
						'type'    => 'button_set',
						'title'   => esc_html__('Upsell Products', 'plethora-framework'),
						'options' => array(
								'display' => esc_html__('Display', 'plethora-framework'),
								'hide'   => esc_html__('Hide', 'plethora-framework'),
								),
						),

					array(
						'id'            => METAOPTION_PREFIX .'product-related-number',
						'type'          => 'slider',
						'title'         => esc_html__('Related/Upsell Products Max Results', 'plethora-framework'), 
						"min"           => 2,
						"step"          => 1,
						"max"           => 36,
						'display_value' => 'text'
						),

					array(
						'id'            => METAOPTION_PREFIX .'product-related-columns',
						'type'          => 'slider',
						'title'         => esc_html__('Related/Upsell Products Columns', 'plethora-framework'), 
						"min"           => 2,
						"step"          => 1,
						"max"           => 4,
						'display_value' => 'text'
						),
				)
	        );

			$sections = array();
			$sections[] = $sections_content;
			if ( has_filter( 'plethora_metabox_singleproduct') ) {

				$sections = apply_filters( 'plethora_metabox_singleproduct', $sections );
			}

		    $metaboxes[] = array(
		        'id'            => 'metabox-single-product',
		        'title'         => esc_html__( 'Product Options', 'plethora-framework' ),
		        'post_types'    => array( 'product' ),
		        'position'      => 'normal', // normal, advanced, side
		        'priority'      => 'default', // high, core, default, low
		        'sections'      => $sections,
		    );

	    	return $metaboxes;
		}

		public static function map_native_shortcodes_to_vc() {

			Plethora_Shortcode::vc_map( self::shortcode_map_recent_products() );
			Plethora_Shortcode::vc_map( self::shortcode_map_featured_products() );
			Plethora_Shortcode::vc_map( self::shortcode_map_product() );
			Plethora_Shortcode::vc_map( self::shortcode_map_products() );
			Plethora_Shortcode::vc_map( self::shortcode_map_add_to_cart() );
			Plethora_Shortcode::vc_map( self::shortcode_map_product_page() );
			Plethora_Shortcode::vc_map( self::shortcode_map_product_category() );
			Plethora_Shortcode::vc_map( self::shortcode_map_product_categories() );
			Plethora_Shortcode::vc_map( self::shortcode_map_sale_products() );
			Plethora_Shortcode::vc_map( self::shortcode_map_best_selling_products() );
			Plethora_Shortcode::vc_map( self::shortcode_map_top_rated_products() );
			Plethora_Shortcode::vc_map( self::shortcode_map_product_attribute() );
			Plethora_Shortcode::vc_map( self::shortcode_map_related_products() );
		}

 		public static function catalog_title_display() {

			$display = true;
			if ( self::is_shop_catalog() ) { 

				$title = Plethora_Theme::get_title( array( 'tag' => '', 'force_display' => false ) );

				if ( empty( $title ) ) { 

					$display = false; 
				}
			}
			return $display;
		}

		public static function catalog_title() {

			$title = Plethora_Theme::get_title( array( 'tag' => '', 'force_display' => false ) );
			return $title;
		}

		public static function catalog_categorydescription() {
			$category_display = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-categorydescription', 'display' );
			if ( $category_display == 'hide') { 
				remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
			}
		}

		public static function catalog_perpage() {
			$products_per_page = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-perpage', 12, 0, false);
			return $products_per_page;			
		}

		public static function catalog_columns() {

			$products_per_page = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-columns', 4, 0, false);
			return $products_per_page;			
		}

		public static function catalog_breadcrumbs() {

			if ( self::is_shop_catalog() ) {
				$breadcrumbs = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-breadcrumbs', 'display');
				if ( $breadcrumbs == 'hide') { 
					remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
				}
			}
		}

		public static function catalog_resultscount() { 

			$resultscount_display = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-resultscount', 'display' );
			if ( $resultscount_display == 'hide' ) { 
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
			}			
		}

		public static function catalog_orderby() {

			$orderby_display = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-orderby', 'display', 0, false);
			if ( $orderby_display == 'hide') { 
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
			}
		}

		public static function catalog_rating() { 

			$rating_display = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-rating', 'display' );
			if ( $rating_display == 'hide') { 
				remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
			}
		}

		public static function catalog_price() {

			$price_display = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-price', 'display' );
			if ( $price_display == 'hide') { 
				remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
			}
		}

		public static function catalog_addtocart() { 

			$cart_display = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-addtocart', 'display' );
			if ( $cart_display == 'hide' ) { 
    			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			}
		}

		public static function catalog_salesflash() {

			$salesflash_display = Plethora_Theme::option( METAOPTION_PREFIX .'archiveproduct-salesflash', 'display' );
			if ( $salesflash_display == 'hide' ) { 
				remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
			}			
		}

		public static function single_breadcrumbs() {

			if ( is_product() ) {
				$breadcrumbs = Plethora_Theme::option( METAOPTION_PREFIX .'product-breadcrumbs', 'display');
				if ( $breadcrumbs == 'hide') { 
					remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
				}

			}
		}

		public static function single_title() {

			$title_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-wootitle', 'display' );
			if ( ! $title_display ) { 
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
			}
		}

		public static function single_rating() {

			$rating_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-rating', 'display' );
			if ( $rating_display == 'hide') {
				remove_action( 'woocommerce_single_product_summary', 	 'woocommerce_template_single_rating', 10 );
			}
		}

		public static function single_salesflash() {

			$salesflash_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-sale', 'display' );
			if ( $salesflash_display == 'hide') {
				remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );		
			}
		}

		public static function single_price() {

			$price_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-price', 'display' );
			if ( $price_display == 'hide') {
				remove_action( 'woocommerce_single_product_summary', 	 'woocommerce_template_single_price', 10 );	
			}
		}

		public static function single_addtocart() { 

			$cart_status = Plethora_Theme::option( METAOPTION_PREFIX .'product-addtocart', 'display');
			if ( $cart_status == 'hide') { 
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			}
		}

		public static function single_meta() { 

			$meta_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-meta', 'display');
			if ( $meta_display == 'hide') { 
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
			}
		}

		public static function single_tab_description( $tabs ) {

			$tab_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-tab-description', 'display');
			if ( $tab_display == 'hide') { 
			    unset( $tabs['description'] );
		    }
		    return $tabs;
		}	        

		public static function single_tab_reviews( $tabs ) {
		  
			$tab_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-tab-reviews', 'display');
			if ( $tab_display == 'hide') { 
			    unset( $tabs['reviews'] );
		    }
		    return $tabs;
		}	        

		public static function single_tab_attributes( $tabs ) {
		  
			$tab_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-tab-attributes', 'display');
			if ( $tab_display == 'hide') { 
			    unset( $tabs['additional_information'] );
		    }
		    return $tabs;
		}	        

		public static function single_related( $args ) {

			$related = Plethora_Theme::option( METAOPTION_PREFIX .'product-related', 'display');
			if ($related == 'display') {
				return $args;
			} else { 
				return array();
			}
		} 

		public static function single_related_config( $args ) {

			$posts_per_page = Plethora_Theme::option( METAOPTION_PREFIX .'product-related-number', 4);
			$columns 		= Plethora_Theme::option( METAOPTION_PREFIX .'product-related-columns', 4);
			$args['posts_per_page'] = $posts_per_page; 
			$args['columns'] 		= $columns;
			return $args;
		}

		public static function single_upsell() {
			$upsell_display = Plethora_Theme::option( METAOPTION_PREFIX .'product-upsell', 'display');
			$upsell_results = Plethora_Theme::option( METAOPTION_PREFIX .'product-related-number', 4);
			$upsell_columns = Plethora_Theme::option( METAOPTION_PREFIX .'product-related-columns', 4);
			if ( $upsell_display == 'display' ) {
				woocommerce_upsell_display( $upsell_results, $upsell_columns ); 
			}
		}

		public static function shortcode_products_orderby( $args ){

			$standard_array = array('menu_order','title','date','rand','id');

			if( isset( $args['orderby'] ) && !in_array( $args['orderby'], $standard_array ) ) {
				$args['meta_key'] = $args['orderby'];
				$args['orderby']  = 'meta_value_num'; 
			}

			return $args;
		}

		public static function shortcode_map_globalfield_perpage(){

			$perpage_params = array(
                      "param_name"    => "per_page",
                      "type"          => "textfield",
                      "heading"       => esc_html__('Maximum results', 'plethora-framework'),
                      "value"         => '12',
                      "description"   => esc_html__("Set the maximum product results. Leave empty or set it to zero to display all results", 'plethora-framework')
					);
			return $perpage_params;
		}

		public static function shortcode_map_globalfield_column(){

			$column_params = array(
                      "param_name"    => "columns",
                      "type"          => "dropdown",
                      "heading"       => esc_html__("Grid columns", 'plethora-framework'),
                      "value"         => array('2' => '2', '3' => '3', '4' => '4'),
                  );
			return $column_params;
		}

		public static function shortcode_map_globalfield_orderby(){

			$orderby_params = array(
                  "param_name"    => "orderby",
                  "type"          => "dropdown",
                  "heading"       => esc_html__('Order by', 'plethora-framework'),
                  "value"         => array(
                  	esc_html__('Date', 'plethora-framework') =>'date', 
                  	esc_html__('Title', 'plethora-framework') =>'title', 
                  	esc_html__('ID', 'plethora-framework') =>'id', 
                  	esc_html__('Sales', 'plethora-framework') => 'total_sales', 
                  	esc_html__('Price', 'plethora-framework') => '_price', 
                  	esc_html__('Menu Order', 'plethora-framework') => 'menu_order', 
                  	esc_html__('Random', 'plethora-framework')  => 'rand'
                  ),
              );
			return $orderby_params;
		}

		public static function shortcode_map_globalfield_order() {

			$order_params = array(
                  "param_name"    => "order",
                  "type"          => "dropdown",
                  "heading"       => esc_html__('Order', 'plethora-framework'),
                  "value"         => array( esc_html__('Descending', 'plethora-framework') =>'DESC', esc_html__('Ascending', 'plethora-framework')  => 'ASC'),
              );
			return $order_params;
		}

		public static function shortcode_map_globalfield_productpicker() { 

			$args = array( 'post_type' => 'product', 'posts_per_page' => -1, 'orderby' => 'date' );
		    $loop = new WP_Query( $args );
		    $products_info = array();
		    while ( $loop->have_posts() ) : $loop->the_post();
		    	global $product; 
		    	$title = esc_html( get_the_title() );
		    	$id = get_the_id();
		    	$products_info[$title] = $id ;
		    endwhile; 
		    wp_reset_query();
			$product_params = array(
                  "param_name"    => "id",
                  "type"          => "dropdown",
                  "heading"       => esc_html__('Select a product', 'plethora-framework'),
                  "value"         => $products_info
               );
            return $product_params;	    
		}

		public static function shortcode_map_globalfield_attributepicker() { 

		    $attributes = wc_get_attribute_taxonomies();
		    $attr_info = array();
		    foreach ( $attributes as $attr ) { 
		    	$id 	= $attr->attribute_name;
		    	$title 	= $attr->attribute_label;
		    	$attr_info[$title] = $id;
		    }
			$attribute_params = array(
                  "param_name"    => "attribute",
                  "type"          => "dropdown",
				  "holder"        => "h4",                                               
                  "class"		  => '',
                  "heading"       => esc_html__('Select an attribute', 'plethora-framework'),
                  "value"         => $attr_info
               );
            return $attribute_params;	    
		}

		public static function shortcode_map_globalfield_categorypicker() { 

			$categories = Plethora_WP::categories(array('taxonomy'=>'product_cat'), 'name', 'slug');
	        $categories_params =     array(
	                "param_name"    => "category",
	                "type"          => "dropdown",
	                "heading"       => esc_html__('Category', 'plethora-framework'),
	                "value"         => $categories,
	                "description"   => esc_html__("Select Category", 'plethora-framework')
	            );
            return $categories_params;	    
		}

		public static function shortcode_map_recent_products() {

          $map_settings =  array(
              "base"              => 'recent_products',
              "name"              => esc_html__("Recent Products", 'plethora-framework'),
              "description"       => esc_html__('Lists recent products', 'plethora-framework'),
              "class"             => "",
              "weight"            => 1,
              "category"          => 'WooCommerce',
			  'icon'        => PLE_FLIB_FEATURES_URI .'/module/woocommerce/woo_icon.png',
              "params"            => array()
          	);
			$map_settings['params'][] = self::shortcode_map_globalfield_perpage();
			$map_settings['params'][] = self::shortcode_map_globalfield_column();
			$map_settings['params'][] = self::shortcode_map_globalfield_orderby();
			$map_settings['params'][] = self::shortcode_map_globalfield_order();
			return $map_settings;
		}

		public static function shortcode_map_featured_products() {

          $map_settings =  array(
				"base"        => 'featured_products',
				"name"        => esc_html__("Featured Products", 'plethora-framework'),
				"description" => esc_html__('Lists featured products', 'plethora-framework'),
				"class"       => "",
				"weight"      => 1,
				"category"    => 'WooCommerce',
				'icon'        => PLE_FLIB_FEATURES_URI .'/module/woocommerce/woo_icon.png',
				"params"      => array()
          	);
			$map_settings['params'][] = self::shortcode_map_globalfield_perpage();
			$map_settings['params'][] = self::shortcode_map_globalfield_column();
			$map_settings['params'][] = self::shortcode_map_globalfield_orderby();
			$map_settings['params'][] = self::shortcode_map_globalfield_order();
			return $map_settings;
		}

		public static function shortcode_map_product() {

          $map_settings =  array(
				"base"        => 'product',
				"name"        => esc_html__("Single Product", 'plethora-framework'),
				"description" => esc_html__('Show a single product', 'plethora-framework'),
				"class"       => "",
				"weight"      => 1,
				"category"    => 'WooCommerce',
				'icon'        => PLE_FLIB_FEATURES_URI .'/module/woocommerce/woo_icon.png',
				"params"      => array()
          	);
			$map_settings['params'][] = self::shortcode_map_globalfield_productpicker();
			return $map_settings;
		}

		public static function shortcode_map_products() {

          $map_settings =  array(
				"base"        => 'products',
				"name"        => esc_html__("Products", 'plethora-framework'),
				"description" => esc_html__('Show multiple products', 'plethora-framework'),
				"class"       => "",
				"weight"      => 1,
				"category"    => 'WooCommerce',
				'icon'        => PLE_FLIB_FEATURES_URI .'/module/woocommerce/woo_icon.png',
				"params"      => array( 
						              	array(
						                  "param_name"    => "ids",
						                  "type"          => "dropdown_posts",
						                  'type_posts'    => array('product'),
						                  "heading"       => esc_html__('Select products', 'plethora-framework'),
						                  'description'   => 'Ctrl + click, for multiple selection'
						               ),
						            )
          	);
			$map_settings['params'][] = self::shortcode_map_globalfield_column();
			$map_settings['params'][] = self::shortcode_map_globalfield_orderby();
			$map_settings['params'][] = self::shortcode_map_globalfield_order();
			return $map_settings;
		}

		public static function shortcode_map_add_to_cart() {

          $map_settings =  array(
				"base"        => 'add_to_cart',
				"name"        => esc_html__("Add To Cart Button", 'plethora-framework'),
				"description" => esc_html__('Show the price/cart button of a single product', 'plethora-framework'),
				"class"       => "",
				"weight"      => 1,
				"category"    => 'WooCommerce',
				'icon'        => PLE_FLIB_FEATURES_URI .'/module/woocommerce/woo_icon.png',
				"params"      => array(
										self::shortcode_map_globalfield_productpicker(),
					                   	array(
					                      "param_name"    => "style",
					                      "type"          => "textfield",
					                      "heading"       => esc_html__('Custom CSS', 'plethora-framework'),
					                      "value"         => '',
					                  	),
             							
						            )
          	);
			return $map_settings;
		}

		public static function shortcode_map_product_page() {

          $map_settings =  array(
				"base"        => 'product_page',
				"name"        => esc_html__("Product Page", 'plethora-framework'),
				"description" => esc_html__('Show a full single product page', 'plethora-framework'),
				"class"       => "",
				"weight"      => 1,
				"category"    => 'WooCommerce',
				'icon'        => PLE_FLIB_FEATURES_URI .'/module/woocommerce/woo_icon.png',
				"params"      => array(self::shortcode_map_globalfield_productpicker())
          	);
			return $map_settings;
		}				

	    public static function shortcode_map_product_category() {

	      $map_settings =  array(
				"base"        => 'product_category',
				"name"        => esc_html__("Product Category", 'plethora-framework'),
				"description" => esc_html__('Show multiple products in a category', 'plethora-framework'),
				"class"       => "",
				"weight"      => 1,
				"category"    => 'WooCommerce',
				'icon'        => PLE_FLIB_FEATURES_URI .'/module/woocommerce/woo_icon.png',
				"params"      => array()
	        );

	      $map_settings['params'][] = self::shortcode_map_globalfield_categorypicker();
	      $map_settings['params'][] = self::shortcode_map_globalfield_perpage();
	      $map_settings['params'][] = self::shortcode_map_globalfield_column();
	      $map_settings['params'][] = self::shortcode_map_globalfield_orderby();
	      $map_settings['params'][] = self::shortcode_map_globalfield_order();

	      return $map_settings;
	    }

	    public static function shortcode_map_product_categories() {

	      $map_settings =  array(
			"base"        => 'product_categories',
			"name"        => esc_html__("Product Categories", 'plethora-framework'),
			"description" => esc_html__('Display product categories loop', 'plethora-framework'),
			"class"       => "",
			"weight"      => 1,
			"category"    => 'WooCommerce',
			'icon'        => PLE_FLIB_FEATURES_URI .'/module/woocommerce/woo_icon.png',
			"params"      => array(

	            array(
	                "param_name"    => "hide_empty",
	                "type"          => "dropdown",
	                "heading"       => esc_html__("Hide empty", 'plethora-framework'),
	                "value"         => array( "Hide" => "1", "Show" => "0" ),
	                "description"   => "Hide empty."
	            ),
	            array(
	                "param_name"    => "parent",
	                "type"          => "textfield",
	                "heading"       => esc_html__("Parent", 'plethora-framework'),
	                "value"         => '',
	                "description"   => esc_html__("Set the parent paramater to 0 to display only top level categories", "plethora-framework")
	            ),
	            array(
	                "param_name"    => "ids",
	                "type"          => "textfield",
	                "heading"       => esc_html__("Display categories", 'plethora-framework'),
	                "value"         => '',
	                "description"   => esc_html__("Add the IDs of the categories you want to display (comma separated)", "plethora-framework")
	            ),
	            array(
	                "param_name"    => "number",
	                "type"          => "textfield",
	                "heading"       => esc_html__('Maximum results', 'plethora-framework'),
	                "value"         => '',
	                "description"   => esc_html__("Set the maximum product results. Leave empty or set it to zero to display all results", 'plethora-framework')
	            ),
	      		self::shortcode_map_globalfield_orderby(),
	      		self::shortcode_map_globalfield_order(),
	       		self::shortcode_map_globalfield_column(),
	          )
	        );

	      return $map_settings;
	    }

	    public static function shortcode_map_sale_products() {

	      $map_settings =  array(
				"base"        => 'sale_products',
				"name"        => esc_html__("Sale Products", 'plethora-framework'),
				"description" => esc_html__('List all products on sale', 'plethora-framework'),
				"class"       => "",
				"weight"      => 1,
				"category"    => 'WooCommerce',
				'icon'        => PLE_FLIB_FEATURES_URI .'/module/woocommerce/woo_icon.png',
				"params"      => array()
	        );

	      $map_settings['params'][] = self::shortcode_map_globalfield_perpage();
	      $map_settings['params'][] = self::shortcode_map_globalfield_column();
	      $map_settings['params'][] = self::shortcode_map_globalfield_orderby();
	      $map_settings['params'][] = self::shortcode_map_globalfield_order();

	      return $map_settings;
	    }

	    public static function shortcode_map_best_selling_products() {

	      $map_settings =  array(
				"base"        => 'best_selling_products',
				"name"        => esc_html__("Best Selling Products", 'plethora-framework'),
				"description" => esc_html__('List best selling products on sale', 'plethora-framework'),
				"class"       => "",
				"weight"      => 1,
				"category"    => 'WooCommerce',
				'icon'        => PLE_FLIB_FEATURES_URI .'/module/woocommerce/woo_icon.png',
				"params"      => array()
	        );

	      $map_settings['params'][] = self::shortcode_map_globalfield_perpage();
	      $map_settings['params'][] = self::shortcode_map_globalfield_column();

	      return $map_settings;
	    }

	    public static function shortcode_map_top_rated_products() {

	      $map_settings =  array(
				"base"        => 'top_rated_products',
				"name"        => esc_html__("Top Rated Products", 'plethora-framework'),
				"description" => esc_html__('List top rated products on sale', 'plethora-framework'),
				"class"       => "",
				"weight"      => 1,
				"category"    => 'WooCommerce',
				'icon'        => PLE_FLIB_FEATURES_URI .'/module/woocommerce/woo_icon.png',
				"params"      => array()
	        );

	      $map_settings['params'][] = self::shortcode_map_globalfield_perpage();
	      $map_settings['params'][] = self::shortcode_map_globalfield_column();
	      $map_settings['params'][] = self::shortcode_map_globalfield_orderby();
	      $map_settings['params'][] = self::shortcode_map_globalfield_order();

	      return $map_settings;
	    }

	    public static function shortcode_map_product_attribute() {

	      $map_settings =  array(
				"base"        => 'product_attribute',
				"name"        => esc_html__("Product Attribute", 'plethora-framework'),
				"description" => esc_html__('List products with an attribute shortcode', 'plethora-framework'),
				"class"       => "",
				"weight"      => 1,
				"category"    => 'WooCommerce',
				'icon'        => PLE_FLIB_FEATURES_URI .'/module/woocommerce/woo_icon.png',
				"params"      => array(
		            self::shortcode_map_globalfield_attributepicker(),
		            array(
		                "param_name"    => "filter",
		                "type"          => "textfield",
		                "heading"       => esc_html__("Add an attribute filter", 'plethora-framework'),
		                "description"       => esc_html__("Add a filter for the attribute you've just selected above", 'plethora-framework'),
		                "value"         => "",
		            ),
	          )
	        );

	      $map_settings['params'][] = self::shortcode_map_globalfield_perpage();
	      $map_settings['params'][] = self::shortcode_map_globalfield_column();
	      $map_settings['params'][] = self::shortcode_map_globalfield_orderby();
	      $map_settings['params'][] = self::shortcode_map_globalfield_order();

	      return $map_settings;
	    }

	    public static function shortcode_map_related_products() {

	      $map_settings =  array(
				"base"        => 'related_products',
				"name"        => esc_html__("Related Products", 'plethora-framework'),
				"description" => esc_html__('List related products', 'plethora-framework'),
				"class"       => "",
				"weight"      => 1,
				"category"    => 'WooCommerce',
				'icon'        => PLE_FLIB_FEATURES_URI .'/module/woocommerce/woo_icon.png',
				"params"      => array()
	        );

	      $map_settings['params'][] = self::shortcode_map_globalfield_perpage();
	      $map_settings['params'][] = self::shortcode_map_globalfield_column();
	      $map_settings['params'][] = self::shortcode_map_globalfield_orderby();

	      return $map_settings;
	    }

	    // Just a helper to avoid writing all these conditionals
	    public static function is_shop_catalog(){

	    	if (  is_shop() || ( is_shop() && is_search() ) || is_product_category() || is_product_tag() ) {

	    		return true;
	    	}
			return false;
	    }

	}
}

// This should be applied before WC's activation, as it should declare shop image sizes right after theme activation
if ( !function_exists( 'plethora_woo_image_dimensions')) { 

	if ( !class_exists('Plethora_WP')) { 

	class Plethora_WP { 

		static function add_action() { }

	}

	}
	add_action( 'after_setup_theme', 'image_dimensions' );

	function image_dimensions() { 

		global $pagenow;
		if ( ! isset( $_GET['activated'] ) || $pagenow != 'themes.php' ) {
		return;
		}
		$catalog = array(
		'width' => '326', // px
		'height'	=> '326', // px
		'crop'	=> 1 // true
		);
		$single = array(
		'width' => '547', // px
		'height'	=> '547', // px
		'crop'	=> 1 // true
		);
		$thumbnail = array(
		'width' => '168', // px
		'height'	=> '168', // px
		'crop'	=> 0 // false
		);
		// Image sizes
		update_option( 'shop_catalog_image_size', $catalog ); // Product category thumbs
		update_option( 'shop_single_image_size', $single ); // Single product image
		update_option( 'shop_thumbnail_image_size', $thumbnail ); // Image gallery thumbs 

	}


}