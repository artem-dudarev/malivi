<?php 
/**
 * Plugin Name: Multisite Store Viewer
 * Plugin URI: http://cedcommerce.com
 * Description: This plugin list all the sites of a multisite wordpress on a single page.
 * Version: 1.0.7
 * Author: CedCommerce
 * Author URI: http://cedcommerce.com
 * Requires at least: 3.5
 * Tested up to: 4.5.3
 * Text Domain: ced-multisite-list
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) )
{
	exit; // Exit if accessed directly
}
if (function_exists('is_multisite') && is_multisite())
{
	define('CED_MSL_PREFIX', 'ced_msl_');
	define('CED_MULTISITE_DIR', plugin_dir_path( __FILE__ ));
	define('CED_MULTISITE_DIR_URL', plugin_dir_url( __FILE__ ));
	
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	include_once( CED_MULTISITE_DIR . 'includes/ced-list-site-class.php' );
	
	add_action('plugins_loaded','ced_msl_load_text_domain');
	
	/**
	 * This function is used to load language'.
	 * @name ced_msl_load_text_domain()
	 * @author CedCommerce<plugins@cedcommerce.com>
	 * @link http://cedcommerce.com/
	 */
	
	function ced_msl_load_text_domain()
	{
		$domain = "ced-multisite-list";
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_textdomain( $domain, CED_MULTISITE_DIR .'languages/'.$domain.'-' . $locale . '.mo' );
		$var=load_plugin_textdomain( 'ced-multisite-list', false, plugin_basename( dirname(__FILE__) ) . '/languages' );
	}
}
else
{
	/**
	 * This function is used for dispalying error notice'.
	 * @name ced_msl_plugin_error_notice()
	 * @author CedCommerce<plugins@cedcommerce.com>
	 * @link http://cedcommerce.com/
	 */
	
	function ced_msl_plugin_error_notice()
	{
	?>
		<div class="error notice is-dismissible">
			<p><?php _e( 'This site is not multisite. Plugin works with wordpress multisite', 'ced-multisite-list' ); ?></p>
		</div>
		<?php
	}
		
	add_action( 'admin_init', 'ced_msl_plugin_deactivate' );
		
	function ced_msl_plugin_deactivate() 
	{
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'ced_msl_plugin_error_notice' );
	}
}	
?>