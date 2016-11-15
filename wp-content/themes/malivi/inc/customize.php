<?php
/**
 * Extend Textarea Customize Control
 */
include_once ABSPATH . WPINC . '/class-wp-customize-control.php';

/**
 * Register customizer controls
 *
 * @param object $wp_customize The WordPress customizer object
 */
class Flat_Message extends WP_Customize_Control{
    private $message = '';
    public function __construct( $manager, $id, $args = array() ) {
        parent::__construct( $manager, $id, $args );
        if(!empty($args['flat_message'])){
            $this->message = $args['flat_message'];
        }
    }
    
    public function render_content(){
        echo '<span class="customize-control-title">'.$this->label.'</span>';
        echo $this->message;
    }
}  
 
function flat_customize_register( $wp_customize ) {
	// Logo
	$wp_customize->add_setting( 'flat_theme_options[logo]', array(
		'capability' => 'edit_theme_options',
		'type' => 'option',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'logo', array(
		'label' => __( 'Site Logo', 'flat' ),
		'section' => 'title_tagline',
		'settings' => 'flat_theme_options[logo]',
	) ) );

	// Header Display
	$wp_customize->add_setting( 'flat_theme_options[header_display]', array(
		'default' => 'site_title',
		'capability' => 'edit_theme_options',
		'type' => 'option',
		'sanitize_callback' => 'flat_sanitize_header_display',
	) );
	$wp_customize->add_control( 'header_display', array(
		'settings' => 'flat_theme_options[header_display]',
		'label' => 'Display as',
		'section' => 'title_tagline',
		'type' => 'select',
		'choices' => array(
			'site_title' => __( 'Site Title', 'flat' ),
			'site_logo' => __( 'Site Logo', 'flat' ),
			'both_title_logo' => __( 'Both Title &amp; Logo', 'flat' ),
		),
	) );

	// Favicon
	$wp_customize->add_setting( 'flat_theme_options[favicon]', array(
		'capability' => 'edit_theme_options',
		'type' => 'option',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'favicon', array(
		'label' => __( 'Site Favicon', 'flat' ),
		'section' => 'title_tagline',
		'settings' => 'flat_theme_options[favicon]',
	) ) );
	
	if ( class_exists( 'WP_Customize_Panel' ) ):
	
		$wp_customize->add_panel( 'panel_design', array(
			'priority' => 29,
			'capability' => 'edit_theme_options',
			'theme_supports' => '',
			'title' => __( 'Design', 'flat' )
		) );
			
		

		// Background Size
		$wp_customize->add_setting( 'flat_theme_options[background_size]', array(
			'default' => 'cover',
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'sanitize_callback' => 'flat_sanitize_background_size',
		) );
		$wp_customize->add_control( 'background_size', array(
			'settings' => 'flat_theme_options[background_size]',
			'label' => __( 'Background size', 'flat' ),
			'section' => 'background_image',
			'type' => 'radio',
			'choices' => array(
				'cover' => __( 'Cover', 'flat' ),
				'contain' => __( 'Contain', 'flat' ),
				'initial' => __( 'Initial', 'flat' ),
			),
		) );

	else:

	
		// Background Size
		$wp_customize->add_setting( 'flat_theme_options[background_size]', array(
			'default' => 'cover',
			'capability' => 'edit_theme_options',
			'type' => 'option',
			'sanitize_callback' => 'flat_sanitize_background_size',
		) );
		$wp_customize->add_control( 'background_size', array(
			'settings' => 'flat_theme_options[background_size]',
			'label' => __( 'Background size', 'flat' ),
			'section' => 'background_image',
			'type' => 'radio',
			'choices' => array(
				'cover' => __( 'Cover', 'flat' ),
				'contain' => __( 'Contain', 'flat' ),
				'initial' => __( 'Initial', 'flat' ),
			),
		) );

		
	
	endif;

	// Single Post Settings
	$wp_customize->add_section( 'layout_single', array(
		'title' => __( 'Single Post', 'flat' ),
		'priority' => 110,
	) );

	// Single Featured Image
	$wp_customize->add_setting( 'flat_theme_options[single_featured_image]', array(
		'default' => '1',
		'capability' => 'edit_theme_options',
		'type' => 'option',
		'sanitize_callback' => 'flat_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'single_featured_image', array(
		'label' => __( 'Hide Featured Image', 'flat' ),
		'section' => 'layout_single',
		'settings' => 'flat_theme_options[single_featured_image]',
		'type' => 'checkbox',
	) );

	// Single Metadata
	$wp_customize->add_setting( 'flat_theme_options[single_metadata]', array(
		'default' => '0',
		'capability' => 'edit_theme_options',
		'type' => 'option',
		'sanitize_callback' => 'flat_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'single_metadata', array(
		'label' => __( 'Hide Metadata', 'flat' ),
		'section' => 'layout_single',
		'settings' => 'flat_theme_options[single_metadata]',
		'type' => 'checkbox',
	) );

	// Single Author Box
	$wp_customize->add_setting( 'flat_theme_options[single_author_box]', array(
		'default' => '0',
		'capability' => 'edit_theme_options',
		'type' => 'option',
		'sanitize_callback' => 'flat_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'single_author_box', array(
		'label' => __( 'Hide Author Box', 'flat' ),
		'section' => 'layout_single',
		'settings' => 'flat_theme_options[single_author_box]',
		'type' => 'checkbox',
	) );

	// Archive Settings
	$wp_customize->add_section( 'layout_archive', array(
		'title' => __( 'Archive Pages', 'flat' ),
		'priority' => 100,
	) );

	// Archive Featured Image
	$wp_customize->add_setting( 'flat_theme_options[archive_featured_image]', array(
		'default' => '0',
		'capability' => 'edit_theme_options',
		'type' => 'option',
		'sanitize_callback' => 'flat_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'archive_featured_image', array(
		'label' => __( 'Hide Featured Image', 'flat' ),
		'section' => 'layout_archive',
		'settings' => 'flat_theme_options[archive_featured_image]',
		'type' => 'checkbox',
	) );

	// Archive Metadata
	$wp_customize->add_setting( 'flat_theme_options[archive_metadata]', array(
		'default' => '0',
		'capability' => 'edit_theme_options',
		'type' => 'option',
		'sanitize_callback' => 'flat_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'archive_metadata', array(
		'label' => __( 'Hide Metadata', 'flat' ),
		'section' => 'layout_archive',
		'settings' => 'flat_theme_options[archive_metadata]',
		'type' => 'checkbox',
	) );

	// Archive Content
	$wp_customize->add_setting( 'flat_theme_options[archive_content]', array(
		'default' => '0',
		'capability' => 'edit_theme_options',
		'type' => 'option',
		'sanitize_callback' => 'flat_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'archive_content', array(
		'label' => __( 'Show Post Excerpt', 'flat' ),
		'section' => 'layout_archive',
		'settings' => 'flat_theme_options[archive_content]',
		'type' => 'checkbox',
	) );
	
}
add_action( 'customize_register', 'flat_customize_register' );

