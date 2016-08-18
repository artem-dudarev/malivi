<?php
/*
Plugin Name: Ajax WP Query Search Filter
Plugin URI: http://www.9-sec.com/
Description: This plugin let you using wp_query to filter taxonomy,custom meta and post type and the result showed by Ajax calling.
Version: 1.0.7
Author: TC 
Author URI: http://www.9-sec.com/
*/
/*  Copyright 2012 TCK (email: devildai@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if ( ! defined( 'AWQSFPLUG' ) )
	define( 'AWQSFPLUG', untrailingslashit( dirname( __FILE__ ) ) );
if ( ! defined( 'ADMINPURL' ) )
	define( 'ADMINPURL', admin_url('?page=ajaxwpqsf') );	

include_once('classes/list-table-class.php');
include_once('classes/ajwpqsf-misc-class.php');
include_once('classes/process.php');
if(!class_exists('ajaxwpqsf')){
	
class ajaxwpqsf{
	const post_type = 'jaxwpsf';
	
	function __construct(){
		
		add_action( 'init' , array( $this,'Ajaxwpqsf_setting' ) );
		add_action('admin_menu', array($this,'ajaxwpqsf_menu'));	
	
		//save form
		add_action('admin_init', array($this,'ajwpqsf_save_from'));
		
	}
	
	function ajaxwpqsf_menu() {
		$plugin_page = add_menu_page(__("Ajax Wp Query Search Filter","AjWPQSF"),__("Ajax WPQSF","AjWPQSF"),'manage_options','ajaxwpqsf', array($this,'Ajwoqsf_page'));
		add_action('admin_print_scripts-'.$plugin_page, array($this,'admin_ajwpqsf_js'));
		add_action('admin_print_styles-'.$plugin_page, array($this,'admin_awpqsf_css'));
	
	}
	
	function Ajwoqsf_page() {
		if(!isset($_GET['aformid']) && !isset($_GET['aformaction']) ){
			global $awqsfmain;
			$awqsfmain = new Ajwpqsf_Table;
			$awqsfmain->prepare_items(); 
			$addlink = add_query_arg(array('aformid' => 'new', 'aformaction' => 'new'), ADMINPURL);
			echo '<div class="wrap"><div id="icon-options-general" class="icon32"></div><h2>'.esc_html( __( 'Ajax WP Query Search Filter', 'AjWPQSF' ) ).'<a href="'.$addlink.'" class="add-new-h2">'.esc_html( __( 'Add New Search Form', 'AjWPQSF' ) ).'</a></h2>'; 
			echo '<form method="post">'; $awqsfmain->display(); echo '</form></div>'; 
		}
		if(isset($_GET['aformid']) && isset($_GET['faormaction']) && $_GET['aformaction']=='new' && $_GET['aformaction']=='new'){
			
			require_once AWQSFPLUG . '/admin/add-new.php';
		}
		if(isset($_GET['aformid']) && isset($_GET['aformaction'])){
			$post_id = $_GET['aformid'];
			require_once AWQSFPLUG . '/admin/add-new.php';
		}
	}
	
	function admin_ajwpqsf_js(){
		wp_enqueue_script('thickbox',null,array('jquery'));
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('js', plugins_url('admin/scripts/admin_awqsfjs.js', __FILE__), array('jquery'), '1.0', true);
		wp_localize_script( 'ajax-request', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) ); 
		
	}
	function admin_awpqsf_css(){
		wp_enqueue_style('awqsfcss', plugins_url('admin/scripts/admin_awpqsf.css', __FILE__), '1.0', true);
		wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');

	}
	
	function Ajaxwpqsf_setting() {
		register_post_type( self::post_type, array(
			'labels' => array(
				'name' => __( 'Ajax WPQSF', 'AjWPQSF' ),
				'singular_name' => __( 'Map AWPSF', 'AjWPQSF' ) ),
  			'exclude_from_search'=>true,
			'publicly_queryable'=>false,
			'rewrite' => false,
			'query_var' => false ) );
		load_plugin_textdomain( 'AjWPQSF', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		add_shortcode('AjaxWPQSF', array($this, 'Aj_wpqsf_shortcode'));
	}
	
	
	function ajwpqsf_save_from() {
		 if(isset($_POST['ajxwpqsfsub'])){

		if (! wp_verify_nonce($_POST['nonce'], 'ajax-wpqsf-edit') ) {
			$this->handle_error()->add('nonce', __("No naughty business here, dude", 'AjWPQSF'));
			return; 
		}


		$postid =sanitize_text_field($_POST['aformid']);
		$themeoption = $cptarray = $taxoarray = $cmfarray =$relarray ='';
			if(!empty($_POST['Ajxqf']['cpt'])){
				foreach($_POST['Ajxqf']['cpt'] as $cv){
						$cptarray[] = sanitize_text_field($cv);
					
				}
			}
			if(isset($_POST['Ajxqf']['taxo'])){
				
				foreach($_POST['Ajxqf']['taxo'] as $tv){
					$taxoarray[]=array(
							'taxname' => sanitize_text_field($tv['taxname']),
							'taxlabel'=> sanitize_text_field($tv['taxlabel']),
							'taxall' => sanitize_text_field($tv['taxall']),
							'hide' => sanitize_text_field($tv['hide']),
							'exc' => sanitize_text_field($tv['exc']),
							'type' => sanitize_text_field($tv['type'])
						);
					
					
					}
			}

			if(isset($_POST['Ajxqf']['cmf'])){
				foreach($_POST['Ajxqf']['cmf'] as $cv){
						$cmfarray[] = array(
							'metakey' => sanitize_text_field($cv['metakey']),
							'label' => sanitize_text_field($cv['label']),
							'all' => sanitize_text_field($cv['all']),
							'compare' => wp_filter_nohtml_kses( stripslashes($cv['compare'])),
							'type' => sanitize_text_field($cv['type']),
							'opt' => sanitize_text_field($cv['opt'])
						);
					
					}
			}

		
				foreach($_POST['Ajxqf']['rel'] as $rv){
						$relarray[] = array(
							'tax'=> isset($rv['tax']) ? sanitize_text_field($rv['tax']) : '',
							'cmf'=> isset($rv['cmf']) ? sanitize_text_field($rv['cmf']) : '',
							'strchk'=> isset($rv['strchk']) ? sanitize_text_field($rv['strchk']) : '',
							'strlabel'=> $rv['strlabel'],
							'smetekey'=> $rv['smetekey'],
							'otype'=> isset($rv['otype']) ? sanitize_text_field($rv['otype']) : '',
							'sorder'=> isset($rv['sorder']) ? sanitize_text_field($rv['sorder']) : '',
							'button'=> $rv['button'],
							'resultc'=> $rv['resultc'],
							'div'=> $rv['div']
						);
					}
					
		 if($_POST['themeopt']){
			 $themeoption = $_POST['themeopt'];
		 }			
		
		 if($postid == 'new'){

				$post_information = array(
					'post_title' => sanitize_text_field($_POST['ftitle']) ,
					'post_type' => self::post_type,
					'post_status' => 'publish'
					);
				$newform_id = wp_insert_post($post_information);
				if(empty($newform_id)){
					$this->handle_error()->add('insert', __("Error! Try to create again.", 'AjWPQSF'));
					return; 
					
				}
				if(!empty($cptarray) ){
				update_post_meta($newform_id, 'ajaxwpqsf-cpt', $cptarray);}
				if(!empty($taxoarray)){
				update_post_meta($newform_id, 'ajaxwpqsf-taxo', $taxoarray);}
				if(!empty($cmfarray)){
				update_post_meta($newform_id, 'ajaxwpqsf-cmf', $cmfarray);}
				if(!empty($relarray)){
				update_post_meta($newform_id, 'ajaxwpqsf-relbool', $relarray);}
				if(!empty($themeoption)){
				update_post_meta($newform_id, 'ajaxwpqsf-theme', $themeoption);}
								
				$returnlink = add_query_arg(array('aformid' => $newform_id, 'aformaction' => 'edit','status'=>'success'), ADMINPURL);
				wp_redirect( $returnlink ); exit;
		}//end add new


		if($postid != 'new' && absint($postid) ){

			 $updateform = array();
 			 $updateform['ID'] = $postid ;
 			 $updateform['post_title'] = sanitize_text_field($_POST['ftitle']);
			$update = wp_update_post( $updateform );
			if(empty($update)){
					$this->handle_error()->add('update', __("Error! Something wrong when updating your setting.", 'AjWPQSF'));
					return; 
					
				}
			
				$oldcpt = get_post_meta($post_id, 'ajaxwpqsf-cpt', true);
				$oldtaxo = get_post_meta($post_id, 'ajaxwpqsf-taxo', true);
				$oldcmf = get_post_meta($post_id, 'ajaxwpqsf-cmf', true);	
				$oldrel = get_post_meta($post_id, 'ajaxwpqsf-relbool', true);
				$oldthemeopt = get_post_meta($post_id, 'ajaxwpqsf-theme', true);
				
				$newcpt = !empty($cptarray) ? $cptarray : '';
				$newtaxo = !empty($taxoarray) ? $taxoarray : '';
				$newcmf = !empty($cmfarray) ? $cmfarray : '';
				$newrel = !empty($relarray) ? $relarray : '';
				$newthemeopt = !empty($themeoption) ? $themeoption : '';

				if (empty($newcpt)) {
				delete_post_meta($postid, 'ajaxwpqsf-cpt', $oldcpt);	
				
				} elseif($oldcpt != $newcpt) {
				update_post_meta($postid, 'ajaxwpqsf-cpt', $newcpt);
				}
				
				if (empty($newtaxo)) {
				delete_post_meta($postid, 'ajaxwpqsf-taxo', $oldtaxo);
				
				} elseif($newtaxo != $oldtaxo) {
				update_post_meta($postid, 'ajaxwpqsf-taxo', $newtaxo);
				}

				if (empty($newcmf)) {
				delete_post_meta($postid, 'ajaxwpqsf-cmf', $oldcmf);
				} elseif ($newcmf != $oldcmf) {
				update_post_meta($postid, 'ajaxwpqsf-cmf', $newcmf);
				}	
				
				
				if (empty($newrel)) {
				delete_post_meta($postid, 'ajaxwpqsf-relbool', $oldrel);
				} elseif ($newrel != $oldrel) {
				update_post_meta($postid, 'ajaxwpqsf-relbool', $newrel);
				}
				
				if($newthemeopt != $oldthemeopt) {
				update_post_meta($postid, 'ajaxwpqsf-theme', $newthemeopt);
				}
				
				$returnlink = add_query_arg(array('aformid' => $postid, 'aformaction' => 'edit','status'=>'updated'), ADMINPURL);
				wp_redirect( $returnlink ); exit;

			
		 }//end update
		
	   }//end submit
		
	}
	
	function Aj_wpqsf_shortcode($atts) {
		extract(shortcode_atts(array('id' => false, 'formtitle' =>1), $atts));
		if($id)
		{
			 ob_start();
			 $output = include AWQSFPLUG . '/html/searchform.php';
			 $output = ob_get_clean();
			 return $output;
		}
		else{
			echo 'no form added.';
		}
	}
	
		
		
 }
}
add_filter('widget_text', 'do_shortcode');
global $ajaxwpqsf;
$ajaxwpqsf = new ajaxwpqsf();
?>
