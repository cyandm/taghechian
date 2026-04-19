<?php

use Cyan\Theme\Helpers\Icon;

$home_page_id     = get_option('page_on_front');
$home_product_cats = get_field('home_product_cats', $home_page_id);

if (empty($home_product_cats) || ! is_array($home_product_cats)) {
    return;
}

$grid_classes = [
    'col-span-1 row-span-1 lg:col-span-4 lg:row-span-6 lg:col-start-1 lg:row-start-1',
    'col-span-1 row-span-1 lg:col-span-3 lg:row-span-6 lg:col-start-5 lg:row-start-1',
    'col-span-1 row-span-1 lg:col-span-3 lg:row-span-3 lg:col-start-8 lg:row-start-4',
    'col-span-1 row-span-1 lg:col-span-3 lg:row-span-3 lg:col-start-8 lg:row-start-1',
];
?>

<section class="container my-14">
    <div class="grid grid-cols-2 lg:grid-cols-10 grid-rows-2 lg:grid-rows-6 gap-3 lg:gap-5 max-h-[680px] lg:h-[510px]">
        <?php
        foreach ($home_product_cats as $index => $term_id) :
            $term = get_term((int) $term_id, 'product_cat');
            if (! $term || is_wp_error($term)) {
                continue;
            }
            $term_link    = get_term_link($term);
            $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);
            $image_url    = $thumbnail_id ? wp_get_attachment_image_url((int) $thumbnail_id, 'large') : '';
            $grid_class   = $grid_classes[$index % 4] ?? 'col-span-2 row-span-2';

            if (!$image_url) continue;
        ?>
            <a href="<?php echo esc_url($term_link); ?>" class="<?php echo esc_attr($grid_class); ?> group block relative rounded-3xl overflow-hidden">

                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($term->name); ?>" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" />

                <div class="absolute bottom-0 left-0 right-0 flex items-center justify-between gap-2 p-4">

                    <div class="bg-white/60 backdrop-blur-xs rounded-xl p-2.5 text-cynBlack flex items-center justify-between gap-2 w-full">
                        <span class="text-sm md:text-base font-semibold">
                            <?php echo esc_html($term->name); ?>
                        </span>

                        <i class="size-5 stroke-[1.5]"><?php Icon::print('Arrow-6'); ?></i>
                    </div>

                </div>

            </a>
        <?php endforeach; ?>
    </div>
</section>