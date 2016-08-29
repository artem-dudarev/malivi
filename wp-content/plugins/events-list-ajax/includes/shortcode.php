<?php
	$fields = get_option( 'sf-fields' );
	foreach( $fields as $field )
		if( $field['name'] == $attr['id'] )
			break;
			
?>
<!-- Search Filter: <?php echo $attr['id']; ?>-->
<div class="sf-wrapper" style="display: none">
	
	<div class="sf-filter">
		<?php if( defined( 'ICL_LANGUAGE_CODE' )  ):
			global $sitepress; ?>
			<input type="hidden" name="wpml" value="<?php echo $sitepress->get_current_language(); ?>" />
		<?php endif; ?>
		<input type="hidden" name="search-id" value="<?php echo $field['name']; ?>" />
		<?php foreach( $field['fields'] as $key => $element ):
			
			if( isset( $element['datasource'] ) ):
				preg_match_all( '^(.*)\[(.*)\]^', $element['datasource'], $match );
				$data_type = $match[1][0];
				$data_value = $match[2][0];
			else:
				$data_type = '';
				$data_value = '';
			endif;
		
		$class_hide = "";
		$style_hide = "";
		$cond_key = "";
		$cond_value = "";
		if( isset( $element['cond_key'] ) ):
			$cond_key = $element['cond_key'];
			$cond_value = $element['cond_value'];
			if( ( $element['cond_key'] != -1 || !empty( $element['cond_key'] ) ) && !empty( $element['cond_value'] ) ):
				$class_hide= "-hide";
				$style_hide = 'style="display:none;"';
			endif;
		endif;
			
		?>
		<fieldset data-id="<?php echo $key; ?>" <?php  echo $style_hide . 'data-condkey="' . $cond_key . '" data-condval="'  . $cond_value .  '"'; ?> class="sf-element<?php echo $class_hide; ?> ">
			<legend><?php echo $element['fieldname']; ?></legend>	
		<?php	
			if( $element['type'] == 'select' ):
			?>
			<select id="sf-field-<?php echo $key; ?>" name="<?php echo $key; ?>"><option></option><?php
				if( $data_type == 'tax' && $element['options'] == 'auto' ):
					$args = array(
						'orderby'       => 'name', 
						'order'         => 'ASC',
						'hide_empty'    => true
					);
					$terms = get_terms( $data_value, $args );
					if( isset( $element['hierarchical'] ) && $element['hierarchical'] == 1 ):
						$terms = order_terms_hierarchical( $terms, $element['hierarchical_symbol_to_indent'] );
					endif;
					
					
					foreach( $terms as $term ):
					?><option value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option><?php
					endforeach;
				elseif( $data_type == 'meta' && $element['options'] == 'auto' ):
					$values = get_postmeta_values( $data_value );
					foreach( $values as $val ):
					?>
					<option value="<?php echo $val->meta_value; ?>"><?php echo $val->meta_value; ?></option>					
					<?php
					endforeach;
				elseif( $element['options'] == 'individual' ):
					foreach( $element['option_key'] as $option_key => $val ):
					?>
					<option value="<?php echo $val; ?>"><?php echo $element['option_val'][$option_key]; ?></option>
					<?php
					endforeach;
				elseif( $data_type == 'others' ):
					if( $data_value == 'author' ):
						$args = array(
							'who'	=>	'authors'
						);
						$authors = apply_filters( 'sf-get-authors', get_users( $args ) );
						foreach( $authors as $author ):
						?>
						<option value="<?php echo $author->ID; ?>"><?php echo $author->data->display_name; ?></option>
						<?php
						endforeach;
					endif;
				endif;
				
			?></select><?php
			elseif( $element['type'] == 'checkbox' ):
			?>
				<div class="sf-checkbox-wrapper">
				<?php 
					if( $element['options'] == 'individual' ): ?>
						<?php foreach( $element['option_key'] as $option_key => $val ):
						?>
						<label><input type="checkbox" value="<?php echo $val; ?>" name="<?php echo $key; ?>[]" /> <?php echo $element['option_val'][$option_key]; ?></label>
						<?php
						endforeach;
			
					elseif( $data_type == 'tax' && $element['options'] == 'auto' ):
						$args = array(
							'orderby'       => 'name', 
							'order'         => 'ASC',
							'hide_empty'    => true
						);
						$terms = get_terms( $data_value, $args );
						foreach( $terms as $term ): 
							?>
							<label><input type="checkbox" value="<?php echo $term->term_id; ?>" name="<?php echo $key; ?>[]" /> <?php echo $term->name; ?></label>
							<?php 
						endforeach;
					elseif( $data_type == 'meta' && $element['options'] == 'auto' ):
						$values = get_postmeta_values( $data_value );
						foreach( $values as $val ):
						?>
						<label><input type="checkbox" value="<?php echo $val->meta_value; ?>" name="<?php echo $key; ?>[]" /> <?php echo $val->meta_value; ?></label>
						<?php
						endforeach;
					elseif( $data_type == 'others' ):
						if( $data_value == 'author' ):
							$args = array(
								'who'	=>	'authors'
							);
							$authors = apply_filters( 'sf-get-authors', get_users( $args ) );
							foreach( $authors as $author ):
							?>
							<label><input type="checkbox" value="<?php echo $author->ID; ?>" name="<?php echo $key; ?>[]" /> <?php echo $author->data->display_name ?></label>
							<?php
							endforeach;
						endif;
					endif; ?>
				</div>
			<?php
			elseif( $element['type'] == 'radiobox' ):
			?>
				<div class="sf-radiobox-wrapper">
				<?php 
					if( $element['options'] == 'individual' ): ?>
						<?php foreach( $element['option_key'] as $option_key => $val ):
						?>
						<label><input type="radio" value="<?php echo $val; ?>" name="<?php echo $key; ?>" /> <?php echo $element['option_val'][$option_key]; ?></label>
						<?php
						endforeach;
			
					elseif( $data_type == 'tax' && $element['options'] == 'auto' ):
						$args = array(
							'orderby'       => 'name', 
							'order'         => 'ASC',
							'hide_empty'    => true
						);
						$terms = get_terms( $data_value, $args );
						foreach( $terms as $term ): 
							?>
							<label><input type="radio" value="<?php echo $term->term_id; ?>" name="<?php echo $key; ?>" /> <?php echo $term->name; ?></label>
							<?php 
						endforeach;
					elseif( $data_type == 'meta' && $element['options'] == 'auto' ):
						$values = get_postmeta_values( $data_value );
						foreach( $values as $val ):
						?>
						<label><input type="radio" value="<?php echo esc_attr( $val->meta_value ); ?>" name="<?php echo $key; ?>" /> <?php echo $val->meta_value; ?></label>					
						<?php
						endforeach;
					
					elseif( $data_type == 'others' ):
						if( $data_value == 'author' ):
							$args = array(
								'who'	=>	'authors'
							);
							$authors = apply_filters( 'sf-get-authors', get_users( $args ) );
							foreach( $authors as $author ):
							?>
							<label><input type="radio" value="<?php echo $author->ID; ?>" name="<?php echo $key; ?>[]" /> <?php echo $author->data->display_name ?></label>
							<?php
							endforeach;
						endif;
					endif; ?>
				</div>
			<?php elseif( $element['type'] == 'fulltext' ):
			?>
				<div class="sf-fulltext-wrapper">
					<input placeholder="<?php echo $element['fieldname']; ?>" name="<?php echo $key; ?>" />
				</div>
			<?php
			
			endif;
		?>
		</fieldset>
		<?php
		endforeach; ?>
	</div>
</div>

<?php
	if( isset( $results ) ):				
		?>
		<script>sf_adjust_elements_waitimg();</script>
		<?php
	endif;
?>
		
<?php
	if( isset( $results['args'] ) ):
?>
<p>Debug Mode</p>
<pre>Args:
<?php print_r( $results['args'] ); ?>
Query:
<?php print_r( $results['query'] ); ?></pre>
<?php endif; ?>