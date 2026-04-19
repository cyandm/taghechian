<?php

use Cyan\Theme\Helpers\Icon;
use Cyan\Theme\Helpers\Templates;

$home_page_id = get_option('page_on_front');
$home_popular_products_title = get_field('home_popular_products_title', $home_page_id);
$home_popular_products_under_title = get_field('home_popular_products_under_title', $home_page_id);
$home_popular_products_selected = get_field('home_popular_products_selected', $home_page_id);
$home_popular_products_button = get_field('home_popular_products_button', $home_page_id);

if (! empty($home_popular_products_selected)) {
    $post_ids = is_array($home_popular_products_selected) ? $home_popular_products_selected : [ (int) $home_popular_products_selected ];
    $home_popular_products_query = new WP_Query([
        'post_type'      => 'product',
        'post__in'       => array_map('intval', $post_ids),
        'orderby'        => 'post__in',
        'posts_per_page' => -1,
    ]);
} else {
    $home_popular_products_query = new WP_Query([
        'post_type'      => 'product',
        'posts_per_page' => 12,
        'meta_key'       => 'total_sales',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
    ]);
}

if (! $home_popular_products_query->have_posts()) {
    return;
}

if (empty($home_popular_products_title) && empty($home_popular_products_under_title) && empty($home_popular_products_button)) {
    return;
}
?>

<section class="flex flex-col gap-3 my-14">
    <div class="container flex justify-between items-center">
        <div class="flex flex-col gap-2 max-md:justify-center max-md:text-center max-md:w-full">
            <?php if ($home_popular_products_title) : ?>
                <p class="text-3xl md:text-[40px] font-normal"><?php echo esc_html($home_popular_products_title); ?></p>
            <?php endif; ?>
            <?php if ($home_popular_products_under_title) : ?>
                <p class="text-cynBlack/50 text-base md:text-xl font-medium"><?php echo esc_html($home_popular_products_under_title); ?></p>
            <?php endif; ?>
        </div>

        <?php if (! empty($home_popular_products_button['url'])) : ?>
            <a href="<?php echo esc_url($home_popular_products_button['url']); ?>" class="primary-btn max-md:hidden"><?php echo esc_html($home_popular_products_button['title'] ?? ''); ?></a>
        <?php endif; ?>
    </div>

    <div class="relative">
        <swiper-container class="w-full" slides-per-view="1.25" centered-slides="true" space-between="0" breakpoints='{ "640":  { "slidesPerView": 2.15 }, "768":  { "slidesPerView": 2.15 }, "1024": { "slidesPerView": 3.25 }, "1280": { "slidesPerView": 4, "centeredSlides": false }}' loop="true" autoplay="false" pagination="false" navigation="true" navigation-next-el="#homePopularProductsNext" navigation-prev-el="#homePopularProductsPrev">
            <?php
            while ($home_popular_products_query->have_posts()) :
                $home_popular_products_query->the_post();
                ?>
                <swiper-slide>
                    <?php Templates::getCard('product', ['product' => get_the_ID()]); ?>
                </swiper-slide>
            <?php endwhile; wp_reset_postdata(); ?>
        </swiper-container>

        <div class="flex justify-between items-center absolute top-1/2 -translate-y-1/2 left-0 right-0 px-2 pointer-events-none z-10">
            <button type="button" id="homePopularProductsPrev" class="bg-cynBlack p-3 cursor-pointer rounded-full pointer-events-auto" aria-label="<?php esc_attr_e('قبلی', 'taghechian'); ?>">
                <i class="text-white size-5"><?php Icon::print('Arrow-19'); ?></i>
            </button>
            <button type="button" id="homePopularProductsNext" class="bg-cynBlack p-3 cursor-pointer rounded-full pointer-events-auto" aria-label="<?php esc_attr_e('بعدی', 'taghechian'); ?>">
                <i class="text-white size-5"><?php Icon::print('Arrow-27'); ?></i>
            </button>
        </div>
    </div>

    <?php if (! empty($home_popular_products_button['url'])) : ?>
        <div class="container">
            <a href="<?php echo esc_url($home_popular_products_button['url']); ?>" class="primary-btn md:hidden w-full flex justify-center items-center"><?php echo esc_html($home_popular_products_button['title'] ?? ''); ?></a>
        </div>
    <?php endif; ?>
</section>
