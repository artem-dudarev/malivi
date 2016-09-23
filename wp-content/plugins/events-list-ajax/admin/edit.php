<?php
	global $wpdb;
	$update = false;
	
	
	$fields = get_option( 'sf-fields' );
	
	/**
		Import
	*/
	if( isset( $_POST['import'] ) ):
		if( ! wp_verify_nonce( $_POST['sf-wpnonce'], 'update-sf' ) )
			wp_die( 'Wrong parameter.' );
		$_POST['import'] = stripslashes( $_POST['import'] );
		$field = unserialize( $_POST['import'] );
		echo $field['name'];
		echo '<pre>';print_r( $field );echo '</pre>';
		$fields[ $field['name'] ] = $field;
		update_option( 'sf-fields', $fields ); 
	endif;
	
	
	foreach( $fields as $field )
		if( $field['name'] == $_GET["ID"] )
			break;
	
	if( isset( $_POST['sf'] ) ):
		if( ! wp_verify_nonce( $_POST['sf-wpnonce'], 'update-sf' ) )
			wp_die( 'Wrong parameter.' );
		$sf = $_POST['sf'];
		
		if( isset( $_POST['sf_step'] ) && $_POST['sf_step'] == 1 ):
			foreach( $sf as $key => $val ):
				$field[ $key ] = $val;
			endforeach;
		endif;
		if( isset( $_POST['sf_step'] ) && $_POST['sf_step'] == 2 ):
			$field['tax'] = $sf['tax'];
			$field['meta'] = $sf['meta'];
		endif;
				
		if( isset( $_POST['sf_step'] ) && $_POST['sf_step'] == 3 ):
			
			foreach( $sf as $key => $val ):
				$field[ $key ] = $val;
			endforeach;
		endif;
		
		
		$fields[ $field['name'] ] = $field;
		update_option( 'sf-fields', $fields ); 
		$update = true;
	endif;
