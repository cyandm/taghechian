<?php
/* Template Name: Archive Blog */

defined('ABSPATH') || exit;

use Cyan\Theme\Helpers\Templates;
use Cyan\Theme\Helpers\Icon;

$archive_blog_event_title = get_field('archive_blog_event_title');
$archive_blog_event_under_title = get_field('archive_blog_event_under_title');
$archive_blog_event_image = get_field('archive_blog_event_image');

$archive_blog_section_one_title = get_field('archive_blog_section_one_title');
$archive_blog_section_one_under_title = get_field('archive_blog_section_one_under_title');
$archive_blog_section_one_english_title = get_field('archive_blog_section_one_english_title');
$archive_blog_section_one_english_under_title = get_field('archive_blog_section_one_english_under_title');

$archive_blog_section_two_title = get_field('archive_blog_section_two_title');
$archive_blog_section_two_under_title = get_field('archive_blog_section_two_under_title');
$archive_blog_section_two_english_title = get_field('archive_blog_section_two_english_title');
$archive_blog_section_two_english_under_title = get_field('archive_blog_section_two_english_under_title');

$archive_show_title = get_field('archive_show_title');
$archive_show_under_title = get_field('archive_show_under_title');


for ($i = 1; $i <= 4; $i++) {
    $archive_show_images[] = get_field('archive_show_image_' . $i);
}

$archive_blog_section_three_title = get_field('archive_blog_section_three_title');
$archive_blog_section_three_under_title = get_field('archive_blog_section_three_under_title');
$archive_blog_section_three_english_title = get_field('archive_blog_section_three_english_title');
$archive_blog_section_three_english_under_title = get_field('archive_blog_section_three_english_under_title');

// Query for fashion posts section
$fashion_posts_query = new WP_Query([
    'post_type' => 'post',
    'posts_per_page' => 12,
    'post_status' => 'publish',
    'category_name' => 'fashion',
]);

$decor_posts_query = new WP_Query([
    'post_type' => 'post',
    'posts_per_page' => 12,
    'post_status' => 'publish',
    'category_name' => 'decor-and-art',
]);

$educational_posts_query = new WP_Query([
    'post_type' => 'post',
    'posts_per_page' => 12,
    'post_status' => 'publish',
    'category_name' => 'educational',
]);

get_header();
?>

<?php Templates::getPart('breadcrumb'); ?>

