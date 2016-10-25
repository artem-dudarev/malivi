<?php
/**
 * Event Submission Form Metabox For Venues
 * This is used to add a metabox to the event submission form to allow for choosing or
 * creating a venue for user submitted events.
 *
 * This is ALSO used in the Venue edit view. Be careful to test changes in both places.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/venue.php
 *
 * @package Tribe__Events__Community__Main
 * @since  2.1
 * @author Modern Tribe Inc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$venue_name           = tribe_get_venue();

if ( ! empty( $_POST ) ) {
	$venue_name = isset( $_POST['venue']['Venue'] ) ? esc_attr( $_POST['venue']['Venue'] ) : '';
} elseif ( $venue_name ) {
	$postId  = Tribe__Events__Main::postIdHelper();
}

if ( ! isset( $event ) ) {
	$event = null;
}
?>

<!-- Venue -->
<div class="tribe-events-community-details eventForm bubble" id="event_venue">

	<table class="tribe-community-event-info" cellspacing="0" cellpadding="0">

		<tr>
			<td colspan="2" class="tribe_sectionheader">
				<h4><label class="<?php echo tribe_community_events_field_has_error( 'venue' ) ? 'error' : ''; ?>"><?php
					printf( __( '%s Details', 'tribe-events-community' ), tribe_get_venue_label_singular() );
				?></label></h4>
			</td><!-- .tribe_sectionheader -->
		</tr>

		<?php tribe_community_events_venue_select_menu( $event ); ?>

	</table><!-- #event_venue -->

</div>
