<?php

$home_page_id = get_option('page_on_front');
$home_double_banner_left_image = get_field('home_double_banner_left_image', $home_page_id);
$home_double_banner_left_link = get_field('home_double_banner_left_link', $home_page_id);
$home_double_banner_right_image = get_field('home_double_banner_right_image', $home_page_id);
$home_double_banner_right_link = get_field('home_double_banner_right_link', $home_page_id);

if (empty($home_double_banner_left_image) && empty($home_double_banner_right_image)) {
    return;
}
?>

<section class="container flex flex-col md:flex-row items-center gap-3 md:gap-5 my-14">
    <a href="<?php echo $home_double_banner_left_link; ?>" class="w-full md:w-1/2">
        <?php echo wp_get_attachment_image($home_double_banner_left_image, 'full', false, ['class' => 'w-full h-full object-contain']); ?>
    </a>
    <a href="<?php echo $home_double_banner_right_link; ?>" class="w-full md:w-1/2">
        <?php echo wp_get_attachment_image($home_double_banner_right_image, 'full', false, ['class' => 'w-full h-full object-contain']); ?>
    </a>
</section>