<main class="archive-blog">

    <section class="container flex flex-col gap-5">

        <?php if ($archive_blog_event_title && $archive_blog_event_under_title && $archive_blog_event_image): ?>
            <div class="flex flex-col gap-2 max-lg:justify-center max-lg:text-center">
                <p class="text-3xl md:text-[40px] font-normal"><?php echo esc_html($archive_blog_event_title); ?></p>
                <p class="text-cynBlack/50 text-base md:text-xl font-medium"><?php echo esc_html($archive_blog_event_under_title); ?></p>
            </div>
        <?php endif; ?>

        <div>
            <?php echo wp_get_attachment_image($archive_blog_event_image, 'full', false, ['class' => 'w-full h-full object-cover']); ?>
        </div>

    </section>

    <?php if ($fashion_posts_query->have_posts()): ?>
        <section class="flex flex-col gap-5 my-14">

            <?php if ($archive_blog_section_one_title && $archive_blog_section_one_under_title && $archive_blog_section_one_english_title && $archive_blog_section_one_english_under_title): ?>
                <div class="container flex flex-col gap-2 max-lg:justify-center max-lg:text-center">
                    <p class="text-3xl md:text-[40px] font-normal"><?php echo esc_html($archive_blog_section_one_title); ?></p>
                    <p class="text-cynBlack/50 text-base md:text-xl font-medium"><?php echo esc_html($archive_blog_section_one_under_title); ?></p>
                </div>
            <?php endif; ?>


            <div class="relative">
                <swiper-container class="w-full relative" slides-per-view="1.25" centered-slides="true" breakpoints='{ "640":  { "slidesPerView": 2.15 }, "768":  { "slidesPerView": 2.15 }, "1024": { "slidesPerView": 3.25 }, "1280": { "slidesPerView": 4, "centeredSlides": false }}' loop="true" autoplay="true" pagination="false" navigation="true" navigation-next-el="#fashionNext" navigation-prev-el="#fashionPrev">
                    <?php while ($fashion_posts_query->have_posts()) : $fashion_posts_query->the_post(); ?>
                        <swiper-slide>
                            <?php Templates::getCard('blog'); ?>
                        </swiper-slide>
                    <?php endwhile; ?>
                </swiper-container>

                <div class="flex justify-between items-center absolute top-1/2 -translate-y-1/2 left-0 right-0 px-2 pointer-events-none z-10">

                    <button id="fashionPrev" class="bg-cynBlack p-3 cursor-pointer rounded-full pointer-events-auto">
                        <div class="text-white size-5">
                            <?php icon::print('Arrow,-Right') ?>
                        </div>
                    </button>

                    <button id="fashionNext" class="bg-cynBlack p-3 cursor-pointer rounded-full pointer-events-auto">
                        <div class="text-white size-5">
                            <?php icon::print('Left-1') ?>
                        </div>
                    </button>

                </div>
            </div>

        </section>
    <?php endif; ?>

    <?php if ($decor_posts_query->have_posts()): ?>
        <section class="flex flex-col gap-5">

            <?php if ($archive_blog_section_two_title && $archive_blog_section_two_under_title && $archive_blog_section_two_english_title && $archive_blog_section_two_english_under_title): ?>
                <div class="container flex flex-col gap-2 max-lg:justify-center max-lg:text-center">
                    <p class="text-3xl md:text-[40px] font-normal"><?php echo esc_html($archive_blog_section_two_title); ?></p>
                    <p class="text-cynBlack/50 text-base md:text-xl font-medium"><?php echo esc_html($archive_blog_section_two_under_title); ?></p>
                </div>
            <?php endif; ?>

            <div class="relative">
                <swiper-container class="w-full relative" slides-per-view="1.25" centered-slides="true" breakpoints='{ "640":  { "slidesPerView": 2.15 }, "768":  { "slidesPerView": 2.15 }, "1024": { "slidesPerView": 3.25 }, "1280": { "slidesPerView": 4, "centeredSlides": false }}' loop="true" autoplay="true" pagination="false" navigation="true" navigation-next-el="#decorNext" navigation-prev-el="#decorPrev">
                    <?php while ($decor_posts_query->have_posts()) : $decor_posts_query->the_post(); ?>
                        <swiper-slide>
                            <?php Templates::getCard('blog'); ?>
                        </swiper-slide>
                    <?php endwhile; ?>
                </swiper-container>

                <div class="flex justify-between items-center absolute top-1/2 -translate-y-1/2 left-0 right-0 px-2 pointer-events-none z-10">

                    <button id="decorPrev" class="bg-cynBlack p-3 cursor-pointer rounded-full pointer-events-auto">
                        <div class="text-white size-5">
                            <?php icon::print('Arrow,-Right') ?>
                        </div>
                    </button>

                    <button id="decorNext" class="bg-cynBlack p-3 cursor-pointer rounded-full pointer-events-auto">
                        <div class="text-white size-5">
                            <?php icon::print('Left-1') ?>
                        </div>
                    </button>

                </div>

        </section>
    <?php endif; ?>

    <?php if ($archive_show_images): ?>
        <section class="container flex flex-col my-14 gap-5">

            <?php if ($archive_show_title && $archive_show_under_title): ?>
                <div class="flex flex-col gap-2 max-lg:justify-center max-lg:text-center">
                    <p class="text-3xl md:text-[40px] font-normal"><?php echo esc_html($archive_show_title); ?></p>
                    <p class="text-cynBlack/50 text-base md:text-xl font-medium"><?php echo esc_html($archive_show_under_title); ?></p>
                </div>
            <?php endif; ?>

            <?php
            $grid_classes = [
                'sm:col-span-1 sm:row-span-4 sm:col-span-5 sm:row-span-6 min-h-0 overflow-hidden',
                'sm:col-span-3 sm:row-span-3 sm:col-start-6 min-h-0 overflow-hidden',
                'sm:col-span-3 sm:row-span-3 sm:col-start-6 sm:row-start-4 min-h-0 overflow-hidden',
                'sm:col-span-4 sm:row-span-6 sm:col-start-9 sm:row-start-1 min-h-0 overflow-hidden',
            ];
            ?>
            <div class="grid grid-cols-1 grid-rows-4 sm:grid-cols-12 sm:grid-rows-6 gap-4 sm:h-[488px]">
                <?php foreach (array_slice($archive_show_images, 0, 4) as $i => $image): ?>
                    <div class="<?= $grid_classes[$i]; ?>">
                        <a href="<?= esc_url(wp_get_attachment_image_url($image, 'full')); ?>" data-fancybox="archive-show-gallery" class="block w-full h-full cursor-pointer">
                            <?= wp_get_attachment_image($image, 'full', false, [
                                'class' => 'w-full h-[352px] sm:h-full object-cover object-center'
                            ]); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

        </section>
    <?php endif; ?>

    <?php if ($educational_posts_query->have_posts()): ?>
        <section class="flex flex-col gap-5">

            <?php if ($archive_blog_section_three_title && $archive_blog_section_three_under_title && $archive_blog_section_three_english_title && $archive_blog_section_three_english_under_title): ?>
                <div class="container flex flex-col gap-2 max-lg:justify-center max-lg:text-center">
                    <p class="text-3xl md:text-[40px] font-normal"><?php echo esc_html($archive_blog_section_three_title); ?></p>
                    <p class="text-cynBlack/50 text-base md:text-xl font-medium"><?php echo esc_html($archive_blog_section_three_under_title); ?></p>
                </div>
            <?php endif; ?>


            <div class="relative">
                <swiper-container class="w-full relative" slides-per-view="1.25" centered-slides="true" breakpoints='{ "640":  { "slidesPerView": 2.15 }, "768":  { "slidesPerView": 2.15 }, "1024": { "slidesPerView": 3.25 }, "1280": { "slidesPerView": 4, "centeredSlides": false }}' loop="true" autoplay="true" pagination="false" navigation="true" navigation-next-el="#educationalNext" navigation-prev-el="#educationalPrev">
                    <?php while ($educational_posts_query->have_posts()) : $educational_posts_query->the_post(); ?>
                        <swiper-slide>
                            <?php Templates::getCard('blog'); ?>
                        </swiper-slide>
                    <?php endwhile; ?>
                </swiper-container>

                <div class="flex justify-between items-center absolute top-1/2 -translate-y-1/2 left-0 right-0 px-2 pointer-events-none z-10">

                    <button id="educationalPrev" class="bg-cynBlack p-3 cursor-pointer rounded-full pointer-events-auto">
                        <div class="text-white size-5">
                            <?php icon::print('Arrow,-Right') ?>
                        </div>
                    </button>

                    <button id="educationalNext" class="bg-cynBlack p-3 cursor-pointer rounded-full pointer-events-auto">
                        <div class="text-white size-5">
                            <?php icon::print('Left-1') ?>
                        </div>
                    </button>

                </div>
            </div>

        </section>
    <?php endif; ?>

</main>

<?php get_footer(); ?>