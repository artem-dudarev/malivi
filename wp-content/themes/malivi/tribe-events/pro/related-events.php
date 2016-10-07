<?php
/**
 * Related Events Template
 * The template for displaying related events on the single event page.
 *
 * You can recreate an ENTIRELY new related events view by doing a template override, and placing
 * a related-events.php file in a tribe-events/pro/ directory within your theme directory, which
 * will override the /views/pro/related-events.php.
 *
 * You can use any or all filters included in this file or create your own filters in
 * your functions.php. In order to modify or extend a single filter, please see our
 * readme on templates hooks and filters
 *
 * @package TribeEventsCalendarPro
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$posts = tribe_get_related_posts();

if ( is_array( $posts ) && ! empty( $posts ) ) : ?>

<h3 class="tribe-events-related-events-title"><?php printf( __( 'Related %s', 'tribe-events-calendar-pro' ), tribe_get_event_label_plural() ); ?></h3>

<div class="events-list-table">
	<?php 
	foreach ( $posts as $post ) {
		tribe_get_template_part( 'list/row' );
	} 
	?>
</ul>
<?php
endif;
