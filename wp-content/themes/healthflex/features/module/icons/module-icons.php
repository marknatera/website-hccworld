<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Icons Module Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Module_Icons') && !class_exists('Plethora_Module_Icons_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-featureslib/features/module/icons/module-icons.php
   */
  class Plethora_Module_Icons_Ext extends Plethora_Module_Icons { 

	public $fontawesome_status           = true;
	public $lineabasic_status            = false;
	public $webfont_medical_icons_status = true;
	public $weather_icons_status         = false;
  }
}