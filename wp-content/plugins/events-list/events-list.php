<?php
/*
Plugin Name: Events list
Plugin URI: 
Description: Plugin that shows the recent posts with thumbnails in the widget and in other parts of the your blog or theme with shortcodes.
Tags: widget, posts, plugin, recent, recent posts, video, latest, latest posts, shortcode, thumbnail, thumbnails, categories, content, featured image, Taxonomy, custom post type, custom
Version: 1.0
Author: Ilia Dudarev
Author URI: 
License: GPLv2 or later
Text Domain: events-list-for-the-events-calendar_domain
*/

/* load js and css styles */
function lptw_recent_posts_register_scripts() {
	wp_register_style( 'lptw-style', plugins_url( 'events-list.css', __FILE__ ) );
	wp_enqueue_style( 'lptw-style' );

    wp_enqueue_script( 'lptw-recent-posts-script', plugins_url( 'events-list.js', __FILE__ ), array('jquery', 'jquery-masonry'), false, true );
}
add_action( 'wp_enqueue_scripts', 'lptw_recent_posts_register_scripts' );

function return_event_date_instead_of_publish_date( $the_date, $d, $post ) {
	if ( is_int( $post) ) {
		$post_id = $post;
	} else {
		$post_id = $post->ID;
	}

	if ( tribe_is_event( $post_id ) ) {
        return date( $d, strtotime(get_post_meta( $post_id, '_EventStartDate', true ) ) );
    }
		
    return $the_date;
}
//add_action( 'get_the_date', 'return_event_date_instead_of_publish_date', 10, 3 );
add_filter( 'get_the_date', 'return_event_date_instead_of_publish_date', 10, 3 );

/* trim excerpt to custom size */
function event_list_custom_excerpt ($limit, $ignore_more_tag) {
    global $more;
    if ($ignore_more_tag == 'true') { $more = 1; }
    else { $more = 0; }
    $excerpt = explode(' ', get_the_excerpt(), $limit);
    if (count($excerpt)>=$limit) {
        array_pop($excerpt);
        $excerpt = implode(" ",$excerpt).'...';
    } else {
        $excerpt = implode(" ",$excerpt);
    }
    $excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
    return $excerpt;
}

register_activation_hook( __FILE__, 'lptw_recent_posts_activate' );

/**
-------------------------------------- Shortcode --------------------------------------
**/

/* Main Class for all Layouts Rendering */
include( plugin_dir_path( __FILE__ ) . 'includes/class.render.layout.php');

