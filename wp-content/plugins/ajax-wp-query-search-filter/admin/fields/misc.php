<?php
	$misc = get_post_meta($post_id, 'ajaxwpqsf-relbool', true);
	
	$string = !empty($misc[0]['strchk']) && (sanitize_text_field($misc[0]['strchk']) == '1') ? 'checked="checked"' : null;
	$slabel = !empty($misc[0]['strlabel']) ? sanitize_text_field($misc[0]['strlabel']) : __("Search by Keyword","AjWPQSF");
	$meta = !empty($misc[0]['smetekey']) ? sanitize_text_field($misc[0]['smetekey']) : null;
	$word =  !empty($misc[0]['otype']) && ($misc[0]['otype'] == 'meta_value' )  ? 'checked="checked"' : null;
	$number =  !empty($misc[0]['otype']) && ($misc[0]['otype'] == 'meta_value_num' )  ? 'checked="checked"' : null;
	$desc = !empty($misc[0]['sorder']) && ($misc[0]['sorder'] == 'DESC' )  ? 'checked="checked"' : null; 
	$asc = !empty($misc[0]['sorder']) && ($misc[0]['sorder'] == 'ASC' )  ? 'checked="checked"' : null; 
	$button = !empty($misc[0]['button']) ? sanitize_text_field($misc[0]['button']) : 'Search'; 
	$ajaxdiv = !empty($misc[0]['div']) ? sanitize_text_field($misc[0]['div']) : '#content'; 
	$defualtresult = get_option('posts_per_page');
	$result = !empty($misc[0]['resultc']) ? sanitize_text_field($misc[0]['resultc']) : $defualtresult; 
	
	$html = "";
	$html .= '<div class="toggle"><span>'.__("Result Setting and Others","AjWPQSF").'</span><div class="plus"></div></div>';
	$html .= '<div class="content">';	
	$html .= '<h3>'.__("Add String Search?","AjWPQSF").'</h3>';
	$html.= '<p><input '.$string.'  name="Ajxqf[rel][0][strchk]" type="checkbox" value="1" />'.__("Enabling string search","AjWPQSF").'<br>';
    $html.= '<span class="desciption">'.__("This will add string search in the form. Note that when user using this to search, the taxonomy and custom meta filter that defined above will not be used. However, the search will still go through the post type defined above.","AjWPQSF").'</span><br>';
    $html.= '<p><label>'.__("Label for string search.","AjWPQSF").':</label><br>';
    $html.= '<input type="text"  name="Ajxqf[rel][0][strlabel]" id="stringlabel" value="'.$slabel.'" /><br>';
   
   


    $html .= '<h3>'.__("Result Page Setting","AjWPQSF").'</h3>';
    $html.= '<p><label>'.__("Sorting Meta Key.","AjWPQSF").':</label><br>';
    $allkeys = new ajaxwpqsfclass();
	$keys = $allkeys->get_all_metakeys();
    $html .= '<select name="Ajxqf[rel][0][smetekey]"><option value=""></option>';
	foreach($keys as $key){
		$selected = ($meta==$key) ? 'selected="selected"' : '';	
    $html .= '<option value="'.$key.'" '.$selected.'>'.$key.'</option>';		
	}		

    $html .=  '</select><br>';	
    $html.= '<span class="desciption">'.__("Insert the meta key that will be used for the result sorting. Leave empty will using the default 'date' value for sorting.","AjWPQSF").'</span></p>';
    
    $html.= '<p><label>'.__("Meta Value Type","AjWPQSF").':</label><br>';
    $html.= '<label><input '.$word.' type="radio" id="taxhide" name="Ajxqf[rel][0][otype]" value="meta_value"/>'.__("Words","AjWPQSF").'</label>';
	$html.= '<label><input '.$number.' type="radio" id="taxhide" name="Ajxqf[rel][0][otype]" value="meta_value_num"/>'.__("Numeric", "AjWPQSF").'</label>';
    $html.= '<br><span class="desciption">'.__("What is the meta value type of the sorting meta key? eg. sorting meta key = 'price', then the meta value type should be numeric. Leave it blank if your sorting meta key is empty.","AjWPQSF").'</span></p>';
    
    $html.= '<p><label>'.__("Sorting Order","AjWPQSF").':</label><br>';
    $html.= '<label><input '.$desc.' type="radio" id="taxhide" name="Ajxqf[rel][0][sorder]" value="DESC"/>'.__("Descending","AjWPQSF").'</label>';
	$html.= '<label><input '.$asc.' type="radio" id="taxhide" name="Ajxqf[rel][0][sorder]" value="ASC"/>'.__("Ascending","AjWPQSF").'</label><br>';
    $html.= '<span class="desciption">'.__("The search result sorting order. Default is descending","AjWPQSF").'</span></p>';
    
    $html.= '<p><label>'.__("Results per Page","AjWPQSF").':</label>';
    $html.= '<input type="text" id="numberpost" name="Ajxqf[rel][0][resultc]" value="'.$result.'" size="2"/><br>';
    $html.= '<span class="desciption">'.__("How many posts shows at each result page?","AjWPQSF").'</span></p>';

    $html.= '<p><label>'.__("Search Button Text","AjWPQSF").':</label>';
    $html.= '<input type="text" id="numberpost" name="Ajxqf[rel][0][button]" value="'.$button.'" /><br>';
    $html.= '<span class="desciption">'.__("The text of the submit button?","AjWPQSF").'</span></p>';	
    
    $html.= '<p><label>'.__("Div to display result","AjWPQSF").':</label>';
    $html.= '<input type="text" id="numberpost" name="Ajxqf[rel][0][div]" value="'.$ajaxdiv.'" /><br>';
    $html.= '<span class="desciption">'.__("The Div id/class of where you want the result to display. eg. #content, .content (<b>Must have the '#' or '.' in front of the div name!</b>)","AjWPQSF").'</span></p>';	
    $html.= '</div><br><br>';
    echo $html;

?>
