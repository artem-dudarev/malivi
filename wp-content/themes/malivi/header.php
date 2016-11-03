<?php flat_hook_html_before(); ?>
<html <?php language_attributes(); ?>>
<head>
	<?php flat_hook_head_top(); ?>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<?php wp_head(); ?>
	<?php flat_hook_head_bottom(); ?>
</head>

<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">
<?php flat_hook_body_top(); ?>
<div id="page">
	<div class="container">
		<div id="secondary">
			<?php flat_hook_header_before(); ?>
			<header id="masthead" class="site-header secondary-panel" role="banner">
				<button type="button" class="btn btn-link hidden-lg toggle-sidebar" data-toggle="offcanvas" aria-label="Sidebar"><?php _e( '<i class="fa fa-bars"></i>', 'flat' ); ?></button>
				<?php flat_hook_header_top(); ?>
				<div class="hgroup">
					<?php flat_logo(); ?>
				</div>
				<button type="button" class="btn btn-link hidden-lg toggle-navigation hidden" aria-label="Navigation Menu"><?php _e( '<i class="fa fa-gear"></i>', 'flat' ); ?></button>
				<?php flat_hook_header_bottom(); ?>
			</header>
			<div class="sidebar-offcanvas secondary-panel">
				<div class="sidebar-offcanvas-container">
					<nav id="site-navigation" class="navigation main-navigation" role="navigation">
						<?php 
						wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu', 'container' => false ) );
						if (apply_filters('check_user_is_events_writer', false)) {
							wp_nav_menu( array( 'theme_location' => 'editors', 'menu_class' => 'nav-menu', 'container' => false ) );
						} 
						?>
					</nav>
					<?php get_sidebar(); ?>
				</div>
			</div>
			<?php flat_hook_header_after(); ?>
		</div>
		<?php flat_hook_content_before(); ?>
		<div id="primary" class="content-area" itemprop="mainContentOfPage">
			<?php flat_hook_content_top(); ?>