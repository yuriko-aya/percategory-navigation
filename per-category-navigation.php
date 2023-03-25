<?php
/*
Plugin Name:  Per Category Navigation
Plugin URI:   https://aya.sanusi.id
Description:  Make Navigation for post by category
Version:      20230326
Author:       Yuriko Aya
Author URI:   https://aya.sanusi.id
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

require( dirname(__FILE__) . '/' . 'admin/admin-menu.php');

add_action('get_header','add_bootstrap');
function add_bootstrap() {
    $the_date = date("Ynj.G.i");
    // let's add bootstrap for nav in case you don't have one! also some cutom css and js for drop down
    wp_enqueue_style('bootstrap_for_nav',plugins_url( 'css/bootstrap.min.css', __FILE__ ), array(), $ver = $the_date);
    wp_enqueue_style('per_category_navi_style', plugins_url( 'css/style.css', __FILE__ ), array(), $ver = $the_date);
    wp_enqueue_script( 'percategory_navi_script', plugins_url( 'js/script.js', __FILE__ ), array(), $ver = $the_date, true );
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
        $current_post_id = $post->ID;
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
            $post_id[] = $category->ID;
        }

        $location = array_search($current_post_id, $post_id); // Search current post location in category

        if(isset($_GET['preview']) && $_GET['preview'] == 'true' && empty($location)){        
            $prev = '<div class="col-sm-4 text-left percanav"><a href="' .get_permalink( $post_id[count($post_list)-1] ).'"><< PREV <br>' .$post_list[count($post_list)-1]. '</a></div>';
            $next = '<div class="col-sm-4 text-right percanav"></div>';
        } else {
            if($location == 0) { 
                // it it was first post, remove previous link
                $prev = '<div class="col-sm-4 text-left percanav"></div>';
            } else {
                // if not first, make normal previous link 
                $prev = '<div class="col-sm-4 text-left percanav"><a href="' .get_permalink( $post_id[$location-1] ).'"><< PREV <br>' .$post_list[$location-1]. '</a></div>';
            }

            if($location+1 == count($post_list)) {
                // if post was last post, remove next link
                $next = '<div class="col-sm-4 text-right percanav"></div>';
            } else {
                // if not last, create next link
                $next = '<div class="col-sm-4 text-right percanav"><a href="' .get_permalink( $post_id[$location+1] ).'">NEXT >><br>' .$post_list[$location+1]. '</a></div>';
            }
        }

        // index link
        $cat_index = '<div class="col-sm-4 text-center percanav"><a href="'.$cat_link.'">INDEX <br>' .$cat_name. '</a>';
        $cat_index .= '<select id="chapter-select">';
        for ($i=0; $i < count($post_list); $i++) {
            if ($i == $location) { 
                $cat_index .= '<option value="' .get_permalink( $post_id[$i] ). '" selected>' .$post_list[$i]. '</option>';
        } else {
                $cat_index .= '<option value="' .get_permalink( $post_id[$i] ). '">' .$post_list[$i]. '</option>';
            }
        }
        $cat_index .= '</select></div>';
        $cat_nav = '<div class="row">'. $prev.$cat_index.$next .'</div>';

        // Checking for existed config
        if (get_option('catnav_location') && get_option('catnav_location') == 'top') {
            return $cat_nav.$content;
        } elseif (get_option('catnav_location') && get_option('catnav_location') == 'bottom') {
            return $content.$cat_nav;
        } else {
            return $cat_nav.$content.$cat_nav;
        }

    } else {
        return $content;
    }
}
