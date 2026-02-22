<?php
/* Template Name: Shop */

use Cyan\Theme\Helpers\Icon;
use Cyan\Theme\Helpers\Templates;

$shop_collection_videos = [];
for ($i = 1; $i <= 3; $i++) {
    $video = get_field('shop_video_' . $i);
    $cover = get_field('shop_video_cover_' . $i);
    $title = get_field('shop_video_title_' . $i);
    if ($video || $cover || $title) {
        $shop_collection_videos[] = [
            'video'  => $video,
            'cover'  => $cover,
            'title'  => $title,
        ];
    }
}

$shop_first_section_title = get_field('shop_first_section_title');
$shop_first_section_under_title = get_field('shop_first_section_under_title');
$shop_first_section_button = get_field('shop_first_section_button');
$shop_first_section_persian_title = get_field('shop_first_section_persian_title');
$shop_first_section_english_title = get_field('shop_first_section_english_title');
$shop_first_section_post = get_field('shop_first_section_post');

if (!empty($shop_first_section_post)) {
    $product_ids = array_map(function ($p) {
        return is_object($p) ? $p->ID : (int) $p;
    }, (array) $shop_first_section_post);
    $first_section_products_query = new WP_Query([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'post__in'       => $product_ids,
        'orderby'        => 'post__in',
    ]);
} else {
    $first_section_products_query = new WP_Query([
        'post_type'      => 'product',
        'posts_per_page' => 12,
        'post_status'    => 'publish',
    ]);
}

$shop_banner_one_image_desktop = get_field('shop_banner_one_image_desktop');
$shop_banner_one_image_mobile = get_field('shop_banner_one_image_mobile');
$shop_banner_one_link = get_field('shop_banner_one_link');

$shop_second_section_title = get_field('shop_second_section_title');
$shop_second_section_under_title = get_field('shop_second_section_under_title');
$shop_second_section_button = get_field('shop_second_section_button');
$shop_second_section_persian_title = get_field('shop_second_section_persian_title');
$shop_second_section_english_title = get_field('shop_second_section_english_title');
$shop_second_section_post = get_field('shop_second_section_post');

if (!empty($shop_second_section_post)) {
    $product_ids = array_map(function ($p) {
        return is_object($p) ? $p->ID : (int) $p;
    }, (array) $shop_second_section_post);
    $second_section_products_query = new WP_Query([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'post__in'       => $product_ids,
        'orderby'        => 'post__in',
    ]);
} else {
    $second_section_products_query = new WP_Query([
        'post_type'      => 'product',
        'posts_per_page' => 12,
        'post_status'    => 'publish',
        'tax_query'      => [
            [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => 'home-decor',
            ],
        ],
    ]);
}

$shop_banner_two_image_desktop = get_field('shop_banner_two_image_desktop');
$shop_banner_two_image_mobile = get_field('shop_banner_two_image_mobile');
$shop_banner_two_link = get_field('shop_banner_two_link');

$shop_third_section_title = get_field('shop_third_section_title');
$shop_third_section_under_title = get_field('shop_third_section_under_title');
$shop_third_section_button = get_field('shop_third_section_button');
$shop_third_section_persian_title = get_field('shop_third_section_persian_title');
$shop_third_section_english_title = get_field('shop_third_section_english_title');
$shop_third_section_post = get_field('shop_third_section_post');

if (!empty($shop_third_section_post)) {
    $product_ids = array_map(function ($p) {
        return is_object($p) ? $p->ID : (int) $p;
    }, (array) $shop_third_section_post);
    $third_section_products_query = new WP_Query([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'post__in'       => $product_ids,
        'orderby'        => 'post__in',
    ]);
} else {
    $third_section_products_query = new WP_Query([
        'post_type'      => 'product',
        'posts_per_page' => 12,
        'post_status'    => 'publish',
        'tax_query'      => [
            [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => 'jewelry',
            ],
        ],
    ]);
}

get_header();
?>

<?php Templates::getPart('breadcrumb'); ?>

