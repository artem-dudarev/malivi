<div class="toggle"><span><?php echo esc_html( __( 'Taxonomy', 'AjWPQSF' ) ); ?></span><div class="plus"></div></div>
<div class="content">
<?php 
$post_id = $_GET['aformid'] ? $_GET['aformid'] : null;
$items = array("AND", "OR");
	echo '<span>'.__("Boolean relationship between the taxonomy queries", "AjWPQSF").'</span><br>';
	foreach($items as $item) {
		$bool = get_post_meta($post_id, 'ajaxwpqsf-relbool', true);
		
		$checked = !empty($bool[0]['tax']) && ($bool[0]['tax']==$item) ? 'checked="checked"' : '';
		echo '<label><input id = "taxrel" '.$checked.' value="'.$item.'" name="Ajxqf[rel][0][tax]" type="radio" />'.$item.'</label>';
	}	
	
	echo '<ul><i><li class="desc">'.__("AND - Must meet all taxonomy search.","AjWPQSF").'</li>';
	echo '<li class="desc">'.__("OR - Either one of the taxonomy search is meet.","AjWPQSF").'</li></i></ul>';
	
?>
<div class="formbutton"><input alt="#TB_inline?width=550&height=650&amp;inlineId=addTaxoForm" title="Add Taxonomy" class="thickbox button-secondary" type="button" value="<?php _e("Add Taxonomy",'AjWPQSF') ;?>" /></div>  
	
	<table id="taxo_table" class="widefat">
    			<thead>
				<tr>
				<th><?php _e('Taxonomy','AjWPQSF'); ?></th>
				<th><?php _e('Label','AjWPQSF'); ?></th>
				<th><?php _e('"Search All" Text','AjWPQSF'); ?></th>
				<th><?php _e('Hide Empty?','AjWPQSF'); ?></th>
				<th><?php _e('Exculde ID','AjWPQSF'); ?></th>
				<th><?php _e('Display Type','AjWPQSF'); ?></th>
				<th><?php _e('Remove?','AjWPQSF'); ?></th>
				</tr>
			</thead>
		 
			<tfoot>
				<tr>
				<th><?php _e('Taxonomy','AjWPQSF'); ?></th>
				<th><?php _e('Label','AjWPQSF'); ?></th>
				<th><?php _e('"Search All" Text','AjWPQSF'); ?></th>
				<th><?php _e('Hide Empty?','AjWPQSF'); ?></th>
				<th><?php _e('Exculde ID','AjWPQSF'); ?></th>
				<th><?php _e('Display Type','AjWPQSF'); ?></th>
				<th><?php _e('Remove?','AjWPQSF'); ?></th>
				</tr>
			</tfoot>
			
				
	<tbody id="sortable"  class="taxbody">
	<?php $html = '<br>';
	$taxo = get_post_meta($post_id, 'ajaxwpqsf-taxo', true);
	if(!empty($taxo)){
		$c =0; 
		$args=array('public'   => true, '_builtin' => false); 
		$output = 'names'; // or objects
		$operator = 'and'; // 'and' or 'or'
		$taxonomies=get_taxonomies($args,$output,$operator); 
		foreach($taxo as $k => $v){
				$html .= '<tr>';
				$html .=  '<td><input type="hidden" id="taxcounter" name="taxcounter" value="'.$c.'"/>';
				//for display taxonomy
			
				$html .= '<select id="taxo" name="Ajxqf[taxo]['.$c.'][taxname]">';
				$catselect = ($v['taxname']== 'category') ? 'selected="selected"' : '';
				$html .= '<option value="category" '.$catselect.'>'.__("category","AjWPQSF").'</option>';
					foreach ($taxonomies  as $taxonomy ) {
				$selected = ($v['taxname']==$taxonomy) ? 'selected="selected"' : '';		
				$html .= '<option value="'.$taxonomy.'" '.$selected.'>'.$taxonomy.'</option>';
						}
				$html .= '</select><br></td>';
				//for label
				$html .=  '<td>';
				$html .= '<input type="text" id="taxlabel" name="Ajxqf[taxo]['.$c.'][taxlabel]" value="'.sanitize_text_field($v['taxlabel']).'"/>';
				$html .= '<br></td>';
				//search all text
				$html .=  '<td>';
				$html .= '<input type="text" id="taxall" name="Ajxqf[taxo]['.$c.'][taxall]" value="'.sanitize_text_field($v['taxall']).'"/>';
				$html .= '<br></td>';
				//hide empty
				$html .= '<td>';
				
				$check1="";
				$check0="";
				if($v['hide'] == 1){$check1 = 'checked="checked"'; };
				if($v['hide'] == 0){$check0 = 'checked="checked"'; };
				$html .= '<label><input '.$check1.' type="radio" id="taxhide" name="Ajxqf[taxo]['.$c.'][hide]" value="1"/>Yes</label>';
				$html .= '<label><input '.$check0.' type="radio" id="taxhide" name="Ajxqf[taxo]['.$c.'][hide]" value="0"/>No</label>'; 
				$html .= '<br></td>';
				//exlude id
				$html .= '<td><input type="text" id="taxexculde" name="Ajxqf[taxo]['.$c.'][exc]" value="'.sanitize_text_field($v['exc']).'"/></td>';

				//dispay type
				$html .= '<td>';
				$taxofields = new ajaxwpqsfclass();	
				$feilds = 	$taxofields->awpqsf_taxo_fields();
				
				foreach($feilds as $val  => $key ){
					$checked = ($v['type']== $val) ?  'checked="checked"' : '';
					$html .= '<label><input type="radio" id="taxtype" name="Ajxqf[taxo]['.$c.'][type]" value="'.$val.'" '.$checked.'/>'.$key.'</label><br>';
				} 
			
	   			$html .= '<br></td>';
				//action
				$html .= '<td><span class="remove_row button-secondary">'.__("Remove","AjWPQSF").'</span><br></td>';
				$html .= '</tr>';
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
