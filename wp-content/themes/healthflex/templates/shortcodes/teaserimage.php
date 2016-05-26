<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                    (c) 2015

Teaser image shortcode template part

*/
// Get attributes sent by set_query_var
$atts = get_query_var( 'shortcode_atts' );
// Extract $atts to variables
if ( is_array( $atts ) ) { extract($atts);
$url = !empty($link['url']) ? esc_attr( $link['url'] ) : '#'; ?>
                <div class="department">
                  <h4 class="<?php echo esc_attr( $title_class ); echo ' '. esc_attr( $title_transparent ); ?>">
                    <?php if ( $icon_enable == 1 ) { ?> <i class="<?php echo esc_attr( $title_icon ); ?>"></i> <?php } ?>
                    <a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a>
                  </h4>
                  <a href="<?php echo esc_url( $url ); ?>" style="background-image:url(<?php echo esc_url( $image ); ?>)" class="department_photo <?php echo esc_attr( $image_ratio ); ?>"></a>
                </div>        
<?php
}