<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<div id="tribe-events-content" class="tribe-events-single">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header group-element">
			<h1 class="entry-title" itemprop="name"><?php the_title(); ?></h1>
		</header>
		<!-- Notices -->
		<?php tribe_the_notices() ?>

		<!-- Event Cost -->
		<?php if ( tribe_get_cost() ) : ?>
			<div class="tribe-events-event-cost">
				<span><?php echo tribe_get_cost( null, true ); ?></span>
			</div>
		<?php endif; ?>
		<div class="entry-content" itemprop="articleBody">
			<div class="tribe-events-list-event-description group-element">
				<!-- Event featured image, but exclude link -->
				<?php echo tribe_event_featured_image( $event_id, 'full', false ); ?>

				<!-- Event content -->
				<div class="tribe-event-description tribe-events-content">
					<?php the_content(); ?>
				</div>
			</div>
			<div class = "tribe-events-list-event-meta"> 
				<!-- .tribe-events-single-event-description -->
				<?php do_action( 'tribe_events_single_event_after_the_content' ) ?>

				<!-- Event meta -->
				<?php do_action( 'tribe_events_single_event_before_the_meta' ) ?>
				<?php tribe_get_template_part( 'modules/meta' ); ?>
				<?php //do_action( 'tribe_events_single_event_after_the_meta' ) ?>
				<?php wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'flat' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) ); ?>
			</div>
		</div>
	</article>
	<?php 
		if ( tribe_get_option( 'showComments', true ) ) {
			comments_template();
		} 
	?>
</div>
