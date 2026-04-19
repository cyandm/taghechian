<?php

use Cyan\Theme\Helpers\Icon;

$home_page_id = get_option('page_on_front');

$home_about_title = get_field('home_about_title', $home_page_id);
$home_about_description = get_field('home_about_description', $home_page_id);
$home_about_file = get_field('home_about_file', $home_page_id);
$home_about_cover = get_field('home_about_cover', $home_page_id);
$home_about_button = get_field('home_about_button', $home_page_id);
?>

<section class="container mb-14 mt-[-60px] flex flex-col items-center md:flex-row gap-3 md:gap-8 backdrop-blur-[30px] z-10 relative pt-9">

    <div class="w-full md:w-2/5 lg:w-1/3">
        <?php
        if (! empty($home_about_file)) {
            $file_id   = is_array($home_about_file) ? ($home_about_file['id'] ?? 0) : (int) $home_about_file;
            $mime_type = $file_id ? get_post_mime_type($file_id) : '';
            $is_video  = $mime_type && strpos($mime_type, 'video/') === 0;

            if ($is_video) {
                $video_url = wp_get_attachment_url($file_id);
                $cover_url = ! empty($home_about_cover) ? wp_get_attachment_image_url((int) $home_about_cover, 'full') : '';
                if ($video_url) :
        ?>
                    <a href="<?php echo esc_url($video_url); ?>"
                        data-fancybox="home-about-video"
                        data-type="html5video"
                        data-src="<?php echo esc_url($video_url); ?>"
                        data-format="<?php echo esc_attr($mime_type); ?>"
                        <?php if ($cover_url) : ?>data-poster="<?php echo esc_url($cover_url); ?>" <?php endif; ?>
                        class="relative block w-full aspect-square md:aspect-video bg-black rounded-full md:rounded-[220px] overflow-hidden md:h-[550px] group cursor-pointer">
                        <?php if ($cover_url) : ?>
                            <img src="<?php echo esc_url($cover_url); ?>" alt="" class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-300 group-hover:scale-105" />
                        <?php endif; ?>
                        <span class="absolute inset-0 bg-black/20 flex items-center justify-center">
                            <img src="<?php echo esc_url(THEME_IMAGES_URI . '/play.svg'); ?>" alt="<?php esc_attr_e('پخش', 'taghechian'); ?>" class="w-11 h-11 md:w-16 md:h-16 relative z-10" />
                        </span>
                    </a>
        <?php
                endif;
            } else {
                echo wp_get_attachment_image($file_id, 'full', false, ['class' => 'w-full h-auto rounded-2xl object-cover']);
            }
        }
        ?>
    </div>

    <div class="w-full md:w-2/3 flex flex-col gap-3">
        <p class="text-3xl md:text-[40px] font-[Dinar] pb-1 text-cynBlack">
            <?php echo $home_about_title; ?>
        </p>
        <div class="text-base md:text-xl font-medium leading-8">
            <?php echo $home_about_description; ?>
        </div>

        <?php if ($home_about_button): ?>
            <div class="flex justify-center md:justify-start">
                <a href="<?php echo $home_about_button['url']; ?>" class="btn-hover w-fit flex justify-center items-center gap-3.5 mt-2">
                    <span class="text-xl md:text-2xl font-medium text-cynBlack"><?php echo $home_about_button['title']; ?></span>
                    <i class="size-6 stroke-[1.5] flex items-center justify-center">
                        <?php Icon::print('Arrow-6'); ?>
                    </i>
                </a>
            </div>
        <?php endif; ?>

    </div>

</section>