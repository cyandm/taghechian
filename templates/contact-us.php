<?php
/* Template Name: contact-us */

use Cyan\Theme\Helpers\Icon;
use Cyan\Theme\Helpers\Templates;

$contact_form_title = get_field('contact_form_title');
$contact_form_under_title = get_field('contact_form_under_title');
$contact_branches_title = get_field('contact_branches_title');
$contact_branches_under_title = get_field('contact_branches_under_title');

$branches = get_posts([
    'post_type' => 'branch',
    'posts_per_page' => -1,
]);

get_header();
?>

<?php Templates::getPart('breadcrumb'); ?>

<main class="container">

    <section class="flex max-lg:flex-col-reverse">

        <div class="w-full lg:w-5/9 flex flex-col gap-5 justify-center">

            <div class="flex flex-col gap-2 max-lg:justify-center max-lg:text-center">
                <p class="text-3xl md:text-[40px] font-normal">
                    <?php echo $contact_title ? $contact_title : __('ارتباط با طاقه‌چیان', 'taghechian'); ?>
                </p>

                <p class="text-cynBlack/50 text-base md:text-xl font-medium">
                    <?php echo $under_title ? $under_title : __('چیزی هست که بخوای به ما بگی', 'taghechian'); ?>
                </p>
            </div>

            <form hx-post="<?php echo rest_url('cyn/v1/contact_form') ?>" hx-target=".result" hx-swap="innerHTML" hx-on::after-request="(function(ev){ var resultEl = document.querySelector('.result'); resultEl.style.display = 'block'; resultEl.style.opacity = '1'; if(ev.detail.successful){ resultEl.textContent = '<?php echo esc_js(__('با موفقیت ارسال شد', 'jonubgard')); ?>'; resultEl.style.background = ''; var f = document.getElementById('contact_form'); if(f) f.reset(); } else { try { var d = JSON.parse(ev.detail.xhr.responseText); resultEl.textContent = d.error || '<?php echo esc_js(__('خطا در ارسال', 'taghechian')); ?>'; } catch(e){ resultEl.textContent = '<?php echo esc_js(__('خطا در ارسال', 'taghechian')); ?>'; } resultEl.style.background = '#ef4444'; } setTimeout(function(){ resultEl.style.transition = 'opacity 0.5s ease-out'; resultEl.style.opacity = '0'; setTimeout(function(){ resultEl.style.display = 'none'; }, 500); }, 5000); })(event)" action="" method="post" id="contact_form" class="flex flex-col gap-4 w-5/6 max-lg:w-full [&_label]:w-full [&_input]:w-full [&_textarea]:w-full">

                <label for="fullname" class="relative">
                    <div class="size-6 text-cynBlack/60 absolute start-3 top-1/2 -translate-y-1/2">
                        <?php icon::print('User,-Profile-7'); ?>
                    </div>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="<?php _e('نام شما'); ?>"
                        class="focus:outline-none w-full font-light text-xl ps-10 pe-5 py-3 bg-white rounded-2xl border border-cynBlack/20" />
                </label>

                <label for="email" class="relative">
                    <div class="size-6 text-cynBlack/60 absolute start-3 top-1/2 -translate-y-1/2">
                        <?php icon::print('email-mail-letter'); ?>
                    </div>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="<?php _e('ایمیل شما'); ?>"
                        class="focus:outline-none w-full font-light text-xl ps-10 pe-5 py-3 bg-white rounded-2xl border border-cynBlack/20" />
                </label>

                <label for="phone" class="relative">
                    <div class="size-6 text-cynBlack/60 absolute start-3 top-1/2 -translate-y-1/2">
                        <?php icon::print('Phone,-Call-11'); ?>
                    </div>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        placeholder="<?php _e('شماره تماس'); ?>"
                        pattern="[0-9]{11}"
                        required
                        dir="rtl"
                        class="focus:outline-none w-full font-light text-xl ps-10 pe-5 py-3 bg-white rounded-2xl border border-cynBlack/20" />

                </label>

                <label for="message" class="relative">
                    <div class="size-6 text-cynBlack/60 absolute start-3 top-3">
                        <?php icon::print('Chat,-Messages-1'); ?>
                    </div>
                    <textarea name="message" id="message" rows="3" maxlength="65525" placeholder="<?php _e('پیام شما'); ?>" required class="focus:outline-none w-full text-cynBlack font-light text-xl resize-y m-0 align-bottom ps-10 pe-5 py-3 bg-white rounded-2xl border border-cynBlack/20"></textarea>
                </label>

                <div class="flex justify-end ">
                    <button type="submit" class="primary-btn text-white flex justify-center items-center gap-1 !text-xl !font-semibold max-md:w-full">
                        <span class="size-6 stroke-[1.5]">
                            <?php icon::print('Send') ?>
                        </span>
                        <span class="pt-1">
                            <?php _e('ارسال پیام'); ?>
                        </span>
                    </button>
                </div>

            </form>

            <div class="result bg-green-500 text-white text-xl font-semibold rounded-2xl p-3 shadow-item fixed top-4 right-4 left-4 z-50 pt-4" style="display:none; opacity: 0; transition: opacity 0.5s ease-out;"></div>

        </div>

        <div class="flex justify-center items-center lg:w-4/9">
            <?php the_post_thumbnail('full', ['class' => 'w-full h-full object-contain sm:w-2/3 lg:w-full']) ?>
        </div>

    </section>


    <?php if ($branches) : ?>
        <section class="mt-14 flex flex-col gap-5">

            <div class="flex flex-col gap-2 max-lg:justify-center max-lg:text-center w-full">
                <p class="text-3xl md:text-[40px] font-normal">
                    <?php echo $contact_branches_title ? $contact_branches_title : __('شعبه‌های طاقه‌چیان', 'taghechian'); ?>
                </p>

                <p class="text-cynBlack/50 text-base md:text-xl font-medium">
                    <?php echo $contact_branches_under_title ? $contact_branches_under_title : __('مارو کجاها میتونید پیدا کنید', 'taghechian'); ?>
                </p>
            </div>

            <div class="w-full flex flex-col gap-8">

                <?php foreach ($branches as $branch) : ?>
                    <?php $branch_title = get_the_title($branch->ID); ?>
                    <?php $branch_thumbnail = get_the_post_thumbnail($branch->ID, 'full', ['class' => 'w-full h-full object-cover object-center rounded-2xl']); ?>
                    <?php $branch_phone = get_field('branch_phone', $branch->ID); ?>
                    <?php $branch_address = get_field('branch_address', $branch->ID); ?>
                    <?php $branch_loc_link_balad = get_field('branch_loc_link_balad', $branch->ID); ?>
                    <?php $branch_loc_link_neshan = get_field('branch_loc_link_neshan', $branch->ID); ?>
                    <?php $branch_loc_link_snapp = get_field('branch_loc_link_snapp', $branch->ID); ?>
                    <?php $branch_loc_link_tapsi = get_field('branch_loc_link_tapsi', $branch->ID); ?>
                    <?php $branch_loc_link_google = get_field('branch_loc_link_google', $branch->ID); ?>
                    <?php $branch_loc_link_waze = get_field('branch_loc_link_waze', $branch->ID); ?>

                    <div class="w-full flex gap-4 items-center flex-col md:flex-row">

                        <div class="w-full md:w-2/5 h-64 md:h-[300px] overflow-hidden">

                            <?php if ($branch_thumbnail) : ?>
                                <?php echo $branch_thumbnail ?>
                            <?php else : ?>
                                <div class="w-full h-full bg-cynBlack/10 rounded-2xl flex items-center justify-center font-medium">لطفا یک عکس برای این شعبه انتخاب کنید</div>
                            <?php endif; ?>

                        </div>

                        <div class="w-full md:w-3/5 flex flex-col gap-4">

                            <?php if ($branch_title) : ?>
                                <p class="text-2xl font-normal"><?php echo $branch_title ?></p>
                            <?php endif; ?>

                            <?php if ($branch_address) : ?>
                                <p class="text-cynBlack text-base font-light"><?php echo $branch_address ?></p>
                            <?php endif; ?>

                            <?php if ($branch_phone) : ?>
                                <div class="flex flex-col gap-1">
                                    <span class="text-xs font-semibold text-cynBlack whitespace-nowrap"><?php _e('شماره تماس', 'jonubgard'); ?></span>
                                    <a href="tel:<?php echo $branch_phone ?>" class="text-cynBlue text-xs font-semibold"><?php echo $branch_phone ?></a>
                                </div>
                            <?php endif; ?>

                            <?php if ($branch_loc_link_balad || $branch_loc_link_neshan || $branch_loc_link_snapp || $branch_loc_link_tapsi || $branch_loc_link_google || $branch_loc_link_waze) : ?>

                                <div class="flex flex-col gap-3">

                                    <span class="text-xs font-semibold text-cynBlack whitespace-nowrap"><?php _e('آدرس روی نقشه', 'jonubgard'); ?></span>

                                    <ul class="flex items-center gap-2.5">

                                        <?php if ($branch_loc_link_balad) : ?>
                                            <li>
                                                <a href="<?php echo $branch_loc_link_balad ?>" class="text-cynBlue text-xs font-semibold">
                                                    <i class="size-9 flex items-center justify-center rounded-[10px]">
                                                        <img src="<?php echo get_template_directory_uri() ?>/assets/image/balad.svg" alt="balad" class="size-full object-contain">
                                                    </i>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php if ($branch_loc_link_neshan) : ?>
                                            <li>
                                                <a href="<?php echo $branch_loc_link_neshan ?>" class="text-cynBlue text-xs font-semibold">
                                                    <i class="size-9 flex items-center justify-center rounded-[10px]">
                                                        <img src="<?php echo get_template_directory_uri() ?>/assets/image/neshan.svg" alt="neshan" class="size-full object-contain">
                                                    </i>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php if ($branch_loc_link_snapp) : ?>
                                            <li>
                                                <a href="<?php echo $branch_loc_link_snapp ?>" class="text-cynBlue text-xs font-semibold">
                                                    <i class="size-9 flex items-center justify-center rounded-[10px]">
                                                        <img src="<?php echo get_template_directory_uri() ?>/assets/image/snapp.svg" alt="snapp" class="size-full object-contain">
                                                    </i>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php if ($branch_loc_link_tapsi) : ?>
                                            <li>
                                                <a href="<?php echo $branch_loc_link_tapsi ?>" class="text-cynBlue text-xs font-semibold">
                                                    <i class="size-9 flex items-center justify-center rounded-[10px]">
                                                        <img src="<?php echo get_template_directory_uri() ?>/assets/image/tapsi.svg" alt="tapsi" class="size-full object-contain">
                                                    </i>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php if ($branch_loc_link_google) : ?>
                                            <li>
                                                <a href="<?php echo $branch_loc_link_google ?>" class="text-cynBlue text-xs font-semibold">
                                                    <i class="size-9 flex items-center justify-center rounded-[10px]">
                                                        <img src="<?php echo get_template_directory_uri() ?>/assets/image/maps.svg" alt="google maps" class="size-full object-contain">
                                                    </i>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php if ($branch_loc_link_waze) : ?>
                                            <li>
                                                <a href="<?php echo $branch_loc_link_waze ?>" class="text-cynBlue text-xs font-semibold">
                                                    <i class="size-9 flex items-center justify-center rounded-[10px]">
                                                        <img src="<?php echo get_template_directory_uri() ?>/assets/image/waze.svg" alt="waze" class="size-full object-contain">
                                                    </i>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                    </ul>

                                </div>

                            <?php endif; ?>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        </section>
    <?php endif; ?>

</main>


<?php get_footer(); ?>