<main class="shop">

    <?php if (!empty($shop_collection_videos)) : ?>

        <section class="relative container">
            <swiper-container class="w-full" slides-per-view="1.15" centered-slides="true" initial-slide="1" breakpoints='{ "280":  { "slidesPerView": 1.15, "spaceBetween": 12 }, "640":  { "slidesPerView": 2.15, "spaceBetween": 12 }, "768":  { "slidesPerView": 3, "spaceBetween": 0, "centeredSlides": false }, "1024": { "slidesPerView": 3, "spaceBetween": 0 }, "1280": { "slidesPerView": 3, "spaceBetween": 0 }}' pagination="false" navigation="true" navigation-next-el="#shopCollectionVideosNext" navigation-prev-el="#shopCollectionVideosPrev">
                <?php foreach ($shop_collection_videos as $index => $item) :
                    $video_id  = is_array($item['video']) ? ($item['video']['ID'] ?? 0) : (int) $item['video'];
                    $video_url = $video_id ? wp_get_attachment_url($video_id) : '';
                    $video_mime = $video_id ? get_post_mime_type($video_id) : '';
                    $cover_id  = is_array($item['cover']) ? ($item['cover']['ID'] ?? 0) : (int) $item['cover'];
                    $cover_url = $cover_id ? wp_get_attachment_image_url($cover_id, 'medium_large') : '';
                    $title     = $item['title'] ?? '';
                    if (!$video_url && !$cover_url) continue;
                ?>
                    <swiper-slide>
                        <a href="<?php echo $video_url ? esc_url($video_url) : '#'; ?>"
                            <?php if ($video_url) : ?>
                            data-fancybox="shop-collection-videos"
                            data-type="html5video"
                            data-src="<?php echo esc_url($video_url); ?>"
                            data-format="<?php echo esc_attr($video_mime); ?>"
                            <?php if ($cover_url) : ?>data-poster="<?php echo esc_url($cover_url); ?>" <?php endif; ?>
                            <?php endif; ?>
                            class="shop-video-card block relative w-full h-[446px] lg:h-[520px] overflow-hidden bg-cynBlack/10 group">
                            <?php if ($cover_url) : ?>
                                <img src="<?php echo esc_url($cover_url); ?>" alt="<?php echo esc_attr($title); ?>" class="absolute inset-0 w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" />
                            <?php endif; ?>
                            <span class="absolute inset-0 bg-black/20"></span>
                            <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 flex items-center justify-center flex-col gap-2">
                                <img src="<?php echo esc_url(THEME_IMAGES_URI . '/play.svg'); ?>" alt="" class="size-16" />
                                <?php if ($title) : ?>
                                    <span class="text-white font-[Dinar] text-xl text-center"><?php echo esc_html($title); ?></span>
                                <?php endif; ?>
                            </span>

                        </a>
                    </swiper-slide>
                <?php endforeach; ?>
            </swiper-container>

            <div class="flex justify-between items-center absolute top-1/2 -translate-y-1/2 left-0 right-0 px-2 pointer-events-none z-10 md:hidden">

                <button id="shopCollectionVideosPrev" class="bg-cynBlack p-1 cursor-pointer rounded-full pointer-events-auto">
                    <div class="text-white size-5">
                        <?php icon::print('Arrow-19') ?>
                    </div>
                </button>

                <button id="shopCollectionVideosNext" class="bg-cynBlack p-1 cursor-pointer rounded-full pointer-events-auto">
                    <div class="text-white size-5">
                        <?php icon::print('Arrow-27') ?>
                    </div>
                </button>

            </div>

        </section>

    <?php endif; ?>

    <?php if ($first_section_products_query->have_posts()) : ?>

        <?php if ($shop_first_section_title && $shop_first_section_under_title && $shop_first_section_persian_title && $shop_first_section_english_title) : ?>

            <section class="flex flex-col gap-3 my-14">

                <div class="container flex justify-between items-center">

                    <div class="flex flex-col gap-2 max-md:justify-center max-md:text-center max-md:w-full">
                        <p class="text-3xl md:text-[40px] font-normal"><?php echo esc_html($shop_first_section_title); ?></p>
                        <p class="text-cynBlack/50 text-base md:text-xl font-medium"><?php echo esc_html($shop_first_section_under_title); ?></p>
                    </div>

                    <a href="<?php echo esc_url($shop_first_section_button['url']); ?>" class="primary-btn max-md:hidden"><?php echo esc_html($shop_first_section_button['title']); ?></a>

                </div>

                <div class="relative">

                    <swiper-container class="w-full" slides-per-view="1.25" centered-slides="true" breakpoints='{ "640":  { "slidesPerView": 2.15 }, "768":  { "slidesPerView": 2.15 }, "1024": { "slidesPerView": 3.25 }, "1280": { "slidesPerView": 4, "centeredSlides": false }}' loop="true" autoplay="false" pagination="false" navigation="true" navigation-next-el="#shopFirstSectionProductsNext" navigation-prev-el="#shopFirstSectionProductsPrev">
                        <?php while ($first_section_products_query->have_posts()) : $first_section_products_query->the_post(); ?>
                            <swiper-slide>
                                <?php Templates::getCard('product', ['product' => get_the_ID()]); ?>
                            </swiper-slide>
                        <?php endwhile; ?>
                    </swiper-container>

                    <div class="flex justify-between items-center absolute top-1/2 -translate-y-1/2 left-0 right-0 px-2 pointer-events-none z-10">
                        <button id="shopFirstSectionProductsPrev" class="bg-cynBlack p-3 cursor-pointer rounded-full pointer-events-auto">
                            <i class="text-white size-5">
                                <?php icon::print('Arrow-19') ?>
                            </i>
                        </button>
                        <button id="shopFirstSectionProductsNext" class="bg-cynBlack p-3 cursor-pointer rounded-full pointer-events-auto">
                            <i class="text-white size-5">
                                <?php icon::print('Arrow-27') ?>
                            </i>
                        </button>
                    </div>

                </div>


                <div class="container">
                    <a href="<?php echo esc_url($shop_first_section_button['url']); ?>" class="primary-btn md:hidden w-full flex justify-center items-center"><?php echo esc_html($shop_first_section_button['title']); ?></a>
                </div>

            </section>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($shop_banner_one_image_desktop || $shop_banner_one_image_mobile) : ?>
        <section class="container">
            <a href="<?php echo esc_url($shop_banner_one_link) ? esc_url($shop_banner_one_link) : '#'; ?>">
                <?php echo wp_get_attachment_image($shop_banner_one_image_desktop, 'full', false, ['class' => 'w-full h-full object-cover max-md:hidden']); ?>
                <?php echo wp_get_attachment_image($shop_banner_one_image_mobile, 'full', false, ['class' => 'w-full h-full object-cover md:hidden']); ?>
            </a>
        </section>
    <?php endif; ?>

    <?php if ($second_section_products_query->have_posts()) : ?>

        <?php if ($shop_second_section_title && $shop_second_section_under_title && $shop_second_section_persian_title && $shop_second_section_english_title) : ?>

            <section class="flex flex-col gap-3 my-14">

                <div class="container flex justify-between items-center">

                    <div class="flex flex-col gap-2 max-md:justify-center max-md:text-center max-md:w-full">
                        <p class="text-3xl md:text-[40px] font-normal"><?php echo esc_html($shop_second_section_title); ?></p>
                        <p class="text-cynBlack/50 text-base md:text-xl font-medium"><?php echo esc_html($shop_second_section_under_title); ?></p>
                    </div>

                    <a href="<?php echo esc_url($shop_second_section_button['url']); ?>" class="primary-btn max-md:hidden"><?php echo esc_html($shop_second_section_button['title']); ?></a>

                </div>

                <div class="relative">

                    <swiper-container class="w-full" slides-per-view="1.25" centered-slides="true" breakpoints='{ "640":  { "slidesPerView": 2.15 }, "768":  { "slidesPerView": 2.15 }, "1024": { "slidesPerView": 3.25 }, "1280": { "slidesPerView": 4, "centeredSlides": false }}' loop="true" autoplay="false" pagination="false" navigation="true" navigation-next-el="#shopSecondSectionProductsNext" navigation-prev-el="#shopSecondSectionProductsPrev">
                        <?php while ($second_section_products_query->have_posts()) : $second_section_products_query->the_post(); ?>
                            <swiper-slide>
                                <?php Templates::getCard('product', ['product' => get_the_ID()]); ?>
                            </swiper-slide>
                        <?php endwhile; ?>
                    </swiper-container>

                    <div class="flex justify-between items-center absolute top-1/2 -translate-y-1/2 left-0 right-0 px-2 pointer-events-none z-10">
                        <button id="shopSecondSectionProductsPrev" class="bg-cynBlack p-3 cursor-pointer rounded-full pointer-events-auto">
                            <i class="text-white size-5">
                                <?php icon::print('Arrow-19') ?>
                            </i>
                        </button>
                        <button id="shopSecondSectionProductsNext" class="bg-cynBlack p-3 cursor-pointer rounded-full pointer-events-auto">
                            <i class="text-white size-5">
                                <?php icon::print('Arrow-27') ?>
                            </i>
                        </button>
                    </div>

                </div>

                <div class="container">
                    <a href="<?php echo esc_url($shop_second_section_button['url']); ?>" class="primary-btn md:hidden w-full flex justify-center items-center"><?php echo esc_html($shop_second_section_button['title']); ?></a>
                </div>

            </section>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($shop_banner_two_image_desktop || $shop_banner_two_image_mobile) : ?>
        <section class="container">
            <a href="<?php echo esc_url($shop_banner_two_link) ? esc_url($shop_banner_two_link) : '#'; ?>">
                <?php echo wp_get_attachment_image($shop_banner_two_image_desktop, 'full', false, ['class' => 'w-full h-full object-cover max-md:hidden']); ?>
                <?php echo wp_get_attachment_image($shop_banner_two_image_mobile, 'full', false, ['class' => 'w-full h-full object-cover md:hidden']); ?>
            </a>
        </section>
    <?php endif; ?>

    <?php if ($third_section_products_query->have_posts()) : ?>

        <?php if ($shop_third_section_title && $shop_third_section_under_title && $shop_third_section_persian_title && $shop_third_section_english_title) : ?>

            <section class="flex flex-col gap-3 my-14">

                <div class="container flex justify-between items-center">

                    <div class="flex flex-col gap-2 max-md:justify-center max-md:text-center max-md:w-full">
                        <p class="text-3xl md:text-[40px] font-normal"><?php echo esc_html($shop_third_section_title); ?></p>
                        <p class="text-cynBlack/50 text-base md:text-xl font-medium"><?php echo esc_html($shop_third_section_under_title); ?></p>
                    </div>

                    <a href="<?php echo esc_url($shop_third_section_button['url']); ?>" class="primary-btn max-md:hidden"><?php echo esc_html($shop_third_section_button['title']); ?></a>

                </div>

                <div class="relative">

                    <swiper-container class="w-full" slides-per-view="1.25" centered-slides="true" breakpoints='{ "640":  { "slidesPerView": 2.15 }, "768":  { "slidesPerView": 2.15 }, "1024": { "slidesPerView": 3.25 }, "1280": { "slidesPerView": 4, "centeredSlides": false }}' loop="true" autoplay="false" pagination="false" navigation="true" navigation-next-el="#shopThirdSectionProductsNext" navigation-prev-el="#shopThirdSectionProductsPrev">
                        <?php while ($third_section_products_query->have_posts()) : $third_section_products_query->the_post(); ?>
                            <swiper-slide>
                                <?php Templates::getCard('product', ['product' => get_the_ID()]); ?>
                            </swiper-slide>
                        <?php endwhile; ?>
                    </swiper-container>

                    <div class="flex justify-between items-center absolute top-1/2 -translate-y-1/2 left-0 right-0 px-2 pointer-events-none z-10">
                        <button id="shopThirdSectionProductsPrev" class="bg-cynBlack p-3 cursor-pointer rounded-full pointer-events-auto">
                            <i class="text-white size-5">
                                <?php icon::print('Arrow-19') ?>
                            </i>
                        </button>
                        <button id="shopThirdSectionProductsNext" class="bg-cynBlack p-3 cursor-pointer rounded-full pointer-events-auto">
                            <i class="text-white size-5">
                                <?php icon::print('Arrow-27') ?>
                            </i>
                        </button>
                    </div>

                </div>

                <div class="container">
                    <a href="<?php echo esc_url($shop_third_section_button['url']); ?>" class="primary-btn md:hidden w-full flex justify-center items-center"><?php echo esc_html($shop_third_section_button['title']); ?></a>
                </div>

            </section>
        <?php endif; ?>
    <?php endif; ?>

</main>

<?php get_footer(); ?>