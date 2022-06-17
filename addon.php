<?php
/**
 * Description: Convenience functions for wordpress and OceanWP
 *
 * 1. Allow #latest custom link to load the most recent blog post
 * 2. Create shortcode that displays the archive like 'Blog Entries' OceanWP style
 *
 * append to functions.php
 */
if (!is_admin()) {
    add_filter('wp_get_nav_menu_items', 'blog_latest_custom_link', 10, 3);
}

// Replaces a custom URL placeholder with the URL to the latest post
function blog_latest_custom_link($blog_items, $menu, $args) {
    foreach ($blog_items as $blog_item) {
        if ('#latest' != $blog_item->url) {
            continue;
        }

        $latestpost = get_posts(array(
            'numberposts' => 1,
        ));

        if (empty($latestpost)) {
            continue;
        }

        // Set placeholder to blog post URL
        $blog_item->url = get_permalink($latestpost[0]->ID);
    }

    return $blog_items;
}

add_shortcode('blog_entries_archive', 'blog_entries_archive');
function blog_entries_archive($atts, $content = null)
{

    global $post;

    extract(shortcode_atts(array(
        'num' => '10',
        'order' => 'DESC',
        'orderby' => 'post_date',
    ), $atts));

    $args = array(
        'posts_per_page' => $num,
        'order' => $order,
        'orderby' => $orderby,
    );

    $posts = get_posts($args);
    $output = '';

    if ($posts) {
        $output .= '
<div id="blog-entries" class="entries clr">';

        foreach ($posts as $post) {
            setup_postdata($post);
            ob_start();

            get_template_part('partials/entry/layout');

            $output .= ob_get_clean();
        }

        $output .= '
</div>';

        wp_reset_postdata();
    }

    return $output;

}