?>
<script>var sf_tab_index = <?php if( isset( $_POST['sf_step'] ) ): echo ( $_POST['sf_step'] - 1 ); else: echo '0'; endif; ?>;</script>
<div id="wrap" class="sf-wrap">
	<p><a href="?page=search-filter"><?php _e( 'Search Filter', 'sf' ); ?></a> &raquo;</p>
	<?php if( isset( $field['title'] ) && trim( $field['title'] ) ) $title = $field['title']; else $title = $field['name']; ?>
	<h2><?php printf( __( 'Edit "%s"', 'sf' ),  $title ); ?></h2>
	<?php if( $update ): ?>
	<div class="updated below-h2"><p><?php _e( 'Filter updated.', 'sf' ); ?></p></div>
	<?php endif; ?>
	<div class="sf-tabs">
		<ul>
			<li><a href="#general-settings"><?php _e( 'General Settings', 'sf' ); ?></a></li>
			<li><a href="#taxonomies-postmeta"><?php _e( 'Taxonomies & Postmetas' ,'sf' ); ?></a></li>
			<!--<li><a href="#layout"><?php _e( 'Layout' ,'sf' ); ?></a></li>-->
			<li><a href="#form-elements"><?php _e( 'Form Elements' ,'sf' ); ?></a></li>
			<li><a href="#import-export"><?php _e( 'Import & Export', 'sf' ); ?></a></li>
		</ul>
		
		<!-- 1. Tab: General Settings -->
		<div id="general-settings">
			<form method="post" class="sf-form"><?php wp_nonce_field( 'update-sf', 'sf-wpnonce', false ); ?>
				<input type="hidden" value="1" name="sf_step" />				

				<fieldset>
					<legend><?php _e( 'General Settings' ,'sf' ); ?></legend>
					<section>
						<label for="sf_id"><?php _e( 'ID', 'sf' ); ?>:</label>
						<input id="sf_id" readonly name="sf[name]" value="<?php echo $field['name']; ?>" />		
					</section>
					<section>
						<label for="sf_name"><?php _e( 'Name', 'sf' ); ?>:</label>
						<input id="sf_name" name="sf[title]" value="<?php if( isset( $field['title'] ) && trim( $field['title'] ) != '' ) echo $field['title']; else echo $field['name']; ?>" />		
					</section>
					<section>
						<label for="sf_posttype"><?php _e( 'Post Type', 'sf' ); ?>:</label>
						<select id="sf_posttype" name="sf[posttype][]">
							<option></option>
							<?php 				
							$args = array(
								'public'	=> true
							);
							$posttypes = get_post_types( $args, 'objects' );
							foreach( $posttypes as $key => $p ): ?>
								<option <?php if( ( is_array( $field['posttype'] ) && in_array( $key, $field['posttype'] ) ) || ( !is_array( $field['posttype'] ) && $key == $field['posttype'] ) ) echo 'selected="selected"'; ?> value="<?php echo $key; ?>"><?php echo $p->labels->name; ?></option>				
							<?php endforeach; ?>
						</select>
					</section>
					<section>
						<label for="sf_debug"><?php _e( 'Debug Mode', 'sf' ); ?>:</label>
						<select id="sf_debug" name="sf[debug]">
							<option <?php if( !isset( $field['debug'] ) || 0 == $field['debug'] ) echo 'selected="selected"'; ?> value="0"><?php _e( 'Off', 'sf' ); ?></option>
							<option <?php if( isset( $field['debug'] ) && 1 == $field['debug'] ) echo 'selected="selected"'; ?> value="1"><?php _e( 'On', 'sf' ); ?></option>
						</select>
						
						<small><?php _e( 'Turn this mode on, in order to get additional data on the WP_Query like the args or the SQL statement. Please turn it off in live mode', 'sf' ); ?></small>
					</section>
					<hr />
					<input class="button" type="submit" value="<?php _e( 'Update', 'sf' ); ?>" />
				</fieldset>
			</form>
		</div>
		
		<!-- 2. Tab: Taxonomies and Postmeta -->
		<div id="taxonomies-postmeta">
			<h3><?php _e( 'Taxonomies & Postmetas' ,'sf' ); ?></h3>
			<p><?php _e( 'Please drag the Taxonomies and Postmetas, which you want to use in your search form from the left field to the right one.', 'sf' ); ?></p>
			<?php
				$metas = get_all_postmetas_from_post_type( $field['posttype'] );
			?>
			<ul class="sf-group1">
				<li><?php _e( 'Taxonomies', 'sf' ); ?>
					<?php $tax = get_all_post_taxonomies( $field['posttype'] ); ?>
					<ul class="sf-tax-ul">
						<?php foreach( $tax as $key => $t ): 
						if( !isset( $field['tax'] ) || ( is_array( $field['tax'] ) && !in_array( $key, $field['tax'] ) ) ): 
						?>
						<li class="sf-drag"><input name="sf[tax][]" value="<?php echo $key; ?>" type="hidden" /><?php echo $t->labels->name; ?> (<?php echo $key; ?>)</li>
						<?php 
						endif;
						endforeach; ?>
					</ul>
				</li>
			
				<li><?php _e( 'Postmeta', 'sf' ); ?>
					<ul class="sf-meta-ul">
					<?php foreach( $metas as $key => $val ): 
					if( !isset( $field['meta'] ) || ( is_array( $field['meta'] ) && !in_array( $key, $field['meta'] ) ) ): ?>
					<li class="sf-drag"><input name="sf[meta][]" value="<?php echo $key; ?>" type="hidden" /><?php echo ucfirst( $key ); ?></li>
					<?php 
					endif;
					endforeach; ?>
					</ul>
				</li>
			</ul>
			
			<form method="post" class="sf-form"><?php wp_nonce_field( 'update-sf', 'sf-wpnonce', false ); ?>
				<input type="hidden" value="2" name="sf_step" />

				<ul class="sf-group2">
					<?php foreach( $tax as $key => $t ): 
					if( isset( $field['tax'] ) && is_array( $field['tax'] ) && in_array( $key, $field['tax'] ) ): 
					?>
					<li class="sf-drag"><input name="sf[tax][]" value="<?php echo $key; ?>" type="hidden" /><?php echo $t->labels->name; ?></li>
					<?php 
					endif;
					endforeach; ?>
					<?php foreach( $metas as $key => $val ): 
					if( isset( $field['meta'] ) && is_array( $field['meta'] ) && in_array( $key, $field['meta'] ) ): ?>
					<li class="sf-drag"><input name="sf[meta][]" value="<?php echo $key; ?>" type="hidden" /><?php echo ucfirst( $key ); ?></li>
					<?php 
					endif;
					endforeach; ?>
				</ul>
				<div class="sf-clear"></div>
				<hr />
				
				<hr />
				<input class="button" type="submit" value="<?php _e( 'Update', 'sf' ); ?>" />
			</form>
		</div>
		
		
	
	
	<!-- 3. Form Elements -->
	<div id="form-elements">
		<h3><?php _e( 'Form Elements' ,'sf' ); ?></h3>
		<p><?php _e( 'Move the form elements, which you want to have in your form, from the right to the left pane. You can edit the elements attributes by clicking on it in the pane "Chosen Form Elements". In this dialog, you can set the necessary attributes.', 'sf' ); ?></p>
		<div style="display:none;">
			<select id="sf-datasource">
				<optgroup label="<?php _e( 'Taxonomies', 'sf' ); ?>">
					<?php foreach( $field['tax'] as $meta ): ?>
					<option value="tax[<?php echo $meta; ?>]"><?php echo $meta; ?></option>
					<?php endforeach; ?>
				</optgroup><optgroup label="<?php _e( 'Postmetas', 'sf' ); ?>">
				<?php foreach( $field['meta'] as $meta ): ?>
					<option value="meta[<?php echo $meta; ?>]"><?php echo $meta; ?></option>
				<?php endforeach; ?></optgroup>
				
			</select>
			
			<div id="sf-orderbysource">
					<?php 
					$i = 0;
					if( is_array( $field['meta'] ) ):
					foreach( $field['meta'] as $meta ): ?>
					<?php echo $meta; ?> <?php _e( 'ascending', 'sf' ); ?>:<br /><input class="sf-array" type="checkbox" checked="checked" name="orderby[<?php echo $i; ?>]" value="meta[<?php echo $meta; ?>|asc]"> <input class="sf-orderbylabel sf-array" name="orderbylabel[<?php echo $i; ?>]" value="<?php echo $meta; ?> <?php _e( 'ascending', 'sf' ); ?>" /><br />
					<?php echo $meta; ?> <?php _e( 'descending', 'sf' ); ?>:<br /><input class="sf-array" type="checkbox" checked="checked" name="orderby[<?php echo $i; ?>]" value="meta[<?php echo $meta; ?>|desc]"> <input class="sf-orderbylabel sf-array" name="orderbylabel[<?php echo $i; ?>]" value="<?php echo $meta; ?> <?php _e( 'descending', 'sf' ); ?>" /><br /><br />
					<?php $i++; endforeach; endif;?>
					<?php _e( 'Date ascending', 'sf' ); ?>:<br /><input class="sf-array" type="checkbox" checked="checked" name="orderby[<?php echo $i; ?>]" value="post[date|asc]"> <input class="sf-orderbylabel sf-array" name="orderbylabel[<?php echo $i++; ?>]" value="<?php _e( 'Date ascending', 'sf' );  ?>" /><br />
					<?php _e( 'Date descending', 'sf' ); ?>:<br /><input class="sf-array" type="checkbox" checked="checked" name="orderby[<?php echo $i; ?>]" value="post[date|desc]"> <input class="sf-orderbylabel sf-array" name="orderbylabel[<?php echo $i++; ?>]" value="<?php _e( 'Date descending', 'sf' ); ?>" /><br />
			</div>
			
			<select id="sf-allpostmeta">
				<?php foreach( $field['meta'] as $meta ): ?>
					<option value="<?php echo $meta; ?>"><?php echo $meta; ?></option>
				<?php endforeach; ?></optgroup>
			</select>
		</div>	

		<form method="post" class="sf-form"><?php wp_nonce_field( 'update-sf', 'sf-wpnonce', false ); ?>
			<input name="sf_step" value="3" type="hidden" />
			<div class="field filter">
				<p><strong><?php _e( 'Chosen Form Elements', 'sf' ); ?></strong></p>
				<?php 
				$img_array = array(
								'fulltext'	=>	'input-fulltext.png',
								'select'	=>	'select.png',
								'input'		=>	'input.png',
								'checkbox'	=>	'checkbox.png',
								'radiobox'	=>	'radiobox.png',
								'range'		=>	'range.png',
								'map'		=>	'maps.png',
								'orderby'	=>	'order-by.png',
								'hidden'	=>	'hidden.png',
								'btnsearch'	=>	'btn-search.png',
								'btnreset'	=>	'btn-reset.png',
								'date'		=>	'date.png'
				);
				$i = 0;
				foreach( $field['fields'] as $key => $f ):
					$i++;
					?>
					<div data-attr='<?php echo json_encode( $f ); ?>' style="" data-id="<?php echo $i; ?>">
						<img alt="" src="<?php echo MALIVI_PLUGIN_URL ?>res/admin/<?php echo $img_array[ $f['type'] ]; ?>">
						<span><?php echo $f['fieldname']; ?></span>
						<?php foreach( $f as $k => $v ): ?>
							<?php if( is_array( $v ) ): ?>
								<?php foreach( $v as $single_v ): ?>
									<input type="hidden" value="<?php echo $single_v; ?>" name="sf[fields][<?php echo $i; ?>][<?php echo $k; ?>][]">
								<?php endforeach; ?>
							<?php else: ?>
								<input type="hidden" value="<?php echo $v; ?>" name="sf[fields][<?php echo $i; ?>][<?php echo $k; ?>]">
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>
		
			<div class="field elements">
				<p><strong><?php _e( 'All Form Elements', 'sf' ); ?></strong></p>
				<div data-attr='{"type":"fulltext"}'>
					<img src="<?php echo MALIVI_PLUGIN_URL ?>res/admin/input-fulltext.png" alt="<?php __( 'Fulltext Search', 'sf' ); ?>" />
					<span><?php _e( 'Fulltext Search', 'sf' ); ?></span>
				</div>
				<div data-attr='{"type":"select"}'>
					<img src="<?php echo MALIVI_PLUGIN_URL ?>res/admin/select.png" alt="<?php __( 'Selectbox', 'sf' ); ?>" />
					<span><?php _e( 'Selectbox', 'sf' ); ?></span>
				</div>
				
				<div data-attr='{"type":"checkbox"}'>
					<img src="<?php echo MALIVI_PLUGIN_URL ?>res/admin/checkbox.png" alt="<?php __( 'Checkbox', 'sf' ); ?>" />
					<span><?php _e( 'Checkbox', 'sf' ); ?></span>
				</div>
				<div data-attr='{"type":"radiobox"}'>
					<img src="<?php echo MALIVI_PLUGIN_URL ?>res/admin/radiobox.png" alt="<?php __( 'Radiobox', 'sf' ); ?>" />
					<span><?php _e( 'Radiobox', 'sf' ); ?></span>
				</div>
				
			</div>
			<div class="sf-clear"></div>
			<hr />
		<input class="button" type="submit" value="<?php _e( 'Update', 'sf' ); ?>" />
		</form>
	</div>
	
	<!-- 4. Tab: Import & Export -->
	<div id="import-export">
		<h3><?php _e( 'Import & Export', 'sf' ); ?></h3>
		<p><?php _e( 'Here you can import & export your search field. Copy the text below and save it in order to export your search field. Paste the settings here, in order to import your exported search field.', 'sf' ); ?></p>
		<form method="post"><?php wp_nonce_field( 'update-sf', 'sf-wpnonce', false ); ?>
			
			<input name="sf_step" value="4" type="hidden" />
			<textarea style="width:100%;height:250px" name="import"><?php echo serialize( $field ); ?></textarea>
			<input class="button" type="submit" value="<?php _e( 'Import', 'sf' ); ?>" />
		</form>
	</div>
</div>
	
</div>