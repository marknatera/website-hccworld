<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2014

File Description: Footer Widgetized Areas template 

*/
$sidebar_one = Plethora_Theme::option( METAOPTION_PREFIX .'footer-sidebar-one', 'sidebar-footer-one');
?>
<div class="col-md-12"><?php dynamic_sidebar( $sidebar_one ); ?></div>
<?php
