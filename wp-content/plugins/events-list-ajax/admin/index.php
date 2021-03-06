<?php
	$fields = get_option( 'sf-fields' );
?>
<div id="wrap">
	<h2><?php _e( 'Search Filter', 'sf' ); ?> 
	</h2>
	<a class="button" href="?page=search-filter-new"><?php _e( 'Create new search filter', 'sf' ); ?></a>
	<hr />
	<div class="sf-half">
	<?php if( is_array( $fields ) && count( $fields ) > 0 ): ?>
	<table class="wp-list-table widefat fixed pages">
		<thead>
			<tr>
				<th><?php _e( 'Name', 'sf' ); ?></th>
				<th><?php _e( 'Shortcode', 'sf' ); ?></th>
				<th><?php _e( 'Delete', 'sf' ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><?php _e( 'Name', 'sf' ); ?></th>
				<th><?php _e( 'Shortcode', 'sf' ); ?></th>
				<th><?php _e( 'Delete', 'sf' ); ?></th>
			</tr>
		</tfoot>
		<tbody id="the-list">
			<?php foreach( $fields as $field ): ?>
			<tr>
				<td><a href="?page=search-filter-edit&ID=<?php echo $field['name']; ?>"><?php if( !isset( $field['title'] ) || trim( $field['title'] ) == '' )echo $field['name']; else echo $field['title'];?></a></td>
				<td><code>[search-form id="<?php echo $field['name']; ?>"]</code></td>
				<td><a href="#" class="sf-form-delete button" data-id="<?php echo $field['name']; ?>"><?php _e( 'Delete', 'sf' ); ?></a></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php else: ?>
	<p><?php _e( 'No Search Filter yet.', 'sf' ); ?></p>
	<?php endif; ?>
	</div>
	
	<hr class="sf"/>
	<p><strong>Current Version: <?php echo EL_CURRENT_VERSION; ?></strong></p>
</div>