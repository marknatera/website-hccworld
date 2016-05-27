<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2014

Base class class for: Plethora_Options_Taxonomy | Plethora_Options_Widget

Version: 1.2

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


/**
 * @package Plethora Framework
 * @version 1.0
 * @author Plethora Dev Team
 * @copyright Plethora Themes (c) 2016
 *
 */
abstract class Plethora_Fields {

    public $index            = array();     // Supported fields index
    public $opts_index       = array();     // Hold core option attributes along with their default values. These will be appended on each field atrributes( expanded in extension class )
    public $approved_opts    = array();		// Validated options attributes
    public $register_scripts = array();		// List with scripts that must be enqueued
    public $register_styles  = array();		// List with styles that must be enqueued
    public $fields           = array();		// Full fields info list ( contains everything needed for rendering too )
    public $error_log        = array();

    public function init( $opts ) {

    	// Well...there is no point without attributes!
    	if ( !is_array( $opts ) || empty( $opts ) ) { return; }

        // Set fields index
        $this->index  = $this->set_index();

        // Set core options index
        $this->opts_index  = $this->set_opts_index();

        // Approve caller given options setup
		$this->approved_opts = $this->approve_options( $opts );

    	// Turn all options into field array items with complete attrs configuration
		$this->fields = $this->set();
    }

    /**
    * Returns the available fields configuration
    * @return string
    */
    private function set_index() {

		$index   = array();
        $index['color'] = array(
                'type'     => 'color',      // Field type ( how it is called in option arguments )
                'scripts'  => array(),      // Related JS assets ( similar to wp_enqueue_script )
                'styles'   => array(),      // Related CSS assets ( similar to wp_enqueue_style )
                'callback' => array()       // callback method ( used ONLY if related field method does not exist in this class )
        );
        $index['select'] = array(
                'type'     => 'select',      // Field type ( how it is called in option arguments )
                'scripts'  => array(),      // Related JS assets ( similar to wp_enqueue_script )
                'styles'   => array(),      // Related CSS assets ( similar to wp_enqueue_style )
                'callback' => array()       // callback method ( used ONLY if related field method does not exist in this class )
        );

		return $index;
    }

    /**
    * Checks if options include the correct basic attributes
    * Returns only those options approved ( should have at least id and a supported field type ) 
    * @return array()
    */
    private function approve_options( $opts = array() ) {

        $fields_index  = $this->index;
        $approved_opts = array();
    	foreach ( $opts as $attr ) {

    		// Check field type first
    		if ( !empty( $attr['type'] ) && array_key_exists( $attr['type'], $fields_index ) ) {

    			// Check field id...if not set, then move on
    			if ( empty( $attr['id'] ) ) { continue; }

    			// Sanitize id
    			$attr['id'] = sanitize_key( $attr['id'] );

    			// Set field priority to 1, if not set already
    			$attr['priority'] = empty( $attr['priority'] ) || !is_numeric( $attr['priority'] ) ? 1 : $attr['priority'];

    			// add to approved
    			$approved_opts[$attr['id']] = $attr;
    		}
    	}

    	// Sort opts according to priority given and returns all approved options
    	$approved_opts = $this->sort_opts_by_priority( $approved_opts ); 
		return $approved_opts;
    }

    /**
    * @return array()
    */
    public function get_core_opts_index() {

        // Default values should be given according to fields config
        $core_opts_index = array( 
                'id'            => '',      // * Unique ID identifying the field. Must be different from all other field IDs.
                'type'          => '',      // * Value identifying the field type.
                'title'         => '',      // Displays title of the option.
                'desc'          => '',      // Description of the option, usualy appearing beneath the field control.
                'default'       => '',      // Value set as default
                'class'         => '',      // Append class(es) to field class attribute
        );
        return $core_opts_index;
    }


// BASIC FIELDS CONSTRUCTION METHOD

