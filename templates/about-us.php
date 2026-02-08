<?php
/* Template Name: About-us */

use Cyan\Theme\Helpers\Icon;
use Cyan\Theme\Helpers\Templates;

$aboutUs_file = get_field('aboutUs_file');
$aboutUs_video_cover = get_field('aboutUs_video_cover');
$aboutUs_video_cover_text = get_field('aboutUs_video_cover_text');
$aboutUs_first_section_title = get_field('aboutUs_first_section_title');
$aboutUs_first_section_description = get_field('aboutUs_first_section_description');
$aboutUs_first_section_image = get_field('aboutUs_first_section_image');
$aboutUs_second_section_title = get_field('aboutUs_second_section_title');
$aboutUs_second_section_description = get_field('aboutUs_second_section_description');
$aboutUs_second_section_image = get_field('aboutUs_second_section_image');
$aboutUs_gallery_title = get_field('aboutUs_gallery_title');
$aboutUs_gallery_under_title = get_field('aboutUs_gallery_under_title');

get_header(null, ['header_light' => true]);
?>

<main class="about-us">

    <?php
    $file_url = '';
    $file_type = '';
    $file_id = null;

    if ($aboutUs_file) {
        if (is_array($aboutUs_file)) {
            $file_url = $aboutUs_file['url'] ?? '';
            $file_type = $aboutUs_file['type'] ?? '';
            $file_id = $aboutUs_file['id'] ?? null;
        } elseif (is_numeric($aboutUs_file)) {
            $file_id = (int) $aboutUs_file;
            $file_url = wp_get_attachment_url($file_id);
            $file_type = get_post_mime_type($file_id);
        } else {
            $file_url = (string) $aboutUs_file;
        }
    }

    $is_video = $file_type && strpos($file_type, 'video') !== false;
    $is_image = $file_type && strpos($file_type, 'image') !== false;

    if ($file_url):
        if ($is_image):
    ?>
            <section class="w-full">
                <div class="w-full">
                    <img src="<?php echo esc_url($file_url); ?>" alt="" class="w-full h-auto object-cover" />
                </div>
            </section>
        <?php else:
            $cover_image_url = '';
            if ($aboutUs_video_cover) {
                if (is_array($aboutUs_video_cover)) {
                    $cover_image_url = $aboutUs_video_cover['url'] ?? '';
                } elseif (is_numeric($aboutUs_video_cover)) {
                    $cover_image_url = wp_get_attachment_image_url((int) $aboutUs_video_cover, 'full');
                }
            }
        ?>
            <section class="w-full">
                <div class="relative w-full aspect-video bg-black">
                    <video class="video video-plyr w-full h-full object-contain" playsinline controls data-poster="<?php echo esc_url($cover_image_url); ?>" src="<?php echo esc_url($file_url); ?>"></video>
                    <?php if ($cover_image_url || $aboutUs_video_cover_text): ?>
                        <div class="video-cover absolute inset-0 flex flex-col items-center justify-center gap-2 opacity-100 pointer-events-auto transition-opacity duration-300 cursor-pointer z-10 bg-black/40" aria-hidden="true">
                            <?php if ($cover_image_url): ?>
                                <img src="<?php echo esc_url($cover_image_url); ?>" alt="" class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none" />
                            <?php endif; ?>
                            <img src="<?php echo esc_url(THEME_IMAGES_URI . '/play.svg'); ?>" alt="Play" class="relative z-10 w-11 h-11 md:w-16 md:h-16" />
                            <?php if ($aboutUs_video_cover_text): ?>
                                <span class="relative z-10 text-white text-3xl md:text-[40px] font-normal">
                                    <?php echo esc_html($aboutUs_video_cover_text); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($aboutUs_first_section_image && $aboutUs_first_section_title && $aboutUs_first_section_description): ?>
        <section class="bg-white/30 backdrop-blur-[30px] -mt-6 md:-mt-16 z-30 relative">
            <div class="container pt-14 flex flex-col-reverse md:flex-row items-center gap-3 md:gap-8">
                <div class="w-full md:w-1/2 flex flex-col gap-2 md:gap-5 text-base md:text-xl font-light text-cynBlack/70 leading-9">
                    <p class="text-3xl md:text-[40px] font-normal text-cynBlack">
                        <?php echo $aboutUs_first_section_title; ?>
                    </p>
                    <?php echo $aboutUs_first_section_description; ?>
                </div>
                <div class="w-full md:w-1/2">
                    <?php echo wp_get_attachment_image($aboutUs_first_section_image, 'full', false, ['class' => 'w-full h-[340px] md:h-[720px] lg:h-[520px] object-cover object-center']); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($aboutUs_second_section_image && $aboutUs_second_section_title && $aboutUs_second_section_description): ?>
        <section class="container flex flex-col-reverse md:flex-row-reverse items-center gap-3 md:gap-8 my-14">
            <div class="w-full md:w-1/2 flex flex-col gap-2 md:gap-5 text-base md:text-xl font-light text-cynBlack/70 leading-9">
                <p class="text-3xl md:text-[40px] font-normal text-cynBlack">
                    <?php echo $aboutUs_second_section_title; ?>
                </p>
                <?php echo $aboutUs_second_section_description; ?>
            </div>
            <div class="w-full md:w-1/2">
                <?php echo wp_get_attachment_image($aboutUs_second_section_image, 'full', false, ['class' => 'w-full h-[340px] md:h-[720px] lg:h-[520px] object-cover object-center']); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php
    // Collect gallery images
    $gallery_images = [];
    for ($i = 1; $i <= 30; $i++) {
        $img = get_field('aboutUs_gallery_image_' . $i);
        if ($img) {
            $url = is_array($img) ? ($img['url'] ?? '') : (is_numeric($img) ? wp_get_attachment_url($img) : '');
            if ($url) $gallery_images[] = $url;
        }
    }
    ?>

    <?php if ($aboutUs_gallery_title || $aboutUs_gallery_under_title || !empty($gallery_images)): ?>
        <section class="flex flex-col gap-5">
            <div class="container flex flex-col gap-2 max-lg:justify-center max-lg:text-center">
                <?php if ($aboutUs_gallery_title): ?>
                    <p class="text-3xl md:text-[40px] font-normal"><?php echo esc_html($aboutUs_gallery_title); ?></p>
                <?php endif; ?>
                <?php if ($aboutUs_gallery_under_title): ?>
                    <p class="text-cynBlack/50 text-base md:text-xl font-medium"><?php echo esc_html($aboutUs_gallery_under_title); ?></p>
                <?php endif; ?>
            </div>

            <?php if ($gallery_images): ?>
                <swiper-container class="w-full" slides-per-view="auto" space-between="12" centered-slides="true" loop="true" grab-cursor="true" autoplay="true" autoplay-delay="2000">
                    <?php
                    $i = 0;
                    $total = count($gallery_images);

                    while ($i < $total):
                        $has_next = isset($gallery_images[$i + 1]);
                        $is_stacked = ($i % 3 < 2) && $has_next;

                        if ($is_stacked):
                    ?>
                            <swiper-slide class="!w-[200px] md:!w-[280px]">
                                <div class="flex flex-col gap-3 h-[420px] md:h-[560px]">
                                    <a href="<?php echo esc_url($gallery_images[$i]); ?>" data-fancybox="gallery" class="block flex-1 overflow-hidden">
                                        <img src="<?php echo esc_url($gallery_images[$i]); ?>" alt="" class="w-full h-full object-cover hover:scale-105 transition-all duration-300" loading="lazy" />
                                    </a>
                                    <a href="<?php echo esc_url($gallery_images[$i + 1]); ?>" data-fancybox="gallery" class="block flex-1 overflow-hidden">
                                        <img src="<?php echo esc_url($gallery_images[$i + 1]); ?>" alt="" class="w-full h-full object-cover hover:scale-105 transition-all duration-300" loading="lazy" />
                                    </a>
                                </div>
                            </swiper-slide>
                        <?php $i += 2;
                        else: ?>
                            <swiper-slide class="!w-[280px] md:!w-[380px]">
                                <a href="<?php echo esc_url($gallery_images[$i]); ?>" data-fancybox="gallery" class="block h-[420px] md:h-[560px] overflow-hidden">
                                    <img src="<?php echo esc_url($gallery_images[$i]); ?>" alt="" class="w-full h-full object-cover hover:scale-105 transition-all duration-300" loading="lazy" />
                                </a>
                            </swiper-slide>
                    <?php $i++;
                        endif;
                    endwhile; ?>
                </swiper-container>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <?php Templates::getPart('faq', ['faq_place' => 'about-us-page']); ?>

</main>


<?php get_footer(); ?>