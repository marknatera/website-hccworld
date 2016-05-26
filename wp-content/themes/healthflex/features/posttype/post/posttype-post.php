<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M             (c) 2015 - 2016

Post Type Config Class

*/

if ( ! defined( 'ABSPATH' )) exit; // NO DIRECT ACCESS

if ( class_exists('Plethora_Posttype_Post') && !class_exists('Plethora_Posttype_Post_Ext') ) {

  /**
   * Extend base class
   * Base class file: /plugins/plethora-framework/features/posttype/post/posttype-post.php
   */
  class Plethora_Posttype_Post_Ext extends Plethora_Posttype_Post { 

  }
}