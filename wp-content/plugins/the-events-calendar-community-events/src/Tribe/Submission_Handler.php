<?php


class Tribe__Events__Community__Submission_Handler {
	/**
	 * @var Tribe__Events__Community__Main
	 */
	protected $community = null;
	protected $submission = array();
	protected $original_submission = array();
	protected $event_id = 0;
	protected $valid = null;
	protected $messages = array();
	protected $invalid_fields = array();
	protected $post_type;

	public function __construct($post_type, $submission, $event_id ) {
		$this->post_type = $post_type;
		$this->community = Tribe__Events__Community__Main::instance();
		$this->original_submission = $submission;
		$submission[ 'ID' ] = $event_id;
		$this->submission = $submission;
		$this->event_id = $event_id;
	}

	public function validate() {
		if ( isset( $this->valid ) ) {
			return $this->valid;
		}
		
		$scrubber = new Tribe__Events__Community__Submission_Scrubber( $this->submission );
		$this->submission = $scrubber->scrub();

		$this->valid = true;

		// show no sympathy for spammers
		if ( ! is_user_logged_in() ) {
			$this->community->spam_check( $this->submission ); // exits on failure
		}

		if ( ! $this->validate_submission_has_required_fields( $this->submission ) ) {
			$this->valid = false;
		}

		if ( ! $this->validate_field_contents( $this->submission ) ) {
			$this->valid = false;
		}

		$this->valid = apply_filters( 'tribe_community_events_validate_submission', $this->valid, $this->submission, $this );
		return $this->valid;
	}

	/**
	 * Get the sanitized submission array
	 *
	 * @return array
	 */
	public function get_submission() {
		return $this->submission;
	}

	public function save($post_type) {
		$post_type_object = get_post_type_object( $post_type );
		$post_label_singular = $post_type_object->labels->singular_name;

		$event = get_post( $this->event_id );

		// if the post isn't an auto-draft, then we're updating a post. Otherwise, we'll consider it new
		if ( $this->event_id && $event && 'auto-draft' !== $event->post_status ) {
			$saved = $this->create_or_update_post($event, $post_type);
			
			if ( $saved ) {
				$this->add_message( sprintf( __( '%s updated. ', 'tribe-events-community' ), $post_label_singular ) . $this->community->get_view_edit_links( $this->event_id ) );
				$this->add_message( '<a href="' . esc_url( $this->community->getUrl( 'add', null, null, $post_type ) ) . '">' . sprintf( __( 'Submit another %s', 'tribe-events-community' ), strtolower( $post_label_singular ) ) . '</a>' );
				do_action( 'tribe_community_event_updated', $this->event_id );
			} else {
				$this->add_message( sprintf( __( 'There was a problem saving your %s, please try again.', 'tribe-events-community' ), strtolower( $post_label_singular ) ), 'error' );
			}
		} else {
			$this->submission['post_status'] = Tribe__Events__Community__Main::instance()->getOption( 'defaultStatus' );

			// if we DO have an event ID, then it is an auto-draft, and thus a new post
			if ( $this->event_id && $event) {
				$saved = $this->create_or_update_post($event, $post_type);
			} else {
				$saved = $this->create_or_update_post($event, $post_type);
			}

			if ( $saved ) {
				$this->event_id = $saved;
				$this->add_message( sprintf( __( '%s submitted. ', 'tribe-events-community' ), $post_label_singular ) . $this->community->get_view_edit_links( $this->event_id ) );
				$this->add_message( '<a href="' . esc_url( $this->community->getUrl( 'add' ) ) . '">' . sprintf( __( 'Submit another %s', 'tribe-events-community' ), strtolower( $post_label_singular ) ) . '</a>' );
				do_action( 'tribe_community_event_created', $this->event_id );
			} else {
				$this->add_message( sprintf( __( 'There was a problem submitting your %s, please try again.', 'tribe-events-community' ), strtolower( $post_label_singular ) ), 'error' );
			}
		}
		$this->save_submitted_attachment( $this->event_id );

		// Logged out or underprivileged users will not have terms automatically added during wp_insert_post
		/*if ( isset( $this->submission['tax_input'] ) ) {
			foreach ( (array) $this->submission['tax_input'] as $taxonomy => $terms ) {
				$taxonomy_obj = get_taxonomy( $taxonomy );
				if ( ! current_user_can( $taxonomy_obj->cap->assign_terms ) ) {
					wp_set_post_terms( $this->event_id, $terms, $taxonomy, true );
				}
			}
		}*/
		return $this->event_id;
	}

