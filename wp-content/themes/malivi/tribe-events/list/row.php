<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
} ?>

<a class="events-list-row group-element coloring-for-group" href="<?php the_permalink() ?>" postid="<?php the_ID() ?>" >
	<?php tribe_get_template_part( 'list/single', get_post_type() ) ?>
</a>
