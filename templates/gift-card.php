<?php
/* Template Name: Gift Card */

use Cyan\Theme\Helpers\Icon;
use Cyan\Theme\Helpers\Templates;

$gift_card_title = get_field('gift_card_title');
$gift_card_desc = get_field('gift_card_desc');
$gift_card_button = get_field('gift_card_button');
$gift_card_image = get_field('gift_card_image');
$gift_card_show_title = get_field('gift_card_show_title');
$gift_card_show_under_title = get_field('gift_card_show_under_title');
$gift_card_show_post = get_field('gift_card_show_post');
$gift_card_box_title = get_field('gift_card_box_title');
$gift_card_box_desc = get_field('gift_card_box_desc');
$gift_card_box_image = get_field('gift_card_box_image');

if (!empty($gift_card_show_post)) {
    $post_ids = is_array($gift_card_show_post) ? $gift_card_show_post : [$gift_card_show_post];
    $gift_cards = get_posts([
        'post_type'      => 'product',
        'post__in'       => $post_ids,
        'posts_per_page' => -1,
        'orderby'        => 'post__in',
    ]);
} else {
    $gift_cards = get_posts([
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'tax_query'      => [
            [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => 'gift-card',
            ],
        ],
    ]);
}

$gift_cards = array_map(function ($post) {
    return [
        'id'    => $post->ID,
        'title' => $post->post_title,
        'image' => get_the_post_thumbnail_url($post->ID, 'full'),
    ];
}, $gift_cards);

get_header();
?>

<?php Templates::getPart('breadcrumb'); ?>

<main class="gift-card">

    <?php if ($gift_card_title && $gift_card_desc && $gift_card_button && $gift_card_image): ?>
        <section class="flex flex-col-reverse md:flex-row items-center md:gap-2 container">

            <div class="flex flex-col w-full max-md:items-center md:w-[45%] xl:w-2/5 [&_p]:max-md:text-center [&_p]:lg:text-[28px] [&_p]:text-base [&_p]:font-medium [&_p]:text-cynBlack">
                <h1 class="text-[40px] lg:text-7xl text-cynBlack font-[Dinar] leading-20 md:leading-24 md:mb-3"><?php echo $gift_card_title; ?></h1>
                <?php echo $gift_card_desc; ?>
                <a href="<?php echo $gift_card_button['url']; ?>" class="primary-btn mt-3 md:mt-5 w-fit"><?php echo $gift_card_button['title']; ?></a>
            </div>

            <div class="relative w-full md:w-[55%] xl:w-3/5 max-md:items-center">
                <?php echo wp_get_attachment_image($gift_card_image, 'full', false, ['class' => 'w-full h-full object-cover']); ?>
            </div>

        </section>
    <?php endif; ?>

    <?php if ($gift_card_show_title && $gift_card_show_under_title): ?>
        <section class="my-14 flex flex-col gap-3 md:gap-5">

            <div class="flex flex-col gap-2 max-lg:justify-center max-lg:text-center container">
                <p class="text-3xl font-[Dinar]">
                    <?php echo $gift_card_show_title ? $gift_card_show_title : __('کارت های هدیه', 'taghechian'); ?>
                </p>

                <p class="text-cynBlack/50 text-base md:text-xl font-medium">
                    <?php echo $gift_card_show_under_title ? $gift_card_show_under_title : __('برای کسی که دوست داری', 'taghechian'); ?>
                </p>
            </div>

            <div class="container relative">
                <swiper-container slides-per-view="1.25" space-between="12" breakpoints='{ "640":  { "slidesPerView": 2.15 }, "768":  { "slidesPerView": 2.15 }, "1024": { "slidesPerView": 3.25 }, "1280": { "slidesPerView": 4, "centeredSlides": false }}'>
                    <?php foreach ($gift_cards as $gift_card): ?>
                        <swiper-slide>
                            <a href="<?php echo get_the_permalink($gift_card['id']); ?>" class="flex flex-col gap-2 rounded-3xl overflow-hidden relative">
                                <?php echo get_the_post_thumbnail($gift_card['id'], 'full', ['class' => 'w-full h-full object-contain']); ?>
                                <div class="absolute bottom-3 left-3 flex items-center gap-1">
                                    <p class="text-white text-xs font-medium"><?php _e('مشاهده جزییات', 'taghechian'); ?></p>
                                    <i class="text-white text-xs size-6"><?php icon::print('Arrow-6') ?></i>
                                </div>
                            </a>
                        </swiper-slide>
                    <?php endforeach; ?>
                </swiper-container>
            </div>

        </section>
    <?php endif; ?>

    <?php if ($gift_card_box_image && $gift_card_box_title && $gift_card_box_desc): ?>
        <section class="container flex flex-col-reverse md:flex-row-reverse items-center gap-3 md:gap-8 my-14">
            <div class="w-full md:w-1/2 flex flex-col gap-3 text-base md:text-xl font-light text-cynBlack/70 leading-9 max-md:text-center">
                <p class="text-3xl md:text-[40px] font-[Dinar] text-cynBlack">
                    <?php echo $gift_card_box_title; ?>
                </p>

                <div class="w-full md:w-1/2 md:hidden">
                    <?php echo wp_get_attachment_image($gift_card_box_image, 'full', false, ['class' => 'w-full h-full md:h-[632px] object-cover object-center']); ?>
                </div>

                <?php echo $gift_card_box_desc; ?>
            </div>
            <div class="w-full md:w-1/2 max-md:hidden">
                <?php echo wp_get_attachment_image($gift_card_box_image, 'full', false, ['class' => 'w-full h-[340px] md:h-[632px] object-cover object-center']); ?>
            </div>
        </section>
    <?php endif; ?>

</main>

<?php get_footer(); ?>