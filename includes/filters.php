<?php

use SourceFlood\Storage;

add_filter('wp_title', 'sourceflood_seo_title');
add_filter('the_content', 'workhorse_the_content_filter');
// parse the generated links
add_filter('post_type_link', 'workhorse_custom_post_permalink', 10, 4);
add_filter('page_rewrite_rules', 'workhorse_allow_numeric_stubs');

function workhorse_allow_numeric_stubs($rules) {
    unset( $rules['(.+?)(/[0-9]+)?/?$'] );

    $rules['(.+?)?/?$'] = 'index.php?pagename=$matches[1]';

    return $rules;
}

function workhorse_custom_post_permalink($permalink, $post, $leavename, $sample) {
	$storage = new Storage('workhouse');
	$prefixes = $storage->permalink_prefixes;
	
    // only do our stuff if we're using pretty permalinks
    // and if it's our target post type
    if (isset($prefixes[$post->post_type]) && get_option('permalink_structure')) {
        $struct = "/{$post->post_type}/%category%/%postname%/";

        $rewritecodes = array(
            '%category%',
            '%postname%'
        );

        // setup data
        $terms = get_the_terms($post->ID, 'category');
        
        // this code is from get_permalink()
        $category = '';

        if (strpos($permalink, '%category%') !== false) {
            $cats = get_the_category($post->ID);
            if ( $cats ) {
                usort($cats, '_usort_terms_by_ID'); // order by ID
                $category = $cats[0]->slug;
                if ( $parent = $cats[0]->parent )
                    $category = get_category_parents($parent, false, '/', true) . $category;
            }
            // show default category in permalinks, without
            // having to assign it explicitly
            if ( empty($category) ) {
                $default_category = get_category( get_option( 'default_category' ) );
                $category = is_wp_error( $default_category ) ? '' : $default_category->slug;
            }
        }

        $replacements = array(
            $category,
            $post->post_name
        );

        // finish off the permalink
        $permalink = home_url( str_replace( $rewritecodes, $replacements, $struct ) );
        $permalink = user_trailingslashit($permalink, 'single');
    }

    // for channel pages
    if ($post->post_type == 'channel' && get_option('permalink_structure')) {
        preg_match("/^(state|city)\-(.*)\-(\d+)$/", $post->post_name, $matches);
        $type = $matches[1];
        $place = $matches[2];
        $project_id = $matches[3];
        
        foreach ($prefixes as $prefix => $config) {
            if ($config['project_id'] == $project_id) break;
        }

        if ($config['deep'] == 1) {
            $permalink = home_url("/$prefix/$place/");
        }
        elseif ($config['deep'] == 2) {
            $parent = get_post($post->post_parent);
            preg_match("/^(state|city)\-(.*)\-(\d+)$/", $parent->post_name, $matches);
            $parent_place = $matches[2];
            
            $permalink = home_url($type == 'city' ? "/$prefix/$parent_place/$place/" : "/$prefix/$place/");
        }
    }

    return $permalink;
}
function sourceflood_seo_title($title) {
	return $title;
}

function workhorse_the_content_filter($content) {
	global $post;
    
	if ($post->post_type == 'channel') {
		$content = str_replace('@citylist', '', $content);
		$content = str_replace('@ziplist', '', $content);
	}

	return $content;
}

function change_blog_links($post_link, $id = 0){
	$post = get_post($id);
 
	$storage = new Storage('workhouse');
	$prefixes = $storage->permalink_prefixes;
	
	/*if (sizeof($prefixes)) {
		foreach ($prefixes as $prefix => $void) {
			if ($prefix != $post->post_type) continue;

			$siteurl = get_option('siteurl');

			return str_replace($siteurl, "{$siteurl}/{$prefix}", $post_link);
		}
	}*/

	return $post_link;
}
