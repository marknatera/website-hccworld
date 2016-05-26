<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2014

File Description: Comments Display Template Part 

*/
if ( post_password_required() ) { return; }

  /**
   * 'plethora_comments_list' hook
   *
   * @hooked Plethora_Template::comments_list() - 10
   * @hooked Plethora_Template::comments_paging() - 15
   */
   do_action('plethora_comments_list');
     
  /**
   * 'plethora_comments_new' hook
   *
   * @hooked Plethora_Template::comments_new() - 10
   */
   do_action('plethora_comments_new');