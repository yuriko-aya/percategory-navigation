<?php
/*
Plugin Name:  Per Category Navigation
Plugin URI:   https://aya.sanusi.id
Description:  Make Navigation for post by category
Version:      20190114
Author:       Yuriko Aya
Author URI:   https://aya.sanusi.id
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

add_action('get_header','add_bootstrap');
function add_bootstrap() {
    // let's add bootstrap for nav in case you don't have one! also some cutom css
    wp_enqueue_style('bootstrap_for_nav','https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css');
    wp_enqueue_style('per_category_navi_style', plugins_url( 'css/style.css', __FILE__ ));
}


add_action('the_content','the_nav');
function the_nav($content) {
    //make sure it was single post page
    if(is_single()){
        //get post data
        global $post;

        //sorting out the terms make sure latest child category at the top
        $term_args = array (
            'orderby' => 'term_id',
            'order' => 'DESC',
        );

        // get post slug, category name and id
        $post_slug = $post->post_name;
        $terms = wp_get_object_terms( $post->ID, 'category', $term_args);
        $cat_name =  $terms[0]->name;
        $cat_id =  $terms[0]->term_id;

        $posts_args = array (
            'numberposts' => -1,
            'category' => $cat_id,
            'orderby' => 'date',
            'order' => 'ASC'
        );

        $cat_lists = get_posts($posts_args); // pull out all post contents from current catogory
        $cat_link = get_category_link($cat_id); // get category link for index

        // get post title and slug from all posts content
        foreach($cat_lists as $category) {
            $post_list[] = $category->post_title;
            $link_list[] = $category->post_name;
        }

        $location = array_search($post_slug, $link_list); // Search current post location in category
        
        if($location == 0) { 
            // it it was first post, remove previous link
            $prev = '<div class="col-sm-4 text-left percanav"></div>';
        } else {
            // if not first, make normal previous link 
            $prev = '<div class="col-sm-4 text-left percanav"><a href="/' .$link_list[$location-1].'"><< PREV <br>' .$post_list[$location-1]. '</a></div>';
        }

        if($location+1 == count($post_list)) {
            // if post was last post, remove next link
            $next = '<div class="col-sm-4 text-right percanav"></div>';
        } else {
            // if not last, create next link
            $next = '<div class="col-sm-4 text-right percanav"><a href="/' .$link_list[$location+1].'">NEXT >><br>' .$post_list[$location+1]. '</a></div>';
        }

        // index link
        $cat_index = '<div class="col-sm-4 text-center percanav"><a href="'.$cat_link.'">INDEX <br>' .$cat_name. '</a></div>';

        $cat_nav = '<div class="row">'. $prev.$cat_index.$next .'</div>';

        return $cat_nav. '<br><br>' .$content.$cat_nav;
    } else {
        return $content;
    }
}