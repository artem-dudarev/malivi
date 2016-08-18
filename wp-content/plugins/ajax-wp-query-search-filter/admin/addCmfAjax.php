<?php 
		$type = $_POST['type'];
		$metakey = isset($_POST['key']) ? sanitize_text_field($_POST['key']) : '';
		$label = isset($_POST['metalabel']) ? sanitize_text_field($_POST['metalabel']) : '';
		$all = isset($_POST['all']) ? sanitize_text_field($_POST['all']) : '';
		$com = isset($_POST['compare']) ? sanitize_text_field($_POST['compare']) : '';
		$check = isset($_POST['check']) ? sanitize_text_field($_POST['check']) : '';
		$option =isset($_POST['opt']) ? sanitize_text_field($_POST['opt']) : '';
		$c = isset($_POST['cmfcounter']) ? sanitize_text_field($_POST['cmfcounter']) : '';
		$ajwpqsfapi = new ajaxwpqsfclass();	
		$campares = $ajwpqsfapi->cmf_compare();
		
		$html ='';
		if($type == 'form'){
		$html .= '<tr style="background:#BEF781"><td><input type="hidden" id="cmfcounter" name="cmfcounter" value="'.$c.'"/>';
		$html .= '<select id="cmfkey" name="Ajxqf[cmf]['.$c.'][metakey]">';
			$allmetakeys = $ajwpqsfapi->get_all_metakeys();
			foreach($allmetakeys as $key){
				$selected = ($metakey==$key) ? 'selected="selected"' : '';	
					$html .= '<option value="'.$key.'" '.$selected.'>'.$key.'</option>';		
				}	
		$html .= '</select><br></td>';
		
		$html .=  '<td>';
		$html .= '<input type="text" id="cmflabel" name="Ajxqf[cmf]['.$c.'][label]" value="'.$label.'"/>';
		$html .= '<br></td>';
		
		$html .=  '<td>';
		$html .= '<input type="text" id="cmfalltext" name="Ajxqf[cmf]['.$c.'][all]" value="'.$all.'"/>';
		$html .= '<br></td>';
		
		$html .=  '<td>';
		$html .= '<select id="cmfcom" name="Ajxqf[cmf]['.$c.'][compare]">';
				foreach ($campares  as $ckey => $cvalue  ) {
				$selected = ($com==$ckey) ? 'selected="selected"' : '';	
		$html .= '<option value="'.$ckey.'" '.$selected.'>'.$cvalue.'</option>';}
		$html .= '</select><br></td>';
		
	        $html .= '<td><textarea id="cmflabel" name="Ajxqf[cmf]['.$c.'][opt]" >'.$option.'</textarea></td>';

		$html .= '<td>';
		$ftypes = $ajwpqsfapi->awpqsf_cmf_fields();
		foreach($ftypes as $mv  => $mk ){
			$checked = ($check== $mv) ?  'checked="checked"' : '';
		 $html .= '<label><input type="radio" id="taxtype" name="Ajxqf[cmf]['.$c.'][type]" value="'.$mv.'" '.$checked.' />'.sprintf(__('%s', 'AjWPQSF'),$mk).'</label><br>';
			
		}
		$html .= '<br></td>'; 
		
		$html .= '<td><span class="remove_row button-secondary">'.__("Remove","AjWPQSF").'</span></td></tr>';

		}
	if($type == 'meta'){
	
		$values = $ajwpqsfapi->get_all_metavalue($metakey);
		$html .= implode(" | ", $values);
	}

       	echo $html;
	exit;

?>
