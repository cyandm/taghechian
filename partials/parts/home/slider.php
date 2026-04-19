<?php

use Cyan\Theme\Helpers\Templates;

$home_slider = new WP_Query([
    'post_type' => 'slider',
    'posts_per_page' => -1,
    'tax_query' => [
        [
            'taxonomy' => 'slider_place',
            'field'    => 'slug',
            'terms'    => 'home',
        ],
    ],
]);

if (!$home_slider->have_posts()) return;
?>

<section class="w-full">
    <div class="relative">

        <swiper-container class="h-[620px] lg:h-[740px]" slides-per-view="1" space-between="0" autoplay="true" direction="vertical" pagination="true" pagination-clickable="true" pagination-el=".swiper-pagination" fade="true">
            <?php
            if ($home_slider->have_posts()) :
                while ($home_slider->have_posts()) :
                    $home_slider->the_post();
            ?>
                    <swiper-slide class="h-full">
                        <?php Templates::getCard('slider', ['post-id' => get_the_ID()]) ?>
                    </swiper-slide>
            <?php
                endwhile;
            endif;
            wp_reset_postdata();
            ?>
        </swiper-container>
        <div class="swiper-pagination [&>.swiper-pagination-bullet]:bg-cynBgItem [&>.swiper-pagination-bullet]:opacity-100 [&>.swiper-pagination-bullet]:size-2 [&>.swiper-pagination-bullet]:rounded-full [&>.swiper-pagination-bullet]:flex [&>.swiper-pagination-bullet.swiper-pagination-bullet-active]:bg-cynBlack [&>.swiper-pagination-bullet]:h-16 [&>.swiper-pagination-bullet]:w-1 absolute bottom-1/2 -translate-y-1/2 left-14 max-sm:hidden z-10 flex flex-col justify-center items-center gap-1.5"></div>
    </div>
</section>