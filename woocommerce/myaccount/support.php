<?php

if (! defined('ABSPATH')) {
    exit;
}

use Cyan\Theme\Helpers\Icon;

$current_user = wp_get_current_user();
$customer_id = get_current_user_id();

$first_name = is_string($current_user->first_name) ? trim($current_user->first_name) : '';
$last_name = is_string($current_user->last_name) ? trim($current_user->last_name) : '';
$display_name = trim($first_name . ' ' . $last_name);
if ($display_name === '') {
    $display_name = is_string($current_user->display_name) ? trim($current_user->display_name) : '';
}

$billing_phone = get_user_meta($customer_id, 'billing_phone', true);
$account_phone = get_user_meta($customer_id, 'phone', true);
$default_phone = is_string($billing_phone) && $billing_phone !== ''
    ? $billing_phone
    : (is_string($account_phone) ? $account_phone : '');
?>

<section class="bg-white border border-cynBlack/10 rounded-2xl px-4 py-5 md:px-6 md:py-6">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-5 md:mb-6">
            <h2 class="text-[26px] md:text-[34px] leading-tight font-[dinar] text-cynBlack">
                <?php esc_html_e('چیزی هست که بخوای به ما بگی', 'taghechian'); ?>
            </h2>
        </div>

        <form
            id="support_form"
            method="post"
            hx-post="<?php echo esc_url(rest_url('cyn/v1/support_form')); ?>"
            hx-target=".support-form-result"
            hx-swap="innerHTML"
            hx-on::before-request="(function(){ var submitBtn = document.getElementById('support-submit-btn'); if(!submitBtn){ return; } submitBtn.disabled = true; submitBtn.setAttribute('aria-busy', 'true'); submitBtn.classList.add('opacity-70', 'cursor-wait'); var idleText = submitBtn.querySelector('[data-submit-idle]'); var loadingText = submitBtn.querySelector('[data-submit-loading]'); if(idleText){ idleText.style.display = 'none'; } if(loadingText){ loadingText.style.display = 'inline-flex'; } })()"
            hx-on::after-request="(function(ev){ var submitBtn = document.getElementById('support-submit-btn'); if(submitBtn){ submitBtn.disabled = false; submitBtn.removeAttribute('aria-busy'); submitBtn.classList.remove('opacity-70', 'cursor-wait'); var idleText = submitBtn.querySelector('[data-submit-idle]'); var loadingText = submitBtn.querySelector('[data-submit-loading]'); if(idleText){ idleText.style.display = 'inline'; } if(loadingText){ loadingText.style.display = 'none'; } } var resultEl = document.querySelector('.support-form-result'); resultEl.style.display = 'block'; resultEl.style.opacity = '1'; if(ev.detail.successful){ resultEl.textContent = '<?php echo esc_js(__('با موفقیت ارسال شد', 'taghechian')); ?>'; resultEl.style.background = ''; var f = document.getElementById('support_form'); if(f) f.reset(); var phoneInput = document.getElementById('support-phone'); if(phoneInput){ phoneInput.value = '<?php echo esc_js($default_phone); ?>'; } var nameInput = document.getElementById('support-name'); if(nameInput){ nameInput.value = '<?php echo esc_js($display_name); ?>'; } } else { try { var d = JSON.parse(ev.detail.xhr.responseText); resultEl.textContent = d.error || '<?php echo esc_js(__('خطا در ارسال', 'taghechian')); ?>'; } catch(e){ resultEl.textContent = '<?php echo esc_js(__('خطا در ارسال', 'taghechian')); ?>'; } resultEl.style.background = '#ef4444'; } setTimeout(function(){ resultEl.style.transition = 'opacity 0.5s ease-out'; resultEl.style.opacity = '0'; setTimeout(function(){ resultEl.style.display = 'none'; }, 500); }, 5000); })(event)"
            class="flex flex-col gap-3 md:gap-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                <label for="support-name" class="relative">
                    <div class="size-5 text-cynBlack/60 absolute end-3 top-1/2 -translate-y-1/2">
                        <?php Icon::print('User,-Profile-7'); ?>
                    </div>
                    <input
                        type="text"
                        id="support-name"
                        name="name"
                        required
                        value="<?php echo esc_attr($display_name); ?>"
                        placeholder="<?php esc_attr_e('نام شما', 'taghechian'); ?>"
                        class="focus:outline-none w-full text-base md:text-lg pe-10 ps-4 py-3 rounded-2xl border border-cynBlack/20 bg-white" />
                </label>

                <label for="support-phone" class="relative">
                    <div class="size-5 text-cynBlack/60 absolute end-3 top-1/2 -translate-y-1/2">
                        <?php Icon::print('Phone,-Call-11'); ?>
                    </div>
                    <input
                        type="tel"
                        id="support-phone"
                        name="phone"
                        required
                        pattern="[0-9]{11}"
                        inputmode="numeric"
                        dir="rtl"
                        value="<?php echo esc_attr($default_phone); ?>"
                        placeholder="<?php esc_attr_e('شماره تماس شما', 'taghechian'); ?>"
                        class="focus:outline-none w-full text-base md:text-lg pe-10 ps-4 py-3 rounded-2xl border border-cynBlack/20 bg-white" />
                </label>
            </div>

            <label for="support-message" class="relative">
                <div class="size-5 text-cynBlack/60 absolute end-3 top-3">
                    <?php Icon::print('Chat,-Messages-1'); ?>
                </div>
                <textarea
                    id="support-message"
                    name="message"
                    rows="3"
                    required
                    maxlength="65525"
                    placeholder="<?php esc_attr_e('پیام شما', 'taghechian'); ?>"
                    class="focus:outline-none w-full text-base md:text-lg pe-10 ps-4 py-3 rounded-2xl border border-cynBlack/20 bg-white resize-y"></textarea>
            </label>

            <div class="flex justify-start">
                <button id="support-submit-btn" type="submit" class="primary-btn !text-cynBlack !text-lg md:!text-xl !font-semibold min-w-[150px] inline-flex items-center justify-center gap-2">
                    <span data-submit-idle><?php esc_html_e('ارسال پیام', 'taghechian'); ?></span>
                    <span data-submit-loading class="hidden items-center gap-2">
                        <span class="inline-block size-4 border-2 border-cynBlack/40 border-t-cynBlack rounded-full animate-spin"></span>
                        <?php esc_html_e('در حال ارسال...', 'taghechian'); ?>
                    </span>
                </button>
            </div>
        </form>

        <div class="support-form-result bg-green-500 text-white text-base md:text-lg font-semibold rounded-2xl p-3 shadow-item fixed top-4 right-4 left-4 z-50" style="display:none; opacity: 0; transition: opacity 0.5s ease-out;"></div>
    </div>
</section>