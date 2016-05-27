<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

File Description: Main navigation template part

*/

$main_menu_location  = Plethora_Theme::option( METAOPTION_PREFIX .'navigation-main-location', 'primary');     // Where to put the title
$main_menu_behavior  = Plethora_Theme::option( METAOPTION_PREFIX .'navigation-main-behavior', 'hover_menu');  // 2nd level items display trigger

?>
       <div class="menu_container"><span class="close_menu">&times;</span>
                <?php 
                  wp_nav_menu( array(
                    'container'       => false, 
                    'menu_class'      => 'main_menu ' . $main_menu_behavior , 
                    'depth'           => 6,
                    'theme_location' => $main_menu_location,
                    'walker'          => ( class_exists( 'Plethora_Module_Navwalker_Ext' )) ? new Plethora_Module_Navwalker_Ext() : ''
                  ));
                ?>
        </div>

        <label class="mobile_collapser"><?php echo esc_html__('MENU', 'healthflex') ?></label> <!-- Mobile menu title -->
