<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<div id="tribe-events-content" class="tribe-events-single">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="entry-content group-element">
			<header class="entry-header">
				<h1 class="entry-title" itemprop="name"><?php the_title(); ?></h1>
			</header>
			<div class="entry-content" itemprop="articleBody">
				<!-- Notices -->
				<?php tribe_the_notices() ?>

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
		<div class="tribe-events-export-links">
			<?php do_action( 'tribe_events_single_event_after_the_content' ) ?>
		</div><!-- .tribe-events-export-links -->
		<!-- Event meta -->
		<?php do_action( 'tribe_events_single_event_before_the_meta' ) ?>
		<?php tribe_get_template_part( 'modules/meta' ); ?>
		<?php //do_action( 'tribe_events_single_event_after_the_meta' ) ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'flat' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) ); ?>
	</article>
	<?php 
		if ( tribe_get_option( 'showComments', true ) ) {
			comments_template();
		} 
	?>
</div>
