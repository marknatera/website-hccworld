<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2014

File Description: Search form(s) template part

*/
Plethora_Theme::dev_comment('Start >>> Search Template Part Loaded: '. PLE_THEME_TEMPLATES_DIR . '/'. basename( __FILE__ ), 'templateparts');
Plethora_Theme::dev_comment('Start >>> Search Form Section', 'layout');
if ( is_404() ) { 

    echo '                    <div class="search_form">';
    echo '                         <form method="get" name="s" id="s" action="'. esc_url( home_url( '/' )).'">';
    echo '                              <div class="row">';
    echo '                                   <div class="col-sm-2 col-md-2"></div>';
    echo '                                   <div class="col-sm-6 col-md-6">';
    echo '                                        <input name="s" id="search" class="form-control" type="search">';
    echo '                                   </div>';
    echo '                                   <div class="col-sm-4 col-md-4"> <input type="submit" id="submit_btn" class="btn btn-primary" name="submit" value="'. esc_attr( esc_html__('Search', 'healthflex') ) .'" /> </div>';
    echo '                                   <div class="col-sm-2 col-md-2"></div>';
    echo '                              </div>';
    echo '                         </form>';
    echo '                    </div>';

} else { 

    echo '                         <form method="get" name="s" id="s" action="'. esc_url(home_url( '/' )) .'">';
    echo '                              <div class="row">';
    echo '                                <div class="col-lg-10">';
    echo '                                    <div class="input-group">';
    echo '                                        <input name="s" id="search" class="form-control" type="text" placeholder="'. esc_attr( esc_html__('Search', 'healthflex') )  .'">';
    echo '                                        <span class="input-group-btn">';
    echo '                                          <button class="btn btn-default" type="submit">'. esc_html__('Go!', 'healthflex') .'</button>';
    echo '                                        </span>';
    echo '                                    </div>';
    echo '                                </div>';
    echo '                              </div>';
    echo '                         </form>';
}
Plethora_Theme::dev_comment('End <<< Search Form Section', 'layout');
Plethora_Theme::dev_comment('End <<< '. PLE_THEME_TEMPLATES_DIR . '/'. basename( __FILE__ ), 'templateparts');?>