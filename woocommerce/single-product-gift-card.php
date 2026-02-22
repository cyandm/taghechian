<?php

/**
 * Single product template for Gift Card category
 * Layout: summary (title, usage, fields, add to cart) | gallery
 *
 * @see https://woocommerce.com/document/template-structure/
 */

if (!defined('ABSPATH')) {
    exit;
}

use Cyan\Theme\Helpers\Templates;
use Cyan\Theme\Helpers\Icon;

global $product;

if (!$product) {
    $product = wc_get_product(get_the_ID());
}

get_header(); ?>

<?php Templates::getPart('breadcrumb'); ?>

<main class="single-product single-product-gift-card container">

    <?php do_action('woocommerce_before_single_product'); ?>

    <section class="flex flex-col md:flex-row gap-8 md:gap-10 lg:gap-14 <?php wc_product_class('', $product); ?>">

        <?php
        $featured_id  = $product->get_image_id();
        $gallery_ids = $product->get_gallery_image_ids();
        $image_ids   = array_filter(array_merge([$featured_id], $gallery_ids ?: []));
        if (empty($image_ids)) {
            $image_ids = [0];
        }
        ?>

        <div class="single-product-gift-card__gallery w-full md:w-1/2 flex flex-col-reverse md:flex-row gap-2">
            <div class="gift-card-gallery__main relative flex-1 overflow-hidden order-2 md:order-2">
                <swiper-container
                    id="gift-card-gallery-main"
                    class="gift-card-gallery-main w-full aspect-[4/3] md:aspect-square"
                    slides-per-view="1"
                    space-between="0"
                    loop="true"
                    thumbs-swiper="#gift-card-gallery-thumbs"
                    navigation="true"
                    navigation-next-el="#giftCardGalleryNext"
                    navigation-prev-el="#giftCardGalleryPrev">
                    <?php foreach ($image_ids as $img_id) : ?>
                        <?php if ($img_id) : ?>
                            <swiper-slide class="!h-full">
                                <div class="w-full h-full flex items-center justify-center bg-[#FCFCFC] overflow-hidden">
                                    <?php echo wp_get_attachment_image($img_id, 'full', false, ['class' => 'w-full h-full object-contain']); ?>
                                </div>
                            </swiper-slide>
                        <?php else : ?>
                            <swiper-slide class="!h-full">
                                <div class="w-full h-full flex items-center justify-center bg-[#FCFCFC]">
                                    <?php echo wc_placeholder_img('woocommerce_single'); ?>
                                </div>
                            </swiper-slide>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </swiper-container>
                <div class="absolute left-1/2 -translate-x-1/2 bottom-3 z-10 flex items-center justify-center gap-3">
                    <button type="button" id="giftCardGalleryPrev" class="size-9 rounded-full bg-cynBlack text-white flex items-center justify-center hover:opacity-90 transition-opacity duration-300" aria-label="<?php echo esc_attr(__('قبلی', 'taghechian')); ?>">
                        <i class="size-5 stroke-[1.5]"><?php Icon::print('Arrow-19'); ?></i>
                    </button>
                    <button type="button" id="giftCardGalleryNext" class="size-9 rounded-full bg-cynBlack text-white flex items-center justify-center hover:opacity-90 transition-opacity duration-300" aria-label="<?php echo esc_attr(__('بعدی', 'taghechian')); ?>">
                        <i class="size-5 stroke-[1.5]"><?php Icon::print('Arrow-27'); ?></i>
                    </button>
                </div>
            </div>
            <div class="gift-card-gallery__thumbs flex-shrink-0 flex md:flex-col gap-2 order-first md:order-1 overflow-hidden">
                <swiper-container
                    id="gift-card-gallery-thumbs"
                    class="gift-card-gallery-thumbs w-full md:w-20 h-20 md:h-auto md:flex-1"
                    slides-per-view="4"
                    space-between="8"
                    direction="horizontal"
                    breakpoints='{"768":{"direction":"vertical","slidesPerView":3,"spaceBetween":8}}'
                    free-mode="true"
                    watch-slides-progress="true"
                    loop="true">
                    <?php foreach ($image_ids as $img_id) : ?>
                        <?php if ($img_id) : ?>
                            <swiper-slide class="cursor-pointer rounded-xl overflow-hidden border-2 border-transparent opacity-60 hover:opacity-100 transition-opacity swiper-slide-thumb-active:!opacity-100 swiper-slide-thumb-active:!border-cynYellow">
                                <div class="aspect-square w-full h-full">
                                    <?php echo wp_get_attachment_image($img_id, 'thumbnail', false, ['class' => 'w-full h-full object-cover']); ?>
                                </div>
                            </swiper-slide>
                        <?php else : ?>
                            <swiper-slide class="cursor-pointer rounded-xl overflow-hidden border-2 border-transparent opacity-60 swiper-slide-thumb-active:!opacity-100 swiper-slide-thumb-active:!border-cynYellow">
                                <div class="aspect-square w-full h-full"><?php echo wc_placeholder_img('thumbnail'); ?></div>
                            </swiper-slide>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </swiper-container>
            </div>
        </div>

        <div class="single-product-gift-card__summary w-full md:w-1/2 flex flex-col gap-6">
            <h1 class="text-3xl text-cynBlack font-[Dinar]">
                <?php the_title(); ?>
            </h1>

            <div class="flex flex-col gap-5 text-cynBlack/80">
                <hr class="border-cynBgItem/30 h-px w-full">
                <div class="flex items-center justify-between">
                    <p class="text-base md:text-xl font-medium text-cynBlack"><?php _e('تاریخ استفاده', 'taghechian'); ?></p>
                    <p class="text-xs md:text-xl font-medium"><?php echo get_field('product_gift_card_expire'); ?></p>
                </div>
                <hr class="border-cynBgItem/30 h-px w-full">
                <div class="flex items-center justify-between">
                    <p class="text-base md:text-xl font-medium text-cynBlack"><?php _e('قابل استفاده', 'taghechian'); ?></p>
                    <p class="text-xs md:text-xl font-medium"><?php echo get_field('product_gift_card_use'); ?></p>
                </div>
                <hr class="border-cynBgItem/30 h-px w-full">
            </div>

            <?php do_action('woocommerce_before_add_to_cart_form'); ?>
            <form class="cart single-product-gift-card__form flex flex-col gap-5" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype="multipart/form-data">
                <?php do_action('woocommerce_before_add_to_cart_button'); ?>

                <div class="flex flex-col gap-5">
                    <label class="flex flex-col gap-1">
                        <span class="text-base md:text-xl font-medium text-cynBlack"><?php _e('نام گیرنده', 'taghechian'); ?></span>
                        <span class="relative flex items-center">
                            <input type="text" name="gift_recipient_name" class="w-full h-12 md:h-14 pl-11 pr-10 border border-cynBorder rounded-2xl text-base font-medium text-cynBlack placeholder:text-cynBlack/50 outline-none transition-all duration-300 focus:border-cynBlack" placeholder="<?php echo esc_attr(__('نام شما', 'taghechian')); ?>" required />
                            <i class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-cynBlack/50 size-6 stroke-[1.5]"><?php Icon::print('User,-Profile-7'); ?></i>
                        </span>
                    </label>

                    <hr class="border-cynBgItem/30 h-px w-full">

                    <label class="flex flex-col gap-1">
                        <span class="text-base md:text-xl font-medium text-cynBlack"><?php _e('جمله ی شخص روی پاکت کارت', 'taghechian'); ?></span>
                        <span class="relative flex items-center">
                            <input type="text" name="gift_card_message" class="w-full h-12 md:h-14 pl-11 pr-10 border border-cynBorder rounded-2xl text-base font-medium text-cynBlack placeholder:text-cynBlack/50 outline-none transition-all duration-300 focus:border-cynBlack" placeholder="<?php echo esc_attr(__('نظر شما', 'taghechian')); ?>" required />
                            <i class="absolute right-3 top-4 pointer-events-none text-cynBlack/50 size-6 stroke-[1.5]"><?php Icon::print('Chat,-Messages-1'); ?></i>
                        </span>
                    </label>
                </div>

                <hr class="border-cynBgItem/30 h-px w-full">

                <div class="flex items-center justify-between gap-3 flex-wrap">

                    <div class=" flex items-center gap-2">

                        <button type="button" id="shareBtn" class="rounded-full border border-cynBorder flex items-center justify-center text-cynBlack hover:border-cynYellow hover:bg-cynYellow transition-all duration-300 share-product p-3" aria-label="<?php echo esc_attr(__('اشتراک', 'taghechian')); ?>">
                            <i class="size-6 stroke-[1.5]"><?php Icon::print('Share-1'); ?></i>
                        </button>



                        <a href="<?php echo esc_url(get_comments_link()); ?>#reviews" class="rounded-full border border-cynBlack/10 flex items-center justify-center text-cynBlack hover:border-cynYellow hover:bg-cynYellow transition-all duration-300 p-3" aria-label="<?php echo esc_attr(__('نظرات', 'taghechian')); ?>">
                            <i class="size-6 stroke-[1.5]"><?php Icon::print('Messages,-Chat-18'); ?></i>
                        </a>

                        <button type="button" class="rounded-full border border-cynBlack/10 flex items-center justify-center text-cynBlack hover:border-cynYellow hover:bg-cynYellow transition-all duration-300 p-3" aria-label="<?php echo esc_attr(__('علاقه‌مندی', 'taghechian')); ?>">
                            <i class="size-6 stroke-[1.5]"><?php Icon::print('Heart'); ?></i>
                        </button>

                    </div>

                    <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="primary-btn flex items-center justify-center gap-2 !py-3 !px-6 text-base font-medium text-cynBlack">
                        <span class=""><?php echo esc_html($product->single_add_to_cart_text()); ?></span>
                        <i class="size-5"><?php Icon::print('Basket,-Shopping-Cart-6'); ?></i>
                    </button>
                </div>
            </form>
        </div>

    </section>

</main>

<?php get_footer();
