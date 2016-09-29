<?php 
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if(isset($_POST["image"]))
{
	 $site_images = $_POST["image"];
	 foreach($site_images as $key=>$site_image)
	 {
	 	update_blog_option($key,'site_image',$site_image);
	 }	 
}
unset($ced_sites[0]);
?>
<h2 class="title"><?php _e('Upload Logos :', 'ced-multisite-list')?></h2>
<form method = "post">
  	<table class="form-table fh-profile-upload-options ced-multi-shop">
   		<tr class="section_body">
   			<td>
   				<b><?php _e('SITES', 'ced-multisite-list')?></b>
   			</td>
   			<td>
   				<b><?php _e('LOGO', 'ced-multisite-list')?></b>
   			</td>
   		</tr>
   		<?php 
   		
   			$blog_id = $val['blog_id'];
   			$box_value =  get_blog_option($blog_id,'site_image',null);
   			$blog_details = get_blog_details($blog_id);
   			$blog_details = (array)$blog_details;
   		?>
   		<tr class="section_body">
   			<td>
   				<p><b><?php _e('Name', 'ced-multisite-list')?> : <?php echo $blog_details['blogname']?></b></p><br/>
   				<p><b><?php _e('Path', 'ced-multisite-list')?> : <?php echo $blog_details['path']?></b></p><br/>
   				<input type="text" name="image[<?php echo $blog_id;?>]" id="image" class="regular-text" value ="<?php echo $box_value; ?>"/>
   				<a href="javascript:void(0)" class="button-primary ceduploadimage" data-url="<?php echo get_site_url ($blog_id);?>"/><?php _e('Upload Image','ced-multisite-list'); ?></a><br />
   				<span class="description"><?php _e('Please upload an image','ced-multisite-list')?> </span>
   			</td>
   			<?php 
   			if(!empty($box_value))
   			{
   			?>
   				<td>
   					<img src="<?php echo $box_value; ?>" width="100px">
   				</td>
   			<?php 
   			}
   			else 
   			{
   			?>
   				 <td>
   				 	<img src="<?php echo home_url(); ?>/wp-content/plugins/multisite-store-viewer/assets/images/default.jpg" width="100px">
   				 </td>
   			<?php 
   			}	
   			?>
   		</tr>
   		<tr>
   			<td>
   				<input type="submit" class="button-primary" name="save_setting" id="image" value="Save"/>
   			</td>
   		</tr>
   	</table>
</form>	