    /**
    * Sets all fields assets ( script, styles and output )
    * @return array()
    */
    public function set() {

        $fields_index = $this->index;
        $opts          = $this->approved_opts;
        $fields        = array();

    	foreach ( $opts as $field_id => $field_attrs ) {

    		$field_index = $fields_index[$field_attrs['type']];

    		// Get field first...if nothing returned, no need to set scripts/styles
    		$field = array();
    		if ( method_exists( $this, 'set_field_'. $field_attrs['type'] ) ) { // field method exists in this class

    			$set_field_func = 'set_field_'. $field_attrs['type'];
    			$field = $this->$set_field_func( $field_attrs );
    		
    		} elseif ( !empty( $field_index['callback'] ) ) { // callback method will be used

    			// if callback is a method
    			if ( is_array( $field_index['callback'] ) && isset( $field_index['callback'][1] ) ) { 

					$set_field_class  = $field_index['callback'][0];
					$set_field_method = $field_index['callback'][1];
    				if ( method_exists( $set_field_class, $set_field_method ) ) {

     					$field = call_user_func( array( $set_field_class, $set_field_method ), $field_attrs );
    				}
    			// if callback is a simple function
    			} else {

    				$set_field_func = $field_index['callback'];
    				if ( function_exists( $set_field_func ) ) {

     					$field = call_user_func( $set_field_func, $field_attrs );
    				}
    			}
    		}

    		// If we have the field set, then move on with scripts/styles
    		if ( !empty( $field ) ) {

	    		// Set scripts
	    		foreach ( $field_index['scripts'] as $script ) {

	    			$this->scripts[$script[0]] = $script;
	    		}

	    		// Set styles
	    		foreach ( $field_index['styles'] as $style ) {

	    			$this->styles[$style[0]] = $style;
	    		}

	    		// ...and finally add it to $fields variable
	    		$fields[$field_id] = $field;
    		}
    	}

        // ADD SCRIPT ENQUEUES HERE

        // ADD STYLE ENQUEUES HERE

        return $fields;
    }
// BASIC FIELDS CONSTRUCTION METHOD ENDED

// HELPER METHODS START

    /**
    * Sorts options by priority attribute
    * @return array
    */
    public function sort_opts_by_priority( $unsorted_opts ) {

        $priority_index = array();
        foreach ( $unsorted_opts as $id => $unsorted_attr ) {

            $priority_index[$id] = $unsorted_attr['priority'];
        }

        // Sort the priority index
        asort($priority_index);

        // Apply the priority to the return value
        $sorted_opts = array();
        foreach ( $priority_index as $id => $priority ) {

            $sorted_opts[$id] = $unsorted_opts[$id];
        }

        return $sorted_opts;
    }
// HELPER METHODS END

// FIELD METHODS START

    /**
    * Parses field attributes with core attributes and return
    * @return array()
    */
    public function parse_core_field_attrs( $field_attrs ) {

		
	    $global_field_attrs = wp_parse_args( $field_attrs, $this->opts_index );   
        return $global_field_attrs;
    }

    /**
    * Global field attributes setup
    * Called using wp_parse_args within each set_field_{field} method
    * @return array()
    */
    public function set_global_field_output( $field ) {

        $field['output_label']      = !empty( $field['title'] ) ? '<label for="'. $field['id'] .'">'. $field['title'] .'</label>' : '';
        $field['output_desc']       = !empty( $field['desc'] ) ? '<p for="'. $field['id'] .'">'. $field['desc'] .'</p>' : '';
        return $field;
    }

    /**
    * COLOR field attributes setup
    * @return array()
    */
    public function set_field_color( $field_attrs ) { 

    	// Parse with global default attributes
    	$global_field_attrs   = $this->parse_core_field_attrs( $field_attrs );

    	// Parse with additional field attributes ( none for this field )
        $field_specific_attrs = array();
        $field                = wp_parse_args( $field_specific_attrs, $global_field_attrs); 

	    // Add the global markup output
        $field      = $this->set_global_field_output( $field );

        // Add the input field markup output
        $option_val = $this->get_field_value( $field['id'], $field['default'] );
        $field['output_field'] = '<input type="text" name="'. $field['id'] .'" id="'. $field['id'] .'" value="'.$option_val.'" class="'. implode(' ', $field['input_classes'] )  .' '. $field['id'] .'-field'.'" data-default-color="'. $field['default'] .'" />';

	    // Return the field set
	    return $field;
    }

    /**
    * SEL field attrEibutes setup
    * @return array()
    */
    public function set_field_select( $field_attrs ) { 

        // Parse with global default attributes
        $global_field_attrs   = $this->parse_core_field_attrs( $field_attrs );

        // Parse with additional field attributes ( none for this field )
        $field_specific_attrs = array();
        $field                = wp_parse_args( $field_specific_attrs, $global_field_attrs); 

        // Add the global markup output
        $field                = $this->set_global_field_output( $field );

        $field['output_field']  = '<select name="'. $field['id'] .'" id="'. $field['id'] .'" class="'. $field['id'] .'-field'.' '. $field['class'] .'" data-default-color="'. $field['default'] .'">';
        $selected_option_val = $this->get_field_value( $field['id'], $field['default'] );

        foreach ( $field['options'] as $option_val => $option_title  ) {

            $selected = $option_val == $selected_option_val ? ' selected="selected"' : '';
            $field['output_field']  .= '<option value="'. $option_val .'"'. $selected .'>'. $option_title .'</option>';
        }
        $field['output_field']  .= '</select>';

        // Return the field set
        return $field;
    }

// FIELD METHODS END

}