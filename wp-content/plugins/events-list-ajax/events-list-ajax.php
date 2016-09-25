<?php
	/*
	Plugin Name: Events List Ajax
	Description: With this Plugin, you can easily create AJAX Search Filters, which enables a more detailed search using Taxonomies and Postmeta data
	Tags: search,filter,postmeta,taxonomies,ajax
	Version: 1.0
	Author: Ilia Dudarev
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
	*/
	
	define( 'EL_CURRENT_VERSION', '1.0' );
	if( !session_id() )
		session_start();
		
	define( 'MALIVI_PLUGIN_URL', plugins_url( '', __FILE__ ) . '/' );
	define( 'MALIVI_PLUGIN_DIR', dirname( __FILE__ ) . '/' );
	define( 'HOME_URL', get_bloginfo( 'url' ) );
	define( 'HOME_NAME', get_bloginfo( 'name' ) );
	
	require_once( MALIVI_PLUGIN_DIR . 'admin/admin.php' );
	require_once( MALIVI_PLUGIN_DIR . 'events-list-ajax-functions.php' );
	require_once( MALIVI_PLUGIN_DIR . 'the-events-calendar-extensions.php' );
	require_once( MALIVI_PLUGIN_DIR . 'includes/wpml-functions.php' );
	
	function sf_textdomain() {
		$plugin_dir = basename( dirname( __FILE__ ) ) . '/res/lang/';
		load_plugin_textdomain( 'sf', false, $plugin_dir );
	}
	add_action('plugins_loaded', 'sf_textdomain');
	
	add_action( 'wp_head', 'sf_head', 1 );
	function sf_head(){
		wp_register_script('yandexMaps', "//api-maps.yandex.ru/2.1/?lang=" . get_bloginfo('language','display'));
		wp_enqueue_script('yandexMaps');

		//wp_register_script('bsscripts', "//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js");
		//wp_enqueue_script('bsscripts');

		//wp_register_script('googleMaps', "//maps.google.com/maps/api/js");
		//wp_enqueue_script('googleMaps');
		

		$settings = get_option( 'search-filter-settings' );
		if( !isset( $settings['style'] ) || $settings['style'] == '' ) {
			wp_register_style( 'events-list-ajax-style', MALIVI_PLUGIN_URL . 'res/style.css' );
		} else {
			wp_register_style( 'events-list-ajax-style', MALIVI_PLUGIN_URL . 'res/' . $settings['style'] . '.css');
		}
		wp_enqueue_style( 'events-list-ajax-style' );	
		
		
		//wp_enqueue_script('jquery');
		//wp_enqueue_script('jquery-ui-slider');
		wp_register_script(
			'events-list-ajax-script',
			MALIVI_PLUGIN_URL . 'res/events-list-ajax-scripts.js',
			array('jquery'),
			EL_CURRENT_VERSION,
			true
		);
		wp_enqueue_script( 'events-list-ajax-script' );
		
		?>
		<script>var sf_ajax_root = '<?php echo admin_url('admin-ajax.php'); ?>'</script>
		<?php
	}
	
	
	function insert_searchform( $id ){
		$attr = array( 'id' => $id );
		
		ob_start();
		require( MALIVI_PLUGIN_DIR . 'includes/shortcode.php' );
		$output_string=ob_get_contents();
		ob_end_clean();
		echo $output_string;
	}
	
	add_shortcode( 'search-form', 'sf_init_searchform' );
	add_filter('widget_text', 'do_shortcode');
	function sf_init_searchform( $attr ){
		ob_start();
		require( MALIVI_PLUGIN_DIR . 'includes/shortcode.php' );
		$output_string=ob_get_contents();
		ob_end_clean();
		return $output_string;
	}

	function get_all_postmetas_from_post_type( $post_type ){
		global $wpdb;
		if( !is_array( $post_type ) )
			$post_type = array( $post_type );
		
		
		$data   =   array();
		$sql = $wpdb->prepare( "
			SELECT $wpdb->postmeta.`meta_key`, $wpdb->postmeta.`meta_value`
			FROM $wpdb->postmeta, $wpdb->posts
			WHERE $wpdb->posts.`post_status` = 'publish' && $wpdb->posts.`post_type` IN (".implode(', ', array_fill(0, count($post_type), '%s')).") && $wpdb->postmeta.`post_id` = $wpdb->posts.`ID`
			group by $wpdb->postmeta.`meta_key`
		", $post_type );
		$wpdb->query( $sql );
		$no_key = array( '_edit_last', '_edit_lock', '_thumbnail_id' );		
		foreach($wpdb->last_result as $k => $v){
			if( !in_array( $v->meta_key , $no_key ) ):
				if( !is_array( maybe_unserialize( $v->meta_value ) ) ):
					$data[$v->meta_key] =   $v->meta_value;
				else:
					$add_this = apply_filters( 'sf_postmeta_serialize', array( 'add_this' => false, 'meta_key' => $v->meta_key ) );
					if( $add_this['add_this'] )
						$data[ $v->meta_key ] = $v->meta_value;
				endif;
			endif;
		};
		return $data;
	}
	
	function get_all_post_taxonomies( $post_type ) {
		$taxonomies = array();
		if( !is_array( $post_type ) )
			$post_type = array( $post_type );
		foreach( $post_type as $p )
			$taxonomies = array_merge( $taxonomies, get_object_taxonomies($p, 'objects') ); 
		return (array) $taxonomies; 
	}
	
	function get_postmeta_values( $meta_key ){
		global $wpdb;
		$sql = $wpdb->prepare( "
			SELECT $wpdb->postmeta.`meta_key`, $wpdb->postmeta.`meta_value`
			FROM $wpdb->postmeta, $wpdb->posts
			WHERE 
				$wpdb->posts.`post_status` = 'publish' && 
				$wpdb->postmeta.`post_id` = $wpdb->posts.`ID` &&
				$wpdb->postmeta.`meta_key` = '%s' 
			group by 
				$wpdb->postmeta.`meta_value` order by $wpdb->postmeta.`meta_value` asc", 
			$meta_key );
		$res = $wpdb->get_results( $sql );
		$res = apply_filters( 'sf_get_postmeta_values', $res );
		return $res;
	}
	
	function sf_content_filter_join( $join_paged_statement, &$wp_query ) {
		global $wpdb;
		$metas = $wp_query->get( 'sf-meta' );
		if( isset( $metas ) && is_array( $metas ) && count( $metas ) > 0 ){
			foreach( $wp_query->get( 'sf-meta' ) as $meta => $val ){
				$join_paged_statement .= " LEFT JOIN " . $wpdb->prefix . "postmeta as sf_" . md5( $meta ) . " ON ( sf_" . md5( $meta ) . ".post_id = " . $wpdb->prefix . "posts.ID ) ";
			}
		}
		return $join_paged_statement;
	}
	
	function sf_content_filter( $sf_where, &$wp_query ){
        global $wpdb;
		
		if( $wp_query->get( 'sf-title' ) || $wp_query->get( 'sf-meta' ) || $wp_query->get( 'sf-content' )  || $wp_query->get( 'sf-excerpt' ) ):
			
			$concat_fields = array();
			
			$st = $wp_query->get( 'sf-title' );
			if( isset( $st ) && !empty( $st ) ):
				$concat_fields[] = $wpdb->posts . '.post_title';
				$search_term = $st;
			endif;
			
			$st = $wp_query->get( 'sf-content' );
			if( isset( $st ) && !empty( $st ) ):
				$concat_fields[] = $wpdb->posts . '.post_content';
				$search_term = $st;
			endif;
			
			$st = $wp_query->get( 'sf-excerpt' );
			if( isset( $st ) && !empty( $st ) ):
				$concat_fields[] = $wpdb->posts . '.post_excerpt';
				$search_term = $st;
			endif;
			
			$metas = $wp_query->get( 'sf-meta' );
			$post_meta_keys = array();
			if( isset( $metas ) && is_array( $metas ) && count( $metas ) > 0 ){
				foreach( $wp_query->get( 'sf-meta' ) as $meta => $search_term ){
					preg_match( '^meta\[(.*)\]^', $meta, $match );
					$concat_fields[] = 'sf_' . md5( $meta ) . '.meta_value';
					$post_meta_keys[ 'sf_' . md5( $meta ) ] = $match[1];
				}
			}
			
			
			$concat_string = '';
			foreach( $concat_fields as $f ):
				$concat_string .= ',' . $f . ', " "';
			endforeach;
			$sf_add_where = '';
			
			if( !empty( $concat_string ) ):
				$sf_add_where = ' AND (';
				$concat_string = "CONCAT( \"\" " . $concat_string . ") LIKE '%" . esc_sql( like_escape( $search_term ) ) . "%' ";
				$sf_add_where .= $concat_string;
				$sf_add_where .= ' ) ';
				if( count( $post_meta_keys ) > 0 ):
					foreach( $post_meta_keys as $key => $val ):
						$sf_add_where .= " AND " . $key . ".meta_key ='" . $val . "'";
					endforeach;
				endif;
			endif;
			$sf_where .= $sf_add_where;
		endif;
		
		return $sf_where;
	}
	
	function order_terms_hierarchical_array( $terms, $parent = 0 ){
		$tmp_terms = array();
		foreach( $terms as $term ):
			if( $term->parent == $parent ):
				$term->children = order_terms_hierarchical_array( $terms, $term->term_id );
				$tmp_terms[] = $term;
			endif;
		endforeach;
		return $tmp_terms;
	}
	
	function flatten_terms_hierarchical_array( $terms, $symbol, $status = 0 ){
		$tmp_terms = array();
		foreach( $terms as $term ):
			if( $status == 1 ):
				$term->name = $symbol . ' ' . $term->name;
			endif;
			$tmp_terms[] = $term;
			
			if( count( $term->children ) > 0 ):
				if( $status == 1 )
					$s = $symbol . $symbol;
				else
					$s = $symbol;
				$tmp_terms = array_merge( $tmp_terms, flatten_terms_hierarchical_array( $term->children, $s, 1 ) ); 
			endif;
		endforeach;
		return $tmp_terms;
	}
	
	function order_terms_hierarchical( $terms, $symbol ){
		$terms = order_terms_hierarchical_array( $terms );
		$terms = flatten_terms_hierarchical_array( $terms, $symbol );		
		return $terms;
	}

	function replace_featured_image_size($size, $post_id) {
		return 'medium';
	}
	add_filter('tribe_event_featured_image_size', 'replace_featured_image_size', 10, 2);

	function return_event_date_instead_of_publish_date( $the_date, $d, $post ) {
		if ( is_int( $post) ) {
			$post_id = $post;
		} else {
			$post_id = $post->ID;
		}

		if ( tribe_is_event( $post_id ) ) {
			return date_i18n( $d, strtotime(get_post_meta( $post_id, '_EventStartDate', true ) ) );
		}
			
		return $the_date;
	}
	add_action( 'get_the_date', 'return_event_date_instead_of_publish_date', 10, 3 );
	//add_filter( 'get_the_date', 'return_event_date_instead_of_publish_date', 10, 3 );
?>