<?php
   $html = '<div class="toggle"><span>'.__("Form's Theme","AjWPQSF").'</span><div class="plus"></div></div>';
	
	$themes = get_post_meta($post_id, 'ajaxwpqsf-theme', true);
	$extheme ='';	
	$themeopt = new ajaxwpqsfclass();	
	$themeops = $themeopt->ajwpqsf_theme();
	if(!empty($themes)){
	$extheme = explode('|', $themes);}
	$html .= '<div class="content">';
	$c=1;	
	foreach($themeops as $k){
		$default = (empty($extheme) && $k['themeid']=='default') ? 'checked="checked"' : '';
		
		$checked = (!empty($extheme) && $k['themeid'] ==  $extheme[0]) ? 'checked="checked"' : '';
			
	
	 	 $value = $k['themeid'].'|'.$k['link'].'|'.$k['id'].'|'.$k['class'];
		 $html .= '<label class="ptheme">';
		 $html .= '<input type="radio" name="themeopt" value="'.$value.'" '.$checked.'  '.$default.'">'.$k['name'].'<br>';
		 $html .= '<a href="#TB_inline?width=600&height=600&inlineId=themethumb_'.$c.'" class="thickbox">'.__(" Preview","AjWPQSF").'</a>';  
		 $html .= '<div class="clear"></div></label>';
		 $html .= '<div style="display:none"><div id="themethumb_'.$c.'"><img src="'.$k['thumb'].'"></div></div>';
		 
		$c++;		 
	}
	
	$html .= '<div class="clear"></div></div><br><br>';	
	echo $html;

?>
