<?php
/**
 * Edit Venue Form
 * This is used to edit an event venue.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/edit-venue.php
 *
 * @package Tribe__Events__Community__Main
 * @since  3.1
 * @author Modern Tribe Inc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$venue_label_singular = tribe_get_venue_label_singular();
?>

<?php tribe_get_template_part( 'community/modules/header-links' ); ?>

<form method="post">
	<input type="hidden" name="post_ID" id="post_ID" value="<?php echo absint( $tribe_event_id ); ?>"/>
	<?php wp_nonce_field( 'ecp_event_submission' ); ?>

	<!-- Venue Title -->
	<?php $venue_name = esc_attr( tribe_get_venue() ); ?>
	<div class="events-community-post-title">
		<label for="post_title" class="<?php echo ( $_POST && empty( $venue_name ) ) ? 'error' : ''; ?>">
			<?php esc_html_e( 'Name', 'tribe-events-community' ); ?>
		</label>
		<input type="text" name="post_title" value="<?php echo esc_attr( $venue_name ); ?>"/>

	</div><!-- .events-community-post-title -->

	<!-- Venue Description -->
	<div class="events-community-post-content">

		<label for="post_content">
			<?php esc_html_e( 'Description', 'tribe-events-community' ); ?>
			<small class="req"></small>
		</label>

		<?php // if admin wants rich editor (and using WP 3.3+) show the WYSIWYG, otherwise default to a textarea
		$content = tribe_community_events_get_venue_description();
		if ( Tribe__Events__Community__Main::instance()->useVisualEditor && function_exists( 'wp_editor' ) ) {
			$settings = array(
				'wpautop' => true,
				'media_buttons' => false,
				'editor_class' => 'frontend',
				'textarea_rows' => 5,
			);
			echo wp_editor( $content, 'tcepostcontent', $settings );
		} else {
			echo '<textarea name="tcepostcontent">' . esc_textarea( $content ) . '</textarea>';
		} ?>

	</div><!-- .events-community-post-content -->

	<?php tribe_get_template_part( 'community/modules/image' ); ?>

	<?php tribe_get_template_part( 'community/modules/venue-fields' ); ?>

	<!-- Form Submit -->
	<div class="tribe-events-community-footer">

		<input type="submit" id="submit" class="button submit events-community-submit" value="<?php
			if ( isset( $tribe_event_id ) && $tribe_event_id ) {
				echo esc_attr( sprintf( __( 'Update %s', 'tribe-events-community' ), $venue_label_singular ) );
			} else {
				echo esc_attr( sprintf( __( 'Submit %s', 'tribe-events-community' ), $venue_label_singular ) );
			}
		?>" name="community-event" />

	</div><!-- .tribe-events-community-footer -->

</form>
