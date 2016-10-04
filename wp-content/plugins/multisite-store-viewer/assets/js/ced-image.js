jQuery( document ).ready(function( $ ){
	var upload_id = '';
	$( '.ceduploadimage' ).on( 'click', function() {
		upload_id = $(this).prev('input');
		var home_url = $(this).data('url');
		formfield=$('#image').attr('name');
		tb_show('Upload Image', home_url+'/wp-admin/media-upload.php?type=image&TB_iframe=1');
		return false;
	});
	$( '.ceddefaultuploadimage' ).on( 'click', function() {
		upload_id = $(this).prev('input');
		var home_url = $(this).data('url');
		formfield=$('#default_image').attr('name');
		tb_show('Upload Image', home_url+'/wp-admin/media-upload.php?type=image&TB_iframe=1');
		return false;
	});
	window.send_to_editor = function( html ) 
	{
		imgurl = $( 'img',html ).attr( 'src' );
		url = $(html).find('img').attr('src');
		if(typeof url == 'undefined')
			url = $(html).attr('src'); 
		upload_id.val(url)
		tb_remove();
	}
});