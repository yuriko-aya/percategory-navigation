<?php
/*
Plugin Name:  Per Category Navigation
Plugin URI:   https://aya.sanus.id
Description:  Make Navigation for post by category
Version:      20181207
Author:       Yuriko Aya
Author URI:   https://aya.sanusi.id
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

add_action('get_header','add_bootstrap');
function add_bootstrap() {
    wp_enqueue_style('bootstrap_for_nav','https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css');
    wp_enqueue_style('per_category_navi_style', plugins_url( 'css/style.css', __FILE__ ));
}


add_action('the_content','the_nav');
function the_nav($content) {
    if(is_single()){
        //Get whatever object we're working with (category or post?)
        $thisObj = get_queried_object();
        $post_title = get_the_title();
    
        //If it's a post, get the category ID
        if(!is_null($thisObj->ID)){
            $currentCat = get_the_category();
            if($currentCat[1]->cat_name == 'Novel') {
                $cat_name = $currentCat[0]->name;
                $cat_id = $currentCat[0]->cat_ID;
                $cat_link = $currentCat[0]->slug;
            } else {
                $cat_name = $currentCat[1]->cat_name;
                $cat_id = $currentCat[1]->cat_ID;
                $cat_link = $currentCat[1]->slug;
            }
        }
    
        $args = array (
            'numberposts' => -1,
            'category' => $cat_id,
            'orderby' => 'date',
            'order' => 'ASC'
        );

        $cat_lists = get_posts($args);
        //print_r($cat_lists);
        foreach($cat_lists as $category) {
            $post_list[] = $category->post_title;
            $link_list[] = $category->post_name;
        }

        $location = array_search($post_title, $post_list);
        if($location == 0) {
            $prev = '<div class="col-sm-4 text-left"></div>';
        } else {
            $prev = '<div class="col-sm-4 text-left"><a href="/' .$link_list[$location-1].'"><< PREV <br>' .$post_list[$location-1]. '</a></div>';
        }
        if($location+1 == count($post_list)) {
            $next = '<div class="col-sm-4 text-right"+></div>';
        } else {
            $next = '<div class="col-sm-4 text-right"><a href="/' .$link_list[$location+1].'">NEXT >><br>' .$post_list[$location+1]. '</a></div>';
        }
        $cat_index = '<div class="col-sm-4 text-center"><a href="/' .$cat_link.'">INDEX <br>' .$cat_name. '</a></div>';
        
        $cat_nav = '<hr><div class="row">'. $prev.$cat_index.$next .'</div><hr>';

        return $content.$cat_nav;
    } else {
        return $content;
    }
}