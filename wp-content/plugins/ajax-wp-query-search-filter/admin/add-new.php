<?php
if ( ! defined( 'ABSPATH' ) )
die( '-1' );
$addlink = add_query_arg(array('aformid' => 'new', 'aformaction' => 'new'), ADMINPURL);
?>
<div class="wrap"><div id="icon-options-general" class="icon32"></div><h2><?php echo esc_html( __( 'Ajax WP Query Search Filter', 'AjWPQSF' ) ); ?><a href="<?php echo $addlink;?>" class="add-new-h2"> <?php echo esc_html( __( 'Add New Search Form', 'AjWPQSF' ) ); ?></a></h2>


<?php  
	//$this->show_messages();

 ?>
<br>
<?php 
$postid = absint($_GET['aformid'])  ? esc_attr($_GET['aformid']) : '';
if(isset($postid) && absint($postid)){
echo '<div class="showcode"><h2>'."[AjaxWPQSF id=$postid]".'</h2><span class="drag">'.esc_html( __( 'Copy this code and paste it into your post, page or text widget content.', 'AjWPQSF' ) ).'</span></div>';
}
?>
<br>
<form method="post" action="" id="ajaxwpqsf_main">

<?php 
$nonce = wp_create_nonce  ('ajax-wpqsf-edit');

echo '<input type="hidden" name="aformid" value="'.esc_attr($_GET['aformid']).'" ><input type="hidden" name="nonce" value="'.$nonce.'" />'
;?>
<h3><span><b><?php _e('Form Title','AjWPQSF'); ?> </b>: <input type="text" class="form_title" name="ftitle" value="<?php echo get_the_title($postid); ?> "></span></h3><br>

<div id="expand"><?php require_once AWQSFPLUG . '/admin/fields/post_type.php'; ?></div>
<div id="expand"><?php require_once AWQSFPLUG . '/admin/fields/taxonomy.php'; ?></div>
<?php do_action( 'ajwpqsf_after_taxo_adminform', $postid ); ?> 
<div id="expand"><?php require_once AWQSFPLUG . '/admin/fields/meta_field.php'; ?></div>
<?php do_action( 'ajwpqsf_after_cmf_adminform', $postid ); ?> 
<div id="expand"><?php require_once AWQSFPLUG . '/admin/fields/misc.php'; ?></div>
<div id="expand"><?php require_once AWQSFPLUG . '/admin/fields/formoption.php'; ?></div>
<?php echo '<input type="submit" class="button-primary" name="ajxwpqsfsub" value="Save" >'; ?>
<div class="advert">Developed By TC.K From <a href="http://www.9-sec.com" target="_blank">9-SEC.COM</a></div>
</form>
<?php require_once AWQSFPLUG . '/admin/fields/add_taxo_form.php'; ?>
<?php require_once AWQSFPLUG . '/admin/fields/add_cmf_form.php'; ?>

</div>
