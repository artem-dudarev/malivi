<?php	
	
	function sf_count_posts($language_code = '', $post_type = 'post', $post_status = 'publish'){
		
		global $sitepress, $wpdb;
		
		$default_language_code = $sitepress->get_default_language();
		$post_type = 'post_' . $post_type;
		

		$slc_param = $sitepress->get_default_language() == $language_code ? "IS NULL" : "= '{$default_language_code}'";
		$query = "SELECT COUNT( {$wpdb->prefix}posts.ID )
						FROM {$wpdb->prefix}posts
						LEFT JOIN {$wpdb->prefix}icl_translations ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}icl_translations.element_id
						WHERE {$wpdb->prefix}icl_translations.language_code = '{$language_code}'
							AND {$wpdb->prefix}icl_translations.source_language_code $slc_param
							AND {$wpdb->prefix}icl_translations.element_type = '{$post_type}'
							AND {$wpdb->prefix}posts.post_status = '$post_status'";
     
		return $wpdb->get_var( $query );
    }

	function get_all_postmetas_from_post_type( $post_type ){
		global $wpdb;
		if( !is_array( $post_type ) ) {
			$post_type = array( $post_type );
		}
		
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
		if( !is_array( $post_type ) ) {
			$post_type = array( $post_type );
		}
		foreach( $post_type as $p ) {
			$taxonomies = array_merge( $taxonomies, get_object_taxonomies($p, 'objects') );
		} 
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
		if( isset( $metas ) && is_array( $metas ) && count( $metas ) > 0 ) {
			foreach( $wp_query->get( 'sf-meta' ) as $meta => $val ) {
				$join_paged_statement .= " LEFT JOIN " . $wpdb->prefix . "postmeta as sf_" . md5( $meta ) . " ON ( sf_" . md5( $meta ) . ".post_id = " . $wpdb->prefix . "posts.ID ) ";
			}
		}
		return $join_paged_statement;
	}
	
	function sf_content_filter( $sf_where, &$wp_query ) {
        global $wpdb;
		
		if( $wp_query->get( 'sf-title' ) || $wp_query->get( 'sf-meta' ) || $wp_query->get( 'sf-content' )  || $wp_query->get( 'sf-excerpt' ) ) {
			
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
			foreach( $concat_fields as $f ) {
				$concat_string .= ',' . $f . ', " "';
			}
			$sf_add_where = '';
			
			if( !empty( $concat_string ) ) {
				$sf_add_where = ' AND (';
				$concat_string = "CONCAT( \"\" " . $concat_string . ") LIKE '%" . esc_sql( like_escape( $search_term ) ) . "%' ";
				$sf_add_where .= $concat_string;
				$sf_add_where .= ' ) ';
				if( count( $post_meta_keys ) > 0 ) {
					foreach( $post_meta_keys as $key => $val ) {
						$sf_add_where .= " AND " . $key . ".meta_key ='" . $val . "'";
					}
				}
			}
			$sf_where .= $sf_add_where;
		}
		
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

	function is_true( $check_box_variable ) {
		$check_box_variable = trim( $check_box_variable );

		return (
			'true' === strtolower( $check_box_variable )
			|| 'yes' === strtolower( $check_box_variable )
			|| true === $check_box_variable
			|| 1 == $check_box_variable
		);
	}

?>