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
$post_id = get_the_ID();

$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($post_id) );
$title = get_the_title();

$current_date = strtotime(current_time("d.m.Y"));
$event_date = strtotime(el_get_event_time_f($post_id,'d.m.Y'));
if ($event_date < $current_date) {
	$event_date = $current_date; // Начало события может быть не сегодня а раньше
}

// Если событие идет весь день, то вместо времени начала показываем надпись "весь день"
if (tribe_event_is_all_day($post_id)) {
	$thumbnail_title = 'ВЕСЬ ДЕНЬ';
	$thumbnail_title_class = "-small";
} else {
	$thumbnail_title = el_get_event_time_f($post_id, 'H:i');
}
// Укажем день события
$show_thumbnail_subtitle = true;
if ($current_date == $event_date) {
	
	$thumbnail_subtitle = 'Сегодня';
} else { // Иначе показываем дату
	$thumbnail_subtitle = el_get_event_time_f($post_id, 'd M.');
	/*$current_year = current_time('Y');
	$event_year = el_get_event_time_f($post_id, 'Y');
	if ($current_year != $event_year) {
		$thumbnail_subtitle .= $event_year;
	}*/
}

if (empty($thumbnail) || $thumbnail == false) {
	$image_content = '<div class="events-list-post-image-noimglink">';
	$image_content .= mb_substr($title, 0, 1);
	$image_content .= '</div>';
} else {
	$image_content = '<img class="events-list-post-image-link" src="'.$thumbnail['0'].'" alt="'.$title.'" />';
}

$cost_utils = Tribe__Events__Cost_Utils::instance();
$prices = $cost_utils->get_event_costs($post_id);
foreach ($prices as $cost) {
	if ($cost == '0') {
		$is_event_free = true;
		break;
	}
}

$row_class = '';
if (Tribe__Date_Utils::is_weekend($event_date)) {
	$row_class = 'coloring-for-group-weekend';
}

global $events_list_last_date;
if (isset($events_list_last_date) && $events_list_last_date != -1 && $events_list_last_date != $event_date) {
	$divider = date_i18n('d M. ( l )', $event_date);
}
$events_list_last_date = $event_date;
?>

<?php if (isset($divider)) : ?>
<div class="events-list-days-divider">
	<span class="events-list-days-divider-label">
		<?php echo $divider?>
	</span>
</div>
<?php endif?>

<a class="events-list-row group-element coloring-for-group <?php echo $row_class ?>" href="<?php the_permalink() ?>" postid="<?php the_ID() ?>" >
	<?php if ($is_event_free) : ?>
	<div class="events-list-post-free-overlay">
		<div></div>
		<div>БЕСПЛАТНО</div>
		<div></div>
	</div>
	<?php endif; ?>

	<div class="events-list-post-date-thumbnail events-list-cell">

		<!--
		<div class="events-list-post-date-thumbnail-subtitle" >
			<?php echo $thumbnail_subtitle_top; ?>
		</div>
		-->

		<div class="events-list-post-date-thumbnail-title<?php echo $thumbnail_title_class; ?>" >
			<?php echo $thumbnail_title; ?>
		</div>
		
		<?php if ($show_thumbnail_subtitle) : ?>
			<div class="events-list-post-date-thumbnail-subtitle" >
			<?php echo $thumbnail_subtitle;?>
			</div>

		<?php endif; ?>
	</div>

	<div class="events-list-post-image-thumbnail events-list-cell">
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