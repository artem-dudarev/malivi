<?php
$nonce = wp_create_nonce  ('ajaxwpsfsearch');
$taxo = get_post_meta($id, 'ajaxwpqsf-taxo', true);
$cmf = get_post_meta($id, 'ajaxwpqsf-cmf', true);
$options = get_post_meta($id, 'ajaxwpqsf-relbool', true);
$css = get_post_meta($id, 'ajaxwpqsf-theme', true);
$excss = explode('|', $css);
$divid = ($excss[2]) ? $excss[2] : 'awpqsf_id';
$divclass = ($excss[3]) ? $excss[3] : 'awpqsf_class';
$apiclass = new ajaxwpqsfclass();

echo '<div id="'.$divid.'">';
if($formtitle){
echo '<div class="form_title">'.get_the_title($id).'</div>';}
echo '<form id="ajax_wpqsffrom_'.$id.'" >';
echo '<input type="hidden" name="s" value="'.$nonce.'" /><input type="hidden" name="aformid" value="'.$id.'">';
echo '<input type="hidden" id="jaxbtn" value="'.$options[0]['div'].'">';
	 do_action( 'awpqsf_form_top', $atts);
if(!empty($taxo)){
	$c = 0;
	foreach($taxo as $k => $v){
		$eid = explode(",", $v['exc']);
		$args = array('hide_empty'=>$v['hide'],'exclude'=>$eid );	
        $terms = get_terms($v['taxname'],$args);
 	    $count = count($terms);
		echo $apiclass->output_formtaxo_fields($v['type'],$v['exc'],$v['hide'],$v['taxname'],$v['taxlabel'],$v['taxall'],$c,$divclass,$id );
	     
	$c++;			
  }
}

if(!empty($cmf)){  
   $i=0;
    foreach($cmf as $k => $v){
		if(isset($v['type'])){
			echo $apiclass->output_formcmf_fields($v['type'],$v['metakey'],$v['compare'],$v['opt'],$v['label'],$v['all'],$i,$divclass,$id);
		 $i++;
	   }	
	}
}

if(isset($options[0]['strchk']) && ($options[0]['strchk'] == '1') ){
		$stext  = '<div class="'.$divclass.'"><label class="'.$divclass.'-keyword">'.$options[0]['strlabel'].'</label>';
		$stext .= '<input id="'.$divid.'_key" type="text" name="skeyword" class="awpqsftext" value="" />';
        $stext .= '<br></div>';
        $textsearch =  apply_filters('ajaxwpqsf_string_search',$stext, $id,$divid,$divclass,$options);
        echo $textsearch;
}

	do_action( 'awpqsf_form_bottom' , $atts);
$html = '<div class="'.$divclass.' awpqsf_submit"><input type="button" id="'.$divid.'_btn" value="'.$options[0]['button'].'" alt="[Submit]" class="searchbtn" /></div>';
$button = apply_filters('ajaxwpsqf_form_btn', $html, $id,$divclass,$divid,$options[0]['button'] );
echo $button;
echo '</form>';


echo '</div>';


?>
