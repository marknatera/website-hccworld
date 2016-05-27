<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M            (c) 2015

File Description: Footer Widgetized Areas template 

*/
$atts = get_query_var( 'atts' );
?>
				<div class="container">
                  	<div class="row">
                	<?php Plethora_WP::get_template_part('templates/global/footer-widgets-layout', $atts['layout']); ?>
					</div>
				</div>