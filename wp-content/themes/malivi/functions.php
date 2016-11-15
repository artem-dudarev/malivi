<?php
/**
 * Flat initiation
 *
 * Initializes Flat's features and includes all necessary files.
 *
 * @package Flat
 */

# Prevent direct access to this file
if ( 1 == count( get_included_files() ) ) {
	header( 'HTTP/1.1 403 Forbidden' );
	die( 'Direct access of this file is prohibited. Thank you.' );
}

/**
 * File inclusions
 */
require get_template_directory() . '/inc/customize.php'; # Enables user customization via admin panel
require get_template_directory() . '/inc/hooks.php'; # Enables user customization via WordPress plugin API
require get_template_directory() . '/inc/template-tags.php'; # Contains functions that output HTML

/**
 * Set the max width for embedded content
 *
 * @link http://codex.wordpress.org/Content_Width
 */
if ( ! isset( $content_width ) ) {
	$content_width = 720;
}

if ( ! function_exists( 'flat_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 */
	function flat_setup() {
		# Localization
		load_theme_textdomain( 'flat', get_template_directory() . '/languages' );

		# Enable WordPress theme features
		add_theme_support( 'automatic-feed-links' ); # @link http://codex.wordpress.org/Automatic_Feed_Links
		$custom_background_support = array(
			'default-color'          => '',
			'default-image'          => get_template_directory_uri() . '/assets/img/default-background.jpg',
			'wp-head-callback'       => '_custom_background_cb',
			'admin-head-callback'    => '',
			'admin-preview-callback' => '',
		);
		
		/* tgm-plugin-activation */
        require_once get_template_directory() . '/class-tgm-plugin-activation.php';
		add_theme_support( 'custom-background', $custom_background_support ); # @link http://codex.wordpress.org/Custom_Backgrounds
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'quote', 'status' ) ); # @link http://codex.wordpress.org/Post%20Formats
		add_theme_support( 'post-thumbnails' ); # @link http://codex.wordpress.org/Post%20Thumbnails
		add_theme_support( 'structured-post-formats', array( 'link', 'video' ) );
		add_theme_support( 'title-tag' ); # @link http://codex.wordpress.org/Title_Tag
		add_theme_support( 'tha-hooks', array( 'all' ) ); # @link https://github.com/zamoose/themehookalliance

		# Add style to the post editor for a more WYSIWYG experience
		add_editor_style( array( 'assets/css/editor-style.css' ) );

		# Flat has one navigation menu; register it with WordPress
		register_nav_menu( 'primary', __( 'Navigation Menu', 'flat' ) );
		register_nav_menu( 'login', __( 'Login Menu', 'flat' ) );
		register_nav_menu( 'editors', __( 'Editors Menu', 'flat' ) );

		# Add filters
		add_filter( 'comments_popup_link_attributes', function() { return ' itemprop="discussionUrl"'; } ); # schema.org property on comments links
		add_filter( 'current_theme_supports-tha_hooks', '__return_true' ); # Enables checking for THA hooks
		add_filter( 'style_loader_tag', 'flat_filter_styles', 10, 2 ); # Filters style tags as needed
		add_filter( 'the_content_more_link', 'modify_read_more_link' ); # Enhances appearance of "Read more..." link
		add_filter( 'use_default_gallery_style', '__return_false' ); # Disable default WordPress gallery styling
		remove_filter( 'the_content','cwp_pac_before_content');

		# Add actions
		add_action( 'flat_html_before', 'flat_doctype' ); # Outputs HTML doctype
		add_action( 'flat_404_content', 'flat_output_404_content' ); # Outputs a helpful message on 404 pages
		add_action( 'widgets_init', 'flat_widgets_init' ); # Registers Flat's sidebar
		add_action( 'wp_enqueue_scripts', 'flat_scripts_styles' ); # Enqueue's Flat's scripts & styles
	}
endif;
add_action( 'after_setup_theme', 'flat_setup' );


