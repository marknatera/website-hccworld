<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2015

File Description: Posts loop / Excerpt
*/
$grid_class = Plethora_Theme::get_archive_list( array( 'output' => 'class' ) );
?>
  <div class="post-grid <?php echo is_array($grid_class) ? esc_attr( implode( ' ', $grid_class ) ) : ''; ?>">
    <div class="row">