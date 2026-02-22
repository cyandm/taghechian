<?php
/* Template Name: Organizational */

use Cyan\Theme\Helpers\Icon;
use Cyan\Theme\Helpers\Templates;

$organizational_image = get_field('organizational_image');
$organizational_first_section_title = get_field('organizational_first_section_title');
$organizational_first_section_description = get_field('organizational_first_section_description');
$organizational_first_section_image = get_field('organizational_first_section_image');
$organizational_cooperation_process_section_title = get_field('organizational_cooperation_process_section_title');

for ($i = 1; $i <= 4; $i++) {
    $organizational_cooperation_process_title[] = get_field('organizational_cooperation_process_title_' . $i);
}

$organizational_second_section_title = get_field('organizational_second_section_title');
$organizational_second_section_description = get_field('organizational_second_section_description');
$organizational_second_section_image = get_field('organizational_second_section_image');
$organizational_form_title = get_field('organizational_form_title');
$organizational_form_under_title = get_field('organizational_form_under_title');
$organizational_form_image = get_field('organizational_form_image');

get_header(null, ['header_light' => true]);
?>

<main class="organizational-collaboration">

    <?php if ($organizational_image): ?>
        <section>
            <div class="w-full">
                <?php echo wp_get_attachment_image($organizational_image, 'full', false, ['class' => 'w-full h-full object-contain']); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($organizational_first_section_image && $organizational_first_section_title && $organizational_first_section_description): ?>
        <section class="container flex flex-col-reverse md:flex-row items-center gap-3 md:gap-8 my-14">
            <div class="w-full md:w-1/2 flex flex-col gap-2 md:gap-5 text-base md:text-xl font-light text-cynBlack/70 leading-9">
                <p class="text-3xl md:text-[40px] font-normal text-cynBlack">
                    <?php echo $organizational_first_section_title; ?>
                </p>
                <?php echo $organizational_first_section_description; ?>
            </div>
            <div class="w-full md:w-1/2">
                <?php echo wp_get_attachment_image($organizational_first_section_image, 'full', false, ['class' => 'w-full h-[340px] md:h-[720px] lg:h-[520px] object-cover object-center']); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($organizational_cooperation_process_section_title): ?>
        <section class="flex flex-col gap-5 justify-center items-center">
            <div>
                <p class="text-3xl md:text-[40px] font-normal text-cynBlack">
                    <?php echo $organizational_cooperation_process_section_title; ?>
                </p>
            </div>

            <div class="bg-cynBgItem/35 w-full py-11">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-16 lg:gap-4 container">
                    <?php foreach ($organizational_cooperation_process_title as $index => $title): ?>
                        <div class="flex justify-center items-center py-6 sm:py-9 bg-no-repeat bg-center bg-contain" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/image/number-<?php echo $index + 1; ?>.svg');">
                            <p class="text-xl font-normal text-cynBlack">
                                <?php echo $title; ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </section>
    <?php endif; ?>

    <?php if ($organizational_second_section_image && $organizational_second_section_title && $organizational_second_section_description): ?>
        <section class="container flex flex-col-reverse md:flex-row-reverse items-center gap-3 md:gap-8 my-14">
            <div class="w-full md:w-1/2 flex flex-col gap-2 md:gap-5 text-base md:text-xl font-light text-cynBlack/70 leading-9">
                <p class="text-3xl md:text-[40px] font-normal text-cynBlack">
                    <?php echo $organizational_second_section_title; ?>
                </p>
                <?php echo $organizational_second_section_description; ?>
            </div>
            <div class="w-full md:w-1/2">
                <?php echo wp_get_attachment_image($organizational_second_section_image, 'full', false, ['class' => 'w-full h-[340px] md:h-[720px] lg:h-[520px] object-cover object-center']); ?>
            </div>
        </section>
    <?php endif; ?>

    <section class="container flex flex-col-reverse md:flex-row items-center gap-3 md:gap-8">

        <div class="w-full md:w-1/2 flex flex-col gap-5 justify-center">

            <div class="flex flex-col gap-2 max-lg:justify-center max-lg:text-center">
                <p class="text-3xl md:text-[40px] font-normal">
                    <?php echo $organizational_form_title ? $organizational_form_title : __('فرم سفارش', 'taghechian'); ?>
                </p>

                <p class="text-cynBlack/50 text-base md:text-xl font-medium">
                    <?php echo $organizational_form_under_title ? $organizational_form_under_title : __('چیزی هست که بخوای به ما بگی', 'taghechian'); ?>
                </p>
            </div>

            <form hx-post="<?php echo rest_url('cyn/v1/order_form') ?>" hx-target=".result" hx-swap="innerHTML" hx-on::after-request="(function(ev){ var resultEl = document.querySelector('.result'); resultEl.style.display = 'block'; resultEl.style.opacity = '1'; if(ev.detail.successful){ resultEl.textContent = '<?php echo esc_js(__('با موفقیت ارسال شد', 'taghechian')); ?>'; resultEl.style.background = ''; var f = document.getElementById('order_form'); if(f) f.reset(); } else { try { var d = JSON.parse(ev.detail.xhr.responseText); resultEl.textContent = d.error || '<?php echo esc_js(__('خطا در ارسال', 'taghechian')); ?>'; } catch(e){ resultEl.textContent = '<?php echo esc_js(__('خطا در ارسال', 'taghechian')); ?>'; } resultEl.style.background = '#ef4444'; } setTimeout(function(){ resultEl.style.transition = 'opacity 0.5s ease-out'; resultEl.style.opacity = '0'; setTimeout(function(){ resultEl.style.display = 'none'; }, 500); }, 5000); })(event)" action="" method="post" id="order_form" class="flex flex-col gap-4 max-lg:w-full [&_label]:w-full [&_input]:w-full [&_textarea]:w-full">

                <label for="organization_name" class="relative">
                    <div class="size-6 text-cynBlack/60 absolute start-3 top-1/2 -translate-y-1/2">
                        <?php icon::print('User,-Profile'); ?>
                    </div>
                    <input
                        type="text"
                        id="organization_name"
                        name="organization_name"
                        placeholder="<?php _e('نام سازمان'); ?>"
                        class="focus:outline-none w-full font-light text-xl ps-10 pe-5 py-3 bg-white rounded-2xl border border-cynBlack/20" />
                </label>

                <label for="product_type" class="relative">
                    <div class="size-6 text-cynBlack/60 absolute start-3 top-1/2 -translate-y-1/2">
                        <?php icon::print('package-box'); ?>
                    </div>
                    <input
                        type="text"
                        id="product_type"
                        name="product_type"
                        placeholder="<?php _e('نوع کالا'); ?>"
                        class="focus:outline-none w-full font-light text-xl ps-10 pe-5 py-3 bg-white rounded-2xl border border-cynBlack/20" />
                </label>

                <label for="quantity" class="relative">
                    <div class="size-6 text-cynBlack/60 absolute start-3 top-1/2 -translate-y-1/2">
                        <?php icon::print('check-list-checkmark-1'); ?>
                    </div>
                    <input
                        type="number"
                        id="quantity"
                        name="quantity"
                        placeholder="<?php _e('تعداد مورد نیاز'); ?>"
                        required
                        dir="rtl"
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

                <div class="flex justify-end ">
                    <button type="submit" class="primary-btn text-white flex justify-center items-center gap-1 !text-xl !font-semibold max-md:w-full">
                        <span class="size-6 stroke-[1.5]">
                            <?php icon::print('Send') ?>
                        </span>
                        <span class="text-xl font-semibold">
                            <?php _e('ارسال درخواست'); ?>
                        </span>
                    </button>
                </div>

            </form>

            <div class="result bg-green-500 text-white text-xl font-semibold rounded-2xl p-3 shadow-item fixed top-4 right-4 z-50" style="display:none; opacity: 0; transition: opacity 0.5s ease-out;"></div>
        </div>

        <div class="flex items-center lg:w-1/2">
            <?php echo wp_get_attachment_image($organizational_form_image, 'full', false, ['class' => 'w-full md:h-[495px] object-contain object-center']); ?>
        </div>

    </section>

    <?php Templates::getPart('faq', ['faq_place' => 'organizational-collaboration']); ?>

</main>


<?php get_footer(); ?>