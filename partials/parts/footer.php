<?php

use Cyan\Theme\Helpers\Templates;
use Cyan\Theme\Helpers\Icon;

$enamad_code = get_option('enamad_code');
$samandehi_code = get_option('samandehi_code');
$developer_code = get_option('developer_code');
$address_text = get_option('address_text');
$address_link = get_option('address_link');
$phone_number = get_option('phone_number');
$phone_number_support = get_option('phone_number_support');
$telegram_link = get_option('telegram_link');
$telegram_text = get_option('telegram_text');
$instagram_link = get_option('instagram_link');
$instagram_text = get_option('instagram_text');
$whatsapp_number = get_option('whatsapp_number');
$twitter_link = get_option('twitter_link');
$twitter_text = get_option('twitter_text');
$linkedin_link = get_option('linkedin_link');
$linkedin_text = get_option('linkedin_text');
$pinterest_link = get_option('pinterest_link');
$pinterest_text = get_option('pinterest_text');
$email_address = get_option('email_address');
$logo_footer = get_option('logo_footer');

$catalog_files = [];
for ($i = 1; $i <= 2; $i++) {
    $catalog_files[] =
        [
            'file' => get_option('catalog_file_' . $i),
            'name' => get_option('catalog_file_name_' . $i)
        ];
}

$branches = get_posts([
    'post_type' => 'branch',
    'posts_per_page' => -1,
]);

$instagram_accounts = [];
for ($i = 1; $i <= 4; $i++) {
    $instagram_accounts[] =
        [
            'text' => get_option('instagram_text_' . $i),
            'link' => get_option('instagram_link_' . $i)
        ];
}
?>

<section class="footer_logo -mb-10 flex justify-center items-center">
    <?php if ($logo_footer) : ?>
        <img src="<?php echo $logo_footer ?>" alt="logo" class="size-20 object-contain">
    <?php else : ?>
        <?php the_custom_logo() ?>
    <?php endif; ?>
</section>

