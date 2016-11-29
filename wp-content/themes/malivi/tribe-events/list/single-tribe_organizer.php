<?php
/**
 * List View Single Event
 * This file contains one event in the list view
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/list/single-event.php
 *
 * @package TribeEventsCalendar
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($post_id) );
$title = get_the_title();


if (empty($thumbnail) || $thumbnail == false) {
	$image_content = '<div class="events-list-post-image-noimglink">';
	$image_content .= mb_substr($title, 0, 1);
	$image_content .= '</div>';
} else {
	$image_content = '<img class="events-list-post-image-link" src="'.$thumbnail['0'].'" alt="'.$title.'" />';
}
			
?>

<a class="events-list-row group-element coloring-for-group" href="<?php the_permalink() ?>" postid="<?php the_ID() ?>" >
	<?php the_views(); ?>
	<div class="events-list-post-image-thumbnail events-list-cell" >
		<?php echo $image_content;?>
	</div>

	<div class="events-list-post-text-cell events-list-post-text" >
		<div class="events-list-post-text-header">
			<?php echo $title; ?>
		</div>

		<div class="events-list-post-text-content">
			<?php echo event_list_custom_excerpt('25', 'true'); ?>
		</div>
	</div>
</a>