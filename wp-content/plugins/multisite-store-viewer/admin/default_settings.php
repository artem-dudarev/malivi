<?php 
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
//$all_sites = wp_get_sites();

//echo "<pre>";print_r($all_sites);die;
if(isset($_POST['save_setting']))
{
	
	$existing_details = get_site_option( 'selected_attribute', false );
	//echo "<pre>";print_r($existing_details);die;
	$blog_data_checkboxes = array( 'public', 'archived', 'spam', 'mature', 'deleted' );
	foreach ( $blog_data_checkboxes as $c ) {
		if ( ! in_array( $existing_details->$c, array( 0, 1 ) ) ) {
			$blog_data[ $c ] = $existing_details->$c;
		} else {
			$blog_data[ $c ] = isset( $_POST['blog'][ $c ] ) ? 1 : 0;
		}
	}
	update_site_option( 'selected_attribute', $blog_data );
	if(isset($_POST["custom_image_size_width"]))
	{
		$site_default_images = $_POST["custom_image_size_width"];
	
		update_site_option('custom_image_size_width',$site_default_images);
	
	}
	if(isset($_POST["custom_image_size_height"]))
	{
		$site_default_images = $_POST["custom_image_size_height"];
	
		update_site_option('custom_image_size_height',$site_default_images);
	}
	if(isset($_POST["default_image"]))
	{ 
		$box_default_value =  get_site_option('default_image');
		$site_default_images = $_POST["default_image"];
		update_site_option('default_image',$site_default_images);
	}
	if(isset($_POST["ced_multi_enable_search"]))
	{
		$enable_search = $_POST["ced_multi_enable_search"];
		update_site_option('ced_multi_enable_search',$enable_search);
	}
	else 
	{
		update_site_option('ced_multi_enable_search','');
	}
	if(isset($_POST["ced_multi_exclude_site"]))
	{
		$enable_search = $_POST["ced_multi_exclude_site"];
		update_site_option('ced_multi_exclude_site',$enable_search);
	}
	else
	{
		update_site_option('ced_multi_exclude_site','');
	}
	
	
	$order_by = $_POST["site_list_order"];
	update_site_option('site_list_order',$order_by);
}
$enable_search = get_site_option('ced_multi_enable_search');
$order_by_value = get_site_option('site_list_order');
if($enable_search == 'yes')
{
	$checked = "checked";
}
else 
{
	$checked = '';
}	

$exclude_site = get_site_option('ced_multi_exclude_site');

if($exclude_site == 'yes')
{
	$ex_checked = "checked";
}
else
{
	$ex_checked = '';
}

