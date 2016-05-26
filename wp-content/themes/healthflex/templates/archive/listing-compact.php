<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2015

File Description: Posts loop / Listing (compact)
*/
$post_format    = get_post_format();
$media_args     = array(
                    'type'         => $post_format, 
                    'stretch'      => true, 
                    'link_to_post' => true,
                    'force_display'=> false 
                  );
$output          = Plethora_Theme::get_post_media( $media_args );
$main_col_width = !empty($media) ? '8' : '12'; 
?>

<div class="col-md-12">
  <article id="post-<?php the_ID(); ?>" <?php post_class( array( 'post', get_post_type() ) ); ?>>
    <div class="row">
      <?php if ( !empty( $output) ) { ?>
      <div class="col-md-4 col-sm-4 col-xs-4">
          <?php echo $output; ?>
      </div>
      <?php } ?>
      <div class="col-md-<?php echo esc_attr( $main_col_width ); ?> col-sm-<?php echo esc_attr( $main_col_width ); ?> col-xs-12">
        <div class="post_headings">
          <?php echo Plethora_Theme::get_title(array( 'listing' => true )); ?>
          <?php echo Plethora_Theme::get_subtitle(array( 'listing' => true )); ?>
        </div>
        <div class="post_figure_and_info">
          <?php Plethora_WP::get_template_part('templates/archive/excerpt'); ?>
          <?php Plethora_WP::get_template_part('templates/archive/meta'); ?>
        </div>
        <?php Plethora_WP::get_template_part('templates/archive_post/readmore'); ?>
      </div>
    </div>
  </article>
</div>
