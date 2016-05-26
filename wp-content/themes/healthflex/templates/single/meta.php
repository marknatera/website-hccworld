<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2015

File Description: Single Post Template Parts / Meta Info
*/
$atts = get_query_var( 'atts' );
if ( is_array( $atts ) ) { extract($atts); } 

$output = '';
// date info
if ( $show_date )   { $output .= '<span class="post_info post_date"><i class="fa fa-calendar"></i> '.get_the_date() .'</span>'; }
// author info
if ( $show_author ) { $output .= '<a href="'. get_author_posts_url(get_the_author_meta( 'ID' )).'" title="' . esc_attr( sprintf( get_the_author() )) . '"><span class="post_info post_author">'. get_the_author() .'</span></a>'; }
// categories info
if ( $show_categories ) { 
  $categories = get_the_category();
  if ( $categories ) {
    foreach($categories as $key=>$category) {

      $output .= '<a href="'.get_category_link( $category->term_id ).'" title="' . esc_attr( sprintf( esc_html__( "View all posts in category: %s", 'healthflex' ), $category->name ) ) . '"><span class="post_info post_categories">'.$category->cat_name.'</span></a>';
    }
  }
}
// tags info
if ( $show_tags ) { 
  $tags = get_the_tags();
  if ( $tags ) {
    foreach( $tags as $key=>$tag ) {

      $output .= '<a href="'.get_tag_link( $tag->term_id ).'" title="' . esc_attr( sprintf( esc_html__( "View all posts in tag: %s", 'healthflex' ), $tag->name ) ) . '"><span class="post_info post_tags">'.$tag->name.'</span></a>';
    }
  }
}

// comments info
if ( $show_comments && comments_open()  ) { 

    $num_comments = get_comments_number();
    if ( $num_comments > 0 ) {

      $output .= '<a href="'. esc_url( get_permalink() .'#post_comments').'"><span class="post_info post_comment"><i class="fa fa-comments"></i>'. $num_comments .' </span></a>' ;
    } 
}
$output = '<div class="post_sub">'. $output .'</div>'; 
echo $output;