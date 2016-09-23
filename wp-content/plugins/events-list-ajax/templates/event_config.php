<table id="event_extensions" class="eventtable">
	<!--   Заголовок раздела настроек   -->
	<tr>
		<td colspan="2" class="tribe_sectionheader">
			<h4><?php printf( esc_html__( 'Дополнительные настройки', 'malivi' ), $events_label_singular ); ?></h4></td>
	</tr>

	<!--   Ограничение по возрасту   -->
	<tr>
		<td style="width:172px;"><?php esc_html_e( 'Возрастной рейтинг:', 'malivi' ); ?></td>
		<td>
			<select tabindex="<?php tribe_events_tab_index(); ?>" name="EventAgeRestriction">
				<?php echo get_age_restriction_variants($EventAgeRestriction); ?>
			</select>
		</td>
	</tr>
	<!--   Чекбокс "Для детей"   -->
	<tr>
		<td><?php printf( esc_html__( 'Для детей:', 'malivi' ), $events_label_singular ); ?></td>
		<td>
			<input tabindex="<?php tribe_events_tab_index(); ?>" type="checkbox" name="EventIsForChildren" value="yes" <?php echo is_true($EventIsForChildren) ? 'checked':''; ?>/>
		</td>
	</tr>

	<!--   Ссылка   -->
	<tr>
		<td><?php printf( esc_html__( 'Для детей:', 'malivi' ), $events_label_singular ); ?></td>
		<td>
			<input tabindex="<?php tribe_events_tab_index(); ?>" type="checkbox" name="EventLink" value="<?php echo $EventLink; ?>"/>
		</td>
	</tr>

	<!--   DEBUG   -->
	<tr>
		<td><?php esc_html_e( 'Debug:', 'malivi' ); ?></td>
		<td>
			<?php echo $debug_log; ?>
		</td>
	</tr>
</table>