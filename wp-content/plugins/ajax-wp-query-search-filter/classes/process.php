<?php
if(!class_exists('awpqsfprocess')){
	class awpqsfprocess{
	
		function __construct(){
			//script
			add_action( 'wp_enqueue_scripts', array($this, 'front_js') );
			//front ajax
			add_action( 'wp_ajax_nopriv_awpqsf_ajax', array($this, 'awpqsf_ajax') );
			add_action( 'wp_ajax_awpqsf_ajax', array($this, 'awpqsf_ajax') );
		}
		
		
		function front_js(){
			$variables = array(
				'url' => admin_url( 'admin-ajax.php' ),
			);
			$themeopt = new ajaxwpqsfclass();	
			$themeops = $themeopt->ajwpqsf_theme();
			$themenames= $themeopt->awpqsf_theme_val();
			if(isset($themenames)){
			foreach($themeops as $k){
				if(in_array($k['themeid'],$themenames) ){
						wp_register_style( $k['themeid'], $k['link'], array(),  'all' );
						wp_enqueue_style( $k['themeid'] );
				}
			}
			wp_enqueue_script(  'awpqsfscript', plugins_url( '/scripts/awpqsfscript.js', __FILE__ ) , array('jquery'), '1.0', true);
           		 wp_localize_script('awpqsfscript', 'ajax', $variables);
		   }
			
			
		}
		
		
		function get_ajaxwqsf_cmf($id, $getcmf){
			$options = get_post_meta($id, 'ajaxwpqsf-relbool', true);
			$cmfrel = isset($options[0]['cmf']) ? $options[0]['cmf'] : 'AND';
			
			if(isset($getcmf)){
				$cmf=array('relation' => $cmfrel,'');
				foreach($getcmf as  $v){
				   $classapi = new ajaxwpqsfclass();	
					$campares = $classapi->cmf_compare();//avoid tags stripped 
					if(!empty($v['value'])){
					if($v['value'] == 'awpqsfcmfall'){
							$cmf[] = array(
								'key' => strip_tags( stripslashes($v['metakey'])),
								'value' => 'get_all_cmf_except_me',
								'compare' => '!='
						);
						  
						}
					elseif( $v['compare'] == '11'){
						$range = explode("-", strip_tags( stripslashes($v['value'])));
						$cmf[] = array(
								'key' => strip_tags( stripslashes($v['metakey'])),
								'value' => $range,
								'type' => 'numeric',
								'compare' => 'BETWEEN'
						);
					  
					  }
					  elseif( $v['compare'] == '12'){
						$range = explode("-", strip_tags( stripslashes($v['value'])));
						$cmf[] = array(
								'key' => strip_tags( stripslashes($v['metakey'])),
								'value' => $range,
								'type' => 'numeric',
								'compare' => 'NOT BETWEEN'
						);
					  
					  }elseif( $v['compare'] == '9' || $v['compare'] == '10' ){
						foreach($campares as $ckey => $cval)
							{  if($ckey == $v['compare'] ){ $safec = $cval;}        }
							$trimmed_array=array_map('trim',$v['value']);
						//implode(',',$v['value'])
						$cmf[] = array(
								'key' => strip_tags( stripslashes($v['metakey'])),
								'value' =>$trimmed_array,
								'compare' => $safec 
						);
					  
					  }elseif( $v['compare'] == '3' || $v['compare'] == '4' || $v['compare'] == '5' || $v['compare'] == '6'){
							
							foreach($campares as $ckey => $cval)
							{  if($ckey == $v['compare'] ){ $safec = $cval;}        }
							
							$cmf[] = array(
							'key' => strip_tags( stripslashes($v['metakey'])),
								'value' => strip_tags( stripslashes($v['value'])),
								'type' => 'DECIMAL',
								'compare' => $safec
							);
						}elseif($v['compare'] == '1' || $v['compare'] == '2' || $v['compare'] == '7' || $v['compare'] == '8' || $v['compare'] == '13'){
								
								foreach($campares as $ckey => $cval)
								{  if($ckey == $v['compare'] ){ $safec = $cval;}        }
								
								$cmf[] = array(
								'key' => strip_tags( stripslashes($v['metakey'])),
								'value' => strip_tags( stripslashes($v['value'])),
								'compare' => $safec
							);
						}
						
					   }//end isset
					}//end foreach
						$output =  apply_filters( 'ajwpqsf_get_cmf', $cmf,$id, $getcmf );
						unset($output[0]);
						return $output;				
						
				}
	
	   }
	   
	   function get_ajaxwqsf_taxo($id, $gettaxo){
			global $wp_query;
		    $options = get_post_meta($id, 'ajaxwpqsf-relbool', true);
			$taxrel = isset($options[0]['tax']) ? $options[0]['tax'] : 'AND';
			if(!empty($gettaxo)){
						
				$taxo=array('relation' => $taxrel,'');
				foreach($gettaxo as  $v){
				   if(!empty($v['term']))	{	
					if( $v['term'] == 'awpqsftaxoall'){
					  $taxo[] = array(
							'taxonomy' => strip_tags( stripslashes($v['name'])),
							'field' => 'slug',
							'terms' => strip_tags( stripslashes($v['term'])),
							'operator' => 'NOT IN'
						);
					  
					  }
					elseif(is_array($v['term'])){
					 $taxo[] = array(
							'taxonomy' =>  strip_tags( stripslashes($v['name'])),
							'field' => 'slug',
							'terms' =>$v['term']
						);
					}
					else{
				  
					$taxo[] = array(
							'taxonomy' => strip_tags( stripslashes($v['name'])),
							'field' => 'slug',
							'terms' => strip_tags( stripslashes($v['term']))
						);
					}
				   }
				 } //end foreach
					$output = apply_filters( 'ajwpqsf_get_taxo', $taxo,$id, $gettaxo );	
					unset($output[0]);
					return $output;				
					
			}
			
		}	
		
			
		
		function awpqsf_ajax(){
			$postdata =parse_str($_POST['getdata'], $getdata);
			$taxo = $getdata['taxo'];
			$cmf = (isset($getdata['cmf']) && !empty($getdata['cmf'])) ? $getdata['cmf'] : null;
			$formid = $getdata['aformid'];
			$nonce =  $getdata['s'];
			$pagenumber = isset($_POST['pagenum']) ? $_POST['pagenum'] : null;
			
			if(isset($formid) && wp_verify_nonce($nonce, 'ajaxwpsfsearch')){
					$id = absint($formid);
					$options = get_post_meta($id, 'ajaxwpqsf-relbool', true);
					$cpts = get_post_meta($id, 'ajaxwpqsf-cpt', true);
					$pagenumber = isset($_POST['pagenum']) ? $_POST['pagenum'] : null;
					$default_number = get_option('posts_per_page');
					
					$cpt        = !empty($cpts) ? $cpts : 'any';
					$ordermeta  = !empty($options[0]['smetekey']) ? $options[0]['smetekey'] : null;
					$ordervalue = (!empty($options[0]['otype']) && $ordermeta) ? $options[0]['otype'] : null;
					$order      = !empty($options[0]['sorder']) ? $options[0]['sorder'] : null;
					$number      = !empty($options[0]['resultc']) ? $options[0]['resultc'] : $default_number;
					//$paged = ( $pagenumber ) ? $pagenumber : 1;
					$keyword = !empty($getdata['skeyword']) ?	 sanitize_text_field($getdata['skeyword']) : null;
					$get_tax = $this->get_ajaxwqsf_taxo($id, $taxo);
					$get_meta = $this->get_ajaxwqsf_cmf($id, $cmf);
					$tax_query = isset($get_tax) && empty($keyword) ? 	$get_tax : null;
					$meta_query = isset($get_meta) && empty($keyword) ? $get_meta : null;    

					$ordermeta  = apply_filters('ajwpsf_ometa_query',$ordermeta,$getdata,$id);
					$ordervalue = apply_filters('ajwpsf_ometa_type',$ordervalue,$getdata,$id);
					$order 	    = apply_filters('ajwpsf_order_query',$order,$getdata,$id);	
					$number     = apply_filters('ajwpsf_pnum_query',$number,$getdata,$id);	
					
					$tax_query =  apply_filters('barg_tax_query', $tax_query , $get_tax, $keyword);
					$meta_query = apply_filters('barg_meta_query', $meta_query , $get_meta, $keyword);	
				
					$args = array(
								'post_type' => $cpt,
								'post_status' => 'publish',
								'meta_key'=> $ordermeta,
								'orderby' => $ordervalue,
								'order' => $order, 
								'paged'=> $pagenumber,
								'posts_per_page' => $number,
								'meta_query' => $meta_query,						
								'tax_query' => $tax_query,
								's' => esc_html($keyword),
								
							);
							
					$arg = apply_filters( 'ajax_wpqsf_query', $args, $id);		
							//include 'result.php';
						
					    
				
						$results =  $this->ajax_result($arg, $id,$pagenumber);
						$result = apply_filters( 'ajax_wpqsf_reoutput',$results , $arg, $id, $getdata );	
					
						echo $result;
							
							
				}else{ echo 'no naughty busisness here';}
	    	die;
    	  }//end ajax
	
	   function ajax_result($arg, $id,$pagenumber){
		   $apiclass = new ajaxwpqsfclass();	
		  // The Query
			$query = new WP_Query( $arg );
			$html ='';
			// The Loop
		if ( $query->have_posts() ) {
			$html .= '<h1>'.__('Search Results :', 'AjWPQSF' ).'</h1>';
			while ( $query->have_posts() ) {
				$query->the_post();
					$html .= '<article><header class="entry-header">'.get_the_post_thumbnail().'';
					$html .= '<h1 class="entry-title"><a href="'.get_permalink().'" rel="bookmark">'.get_the_title().'</a></h1>';
					$html .= '</header>';
					$html .= '<div class="entry-summary">'.get_the_excerpt().'</div></article>';
				
			}
			
			$html .= $apiclass->ajax_pagination($pagenumber,$query->max_num_pages, 4, $id);
		 } else {
					$html .= __( 'Nothing Found', 'AjWPQSF' );
				}
				/* Restore original Post Data */
				wp_reset_postdata();
				
			return $html;
			
		
	   }	
	
	}//end class
	
}// end if class
global $awpqsfprocess;
$awpqsfprocess = new awpqsfprocess();
?>
