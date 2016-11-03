<?php
/**
 * Event Submission Form Price Block
 * Renders the pricing fields in the submission form.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/cost.php
 *
 * @package Tribe__Events__Community__Main
 * @since  3.1
 * @author Modern Tribe Inc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$events_label_singular = tribe_get_event_label_singular();
$events_label_plural = tribe_get_event_label_plural();

global $post;

if ( $post instanceof WP_Post ) {
	$_EventCurrencyPosition = get_post_meta( $post->ID, '_EventCurrencyPosition', true );
}
if ( ! $_POST ) {
	$event_cost = get_post_meta( $post->ID, '_EventCost', '' );
	if (is_array($event_cost)) {
		$event_cost = $event_cost[0];
	}
} else {
	$event_cost = isset( $_POST['EventCost'] ) ? esc_attr( $_POST['EventCost'] ) : '';
}
?>

<!-- Event Cost -->
<?php
do_action( 'tribe_events_community_before_the_cost' );

if ( apply_filters( 'tribe_events_community_display_cost_section', true ) ) {
	?>
	<div class="tribe-events-community-details eventForm bubble" id="event_cost">
		<table class="tribe-community-event-info" cellspacing="0" cellpadding="0">
			<tr>
				<td colspan="2" class="tribe_sectionheader">
					<h4><?php esc_html_e( 'Cost', 'tribe-events-community' ); ?></h4>
				</td><!-- .tribe_sectionheader -->
			</tr>
			<tr>
				<td>
					<?php tribe_community_events_field_label( 'EventCost', __( 'Cost:', 'tribe-events-community' ) ); ?>
				</td>
				<td><input type="text" id="EventCost" name="EventCost" size="25" value="<?php echo esc_attr( $event_cost ); ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><small><?php esc_html_e( 'Leave blank to hide the field. Enter a 0 for events that are free.', 'tribe-events-community' ); ?></small></td>
			</tr>
		</table><!-- #event_cost -->
	</div><!-- .tribe-events-community-details -->
	<?php
}//end if
do_action( 'tribe_events_community_after_the_cost' );
