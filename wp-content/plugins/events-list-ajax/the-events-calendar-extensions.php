<?php

	// Наличие этого класса делает публичными некоторые типы, заданные в events_calendar
	class Tribe__Events__Pro__Main {
	}
	
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

	add_action( 'init', 'register_events_directions' );
	function register_events_directions() {
		// create a new taxonomy
		register_taxonomy(
			'events_directions',
			'tribe_events',
			array(
				'labels' 				=> array(
					'name'			=> __( 'Направления' ),
					'singular_name'	=> __( 'Направление' ),
				),
				'rewrite' 				=> array( 
					'slug' => 'event_direction',
					'with_front'   => false,
					'hierarchical' => true, 
				),
				'hierarchical'          => true,
				'public'                => true,
				'show_ui'               => true,
			)
		);

		add_post_type_support(Tribe__Events__Venue::POSTTYPE, array('thumbnail', 'author', 'revisions', 'comments'));
	}

	add_action( 'tribe_events_eventform_top', 'event_config_extension', 10, 1 );
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

	add_action ('tribe_events_event_save', 'save_event_extensions', 10, 1);
	function save_event_extensions($event_id) {
		foreach (get_malivi_custom_fields() as $custom_field_name => $default_value) {
			$value = isset($_POST[$custom_field_name]) ? $_POST[$custom_field_name] : $default_value;
			update_post_meta( $event_id, $custom_field_name, esc_html($value) );
		}
		//add_metadata( 'post', $event_id, '_EventForChildren', '1' );
		//delete_metadata( 'post', $event_id, '_EventIsForChildren' );
	}

	add_action( 'get_the_date', 'return_event_date_instead_of_publish_date', 10, 3 );
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

?>