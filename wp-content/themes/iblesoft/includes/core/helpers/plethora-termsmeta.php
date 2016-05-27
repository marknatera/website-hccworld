<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2014

Extension class for Plethora_Fields, it handles the taxonomy meta fields

*/

if ( ! defined( 'ABSPATH' ) ) exit; // NO DIRECT ACCESS 


/**
 * @package Plethora Framework
 * @version 1.0
 * @author Plethora Dev Team
 * @copyright Plethora Themes (c) 2016
 *
 */
class Plethora_TermsMeta extends Plethora_Fields {

	public $taxonomy;
	public $term_id;

    public function __construct( $taxonomy, $opts ) {

		$this->taxonomy = $taxonomy;
		$this->opts     = $opts;
    	add_action( 'admin_init', array( $this, 'initialize' ), 20 );
    }

    public function initialize() {

    	// Check if taxonomy is valid first
    	if ( is_admin() && taxonomy_exists( $this->taxonomy ) ) {

	    	// Set term ID ( is a valid value, if we are in an edit page, otherise is set to 0 )
    		$this->term_id = !empty( $_GET['tag_ID'] ) ? intval( $_GET['tag_ID'] ) : 0;

	    	// Set expanded core option attributes configuration 
	    	$this->opts_index = $this->set_opts_index();

			// Build form field options according to given configuration
	    	$this->init( $this->opts );

	    	// Add fields to new term form
	    	add_action( $this->taxonomy .'_add_form_fields', array( $this, 'taxonomy_add_form_fields' ), 10, 2 );

	    	// Save the new created meta
	    	add_action( 'created_'. $this->taxonomy , array( $this, 'taxonomy_save_meta' ), 10, 2 );

	    	// Add fields to update term form
			add_action( $this->taxonomy .'_edit_form_fields', array( $this, 'taxonomy_edit_form_fields' ), 10, 2 );

	    	// Save the updated meta
	    	add_action( 'edited_'. $this->taxonomy , array( $this, 'taxonomy_update_meta' ), 10, 2 );

	    	// Add columns on terms list table headers
	    	add_action( 'manage_edit-'. $this->taxonomy .'_columns' , array( $this, 'taxonomy_add_columns' ) );

	    	// Add columns content on terms list table 
			add_filter('manage_'. $this->taxonomy .'_custom_column', array( $this, 'taxonomy_add_columns_content' ), 10, 3 );
		}
    }

    /**
    * Taxonomy term meta option attribute configuration
    * These will be merged with the abstract class core attributes
    * @return array()
    */
    public function set_opts_index(){

    	// These options will be appended on Plethora_Fields opts_core_attrs
		$termsmeta_opts_index = array( 
				'admin_col'          => false,	// Display this option value on terms list table ( default: false )
				'admin_col_sortable' => false,	// Display this option value on terms list table and make it sortable ( default: false )
				'admin_col_markup'   => false, 	// Display markup for terms list table ( admin_col OR admin_col_sortable must be true ) 	
		);

		// Merge with default $opts_core_attrs
	    $opts_index = array_merge( $this->get_core_opts_index(), $termsmeta_opts_index);
	    return $opts_index;
    }

    /**
    * This method is used by the base class to determin the saved field value
    * @return multi ( string, array, boolean )
    */
    public function get_field_value( $field_id, $default ) {

     	$field_value = $default; 

     	if ( !empty( $field_id ) && $this->term_id  ) {

     		$field_value = get_term_meta( $this->term_id, $field_id, true );
     	} 

     	return $field_value;
    }

    /**
    * Adds form fields to add new term admin screen
    * Hooked @ $this->taxonomy .'_add_form_fields'
    * @return NULL
    */
    public function taxonomy_add_form_fields() {

     	$fields = $this->fields;
     	foreach ( $fields as $field ) {

     		$meta_key = $field['id'];

     		echo '<div class="form-field term-'.$meta_key.'-wrap">';
     		echo $field['output_label'];
     		echo $field['output_field'];
     		echo $field['output_desc'];
     		echo '</div>';
     	}
     }


    /**
    * Saves new term form meta fields
    * Hooked @ 'created_'. $this->taxonomy
    * @return NULL
    */
     public function taxonomy_save_meta( $term_id, $tt_id ) {

     	$fields = $this->fields;
     	foreach ( $fields as $field ) {
     		
     		$meta_key = $field['id'];
     		$meta_value = !empty( $_POST[$field['id']] ) ? $_POST[$field['id']] : '';
		    if( !empty( $meta_key ) && !empty( $meta_value ) ){

		        add_term_meta( $term_id, $meta_key, $meta_value, true );
		    }
     	}
    }

