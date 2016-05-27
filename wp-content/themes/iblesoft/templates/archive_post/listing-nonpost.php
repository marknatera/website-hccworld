<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2015

File Description: Posts loop / Listing (default)
*/
$post_format = get_post_format();
$media_args = array(
      'type'         => $post_format, 
      'stretch'      => true, 
      'link_to_post' => true,
      'force_display'=> false 
    );
$output = Plethora_Theme::get_post_media( $media_args );
?>

<div class="col-md-12">
  <article id="post-<?php the_ID(); ?>" <?php post_class('post'); ?>>
    <div class="post_headings">
      <?php echo Plethora_Theme::get_title(array( 'listing' => true )); ?>
      <?php echo Plethora_Theme::get_subtitle(array( 'listing' => true )); ?>
    </div>  
      <div class="post_figure_and_info">
        <?php echo $output; ?>
      </div>
      <?php echo Plethora_Theme::get_listing_content(); ?>
  </article>
</div>