function events_list_display ( $atts ) {
    $default_posts_per_page =  get_option( 'posts_per_page', '10' );

    $a = shortcode_atts( array(
        'post_type'                 => 'post',
        'category_id'               => '',
        'authors_id'                => '',
        'post_parent'               => '0',
        'posts_per_page'            => $default_posts_per_page,
        'exclude_posts'             => '',
        'exclude_current_post'      => 'false',
        'random_thumbnail'          => 'false',
        'color_scheme'              => 'no-overlay',
        'fluid_images'              => 'false',
        'columns'                   => '1',
        'height'                    => '',
        'featured_height'           => '400',
        'min_height'                => '400',
        'width'                     => '300',
        'date_format'               => 'd.m.Y',
        'time_format'               => 'H:i',
        'show_time'                 => 'true',
        'show_time_before'          => 'true',
        'show_date_before_title'    => 'true',
        'order'                     => 'DESC',
        'orderby'                   => 'date',
        'reverse_post_order'        => 'false',
        'background_color'          => '#4CAF50',
        'text_color'                => '#ffffff',
        'no_thumbnails'             => 'hide',
        'space_hor'                 => 10,
        'space_ver'                 => 10,
        'tags_id'                   => '',
        'tags_exclude'              => 'false',
        'override_colors'           => 'false',
        'excerpt_show'              => 'true',
        'excerpt_lenght'            => '35',
        'ignore_more_tag'           => 'false',
        'post_offset'               => 0,
        'read_more_show'            => 'false',
        'read_more_inline'          => 'false',
        'read_more_content'         => 'Read more &rarr;',
        'link_target'               => 'self'
    ), $atts );

    /* get the list of the post categories */
    if ($a['category_id'] == 'same_as_post') {
        $post_categories = get_the_category();
        if ( !empty($post_categories) ) {
            foreach ($post_categories as $category) {
                if ( $category->taxonomy == 'category' ) { $post_category[] = $category->term_id; }
            }
        }
    }
    
    /* ------------------------------------ WP_Query arguments filter start ------------------------------------ */
    if ($a['no_thumbnails'] == 'hide') { $meta_key = '_thumbnail_id'; }
    else { $meta_key = ''; }

    if (!empty($a['exclude_posts'])) {
        $exclude_post = explode(',', $a['exclude_posts']);
        }
    else { $exclude_post = ''; }
    if ($a['exclude_current_post'] == 'true') {
        $current_post = get_the_ID();
        $exclude_post[] = $current_post;
    }

    if ( strpos($a['authors_id'], ',') !== false ) {
        $authors_id = array_map('intval', explode(',', $a['authors_id']));
    } else { $authors_id = (integer) $a['authors_id']; }

    if ( strpos($a['category_id'], ',') !== false ) {
        $post_category = array_map('intval', explode(',', $a['category_id']));
    } else if ( $a['category_id'] != 'same_as_post' ) {
        $post_category = (integer) $a['category_id'];
    }

    $tax_query = '';

    if ( $a['post_type'] != 'post' && !empty($post_category) ) {
        $tax_query = array('relation' => 'AND');
        $taxonomies = get_object_taxonomies($a['post_type']);
        if (!empty($taxonomies)) {
            foreach ($taxonomies as $taxonomy) {
                $tax_array = array('taxonomy' => $taxonomy, 'field' => 'term_id', 'terms' => $post_category, 'include_children' => false);
                array_push ($tax_query, $tax_array);
            }
        }
        $post_category = '';
    }

    if ( strpos($a['tags_id'], ',') !== false ) {
        $post_tags = array_map('intval', explode(',', $a['tags_id']));
    } else { $post_tags = (integer) $a['tags_id']; }

    if ( $a['post_type'] != 'post' ) { $post_tags = ''; }

    if ( $a['tags_exclude'] == 'true' ) { $tags_type = 'tag__not_in'; }
    else { $tags_type = 'tag__in'; }

    $lptw_shortcode_query_args = array(
        'post_type'             => $a['post_type'],
        'posts_per_page'        => $a['posts_per_page'],
		'no_found_rows'         => true,
		'post_status'           => 'publish',
		'ignore_sticky_posts'   => true,
        'post__not_in'          => $exclude_post,
        'author__in'            => $authors_id,
        'category__in'          => $post_category,
        $tags_type              => $post_tags,
        'tax_query'             => $tax_query,
        'order'                 => $a['order'],
        'orderby'               => $a['orderby'],
        'meta_key'              => $meta_key,
        'offset'                => $a['post_offset'],
        'suppress_filters'      => true
        );
        
    /* ------------------------------------ WP_Query arguments filter end ------------------------------------ */

    /* link target start */
    if ( $a['link_target'] == 'new' ) { $link_target = '_blank'; }
    else { $link_target = '_self'; }
    /* link target end */

    /* date, title and subtitle position start */
    if ( $a['show_date_before_title'] == 'true' ) {
        $date_pos = 1;
        $title_pos = 2;
    } else {
        $date_pos = 2;
        $title_pos = 1;
    }
    /* date, title and subtitle position end */

    $show_excerp = false;
    if ( $a['excerp_show'] == 'true' ) {
        $show_excerp = true;
    }


    $lptw_shortcode_query = new WP_Query( $lptw_shortcode_query_args );
    if( $lptw_shortcode_query->have_posts() ) {
        if ($a['reverse_post_order'] == 'true') { $lptw_shortcode_query->posts = array_reverse($lptw_shortcode_query->posts); }
        $i=1;
        $content = '<div class="events-list-table">';
        while( $lptw_shortcode_query->have_posts() ) {
            $lptw_shortcode_query->the_post();

            $post_id = get_the_ID();

            


            /* ------------------------------------ start layouts output ------------------------------------ */

            $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), array ( 100,100 ) );
            $title = get_the_title();

            $current_date = date("d.m.Y");
            $post_date = get_the_date('d.m.Y');

            // Если событие сегодня, то на иконке отображаем время начала
            // Иначе показываем дату
            if ( $current_date == $post_date) {
                // Если событие идет весь день, то вместо времени начала показываем надпись "весь день"
                if (tribe_event_is_all_day($post_id)) {
                    $post_time = '';
                    $thumbnail_title = "ВЕСЬ ДЕНЬ";
                } else {
                    $post_time = get_the_date($a['time_format']);
                    $thumbnail_title = $post_time;
                }
                $show_thumbnail_subtitle = false;
            } else {
                $thumbnail_title = get_the_date('d');
                $thumbnail_subtitle = get_the_date('M.Y');
                $show_thumbnail_subtitle = true;
            }

            
            if (empty($thumbnail) || $thumbnail == false) {
                $image_content = '<span class="events-list-post-image-noimglink">';
                $image_content .= substr($title, 0, 1);
                $image_content .= '</span>';
            } else {
                $image_content = '<img class="events-list-post-image-link" src="'.$thumbnail['0'].'" alt="'.$title.'" />';
            }

            
            $content .= '<a class="events-list-row" href="' . get_the_permalink() . '" >';
                $content .= '<div class="events-list-post-date-thumbnail events-list-cell" >';
                    $content .= '<div class="events-list-post-date-thumbnail-title" >';
                    $content .= $thumbnail_title;
                    $content .= '</div>';
                    
                    if ($show_thumbnail_subtitle) {
                    $content .= '<div class="events-list-post-date-thumbnail-subtitle" >';
                    $content .= $thumbnail_subtitle;
                    $content .= '</div>';
                    }
                $content .= '</div>';

                $content .= '<div class="events-list-post-image-thumbnail events-list-cell" >';
                    $content .= $image_content;
                $content .= '</div>';

                $content .= '<div class="events-list-post-text events-list-cell" >';
                    $content .= '<div class="events-list-post-text-header">';
                        $content .= $title;
                    $content .= '</div>';

                    //if ($show_excerp) {
                    $content .= '<div class="events-list-post-text-content">';
                        //$content .= event_list_custom_excerpt(a['excerpt_lenght'], a['ignore_more_tag']);
                        $content .= event_list_custom_excerpt('25', 'true');
                    $content .= '</div>';
                    //}
                $content .= '</div>';
                
            $content .= '</a>';
            
            // 'href' => get_the_permalink() 
            

            $i++;
        } // end while( $lptw_shortcode_query->have_posts() )
        $content .= '</div>';
    } else {
        $content = __( 'No recent posts', 'lptw_recent_posts_domain' );
    }
    wp_reset_postdata();
    return $content;
}