	/**
	* Adds form fields to edit term admin screen
    * Hooked @ $this->taxonomy .'_edit_form_fields'
	* @return NULL
	*/
    public function taxonomy_edit_form_fields( $term, $taxonomy  ) {

     	$term_id = $term->term_id;
     	$fields = $this->fields;
     	foreach ( $fields as $field ) {

     		$meta_key = $field['id'];
     		$meta_value = get_term_meta( $term_id, $meta_key, true  );

     		echo '<tr class="form-field form-required term-name-wrap">';
     		echo '<th scope="row">';
     		echo $field['output_label'];
     		echo '</th>';
     		echo '<td>';
			echo $field['output_field'];
     		echo $field['output_desc'];
     		echo '</td>';
     		echo '</tr>';
     	}
    }

    /**
    * Saves update term form meta fields
    * Hooked @ 'edited_'. $this->taxonomy
    * @return NULL
    */
    public function taxonomy_update_meta( $term_id, $tt_id ) {

     	$fields = $this->fields;
     	foreach ( $fields as $field ) {
     		
     		$meta_key = $field['id'];
     		$meta_value = !empty( $_POST[$field['id']] ) ? $_POST[$field['id']] : '';
		    if( !empty( $meta_key ) && !empty( $meta_value ) ){

		        update_term_meta( $term_id, $meta_key, $meta_value );
		    }
     	}
    }

	public function taxonomy_add_columns( $columns ){

     	$fields = $this->fields;
     	foreach ( $fields as $field ) {

     		if ( $field['admin_col'] === true ) {

	    		$columns[$field['id']] = $field['title'];
	    	}
	    }
	    return $columns;
	}

	public function taxonomy_add_columns_content( $content, $column_name, $term_id ){
	    
		$fields = $this->fields;
		foreach ( $fields as $field ) {

		    if( $column_name !== $field['id'] ){
		        continue;
		    }

			$term = get_term( $term_id, $this->taxonomy );
			$saved_meta_value = get_term_meta( $term_id, $field['id'], true );
			$opt_title = !empty( $field['options'][$saved_meta_value] ) ? $field['options'][$saved_meta_value] : '';
		    if ( !empty( $field['admin_col_markup'] ) ) {

		        $content .= sprintf( $field['admin_col_markup'], $saved_meta_value, $opt_title, $field['title'], $term->name );
		   
		    } else {

		        $content .= $saved_meta_value;
		    }
		}
	    return $content;
	}
}

/* CLASS DOCUMENTATION ( latest update: 22/04/2016 )

	BASIC USAGE:

		$category_terms = new Plethora_TermsMeta( 'category' )

	DESCRIPTION:

		You may add term meta fields on new/edit forms on any taxonomy using this class.
		Apart from new/edit screen form fields, it can handle column values displays.

	PARAMETERS:
 		
 		$taxonomy : the taxonomy slug ( required )
		$opts 	  : the field configuration options ( required, check below for more info )

	OPTIONS CONFIGURATION:

		$opts must be a set of arrays, each array represents a field configuration.
		All fields share some common configuration, while there are some field type specific ones.
		
		These are the common attributes:

			// Core mandatory attrs ( handled by Plethora_Fields )
				'id'            => string					// * Unique ID identifying the field. Must be different from all other field IDs.
				'type'          => string					// * Value identifying the field type.
			// Core attrs ( handled by Plethora_Fields )
				'title'         => string					// Displays title of the option.
				'desc'          => string,					// Description of the option, usualy appearing beneath the field control.
				'default'       => string|array|boolean		// Value set as default
				'class'			=> string					// Append class(es) to field class attribute
			// Tax terms meta core attrs ( handled by Plethora_TermsMeta )
				'admin_col'          => boolean				// Display this option value on terms list table ( default: false )
				'admin_col_sortable' => boolean				// Display this option value on terms list table and make it sortable ( default: false )
				'admin_col_markup'   => string				// Terms list table column markup ( Use %1$s for value, %2$s for the title )

		These are additional field-specific attributes:

			'text' field:
				'placeholder' => string		// Text to display in the input when no value is present.

			'textarea' field"
				'placeholder' => string		// Text to display in the input when no value is present.
				'rows'        => string		// Numbers of text rows to display

			'select' field"
				'options' => array()		// Array of options in key pair format.  The key represents the ID of the option.  The value represents the text to appear in the selector.
				'multi'   => boolean		// Set true for multi-select variation of the field  ( default: false )
*/