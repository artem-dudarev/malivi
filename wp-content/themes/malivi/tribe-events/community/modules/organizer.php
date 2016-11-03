<?php
/**
 * Event Submission Form Metabox For Organizers
 * This is used to add a metabox to the event submission form to allow for choosing or
 * creating an organizer for user submitted events.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/organizer.php
 *
 * @package Tribe__Events__Community__Main
 * @since  2.1
 * @author Modern Tribe Inc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! isset( $event ) ) {
	$event = null;
}
?>

<!-- Organizer -->
<div class="tribe-events-community-details eventForm bubble" id="event_organizer">

	<table class="tribe-community-event-info" cellspacing="0" cellpadding="0">

		<thead> <tr>
			<td colspan="2" class="tribe_sectionheader">
				<h4> <label class="<?php echo tribe_community_events_field_has_error( 'organizer' ) ? 'error' : ''; ?>"> <?php
					printf( __( '%s Details', 'tribe-events-community' ), tribe_get_organizer_label_singular() );
				?> </label> </h4>
			</td><!-- .tribe_sectionheader -->
		</tr> </thead>

		<?php
		// The organizer meta box will render everything within a <tbody>
		/*$organizer_meta_box = new Tribe__Events__Admin__Organizer_Chooser_Meta_Box( $event );
		$organizer_meta_box->render();*/
		?>

		<?php tribe_community_events_organizer_select_menu( $event ); ?>

	</table> <!-- #event_organizer -->

</div>