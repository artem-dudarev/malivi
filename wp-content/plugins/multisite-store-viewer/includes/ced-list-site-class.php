<?php
/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if( ! class_exists( 'Ced_multisite_list' ) )
{
	class Ced_multisite_list
	{
		/**
		 * This is a class constructor Where all actions and filters are defined'.
		 * @name __construct()
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		public function __construct()
		{ 
			add_action( 'network_admin_menu', array($this, 'ced_network_msl_configuration_setting'));
			add_action( 'admin_menu', array($this, 'ced_msl_configuration_setting'));
			add_shortcode('CED_MULTISITE_LIST', array ( $this, 'ced_multisite_list_shortcode'));
			add_action('wp_enqueue_scripts', array ( $this, 'ced_multisite_list_assets'));
 			add_action( 'admin_enqueue_scripts', array ( $this,'style_script_admin'));
 		}
		
		/**
		 * This function is used to add setting menu'.
		 * @name ced_msl_configuration_setting()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
				
		function ced_msl_configuration_setting()
		{
			add_menu_page('Multisite Store Viewer', 'Multisite Store Viewer', 'manage_options', 'multisite-list-shortcode-setting', array($this,'ced_msl_configuration') );
			add_submenu_page('multisite-list-shortcode-setting','Upload Site Logo', 'Upload Site Logo', 'manage_options', 'multisite-list-shortcode-logo', array($this,'ced_msl_logo') );
			remove_submenu_page( 'multisite-list-shortcode-setting', 'multisite-list-shortcode-setting' );
		}	
		
		/**
		 * This function is used to add setting menu for network admin'.
		 * @name ced_network_msl_configuration_setting()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		
		function ced_network_msl_configuration_setting()
		{
			add_menu_page('Multisite Store Viewer', 'Multisite Store Viewer', 'manage_options', 'multisite-list-shortcode-setting', array($this,'ced_msl_network_configuration') );
			add_submenu_page('multisite-list-shortcode-setting','Instructions', 'Instructions', 'manage_options', 'multisite-list-shortcode-setting', array($this,'ced_msl_network_configuration') );
			add_submenu_page('multisite-list-shortcode-setting','Default Settings', 'Default Settings', 'manage_options', 'multisite-list-default-setting', array($this,'ced_msl_configuration') );
			add_submenu_page('multisite-list-shortcode-setting','Upload Site Logo', 'Upload Site Logo', 'manage_options', 'multisite-list-shortcode-logo', array($this,'ced_network_msl_logo') );
		}
		
	
		/**
		 * This function is used to add setting logo for all site this is for network admin'.
		 * @name ced_network_msl_logo()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		
		function ced_network_msl_logo()
		{
			include_once CED_MULTISITE_DIR.'admin/logo-network-settings.php';
		}
		
		/**
		 * This function is used to add setting logo for your site'.
		 * @name ced_msl_logo()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		
		function ced_msl_logo()
		{
			include_once CED_MULTISITE_DIR.'admin/logo-settings.php';
		}
		
		/**
		 * This function is used to add setting.
		 * @name ced_msl_configuration()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		
		function ced_msl_configuration()
		{
			include_once CED_MULTISITE_DIR.'admin/default_settings.php';
		}
		
		/**
		 * This function is used to add default setting for network admin.
		 * @name ced_msl_network_configuration()
		 * @author CedCommerce<plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		
		function ced_msl_network_configuration()
		{
			include_once CED_MULTISITE_DIR.'admin/instructions.php';
		}
		
		/**
		 * This is a function to create a shortcode for listing all site'.
		 * @name ced_multisite_list_shortcode()
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		function ced_multisite_list_shortcode()
		{
			global $blog_id;
			$ced_sites = wp_get_sites();
			$exclude_site = get_site_option('ced_multi_exclude_site');
			if($exclude_site == 'yes')
			{
				foreach ($ced_sites as $key=> $site)
				{
					if($site['blog_id'] == $blog_id)
					{
						unset($ced_sites[$key]);
					}
				}
			}
			
			/***   check and hide current site setting ****/
			$checkbox_value =  get_site_option('hidesite');
			foreach ($ced_sites as $key=> $shop)
			{
				$blog_id = $shop['blog_id'];
				if(is_array($checkbox_value))
				{
					if(in_array($blog_id, $checkbox_value))
					{
						unset($ced_sites[$key]);
						
					}
				}
			}
			
			/****** hide sites according to the checkboxes in attribute setting *****/
			$checked = array();
			$existing_details = get_site_option( 'selected_attribute', false );
			if(!empty($existing_details))
			{
				foreach ($existing_details as $K=> $value)
				{
					if($value == 1)
					$checked[] = $K;
				}
				foreach ($ced_sites as $key=> $shop)
				{
					 foreach ($checked as $values)
					 { 
						if($shop[$values] == '1')
						{
							unset($ced_sites[$key]);
						}
					 }
				}
			}
			
			
			if(isset($ced_sites))
			{
				if(is_array($ced_sites))
				{
					if(isset($ced_sites))
					{
						if(is_array($ced_sites))
						{
							$disable_search = get_site_option('ced_multi_enable_search');
							if($disable_search != 'yes')
							{
								$site_html = '<p>Search site: <input type="text" id="ced-site-search" data-list=".ced_multi_site_list"></p>';
							}
							$order_by_value = get_site_option('site_list_order');
							if($order_by_value=="Name")
							{
								foreach ( $ced_sites as $i => $site ) {
										
									switch_to_blog( $site[ 'blog_id' ] );
									$ced_sites[ $i ][ 'name' ] = get_bloginfo();
									restore_current_blog();
								}
									
									uasort( $ced_sites, function( $site_a, $site_b ) {
									return strcasecmp( $site_a[ 'name' ], $site_b[ 'name' ] );
								});
							}
							elseif($order_by_value=="Recently Added")
							{
								global $wpdb;
								$ced_sites = $wpdb->get_results( $wpdb->prepare("SELECT blog_id, domain, path FROM $wpdb->blogs WHERE site_id = %d AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ORDER BY registered DESC", $wpdb->siteid), ARRAY_A );
									
								foreach ($ced_sites as $key=> $shop)
								{
									$blog_id = $shop['blog_id'];
									$checkbox_value =  get_blog_option($blog_id,'hidesite');
									if($checkbox_value=='yes')
									{
										unset($ced_sites[$key]);
									}
								}
								
							}
							elseif($order_by_value=="ID")
							{
								global $wpdb;
								$ced_sites = $wpdb->get_results( $wpdb->prepare("SELECT blog_id, domain, path FROM $wpdb->blogs WHERE site_id = %d AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ORDER BY blog_id ASC", $wpdb->siteid), ARRAY_A );
								foreach ($ced_sites as $key=> $shop)
								{
									$blog_id = $shop['blog_id'];
									$checkbox_value =  get_blog_option($blog_id,'hidesite');
									if($checkbox_value=='yes')
									{
										unset($ced_sites[$key]);
									}
								
								}
							}
							else{
								foreach ($ced_sites as $key=> $shop)
								{
									$blog_id = $shop['blog_id'];
									$checkbox_value =  get_blog_option($blog_id,'hidesite');
									if($checkbox_value=='yes')
									{
										unset($ced_sites[$key]);
									}
								
								}
							}
							$site_html .= '<ul class="ced_multi_site_list">';
							foreach ($ced_sites as $ced_site) 
							{	
								$blog_details = get_blog_details($ced_site['blog_id']);
								$blog_details = (array)$blog_details;
								$site_img = get_blog_option($ced_site['blog_id'],'site_image');
								$default_img =  get_site_option('default_image');
								$custom_image_size_width = get_site_option('custom_image_size_width');
   								$custom_image_size_height = get_site_option('custom_image_size_height');
								if($site_img)
								{
									if($custom_image_size_width && $custom_image_size_height)
									{
										$site_html .= '<li class="ced_float"><div class="ced_site_Content"><a href="'.$blog_details['siteurl'].'" ><img src="'.$site_img .'" class ="front-image" height ="'.$custom_image_size_height.'px" width = "'.$custom_image_size_width.'px"><span class="ced_site_title">'.$blog_details['blogname'].'</span></a></div></li>';
									}
									else
									{
										$site_html .= '<li class="ced_float"><div class="ced_site_Content"><a href="'.$blog_details['siteurl'].'" ><img src="'.$site_img .'" class ="front-image" height ="150px" width = "150px"><span class="ced_site_title">'.$blog_details['blogname'].'</span></a></div></li>';
									}
								}
								elseif($default_img) 
								{
									if($custom_image_size_width && $custom_image_size_height)
									{
										$site_html .= '<li class="ced_float"><div class="ced_site_Content"><a href="'.$blog_details['siteurl'].'"><img src="'.$default_img .'" class ="front-image" height = "'.$custom_image_size_height.'px" width = "'.$custom_image_size_width.'px"><span class="ced_site_title">'.$blog_details['blogname'].'</span></a></div></li>';
									}
									else
									{
										$site_html .= '<li class="ced_float"><div class="ced_site_Content"><a href="'.$blog_details['siteurl'].'"><img src="'.$default_img .'" class ="front-image" height ="150px" width = "150px"><span class="ced_site_title">'.$blog_details['blogname'].'</span></a></div></li>';
									}
								}
								else 
								{
									if($custom_image_size_width && $custom_image_size_height)
									{
										$site_html .= '<li class="ced_float"><div class="ced_site_Content"><a href="'.$blog_details['siteurl'].'"><img src="'.CED_MULTISITE_DIR_URL.'assets/images/default.jpg" height = "'.$custom_image_size_height.'px" width = "'.$custom_image_size_width.'px"><span class="ced_site_title">'.$blog_details['blogname'].'</span></a></div></li>';
									}
									else 
									{
										$site_html .= '<li class="ced_float"><div class="ced_site_Content"><a href="'.$blog_details['siteurl'].'" ><img src="'.CED_MULTISITE_DIR_URL.'assets/images/default.jpg" width = "150px" height = "150px"><span class="ced_site_title">'.$blog_details['blogname'].'</span></a></div></li>';
										
									}
								}
							}
							$site_html .= '</ul>';
						}
					}
				}
			}
			return $site_html;
		}
		
		
		/**
		 * This is a function to create a shortcode for listing all site'.
		 * @name ced_multisite_list_shortcode()
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @link http://cedcommerce.com/
		 */
		
		function ced_multisite_list_assets()
		{	
			wp_enqueue_script('ced-search-js', CED_MULTISITE_DIR_URL.'assets/js/ced-search.js',array( 'jquery'), '1.0.0', true);
			wp_enqueue_style('ced-multicss', CED_MULTISITE_DIR_URL.'assets/css/ced-multi.css');
			wp_enqueue_style('ced-image-css', CED_MULTISITE_DIR_URL.'assets/css/ced-image.css');
			wp_enqueue_script('ced-multi-hideseek-js', CED_MULTISITE_DIR_URL.'assets/js/jquery.hideseek.min.js',array( 'jquery'));
		}
		 /**
		  * this function is for image upload setting
		  * @name style_script_admin
		  * @author CedCommerce <plugins@cedcommerce.com>
		  * @link http://cedcommerce.com/
		  */
		  
		 function style_script_admin()
	     {
	    	wp_enqueue_script('thickbox');
	    	wp_enqueue_style('thickbox');
	    	wp_enqueue_style('ced-style-setting', CED_MULTISITE_DIR_URL.'assets/css/ced-setting.css');
	    	wp_enqueue_script('media-upload');
	    	wp_enqueue_script('ced-image-js', CED_MULTISITE_DIR_URL.'assets/js/ced-image.js',array( 'jquery'),true);
	     }   
	}
	new Ced_multisite_list();
}	
?>