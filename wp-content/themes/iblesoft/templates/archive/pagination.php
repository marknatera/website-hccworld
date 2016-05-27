<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2014

File Description: Posts paging
*/

Plethora_Theme::dev_comment('Start >>> Posts Pagination', 'layout');
 $output = '';
 $pages = '';
 $range = 5;
 $showitems = ($range * 2)+1;  
 global $paged;
 if(empty($paged)) $paged = 1;

 if($pages == '')
 {
     global $wp_query;
     $pages = $wp_query->max_num_pages;
     if(!$pages)
     {
         $pages = 1;
     }
 }   

if ( $pages != 1 ) {

    $output .= '<div class="pagination_wrapper">';
    $output .= '  <ul class="pagination pagination-centered">';
    $output .= '    <li>'. get_previous_posts_link( esc_html__('Prev', 'healthflex') ).'</li>';
    if ( $paged > 2 && $paged > $range+1 && $showitems < $pages ) { 

      $output .= '    <li><a href="'.get_pagenum_link(1).'">&laquo;</a></li>'; 

    }
    if ( $paged > 1 && $showitems < $pages ) { 

      $output .= '    <li><a href="'.get_pagenum_link($paged - 1).'">&lsaquo;</a></li>'; 

    }
    for ( $i=1; $i <= $pages; $i++ ) { 

      if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )) {

      $class = ( $paged == $i ) ? $class = ' class="active"': $class = '';
      $output .= '    <li'.$class.'><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
      
      }
    }
    if ($paged < $pages && $showitems < $pages) {

      $output .= '    <li><a href="' .get_pagenum_link($paged + 1). '">&rsaquo;</a></li>';  

    }
    if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) {

      $output .= '    <li><a href="' .get_pagenum_link($pages).'">&raquo;</a></li>';

    }
    
    $output .= '    <li>'. get_next_posts_link( esc_html__('Next', 'healthflex') ).'</li>';
    $output .= '  </ul>';
    $output .= '</div>';

}

echo $output;
?>
<?php Plethora_Theme::dev_comment('End <<< Posts Pagination', 'layout'); 
