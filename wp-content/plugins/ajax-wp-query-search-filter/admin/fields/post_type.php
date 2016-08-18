<div class="toggle"><span><?php echo esc_html( __( 'Post Type', 'AjWPQSF' ) ); ?></span><div class="plus"></div></div>
<div class="content">
<?php

	  echo '<label>'.__("Choose the post type you want to include in the search","AjWPQSF").'</label><br>';
			$post_types=get_post_types('','names'); 
			unset($post_types['revision']); unset($post_types['attachment']);unset($post_types['nav_menu_item']);unset($post_types['jaxwpsf']);
			$post_id = isset($_GET['aformid']) ? $_GET['aformid'] : null;
			
			$oldcpts = get_post_meta($post_id, 'ajaxwpqsf-cpt', true);
			
			foreach($post_types as $post_type ) {
			    $checked = null;		
			   
			    if(!empty($oldcpts)){
				  foreach ($oldcpts as $checkedtyped)
				   {
					if($checkedtyped == $post_type)  $checked = 'checked="checked"';   
				   }
			     }
			  
			  
			  echo '<div class="ajwpqsf_cpt_div"><input '.$checked.' id="cpt" name="Ajxqf[cpt][]" type="checkbox" value="'.$post_type.'" />'.$post_type.'</div>';
			
			}
	echo '<div class="clear"></div>';	
?>
</div>
<br><br>
