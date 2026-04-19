<?php

use Cyan\Theme\Helpers\Icon;

$home_page_id = get_option('page_on_front');
$home_show_product_one_title   = get_field('home_show_product_one_title', $home_page_id);
$home_show_product_one_under_title = get_field('home_show_product_one_under_title', $home_page_id);
$home_show_product_one_image = get_field('home_show_product_one_image', $home_page_id);
$home_show_product_one_button = get_field('home_show_product_one_button', $home_page_id);

if (empty($home_show_product_one_title) || empty($home_show_product_one_under_title) || empty($home_show_product_one_image)) {
    return;
}

?>

<section class="container flex flex-col-reverse md:flex-row justify-between items-center gap-4 px-7">

    <div class="w-full md:w-1/2 flex flex-col gap-4 max-md:items-center">
        <p class="text-4xl md:text-5xl font-[Dinar] text-cynBlack"><?php echo $home_show_product_one_title; ?></p>
        <p class="text-cynBlack text-base md:text-2xl font-medium"><?php echo $home_show_product_one_under_title; ?></p>
        <a href="<?php echo $home_show_product_one_button['url']; ?>" class="btn-hover md:mt-5 w-fit !text-base md:!text-xl font-medium flex items-center gap-2">
            <?php echo $home_show_product_one_button['title']; ?>
            <i class="size-6 text-cynBlack stroke-[1.5] flex items-center justify-center">
                <?php Icon::print('Arrow-6') ?>
            </i>
        </a>
    </div>

    <div class="w-full md:w-1/2">
        <?php echo wp_get_attachment_image($home_show_product_one_image, 'full', false, ['class' => 'w-full max-h-[420px] md:max-h-[465px] object-contain']); ?>
    </div>

</section>