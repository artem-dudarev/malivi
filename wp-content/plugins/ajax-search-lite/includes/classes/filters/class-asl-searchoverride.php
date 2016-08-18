<?php
if (!defined('ABSPATH')) die('-1');

if (!class_exists("WD_ASL_SearchOverride_Filter")) {
    /**
     * Class WD_ASL_SearchOverride_Filter
     *
     * Handles search override filters
     *
     * @class         WD_ASL_SearchOverride_Filter
     * @version       1.0
     * @package       AjaxSearchPro/Classes/Filters
     * @category      Class
     * @author        Ernest Marcinko
     */
    class WD_ASL_SearchOverride_Filter extends WD_ASL_Filter_Abstract {

        public function handle() {}

        public function override($posts, $wp_query) {

            // Not working on paginated results, multisite
            /*if (!$wp_query->is_search())
                return $posts;*/

            // Is this a search query
            if (empty($wp_query->query_vars['s'])) {
                return $posts;
            }

            // Is this just a NEW regular search?
            if ( !isset($_GET['asl_active']) ) {
                // Then clear the override
                wd_asl()->wp_session['asl_override'] = false;
            }

            // Memorize the data across pagination for the form values..
            if (isset($_POST['p_asl_data']) || isset($_POST['np_asl_data'])) {
                $_p_data = isset($_POST['p_asl_data']) ? $_POST['p_asl_data'] : $_POST['np_asl_data'];
                wd_asl()->wp_session['asl_form_data'] = array("data" => $_p_data);
            } else if ( !empty(wd_asl()->wp_session['asl_form_data']) ) {
                // Get the memorized data if exists
                if ( is_object(wd_asl()->wp_session['asl_form_data']['data']) )
                    $_POST['np_asl_data'] = wd_asl()->wp_session['asl_form_data']['data']->toArray();
                else
                    $_POST['np_asl_data'] = wd_asl()->wp_session['asl_form_data']['data'];
            }

            if (isset($_POST['p_asl_data']) && $_POST['p_asl_data'] != '') {
                $_method = &$_POST;
                parse_str($_method['p_asl_data'], $s_data);

                // this is a new POST search, so set the session
                wd_asl()->wp_session['asl_override'] = array("asl_data" => $s_data);
            } else {
                // Is this a paginated search?
                if ( !empty(wd_asl()->wp_session['asl_override']) ) {
                    // ->toArray() method needs to be invoked, if the session handler converts the recursive array
                    if ( is_object(wd_asl()->wp_session['asl_override']['asl_data']) )
                        $s_data = wd_asl()->wp_session['asl_override']['asl_data']->toArray();
                    else
                        $s_data = wd_asl()->wp_session['asl_override']['asl_data'];
                } else {
                    return $posts;
                }
            }

            $_POST['options'] = $s_data;
            $_POST['options']['non_ajax_search'] = true;
            $_POST['aslp'] = $_GET['s'];
            $_POST['asl_get_as_array'] = 1;

            $o = WD_ASL_Search_Handler::getInstance();
            $res = $o->handle( true );

            // The get_query_var() is malfunctioning in some cases!!! use $_GET['paged']
            //$paged = (get_query_var('paged') != 0) ? get_query_var('paged') : 1;
            if ( isset($_GET['paged']) ) {
                $paged = $_GET['paged'];
            } else if ( isset($wp_query->query_vars['paged']) ) {
                $paged = $wp_query->query_vars['paged'];
            } else {
                $paged = 1;
            }

            $paged = $paged <= 0 ? 1 : $paged;

            $posts_per_page = (int)get_option('posts_per_page');

            // Get and convert the results needed
            $n_posts = asl_results_to_wp_obj( $res, ( $paged - 1 ) * $posts_per_page, $posts_per_page );

            $wp_query->found_posts = count($res);
            if (($wp_query->found_posts / $posts_per_page) > 1)
                $wp_query->max_num_pages = ceil($wp_query->found_posts / $posts_per_page);
            else
                $wp_query->max_num_pages = 0;

            return $n_posts;
        }

        public function fixUrls( $url, $post, $leavename ) {
            if (isset($post->asl_guid))
                return $post->asl_guid;
            return $url;
        }

        // ------------------------------------------------------------
        //   ---------------- SINGLETON SPECIFIC --------------------
        // ------------------------------------------------------------
        public static function getInstance() {
            if ( ! ( self::$_instance instanceof self ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }
    }
}