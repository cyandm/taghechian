<?php

use Cyan\Theme\Helpers\Icon;
use Cyan\Theme\Helpers\Templates;

$home_page_id         = get_option('page_on_front');
$home_amazing_title   = get_field('home_amazing_title', $home_page_id);
$home_amazing_under_title = get_field('home_amazing_under_title', $home_page_id);
$home_amazing_date_time  = get_field('home_amazing_date_time', $home_page_id);
$home_amazing_post_ids   = get_field('home_amazing_post', $home_page_id);
$home_amazing_renew      = get_field('home_amazing_status', $home_page_id);

if (empty($home_amazing_title) && empty($home_amazing_under_title) && empty($home_amazing_date_time)) {
    return;
}

if (! empty($home_amazing_post_ids)) {
    $post_ids = is_array($home_amazing_post_ids) ? $home_amazing_post_ids : [$home_amazing_post_ids];
    $home_amazing_products = new WP_Query([
        'post_type'      => 'product',
        'post__in'       => array_map('intval', $post_ids),
        'orderby'        => 'post__in',
        'posts_per_page' => -1,
    ]);
} else {
    $home_amazing_products = new WP_Query([
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'tax_query'      => [
            [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => 'shegeftchian',
            ],
        ],
    ]);
}

if (! $home_amazing_products->have_posts()) {
    return;
}

$countdown_target = '';
$countdown_renew  = ($home_amazing_renew === 'yes' || $home_amazing_renew === true);
if (! empty($home_amazing_date_time)) {
    $tz = function_exists('wp_timezone') ? wp_timezone() : new \DateTimeZone(get_option('timezone_string') ?: 'UTC');
    $dt = \DateTime::createFromFormat('YmdHis', $home_amazing_date_time, $tz);
    if ($dt) {
        $countdown_target = $dt->format('c');
        if (! $countdown_renew && $dt->getTimestamp() <= time()) {
            return;
        }
    }
}
?>

<section class="my-14 bg-cynYellow py-6 flex flex-col gap-3.5 md:gap-5">

    <div class="container flex justify-between items-center flex-col md:flex-row gap-3">

        <div class="flex flex-col gap-2 max-md:justify-center max-md:text-center max-md:w-full">
            <p class="text-3xl md:text-[40px] font-normal"><?php echo esc_html($home_amazing_title); ?></p>
            <p class="text-cynBlack text-base md:text-xl font-medium"><?php echo esc_html($home_amazing_under_title); ?></p>
        </div>

        <?php if ($countdown_target) : ?>
            <div id="flipclock-sales" class="flipclock-sales" dir="rtl" data-countdown-to="<?php echo esc_attr($countdown_target); ?>" data-renew-on-end="<?php echo $countdown_renew ? 'yes' : 'no'; ?>"></div>
        <?php endif; ?>
    </div>

    <swiper-container class="w-full" space-between="20" slides-per-view="1.25" centered-slides="true" breakpoints='{ "640":  { "slidesPerView": 2.15 }, "768":  { "slidesPerView": 2.15 }, "1024": { "slidesPerView": 3.25 }, "1280": { "slidesPerView": 4, "centeredSlides": false }}' loop="true" autoplay="true">
        <?php
        while ($home_amazing_products->have_posts()) :
            $home_amazing_products->the_post();
        ?>
            <swiper-slide>
                <?php Templates::getCard('product', ['product' => get_the_ID()]); ?>
            </swiper-slide>
        <?php
        endwhile;
        wp_reset_postdata();
        ?>
    </swiper-container>

</section>