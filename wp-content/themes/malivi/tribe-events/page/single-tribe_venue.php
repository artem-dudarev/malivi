<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$venue_id = get_the_ID();
?>

<div id="tribe-events-content" class="tribe-events-single tribe-events-venue">

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header group-element">
			<h1 class="entry-title" itemprop="name"><?php the_title(); ?></h1>
		</header>

		<div class="entry-content" itemprop="articleBody">
			<div class="tribe-events-list-event-description group-element">
				<!-- Venue Featured Image -->
				<?php echo tribe_event_featured_image( null, 'full' ) ?>
				<!-- Venue Description -->
				<div class="tribe-venue-description tribe-events-content">
					<?php the_content(); ?>
				</div>
			</div>
			<div class = "tribe-events-list-event-meta">
				<!-- Venue Meta -->
				<?php do_action( 'tribe_events_single_venue_before_the_meta' ) ?>
				<?php tribe_get_template_part( 'modules/meta' ); ?>
				<?php do_action( 'tribe_events_single_venue_after_the_meta' ) ?>
			</div>
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