function flat_sanitize_checkbox( $input ) {
	if ( 1 == $input ) {
		return 1;
	} else {
		return '';
	}
}

function flat_sanitize_header_display( $header_display ) {
	if ( ! in_array( $header_display, array( 'site_title', 'site_logo', 'both_title_logo' ) ) ) {
		$header_display = 'site_title';
	}

	return $header_display;
}

function flat_sanitize_background_size( $background_size ) {
	if ( ! in_array( $background_size, array( 'cover', 'contain', 'initial' ) ) ) {
		$background_size = 'cover';
	}

	return $background_size;
}

/**
 * Get Theme Options
 */
function flat_get_theme_option( $option_name, $default = '' ) {
	$options = get_option( 'flat_theme_options' );

	if ( isset( $options[ $option_name ] ) ) {
		return $options[ $option_name ];
	}

	return $default;
}

/**
 * Change Favicon
 */
function flat_favicon() {
	$icon_path = esc_url( flat_get_theme_option( 'favicon' ) );

	if ( ! empty( $icon_path ) ) {
		echo '<link type="image/x-icon" href="' . esc_attr( $icon_path ) . '" rel="shortcut icon">';
	}
}
add_action( 'wp_head', 'flat_favicon' );

/**
 * Custom CSS
 */
function flat_custom_css() {
	echo '<style type="text/css">';
	$custom_style = '';
	
	$background_size = flat_get_theme_option( 'background_size' );

	if ( ! empty( $background_size ) ) {
		$custom_style .= 'body { background-size: ' . $background_size . '; }';
	}

	echo esc_attr( $custom_style );
	echo '</style>';
}
add_action( 'wp_head', 'flat_custom_css' );

/**
 * Display Logo
 */
function flat_logo() {
	$header_display = flat_get_theme_option( 'header_display', 'site_title' );

	if ( 'both_title_logo' === $header_display ) {
		$header_class = 'display-title-logo';
	} else if ( 'site_logo' === $header_display ) {
		$header_class = 'display-logo';
	} else {
		$header_class = 'display-title';
	}

	$logo = esc_url( flat_get_theme_option( 'logo' ) );
	$tagline = get_bloginfo( 'description' );

	echo '<a href="' . esc_url( home_url( '/' ) ) . '" title="'. esc_attr( get_bloginfo( 'name', 'display' ) ) . '" rel="home"><h1 class="site-title ' . esc_attr( $header_class ) . '">';

	if ( 'display-title' !== $header_class ) {
		echo '<img itemprop="primaryImageofPage" alt="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" src="' . esc_attr( $logo ) . '" />';
	}
	if ( 'display-logo' !== $header_class ) {
		echo '<span itemprop="name">' . esc_attr( get_bloginfo( 'name' ) ) . '</span>';
	}

	echo '</h1>';

	if ( $tagline ) {
		echo '<h2 itemprop="description" class="site-description">' . esc_attr( $tagline ) . '</h2>';
	}
	echo '</a>';
}

function flat_customizer_registers() {
	
	wp_enqueue_script( 'flat_customizer_script', get_template_directory_uri() . '/assets/js/flat_customizer.js', array("jquery"), '20120206', true  );
	wp_localize_script( 'flat_customizer_script', 'flatCustomizerObject', array(
		'documentation' => __( 'View Documentation', 'flat' ),
		'review' => __( 'Leave us a review(it will help us)', 'flat' ),
		'github' => __( 'Github', 'flat' ),
		'pro' => __( 'Upgrade to pro', 'flat' ),
	) );
}
add_action( 'customize_controls_enqueue_scripts', 'flat_customizer_registers' );
