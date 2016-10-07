<?php
/**
 * Single Organizer Template
 * The template for an organizer. By default it displays organizer information and lists
 * events that occur with the specified organizer.
 *
 * This view contains the filters required to create an effective single organizer view.
 *
 * You can recreate an ENTIRELY new single organizer view by doing a template override, and placing
 * a Single_Organizer.php file in a tribe-events/pro/ directory within your theme directory, which
 * will override the /views/pro/single_organizer.php.
 *
 * You can use any or all filters included in this file or create your own filters in
 * your functions.php. In order to modify or extend a single filter, please see our
 * readme on templates hooks and filters (TO-DO)
 *
 * @package TribeEventsCalendarPro
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

?>
<div id="tribe-events-content" class="tribe-events-single tribe-events-organizer">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<?php do_action( 'tribe_events_single_venue_before_title' ) ?>
			<h1 class="entry-title" itemprop="name"><?php the_title(); ?></h1>
			<?php do_action( 'tribe_events_single_venue_after_title' ) ?>
		</header>
		<div class="entry-content" itemprop="articleBody">
			<?php flat_hook_entry_top(); ?>
			<div class="tribe-events-venue-meta tribe-clearfix">
				<div class="tribe-events-list-event-description">
					<!-- Event featured image, but exclude link -->
					<?php echo tribe_event_featured_image( $event_id, 'full', false ); ?>

					<!-- Event content -->
					<?php do_action( 'tribe_events_single_event_before_the_content' ) ?>
					<div class="tribe-event-description tribe-events-content">
						<?php the_content(); ?>
					</div>
				</div>
				<!-- Organizer Meta -->
				<?php do_action( 'tribe_events_single_organizer_before_the_meta' ); ?>
				<?php echo tribe_get_meta_group( 'tribe_event_organizer' ) ?>
				<?php do_action( 'tribe_events_single_organizer_after_the_meta' ) ?>
			</div>
			<!-- .tribe-events-organizer-meta -->
		<?php do_action( 'tribe_events_single_organizer_after_organizer' ) ?>

		<!-- Upcoming event list -->
		<?php do_action( 'tribe_events_single_organizer_before_upcoming_events' ) ?>

		<?php
		// Use the tribe_events_single_organizer_posts_per_page to filter the number of events to get here.
		echo tribe_organizer_upcoming_events( $organizer_id ); ?>

		<?php do_action( 'tribe_events_single_organizer_after_upcoming_events' ) ?>

		</div><!-- .tribe-events-organizer -->
	</article>
</div>
<?php
do_action( 'tribe_events_single_organizer_after_template' );
