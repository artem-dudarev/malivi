<?php 
		$taxo = $_POST['taxo'];
		$label = sanitize_text_field($_POST['label']);
		$text = sanitize_text_field($_POST['text']);
		$hide =$_POST['hide'];
		$exclude = $_POST['excl'];
		$type = $_POST['type'];
		$c = $_POST['counter'];
			$args=array(
	  'public'   => true,
	  '_builtin' => false
	  
	); 
	$output = 'names'; // or objects
	$operator = 'and'; // 'and' or 'or'
	$taxonomies=get_taxonomies($args,$output,$operator); 
		
		$html = '<tr style="background:#BEF781"><td><input type="hidden" id="taxcounter" name="counter" value="'.$c.'"/>';
		$html .= '<select id="taxo" name="Ajxqf[taxo]['.$c.'][taxname]">';
				$catselect = ($taxo == 'category') ? 'selected="selected"' : '';
		$html .= '<option value="category" '.$catselect.'>'.__("category","WQSF").'</option>';
				foreach ($taxonomies  as $taxonomy ) {
				$selected = ($taxo==$taxonomy) ? 'selected="selected"' : '';		
		$html .= '<option value="'.$taxonomy.'" '.$selected.'>'.$taxonomy.'</option>';
						}
		$html .= '</select><br></td>';
        
		$html .=  '<td>';
		$html .= '<input type="text" id="taxlabel" name="Ajxqf[taxo]['.$c.'][taxlabel]" value="'.$label.'"/>';
		$html .= '<br></td>';
       
		$html .=  '<td>';
		$html .= '<input type="text" id="taxall" name="Ajxqf[taxo]['.$c.'][taxall]" value="'.$text.'"/>';
		$html .= '<br></td>';
				
	    $check1="";
		$check0="";
		if($hide == 1){$check1 = 'checked="checked"'; }
		elseif($hide == 0){$check0 = 'checked="checked"'; };
	    $html .= '<td>';
		$html .= '<label><input '.$check1.' type="radio" id="taxhide" name="Ajxqf[taxo]['.$c.'][hide]" value="1"/>Yes</label>';
		$html .= '<label><input '.$check0.' type="radio" id="taxhide" name="Ajxqf[taxo]['.$c.'][hide]" value="0"/>No</label>'; 
		$html .= '<br></td>';
		$html .= '<td><input type="text" id="taxexculde" name="Ajxqf[taxo]['.$c.'][exc]" value="'.$exclude.'"/></td>';

				
		$html .= '<td>';
		$taxofields = new ajaxwpqsfclass();	
		$feilds = 	$taxofields->awpqsf_taxo_fields();
			foreach($feilds as $val  => $key ){
				$checked = ($type== $val) ?  'checked="checked"' : '';
				$html .= '<label><input  type="radio" id="taxtype" name="Ajxqf[taxo]['.$c.'][type]" '.$checked.' value="'.$val.'"/>'.sprintf(__('%s', 'AjWPQSF'), $key).'</label><br>';
			}
		
	
	   	$html .= '<br></td>';
	   $html .= '<td><span class="remove_row button-secondary">'.__("Remove","WQFS").'</span></td></tr>';
		echo $html;exit;

?>
