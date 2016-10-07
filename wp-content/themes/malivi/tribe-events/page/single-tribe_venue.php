<?php
/**
 * Single Venue Template
 * The template for a venue. By default it displays venue information and lists
 * events that occur at the specified venue.
 *
 * This view contains the filters required to create an effective single venue view.
 *
 * You can recreate an ENTIRELY new single venue view by doing a template override, and placing
 * a single-venue.php file in a tribe-events/pro/ directory within your theme directory, which
 * will override the /views/pro/single-venue.php.
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

$venue_id = get_the_ID();
?>

<div id="tribe-events-content" class="tribe-events-single tribe-events-venue">

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
					<!-- Venue Featured Image -->
					<?php echo tribe_event_featured_image( null, 'full' ) ?>
					<!-- Venue Description -->
					<div class="tribe-venue-description tribe-events-content">
						<?php the_content(); ?>
					</div>
				</div>
				
				<!-- Venue Meta -->
				<?php do_action( 'tribe_events_single_venue_before_the_meta' ) ?>
				<?php tribe_get_template_part( 'modules/meta' ); ?>
				<?php do_action( 'tribe_events_single_venue_after_the_meta' ) ?>


			</div><!-- .tribe-events-event-meta -->
		</div>

		<!-- Upcoming event list -->
		<?php do_action( 'tribe_events_single_venue_before_upcoming_events' ) ?>

		<?php
		// Use the tribe_events_single_venuer_posts_per_page to filter the number of events to get here.
		echo tribe_venue_upcoming_events( $venue_id ); 
		?>

		<?php do_action( 'tribe_events_single_venue_after_upcoming_events' ) ?>

	</article>

</div><!-- #tribe-events-content -->
