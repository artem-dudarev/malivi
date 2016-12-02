<?php 
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ced_sites = wp_get_sites();

$upd_arr = array();
if(isset($_POST["image"]))
{
	 $site_images = $_POST["image"];
	 foreach($site_images as $key=>$site_image)
	 {
	 	update_blog_option($key,'site_image',$site_image);
	 }	
 	 
	 if(!empty($ced_sites)){
	 	foreach ($ced_sites as $val){
	 		if(!in_array($val['blog_id'], $upd_arr)){
	 			update_blog_option($val['blog_id'],'hidesite','No');
	 		}
	 	}
	 }
}
if(isset($_POST["hidesite"]))
{
	$hidesites = $_POST["hidesite"];
	foreach($hidesites as $key1=>$hidesite)
	{
		$upd_arr[] = $key1;
	}
	update_site_option('hidesite',$upd_arr);
}
else{
	update_site_option('hidesite',array());
}
?>
<form method = "post">
	<h2>Custom Settings:</h2>
	
   	<table class="form-table fh-profile-upload-options ced-multi-shop">
		<tr id="attr_filter">
	   		<td colspan="4">
	   			<h3 class="title"><?php _e('Upload Logos :', 'ced-multisite-list')?></h3>
	   			 <span class='ced_mul_setting'><b>* </b><?php _e('Upload the logo for your site.','ced-multisite-list')?> </span><br/>
	   			 <span class='ced_mul_setting'><b>* </b><?php _e('Activated the default logo for the site which doesn\'t holding any logo.','ced-multisite-list')?> </span><br>
	   		</td>
   		</tr>
   		<tr class="section_body">
   			<td width="10%"><b><?php _e('Hide Site', 'ced-multisite-list')?></b></td>
   			<td width="20%"><b><?php _e('SITES', 'ced-multisite-list')?></b></td>
   			<td width="40%" style="text-align:center;"><b><?php _e('Upload Logo', 'ced-multisite-list')?></b></td>
   			<td width="30%" style="text-align:center;"><b><?php _e('Logo', 'ced-multisite-list')?></b></td>
		</tr>
		
   		<?php 
   		if(isset($ced_sites))
   		{
   			$checkbox_value =  get_site_option('hidesite');
   			foreach($ced_sites as $key=>$val)
   			{
   				$blog_id = $val['blog_id'];
   				$box_value =  get_blog_option($blog_id,'site_image',null);
   				$default_img =  get_site_option('default_image');
   				$blog_details = get_blog_details($blog_id);
   				$blog_details = (array)$blog_details;
   				
   				if(in_array($blog_id, $checkbox_value))
   				{
   					$checked = "checked";
   				}
   				else
   				{
   					$checked = '';
   				}
   				?>
		   				
   				<tr class="section_body">
	   				<td><input type="checkbox" <?php echo $checked;?> value="yes" id = "hidesite" name="hidesite[<?php echo $blog_id;?>]" ></td>
		   			<td><b> <?php echo $blog_details['blogname']?></b></td>
		   			<td>
	   					<input type="text" name="image[<?php echo $blog_id;?>]" id="image" class="regular-text" value ="<?php echo $box_value; ?>"/>
	   					<a href="javascript:void(0)" class="button-primary ceduploadimage" data-url="<?php echo get_site_url ($blog_id);?>"/><?php _e('Upload Image','ced-multisite-list'); ?></a>
		   			</td>
	   				<?php 
	   				if(!empty($box_value))
	   				{
	   				?>
	   					
	   					<td><img src="<?php echo $box_value; ?>" width ="100px"></td>
	   				<?php 
	   				}
	   				else
	   				{
   					?>
   						<td><img src="<?php echo home_url(); ?>/wp-content/plugins/multisite-store-viewer/assets/images/default.jpg" width="100px"></td>
   					<?php
	   				}	
	   				?>
	   			</tr>
   				<?php 
   			}
   		}
   		?>
   		<tr>
   			<td>
		   		<input type="submit" class="button-primary" name="save_setting" id="image" value="Save"/>
		   	</td>
	   	</tr>
	</table>
</form>	