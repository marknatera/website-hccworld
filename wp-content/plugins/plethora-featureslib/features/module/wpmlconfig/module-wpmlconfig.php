<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 			 (c) 2013-2016

File Description: WPML Configuration Module

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


if ( !class_exists('Plethora_Module_Wpmlconfig') ) {

	/**
	 */
	class Plethora_Module_Wpmlconfig {

		public static $feature_title        = "WPML CONFIG XML";
		public static $feature_description  = "Used for wpml-config.xml file creation ( in-house use, not a public feature )";
		public static $theme_option_control  = false;											// Will this feature be controlled in theme options panel ( boolean )
		public static $theme_option_default  = true;											// Default activation option status ( boolean )
		public static $theme_option_requires = array();											// Which features are required to be active for this feature to work ? ( array: $controller_slug => $feature_slug )
		public static $dynamic_construct     = true;											// Dynamic class construction ? ( boolean )
		public static $dynamic_method        = false;											// Additional method invocation ( string/boolean | method name or false )

		public static $xml_file = 'wpml-config.xml';
		public $wpml;
		public $config;

		public function __construct() {

			add_action('init', array( $this, 'init'), 999);
		}

		public function init() {

			// Export the XML file
			$this->prepare_document();
			$this->set_custom_fields();
			$this->set_custom_types();
			$this->set_taxonomies();
			$this->set_admin_texts();
			$this->save_file();
		}

		public function get_metabox_texts(){

			$metabox_texts = array();

			global $metaboxes;
			foreach ( $metaboxes as $metabox ) {
				$sections = isset( $metabox['sections'] ) ? $metabox['sections'] : array();
				foreach ( $sections as $section ) {
					$fields = isset( $section['fields'] ) ? $section['fields'] : array();
					foreach ($fields as $field ) {

						if ( !empty( $field['id'] ) && !array_key_exists( $field['id'], $metabox_texts) ) {

							if ( !empty( $field['translate'] ) && $field['translate'] ) {
								
								$metabox_texts[$field['id']] = 'translate';

							} elseif ( empty( $field['translate'] ) && $field['type'] !== 'section' ) {

								$metabox_texts[$field['id']] = 'copy';

							} else {

								$metabox_texts[$field['id']] = 'ignore';
							}
						} 
					}
				}
			}
			// asort($metabox_texts);
			return array_filter($metabox_texts);

		}

		public function get_post_types(){

			$public_post_types = Plethora_Theme::get_supported_post_types( array('exclude' => array('post', 'page')) );
			$nonpublic_post_types = Plethora_Theme::get_supported_post_types( array('exclude' => array('post', 'page'), 'public' => false) );
			$post_types = array_merge($public_post_types, $nonpublic_post_types );
			asort($post_types);
			return array_filter($post_types);
		}

		public function get_taxonomies(){

			$taxonomies = array();
			$post_types = $this->get_post_types();
			foreach ( $post_types as $post_type ) {
				$the_taxonomies = get_object_taxonomies( $post_type );
				$taxonomies = array_merge($taxonomies, $the_taxonomies );
			}
			asort($taxonomies);
			return array_filter($taxonomies);
		}

		public function get_admin_texts(){

			$admin_texts = array();
			global $plethora_options_config;
			$sections = $plethora_options_config->sections;
			foreach ( $sections as $section ) {
				$fields = isset( $section['fields'] ) ? $section['fields'] : array();
				foreach ($fields as $field ) {

					if ( !empty($field['type']) && !empty($field['translate']) && $field['translate'] && ( $field['type'] === 'text' || $field['type'] === 'textarea' )) {
						$admin_texts[]['id'] = $field['id'];
					}
				}
			}
			return $admin_texts;
		}

		public function prepare_document() {

			$wpml = new DOMDocument('1.0', 'UTF-8');
			$wpml->preserveWhiteSpace = false;
			$wpml->formatOutput = true;
			$config = $wpml->createElement('wpml-config');
			$wpml->appendChild($config);
			$this->wpml = $wpml;
			$this->config = $config;

		}

		public function set_custom_fields() {

			$wpml = $this->wpml;
			$config = $this->config;

			$custom_fields = $wpml->createElement('custom-fields');
			$config->appendChild($custom_fields);
			$metabox_texts = $this->get_metabox_texts();
			foreach( $metabox_texts as $id_val=> $action_val )  {

				// create the field
				$custom_field = $wpml->createElement('custom-field', $id_val );
				// add action attribute
				$action_attr  = $wpml->createAttribute ('action');
				$action_attr->value = $action_val;
				$custom_field->appendChild($action_attr);
				// add field to document
				$custom_fields->appendChild($custom_field);

			}
			$this->wpml = $wpml;
			$this->config = $config;

		}
		public function set_custom_types() {

			$wpml = $this->wpml;
			$config = $this->config;

			$custom_types = $wpml->createElement('custom-types');
			$types = $this->get_post_types();
			foreach( $types as $type )  {

				// create the field
				$custom_type = $wpml->createElement('custom-type', $type);
				// add 'translate' attribute
				$translate_attr  = $wpml->createAttribute ('translate');
				$translate_attr->value = '1';
				$custom_type->appendChild($translate_attr);
				// add field to document
				$custom_types->appendChild($custom_type);
			}

			// add fields to document
			$config->appendChild($custom_types);
			$this->wpml = $wpml;

		}
		public function set_taxonomies() {

			$wpml = $this->wpml;
			$config = $this->config;

			$taxonomies = $wpml->createElement('taxonomies');
			$taxs = $this->get_taxonomies();
			foreach( $taxs as $tax )  {

				// create the field
				$taxonomy = $wpml->createElement('taxonomy', $tax );
				// add 'translate' attribute
				$translate_attr  = $wpml->createAttribute ('translate');
				$translate_attr->value = '1';
				$taxonomy->appendChild($translate_attr);
				// add field to document
				$taxonomies->appendChild($taxonomy);
			}
			$config->appendChild($taxonomies);

			$this->wpml = $wpml;
			$this->config = $config;
		}

		public function set_admin_texts() {

			$wpml = $this->wpml;
			$config = $this->config;

			$admin_texts = $wpml->createElement('admin-texts');
			$wrapkey = $wpml->createElement('key');
			$wrap_name_attr  = $wpml->createAttribute ('name');
			$wrap_name_attr->value = THEME_OPTVAR;
			$wrapkey->appendChild($wrap_name_attr);
			$admin_texts->appendChild($wrapkey);
			$texts = $this->get_admin_texts();
			foreach ( $texts as $admin_text) {
				// create the 'key' field
				$key = $wpml->createElement('key');
				// add 'name' attribute
				$name_attr  = $wpml->createAttribute ('name');
				$name_attr->value = $admin_text['id'];
				$key->appendChild($name_attr);
				// add field to document
				$wrapkey->appendChild($key);
			}
			$config->appendChild($admin_texts);

			$this->wpml = $wpml;
			$this->config = $config;

		}

		public function save_file(){

			$wpml = $this->wpml;
			$wpml->save( PLE_THEME_DIR .'/'. self::$xml_file );
		}
	}
}