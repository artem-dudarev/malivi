<?php
/**
 * Email Template
 * The template for the Event Submission Notification Email
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/email-template.php
 *
 * @package Tribe__Events__Community__Main
 * @since  3.6
 * @author Modern Tribe Inc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


$event_link = esc_url( get_permalink( $tribe_event_id ) );

$organizer_id = tribe_get_event_meta( $post->ID, '_EventOrganizerID', true );
$organizer_name = tribe_get_organizer( $organizer_id );
$organizer_link = esc_url( get_permalink( $organizer_id ) );

$venue_id = tribe_get_event_meta( $post->ID, '_EventVenueID', true );
$venue_name = tribe_get_organizer( $venue_id );
$venue_link = esc_url( get_permalink( $venue_id ) );

?>
<html>
	<body>
		
		<h2><a href="<?php echo $event_link; ?>"><?php echo $post->post_title; ?></a></h2>
		<h4><?php echo tribe_get_start_date( $tribe_event_id ); ?> - <?php echo tribe_get_end_date( $tribe_event_id ); ?></h4>

		<hr />

		<h3><?php esc_html_e( 'Organizer', 'tribe-events-community' ); ?></h3>
		<p><a href="<?php echo $organizer_link; ?>"><?php echo $organizer_name; ?></a></p>

		<h3><?php esc_html_e( 'Venue', 'tribe-events-community' ); ?></h3>
		<p><a href="<?php echo $venue_link; ?>"><?php echo $venue_name; ?></a></p>

		<h3><?php esc_html_e( 'Description', 'tribe-events-community' ); ?></h3>
		<?php echo $post->post_content; ?>

		<hr />

		<h4><?php
		echo $this->getEditButton( $post, esc_html__( 'Review event', 'tribe-events-community' ) );
		if ( 'publish' == $post->post_status ) {
			echo ' | ';
			echo '<a href="' . $event_link .'">' . esc_html__( 'View event', 'tribe-events-community' ) . '</a>';
		}
		?></h4>
	</body>
</html>
