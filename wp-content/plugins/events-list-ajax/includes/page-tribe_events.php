<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h1 class="entry-title" itemprop="name"><?php the_title(); ?></h1>
	</header>
	<?php flat_hook_entry_before(); ?>
	<div class="entry-content" itemprop="articleBody">
		<?php flat_hook_entry_top(); ?>
		<?php the_content( __( 'Continue reading <span class="meta-nav">...</span>', 'flat' ) ); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'flat' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) ); ?>
		<?php flat_hook_entry_bottom(); ?>
	</div>
	<?php flat_hook_entry_after(); ?>
</article>
<?php comments_template(); ?>

