<?php

use Cyan\Theme\Helpers\Icon;

$home_page_id = get_option('page_on_front');
$home_banner_image_desktop = get_field('home_banner_image_desktop', $home_page_id);
$home_banner_image_mobile = get_field('home_banner_image_mobile', $home_page_id);
$home_banner_title = get_field('home_banner_title', $home_page_id);
$home_banner_under_title = get_field('home_banner_under_title', $home_page_id);
$home_banner_button = get_field('home_banner_button', $home_page_id);

if (empty($home_banner_image_desktop) || empty($home_banner_image_mobile) || empty($home_banner_title) || empty($home_banner_under_title) || empty($home_banner_button)) {
    return;
}
?>

<section class="container my-14">
    <div class="relative bg-[#EEDAAD] overflow-hidden">
        <div class="w-full z-20 relative pointer-events-none">
            <?php echo wp_get_attachment_image($home_banner_image_desktop, 'full', false, ['class' => 'w-full h-full object-contain hidden md:block']); ?>
            <?php echo wp_get_attachment_image($home_banner_image_mobile, 'full', false, ['class' => 'w-full h-full object-contain block md:hidden']); ?>
        </div>
        <div class="w-full md:w-[50%] absolute top-8 left-0 flex flex-col gap-4 md:gap-6 z-10 max-md:items-center max-md:justify-center">
            <p class="text-3xl xl:text-5xl font-[Dinar] text-cynBlack"><?php echo $home_banner_title; ?></p>
            <p class="text-cynBlack text-base xl:text-2xl font-medium"><?php echo $home_banner_under_title; ?></p>
            <a href="<?php echo $home_banner_button['url']; ?>" class="btn-hover md:mt-5 w-fit !text-base md:!text-xl font-medium flex items-center gap-2 z-10 max-xl:ms-11 max-md:ms-0">
                <?php echo $home_banner_button['title']; ?>
                <i class="size-6 text-cynBlack stroke-[1.5] flex items-center justify-center">
                    <?php Icon::print('Arrow-6') ?>
                </i>
            </a>
        </div>
    </div>
</section>