  <div class="add_tax_form_div"  style="display:none">
	<form id="addTaxoForm">
	<h3><b><?php _e("Taxonomy Details","AjWPQSF");?></b></h3>
	
	<p>
	<span><b><?php _e("Select Taxonomy","AjWPQSF");?></b></span><br>
	<select id="pretax" name="pre_add_tax">
	<option value="category"><?php _e("category","AjWPQSF");?></option>
	<?php
	
		$args=array('public'   => true, '_builtin' => false); 
		$output = 'names'; // or objects
		$operator = 'and'; // 'and' or 'or'
		$taxonomies=get_taxonomies($args,$output,$operator); 
		if  ($taxonomies) {
			foreach ($taxonomies  as $taxonomy ) {
			echo '<option value="'.$taxonomy.'">'.$taxonomy.'</option>';
	     }
		};
	?>
	</select>
	</p>
	<p>
	<span><b><?php _e("Label","AjWPQSF");?></b></span><br>
	<input type="text" id="prelabel" name="pre_tax_label" size="20" value=""><br>
	<span class="desciption"><?php _e("To be displayed in the search form", "AjWPQSF");?></span>
	</p>
	<p>
	<span><b><?php _e("Text For 'Search All' Options","AjWPQSF");?></b></span><br>
	<input type="text" id="preall" name="pre_all_text" size="20" value="" /><br>
	<span class="desciption"><?php _e("eg, All cities, All genres", "AjWPQSF") ;?></span>
	</p>
	<p>
	<span><b><?php _e("Hide Empty Terms?","AjWPQSF");?></b></span><br>
	<label><input type="radio" id="prezero" name="pre_hide_empty" value="1"   />Yes</label>
	<label><input type="radio" id="prezero" name="pre_hide_empty" value="0"   />No</label><br>
	<span class="desciption"><?php _e("Empty terms are the terms that no posts assigned to them. ", "AjWPQSF") ;?></span>
	</p>
	<p>
	<span><b><?php _e("Exculde Term ID","AjWPQSF");?></b></span><br>
	<input type="text" id="preexclude" name="pre_tax_exclude" size="20" value=""><br>
	<span class="desciption"><?php _e("Enter the term's ID that you want to exclude. Seperate by comma ',' ", "AjWPQSF") ;?></span>
	</p>
	<p>
	<span><b><?php _e("Display Type?","AjWPQSF");?></b></span><br>	
	<?php
	$taxofields = new ajaxwpqsfclass();	
	$feilds = 	$taxofields->awpqsf_taxo_fields();
	foreach ($feilds as $v => $k){
		echo '<label><input type="radio" id="pretype" name="displyatype" value="'.$v.'"   />';
		printf( __( '%s', 'AjWPQSF' ), $k);
		echo '</label>';
	}	
	;?>
	<br>
	<?php do_action( 'ajwpsf_taxodisplay_desc'); ?>
	</span>
	</p>
	<input type="submit" value="<?php _e("Add Taxonomy","AjWPQSF");?>" class="addTaxo button-secondary" />
	</form>
	</div>
