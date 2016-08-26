<?php
/*
Plugin Name: Events list
Plugin URI: 
Description: Plugin that shows upcoming events of The Events Calendar plugin with shortcodes.
Tags: widget, plugin, events, video, shortcode, list, thumbnail, thumbnails, categories, content, featured image, Taxonomy, custom post type, custom
Version: 1.0
Author: Ilia Dudarev
Author URI: 
License: GPLv2 or later
Text Domain: events-list-for-the-events-calendar_domain
*/

/* load js and css styles */
function events_list_register_scripts() {
	wp_register_style( 'events-list-style', plugins_url( 'events-list.css', __FILE__ ) );
	wp_enqueue_style( 'events-list-style' );

    wp_enqueue_script( 'events-list-script', plugins_url( 'events-list.js', __FILE__ ), array('jquery', 'jquery-masonry'), false, true );
}
add_action( 'wp_enqueue_scripts', 'events_list_register_scripts' );

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


function replace_featured_image_size($size, $post_id) {
    return 'medium';
}
add_filter('tribe_event_featured_image_size', 'replace_featured_image_size', 10, 2);


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


/**
-------------------------------------- Shortcode --------------------------------------
**/

function events_list_display ( $atts ) {
    $default_posts_per_page =  get_option( 'posts_per_page', '10' );

    $a = shortcode_atts( array(
        'post_type'                 => 'post',
        'posts_per_page'            => $default_posts_per_page,
        'columns'                   => '1',
        'show_date_before_title'    => 'true',
        'order'                     => 'DESC',
        'orderby'                   => 'date',
        'reverse_post_order'        => 'false',
        'excerpt_lenght'            => '35',
        'post_offset'               => 0,
        'link_target'               => 'self'
    ), $atts );

    /* ------------------------------------ WP_Query arguments filter start ------------------------------------ */
    if ($a['no_thumbnails'] == 'hide') { $meta_key = '_thumbnail_id'; }
    else { $meta_key = ''; }

    $query_args = array(
        'post_type'             => $a['post_type'],
        'posts_per_page'        => $a['posts_per_page'],
		'no_found_rows'         => true,
		'post_status'           => 'publish',
		'ignore_sticky_posts'   => true,
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

    /* date, title and subtitle position end */

    $query = new WP_Query( $query_args );
    if( $query->have_posts() ) {
        if ($a['reverse_post_order'] == 'true') { $query->posts = array_reverse($query->posts); }
        $content = '<div class="events-list-table">';
        while( $query->have_posts() ) {
            $query->the_post();
            $post_id = get_the_ID();

            /* ------------------------------------ start layouts output ------------------------------------ */

            $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($post_id) );
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
                    $post_time = get_the_date('H:i');
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
                $image_content .= mb_substr($title, 0, 1);
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

                    $content .= '<div class="events-list-post-text-content">';
                        //$content .= event_list_custom_excerpt(a['excerpt_lenght'], a['ignore_more_tag']);
                        $content .= event_list_custom_excerpt('25', 'true');
                    $content .= '</div>';
                $content .= '</div>';
                
            $content .= '</a>';
            
        } // end while( $query->have_posts() )
        $content .= '</div>';
    } else {
        $content = __( 'No recent posts', 'lptw_recent_posts_domain' );
    }
    wp_reset_postdata();
    return $content;
}

add_shortcode( 'events_list', 'events_list_display' );


/* Find embedded video and use standard oembed to display it */
function events_list_get_first_embed_media($post_id) {

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

add_filter('oembed_result','events_list_oembed_result', 10, 3);
function events_list_oembed_result ($html, $url, $args) {
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