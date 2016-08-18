<div class="add_tax_form_div"  style="display:none">
	<form id="addCmfForm" >
	
	<h3><?php _e("Meta Field Setting","AjWPQSF");?></h3>
	<p><span><b><?php _e("Meta Key","AjWPQSF");?></b>:</span>
	<select type="text" id="precmfkey" name="prekey"><br>
	<?php
	$classapi = new ajaxwpqsfclass();	
	$keys = $classapi->get_all_metakeys();
	echo '<option value="">'.__("Choose a meta key","AjWPQSF").'</option>';	
		foreach($keys as $key){
			
			 echo  '<option value="'.$key.'">'.$key.'</option>';
		}
	?>	
	</select>
	</p>
	
	<p><span><b><?php _e("Label","AjWPQSF");?></b>:</span>
	<input type="text" id="precmflabel" name="precmflabel" value=""/><span class="desciption"><?php _e(" To be displayed in the search form", "AjWPQSF");?></span>
	
	</p>
	
	<p><span><b><?php _e("Text For 'Search All' Options","AjWPQSF");?></b>:</span>
	<input type="text" id="precmfall" name="precmfall" value=""/><span class="desciption"><?php _e(" eg, All prices, All weight", "AjWPQSF") ;?></span>

	</p>
	
	<p><span><b><?php _e("Compare","AjWPQSF");?></b>:</span>
	<select id="precompare" name="precompare">
	<?php 
		$campares = $classapi->cmf_compare();
		foreach ($campares   as $ckey => $cvalue ) {
			echo '<option value="'.$ckey.'">'.$cvalue.'</option>';
	     }
	?>
	</select><br>
	<span class="desciption"><?php _e("Operator to test. Use it carefully. If you choose 'BETWEEN', then your options should be range." , "AjWPQSF") ;?></span><br>
	<?php $link = 'http://wordpress.stackexchange.com/questions/70864/meta-query-compare-operator-explanation/70870#70870';
	echo '<span class="desciption">'.sprintf(__("More about compare, please visit <a href='%s' target='_blank'>here</a>", "AjWPQSF"), $link ).'</span>';
	;?>
	<?php do_action( 'ajwpsf_cmfcompare_desc'); ?>
	</p>

	<p>
	<span><b><?php _e("Display Type?","AjWPQSF");?></b></span><br>
	<?php
		$feilds = 	$classapi->awpqsf_cmf_fields();
		foreach ($feilds as $v => $k){
			echo '<label><input type="radio" id="pretype" name="cmfdisplay" value="'.$v.'"   />';
			printf( __( '%s', 'AjWPQSF' ), $k);
			echo '</label>';
			}
	?>
	<br/>
	<span class="generate"><?php _e('* Warning! Checkbox only work with "IN" and "NOT IN" compare operator.','AjWPQSF') ;?> </span>	
	<br>
	<?php do_action( 'ajwpsf_cmfdispaly_desc'); ?>	
	</span>
	</p>
	
	<p><span><b><?php _e("Dropdown Options","AjWPQSF");?></b>:</span><br>
	<span class="desciption"><?php _e("Your options should defined in xxx::xxx and each option is separated by '|' .<br> The left value in option xxx::xxx is the <b>real meta value</b>, and the right value is for <b>user viewed value</b>. <br>eg. 100::$100, 2000::$2,000" , "AjWPQSF") ;?></span><br>
	<textarea  id="preopt" name="preopt" rows="5" cols="75"></textarea><br><input type="button" class="genv" value="<?php _e('Generate Value','AjWPQSF') ;?>">
	<span class="generate"><?php _e('Based on the meta key selected above','AjWPQSF') ;?> </span><br>	
	<span class="desciption"><?php _e("eg: for normal options set 100::$100 | 200::$200 | 100::$300...etc" , "AjWPQSF") ;?></span><br>
	<span class="desciption"><?php _e("eg: for range option 1-100::$1 - $100 | 101-200 :: $101 - $200 | 201-300 :: $201 - $300...etc" , "AjWPQSF") ;?></span>
	<?php do_action( 'ajwpsf_cmfoption_desc'); ?>
	</p>
		
	<input type="submit" value="<?php _e("Add Custom Field","AjWPQSF");?>" class="addCmf button-secondary" />
	</form>
</div>
