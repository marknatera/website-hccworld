<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2015

File Description: Posts loop / Meta Info
*/
$post_type          = get_post_type();
$the_id             = get_the_id();
$show_primary_tax   = Plethora_Theme::option( METAOPTION_PREFIX .'archive'.$post_type.'-info-primarytax', 1, $the_id );
$primary_tax        = Plethora_Theme::option( METAOPTION_PREFIX .$post_type.'-info-primarytax-slug', 'category', $the_id );
$show_secondary_tax = Plethora_Theme::option( METAOPTION_PREFIX .'archive'.$post_type.'-info-secondarytax', 1, $the_id );
$secondary_tax      = Plethora_Theme::option( METAOPTION_PREFIX .$post_type.'-info-secondarytax-slug', 'post_tag', $the_id );
$show_author        = Plethora_Theme::option( METAOPTION_PREFIX .'archive'.$post_type.'-info-author', 1, $the_id );
$show_date          = Plethora_Theme::option( METAOPTION_PREFIX .'archive'.$post_type.'-info-date', 1, $the_id );
$show_comments      = Plethora_Theme::option( METAOPTION_PREFIX .'archive'.$post_type.'-info-comments', 1, $the_id );
$output             = '';

// date info
if ( $show_date )   { $output .= '<span class="post_info post_date"><i class="fa fa-calendar"></i> '.get_the_date() .'</span>'; }
// author info
if ( $show_author ) { $output .= '<a href="#" title="' . esc_attr( sprintf( get_the_author() )) . '"><span class="post_info post_author">'. get_the_author() .'</span></a>'; }
// primary taxonomy info
if ( $show_primary_tax && !empty( $primary_tax ) ) { 
  $terms = get_the_terms( $the_id, $primary_tax );
  if ( ! is_wp_error( $terms ) && !empty( $terms ) ) {
    foreach($terms as $key=>$term) {

      $output .= '<a href="'.get_term_link( $term ).'" title="' . esc_attr( sprintf( esc_html__( "View all in category: %s", 'healthflex' ), $term->name ) ) . '"><span class="post_info post_categories">'.$term->name.'</span></a>';
    }
  }
}
// secondary taxonomy info
if ( $show_secondary_tax && !empty( $secondary_tax ) ) { 
  $terms = get_the_terms( $the_id, $secondary_tax );
  if ( ! is_wp_error( $terms ) && !empty( $terms ) ) {
    foreach($terms as $key=>$term) {

      $output .= '<a href="'.get_term_link( $term ).'" title="' . esc_attr( sprintf( esc_html__( "View all in category: %s", 'healthflex' ), $term->name ) ) . '"><span class="post_info post_tags">'.$term->name.'</span></a>';
    }
  }
}

// comments info
$html_comments  = '';
if ( $show_comments && comments_open()  ) { 

    $num_comments = get_comments_number();
    if ( $num_comments > 0 ) {

      $output .= '<a href="'. esc_url( get_permalink() .'#post_comments').'"><span class="post_info post_comment"><i class="fa fa-comments"></i>'. $num_comments .' </span></a>' ;
    } 
}

if ( !empty( $output ) ) { 
  $output = '<div class="post_sub">'. $output .'</div>'; 
  echo $output;
}