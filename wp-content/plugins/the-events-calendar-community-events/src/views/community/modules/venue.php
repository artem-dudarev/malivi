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

$venue_label_singular = tribe_get_venue_label_singular();
$venue_name           = tribe_get_venue();

if ( ! empty( $_POST ) ) {
	$venue_name = isset( $_POST['venue']['Venue'] ) ? esc_attr( $_POST['venue']['Venue'] ) : '';
	$venue_phone = isset( $_POST['venue']['Phone'] ) ? esc_attr( $_POST['venue']['Phone'] ) : '';
	$venue_website = isset( $_POST['venue']['URL'] ) ? esc_attr( $_POST['venue']['URL'] ) : '';
	$venue_address = isset( $_POST['venue']['Address'] ) ? esc_attr( $_POST['venue']['Address'] ) : '';
	$venue_city = isset( $_POST['venue']['City'] ) ? esc_attr( $_POST['venue']['City'] ) : '';
	$venue_province = isset( $_POST['venue']['Province'] ) ? esc_attr( $_POST['venue']['Province'] ) : '';
	$venue_state = isset( $_POST['venue']['State'] ) ? esc_attr( $_POST['venue']['State'] ) : '';
	$venue_country = isset( $_POST['venue']['Country'] ) ? esc_attr( $_POST['venue']['Country'] ) : '';
	$venue_zip = isset( $_POST['venue']['Zip'] ) ? esc_attr( $_POST['venue']['Zip'] ) : '';
	$venue_show_map_link = isset( $_POST['EventShowMapLink'] ) ? esc_attr( $_POST['EventShowMapLink'] ) : '';
	$venue_show_map = isset( $_POST['EventShowMap'] ) ? esc_attr( $_POST['EventShowMap'] ) : '';
}
elseif ( $venue_name ) {
	$postId  = Tribe__Events__Main::postIdHelper();
	$venue_show_map_link = get_post_meta( $postId, '_EventShowMapLink', true );
	$venue_show_map = tribe_embed_google_map( $postId );
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
					printf( __( '%s Details', 'tribe-events-community' ), $venue_label_singular );
				?></label></h4>
			</td><!-- .tribe_sectionheader -->
		</tr>

		<?php tribe_community_events_venue_select_menu( $event ); ?>

		<?php if ( ! tribe_community_events_is_venue_edit_screen() ) { ?>
		<tr class="venue">
			<td>
				<label for="VenueVenue" <?php if ( $event && $_POST && empty( $venue_name ) ) echo 'class="error"'; ?>>
					<?php printf( __( '%s Name', 'tribe-events-community' ), $venue_label_singular ); ?>:
				</label>
			</td>
			<td>
				<input type="text" id="VenueVenue" name="venue[Venue]" size="25"  value="<?php esc_attr_e( $venue_name ); ?>" />
			</td>
		</tr><!-- .venue -->
		<?php } ?>

		<tr class="venue">
			<td>
				<label for="VenueAddress">
					<?php esc_html_e( 'Address', 'tribe-events-community' ); ?>:
				</label>
			</td>
			<td>
				<input type="text" id="VenueAddress" name="venue[Address]" size="25" value="<?php esc_attr_e( $venue_address ); ?>" />
			</td>
		</tr><!-- .venue -->

		<tr class="venue">
			<td>
				<label for="VenueCity">
					<?php esc_html_e( 'City', 'tribe-events-community' ); ?>:
				</label>
			</td>
			<td><input type="text" id="VenueCity" name="venue[City]" size="25" value="<?php esc_attr_e( $venue_city ); ?>" /></td>
		</tr><!-- .venue -->

		<tr class="venue">
			<td>
				<label for="EventCountry">
					<?php esc_html_e( 'Country', 'tribe-events-community' ); ?>:
				</label>
			</td>
			<td>
				<select class="chosen" name="venue[Country]" id="EventCountry">
					<?php
					foreach (
						Tribe__View_Helpers::constructCountries() as $abbr => $fullname ) {
						echo '<option value="'. esc_attr( $fullname ) .'" ';

						selected( $venue_country == $fullname );
						echo '>' . esc_html( $fullname ) . '</option>';
					} ?>
				</select>
			</td>
		</tr><!-- .venue -->

		<tr class="venue">
			<?php
			if ( ! isset( $venue_stateProvince ) || $venue_stateProvince == '' ) {
				$venue_stateProvince = -1;
			}
			?>
			<td>
				<label for="StateProvinceText">
					<?php esc_html_e( 'State or Province', 'tribe-events-community' ); ?>:
				</label>
			</td>
			<td>
				<input id="StateProvinceText" name="venue[Province]" type="text" name="" size="25" value="<?php echo esc_attr( isset( $venue_province ) && $venue_province != '' && $venue_province != -1 ? $venue_province : '' ); ?>" />
				<select class="chosen" id="StateProvinceSelect" name="venue[State]">
					<option value=""><?php esc_html_e( 'Select a State', 'tribe-events-community' ); ?></option>
					<?php foreach ( Tribe__View_Helpers::loadStates() as $abbr => $fullname ) {
						echo '<option value="' . esc_attr( $abbr ) .'" ';
						selected( $venue_state == $abbr );
						echo '>'. esc_html( $fullname ) .'</option>'. "\n";
					} ?>
				</select>
			</td>
		</tr><!-- .venue -->

		<tr class="venue">
			<td>
				<label for="EventZip">
					<?php esc_html_e( 'Postal Code', 'tribe-events-community' ); ?>:
				</label>
			</td>
			<td>
				<input type="text" id="EventZip" name="venue[Zip]" size="6" value="<?php esc_attr_e( $venue_zip ); ?>" />
			</td>
		</tr><!-- .venue -->

		<tr class="venue">
			<td>
				<label for="EventPhone">
					<?php esc_html_e( 'Phone', 'tribe-events-community' ); ?>:
				</label>
			</td>
			<td>
				<input type="text" id="EventPhone" name="venue[Phone]" size="14" value="<?php esc_attr_e( $venue_phone ); ?>" />
			</td>
		</tr><!-- .venue -->

		<tr class="venue">
			<td>
				<label for="EventWebsite">
					<?php esc_html_e( 'Website', 'tribe-events-community' ); ?>:
				</label>
			</td>
			<td>
				<input type="text" id="EventWebsite" name="venue[URL]" size="14" value="<?php esc_attr_e( $venue_website ); ?>" />
			</td>
		</tr><!-- .venue -->

		<?php
		if ( ! tribe_community_events_is_venue_edit_screen() ) {
			?>
			<tr id="google_map_link_toggle">
				<td>
					<label for="EventShowMapLink">
						<?php esc_html_e( 'Show Google Maps Link', 'tribe-events-community' ); ?>:
					</label>
				</td>
				<td>
					<input type="checkbox" id="EventShowMapLink" name="EventShowMapLink" value="1" <?php checked( $venue_show_map_link ); ?> />
				</td>
			</tr><!-- #google_map_link_toggle -->

			<?php
			if ( tribe_get_option( 'embedGoogleMaps', true ) ) {
				?>

				<tr id="google_map_toggle">
					<td>
						<label for="EventShowMap">
							<?php esc_html_e( 'Show Google Map', 'tribe-events-community' ); ?>:
						</label>
					</td>
					<td>
						<input type="checkbox" id="EventShowMap" name="EventShowMap" value="1" <?php checked( $venue_show_map ); ?> />
					</td>
				</tr><!-- #google_map_toggle -->
				<?php
			}
		} // if ( tribe_community_events_is_venue_edit_screen() )
		?>

	</table><!-- #event_venue -->

</div>
