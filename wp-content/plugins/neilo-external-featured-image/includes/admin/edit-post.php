<?php

/**
 * This function returns Nelio EFI's post meta key. The key can be changed
 * using the filter `nelioefi_post_meta_key'
 */
function _nelioefi_url() {
	return apply_filters( 'nelioefi_post_meta_key', '_nelioefi_url' );
}


/**
 * This function returns whether the post whose id is $id uses an external
 * featured image or not
 */
function uses_nelioefi( $id ) {
	$image_url = nelioefi_get_thumbnail_src( $id );
	if ( $image_url === false )
		return false;
	else
		return true;
}

// Creating box
add_action( 'add_meta_boxes', 'nelioefi_add_url_metabox' );
function nelioefi_add_url_metabox() {

	$excluded_post_types = array(
		'attachment', 'revision', 'nav_menu_item', 'wpcf7_contact_form',
	);

	foreach ( get_post_types( '', 'names' ) as $post_type ) {
		if ( in_array( $post_type, $excluded_post_types ) )
			continue;
		add_meta_box(
			'nelioefi_url_metabox',
			'Изображение',
			'nelioefi_url_metabox',
			$post_type,
			'side',
			'default'
		);
	}

}

function nelioefi_url_metabox( $post ) {
	$nelioefi_url = get_post_meta( $post->ID, _nelioefi_url(), true );
	$nelioefi_alt = get_post_meta( $post->ID, '_nelioefi_alt', true );
	$has_img = strlen( $nelioefi_url ) > 0;
	if ( $has_img ) {
		$hide_if_img = 'display:none;';
		$show_if_img = '';
	}
	else {
		$hide_if_img = '';
		$show_if_img = 'display:none;';
	}
	?>
	<input type="text" placeholder="Описание картинки" style="width:100%;margin-top:10px;<?php echo $show_if_img; ?>"
		id="nelioefi_alt" name="nelioefi_alt"
		value="<?php echo esc_attr( $nelioefi_alt ); ?>" /><?php
	if ( $has_img ) { ?>
	<div id="nelioefi_preview_block"><?php
	} else { ?>
	<div id="nelioefi_preview_block" style="display:none;"><?php
	} ?>
		<div id="nelioefi_image_wrapper" style="<?php
			echo (
				'width:100%;' .
				'max-width:300px;' .
				'height:200px;' .
				'margin-top:10px;' .
				'background:url(' . $nelioefi_url . ') no-repeat center center; ' .
				'-webkit-background-size:cover;' .
				'-moz-background-size:cover;' .
				'-o-background-size:cover;' .
				'background-size:cover;' );
			?>">
		</div>

	<a id="nelioefi_remove_button" href="#" onClick="javascript:nelioefiRemoveFeaturedImage();" style="<?php echo $show_if_img; ?>">Удалить изображение</a>
	<script>
	function nelioefiRemoveFeaturedImage() {
		jQuery("#nelioefi_preview_block").hide();
		jQuery("#nelioefi_image_wrapper").hide();
		jQuery("#nelioefi_remove_button").hide();
		jQuery("#nelioefi_alt").hide();
		jQuery("#nelioefi_alt").val('');
		jQuery("#nelioefi_url").val('');
		jQuery("#nelioefi_url").show();
		jQuery("#nelioefi_preview_button").parent().show();
	}
	function nelioefiPreview() {
		jQuery("#nelioefi_preview_block").show();
		jQuery("#nelioefi_image_wrapper").css('background-image', "url('" + jQuery("#nelioefi_url").val() + "')" );
		jQuery("#nelioefi_image_wrapper").show();
		jQuery("#nelioefi_remove_button").show();
		jQuery("#nelioefi_alt").show();
		jQuery("#nelioefi_url").hide();
		jQuery("#nelioefi_preview_button").parent().hide();
	}
	</script>
	</div>
	<input type="text" placeholder="Image URL" style="width:100%;margin-top:10px;<?php echo $hide_if_img; ?>"
		id="nelioefi_url" name="nelioefi_url"
		value="<?php echo esc_attr( $nelioefi_url ); ?>" />
	<div style="text-align:right;margin-top:10px;<?php echo $hide_if_img; ?>">
		<a class="button" id="nelioefi_preview_button" onClick="javascript:nelioefiPreview();">Preview</a>
	</div>
	<?php
}

add_action( 'save_post', 'nelioefi_save_url' );
function nelioefi_save_url( $post_id ) {
	if ( isset( $_POST['nelioefi_url'] ) ) {
		$url = strip_tags( $_POST['nelioefi_url'] );
		update_post_meta( $post_id, _nelioefi_url(), $url );
	}

	if ( isset( $_POST['nelioefi_alt'] ) ) {
		update_post_meta( $post_id, '_nelioefi_alt', strip_tags( $_POST['nelioefi_alt'] ) );
	}
	if ( wp_is_post_revision( $post_id) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	$attachment_id = get_post_thumbnail_id($post_id);
	if ($attachment_id > 0) {
		;
		$old_url = get_post($attachment_id)->post_content;
		if ($old_url == $url) {
			return;
		}
		wp_delete_attachment($attachment_id, true/*forced*/);
		unlink( $upload['file'] );
	}
	if (isset($url) && strlen($url) > 0) {
		events_list_save_external_image($post_id, $url);
	}
} 

function events_list_save_external_image($post_id, $external_image_url) {
	// Add Featured Image to Post
	$image_name       = 'post-' . $post_id . '-featured-image.png';
	$upload_dir       = wp_upload_dir(); // Set upload folder
	$unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
	$filename         = basename( $unique_file_name ); // Create image file name

	// Check folder permission and define file location
	if( wp_mkdir_p( $upload_dir['path'] ) ) {
		$file = $upload_dir['path'] . '/' . $filename;
	} else {
		$file = $upload_dir['basedir'] . '/' . $filename;
	}

	//$external_image_url        = nelioefi_get_thumbnail_src($post_id); // Define the image URL here
	//$external_image_url = 'http://ultraimg.com/images/2016/07/29/Simplest-Responsive-jQuery-Image-Lightbox-Plugin-simple-lightbox.jpg';
	if ($external_image_url) {
		$image_data       = file_get_contents($external_image_url); // Get image data
	}

	// Create the image  file on the server
	file_put_contents( $file, $image_data );

	// Check image file type
	$wp_filetype = wp_check_filetype( $filename, null );

	// Set attachment data
	$attachment = array(
		'post_mime_type' => $wp_filetype['type'],
		'post_title'     => sanitize_file_name( $filename ),
		'post_content'   => $external_image_url,
		'post_status'    => 'inherit'
	);

	// Create the attachment
	$attach_id = wp_insert_attachment( $attachment, $file, $post_id );

	// Include image.php
	require_once(ABSPATH . 'wp-admin/includes/image.php');

	// Define attachment metadata
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

	// Assign metadata to attachment
	wp_update_attachment_metadata( $attach_id, $attach_data );

	// And finally assign featured image to post
	set_post_thumbnail( $post_id, $attach_id );
}

/*add_action( 'transition_post_status', 'save_external_image', 10, 3 );
function save_external_image( $new_status, $old_status, $post ) {
    if ( $new_status === 'publish' ) {
        
    }
}*/



