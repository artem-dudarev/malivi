<?php
// Fetch the Help page Instance
$help = Tribe__Admin__Help_Page::instance();

// Fetch plugins
$plugins = $help->get_plugins( null, false );

// Creates the System Info section
$help->add_section( 'system-info', __( 'System Information', 'tribe-common' ), 30 );
?>

<div id="tribe-help-general">
	<?php $help->get_sections(); ?>
</div>