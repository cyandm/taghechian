<?php
$postId = $args['post-id'] ?? get_the_ID();
$mobile_slider = get_field('mobile_slider', $postId);
$desktop_slider = get_field('desktop_slider', $postId);
?>

<a href="<?php echo get_field('url', $postId) ?>" class="relative">
    <?php
    if ($desktop_slider && $mobile_slider) :
        echo wp_get_attachment_image($mobile_slider, 'full', false, ['class' => 'w-full h-full object-cover object-top md:hidden']);
        echo wp_get_attachment_image($desktop_slider, 'full', false, ['class' => 'w-full h-full object-cover object-top max-md:hidden']);
    else :
        echo 'لطفا تصویر انتخاب نمایید!';
    endif;
    ?>
</a>