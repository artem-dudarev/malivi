<?php
/**
 * My Events List Template
 * The template for a list of a users events.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/event-list.php
 *
 * @package Tribe__Events__Community__Main
 * @since  2.1
 * @author Modern Tribe Inc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$organizer_label_singular = tribe_get_organizer_label_singular();
$venue_label_singular = tribe_get_venue_label_singular();
$events_label_plural = tribe_get_event_label_plural();

$event_type_object = get_post_type_object(Tribe__Events__Main::POSTTYPE);
$venue_type_object = get_post_type_object(Tribe__Events__Main::VENUE_POST_TYPE);
$organizer_type_object = get_post_type_object(Tribe__Events__Main::ORGANIZER_POST_TYPE);

$is_past_events = !empty( $_GET['eventDisplay'] ) && 'past' === $_GET['eventDisplay'];

// List "Add New" Button
do_action( 'tribe_ce_before_event_list_top_buttons' ); ?>
<div class="community-add-new">
	<?php if(current_user_can($event_type_object->cap->publish_posts)) : ?>
	<div id="add-new-event"><a href="<?php echo esc_url( tribe_community_events_add_event_link() ); ?>" class="button"><?php echo  __( 'Add New Event', 'tribe-events-community' ); ?></a></div>
	<?php endif; ?>

	<?php if(current_user_can($venue_type_object->cap->publish_posts)) : ?>
		<div id="add-new=place"><a href="<?php echo esc_url( tribe_community_events_add_venue_link() ); ?>" class="button"><?php echo  __( 'Add New Venue', 'tribe-events-community' ); ?></a></div>
	<?php endif; ?>

	<?php if(current_user_can($organizer_type_object->cap->publish_posts)) : ?>
	<div id="add-new-organizer"><a href="<?php echo esc_url( tribe_community_events_add_organizer_link() ); ?>" class="button"><?php echo  __( 'Add New Organizer', 'tribe-events-community' ); ?></a></div>
	<?php endif; ?>
</div>

<?php // list admin link
$current_user = wp_get_current_user(); ?>
<div class="community-user-section">
	<div>
		<?php esc_html_e( 'Not', 'tribe-events-community' ); ?>
		<i><?php echo $current_user->display_name; ?></i> ?
		<a href="<?php echo esc_url( tribe_community_events_logout_url() ); ?>">
			<?php esc_html_e( 'Log Out', 'tribe-events-community' ); ?>
		</a>
	</div>
	<br>
	<div>
		<?php
		add_filter( 'get_pagenum_link', array( Tribe__Events__Community__Main::instance(), 'fix_pagenum_link' ) );
		$link = get_pagenum_link( 1 );
		$link = remove_query_arg( 'eventDisplay', $link );

		if ( $is_past_events ) {
			?>
			<a href="<?php echo esc_url( $link . '?eventDisplay=list' ); ?>"><?php echo esc_html__( 'View upcoming events', 'tribe-events-community' ); ?></a>
			<?php
		} else {
			?>
			<a href="<?php echo esc_url( $link . '?eventDisplay=past' ); ?>"><?php echo esc_html__( 'View past events', 'tribe-events-community' ); ?></a>
			<?php
		}
		?>
	</div>
</div>

<div style="clear:both"></div>

<?php // list pagination
if ( ! $events->have_posts() ) {
	if ($is_past_events) {
		$this->enqueueOutputMessage( esc_html__( 'You have no past events.', 'tribe-events-community' ) );
	} else {
		$this->enqueueOutputMessage( esc_html__( 'You have no upcoming events.', 'tribe-events-community' ) );
	}
}
echo tribe_community_events_get_messages();
$tbody = '';

echo $this->pagination( $events, '', $this->paginationRange );

do_action( 'tribe_ce_before_event_list_table' );
if ( $events->have_posts() ) {
	?>
	<div class="my-events-table-wrapper">
		<table class="events-community my-events" cellspacing="0" cellpadding="4">
			<thead id="my-events-display-headers">
				<tr>
					<th class="essential persist"><?php esc_html_e( 'Status', 'tribe-events-community' ); ?></th>
					<th class="essential persist"><?php esc_html_e( 'Title', 'tribe-events-community' ); ?></th>
					<th class="essential"><?php _e( $organizer_label_singular, 'tribe-events-community' ); ?></th>
					<th class="essential"><?php _e( $venue_label_singular, 'tribe-events-community' ); ?></th>
					<th class="optional1"><?php esc_html_e( 'Category', 'tribe-events-community' ); ?></th>
					<th class="essential"><?php esc_html_e( 'Start Date', 'tribe-events-community' ); ?></th>
					<th class="essential"><?php esc_html_e( 'End Date', 'tribe-events-community' ); ?></th>
				</tr>
			</thead><!-- #my-events-display-headers -->

			<tbody id="the-list"><tr>
				<?php $rewriteSlugSingular = Tribe__Settings_Manager::get_option( 'singleEventSlug', 'event' );
				global $post;
				$old_post = $post;
				while ( $events->have_posts() ) {
					$e = $events->next_post();
					$post = $e; ?>

					<tr>

						<td><?php echo Tribe__Events__Community__Main::instance()->getEventStatusIcon( $post->post_status ); ?></td>
						<td>
						<?php
						$canView = ( get_post_status( $post->ID ) == 'publish' || current_user_can( 'edit_post', $post->ID ) );
						$canEdit = current_user_can( 'edit_post', $post->ID );
						$canDelete = current_user_can( 'delete_post', $post->ID );
						if ( $canEdit ) {
							?>
							<span class="title">
								<a href="<?php echo esc_url( tribe_community_events_edit_event_link( $post->ID ) ); ?>"><?php echo $post->post_title; ?></a>
							</span>
							<?php
						} else {
							echo $post->post_title;
						}
						?>
						<div class="row-actions">
							<?php
							if ( $canView ) {
								?>
								<span class="view">
									<a href="<?php echo esc_url( tribe_get_event_link( $post ) ); ?>"><?php esc_html_e( 'View', 'tribe-events-community' ); ?></a>
								</span>
								<?php
							}

							if ( $canEdit ) {
								echo Tribe__Events__Community__Main::instance()->getEditButton( $post, __( 'Edit', 'tribe-events-community' ), '<span class="edit wp-admin events-cal"> |', '</span> ' );
							}

							if ( $canDelete ) {
								echo Tribe__Events__Community__Main::instance()->getDeleteButton( $post );
							}
							do_action( 'tribe_ce_event_list_table_row_actions', $post );
							?>
						</div><!-- .row-actions -->
						</td>

						<td>
							<?php
							if ( tribe_has_organizer( $post->ID ) ) {
								$organizer_id = tribe_get_organizer_id( $post->ID );
								if ( current_user_can( 'edit_post', $organizer_id ) ) {
									echo '<a href="'. esc_url( Tribe__Events__Community__Main::instance()->getUrl( 'edit', $organizer_id, null, Tribe__Events__Main::ORGANIZER_POST_TYPE ) ) .'">'. tribe_get_organizer( $post->ID ) .'</a>';
								} else {
									echo tribe_get_organizer( $post->ID );
								}
							}
							?>
						</td>

						<td>
							<?php
							if ( tribe_has_venue( $post->ID ) ) {
								$venue_id = tribe_get_venue_id( $post->ID );
								if ( current_user_can( 'edit_post', $venue_id ) ) {
									echo '<a href="' . esc_url( Tribe__Events__Community__Main::instance()->getUrl( 'edit', $venue_id, null, Tribe__Events__Main::VENUE_POST_TYPE ) ) . '">'. tribe_get_venue( $post->ID ) .'</a>';
								} else {
									echo tribe_get_venue( $post->ID );
								}
							}
							?>
						</td>

						<td><?php echo Tribe__Events__Admin_List::custom_columns( 'events-cats', $post->ID, false ); ?></td>

						<td>
							<?php echo esc_html( tribe_get_start_date( $post->ID, Tribe__Events__Community__Main::instance()->eventListDateFormat ) ) ?>
						</td>

						<td>
							<?php echo esc_html( tribe_get_end_date( $post->ID, Tribe__Events__Community__Main::instance()->eventListDateFormat ) ) ?>
						</td>

					</tr>

				<?php } // end list loop
				$post = $old_post; ?>

			</tbody><!-- #the-list -->

			<?php do_action( 'tribe_ce_after_event_list_table' ); ?>

		</table><!-- .events-community -->

	</div><!-- .my-events-table-wrapper -->

	<?php // list pagination
	echo $this->pagination( $events, '', $this->paginationRange );

} // if ( $events->have_posts() )
