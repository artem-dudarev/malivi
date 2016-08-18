=== Ajax WP Query Search Filter ===
Contributors: TC.K
Donate link: http://9-sec.com/donation/
Tags: Search Filter, taxonoy, custom post type, custom meta field, taxonomy & meta field filter, advanced search, Ajax, search engine
Requires at least: 3.4
Tested up to: 3.7.1
Stable tag: 1.0.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Ajax WP Query Search Filter let you search through post type, taxonomy and meta field. 

== Description ==
**THIS PLUGIN WILL NOT BE UPDATE ANYMORE, PLEASE USE MY NEW PLUGN [ULTIMATE WP QUERY SEARCH QUERY](http://wordpress.org/plugins/ultimate-wp-query-search-filter/) INSTEAD**

Ajax WP Query Search Filter is a powerful ajax search engine that let your user perform more precisely search by filtering the search through post type, taxonomy and meta field. 

**Plugin Features:**

* Admin are free to choose whether the search go through post type, taxonomy, meta field or even all of them.
* Multiple Search Form Supported.
* Search form support checkbox,radio and dropdown fields.
* Using Ajax to display result in the same page complete with pagination.
* Plugin extendable with hooks.
* Using shortcode to display the search form.


If you have any problems with current plugin, please leave a
message on Forums Posts or goto [Here](http://9-sec.com/).


== Installation ==

1. Upload `ajax-wqsf` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create your search form in the Advance WQSF.
4. Using `[AjaxWPQSF id={form id}]` to display the form. 

== Frequently Asked Questions ==

= How can I styling the search form? =

You can simply refer the classes/scripts/default.css that come with the folder and alter it or override it at your theme css file. 

= What if I want to display the search form in the template? =

Put this into `<?php echo do_shortcode("[AjaxWPQSF id={form id}"); ?>` your template.

= What if I want to display the search form in the sidebar widget? =

Just insert the shortcodes like you inserted in the post content. eg. '[AjaxWPQSF id=3299]`

= What if I don't want to display the title of the search form? =

Just giving `0` to `formtitle` atribute in the shortcode eg. '[AjaxWPQSF id=3299 formtitle="0"]`

= How can I customize the plugin? =

You can goto this [website](http://9-sec.com/) to get the details.

== Screenshots ==
1. Ajax WP Query Search Filter setting page 1
2. Ajax WP Query Search Filter setting page 2
2. Ajax WP Query Search Filter setting page 3
4. Ajax WP Query Search Filter setting page 4
5. Ajax WP Query Search Filter search form in the content and sidebar


== Changelog ==


= 1.0.0 =
* First version released.

= 1.0.1 =
* Fixed minor error and prevent submit form from key press enter.

= 1.0.2 =
* Fix js error in IE browser (Thanks member iveranus2 for this fix)
* Add another two filters for taxonomy and meta field before passing to wp_query.

= 1.0.3 =
* Fix plugin activation error.
* Make js global variable.
* Add new filters and add parameters to existing filter and action.
* Fix default css loading image error.

= 1.0.4 =
* Add form id to multiple filters.
* Fix pop up witdh and height in setting page.
* Fix activation error on front page. 

= 1.0.5 =
* Fix ehco error. 
* add new parameter to result ouput filter
* Change variable in search form action hook

= 1.0.6 =
* Fix IE Js Error 
* add new filter to admin
* add taxonomy filter
* Search All will be not included when it is not set.

= 3.7.1 =
* Fix JS error for wp 3.7.1
