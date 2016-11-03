<?php
/**
 * Single Event Meta Template
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe-events/modules/meta.php
 *
 * @package TribeEventsCalendar
 */

do_action( 'tribe_events_single_meta_before' );

// Do we want to group venue meta separately?
$set_venue_apart = apply_filters( 'tribe_events_single_event_the_meta_group_venue', false, get_the_ID() );
?>

<div class="tribe-events-single-section tribe-events-event-meta primary group-element tribe-clearfix">

<?php
do_action( 'tribe_events_single_event_meta_primary_section_start' );

if (get_post_type() == Tribe__Events__Main::POSTTYPE) {
	// Always include the main event details in this first section
	tribe_get_template_part( 'modules/meta/details' );
}
if ( ! $set_venue_apart && ! tribe_has_organizer() ) {
	// If we have no organizer, no need to separate the venue but we have a map to embed...
	tribe_get_template_part( 'modules/meta/venue' );
	tribe_get_template_part( 'modules/meta/map' );
} else {
	// If the venue meta has not already been displayed then it will be printed separately by default
	$set_venue_apart = true;
}

// Include organizer meta if appropriate
if ( tribe_has_organizer() ) {
	tribe_get_template_part( 'modules/meta/organizer' );
}

do_action( 'tribe_events_single_event_meta_primary_section_end' );
?>

</div>


<?php if ( $set_venue_apart && get_post_type() == Tribe__Events__Main::POSTTYPE) : ?>
<div class="tribe-events-single-section tribe-events-event-meta secondary group-element tribe-clearfix">
	<?php
	do_action( 'tribe_events_single_event_meta_secondary_section_start' );

	tribe_get_template_part( 'modules/meta/venue' );
	tribe_get_template_part( 'modules/meta/map' );

	do_action( 'tribe_events_single_event_meta_secondary_section_end' );
	?>
</div>
<?php
endif;
do_action( 'tribe_events_single_meta_after' );