$details = get_site_option( 'selected_attribute', false );
//echo "<pre>";print_r($details);//die;
?>
<form method = "post">
	<h2 class="title"><?php _e('Default Settings', 'ced-multisite-list')?></h2>
	<table class="form-table">
	   	<tr class="section_body">
	   		<td width="30%">
		   		<b class="title"><?php _e('Select order to list the sites', 'ced-multisite-list')?> : </h2>
		   </td>
		   <td >
	   			<select name="site_list_order" id= "site_list_order">
				  <option value="None" <?php  if($order_by_value=="None"): echo 'Selected="selected"';endif;?>>None</option>
				  <option value="Name" <?php  if($order_by_value=="Name"): echo 'Selected="selected"';endif;?>>Name</option>
				  <option value="ID" <?php  if($order_by_value=="ID"): echo 'Selected="selected"';endif;?>>ID</option>
				  <option value="Recently Added" <?php  if($order_by_value=="Recently Added"): echo 'Selected="selected"';endif;?>>Recently Added</option>
				</select><br/>
				<span class="description "><?php _e('This setting let you to select the listing order of sites to display.','ced-multisite-list')?> </span>
	   		</td>
	   		<td></td>
	   	</tr>
	
	<?php 
	   	$blog_id = $val['blog_id'];
	   	$box_default_value =  get_site_option('default_image');
	   	$blog_details = get_blog_details($blog_id);
	   	$blog_details = (array)$blog_details;
	   	$custom_image_size_width = get_site_option('custom_image_size_width');
	   	$custom_image_size_height = get_site_option('custom_image_size_height');
	 ?> 
	 	<tr class="section_body">
			<td width="30%">
				<b class="title"><?php _e('Default Logo', 'ced-multisite-list')?> : </h2>
			</td>
	   		<td>
	   			<input type="text" size= "30" name="default_image" id="default_image" class="regular-text"  value ="<?php echo $box_default_value; ?>"/>
				<a href="javascript:void(0)" class="button-primary ceddefaultuploadimage" data-url="<?php echo get_site_url ($blog_id);?>"/><?php _e('Upload Image','ced-multisite-list'); ?></a><br />
				<span class="description "><?php _e('This default logo is liable when any site is not holding any logo.','ced-multisite-list')?></span>
			</td>
	  <?php 
	  		if(!empty($box_default_value))
	   		{
		?>		<td>
		  			<img src="<?php echo $box_default_value; ?>" width="100px">
		  		</td>
	   <?php 
	   		}
	   		else 
	   		{
	   	?>	   	<td>
	   				<img src="<?php echo home_url(); ?>/wp-content/plugins/multisite-store-viewer/assets/images/default.jpg" width="100px">
	   			</td>
	   <?php 
	   		}	
	   ?></tr>
	
		<tr class="section_body">
	   		<td width="30%">
	   			<b><?php _e('Size of logo appearing on listing', 'ced-multisite-list')?> :  </h2>
		   	</td>
			<td>	
				<span class="description "><?php _e('150px * 150px (Default)','ced-multisite-list')?> </span>
			</td>
			<td></td>
	   	</tr>
		<tr class="section_body">
		   	<td>
	   			<b class="title"><?php _e('Custom', 'ced-multisite-list')?> : <span class="default_span"></span></h2>
	   		</td>
	   		<td>
				<input type="text" name="custom_image_size_width" id="custom_image_size_width" size="9" value ="<?php echo $custom_image_size_width; ?>"/>
			   	*
			   	<input type="text" name="custom_image_size_height" id="custom_image_size_height" size="9" value ="<?php echo $custom_image_size_height; ?>"/>
			   	<span class="description"><?php _e('Enter the size in pixels','ced-multisite-list')?> </span>
			</td>
			<td></td>
	   	</tr>
	
		<tr class="section_body">
			<td width="30%">
				<b class="title"><?php _e('Disable Search', 'ced-multisite-list')?> : </h2>
			</td>
			<td>
				<input type="checkbox" <?php echo $checked?> value="yes" name="ced_multi_enable_search">
			</td>
			<td></td>
		</tr>
		<tr class="section_body">
			<td width="30%">
				<b class="title"><?php _e('Exclude Current Site from Listing', 'ced-multisite-list')?> : </h2>
			</td>
			<td>
				<input type="checkbox" <?php echo $ex_checked?> value="yes" name="ced_multi_exclude_site">
			</td>
			<td></td>
		</tr>
		<?php
		$attribute_fields = array( 'public' => __( 'Public' ) );
		if ( ! $is_main_site ) {
			$attribute_fields['archived'] = __( 'Archived' );
			$attribute_fields['spam']     = _x( 'Spam', 'site' );
			$attribute_fields['deleted']  = __( 'Deleted' );
		}
		$attribute_fields['mature'] = __( 'Mature' );
		?>
		<tr class="section_body">
			<td>
				<b class="title"><?php _e('Exclude sites according to attributes', 'ced-multisite-list')?> : </h2>
			</th>
			<td>
			<fieldset>
			<legend class="screen-reader-text"><?php _e( 'Set site attributes' ) ?></legend>
			<?php foreach ( $attribute_fields as $field_key => $field_label ) : ?>
				<label><input type="checkbox" name="blog[<?php echo $field_key; ?>]" value="1" <?php checked( (bool) $details[$field_key], true ); disabled( ! in_array( $details[$field_key], array( 0, 1 ) ) ); ?> />
				<?php echo $field_label; ?></label><br/>
			<?php endforeach; ?>
			<fieldset>
			</td>
			<td></td>
		</tr>
		<tr>
			<td>
				<input type="submit" class="button-primary" name="save_setting" id="image" value="Save"/>
			</td>
		</tr>
	</table>
</form>	