if ( ! function_exists( 'flat_widgets_init' ) ) :
	/**
	 * Registers our sidebar with WordPress
	 */
	function flat_widgets_init() {
		register_sidebar( array(
			'name'          => __( 'Main Widget Area', 'flat' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Appears in the sidebar section of the site', 'flat' ),
			'before_widget' => "\t\t\t\t\t" . '<aside id="%1$s" class="widget %2$s">' . "\n",
			'after_widget'  => "\t\t\t\t\t</aside>\n",
			'before_title'  => "\t\t\t\t\t\t<h3 class='widget-title'>",
			'after_title'   => "</h3>\n",
		) );
	}
endif;

if ( ! function_exists( 'flat_scripts_styles' ) ) :
	/**
	 * Sets up necessary scripts and styles
	 */
	function flat_scripts_styles() {
		global $wp_version;

		# Get the current version of Flat, even if a child theme is being used
		$version = wp_get_theme( wp_get_theme()->template )->get( 'Version' );

		# When needed, enqueue comment-reply script
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		wp_enqueue_script('jquery');
		//wp_enqueue_style( 'flat-fonts', flat_fonts_url(), array(), null ); # Web fonts
		wp_enqueue_style( 'flat-basics', get_template_directory_uri() . '/assets/css/basics.css', array(), $version); # Flat's styling
		wp_enqueue_script( 'flat-js', get_template_directory_uri() . '/assets/js/flat.js'); # Flat's scripting
		wp_enqueue_style( 'flat-style', get_stylesheet_uri(), array(), $version); # Load main stylesheet, for child theme supports

		wp_enqueue_script(
			'jquery-mobile',
			get_template_directory_uri() . '/assets/js/jquery.mobile.custom.min.js',
			array('jquery'),
			'1.4.5'
		);
		//wp_enqueue_script( 'jquery-mobile');
		/*wp_enqueue_style(
			'jqm_css',
			'http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css',
			'',
			'1.4.5'
		);*/

		//wp_enqueue_style( 'wp-admin' );

		wp_register_script('vkshare', "//vk.com/js/api/share.js?90");
		wp_enqueue_script('vkshare');

		wp_register_script('vkwidgets', "//vk.com/js/api/openapi.js?136");
		wp_enqueue_script('vkwidgets');

		wp_register_script('fbconnect', "//connect.facebook.net/ru_RU/sdk.js#&version=v2.8");
		wp_enqueue_script('fbconnect');

		wp_register_script('googleplus', "//apis.google.com/js/platform.js");
		wp_enqueue_script('googleplus');

		wp_register_script('yandexshare', "//yastatic.net/share2/share.js");
		wp_enqueue_script('yandexshare');
		
		# If the `script_loader_tag` filter is unavailable, this script will be added via the `wp_head` hook
		if ( version_compare( '4.1', $wp_version, '<=' ) ) {
			wp_enqueue_script( 'html5shiv', get_template_directory_uri() . '/assets/js/html5shiv.js', array(), '3.7.2', false );
		}
		
	}
endif;

# The following function uses a filter introduced in WP 4.1
if ( version_compare( '4.1', $wp_version, '<=' ) ) :
	if ( ! function_exists( 'flat_filter_scripts' ) ) :
		/**
		 * Filters enqueued script output to better suit Flat's needs
		 */
		function flat_filter_scripts( $tag, $handle, $src ) {
			# Remove `type` attribute (unneeded in HTML5)
			$tag = str_replace( ' type=\'text/javascript\'', '', $tag );

			# Apply conditionals to html5shiv for legacy IE
			if ( 'html5shiv' === $handle ) {
				$tag = "<!--[if lt IE 9]>\n$tag<![endif]-->\n";
			}

			return $tag;
		}
	endif;
	add_filter( 'script_loader_tag', 'flat_filter_scripts', 10, 3 );
else : # If the `script_loader_tag` filter is unavailable...
	/**
	 * Adds html5shiv the "old" way (WP < 4.1)
	 */
	function flat_add_html5shiv() {
		echo "<!--[if lt IE 9]>\n<scr" . 'ipt src="' . esc_url( get_template_directory_uri() ) . '/assets/js/html5shiv.min.js"></scr' . "ipt>\n<![endif]-->"; # This is a hack to disguise adding the script without using WordPress' enqueue function
	}
	add_action( 'wp_head', 'flat_add_html5shiv' );
endif;

if ( ! function_exists( 'flat_filter_styles' ) ) :
	/**
	 * Filter enqueued style output to better suit HTML5
	 */
	function flat_filter_styles( $tag, $handle ) {
		# Get rid of unnecessary `type` attribute
		$tag = str_replace( ' type=\'text/css\'', '', $tag );

		# Get rid of double-spaces
		$tag = str_replace( '  ', ' ', $tag );

		return $tag;
	}
endif;

/**
 * Enhances "Read more..." links with Bootstrap button styling
 */
function modify_read_more_link() {
	return '<a class="btn btn-default btn-sm" href="' . esc_url( get_permalink() ) . '">' . sprintf( __( 'Continue reading %s', 'flat' ), '<i class="fa fa-angle-double-right"></i></a>' );
}

function malivi_can_user_access_admin() {
	$user_id = get_current_user_id();
	if (!$user_id || empty( $user_id)) {
		return false;
	}
	if ( is_super_admin( $user_id ) ) {
		return true;
	}
	if ( empty( $user_id ) ) {
		return array();
	}

	$user = new WP_User( $user_id );
	if (!isset( $user->roles ) ) {
		return false;
	}

	$allowed_roles = array('administrator', 'editor');
	// if a user has multiple roles, still let him in if he has a non-blocked role
	$result = array_intersect( $user->roles, $allowed_roles );
	if ( empty( $result ) ) {
		return false;
	}
	return true;
}
/* // На данный момент это делается плагином theme-my-login
// Отключим админску панель у всех кроме админа и редактора
add_action('after_setup_theme', 'malivi_remove_admin_bar');
function malivi_remove_admin_bar() {
	if (!is_admin() && !malivi_can_user_access_admin()) {
		show_admin_bar(false);
	}
}
add_action( 'init', 'malivi_block_roles_from_admin' );
function malivi_block_roles_from_admin() {
	// Let WordPress worry about admin access for unauthenticated users
	if ( ! is_user_logged_in() ) {
		return;
	}
	//If User Cannot Access Admin Hide the Admin Bar
	if ( ! malivi_can_user_access_admin() ) {
		add_filter( 'show_admin_bar', '__return_false' );
	}

	// If it is not an admin request - or if it is an ajax request - then we don't need to interfere
	if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		return;
	}

	// If the user has access privileges then we don't need to interfere
	if ( malivi_can_user_access_admin() ) {
		return;
	}

	// Redirect user to appropriate location
	wp_safe_redirect( wp_validate_redirect( home_url() ) );
	exit;
}*/

// В конец содержимого текста добавляем кнопки лайков
add_action('post_social_like_buttons', 'add_post_social_like_buttons');
function add_post_social_like_buttons() {
	$page_id = 'site'.get_current_blog_id().'_page'.get_the_ID();

	$google_like_button_id = 'google_like_button_'.get_the_ID();
	echo '<div class="post-share-button"">';
	//echo '<div id="'.$google_like_button_id.'"></div><script type="text/javascript">gapi.plusone.render("'.$google_like_button_id.'", {size: "standard"})</script>';
	echo '</div>';

	$vk_like_button_id = 'vk_like_button_'.get_the_ID();
	echo '<div class="post-share-button" id="'.$vk_like_button_id.'">';
	echo '<script type="text/javascript">VK.Widgets.Like("'.$vk_like_button_id.'", {type: "button", height: 30, pageUrl: "'.get_permalink().'"}, "'.$page_id.'");</script>';
	echo '</div>';

	$fb_button_id = 'fb_like_button_'.get_the_ID(); 
	echo '<div class="post-share-button" id="'.$fb_button_id.'">';
	echo '<div class="fb-like" data-href="'. get_permalink() .'" data-layout="button_count" data-action="like" data-size="large" data-show-faces="false" data-share="false"></div><script type="text/javascript">FB.XFBML.parse(document.getElementById("'.$fb_button_id.'"));</script>';
	echo '</div>';
}

// После раздела с содержимым поста, вставляем кнопки для отправки страницы в соц-сети
add_action('tribe_events_single_event_after_the_content', 'add_post_share_buttons');
function add_post_share_buttons() {
	echo '<a class="tribe-events-gcal button post-share-button" href="' . Tribe__Events__Main::instance()->esc_gcal_url( tribe_get_gcal_link() ) . '" title="' . esc_attr__( 'Add to Google Calendar', 'the-events-calendar' ) . '">' . esc_html__( 'Google Calendar', 'the-events-calendar' ).'</a>';
	$ya_button_id = "ya_share_button_".get_the_ID();
	echo '<div id="'.$ya_button_id.'" class="post-share-button"></div>';
	echo '<script type="text/javascript">Ya.share2("'.$ya_button_id.'", { content: { url: "'.get_permalink().'"}, theme : {services: "vkontakte,facebook,odnoklassniki,moimir,gplus,twitter"} });</script>';
}

// Добавляем инициализацию скриптов в заголовок страницы
add_action('flat_body_top', 'flat_add_body_scripts');
function flat_add_body_scripts() {
	echo '<script type="text/javascript">';
  	echo 'VK.init({ apiId: 5702053, onlyWidgets: true });';
	echo '(window.Image ? (new Image()) : document.createElement("img")).src = location.protocol + "//vk.com/rtrg?r=bQxSJFcA7HtwRjTgT08an90Xx6sPauMgaMaiDYIoZxLwOWy3ch1FLTISQYCCYw6NwSB0U2HgbADMHHCgpaIkc0SM75f01ZJb7Fsvz3lnr4QVi3VNwyQEEkwkDObZmvI1HU2lAd6UPtcCgfYAC254UP5WCJT67tLuTvqfhsZ*2nc-&pixel_id=1000030378";';
	echo '</script>';
	echo '<script type="text/javascript">';
	echo 'FB.init({appId: "182398415550570", xfbml: false, version: "v2.8" });';
	echo '</script>';
}

//Каждую страницу плагина theme-my-login надо обернуть в контейнер
add_filter('tml_display', 'wrap_theme_my_login_content', 10, 3);
function wrap_theme_my_login_content($output, $action, $template) {
	$output = '<div class="tml-form-container group-element">' . $output . '</div>';
	return $output;
}

// Добавляем надпись о пользовательском соглашении в форму регистрации
add_action('register_form', 'add_user_agreement');
function add_user_agreement() {
	echo '<p class="tml-agreement" id="reg_agreement">';
	$agreement_link = '<a href="/agreement" target="_blank">' . __( 'user agreement', 'flat') . '</a>';
	echo sprintf( __( 'By registering you are accepting %s.', 'flat'), $agreement_link); 
	echo '</p>';
}


?>