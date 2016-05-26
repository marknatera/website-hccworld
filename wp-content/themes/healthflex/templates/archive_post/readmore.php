<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2015

File Description: Posts loop / Read more button
*/
$blog_linkbutton = Plethora_Theme::option( METAOPTION_PREFIX .'archivepost-show-linkbutton', 1, get_the_id()); // Show Post Link Button
$blog_linktext      = Plethora_Theme::option( METAOPTION_PREFIX .'archivepost-show-linkbutton-text', esc_html__('Read More', 'healthflex'), get_the_id()); // Link Button Text
if ( $blog_linkbutton == 1 ) { ?>
<p><a href="<?php the_permalink(); ?>" class="btn btn-primary"><?php echo wp_strip_all_tags( $blog_linktext ); ?></a></p>
<?php } ?>