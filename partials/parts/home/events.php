<?php

use Cyan\Theme\Helpers\Icon;
use Cyan\Theme\Helpers\Templates;

$home_page_id = get_option('page_on_front');
$home_events_title = get_field('home_events_title', $home_page_id);
$home_events_under_title = get_field('home_events_under_title', $home_page_id);
$home_events_image_desktop = get_field('home_events_image_desktop', $home_page_id);
$home_events_image_mobile = get_field('home_events_image_mobile', $home_page_id);
$home_events_button = get_field('home_events_button', $home_page_id);

if (empty($home_events_title) && empty($home_events_under_title) && empty($home_events_image_desktop) && empty($home_events_image_mobile) && empty($home_events_button)) {
    return;
}
?>

<section class="container flex flex-col max-md:gap-3 my-14 gap-3">
    <div class="flex justify-between items-center">
        <div class="flex flex-col gap-2 max-md:justify-center max-md:text-center max-md:w-full">
            <?php if ($home_events_title) : ?>
                <p class="text-3xl md:text-[40px] font-normal"><?php echo esc_html($home_events_title); ?></p>
            <?php endif; ?>
            <?php if ($home_events_under_title) : ?>
                <p class="text-cynBlack/50 text-base md:text-xl font-medium"><?php echo esc_html($home_events_under_title); ?></p>
            <?php endif; ?>
        </div>

        <?php if (! empty($home_events_button['url'])) : ?>
            <a href="<?php echo esc_url($home_events_button['url']); ?>" class="primary-btn max-md:hidden"><?php echo esc_html($home_events_button['title'] ?? ''); ?></a>
        <?php endif; ?>

    </div>

    <div class="w-full">

        <?php if ($home_events_image_desktop && $home_events_image_mobile) : ?>
            <div class="w-full">
                <?php echo wp_get_attachment_image($home_events_image_desktop, 'full', false, ['class' => 'w-full h-full object-contain hidden md:block']); ?>
                <?php echo wp_get_attachment_image($home_events_image_mobile, 'full', false, ['class' => 'w-full h-full object-contain block md:hidden']); ?>
            </div>
        <?php endif; ?>

    </div>

    <?php Templates::getPart('home/blogs'); ?>

    <?php if (! empty($home_events_button['url'])) : ?>
        <a href="<?php echo esc_url($home_events_button['url']); ?>" class="primary-btn md:hidden w-full flex justify-center items-center"><?php echo esc_html($home_events_button['title'] ?? ''); ?></a>
    <?php endif; ?>
</section>