<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<div id="tribe-events-content" class="tribe-events-single tribe-events-organizer">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="group-element">
			<header class="entry-header">
				<h1 class="entry-title" itemprop="name"><?php the_title(); ?></h1>
			</header>
			<div class="entry-content" itemprop="articleBody">
				<?php flat_hook_entry_top(); ?>
				<div class="tribe-events-list-event-description">
					<!-- Event featured image, but exclude link -->
					<?php echo tribe_event_featured_image( $event_id, 'full', false ); ?>

					<!-- Event content -->
					<div class="tribe-event-description tribe-events-content">
						<?php the_content(); ?>
					</div>
					<div class="post-social-buttons-group">
						<?php do_action('post_social_like_buttons') ?>
					</div>
				</div>
			</div>
		</div>

		<!-- Organizer Meta -->
		<?php do_action( 'tribe_events_single_organizer_before_the_meta' ); ?>
		<?php tribe_get_template_part( 'modules/meta' ); ?>
		<?php do_action( 'tribe_events_single_organizer_after_the_meta' ) ?>

		<?php do_action( 'tribe_events_single_organizer_after_organizer' ) ?>

		<!-- Upcoming event list -->
		<?php do_action( 'tribe_events_single_organizer_before_upcoming_events' ) ?>

		<?php
		// Use the tribe_events_single_organizer_posts_per_page to filter the number of events to get here.
		echo tribe_organizer_upcoming_events( $organizer_id ); ?>

		<?php do_action( 'tribe_events_single_organizer_after_upcoming_events' ) ?>

		
	</article>
</div>
<?php
do_action( 'tribe_events_single_organizer_after_template' );
