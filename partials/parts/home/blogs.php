<?php

use Cyan\Theme\Helpers\Icon;
use Cyan\Theme\Helpers\Templates;

$home_page_id = get_option('page_on_front');
$home_blogs_selected = get_field('home_blogs_selected', $home_page_id);

if (! empty($home_blogs_selected)) {
    $post_ids = is_array($home_blogs_selected) ? $home_blogs_selected : [(int) $home_blogs_selected];
    $home_blogs_query = new WP_Query([
        'post_type'      => 'post',
        'post__in'       => array_map('intval', $post_ids),
        'orderby'        => 'post__in',
        'posts_per_page' => 3,
    ]);
} else {
    $home_blogs_query = new WP_Query([
        'post_type'      => 'post',
        'posts_per_page' => 3,
    ]);
}

if (! $home_blogs_query->have_posts()) {
    return;
}

?>

<div class="flex flex-col md:flex-row max-md:gap-3">
    <?php while ($home_blogs_query->have_posts()) : $home_blogs_query->the_post(); ?>
        <?php Templates::getCard('blog'); ?>
    <?php endwhile; ?>
</div>