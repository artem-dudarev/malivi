<div class="toggle"><span><?php echo esc_html( __( 'Custom Meta Field', 'AjWPQSF' ) ); ?></span><div class="plus"></div></div>
<div class="content">
<?php
	$bool = get_post_meta($post_id, 'ajaxwpqsf-relbool', true);
	$items = array("AND", "OR");
	echo '<span>'.__("Boolean relationship between the meta queries", "AjWPQSF").'</span><br>';
	foreach($items as $item) {
		
		$checked = !empty($bool[0]['cmf']) && ($bool[0]['cmf']==$item) ? 'checked="checked"' : '';
		echo '<label><input id = "cmfrel" '.$checked.' value="'.$item.'" name="Ajxqf[rel][0][cmf]" type="radio" />'.$item.'</label>';
	}	
	
		echo '<ul><i><li class="desc">'.__("AND - Must meet all meta field search.","AjWPQSF").'</li>';
		echo '<li class="desc">'.__("OR - Either one of the meta field search is meet.","AjWPQSF").'</li></i></ul>';
				
?>

	<div class="formbutton">
	<input alt="#TB_inline?width=550&height=650&inlineId=addCmfForm" title="Add Custom Meta Field" class="thickbox button-secondary" type="button" value="<?php _e("Add Custom Meta",'AjWPQSF') ;?>" />
	</div>  
   	<table id="cmf_table" class="widefat">
	
			<thead>
				<tr>
				<th><?php _e('Meta key','AjWPQSF'); ?></th>
				<th><?php _e('Label','AjWPQSF'); ?></th>
				<th><?php _e('"Search All" Text','AjWPQSF'); ?></th>
				<th><?php _e('Compare','AjWPQSF'); ?></th>
				<th><?php _e('Options','AjWPQSF'); ?></th>
				<th><?php _e('Display Type','AjWPQSF'); ?></th>
				<th><?php _e('Remove?','AjWPQSF'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
				<th><?php _e('Meta key','AjWPQSF'); ?></th>
				<th><?php _e('Label','AjWPQSF'); ?></th>
				<th><?php _e('"Search All" Text','AjWPQSF'); ?></th>
				<th><?php _e('Compare','AjWPQSF'); ?></th>
				<th><?php _e('Options','AjWPQSF'); ?></th>
				<th><?php _e('Display Type','AjWPQSF'); ?></th>
				<th><?php _e('Remove?','AjWPQSF'); ?></th>
				</tr>
			</tfoot>	
			
			<tbody id="sortable2" class="cmfbody">
			 <?php 	 $html = '<br>';
			$cmf = get_post_meta($post_id, 'ajaxwpqsf-cmf', true);
			$classapi = new ajaxwpqsfclass();	
			$campares = $classapi->cmf_compare();	
		    $c =0; 
			if(!empty($cmf)){
			  	foreach($cmf as $k => $v){
					$html .= '<tr>';
					$html .=  '<td><input type="hidden" id="cmfcounter" name="cmfcounter" value="'.$c.'"/>';//counter
					//for custom meta key
					$awpqsfkeys = new ajaxwpqsfclass();
					$keys = $awpqsfkeys->get_all_metakeys();
					$html .= '<select id="cmfkey" name="Ajxqf[cmf]['.$c.'][metakey]">';
						foreach($keys as $key){
								$selected = ($v['metakey']==$key) ? 'selected="selected"' : '';	
								$html .= '<option value="'.$key.'" '.$selected.'>'.$key.'</option>';		
							}	
					$html .= '</select><br></td>';
					//for Label
					$html .=  '<td>';
					$html .= '<input type="text" id="cmflabel" name="Ajxqf[cmf]['.$c.'][label]" value="'.sanitize_text_field($v['label']).'"/>';
					$html .= '<br></td>';
					//search all text
					$html .=  '<td>';
					$html .= '<input type="text" id="cmfalltext" name="Ajxqf[cmf]['.$c.'][all]" value="'.sanitize_text_field($v['all']).'"/>';
					$html .= '<br></td>';
					//for compare
					$html .=  '<td>';
					$html .= '<select id="cmfcom" name="Ajxqf[cmf]['.$c.'][compare]">';
						foreach ($campares  as $ckey => $cvalue ) {
						$selected = ($v['compare']==$ckey) ? 'selected="selected"' : '';	
					$html .= '<option value="'.$ckey.'" '.$selected.'>'.$cvalue.'</option>';}
					$html .= '</select><br></td>';
					
					//for options
					$html .=  '<td>';
					
					$html .= '<textarea id="cmflabel" name="Ajxqf[cmf]['.$c.'][opt]" >'.esc_html($v['opt']).'</textarea>';
					$html .= '</td>';

					//display type

					//dispay type
					$html .= '<td>';
				
					$ftypes = $classapi->awpqsf_cmf_fields();
					foreach($ftypes as $mv  => $mk ){
						$checked = ($v['type']== $mv) ?  'checked="checked"' : '';
						
						$html .= '<label><input type="radio" id="taxtype" name="Ajxqf[cmf]['.$c.'][type]" value="'. $mv.'" '.$checked.' />'.sprintf(__('%s', 'AjWPQSF'),$mk).'</label><br>'; 
					}
				
	   				$html .= '<br></td>'; 
					
				    $html .= '<td><span class="remove_row button-secondary">'.__("Remove","AjWPQSF").'</span></td></tr>';
				  $c++; 
				}
				
				
			 }
			 	echo $html; 
			 ?>
			</tbody>
	
	</table>
<span class="drag"><?php _e('*Drag and Drop to reorder your table row. The table row order indicates the order of the search form fields in the frontend. ','AjWPQSF') ;?></span>	

</div>
<br><br>
