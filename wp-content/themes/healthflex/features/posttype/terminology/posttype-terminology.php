<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Terminology Post Type Config Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Posttype_Terminology') && !class_exists('Plethora_Posttype_Terminology_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-featureslib/features/posttype/terminology/posttype-terminology.php
   */
  class Plethora_Posttype_Terminology_Ext extends Plethora_Posttype_Terminology { 

	/** 
	* Override default theme option values. Check base class for the theme option values variables list
	*/
    // public $terminology_layout       = 'no_sidebar';
    // public $terminology_sidebar      = 'sidebar-default';
    // public $terminology_colorset     = 'foo';
    // public $terminology_title        = 1;
    // public $terminology_subtitle     = 1;
    // public $terminology_mediadisplay = 1;
    // public $terminology_categories   = 1;
    // public $terminology_author       = 1;
    // public $terminology_date         = 1;
    // public $terminology_urlrewrite   = 'terminology';

	
	/** 
	* Returns extra theme-specific fields for the theme options configuration
	* Return an array of option settings according to Redux Framework
	* Check Redux docs: https://docs.reduxframework.com/category/core/fields/
	* @return array
	*/
    public function single_themeoptions_ext() { return array(); }

	/** 
	* Returns theme-specific fields for the single post metabox configuration
	* Return an array of option settings according to Redux Framework
	* If an a option is an override of a theme option, do not set a default value
	* Check Redux docs: https://docs.reduxframework.com/category/core/fields/
	* @return array
	*/
    public function single_metabox_ext() { return array(); }

  }
}