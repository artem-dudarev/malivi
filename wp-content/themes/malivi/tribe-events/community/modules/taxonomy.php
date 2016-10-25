<?php
/**
 * Event Submission Form Taxonomy Block
 * Renders the taxonomy field in the submission form.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/taxonomy.php
 *
 * @package Tribe__Events__Community__Main
 * @since  3.1
 * @author Modern Tribe Inc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$categories_label = get_taxonomy(Tribe__Events__Main::TAXONOMY)->label;
$directions_label = get_taxonomy('events_directions')->label;

?>
<!-- Event Categories -->
<?php do_action( 'tribe_events_community_before_the_categories' ); ?>
<div class="tribe-events-community-details eventForm bubble" id="event_categories">
	<table class="tribe-community-event-info" cellspacing="0" cellpadding="0">
		<tr>
			<td class="tribe_sectionheader">
				<h4 class="event-time"><?php echo $categories_label; ?></h4>
			</td>
		</tr>
		<tr>
			<td><?php Tribe__Events__Community__Main::instance()->formCategoryDropdown( get_post(), Tribe__Events__Main::TAXONOMY ); ?></td>
		</tr>
	</table><!-- .tribe-community-event-info -->

</div><!-- .tribe-events-community-details -->

<div class="tribe-events-community-details eventForm bubble" id="event_directions">
	<table class="tribe-community-event-info" cellspacing="0" cellpadding="0">
		<tr>
			<td class="tribe_sectionheader">
				<h4 class="event-time"><?php echo $directions_label; ?></h4>
			</td>
		</tr>
		<tr>
			<td><?php Tribe__Events__Community__Main::instance()->formCategoryDropdown( get_post(), 'events_directions' ); ?></td>
		</tr>
	</table><!-- .tribe-community-event-info -->

</div><!-- .tribe-events-community-details -->

<?php
do_action( 'tribe_events_community_after_the_categories' );
?>