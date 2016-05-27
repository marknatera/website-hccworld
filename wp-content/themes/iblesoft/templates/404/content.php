<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M                       (c) 2015

File Description: 404 Content Template Part 

*/
$atts = get_query_var( 'atts', $atts );
if ( is_array( $atts ) ) { extract($atts); } 
?>

<div class="col-md-12 section_header elegant centered">
	<h2><?php echo esc_html( $title ); ?></h2>
	<p><?php echo esc_html( $content ); ?></p>
</div>
<?php if ( $search ) { ?>
<div class="search_form">
	<form role="search" method="get" name="s" id="s" action="<?php echo esc_url( home_url( '/' )); ?>">
		<div class="col-md-6 col-md-offset-2">
			<input type="text" class="form-control" name="s" id="search"  type="search">
		</div>
		<div class="col-md-4">
			<button type="submit" id="submit_btn" class="btn btn-primary"><?php echo esc_html( $search_btntext ); ?></button>
		</div>
	</form>
</div>
<?php } ?>