	protected function create_or_update_post($post, $post_type) {
		if($this->event_id && $post) {
			if ($post->post_type == Tribe__Events__Main::POSTTYPE) {
				return Tribe__Events__API::updateEvent( $post->ID, $this->submission );
			}
			if ($post->post_type == Tribe__Events__Main::VENUE_POST_TYPE) {
				return Tribe__Events__API::updateVenue( $post->ID, $this->submission );
			}
			if ($post->post_type == Tribe__Events__Main::ORGANIZER_POST_TYPE) {
				return Tribe__Events__API::updateOrganizer( $post->ID, $this->submission );
			}
		} else {
			if ($post_type == Tribe__Events__Main::POSTTYPE) {
				return Tribe__Events__API::createEvent( $this->submission );
			}
			if ($post_type == Tribe__Events__Main::VENUE_POST_TYPE) {
				return Tribe__Events__API::createVenue( $this->submission );
			}
			if ($post_type == Tribe__Events__Main::ORGANIZER_POST_TYPE) {
				return Tribe__Events__API::createOrganizer( $this->submission );
			}
		}
	}


	protected function save_submitted_attachment( $parent_id ) {
		// TODO: we still have to use the global here for now
		if ( isset( $_FILES['event_image']['name'] ) && ! empty( $_FILES['event_image']['name'] ) ) {
			return $this->community->insert_attachment( 'event_image', $parent_id, true );
		}
		return 0;
	}

	protected function validate_submission_has_required_fields( $submission ) {
		$valid = true;
		foreach ( $this->community->required_fields_for_submission() as $key ) {
			if ( ! $this->submission_has_value_for_key( $submission, $key ) ) {
				if ($valid) {
					$message = __( 'Not all fields are set', 'tribe-events-community' );
					$this->add_message( $message, 'error' );
				}
				
				$valid = false;
				$this->invalid_fields[] = $key;
			}
		}
		return $valid;
	}

	protected function submission_has_value_for_key( $submission, &$key ) {
		if (substr($key, 0, strlen('taxonomy_')) == 'taxonomy_') {
			$key = substr($key, strlen('taxonomy_'));
			if (empty($submission['tax_input'])) {
				return false;
			}
			return $this->is_array_valid($submission['tax_input'][$key]);
		}
		switch ( $key ) {
			case 'venue':
				if ( empty( $submission['Venue'] ) ) {
					return false;
				}
				if ( empty( $submission['Venue']['VenueID'] ) ) {
					return false;
				}
				if ( is_array($submission['Venue']['VenueID']) ) {
					return $this->is_array_valid($submission['Venue']['VenueID']);
				} else {
					if ( $submission['Venue']['VenueID'] == 0 ) {
						return false;
					}
				}
				return true;
			case 'organizer':
				if ( empty( $submission['Organizer'] ) ) {
					return false;
				}
				if ( empty( $submission['Organizer']['OrganizerID'] ) ) {
					return false;
				}
				if ( is_array($submission['Organizer']['OrganizerID']) ) {
					return $this->is_array_valid($submission['Organizer']['OrganizerID']);
				} else {
					if ( $submission['Organizer']['OrganizerID'] == 0 ) {
						return false;
					}
				}
				return true;

				return false;
			case 'event_image':
				if ( $this->event_id && has_post_thumbnail( $this->event_id ) ) {
					return true;
				}
				$attachment = $this->get_attachment_array();
				return ! empty( $attachment['name'] );
			default:
				return ! empty( $submission[ $key ] );
		}
	}