<section class="bg-[#D2D2D224] px-5 md:px-16 pt-14 md:pt-20 pb-60 md:pb-20 rounded-4xl bg-contain bg-left-bottom bg-no-repeat" style="background-image: url('<?php echo get_template_directory_uri() ?>/assets/image/signature.svg'); background-size: 180px;">

    <div class="flex gap-10 max-lg:flex-wrap overflow-hidden">

        <div class="lg:flex-1 max-lg:w-[calc(50%-2rem)] max-sm:w-full">

            <?php wp_nav_menu([
                'menu_id' => '',
                'menu_class' => "text-cynBlack text-xs font-medium flex flex-col gap-3 [&>li]:w-fit [&>li]:flex [&>li]:items-center [&>li]:before:content-['●'] [&>li]:before:text-[#D9D9D9] [&>li]:before:text-sm [&>li]:before:leading-none [&>li]:before:-me-3 [&_li]:whitespace-nowrap [&>li>a]:ps-4 [&>li]:hover:before:text-cynYellow [&>li]:before:transition-all [&>li]:before:duration-300",
                'depth' => '',
                'theme_location' => 'footer-menu',
                'container' => 'ul'
            ]); ?>

        </div>

        <div class="lg:flex-1 max-lg:w-[calc(50%-2rem)] max-sm:w-full">

            <?php wp_nav_menu([
                'menu_id' => '',
                'menu_class' => "text-cynBlack text-xs font-medium flex flex-col gap-3 [&>li]:w-fit [&>li]:flex [&>li]:items-center [&>li]:before:content-['●'] [&>li]:before:text-[#D9D9D9] [&>li]:before:text-sm [&>li]:before:leading-none [&>li]:before:-me-3 [&_li]:whitespace-nowrap [&>li]:hover:before:text-cynYellow [&>li]:before:transition-all [&>li]:before:duration-300 [&>li>a]:ps-4",
                'depth' => '',
                'theme_location' => 'footer-quick-menu',
                'container' => 'ul'
            ]); ?>

        </div>

        <?php if ($phone_number || $email_address || $catalog_files) : ?>
            <div class="lg:flex-1 max-lg:w-[calc(50%-2rem)] max-sm:w-full flex flex-col gap-4">

                <?php if ($phone_number) : ?>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-semibold text-cynBlack whitespace-nowrap"><?php _e('شماره تماس', 'jonubgard'); ?></span>
                        <a href="tel:<?php echo $phone_number ?>" class="text-cynBlue text-xs font-semibold"><?php echo $phone_number ?></a>
                    </div>
                <?php endif; ?>

                <?php if ($email_address) : ?>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-semibold text-cynBlack whitespace-nowrap"><?php _e('ایمیل', 'jonubgard'); ?></span>
                        <a href="mailto:<?php echo $email_address ?>" class="text-cynBlue text-xs font-semibold"><?php echo $email_address ?></a>
                    </div>
                <?php endif; ?>

                <?php if ($catalog_files) : ?>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-semibold text-cynBlack whitespace-nowrap"><?php _e('دانلود کاتالوگ ها', 'jonubgard'); ?></span>
                        <?php foreach ($catalog_files as $catalog_file) : ?>
                            <a href="<?php echo $catalog_file['file'] ?>" target="_blank" class="text-cynBlue text-xs font-semibold flex items-center">
                                <i class="size-5 flex items-center justify-center text-cynBlue">
                                    <?php Icon::print('download-arrow') ?>
                                </i>
                                <?php echo $catalog_file['name']; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        <?php endif; ?>

        <?php if ($branches) : ?>
            <div class="lg:flex-2 max-lg:w-[calc(50%-2rem)] max-sm:w-full">

                <div class="flex flex-col gap-1.5">
                    <span class="text-xs font-semibold text-cynBlack whitespace-nowrap"><?php _e('اطلاعات شعب', 'jonubgard'); ?></span>

                    <ul class="flex flex-col gap-4">

                        <?php foreach ($branches as $branch) : ?>
                            <li class="flex items-center justify-between bg-cynWhite py-0.5 px-1 rounded-xl cursor-pointer whitespace-nowrap" modal-opener data-modal-name="branch-modal-<?php echo $branch->ID ?>">
                                <span class="text-cynBlack text-xs font-medium"><?php echo get_the_title($branch->ID) ?></span>
                                <i class="size-5 [&_svg]:stroke-[1.5] flex items-center justify-center text-cynBlue">
                                    <?php Icon::print('Arrow-12') ?>
                                </i>
                            </li>
                        <?php endforeach; ?>

                    </ul>

                    <?php foreach ($branches as $branch) : ?>

                        <?php $branch_phone = get_field('branch_phone', $branch->ID); ?>
                        <?php $branch_address = get_field('branch_address', $branch->ID); ?>
                        <?php $branch_loc_link_balad = get_field('branch_loc_link_balad', $branch->ID); ?>
                        <?php $branch_loc_link_neshan = get_field('branch_loc_link_neshan', $branch->ID); ?>
                        <?php $branch_loc_link_snapp = get_field('branch_loc_link_snapp', $branch->ID); ?>
                        <?php $branch_loc_link_tapsi = get_field('branch_loc_link_tapsi', $branch->ID); ?>
                        <?php $branch_loc_link_google = get_field('branch_loc_link_google', $branch->ID); ?>
                        <?php $branch_loc_link_waze = get_field('branch_loc_link_waze', $branch->ID); ?>

                        <div class="container flex justify-center items-center h-fit top-1/2 -translate-y-1/2 fixed inset-0 z-50 opacity-0 pointer-events-none w-full md:!w-4/10 data-[active='true']:opacity-100 data-[active='true']:pointer-events-auto duration-500" modal data-modal-name="branch-modal-<?php echo $branch->ID ?>" data-active="false">

                            <div class="w-full px-6 pb-6 pt-8 bg-cynWhite rounded-3xl shadow-item flex flex-col gap-6 justify-center items-center relative border border-cynBlack/10">

                                <div class="absolute top-3 right-3 w-fit cursor-pointer flex items-center" modal-closer data-modal-name="branch-modal-<?php echo $branch->ID ?>">
                                    <i class="size-8 text-cynBlack"><?php Icon::print('Delete,-Disabled'); ?></i>
                                    <span class="text-xs font-semibold pb-0.5"><?php _e('بستن', 'taghechian'); ?></span>
                                </div>

                                <div class="flex flex-col gap-4 text-cynBlack w-full">

                                    <p class="text-2xl font-normal text-center"><?php echo get_the_title($branch->ID) ?></p>

                                    <?php if ($branch_address) : ?>
                                        <p class="text-base font-light"><?php echo $branch_address ?></p>
                                    <?php endif; ?>

                                    <?php if ($branch_phone) : ?>
                                        <div class="flex flex-col gap-1">
                                            <span class="text-xs font-semibold text-cynBlack whitespace-nowrap"><?php _e('شماره تماس', 'jonubgard'); ?></span>
                                            <a href="tel:<?php echo $branch_phone ?>" class="text-cynBlue text-xs font-semibold"><?php echo $branch_phone ?></a>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($branch_loc_link_balad) : ?>
                                        <div class="flex flex-col gap-1">
                                            <span class="text-xs font-semibold text-cynBlack whitespace-nowrap"><?php _e('مکان یاب بلد', 'jonubgard'); ?></span>
                                            <a href="<?php echo $branch_loc_link_balad ?>" class="text-cynBlue text-xs font-semibold"><?php echo $branch_loc_link_balad ?></a>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($branch_loc_link_balad || $branch_loc_link_neshan || $branch_loc_link_snapp || $branch_loc_link_tapsi || $branch_loc_link_google || $branch_loc_link_waze) : ?>
                                        <div class="flex flex-col gap-3">
                                            <span class="text-xs font-semibold text-cynBlack whitespace-nowrap"><?php _e('آدرس روی نقشه', 'jonubgard'); ?></span>

                                            <div class="flex items-center gap-2.5 flex-wrap">

                                                <?php if ($branch_loc_link_balad) : ?>
                                                    <a href="<?php echo $branch_loc_link_balad ?>">
                                                        <i class="size-9 flex items-center justify-center text-cynBlack rounded-[10px]">
                                                            <img src="<?php echo get_template_directory_uri() ?>/assets/image/balad.svg" alt="balad" class="size-full object-contain">
                                                        </i>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if ($branch_loc_link_neshan) : ?>
                                                    <a href="<?php echo $branch_loc_link_neshan ?>">
                                                        <i class="size-9 flex items-center justify-center text-cynBlack rounded-[10px]">
                                                            <img src="<?php echo get_template_directory_uri() ?>/assets/image/neshan.svg" alt="neshan" class="size-full object-contain">
                                                        </i>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if ($branch_loc_link_snapp) : ?>
                                                    <a href="<?php echo $branch_loc_link_snapp ?>">
                                                        <i class="size-9 flex items-center justify-center text-cynBlack rounded-[10px]">
                                                            <img src="<?php echo get_template_directory_uri() ?>/assets/image/snapp.svg" alt="snapp" class="size-full object-contain">
                                                        </i>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if ($branch_loc_link_tapsi) : ?>
                                                    <a href="<?php echo $branch_loc_link_tapsi ?>">
                                                        <i class="size-9 flex items-center justify-center text-cynBlack rounded-[10px]">
                                                            <img src="<?php echo get_template_directory_uri() ?>/assets/image/tapsi.svg" alt="tapsi" class="size-full object-contain">
                                                        </i>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if ($branch_loc_link_google) : ?>
                                                    <a href="<?php echo $branch_loc_link_google ?>">
                                                        <i class="size-9 flex items-center justify-center text-cynBlack rounded-[10px]">
                                                            <img src="<?php echo get_template_directory_uri() ?>/assets/image/maps.svg" alt="google maps" class="size-full object-contain">
                                                        </i>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if ($branch_loc_link_waze) : ?>
                                                    <a href="<?php echo $branch_loc_link_waze ?>">
                                                        <i class="size-9 flex items-center justify-center text-cynBlack rounded-[10px]">
                                                            <img src="<?php echo get_template_directory_uri() ?>/assets/image/waze.svg" alt="waze" class="size-full object-contain">
                                                        </i>
                                                    </a>
                                                <?php endif; ?>

                                            </div>
                                        </div>
                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>
                    <?php endforeach; ?>

                </div>

            </div>
        <?php endif; ?>

        <?php if ($telegram_text || $linkedin_text || $whatsapp_number || $pinterest_text || $instagram_accounts) : ?>
            <div class="lg:flex-2 max-lg:w-[calc(50%-2rem)] max-sm:w-full">

                <div class="flex flex-col gap-1.5">

                    <span class="text-xs font-semibold text-cynBlack whitespace-nowrap"><?php _e('شبکه های اجتماعی', 'jonubgard'); ?></span>

                    <ul class="flex flex-col gap-3">

                        <?php if ($telegram_text && $telegram_link) : ?>
                            <li class="w-full">
                                <a href="<?php echo $telegram_link ?>" class="flex items-center gap-1 bg-cynWhite px-1 py-0.5 rounded-xl cursor-pointer">
                                    <i class="size-6 flex items-center justify-center text-cynBlack">
                                        <?php Icon::print('Telegram') ?>
                                    </i>
                                    <span class="text-cynBlack text-xs font-medium"><?php echo $telegram_text ?></span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if ($instagram_accounts) : ?>
                            <li class="w-full">
                                <a class="flex items-center justify-between bg-cynWhite px-1 py-0.5 rounded-xl cursor-pointer" modal-opener data-modal-name="instagram-modal">
                                    <div class="flex items-center gap-1">
                                        <i class="size-6 flex items-center justify-center text-cynBlack">
                                            <?php Icon::print('Instagram') ?>
                                        </i>
                                        <span class="text-cynBlack text-xs font-medium"><?php _e('اینستاگرام ها', 'jonubgard'); ?></span>
                                    </div>
                                    <i class="size-5 [&_svg]:stroke-[1.5] flex items-center justify-center text-cynBlack">
                                        <?php Icon::print('Arrow-12') ?>
                                    </i>
                                </a>


                                <div class="container flex justify-center items-center h-fit top-1/2 -translate-y-1/2 fixed inset-0 z-50 opacity-0 pointer-events-none w-full md:!w-4/10 data-[active='true']:opacity-100 data-[active='true']:pointer-events-auto duration-500" modal data-modal-name="instagram-modal" data-active="false">

                                    <div class="w-full px-6 pb-6 pt-8 bg-cynWhite rounded-3xl shadow-item flex flex-col gap-6 justify-center items-center relative border border-cynBlack/10">

                                        <div class="absolute top-3 right-3 w-fit cursor-pointer flex items-center" modal-closer data-modal-name="instagram-modal">
                                            <i class="size-8 text-cynBlack"><?php Icon::print('Delete,-Disabled'); ?></i>
                                            <span class="text-xs font-semibold pb-0.5"><?php _e('بستن', 'taghechian'); ?></span>
                                        </div>

                                        <div class="flex flex-col gap-4 text-cynBlack w-full">

                                            <p class="text-2xl font-normal text-center"><?php _e('اینستاگرام ها', 'jonubgard') ?></p>

                                            <?php foreach ($instagram_accounts as $instagram_account) : ?>

                                                <a href="<?php echo $instagram_account["link"] ?>" class="text-cynBlack text-xs font-medium whitespace-nowrap flex items-center gap-1 px-1 py-2 bg-cynBgItem/30 rounded-xl">
                                                    <i class="size-6 flex items-center justify-center text-cynBlack"><?php Icon::print('Instagram') ?></i>
                                                    <?php echo $instagram_account["text"] ?>
                                                </a>

                                            <?php endforeach; ?>

                                        </div>

                                    </div>

                                </div>

                            </li>
                        <?php endif; ?>

                        <?php if ($linkedin_text && $linkedin_link) : ?>
                            <li class="w-full">
                                <a href="<?php echo $linkedin_link ?>" class="flex items-center gap-1 bg-cynWhite px-1 py-0.5 rounded-xl cursor-pointer">
                                    <i class="size-6 flex items-center justify-center text-cynBlack">
                                        <?php Icon::print('Linkedin') ?>
                                    </i>
                                    <span class="text-cynBlack text-xs font-medium"><?php echo $linkedin_text ?></span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if ($whatsapp_number) : ?>
                            <li class="w-full">
                                <a href="http://wa.me/<?php echo $whatsapp_number ?>" class="flex items-center gap-1 bg-cynWhite px-1 py-0.5 rounded-xl cursor-pointer">
                                    <i class="size-6 flex items-center justify-center text-cynBlack">
                                        <?php Icon::print('Whatsup') ?>
                                    </i>
                                    <span class="text-cynBlack text-xs font-medium"><?php echo $whatsapp_number ?></span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if ($pinterest_text && $pinterest_link) : ?>
                            <li class="w-full">
                                <a href="<?php echo $pinterest_link ?>" class="flex items-center gap-1 bg-cynWhite px-1 py-0.5 rounded-xl cursor-pointer">
                                    <i class="size-6 flex items-center justify-center text-cynBlack">
                                        <?php Icon::print('Pinterest') ?>
                                    </i>
                                    <span class="text-cynBlack text-xs font-medium"><?php echo $pinterest_text ?></span>
                                </a>
                            </li>
                        <?php endif; ?>

                    </ul>

                </div>

            </div>
        <?php endif; ?>

        <?php if ($enamad_code || $samandehi_code) : ?>
            <div class="lg:flex-2 max-lg:w-[calc(50%-2rem)] max-sm:w-full">

                <div class="flex flex-wrap gap-5 max-lg:items-center max-lg:justify-center">
                    <?php if ($enamad_code) : ?>
                        <div class="flex items-center justify-center w-[calc(50%-10px)]">
                            <div class="size-[70px]">
                                <?php echo $enamad_code; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($samandehi_code) : ?>
                        <div class="flex items-center justify-center w-[calc(50%-10px)]">
                            <div class="size-[70px]">
                                <?php echo $samandehi_code; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($samandehi_code) : ?>
                        <div class="flex items-center justify-center w-[calc(50%-10px)]">
                            <div class="size-[70px]">
                                <?php echo $samandehi_code; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>

            </div>
        <?php endif; ?>

</section>

<section class="container text-cynBlack text-base font-medium text-center mt-3">
    <p class="whitespace-nowrap [&>a]:text-cynBlue"><?php echo $developer_code; ?></p>
</section>

<?php Templates::getPart('navbar-mobile'); ?>