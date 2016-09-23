<?php
	
	if( isset( $_POST['sf'] ) )
		$sf = $_POST['sf'];
?>

<div id="wrap" class="sf-wrap">
	<small><a href="?page=search-filter"><?php _e( 'Search Filter', 'sf' ); ?></a> &raquo;</small>
	<h2><?php _e( 'New search filter', 'sf' ); ?></h2>
	<hr />
	<?php if( !isset( $_POST['sf_step'] ) ): ?>
	<form method="post" class="sf-form">
		<input type="hidden" value="1" name="sf_step" />
		<?php if( isset( $sf ) ): foreach( $sf as $key => $val ):
			if( is_array( $val ) ):
			foreach( $val as $v ):
			?>
			<input type="hidden" name="sf[<?php echo $key;?>][]" value="<?php echo $v; ?>" />			
			<?php
			endforeach;
			else:
			?>
			<input type="hidden" name="sf[<?php echo $key;?>]" value="<?php echo $val; ?>" />
			<?php
			endif;
		endforeach; endif; ?>
		<fieldset>
		<legend><?php _e( 'General Settings' ,'sf' ); ?></legend>
		<section>
			<label for="sf_id"><?php _e( 'ID', 'sf' ); ?>:</label>
			<input id="sf_id" name="sf[name]" value="" />		
		</section>
		<section>
			<label for="sf_name"><?php _e( 'Name', 'sf' ); ?>:</label>
			<input id="sf_name" name="sf[title]" value="" />		
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
				<option value="<?php echo $key; ?>"><?php echo $p->labels->name; ?></option>
				<?php endforeach; ?>
			</select>
		</section>
		<hr />
		<input class="button" type="submit" value="<?php _e( 'Next Step', 'sf' ); ?> &#10148;" />
		</fieldset>
	</form>
	<?php elseif( $_POST['sf_step'] == 1 ): ?>
		<h3><?php _e( 'Taxonomies & Postmetas' ,'sf' ); ?></h3>
		<p><?php _e( 'Please drag the Taxonomies and Postmetas, which you want to use in your search form from the left field to the right one.', 'sf' ); ?></p>
		<?php
			$metas = get_all_postmetas_from_post_type( $sf['posttype'] );
		?>
		<ul class="sf-group1">
			<li><?php _e( 'Taxonomies', 'sf' ); ?>
				<?php $tax = get_all_post_taxonomies( $sf['posttype'] ); ?>
				<ul class="sf-tax-ul">
				<?php foreach( $tax as $key => $t ): ?>
				<li class="sf-drag"><input name="sf[tax][]" value="<?php echo $key; ?>" type="hidden" /><?php echo $t->labels->name; ?> (<?php echo $key; ?>)</li>
				<?php endforeach; ?>
				</ul>
			</li>
			
			<li><?php _e( 'Postmeta', 'sf' ); ?><ul class="sf-meta-ul">
				<?php foreach( $metas as $key => $val ): ?>
				<li class="sf-drag"><input name="sf[meta][]" value="<?php echo $key; ?>" type="hidden" /><?php echo ucfirst( $key ); ?></li>
				<?php endforeach; ?>
			</ul></li>
		</ul>
		
		<form method="post" class="sf-form">
			<input type="hidden" value="2" name="sf_step" />
					<?php if( isset( $sf ) ): foreach( $sf as $key => $val ):
				if( is_array( $val ) ):
				foreach( $val as $v ):
				?>
				<input type="hidden" name="sf[<?php echo $key;?>][]" value="<?php echo $v; ?>" />			
				<?php
				endforeach;
				else:
				?>
				<input type="hidden" name="sf[<?php echo $key;?>]" value="<?php echo $val; ?>" />
				<?php
				endif;
			endforeach; endif; ?>
			<ul class="sf-group2">
			
			</ul>
			<div class="sf-clear"></div>
			<hr />
			
			
			<hr />
			<input class="button" type="submit" value="<?php _e( 'Next Step', 'sf' ); ?> &#10148;" />
		</form>
	<?php elseif( $_POST['sf_step'] == 2 ): ?>	
		<h3><?php _e( 'Form Elements' ,'sf' ); ?></h3>
		<p><?php _e( 'Move the form elements, which you want to have in your form, from the right to the left pane. You can edit the elements attributes by clicking on it in the pane "Chosen Form Elements". In this dialog, you can set the necessary attributes.', 'sf' ); ?></p>
		<div style="display:none;">
			<select id="sf-datasource">
				<optgroup label="<?php _e( 'Taxonomies', 'sf' ); ?>">
					<?php foreach( $sf['tax'] as $meta ): ?>
					<option value="tax[<?php echo $meta; ?>]"><?php echo $meta; ?></option>
					<?php endforeach; ?>
				</optgroup><optgroup label="<?php _e( 'Postmetas', 'sf' ); ?>">
				<?php foreach( $sf['meta'] as $meta ): ?>
					<option value="meta[<?php echo $meta; ?>]"><?php echo $meta; ?></option>
				<?php endforeach; ?></optgroup>
			</select>
			
			<div id="sf-orderbysource">
					<?php 
					$i = 0;
					if( is_array( $sf['meta'] ) ):
					foreach( $sf['meta'] as $meta ): ?>
					<?php echo $meta; ?> <?php _e( 'ascending', 'sf' ); ?>:<br /><input class="sf-array" type="checkbox" checked="checked" name="orderby[<?php echo $i; ?>]" value="meta[<?php echo $meta; ?>|asc]"> <input class="sf-orderbylabel sf-array" name="orderbylabel[<?php echo $i; ?>]" value="<?php echo $meta; ?> <?php _e( 'ascending', 'sf' ); ?>" /><br />
					<?php echo $meta; ?> <?php _e( 'descending', 'sf' ); ?>:<br /><input class="sf-array" type="checkbox" checked="checked" name="orderby[<?php echo $i; ?>]" value="meta[<?php echo $meta; ?>|desc]"> <input class="sf-orderbylabel sf-array" name="orderbylabel[<?php echo $i; ?>]" value="<?php echo $meta; ?> <?php _e( 'descending', 'sf' ); ?>" /><br /><br />
					<?php $i++; endforeach; endif;?>	
					<?php _e( 'Date ascending', 'sf' ); ?>:<br /><input class="sf-array" type="checkbox" checked="checked" name="orderby[<?php echo $i; ?>]" value="post[date|asc]"> <input class="sf-orderbylabel sf-array" name="orderbylabel[<?php echo $i++; ?>]" value="<?php _e( 'Date ascending', 'sf' );  ?>" /><br />
					<?php _e( 'Date descending', 'sf' ); ?>:<br /><input class="sf-array" type="checkbox" checked="checked" name="orderby[<?php echo $i; ?>]" value="post[date|desc]"> <input class="sf-orderbylabel sf-array" name="orderbylabel[<?php echo $i++; ?>]" value="<?php _e( 'Date descending', 'sf' ); ?>" /><br />
			</div>
			
			<select id="sf-allpostmeta">
				<?php foreach( $sf['meta'] as $meta ): ?>
					<option value="<?php echo $meta; ?>"><?php echo $meta; ?></option>
				<?php endforeach; ?></optgroup>
			</select>
		</div>	

		<form method="post" class="sf-form">
			<?php wp_nonce_field( 'save-new-sf', 'sf-wpnonce', false ); ?>
			<input name="sf_step" value="3" type="hidden" />
				<?php 
				if( isset( $sf ) ):
					foreach( $sf as $key => $val ):
						if( is_array( $val ) ):
							foreach( $val as $v ):
								?>
								<input type="hidden" name="sf[<?php echo $key;?>][]" value="<?php echo $v; ?>" />			
								<?php
							endforeach;
						else:
							?>
							<input type="hidden" name="sf[<?php echo $key;?>]" value="<?php echo $val; ?>" />
							<?php
						endif;
					endforeach;
				endif; 
				?>	
			<div class="field filter">
				<p><strong><?php _e( 'Chosen Form Elements', 'sf' ); ?></strong></p>
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
		<input class="button" type="submit" value="<?php _e( 'Next Step', 'sf' ); ?> &#10148;" />
	</form>
	<?php elseif( $_POST['sf_step'] == 3 ): 
	if( ! wp_verify_nonce( $_POST['sf-wpnonce'], 'save-new-sf' ) )
			wp_die( 'Wrong parameter.' );
	?>
	<h3><?php _e( 'Saved' ,'sf' ); ?></h3>
		<form class="sf-form">
			<fieldset>
				<legend><?php _e( 'Yeeeeha!', 'sf' ); ?></legend>
				<div class="update-nag">
					<p><?php _e( 'Your detail search is now updated. You can insert this form by using the following shortcode:', 'sf' ); ?></p>
					<input onclick="this.select();" onfocus="this.select();" value='[search-form id="<?php echo $sf['name']; ?>"]' />
				</div>
			</fieldset>
			<fieldset class="big">
				<legend><?php _e( 'What\'s next?', 'sf' ); ?></legend>
				<p><?php printf( __( 'You have created your search from. This form needs to be inserted into a Page. We recommend to <a target="_blank" href="%s" target="_blank">create a new Page</a> and insert this shortcode [search-form id="%s"] into the Editor.', 'sf' ), 'post-new.php?post_type=page', $sf['name'] ); ?><br />
				<?php printf( __( 'If you are not sure, what Shortcodes are, have a look into the <a href="%s" target="_blank">WordPress Documentation</a>.', 'sf' ), 'http://en.support.wordpress.com/shortcodes/' ); ?><br />
				<?php _e( 'Once, you have done this and published the new Page, your visitors can access this page and search your WordPress Database very detailed.', 'sf' ); ?><br />		
				<?php printf( __( 'Thanks a lot for using <a href="%s" target="_blank">Profi Search Form</a>', 'sf' ), 'http://profisearchform.com/' ); ?></p>
			</fieldset>
		</form>
		<?php  
		$fields = get_option( 'sf-fields' );
		$fields[ $sf['name'] ] = $sf;
		update_option( 'sf-fields', $fields ); ?>
	</form>
	<?php endif; ?>
	
</div>