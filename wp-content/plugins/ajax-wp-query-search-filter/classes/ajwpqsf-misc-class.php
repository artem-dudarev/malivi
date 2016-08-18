<?php
if(!class_exists('ajaxwpqsfclass')){
	class ajaxwpqsfclass{
		function __construct(){
				// admin add taxonomy ajax
				add_action( 'wp_ajax_awpqsfTaxo_ajax', array( $this,'awpqsfTaxo_ajax') );  
				// admin add meta fields ajax
				add_action( 'wp_ajax_awpqsfCmf_ajax', array( $this,'awpqsfCmf_ajax') ); 
		}
		function awpqsfTaxo_ajax(){
				include AWQSFPLUG . '/admin/addTaxoAjax.php';
		}
	
		function awpqsfCmf_ajax(){
			include AWQSFPLUG . '/admin/addCmfAjax.php';
		} 
		function awpqsf_taxo_fields(){
			$fields =  array('checkbox' => 'Check Box' ,'dropdown' => 'Drop Down','radio' => 'Radio');
			$output = apply_filters( 'ajax_wpqsftaxo_field', $fields );
			return $output;
		}
		
		function awpqsf_cmf_fields(){
			$fields =  array('checkbox' => 'Check Box' ,'dropdown' => 'Drop Down','radio' => 'Radio');
			$output = apply_filters( 'ajax_wpqsfcmf_field', $fields );
			return $output;
		}
				
		function cmf_compare(){
			 $campares = array( '1' => '=', '2' =>'!=', '3' =>'>', '4' =>'>=', '5' =>'<', '6' =>'<=', '7' =>'LIKE', '8' =>'NOT LIKE', '9' =>'IN', '10' =>'NOT IN', '11' =>'BETWEEN', '12' =>'NOT BETWEEN','13' => 'NOT EXISTS');	
			 return apply_filters( 'ajax_cmf_compare',  $campares );
		}
		
		function get_all_metakeys(){
		global $wpdb;
		$table = $wpdb->prefix.'postmeta';
		$keys = $wpdb->get_results( "SELECT meta_key FROM $table GROUP BY meta_key",ARRAY_A);

		foreach($keys as $key){
			if($key['meta_key']=='ajaxwpqsf-cpt' || $key['meta_key']=='ajaxwpqsf-taxo' || $key['meta_key']=='ajaxwpqsf-relbool' || $key['meta_key']=='ajaxwpqsf-cmf'){
			}
			else{
				$meta_keys[] = 	$key['meta_key'];		 
				}
		}
		return $meta_keys;
	}
	
	function get_all_metavalue($metakey){
		global $wpdb;
		$table = $wpdb->prefix.'postmeta';
		$values = $wpdb->get_results( "SELECT meta_value FROM $table WHERE meta_key = '$metakey' GROUP BY meta_value", ARRAY_A);
		foreach($values as $value){
			 $metavalue[] = $value['meta_value'].'::'.$value['meta_value']; 
			}
		
		return $metavalue;
	}
	
	function ajwpqsf_theme(){
			$theme = array(
				array(
					'name' => __('Default Theme','AjWPQSF'),
					'themeid' => 'default',
					'link' => plugins_url( '/scripts/default.css', __FILE__ ) ,
					'id'   => 'awpqsf_id',
					'class' => 'awpqsf_class',
					'thumb' => plugins_url( '/scripts/default.png', __FILE__ )
					)
			);
			$output = apply_filters( 'ajwpqsf_theme_opt', $theme );
			return $output;
		}
		
	function awpqsf_theme_val(){
		global $wpdb;
		$table = $wpdb->prefix.'postmeta';
		$values = $wpdb->get_results( "SELECT meta_value FROM $table WHERE meta_key = 'ajaxwpqsf-theme' GROUP BY meta_value", ARRAY_A);
		if($values){
		foreach($values as $value){
			$extract = explode('|', $value['meta_value']);
			$return[] = $extract[0];
		  }
		  return  $return; 
		}
	    
	  }
	  
	 function output_formtaxo_fields($type,$exc,$hide,$taxname,$taxlabel,$taxall,$c,$divclass, $formid){
		$eid = explode(",", $exc);
		$args = array('hide_empty'=>$hide,'exclude'=>$eid );	
		$taxoargs = apply_filters('ajwpqsf_taxonomy_arg',$args,$formid);
        	$terms = get_terms($taxname,$taxoargs);
 	    $count = count($terms);
		 if($type == 'dropdown'){
			$html  = '<div class="'.$divclass.' taxdropdown-'.$c.'"><label class="tax-label-'.$c.'">'.$taxlabel.'</label>';
			$html .= '<input  type="hidden" name="taxo['.$c.'][name]" value="'.$taxname.'">';
			$html .=  '<select id="taxselect-'.$c.'" name="taxo['.$c.'][term]">'; 
			if(!empty($taxall)){
				$html .= '<option selected value="awpqsftaxoall">'.$taxall.'</option>';
			}
					if ( $count > 0 ){
						foreach ( $terms as $term ) {							
					$html .= '<option value="'.$term->slug.'">'.$term->name.'</option>';}
			}				
			$html .= '</select><br>';
			$html .= '</div>';
			return  apply_filters( 'ajwpqs_tax_field_dropdown', $html ,$type,$exc,$hide,$taxname,$taxlabel,$taxall,$c,$divclass,$formid);
		}
		if($type == 'checkbox'){
 			if ( $count > 0 ){
				$html  = '<div class="'.$divclass.' taxcheckbox-'.$c.' togglecheck"><label class="tax-label-'.$c.'">'.$taxlabel.'</label>';
				$html .= '<input  type="hidden" name="taxo['.$c.'][name]" value="'.$taxname.'">';
				if(!empty($taxall)){
				$html .= '<label class="taxchklabel"><input type="checkbox" id="taxcheckid-'.$c.'" name="taxo['.$c.'][call]" class="awpsfcheckall" >'.$taxall.'</label>';
				}
				foreach ( $terms as $term ) {
				$value = $term->slug;
				$html .= '<label class="taxchklabel"><input type="checkbox" id="taxcheckid-'.$c.'" name="taxo['.$c.'][term][]" value="'.$value.'" >'.$term->name.'</label>';
				}
				$html .= '<br></div>';
				return  apply_filters( 'ajwpqs_tax_field_checkbox', $html ,$type,$exc,$hide,$taxname,$taxlabel,$taxall,$c,$divclass,$formid);
			}
 			
		}
		if($type == 'radio'){
 			if ( $count > 0 ){
				$html  = '<div class="'.$divclass.' taxradio-'.$c.'"><label class="tax-label-'.$c.'">'.$taxlabel.'</label>';
				$html .= '<input  type="hidden" name="taxo['.$c.'][name]" value="'.$taxname.'">';
				if(!empty($taxall)){
				$html .= '<label class="taxrdlabel"><input type="radio" id="taxradioid-'.$c.'" name="taxo['.$c.'][term]" value="awpqsftaxoall">'.$taxall.'</label>';
				}
			foreach ( $terms as $term ) {
				$html .= '<label class="taxrdlabel"><input type="radio" id="taxradioid-'.$c.'" name="taxo['.$c.'][term]" value="'.$term->slug.'">'.$term->name.'</label>';
			}

				$html .= '<br>';
				$html .= '</div>';
				return  apply_filters( 'ajwpqs_tax_field_radio', $html ,$type,$exc,$hide,$taxname,$taxlabel,$taxall,$c,$divclass,$formid);
			}
 			
		}
		 if($type != 'dropdown' or $type != 'checkbox' or $type != 'radio') {
			return apply_filters( 'ajwpqs_addtax_field_'.$type.'', $type,$exc,$hide,$taxname,$taxlabel,$taxall,$c,$divclass,$formid);
	
		}
		
		
	 }
	
	 function output_formcmf_fields($type,$metakey,$compare,$metaval,$label,$all,$i,$divclass,$formid){
		 $opts = explode("|", $metaval);
		
		 if($type == 'dropdown'){
				$html = '<div class="'.$divclass.' cmfddwon-'.$i.'"><label class="tcmf-label-'.$i.'">'.$label.'</label>';
				$html .= '<input type="hidden" name="cmf['.$i.'][metakey]" value="'.$metakey.'">';
				$html .= '<input type="hidden" name="cmf['.$i.'][compare]" value="'.$compare.'">';
				$html .=  '<select id="cmf-'.$i.'" name="cmf['.$i.'][value]">'; 
				if(!empty($all)){
				$html .= '<option value="awpqsfcmfall">'.$all.'</option>';
				}
				
					foreach ( $opts as $opt ) {
							 $val = explode('::',$opt);
							$html .= '<option value="'.$val[0].'">'.$val[1].'</option>';
					}
				$html .= '</select><br>';
				$html .= '</div>';
				
				return  apply_filters( 'ajwpqs_cmf_field_dropdown', $html,$type,$metakey,$compare,$metaval,$label,$all,$i,$divclass,$formid);
			
			}
		 if($type == 'checkbox'){
			     $html  = '<div class="'.$divclass.' cmfcheckbox-'.$i.' togglecheck"><label class="tcmf-label-'.$i.'">'.$label.'</label>';
				 $html .= '<input type="hidden" name="cmf['.$i.'][metakey]" value="'.$metakey.'">';
				 $html .= '<input type="hidden" name="cmf['.$i.'][compare]" value="'.$compare.'">';
				if(!empty($all)){
				 $html .= '<label class="cmfchklabel"><input type="checkbox" id="cmf-'.$i.'" name="cmf['.$i.'][call]" class="awpsfcheckall" >'.$all.'</label>';
				}				
				foreach ( $opts as $opt ) {
						 $val = explode('::',$opt);
				$html .= '<label class="cmfchklabel"><input type="checkbox" id="cmf-'.$i.'" name="cmf['.$i.'][value][]" value="'.$val[0].'" >'.$val[1].'</label>';	
					}
			 	$html .= '<br></div>';
				
				return  apply_filters( 'ajwpqs_cmf_field_checkbox', $html,$type,$metakey,$compare,$metaval,$label,$all,$i,$divclass,$formid);
		 }	
		 if($type == 'radio'){
			    $html  = '<div class="'.$divclass.' cmfradio-'.$i.'"><label class="tcmf-label-'.$i.'">'.$label.'</label>';
				$html .= '<input type="hidden" name="cmf['.$i.'][metakey]" value="'.$metakey.'">';
				$html .= '<input type="hidden" name="cmf['.$i.'][compare]" value="'.$compare.'">';
			if(!empty($all)){
        	   		 $html .= '<label class="cmfrdlabel"><input type="radio" id="cmf-'.$i.'" name="cmf['.$i.'][value]" value="awpqsfcmfall">'.$all.'</label>';
			}
		
			foreach ( $opts as $opt ) {
				 $val = explode('::',$opt);
				$html .= '<label class="cmfrdlabel"><input type="radio" id="cmf-'.$i.'" name="cmf['.$i.'][value]" value="'.$val[0].'" >'.$val[1].'</label>';	
			} 
				$html .= '<br></div>';
				
				return  apply_filters( 'ajwpqs_cmf_field_radio', $html,$type,$metakey,$compare,$metaval,$label,$all,$i,$divclass,$formid);
		 }
		if($type != 'dropdown' or $type != 'checkbox' or $type != 'radio') {
			return apply_filters( 'ajwpqs_addcmf_field_'.$type.'', $type,$metakey,$compare,$metaval,$label,$all,$i,$divclass,$formid);
	
		}  	
		 
	  }
	 
	function ajax_pagination($pagenumber, $pages = '', $range = 4, $id)
	{
		 $showitems = ($range * 2)+1;  
	 
		 $paged = $pagenumber;
		 if(empty($paged)) $paged = 1;

		 if($pages == '')
		 {
			 
			 global $wp_query;
			 $pages = $query->max_num_pages;
			 
			 if(!$pages)
			 {
				 $pages = 1;
			 }
		 }   
	 
		 if(1 != $pages)
		 {
			  $html = "<div class=\"ajaxsfpagi\">  ";  
			  $html .= '<input type="hidden" id="curform" value="#ajax_wpqsffrom_'.$id.'">';
			  //<span>Page ".$paged." of ".$pages."</span>";
			 if($paged > 2 && $paged > $range+1 && $showitems < $pages) 
			 $html .= '<a id="1" class="pagievent" href="#">&laquo; '.__("First","AjWPQSF").'</a>';
			 $previous = $paged - 1;
			 if($paged > 1 && $showitems < $pages) $html .= '<a id="'.$previous.'" class="pagievent" href="#">&lsaquo; '.__("Previous","AjWPQSF").'</a>';
	 
			 for ($i=1; $i <= $pages; $i++)
			 {
				 if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
				 {
					 $html .= ($paged == $i)? '<span class="pagicurrent">'.$i.'</span>': '<a id="'.$i.'" href="#" class="pagievent inactive">'.$i.'</a>';
				 }
			 }
				
			 if ($paged < $pages && $showitems < $pages){
			 $next = $paged + 1;
			 $html .= '<a id="'.$next.'" class="pagievent"  href="#">'.__("Next","AjWPQSF").' &rsaquo;</a>';}
			 if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) {
			 $html .= '<a id="'.$pages.'" class="pagievent"  href="#">'.__("Last","AjWPQSF").' &raquo;</a>';}
			 $html .= "</div>\n";$max_num_pages = $pages;
			 return apply_filters('ajwpqsf_pagination',$html,$max_num_pages,$pagenumber,$id);
		 }
		 
		 
	}
	 
	}//end of class
}
global $awpqsfapi;
$awpqsfapi = new ajaxwpqsfclass();
?>
