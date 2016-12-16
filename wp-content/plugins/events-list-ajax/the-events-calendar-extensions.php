<?php

	function get_malivi_custom_fields() {
		return array (
			'EventIsForChildren' => false,
			'EventAgeRestriction' => '0+',
		);
	}

	function get_age_restriction_variants($current_value) {
		$ages = array('0+','3+','5+','6+','7+','12+','14+','15+','16+','17+','18+','19+','20+','21+');
		$variants = '';
		foreach ($ages as $age) {
			if ( $age == $current_value ) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			$variants .= "<option value='$age' $selected>$age</option>\n";
		}
		
		return $variants;
	}

	
	add_action( 'init', 'register_events_extensions' );
	function register_events_extensions() {
		if ( !class_exists( 'Tribe__Events__Main' ) ) {
			return;
		}

		$roles = array ('administrator', 'editor', 'author', 'contributor');
		foreach ($roles as $role) {
			$role = get_role($role);
			$role->add_cap('read_private_tribe_venues');
		}
		
		// Дополнительные категории для событий
		register_taxonomy(
			'events_directions',
			Tribe__Events__Main::POSTTYPE,
			array(
				'labels' 				=> array(
					'name'			=> __( 'Directions', 'sf'),
					'singular_name'	=> __( 'Direction', 'sf'),
				),
				'rewrite' 				=> array( 
					'slug' => 'direction',
					'with_front'   => false,
					'hierarchical' => true, 
				),
				'hierarchical'          => true,
				'public'                => true,
				'show_ui'               => true,
			)
		);

		register_taxonomy(
			'venues_types',
			Tribe__Events__Main::VENUE_POST_TYPE,
			array(
				'labels' 				=> array(
					'name'			=> __( 'Categories', 'sf' ),
					'singular_name'	=> __( 'Category', 'sf' ),
				),
				'rewrite' 				=> array( 
					'slug' => 'category',
					'with_front'   => false,
					'hierarchical' => true, 
				),
				'hierarchical'          => true,
				'public'                => true,
				'show_ui'               => true,
			)
		);

		add_post_type_support(Tribe__Events__Main::VENUE_POST_TYPE, array('thumbnail', 'author', 'revisions'));
		add_post_type_support(Tribe__Events__Main::ORGANIZER_POST_TYPE, array('thumbnail', 'author', 'revisions'));
	}

	//add_action( 'tribe_events_eventform_top', 'event_config_extension', 10, 1 );
	function event_config_extension($event_id) {
		$fields_data = array();
		$debug_log = '';
		foreach (get_malivi_custom_fields() as $custom_field_name => $default_value) {
			$value = get_post_meta($event_id, $custom_field_name, true);
			$fields_data[$custom_field_name] = empty($value) ? $default_value : $value; 
		}

		foreach ($fields_data as $custom_field_name => $value) {
			$debug_log .= $custom_field_name . '=' . $value . ' ; ';
		}
		extract($fields_data);
		include( SF_DIR . 'templates/event_config.php' );
	}

	//add_action ('tribe_events_event_save', 'save_event_extensions', 10, 1);
	function save_event_extensions($event_id) {
		foreach (get_malivi_custom_fields() as $custom_field_name => $default_value) {
			$value = isset($_POST[$custom_field_name]) ? $_POST[$custom_field_name] : $default_value;
			update_post_meta( $event_id, $custom_field_name, esc_html($value) );
		}
		//add_metadata( 'post', $event_id, '_EventForChildren', '1' );
		//delete_metadata( 'post', $event_id, '_EventIsForChildren' );
	}

	//add_action( 'get_the_date', 'return_event_date_instead_of_publish_date', 10, 3 );
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

	add_filter('tribe_events_rewrite_prepared_slug', 'replace_rewrite_slugs', 10, 3);
	function replace_rewrite_slugs($sanitized_slug, $permastruct_name, $slug) {
		if ($permastruct_name == Tribe__Events__Main::POSTTYPE) {
			return "event";
		}
		if ($permastruct_name == Tribe__Events__Venue::POSTTYPE) {
			return 'place';
		}
		if ($permastruct_name == Tribe__Events__Organizer:: POSTTYPE) {
			return 'organization';
		}
		return $sanitized_slug;
	}

	// Choose the wordpress theme template to use
	//add_filter( 'template_include', 'events_handle_template_include', 20 );
	function events_handle_template_include($template) {
		if ( !class_exists( 'Tribe__Events__Main' ) ) {
			return;
		}
		echo '<div class="test">' . 'events-list-plugin template_include: ' . $template . '</div>';
		if (tribe_is_event_query()) {
			$type_name = '';
			if (is_singular( Tribe__Events__Main::POSTTYPE )) {
				$type_name = Tribe__Events__Main::POSTTYPE;
			}
			if (is_singular( Tribe__Events__Venue::POSTTYPE )) {
				$type_name = Tribe__Events__Venue::POSTTYPE;
			}
			if (is_singular( Tribe__Events__Organizer::POSTTYPE )) {
				$type_name = Tribe__Events__Organizer::POSTTYPE;
			}
			if (!empty($type_name)) {
				echo '<div class="test">' . 'events-list-plugin changed template to: ' . get_events_page_template( $type_name ) . '</div>';
				return get_page_template( $type_name );
			}
		}
		return $template;
	}

	// include our view class
	//add_action( 'template_redirect', 'events_handle_template_redirect' );
	function events_handle_template_redirect() {
	}

	function el_get_event_time($post_id) {
		if (!isset($post_id)) {
			$post_id = get_the_ID();
		}
		return get_post_meta( $post_id, '_EventStartDate', true );
	}

	function el_get_event_time_f($post_id, $date_format) {
		if (!isset($post_id)) {
			$post_id = get_the_ID();
		}
		return date_i18n( $date_format, strtotime(get_post_meta( $post_id, '_EventStartDate', true ) ) );
	}


	add_filter( 'term_link', 'override_category_link', 10, 3);

	function override_category_link($termlink, $term, $taxonomy) {
		return '#';
	}

	add_filter( 'check_user_is_events_writer', 'check_user_is_events_writer', 10, 1);

	function check_user_is_events_writer($value) {
		return $value || current_user_can('edit_tribe_events');
	}

?>