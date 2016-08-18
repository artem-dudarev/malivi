<div class="item">
    <?php
    $o = new wpdreamsCustomSelect("shortcode_op", __("What to do with shortcodes in results content?", "ajax-search-lite"), array(
        'selects'=>array(
            array("option"=>"Remove them, keep the content", "value" => "remove"),
            array("option"=>"Execute them (can by really slow)", "value" => "execute")
        ),
        'value'=>$sd['shortcode_op']
    ));
    $params[$o->getName()] = $o->getData();
    ?>
    <p class="descMsg">
        <?php echo __("Removing shortcode is usually much faster, especially if you have many of them within posts.", "ajax-search-lite"); ?>
    </p>
</div>
<div class="item">
    <?php
    $o = new wpdreamsCustomFSelect("titlefield", __("Title Field", "ajax-search-lite"), array(
        'selects'=>$sd['titlefield_def'],
        'value'=>$sd['titlefield']
    ));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsCustomFSelect("descriptionfield", __("Description Field", "ajax-search-lite"), array(
        'selects'=>$sd['descriptionfield_def'],
        'value'=>$sd['descriptionfield']
    ));
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsCategories("excludecategories", __("Exclude categories", "ajax-search-lite"), $sd['excludecategories']);
    $params[$o->getName()] = $o->getData();
    $params['selected-'.$o->getName()] = $o->getSelected();
    ?>
</div>
<div class="item">
    <?php
    $o = new wpdreamsTextarea("excludeposts", __("Exclude Posts by ID's (comma separated post ID-s)", "ajax-search-lite"), $sd['excludeposts']);
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
	<?php
	$o = new wpdreamsTextarea("exclude_term_ids", __("Exclude Terms by ID (comma separated term ID-s)", "ajax-search-lite"), $sd['exclude_term_ids']);
	$params[$o->getName()] = $o->getData();
	?>
	<p class="descMsg">
		<?php echo __("Use this field to exclude any taxonomy term (tags, product categorories etc..)", "ajax-search-lite"); ?>
	</p>
</div>
<div class="item">
    <?php
    $o = new wpdreamsYesNo("wpml_compatibility", __("WPML compatibility", "ajax-search-lite"), $sd['wpml_compatibility']);
    $params[$o->getName()] = $o->getData();
    ?>
</div>
<div class="item">
    <input type="hidden" name='asl_submit' value=1 />
    <input name="submit_asl" type="submit" value="Save options!" />
</div>