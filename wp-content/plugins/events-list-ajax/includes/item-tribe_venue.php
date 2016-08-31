<?php
	$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($post_id) );
	$title = get_the_title();

	$current_date = date("d.m.Y");
	$post_date = get_the_date('d.m.Y');

	// Если событие сегодня, то на иконке отображаем время начала
	// Иначе показываем дату
	if ( $current_date == $post_date) {
		// Если событие идет весь день, то вместо времени начала показываем надпись "весь день"
		if (tribe_event_is_all_day($post_id)) {
			$thumbnail_title = "ВЕСЬ ДЕНЬ";
		} else {
			$thumbnail_title = get_the_date('H:i');
		}
		$show_thumbnail_subtitle = false;
	} else {
		$thumbnail_title = get_the_date('d');
		//$thumbnail_title .= ' - ' . date_i18n( 'd', strtotime(get_post_meta( $post_id, '_EventEndDate', true ) ) );
		$thumbnail_subtitle = get_the_date('M.Y');
		//$thumbnail_subtitle2 = get_the_date('H:i');
		$show_thumbnail_subtitle = true;
	}

	
	if (empty($thumbnail) || $thumbnail == false) {
		$image_content = '<span class="events-list-post-image-noimglink">';
		$image_content .= mb_substr($title, 0, 1);
		$image_content .= '</span>';
	} else {
		$image_content = '<img class="events-list-post-image-link" src="'.$thumbnail['0'].'" alt="'.$title.'" />';
	}
			
?>

	<!--
	<div class="events-list-post-date-thumbnail events-list-cell" >

		<div class="events-list-post-date-thumbnail-subtitle" >
			
		</div>

		<div class="events-list-post-date-thumbnail-title" >
			<?php echo $thumbnail_title; ?>
		</div>
		
		<?php if ($show_thumbnail_subtitle) { ?>
		<div class="events-list-post-date-thumbnail-subtitle" >
		<?php echo $thumbnail_subtitle;?>
		</div>

		<?php
		}
		?>
	</div>
	-->

	<div class="events-list-post-image-thumbnail events-list-cell" >
		<?php echo $image_content;?>
	</div>

	<div class="events-list-cell" >
		<div class="events-list-post-text" >
			<div class="events-list-post-text-header">
				<?php echo $title; ?>
			</div>

			<div class="events-list-post-text-content">
				<?php echo event_list_custom_excerpt('25', 'true'); ?>
			</div>
		</div>
	</div>