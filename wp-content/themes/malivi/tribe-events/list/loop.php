<?php
/**
 * List View Loop
 * This file sets up the structure for the list loop
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/list/loop.php
 *
 * @package TribeEventsCalendar
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
} ?>

<?php
global $post;
global $more;
$more = false;
?>

<div class="events-list-table">

	<?php while ( have_posts() ) : the_post(); ?>
		<?php tribe_get_template_part( 'list/single', get_post_type() ) ?>
	<?php endwhile; ?>

</div><!-- .tribe-events-loop -->
