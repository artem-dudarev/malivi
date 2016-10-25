<?php
	add_action('wp_ajax_sf-deleteform', 'sf_ajax_deleteform');
	function sf_ajax_deleteform(){		
		$fields = get_option( 'sf-fields' );
		unset( $fields[ $_POST['id'] ] );
		update_option( 'sf-fields', $fields );
		echo 'OK';
		die();
	}
	
	add_action('wp_ajax_sf-optionsearch', 'sf_ajax_optionsearch');
	function sf_ajax_optionsearch(){
		$data = array();
		$i = 0;
		preg_match_all( '^(.*)\[(.*)\]^', $_POST['val'], $match );
		$data_type = $match[1][0];
		$data_value = $match[2][0];
		if( $data_type == 'meta' ){
			$terms = get_postmeta_values( $data_value );
			if( is_array( $terms ) ):
			foreach( $terms as $term ){
				$data[ $i ]['key'] = $term->meta_value;
				$data[ $i ]['val'] = $term->meta_value;
				$i++;
			}
			endif;
		}elseif( $data_type == 'tax' ){
			$args = array(
						'orderby'       => 'name', 
						'order'         => 'ASC',
						'hide_empty'    => true
					);
			$terms = get_terms( $data_value, $args );
			if( is_array( $terms ) ):
			foreach( $terms as $term ){
				$data[ $i ]['key'] = $term->term_id;
				$data[ $i ]['val'] = $term->name;
				$i++;
			}
			endif;
		}
		
		echo json_encode( $data );
		die();
	}

	/* trim excerpt to custom size */
	function event_list_custom_excerpt ($limit, $ignore_more_tag) {
		global $more;
		if ($ignore_more_tag == 'true') { $more = 1; }
		else { $more = 0; }
		$excerpt = explode(' ', get_the_excerpt(), $limit);
		if (count($excerpt)>=$limit) {
			array_pop($excerpt);
			$excerpt = implode(" ",$excerpt).'...';
		} else {
			$excerpt = implode(" ",$excerpt);
		}
		$excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
		return $excerpt;
	}
	
	add_action('wp_ajax_sf-search', 'sf_ajax_search');
	add_action('wp_ajax_nopriv_sf-search', 'sf_ajax_search');
	function sf_ajax_search(){	
		error_reporting( 0 );
		global $events_list_last_date;
		$events_list_last_date = $_POST['last_date'];
		$response = sf_get_posts($_POST['page'], $_POST['data']['search-id'], $_POST['data']);
		$response['request_id'] = $_POST['request_id'];
		echo json_encode( $response );
		die();	
	}
	
	function sf_get_posts($page, $search_id, $filters) {
		global $wpdb;
		
		$fulltext = "";
		$fields = get_option( 'sf-fields' );
		$found = false;
		foreach( $fields as $field ) {
			if( $field['name'] == $search_id ) {
				$found = true;
				break;
			}
		}
		
		if( !$found ) {
			die( 'Wrong parameter' );
		}

		$post_type = $field['posttype'];
		if( is_array( $field['posttype'] ) ) {
			foreach( $field['posttype'] as $posttype ) {
				$post_type = $posttype;
				break;
			}
		} else {
			$post_type = $field['posttype'];
		}
				
		$args = array(
			'post_type'		=> $post_type,
			'post_status'	=> 'publish'
		);

			
		$request_parameters = array();
		if (isset($filters)) {
			foreach( $filters as $key => $val ) {
				if( $val == '' || empty( $val ) ) {
					continue;
				}
					
				$key = explode( '|', $key );
				if( !isset( $key[1] ) ) {
					$request_parameters[ $key[0] ]['val'] = $val;
				}
				if( isset( $key[1] ) ) {
					$request_parameters[ $key[0] ][ $key[1] ] = $val;
				}
			}
		}		
		
		$operator = array( 'like' => 'LIKE', 'between' => 'BETWEEN', 'equal' => '=', 'bt' => '>', 'st' => '<', 'bte' => '>=', 'ste' => '<=' );
		foreach( $field['fields'] as $key => $val ):
			if( isset( $val['datasource'] ) && !in_array( $val['type'], array( 'map','fulltext' ) ) ):
				preg_match_all( '^(.*)\[(.*)\]^', $val['datasource'], $match );
				$data_type = $match[1][0];
				$data_value = $match[2][0];
			else:
				$data_type = $val['type'];
				$data_value = $val['type'] ;
			endif;
			if( isset( $request_parameters[ $key ] ) ):
				/** Taxonomy Query */
				if( $data_type == 'tax' ):
					if( !isset( $args['tax_query'] ) ):
						$args['tax_query']['relation'] = 'AND';
					endif;
					
					/** Select Field */
					if( $val['type'] == 'select' && $request_parameters[ $key ]['val']  != "" ):
						$args['tax_query'][] = array( 
							'taxonomy'	=> $data_value, 
							'terms'		=> (int) $request_parameters[ $key ]['val'] 
						);
					/** Input Field */
					elseif( $val['type'] == 'checkbox' ):
						$operator = 'IN';
						$include_children = true;
						if( isset( $val['include_children'] ) && $val['include_children'] == 0 )
							$include_children = false;
						if( isset( $val['operator'] ) )
							$operator = $val['operator'];
							
						$args['tax_query'][] = array( 
							'taxonomy'	=> $data_value, 
							'terms'		=> $request_parameters[ $key ]['val'],
							'operator'	=> $operator,
							'include_children' => $include_children
						);
						
					/** Input Field */
					elseif( $val['type'] == 'radiobox' ):						
						$args['tax_query'][] = array( 
							'taxonomy'	=> $data_value, 
							'terms'		=> $request_parameters[ $key ]['val'] 
						);
						
					endif;
				/**
				Postmeta Query				
				*/					
				elseif( $data_type == 'meta' ):
					if( !isset( $args['meta_query'] ) )
						$args['meta_query'] = array();
					
					/** Select Field */
					if( $val['type'] == 'select' ):
						$args['meta_query'][] = array(
									'key'		=>	$data_value,
									'value'		=>	$request_parameters[ $key ]['val'],
									'compare'	=>	'='
						);
					elseif( $val['type'] == 'checkbox' ):
						$args['meta_query'][] = array(
									'key'		=> $data_value,
									'value'		=> $request_parameters[ $key ]['val'],
									'type' 		=> 'CHAR',
									'compare'	=> 'IN'
						);
					elseif( $val['type'] == 'radiobox' ):
						$args['meta_query'][] = array(
									'key'		=> $data_value,
									'value'		=> $request_parameters[ $key ]['val'],
									'type' 		=> 'CHAR',
									'compare'	=> '='
						);
					endif;
						
				elseif( $val['type'] == 'fulltext' && !empty( $request_parameters[ $key ]['val'] ) ):
					if( in_array( 'the_title', $val['contents'] ) )
						$args['sf-title'] = $request_parameters[ $key ]['val'];
					if( in_array( 'the_content', $val['contents'] ) )
						$args['sf-content'] = $request_parameters[ $key ]['val'];
					if( in_array( 'the_excerpt', $val['contents'] ) )
						$args['sf-excerpt'] = $request_parameters[ $key ]['val'];
					foreach( $val['contents'] as $v ):
						if( preg_match( '^meta\[(.*)\]^', $v ) ):
							if( !isset( $args['sf-meta'] ) ):
								$args['sf-meta'] = array();
							endif;
							$args['sf-meta'][ $v ] = $request_parameters[ $key ]['val'];
						endif;
					endforeach;
					add_filter( 'posts_where', 'sf_content_filter', 10, 2 );
					if( isset( $args['sf-meta'] ) )
						add_filter( 'posts_join_paged', 'sf_content_filter_join', 10, 2 );
					
					$fulltext = $request_parameters[ $key ]['val'] ;
				endif;
			endif;				
		endforeach;
		
		
		if( isset( $page ) ) {
			$args['paged'] = (int) $page;
		}
		
		$args = apply_filters( 'sf-filter-args', $args );
		$wpdb->query( 'SET OPTION SQL_BIG_SELECTS = 1' );
		$query = new WP_Query( $args );
		remove_filter( 'posts_join_paged', 'sf_content_filter_join' );
		remove_filter( 'posts_where', 'sf_content_filter' );
		$content = '';
		if( $query->have_posts() ) {
			while( $query->have_posts() ) {
				$query->the_post();
				ob_start();
				tribe_get_template_part( 'list/single', get_post_type() );
				$content .= ob_get_clean();
			} // end while( $query->have_posts() )
			wp_reset_postdata();
		} else if( !isset( $page ) ) {
			$content = '<div>Ничего не найдено, попробуйте изменить настройки фильтров.</div>';
		}
		
		$response = array();
		$response['html'] = $content;
		$response['pages_count'] = $query->max_num_pages;
		global $events_list_last_date;
		$response['last_date'] = $events_list_last_date;
		return $response;
	}

	add_action('wp_ajax_get-post-page', 'get_post_page');
	add_action('wp_ajax_nopriv_get-post-page', 'get_post_page');
	function get_post_page() {
		error_reporting( 0 );
		do_get_post_page();
		die();	
	}
	

	function do_get_post_page( ) {
		$post_id = $_POST['post_id'];
		global $wpdb;
		global $post;
		$post = get_post($post_id);
		setup_postdata($post);
		//$template = get_events_page_template( $post->post_type );
		//include($template);
		tribe_get_template_part( 'page/single', get_post_type() );
	}

	function get_events_page_template($post_type) {
		return SF_DIR . 'includes/page-'. $post_type .'.php';
	}
?>