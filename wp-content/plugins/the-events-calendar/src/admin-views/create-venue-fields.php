<?php
global $post;
?>
<tr class="linked-post venue">
	<td class='tribe-table-field-label'><?php esc_html_e( 'Address:', 'the-events-calendar' ); ?></td>
	<td>
		<input tabindex="<?php tribe_events_tab_index(); ?>" type='text' name='venue[Address][]' size='25' value='<?php if ( isset( $_VenueAddress ) ) {
			echo esc_attr( $_VenueAddress );
		} ?>' /></td>
</tr>
<tr class="linked-post venue">
	<td class='tribe-table-field-label'><?php esc_html_e( 'City:', 'the-events-calendar' ); ?></td>
	<td>
		<input tabindex="<?php tribe_events_tab_index(); ?>" type='text' name='venue[City][]' size='25' value='<?php if ( isset( $_VenueCity ) ) {
			echo esc_attr( $_VenueCity );
		} ?>' /></td>
</tr>
<tr class="linked-post venue">
	<td class='tribe-table-field-label'><?php esc_html_e( 'Country:', 'the-events-calendar' ); ?></td>
	<td>
		<?php
		$countries = Tribe__View_Helpers::constructCountries( $post->ID );

		if ( isset( $_VenueCountry ) && $_VenueCountry ) {
			$current = $_VenueCountry;
		} else {
			$current = null;
		}

		if ( is_array( $current ) && isset( $current[1] ) ) {
			$current = $current[1];
		}
		?>
		<select class="chosen" tabindex="<?php tribe_events_tab_index(); ?>" name='venue[Country][]' id="EventCountry">
			<?php
			foreach ( $countries as $abbr => $fullname ) {
				if ( $abbr == '' ) {
					echo '<option value="">' . esc_html( $fullname ) . '</option>';
				} else {
					echo '<option value="' . esc_attr( $fullname ) . '" ';

					selected( ( $current == $fullname ) );

					echo '>' . esc_html( $fullname ) . '</option>';
				}
			}
			?>
		</select>
	</td>
</tr>
<tr class="linked-post venue">
	<?php if ( ! isset( $_VenueStateProvince ) || $_VenueStateProvince == '' ) {
		$_VenueStateProvince = - 1;
	};
	$currentState = ( $_VenueStateProvince == - 1 ) ? '' : $_VenueStateProvince;
	$currentProvince = empty( $_VenueProvince ) ? '' : $_VenueProvince;
	?>
	<td class='tribe-table-field-label'><?php esc_html_e( 'State or Province:', 'the-events-calendar' ); ?></td>
	<td>
		<input tabindex="<?php tribe_events_tab_index(); ?>" id="StateProvinceText" name="venue[Province][]" type='text' name='' size='25' value='<?php echo esc_attr( $currentProvince ); ?>' />
		<select class="chosen" tabindex="<?php tribe_events_tab_index(); ?>" id="StateProvinceSelect" name="venue[State]">
			<option value=""><?php esc_html_e( 'Select a State:', 'the-events-calendar' ); ?></option>
			<?php
			foreach ( Tribe__View_Helpers::loadStates() as $abbr => $fullname ) {
				echo '<option value="' . esc_attr( $abbr ) . '"';
				selected( ( ( $_VenueStateProvince != - 1 ? $_VenueStateProvince : $currentState ) == $abbr ) );
				echo '>' . esc_html( $fullname ) . '</option>';
			}
			?>
		</select>

	</td>
</tr>
<tr class="linked-post venue">
	<td class='tribe-table-field-label'><?php esc_html_e( 'Postal Code:', 'the-events-calendar' ); ?></td>
	<td>
		<input tabindex="<?php tribe_events_tab_index(); ?>" type='text' id='EventZip' name='venue[Zip][]' size='6' value='<?php if ( isset( $_VenueZip ) ) {
			echo esc_attr( $_VenueZip );
		} ?>' /></td>
</tr>
<tr class="linked-post venue">
	<td class='tribe-table-field-label'><?php esc_html_e( 'Phone:', 'the-events-calendar' ); ?></td>
	<td>
		<input tabindex="<?php tribe_events_tab_index(); ?>" type='text' id='EventPhone' name='venue[Phone][]' size='14' value='<?php if ( isset( $_VenuePhone ) ) {
			echo esc_attr( $_VenuePhone );
		} ?>' /></td>
</tr>
<tr class="linked-post venue">
	<td class='tribe-table-field-label'><?php esc_html_e( 'Website:', 'the-events-calendar' ); ?></td>
	<td>
		<input tabindex="<?php tribe_events_tab_index(); ?>" type='text' id='EventWebsite' name='venue[URL][]' size='14' value='<?php if ( isset( $_VenueURL ) ) {
			echo esc_attr( $_VenueURL );
		} ?>' /></td>
</tr>
