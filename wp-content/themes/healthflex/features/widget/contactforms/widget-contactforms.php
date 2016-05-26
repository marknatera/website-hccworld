<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Contact Forms Widget Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Widget_Contactforms') && !class_exists('Plethora_Widget_Contactforms_Ext') ) {

	add_action('widgets_init', create_function('', 'return register_widget("Plethora_Widget_Contactforms_Ext");') ); 	

  /**
   * Extend base class
   * Base class file: /plugins/plethora-framework/features/widget/widget-contactforms.php
   */
  class Plethora_Widget_Contactforms_Ext extends Plethora_Widget_Contactforms { 

  }
}