add_shortcode( 'events_list', 'events_list_display' );


/**
 Find embedded video and use standard oembed to display it
 **/
/* --------------------------------------------- second function --------------------------------------------- */
function lptw_get_first_embed_media($post_id) {

    $post = get_post($post_id);
    $reg = preg_match('|^\s*(https?://[^\s"]+)\s*$|im', get_the_content(), $embeds);

    $embed_args = Array ( 'width' => 400, 'height' => 200 );

    if( !empty($embeds) ) {
        //return first embed
        $embed_code = wp_oembed_get( trim($embeds[0]), $embed_args );
        return $embed_code;

    } else {
        //No embeds found
        return false;
    }

}

/* --------------------------------------------- Filter video output --------------------------------------------- */

add_filter('oembed_result','lptw_oembed_result', 10, 3);
function lptw_oembed_result ($html, $url, $args) {
    global $post;

    // $args includes custom argument
    /* ---------------- only for youtube ---------------- */
    /* all arguments */
    //$args = array( 'rel' => '0', 'controls' => '0', 'showinfo' => '0' );

    $hide_youtube_controls = get_post_meta ($post->ID, 'hide_youtube_controls', true);
    if ($hide_youtube_controls == 'on') {
        /* only hide controls */
        $args = array( 'controls' => 0 );
    } else { $args = ''; }


    if ( strpos($html, 'youtu') !== false && !empty($args) ) {
    	$parameters = http_build_query( $args );

    	// Modify video parameters
	    $html = str_replace( '?feature=oembed', '?feature=oembed'.'&amp;'.$parameters, $html );
    }

    return $html;
}

?>