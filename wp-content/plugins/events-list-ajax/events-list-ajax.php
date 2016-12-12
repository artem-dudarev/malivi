<?php
	/*
	Plugin Name: Events List Ajax
	Description: With this Plugin, you can easily create AJAX Search Filters, which enables a more detailed search using Taxonomies and Postmeta data
	Tags: search,filter,postmeta,taxonomies,ajax
	Version: 1.0.2
	Author: Ilia Dudarev
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
	*/
	
	if( !session_id() )
		session_start();
		
	define( 'SF_URL', plugins_url( '', __FILE__ ) . '/' );
	define( 'SF_DIR', dirname( __FILE__ ) . '/' );
	define( 'HOME_URL', get_bloginfo( 'url' ) );
	define( 'HOME_NAME', get_bloginfo( 'name' ) );
	
	require_once( SF_DIR . 'admin/admin.php' );
	require_once( SF_DIR . 'events-list-ajax-functions.php' );
	require_once( SF_DIR . 'the-events-calendar-extensions.php' );
	require_once( SF_DIR . 'includes/utils.php' );
	if( is_multisite() ) { 
		//require_once( SF_DIR . 'multisite-user-autoregister.php' );
	}
	
	add_action('plugins_loaded', 'sf_textdomain');
	function sf_textdomain() {
		$plugin_dir = basename( dirname( __FILE__ ) ) . '/res/lang/';
		load_plugin_textdomain( 'sf', false, $plugin_dir );
	}

	add_action( 'wp_head', 'events_list_head', 1 );
	function events_list_head() {
		$version = '1.0.1';
		//wp_register_script('yandexMaps', "//api-maps.yandex.ru/2.1/?lang=" . get_bloginfo('language','display'));
		//wp_enqueue_script('yandexMaps');

		//wp_register_script('bsscripts', "//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js");
		//wp_enqueue_script('bsscripts');

		//wp_register_script('googleMaps', "//maps.google.com/maps/api/js");
		//wp_enqueue_script('googleMaps');
		

		wp_register_style( 'events-list-ajax-style', SF_URL . 'res/sf-style.css', array(), $version);
		
		wp_register_style( 'events-list-style', SF_URL . 'style.css', array(), $version);
		wp_enqueue_style( 'events-list-style' );	
		
		
		//wp_enqueue_script('jquery');
		//wp_enqueue_script('jquery-ui-slider');
		wp_register_script(
			'events-list-ajax-script',
			SF_URL . 'res/events-list-ajax-scripts.js',
			array('jquery'),
			EL_CURRENT_VERSION,
			true
		);
		wp_enqueue_script( 'events-list-ajax-script' );

		/*wp_register_script(
			'events-list-ajax-popups',
			SF_URL . 'res/events-list-ajax-popups.js',
			array('jquery'),
			EL_CURRENT_VERSION,
			true
		);
		wp_enqueue_script( 'events-list-ajax-popups' );*/
		
		?>
		<script>var sf_ajax_root = '<?php echo admin_url('admin-ajax.php'); ?>'</script>
		<?php
	}
	
	add_shortcode( 'search-form', 'sf_init_searchform' );
	function sf_init_searchform( $attr, $content ){
		ob_start();
		require( SF_DIR . 'includes/shortcode.php' );
		$output_string=ob_get_contents();
		ob_end_clean();
		return $output_string;
	}

	//add_action('wp_head', 'sf_fix_admin_toolbar_padding');
	function sf_fix_admin_toolbar_padding() {
		if ( is_admin_bar_showing() ) {
			echo '<style>'.PHP_EOL;
			echo '.admin-toolbar-fix-for-fixed-position { margin-top: 32px !important; }'.PHP_EOL;
			echo '@media screen and ( max-width: 782px ) {'.PHP_EOL;
			echo '    .admin-toolbar-fix-for-fixed-position  { margin-top: 46px !important; }'.PHP_EOL;
			echo '}'.PHP_EOL;
			echo '</style>'.PHP_EOL;
		}
	}

	add_action('events_list_before_event_view', 'process_postviews');
	
?>