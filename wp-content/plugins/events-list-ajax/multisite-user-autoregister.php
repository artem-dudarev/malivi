<?php
if( !is_multisite() ) { 
	exit('This plugin only functions on WordPress Multisite.'); 
}

add_action('init', 'auto_join_multisite'); 
function auto_join_multisite( ) {
	global $current_user, $blog_id;
	
	if(!is_user_logged_in()) {
		return false;
	}
	
	if( !is_user_member_of_blog() ) {
		add_user_to_blog($blog_id, $current_user->ID, 'subscriber');
	}
}

?>