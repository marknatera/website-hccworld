<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

File Description: Footer Info Bar template part

*/
$atts = get_query_var( 'atts' );
?>
            <div class="copyright <?php echo esc_attr( $atts['colorset'] ); ?>">
              <div class="<?php echo esc_attr( $atts['colorset'] ); ?> <?php echo esc_attr( $atts['transparentfilm'] ); ?>">
                 <div class="container">
                      <div class="row">
                           <div class="col-sm-6 col-md-6">
            					<?php echo wp_kses_post( $atts['copyright'] ); ?>
                           </div>
                           <div class="col-sm-6 col-md-6 text-right">
            					<?php echo wp_kses_post( $atts['credits'] ); ?>
                           </div>
                      </div>
                 </div>
              </div>
            </div>