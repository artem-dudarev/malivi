<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

while ( have_posts() ) {  
	the_post();
	tribe_get_template_part( 'page/single', get_post_type() );
}
?>
