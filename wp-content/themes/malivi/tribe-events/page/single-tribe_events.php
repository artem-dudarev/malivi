<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$notices = tribe_the_notices(false);
if (!empty($notices)) {
	$notices = '<div class="entry-header group-element">' . $notices . '</div>';
}
?>
<div id="tribe-events-content" class="tribe-events-single">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="group-element">
			<header class="entry-header">
				<h1 class="entry-title" itemprop="name"><?php the_title(); ?></h1>
			</header>
			<!-- Notices -->
			<?php echo $notices ?>

			<div class="tribe-events-list-event-description">
				<!-- Event featured image, but exclude link -->
				<?php echo tribe_event_featured_image( $event_id, 'full', false ); ?>

				<!-- Event content -->
				<div class="tribe-event-description tribe-events-content">
					<?php the_content(); ?>
				</div>
				<!--<div class="post-social-share-buttons-group">
					<script type="text/javascript">
						document.write(VK.Share.button(true,{type: "custom", text: "<img src=\"https://vk.com/images/share_32.png\" width=\"32\" height=\"32\" />"}));
					</script>
				</div>-->
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
	</article>
	<?php 
		if ( tribe_get_option( 'showComments', true ) ) {
			comments_template();
		} 
	?>
</div>