	protected function is_array_valid($array) {
		if (empty($array)) {
			return false;
		}
		foreach ($array as $key => $value) {
			if ($value == 0) {
				return false;
			}
		}
		return true;
	}

	protected function get_field_label( $field ) {
		$events_label_singular = tribe_get_event_label_singular();

		switch ( $field ) {
			default:
				if ( strpos( $field, '_ecp_custom_' ) === 0 ) {
					$label = $this->get_custom_field_label( $field );
				} else {
					$label = $this->format_field_name_as_label( $field );
				}
				break;
		}
		return apply_filters( 'tribe_community_form_field_label', $label, $field );
	}

	protected function format_field_name_as_label( $field ) {
		$regex = '/(?#! splitCamelCase Rev:20140412)
    # Split camelCase "words". Two global alternatives. Either g1of2:
      (?<=[a-z])      # Position is after a lowercase,
      (?=[A-Z])       # and before an uppercase letter.
    | (?<=[A-Z])      # Or g2of2; Position is after uppercase,
      (?=[A-Z][a-z])  # and before upper-then-lower case.
    /x';
		$parts = preg_split( $regex, $field );
		$label = implode( ' ', $parts );
		$label = str_replace( '_', ' ', $label );
		$label = ucwords( $label );
		return $label;
	}

	protected function get_custom_field_label( $name ) {
		$fields = tribe_get_option( 'custom-fields' );
		foreach ( $fields as $field ) {
			if ( $field['name'] == $name ) {
				return $field['label'];
			}
		}
		return $name;
	}

	protected function validate_field_contents( $submission ) {
		$valid = true;
		foreach ( $submission as $key => $value ) {
			if ( ! $this->is_field_valid( $key, $value ) ) {
				$message = __( 'Invalid value for %s', 'tribe-events-community' );
				$message = sprintf( $message, $this->get_field_label( $key ) );
				$this->add_message( $message, 'error' );
				$valid = false;
			}
		}
		if ( $attachment = $this->get_attachment_array() ) {
			if ( $attachment && isset( $attachment['error'] ) && $attachment['error'] ) {
				$this->add_message( $this->get_img_upload_error_msg( $attachment['error'] ), 'error' );
				$valid = false;
			}
			elseif ( $attachment && ! in_array( $attachment['type'], $this->image_mime_types() ) ) {
				$message = __( 'Images must be png, jpg, or gif', 'tribe-events-community' );
				$this->add_message( $message, 'error' );
				$valid = false;
			}
		}
		return $valid;
	}

	protected function image_mime_types() {
		return array(
			'image/png',
			'image/jpeg',
			'image/gif',
		);
	}

	protected function get_img_upload_error_msg( $upload_error_code ) {
		switch ( $upload_error_code ) {
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				return __( 'Image exceeded the allowed file size', 'tribe-events-community' );
				break;

			case UPLOAD_ERR_PARTIAL:
			case UPLOAD_ERR_NO_FILE:
				return __( 'The image failed to upload successfully', 'tribe-events-community' );
				break;

			default:
				return __( 'The uploaded image could not be processed', 'tribe-events-community' );
				break;
		}
	}

	protected function get_attachment_array() {
		// TODO: we still have to use the global here for now
		if ( empty( $_FILES ) || empty( $_FILES['event_image'] ) || empty( $_FILES['event_image']['name'] ) ) {
			return array();
		}
		return $_FILES['event_image'];
	}

	protected function is_field_valid( $key, $value ) {
		$valid = true;
		$valid = apply_filters( 'tribe_community_is_field_valid', $valid, $key, $value );
		return $valid;
	}

	public function get_messages() {
		return $this->messages;
	}

	public function add_message( $message, $type = 'update' ) {
		$message = apply_filters( 'tribe_events_community_submission_message', $message, $type );
		$this->messages[] = (object) array( 'message' => $message, 'type' => $type );
	}

	public function get_invalid_fields() {
		return array_unique( $this->invalid_fields );
	}
}
